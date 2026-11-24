<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolBusTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'email' => ['type' => 'VARCHAR', 'constraint' => 160],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'parent'],
            'created_at DATETIME NULL',
            'updated_at DATETIME NULL',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('role');
        $this->forge->createTable('users');

        $this->createCoreTables();
    }

    public function down(): void
    {
        foreach (['notifications', 'gps_locations', 'trips', 'student_bus', 'bus_stops', 'bus_route', 'buses', 'routes', 'student_parent', 'students', 'users'] as $table) {
            $this->forge->dropTable($table, true);
        }
    }

    private function createCoreTables(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'admission_number' => ['type' => 'VARCHAR', 'constraint' => 80],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 80],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => 80],
            'class_name' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at DATETIME NULL', 'updated_at DATETIME NULL',
        ]);
        $this->forge->addKey('id', true); $this->forge->addUniqueKey('admission_number'); $this->forge->createTable('students');

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'start_location' => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'end_location' => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at DATETIME NULL', 'updated_at DATETIME NULL',
        ]);
        $this->forge->addKey('id', true); $this->forge->createTable('routes');

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'registration_number' => ['type' => 'VARCHAR', 'constraint' => 50],
            'device_id' => ['type' => 'VARCHAR', 'constraint' => 80],
            'device_token_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'capacity' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 30],
            'driver_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at DATETIME NULL', 'updated_at DATETIME NULL',
        ]);
        $this->forge->addKey('id', true); $this->forge->addUniqueKey('registration_number'); $this->forge->addUniqueKey('device_id'); $this->forge->addForeignKey('driver_id', 'users', 'id', 'SET NULL', 'CASCADE'); $this->forge->createTable('buses');

        $this->createRelationshipTables();
    }

    private function createRelationshipTables(): void
    {
        $tables = [
            'student_parent' => ['student_id', 'parent_id'],
            'bus_route' => ['bus_id', 'route_id'],
        ];
        foreach ($tables as $table => $columns) {
            $this->forge->addField(['id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true], $columns[0] => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true], $columns[1] => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true], 'created_at DATETIME NULL', 'updated_at DATETIME NULL']);
            if ($table === 'student_parent') { $this->forge->addField(['relationship' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true]]); }
            if ($table === 'bus_route') { $this->forge->addField(['service_date' => ['type' => 'DATE', 'null' => true]]); }
            $this->forge->addKey('id', true); $this->forge->addKey($columns); $this->forge->addForeignKey($columns[0], $columns[0] === 'student_id' ? 'students' : 'buses', 'id', 'CASCADE', 'CASCADE'); $this->forge->addForeignKey($columns[1], $columns[1] === 'parent_id' ? 'users' : 'routes', 'id', 'CASCADE', 'CASCADE'); $this->forge->createTable($table);
        }

        $this->forge->addField(['id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true], 'route_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true], 'name' => ['type' => 'VARCHAR', 'constraint' => 120], 'latitude' => ['type' => 'DECIMAL', 'constraint' => '10,7'], 'longitude' => ['type' => 'DECIMAL', 'constraint' => '10,7'], 'stop_order' => ['type' => 'INT', 'unsigned' => true], 'created_at DATETIME NULL', 'updated_at DATETIME NULL']);
        $this->forge->addKey('id', true); $this->forge->addUniqueKey(['route_id', 'stop_order']); $this->forge->addForeignKey('route_id', 'routes', 'id', 'CASCADE', 'CASCADE'); $this->forge->createTable('bus_stops');

        $this->forge->addField(['id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true], 'student_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true], 'bus_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true], 'bus_stop_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true], 'starts_on' => ['type' => 'DATE', 'null' => true], 'ends_on' => ['type' => 'DATE', 'null' => true], 'created_at DATETIME NULL', 'updated_at DATETIME NULL']);
        $this->forge->addKey('id', true); $this->forge->addKey(['student_id', 'bus_id']); $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE'); $this->forge->addForeignKey('bus_id', 'buses', 'id', 'CASCADE', 'CASCADE'); $this->forge->addForeignKey('bus_stop_id', 'bus_stops', 'id', 'SET NULL', 'CASCADE'); $this->forge->createTable('student_bus');

        $this->createTrackingTables();
    }

    private function createTrackingTables(): void
    {
        $this->forge->addField(['id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true], 'bus_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true], 'latitude' => ['type' => 'DECIMAL', 'constraint' => '10,7'], 'longitude' => ['type' => 'DECIMAL', 'constraint' => '10,7'], 'speed_kmh' => ['type' => 'DECIMAL', 'constraint' => '7,2', 'null' => true], 'heading' => ['type' => 'DECIMAL', 'constraint' => '6,2', 'null' => true], 'recorded_at' => ['type' => 'DATETIME'], 'created_at DATETIME NULL', 'updated_at DATETIME NULL']);
        $this->forge->addKey('id', true); $this->forge->addKey(['bus_id', 'recorded_at']); $this->forge->addForeignKey('bus_id', 'buses', 'id', 'CASCADE', 'CASCADE'); $this->forge->createTable('gps_locations');

        $this->forge->addField(['id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true], 'bus_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true], 'route_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true], 'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'scheduled'], 'scheduled_start_at' => ['type' => 'DATETIME', 'null' => true], 'started_at' => ['type' => 'DATETIME', 'null' => true], 'completed_at' => ['type' => 'DATETIME', 'null' => true], 'predicted_eta_minutes' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true], 'actual_duration_minutes' => ['type' => 'SMALLINT', 'unsigned' => true, 'null' => true], 'eta_variance_minutes' => ['type' => 'INT', 'null' => true], 'students_picked' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0], 'students_remaining' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0], 'distance_km' => ['type' => 'DECIMAL', 'constraint' => '7,2', 'null' => true], 'notes' => ['type' => 'TEXT', 'null' => true], 'created_at DATETIME NULL', 'updated_at DATETIME NULL']);
        $this->forge->addKey('id', true); $this->forge->addKey(['bus_id', 'status']); $this->forge->addKey(['route_id', 'status']); $this->forge->addForeignKey('bus_id', 'buses', 'id', 'CASCADE', 'CASCADE'); $this->forge->addForeignKey('route_id', 'routes', 'id', 'SET NULL', 'CASCADE'); $this->forge->createTable('trips');

        $this->forge->addField(['id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true], 'user_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true], 'student_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true], 'bus_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true], 'type' => ['type' => 'VARCHAR', 'constraint' => 50], 'title' => ['type' => 'VARCHAR', 'constraint' => 255], 'body' => ['type' => 'TEXT'], 'data' => ['type' => 'JSON', 'null' => true], 'read_at' => ['type' => 'DATETIME', 'null' => true], 'sent_at' => ['type' => 'DATETIME', 'null' => true], 'created_at DATETIME NULL', 'updated_at DATETIME NULL']);
        $this->forge->addKey('id', true); $this->forge->addKey(['user_id', 'read_at']); $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE'); $this->forge->addForeignKey('student_id', 'students', 'id', 'SET NULL', 'CASCADE'); $this->forge->addForeignKey('bus_id', 'buses', 'id', 'SET NULL', 'CASCADE'); $this->forge->createTable('notifications');
    }
}
