<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateTimetrackerTracker6 extends Migration
{
    public function up()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->time('workday_lunch_start_max')->nullable();
            $table->time('workday_lunch_end_min')->nullable();
            $table->time('workday_lunch_end_max')->nullable();
            $table->dropColumn('wokday_lunch_start_max');
            $table->dropColumn('wokday_lunch_end_min');
            $table->dropColumn('wokday_lunch_end_max');
        });
    }
    
    public function down()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->dropColumn('workday_lunch_start_max');
            $table->dropColumn('workday_lunch_end_min');
            $table->dropColumn('workday_lunch_end_max');
            $table->time('wokday_lunch_start_max')->nullable();
            $table->time('wokday_lunch_end_min')->nullable();
            $table->time('wokday_lunch_end_max')->nullable();
        });
    }
}
