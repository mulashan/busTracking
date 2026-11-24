<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SchoolBusSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('users')->ignore(true)->insertBatch([
            ['name' => 'Transport Admin', 'email' => 'admin@school.test', 'password' => password_hash('password', PASSWORD_DEFAULT), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Demo Parent', 'email' => 'parent@school.test', 'password' => password_hash('password', PASSWORD_DEFAULT), 'role' => 'parent', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Demo Driver', 'email' => 'driver@school.test', 'password' => password_hash('password', PASSWORD_DEFAULT), 'role' => 'driver', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $driver = $this->db->table('users')->where('email', 'driver@school.test')->get()->getRowArray();
        $this->db->table('routes')->ignore(true)->insert(['name' => 'Morning Route A', 'start_location' => 'School Gate', 'end_location' => 'Njiro', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        $route = $this->db->table('routes')->where('name', 'Morning Route A')->get()->getRowArray();
        $this->db->table('buses')->ignore(true)->insert(['registration_number' => 'T123 ABC', 'device_id' => 'BUS-001', 'device_token_hash' => password_hash(env('DEMO_DEVICE_TOKEN', 'test-device-token'), PASSWORD_DEFAULT), 'capacity' => 30, 'driver_id' => $driver['id'], 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]);
        $bus = $this->db->table('buses')->where('device_id', 'BUS-001')->get()->getRowArray();
        $this->db->table('bus_route')->ignore(true)->insert(['bus_id' => $bus['id'], 'route_id' => $route['id'], 'created_at' => $now, 'updated_at' => $now]);
        $this->db->table('bus_stops')->ignore(true)->insert(['route_id' => $route['id'], 'name' => 'Njiro Main Stop', 'latitude' => -3.3869, 'longitude' => 36.683, 'stop_order' => 1, 'created_at' => $now, 'updated_at' => $now]);
    }
}
