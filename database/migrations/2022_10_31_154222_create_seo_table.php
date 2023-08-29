<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->string('seoable_type')->nullable();
            $table->unsignedBigInteger('seoable_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('keyword')->nullable();
            $table->text('schema_markup')->nullable();
            $table->text('header_script')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('seo');
    }
};
