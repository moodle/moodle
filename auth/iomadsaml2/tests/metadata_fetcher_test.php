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
 * Testcase class for metadata_fetcher class.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use auth_iomadsaml2\metadata_fetcher;

/**
 * Testcase class for metadata_fetcher class.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_metadata_fetcher_testcase extends advanced_testcase {

    /** @var \Prophecy\Prophet */
    protected $prophet;

    /**
     * Set up
     */
    public function setUp(): void {
        if (class_exists('\\Prophecy\\Prophet')) {
            $this->prophet = new \Prophecy\Prophet();
        }
    }

    /**
     * Tear down after every test.
     */
    protected function tearDown(): void {
        $this->prophet = null; // Required for Totara 12+ support (see issue #578).
    }

    public function test_fetch_metadata_404() {
        $url = $this->getExternalTestFileUrl('/test404.xml');
        $fetcher = new metadata_fetcher();

        try {
            $fetcher->fetch($url);
            // Fail if the exception is not thrown.
            $this->fail();
        } catch (\moodle_exception $e) {
            $this->assertEquals(404, (int) $fetcher->get_curlinfo()['http_code']);
        }
    }

    public function test_fetch_metadata_success() {
        $url = $this->getExternalTestFileUrl('/test.html');
        $fetcher = new metadata_fetcher();

        $result = $fetcher->fetch($url);
        $this->assertNotEmpty($result);
        $this->assertEquals(0, (int) $fetcher->get_curlerrorno());
        $this->assertEquals(200, (int) $fetcher->get_curlinfo()['http_code']);
    }

    public function test_fetch_metadata_curlerrorno() {
        if (!isset($this->prophet)) {
            $this->markTestSkipped('Skipping due to Prophecy library not available');
        }

        $url = 'http://fakeurl.localhost';
        $curl = $this->prophet->prophesize('curl');

        $fetcher = new metadata_fetcher();
        $curl->get($url, Prophecy\Argument::type('array'))->willReturn('some bad stuff');
        $curl->get_errno()->willReturn(CURLE_READ_ERROR);
        $curl->get_info()->willReturn(['http_status' => 503]);

        try {
            $fetcher->fetch($url, $curl->reveal());
            // Fail if the exception is not thrown.
            $this->fail();
        } catch (\moodle_exception $e) {
            $this->assertEquals(CURLE_READ_ERROR, (int) $fetcher->get_curlerrorno());
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString('Metadata fetch failed: some bad stuff', $e->getMessage());
            } else {
                // Maintains Support for Moodle 3.5 - remove when this branch does not support Moodle 3.5 anymore.
                $this->assertContains('Metadata fetch failed: some bad stuff', $e->getMessage());
            }
            $this->assertEquals('some bad stuff', $fetcher->get_curlerror());
        }
    }

    public function test_fetch_metadata_nohttpstatus() {
        if (!isset($this->prophet)) {
            $this->markTestSkipped('Skipping due to Prophecy library not available');
        }

        $url = 'http://fakeurl.localhost';
        $curl = $this->prophet->prophesize('curl');

        $fetcher = new metadata_fetcher();
        $curl->get($url, Prophecy\Argument::type('array'))->willReturn('');
        $curl->get_info()->willReturn([]);
        $curl->get_errno()->willReturn(0);

        try {
            $fetcher->fetch($url, $curl->reveal());
            // Fail if the exception is not thrown.
            $this->fail();
        } catch (\moodle_exception $e) {
            if (method_exists($this, 'assertStringContainsString')) {
                $this->assertStringContainsString('Metadata fetch failed: Unknown cURL error', $e->getMessage());
            } else {
                // Maintains Support for Moodle 3.5 - remove when this branch does not support Moodle 3.5 anymore.
                $this->assertContains('Metadata fetch failed: Unknown cURL error', $e->getMessage());
            }
        }
    }

    public function test_fetch_metadata_override_ssl_options() {
        global $CFG;

        if (!isset($this->prophet)) {
            $this->markTestSkipped('Skipping due to Prophecy library not available');
        }

        $this->resetAfterTest(true);

        $options = [
            'CURLOPT_SSL_VERIFYPEER' => false,
            'CURLOPT_SSL_VERIFYHOST' => false,
            'CURLOPT_CONNECTTIMEOUT' => 20,
            'CURLOPT_FOLLOWLOCATION' => 1,
            'CURLOPT_MAXREDIRS'      => 5,
            'CURLOPT_TIMEOUT'        => 300,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_NOBODY'         => false,
        ];
        $url = 'https://fakeurl.localhost';
        if (!is_array($CFG->forced_plugin_settings)) {
            $CFG->forced_plugin_settings = [];
        }
        if (!array_key_exists('auth_iomadsaml2', $CFG->forced_plugin_settings)) {
            $CFG->forced_plugin_settings['auth_iomadsaml2'] = [];
        }
        $CFG->forced_plugin_settings['auth_iomadsaml2']['CURLOPT_SSL_VERIFYPEER'] = 0;
        $CFG->forced_plugin_settings['auth_iomadsaml2']['CURLOPT_SSL_VERIFYHOST'] = 0;

        $curl = $this->prophet->prophesize('curl');

        $fetcher = new metadata_fetcher();

        // Prophecy asserts that indeed the options passed to curl->get() from fetch() contained forced settings.
        $curl->get($url, $options)->willReturn('Some error');
        $curl->get_info()->willReturn(['http_code' => 200]);
        $curl->get_errno()->willReturn(0);

        $fetcher->fetch($url, $curl->reveal());
    }
}
