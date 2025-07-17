<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrations.
     */
    public function up(): void
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->char('vehiclePlate', 7);
            $table->dateTime('entryDateTime');
            $table->dateTime('exitDateTime')->default('0001-01-01 00:00:00');
            $table->string('priceType', 55)->nullable();
            $table->decimal('price', 12, 2)->default(0.00);
            $table->unsignedBigInteger('userId');
            
            // Chave estrangeira
            $table->foreign('userId')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Índices
            $table->index('vehiclePlate');
            $table->index('entryDateTime');
            $table->index('userId');
        });
    }

    /**
     * Reverte as migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
