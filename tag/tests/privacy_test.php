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
 * Privacy tests for core_tag.
 *
 * @package    core_comment
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/tag/lib.php');

use \core_privacy\tests\provider_testcase;
use \core_privacy\local\request\writer;
use \core_tag\privacy\provider;

/**
 * Unit tests for tag/classes/privacy/policy
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_tag_privacy_testcase extends provider_testcase {

    /**
     * Check the exporting of tags for a user id in a context.
     */
    public function test_export_tags() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a user to perform tagging.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $subcontext = [];

        // Create three dummy tags and tag instances.
        $dummytags = [ 'Tag 1', 'Tag 2', 'Tag 3' ];
        core_tag_tag::set_item_tags('core_course', 'course', $course->id, context_course::instance($course->id),
                                    $dummytags, $user->id);

        // Get the tag instances that should have been created.
        $taginstances = $DB->get_records('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id));
        $this->assertCount(count($dummytags), $taginstances);

        // Check tag instances match the component and context.
        foreach ($taginstances as $taginstance) {
            $this->assertEquals('core_course', $taginstance->component);
            $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);
        }

        // Retrieve tags only for this user.
        provider::export_item_tags($user->id, $context, $subcontext, 'core_course', 'course', $course->id, true);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());

        $exportedtags = $writer->get_related_data($subcontext, 'tags');
        $this->assertCount(count($dummytags), $exportedtags);

        // Check the exported tag's rawname is found in the initial dummy tags.
        foreach ($exportedtags as $exportedtag) {
            $this->assertContains($exportedtag->rawname, $dummytags);
        }
    }
}
