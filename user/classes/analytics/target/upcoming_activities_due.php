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
 * Upcoming activities due target.
 *
 * @package   core
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\analytics\target;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/enrollib.php');

/**
 * Upcoming activities due target.
 *
 * @package   core
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upcoming_activities_due extends \core_analytics\local\target\binary {

    /**
     * Machine learning backends are not required to predict.
     *
     * @return bool
     */
    public static function based_on_assumptions() {
        return true;
    }

    /**
     * Only update last analysis time when analysables are processed.
     * @return bool
     */
    public function always_update_analysis_time(): bool {
        return false;
    }

    /**
     * Returns the name.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('target:upcomingactivitiesdue', 'user');
    }

    /**
     * Overwritten to show a simpler language string.
     *
     * @param  int $modelid
     * @param  \context $context
     * @return string
     */
    public function get_insight_subject(int $modelid, \context $context) {
        return get_string('youhaveupcomingactivitiesdue');
    }

    /**
     * classes_description
     *
     * @return string[]
     */
    protected static function classes_description() {
        return array(
            get_string('no'),
            get_string('yes'),
        );
    }

    /**
     * Returns the predicted classes that will be ignored.
     *
     * @return array
     */
    public function ignored_predicted_classes() {
        // No need to process users without upcoming activities due.
        return array(0);
    }

    /**
     * get_analyser_class
     *
     * @return string
     */
    public function get_analyser_class() {
        return '\core\analytics\analyser\users';
    }

    /**
     * All users are ok.
     *
     * @param \core_analytics\analysable $analysable
     * @param mixed $fortraining
     * @return true|string
     */
    public function is_valid_analysable(\core_analytics\analysable $analysable, $fortraining = true) {
        // The calendar API used by \core_course\analytics\indicator\activities_due is already checking
        // if the user has any courses.
        return true;
    }

    /**
     * Samples are users and all of them are ok.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $analysable
     * @param bool $fortraining
     * @return bool
     */
    public function is_valid_sample($sampleid, \core_analytics\analysable $analysable, $fortraining = true) {
        return true;
    }

    /**
     * Calculation based on activities due indicator.
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $analysable
     * @param int $starttime
     * @param int $endtime
     * @return float
     */
    protected function calculate_sample($sampleid, \core_analytics\analysable $analysable, $starttime = false, $endtime = false) {

        $activitiesdueindicator = $this->retrieve('\core_course\analytics\indicator\activities_due', $sampleid);
        if ($activitiesdueindicator == \core_course\analytics\indicator\activities_due::get_max_value()) {
            return 1;
        }
        return 0;
    }

    /**
     * Adds a view upcoming events action.
     *
     * @param \core_analytics\prediction $prediction
     * @param mixed $includedetailsaction
     * @param bool $isinsightuser
     * @return \core_analytics\prediction_action[]
     */
    public function prediction_actions(\core_analytics\prediction $prediction, $includedetailsaction = false,
            $isinsightuser = false) {
        global $CFG, $USER;

        $parentactions = parent::prediction_actions($prediction, $includedetailsaction);

        if (!$isinsightuser && $USER->id != $prediction->get_prediction_data()->sampleid) {
            return $parentactions;
        }

        // We force a lookahead of 30 days so we are sure that the upcoming activities due are shown.
        $url = new \moodle_url('/calendar/view.php', ['view' => 'upcoming', 'lookahead' => '30']);
        $pix = new \pix_icon('i/calendar', get_string('upcomingevents', 'calendar'));
        $action = new \core_analytics\prediction_action('viewupcoming', $prediction,
            $url, $pix, get_string('upcomingevents', 'calendar'));

        return array_merge([$action], $parentactions);
    }
}
