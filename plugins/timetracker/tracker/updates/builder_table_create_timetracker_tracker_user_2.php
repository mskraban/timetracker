<?php namespace Timetracker\Tracker\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateTimetrackerTrackerUser2 extends Migration
{
    public function up()
    {
        Schema::create('timetracker_tracker_user', function($table)
        {
            $table->integer('tracker_id');
            $table->integer('user_id');
            $table->primary(['tracker_id','user_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('timetracker_tracker_user');
    }
}
