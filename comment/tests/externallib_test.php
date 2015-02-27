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
 * Events tests.
 *
 * @package    block_comments
 * @category   test
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Events tests class.
 *
 * @package    block_comments
 * @category   test
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG;
class core_comment_commentlib_testcase extends advanced_testcase {
    
    public $course;
    public $wiki;

    public function test_comments_get_comments() {
        
        $commenttext = 'New comment';
        //$this->resetAfterTest();
        $this->setAdminUser();
        
        $this->course = $this->getDataGenerator()->create_course();
        $this->wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $this->course->id));
        require_once($CFG->dirroot . '/comment/lib.php');

        // Comment on course page.
        $context = context_course::instance($this->course->id);
        $args = new stdClass;
        $args->context = $context;
        $args->course = $this->course;
        $args->area = 'page_comments';
        $args->itemid = 0;
        $args->component = 'block_comments';
        $args->linktext = get_string('showcomments');
        $args->notoggle = true;
        $args->autostart = true;
        $args->displaycancel = false;
        $comment = new comment($args);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $comment->add($commenttext);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\block_comments\event\comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new moodle_url('/course/view.php', array('id' => $this->course->id));
        $this->assertEquals($url, $event->get_url());
        $a = $comment->get_comments(0);
        print_object($a);
        exit();
        
        /*
        // Comments when block is on module (wiki) page.
        $context = context_module::instance($this->wiki->cmid);
        $args = new stdClass;
        $args->context   = $context;
        $args->course    = $this->course;
        $args->area      = 'page_comments';
        $args->itemid    = 0;
        $args->component = 'block_comments';
        $args->linktext  = get_string('showcomments');
        $args->notoggle  = true;
        $args->autostart = true;
        $args->displaycancel = false;
        $comment = new comment($args);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $comment->add('New comment 1');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\block_comments\event\comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new moodle_url('/mod/wiki/view.php', array('id' => $this->wiki->cmid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
        */
    }
}
