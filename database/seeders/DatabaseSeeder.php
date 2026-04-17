<?php

namespace Database\Seeders;

use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
            'role'  => UserRole::Admin,
        ]);

        $citizens = User::factory()
            ->count(3)
            ->state(['role' => UserRole::Citizen])
            ->create();

        // Known demo citizen so you can log in predictably.
        $demoCitizen = User::factory()->create([
            'name'  => 'Demo Citizen',
            'email' => 'citizen@example.com',
            'role'  => UserRole::Citizen,
        ]);

        // Each citizen files a handful of complaints in varied states.
        foreach ($citizens->push($demoCitizen) as $citizen) {
            Complaint::factory()->count(2)->for($citizen)->create();
            Complaint::factory()->inProgress()->for($citizen)->create([
                'assigned_to' => $admin->id,
            ]);
            Complaint::factory()->resolved()->for($citizen)->create([
                'assigned_to' => $admin->id,
            ]);
        }
    }
}
