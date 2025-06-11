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
 * Step definitions to add enrolment.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
// For that reason, we can't even rely on $CFG->admin being available here.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Step definitions to add enrolment.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_availability_relativedate extends behat_base {
    /**
     * See a relative date
     * @Then /^I should see relativedate "([^"]*)"$/
     * @param string $date
     */
    public function i_should_see_relativedate($date) {
        $user = self::get_session_user();
        $times = array_filter(explode('##', $date));
        $time = reset($times);
        $stime = userdate($time, get_string('strftimedate', 'langconfig'), $user->timezone);
        $this->execute("behat_general::assert_element_contains_text", [$stime, '.course-content', 'css_element']);
    }

    /**
     * Add a self enrolment method starting
     * @Given /^selfenrolment exists in course "(?P<course>[^"]*)" starting "(?P<date>[^"]*)"$/
     * @param string $course
     * @param string $date
     */
    public function selfenrolment_exists_in_course_starting($course, $date) {
        $this->config_self_enrolment($course, $date, '');
    }

    /**
     * Add a self enrolment method ending
     * @Given /^selfenrolment exists in course "(?P<course>[^"]*)" ending "(?P<date>[^"]*)"$/
     * @param string $course
     * @param string $date
     */
    public function selfenrolment_exists_in_course_ending($course, $date) {
        $this->config_self_enrolment($course, '', $date);
    }

    /**
     * Make one activity available after another
     * @Given /^I make "(?P<activity2>[^"]*)" relative date depending on "(?P<activity1>[^"]*)"$/
     * @param string $activity1
     * @param string $activity2
     */
    public function i_make_activity_relative_date_depending_on($activity1, $activity2) {
        global $DB;
        $cm1 = $this->get_course_module_for_identifier($activity1);
        $cm2 = $this->get_course_module_for_identifier($activity2);
        if ($cm1 && $cm2) {
            $str = '{"op":"|","c":[{"type":"relativedate","n":1,"d":1,"s":7,"m":' . $cm1->id . '}],"show":true}';
            $DB->set_field('course_modules', 'availability', $str, ['id' => $cm2->id]);
        }
        $this->execute('behat_general::i_run_all_adhoc_tasks');
        core_courseformat\base::reset_course_cache(0);
        get_fast_modinfo(0, 0, true);
    }

    /**
     * Configure self enrolment
     * @param string $course
     * @param string $start
     * @param string $end
     */
    private function config_self_enrolment($course, $start, $end) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/enrol/self/lib.php');
        $courseid = $this->get_course_id($course);
        $selfplugin = enrol_get_plugin('self');
        $instance = $DB->get_record('enrol', ['courseid' => $courseid, 'enrol' => 'self'], '*', MUST_EXIST);
        $instance->customint6 = 1;
        $instance->enrolstartdate = $this->get_transformed_timestamp($start);
        $instance->enrolenddate = $this->get_transformed_timestamp($end);
        $DB->update_record('enrol', $instance);
        $selfplugin->update_status($instance, ENROL_INSTANCE_ENABLED);
    }

    /**
     * Return timestamp for the time passed.
     *
     * @param string $time time to convert
     * @return string
     */
    protected function get_transformed_timestamp($time) {
        if ($time === '') {
            return 0;
        }
        if (intval($time) > 0) {
            return $time;
        }
        $timepassed = array_filter(explode('##', $time));
        $first = reset($timepassed);
        $sfirst = strtotime($first);
        return ($sfirst == '') ? $first : $sfirst;
    }
}
