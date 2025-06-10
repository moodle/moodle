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
 * Plugin file url properties model.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally\models;


class pluginfileurlprops {

    /**
     * @var int
     */
    public $contextid;

    /**
     * @var string
     */
    public $component;

    /**
     * @var string
     */
    public $filearea;

    /**
     * @var int
     */
    public $itemid;

    /**
     * @var string
     */
    public $filename;

    /**
     * @var string
     */
    public $filepath;

    /**
     * pluginfileurlprops constructor.
     * @param int $contextid
     * @param string $component
     * @param string $filearea
     * @param int $itemid
     * @param string $filename
     */
    public function __construct($contextid, $component, $filearea, $itemid, $filename) {
        $this->contextid = $contextid;
        $this->component = $component;
        $this->filearea = $filearea;
        $this->itemid = $itemid;
        $this->filename = $filename;

        $this->process_props();
    }

    private function process_props() {
        // Strip params from end of the url .e.g. file.pdf?forcedownload=1.
        // No need to worry about & symbol as it always comes after ? symbol.
        $query = strpos($this->filename, '?');
        if ($query) {
            $this->filename = substr($this->filename, 0, $query);
        }

        $this->filename = urldecode($this->filename);
        $this->filearea = urldecode($this->filearea);
        $filepath = dirname($this->filename);
        $this->filepath = $filepath == '.' ? '/' : '/' . $filepath . '/';
    }

    public function to_list() {
        return [$this->contextid, $this->component, $this->filearea, $this->itemid, $this->filename];
    }

}
