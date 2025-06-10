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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class setting_form extends moodleform {
    public function definition() {
        $m =& $this->_form;

        $user = $this->_customdata['user'];

        $s = ues::gen_str('block_cps');

        $isadmin    = is_siteadmin();
        $isteacher  = cps_setting::is_valid(ues_user::sections(true));
        $altexists  = strlen($user->alternatename) > 0;
        $fieldtype  = !$altexists || $isteacher ? 'text' : 'static';
        $m->addElement($fieldtype, 'user_firstname', $s('user_firstname'));
        $m->setDefault('user_firstname', $user->firstname);
        $m->setType('user_firstname', PARAM_TEXT);

        if ($isteacher) {
            $m->addElement('checkbox', 'user_grade_restore', $s('grade_restore'));
            $m->addHelpButton('user_grade_restore', 'grade_restore', 'block_cps');
        }

        $m->addElement('hidden', 'id', $user->id);
        $m->setType('id', PARAM_INT);

        $buttons = array(
            $m->createElement('cancel')
        );

        // Only show the save button if the user has not already savfed a preferred name.
        if (!$altexists || $isadmin || $isteacher) {
            array_unshift($buttons, $m->createElement('submit', 'save', get_string('savechanges')));
        }

        $m->addGroup($buttons, 'buttons', '&nbsp;', array(' '), false);
    }
}

class setting_search_form extends moodleform {
    public function definition() {
        $m =& $this->_form;

        $m->addElement('text', 'username', get_string('username'));
        $m->setType('username', PARAM_ALPHANUMEXT);

        $m->addElement('text', 'idnumber', get_string('idnumber'));
        $m->setType('idnumber', PARAM_ALPHANUM);

        $buttons = array(
            $m->createElement('submit', 'search', get_string('search')),
            $m->createElement('cancel')
        );

        $m->addGroup($buttons, 'buttons', '&nbsp;', array(' '), false);
    }
}
