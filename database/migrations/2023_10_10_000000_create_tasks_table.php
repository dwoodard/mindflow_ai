<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('input')->nullable();
            $table->text('output')->nullable();
            $table->string('status');
            $table->string('pattern')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
