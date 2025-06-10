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
 * Component content model.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\models;

use tool_ally\local_content;
use stdClass;

class component {
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $component;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $field;

    /**
     * @var int
     */
    public $courseid;

    /**
     * @var int
     */
    public $timemodified;

    /**
     * @var int
     */
    public $contentformat;

    /**
     * @var string
     */
    public $title;

    /**
     * @var stdClass
     */
    public $meta;

    /**
     * Component constructor.
     * @param int $id
     * @param string $component
     * @param string $table
     * @param string $field
     * @param int $courseid
     * @param int $timemodified
     * @param int $contentformat
     * @param string $title
     */
    public function __construct($id, $component, $table, $field, $courseid, $timemodified, $contentformat, $title = null) {
        if (empty($timemodified)) {
            $timemodified = time();
        }
        $timemodified = (int) $timemodified;
        $courseid = $courseid ? (int) $courseid : $courseid;
        $this->id = (int) $id;
        $this->component = $component;
        $this->table = $table;
        $this->field = $field;
        $this->courseid = $courseid;
        $this->timemodified = $timemodified;
        $this->contentformat = (string) $contentformat;
        $this->title = $title;
        $this->meta = new stdClass;
    }

    /**
     * @return string
     */
    public function entity_id() {
        $entities = [$this->component, $this->table, $this->field, $this->id];
        return implode(':', $entities);
    }

    /**
     * @return int
     */
    public function get_courseid() {
        if (!empty($this->courseid)) {
            return $this->courseid;
        }
        $component = local_content::component_instance($this->component);
        return $component->resolve_course_id($this->id, $this->table, $this->field);
    }
}
