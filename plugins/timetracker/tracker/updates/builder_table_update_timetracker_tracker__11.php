<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateTimetrackerTracker11 extends Migration
{
    public function up()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->smallInteger('active')->nullable()->unsigned(false)->default(0)->comment(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->boolean('active')->nullable()->unsigned(false)->default(0)->comment(null)->change();
        });
    }
}
