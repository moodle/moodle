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
 * Html content support for courses.
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport;

defined ('MOODLE_INTERNAL') || die();

use context;
use moodle_url;
use stored_file;
use tool_ally\componentsupport\traits\html_content;
use tool_ally\componentsupport\traits\embedded_file_map;
use tool_ally\componentsupport\interfaces\html_content as iface_html_content;
use tool_ally\logging\logger;
use tool_ally\models\component;
use tool_ally\models\component_content;

require_once($CFG->dirroot.'/course/lib.php');

/**
 * Html content support for courses.
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_component extends component_base implements iface_html_content {

    use html_content;
    use embedded_file_map;

    protected $tablefields = [
        'course' => ['summary'],
        'course_sections' => ['summary']
    ];

    public static function component_type() {
        return self::TYPE_CORE;
    }

    /**
     * @param int $courseid
     * @return \moodle_recordset
     * @throws \dml_exception
     */
    public function get_course_section_summary_rows($courseid) {
        global $DB;

        $select = "course = ? AND summaryformat = ? AND summary !='' ORDER BY section";
        return $DB->get_recordset_select('course_sections', $select, [$courseid, FORMAT_HTML]);
    }

    /**
     * Taken from format/lib.php
     *
     * Returns the display name of the given section that the course prefers.
     *
     * @param int $course
     * @param int|stdClass $section Section object from database or just field course_sections.section
     * @return Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($course, $section) {
        $course = get_course($course);

        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }

        if (!empty($course->format)
            && get_string_manager()->string_exists('sectionname', 'format_' . $course->format)) {
            return get_string('sectionname', 'format_' . $course->format) . ' ' . $sectionnum;
        }

        // Return best guess if section name could not be created from format language file.
        return get_string('section', 'tool_ally', $sectionnum);
    }

    public function get_course_html_content_items($courseid) {
        global $DB;

        $array = [];

        // Add course summary.
        $select = "id = ? AND summaryformat = ? AND summary !=''";
        $row = $DB->get_record_select('course', $select, [$courseid, FORMAT_HTML]);
        if ($row) {
            $array[] = new component(
                    $row->id, 'course', 'course', 'summary', $courseid, $row->timemodified,
                    $row->summaryformat, $row->fullname);
        }

        // Add course sections.
        $rs = $this->get_course_section_summary_rows($courseid);
        foreach ($rs as $row) {
            $sectionname = !empty($row->name) ? $row->name : $this->get_section_name($courseid, $row);
            $array[] = new component(
                    $row->id, 'course', 'course_sections', 'summary', $courseid, $row->timemodified,
                    $row->summaryformat, $sectionname);
        }
        $rs->close();

        return $array;
    }

    public function get_html_content($id, $table, $field, $courseid = null) : ?component_content {
        $titlefield = $table === 'course' ? 'fullname' : 'name';
        $recordlambda = null;
        if ($table === 'course_sections') {
            $recordlambda = function($record) {
                if (empty($record->timemodified)) {
                    $course = get_course($record->course);
                    $record->timemodified = $course->timecreated;
                }
                if (!empty($record->name)) {
                    return; // Don't bother modifying $record - we have a name already!
                }
                try {
                    $sectionname = $this->get_section_name($record->course, $record);
                    // Override original section name.
                    $record->name = $sectionname;
                } catch (\Exception $ex) {
                    // Somehow the section is course-less or the course format could not be retrieved.
                    // The section name remains unchanged.
                    $msg = '<br> Course ID: '.$record->course;

                    logger::get()->info('logger:failedtogetcoursesectionname', [
                        'content' => $msg,
                        '_exception' => $ex
                    ]);
                }
            };
        }

        $content = $this->std_get_html_content($id, $table, $field, $courseid, $titlefield, 'timemodified', $recordlambda);
        return $content;
    }

    /**
     * Get section number corresponding to $sectionid.
     * @param $sectionid
     * @return int
     * @throws \dml_exception
     */
    private function get_section_number($sectionid) {
        global $DB;

        $section = $DB->get_record('course_sections', ['id' => $sectionid], 'section');
        return $section->section;
    }

    /**
     * Attempt to make url for content.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param null|int $courseid
     */
    public function make_url($id, $table, $field, $courseid) {
        global $DB;

        if ($table === 'course') {
            return new moodle_url('/course/edit.php?id='.$id).'';
        } else if ($table === 'course_sections') {
            $sectionnumber = $this->get_section_number($id);
            if (empty($courseid)) {
                $courseid = $DB->get_field('course_sections', 'course', ['id' => $id]);
            }
            return new moodle_url('/course/view.php?id='.$courseid.'#section-'.$sectionnumber).'';
        }
        return null;
    }

    public function get_all_html_content($id) {
        global $DB;
        $content = [];
        $content[] = $this->get_html_content($id, 'course', 'summary');
        $sections = $DB->get_records('course_sections', ['course' => $id]);
        foreach ($sections as $section) {
            $content[] = $this->get_html_content($section->id, 'course_sections', 'summary');
        }
        return $content;
    }

    public function replace_html_content($id, $table, $field, $content) {
        global $DB;

        if ($table === 'course_sections') {
            $section = $DB->get_record('course_sections', ['id' => $id]);
            if ($section) {
                $data = ['id' => $section->id, 'summary' => $content];
                course_update_section($section->course, $section, $data);
                return true;
            }
            return false;
        } else {

            return $this->std_replace_html_content($id, $table, $field, $content);
        }
    }

    public function resolve_course_id($id, $table, $field) {
        global $DB;

        if ($table === 'course') {
            return $id;
        } else if ($table === 'course_sections') {
            $section = $DB->get_record('course_sections', ['id' => $id]);
            return $section->course;
        }

        throw new \coding_exception('Invalid table used to recover course id '.$table);
    }

    /**
     * Get a file item id for a specific table / field / id.
     *
     * @param string $table
     * @param string $field
     * @param int $id
     * @return int
     */
    public function get_file_item($table, $field, $id) {
        if ($table === 'course_sections') {
            return $id;
        }
        return 0;
    }

    /**
     * Get a file area for a specific table / field.
     *
     * @param $table
     * @param $field
     * @return mixed
     */
    public function get_file_area($table, $field) {
        if ($table === 'course_sections') {
            return 'section';
        }
        return parent::get_file_area($table, $field);
    }

    public function check_file_in_use(stored_file $file, ?context $context = null): bool {
        if ($file->get_filearea() == 'overviewfiles') {
            // Overview files is the area for the course image.
            return true;
        }

        return $this->check_embedded_file_in_use($file, $context);
    }
}
