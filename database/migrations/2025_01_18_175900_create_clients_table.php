<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->unique();
            $table->string('username');
            $table->enum("status", ["due", "paid"])->default("due");
            $table->string('phone_number');
            $table->text('address');
            $table->unsignedBigInteger('package_id');
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->decimal('due', 10, 2)->default(0);
            $table->decimal('bill_amount', 10, 2);

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('disabled')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
