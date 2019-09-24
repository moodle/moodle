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
 * Generator for the core_grading subsystem generator.
 *
 * @package    core_grading
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Generator for the core_grading subsystem generator.
 *
 * @package    core_grading
 * @category   test
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grading_generator extends component_generator_base {

    /**
     * Create an instance of an advanced grading area.
     *
     * @param context $context
     * @param string $component
     * @param string $areaname An area belonging to the specified component
     * @param string $method An available gradingform type
     * @return gradingform_controller The controller for the created instance
     */
    public function create_instance(context $context, string $component, string $areaname, string $method): gradingform_controller {
        require_once(__DIR__ . '/../../lib.php');

        $manager = get_grading_manager($context, $component, $areaname);
        $manager->set_active_method($method);

        return $manager->get_controller($method);
    }
}
