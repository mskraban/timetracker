<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteTimetrackerTrackerUser extends Migration
{
    public function up()
    {
        Schema::dropIfExists('timetracker_tracker_user');
    }
    
    public function down()
    {
        Schema::create('timetracker_tracker_user', function($table)
        {
            $table->integer('tracker_id');
            $table->integer('user_id');
            $table->primary(['tracker_id','user_id']);
        });
    }
}
