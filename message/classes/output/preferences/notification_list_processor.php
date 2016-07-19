<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contains notification_list_processor class for displaying on message preferences
 * page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\preferences;

require_once($CFG->dirroot . '/message/lib.php');

use renderable;
use templatable;

/**
 * Class to create context for a notification component on the message
 * preferences page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_list_processor implements templatable, renderable {

    /**
     * A notification processor.
     */
    protected $processor;

    /**
     * A notification provider.
     */
    protected $provider;

    /**
     * A list of message preferences.
     */
    protected $preferences;

    /**
     * Constructor.
     *
     * @param stdClass $processor
     * @param stdClass $provider
     * @param stdClass $preferences
     */
    public function __construct($processor, $provider, $preferences) {
        $this->processor = $processor;
        $this->provider = $provider;
        $this->preferences = $preferences;
    }

    /**
     * Get the base key prefix for the given provider.
     *
     * @return string
     */
    private function get_preference_base() {
        return $this->provider->component . '_' . $this->provider->name;
    }

    /**
     * Check if the given preference is enabled or not.
     *
     * @param string $name preference name
     * @param stdClass $processor the processors for the preference
     * @param stdClass $preferences the preferences config
     * @return bool
     */
    private function is_preference_enabled($name) {
        $processor = $this->processor;
        $preferences = $this->preferences;
        $defaultpreferences = get_message_output_default_preferences();

        $checked = false;
        // See if user has touched this preference
        if (isset($preferences->{$name})) {
            // User have some preferneces for this state in the database, use them
            $checked = isset($preferences->{$name}[$processor->name]);
        } else {
            // User has not set this preference yet, using site default preferences set by admin
            $defaultpreference = 'message_provider_'.$name;
            if (isset($defaultpreferences->{$defaultpreference})) {
                $checked = (int)in_array($processor->name, explode(',', $defaultpreferences->{$defaultpreference}));
            }
        }

        return $checked;
    }

    public function export_for_template(\renderer_base $output) {
        $processor = $this->processor;
        $provider = $this->provider;
        $preferences = $this->preferences;

        $context = [
            'displayname' => get_string('pluginname', 'message_'.$processor->name),
            'name' => $processor->name,
            'locked' => false,
            'radioname' => strtolower(str_replace(" ", "-", $processor->name)),
            'states' => []
        ];
        // determine the default setting
        $preferencebase = $this->get_preference_base($provider);
        $permitted = MESSAGE_DEFAULT_PERMITTED;
        $defaultpreferences = get_message_output_default_preferences();
        $defaultpreference = $processor->name.'_provider_'.$preferencebase.'_permitted';
        if (isset($defaultpreferences->{$defaultpreference})) {
            $permitted = $defaultpreferences->{$defaultpreference};
        }
        // If settings are disallowed or forced, just display the
        // corresponding message, if not use user settings.
        if ($permitted == 'disallowed') {
            $context['locked'] = true;
            $context['lockedmessage'] = get_string('disallowed', 'message');
        } else if ($permitted == 'forced') {
            $context['locked'] = true;
            $context['lockedmessage'] = get_string('forced', 'message');
        } else {
            $statescontext = [
                'loggedin' => [
                    'name' => 'loggedin',
                    'displayname' => get_string('loggedindescription', 'message'),
                    'checked' => $this->is_preference_enabled($preferencebase.'_loggedin', $processor, $preferences),
                    'iconurl' => $output->pix_url('i/completion-auto-y')->out(),
                ],
                'loggedoff' => [
                    'name' => 'loggedoff',
                    'displayname' => get_string('loggedoffdescription', 'message'),
                    'checked' => $this->is_preference_enabled($preferencebase.'_loggedoff', $processor, $preferences),
                    'iconurl' => $output->pix_url('i/completion-auto-n')->out(),
                ],
                'both' => [
                    'name' => 'both',
                    'displayname' => get_string('always'),
                    'checked' => false,
                    'iconurl' => $output->pix_url('i/completion-auto-pass')->out(),
                ],
                'none' => [
                    'name' => 'none',
                    'displayname' => get_string('never'),
                    'checked' => false,
                    'iconurl' => $output->pix_url('i/completion-auto-fail')->out(),
                ],
            ];

            if ($statescontext['loggedin']['checked'] && $statescontext['loggedoff']['checked']) {
                $statescontext['both']['checked'] = true;
                $statescontext['loggedin']['checked'] = false;
                $statescontext['loggedoff']['checked'] = false;
            } else if (!$statescontext['loggedin']['checked'] && !$statescontext['loggedoff']['checked']) {
                $statescontext['none']['checked'] = true;
            }

            $context['states'] = array_values($statescontext);
        }

        return $context;
    }
}
