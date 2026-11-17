# Release guide — v1.0.1

Repository: `ITMOSOFT/laravel-forms`

Composer package: `itmosoft/laravel-forms`

## Repository description

Dynamic form definitions, versioned fields, submissions, and validation for Laravel applications.

## User-run Git commands

For an existing clone of the v1.0.0 repository, replace its files with this patch release and run:

```bash
git add .
git status
git commit -m "fix: correct Laravel Forms PSR-4 autoloading"
git push origin main
```

After local tests and GitHub Actions pass:

```bash
git tag -a v1.0.1 -m "Release v1.0.1"
git push origin v1.0.1
```

## GitHub Release

Title: `v1.0.1 - Fix PSR-4 Autoloading`

Description:

Patch release fixing Composer PSR-4 namespace escaping for the package source and PHPUnit test namespaces. This allows Composer to autoload `ItmoSoft\Forms\...` classes and the shared `ItmoSoft\Forms\Tests\TestCase` test base class correctly. No public API behavior is intentionally changed.

## Packagist

Packagist should detect the new `v1.0.1` tag automatically after the repository webhook runs. If needed, update the package at https://packagist.org/packages/itmosoft/laravel-forms.

Do not publish until local tests and GitHub Actions pass.
