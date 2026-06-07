<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device', 20)->nullable(); // desktop, mobile, tablet
            $table->date('viewed_date');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['article_id', 'viewed_date']);
            $table->index('viewed_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
