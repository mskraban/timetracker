<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateTimetrackerTracker2 extends Migration
{
    public function up()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->time('workday_start_min')->nullable();
            $table->time('workday_start_max')->nullable();
            $table->time('wokday_lunch_start_min')->nullable();
            $table->time('wokday_lunch_start_max')->nullable();
            $table->time('wokday_lunch_end_min')->nullable();
            $table->time('wokday_lunch_end_max')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->dropColumn('workday_start_min');
            $table->dropColumn('workday_start_max');
            $table->dropColumn('wokday_lunch_start_min');
            $table->dropColumn('wokday_lunch_start_max');
            $table->dropColumn('wokday_lunch_end_min');
            $table->dropColumn('wokday_lunch_end_max');
        });
    }
}
