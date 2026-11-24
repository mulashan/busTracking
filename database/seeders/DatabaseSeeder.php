<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusStop;
use App\Models\Route;
use App\Models\Student;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@school.test',
        ], [
            'name' => 'Transport Admin',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $parent = User::updateOrCreate([
            'email' => 'parent@school.test',
        ], [
            'name' => 'Demo Parent',
            'password' => 'password',
            'role' => 'parent',
        ]);

        $driver = User::updateOrCreate([
            'email' => 'driver@school.test',
        ], [
            'name' => 'Demo Driver',
            'password' => 'password',
            'role' => 'driver',
        ]);

        $route = Route::updateOrCreate([
            'name' => 'Morning Route A',
        ], [
            'start_location' => 'School Gate',
            'end_location' => 'Njiro',
            'is_active' => true,
        ]);

        $stop = BusStop::updateOrCreate([
            'route_id' => $route->id,
            'stop_order' => 1,
        ], [
            'name' => 'Njiro Main Stop',
            'latitude' => -3.3869000,
            'longitude' => 36.6830000,
        ]);

        $deviceToken = env('DEMO_DEVICE_TOKEN', 'test-device-token');

        $bus = Bus::updateOrCreate([
            'registration_number' => 'T123 ABC',
        ], [
            'device_id' => 'BUS-001',
            'device_token_hash' => Hash::make($deviceToken),
            'capacity' => 30,
            'driver_id' => $driver->id,
            'is_active' => true,
        ]);

        $bus->routes()->syncWithoutDetaching([$route->id]);

        $student = Student::updateOrCreate([
            'admission_number' => 'STD-001',
        ], [
            'first_name' => 'Amina',
            'last_name' => 'Mushi',
            'class_name' => 'Grade 3',
            'is_active' => true,
        ]);

        $student->parents()->syncWithoutDetaching([$parent->id => ['relationship' => 'mother']]);
        $student->buses()->syncWithoutDetaching([$bus->id => ['bus_stop_id' => $stop->id]]);

        Trip::updateOrCreate([
            'bus_id' => $bus->id,
            'route_id' => $route->id,
            'status' => 'active',
        ], [
            'started_at' => now()->subMinutes(18),
            'predicted_eta_minutes' => 45,
            'students_picked' => 12,
            'students_remaining' => 18,
            'distance_km' => 11.6,
            'notes' => 'Morning pickup in progress.',
        ]);

        Trip::updateOrCreate([
            'bus_id' => $bus->id,
            'route_id' => $route->id,
            'status' => 'completed',
        ], [
            'scheduled_start_at' => now()->subDay()->setTime(7, 0),
            'started_at' => now()->subDay()->setTime(7, 4),
            'completed_at' => now()->subDay()->setTime(7, 48),
            'predicted_eta_minutes' => 45,
            'actual_duration_minutes' => 44,
            'eta_variance_minutes' => -1,
            'students_picked' => 30,
            'students_remaining' => 0,
            'distance_km' => 11.8,
            'notes' => 'Arrived within ETA target.',
        ]);

        $this->command?->info('Demo admin: admin@school.test');
        $this->command?->info('Demo IoT device: BUS-001');
        $this->command?->info('Set DEMO_DEVICE_TOKEN in .env before db:seed to control the test token.');
    }
}
