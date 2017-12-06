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
 * Dataform content condition.
 *
 * @package availability_dataformcontent
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_dataformcontent;

defined('MOODLE_INTERNAL') || die();

/**
 * Dataform content condition.
 *
 * @package availability_dataformcontent
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var array Array from dataform id => name */
    protected static $dataforms = null;

    /** @var int ID of the dataform that this condition requires. */
    protected $dataformid;

    /**
     * Wipes the static cache used to store grouping names.
     */
    public static function wipe_static_cache() {
        self::$dataforms = null;
    }

    /**
     * Gets names of applicable dataforms
     *
     * @param int $courseid Course id
     * @return array Array of dataform names indexed by dataformid.
     */
    public static function get_dataforms($courseid) {
        global $DB, $CFG;

        // Get the course dataforms.
        $dataforms = $DB->get_records_menu('dataform', array('course' => $courseid), 'name', 'id,name');
        if (!$dataforms) {
            return array();
        }

        // Get the designated field name.
        $fieldname = self::get_reserved_field_name();

        // Get all the dataforms which have a designated field.
        list($inids, $params) = $DB->get_in_or_equal(array_keys($dataforms));
        $select = " dataid $inids AND name = ? ";
        $params[] = $fieldname;
        $dataids = $DB->get_records_select_menu(
            'dataform_fields',
            $select,
            $params,
            'dataid',
            'id,dataid'
        );
        if (!$dataids) {
            return array();
        }
        $dataids = array_unique($dataids);

        // Now adjust the dataforms list.
        $menu = array();
        foreach ($dataids as $dataid) {
            if (array_key_exists($dataid, $dataforms)) {
                $menu[$dataid] = $dataforms[$dataid];
            }
        }
        asort($menu);

        return $menu;
    }

    /**
     * Returns the reserved dataform field name. If not defined in config, it is fetched
     * from a lang string.
     *
     * @return string
     */
    public static function get_reserved_field_name() {
        global $CFG;

        if (!empty($CFG->availability_dataformcontent_reservedfield)) {
            $name = $CFG->availability_dataformcontent_reservedfield;
        } else {
            $name = get_string('reservedfieldname', 'availability_dataformcontent');
        }

        return $name;
    }

    /**
     * Returns the reserved dataform filter name. If not defined in config, it is fetched
     * from a lang string.
     *
     * @return string
     */
    public static function get_reserved_filter_name() {
        global $CFG;

        if (!empty($CFG->availability_dataformcontent_reservedfilter)) {
            $name = $CFG->availability_dataformcontent_reservedfilter;
        } else {
            $name = get_string('reservedfiltername', 'availability_dataformcontent');
        }

        return $name;
    }

    /**
     * Returns a JSON object which corresponds to a condition of this type.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * @param int $dataformid Required dataform id.
     * @return stdClass Object representing condition
     */
    public static function get_json($dataformid = 0) {
        return (object)array('type' => 'dataformcontent', 'id' => (int) $dataformid);
    }

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        // Get field id.
        if (!property_exists($structure, 'id')) {
            $this->dataformid = 0;
        } else if (is_int($structure->id)) {
            $this->dataformid = $structure->id;
        } else {
            throw new \coding_exception('Invalid ->id for dataformcontent condition');
        }
    }

    /**
     *
     */
    public function save() {
        $result = (object)array('type' => 'dataformcontent');
        if ($this->dataformid) {
            $result->id = $this->dataformid;
        }
        return $result;
    }

    /**
     *
     */
    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        if (!$this->dataformid) {
            return false;
        }

        // Get the dataform.
        try {
            $df = new \mod_dataform_dataform($this->dataformid);

        } catch (Exception $e) {
            $this->dataformid = null;
            return false;
        }

        // Get the filter.
        $filter = $this->get_reserved_filter();

        // The activity search criterion.
        $fieldname = self::get_reserved_field_name();
        if (!$field = $df->field_manager->get_field_by_name($fieldname)) {
            return false;
        }

        $cmref = $this->get_activity_reference($info);

        $searchactivity = array(
            $field->id => array(
                'AND' => array(
                    array('content', '', '=', $cmref)
                )
            )
        );
        $filter->append_search_options($searchactivity);

        $entryman = new \mod_dataform_entry_manager($this->dataformid, 0);
        $entrycount = $entryman->count_entries(array('filter' => $filter));

        $allow = !empty($entrycount);
        if ($not) {
            $allow = !$allow;
        }

        return $allow;
    }

    /**
     *
     */
    public function get_description($full, $not, \core_availability\info $info) {
        global $DB;

        // Need to get the name for the dataform. Unfortunately this requires
        // a database query. To save queries, get all dataforms for course at
        // once in a static cache.
        $courseid = $info->get_course()->id;
        if (self::$dataforms === null) {
            self::$dataforms = self::get_dataforms($courseid);
        }

        // If dataformid doesn't exist, it must have been misplaced.
        if (!array_key_exists($this->dataformid, self::$dataforms)) {
            $name = get_string('missing', 'availability_dataformcontent');
        } else {
            $dataformname = self::$dataforms[$this->dataformid];
            $context = \context_course::instance($courseid);
            $name = format_string($dataformname, true, array('context' => $context));
        }

        $requireornot = $not ? 'requires_notdataformcontent' : 'requires_dataformcontent';
        return get_string($requireornot, 'availability_dataformcontent', $name);
    }

    /**
     *
     */
    protected function get_debug_string() {
        return $this->dataformid ? '#' . $this->dataformid : 'none';
    }

    /**
     * Include this condition only if dataformid is not empty.
     *
     * @param int $restoreid The restore Id.
     * @param int $courseid The ID of the course.
     * @param base_logger $logger The logger being used.
     * @param string $name Name of item being restored.
     * @param base_task $task The task being performed.
     *
     * @return bool
     */
    public function include_after_restore($restoreid, $courseid, \base_logger $logger,
            $name, \base_task $task) {
        return !empty($this->dataformid);
    }

    /**
     *
     */
    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        global $DB;

        if (!$this->dataformid) {
            return false;
        }

        $rec = \restore_dbops::get_backup_ids_record($restoreid, 'dataform', $this->dataformid);
        if (!$rec || !$rec->newitemid) {
            // If we are on the same course (e.g. duplicate) then we can just
            // use the existing one.
            $params = array('id' => $this->dataformid, 'course' => $courseid);
            if ($DB->record_exists('dataform', $params)) {
                return false;
            }
            // Otherwise it's a warning.
            $this->dataformid = null;
            $logger->process('Restored item (' . $name .
                    ') has availability condition on dataformcontent that was not restored',
                    \backup::LOG_WARNING);
        } else {
            $this->dataformid = (int) $rec->newitemid;
        }
        return true;
    }

    /**
     *
     */
    public function update_dependency_id($table, $oldid, $newid) {
        if ($table === 'dataform' && (int) $this->dataformid === (int) $oldid) {
            $this->dataformid = $newid;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the reserved dataform filter if exists, otherwise return blank filter.
     *
     * @return \mod_dataform\pluginbase\dataformfilter
     */
    protected function get_reserved_filter() {
        global $DB;

        $fm = new \mod_dataform_filter_manager($this->dataformid);

        $filtername = self::get_reserved_filter_name();
        $params = array('name' => $filtername, 'dataid' => $this->dataformid);
        $filterid = $DB->get_field('dataform_filters', 'id', $params);

        if ($filterid) {
            $filter = $fm->get_filter_by_id($filterid);
        } else {
            $filter = $fm->get_filter_blank();
        }

        return $filter;
    }

    /**
     *
     */
    protected function get_activity_reference(\core_availability\info $info) {
        global $CFG;

        if (empty($CFG->availability_dataformcontent_activityref)) {
            $refitem = 'name';
        } else {
            $refitem = $CFG->availability_dataformcontent_activityref;
        }

        $cm = $info->get_course_module();

        if ($refitem == 'name') {
            $ref = $cm->get_formatted_name();
        } else if ($refitem == 'id') {
            $ref = $cm->instance;
        } else if ($refitem == 'cmid') {
            $ref = $cm->id;
        } else {
            $ref = $cm->get_formatted_name();
        }

        return $ref;
    }
}
