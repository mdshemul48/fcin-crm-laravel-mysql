<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_bills', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->date('generated_date');
            $table->enum("bill_type", ["monthly", "one_time"])->default("monthly");
            $table->string('remarks')->nullable();
            $table->enum("month", ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December", "other"])->nullable();
            $table->timestamps();

            $table->foreignId('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_bills');
    }
};
