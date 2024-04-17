<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateTimetrackerTracker7 extends Migration
{
    public function up()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->renameColumn('wokday_lunch_start_min', 'workday_lunch_start_min');
        });
    }
    
    public function down()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->renameColumn('workday_lunch_start_min', 'wokday_lunch_start_min');
        });
    }
}
