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
 * Contains event class for displaying a calendar event's subscription.
 *
 * @package   core_calendar
 * @copyright 2017 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use \core\external\exporter;
use \core_calendar\local\event\entities\event_interface;

/**
 * Class for displaying a calendar event's subscription.
 *
 * @package   core_calendar
 * @copyright 2017 Simey Lameze <simey@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_subscription_exporter extends exporter {

    /**
     * Constructor.
     *
     * @param event_interface $event
     */
    public function __construct(event_interface $event) {
        global $CFG;

        $data = new \stdClass();
        $data->displayeventsource = false;
        if ($event->get_subscription()) {
            $subscription = calendar_get_subscription($event->get_subscription()->get('id'));
            if (!empty($subscription) && $CFG->calendar_showicalsource) {
                $data->displayeventsource = true;
                if (!empty($subscription->url)) {
                    $data->subscriptionurl = $subscription->url;
                }
                $data->subscriptionname = $subscription->name;
            }
        }

        parent::__construct($data);
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'displayeventsource' => [
                'type' => PARAM_BOOL
            ],
            'subscriptionname' => [
                'type' => PARAM_RAW,
                'optional' => true
            ],
            'subscriptionurl' => [
                'type' => PARAM_URL,
                'optional' => true
            ],
        ];
    }
}
