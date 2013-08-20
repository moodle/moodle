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
 * The module forums external functions unit tests
 *
 * @package    mod_forum
 * @category   external
 * @copyright  2013 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

class mod_forum_maildigest_testcase extends advanced_testcase {

    public function test_mod_forum_set_maildigest() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user();

        // Set to the user.
        self::setUser($user);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();

        $forumids = array();

        // First forum.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);
        $forumids[] = $forum1->id;

        // Check the forum was correctly created.
        list ($test, $params) = $DB->get_in_or_equal($forumids, SQL_PARAMS_NAMED, 'forum');

        $this->assertEquals(count($forumids),
            $DB->count_records_select('forum', 'id ' . $test, $params));

        // Enrol the user in the courses.
        // DataGenerator->enrol_user automatically sets a role for the user
        $this->getDataGenerator()->enrol_user($user->id, $course1->id, null, 'manual');

        // Confirm that there is no current value.
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertFalse($currentsetting);

        // Test with each of the valid values:
        // 0, 1, and 2 are valid values.
        forum_set_user_maildigest($forum1, 0, $user);
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertEquals($currentsetting->maildigest, 0);

        forum_set_user_maildigest($forum1, 1, $user);
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertEquals($currentsetting->maildigest, 1);

        forum_set_user_maildigest($forum1, 2, $user);
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertEquals($currentsetting->maildigest, 2);

        // And the default value - this should delete the record again
        forum_set_user_maildigest($forum1, -1, $user);
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertFalse($currentsetting);

        // Try with an invalid value.
        $this->setExpectedException('moodle_exception');
        forum_set_user_maildigest($forum1, 42, $user);
    }

    public function test_mod_forum_get_user_digest_options_default() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user();

        // Set to the user.
        self::setUser($user);

        // We test against these options.
        $digestoptions = array(
            '0' => get_string('emaildigestoffshort', 'mod_forum'),
            '1' => get_string('emaildigestcompleteshort', 'mod_forum'),
            '2' => get_string('emaildigestsubjectsshort', 'mod_forum'),
        );

        // The default settings is 0.
        $this->assertEquals(0, $user->maildigest);
        $options = forum_get_user_digest_options();
        $this->assertEquals($options[-1], get_string('emaildigestdefault', 'mod_forum', $digestoptions[0]));

        // Update the setting to 1.
        $USER->maildigest = 1;
        $this->assertEquals(1, $USER->maildigest);
        $options = forum_get_user_digest_options();
        $this->assertEquals($options[-1], get_string('emaildigestdefault', 'mod_forum', $digestoptions[1]));

        // Update the setting to 2.
        $USER->maildigest = 2;
        $this->assertEquals(2, $USER->maildigest);
        $options = forum_get_user_digest_options();
        $this->assertEquals($options[-1], get_string('emaildigestdefault', 'mod_forum', $digestoptions[2]));
    }

    public function test_mod_forum_get_user_digest_options_sorting() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        // Create a user.
        $user = self::getDataGenerator()->create_user();

        // Set to the user.
        self::setUser($user);

        // Retrieve the list of applicable options.
        $options = forum_get_user_digest_options();

        // The default option must always be at the top of the list.
        $lastoption = -2;
        foreach ($options as $value => $description) {
            $this->assertGreaterThan($lastoption, $value);
            $lastoption = $value;
        }
    }

}
