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
 * IMS Enterprise enrol plugin implementation.
 *
 * @package    enrol_imsenterprise
 * @copyright  2010 Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class imsenterprise_roles {
    private $imsroles;

    function __construct() {
        $this->imsroles = array(
        '01'=>'Learner',
        '02'=>'Instructor',
        '03'=>'Content Developer',
        '04'=>'Member',
        '05'=>'Manager',
        '06'=>'Mentor',
        '07'=>'Administrator',
        '08'=>'TeachingAssistant',
        );
        // PLEASE NOTE: It may seem odd that "Content Developer" has a space in it
        // but "TeachingAssistant" doesn't. That's what the spec says though!!!
    }

    function get_imsroles() {
        return $this->imsroles;
    }

    /**
    * This function is only used when first setting up the plugin, to
    * decide which role assignments to recommend by default.
    * For example, IMS role '01' is 'Learner', so may map to 'student' in Moodle.
    */
    function determine_default_rolemapping($imscode) {
        global $DB;

        switch($imscode) {
            case '01':
            case '04':
                $shortname = 'student';
                break;
            case '06':
            case '08':
                $shortname = 'teacher';
                break;
            case '02':
            case '03':
                $shortname = 'editingteacher';
                break;
            case '05':
            case '07':
                $shortname = 'admin';
                break;
            default:
                return 0; // Zero for no match
        }
        return (string)$DB->get_field('role', 'id', array('shortname'=>$shortname));
    }


}  // class


/**
 * Mapping between Moodle course attributes and IMS enterprise group description tags
 *
 * @package   enrol_imsenterprise
 * @copyright 2011 Aaron C Spike
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class imsenterprise_courses {

    private $imsnames;
    private $courseattrs;

    /**
     * Loads default
     */
    function __construct() {
        $this->imsnames = array(
            'short' => 'short',
            'long' => 'long',
            'full' => 'full',
            'coursecode' => 'coursecode');
        $this->courseattrs = array('shortname', 'fullname', 'summary');
    }

    /**
     * Returns the assignable values for the course attribute
     * @param string $courseattr The course attribute (shortname, fullname...)
     * @return array Array of assignable values
     */
    function get_imsnames($courseattr) {

        $values = $this->imsnames;
        if ($courseattr == 'summary') {
            $values = array_merge(array('ignore' => get_string('emptyattribute', 'enrol_imsenterprise')), $values);
        }
        return $values;
    }

    /**
     * courseattrs getter
     * @return array
     */
    function get_courseattrs() {
        return $this->courseattrs;
    }

    /**
     * This function is only used when first setting up the plugin, to
     * decide which name assignments to recommend by default.
     *
     * @param string $coursename
     * @return string
     */
    function determine_default_coursemapping($courseattr) {
        switch($courseattr) {
            case 'fullname':
                $imsname = 'short';
                break;
            case 'shortname':
                $imsname = 'coursecode';
                break;
            default:
                $imsname = 'ignore';
        }

        return $imsname;
    }

}  // class
