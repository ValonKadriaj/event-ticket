<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Venue;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create some admin users
        $admin1 = User::factory()->create([
            'name' => 'Admin One',
            'email' => 'admin1@example.com',
            'password' => Hash::make('password'),
            'admin' => 1

            // Add an 'is_admin' field if you have one
        ]);

        $admin2 = User::factory()->create([
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'admin' => 1
            // Add an 'is_admin' field if you have one
        ]);

        $admins = collect([$admin1, $admin2]);

        // Create some regular users
        $users = User::factory(10)->create();

        // Create some venues
        $venues = collect();
        for ($i = 0; $i < 5; $i++) {
            $venues->push(Venue::create([
                'name' => $faker->company . ' Hall',
                'location' => $faker->address,
                'capacity' => $faker->numberBetween(50, 500),
            ]));
        }

        // Create some events
        $events = collect();
        for ($i = 0; $i < 15; $i++) {
            $startDate = Carbon::now()->addDays($faker->numberBetween(1, 30));
            $endDate = $startDate->copy()->addHours($faker->numberBetween(2, 5));
            $events->push(Event::create([
                'name' => $faker->sentence(3),
                'venue_id' => $venues->random()->id,
                'admin_id' => $admins->random()->id,
                'start_time' => $startDate,
                'end_time' => $endDate,
                'description' => $faker->paragraph,
                'category' => $faker->randomElement(['Music', 'Sports', 'Theater', 'Comedy', 'Seminar']), // Example category
                'price' => $faker->randomFloat(2, 10, 100), // Example price
                // Add other relevant event fields
            ]));
        }

        // Create some tickets for the events
        foreach ($events as $event) {
            $numTickets = $faker->numberBetween(10, min(50, $event->venue->capacity ?? 50)); // Don't create more tickets than capacity
            for ($i = 0; $i < $numTickets; $i++) {
                Ticket::create([
                    'user_id' => $users->random()->id,
                    'event_id' => $event->id,
                    'price' => $event->price,
                    'seat_info' => $event->venue->capacity ? 'Seat ' . $faker->numberBetween(1, $event->venue->capacity) : null, // Optional seat info
                    'booking_time' => $faker->dateTimeBetween($event->start_time->subDays(7), $event->start_time->subHours(1)),
                ]);
            }

            // Create a few tickets for a specific user for testing
            $specificUser = $users->first();
            for ($i = 0; $i < 3; $i++) {
                Ticket::create([
                    'user_id' => $specificUser->id,
                    'event_id' => $event->id,
                    'price' => $event->price,
                    'seat_info' => $event->venue->capacity ? 'VIP Seat ' . ($i + 1) : null,
                    'booking_time' => now()->subDays($faker->numberBetween(1, 5)),
                ]);
            }
        }


    }
}
