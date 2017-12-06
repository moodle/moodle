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
 * @package dataformfield
 * @subpackage entryauthor
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_entryauthor_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $edit = !empty($options['edit']);

        // No edit mode.
        $replacements = array();

        foreach ($patterns as $pattern) {
            list(, $internalname) = explode(':', trim($pattern, '[]'));

            if ($internalname == 'edit') {
                if ($edit and has_capability('mod/dataform:manageentries', $field->get_df()->context)) {
                    $replacements[$pattern] = array(array($this, 'display_edit'), array($entry));
                } else {
                    $replacements[$pattern] = $this->display_name($entry);
                }
            } else if ($internalname == 'picturelarge') {
                    $replacements[$pattern] = $this->display_picture($entry, true);

            } else {
                $replacements[$pattern] = $this->{"display_$internalname"}($entry);
            }
        }
        return $replacements;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {
        global $USER;

        if ($entry->id < 0) {
            // New entry.
            $entry->firstname = $USER->firstname;
            $entry->lastname = $USER->lastname;
            $entry->userid = $USER->id;
        }

        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "entry_{$entryid}_userid";

        $selected = $entry->userid;

        static $usersmenu = null;
        if (is_null($usersmenu)) {
            $users = $field->df->grade_manager->get_gradebook_users();
            $users[$USER->id] = $USER;
            // Add a supervisor's id.
            if (!in_array($entry->userid, array_keys($users))) {
                $user = new \stdClass;
                $user->id = $entry->userid;
                $user->firstname = $entry->firstname;
                $user->lastname = $entry->lastname;
                $users[$entry->userid] = $user;
            }
        }

        $usermenu = array();
        foreach ($users as $userid => $user) {
            $usermenu[$userid] = $user->firstname. ' '. $user->lastname;
        }
        $mform->addElement('select', $fieldname, null, $usermenu);
        $mform->setDefault($fieldname, $selected);
    }

    /**
     *
     */
    public function display_name($entry) {
        global $USER;

        if ($entry->id < 0) {
            // New entry.
            $allnames = get_all_user_name_fields();
            foreach ($allnames as $allname) {
                $entry->$allname = $USER->$allname;
            }
            $entry->userid = $USER->id;
        }

        $df = $this->_field->get_df();
        return html_writer::link(new moodle_url('/user/view.php', array('id' => $entry->userid, 'course' => $df->course->id)), fullname($entry));
    }

    /**
     *
     */
    public function display_firstname($entry) {
        global $USER;

        if ($entry->id < 0) {
            // New entry.
            return $USER->firstname;
        } else {
            return $entry->firstname;
        }
    }

    /**
     *
     */
    public function display_lastname($entry) {
        global $USER;

        if ($entry->id < 0) {
            // New entry.
            return $USER->lastname;
        } else {
            return $entry->lastname;
        }
    }

    /**
     *
     */
    public function display_username($entry) {
        global $USER;

        if ($entry->id < 0) {
            // New entry.
            return $USER->username;
        } else {
            return $entry->username;
        }
    }

    /**
     *
     */
    public function display_id($entry) {
        global $USER;

        if ($entry->id < 0) {
            // New entry.
            return $USER->id;
        } else {
            return $entry->userid;
        }
    }

    /**
     *
     */
    public function display_idnumber($entry) {
        global $USER;

        if ($entry->id < 0) {
            // New entry.
            return $USER->idnumber;
        } else {
            return $entry->idnumber;
        }
    }

    /**
     *
     */
    public function display_picture($entry, $large = false) {
        global $USER, $OUTPUT;

        if ($entry->id < 0) {
            // New entry.
            $user = $USER;
        } else {
            $user = new stdClass;
            foreach (explode(',', user_picture::fields()) as $userfield) {
                if ($userfield == 'id') {
                    $user->id = $entry->uid;
                } else {
                    $user->{$userfield} = $entry->{$userfield};
                }
            }
        }

        $pictureparams = array('courseid' => $this->_field->get_df()->course->id);
        if ($large) {
            $pictureparams['size'] = 100;
        }
        return $OUTPUT->user_picture($user, $pictureparams);
    }

    /**
     *
     */
    public function display_email($entry) {
        global $USER;

        if ($entry->id < 0) {
            // New entry.
            return $USER->email;
        } else {
            return $entry->email;
        }
    }

    /**
     *
     */
    public function display_assignme($entry) {
        global $USER, $OUTPUT;

        $field = $this->_field;
        $df = $field->df;

        // Assign.
        $viewurl = $entry->baseurl;
        $urlparams = array(
            'd' => $df->id,
            'vid' => $df->currentview->id,
            'eid' => $entry->id,
            'ret' => urlencode($viewurl->out(false)),
        );
        $url = new \moodle_url('/mod/dataform/field/entryauthor/assign.php', $urlparams);
        if ($USER->id == $entry->userid) {
            // Display unassign button.
            $url->param('action', 'unassign');
            $label = get_string('unassignme', 'dataformfield_entryauthor');
            return $OUTPUT->single_button($url, $label);
        } else {
            $gbusers = $df->grade_manager->get_gradebook_users(array($entry->userid, $USER->id));
            // Student cannot self-assign an entry of a peer.
            if (!empty($gbusers[$entry->userid]) and !empty($gbusers[$USER->id])) {
                return null;
            }

            // Do no display assign button if user at max entries.
            if ($df->user_at_max_entries(true)) {
                return null;
            }
            // Display assign button.
            $url->param('action', 'assign');
            $label = get_string('assignme', 'dataformfield_entryauthor');
            return $OUTPUT->single_button($url, $label);
        }
        return null;
    }

    /**
     * Overriding {@link dataformfieldrenderer::get_pattern_import_settings()}
     * to return import settings only for username, id, idnumber.
     *
     * @param moodleform $mform
     * @param string $pattern
     * @return array
     */
    public function get_pattern_import_settings(&$mform, $patternname, $header) {
        $allowedpatternparts = array('username', 'id', 'idnumber');

        $fieldname = $this->_field->name;
        $patternpart = trim(str_replace($fieldname, '', $patternname), ':');

        if (!in_array($patternpart, $allowedpatternparts)) {
            return array(array(), array());
        }
        return parent::get_pattern_import_settings($mform, $patternname, $header);
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;
        $cat = get_string('pluginname', 'dataformfield_entryauthor');

        $patterns = array();

        foreach (explode(',', user_picture::fields()) as $internalname) {
            $patterns["[[$fieldname:{$internalname}]]"] = array(true, $cat);
        }

        // For user name.
        $patterns["[[$fieldname:username]]"] = array(true, $cat);
        $patterns["[[$fieldname:name]]"] = array(true, $cat);
        $patterns["[[$fieldname:edit]]"] = array(false, $cat);
        $patterns["[[$fieldname:assignme]]"] = array(false, $cat);
        // For user picture add the large picture.
        $patterns["[[$fieldname:picturelarge]]"] = array(true, $cat);

        return $patterns;
    }
}
