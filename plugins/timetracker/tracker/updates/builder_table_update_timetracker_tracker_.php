<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateTimetrackerTracker extends Migration
{
    public function up()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->time('workday_lunch_start')->nullable();
            $table->time('workday_lunch_end')->nullable();
            $table->dropColumn('workday_lunch');
            $table->dropColumn('workday_end');
        });
    }
    
    public function down()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->dropColumn('workday_lunch_start');
            $table->dropColumn('workday_lunch_end');
            $table->time('workday_lunch')->nullable();
            $table->time('workday_end')->nullable();
        });
    }
}
