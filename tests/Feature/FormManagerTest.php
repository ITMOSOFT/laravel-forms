<?php

namespace ItmoSoft\Forms\Tests\Feature;

use Illuminate\Validation\ValidationException;
use ItmoSoft\Forms\Enums\FormStatus;
use ItmoSoft\Forms\Exceptions\FormNotPublishedException;
use ItmoSoft\Forms\Services\FormManager;
use ItmoSoft\Forms\Tests\TestCase;

class FormManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    public function test_it_creates_a_draft_form(): void
    {
        $form = app(FormManager::class)->create(['name' => 'Contact us', 'slug' => 'contact-us']);

        $this->assertSame(FormStatus::Draft, $form->status);
        $this->assertSame(1, $form->version);
    }

    public function test_it_adds_a_field_to_a_draft_form(): void
    {
        $manager = app(FormManager::class);
        $form = $manager->create(['name' => 'Contact us', 'slug' => 'contact-us']);
        $field = $manager->addField($form, ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'is_required' => true]);

        $this->assertSame('email', $field->key);
        $this->assertTrue($field->is_required);
    }

    public function test_it_requires_at_least_one_field_to_publish(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        app(FormManager::class)->publish(app(FormManager::class)->create(['name' => 'Empty', 'slug' => 'empty']));
    }

    public function test_it_publishes_a_form(): void
    {
        $manager = app(FormManager::class);
        $form = $manager->create(['name' => 'Contact us', 'slug' => 'contact-us']);
        $manager->addField($form, ['key' => 'email', 'label' => 'Email', 'type' => 'email']);
        $published = $manager->publish($form);

        $this->assertSame(FormStatus::Published, $published->status);
        $this->assertNotNull($published->published_at);
    }

    public function test_it_accepts_a_valid_submission(): void
    {
        $manager = app(FormManager::class);
        $form = $manager->create(['name' => 'Contact us', 'slug' => 'contact-us']);
        $manager->addField($form, ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'is_required' => true, 'rules' => ['email']]);
        $manager->publish($form);

        $submission = $manager->submit($form, ['email' => 'adeel@example.com'], ['ip' => '127.0.0.1']);

        $this->assertSame(['email' => 'adeel@example.com'], $submission->payload);
        $this->assertSame(1, $submission->form_version);
        $this->assertSame(['ip' => '127.0.0.1'], $submission->metadata);
    }

    public function test_it_rejects_submissions_for_draft_forms(): void
    {
        $this->expectException(FormNotPublishedException::class);
        $manager = app(FormManager::class);
        $form = $manager->create(['name' => 'Draft', 'slug' => 'draft']);
        $manager->submit($form, []);
    }

    public function test_it_rejects_missing_required_fields(): void
    {
        $this->expectException(ValidationException::class);
        $manager = app(FormManager::class);
        $form = $manager->create(['name' => 'Contact us', 'slug' => 'contact-us']);
        $manager->addField($form, ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'is_required' => true]);
        $manager->publish($form);
        $manager->submit($form, []);
    }

    public function test_it_rejects_unknown_payload_keys(): void
    {
        $this->expectException(ValidationException::class);
        $manager = app(FormManager::class);
        $form = $manager->create(['name' => 'Contact us', 'slug' => 'contact-us']);
        $manager->addField($form, ['key' => 'name', 'label' => 'Name', 'type' => 'text']);
        $manager->publish($form);
        $manager->submit($form, ['name' => 'Adeel', 'admin' => true]);
    }

    public function test_it_prevents_field_changes_after_publication(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $manager = app(FormManager::class);
        $form = $manager->create(['name' => 'Contact us', 'slug' => 'contact-us']);
        $manager->addField($form, ['key' => 'name', 'label' => 'Name', 'type' => 'text']);
        $manager->publish($form);
        $manager->addField($form, ['key' => 'phone', 'label' => 'Phone', 'type' => 'text']);
    }

    public function test_it_archives_a_form(): void
    {
        $manager = app(FormManager::class);
        $form = $manager->create(['name' => 'Contact us', 'slug' => 'contact-us']);
        $manager->addField($form, ['key' => 'name', 'label' => 'Name', 'type' => 'text']);
        $manager->publish($form);
        $form->archive();

        $this->assertSame(FormStatus::Archived, $form->refresh()->status);
    }
}
