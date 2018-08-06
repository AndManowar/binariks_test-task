<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TaskTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Организации
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('organization_name');
            $table->date('registration_date');
            $table->integer('owner_id')->unsigned()->index();
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
        });

        // Пользователи-организации
        Schema::create('users_organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
        });

        // Задания
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->unsigned()->index();
            $table->integer('performer_id')->unsigned()->index()->nullable(); // Задача может быть создана без исполнителя
            $table->integer('organization_id')->unsigned()->index(); // Задача не в воздухе висит, а привязана к организации
            $table->string('name');
            $table->integer('status')->default(1);
            $table->string('cancellation_reason')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->date('deadline');
            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('performer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_organizations');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('organizations');
    }
}
