<?php

namespace ItmoSoft\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('itmosoft-forms.table_prefix', 'forms_')
            .config('itmosoft-forms.tables.fields', 'fields');
    }

    protected function casts(): array
    {
        return ['rules' => 'array', 'options' => 'array', 'is_required' => 'boolean'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(config('itmosoft-forms.models.form'), 'form_id');
    }
}
