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
 * @coversDefaultClass \core_contentbank\contenttype
 *
 */
class core_contenttype_contenttype_testcase extends \advanced_testcase {

    /**
     * Tests get_contenttype_name result.
     *
     * @covers ::get_contenttype_name
     */
    public function test_get_contenttype_name() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);

        $this->assertEquals('contenttype_testable', $testable->get_contenttype_name());
    }

    /**
     * Tests get_plugin_name result.
     *
     * @covers ::get_plugin_name
     */
    public function test_get_plugin_name() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);

        $this->assertEquals('testable', $testable->get_plugin_name());
    }

    /**
     * Tests get_icon result.
     *
     * @covers ::get_icon
     */
    public function test_get_icon() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);
        $icon = $testable->get_icon('new content');
        $this->assertContains('archive', $icon);
    }

    /**
     * Tests is_feature_supported behavior .
     *
     * @covers ::is_feature_supported
     */
    public function test_is_feature_supported() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);

        $this->assertTrue($testable->is_feature_supported(contenttype::CAN_TEST));
        $this->assertFalse($testable->is_feature_supported(contenttype::CAN_UPLOAD));
    }

    /**
     * Tests can_upload behavior with no implemented upload feature.
     *
     * @covers ::can_upload
     */
    public function test_no_upload_feature_supported() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $testable = new contenttype($systemcontext);

        $this->setAdminUser();
        $this->assertFalse($testable->is_feature_supported(contenttype::CAN_UPLOAD));
        $this->assertFalse($testable->can_upload());
    }

    /**
     * Test create_content() with empty data.
     *
     * @covers ::create_content
     */
    public function test_create_empty_content() {
        $this->resetAfterTest();

        // Create empty content.
        $record = new stdClass();

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);

        $this->assertEquals('contenttype_testable', $content->get_content_type());
        $this->assertInstanceOf('\\contenttype_testable\\content', $content);
    }

    /**
     * Tests for behaviour of create_content() with data.
     *
     * @covers ::create_content
     */
    public function test_create_content() {
        $this->resetAfterTest();

        // Create content.
        $record = new stdClass();
        $record->name = 'Test content';
        $record->configdata = '';
        $record->contenttype = '';

        $contenttype = new contenttype(context_system::instance());
        $content = $contenttype->create_content($record);

        $this->assertEquals('contenttype_testable', $content->get_content_type());
        $this->assertInstanceOf('\\contenttype_testable\\content', $content);
    }
}
