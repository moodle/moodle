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
 * Behat steps definitions for drag and drop onto image.
 *
 * @package   gradereport_grader
 * @category  test
 * @copyright 2015 Oakland University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Steps definitions related with the drag and drop onto image question type.
 *
 * @copyright 2015 Oakland University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_gradereport_grader extends behat_base {

    /**
     * Gets the user id from its name.
     *
     * @throws Exception
     * @param string $name
     * @return int
     */
    protected function get_user_id($name) {
        global $DB;
        $names = explode(' ', $name);

        if (!$id = $DB->get_field('user', 'id', array('firstname' => $names[0], 'lastname' => $names[1]))) {
            throw new Exception('The specified user with username "' . $name . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the grade item id from its name.
     *
     * @throws Exception
     * @param string $itemname
     * @return int
     */
    protected function get_grade_item_id($itemname) {

        global $DB;

        if ($id = $DB->get_field('grade_items', 'id', array('itemname' => $itemname))) {
            return $id;
        }

        // The course total is a special case.
        if ($itemname === "Course total") {
            if (!$id = $DB->get_field('grade_items', 'id', array('itemtype' => 'course'))) {
                throw new Exception('The specified grade_item with name "' . $itemname . '" does not exist');
            }
            return $id;
        }

        // Find a category with the name.
        if ($catid = $DB->get_field('grade_categories', 'id', array('fullname' => $itemname))) {
            if ($id = $DB->get_field('grade_items', 'id', array('iteminstance' => $catid))) {
                return $id;
            }
        }

        throw new Exception('The specified grade_item with name "' . $itemname . '" does not exist');
    }

}
