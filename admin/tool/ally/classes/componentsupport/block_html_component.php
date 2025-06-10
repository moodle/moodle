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
 * Html content support for book module.
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport;

use tool_ally\componentsupport\interfaces\annotation_map;
use tool_ally\componentsupport\interfaces\html_content as iface_html_content;
use tool_ally\componentsupport\traits\html_content;
use tool_ally\componentsupport\traits\embedded_file_map;
use tool_ally\models\component;
use tool_ally\models\component_content;

use moodle_url;

/**
 * Html content support for block_html.
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_html_component extends component_base implements iface_html_content, annotation_map {

    use html_content;
    use embedded_file_map;

    protected $tablefields = [
        'block_instances' => ['configdata']
    ];

    protected function unpack_configdata($configdata) {
        return unserialize(base64_decode($configdata));
    }

    protected function pack_configdata(\stdClass $configdata) {
        return base64_encode(serialize($configdata));
    }

    public static function component_type() {
        return self::TYPE_BLOCK;
    }

    public function get_file_area($table, $field) {
        return 'content'; // Always content for this component.
    }

    public function get_course_html_content_items($courseid) {
        global $DB;

        $array = [];
        if (!$this->module_installed()) {
            return $array;
        }

        $context = \context_course::instance($courseid);
        $rs = $DB->get_recordset('block_instances', ['parentcontextid' => $context->id, 'blockname' => 'html']);
        foreach ($rs as $row) {
            $contentobj = unserialize(base64_decode($row->configdata));
            $array[] = new component(
                $row->id,
                'block_html',
                'block_instances',
                'configdata',
                $courseid,
                $row->timemodified,
                $contentobj->format ?? '',
                $contentobj->title ?? ''
            );
        }

        $rs->close();

        return $array;
    }

    public function get_html_content($id, $table, $field, $courseid = null) : ?component_content {
        global $DB;

        if (!$this->module_installed()) {
            return null;
        }

        $this->validate_component_table_field($table, $field);

        // We only support the table block_instances and configdata!

        $row = $DB->get_record('block_instances', ['id' => $id]);
        $contentobj = $this->unpack_configdata($row->configdata);

        $url = $courseid ? new moodle_url('course/view.php', ['id' => $courseid]) : null;

        $contentmodel = new component_content(
            $id,
            $this->get_component_name(),
            'block_instances',
            'configdata',
            $courseid,
            $row->timemodified,
            $contentobj->format ?? '',
            $contentobj->text ?? '',
            $contentobj->title ?? '',
            $url
            );

        return $contentmodel;
    }

    public function get_all_html_content($id) {
        return [$this->get_html_content($id, 'block_instances', 'configdata')];
    }

    public function replace_html_content($id, $table, $field, $content) {
        global $DB;

        $row = $DB->get_record('block_instances', ['id' => $id]);
        $configdata = $this->unpack_configdata($row->configdata);
        $configdata->text = $content;
        $row->configdata = $this->pack_configdata($configdata);
        return $DB->update_record('block_instances', $row);
    }

    public function resolve_course_id($id, $table, $field) {
        global $DB;

        if ($table === 'block_instances') {
            $row = $DB->get_record('block_instances', ['id' => $id]);
            $context = \context::instance_by_id($row->parentcontextid);
            if ($context->contextlevel === CONTEXT_COURSE) {
                return $context->instanceid;
            } else {
                throw new \coding_exception('Failed to get courseid for block instance '.$id);
            }
        }

        throw new \coding_exception('Invalid table used to recover course id '.$table);
    }

    public function get_annotation($id) {
        return $this->get_component_name().':'.$this->get_component_name().':configdata:'.$id;
    }

    public function get_annotation_maps($courseid) {
        if (!$this->module_installed()) {
            return [];
        }

        $entities = [];
        $contents = $this->get_course_html_content_items($courseid);
        foreach ($contents as $content) {
            $entities[$content->id] = $content->entity_id();
        }

        return $entities;
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

        $row = $DB->get_record('block_instances', ['id' => $id]);
        $context = \context::instance_by_id($row->parentcontextid);
        if ($context->contextlevel === CONTEXT_COURSE) {
            $courseid = $context->instanceid;
            return new moodle_url('course/view.php', ['id' => $courseid]);
        }

        return null;

    }
}
