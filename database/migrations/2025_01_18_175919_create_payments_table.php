<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->date('payment_date');
            $table->enum("payment_type", ["monthly", "one_time"])->default("monthly");
            $table->enum("month", ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"])->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->foreignId('collected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
