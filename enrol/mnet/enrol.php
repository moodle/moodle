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
 * Implements the XML-RPC methods this plugin publishes to MNet peers
 *
 * This file must be named enrol.php because current MNet framework has the
 * filename hardcoded in XML-RPC path and we want to be compatible with
 * Moodle 1.x MNet clients. There is a proposal in MDL-21993 to allow
 * map XMP-RPC calls to whatever file, function, class or methods. Once this
 * is fixed, this file will be probably renamed to mnetlib.php (which could
 * be a common name of a plugin library containing functions/methods callable
 * via MNet framework.
 *
 * @package    enrol
 * @subpackage mnet
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
defined('MNET_SERVER') || die();

/**
 * MNet server-side methods that are part of mnetservice_enrol
 *
 * The weird name of the class tries to follow a pattern
 * {plugintype}_{pluginnname}_mnetservice_{servicename}
 *
 * Class methods are compatible with API 1 of the service used by Moodle 1.x
 * and 2.0 peers. The API version might become a part of class name but it is
 * not neccessary due to how xml-rcp methods are/will be mapped to php methods.
 */
class enrol_mnet_mnetservice_enrol {

    /**
     * Returns list of courses that we offer to the caller for remote enrolment of their users
     *
     * Since Moodle 2.0, courses are made available for MNet peers by creating an instance
     * of enrol_mnet plugin for the course. Hidden courses are not returned.
     *
     * @uses mnet_remote_client Callable via XML-RPC only
     * @return array
     */
    public function available_courses() {
        global $DB;

        if (!$client = get_mnet_remote_client()) {
            die('Callable via XML-RPC only');
        }

        $sql = "SELECT c.id AS remoteid, c.fullname, c.shortname, c.idnumber, c.summary, c.summaryformat,
                       c.sortorder, c.startdate, cat.id AS cat_id, cat.name AS cat_name,
                       cat.description AS cat_description, cat.descriptionformat AS cat_descriptionformat,
                       e.cost, e.currency, e.roleid AS defaultroleid, r.name
                  FROM {enrol} e
            INNER JOIN {course} c ON c.id = e.courseid
            INNER JOIN {course_categories} cat ON cat.id = c.category
            INNER JOIN {role} r ON r.id = e.roleid
                 WHERE e.enrol = 'mnet'
                       AND (e.customint1 = 0 OR e.customint1 = ?)
                       AND c.visible = 1
              ORDER BY cat.sortorder, c.sortorder, c.shortname";

        return $DB->get_records_sql($sql, array($client->id));
    }

    /**
     * TODO: short description.
     *
     * @return TODO
     */
    public function user_enrolments() {
        return array();
    }

    /**
     * TODO: short description.
     *
     * @return TODO
     */
    public function enrol_user() {
        return false;
    }

    /**
     * TODO: short description.
     *
     * @return TODO
     */
    public function unenrol_user() {
        return false;
    }

    /**
     * TODO: short description.
     *
     * @return TODO
     */
    public function course_enrolments() {
        return array();
    }
}
