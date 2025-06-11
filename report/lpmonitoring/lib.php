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
 * Public API of the competency report.
 *
 * Defines the APIs used by competency reports
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2016 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $categorycontext The context of the course category
 */
function report_lpmonitoring_extend_navigation_category_settings($navigation, $categorycontext) {
    if (!get_config('core_competency', 'enabled')) {
        return false;
    }

    $canreadtemplate = \core_competency\template::can_read_context($categorycontext);
    $canmanagecompetency = \core_competency\competency_framework::can_manage_context($categorycontext);

    // Set navigation for monitoring of learning plans report.
    if ($canreadtemplate) {
        $url = new moodle_url('/report/lpmonitoring/index.php', ['pagecontextid' => $categorycontext->id]);
        $urlstats = new moodle_url('/report/lpmonitoring/stats.php', ['pagecontextid' => $categorycontext->id]);
        $urlbulkrating = new moodle_url('/report/lpmonitoring/bulkrating.php', ['pagecontextid' => $categorycontext->id]);
        $name = get_string('pluginname', 'report_lpmonitoring');
        $namestats = get_string('statslearningplan', 'report_lpmonitoring');
        $namebulkratingnode = get_string('bulkdefaultrating', 'report_lpmonitoring');
        $settingsnodestats = navigation_node::create($namestats,
                                                $urlstats,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('i/report', ''));
        $settingsnode = navigation_node::create($name,
                                                $url,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('i/report', ''));
        $bulkratingnode = navigation_node::create($namebulkratingnode,
                                                $urlbulkrating,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('i/grades', ''));
        if ($bulkratingnode->check_if_active(URL_MATCH_BASE)) {
            $bulkratingnode->make_active();
        }

        $reportsnode = navigation_node::create(get_string('competencyreports', 'report_lpmonitoring'),
                                               null,
                                               navigation_node::TYPE_CATEGORY,
                                               null,
                                               'categoryreports',
                                               new pix_icon('i/stats', ''));

        if (isset($settingsnode) && isset($reportsnode)) {
            $reportnode = $navigation->add_node($reportsnode);
            $reportnode->add_node($settingsnode);
            $reportnode->add_node($bulkratingnode);
            $reportnode->add_node($settingsnodestats);
        }
    }

    // Set navigation for scales colors setting page.
    if ($canmanagecompetency) {
        $url = new moodle_url('/report/lpmonitoring/scalecolorconfiguration.php',
                ['pagecontextid' => $categorycontext->id]);
        $name = get_string('colorconfiguration', 'report_lpmonitoring');
        $settingsnode = navigation_node::create($name,
                                                $url,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('i/competencies', ''));
        if (isset($settingsnode)) {
            $navigation->add_node($settingsnode);
        }
    }
}

/**
 * Add node to report in myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function report_lpmonitoring_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if (!get_config('core_competency', 'enabled')) {
        return false;
    } else if (!\core_competency\plan::can_read_user($user->id)) {
        return false;
    }

    $url = new moodle_url('/report/lpmonitoring/userreport.php', ['userid' => $user->id]);
    $node = new core_user\output\myprofile\node('reports', 'lpmonitoringreport',
            get_string('pluginname', 'report_lpmonitoring'), null, $url);
    $tree->add_node($node);

    return true;
}

/**
 * Serve the manage tags form as a fragment.
 *
 * @param array $args List of named arguments for the fragment loader.
 * @return string
 */
function report_lpmonitoring_output_fragment_tags($args) {
    global $CFG, $DB;

    require_once($CFG->libdir.'/formslib.php');
    require_once($CFG->dirroot . '/report/lpmonitoring/classes/form/tags.php');
    $args = (object) $args;

    $planid = $args->planid;

    $plan = new \core_competency\plan($planid);
    $cangrade = \core_competency\user_competency::can_grade_user($plan->get('userid'));
    if ($cangrade) {

        $mform = new \report_lpmonitoring\form\tags(null, ['planid' => $planid]);
        // Used to set the planid.
        $data = $DB->get_record('competency_plan', ['id' => $planid]);
        $data->tags = core_tag_tag::get_item_tags_array('report_lpmonitoring', 'competency_plan', $planid);
        $mform->set_data($data);

        if (!empty($args->jsonformdata)) {
            // If we were passed non-empty form data we want the mform to call validation functions and show errors.
            $mform->is_validated();
        }

        return $mform->render();
    }
    return "";
}
