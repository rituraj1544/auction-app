<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            if (! Schema::hasColumn('auctions', 'condition')) {
                $table->string('condition')->default('Excellent')->after('image_path');
            }

            if (! Schema::hasColumn('auctions', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('condition');
            }

            if (! Schema::hasColumn('auctions', 'specifications')) {
                $table->json('specifications')->nullable()->after('gallery_images');
            }

            if (! Schema::hasColumn('auctions', 'shipping_details')) {
                $table->string('shipping_details')->nullable()->after('specifications');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            foreach (['condition', 'gallery_images', 'specifications', 'shipping_details'] as $column) {
                if (Schema::hasColumn('auctions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
