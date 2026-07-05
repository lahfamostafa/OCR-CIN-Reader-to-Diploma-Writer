<?php

namespace Tests\Feature;

use App\Models\DiplomaTemplate;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiplomaWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_secretaire_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'secretaire']);

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.templates.index'))->assertForbidden();
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.templates.index'))->assertOk();
    }

    public function test_cin_upload_creates_student_and_redirects_to_validation(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'secretaire']);

        $response = $this->actingAs($user)->post(route('cin.store'), [
            'cin_image' => UploadedFile::fake()->image('cin.png', 640, 400),
        ]);

        $this->assertDatabaseCount('students', 1);
        $student = Student::first();
        $response->assertRedirect(route('students.edit', $student));
        Storage::disk('public')->assertExists($student->cin_image_path);
    }

    public function test_student_data_can_be_validated_and_saved(): void
    {
        $user = User::factory()->create(['role' => 'secretaire']);
        $student = Student::create([
            'first_name' => '',
            'last_name' => '',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->put(route('students.update', $student), [
            'first_name' => 'Yassine',
            'last_name' => 'El Amrani',
            'birth_date' => '1999-05-12',
            'birth_place' => 'Casablanca',
            'cin_number' => 'BK123456',
        ])->assertRedirect(route('students.show', $student));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Yassine',
            'last_name' => 'El Amrani',
        ]);
    }

    public function test_diploma_is_generated_with_pdf(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'secretaire']);
        $student = Student::create([
            'first_name' => 'Yassine',
            'last_name' => 'El Amrani',
            'birth_date' => '1999-05-12',
            'created_by' => $user->id,
        ]);
        $template = DiplomaTemplate::create([
            'name' => 'Licence en Informatique',
            'level' => 'Licence',
            'field' => 'Informatique',
            'view' => 'licence',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('diplomas.store'), [
            'student_id' => $student->id,
            'diploma_template_id' => $template->id,
            'mention' => 'Très bien',
            'academic_year' => '2024-2025',
        ]);

        $this->assertDatabaseCount('diplomas', 1);
        $diploma = $student->diplomas()->first();
        $response->assertRedirect(route('diplomas.show', $diploma));
        $this->assertNotNull($diploma->pdf_path);
        Storage::disk('public')->assertExists($diploma->pdf_path);
    }
}
