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
 * Defines restore_gradingform_plugin class
 * @package     core_backup
 * @subpackage  moodle2
 * @category    backup
 * @copyright   2011 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for all advanced grading form plugins
 */
abstract class restore_gradingform_plugin extends restore_plugin {

    /**
     * Helper method returning the mapping identifierto use for
     * grading form instance's itemid field
     *
     * @param array $areaname the name of the area the form is defined for
     * @return string the mapping identifier
     */
    public static function itemid_mapping($areaname) {
        return 'grading_item_'.$areaname;
    }
}
