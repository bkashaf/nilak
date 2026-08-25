<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 60)->nullable()->after('id');
            $table->string('last_name', 60)->nullable()->after('first_name');
            $table->string('username', 60)->nullable()->unique()->after('last_name');
            $table->string('secondary_phone', 20)->nullable()->after('mobile');
            $table->string('postal_code', 20)->nullable()->after('secondary_phone');
            $table->text('address')->nullable()->after('postal_code');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('recipient_name', 120)->nullable()->after('address');
            $table->string('recipient_mobile', 20)->nullable()->after('recipient_name');
            $table->string('recipient_phone_alt', 20)->nullable()->after('recipient_mobile');
            $table->string('postal_code', 20)->nullable()->after('recipient_phone_alt');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['recipient_name', 'recipient_mobile', 'recipient_phone_alt', 'postal_code']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->dropColumn(['first_name', 'last_name', 'username', 'secondary_phone', 'postal_code', 'address']);
        });
    }
};
