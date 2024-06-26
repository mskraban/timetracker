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

    public function getTimeOff($apiKey, $employeeId, $date)
    {
        require_once('vendor/autoload.php');

        $client = new \GuzzleHttp\Client();
        $encodedApiKey = base64_encode($apiKey . ':');

        $response = $client->request('GET', "https://api.bamboohr.com/api/gateway.php/flaviar/v1/time_off/requests/?action=view&employeeId=$employeeId&start=$date&end=$date&status=approved", [
            'headers' => [
                'Accept' => 'application/json',
                'authorization' => "Basic ". $encodedApiKey
            ],
        ]);

        return json_decode($response->getBody(), true);
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
        $dateTime = DateTime::createFromFormat('H:i', $time);
        $dateTime->add(new DateInterval('PT' . $minutes . 'M'));
        return $dateTime->format('H:i');
    }

    function has_current_date($dates): bool
    {
        $currentDate = strtotime(date('Y-m-d')); // Convert current date to a timestamp
        foreach ($dates as $dateString) {
            $dateTimestamp = strtotime($dateString);
            if ($currentDate === $dateTimestamp) {
                return true;
            }
        }
        return false;
    }

    function convertTimeWithDateTime($dateTimeString) {
        $dateTime = new DateTime($dateTimeString);
        $formattedTime = $dateTime->format('H:i');
        return $formattedTime;
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

                    echo "Tracker ID: $trackerId<br>";
                    echo "API key: $apiKey<br>";
                    echo "Employee ID: $employeeId<br>";
                    echo date("H:i")."<br>";
                    echo date("Y-m-d")."<br>";
                    echo "<br>";
                    echo "<br>";
                    echo $disabledDatesString;
                    echo "<br>";

                    $timeOffData = $this->getTimeOff($apiKey, $employeeId, date("Y-m-d"));

                    if ($timeOffData) {
                        foreach ($timeOffData as $item) {
                            foreach ($item["dates"] as $date => $value) {
                                if ($date == date("Y-m-d")) {
                                    return;
                                }
                            }
                        }
                    }

                    $workdayStart = $trackerForm->convertTimeWithDateTime($activeTracker->workday_start);
                    $workdayLunchStart = $trackerForm->convertTimeWithDateTime($activeTracker->workday_lunch_start);
                    $workdayLunchEnd = $trackerForm->convertTimeWithDateTime($activeTracker->workday_lunch_end);


                    if (date("H:i") == $workdayStart) {
                        $trackerForm->onClockIn($apiKey, $employeeId, 'Started working');
                        echo "Started working";
                    }

                    if (date("H:i") == $workdayLunchStart) {
                        $trackerForm->onClockOut($apiKey, $employeeId,'Lunch break');
                        sleep(10);
                        $trackerForm->onClockIn($apiKey, $employeeId,'Lunch break end');
                        echo "Lunch break";
                    }

                    if (date("H:i") == $workdayLunchEnd) {
                        $trackerForm->onClockOut($apiKey, $employeeId,'Resumed working');
                        sleep(10);
                        $trackerForm->onClockIn($apiKey, $employeeId,'Finished working');
                        echo "Resumed working";
                    }

                    // Work start to Lunch start duration in minutes
                    $startToLunchDuration = $this->getTimeDifference(
                        date_create($workdayStart),
                        date_create($workdayLunchStart)
                    );

                    // Lunch start to Lunch end duration in minutes
                    $startLunchToEndLunchDuration = $this->getTimeDifference(
                        date_create($workdayLunchStart),
                        date_create($workdayLunchEnd)
                    );

                    // Calculate remaining work minutes to spend
                    $workMinutesSpent = $startToLunchDuration + $startLunchToEndLunchDuration;
                    $workMinutesUnspent = $workdayMinutesTotal - $workMinutesSpent;

                    // Sum remaining minutes to lunch end time to get clock-out time
                    $startTime = $workdayLunchEnd;
                    $workdayEndTime = $this->add_minutes($startTime, $workMinutesUnspent);

                    // Check if calculated time matches 8 hours
                    $calculatedHoursDiff = $this->differenceInHours($workdayStart, $workdayEndTime);
                    if ($calculatedHoursDiff == $workdayHoursTotal) {
                        if (date("H:i") == $workdayEndTime) {
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
