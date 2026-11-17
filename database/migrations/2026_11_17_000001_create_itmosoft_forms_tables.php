<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('itmosoft-forms.table_prefix', 'forms_');
        $forms = $prefix.config('itmosoft-forms.tables.forms', 'forms');
        $fields = $prefix.config('itmosoft-forms.tables.fields', 'fields');
        $submissions = $prefix.config('itmosoft-forms.tables.submissions', 'submissions');

        Schema::create($forms, function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create($fields, function (Blueprint $table) use ($forms): void {
            $table->id();
            $table->foreignId('form_id')->constrained($forms)->cascadeOnDelete();
            $table->string('key', 100);
            $table->string('label');
            $table->string('type', 30);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_required')->default(false);
            $table->json('rules')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
            $table->unique(['form_id', 'key']);
            $table->index(['form_id', 'position']);
        });

        Schema::create($submissions, function (Blueprint $table) use ($forms): void {
            $table->id();
            $table->foreignId('form_id')->constrained($forms)->cascadeOnDelete();
            $table->unsignedInteger('form_version');
            $table->json('payload');
            $table->json('metadata')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
            $table->index(['form_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        $prefix = config('itmosoft-forms.table_prefix', 'forms_');
        Schema::dropIfExists($prefix.config('itmosoft-forms.tables.submissions', 'submissions'));
        Schema::dropIfExists($prefix.config('itmosoft-forms.tables.fields', 'fields'));
        Schema::dropIfExists($prefix.config('itmosoft-forms.tables.forms', 'forms'));
    }
};
