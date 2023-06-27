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

/*
 * @package     block_use_stats
 * @category    blocks
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

require('../../config.php');
require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

$config = get_config('block_use_stats');

$courseid = required_param('course', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$id = required_param('id', PARAM_INT); // ID of the calling use_stat block.
$fromwhen = optional_param('ts_from', @$config->fromwhen, PARAM_INT);
$towhen = optional_param('ts_to', time(), PARAM_INT);
$onlycourse = optional_param('restrict', false, PARAM_BOOL);

// Security.

require_login($courseid);

if ($COURSE->id > SITEID) {
    $returnurl = new moodle_url('/course/view.php', array('id' => $COURSE->id));
} else {
    $returnurl = $CFG->wwwroot;
}

$blockcontext = context_block::instance($id);
$coursecontext = context_course::instance($COURSE->id);
$systemcontext = context_system::instance();

// Check for capability to view user details and resolve.
$cansee = false;
if (has_capability('block/use_stats:seesitedetails', $blockcontext)) {
    $cansee = true;
} else if ($USER->id != $userid) {
    if (has_capability('block/use_stats:seegroupdetails', $blockcontext)) {
        // If not in a group of mine, is an error.
        $mygroups = groups_get_user_groups($COURSE->id);
        $groups = array();
        foreach ($mygroups as $grouping) {
            $groups = $groups + $grouping;
        }
        if (!empty($groups)) {
            foreach (array_keys($groups) as $groupid) {
                if (groups_is_member($groupid)) {
                    $cansee = true;
                    break;
                }
            }
        }
    }

    if (has_capability('block/use_stats:seecoursedetails', $blockcontext)) {
        // If not user in current course of mine, is an error.
        if (has_capability('moodle/course:view', $coursecontext, $userid)) {
            $cansee = true;
        }
    }

    // Final resolution.
} else {
    if (has_capability('block/use_stats:seeowndetails', $blockcontext)) {
        $cansee = true;
    }
}

if (!$cansee) {
    print_error('notallowed', 'block_use_stats');
}

$fields = 'id,'.get_all_user_name_fields(true, '').',picture,imagealt,email';
$user = $DB->get_record('user', array('id' => $userid), $fields);

$PAGE->set_title(get_string('modulename', 'block_use_stats'));
$PAGE->set_heading('');
$PAGE->set_focuscontrol('');
$PAGE->set_cacheable(true);
$PAGE->set_button('');
$params = array('courseid' => $courseid, 'is' => $id, 'userid' => $userid);
$PAGE->set_url(new moodle_url('/blocks/use_stats/detail.php', $params));
$PAGE->set_headingmenu('');
$PAGE->navbar->add(get_string('blockname', 'block_use_stats'));
$PAGE->navbar->add(fullname($user, has_capability('moodle/site:viewfullnames', context_system::instance())));
echo $OUTPUT->header();

$daystocompilelogs = $fromwhen * DAYSECS;
$timefrom = $towhen - $daystocompilelogs;

echo '<table class="list" summary=""><tr><td>';
echo $OUTPUT->user_picture($user, array('size' => 100));
echo '</td><td>';
$userurl = new moodle_url('/user/view.php', array('id' => $user->id));
echo '<h2><a href="'.$userurl.'">'.fullname($user, has_capability('moodle/site:viewfullnames', $systemcontext)).'</a></h2>';
echo '<table class="list" summary="" width="100%">';
profile_display_fields($user->id);
echo '</table>';
echo '</td></tr></table>';

$logs = use_stats_extract_logs($timefrom, $towhen, $userid);

// Log aggregation function.

$aggregate = use_stats_aggregate_logs($logs, 'module', 0, $fromwhen, $towhen);

$dimensionitemstr = get_string('dimensionitem', 'block_use_stats');
$timestr = get_string('timeelapsed', 'block_use_stats');
$eventsstr = get_string('eventscount', 'block_use_stats');

$table = new html_table();
$table->head = array("<b>$dimensionitemstr</b>", "<b>$timestr</b>", "<b>$eventsstr</b>");
$table->width = '100%';
$table->size = array('70%', '30%');
$table->align = array('left', 'left');

foreach ($aggregate as $module => $moduleset) {
    if (preg_match('/label$/', $module)) {
        continue;
    }
    $table->data[] = array("<b>$module</b>", '');
    foreach ($moduleset as $key => $value) {
        if (!is_object($value)) {
            continue;
        }
        if ($module != 'course' && $module != 'coursetotal') {
            $cm = $DB->get_record('course_modules', array('id' => $key));
            if ($cm) {
                $module = $DB->get_record('modules', array('id' => $cm->module));
                if ($modrec = $DB->get_record($module->name, array('id' => $cm->instance))) {
                    $table->data[] = array($modrec->name, block_use_stats_format_time($value->elapsed, 'html'), 0 + @$value->events);
                }
            } else {
                $table->data[] = array('', block_use_stats_format_time(0 + @$value->elapsed, 'html'));
            }
        } else {
            if ($course = $DB->get_record('course', array('id' => $key))) {
                $table->data[] = array($course->shortname.' ('.$course->id.')', block_use_stats_format_time(0 + @$value->elapsed, 'html'));
            } else {
                $table->data[] = array('N.C.', block_use_stats_format_time(0 + @$value->elapsed, 'html'));
            }
        }
    }
}

if (!empty($table->data)) {
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('errornorecords', 'block_use_stats'), $returnurl);
}

echo $OUTPUT->continue_button($returnurl);

echo $OUTPUT->footer();
