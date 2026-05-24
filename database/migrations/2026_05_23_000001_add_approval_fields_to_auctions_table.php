<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            if (! Schema::hasColumn('auctions', 'approval_status')) {
                $table->string('approval_status')->default('approved')->after('shipping_details')->index();
            }

            if (! Schema::hasColumn('auctions', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('approval_status')->index();
            }

            if (! Schema::hasColumn('auctions', 'auction_request_id')) {
                $table->foreignId('auction_request_id')->nullable()->after('is_featured');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            foreach (['auction_request_id', 'is_featured', 'approval_status'] as $column) {
                if (Schema::hasColumn('auctions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
