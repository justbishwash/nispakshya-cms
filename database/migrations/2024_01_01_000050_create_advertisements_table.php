<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('position', [
                'home_top', 'home_middle', 'home_footer',
                'article_above', 'article_mid', 'article_end',
                'sidebar_top', 'sidebar_middle', 'sidebar_bottom',
                'mobile_sticky_bottom',
            ]);
            $table->enum('type', ['image', 'html'])->default('image');
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->text('html_code')->nullable();
            $table->boolean('open_new_tab')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamps();

            $table->index(['position', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
