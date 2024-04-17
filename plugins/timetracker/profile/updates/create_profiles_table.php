<?php namespace TimeTracker\Profile\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateProfilesTable Migration
 *
 * @link https://docs.octobercms.com/3.x/extend/database/structure.html
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->string('api_key')->nullable();
            $table->string('employee_id')->nullable();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('api_key');
            $table->dropColumn('employee_id');
        });
    }
};
