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
 * Unit tests for the filter_oembed.
 *
 * @package    filter_oembed
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 The POET Group
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/oembed/tests/testable_oembed.php');

/**
 * @group filter_oembed
 */
class filter_oembed_service_testcase extends advanced_testcase {

    /**
     * Make sure providers array is correct.
     * @param array $providers
     */
    public function assert_providers_ok($providers) {
        $this->assertNotEmpty($providers);
        $provider = reset($providers);
        if (is_object($provider)) {
            // Test the provider object.
            $this->assertNotEmpty($provider->providername);
            $this->assertNotEmpty($provider->providerurl);
            $this->assertNotEmpty($provider->endpoints);
        } else if (is_array($provider)) {
            // Test the provider decoded JSON array.
            $this->assertArrayHasKey('provider_name', $provider);
            $this->assertArrayHasKey('provider_url', $provider);
            $this->assertArrayHasKey('endpoints', $provider);
        } else {
            // Test failed.
            $this->assertTrue(false);
        }
    }

    /**
     * Test instance.
     */
    public function test_instance() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $oembed = testable_oembed::get_instance();
        $this->assertNotEmpty($oembed);
    }

    public function test_set_providers() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $oembed = testable_oembed::get_instance();
        $oembed->empty_providers();
        $oembed->protected_set_providers('all');
        $this->assertNotEmpty($oembed->providers);
        $this->assert_providers_ok($oembed->providers);
    }

    /**
     * Test providers.
     */
    public function test_providers() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $oembed = testable_oembed::get_instance();
        $providers = $oembed->providers;
        $this->assert_providers_ok($providers);
    }

    /**
     * Test html.
     * TODO - have a local oembed service with test fixtures for performing test.
     */
    public function test_embed_html() {
        $this->resetAfterTest(true);
        set_config('lazyload', 0, 'filter_oembed');
        $this->setAdminUser();
        $oembed = testable_oembed::get_instance();
        $text = $oembed->html_output('https://www.youtube.com/watch?v=ns6gCZI-Nj8');
        $expectedtext = '<div class="oembed-content oembed-responsive"><iframe width="480" height="270"' .
            ' src="https://www.youtube.com/embed/ns6gCZI-Nj8?feature=oembed&v=ns6gCZI-Nj8"';
        $this->assertContains($expectedtext, $text);
    }

    /**
     * Test lazy load html.
     * TODO - have a local oembed service with test fixtures for performing test.
     */
    public function test_preloader_html() {
        $this->resetAfterTest(true);
        set_config('lazyload', 1, 'filter_oembed');
        $this->setAdminUser();
        $oembed = testable_oembed::get_instance();
        $text = $oembed->html_output('https://www.youtube.com/watch?v=ns6gCZI-Nj8');
        $this->assertContains('<div class="oembed-card-container oembed-responsive">', $text);
        $this->assertRegExp('/<div class="oembed-card oembed-processed" style="(?:.*)" data-embed="(?:.*)"(?:.*)' .
            'data-aspect-ratio = "(?:.*)"(?:.*)>/is', $text);
        $this->assertRegExp('/<div class="oembed-card-title">(?:.*)<\/div>/', $text);
        $this->assertContains('<button class="btn btn-link oembed-card-play" aria-label="Play"></button>', $text);

    }

    /**
     * Test download providers.
     */
    public function test_download_providers() {
        $this->resetAfterTest(true);
        $providers = testable_oembed::protected_download_providers();
        $this->assert_providers_ok($providers);
    }

    /**
     * Test get local providers.
     */
    public function test_get_local_providers() {
        $this->resetAfterTest(true);
        $providers = testable_oembed::protected_get_local_providers();
        $this->assert_providers_ok($providers);
    }

    /**
     * Test get plugin providers.
     */
    public function test_get_plugin_providers() {
        $this->resetAfterTest(true);
        $providers = testable_oembed::protected_get_plugin_providers();
        $this->assert_providers_ok($providers);
    }

    /**
     * Test match_provider_names.
     */
    public function test_match_provider_names() {
        $this->resetAfterTest(true);
        $providerdata = [
            (object)['id' => 1, 'providername' => 'Alpha1', 'providerurl' => 'http://www.one.com',
                     'endpoints' => '', 'source' => '', 'enabled' => 1, 'timecreated' => 0, 'timemodified' => 0],
            (object)['id' => 2, 'providername' => 'Alpha1', 'providerurl' => 'http://www.another.com',
                     'endpoints' => '', 'source' => '', 'enabled' => 1, 'timecreated' => 0, 'timemodified' => 0],
            (object)['id' => 3, 'providername' => 'Beta1', 'providerurl' => 'http://www.two.com',
                     'endpoints' => '', 'source' => '', 'enabled' => 1, 'timecreated' => 0, 'timemodified' => 0],
        ];

        // Test that more than one of the same name, returns matching URL.
        $provider = ['provider_name' => 'Alpha1', 'provider_url' => 'http://www.another.com',
                     'endpoints' => [['schemes' => [''], 'url' => '', 'formats' => ['']]]];
        $matched = testable_oembed::protected_match_provider_names($providerdata, $provider);
        $this->assertTrue(is_object($matched));
        $this->assertEquals(2, $matched->id);
        $this->assertEquals($provider['provider_name'], $matched->providername);

        // Test that only one of the name, returns the one regardless of URL.
        $provider = ['provider_name' => 'Beta1', 'provider_url' => 'http://notthesameurl.com',
                     'endpoints' => [['schemes' => [''], 'url' => '', 'formats' => ['']]]];
        $matched = testable_oembed::protected_match_provider_names($providerdata, $provider);
        $this->assertTrue(is_object($matched));
        $this->assertEquals(3, $matched->id);
        $this->assertEquals($provider['provider_name'], $matched->providername);

        // Test that more than one of the same name, and no matching URL, returns false.
        $provider = ['provider_name' => 'Alpha1', 'provider_url' => 'http://www.anewone.com',
                     'endpoints' => [['schemes' => [''], 'url' => '', 'formats' => ['']]]];
        $matched = testable_oembed::protected_match_provider_names($providerdata, $provider);
        $this->assertFalse($matched);

        // Test that no matching name returns false.
        $provider = ['provider_name' => 'Delta1', 'provider_url' => 'http://www.delta.com',
                     'endpoints' => [['schemes' => [''], 'url' => '', 'formats' => ['']]]];
        $matched = testable_oembed::protected_match_provider_names($providerdata, $provider);
        $this->assertFalse($matched);
    }

    /**
     * Test the "__get" magic method.
     */
    public function test_get() {
        $this->resetAfterTest(true);
        $oembed = testable_oembed::get_instance();

        try {
            $providers = $oembed->providers;
            $this->assert_providers_ok($providers);
        } catch (Exception $e) {
            $this->assertTrue(false);
        }

        try {
            $warnings = $oembed->warnings;
            $this->assertTrue(is_array($warnings));
        } catch (Exception $e) {
            $this->assertTrue(false);
        }

        try {
            $noaccess = $oembed->noaccess;
            $this->assertTrue(false);
        } catch (coding_exception $e) {
            $expectedmessage = 'Coding error detected, it must be fixed by a programmer: ' .
                               'noaccess is not a publicly accessible property of testable_oembed';
            $this->assertEquals($expectedmessage, $e->getMessage());
        }
    }

    /**
     * Test enable and disable provider functions.
     * Tests: enable_provider, disable_provider, set_provider_enable_value.
     */
    public function test_enable_disable_provider() {
        $this->resetAfterTest(true);
        $oembed = testable_oembed::get_instance();

        // Test by object.
        $providers = $oembed->providers;
        $provider = reset($providers);
        $oembed->disable_provider($provider);
        $this->assertFalse($oembed->providers[$provider->id]->enabled);

        // Test by id.
        $oembed->enable_provider($provider->id);
        $this->assertTrue($oembed->providers[$provider->id]->enabled);
    }
}
