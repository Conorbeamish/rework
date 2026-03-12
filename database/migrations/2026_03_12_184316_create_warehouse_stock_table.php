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
        Schema::create('warehouse_stock', function (Blueprint $table) {
            $table->uuid('warehouse_uuid');
            $table->uuid('product_uuid');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('threshold')->default(0);
            $table->timestamps();

            $table->primary(['warehouse_uuid', 'product_uuid']);

            $table->foreign('warehouse_uuid')
                ->references('uuid')
                ->on('warehouses')
                ->onDelete('cascade');

            $table->foreign('product_uuid')
                ->references('uuid')
                ->on('products')
                ->onDelete('cascade');

            $table->index('product_uuid');
            $table->index('warehouse_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_stock');
    }
};
