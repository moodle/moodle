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

declare(strict_types=1);

namespace core_badges;

/**
 * Unit tests for backpack_api class.
 *
 * @package     core_badges
 * @copyright   2025 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(backpack_api::class)]
final class backpack_api_test extends \advanced_testcase {
    /**
     * Test get_providers function.
     */
    public function test_get_providers(): void {
        global $CFG;

        $providers = backpack_api::get_providers();
        $this->assertCount(2, $providers);
        $this->assertArrayHasKey(backpack_api::PROVIDER_CANVAS_CREDENTIALS, $providers);
        $this->assertArrayHasKey(backpack_api::PROVIDER_OTHER, $providers);
    }

    /**
     * Test get_regions function.
     */
    public function test_get_regions(): void {
        global $CFG;

        $this->resetAfterTest();

        // Default: 5 regions (Canvas Credentials).
        $regions = backpack_api::get_regions();
        $this->assertCount(5, $regions);

        // No regions.
        $CFG->badges_canvasregions = '';
        $regions = backpack_api::get_regions();
        $this->assertEmpty($regions);

        // One region.
        $CFG->badges_canvasregions = 'Australia|https://au.badgr.io|https://api.au.badgr.io/v2';
        $regions = backpack_api::get_regions();
        $this->assertCount(1, $regions);
        $this->assertEquals('Australia', $regions[0]['name']);
        $this->assertEquals('https://au.badgr.io', $regions[0]['url']);
        $this->assertEquals('https://api.au.badgr.io/v2', $regions[0]['apiurl']);

        // Two regions + empty lines + invalid line.
        $CFG->badges_canvasregions = "\nUnited States|https://badgr.io|https://api.badgr.io/v2\ninvalidline\n" .
                                     'Europe|https://eu.badgr.io|https://api.eu.badgr.io/v2' . "\n";
        $regions = backpack_api::get_regions();
        $this->assertCount(2, $regions);
        $expected = [
            [
                'name' => 'United States',
                'url' => 'https://badgr.io',
                'apiurl' => 'https://api.badgr.io/v2',
            ],
            [
                'name' => 'Europe',
                'url' => 'https://eu.badgr.io',
                'apiurl' => 'https://api.eu.badgr.io/v2',
            ],
        ];
        $this->assertEquals($expected, $regions);
    }

    /**
     * Test display_canvas_credentials_fields function.
     */
    public function test_display_canvas_credentials_fields(): void {
        global $CFG;

        $this->resetAfterTest();

        // By default, the fields should be displayed (5 regions).
        $this->assertTrue(backpack_api::display_canvas_credentials_fields());

        // No regions configured, fields should not be displayed.
        $CFG->badges_canvasregions = '';
        $this->assertFalse(backpack_api::display_canvas_credentials_fields());

        // One region configured, fields should be displayed.
        $CFG->badges_canvasregions = 'Australia|https://au.badgr.io|https://api.au.badgr.io/v2';
        $this->assertTrue(backpack_api::display_canvas_credentials_fields());
    }

    /**
     * Test get_region_url and get_region_api_url functions.
     */
    public function test_get_region_urls(): void {
        global $CFG;

        $this->resetAfterTest();

        // Default: 5 regions (Canvas Credentials).
        $regions = backpack_api::get_regions();
        $this->assertCount(5, $regions);
        $this->assertEquals('https://au.badgr.io', backpack_api::get_region_url(0));
        $this->assertEquals('https://ca.badgr.io', backpack_api::get_region_url(1));
        $this->assertEquals('https://eu.badgr.io', backpack_api::get_region_url(2));
        $this->assertEquals('https://sg.badgr.io', backpack_api::get_region_url(3));
        $this->assertEquals('https://badgr.io', backpack_api::get_region_url(4));
        $this->assertEquals('https://api.au.badgr.io/v2', backpack_api::get_region_api_url(0));
        $this->assertEquals('https://api.ca.badgr.io/v2', backpack_api::get_region_api_url(1));
        $this->assertEquals('https://api.eu.badgr.io/v2', backpack_api::get_region_api_url(2));
        $this->assertEquals('https://api.sg.badgr.io/v2', backpack_api::get_region_api_url(3));
        $this->assertEquals('https://api.badgr.io/v2', backpack_api::get_region_api_url(4));

        // Wrong index.
        $this->assertNull(backpack_api::get_region_url(10));
        $this->assertNull(backpack_api::get_region_api_url(10));

        // No regions.
        $CFG->badges_canvasregions = '';
        $this->assertNull(backpack_api::get_region_url(0));
        $this->assertNull(backpack_api::get_region_api_url(0));
    }

    /**
     * Test get_regionid_from_url function.
     */
    public function test_get_regionid_from_url(): void {
        global $CFG;

        $this->resetAfterTest();

        // Default: 5 regions (Canvas Credentials).
        $regions = backpack_api::get_regions();
        $this->assertCount(5, $regions);
        $this->assertEquals(0, backpack_api::get_regionid_from_url('https://au.badgr.io'));
        $this->assertEquals(1, backpack_api::get_regionid_from_url('https://ca.badgr.io'));
        $this->assertEquals(2, backpack_api::get_regionid_from_url('https://eu.badgr.io'));
        $this->assertEquals(3, backpack_api::get_regionid_from_url('https://sg.badgr.io'));
        $this->assertEquals(4, backpack_api::get_regionid_from_url('https://badgr.io'));
        // Test with trailing slash.
        $this->assertEquals(0, backpack_api::get_regionid_from_url('https://au.badgr.io/'));

        // Wrong URL.
        $this->assertEquals(4, backpack_api::get_regionid_from_url('https://unknown.badgr.io'));

        // One region.
        $CFG->badges_canvasregions = 'Australia|https://au.badgr.io|https://api.au.badgr.io/v2';
        $regions = backpack_api::get_regions();
        $this->assertEquals(0, backpack_api::get_regionid_from_url('https://au.badgr.io'));

        // No regions.
        $CFG->badges_canvasregions = '';
        $this->assertEquals(backpack_api::REGION_EMPTY, backpack_api::get_regionid_from_url('https://au.badgr.io'));
    }

    /**
     * Test is_canvas_credentials_region function.
     */
    public function test_is_canvas_credentials_region(): void {
        global $CFG;

        $this->resetAfterTest();

        // Default: 5 regions (Canvas Credentials).
        $regions = backpack_api::get_regions();
        $this->assertCount(5, $regions);
        $this->assertTrue(backpack_api::is_canvas_credentials_region('https://au.badgr.io'));
        $this->assertTrue(backpack_api::is_canvas_credentials_region('https://ca.badgr.io'));
        $this->assertTrue(backpack_api::is_canvas_credentials_region('https://eu.badgr.io'));
        $this->assertTrue(backpack_api::is_canvas_credentials_region('https://sg.badgr.io'));
        $this->assertTrue(backpack_api::is_canvas_credentials_region('https://badgr.io'));

        // Non Canvas URL.
        $this->assertFalse(backpack_api::is_canvas_credentials_region('https://unknown.badgr.io'));

        // No regions.
        $CFG->badges_canvasregions = '';
        $this->assertFalse(backpack_api::is_canvas_credentials_region('https://au.badgr.io'));
    }
}
