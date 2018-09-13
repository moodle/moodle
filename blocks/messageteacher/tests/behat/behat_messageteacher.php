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
 * Defines custom behat steps for messageteacher
 *
 * @package    block_messageteacher
 * @author      Mark Johnson <mark@barrenfrozenwasteland.com>
 * @copyright   2013 Mark Johnson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given;
use Behat\Behat\Context\Step\When as When;
use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Custom behat steps for messageteacher
 */
class behat_messageteacher extends behat_base {
    /**
     * Adds an instance of block_messageteacher to a given course.
     *
     * @Given /^there is an instance of messageteacher on "(?P<coursename_string>(?:[^"]|\\")*)"$/
     * @params string $coursename The full name of the course.
     */
    public function there_is_an_instance_of_messageteacher_on($coursename) {

        return array(new Given('I log in as "admin"'),
            new Given('I am on "'.$coursename.'" course homepage with editing mode on'),
            new Given('I add the "Message My Teacher" block'),
            new Given('I log out'));

    }

    /**
     * Sets configuration for the block_messageteacher plugin. A table with | Setting name | value | is expected.
     *
     * @Given /^messageteacher has the following settings:$/
     * @param TableNode $table The list of settings.
     * @return void
     */
    public function messageteacher_has_the_following_settings(TableNode $table) {

        if (!$data = $table->getRowsHash()) {
            return;
        }

        foreach ($data as $setting => $value) {
            set_config($setting, $value, 'block_messageteacher');
        }
    }

    /**
     * Creates course category enrolments. A table with | user | category | role | is expected.
     * User is found by username, category by idnumber and role by shortname.
     *
     * @Given /^the following category enrolments exist:$/
     * @param TableNode $table The list of category enrolments.
     * @return bool;
     */
    public function the_following_category_enrolments_exists(TableNode $table) {
        global $DB;

        if (!$data = $table->getRowsHash()) {
            return;
        }
        foreach ($data as $first => $rest) {
            if ($first == 'user') {
                continue;
            }
            $userid = $DB->get_field('user', 'id', array('username' => $first));
            $catid = $DB->get_field('course_categories', 'id', array('idnumber' => $rest[0]));
            $contextid = context_coursecat::instance($catid)->id;
            $roleid = $DB->get_field('role', 'id', array('shortname' => $rest[1]));
            if (!$userid || !$contextid || !$roleid) {
                throw new Exception('Invalid category enrolment data provided. Expected table of | user | category | role |.');
            }
            role_assign($roleid, $userid, $contextid);
        }
        return true;
    }
}
