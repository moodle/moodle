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
 * Code fragment to define the module version etc.
 * This fragment is called by /admin/index.php
 *
 * @package mod-forum
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/simpletest/portfolio_testclass.php");
require_once("$CFG->dirroot/mod/forum/lib.php");

/*
 * TODO: The portfolio unit tests were obselete and did not work.
 * They have been commented out so that they do not break the
 * unit tests in Moodle 2.
 *
 * At some point:
 * 1. These tests should be audited to see which ones were valuable.
 * 2. The useful ones should be rewritten using the current standards
 *    for writing test cases.
 *
 * This might be left until Moodle 2.1 when the test case framework
 * is due to change.
 */
Mock::generate('forum_portfolio_caller', 'mock_caller');
Mock::generate('portfolio_exporter', 'mock_exporter');

class testForumPortfolioCallers extends portfoliolib_test {
/*
    public static $includecoverage = array('lib/portfoliolib.php', 'mod/forum/lib.php');
    public $module_type = 'forum';
    public $modules = array();
    public $entries = array();
    public $postcaller;
    public $discussioncaller;

    public function setUp() {
        global $DB, $USER;

        parent::setUp();

        $settings = array('quiet' => 1,
                          'verbose' => 0,

                          'pre_cleanup' => 0,
                          'post_cleanup' => 0,
                          'modules_list' => array($this->module_type),
                          'discussions_per_forum' => 5,
                          'posts_per_discussion' => 10,
                          'number_of_students' => 5,
                          'students_per_course' => 5,
                          'number_of_sections' => 1,
                          'number_of_modules' => 1,
                          'questions_per_course' => 0);

        generator_generate_data($settings);

        $this->modules = $DB->get_records($this->module_type);
        $first_module = reset($this->modules);
        $cm = get_coursemodule_from_instance($this->module_type, $first_module->id);

        $discussions = $DB->get_records('forum_discussions', array('forum' => $first_module->id));
        $first_discussion = reset($discussions);

        $posts = $DB->get_records('forum_posts', array('discussion' => $first_discussion->id));
        $first_post = reset($posts);

        $callbackargs = array('postid' => $first_post->id, 'discussionid' => $first_discussion->id);
        $this->postcaller = parent::setup_caller('forum_portfolio_caller', $callbackargs, $first_post->userid);

        unset($callbackargs['postid']);
        $this->discussioncaller = parent::setup_caller('forum_portfolio_caller', $callbackargs, $first_post->userid);

    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_caller_sha1() {
        $sha1 = $this->postcaller->get_sha1();
        $this->postcaller->prepare_package();
        $this->assertEqual($sha1, $this->postcaller->get_sha1());

        $sha1 = $this->discussioncaller->get_sha1();
        $this->discussioncaller->prepare_package();
        $this->assertEqual($sha1, $this->discussioncaller->get_sha1());
    }

    public function test_caller_with_plugins() {
        parent::test_caller_with_plugins();
    }
*/
}
