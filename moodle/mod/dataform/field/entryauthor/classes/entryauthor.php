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
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class dataformfield_entryauthor_entryauthor extends \mod_dataform\pluginbase\dataformfield_internal {

    const INTERNALID = -2;

    /**
     * Returns instance defaults for for the field
     * (because internal fields do not have DB record).
     *
     * @return null|stdClass
     */
    public static function get_default_data($dfid) {
        $field = (object) array(
            'id' => self::INTERNALID,
            'dataid' => $dfid,
            'type' => 'entryauthor',
            'name' => get_string('fieldname', 'dataformfield_entryauthor'),
            'description' => '',
            'visible' => 2,
            'editable' => -1,
            'internalname' => ''
        );
        return $field;
    }

    /**
     * Assigns/unassigned the specified user as the entry author.
     * Only current user can unassign him/her self.
     * Unassigning sets the entry userid to 0. Possible conflicts need to examined.
     * Gradebook users can assign only to entries not currently owned by gradebook users.
     *
     * @param int $userid The user to assign as the entry owner (0 for unassign).
     * @param int $entryid The entry to be assigned.
     * @param int
     * @param int
     * @return bool
     */
    public function assign_user($userid, $entryid, $viewid) {
        global $DB, $USER;

        // Get the entry.
        if (!$entry = $DB->get_record('dataform_entries', array('id' => $entryid))) {
            return false;
        }

        $dataformid = $entry->dataid;

        // The view must have the assignme pattern.
        $view = \mod_dataform_view_manager::instance($dataformid)->get_view_by_id($viewid);
        $patterns = $view->get_pattern_set('field');
        if (!$patterns or empty($patterns[$this->id])) {
            return  false;
        }

        $fieldname = $this->name;
        if (!in_array("[[$fieldname:assignme]]", $patterns[$this->id])) {
            return false;
        }

        // If same user (for some reason), no need to do anything.
        if ($entry->userid == $userid) {
            return true;
        }

        // UNASSIGN.
        // Only the current user can unassign him/her self.
        if ($userid == 0) {
            if ($entry->userid != $USER->id) {
                return false;
            }
            // Try to get a teacher id for the user id.
            if ($roles = $DB->get_records('role', array('archetype' => 'editingteacher'))) {
                foreach ($roles as $role) {
                    $teachers = get_role_users(
                        $role->id,
                        $this->df->context,
                        true,
                        user_picture::fields('u'),
                        'u.lastname ASC',
                        false
                    );
                    if ($teachers) {
                        $userid = key($teachers);
                        break;
                    }
                }
            }
            // If no teachers, get admin id.
            if (!$userid = ($userid ? $userid : get_admin()->id)) {
                return false;
            }

            $DB->set_field('dataform_entries', 'userid', $userid, array('id' => $entryid));
            // Notify.

            return true;
        }

        // ASSIGN.
        // Self assign of gradebook user can only be allowed when the current
        // entry user is not gradebook user.
        // In other words, a student cannot override another student's selection.
        if ($gbusers = $this->df->grade_manager->get_gradebook_users(array($entry->userid, $userid))) {
            if (array_key_exists($entry->userid, $gbusers)) {
                if (array_key_exists($userid, $gbusers)) {
                    return false;
                }
            }
        }

        // Set the user as entry owner.
        $DB->set_field('dataform_entries', 'userid', $userid, array('id' => $entryid));

        // Update the entry the standard way, to trigger events.
        $eids = array($entryid);
        $data = (object) array('submitbutton_save' => 'Save');
        $processed = $view->entry_manager->process_entries('update', $eids, $data, true);

        return true;
    }

    /**
     * Overrides {@link dataformfield::prepare_import_content()} to set import of entry::userid.
     *
     * @return stdClass
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = 0) {
        global $DB;

        $csvname = '';

        // Author id.
        if (!empty($importsettings['id'])) {
            $setting = $importsettings['id'];
            if (!empty($setting['name'])) {
                $csvname = $setting['name'];

                if (isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
                    $data->{"entry_{$entryid}_userid"} = $csvrecord[$csvname];
                    return $data;
                }
            }
        }

        // Author username.
        if (!empty($importsettings['username'])) {
            $setting = $importsettings['username'];
            if (!empty($setting['name'])) {
                $csvname = $setting['name'];

                if (isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
                    if ($userid = $DB->get_field('user', 'id', array('username' => $csvrecord[$csvname]))) {
                        $data->{"entry_{$entryid}_userid"} = $userid;
                        return $data;
                    }
                }
            }
        }

        // Author idnumber.
        if (!empty($importsettings['idnumber'])) {
            $setting = $importsettings['idnumber'];
            if (!empty($setting['name'])) {
                $csvname = $setting['name'];

                if (isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
                    if ($userid = $DB->get_field('user', 'id', array('idnumber' => $csvrecord[$csvname]))) {
                        $data->{"entry_{$entryid}_userid"} = $userid;
                    }
                }
            }
        }
        return $data;
    }

    /**
     *
     */
    public function get_sort_sql($element = null) {
        if ($element == 'name') {
            $element = 'id';
        }
        return parent::get_sort_sql($element);
    }

    /**
     *
     */
    public function get_search_sql($search) {
        global $USER;

        // Set search current user entries entries.
        if ($search[0] == 'currentuser') {
            $search[0] = 'id';
            $search[3] = $USER->id;
            if ($search[1] == '' and $search[2] == '') {
                // IS EMPTY == NOT equal.
                $search[1] = 'NOT';
                $search[2] = '=';
            } else if ($search[1] == 'NOT' and $search[2] == '') {
                // NOT EMPTY == IS equal.
                $search[1] = '';
                $search[2] = '=';
            } else {
                // No other settings for this element should be processed.
                return null;
            }
        }

        return parent::get_search_sql($search);
    }

    /**
     * Returns the field alias for sql queries.
     *
     * @param string The field element to query
     * @return string
     */
    protected function get_sql_alias($element = null) {
        return 'u';
    }

    /**
     * Return array of sort options menu as
     * $fieldid,element => name, for the filter form.
     *
     *
     * @return null|array
     */
    public function get_sort_options_menu() {
        $fieldid = $this->id;
        $fieldname = $this->name;
        return array(
            "$fieldid,firstname" => "$fieldname ". get_string('userfirstname', 'dataformfield_entryauthor'),
            "$fieldid,lastname" => "$fieldname ". get_string('userlastname', 'dataformfield_entryauthor'),
            "$fieldid,username" => "$fieldname ". get_string('userusername', 'dataformfield_entryauthor'),
            "$fieldid,idnumber" => "$fieldname ". get_string('useridnumber', 'dataformfield_entryauthor'),
        );
    }

    /**
     * Return array of search options menu as
     * $fieldid,element => name, for the filter form.
     *
     * @return null|array
     */
    public function get_search_options_menu() {
        $fieldid = $this->id;
        $currentuser = array("$fieldid,currentuser" => get_string('currentuser', 'dataformfield_entryauthor'));
        return array_merge($currentuser, $this->get_sort_options_menu());
    }

    /**
     * @return string SQL fragment.
     */
    public function get_search_from_sql() {
        return " JOIN {user} u ON u.id = e.userid ";
    }

    /**
     * Returns an array of distinct content of the field.
     *
     * @param string $element
     * @param int $sortdir Sort direction 0|1 ASC|DESC
     * @return array
     */
    public function get_distinct_content($element, $sortdir = 0) {
        global $CFG, $DB;

        $sortdir = $sortdir ? 'DESC' : 'ASC';
        $contentfull = $this->get_sort_sql();
        $sql = "SELECT DISTINCT $contentfull
                FROM {user} u
                    JOIN {dataform_entries} e ON u.id = e.userid
                WHERE e.dataid = ? AND  $contentfull IS NOT NULL
                ORDER BY $contentfull $sortdir";

        $distinctvalues = array();
        if ($options = $DB->get_records_sql($sql, array($this->df->id))) {
            if ($this->internalname == 'name') {
                $internalname = 'id';
            } else {
                $internalname = $this->internalname;
            }
            foreach ($options as $data) {
                $value = $data->{$internalname};
                if ($value === '') {
                    continue;
                }
                $distinctvalues[] = $value;
            }
        }
        return $distinctvalues;
    }
}
