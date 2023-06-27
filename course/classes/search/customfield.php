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
 * Search area for course custom fields.
 *
 * @package core_course
 * @copyright Toni Barbera <toni@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\search;

use core_course\customfield\course_handler;
use core_customfield\data_controller;
use core_customfield\field_controller;

defined('MOODLE_INTERNAL') || die();

/**
 * Search area for course custom fields.
 *
 * @package core_course
 * @copyright Toni Barbera <toni@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customfield extends \core_search\base {

    /**
     * Custom fields are indexed at course context.
     *
     * @var array
     */
    protected static $levels = [CONTEXT_COURSE];

    /**
     * Returns recordset containing required data for indexing
     * course custom fields.
     *
     * @param int $modifiedfrom timestamp
     * @param \context|null $context Restriction context
     * @return \moodle_recordset|null Recordset or null if no change possible
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;

        list ($contextjoin, $contextparams) = $this->get_course_level_context_restriction_sql($context, 'c', SQL_PARAMS_NAMED);
        if ($contextjoin === null) {
            return null;
        }

        $fields = course_handler::create()->get_fields();
        if (!$fields) {
            $fields = array();
        }
        list($fieldsql, $fieldparam) = $DB->get_in_or_equal(array_keys($fields), SQL_PARAMS_NAMED, 'fld', true, 0);

        // Restrict recordset to CONTEXT_COURSE (since we are implementing it to core_course\search).
        $sql = "SELECT d.*
                  FROM {customfield_data} d
                  JOIN {course} c ON c.id = d.instanceid
                  JOIN {context} cnt ON cnt.instanceid = c.id
           $contextjoin
                 WHERE d.timemodified >= :modifiedfrom
                   AND cnt.contextlevel = :contextlevel
                   AND d.fieldid $fieldsql
              ORDER BY d.timemodified ASC";
        return $DB->get_recordset_sql($sql , array_merge($contextparams,
            ['modifiedfrom' => $modifiedfrom, 'contextlevel' => CONTEXT_COURSE], $fieldparam));
    }

    /**
     * Returns the document associated with this section.
     *
     * @param \stdClass $record
     * @param array $options
     * @return \core_search\document|bool
     */
    public function get_document($record, $options = array()) {
        global $PAGE;

        try {
            $context = \context_course::instance($record->instanceid);
        } catch (\moodle_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document, not all required data is available: ' .
                $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        $handler = course_handler::create();
        $field = $handler->get_fields()[$record->fieldid];
        $data = data_controller::create(0, $record, $field);

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($field->get('name'), false));
        $doc->set('content', content_to_text($data->export_value(), FORMAT_HTML));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $context->instanceid);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime']) && ($options['lastindexedtime'] < $record->timecreated)) {
            // If the document was created after the last index time, it must be new.
            $doc->set_is_new(true);
        }

        return $doc;
    }

    /**
     * Whether the user can access the document or not.
     *
     * @param int $id The course instance id.
     * @return int
     */
    public function check_access($id) {
        global $DB;
        $course = $DB->get_record('course', array('id' => $id));
        if (!$course) {
            return \core_search\manager::ACCESS_DELETED;
        }
        if (\core_course_category::can_view_course_info($course)) {
            return \core_search\manager::ACCESS_GRANTED;
        }
        return \core_search\manager::ACCESS_DENIED;
    }

    /**
     * Link to the course.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        return $this->get_context_url($doc);
    }

    /**
     * Link to the course.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        return new \moodle_url('/course/view.php', array('id' => $doc->get('courseid')));
    }

    /**
     * Returns the moodle component name.
     *
     * It might be the plugin name (whole frankenstyle name) or the core subsystem name.
     *
     * @return string
     */
    public function get_component_name() {
        return 'course';
    }

    /**
     * Returns an icon instance for the document.
     *
     * @param \core_search\document $doc
     * @return \core_search\document_icon
     */
    public function get_doc_icon(\core_search\document $doc) : \core_search\document_icon {
        return new \core_search\document_icon('i/customfield');
    }

    /**
     * Returns a list of category names associated with the area.
     *
     * @return array
     */
    public function get_category_names() {
        return [
            \core_search\manager::SEARCH_AREA_CATEGORY_COURSE_CONTENT,
            \core_search\manager::SEARCH_AREA_CATEGORY_COURSES
        ];
    }
}
