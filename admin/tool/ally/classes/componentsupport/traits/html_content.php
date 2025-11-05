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
 * Trait for supporting html content.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport\traits;

use tool_ally\local;
use tool_ally\local_content;
use tool_ally\models\component;
use tool_ally\models\component_content;

use stdClass;

trait html_content {

    /**
     * Standard method for getting course html content items.
     *
     * @param $courseid
     * @return array
     * @throws \dml_exception
     */
    protected function std_get_course_html_content_items($courseid) {
        global $DB, $PAGE;

        $array = [];
        if (!$this->module_installed()) {
            return $array;
        }

        $component = $this->get_component_name();

        if (!empty($this->tablefields) && !empty($this->tablefields[$component])) {
            $fields = $this->tablefields[$component];
        } else {
            $fields = ['intro'];
        }

        $fieldselect = [];
        $params = [$courseid];
        foreach ($fields as $field) {
            $params[] = FORMAT_HTML;
            $fieldselect[] = "({$field}format = ? AND $field != '')";
        }

        $fieldselect = implode(' OR ', $fieldselect);
        $select = "course = ? AND ($fieldselect)";

        $rs = $DB->get_recordset_select($component, $select, $params);
        foreach ($rs as $row) {
            foreach ($fields as $field) {
                $formatfield = $field.'format';
                if (!empty($row->$field) && $row->$formatfield === FORMAT_HTML) {
                    if ($component == 'label' && !empty($row->intro)) {
                        $PAGE->set_context(\context_course::instance($courseid));
                        $row->name = strip_tags(format_string($row->intro, true));
                    }
                    $array[] = new component(
                        $row->id, $component, $component, $field, $courseid, $row->timemodified,
                        $row->$formatfield, $row->name);
                }
            }
        }
        $rs->close();

        return $array;
    }

    /**
     * Standard method for getting html content.
     *
     * @param int $id
     * @param string $table
     * @param string $field
     * @param array $tablefields
     * @param null|int $courseid
     * @param string $titlefield
     * @param string $modifiedfield
     * @param callable $recordlambda - lambda to run on record once recovered.
     * @param stdClass|null|bool $record
     * @return component_content | null;
     * @throws \coding_exception
     */
    protected function std_get_html_content($id, $table, $field, $courseid = null, $titlefield = 'name',
                                            $modifiedfield = 'timemodified', $recordlambda = null,
                                            $record = null) : ?component_content {
        global $DB;

        static $prevrecord = null;
        static $prevrecordkey = null;

        if (local::duringtesting()) {
            $prevrecord = null;
            $prevrecordkey = null;
        }

        if (!$this->module_installed()) {
            return null;
        }

        $component = $this->get_component_name();

        $this->validate_component_table_field($table, $field);

        // If we don't have a record, was the previous record we got the same as the one we need now?
        // Note: This is an optimisation for where we want to build content for one record that has
        // multiple content fields.
        if (empty($record)) {
            $newrecordkey = $table . '_' . $id;
            if ($newrecordkey === $prevrecordkey && $prevrecord) {
                $record = $prevrecord;
            }
        }

        // Record is still null, let's get it.
        if (empty($record)) {
            $record = $DB->get_record($table, ['id' => $id]);
            if (isset($record->course) && !empty($courseid) && $record->course != $courseid) {
                return null;
            }
        }
        if (!$record) {
            return null;
        }

        // Static cache the record.
        $prevrecord = $record;
        $prevrecordkey = $table . '_' . $id;

        if ($recordlambda) {
            $recordlambda($record);
            if ($courseid === null) {
                if (!empty($record->course)) {
                    $courseid = $record->course;
                } else if (!empty($record->courseid)) {
                    $courseid = $record->courseid;
                }
            }
        }

        $timemodified = $record->$modifiedfield;
        if ($modifiedfield === 'timemodified' && empty($timemodified)) {
            if (!empty($record->timecreated)) {
                $timemodified = $record->timecreated;
            }
        }
        $content = $record->$field;
        $formatfield = $field.'format';
        $contentformat = $record->$formatfield;
        $title = !empty($record->$titlefield) ? $record->$titlefield : null;
        $url = null;
        if (method_exists($this, 'make_url')) {
            $url = $this->make_url($id, $table, $field, $courseid);
        }

        $contentmodel = new component_content($id, $component, $table, $field, $courseid, $timemodified, $contentformat,
            $content, $title, $url);

        return $contentmodel;
    }

