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
 * Html file replacement support for glossary.
 * @package tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport;

use cm_info;
use context;
use stored_file;
use tool_ally\componentsupport\interfaces\annotation_map;
use tool_ally\componentsupport\interfaces\content_sub_tables;
use tool_ally\componentsupport\interfaces\html_content as iface_html_content;
use tool_ally\componentsupport\traits\embedded_file_map;
use tool_ally\componentsupport\traits\html_content;
use tool_ally\local_file;
use tool_ally\models\component;
use tool_ally\models\component_content;

/**
 * Html file replacement support for glossary.
 * @package tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class glossary_component extends file_component_base implements
        iface_html_content, annotation_map, content_sub_tables {

    use html_content;
    use embedded_file_map;

    protected $tablefields = [
        'glossary' => ['intro'],
        'glossary_entries' => ['definition']
    ];

    public static function component_type() {
        return self::TYPE_MOD;
    }

    public function replace_file_links() {

        $file = $this->file;

        $area = $file->get_filearea();
        if ($area !== 'entry') {
            debugging('Glossary area of '.$area.' is not yet supported');
            return;
        }

        $itemid = $file->get_itemid();
        $table = 'glossary_entries';
        $idfield = 'id';
        $repfield = 'definition';

        local_file::update_filenames_in_html($repfield, $table, ' id = ? ',
            [$idfield => $itemid], $this->oldfilename, $file->get_filename());
    }

    public function resolve_course_id($id, $table, $field) {
        global $DB;

        if ($table === 'glossary') {
            $label = $DB->get_record('glossary', ['id' => $id]);
            return $label->course;
        }

        throw new \coding_exception('Invalid table used to recover course id '.$table);
    }

    /**
     * @param int $courseid
     * @param null $glossaryid
     * @return component[]
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function get_entry_html_content_items($courseid, $glossaryid = null) {
        global $DB;

        if (!$this->module_installed()) {
            return [];
        }

        $array = [];

        // We are going to limit entry content to that where user is admin or teacher, etc at course level.
        // Faster than doing it per module instance.
        $userids = $this->get_approved_author_ids_for_context(\context_course::instance($courseid));

        list($userinsql, $userparams) = $DB->get_in_or_equal($userids);

        $params = [$courseid, FORMAT_HTML];

        $params = array_merge($params, $userparams);

        $idfilter = '';
        if ($glossaryid) {
            $idfilter = ' AND g.id = ?';
            $params[] = $glossaryid;
        }

        $sql = <<<SQL
            SELECT ge.*
              FROM {glossary} g
              JOIN {glossary_entries} ge
                ON ge.glossaryid = g.id
             WHERE g.course = ? AND ge.definitionformat = ?
               AND ge.userid $userinsql
               $idfilter
SQL;

        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $row) {
            $array[] = new component(
                $row->id, 'glossary', 'glossary_entries', 'definition', $courseid, $row->timemodified,
                $row->definitionformat, $row->concept);
        }
        $rs->close();

        return $array;
    }

    /**
     * @param $courseid
     * @return component[];
     */
    public function get_course_html_content_items($courseid) {
        if (!$this->module_installed()) {
            return [];
        }

        $introarray = $this->get_intro_html_content_items($courseid);
        $discussionarray = $this->get_entry_html_content_items($courseid);

        return array_merge($introarray, $discussionarray);

        return $array;
    }

    /**
     * Get the html content for a specific content item.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param null|int $courseid
     * @return component_content
     */
    public function get_html_content($id, $table, $field, $courseid = null) : ?component_content {
        if ($table === 'glossary') {
            return $this->std_get_html_content($id, $table, $field, $courseid);
        } else if ($table === 'glossary_entries') {
            return $this->std_get_html_content($id, $table, $field, $courseid, 'concept');
        }
    }

    public function get_all_html_content($id) {
        global $DB;

        if (!$this->module_installed()) {
            return;
        }

        $pagetable = '{glossary}';
        $course = $DB->get_record_sql("
                    SELECT c.*
                      FROM $pagetable instance
                      JOIN {course} c ON c.id = instance.course
                     WHERE instance.id = ?", array($id), MUST_EXIST);

        $main = $this->get_html_content($id, 'glossary', 'intro');
        $entries = $this->get_entry_html_content_items($course->id, $id);
        return array_merge([$main], $entries);
    }

    /**
     * Replaces the html content for a specific content item.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param string $content
     * @return string
     */
    public function replace_html_content($id, $table, $field, $content) {
        return $this->std_replace_html_content($id, $table, $field, $content);
    }

    public function get_annotation_maps($courseid) {
        global $PAGE;

        if (!$this->module_installed()) {
            return [];
        }

        if ($PAGE->pagetype === 'mod-glossary-view') {
            $cmid = optional_param('id', null, PARAM_INT);
            if (empty($cmid)) {
                return [];
            }
            list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'glossary', $courseid);
            unset($course);
            $glossaryid = $cm->instance;
            if (!$glossaryid) {
                return [];
            }
            $contentitems = $this->get_entry_html_content_items($courseid, $glossaryid);
            $contentitems = array_merge($contentitems, $this->get_intro_html_content_items($courseid));
        } else {
            $contentitems = $this->get_intro_html_content_items($courseid);
        }

        $entries = [];
        $intros = [];
        foreach ($contentitems as $contentitem) {
            if ($contentitem->table === 'glossary_entries') {
                $entries[$contentitem->id] = $contentitem->entity_id();
            } else if ($contentitem->table === 'glossary') {
                list($course, $cm) = get_course_and_cm_from_instance($contentitem->id, 'glossary', $courseid);
                $intros[$cm->id] = $contentitem->entity_id();
            }
        }

        return ['entries' => $entries, 'intros' => $intros];
    }

    public function queue_delete_sub_tables(cm_info $cm) {
        $entries = $this->get_entry_html_content_items($cm->course, $cm->instance);
        $this->bulk_queue_delete_content($entries);
    }

    public function get_file_area($table, $field) {
        if ($table === 'glossary_entries' && $field === 'definition') {
            return 'entry';
        }
        return parent::get_file_area($table, $field);
    }

    public function get_file_item($table, $field, $id) {
        if ($table === 'glossary_entries' && $field === 'definition') {
            return $id;
        }
        return parent::get_file_item($table, $field, $id);
    }

    public function check_file_in_use(stored_file $file, ?context $context = null): bool {
        if ($file->get_filearea() == 'attachment') {
            // All attachments are in use.
            return true;
        }

        return $this->check_embedded_file_in_use($file, $context);
    }
}
