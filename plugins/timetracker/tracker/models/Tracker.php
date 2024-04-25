<?php namespace Timetracker\Tracker\Models;

use Model;

/**
 * Model
 */
class Tracker extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string table in the database used by the model.
     */
    public $table = 'timetracker_tracker_';

    public $belongsToMany = [
        'tracker_id' =>[
            'Rainlab\user\models\User',
            'table' => 'timetracker_tracker_user',
            'order' => 'user_id'
        ],
    ];

    /**
     * @var array rules for validation.
     */
    public $rules = [
    ];

    protected $fillable = [
        'workday_start_min',
        'workday_lunch_start_min',
        'workday_lunch_end_min',
        'workday_start_max',
        'workday_lunch_start_max',
        'workday_lunch_end_max',
        'workday_start',
        'workday_lunch_start',
        'workday_lunch_end',
        'active',
        'disabled_dates'
    ];
}
