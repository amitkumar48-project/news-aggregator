<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('source', 50);              // Here we have used 'newsapi','guardian','nyt' source
            $table->string('external_id')->nullable();  // provider’s unique id
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('category')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('url', 1024)->nullable();
            $table->string('thumbnail', 2048)->nullable();
            $table->longText('content')->nullable();
            $table->json('source_raw')->nullable();             // raw payload for traceability
            $table->timestamps();
            $table->unique(['source', 'external_id']);         // for avoids duplicates by provider id
            $table->index(['published_at', 'id']);
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
