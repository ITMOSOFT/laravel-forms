# ITMOSOFT Laravel Forms

Dynamic form definitions, fields, publication, validation, and submission storage for Laravel 12 and 13.

## Requirements

- PHP 8.3+
- Laravel 12 or 13
- SQLite, MySQL, or PostgreSQL supported by Laravel migrations

## Installation

```bash
composer require itmosoft/laravel-forms
php artisan vendor:publish --tag=itmosoft-forms-config
php artisan migrate
```

Laravel package auto-discovery registers the service provider.

## Basic usage

```php
use ItmoSoft\Forms\Services\FormManager;

$manager = app(FormManager::class);

$form = $manager->create([
    'name' => 'Contact us',
    'slug' => 'contact-us',
    'description' => 'General enquiries',
]);

$manager->addField($form, [
    'key' => 'email',
    'label' => 'Email address',
    'type' => 'email',
    'is_required' => true,
    'rules' => ['email', 'max:255'],
]);

$manager->addField($form, [
    'key' => 'message',
    'label' => 'Message',
    'type' => 'textarea',
    'is_required' => true,
    'rules' => ['string', 'max:5000'],
]);

$manager->publish($form);

$submission = $manager->submit($form, [
    'email' => 'person@example.com',
    'message' => 'Hello',
], [
    'source' => 'website',
]);
```

## Supported field types

`text`, `textarea`, `email`, `number`, `select`, `checkbox`, `date`, `file`.

The package stores field rules as Laravel validation rules. Supply `options` for fields such as `select`; UI rendering and file upload handling belong to the consuming application.

## Lifecycle and behavior

- Forms begin in `draft` status.
- Fields can be added only while a form is a draft.
- A form needs at least one field before it can be published.
- Published forms accept validated submissions.
- Submission payload keys not declared by the form are rejected.
- Each submission stores the form version and optional metadata.
- Archived forms reject new submissions.
- Field definitions are immutable after publication in this initial release.

## Configuration

The `itmosoft-forms` config supports table prefix/name overrides, model class overrides, maximum field count, and maximum encoded payload size. The migration uses the configured table names when it runs.

## Important production considerations

- Apply rate limiting, CSRF protection, bot protection, and authorization in your application routes/controllers.
- Never trust client-supplied metadata. Populate metadata server-side.
- The `file` field type only validates/stores the field value; this package does not upload, scan, or store files.
- Define validation rules carefully. Do not accept arbitrary executable rules from untrusted users.
- Add tenant scoping/authorization in your application. This package does not automatically isolate tenants.
- Published forms are immutable in v1.0.0. Create a new form if the schema needs to change; versioned editing/roll-forward is a future feature.
- Form submissions may contain personal data. Define retention, export, deletion, and access-control policies appropriate to your jurisdiction.

## Testing

```bash
composer install
composer test
```

## Scope boundaries

This package owns form definitions, fields, publication state, payload validation, and stored submissions. It does not provide a frontend builder, HTTP routes, admin UI, lead conversion, email delivery, analytics dashboards, spam detection, file uploads, or multi-tenant isolation.

## License

MIT.
