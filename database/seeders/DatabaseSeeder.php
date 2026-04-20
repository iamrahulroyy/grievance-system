<?php

namespace Database\Seeders;

use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Complaint;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Admins ──────────────────────────────────────
        $adminRahul = User::factory()->create([
            'name'  => 'Rahul Roy',
            'email' => 'admin@example.com',
            'role'  => UserRole::Admin,
        ]);

        $adminPreeti = User::factory()->create([
            'name'  => 'Preeti Sharma',
            'email' => 'preeti@example.com',
            'role'  => UserRole::Admin,
        ]);

        // ── Citizens ────────────────────────────────────
        $ravi = User::factory()->create([
            'name'  => 'Ravi Kumar',
            'email' => 'citizen@example.com',
            'role'  => UserRole::Citizen,
        ]);

        $anita = User::factory()->create([
            'name'  => 'Anita Devi',
            'email' => 'anita@example.com',
            'role'  => UserRole::Citizen,
        ]);

        $suresh = User::factory()->create([
            'name'  => 'Suresh Patel',
            'email' => 'suresh@example.com',
            'role'  => UserRole::Citizen,
        ]);

        $meena = User::factory()->create([
            'name'  => 'Meena Iyer',
            'email' => 'meena@example.com',
            'role'  => UserRole::Citizen,
        ]);

        // ── Ravi's complaints ───────────────────────────
        $c1 = Complaint::create([
            'title'       => 'Broken streetlight on MG Road',
            'description' => 'The streetlight near MG Road crossing has been out for 2 weeks. Pedestrians are at risk at night, especially near the school zone.',
            'status'      => ComplaintStatus::Resolved,
            'user_id'     => $ravi->id,
            'assigned_to' => $adminRahul->id,
        ]);
        Comment::create(['complaint_id' => $c1->id, 'user_id' => $adminRahul->id, 'body' => 'Municipal team dispatched. Streetlight repaired on 15th April.']);
        Comment::create(['complaint_id' => $c1->id, 'user_id' => $ravi->id, 'body' => 'Confirmed. The light is working now. Thank you!']);

        $c2 = Complaint::create([
            'title'       => 'Garbage not collected for 5 days',
            'description' => 'Ward 12, Block C — garbage bins are overflowing. The collection truck has not visited since Monday. Strong smell and health hazard.',
            'status'      => ComplaintStatus::InProgress,
            'user_id'     => $ravi->id,
            'assigned_to' => $adminPreeti->id,
        ]);
        Comment::create(['complaint_id' => $c2->id, 'user_id' => $adminPreeti->id, 'body' => 'We have notified the sanitation department. Collection scheduled for tomorrow morning.']);

        Complaint::create([
            'title'       => 'Pothole on NH-44 near petrol pump',
            'description' => 'Large pothole on the left lane of NH-44 near Indian Oil petrol pump. Caused a bike accident last week. Needs urgent repair.',
            'status'      => ComplaintStatus::Open,
            'user_id'     => $ravi->id,
        ]);

        // ── Anita's complaints ──────────────────────────
        $c4 = Complaint::create([
            'title'       => 'Water supply disrupted in Sector 7',
            'description' => 'No water supply since 3 days in Sector 7, Block A. We are buying tanker water which is expensive. Please restore the main pipeline.',
            'status'      => ComplaintStatus::InProgress,
            'user_id'     => $anita->id,
            'assigned_to' => $adminRahul->id,
        ]);
        Comment::create(['complaint_id' => $c4->id, 'user_id' => $adminRahul->id, 'body' => 'Pipeline repair team sent to Sector 7. Estimated 24 hours for restoration.']);
        Comment::create(['complaint_id' => $c4->id, 'user_id' => $anita->id, 'body' => 'It has been 2 days since your update. Still no water. Please expedite.']);

        Complaint::create([
            'title'       => 'Stray dog menace near Government School',
            'description' => 'Pack of 8-10 stray dogs near Government Primary School, Sector 3. Children are scared to walk to school. Dog bites reported twice this month.',
            'status'      => ComplaintStatus::Open,
            'user_id'     => $anita->id,
        ]);

        Complaint::create([
            'title'       => 'Illegal construction blocking drainage',
            'description' => 'A new construction on Plot 45, Lane 3 has blocked the main drainage line. During last rain, the entire street was flooded knee-deep.',
            'status'      => ComplaintStatus::Rejected,
            'user_id'     => $anita->id,
            'assigned_to' => $adminPreeti->id,
        ]);

        // ── Suresh's complaints ─────────────────────────
        Complaint::create([
            'title'       => 'Traffic signal not working at City Center',
            'description' => 'The traffic signal at City Center junction has been blinking yellow for 3 days. There have been 2 minor accidents. Traffic police are not present during peak hours.',
            'status'      => ComplaintStatus::Open,
            'user_id'     => $suresh->id,
        ]);

        $c8 = Complaint::create([
            'title'       => 'Public toilet in park is unmaintained',
            'description' => 'The public toilet in Central Park is in terrible condition. No water, broken locks, extremely dirty. Visitors avoid the park because of this.',
            'status'      => ComplaintStatus::Resolved,
            'user_id'     => $suresh->id,
            'assigned_to' => $adminPreeti->id,
        ]);
        Comment::create(['complaint_id' => $c8->id, 'user_id' => $adminPreeti->id, 'body' => 'Maintenance team cleaned and repaired the facility. New locks installed.']);

        Complaint::create([
            'title'       => 'Noise pollution from factory at night',
            'description' => 'The textile factory on Industrial Road operates heavy machinery from 10 PM to 6 AM. The noise makes it impossible to sleep. Violates noise pollution norms.',
            'status'      => ComplaintStatus::Open,
            'user_id'     => $suresh->id,
        ]);

        // ── Meena's complaints ──────────────────────────
        Complaint::create([
            'title'       => 'Pension payment delayed by 3 months',
            'description' => 'My mother (age 72) has not received her old-age pension for 3 months. Account number: XXXXX4521. We have visited the district office twice with no resolution.',
            'status'      => ComplaintStatus::Open,
            'user_id'     => $meena->id,
        ]);

        Complaint::create([
            'title'       => 'Ration card application pending since January',
            'description' => 'Applied for a new ration card on 10th January (Application ID: RC-2026-4478). Status shows "Under Review" for 3 months. No one at the office can give an update.',
            'status'      => ComplaintStatus::InProgress,
            'user_id'     => $meena->id,
            'assigned_to' => $adminRahul->id,
        ]);

        $c12 = Complaint::create([
            'title'       => 'Road flooded due to blocked drain',
            'description' => 'The main road in Ward 5 near the temple is completely flooded after even light rain. The drain is blocked with construction debris. Two-wheelers cannot pass.',
            'status'      => ComplaintStatus::Resolved,
            'user_id'     => $meena->id,
            'assigned_to' => $adminPreeti->id,
        ]);
        Comment::create(['complaint_id' => $c12->id, 'user_id' => $adminPreeti->id, 'body' => 'Drain cleared by public works department. Road is now passable.']);
        Comment::create(['complaint_id' => $c12->id, 'user_id' => $meena->id, 'body' => 'Thank you. The road is much better now after yesterday\'s rain.']);
    }
}
