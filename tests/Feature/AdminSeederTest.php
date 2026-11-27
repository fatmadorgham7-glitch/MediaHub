<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_cree_un_compte_avec_les_identifiants_configures(): void
    {
        config([
            'mediahub.admin_email' => 'admin@example.test',
            'mediahub.admin_password' => 'une-cle-secrete-de-test',
        ]);

        $this->seed(AdminSeeder::class);

        $admin = User::where('email', 'admin@example.test')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('une-cle-secrete-de-test', $admin->password));
    }

    public function test_database_seeders_peuvent_etre_relances_sans_doublons(): void
    {
        config([
            'mediahub.admin_email' => 'admin@example.test',
            'mediahub.admin_password' => 'une-cle-secrete-de-test',
        ]);

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('categories', 3);
        $this->assertDatabaseCount('users', 1);
    }
}
