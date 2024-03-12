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

namespace core\external\check;

defined('MOODLE_INTERNAL') || die();

use admin_category;
use admin_root;
use admin_setting_check;
use admin_settingpage;
use core\check\result;
use externallib_advanced_testcase;
use required_capability_exception;
use context_system;
use core\check\access\guestrole;
use core\check\check;
use core\check\external\get_result_admintree;
use core\check\security\passwordpolicy;
use ReflectionMethod;

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->libdir . '/adminlib.php');

/**
 * Unit tests check API get_result webservice
 *
 * @package     core
 * @covers      \core\check\external\get_result_admintree
 * @author      Matthew Hilton <matthewhilton@catalyst-au.net>
 * @copyright   Catalyst IT, 2023
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_result_admintree_test extends externallib_advanced_testcase {

    /**
     * Sets up admin tree for the given settings.
     *
     * @param array $settings array of admin_settings. Each will be placed into a single category, but each on their own page.
     * @return admin_root admin root that was created.
     */
    private function setup_admin_tree(array $settings) {
        $root = new admin_root(true);

        $category = new admin_category('testcategory', 'testcategory');
        $root->add('root', $category);

        foreach ($settings as $i => $setting) {
            $page = new admin_settingpage('testpage_' . $i, 'testpage');
            $page->add($setting);
            $root->add('testcategory', $page);
        }

        return $root;
    }

    /**
     * Provides values to execute_test
     *
     * @return array
     */
    public static function execute_options_provider(): array {
        return [
            'get check result (ok, no details)' => [
                'triggererror' => false,
                'check' => new passwordpolicy(),
                'includedetails' => false,
                'expectedreturn' => [
                    'status' => result::OK,
                    'summary' => get_string('check_passwordpolicy_ok', 'report_security'),

                    // Note for details and html, null = not returned and anything else will simply check something was returned.
                    'details' => null,
                    'html' => '',
                ],
            ],
            'get check result (ok, with details)' => [
                'triggererror' => false,
                'check' => new passwordpolicy(),
                'includedetails' => true,
                'expectedreturn' => [
                    'status' => result::OK,
                    'summary' => get_string('check_passwordpolicy_ok', 'report_security'),

                    // Note for details and html, null = not returned and anything else will simply check something was returned.
                    'details' => '',
                    'html' => '',
                ],
            ],
            'get check result (error, no details)' => [
                'triggererror' => true,
                'check' => new passwordpolicy(),
                'includedetails' => false,
                'expectedreturn' => [
                    'status' => result::WARNING,
                    'summary' => get_string('check_passwordpolicy_error', 'report_security'),

                    // Note for details and html, null = not returned and anything else will simply check something was returned.
                    'details' => null,
                    'html' => '',
                ],
            ],
            'get check result (error, with details)' => [
                'triggererror' => true,
                'check' => new passwordpolicy(),
                'includedetails' => true,
                'expectedreturn' => [
                    'status' => result::WARNING,
                    'summary' => get_string('check_passwordpolicy_error', 'report_security'),

                    // Note for details and html, null = not returned and anything else will simply check something was returned.
                    'details' => '',
                    'html' => '',
                ],
            ],
        ];
    }

    /**
     * Tests the execute function
     *
     * @param bool $triggererror If the test should setup the conditions so that the check will fail
     * @param check $check Check to use
     * @param bool $includedetails if details are included
     * @param array $expectedreturn an array of key value pairs. For each key, if the value is null it expects the
     * webservice to not return it. If it has a value, it checks that that value was inside what was returned from the webservice.
     * @dataProvider execute_options_provider
     */
    public function test_execute_options(bool $triggererror, check $check, bool $includedetails, array $expectedreturn): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // This makes the check we test (password policy) warn or be OK depending on the test.
        $CFG->passwordpolicy = $triggererror ? false : true;

        // Add the admin setting.
        $checksetting = new admin_setting_check('core/testcheck', $check);
        $root = $this->setup_admin_tree([$checksetting]);

        // Execute the ws function.
        $this->setAdminUser();
        $wsresult = (object) get_result_admintree::execute($checksetting->get_id(), $checksetting->name, $includedetails, $root);

        foreach ($expectedreturn as $key => $expectedvalue) {
            // If the expected result is null, ensure the return value was also null.
            if (is_null($expectedvalue)) {
                $this->assertTrue(empty($wsresult->$key));
            }

            // If the expected result is set, ensure it is contained in the return value.
            if (!is_null($expectedvalue)) {
                $this->assertTrue(!empty($wsresult->$key));
                $this->assertStringContainsString($expectedvalue, $wsresult->$key);
            }
        }
    }

    /**
     * Provides values to test_find_check_from_settings_tree
     *
     * @return array
     */
    public static function find_check_from_setting_tree_provider(): array {
        $testsetting1 = new admin_setting_check('testsetting', new passwordpolicy());

        return [
            'setting is in tree, correctly linked' => [
                'settings' => [
                    $testsetting1,
                    new admin_setting_check('testsetting2', new guestrole()),
                    new admin_setting_check('testsetting3', new guestrole()),
                ],
                'searchname' => $testsetting1->name,
                'searchid' => $testsetting1->get_id(),
                'expectedcheck' => passwordpolicy::class,
            ],
            'setting is not in tree' => [
                'settings' => [],
                'searchname' => $testsetting1->name,
                'searchid' => $testsetting1->get_id(),
                'expectedcheck' => '',
            ],
            'setting in tree, but name has conflict' => [
                'settings' => [
                    $testsetting1,
                    new admin_setting_check('testsetting', new guestrole()),
                ],
                'searchname' => $testsetting1->name,
                'searchid' => $testsetting1->get_id(),

                // Because the two settings have the same name + id, its impossible to tell them apart.
                // So the check should not be returned.
                'expectedcheck' => '',
            ],
        ];
    }

    /**
     * Tests finding the check using the admin tree in various situations.
     *
     * @param array $settings array of admin_settings to setup
     * @param string $searchname name of setting to search for
     * @param string $searchid id of setting to search for
     * @param string $expectedcheck class name of expected check to be found. If empty, expects that none was found.
     * @dataProvider find_check_from_setting_tree_provider
     */
    public function test_find_check_from_setting_tree(array $settings, string $searchname, string $searchid,
        string $expectedcheck): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $root = $this->setup_admin_tree($settings);

        $method = new ReflectionMethod(get_result_admintree::class, 'get_check_from_setting');

        $result = $method->invoke(new get_result_admintree(), $searchid, $searchname, $root);

        if (!empty($expectedcheck)) {
            $this->assertInstanceOf($expectedcheck, $result);
        } else {
            $this->assertEmpty($result);
        }
    }

    /**
     * Provides values to test_capability_check
     *
     * @return array
     */
    public static function capability_check_provider(): array {
        return [
            'has permission' => [
                'permission' => CAP_ALLOW,
                'expectedexception' => null,
            ],
            'does not have permission' => [
                'permission' => CAP_PROHIBIT,
                'expectedexception' => required_capability_exception::class,
            ],
        ];
    }

    /**
     * Tests that capabilites are being checked correctly by the webservice.
     *
     * @param int $permission the permission level to assign the capability to the role for.
     * @param string|null $expectedexception Exception class expected, or null if none is expected.
     * @dataProvider capability_check_provider
     */
    public function test_capability_check($permission, $expectedexception): void {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $role = $this->getDataGenerator()->create_role();
        role_assign($role, $user->id, context_system::instance()->id);
        role_change_permission($role, context_system::instance(), 'moodle/site:config', $permission);

        if (!empty($expectedexception)) {
            $this->expectException($expectedexception);
        }

        // Setup check setting as admin.
        $this->setAdminUser();
        $checksetting = new admin_setting_check('core/testcheck', new passwordpolicy());
        $root = $this->setup_admin_tree([$checksetting]);

        $this->setUser($user);
        get_result_admintree::execute($checksetting->get_id(), $checksetting->name, false, $root);
    }
}