    /**
     * Return a content model for a deleted content item.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param int $courseid // This is mandatory because you should be able to get it from the event.
     * @param null|int $timemodified
     * @return component_content
     */
    public function get_html_content_deleted($id, $table, $field, $courseid, $timemodified = null) {
        if (!$this->module_installed()) {
            return null;
        }

        $timemodified = $timemodified ? $timemodified : time();
        $component = $this->get_component_name();
        $contentmodel = new component_content($id, $component, $table, $field, $courseid, $timemodified,
            FORMAT_HTML, '', '');
        return $contentmodel;
    }

    /**
     * Standard method for replacing html content.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param string $content
     * @return mixed
     * @throws \coding_exception
     */
    protected function std_replace_html_content($id, $table, $field, $content) {
        global $DB;

        if (!$this->module_installed()) {
            return null;
        }

        $this->validate_component_table_field($table, $field);

        $dobj = (object) [
            'id' => $id,
            $field => $content
        ];
        if (!$DB->update_record($table, $dobj)) {
            return false;
        }

        if ($this->component_type() === self::TYPE_MOD && $table === $this->get_component_name()) {
            list ($course, $cm) = get_course_and_cm_from_instance($id, $table);
            \core\event\course_module_updated::create_from_cm($cm, $cm->context)->trigger();
            // Course cache needs updating to show new module text.
            rebuild_course_cache($course->id, true);
        }

        return true;
    }

    /**
     * @param int $courseid
     * @param string $contentfield
     * @param null|string $table
     * @param null|string $selectfield
     * @param null|string|array $selectval
     * @param null|string $titlefield
     * @param null| \callable $compmetacallback
     * @param boolean $includecontentcheck
     * @return component[]
     * @throws \dml_exception
     */
    protected function get_selected_html_content_items($courseid, $contentfield,
                                                       $table = null, $selectfield = null,
                                                       $selectval = null, $titlefield = null,
                                                       $compmetacallback = null, $includecontentcheck = true) {
        global $DB;

        if (!$this->module_installed()) {
            return [];
        }

        $array = [];

        $compname = $this->get_component_name();
        $table = $table === null ? $compname : $table;
        $selectfield = $selectfield === null ? 'course' : $selectfield;
        $selectval = $selectval === null ? $courseid : $selectval;
        $titlefield = $titlefield === null ? 'name' : $titlefield;
        list($selectsql, $selectparams) = $DB->get_in_or_equal($selectval);

        $formatfld = $contentfield.'format';

        if ($includecontentcheck) {
            $select = "$selectfield $selectsql AND $formatfld = ? AND $contentfield != ''";
        } else {
            $select = "$selectfield $selectsql AND $formatfld = ?";
        }

        $params = array_merge($selectparams, [FORMAT_HTML]);
        $rs = $DB->get_recordset_select($table, $select, $params);
        foreach ($rs as $row) {
            $comp = new component(
                $row->id, $compname, $table, $contentfield, $courseid, $row->timemodified,
                $row->$formatfld, $row->$titlefield);
            if (is_callable($compmetacallback)) {
                $comp->meta = $compmetacallback($row);
            }
            $array[] = $comp;
        }
        $rs->close();

        return $array;
    }

    /**
     * Get introduction html content items.
     * @param int $courseid
     * @param boolean $includecontentcheck
     * @return array
     * @throws \dml_exception
     */
    protected function get_intro_html_content_items($courseid, $includecontentcheck = true) {
        return $this->get_selected_html_content_items($courseid,
            'intro',
            null,
            null,
            null,
            null,
            null,
            $includecontentcheck);
    }


    /**
     * @param string $module
     * @param int $id
     * @return string
     * @throws \moodle_exception
     */
    protected function make_module_instance_url($module, $id) {
        try {
            list($course, $cm) = get_course_and_cm_from_instance($id, $module);
        } catch (\moodle_exception $e) {
            // Sometimes this can get called before the module is in the core functions, so just return empty.
            return '';
        }

        return new \moodle_url('/course/view.php?id=' . $course->id . '#module-' . $cm->id) . '';
    }

    /**
     * @param component[] $contents
     */
    protected function bulk_queue_delete_content(array $contents) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        foreach ($contents as $content) {
            local_content::queue_delete($content->courseid,
                $content->id, $content->component, $content->table, $content->field);
        }

        $transaction->allow_commit();
    }

    /**
     * Stub method - must be overriden if annotation to be supported.
     * Returns annotation 'componentname:table:field:id'.
     * @param int $id
     * @return string
     */
    public function get_annotation($id) {
        return '';
    }
}
