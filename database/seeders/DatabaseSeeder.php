<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Planner',
            'email' => 'planner@example.com',
            'phone' => '010-0000000',
            'role' => 'planner',
            'password' => 'password',
        ]);

        User::factory(10)->create(['role' => 'medewerker']);

        foreach (range(0, 13) as $day) {
            $date = Carbon::today()->addDays($day);
            Shift::factory()->create([
                'title' => 'Dienst '.($day + 1),
                'department' => $day % 2 === 0 ? 'Logistiek' : 'Klantenservice',
                'scheduled_date' => $date,
                'begin_time' => '09:00',
                'end_time' => '17:00',
                'status' => 'published',
            ]);
        }
    }
}
