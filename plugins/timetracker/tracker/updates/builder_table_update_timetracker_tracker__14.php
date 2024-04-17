<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateTimetrackerTracker14 extends Migration
{
    public function up()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->smallInteger('active')->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->smallInteger('active')->default(1)->change();
        });
    }
}
