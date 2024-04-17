<?php
use Timetracker\Tracker\Components\TrackerForm as TrackerForm;

Route::get('cron', function() {
    $trackerForm = new TrackerForm();
    $trackerForm->onGetAllUsersClockIns();
});
