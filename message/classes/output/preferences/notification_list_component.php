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
 * Contains notification_list_component class for displaying on message preferences page.
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
class notification_list_component implements templatable, renderable {

    /**
     * @var array A list of message processors.
     */
    protected $processors;

    /**
     * @var array A list of message providers.
     */
    protected $providers;

    /**
     * @var array A list of message preferences.
     */
    protected $preferences;

    /**
     * @var string The component name.
     */
    protected $component;

    /**
     * @var \stdClass A user.
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param string $component
     * @param array $processors
     * @param array $providers
     * @param \stdClass $preferences
     * @param \stdClass $user
     */
    public function __construct($component, $processors, $providers, $preferences, $user) {
        $this->processors = $processors;
        $this->providers = $providers;
        $this->preferences = $preferences;
        $this->component = $component;
        $this->user = $user;
    }

    /**
     * Get the base key prefix for the given provider.
     *
     * @param \stdClass $provider The message provider
     * @return string
     */
    private function get_preference_base($provider) {
        return $provider->component.'_'.$provider->name;
    }

    /**
     * Get the display name for the given provider.
     *
     * @param \stdClass $provider The message provider
     * @return string
     */
    private function get_provider_display_name($provider) {
        return get_string('messageprovider:'.$provider->name, $provider->component);
    }

    /**
     * Determine if the preference should be displayed.
     *
     * @param string $preferencekey
     * @return bool
     */
    protected function should_show_preference_key($preferencekey) {
        return $preferencekey !== 'message_provider_moodle_instantmessage';
    }

    public function export_for_template(\renderer_base $output) {
        $processors = $this->processors;
        $providers = $this->providers;
        $preferences = $this->preferences;
        $component = $this->component;
        $defaultpreferences = get_message_output_default_preferences();

        if ($component != 'moodle') {
            $componentname = get_string('pluginname', $component);
        } else {
            $componentname = get_string('coresystem');
        }

        $context = [
            'displayname' => $componentname,
            'colspan' => count($processors) + 1,
            'notifications' => [],
        ];

        foreach ($providers as $provider) {
            $preferencebase = $this->get_preference_base($provider);
            $preferencekey = 'message_provider_'.$preferencebase;

            // Hack to stop this one specific preference from showing up in the
            // notification list because it belongs to the message preferences page.
            if (!$this->should_show_preference_key($preferencekey)) {
                continue;
            }

            // If provider component is not same or provider disabled then don't show.
            if (($provider->component != $component) ||
                    (!empty($defaultpreferences->{$preferencebase.'_disable'}))) {
                continue;
            }

            $notificationcontext = [
                'displayname' => $this->get_provider_display_name($provider),
                'preferencekey' => $preferencekey,
                'processors' => [],
            ];

            foreach ($processors as $processor) {
                $notificationprocessor = new notification_list_processor($processor, $provider, $preferences);
                $notificationcontext['processors'][] = $notificationprocessor->export_for_template($output);
            }

            $context['notifications'][] = $notificationcontext;
        }

        $context['hasnotifications'] = (count($context['notifications']) > 0);

        return $context;
    }
}
