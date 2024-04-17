<?php namespace TimeTracker\Profile;

use Backend;
use System\Classes\PluginBase;
use Rainlab\User\Models\User as UserModel;
use Rainlab\User\Controllers\Users as UsersController;
use Timetracker\Tracker\Components\TrackerForm as TrackerForm;

/**
 * Plugin Information File
 *
 * @link https://docs.octobercms.com/3.x/extend/system/plugins.html
 */
class Plugin extends PluginBase
{
    /**
     * pluginDetails about this plugin.
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Profile',
            'description' => 'No description provided yet...',
            'author' => 'TimeTracker',
            'icon' => 'icon-leaf'
        ];
    }

    /**
     * register method, called when the plugin is first registered.
     */
    public function register()
    {
        //
    }

    /**
     * boot method, called right before the request route.
     */
    public function boot()
    {
        UserModel::extend(function($model){
            $model->addFillable([
                'api_key',
                'employee_id'
            ]);
            $model->hasOne['profile'] = ['TimeTracker\Profile\Models\Profile'];
        });

        UsersController::extendFormFields(function($form, $model, $context) {

            $form->addTabFields([
                'api_key' => [
                    'label' => 'API key',
                    'type' => 'text',
                    'tab' => 'Bamboo'
                ],
                'employee_id' => [
                    'label' => 'Employee ID',
                    'type' => 'text',
                    'tab' => 'Bamboo'
                ]
            ]);
        });
    }

    /**
     * registerComponents used by the frontend.
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'TimeTracker\Profile\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * registerPermissions used by the backend.
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'timetracker.profile.some_permission' => [
                'tab' => 'Profile',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * registerNavigation used by the backend.
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'profile' => [
                'label' => 'Profile',
                'url' => Backend::url('timetracker/profile/mycontroller'),
                'icon' => 'icon-leaf',
                'permissions' => ['timetracker.profile.*'],
                'order' => 500,
            ],
        ];
    }
}
