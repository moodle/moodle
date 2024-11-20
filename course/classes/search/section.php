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
 * Search area for course sections (title and summary).
 *
 * @package core_course
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\search;

defined('MOODLE_INTERNAL') || die();

/**
 * Search area for course sections (title and summary).
 *
 * Note this does not include the activities within the section, as these have their own search
 * areas.
 *
 * @package core_course
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section extends \core_search\base {
    /**
     * Sections are indexed at course context.
     *
     * @var array
     */
    protected static $levels = [CONTEXT_COURSE];

    /**
     * Returns recordset containing required data for indexing course sections.
     *
     * @param int $modifiedfrom timestamp
     * @param \context|null $context Restriction context
     * @return \moodle_recordset|null Recordset or null if no change possible
     */
    public function get_document_recordset($modifiedfrom = 0, ?\context $context = null) {
        global $DB;

        list ($contextjoin, $contextparams) = $this->get_course_level_context_restriction_sql($context, 'c');
        if ($contextjoin === null) {
            return null;
        }

        $comparetext = $DB->sql_compare_text('cs.summary', 1);

        return $DB->get_recordset_sql("
                SELECT cs.id,
                       cs.course,
                       cs.section,
                       cs.name,
                       cs.summary,
                       cs.summaryformat,
                       cs.timemodified
                  FROM {course_sections} cs
                  JOIN {course} c ON c.id = cs.course
          $contextjoin
                 WHERE cs.timemodified >= ?
                   AND (cs.name != ? OR $comparetext != ?)
              ORDER BY cs.timemodified ASC", array_merge($contextparams, [$modifiedfrom, '', '']));
    }

    /**
     * Returns the document associated with this section.
     *
     * @param \stdClass $record
     * @param array $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        // Get the context, modinfo, and section.
        try {
            $context = \context_course::instance($record->course);
        } catch (\moodle_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id .
                    ' document, not all required data is available: ' . $ex->getMessage(),
                    DEBUG_DEVELOPER);
            return false;
        }

        // Title - use default if none given.
        $title = get_section_name($record->course, $record->section);

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($title, false));
        $doc->set('content', content_to_text($record->summary, $record->summaryformat));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $record->course);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);

        return $doc;
    }

    /**
     * Whether the user can access the section or not.
     *
     * @param int $id The course section id.
     * @return int One of the \core_search\manager:ACCESS_xx constants
     */
    public function check_access($id) {
        global $DB;

        // Check we can get the section and the course modinfo.
        $sectionrec = $DB->get_record('course_sections', ['id' => $id], '*', IGNORE_MISSING);
        if (!$sectionrec) {
            return \core_search\manager::ACCESS_DELETED;
        }
        try {
            $modinfo = get_fast_modinfo($sectionrec->course);
        } catch (\moodle_exception $e) {
            return \core_search\manager::ACCESS_DELETED;
        }
        $section = $modinfo->get_section_info($sectionrec->section, IGNORE_MISSING);
        if (!$section) {
            return \core_search\manager::ACCESS_DELETED;
        }

        // Check access to course and that the section is visible to current user.
        if (can_access_course($modinfo->get_course()) && $section->uservisible) {
            return \core_search\manager::ACCESS_GRANTED;
        }

        return \core_search\manager::ACCESS_DENIED;
    }

    /**
     * Gets a link to the section.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        global $DB;
        $section = $DB->get_field('course_sections', 'section', ['id' => $doc->get('itemid')], MUST_EXIST);
        $format = course_get_format($doc->get('courseid'));
        return $format->get_view_url($section);
    }

    /**
     * Gets a link to the section.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        return $this->get_doc_url($doc);
    }

    /**
     * Returns true to include summary files in the index.
     *
     * @return bool True
     */
    public function uses_file_indexing() {
        return true;
    }

    /**
     * Return the file area that is used for summary files.
     *
     * @return array File area name
     */
    public function get_search_fileareas() {
        return ['section'];
    }

    /**
     * Returns the moodle component name, as used in the files table.
     *
     * @return string Component name
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
    public function get_doc_icon(\core_search\document $doc): \core_search\document_icon {
        return new \core_search\document_icon('i/section');
    }

    /**
     * Returns a list of category names associated with the area.
     *
     * @return array
     */
    public function get_category_names() {
        return [\core_search\manager::SEARCH_AREA_CATEGORY_COURSE_CONTENT];
    }
}
