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

use moodle_url;

class component_content extends component {
    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $contenthash;

    /**
     * @var string
     */
    public $contenturl;

    /**
     * @var array - array of image file content hashes
     */
    public $embeddedfiles;

    /**
     * Component with content constructor.
     * @param int $id
     * @param string $component
     * @param string $table
     * @param string $field
     * @param int $courseid
     * @param int $timemodified
     * @param int $contentformat
     * @param string $content
     * @param null|string $title
     * @param null|string|moodle_url $url
     */
    public function __construct($id, $component, $table, $field, $courseid, $timemodified, $contentformat, $content,
                                $title = null, $url = null) {
        parent::__construct($id, $component, $table, $field, $courseid, $timemodified, $contentformat, $title);
        $this->content = $content;
        $this->contenthash = is_string($this->content) ? sha1($this->content) : '';
        $this->contenturl = $url ? $url instanceof moodle_url ? $url->out() : $url : $url;
        $this->embeddedfiles = [];
    }
}
