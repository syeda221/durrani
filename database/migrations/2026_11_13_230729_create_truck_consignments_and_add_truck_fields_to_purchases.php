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
        if (!Schema::hasTable('truck_consignments')) {
            Schema::create('truck_consignments', function (Blueprint $table) {
                $table->id();
                $table->string('consignment_no')->unique(); // e.g. TRK-202611-001
                $table->string('truck_no');                // Truck / Vehicle Registration No
                $table->string('driver_name')->nullable();
                $table->string('driver_phone')->nullable();
                $table->string('transporter_name')->nullable(); // Goods forwarding / transport company
                $table->string('bilty_no')->nullable();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->date('arrival_date')->nullable();
                $table->decimal('total_freight', 12, 2)->default(0); // Truck Kiraya
                $table->string('status')->default('received');       // received, draft, completed
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('warehouse_id');
                $table->index('truck_no');
            });
        }

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'truck_consignment_id')) {
                $table->unsignedBigInteger('truck_consignment_id')->nullable()->after('vendor_id');
                $table->index('truck_consignment_id');
            }
            if (!Schema::hasColumn('purchases', 'truck_no')) {
                $table->string('truck_no')->nullable()->after('vendor_bill_no');
            }
            if (!Schema::hasColumn('purchases', 'driver_name')) {
                $table->string('driver_name')->nullable()->after('truck_no');
            }
            if (!Schema::hasColumn('purchases', 'driver_phone')) {
                $table->string('driver_phone')->nullable()->after('driver_name');
            }
            if (!Schema::hasColumn('purchases', 'transporter_name')) {
                $table->string('transporter_name')->nullable()->after('driver_phone');
            }
            if (!Schema::hasColumn('purchases', 'bilty_no')) {
                $table->string('bilty_no')->nullable()->after('transporter_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['truck_consignment_id', 'truck_no', 'driver_name', 'driver_phone', 'transporter_name', 'bilty_no'] as $col) {
                if (Schema::hasColumn('purchases', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::dropIfExists('truck_consignments');
    }
};
