<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('ganadores', function (Blueprint $table) {
        $table->boolean('alerta')->default(false)->after('caza_premios');
    });
}

    public function down()
{
    Schema::table('ganadores', function (Blueprint $table) {
        $table->dropColumn('alerta');
    });
}
};
