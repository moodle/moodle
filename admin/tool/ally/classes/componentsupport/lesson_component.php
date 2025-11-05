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
 * Html file replacement support for core lessons.
 * @package tool_ally
 * @author    David Castro <david.castro@openlms.net>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport;

use tool_ally\componentsupport\interfaces\annotation_map;
use tool_ally\componentsupport\traits\html_content;
use tool_ally\componentsupport\traits\embedded_file_map;
use tool_ally\componentsupport\interfaces\html_content as iface_html_content;
use tool_ally\local_file;
use tool_ally\models\component;
use tool_ally\webservice\content;
use tool_ally\models\component_content;

use moodle_url;

/**
 * Class lesson_component.
 * Html file replacement support for core lessons.
 * @package tool_ally
 * @author    David Castro <david.castro@openlms.net>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lesson_component extends file_component_base implements iface_html_content, annotation_map {

    use html_content;
    use embedded_file_map;

    protected $tablefields = [
        'lesson'       => ['intro'],
        'lesson_pages' => ['contents'],
        'lesson_answers' => ['answer', 'response']
    ];

    public static function component_type() {
        return self::TYPE_MOD;
    }

    /**
     * Get all course content and hash it by identifier.
     * @param int $courseid
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function content_by_identifier($courseid) {
        global $DB;

        $array = [];
        if (!$this->module_installed()) {
            return $array;
        }

        $params = [
            FORMAT_HTML, $courseid,
            FORMAT_HTML, $courseid,
            FORMAT_HTML, $courseid,
            FORMAT_HTML, $courseid
        ];

        $sql = <<<SQL
               (SELECT concat('0~', id) AS id,
                       timemodified,
                       0 AS timecreated,
                       introformat AS format,
                       name AS title,
                       name AS primarytitle,
                       0 AS parentid
                  FROM {lesson}
                 WHERE introformat = ? AND course = ?)

                 UNION

               (SELECT concat('lesson_pages~', lp.id) AS id,
                       lp.timemodified,
                       lp.timecreated,
                       lp.contentsformat AS format,
                       lp.title,
                       l.name AS primarytitle,
                       l.id AS parentid
                  FROM {lesson} l
                  JOIN {lesson_pages} lp ON lp.lessonid = l.id AND lp.contentsformat = ?
                   AND lp.contents IS NOT NULL AND lp.contents !=''
                 WHERE l.course = ?)

                 UNION

               (SELECT concat('lesson_answers~', la.id) AS id,
                       la.timemodified,
                       la.timecreated,
                       la.answerformat AS format,
                       '[answernotitle]' AS title,
                       l.name AS primarytitle,
                       la.pageid AS parentid
                  FROM {lesson} l
                  JOIN {lesson_answers} la ON la.lessonid = l.id AND la.answerformat = ?
                   AND la.answer IS NOT NULL AND la.answer != ''
                 WHERE l.course = ?)

                 UNION

               (SELECT concat('lesson_answers_response~', la.id) AS id,
                       la.timemodified,
                       la.timecreated,
                       la.responseformat AS format,
                       '[answernotitle]' AS title,
                       l.name AS primarytitle,
                       la.pageid AS parentid
                  FROM {lesson} l
                  JOIN {lesson_answers} la ON la.lessonid = l.id AND la.responseformat = ?
                   AND la.response IS NOT NULL AND la.response != ''
                 WHERE l.course = ?)

              ORDER BY id ASC
SQL;

        $rs = $DB->get_recordset_sql($sql, $params);

        foreach ($rs as $row) {

            $tmparr = explode('~', $row->id);
            $ident = $tmparr[0];
            $id = $tmparr[1];
            $title = $row->title;

            if ($ident === '0') {
                $ident = 'intros'; // Here 0 is for sorting purposes.
                $table = 'lesson';
                $field = 'intro';
            } else if ($ident === 'lesson_pages') {
                $table = 'lesson_pages';
                $field = 'contents';
            } else if ($ident === 'lesson_answers') {
                $table = 'lesson_answers';
                $field = 'answer';
                $title = get_string('lessonanswertitle', 'tool_ally', $row->primarytitle);
            } else if ($ident === 'lesson_answers_response') {
                $table = 'lesson_answers';
                $field = 'response';
                $title = get_string('lessonresponsetitle', 'tool_ally', $row->primarytitle);
            }

            if (empty($row->timemodified)) {
                $row->timemodified = $row->timecreated;
            }

            if (!isset($array[$ident])) {
                $array[$ident] = [];
            }

            $array[$ident][$id] = new component(
                $id, 'lesson', $table, $field, $courseid, $row->timemodified,
                $row->format, $title);

            $array[$ident][$id]->meta->parentid = $row->parentid;

        }
        $rs->close();
        return $array;
    }

    public function get_annotation_maps($courseid) {
        $retarray = [];

        $array = $this->content_by_identifier($courseid);
        foreach ($array as $ident => $values) {
            $parentid = null;
            $prevparentid = null;
            $count = 0;
            foreach ($values as $id => $content) {
                $parentid = $content->meta->parentid;
                if ($parentid !== $prevparentid) {
                    $count = 0;
                }
                $prevparentid = $parentid;
                $count++;
                if ($ident === 'intros') {
                    list($course, $cm) = get_course_and_cm_from_instance($content->id, 'lesson', $courseid);
                    $retarray[$ident][$cm->id] = $content->entity_id();
                } else if ($ident === 'lesson_answers' || $ident === 'lesson_answers_response') {
                    $retarray[$ident][$content->meta->parentid.'_'.$id.'_'.$count] = $content->entity_id();
                } else {
                    $retarray[$ident][$id] = $content->entity_id();
                }
            }
        }

        return $retarray;
    }

    public function get_course_html_content_items($courseid) {

        $retarray = [];

        $array = $this->content_by_identifier($courseid);
        foreach ($array as $key => $values) {
            $retarray = array_merge($retarray, $values);
        }

        return $retarray;
    }

    public function replace_file_links() {
        $file = $this->file;

        $area = $file->get_filearea();
        $itemid = $file->get_itemid();

        if ($area === 'page_contents') {
            local_file::update_filenames_in_html('contents', 'lesson_pages', ' id = ? ',
                ['id' => $itemid], $this->oldfilename, $file->get_filename());
        }
    }

    public function get_html_content($id, $table, $field, $courseid = null) : ?component_content {
        global $DB;

        $row = null;
        $lambda = null;

        $titlefld = 'name';
        if ($table === 'lesson_pages') {
            $titlefld = 'title';
        }
        if ($table === 'lesson_answers') {
            $titlefld = 'lambdatitle';

            if ($field === 'answer') {
                $params = [
                    FORMAT_HTML,
                    $id
                ];

                $sql = <<<SQL
                SELECT la.id,
                       la.timemodified,
                       la.timecreated,
                       la.answerformat,
                       la.answer,
                       l.name as primarytitle
                  FROM {lesson} l
                  JOIN {lesson_answers} la on la.lessonid = l.id AND la.answerformat = ?
                   AND la.answer IS NOT NULL AND la.answer != ''
                 WHERE la.id = ?
SQL;
                $row = $DB->get_record_sql($sql, $params);
                $lambda = function ($row) {
                    $row->lambdatitle = get_string('lessonanswertitle', 'tool_ally', $row->primarytitle);
                };
            } else if ($field === 'response') {
                $params = [
                    FORMAT_HTML,
                    $id
                ];

                $sql = <<<SQL
                SELECT la.id,
                       la.timemodified,
                       la.timecreated,
                       la.responseformat,
                       la.response,
                       l.name as primarytitle
                  FROM {lesson} l
                  JOIN {lesson_answers} la on la.lessonid = l.id AND la.responseformat = ?
                   AND la.response IS NOT NULL AND la.response != ''
                 WHERE la.id = ?
SQL;
                $row = $DB->get_record_sql($sql, $params);
                $lambda = function ($row) {
                    $row->lambdatitle = get_string('lessonresponsetitle', 'tool_ally', $row->primarytitle);
                };
            }
        }
        return $this->std_get_html_content($id, $table, $field, $courseid, $titlefld, 'timemodified', $lambda, $row);
    }

    private function get_lesson_pages($lessonid) {
        global $DB;
        return $DB->get_records('lesson_pages', ['lessonid' => $lessonid]);
    }

    public function get_all_html_content($id) {
        $lesson = $this->get_html_content($id, 'lesson', 'intro');
        $pagerows = $this->get_lesson_pages($id);
        $pages = [];
        foreach ($pagerows as $row) {
            $pages[] = $this->std_get_html_content(
                $row->id, 'lesson_pages', 'contents', $lesson->courseid, 'title', 'timemodified', null, $row);
        }
        return array_merge([$lesson], $pages);
    }

    public function replace_html_content($id, $table, $field, $content) {
        return $this->std_replace_html_content($id, $table, $field, $content);
    }

    public function resolve_course_id($id, $table, $field) {
        global $DB;

        if ($table === 'lesson') {
            return $DB->get_field('lesson', 'course', ['id' => $id]);
        } else if ($table === 'lesson_pages') {
            $params = [$id];
            $sql = <<<SQL
            SELECT course FROM {lesson} l
              JOIN {lesson_pages} lp ON lp.lessonid = l.id
             WHERE lp.id = ?
SQL;

            return $DB->get_field_sql($sql, $params);
        }
    }

    public function get_file_area($table, $field) {
        if ($table === 'lesson_pages' && $field === 'contents') {
            return 'page_contents';
        }
        if ($table === 'lesson_answers' && $field === 'answer') {
            return 'page_answers';
        }
        if ($table === 'lesson_answers' && $field === 'response') {
            return 'page_responses';
        }
        return parent::get_file_area($table, $field);
    }

    public function get_file_item($table, $field, $id) {
        if ($table === 'lesson_pages' && $field === 'contents') {
            return $id;
        }
        if ($table === 'lesson_answers' && ($field === 'answer' || $field === 'response')) {
            return $id;
        }
        return parent::get_file_item($table, $field, $id);
    }

    /**
     * Attempt to make url for content.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param int $courseid
     * @return null|string;
     */
    public function make_url($id, $table, $field = null, $courseid = null) {
        global $DB;

        if (is_null($courseid)) {
            $courseid = 0;
        }

        if (!isset($this->tablefields[$table])) {
            return null;
        }
        if ($table === 'lesson') {
            return $this->make_module_instance_url($table, $id);
        } else if ($table === 'lesson_pages') {
            $lessonid = $DB->get_field('lesson_pages', 'lessonid', ['id' => $id]);
            try {
                list ($course, $cm) = get_course_and_cm_from_instance($lessonid, 'lesson', $courseid);
                return new moodle_url('/mod/lesson/view.php', ['id' => $cm->id, 'pageid' => $id]).'';
            } catch (\moodle_exception $ex) {
                return null;
            }

        }
        return null;
    }

    public function get_all_files_search_html(int $id): ?array {
        global $DB;

        // Get the main content, and extract the lesson from hit.
        $content = $this->get_all_html_content($id);
        $lesson = reset($content);

        // Now we are going to add content for each answer.
        $answerrows = $DB->get_recordset('lesson_answers', ['lessonid' => $id]);
        foreach ($answerrows as $row) {
            // For each answer row, we need to make content for both the answer and the response.
            if (!empty($row->answer) && $row->answerformat == FORMAT_HTML) {
                $content[] = $this->std_get_html_content(
                    $row->id, 'lesson_answers', 'answer', $lesson->courseid, null, 'timemodified', null, $row);
            }
            if (!empty($row->response) && $row->responseformat == FORMAT_HTML) {
                $content[] = $this->std_get_html_content(
                    $row->id, 'lesson_answers', 'response', $lesson->courseid, null, 'timemodified', null, $row);
            }
        }
        $answerrows->close();

        return $content;
    }
}
