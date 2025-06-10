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
 * PHPUnit block content tests.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__). '/lib.php');

/**
 * PHPUnit block content test case.
 *
 * @package     block_mhaairs
 * @category    phpunit
 * @group       block_mhaairs
 * @group       block_mhaairs_content
 * @copyright   2014 Itamar Tzadok <itamar@substantialmethods.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_mhaairs_content_testcase extends block_mhaairs_testcase {

    /**
     * Tests the block content without integration configuration.
     *
     * @return void
     */
    public function test_content_no_configuration() {
        global $PAGE;

        $blockname = 'mhaairs';

        // Admin should see a warning message.
        $this->set_user('admin');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $content = $block->get_content();
        $this->assertEquals($block->get_warning_message('sitenotconfig'), $content->text);

        // Teacher should see a warning message.
        $this->set_user('teacher');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $content = $block->get_content();
        $this->assertEquals($block->get_warning_message('sitenotconfig'), $content->text);

        // Assistant should see a warning message.
        $this->set_user('assistant');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $content = $block->get_content();
        $this->assertEquals('', $content->text);

        // Student should see nothing.
        $this->set_user('student1');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $content = $block->get_content();
        $this->assertEquals('', $content->text);
    }

    /**
     * Tests the block content with integration configuration omitting services.
     *
     * @return void
     */
    public function test_content_no_services() {
        global $PAGE;

        $blockname = 'mhaairs';

        $config = array();
        $config['block_mhaairs_customer_number'] = 'Test Customer';
        $config['block_mhaairs_shared_secret'] = '1234';

        // Admin should see a warning message.
        $this->set_user('admin');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertEquals($block->get_warning_message('sitenotconfig'), $content->text);

        // Teacher should see a warning message.
        $this->set_user('teacher');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertEquals($block->get_warning_message('sitenotconfig'), $content->text);

        // Assistant should see a warning message.
        $this->set_user('assistant');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertEquals('', $content->text);

        // Student should see nothing.
        $this->set_user('student1');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertEquals('', $content->text);
    }

    /**
     * Tests the block content with services enabled site level but not
     * block level.
     *
     * @return void
     */
    public function test_content_with_site_services() {
        global $PAGE;

        $blockname = 'mhaairs';

        $config = array();
        $config['block_mhaairs_customer_number'] = 'Test Customer';
        $config['block_mhaairs_shared_secret'] = '1234';
        $config['block_mhaairs_display_services'] = 'Test Service';

        // Admin should see link to service.
        $this->set_user('admin');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertEquals($block->get_warning_message('blocknotconfig'), $content->text);

        // Teacher should see link to service.
        $this->set_user('teacher');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertEquals($block->get_warning_message('blocknotconfig'), $content->text);

        // Assistant should see link to service.
        $this->set_user('assistant');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertEquals('', $content->text);

        // Student should see link to service.
        $this->set_user('student1');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertEquals('', $content->text);
    }

    /**
     * Tests the block content with services enabled site level and
     * block level.
     *
     * @return void
     */
    public function test_content_with_block_services() {
        global $DB, $PAGE;

        $blockname = 'mhaairs';

        // Site config.
        $config = array();
        $config['block_mhaairs_customer_number'] = 'Test Customer';
        $config['block_mhaairs_shared_secret'] = '1234';
        $config['block_mhaairs_display_services'] = 'TestService';

        // Test service data.
        $testservicedata = array(
            'ServiceIconUrl' => null,
            'ServiceUrl' => null,
            'ServiceID' => 'TestService',
            'ServiceName' => 'Test Service',
        );
        $servicedata = array('Tools' => array($testservicedata));
        $config['service_data'] = $servicedata;

        // Service enabling in the block.
        $blockconfig = (object) array('TestService' => 1);
        $blockconfigdata = base64_encode(serialize($blockconfig));
        $this->bi->configdata = $blockconfigdata;

        // Admin should see link to service.
        $this->set_user('admin');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertContains('Test Service', $content->text);

        // Teacher should see link to service.
        $this->set_user('teacher');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertContains('Test Service', $content->text);

        // Assistant should see link to service.
        $this->set_user('assistant');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertContains('Test Service', $content->text);

        // Student should see link to service.
        $this->set_user('student1');
        $block = block_instance($blockname, $this->bi, $PAGE);
        $block->set_phpunit_test_config($config);
        $content = $block->get_content();
        $this->assertContains('Test Service', $content->text);
    }

}
