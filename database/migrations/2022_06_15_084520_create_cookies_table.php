<?php

use App\Models\Proxy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cookies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Proxy::class);
            $table->string('name');
            $table->string('value')->nullable();
            $table->string('domain')->nullable();
            $table->string('path')->default('/');
            $table->string('max_age')->nullable();
            $table->string('expires')->nullable();
            $table->boolean('secure')->default(false);
            $table->boolean('http_only')->default(false);
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
        Schema::dropIfExists('cookies');
    }
};
