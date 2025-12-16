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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // SERIAL PRIMARY KEY
            $table->string('full_name', 150);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('staff_id', 80)->unique();
            $table->string('phone', 32)->nullable();
            $table->string('role', 50)->nullable();// FK to districts.id
            $table->string('department', 100)->nullable();
            $table->string('otp', 10)->nullable();
            $table->timestamp('otp_created_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('avatar', 255)->nullable();
            $table->timestamps();
            $table->boolean('is_active')->default(true);
        });


        Schema::create('password_reset_otp', function (Blueprint $table) {
            $table->string('phone')->primary();
            $table->string('otp');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
