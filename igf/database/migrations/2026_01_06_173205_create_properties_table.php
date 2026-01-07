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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();//this auto generates a unique id for each property

            //i took these from the property rate form fields
            $table->string('address');
            $table->string('owner_name');
            $table->decimal('rate_amount', 10, 2);
            $table->string('owner_phone')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('zone');
            $table->string('district');
            $table->string('community');
            
            //system fields
            $table->string('registration_number')->unique();
            $table->string('status')->default('unpaid');
            $table->date('due_date')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
