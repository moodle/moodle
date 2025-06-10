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
 * Support for course content
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport;

use tool_ally\componentsupport\interfaces\annotation_map;
use tool_ally\componentsupport\interfaces\html_content as iface_html_content;
use tool_ally\componentsupport\traits\html_content;
use tool_ally\componentsupport\traits\embedded_file_map;

use tool_ally\models\component_content;

/**
 * Html content support for pages.
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_component extends component_base implements iface_html_content, annotation_map {

    use html_content;
    use embedded_file_map;

    protected $tablefields = [
        'page' => ['intro', 'content']
    ];

    public static function component_type() {
        return self::TYPE_MOD;
    }

    public function get_course_html_content_items($courseid) {
        return $this->std_get_course_html_content_items($courseid);
    }

    public function get_html_content($id, $table, $field, $courseid = null) : ?component_content {
        global $DB;
        $content = $this->std_get_html_content($id, $table, $field, $courseid);
        if (empty($content)) {
            return $content;
        }

        if ($table === 'page') {
            $content->title = $DB->get_field('page', 'name', ['id' => $id]);
        }
        return ($content);
    }

    public function get_all_html_content($id) {
        return [
            $this->get_html_content($id, 'page', 'intro'),
            $this->get_html_content($id, 'page', 'content')
        ];
    }

    public function replace_html_content($id, $table, $field, $content) {
        return $this->std_replace_html_content($id, $table, $field, $content);
    }

    public function resolve_course_id($id, $table, $field) {
        global $DB;

        if ($table === 'page') {
            $course = $DB->get_field('page', 'course', ['id' => $id]);
            return $course;
        }

        throw new \coding_exception('Invalid table used to recover course id '.$table);
    }

    public function get_annotation_maps($courseid) {
        if (!$this->module_installed()) {
            return [];
        }

        $introcis = $this->get_intro_html_content_items($courseid);
        $contentcis = $this->get_selected_html_content_items($courseid, 'content');
        $mappings = $this->map_content_items_to_cmids('page', array_merge($introcis, $contentcis));

        $intros = [];
        foreach ($introcis as $introci) {
            $intros[$mappings[$introci->id]] = $introci->entity_id();
        }

        $content = [];
        foreach ($contentcis as $contentci) {
            $content[$mappings[$contentci->id]] = $contentci->entity_id();
        }

        return ['intros' => $intros, 'content' => $content];
    }

    /**
     * Attempt to make url for content.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param int $courseid
     */
    public function make_url($id, $table, $field = null, $courseid = null) {
        if (!isset($this->tablefields[$table])) {
            return null;
        }
        return $this->make_module_instance_url($table, $id);
    }
}
