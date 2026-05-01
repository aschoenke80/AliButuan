<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Default accounts ────────────────────────────────────────────
        $admin = User::create([
            'name'         => 'Admin AliButuan',
            'email'        => 'admin@alibutuan.com',
            'password'     => Hash::make('password'),
            'role'         => 'admin',
            'phone_number' => '+63 900 000 0001',
        ]);

        $organizer = User::create([
            'name'         => 'Sample Organizer',
            'email'        => 'organizer@alibutuan.com',
            'password'     => Hash::make('password'),
            'role'         => 'organizer',
            'phone_number' => '+63 900 000 0002',
        ]);

        User::create([
            'name'         => 'Test User',
            'email'        => 'user@alibutuan.com',
            'password'     => Hash::make('password'),
            'role'         => 'user',
            'phone_number' => '+63 900 000 0003',
        ]);

        // ── Sample events ────────────────────────────────────────────────
        $events = [
            [
                'title'          => 'Butuan City Fiesta 2026',
                'description'    => 'Join the biggest fiesta celebration in Butuan City! Featuring live music, street food, cultural performances, and a grand parade through the city center.',
                'category'       => 'Festival',
                'audience'       => 'All Ages',
                'location_name'  => 'Robinsons Place Butuan, Km. 4',
                'latitude'       => 8.9490,
                'longitude'      => 125.5420,
                'start_datetime' => now()->addDays(10)->setTime(8, 0),
                'end_datetime'   => now()->addDays(10)->setTime(22, 0),
                'status'         => 'approved',
                'is_featured'    => true,
            ],
            [
                'title'          => 'Agusan River Farmers Market',
                'description'    => 'Fresh produce, native delicacies, and handcrafted goods by local farmers and artisans from Caraga region. Support local!',
                'category'       => 'Market',
                'audience'       => 'All Ages',
                'location_name'  => 'Agusan River Riverbank Park',
                'latitude'       => 8.9475,
                'longitude'      => 125.5406,
                'start_datetime' => now()->addDays(5)->setTime(6, 0),
                'end_datetime'   => now()->addDays(5)->setTime(12, 0),
                'status'         => 'approved',
                'is_featured'    => true,
            ],
            [
                'title'          => 'OPM Live Concert Night',
                'description'    => 'A night of Original Pilipino Music featuring local Butuanon artists and regional bands.',
                'category'       => 'Concert',
                'audience'       => 'Adults',
                'location_name'  => 'SM City Butuan Events Center',
                'latitude'       => 8.9510,
                'longitude'      => 125.5380,
                'start_datetime' => now()->addDays(15)->setTime(18, 0),
                'end_datetime'   => now()->addDays(15)->setTime(23, 0),
                'status'         => 'approved',
                'is_featured'    => false,
            ],
            [
                'title'          => 'Butuan City Fun Run 2026',
                'description'    => '5K and 10K fun run for all fitness levels. Proceeds go to local school feeding programs.',
                'category'       => 'Sports',
                'audience'       => 'All Ages',
                'location_name'  => 'Plaza Rizal, Butuan City',
                'latitude'       => 8.9460,
                'longitude'      => 125.5400,
                'start_datetime' => now()->addDays(20)->setTime(5, 0),
                'end_datetime'   => now()->addDays(20)->setTime(9, 0),
                'status'         => 'approved',
                'is_featured'    => false,
            ],
            [
                'title'          => 'Caraga Food Festival',
                'description'    => 'Taste the best dishes from Caraga region — from sinuglaw and kinilaw to crispy pata and nilaga.',
                'category'       => 'Food',
                'audience'       => 'All Ages',
                'location_name'  => 'Butuan City Hall Grounds',
                'latitude'       => 8.9480,
                'longitude'      => 125.5410,
                'start_datetime' => now()->addDays(3)->setTime(10, 0),
                'end_datetime'   => now()->addDays(3)->setTime(20, 0),
                'status'         => 'approved',
                'is_featured'    => true,
            ],
            [
                'title'          => 'Youth Leadership Summit 2026',
                'description'    => 'A one-day summit for students and young professionals featuring keynote speakers and workshops.',
                'category'       => 'General',
                'audience'       => 'Students',
                'location_name'  => 'Agusan del Norte Capitol',
                'latitude'       => 8.9500,
                'longitude'      => 125.5390,
                'start_datetime' => now()->addDays(25)->setTime(8, 0),
                'end_datetime'   => now()->addDays(25)->setTime(17, 0),
                'status'         => 'pending',
                'is_featured'    => false,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create(array_merge($eventData, ['organizer_id' => $organizer->id]));
        }
    }
}
