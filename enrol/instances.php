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
 * Main course enrolment management UI.
 *
 * @package    core_enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');

$id         = required_param('id', PARAM_INT); // course id
$action     = optional_param('action', '', PARAM_ALPHANUMEXT);
$instanceid = optional_param('instance', 0, PARAM_INT);
$confirm    = optional_param('confirm', 0, PARAM_BOOL);
$confirm2   = optional_param('confirm2', 0, PARAM_BOOL);

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

if ($course->id == SITEID) {
    redirect("$CFG->wwwroot/");
}

require_login($course);
require_capability('moodle/course:enrolreview', $context);

$canconfig = has_capability('moodle/course:enrolconfig', $context);

$PAGE->set_url('/enrol/instances.php', array('id'=>$course->id));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('enrolmentinstances', 'enrol'));
$PAGE->set_heading($course->fullname);

$instances = enrol_get_instances($course->id, false);
$plugins   = enrol_get_plugins(false);

if ($canconfig and $action and confirm_sesskey()) {
    if (isset($instances[$instanceid]) and isset($plugins[$instances[$instanceid]->enrol])) {
        if ($action === 'up') {
            $order = array_keys($instances);
            $order = array_flip($order);
            $pos = $order[$instanceid];
            if ($pos > 0) {
                $switch = $pos - 1;
                $resorted = array_values($instances);
                $temp = $resorted[$pos];
                $resorted[$pos] = $resorted[$switch];
                $resorted[$switch] = $temp;
                // now update db sortorder
                foreach ($resorted as $sortorder=>$instance) {
                    if ($instance->sortorder != $sortorder) {
                        $instance->sortorder = $sortorder;
                        $DB->update_record('enrol', $instance);
                    }
                }
            }
            redirect($PAGE->url);

        } else if ($action === 'down') {
            $order = array_keys($instances);
            $order = array_flip($order);
            $pos = $order[$instanceid];
            if ($pos < count($instances) - 1) {
                $switch = $pos + 1;
                $resorted = array_values($instances);
                $temp = $resorted[$pos];
                $resorted[$pos] = $resorted[$switch];
                $resorted[$switch] = $temp;
                // now update db sortorder
                foreach ($resorted as $sortorder=>$instance) {
                    if ($instance->sortorder != $sortorder) {
                        $instance->sortorder = $sortorder;
                        $DB->update_record('enrol', $instance);
                    }
                }
            }
            redirect($PAGE->url);

        } else if ($action === 'delete') {
            $instance = $instances[$instanceid];
            $plugin = $plugins[$instance->enrol];

            if ($plugin->can_delete_instance($instance)) {
                if ($confirm) {
                    if (enrol_accessing_via_instance($instance)) {
                        if (!$confirm2) {
                            $yesurl = new moodle_url('/enrol/instances.php',
                                                     array('id' => $course->id,
                                                           'action' => 'delete',
                                                           'instance' => $instance->id,
                                                           'confirm' => 1,
                                                           'confirm2' => 1,
                                                           'sesskey' => sesskey()));
                            $displayname = $plugin->get_instance_name($instance);
                            $message = markdown_to_html(get_string('deleteinstanceconfirmself',
                                                                   'enrol',
                                                                   array('name' => $displayname)));
                            echo $OUTPUT->header();
                            echo $OUTPUT->confirm($message, $yesurl, $PAGE->url);
                            echo $OUTPUT->footer();
                            die();
                        }
                    }
                    $plugin->delete_instance($instance);
                    redirect($PAGE->url);
                }

                echo $OUTPUT->header();
                $yesurl = new moodle_url('/enrol/instances.php',
                                         array('id' => $course->id,
                                               'action' => 'delete',
                                               'instance' => $instance->id,
                                               'confirm' => 1,
                                               'sesskey' => sesskey()));
                $displayname = $plugin->get_instance_name($instance);
                $users = $DB->count_records('user_enrolments', array('enrolid' => $instance->id));
                if ($users) {
                    $message = markdown_to_html(get_string('deleteinstanceconfirm', 'enrol',
                                                           array('name' => $displayname,
                                                                 'users' => $users)));
                } else {
                    $message = markdown_to_html(get_string('deleteinstancenousersconfirm', 'enrol',
                                                           array('name' => $displayname)));
                }
                echo $OUTPUT->confirm($message, $yesurl, $PAGE->url);
                echo $OUTPUT->footer();
                die();
            }

        } else if ($action === 'disable') {
            $instance = $instances[$instanceid];
            $plugin = $plugins[$instance->enrol];
            if ($plugin->can_hide_show_instance($instance)) {
                if ($instance->status != ENROL_INSTANCE_DISABLED) {
                    if (enrol_accessing_via_instance($instance)) {
                        if (!$confirm2) {
                            $yesurl = new moodle_url('/enrol/instances.php',
                                                     array('id' => $course->id,
                                                           'action' => 'disable',
                                                           'instance' => $instance->id,
                                                           'confirm2' => 1,
                                                           'sesskey' => sesskey()));
                            $displayname = $plugin->get_instance_name($instance);
                            $message = markdown_to_html(get_string('disableinstanceconfirmself',
                                                        'enrol',
                                                        array('name' => $displayname)));
                            echo $OUTPUT->header();
                            echo $OUTPUT->confirm($message, $yesurl, $PAGE->url);
                            echo $OUTPUT->footer();
                            die();
                        }
                    }
                    $plugin->update_status($instance, ENROL_INSTANCE_DISABLED);
                    redirect($PAGE->url);
                }
            }

        } else if ($action === 'enable') {
            $instance = $instances[$instanceid];
            $plugin = $plugins[$instance->enrol];
            if ($plugin->can_hide_show_instance($instance)) {
                if ($instance->status != ENROL_INSTANCE_ENABLED) {
                    $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);
                    redirect($PAGE->url);
                }
            }
        }
    }
}


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('enrolmentinstances', 'enrol'));

echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');

// display strings
$strup      = get_string('up');
$strdown    = get_string('down');
$strdelete  = get_string('delete');
$strenable  = get_string('enable');
$strdisable = get_string('disable');
$strmanage  = get_string('manageinstance', 'enrol');

$table = new html_table();
$table->head  = array(get_string('name'), get_string('users'), $strup.'/'.$strdown, get_string('edit'));
$table->align = array('left', 'center', 'center', 'center');
$table->width = '100%';
$table->data  = array();

// iterate through enrol plugins and add to the display table
$updowncount = 1;
$icount = count($instances);
$url = new moodle_url('/enrol/instances.php', array('sesskey'=>sesskey(), 'id'=>$course->id));
foreach ($instances as $instance) {
    if (!isset($plugins[$instance->enrol])) {
        continue;
    }
    $plugin = $plugins[$instance->enrol];

    $displayname = $plugin->get_instance_name($instance);
    if (!enrol_is_enabled($instance->enrol) or $instance->status != ENROL_INSTANCE_ENABLED) {
        $displayname = html_writer::tag('span', $displayname, array('class'=>'dimmed_text'));
    }

    $users = $DB->count_records('user_enrolments', array('enrolid'=>$instance->id));

    $updown = array();
    $edit = array();

    if ($canconfig) {
        if ($updowncount > 1) {
            $aurl = new moodle_url($url, array('action'=>'up', 'instance'=>$instance->id));
            $updown[] = $OUTPUT->action_icon($aurl, new pix_icon('t/up', $strup, 'core', array('class' => 'iconsmall')));
        } else {
            $updown[] = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('spacer'), 'alt'=>'', 'class'=>'iconsmall'));
        }
        if ($updowncount < $icount) {
            $aurl = new moodle_url($url, array('action'=>'down', 'instance'=>$instance->id));
            $updown[] = $OUTPUT->action_icon($aurl, new pix_icon('t/down', $strdown, 'core', array('class' => 'iconsmall')));
        } else {
            $updown[] = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('spacer'), 'alt'=>'', 'class'=>'iconsmall'));
        }
        ++$updowncount;

        if ($plugin->can_delete_instance($instance)) {
            $aurl = new moodle_url($url, array('action'=>'delete', 'instance'=>$instance->id));
            $edit[] = $OUTPUT->action_icon($aurl, new pix_icon('t/delete', $strdelete, 'core', array('class' => 'iconsmall')));
        }

        if (enrol_is_enabled($instance->enrol) && $plugin->can_hide_show_instance($instance)) {
            if ($instance->status == ENROL_INSTANCE_ENABLED) {
                $aurl = new moodle_url($url, array('action'=>'disable', 'instance'=>$instance->id));
                $edit[] = $OUTPUT->action_icon($aurl, new pix_icon('t/hide', $strdisable, 'core', array('class' => 'iconsmall')));
            } else if ($instance->status == ENROL_INSTANCE_DISABLED) {
                $aurl = new moodle_url($url, array('action'=>'enable', 'instance'=>$instance->id));
                $edit[] = $OUTPUT->action_icon($aurl, new pix_icon('t/show', $strenable, 'core', array('class' => 'iconsmall')));
            } else {
                // plugin specific state - do not mess with it!
                $edit[] = html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/show'), 'alt'=>'', 'class'=>'iconsmall'));
            }

        }
    }

    // link to instance management
    if (enrol_is_enabled($instance->enrol) && $canconfig) {
        if ($icons = $plugin->get_action_icons($instance)) {
            $edit = array_merge($edit, $icons);
        }
    }

    // Add a row to the table.
    $table->data[] = array($displayname, $users, implode('', $updown), implode('', $edit));

}
echo html_writer::table($table);

// access security is in each plugin
$candidates = array();
foreach (enrol_get_plugins(true) as $name=>$plugin) {
    if ($plugin->use_standard_editing_ui()) {
        if ($plugin->can_add_instance($course->id)) {
            // Standard add/edit UI.
            $params = array('type' => $name, 'courseid' => $course->id);
            $url = new moodle_url('/enrol/editinstance.php', $params);
            $link = $url->out(false);
            $candidates[$link] = get_string('pluginname', 'enrol_'.$name);
        }
    } else if ($url = $plugin->get_newinstance_link($course->id)) {
        // Old custom UI.
        $link = $url->out(false);
        $candidates[$link] = get_string('pluginname', 'enrol_'.$name);
    }
}

if ($candidates) {
    $select = new url_select($candidates);
    $select->set_label(get_string('addinstance', 'enrol'));
    echo $OUTPUT->render($select);
}

echo $OUTPUT->box_end();

echo $OUTPUT->footer();
