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
 * Contains notification_list_processor class for displaying on message preferences page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\preferences;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/message/lib.php');

use renderable;
use templatable;

/**
 * Class to create context for a notification component on the message preferences page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_list_processor implements templatable, renderable {

    /**
     * @var \stdClass A notification processor.
     */
    protected $processor;

    /**
     * @var \stdClass A notification provider.
     */
    protected $provider;

    /**
     * @var \stdClass A list of message preferences.
     */
    protected $preferences;

    /**
     * Constructor.
     *
     * @param \stdClass $processor
     * @param \stdClass $provider
     * @param \stdClass $preferences
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
     * @param string $locked Wether the preference is locked by admin.
     * @return bool
     */
    private function is_preference_enabled($name, $locked) {
        $processor = $this->processor;
        $preferences = $this->preferences;
        $defaultpreferences = get_message_output_default_preferences();

        $checked = false;
        // See if user has touched this preference.
        if (!$locked && isset($preferences->{$name})) {
            // User has some preferences for this state in the database.
            $checked = isset($preferences->{$name}[$processor->name]);
        } else {
            // User has not set this preference yet, using site default preferences set by admin.
            $defaultpreference = 'message_provider_'.$name;
            if (isset($defaultpreferences->{$defaultpreference})) {
                $checked = (int)in_array($processor->name, explode(',', $defaultpreferences->{$defaultpreference}));
            }
        }

        return $checked;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $processor = $this->processor;
        $preferencebase = $this->get_preference_base();
        $defaultpreferences = get_message_output_default_preferences();
        $defaultpreference = $processor->name.'_provider_'.$preferencebase.'_locked';
        $providername = get_string('messageprovider:'.$this->provider->name, $this->provider->component);
        $processorname = get_string('pluginname', 'message_'.$processor->name);
        $labelparams = [
            'provider'  => $providername,
            'processor' => $processorname,
        ];

        $context = [
            'displayname' => $processorname,
            'name' => $processor->name,
            'locked' => false,
            'userconfigured' => $processor->object->is_user_configured(),
            'enabled' => false,
            'enabledlabel' => get_string('sendingviaenabled', 'message', $labelparams),
        ];

        // Determine the default setting.
        if (isset($defaultpreferences->{$defaultpreference})) {
            $context['locked'] = $defaultpreferences->{$defaultpreference};
        }

        $context['enabled'] = $this->is_preference_enabled($preferencebase.'_enabled', $context['locked']);

        // If settings are disallowed or forced, just display the corresponding message, if not use user settings.
        if ($context['locked']) {
            if ($context['enabled']) {
                $context['lockedmessage'] = get_string('forcedmessage', 'message');
                $context['lockedlabel'] = get_string('providerprocesorislocked', 'message', $labelparams);
            } else {
                $context['lockedmessage'] = get_string('disallowed', 'message');
                $context['lockedlabel'] = get_string('providerprocesorisdisallowed', 'message', $labelparams);
            }
        }

        return $context;
    }
}
