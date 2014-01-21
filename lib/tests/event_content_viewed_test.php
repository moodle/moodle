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
 * Tests for base content viewed event.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__.'/fixtures/event_fixtures.php');

/**
 * Class core_event_page_viewed_testcase
 *
 * Tests for event \core\event\page_viewed
 */
class core_event_content_viewed_testcase extends advanced_testcase {

    /**
     * Set basic page properties.
     */
    public function setUp() {
        global $PAGE;
        // Set page details.
        $PAGE->set_url('/someurl.php');
        $PAGE->set_pagelayout('somelayout');
    }

    /**
     * Test event properties and methods.
     */
    public function test_event_attributes() {
        global $PAGE;

        $this->resetAfterTest();

        // Trigger the page view event.
        $sink = $this->redirectEvents();
        $pageevent = \core_tests\event\content_viewed::create(array('other' => array('content' => 'tests')));
        $pageevent->set_page_detail(); // Set page details.
        $legacydata = array(SITEID, 'site', 'view', 'view.php?id=' . SITEID, SITEID);
        $pageevent->set_legacy_logdata($legacydata); // Set legacy data.
        $pageevent->trigger();
        $result = $sink->get_events();
        $event = reset($result);

        // Test page details.
        $data = array( 'url'         => $PAGE->url->out_as_local_url(false),
                       'heading'     => $PAGE->heading,
                       'title'       => $PAGE->title,
                       'content'     => 'tests');
        $this->assertEquals($data, $event->other);

        // Test legacy stuff.
        $this->assertEventLegacyLogData($legacydata, $event);
        $pageevent = \core_tests\event\content_viewed::create(array('other' => array('content' => 'tests')));
        $pageevent->trigger();
        $result = $sink->get_events();
        $event = $result[1];
        $this->assertEventLegacyLogData(null, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test custom validations.
     */
    public function test_event_context_exception() {

        $this->resetAfterTest();

        // Make sure content identifier is always set.
        $this->setExpectedException('coding_exception');
        $pageevent = \core_tests\event\content_viewed::create();
        $pageevent->set_page_detail();
        $pageevent->trigger();
        $this->assertEventContextNotUsed($pageevent);
    }
}

