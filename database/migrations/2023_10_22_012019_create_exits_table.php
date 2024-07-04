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
        Schema::create('exits', function (Blueprint $table) {
            $table->increments('id_exit');
            $table->string('description', 50);
            $table->decimal('value', $precision = 65, $scale = 2);
            $table->foreignId('id_user_fk')->constrained('users');
            $table->date('exit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exits');
    }
};
