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
 * @subpackage entrygroup
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class dataformfield_entrygroup_entrygroup extends \mod_dataform\pluginbase\dataformfield_internal {

    const INTERNALID = -3;

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
            'type' => 'entrygroup',
            'name' => get_string('fieldname', 'dataformfield_entrygroup'),
            'description' => '',
            'visible' => 2,
            'editable' => -1,
        );
        return $field;
    }

    /**
     * Loads the field content in the entry and returns the entry.
     * This will fetch from DB and add to the entry object any of the field
     * content that is not already there.
     *
     * @param stdClass $entry
     * @return stdClass
     */
    public function load_entry_content($entry) {
        global $DB;

        $fieldid = $this->id;

        // Must have entry group id.
        if (empty($entry->groupid)) {
            return $entry;
        }

        // Content parts.
        $contentvars = array(
            'name' => 'groupname',
            'idnumber' => 'groupidnumber',
            'hidepicture' => 'grouphidepic',
            'picture' => 'grouppic',
        );

        $fetch = false;

        // Make sure we have the group content in the entry.
        foreach ($contentvars as $alias) {
            if (!isset($entry->$alias)) {
                $fetch = true;
                break;
            }
        }

        if ($fetch) {
            $params = array('id' => (int) $entry->groupid);
            $fields = implode(',', array_keys($contentvars));
            if (!$group = $DB->get_record('groups', $params, $fields)) {
                return $entry;
            }
            // Add the content to the entry.
            foreach ($contentvars as $var => $alias) {
                $entry->$alias = $group->$var;
            }
        }
        return $entry;
    }

    /**
     * Overrides {@link dataformfield::prepare_import_content()} to set import into entry::groupid.
     *
     * @return stdClass
     */
    public function prepare_import_content($data, $importsettings, $csvrecord = null, $entryid = 0) {
        global $DB;

        $courseid = $this->df->course->id;

        // Group id.
        if (!empty($importsettings['id'])) {
            $setting = $importsettings['id'];
            if (!empty($setting['name'])) {
                $csvname = $setting['name'];

                if (isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
                    $sqlparams = array('courseid' => $courseid, 'id' => $csvrecord[$csvname]);
                    if ($groupid = $DB->get_field('groups', 'id', $sqlparams)) {
                        $data->{"entry_{$entryid}_groupid"} = $groupid;
                    }
                }
            }
            return $data;
        }

        // Group idnumber.
        if (!empty($importsettings['idnumber'])) {
            $setting = $importsettings['idnumber'];
            if (!empty($setting['name'])) {
                $csvname = $setting['name'];

                if (isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
                    $sqlparams = array('courseid' => $courseid, 'idnumber' => $csvrecord[$csvname]);
                    if ($groupid = $DB->get_field('groups', 'id', $sqlparams)) {
                        $data->{"entry_{$entryid}_groupid"} = $groupid;
                    }
                }
            }
            return $data;
        }

        // Group name.
        if (!empty($importsettings['name'])) {
            $setting = $importsettings['name'];
            if (!empty($setting['name'])) {
                $csvname = $setting['name'];

                if (isset($csvrecord[$csvname]) and $csvrecord[$csvname] !== '') {
                    $sqlparams = array('courseid' => $courseid, 'name' => $csvrecord[$csvname]);
                    if ($groupid = $DB->get_field('groups', 'id', $sqlparams)) {
                        $data->{"entry_{$entryid}_groupid"} = $groupid;
                    }
                }
            }
            return $data;
        }

        return $data;
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
            "$fieldid,name" => "$fieldname ". get_string('name'),
            "$fieldid,idnumber" => "$fieldname ". get_string('idnumber'),
        );
    }

    /**
     * Returns the field alias for sql queries.
     *
     * @param string The field element to query
     * @return string
     */
    protected function get_sql_alias($element = null) {
        return 'g';
    }

    /**
     * @return string SQL fragment.
     */
    public function get_search_from_sql() {
        return " JOIN {groups} g ON g.id = e.groupid  ";
    }

    /**
     *
     */
    public function get_select_sql() {
        $elements = array(
            'g.idnumber AS groupidnumber',
            'g.name AS groupname',
            'g.hidepicture AS grouphidepic',
            'g.picture AS grouppic',
        );
        $selectsql = implode(',', $elements);
        return " $selectsql ";
    }

    /**
     *
     */
    public function get_sort_from_sql() {
        $sql = " LEFT JOIN {groups} g ON g.id = e.groupid  ";
        return array($sql, null);
    }


}
