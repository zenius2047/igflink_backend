<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registered_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->string('owner_name', 150);
            $table->string('registration_number', 32);
            $table->string('vehicle_type', 100);
            $table->string('phone', 32)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('make', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->decimal('monthly_levy', 10, 2)->nullable();
            $table->string('status')->default('active');
            $table->string('compliance_status')->default('compliant');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registered_vehicles');
    }
};
