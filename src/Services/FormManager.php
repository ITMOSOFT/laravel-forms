<?php

namespace ItmoSoft\Forms\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ItmoSoft\Forms\Enums\FormStatus;
use ItmoSoft\Forms\Exceptions\FormNotPublishedException;
use ItmoSoft\Forms\Exceptions\FormPayloadTooLargeException;
use ItmoSoft\Forms\Models\Form;
use ItmoSoft\Forms\Models\FormField;
use ItmoSoft\Forms\Models\FormSubmission;
use InvalidArgumentException;

class FormManager
{
    public function create(array $attributes): Form
    {
        return $this->formModel()::query()->create([
            'name' => $attributes['name'],
            'slug' => $attributes['slug'],
            'description' => $attributes['description'] ?? null,
            'status' => FormStatus::Draft,
            'version' => 1,
        ]);
    }

    public function addField(Form $form, array $attributes): FormField
    {
        if ($form->status !== FormStatus::Draft) {
            throw new InvalidArgumentException('Fields can only be changed while a form is in draft status.');
        }

        $max = (int) config('itmosoft-forms.max_fields', 200);
        if ($form->fields()->count() >= $max) {
            throw new InvalidArgumentException("A form cannot contain more than {$max} fields.");
        }

        $type = $attributes['type'] ?? null;
        if (! in_array($type, ['text', 'textarea', 'email', 'number', 'select', 'checkbox', 'date', 'file'], true)) {
            throw new InvalidArgumentException('Unsupported form field type.');
        }

        return $form->fields()->create([
            'key' => $attributes['key'],
            'label' => $attributes['label'],
            'type' => $type,
            'position' => $attributes['position'] ?? ((int) $form->fields()->max('position') + 1),
            'is_required' => (bool) ($attributes['is_required'] ?? false),
            'rules' => $attributes['rules'] ?? [],
            'options' => $attributes['options'] ?? null,
        ]);
    }

    public function publish(Form $form): Form
    {
        if ($form->fields()->count() === 0) {
            throw new InvalidArgumentException('A form must have at least one field before publication.');
        }

        $form->publish();

        return $form->refresh();
    }

    public function submit(Form $form, array $payload, array $metadata = []): FormSubmission
    {
        if (! $form->isAcceptingSubmissions()) {
            throw FormNotPublishedException::forForm($form->getKey());
        }

        $maxBytes = (int) config('itmosoft-forms.max_payload_bytes', 65535);
        $encoded = json_encode($payload, JSON_THROW_ON_ERROR);
        if (strlen($encoded) > $maxBytes) {
            throw FormPayloadTooLargeException::maximum($maxBytes);
        }

        $fields = $form->fields()->get();
        $rules = [];
        $allowed = [];
        foreach ($fields as $field) {
            $allowed[] = $field->key;
            $fieldRules = is_array($field->rules) ? $field->rules : [];
            if ($field->is_required && ! in_array('required', $fieldRules, true)) {
                array_unshift($fieldRules, 'required');
            }
            $rules[$field->key] = $fieldRules;
        }

        $unknown = array_diff(array_keys($payload), $allowed);
        if ($unknown !== []) {
            throw ValidationException::withMessages([
                'payload' => ['Unknown field keys: '.implode(', ', $unknown)],
            ]);
        }

        $validated = Validator::make($payload, $rules)->validate();

        return DB::transaction(fn () => $this->submissionModel()::query()->create([
            'form_id' => $form->getKey(),
            'form_version' => $form->version,
            'payload' => $validated,
            'metadata' => $metadata,
            'submitted_at' => now(),
        ]));
    }

    protected function formModel(): string
    {
        return config('itmosoft-forms.models.form', Form::class);
    }

    protected function submissionModel(): string
    {
        return config('itmosoft-forms.models.submission', FormSubmission::class);
    }
}
