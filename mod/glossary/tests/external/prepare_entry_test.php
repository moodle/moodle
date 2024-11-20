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

namespace mod_glossary\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use core_external\external_api;
use externallib_advanced_testcase;

/**
 * External function test for prepare_entry.
 *
 * @package    mod_glossary
 * @category   external
 * @covers     \mod_glossary\external\prepare_entry
 * @since      Moodle 3.10
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class prepare_entry_test extends externallib_advanced_testcase {

    /**
     * test_prepare_entry
     */
    public function test_prepare_entry(): void {
        global $USER;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', ['course' => $course->id]);
        $gg = $this->getDataGenerator()->get_plugin_generator('mod_glossary');

        $this->setAdminUser();
        $aliases = ['alias1', 'alias2'];
        $entry = $gg->create_content(
            $glossary,
            ['approved' => 1, 'userid' => $USER->id],
            $aliases
        );

        $cat1 = $gg->create_category($glossary, [], [$entry]);
        $gg->create_category($glossary);

        $return = prepare_entry::execute($entry->id);
        $return = external_api::clean_returnvalue(prepare_entry::execute_returns(), $return);

        $this->assertNotEmpty($return['inlineattachmentsid']);
        $this->assertNotEmpty($return['attachmentsid']);
        $this->assertEquals($aliases, $return['aliases']);
        $this->assertEquals([$cat1->id], $return['categories']);
        $this->assertCount(2, $return['areas']);
        $this->assertNotEmpty($return['areas'][0]['options']);
        $this->assertNotEmpty($return['areas'][1]['options']);
    }
}
