<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // Dans la migration générée
public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->string('slug')->unique()->after('name');
        $table->foreignId('category_id')->nullable()->constrained()->after('image_url');
        $table->float('rating')->default(0)->after('category_id');
        $table->json('sizes')->nullable()->after('rating');

        // Index pour les recherches
        $table->index('category_id');
        $table->index('price');
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['slug', 'category_id', 'rating', 'sizes']);
    });
}
};