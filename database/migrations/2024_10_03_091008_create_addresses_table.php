<?php

use App\Models\Country;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('type', 45);
            $table->string('address1', 255);
            $table->string('address2', 255);
            $table->string('city', 255);
            $table->string('state', 45)->nullable();
            $table->string('zipcode', 45);
            $table->string('country_code', 25);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');      
            $table->foreignIdFor(Country::class, 'country_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
