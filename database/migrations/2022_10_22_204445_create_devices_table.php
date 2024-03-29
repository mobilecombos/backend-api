<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();

            $table->string('device_name')->index();
            $table->string('model_name')->index();
            $table->string('manufacturer')->index();

            $table->foreignId('modem_id')->index()->nullable()->constrained('modems')->nullOnDelete()->cascadeOnUpdate();

            $table->date('release_date')->index();

            $table->timestamps();
            $table->index('updated_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
