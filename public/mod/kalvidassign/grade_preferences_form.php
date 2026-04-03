<?php
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
 * Kaltura grade preferences form.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once(dirname(dirname(dirname(__FILE__))).'/course/moodleform_mod.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->libdir.'/formslib.php');

class kalvidassign_gradepreferences_form extends moodleform {
    /**
     * This function defines all of the elements displayed on the grade preferences form.
     */
    public function definition() {
        global $CFG, $COURSE, $USER;

        $mform =& $this->_form;

        $mform->addElement('hidden', 'cmid', $this->_customdata['cmid']);
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('header', 'kal_vid_subm_hdr', get_string('optionalsettings', 'kalvidassign'));

        $context = context_module::instance($this->_customdata['cmid']);

        $group_opt = array();
        $groups    = array();

        // If the user doesn't have access to all group print the groups they have access to
        if (!has_capability('moodle/site:accessallgroups', $context)) {

            // Determine the groups mode
            switch($this->_customdata['groupmode']) {
                case NOGROUPS:
                    // No groups, do nothing
                    break;
                case SEPARATEGROUPS:
                    $groups = groups_get_all_groups($COURSE->id, $USER->id);
                    break;
                case VISIBLEGROUPS:
                    $groups = groups_get_all_groups($COURSE->id);
                    break;
            }

            $group_opt[0] = get_string('all', 'mod_kalvidassign');

            foreach ($groups as $group_obj) {
                $group_opt[$group_obj->id] = $group_obj->name;
            }

        } else {
            $groups = groups_get_all_groups($COURSE->id);

            $group_opt[0] = get_string('all', 'mod_kalvidassign');

            foreach ($groups as $group_obj) {
                $group_opt[$group_obj->id] = $group_obj->name;
            }

        }

        $mform->addElement('select', 'group_filter', get_string('group_filter', 'mod_kalvidassign'), $group_opt);

        $filters = array(
            KALASSIGN_ALL => get_string('all', 'kalvidassign'),
            KALASSIGN_REQ_GRADING => get_string('reqgrading', 'kalvidassign'),
            KALASSIGN_SUBMITTED => get_string('submitted', 'kalvidassign')
        );

        $mform->addElement('select', 'filter', get_string('show'), $filters);
        $mform->addHelpButton('filter', 'show', 'kalvidassign');

        $mform->addElement('text', 'perpage', get_string('pagesize', 'kalvidassign'), array('size' => 3, 'maxlength' => 3));
        $mform->setType('perpage', PARAM_INT);
        $mform->addHelpButton('perpage', 'pagesize', 'kalvidassign');

        $mform->addElement('checkbox', 'quickgrade', get_string('quickgrade', 'kalvidassign'));
        $mform->setDefault('quickgrade', '');
        $mform->addHelpButton('quickgrade', 'quickgrade', 'kalvidassign');

        $savepref = get_string('savepref', 'kalvidassign');

        $mform->addElement('submit', 'savepref', $savepref);
    }

    /**
     * This funciton validates te submitted data.
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (0 == (int) $data['perpage']) {
            $errors['perpage'] = get_string('invalidperpage', 'kalvidassign');
        }

        return $errors;
    }
}