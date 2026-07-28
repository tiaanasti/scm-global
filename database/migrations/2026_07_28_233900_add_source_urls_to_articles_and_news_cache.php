<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'source_url')) {
                $table->text('source_url')->nullable()->after('category');
            }
        });

        Schema::table('news_cache', function (Blueprint $table) {
            if (!Schema::hasColumn('news_cache', 'image_url')) {
                $table->text('image_url')->nullable()->after('url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('news_cache', function (Blueprint $table) {
            if (Schema::hasColumn('news_cache', 'image_url')) {
                $table->dropColumn('image_url');
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'source_url')) {
                $table->dropColumn('source_url');
            }
        });
    }
};
