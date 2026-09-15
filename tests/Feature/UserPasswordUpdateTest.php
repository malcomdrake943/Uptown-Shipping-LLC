<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserPasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_user_with_password(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'New Staff User',
                'email' => 'newstaff@example.com',
                'role' => 'staff',
                'password' => 'created-password-123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $createdUser = User::where('email', 'newstaff@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue(Hash::check('created-password-123', $createdUser->password));
    }

    public function test_superadmin_can_update_user_password_via_edit_user_header_action(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $targetUser = User::factory()->create([
            'name' => 'Jane Staff',
            'email' => 'jane@example.com',
            'role' => 'staff',
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(UserResource\Pages\EditUser::class, [
            'record' => $targetUser->getKey(),
        ])
            ->callAction('updatePassword', data: [
                'password' => 'another-new-password',
                'password_confirmation' => 'another-new-password',
            ])
            ->assertHasNoActionErrors();

        $this->assertTrue(Hash::check('another-new-password', $targetUser->fresh()->password));
    }

    public function test_superadmin_can_update_user_password_via_edit_user_form(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $targetUser = User::factory()->create([
            'name' => 'John Staff',
            'email' => 'john@example.com',
            'role' => 'staff',
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(UserResource\Pages\EditUser::class, [
            'record' => $targetUser->getKey(),
        ])
            ->fillForm([
                'password' => 'form-updated-password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('form-updated-password', $targetUser->fresh()->password));
    }

    public function test_superadmin_can_access_admin_profile_page(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin/profile');

        $response->assertSuccessful();
    }

    public function test_admin_can_update_own_profile_information(): void
    {
        $superAdmin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'adminprofile@example.com',
            'role' => 'superadmin',
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(\App\Filament\Pages\Auth\EditProfile::class)
            ->fillForm([
                'name' => 'Updated Admin User',
                'email' => 'updatedadmin@example.com',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Updated Admin User', $superAdmin->fresh()->name);
        $this->assertEquals('updatedadmin@example.com', $superAdmin->fresh()->email);
    }

    public function test_superadmin_can_reset_password_via_settings_change_password_page(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'password' => Hash::make('my-current-password'),
        ]);

        $this->actingAs($superAdmin);

        $response = $this->get('/admin/change-password');
        $response->assertSuccessful();

        Livewire::test(\App\Filament\Pages\ChangePassword::class)
            ->fillForm([
                'current_password' => 'my-current-password',
                'new_password' => 'brand-new-secret-123',
                'new_password_confirmation' => 'brand-new-secret-123',
            ])
            ->call('submit')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('brand-new-secret-123', $superAdmin->fresh()->password));
    }

    public function test_admin_user_seeder_seeds_superadmin_phone_numbers(): void
    {
        $this->seed(\Database\Seeders\AdminUserSeeder::class);

        $admin1 = User::where('email', 'admin1@parcelproxy.com')->first();
        $admin2 = User::where('email', 'admin2@parcelproxy.com')->first();

        $this->assertNotNull($admin1);
        $this->assertEquals('+1 (478) 442-3863', $admin1->phone);
        $this->assertEquals('superadmin', $admin1->role);

        $this->assertNotNull($admin2);
        $this->assertEquals('+1 (804) 915-7862', $admin2->phone);
        $this->assertEquals('superadmin', $admin2->role);
    }
}
