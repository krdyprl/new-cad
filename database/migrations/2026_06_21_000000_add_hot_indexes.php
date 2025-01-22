<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ponytail: only the columns actually filtered+ordered get an index.
// users.is_admin skipped: boolean on a tiny table, the optimizer ignores it.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', fn (Blueprint $t) => $t->index(['status', 'created_at']));
        Schema::table('products', fn (Blueprint $t) => $t->index(['is_active', 'category']));
    }

    public function down(): void
    {
        Schema::table('bookings', fn (Blueprint $t) => $t->dropIndex(['status', 'created_at']));
        Schema::table('products', fn (Blueprint $t) => $t->dropIndex(['is_active', 'category']));
    }
};
