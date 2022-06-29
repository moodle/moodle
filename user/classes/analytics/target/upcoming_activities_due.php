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
     * Only upcoming stuff.
     *
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @return bool
     */
    public function can_use_timesplitting(\core_analytics\local\time_splitting\base $timesplitting): bool {
        return ($timesplitting instanceof \core_analytics\local\time_splitting\after_now);
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
     * No need to link to the insights report in this case.
     *
     * @return bool
     */
    public function link_insights_report(): bool {
        return false;
    }

    /**
     * Returns the body message for an insight of a single prediction.
     *
     * This default method is executed when the analysable used by the model generates one insight
     * for each analysable (one_sample_per_analysable === true)
     *
     * @param  \context                             $context
     * @param  \stdClass                            $user
     * @param  \core_analytics\prediction           $prediction
     * @param  \core_analytics\action[]             $actions        Passed by reference to remove duplicate links to actions.
     * @return array                                                Plain text msg, HTML message and the main URL for this
     *                                                              insight (you can return null if you are happy with the
     *                                                              default insight URL calculated in prediction_info())
     */
    public function get_insight_body_for_prediction(\context $context, \stdClass $user, \core_analytics\prediction $prediction,
            array &$actions) {
        global $OUTPUT;

        $fullmessageplaintext = get_string('youhaveupcomingactivitiesdueinfo', 'moodle', $user->firstname);

        $sampledata = $prediction->get_sample_data();
        $activitiesdue = $sampledata['core_course\analytics\indicator\activities_due:extradata'];

        if (empty($activitiesdue)) {
            // We can throw an exception here because this is a target based on assumptions and we require the
            // activities_due indicator.
            throw new \coding_exception('The activities_due indicator must be part of the model indicators.');
        }

        $activitiestext = [];
        foreach ($activitiesdue as $key => $activitydue) {

            // Human-readable version.
            $activitiesdue[$key]->formattedtime = userdate($activitydue->time);

            // We provide the URL to the activity through a script that records the user click.
            $activityurl = new \moodle_url($activitydue->url);
            $actionurl = \core_analytics\prediction_action::transform_to_forward_url($activityurl, 'viewupcoming',
                $prediction->get_prediction_data()->id);
            $activitiesdue[$key]->url = $actionurl->out(false);

            if (count($activitiesdue) === 1) {
                // We will use this activity as the main URL of this insight.
                $insighturl = $actionurl;
            }

            $activitiestext[] = $activitydue->name . ': ' . $activitiesdue[$key]->url;
        }

        foreach ($actions as $key => $action) {
            if ($action->get_action_name() === 'viewupcoming') {

                // Use it as the main URL of the insight if there are multiple activities due.
                if (empty($insighturl)) {
                    $insighturl = $action->get_url();
                }

                // Remove the 'viewupcoming' action from the list of actions for this prediction as the action has
                // been included in the link to the activity.
                unset($actions[$key]);
                break;
            }
        }

        $activitieshtml = $OUTPUT->render_from_template('core_user/upcoming_activities_due_insight_body', (object) [
            'activitiesdue' => array_values($activitiesdue),
            'userfirstname' => $user->firstname
        ]);

        return [
            FORMAT_PLAIN => $fullmessageplaintext . PHP_EOL . PHP_EOL . implode(PHP_EOL, $activitiestext) . PHP_EOL,
            FORMAT_HTML => $activitieshtml,
            'url' => $insighturl,
        ];
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

        $parentactions = parent::prediction_actions($prediction, $includedetailsaction, $isinsightuser);

        if (!$isinsightuser && $USER->id != $prediction->get_prediction_data()->sampleid) {
            return $parentactions;
        }

        // We force a lookahead of 30 days so we are sure that the upcoming activities due are shown.
        $url = new \moodle_url('/calendar/view.php', ['view' => 'upcoming', 'lookahead' => '30']);
        $pix = new \pix_icon('i/calendar', get_string('viewupcomingactivitiesdue', 'calendar'));
        $action = new \core_analytics\prediction_action('viewupcoming', $prediction,
            $url, $pix, get_string('viewupcomingactivitiesdue', 'calendar'));

        return array_merge([$action], $parentactions);
    }
}
