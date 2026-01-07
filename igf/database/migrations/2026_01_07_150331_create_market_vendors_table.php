<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('market_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('stall_name');     
            $table->string('vendor_name');        
            $table->string('phone');              
            $table->string('market_location');  
            $table->string('community')->nullable();  
            $table->decimal('daily_fees_collected', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_vendors');
    }
};