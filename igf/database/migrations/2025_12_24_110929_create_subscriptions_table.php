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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // assigned district id
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('package_id');
            $table->string('payment_method');
            $table->string('reference_number')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
