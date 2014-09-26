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
 * General class providing access to common grading features
 *
 * Grading manager provides access to the particular grading method controller
 * in that area.
 *
 * Fully initialized instance of the grading manager operates over a single
 * gradable area. It is possible to work with a partially initialized manager
 * that knows just context and component without known area, for example.
 * It is also possible to change context, component and area of an existing
 * manager. Such pattern is used when copying form definitions, for example.
 *
 * @package    mod_forum
 * @copyright  2014 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category   grading
 */

defined('MOODLE_INTERNAL') || die();


class mod_forum_grading_manager extends grading_manager {
    /**
     * Returns the list of installed grading plugins together, optionally extended
     * with a simple direct grading.
     *
     * @param bool $includenone should the 'Simple direct grading' be included
     * @return array of the (string)name => (string)localized title of the method
     */
    public static function available_methods($includenone = true) {
        if ($includenone) {
            $list = array('none' => get_string('gradingmethodnone', 'mod_forum'));
        } else {
            $list = array();
        }

        foreach (core_component::get_plugin_list('gradingform') as $name => $location) {
            $list[$name] = get_string('pluginname', 'gradingform_'.$name);
        }

        return $list;
    }
}