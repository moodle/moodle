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
 * Test for content bank contenttype class.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_contenttype.php');
require_once($CFG->dirroot . '/contentbank/tests/fixtures/testable_content.php');

use stdClass;
use context_system;
use contenttype_testable\contenttype as contenttype;
/**
 * Test for content bank contenttype class.
 *
 * @package    core_contentbank
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_contentbank\content
 *
 */
class core_contenttype_content_testcase extends \advanced_testcase {

    /**
     * Tests for behaviour of get_name().
     *
     * @covers ::get_name
     */
    public function test_get_name() {
        $this->resetAfterTest();

        // Create content.
        $record = new stdClass();
        $record->name = 'Test content';
        $record->configdata = '';

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);
        $this->assertEquals($record->name, $content->get_name());
    }

    /**
     * Tests for behaviour of get_content_type().
     *
     * @covers ::get_content_type
     */
    public function test_get_content_type() {
        $this->resetAfterTest();

        // Create content.
        $record = new stdClass();
        $record->name = 'Test content';
        $record->configdata = '';

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);
        $this->assertEquals('contenttype_testable', $content->get_content_type());
    }

    /**
     * Tests for 'configdata' behaviour.
     *
     * @covers ::set_configdata
     */
    public function test_configdata_changes() {
        $this->resetAfterTest();

        $configdata = "{img: 'icon.svg'}";

        // Create content.
        $record = new stdClass();
        $record->configdata = $configdata;

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);
        $this->assertEquals($configdata, $content->get_configdata());

        $configdata = "{alt: 'Name'}";
        $content->set_configdata($configdata);
        $this->assertEquals($configdata, $content->get_configdata());
    }
}
