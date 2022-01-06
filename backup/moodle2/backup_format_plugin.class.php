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
 * Defines backup_format_plugin class
 *
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2011 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class extending standard backup_plugin in order to implement some
 * helper methods related with the course formats (format plugin)
 *
 * TODO: Finish phpdocs
 */
abstract class backup_format_plugin extends backup_plugin {

    protected $courseformat; // To store the format (course->format) of the instance

    public function __construct($plugintype, $pluginname, $optigroup, $step) {

        parent::__construct($plugintype, $pluginname, $optigroup, $step);

        $this->courseformat = backup_plan_dbops::get_courseformat_from_courseid($this->task->get_courseid());

    }

    /**
     * Return the condition encapsulated into sqlparam format
     * to get evaluated by value, not by path nor processor setting
     */
    protected function get_format_condition() {
        return array('sqlparam' => $this->courseformat);
    }
}
