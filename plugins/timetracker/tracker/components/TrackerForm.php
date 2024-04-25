<?php namespace Timetracker\Tracker\Components;

use Cms\Classes\ComponentBase;
use DateTime;
use DateInterval;
use Db;
use Input;
use InvalidArgumentException;
use RainLab\User\Models\User;
use Validator;
use Redirect;
use Timetracker\Tracker\Models\Tracker;
use Flash;

/**
 * TrackerForm Component
 *
 * @link https://docs.octobercms.com/3.x/extend/cms-components.html
 */
class TrackerForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'TrackerForm Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function onUpdate()
    {
        $id = post('record'); // Assuming the primary key is called 'id'
        $tracker = Tracker::find($id);

        if (!$tracker) {
            // Handle case where record not found
            Flash::error('Tracker not found!');
            return;
        }

        $data = (array)post();
        $tracker->fill($data);
        $tracker->save();
        Flash::success('Tracker updated!');
        return Redirect::back();
    }

    public function onSave()
    {
        $tracker = new Tracker();
        $tracker->tracker_id = Input::get('user_id');
        $data = (array)post();
        $tracker->fill($data);
        $tracker->save();
        Flash::success('Tracker added!');
        return Redirect::to('/list');
    }

    public function onDelete()
    {
        $id = post('record'); // Assuming the primary key is called 'id'
        $tracker = Tracker::find($id);

        if (!$tracker) {
            // Handle case where record not found
            Flash::error('Tracker not found!');
            return;
        }

        $tracker->delete();
        Flash::success('Tracker deleted!');
        return Redirect::to('/list');
    }

    public function onClockIn($apiKey, $employeeId, $note)
    {
        require_once('vendor/autoload.php');

        $client = new \GuzzleHttp\Client();
        $encodedApiKey = base64_encode($apiKey . ':');

        echo "<br>";
        echo "<br>";
        echo "Basic". $encodedApiKey;
        echo "<br>";
        echo "https://api.bamboohr.com/api/gateway.php/flaviar/v1/time_tracking/employees/$employeeId/clock_in";

        $response = $client->request('POST', "https://api.bamboohr.com/api/gateway.php/flaviar/v1/time_tracking/employees/$employeeId/clock_in", [
            'body' => '{"note":' . $note . '}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Basic ". $encodedApiKey,
                'content-type' => 'application/json',
            ],
        ]);

        echo $response->getBody();
    }

    public function onClockOut($apiKey, $employeeId, $note)
    {
        require_once('vendor/autoload.php');

        $client = new \GuzzleHttp\Client();
        $encodedApiKey = base64_encode($apiKey . ':');

        $response = $client->request('POST', "https://api.bamboohr.com/api/gateway.php/flaviar/v1/time_tracking/employees/$employeeId/clock_out", [
            'body' => '{"note":' . $note . '}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Basic ". $encodedApiKey,
                'content-type' => 'application/json',
            ],
        ]);

        echo $response->getBody();
    }

    public function getTimeDifference($startTime, $endTime): float|int
    {
        $interval = date_diff($startTime, $endTime);
        $min = $interval->days * 24 * 60;
        $min += $interval->h * 60;
        $min += $interval->i;

        return $min;
    }

    public function differenceInHours($startTime, $endTime): float
    {
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);
        $difference = abs($endTimestamp - $startTimestamp);

        return round($difference / 3600, 2);
    }

    function add_minutes($time, $minutes): string
    {
        $dateTime = DateTime::createFromFormat('H:i:s', $time);
        $dateTime->add(new DateInterval('PT' . $minutes . 'M'));
        return $dateTime->format('H:i:s');
    }

     function has_current_date($dates): bool
     {
        $currentDate = date('m/d/Y');
        return in_array($currentDate, $dates);
    }

    public function onGetAllUsersClockIns()
    {
        $trackerForm = new TrackerForm();
        $activeTrackers = Db::table('timetracker_tracker_')->where('active', 1)->get();
        $scheduleType = null;
        $scheduleDataAvailable = false;
        $isWorkday = false;
        $workdayHoursTotal = 8;
        $workdayMinutesTotal = $workdayHoursTotal * 60;

        date_default_timezone_set('Europe/Ljubljana');

        // Check if day of the week is lower than saturday
        if (date('N') < 6) {
            $isWorkday = true;
        }

        if ($isWorkday) {
            foreach ($activeTrackers as $activeTracker) {
                if ($activeTracker->workday_start && $activeTracker->workday_lunch_start && $activeTracker->workday_lunch_end) {
                    $scheduleType = 'fixed';
                    $scheduleDataAvailable = true;
                    $trackerId = $activeTracker->id; // Replace with your actual tracker ID

                    $userId = User::select('users.id')
                        ->join('timetracker_tracker_user', function ($join) use ($trackerId) {
                            $join->on('users.id', '=', 'timetracker_tracker_user.user_id');
                        })
                        ->where('timetracker_tracker_user.tracker_id', $trackerId)
                        ->pluck('id');

                    $userData = User::where('id', $userId)->first();
                    $apiKey = $userData->api_key;
                    $employeeId = $userData->employee_id;

                    if (!$apiKey || !$employeeId) {
                        echo "here";
                        return;
                    }

                    $disabledDatesString = $activeTracker->disabled_dates;
                    $disabledDates = explode(',', $disabledDatesString);

                    if ($trackerForm->has_current_date($disabledDates)) {
                      return;
                    }

                    echo "<br>";
                    echo $trackerId;
                    echo "<br>";
                    echo $apiKey;
                    echo "<br>";
                    echo $employeeId;
                    echo "<br>";
                    echo date("H:i:s");
                    echo "<br>";
                    echo $activeTracker->workday_start;

                    if (date("H:i:s") == $activeTracker->workday_start) {
                        $trackerForm->onClockIn($apiKey, $employeeId, 'Started working');
                        echo "Started working";
                    }

                    if (date("H:i:s") == $activeTracker->workday_lunch_start) {
                        $trackerForm->onClockOut($apiKey, $employeeId,'Lunch break');
                        sleep(10);
                        $trackerForm->onClockIn($apiKey, $employeeId,'Lunch break end');
                        echo "Lunch break";
                    }

                    if (date("H:i:s") == $activeTracker->workday_lunch_end) {
                        $trackerForm->onClockOut($apiKey, $employeeId,'Resumed working');
                        sleep(10);
                        $trackerForm->onClockIn($apiKey, $employeeId,'Finished working');
                        echo "Resumed working";
                    }

                    // Work start to Lunch start duration in minutes
                    $startToLunchDuration = $this->getTimeDifference(
                        date_create($activeTracker->workday_start),
                        date_create($activeTracker->workday_lunch_start)
                    );

                    // Lunch start to Lunch end duration in minutes
                    $startLunchToEndLunchDuration = $this->getTimeDifference(
                        date_create($activeTracker->workday_lunch_start),
                        date_create($activeTracker->workday_lunch_end)
                    );

                    // Calculate remaining work minutes to spend
                    $workMinutesSpent = $startToLunchDuration + $startLunchToEndLunchDuration;
                    $workMinutesUnspent = $workdayMinutesTotal - $workMinutesSpent;

                    // Sum remaining minutes to lunch end time to get clock-out time
                    $startTime = $activeTracker->workday_lunch_end;
                    $workdayEndTime = $this->add_minutes($startTime, $workMinutesUnspent);

                    // Check if calculated time matches 8 hours
                    $calculatedHoursDiff = $this->differenceInHours( $activeTracker->workday_start, $workdayEndTime);
                    if ($calculatedHoursDiff == $workdayHoursTotal) {
                        if (date("H:i:s") == $workdayEndTime) {
                            $trackerForm->onClockOut($apiKey, $employeeId,'Finished working');
                            echo "Finished working";
                        }
                    }

                    echo $workdayEndTime;
                }
            }
        }
    }

    /**
     * @link https://docs.octobercms.com/3.x/element/inspector-types.html
     */
    public function defineProperties()
    {
        return [];
    }
}
