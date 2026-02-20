<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            // URL del archivo subido a Cloudinary
            $table->string('media_url')->nullable()->after('text');
            // 'image' | 'video' | null
            $table->string('media_type')->nullable()->after('media_url');
        });
    }

    public function down(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropColumn(['media_url', 'media_type']);
        });
    }
};