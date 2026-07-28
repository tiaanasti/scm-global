<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            if (!Schema::hasColumn('ports', 'external_source')) {
                $table->string('external_source', 80)->nullable()->after('id');
            }

            if (!Schema::hasColumn('ports', 'external_id')) {
                $table->string('external_id', 120)->nullable()->after('external_source');
            }
        });

        Schema::table('ports', function (Blueprint $table) {
            $table->unique(['external_source', 'external_id'], 'ports_external_source_id_unique');
        });

        Schema::table('news_cache', function (Blueprint $table) {
            if (!Schema::hasColumn('news_cache', 'url_hash')) {
                $table->string('url_hash', 64)->nullable()->after('url');
            }
        });

        Schema::table('news_cache', function (Blueprint $table) {
            $table->unique('url_hash', 'news_cache_url_hash_unique');
        });
    }

    public function down(): void
    {
        Schema::table('news_cache', function (Blueprint $table) {
            $table->dropUnique('news_cache_url_hash_unique');
            $table->dropColumn('url_hash');
        });

        Schema::table('ports', function (Blueprint $table) {
            $table->dropUnique('ports_external_source_id_unique');
            $table->dropColumn(['external_source', 'external_id']);
        });
    }
};
