<?php namespace TimeTracker\Profile\Models;

use Model;

/**
 * Profile Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Profile extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'timetracker_profile_profiles';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    public $belongsTo = [
      'user' => ['RainLab\User\Models\User']
    ];

    public static function getFromUser($user) {
        if ($user->profile) {
            return $user->profile;
        } else {
            $profile = new static;
            $profile->user = $user;
            $profile->save();

            $user->profile = $profile;

            return $profile;
        }
    }
}
