<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->string('size', 20);
            $table->unsignedInteger('quantity');
            // Tambahan harga per pcs untuk ukuran tertentu (mis. XXL).
            $table->decimal('extra_price', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['order_item_id', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_sizes');
    }
};
