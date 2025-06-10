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
 * Interface for supporting html content.
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\componentsupport\interfaces;

use tool_ally\models\component;
use tool_ally\models\component_content;

interface html_content {

    /**
     * @param $courseid
     * @return component[];
     */
    public function get_course_html_content_items($courseid);

    /**
     * Get the html content for a specific content item.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param null|int $courseid
     * @return component_content
     */
    public function get_html_content($id, $table, $field, $courseid = null) : ?component_content;

    /**
     * Get all html content for instance ($id).
     * E.g, for a course it will give you the summary and all the section content.
     * For modules with multiple rich fields it will give you all the content for each field.
     * @param $id
     * @return component[];
     */
    public function get_all_html_content($id);

    /**
     * Replaces the html content for a specific content item.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param string $content
     * @return string
     */
    public function replace_html_content($id, $table, $field, $content);

    /**
     * @param int $id
     * @param string $table
     * @param string $field
     * @return int
     */
    public function resolve_course_id($id, $table, $field);
}
