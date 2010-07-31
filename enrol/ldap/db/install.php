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
 * LDAP enrolment plugin installation.
 *
 * @package    enrol
 * @subpackage ldap
 * @author     Iñaki Arenaza
 * @copyright  2010 Iñaki Arenaza <iarenaza@eps.mondragon.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_ldap_install() {
    global $CFG, $DB;

    // Check for the presence of old 'legacy' config settings. If they
    // exist, correct them.
    if (isset($CFG->enrol_ldap_student_contexts)) {
        if ($student_role = $DB->get_record('role', array('shortname' => 'student'))) {
            set_config('enrol_ldap_contexts_role'.$student_role->id, $CFG->enrol_ldap_student_contexts);
        }
        unset_config('enrol_ldap_student_contexts');
    }

    if (isset($CFG->enrol_ldap_student_memberattribute)) {
        if (isset($student_role) or $student_role = $DB->get_record('role', array('shortname' => 'student'))) {
            set_config('enrol_ldap_memberattribute_role'.$student_role->id, $CFG->enrol_ldap_student_memberattribute);
        }
        unset_config('enrol_ldap_student_memberattribute');
    }

    if (isset($CFG->enrol_ldap_teacher_contexts)) {
        if ($teacher_role = $DB->get_record('role', array('shortname' => 'editingteacher'))) {
            set_config('enrol_ldap_contexts_role'.$teacher_role->id, $CFG->enrol_ldap_student_contexts);
        }
        unset_config('enrol_ldap_teacher_contexts');
    }

    if (isset($CFG->enrol_ldap_teacher_memberattribute)) {
        if (isset($teacher_role) or $teacher_role = $DB->get_record('role', array('shortname' => 'teacher'))) {
            set_config('enrol_ldap_memberattribute_role'.$teacher_role->id, $CFG->enrol_ldap_teacher_memberattribute);
        }

        unset_config('enrol_ldap_teacher_memberattribute');
    }

    // Now migrate the real plugin settings. 'enrol_ldap_version' is the only
    // setting that cannot be migrated like the rest, as it clashes with the
    // plugin version number once we strip the 'enrol_ldap_' prefix.
    if (isset($CFG->enrol_ldap_version)) {
        set_config('ldap_version', $CFG->enrol_ldap_version, 'enrol_ldap');
        unset_config('enrol_ldap_version');
    }

    $settings = array ('host_url', 'bind_dn', 'bind_pw', 'objectclass', 'course_idnumber',
                       'course_shortname', 'course_fullname', 'course_summary',
                       'course_shortname_updatelocal', 'course_fullname_updatelocal', 'course_summary_updatelocal',
                       'course_shortname_editlock', 'course_fullname_editlock', 'course_summary_editlock',
                       'autocreate', 'category', 'template');

    // Add roles settings to the array of settings to migrate.
    $roles = $DB->get_records('role');
    foreach($roles as $role) {
        array_push($settings, 'contexts_role'.$role->id);
        array_push($settings, 'memberattribute_role'.$role->id);
    }

    // Migrate all the settings from $CFG->enrol_ldap_XXXXX to the
    // plugin configuration in mdl_config_plugin, stripping the 'enrol_ldap_' prefix.
    foreach ($settings as $setting) {
        if (isset($CFG->{'enrol_ldap_'.$setting})) {
            set_config($setting, $CFG->{'enrol_ldap_'.$setting}, 'enrol_ldap');
            unset_config('enrol_ldap_'.$setting);
        }
    }

    // $CFG->enrol_ldap_search_sub is special, as we need to rename it to
    // course_search_sub' (there is also a new search_sub for users)
    if (isset($CFG->enrol_ldap_search_sub)) {
        set_config('course_search_sub', $CFG->enrol_ldap_search_sub, 'enrol_ldap');
        unset_config('enrol_ldap_search_sub');
    }

    // Remove a setting that's never been used at all
    if (isset($CFG->enrol_ldap_user_memberfield)) {
        unset_config('enrol_ldap_user_memberfield');
    }
}
