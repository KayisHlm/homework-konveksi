<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->nullable()->after('customer_id');
            $table->string('material')->nullable()->after('product_name');
            $table->unsignedInteger('quantity')->default(0)->after('material');
            $table->text('size_details')->nullable()->after('quantity');
            $table->decimal('unit_price', 12, 2)->default(0)->after('size_details');
            $table->decimal('total_price', 12, 2)->default(0)->after('unit_price');
            $table->decimal('down_payment', 12, 2)->default(0)->after('total_price');
            $table->date('due_date')->nullable()->after('down_payment');
            $table->string('production_status')->default('pesanan_diterima')->after('due_date');
            $table->text('notes')->nullable()->after('production_status');
        });

        $orders = DB::table('orders')->select('id', 'created_at')->orderBy('id')->get();

        foreach ($orders as $order) {
            $createdAt = $order->created_at ? \Carbon\Carbon::parse($order->created_at) : now();

            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'order_number' => sprintf('ORD-%s-%04d', $createdAt->format('ym'), $order->id),
                ]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('order_number');
            $table->dropColumn(['name', 'email', 'phone', 'address']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->dropUnique(['order_number']);
            $table->dropColumn([
                'order_number',
                'material',
                'quantity',
                'size_details',
                'unit_price',
                'total_price',
                'down_payment',
                'due_date',
                'production_status',
                'notes',
            ]);
        });
    }
};
