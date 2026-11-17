<?php

namespace ItmoSoft\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ItmoSoft\Forms\Enums\FormStatus;

class Form extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('itmosoft-forms.table_prefix', 'forms_')
            .config('itmosoft-forms.tables.forms', 'forms');
    }

    protected function casts(): array
    {
        return ['status' => FormStatus::class, 'published_at' => 'datetime'];
    }

    public function fields(): HasMany
    {
        return $this->hasMany(config('itmosoft-forms.models.field'), 'form_id')->orderBy('position');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(config('itmosoft-forms.models.submission'), 'form_id');
    }

    public function publish(): void
    {
        $this->forceFill(['status' => FormStatus::Published, 'published_at' => now()])->save();
    }

    public function archive(): void
    {
        $this->forceFill(['status' => FormStatus::Archived])->save();
    }

    public function isAcceptingSubmissions(): bool
    {
        return $this->status === FormStatus::Published;
    }
}
