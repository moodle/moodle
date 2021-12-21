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
     * @var bool $showfullcalendarlink Whether the full calendar link should be displayed or not.
     */
    protected $showfullcalendarlink;

    /**
     * Constructor for month_exporter.
     *
     * @param \calendar_information $calendar The calendar being represented
     * @param int $userid The user id
     * @param string $token The user sha1 token.
     * @param array $options Display options for the footer. If an option is not set, a default value will be provided.
     *                      It consists of:
     *                      - showfullcalendarlink - bool - Whether to show the full calendar link or not. Defaults to false.
     */
    public function __construct(\calendar_information $calendar, $userid, $token, array $options = []) {
        $this->calendar = $calendar;
        $this->userid = $userid;
        $this->token = $token;
        $this->showfullcalendarlink = $options['showfullcalendarlink'] ?? false;
    }

    /**
     * Get manage subscription link.
     *
     * @return string|null The manage subscription hyperlink.
     */
    protected function get_manage_subscriptions_link(): ?string {
        if (calendar_user_can_add_event($this->calendar->course)) {
            $managesubscriptionurl = new moodle_url('/calendar/managesubscriptions.php');
            return $managesubscriptionurl->out(true);
        }
        return null;
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
        $values->footerlinks = [];

        if ($this->showfullcalendarlink) {
            $values->footerlinks[] = (object)[
                'url' => $this->get_calendar_url(),
                'linkname' => get_string('fullcalendar', 'calendar'),
            ];
        }

        if (!empty($CFG->enablecalendarexport) && $managesubscriptionlink = $this->get_manage_subscriptions_link()) {
            $values->footerlinks[] = (object)[
                'url' => $managesubscriptionlink,
                'linkname' => get_string('managesubscriptions', 'calendar'),
            ];
        }

        return (array) $values;
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    public static function define_other_properties() {
        return [
            'footerlinks' => [
                'type' => [
                    'url' => [
                        'type' => PARAM_URL,
                    ],
                    'linkname' => [
                        'type' => PARAM_TEXT,
                    ],
                ],
                'multiple' => true,
                'optional' => true,
            ],
        ];
    }

    /**
     * Build the calendar URL.
     *
     * @return string The calendar URL.
     */
    public function get_calendar_url() {
        $url = new moodle_url('/calendar/view.php', [
            'view' => 'month',
            'time' => $this->calendar->time,
        ]);

        return $url->out(false);
    }
}
