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
 * This page contains navigation hooks for learning plans.
 *
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This function extends the user navigation.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $user The user object
 * @param context_user $usercontext The user context
 * @param stdClass $course The course object
 * @param context_course $coursecontext The context of the course
 */
function tool_lp_extend_navigation_user($navigation, $user, $usercontext, $course, $coursecontext) {
    if (!get_config('core_competency', 'enabled')) {
        return;
    }

    if (\core_competency\plan::can_read_user($user->id)) {
        $node = $navigation->add(get_string('learningplans', 'tool_lp'),
            new moodle_url('/admin/tool/lp/plans.php', array('userid' => $user->id)));

        if (\core_competency\user_evidence::can_read_user($user->id)) {
            $node->add(get_string('userevidence', 'tool_lp'),
                new moodle_url('/admin/tool/lp/user_evidence_list.php', array('userid' => $user->id)));
        }
    }

}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function tool_lp_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if (!get_config('core_competency', 'enabled')) {
        return false;
    } else if (!\core_competency\plan::can_read_user($user->id)) {
        return false;
    }

    $url = new moodle_url('/admin/tool/lp/plans.php', array('userid' => $user->id));
    $node = new core_user\output\myprofile\node('miscellaneous', 'learningplans',
                                                get_string('learningplans', 'tool_lp'), null, $url);
    $tree->add_node($node);

    return true;
}

/**
 * This function extends the category navigation to add learning plan links.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $coursecategorycontext The context of the course category
 */
function tool_lp_extend_navigation_category_settings($navigation, $coursecategorycontext) {
    if (!get_config('core_competency', 'enabled')) {
        return false;
    }

    // We check permissions before renderring the links.
    $templatereadcapability = \core_competency\template::can_read_context($coursecategorycontext);
    $competencyreadcapability = \core_competency\competency_framework::can_read_context($coursecategorycontext);
    if (!$templatereadcapability && !$competencyreadcapability) {
        return false;
    }

    // The link to the learning plan page.
    if ($templatereadcapability) {
        $title = get_string('templates', 'tool_lp');
        $path = new moodle_url("/admin/tool/lp/learningplans.php", array('pagecontextid' => $coursecategorycontext->id));
        $settingsnode = navigation_node::create($title,
                                                $path,
                                                navigation_node::TYPE_SETTING,
                                                null,
                                                null,
                                                new pix_icon('i/competencies', ''));
        if (isset($settingsnode)) {
            $navigation->add_node($settingsnode);
        }
    }

    // The link to the competency frameworks page.
    if ($competencyreadcapability) {
        $title = get_string('competencyframeworks', 'tool_lp');
        $path = new moodle_url("/admin/tool/lp/competencyframeworks.php", array('pagecontextid' => $coursecategorycontext->id));
        $settingsnode = navigation_node::create($title,
                                                $path,
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
 * Inject the competencies elements into all moodle module settings forms.
 *
 * @param moodleform $formwrapper The moodle quickforms wrapper object.
 * @param MoodleQuickForm $mform The actual form object (required to modify the form).
 */
function tool_lp_coursemodule_standard_elements($formwrapper, $mform) {
    global $CFG, $COURSE;

    if (!get_config('core_competency', 'enabled')) {
        return;
    } else if (!has_capability('moodle/competency:coursecompetencymanage', $formwrapper->get_context())) {
        return;
    }

    $mform->addElement('header', 'competenciessection', get_string('competencies', 'core_competency'));

    MoodleQuickForm::registerElementType('course_competencies',
                                         "$CFG->dirroot/$CFG->admin/tool/lp/classes/course_competencies_form_element.php",
                                         'tool_lp_course_competencies_form_element');
    $cmid = null;
    if ($cm = $formwrapper->get_coursemodule()) {
        $cmid = $cm->id;
    }
    $options = array(
        'courseid' => $COURSE->id,
        'cmid' => $cmid
    );
    $mform->addElement('course_competencies', 'competencies', get_string('modcompetencies', 'tool_lp'), $options);
    $mform->addHelpButton('competencies', 'modcompetencies', 'tool_lp');
    MoodleQuickForm::registerElementType('course_competency_rule',
                                         "$CFG->dirroot/$CFG->admin/tool/lp/classes/course_competency_rule_form_element.php",
                                         'tool_lp_course_competency_rule_form_element');
    // Reuse the same options.
    $mform->addElement('course_competency_rule', 'competency_rule', get_string('uponcoursemodulecompletion', 'tool_lp'), $options);
}

/**
 * Hook the add/edit of the course module.
 *
 * @param stdClass $data Data from the form submission.
 * @param stdClass $course The course.
 */
function tool_lp_coursemodule_edit_post_actions($data, $course) {
    if (!get_config('core_competency', 'enabled')) {
        return $data;
    }

    // It seems like the form did not contain any of the form fields, we can return.
    if (!isset($data->competency_rule) && !isset($data->competencies)) {
        return $data;
    }

    // We bypass the API here and go direct to the persistent layer - because we don't want to do permission
    // checks here - we need to load the real list of existing course module competencies.
    $existing = \core_competency\course_module_competency::list_course_module_competencies($data->coursemodule);

    $existingids = array();
    foreach ($existing as $cmc) {
        array_push($existingids, $cmc->get('competencyid'));
    }

    $newids = isset($data->competencies) ? $data->competencies : array();

    $removed = array_diff($existingids, $newids);
    $added = array_diff($newids, $existingids);

    foreach ($removed as $removedid) {
        \core_competency\api::remove_competency_from_course_module($data->coursemodule, $removedid);
    }
    foreach ($added as $addedid) {
        \core_competency\api::add_competency_to_course_module($data->coursemodule, $addedid);
    }

    if (isset($data->competency_rule)) {
        // Now update the rules for each course_module_competency.
        $current = \core_competency\api::list_course_module_competencies_in_course_module($data->coursemodule);
        foreach ($current as $coursemodulecompetency) {
            \core_competency\api::set_course_module_competency_ruleoutcome($coursemodulecompetency, $data->competency_rule);
        }
    }

    return $data;
}

/**
 * Map icons for font-awesome themes.
 */
function tool_lp_get_fontawesome_icon_map() {
    return [
        'tool_lp:url' => 'fa-external-link'
    ];
}
