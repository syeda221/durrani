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
        // 1. Truck Consignment Items Table
        if (!Schema::hasTable('truck_consignment_items')) {
            Schema::create('truck_consignment_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('truck_consignment_id');
                $table->unsignedBigInteger('purchase_id')->nullable();
                $table->unsignedBigInteger('vendor_id');
                $table->unsignedBigInteger('product_id');
                $table->string('unit')->default('Pcs');
                $table->decimal('loaded_qty', 12, 3)->default(0);
                $table->decimal('purchase_price', 12, 2)->default(0);
                $table->decimal('sold_qty', 12, 3)->default(0);
                $table->decimal('unloaded_qty', 12, 3)->default(0);
                $table->decimal('remaining_qty', 12, 3)->default(0);
                $table->timestamps();

                $table->index('truck_consignment_id');
                $table->index('purchase_id');
                $table->index('vendor_id');
                $table->index('product_id');
            });
        }

        // 2. Add fields to truck_consignments
        Schema::table('truck_consignments', function (Blueprint $table) {
            if (!Schema::hasColumn('truck_consignments', 'unloaded_at')) {
                $table->timestamp('unloaded_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('truck_consignments', 'unloaded_warehouse_id')) {
                $table->unsignedBigInteger('unloaded_warehouse_id')->nullable()->after('unloaded_at');
            }
        });

        // 3. Add fields to sales table
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'truck_consignment_id')) {
                $table->unsignedBigInteger('truck_consignment_id')->nullable()->after('warehouse_id');
                $table->index('truck_consignment_id');
            }
        });

        // 4. Add fields to sale_items table
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'truck_consignment_id')) {
                $table->unsignedBigInteger('truck_consignment_id')->nullable()->after('warehouse_id');
                $table->index('truck_consignment_id');
            }
            if (!Schema::hasColumn('sale_items', 'truck_consignment_item_id')) {
                $table->unsignedBigInteger('truck_consignment_item_id')->nullable()->after('truck_consignment_id');
                $table->index('truck_consignment_item_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (Schema::hasColumn('sale_items', 'truck_consignment_item_id')) {
                $table->dropColumn('truck_consignment_item_id');
            }
            if (Schema::hasColumn('sale_items', 'truck_consignment_id')) {
                $table->dropColumn('truck_consignment_id');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'truck_consignment_id')) {
                $table->dropColumn('truck_consignment_id');
            }
        });

        Schema::table('truck_consignments', function (Blueprint $table) {
            if (Schema::hasColumn('truck_consignments', 'unloaded_warehouse_id')) {
                $table->dropColumn('unloaded_warehouse_id');
            }
            if (Schema::hasColumn('truck_consignments', 'unloaded_at')) {
                $table->dropColumn('unloaded_at');
            }
        });

        Schema::dropIfExists('truck_consignment_items');
    }
};
