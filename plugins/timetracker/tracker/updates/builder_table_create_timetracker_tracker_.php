<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateTimetrackerTracker extends Migration
{
    public function up()
    {
        Schema::create('timetracker_tracker_', function($table)
        {
            $table->increments('id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->time('workday_start')->nullable();
            $table->time('workday_lunch')->nullable();
            $table->time('workday_end')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('timetracker_tracker_');
    }
}
