<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->string('admission_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('class_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_parent', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('relationship', 30)->nullable();
            $table->unique(['student_id', 'parent_id']);
            $table->timestamps();
        });

        Schema::create('routes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('start_location')->nullable();
            $table->string('end_location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('buses', function (Blueprint $table): void {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('device_id')->unique();
            $table->string('device_token_hash');
            $table->unsignedSmallInteger('capacity')->default(30);
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bus_route', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->date('service_date')->nullable();
            $table->timestamps();
            $table->index(['bus_id', 'route_id']);
        });

        Schema::create('bus_stops', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('stop_order');
            $table->timestamps();
            $table->unique(['route_id', 'stop_order']);
        });

        Schema::create('student_bus', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bus_stop_id')->nullable()->constrained('bus_stops')->nullOnDelete();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'bus_id']);
        });

        Schema::create('gps_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('speed_kmh', 7, 2)->nullable();
            $table->decimal('heading', 6, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['bus_id', 'recorded_at']);
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('gps_locations');
        Schema::dropIfExists('student_bus');
        Schema::dropIfExists('bus_stops');
        Schema::dropIfExists('bus_route');
        Schema::dropIfExists('buses');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('student_parent');
        Schema::dropIfExists('students');
    }
};
