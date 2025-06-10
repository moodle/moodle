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
 * Version information
 *
 * @package    tool
 * @subpackage mergeusers
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php'); /// forms library

/**
 * Define form snippet for getting the userids of the two users to merge
 */
class mergeuserform extends moodleform {

    /**
     * Form definition
     *
     * @uses $CFG
     */
    public function definition() {

        $mform =& $this->_form;

        $strrequired = get_string('required');

        $idstype = array(
            'username' => get_string('username'),
            'idnumber' => get_string('idnumber'),
            'id'       => 'Id',
        );

        $searchfields = array(
            'idnumber' => get_string('idnumber'),
            ''          => get_string('all'),
            'id'        => 'Id',
            'username'  => get_string('username'),
            'firstname' => get_string('firstname'),
            'lastname'  => get_string('lastname'),
            'email'     => get_string('email'),
        );

        $mform->addElement('header', 'mergeusers', get_string('header', 'tool_mergeusers'));

        // Add elements
        $searchuser = array();
        $searchuser[] = $mform->createElement('text', 'searcharg');
        $searchuser[] = $mform->createElement('select', 'searchfield', '', $searchfields, '');
        $mform->addGroup($searchuser, 'searchgroup', get_string('searchuser', 'tool_mergeusers'));
        $mform->setType('searchgroup[searcharg]', PARAM_TEXT);
        $mform->addHelpButton('searchgroup', 'searchuser', 'tool_mergeusers');

        $mform->addElement('static', 'mergeusersadvanced', get_string('mergeusersadvanced', 'tool_mergeusers'));
        $mform->addHelpButton('mergeusersadvanced', 'mergeusersadvanced', 'tool_mergeusers');
        $mform->setAdvanced('mergeusersadvanced');

        $olduser = array();
        $olduser[] = $mform->createElement('text', 'olduserid', "", 'size="10"');
        $olduser[] = $mform->createElement('select', 'olduseridtype', '', $idstype, '');
        $mform->addGroup($olduser, 'oldusergroup', get_string('olduserid', 'tool_mergeusers'));
        $mform->setType('oldusergroup[olduserid]', PARAM_RAW_TRIMMED);
        $mform->setAdvanced('oldusergroup');

        $newuser = array();
        $newuser[] = $mform->createElement('text', 'newuserid', "", 'size="10"');
        $newuser[] = $mform->createElement('select', 'newuseridtype', '', $idstype, '');
        $mform->addGroup($newuser, 'newusergroup', get_string('newuserid', 'tool_mergeusers'));
        $mform->setType('newusergroup[newuserid]', PARAM_RAW_TRIMMED);
        $mform->setAdvanced('newusergroup');

        $this->add_action_buttons(false, get_string('search'));
    }
}
