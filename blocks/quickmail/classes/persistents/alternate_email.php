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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\persistents;

defined('MOODLE_INTERNAL') || die();

use core_user;
use core\ip_utils;
use block_quickmail_string;
use block_quickmail\persistents\concerns\enhanced_persistent;
use block_quickmail\persistents\concerns\can_be_soft_deleted;
use block_quickmail\persistents\concerns\belongs_to_a_course;
use block_quickmail\repos\role_repo;

class alternate_email extends \block_quickmail\persistents\persistent {

    use enhanced_persistent,
        belongs_to_a_course,
        can_be_soft_deleted;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_alt_emails';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'setup_user_id' => [
                'type' => PARAM_INT,
            ],
            'firstname' => [
                'type' => PARAM_TEXT,
            ],
            'lastname' => [
                'type' => PARAM_TEXT,
            ],
            'allowed_role_ids' => [
                'type' => PARAM_TEXT,
                'default' => ''
            ],
            'course_id' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'user_id' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'email' => [
                'type' => PARAM_EMAIL,
            ],
            'is_validated' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'timedeleted' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    // Relationships.
    /**
     * Returns the user object for the user who created this alternate email
     *
     * @return stdClass
     */
    public function get_setup_user() {
        return core_user::get_user($this->get('setup_user_id'));
    }

    /**
     * Returns the course object of this alternate email.
     *
     * @return stdClass|false
     */
    public function get_course() {
        $courseid = $this->get('course_id');

        return $courseid ? get_course($this->get('course_id')) : false;
    }

    /**
     * Returns the user object of this alternate email.
     *
     * @return stdClass|false
     */
    public function get_user() {
        $userid = $this->get('user_id');

        return $userid ? core_user::get_user($this->get('user_id')) : false;
    }

    // Getters.
    /**
     * Returns the status of this alternates approval (confirmed or waiting)
     *
     * @return string
     */
    public function get_status() {
        return $this->get('is_validated') ?
            block_quickmail_string::get('alternate_confirmed') :
            block_quickmail_string::get('alternate_waiting');
    }

    /**
     * Returns the full name assigned to this alternate
     *
     * @return string
     */
    public function get_fullname() {
        return $this->get('firstname') . ' ' .  $this->get('lastname');
    }

    /**
     * Returns the "scope" (availability) of the usage of the alternate email
     *
     * @return string
     */
    public function get_scope() {
        // First, get course if assigned to one.
        if (!empty($this->get('course_id'))) {
            // Get course short name, defaulting to a generic name if necessary.
            if ($course = $this->get_course()) {
                $courseshortname = $course->shortname;
            } else {
                $courseshortname = 'Non-Existent Course';
            }
        }

        if (!empty($this->get('course_id')) && ! empty($this->get('user_id'))) {
            return block_quickmail_string::get('alternate_availability_only', (object) ['courseshortname' => $courseshortname]);
        } else if (!empty($this->get('course_id'))) {
            return block_quickmail_string::get('alternate_availability_course', (object) ['courseshortname' => $courseshortname]);
        } else {
            return block_quickmail_string::get('alternate_availability_user');
        }
    }

    /**
     * Returns the domain of this email
     *
     * @return string
     */
    public function get_domain() {
        $email = $this->get('email');

        return substr($email, strpos($email, '@') + 1);
    }

    /**
     * Returns any specified allowed_role_ids as an array
     *
     * @return array
     */
    public function get_allowed_roles() {
        if (!$allowedroleids = $this->get('allowed_role_ids')) {
            return [];
        }

        return explode(',', $allowedroleids);
    }

    // Setters.
    /**
     * Convenience method to set the "setup" user ID.
     *
     * @param object|int $idorobject The user ID, or a user object.
     */
    protected function set_setup_user_id($idorobject) {
        $usersid = $idorobject;

        if (is_object($idorobject)) {
            $usersid = $idorobject->id;
        }

        $this->raw_set('setup_user_id', $usersid);
    }

    /**
     * Convenience method to set the course ID.
     *
     * @param object|int $idorobject The course ID, or a course object.
     */
    protected function set_course_id($idorobject) {
        $coursesid = $idorobject;

        if (is_object($idorobject)) {
            $coursesid = $idorobject->id;
        }

        $this->raw_set('course_id', $coursesid);
    }

    /**
     * Convenience method to set the user ID.
     *
     * @param object|int $idorobject The user ID, or a user object.
     */
    protected function set_user_id($idorobject) {
        $usersid = $idorobject;

        if (is_object($idorobject)) {
            $usersid = $idorobject->id;
        }

        $this->raw_set('user_id', $usersid);
    }

    /**
     * Convenience method to format the firstname
     *
     * @param string  $firstname
     */
    protected function set_firstname($firstname) {
        $firstname = ucfirst($firstname);

        $this->raw_set('firstname', $firstname);
    }

    /**
     * Convenience method to format the lastname
     *
     * @param string  $lastname
     */
    protected function set_lastname($lastname) {
        $lastname = ucfirst($lastname);

        $this->raw_set('lastname', $lastname);
    }

    /**
     * Convenience method to format the email
     *
     * @param string  $email
     */
    protected function set_email($email) {
        $email = strtolower($email);

        $this->raw_set('email', $email);
    }

    // Validators.
    // Custom Methods.
    /**
     * Reports whether or not this alternate email is in the "allowed email domain" list
     *
     * @return bool
     */
    public function is_in_allowed_sending_domains() {
        global $CFG;

        // If no config set, not allowed!!
        if (!isset($CFG->allowedemaildomains) || empty(trim($CFG->allowedemaildomains))) {
            return false;
        }

        // Get the allowed domain array.
        $alloweddomains = array_map('trim', explode("\n", $CFG->allowedemaildomains));

        // Get this alternate email.
        $email = $this->get('email');

        return ip_utils::is_domain_in_allowed_list(substr($email, strpos($email, '@') + 1), $alloweddomains);
    }

    // Custom Static Methods.
    /**
     * Returns all alternate emails belonging to the given user id
     *
     * @param  int     $usersid
     * @return array
     */
    public static function get_all_for_user($usersid) {
        // Get all alternate emails set up by this user, if any.
        return self::get_records(['setup_user_id' => $usersid, 'timedeleted' => 0]);
    }

    /**
     * Returns an array of alternate emails available to the given course/user combination
     *
     * @param  int        $coursesid
     * @param  mdl_user   $user
     * @param  bool       $includeuseremail    whether or not to include this user's email in the results
     * @return array   (alternate_email id => alternate_email title)
     */
    public static function get_flat_array_for_course_user($coursesid, $user, $includeuseremail = true) {
        // Get all validated alternates available to this user.
        $useralternateemails = self::get_records(['user_id' => $user->id, 'is_validated' => 1, 'timedeleted' => 0]);

        // Include user's email in results if necessary.
        $initial = $includeuseremail
            ? [0 => $user->email]
            : [];

        $results = array_reduce($useralternateemails, function ($carry, $alternateemail) {
            $carry[$alternateemail->get('id')] = $alternateemail->get('email');

            return $carry;
        }, $initial);

        // Get all validated alternates available to this course.
        $coursealternateemails = self::get_records(
            ['course_id' => $coursesid, 'user_id' => 0, 'is_validated' => 1, 'timedeleted' => 0]
        );

        $userroleids = role_repo::get_user_roles_in_course($user->id, $coursesid);

        // Iterate through all course-scoped alternates.
        foreach ($coursealternateemails as $alternate) {
            $allowedroleids = $alternate->get_allowed_roles();

            // If no roles required for this alternate, add to results.
            if (empty($allowedroleids)) {
                $results[$alternate->get('id')] = $alternate->get('email');

                // Otherwise, if this user has a role within the allowed roles, add to results.
            } else if (!empty(array_intersect($userroleids, $allowedroleids))) {
                $results[$alternate->get('id')] = $alternate->get('email');
            }
        }

        return $results;
    }

}
