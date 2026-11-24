<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bus_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('scheduled')->index();
            $table->timestamp('scheduled_start_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('predicted_eta_minutes')->nullable();
            $table->unsignedSmallInteger('actual_duration_minutes')->nullable();
            $table->integer('eta_variance_minutes')->nullable();
            $table->unsignedSmallInteger('students_picked')->default(0);
            $table->unsignedSmallInteger('students_remaining')->default(0);
            $table->decimal('distance_km', 7, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['bus_id', 'status']);
            $table->index(['route_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
