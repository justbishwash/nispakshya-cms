<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Nepali date in English digits  e.g. "2082 Jestha 24"
            $table->string('published_date_np_en', 60)->nullable()->after('published_at');
            // Nepali date in Devanagari script e.g. "२०८२ जेठ २४"
            $table->string('published_date_np', 60)->nullable()->after('published_date_np_en');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['published_date_np_en', 'published_date_np']);
        });
    }
};
