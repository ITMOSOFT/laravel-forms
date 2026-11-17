<?php

return [
    'table_prefix' => 'forms_',
    'tables' => [
        'forms' => 'forms',
        'fields' => 'fields',
        'submissions' => 'submissions',
    ],
    'models' => [
        'form' => ItmoSoft\Forms\Models\Form::class,
        'field' => ItmoSoft\Forms\Models\FormField::class,
        'submission' => ItmoSoft\Forms\Models\FormSubmission::class,
    ],
    'max_fields' => 200,
    'max_payload_bytes' => 65535,
];
