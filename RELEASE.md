# Release guide — v1.0.0

Repository: `ITMOSOFT/laravel-forms`

Composer package: `itmosoft/laravel-forms`

## Repository description

Dynamic form definitions, versioned fields, submissions, and validation for Laravel applications.

## User-run Git commands

Run these commands locally from the extracted package directory. Review `git status` before committing.

```bash
git init
git branch -M main
git add .
git status
git commit -m "chore: initial Laravel forms package"
git remote add origin https://github.com/ITMOSOFT/laravel-forms.git
git push -u origin main
```

After local tests and GitHub Actions pass:

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

## GitHub Release

Title: `v1.0.0 - Initial Release`

Description:

Initial release of ITMOSOFT Laravel Forms.

- Create and manage draft form definitions.
- Define typed fields with Laravel validation rules and options.
- Publish and archive forms.
- Validate submission payloads against published form schemas.
- Reject undeclared payload keys and submissions to unpublished forms.
- Store submissions with form-version and metadata snapshots.
- Configurable table names/prefix, model classes, field count, and payload-size limits.
- Laravel 12/13 and PHP 8.3+ support.

## Packagist

1. Open https://packagist.org/packages/submit
2. Submit `https://github.com/ITMOSOFT/laravel-forms`
3. Configure the GitHub service hook/webhook in Packagist if prompted.
4. Confirm Packagist detects tag `v1.0.0`.
5. Verify installation with `composer require itmosoft/laravel-forms`.

Do not publish until local tests and the GitHub Actions matrix pass.
