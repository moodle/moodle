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
 * Tests for tool_licensemanager manager class.
 *
 * @package    tool_licensemanager
 * @copyright  2020 Tom Dickman <tom.dickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/licenselib.php');

/**
 * Tests for tool_licensemanager manager class.
 *
 * @package    tool_licensemanager
 * @copyright  2020 Tom Dickman <tom.dickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group      tool_licensemanager
 */
class manager_test extends advanced_testcase {

    /**
     * Test editing a license.
     */
    public function test_edit_existing_license() {
        $this->resetAfterTest();

        // Create initial custom license to edit.
        $testlicense = new stdClass();
        $testlicense->shortname = 'my-lic';
        $testlicense->fullname = 'My License';
        $testlicense->source = 'https://fakeurl.net';
        $testlicense->version = date('Ymd', time()) . '00';
        $testlicense->custom = license_manager::CUSTOM_LICENSE;

        license_manager::save($testlicense);
        license_manager::enable($testlicense->shortname);

        $manager = new \tool_licensemanager\manager();

        // Attempt to submit form data with altered details.
        $formdata = [
            'shortname' => 'new-value',
            'fullname' => 'New License Name',
            'source' => 'https://updatedfakeurl.net',
            'version' => time()
        ];

        // Attempt to submit form data with an altered shortname.
        \tool_licensemanager\form\edit_license::mock_submit($formdata);

        // We're testing a private method, so we need to setup reflector magic.
        $method = new ReflectionMethod('\tool_licensemanager\manager', 'edit');
        $method->invoke($manager, \tool_licensemanager\manager::ACTION_UPDATE, $testlicense->shortname);

        // Should not create a new license when updating an existing license.
        $this->assertEmpty(license_manager::get_license_by_shortname($formdata['shortname']));

        $actual = license_manager::get_license_by_shortname('my-lic');
        // Should not be able to update the shortname of the license.
        $this->assertNotSame($formdata['shortname'], $actual->shortname);
        // Should be able to update other details of the license.
        $this->assertSame($formdata['fullname'], $actual->fullname);
        $this->assertSame($formdata['source'], $actual->source);
        $this->assertSame(date('Ymd', $formdata['version']) . '00', $actual->version);
    }

    public function test_edit_license_not_exists() {
        $manager = new \tool_licensemanager\manager();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new ReflectionMethod('\tool_licensemanager\manager', 'edit');

        // Attempt to update a license that doesn't exist.
        $formdata = [
            'shortname' => 'new-value',
            'fullname' => 'New License Name',
            'source' => 'https://updatedfakeurl.net',
            'version' => time()
        ];
        \tool_licensemanager\form\edit_license::mock_submit($formdata);

        // Should not be able to update a license with a shortname that doesn't exist.
        $this->expectException('moodle_exception');
        $method->invoke($manager, \tool_licensemanager\manager::ACTION_UPDATE, $formdata['shortname']);
    }

    public function test_edit_license_no_shortname() {
        $manager = new \tool_licensemanager\manager();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new ReflectionMethod('\tool_licensemanager\manager', 'edit');

        // Attempt to update a license without passing license shortname.
        $formdata = [
            'fullname' => 'New License Name',
            'source' => 'https://updatedfakeurl.net',
            'version' => time()
        ];
        \tool_licensemanager\form\edit_license::mock_submit($formdata);

        // Should not be able to update empty license shortname.
        $this->expectException('moodle_exception');
        $method->invoke($manager, \tool_licensemanager\manager::ACTION_UPDATE, '');
    }

    /**
     * Test creating a new license.
     */
    public function test_edit_create_license() {
        $this->resetAfterTest();

        $licensecount = count(license_manager::get_licenses());

        $manager = new \tool_licensemanager\manager();

        $formdata = [
            'shortname' => 'new-value',
            'fullname' => 'My License',
            'source' => 'https://fakeurl.net',
            'version' => time()
        ];

        // Attempt to submit form data for a new license.
        \tool_licensemanager\form\edit_license::mock_submit($formdata);

        // We're testing a private method, so we need to setup reflector magic.
        $method = new ReflectionMethod('\tool_licensemanager\manager', 'edit');
        $method->invoke($manager, \tool_licensemanager\manager::ACTION_CREATE, $formdata['shortname']);

        // Should create a new license in database.
        $this->assertCount($licensecount + 1, license_manager::get_licenses());
        $actual = license_manager::get_license_by_shortname($formdata['shortname']);
        $this->assertSame($formdata['shortname'], $actual->shortname);
        $this->assertSame($formdata['fullname'], $actual->fullname);
        $this->assertSame($formdata['source'], $actual->source);
        $this->assertSame(date('Ymd', $formdata['version']) . '00', $actual->version);

        // Attempt to submit form data for a duplicate license.
        \tool_licensemanager\form\edit_license::mock_submit($formdata);

        // Should not be able to create duplicate licenses.
        $this->expectException('moodle_exception');
        $method->invoke($manager, \tool_licensemanager\manager::ACTION_CREATE, $formdata['shortname']);
    }

    /**
     * Test changing the order of licenses.
     */
    public function test_change_license_order() {
        $this->resetAfterTest();

        $licenseorder = array_keys(license_manager::get_licenses());
        $initialposition = array_search('cc-nc-4.0', $licenseorder);

        $manager = new tool_licensemanager\manager();

        // We're testing a private method, so we need to setup reflector magic.
        $method = new ReflectionMethod('\tool_licensemanager\manager', 'change_license_order');
        $method->invoke($manager, \tool_licensemanager\manager::ACTION_MOVE_UP, 'cc-nc-4.0');

        $licenseorder = array_keys(license_manager::get_licenses());
        $newposition = array_search('cc-nc-4.0', $licenseorder);

        $this->assertLessThan($initialposition, $newposition);

        $initialposition = array_search('allrightsreserved', $licenseorder);
        $method->invoke($manager, \tool_licensemanager\manager::ACTION_MOVE_DOWN, 'allrightsreserved');
        $licenseorder = array_keys(license_manager::get_licenses());
        $newposition = array_search('cc-nc-4.0', $licenseorder);

        $this->assertGreaterThan($initialposition, $newposition);
    }

}
