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
 * File containing the helper class.
 *
 * @package    tool_bulkenrol
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/cache/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Class containing a set of helpers.
 *
 * @package    tool_bulkenrol
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_bulkenrol_helper {




    /**
     * Resolve a user based on the data passed.
     *
     * Key accepted are:
     * - category, which is supposed to be a category ID.
     * - category_idnumber
     * - category_path, array of categories from parent to child.
     *
     * @param array $data to resolve the category from.
     * @param array $errors will be populated with errors found.
     * @return int category ID.
     */
    public static function resolve_user($data, $resolveby, &$errors = array()) {
        global $DB;
        $user = $DB->get_record('user', array($resolveby => $data));
        if (!$user) {
            $errors['couldnotresolveuser'] =
                new lang_string('couldnotresolveuser', 'tool_bulkenrol', $resolveby);
        }

        return $user;
    }

    public static function resolve_course($data, $resolveby, &$errors = array()) {
        
        global $DB;
        $course = $DB->get_record('course', array($resolveby => $data));
        if (!$course) {
            $errors['couldnotresolvecourse'] =
                new lang_string('couldnotresolvecourse', 'tool_bulkenrol', $resolveby);
        }

        return $course;
    }

    public static function resolve_role($data, $resolveby, &$errors = array()) {
        
        global $DB;
        $role = $DB->get_record('role', array($resolveby => $data));
        if (!$role) {
            $errors['couldnotresolverole'] =
                new lang_string('couldnotresolverole', 'tool_bulkenrol', $resolveby);
        }

        return $role;
    }

   
}