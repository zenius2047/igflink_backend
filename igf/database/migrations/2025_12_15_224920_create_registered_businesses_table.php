<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registered_businesses', function (Blueprint $table) {
            $table->id(); // Business ID (SERIAL PK)

            $table->foreignId('district_id')
                  ->constrained('districts')
                  ->cascadeOnDelete(); // District

            $table->string('business_name', 200);
            $table->string('owner_name', 150);
            $table->string('phone', 32);
            $table->text('location');
            $table->string('category', 100);

            $table->foreignId('qr_code_id')
                  ->nullable()
                  ->constrained('qr_codes')
                  ->nullOnDelete(); // Linked QR code

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registered_businesses');
    }
};
