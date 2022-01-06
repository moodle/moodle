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
 * Class for exporting calendar footer view options data.
 *
 * @package    core_calendar
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use renderer_base;
use stdClass;
use moodle_url;

/**
 * Class for exporting calendar footer view options data.
 *
 * @copyright  2017 Simey Lameze
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class footer_options_exporter extends exporter {

    /**
     * @var \calendar_information $calendar The calendar to be rendered.
     */
    protected $calendar;

    /**
     * @var int $userid The user id.
     */
    protected $userid;

    /**
     * @var string $token The user sha1 token.
     */
    protected $token;

    /**
     * Constructor for month_exporter.
     *
     * @param \calendar_information $calendar The calendar being represented
     * @param int $userid The user id
     * @param string $token The user sha1 token.
     */
    public function __construct(\calendar_information $calendar, $userid, $token) {
        $this->calendar = $calendar;
        $this->userid = $userid;
        $this->token = $token;
    }

    /**
     * Get the export calendar button.
     *
     * @return \single_button The export calendar button html.
     */
    protected function get_export_calendar_button() {
        $exportcalendarurl = new moodle_url('/calendar/export.php', $this->get_link_params());
        return new \single_button($exportcalendarurl, get_string('exportcalendar', 'calendar'), 'get');
    }

    /**
     * Get manage subscription button.
     *
     * @return string The manage subscription button html.
     */
    protected function get_manage_subscriptions_button() {
        if (calendar_user_can_add_event($this->calendar->course)) {
            $managesubscriptionurl = new moodle_url('/calendar/managesubscriptions.php', $this->get_link_params());
            return new \single_button($managesubscriptionurl,
                    get_string('managesubscriptions', 'calendar'), 'get');
        }
    }

    /**
     * Get the list of URL parameters for calendar links.
     *
     * @return array
     */
    protected function get_link_params() {
        $params = [];
        if (SITEID !== $this->calendar->course->id) {
            $params['course'] = $this->calendar->course->id;
        } else if (null !== $this->calendar->categoryid && $this->calendar->categoryid > 0) {
            $params['category'] = $this->calendar->categoryid;
        }

        return $params;
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        global $CFG;

        $values = new stdClass();

        if (!empty($CFG->enablecalendarexport)) {
            if ($exportbutton = $this->get_export_calendar_button()) {
                $values->exportcalendarbutton = $exportbutton->export_for_template($output);
            }
            if ($managesubscriptionbutton = $this->get_manage_subscriptions_button()) {
                $values->managesubscriptionbutton = $managesubscriptionbutton->export_for_template($output);
            }
        }

        return (array) $values;
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    public static function define_other_properties() {
        return array(
            'exportcalendarbutton' => [
                'type' => PARAM_RAW,
                'default' => null,
            ],
            'managesubscriptionbutton' => [
                'type' => PARAM_RAW,
                'default' => null,
            ],
        );
    }
}
