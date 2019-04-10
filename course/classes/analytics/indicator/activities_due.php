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
 * Activities due indicator.
 *
 * @package   core
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\analytics\indicator;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/calendar/externallib.php');

/**
 * Activities due indicator.
 *
 * @package   core
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activities_due extends \core_analytics\local\indicator\binary {

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('indicator:activitiesdue');
    }

    /**
     * required_sample_data
     *
     * @return string[]
     */
    public static function required_sample_data() {
        return array('user');
    }

    /**
     * calculate_sample
     *
     * @param int $sampleid
     * @param string $sampleorigin
     * @param int $starttime
     * @param int $endtime
     * @return float
     */
    protected function calculate_sample($sampleid, $sampleorigin, $starttime = false, $endtime = false) {

        $actionevents = \core_calendar_external::get_calendar_action_events_by_timesort($starttime, $endtime, 0, 1,
            true, $sampleid);

        if ($actionevents->events) {

            // We first need to check that at least one of the core_calendar_provide_event_action
            // callbacks has the $userid param.
            foreach ($actionevents->events as $event) {
                $nparams = $this->get_provide_event_action_num_params($event->modulename);
                if ($nparams > 2) {
                    return self::get_max_value();
                }
            }
        }

        return self::get_min_value();
    }

    /**
     * Returns the number of params declared in core_calendar_provide_event_action's implementation.
     *
     * @param  string $modulename The module name
     * @return int
     */
    private function get_provide_event_action_num_params(string $modulename) {
        $functionname = 'mod_' . $modulename . '_core_calendar_provide_event_action';
        $reflection = new \ReflectionFunction($functionname);
        return $reflection->getNumberOfParameters();
    }
}
