<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateTimetrackerTracker8 extends Migration
{
    public function up()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->string('workday_start', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('workday_lunch_start', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('workday_lunch_end', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('workday_start_min', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('workday_start_max', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('workday_lunch_start_min', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('workday_lunch_start_max', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('workday_lunch_end_min', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->string('workday_lunch_end_max', 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('timetracker_tracker_', function($table)
        {
            $table->time('workday_start')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->time('workday_lunch_start')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->time('workday_lunch_end')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->time('workday_start_min')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->time('workday_start_max')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->time('workday_lunch_start_min')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->time('workday_lunch_start_max')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->time('workday_lunch_end_min')->nullable()->unsigned(false)->default(null)->comment(null)->change();
            $table->time('workday_lunch_end_max')->nullable()->unsigned(false)->default(null)->comment(null)->change();
        });
    }
}
