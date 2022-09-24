<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssociationRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('association_rules', function (Blueprint $table) {
            $table->id();
            $table->string('consequent')->nullable();
            $table->string('antecedent')->nullable();
            $table->integer('consequentSet');
            $table->integer('antecedentSet');
            $table->double('confidence')->nullable();
            $table->double('support')->nullable();
            $table->double('rule_confidence')->nullable();
            $table->double('rule_support')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
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
        Schema::dropIfExists('association_rules');
    }
}
