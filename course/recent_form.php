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
 * Display all recent activity in a flexible way
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir.'/formslib.php');

class recent_form extends moodleform {
    function definition() {
        global $CFG, $COURSE, $USER;

        $mform =& $this->_form;
        $context = context_course::instance($COURSE->id);
        $modinfo = get_fast_modinfo($COURSE);

        $mform->addElement('header', 'filters', get_string('managefilters')); //TODO: add better string

        $groupoptions = array();
        if (groups_get_course_groupmode($COURSE) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
            // limited group access
            $groups = groups_get_user_groups($COURSE->id);
            $allgroups = groups_get_all_groups($COURSE->id);
            if (!empty($groups[$COURSE->defaultgroupingid])) {
                foreach ($groups[$COURSE->defaultgroupingid] AS $groupid) {
                    $groupoptions[$groupid] = format_string($allgroups[$groupid]->name, true, array('context'=>$context));
                }
            }
        } else {
            $groupoptions = array('0'=>get_string('allgroups'));
            if (has_capability('moodle/site:accessallgroups', $context)) {
                // user can see all groups
                $allgroups = groups_get_all_groups($COURSE->id);
            } else {
                // user can see course level groups
                $allgroups = groups_get_all_groups($COURSE->id, 0, $COURSE->defaultgroupingid);
            }
            foreach($allgroups as $group) {
                $groupoptions[$group->id] = format_string($group->name, true, array('context'=>$context));
            }
        }

        if ($COURSE->id == SITEID) {
            $viewparticipants = course_can_view_participants(context_system::instance());
        } else {
            $viewparticipants = course_can_view_participants($context);
        }

        if ($viewparticipants) {
            $viewfullnames = has_capability('moodle/site:viewfullnames', context_course::instance($COURSE->id));

            $options = array();
            $options[0] = get_string('allparticipants');
            $options[$CFG->siteguest] = get_string('guestuser');

            if (isset($groupoptions[0])) {
                // can see all enrolled users
                if ($enrolled = get_enrolled_users($context, null, 0, user_picture::fields('u'))) {
                    foreach ($enrolled as $euser) {
                        $options[$euser->id] = fullname($euser, $viewfullnames);
                    }
                }
            } else {
                // can see users from some groups only
                foreach ($groupoptions as $groupid=>$unused) {
                    if ($enrolled = get_enrolled_users($context, null, $groupid, user_picture::fields('u'))) {
                        foreach ($enrolled as $euser) {
                            if (!array_key_exists($euser->id, $options)) {
                                $options[$euser->id] = fullname($euser, $viewfullnames);
                            }
                        }
                    }
                }
            }

            $mform->addElement('select', 'user', get_string('participants'), $options);
            $mform->setAdvanced('user');
        } else {
            // Default to no user.
            $mform->addElement('hidden', 'user', 0);
            $mform->setType('user', PARAM_INT);
            $mform->setConstant('user', 0);
        }

        $options = array(''=>get_string('allactivities'));
        $modsused = array();

        foreach($modinfo->cms as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            $modsused[$cm->modname] = true;
        }

        foreach ($modsused as $modname=>$unused) {
            $libfile = "$CFG->dirroot/mod/$modname/lib.php";
            if (!file_exists($libfile)) {
                unset($modsused[$modname]);
                continue;
            }
            include_once($libfile);
            $libfunction = $modname."_get_recent_mod_activity";
            if (!function_exists($libfunction)) {
                unset($modsused[$modname]);
                continue;
            }
            $options["mod/$modname"] = get_string('allmods', '', get_string('modulenameplural', $modname));
        }

        foreach ($modinfo->sections as $section=>$cmids) {
            $options["section/$section"] = "-- ".get_section_name($COURSE, $section)." --";
            foreach ($cmids as $cmid) {
                $cm = $modinfo->cms[$cmid];
                if (empty($modsused[$cm->modname]) or !$cm->uservisible) {
                    continue;
                }
                $options[$cm->id] = format_string($cm->name);
            }
        }
        $mform->addElement('select', 'modid', get_string('activities'), $options);
        $mform->setAdvanced('modid');


        if ($groupoptions) {
            $mform->addElement('select', 'group', get_string('groups'), $groupoptions);
            $mform->setAdvanced('group');
        } else {
            // no access to groups in separate mode
            $mform->addElement('hidden','group');
            $mform->setType('group', PARAM_INT);
            $mform->setConstants(array('group'=>-1));
        }

        $options = array('default'  => get_string('bycourseorder'),
                         'dateasc'  => get_string('datemostrecentlast'),
                         'datedesc' => get_string('datemostrecentfirst'));
        $mform->addElement('select', 'sortby', get_string('sortby'), $options);
        $mform->setAdvanced('sortby');

        $mform->addElement('date_time_selector', 'date', get_string('since'), array('optional'=>true));

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons(false, get_string('showrecent'));
    }
}
