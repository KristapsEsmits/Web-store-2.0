<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToFavoriteItems extends Migration
{
    public function up()
    {
        Schema::table('favorite_items', function (Blueprint $table) {
            $table->unique(['user_id', 'item_id']);
        });
    }

    public function down()
    {
        Schema::table('favorite_items', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'item_id']);
        });
    }
    
}
