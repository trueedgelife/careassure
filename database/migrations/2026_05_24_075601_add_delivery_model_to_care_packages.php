<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('care_packages', function (Blueprint $table) {
            $table->string('delivery_model')->default('direct_payment')->after('service_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('care_packages', function (Blueprint $table) {
            $table->dropColumn('delivery_model');
        });
    }
};
