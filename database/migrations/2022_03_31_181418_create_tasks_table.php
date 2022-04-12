<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('parent_id')->nullable()->constrained('tasks');
            $table->string('title');
            $table->text('description');
            $table->enum('priority', [1, 2, 3, 4, 5]);
            $table->enum('status', ['todo', 'done']);
            $table->timestamps();
            $table->timestamp('closed_at')->nullable();
        });
        DB::statement('ALTER TABLE tasks ADD FULLTEXT full(title, description)');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
