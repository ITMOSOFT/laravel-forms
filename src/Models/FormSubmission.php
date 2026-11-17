<?php

namespace ItmoSoft\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('itmosoft-forms.table_prefix', 'forms_')
            .config('itmosoft-forms.tables.submissions', 'submissions');
    }

    protected function casts(): array
    {
        return ['payload' => 'array', 'submitted_at' => 'datetime'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(config('itmosoft-forms.models.form'), 'form_id');
    }
}
