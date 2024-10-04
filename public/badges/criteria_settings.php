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
 * Page for editing badges criteria settings.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2013 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/criteria_form.php');

$badgeid = optional_param('badgeid', 0, PARAM_INT); // Badge ID.
$type    = optional_param('type', 0, PARAM_INT); // Criteria type.
$edit    = optional_param('edit', 0, PARAM_INT); // Edit criteria ID.
$crit    = optional_param('crit', 0, PARAM_INT); // Criteria ID for managing params.
$param   = optional_param('param', '', PARAM_TEXT); // Param name for managing params.
$goback    = optional_param('cancel', '', PARAM_TEXT);
$addcourse = optional_param('addcourse', '', PARAM_TEXT);
$submitcourse = optional_param('submitcourse', '', PARAM_TEXT);

require_login();

$return = new moodle_url('/badges/criteria.php', array('id' => $badgeid));
$badge = new badge($badgeid);
$title = [get_string('addcriterion', 'badges'), $badge->name];
$context = $badge->get_context();
$navurl = new moodle_url('/badges/index.php', array('type' => $badge->type));

require_capability('moodle/badges:configurecriteria', $context);

if (!empty($goback)) {
    redirect($return);
}

// Make sure that no actions available for locked or active badges.
if ($badge->is_active() || $badge->is_locked()) {
    redirect($return);
}

// Make sure the criteria type is accepted.
$accepted = $badge->get_accepted_criteria();
if (!in_array($type, $accepted)) {
    redirect($return);
}

if ($badge->type == BADGE_TYPE_COURSE) {
    require_login($badge->courseid);
    $course = get_course($badge->courseid);
    $heading = format_string($course->fullname, true, ['context' => $context]);
    $title[] = $heading;
    $navurl = new moodle_url('/badges/index.php', array('type' => $badge->type, 'id' => $badge->courseid));
    $PAGE->set_pagelayout('standard');
    navigation_node::override_active_url($navurl);
} else {
    $PAGE->set_pagelayout('admin');
    $heading = get_string('administrationsite');
    navigation_node::override_active_url($navurl, true);
}

$urlparams = array('badgeid' => $badgeid, 'edit' => $edit, 'type' => $type, 'crit' => $crit);
$PAGE->set_context($context);
$PAGE->set_url('/badges/criteria_settings.php', $urlparams);
$PAGE->set_heading($heading);
$PAGE->set_title(implode(\moodle_page::TITLE_SEPARATOR, $title));
$PAGE->navbar->add($badge->name, new moodle_url('overview.php', array('id' => $badge->id)))
    ->add(get_string('bcriteria', 'badges'), new moodle_url('criteria.php', ['id' => $badge->id]))
    ->add(get_string('criteria_' . $type, 'badges'));

$cparams = array('criteriatype' => $type, 'badgeid' => $badge->id);
if ($edit) {
    $criteria = $badge->criteria[$type];
    $msg = 'criteriaupdated';
} else {
    $criteria = award_criteria::build($cparams);
    $msg = 'criteriacreated';
}

$mform = new edit_criteria_form($FULLME, array('criteria' => $criteria, 'addcourse' => $addcourse, 'course' => $badge->courseid));

if (!empty($addcourse)) {
    if ($data = $mform->get_data()) {
        // If no criteria yet, add overall aggregation.
        if (count($badge->criteria) == 0) {
            $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
            $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL));
        }

        $id = $criteria->add_courses($data->courses);
        redirect(new moodle_url('/badges/criteria_settings.php',
            array('badgeid' => $badgeid, 'edit' => true, 'type' => BADGE_CRITERIA_TYPE_COURSESET, 'crit' => $id)));
    }
} else if ($data = $mform->get_data()) {
    // If no criteria yet, add overall aggregation.
    if (count($badge->criteria) == 0) {
        $criteria_overall = award_criteria::build(array('criteriatype' => BADGE_CRITERIA_TYPE_OVERALL, 'badgeid' => $badge->id));
        $criteria_overall->save(array('agg' => BADGE_CRITERIA_AGGREGATION_ALL));
    }
    $criteria->save((array)$data);
    $return->param('msg', $msg);
    redirect($return);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
