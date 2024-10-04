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
 * Form to edit a users forum preferences.
 *
 * These are stored as columns in the user table, which
 * is why they are in /user and not /mod/forum.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Class user_edit_forum_form.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_edit_forum_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG, $COURSE;

        $mform = $this->_form;

        $choices = array();
        $choices['0'] = get_string('emaildigestoff');
        $choices['1'] = get_string('emaildigestcomplete');
        $choices['2'] = get_string('emaildigestsubjects');
        $mform->addElement('select', 'maildigest', get_string('emaildigest'), $choices);
        $mform->setDefault('maildigest', core_user::get_property_default('maildigest'));
        $mform->addHelpButton('maildigest', 'emaildigest');

        $choices = array();
        $choices['1'] = get_string('autosubscribeyes');
        $choices['0'] = get_string('autosubscribeno');
        $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
        $mform->setDefault('autosubscribe', core_user::get_property_default('autosubscribe'));

        $choices = array();
        $choices['1'] = get_string('yes');
        $choices['0'] = get_string('no');
        $mform->addElement('select', 'useexperimentalui', get_string('useexperimentalui', 'mod_forum'), $choices);
        $mform->setDefault('useexperimentalui', '0');

        if (!empty($CFG->forum_trackreadposts)) {
            $mform->addElement('header', 'trackreadposts', get_string('trackreadposts_header', 'mod_forum'));
            $choices = array();
            $choices['0'] = get_string('trackforumsno');
            $choices['1'] = get_string('trackforumsyes');
            $mform->addElement('select', 'trackforums', get_string('trackforums'), $choices);
            $mform->setDefault('trackforums', core_user::get_property_default('trackforums'));

            $choices = [
                1   => get_string('markasreadonnotificationyes', 'mod_forum'),
                0   => get_string('markasreadonnotificationno', 'mod_forum'),
            ];
            $mform->addElement('select', 'markasreadonnotification', get_string('markasreadonnotification', 'mod_forum'), $choices);
            $mform->addHelpButton('markasreadonnotification', 'markasreadonnotification', 'mod_forum');
            $mform->disabledIf('markasreadonnotification', 'trackforums', 'eq', "0");
            $mform->setDefault('markasreadonnotification', 1);
        }

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}


