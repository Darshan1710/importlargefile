<?php

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    use SoftDeletes;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id');
            $table->string('name');
            $table->string('domain')->nullable();
            $table->integer('year_founded')->nullable();
            $table->string('industry')->nullable();
            $table->string('size_range')->nullable();
            $table->string('locality')->nullable();
            $table->string('country')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->integer('current_employee_estimate')->nullable();
            $table->integer('total_employee_estimate')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('employees');
    }
};
