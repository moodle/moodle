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
 * Contains notification_list class for displaying on message preferences
 * page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output\preferences;

use renderable;
use templatable;
use context_user;

/**
 * Class to create context for the list of notifications on the message
 * preferences page.
 *
 * @package   core_message
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_list implements templatable, renderable {

    /**
     * A list of message processors.
     */
    protected $processors;

    /**
     * A list of message providers.
     */
    protected $providers;

    /**
     * A list of message preferences.
     */
    protected $preferences;

    /**
     * A user.
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param array $processors
     * @param array $providers
     * @param stdClass $preferences
     * @param stdClass $user
     */
    public function __construct($processors, $providers, $preferences, $user) {
        $this->processors = $processors;
        $this->providers = $providers;
        $this->preferences = $preferences;
        $this->user = $user;
    }

    public function export_for_template(\renderer_base $output) {
        $processors = $this->processors;
        $providers = $this->providers;
        $preferences = $this->preferences;
        $user = $this->user;
        $usercontext = context_user::instance($user->id);

        foreach($providers as $provider) {
            if($provider->component != 'moodle') {
                $components[] = $provider->component;
            }
        }

        // Lets arrange by components so that core settings (moodle) appear as the first table.
        $components = array_unique($components);
        asort($components);
        array_unshift($components, 'moodle'); // pop it in front! phew!
        asort($providers);

        $context = [
            'userid' => $user->id,
            'disableall' => $user->emailstop,
            'processors' => [],
        ];

        foreach ($processors as $processor) {
            $context['processors'][] = [
                'displayname' => get_string('pluginname', 'message_'.$processor->name),
                'name' => $processor->name,
                'hassettings' => !empty($processor->object->config_form($preferences)),
                'contextid' => $usercontext->id,
            ];
        }

        foreach ($components as $component) {
            $notificationcomponent = new \core_message\output\preferences\notification_list_component(
                $component, $processors, $providers, $preferences, $user);
            $context['components'][] = $notificationcomponent->export_for_template($output);
        }

        return $context;
    }
}
