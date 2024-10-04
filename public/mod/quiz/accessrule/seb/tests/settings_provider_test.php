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

namespace quizaccess_seb;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/test_helper_trait.php');

/**
 * PHPUnit tests for settings_provider.
 *
 * @package   quizaccess_seb
 * @author    Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class settings_provider_test extends \advanced_testcase {
    use \quizaccess_seb_test_helper_trait;

    /**
     * Mocked quiz form instance.
     * @var \mod_quiz_mod_form
     */
    protected $mockedquizform;

    /**
     * Test moodle form.
     * @var \MoodleQuickForm
     */
    protected $mockedform;

    /**
     * Context for testing.
     * @var \context
     */
    protected $context;

    /**
     * Test user.
     * @var \stdClass
     */
    protected $user;

    /**
     * Test role ID.
     * @var int
     */
    protected $roleid;

    /**
     * Helper method to set up form mocks.
     */
    protected function set_up_form_mocks() {
        if (empty($this->context)) {
            $this->context = \context_module::instance($this->quiz->cmid);
        }

        $this->mockedquizform = $this->createMock('mod_quiz_mod_form');
        $this->mockedquizform->method('get_context')->willReturn($this->context);
        $this->mockedquizform->method('get_instance')->willReturn($this->quiz->id);
        $this->mockedform = new \MoodleQuickForm('test', 'post', '');
        $this->mockedform->addElement('static', 'security');
    }

    /**
     * Helper method to set up user and role for testing.
     */
    protected function set_up_user_and_role() {
        $this->user = $this->getDataGenerator()->create_user();

        $this->setUser($this->user);
        $this->roleid = $this->getDataGenerator()->create_role();

        $this->getDataGenerator()->role_assign($this->roleid, $this->user->id, $this->context->id);
    }

    /**
     * Capability data for testing.
     *
     * @return array
     */
    public static function settings_capability_data_provider(): array {
        $data = [];

        // Build first level SEB config settings. Any of this setting let us use SEB manual config.
        foreach (settings_provider::get_seb_settings_map()[settings_provider::USE_SEB_CONFIG_MANUALLY] as $name => $children) {
            if (key_exists($name, settings_provider::get_seb_config_elements())) {
                $cap = settings_provider::build_setting_capability_name($name);
                $data[] = [$cap];
            }
        }

        return $data;
    }

    /**
     * Test that settings types to be added to quiz settings, are part of quiz_settings persistent class.
     */
    public function test_setting_elements_are_part_of_quiz_settings_table(): void {
        $dbsettings = (array) (new seb_quiz_settings())->to_record();
        $settingelements = settings_provider::get_seb_config_elements();
        $settingelements = (array) $this->strip_all_prefixes((object) $settingelements);

        // Get all elements to be added to form, that are not in the persistent quiz_settings class.
        $diffelements = array_diff_key($settingelements, $dbsettings);

        $this->assertEmpty($diffelements);
    }

    /**
     * Make sure that all SEB settings have related capabilities.
     */
    public function test_that_all_seb_settings_have_capabilities(): void {
        foreach (settings_provider::get_seb_config_elements() as $name => $notused) {
            $this->assertNotEmpty(get_capability_info(settings_provider::build_setting_capability_name($name)));
        }
    }

    /**
     * Test that setting defaults only refer to settings defined in setting types.
     */
    public function test_setting_defaults_are_part_of_file_types(): void {
        $settingelements = settings_provider::get_seb_config_elements();
        $settingdefaults = settings_provider::get_seb_config_element_defaults();

        // Get all defaults that have no matching element in settings types.
        $diffelements = array_diff_key($settingdefaults, $settingelements);

        $this->assertEmpty($diffelements);
    }

    /**
     * Test that setting types only refer to settings defined in setting types.
     */
    public function test_setting_types_are_part_of_file_types(): void {
        $settingelements = settings_provider::get_seb_config_elements();
        $settingtypes = settings_provider::get_seb_config_element_types();

        // Get all defaults that have no matching element in settings types.
        $diffelements = array_diff_key($settingtypes, $settingelements);

        $this->assertEmpty($diffelements);
    }

    /**
     * Helper method to assert hide if element.
     * @param hideif_rule $hideif Rule to check.
     * @param string $element Expected element.
     * @param string $dependantname Expected dependant element name.
     * @param string $condition Expected condition.
     * @param mixed $value Expected value.
     */
    protected function assert_hide_if(hideif_rule $hideif, $element, $dependantname, $condition, $value) {
        $this->assertEquals($element, $hideif->get_element());
        $this->assertEquals($dependantname, $hideif->get_dependantname());
        $this->assertEquals($condition, $hideif->get_condition());
        $this->assertEquals($value, $hideif->get_dependantvalue());
    }

    /**
     * Test hideif rules.
     */
    public function test_hideifs(): void {
        $settinghideifs = settings_provider::get_quiz_hideifs();

        $this->assertCount(25, $settinghideifs);

        $this->assertArrayHasKey('seb_templateid', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_templateid']);
        $this->assert_hide_if(
            $settinghideifs['seb_templateid'][0],
            'seb_templateid',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_TEMPLATE
        );

        $this->assertArrayHasKey('filemanager_sebconfigfile', $settinghideifs);
        $this->assertCount(1, $settinghideifs['filemanager_sebconfigfile']);
        $this->assert_hide_if(
            $settinghideifs['filemanager_sebconfigfile'][0],
            'filemanager_sebconfigfile',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_UPLOAD_CONFIG
        );

        $this->assertArrayHasKey('seb_showsebtaskbar', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_showsebtaskbar']);
        $this->assert_hide_if(
            $settinghideifs['seb_showsebtaskbar'][0],
            'seb_showsebtaskbar',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_showwificontrol', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_showwificontrol']);
        $this->assert_hide_if(
            $settinghideifs['seb_showwificontrol'][0],
            'seb_showwificontrol',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_showwificontrol'][1],
            'seb_showwificontrol',
            'seb_showsebtaskbar',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_showreloadbutton', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_showreloadbutton']);
        $this->assert_hide_if(
            $settinghideifs['seb_showreloadbutton'][0],
            'seb_showreloadbutton',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_showreloadbutton'][1],
            'seb_showreloadbutton',
            'seb_showsebtaskbar',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_showtime', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_showtime']);
        $this->assert_hide_if(
            $settinghideifs['seb_showtime'][0],
            'seb_showtime',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_showtime'][1],
            'seb_showtime',
            'seb_showsebtaskbar',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_showkeyboardlayout', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_showkeyboardlayout']);
        $this->assert_hide_if(
            $settinghideifs['seb_showkeyboardlayout'][0],
            'seb_showkeyboardlayout',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_showkeyboardlayout'][1],
            'seb_showkeyboardlayout',
            'seb_showsebtaskbar',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_allowuserquitseb', $settinghideifs);
        $this->assertCount(3, $settinghideifs['seb_allowuserquitseb']);
        $this->assert_hide_if(
            $settinghideifs['seb_allowuserquitseb'][0],
            'seb_allowuserquitseb',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_NO
        );
        $this->assert_hide_if(
            $settinghideifs['seb_allowuserquitseb'][1],
            'seb_allowuserquitseb',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_CLIENT_CONFIG
        );
        $this->assert_hide_if(
            $settinghideifs['seb_allowuserquitseb'][2],
            'seb_allowuserquitseb',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_UPLOAD_CONFIG
        );

        $this->assertArrayHasKey('seb_quitpassword', $settinghideifs);
        $this->assertCount(4, $settinghideifs['seb_quitpassword']);
        $this->assert_hide_if(
            $settinghideifs['seb_quitpassword'][0],
            'seb_quitpassword',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_NO
        );
        $this->assert_hide_if(
            $settinghideifs['seb_quitpassword'][1],
            'seb_quitpassword',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_CLIENT_CONFIG
        );
        $this->assert_hide_if(
            $settinghideifs['seb_quitpassword'][2],
            'seb_quitpassword',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_UPLOAD_CONFIG
        );
        $this->assert_hide_if(
            $settinghideifs['seb_quitpassword'][3],
            'seb_quitpassword',
            'seb_allowuserquitseb',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_linkquitseb', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_linkquitseb']);
        $this->assert_hide_if(
            $settinghideifs['seb_linkquitseb'][0],
            'seb_linkquitseb',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_userconfirmquit', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_userconfirmquit']);
        $this->assert_hide_if(
            $settinghideifs['seb_userconfirmquit'][0],
            'seb_userconfirmquit',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_enableaudiocontrol', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_enableaudiocontrol']);
        $this->assert_hide_if(
            $settinghideifs['seb_enableaudiocontrol'][0],
            'seb_enableaudiocontrol',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_allowcapturecamera', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_allowcapturecamera']);
        $this->assert_hide_if(
            $settinghideifs['seb_allowcapturecamera'][0],
            'seb_allowcapturecamera',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_allowcapturemicrophone', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_allowcapturemicrophone']);
        $this->assert_hide_if(
            $settinghideifs['seb_allowcapturemicrophone'][0],
            'seb_allowcapturemicrophone',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_muteonstartup', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_muteonstartup']);
        $this->assert_hide_if(
            $settinghideifs['seb_muteonstartup'][0],
            'seb_muteonstartup',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_muteonstartup'][1],
            'seb_muteonstartup',
            'seb_enableaudiocontrol',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_allowspellchecking', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_allowspellchecking']);
        $this->assert_hide_if(
            $settinghideifs['seb_allowspellchecking'][0],
            'seb_allowspellchecking',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_allowreloadinexam', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_allowreloadinexam']);
        $this->assert_hide_if(
            $settinghideifs['seb_allowreloadinexam'][0],
            'seb_allowreloadinexam',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_activateurlfiltering', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_activateurlfiltering']);
        $this->assert_hide_if(
            $settinghideifs['seb_activateurlfiltering'][0],
            'seb_activateurlfiltering',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );

        $this->assertArrayHasKey('seb_filterembeddedcontent', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_filterembeddedcontent']);
        $this->assert_hide_if(
            $settinghideifs['seb_filterembeddedcontent'][0],
            'seb_filterembeddedcontent',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_filterembeddedcontent'][1],
            'seb_filterembeddedcontent',
            'seb_activateurlfiltering',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_expressionsallowed', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_expressionsallowed']);
        $this->assert_hide_if(
            $settinghideifs['seb_expressionsallowed'][0],
            'seb_expressionsallowed',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_expressionsallowed'][1],
            'seb_expressionsallowed',
            'seb_activateurlfiltering',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_regexallowed', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_regexallowed']);
        $this->assert_hide_if(
            $settinghideifs['seb_regexallowed'][0],
            'seb_regexallowed',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_regexallowed'][1],
            'seb_regexallowed',
            'seb_activateurlfiltering',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_expressionsblocked', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_expressionsblocked']);
        $this->assert_hide_if(
            $settinghideifs['seb_expressionsblocked'][0],
            'seb_expressionsblocked',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_expressionsblocked'][1],
            'seb_expressionsblocked',
            'seb_activateurlfiltering',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_regexblocked', $settinghideifs);
        $this->assertCount(2, $settinghideifs['seb_regexblocked']);
        $this->assert_hide_if(
            $settinghideifs['seb_regexblocked'][0],
            'seb_regexblocked',
            'seb_requiresafeexambrowser',
            'noteq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_regexblocked'][1],
            'seb_regexblocked',
            'seb_activateurlfiltering',
            'eq',
            0
        );

        $this->assertArrayHasKey('seb_showsebdownloadlink', $settinghideifs);
        $this->assertCount(1, $settinghideifs['seb_showsebdownloadlink']);
        $this->assert_hide_if(
            $settinghideifs['seb_showsebdownloadlink'][0],
            'seb_showsebdownloadlink',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_NO
        );

        $this->assertArrayHasKey('seb_allowedbrowserexamkeys', $settinghideifs);
        $this->assertCount(3, $settinghideifs['seb_allowedbrowserexamkeys']);
        $this->assert_hide_if(
            $settinghideifs['seb_allowedbrowserexamkeys'][0],
            'seb_allowedbrowserexamkeys',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_NO
        );
        $this->assert_hide_if(
            $settinghideifs['seb_allowedbrowserexamkeys'][1],
            'seb_allowedbrowserexamkeys',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_CONFIG_MANUALLY
        );
        $this->assert_hide_if(
            $settinghideifs['seb_allowedbrowserexamkeys'][2],
            'seb_allowedbrowserexamkeys',
            'seb_requiresafeexambrowser',
            'eq',
            settings_provider::USE_SEB_TEMPLATE
        );
    }

    /**
     * Test that setting hideif rules only refer to settings defined in setting types, including the conditions.
     */
    public function test_setting_hideifs_are_part_of_file_types(): void {
        $settingelements = settings_provider::get_seb_config_elements();
        $settinghideifs = settings_provider::get_quiz_hideifs();

        // Add known additional elements.
        $settingelements['seb_templateid'] = '';
        $settingelements['filemanager_sebconfigfile'] = '';
        $settingelements['seb_showsebdownloadlink'] = '';
        $settingelements['seb_allowedbrowserexamkeys'] = '';

        // Get all defaults that have no matching element in settings types.
        $diffelements = array_diff_key($settinghideifs, $settingelements);

        // Check no diff for elements to hide.
        $this->assertEmpty($diffelements);

        // Check each element's to hide conditions that each condition refers to element in settings types.
        foreach ($settinghideifs as $conditions) {
            foreach ($conditions as $condition) {
                $this->assertTrue(array_key_exists($condition->get_element(), $settingelements));
            }
        }
    }

    /**
     * Test that exception thrown if we try to build capability name from the incorrect setting name.
     */
    public function test_build_setting_capability_name_incorrect_setting(): void {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Incorrect SEB quiz setting broken');

        $broken = settings_provider::build_setting_capability_name('broken');
    }

    /**
     * Test we can build capability name from the the setting name.
     */
    public function test_build_setting_capability_name_correct_setting(): void {
        foreach (settings_provider::get_seb_config_elements() as $name => $type) {
            $expected = 'quizaccess/seb:manage_' . $name;
            $actual = settings_provider::build_setting_capability_name($name);

            $this->assertSame($expected, $actual);
        }
    }


    /**
     * Test can check if can manage SEB settings respecting settings structure.
     */
    public function test_can_manage_seb_config_setting(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);

        $this->set_up_user_and_role();

        foreach (settings_provider::get_seb_settings_map()[settings_provider::USE_SEB_CONFIG_MANUALLY] as $setting => $children) {
            // Skip not SEB setting.
            if ($setting == 'seb_showsebdownloadlink') {
                continue;
            }

            $this->assertFalse(settings_provider::can_manage_seb_config_setting($setting, $this->context));
            foreach ($children as $child => $empty) {
                $this->assertFalse(settings_provider::can_manage_seb_config_setting($child, $this->context));

                // Assign child capability without having parent one. Should not have access to manage child.
                $childcap = settings_provider::build_setting_capability_name($child);
                assign_capability($childcap, CAP_ALLOW, $this->roleid, $this->context->id);
                $this->assertFalse(settings_provider::can_manage_seb_config_setting($child, $this->context));
            }

            // Assign parent capability. Should be able to manage children now.
            $parentcap = settings_provider::build_setting_capability_name($setting);
            assign_capability($parentcap, CAP_ALLOW, $this->roleid, $this->context->id);

            $this->assertTrue(settings_provider::can_manage_seb_config_setting($setting, $this->context));
            foreach ($children as $child => $empty) {
                $this->assertTrue(settings_provider::can_manage_seb_config_setting($child, $this->context));
            }
        }
    }

    /**
     * Test SEB usage options.
     *
     * @param string $settingcapability Setting capability to check options against.
     *
     * @dataProvider settings_capability_data_provider
     */
    public function test_get_requiresafeexambrowser_options($settingcapability): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);

        $options = settings_provider::get_requiresafeexambrowser_options($this->context);

        $this->assertCount(4, $options);
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_NO, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CONFIG_MANUALLY, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_UPLOAD_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CLIENT_CONFIG, $options));

        // Create a template.
        $this->create_template();

        // The template options should be visible now.
        $options = settings_provider::get_requiresafeexambrowser_options($this->context);
        $this->assertCount(5, $options);
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));

        // A new user does not have the capability to use the file manager and template.
        $this->set_up_user_and_role();

        $options = settings_provider::get_requiresafeexambrowser_options($this->context);

        $this->assertCount(1, $options);
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_CONFIG_MANUALLY, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_UPLOAD_CONFIG, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_CLIENT_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_NO, $options));

        assign_capability($settingcapability, CAP_ALLOW, $this->roleid, $this->context->id);
        $options = settings_provider::get_requiresafeexambrowser_options($this->context);
        $this->assertCount(1, $options);
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_CONFIG_MANUALLY, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_UPLOAD_CONFIG, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_CLIENT_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_NO, $options));

        assign_capability('quizaccess/seb:manage_seb_configuremanually', CAP_ALLOW, $this->roleid, $this->context->id);
        $options = settings_provider::get_requiresafeexambrowser_options($this->context);
        $this->assertCount(2, $options);
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CONFIG_MANUALLY, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_UPLOAD_CONFIG, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_CLIENT_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_NO, $options));

        assign_capability('quizaccess/seb:manage_seb_usesebclientconfig', CAP_ALLOW, $this->roleid, $this->context->id);
        $options = settings_provider::get_requiresafeexambrowser_options($this->context);
        $this->assertCount(3, $options);
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CONFIG_MANUALLY, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_UPLOAD_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CLIENT_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_NO, $options));

        assign_capability('quizaccess/seb:manage_seb_templateid', CAP_ALLOW, $this->roleid, $this->context->id);
        $options = settings_provider::get_requiresafeexambrowser_options($this->context);
        $this->assertCount(4, $options);
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CONFIG_MANUALLY, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));
        $this->assertFalse(array_key_exists(settings_provider::USE_SEB_UPLOAD_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CLIENT_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_NO, $options));

        assign_capability('quizaccess/seb:manage_filemanager_sebconfigfile', CAP_ALLOW, $this->roleid, $this->context->id);
        $options = settings_provider::get_requiresafeexambrowser_options($this->context);
        $this->assertCount(5, $options);
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CONFIG_MANUALLY, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_UPLOAD_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_CLIENT_CONFIG, $options));
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_NO, $options));
    }

    /**
     * Test SEB usage options with conflicting permissions.
     */
    public function test_get_requiresafeexambrowser_options_with_conflicting_permissions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        $template = $this->create_template();

        $settings = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('templateid', $template->get('id'));
        $settings->set('requiresafeexambrowser', settings_provider::USE_SEB_TEMPLATE);
        $settings->save();

        $this->set_up_user_and_role();

        $options = settings_provider::get_requiresafeexambrowser_options($this->context);

        // If there is nay conflict we return full list of options.
        $this->assertCount(5, $options);
        $this->assertTrue(array_key_exists(settings_provider::USE_SEB_TEMPLATE, $options));
    }

    /**
     * Test that SEB options and templates are frozen if conflicting permissions.
     */
    public function test_form_elements_are_frozen_if_conflicting_permissions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        // Setup conflicting permissions.
        $template = $this->create_template();
        $settings = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('templateid', $template->get('id'));
        $settings->set('requiresafeexambrowser', settings_provider::USE_SEB_TEMPLATE);
        $settings->save();

        $this->set_up_user_and_role();

        assign_capability('quizaccess/seb:manage_seb_requiresafeexambrowser', CAP_ALLOW, $this->roleid, $this->context->id);
        assign_capability('quizaccess/seb:manage_seb_showsebdownloadlink', CAP_ALLOW, $this->roleid, $this->context->id);
        assign_capability('quizaccess/seb:manage_seb_allowedbrowserexamkeys', CAP_ALLOW, $this->roleid, $this->context->id);

        $this->set_up_form_mocks();

        settings_provider::add_seb_settings_fields($this->mockedquizform, $this->mockedform);

        $this->assertTrue($this->mockedform->isElementFrozen('seb_requiresafeexambrowser'));
        $this->assertTrue($this->mockedform->isElementFrozen('seb_templateid'));
        $this->assertTrue($this->mockedform->isElementFrozen('seb_showsebdownloadlink'));
        $this->assertTrue($this->mockedform->isElementFrozen('seb_allowedbrowserexamkeys'));
    }

    /**
     * Test that All settings are frozen if quiz was attempted and use seb with manual settings.
     */
    public function test_form_elements_are_locked_when_quiz_attempted_manual(): void {
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        $user = $this->getDataGenerator()->create_user();
        $this->attempt_quiz($this->quiz, $user);

        $this->setAdminUser();
        $this->set_up_form_mocks();

        settings_provider::add_seb_settings_fields($this->mockedquizform, $this->mockedform);

        $this->assertTrue($this->mockedform->isElementFrozen('seb_requiresafeexambrowser'));
        $this->assertTrue($this->mockedform->elementExists('filemanager_sebconfigfile'));
        $this->assertFalse($this->mockedform->elementExists('seb_templateid'));
        $this->assertTrue($this->mockedform->isElementFrozen('seb_showsebdownloadlink'));
        $this->assertTrue($this->mockedform->isElementFrozen('seb_allowedbrowserexamkeys'));

        foreach (settings_provider::get_seb_config_elements() as $name => $type) {
            $this->assertTrue($this->mockedform->isElementFrozen($name));
        }
    }

    /**
     * Test that All settings are frozen if a quiz was attempted and use template.
     */
    public function test_form_elements_are_locked_when_quiz_attempted_template(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        $template = $this->create_template();

        $settings = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('templateid', $template->get('id'));
        $settings->set('requiresafeexambrowser', settings_provider::USE_SEB_TEMPLATE);
        $settings->save();

        $user = $this->getDataGenerator()->create_user();
        $this->attempt_quiz($this->quiz, $user);

        $this->setAdminUser();
        $this->set_up_form_mocks();

        settings_provider::add_seb_settings_fields($this->mockedquizform, $this->mockedform);

        $this->assertTrue($this->mockedform->isElementFrozen('seb_requiresafeexambrowser'));
        $this->assertTrue($this->mockedform->elementExists('filemanager_sebconfigfile'));
        $this->assertTrue($this->mockedform->isElementFrozen('seb_templateid'));
        $this->assertTrue($this->mockedform->isElementFrozen('seb_showsebdownloadlink'));
        $this->assertTrue($this->mockedform->isElementFrozen('seb_allowedbrowserexamkeys'));

        foreach (settings_provider::get_seb_config_elements() as $name => $type) {
            $this->assertTrue($this->mockedform->isElementFrozen($name));
        }
    }

    /**
     * Test Show Safe Exam Browser download button setting in the form.
     */
    public function test_showsebdownloadlink_in_form(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        $this->set_up_user_and_role();

        assign_capability('quizaccess/seb:manage_seb_requiresafeexambrowser', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->set_up_form_mocks();

        // Shouldn't be in the form if no permissions.
        settings_provider::add_seb_settings_fields($this->mockedquizform, $this->mockedform);
        $this->assertFalse($this->mockedform->elementExists('seb_showsebdownloadlink'));

        // Should be in the form if we grant require permissions.
        assign_capability('quizaccess/seb:manage_seb_showsebdownloadlink', CAP_ALLOW, $this->roleid, $this->context->id);

        settings_provider::add_seb_settings_fields($this->mockedquizform, $this->mockedform);
        $this->assertTrue($this->mockedform->elementExists('seb_showsebdownloadlink'));
    }

    /**
     * Test Allowed Browser Exam Keys setting in the form.
     */
    public function test_allowedbrowserexamkeys_in_form(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);
        $this->context = \context_module::instance($this->quiz->cmid);

        $this->set_up_user_and_role();

        assign_capability('quizaccess/seb:manage_seb_requiresafeexambrowser', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->set_up_form_mocks();

        // Shouldn't be in the form if no permissions.
        settings_provider::add_seb_settings_fields($this->mockedquizform, $this->mockedform);
        $this->assertFalse($this->mockedform->elementExists('seb_allowedbrowserexamkeys'));

        // Should be in the form if we grant require permissions.
        assign_capability('quizaccess/seb:manage_seb_allowedbrowserexamkeys', CAP_ALLOW, $this->roleid, $this->context->id);
        settings_provider::add_seb_settings_fields($this->mockedquizform, $this->mockedform);
        $this->assertTrue($this->mockedform->elementExists('seb_allowedbrowserexamkeys'));
    }

    /**
     * Test the validation of a seb config file.
     */
    public function test_validate_draftarea_configfile_success(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n"
            . "<plist version=\"1.0\"><dict><key>hashedQuitPassword</key><string>hashedpassword</string>"
            . "<key>allowWlan</key><false/></dict></plist>\n";
        $itemid = $this->create_test_draftarea_file($xml);
        $errors = settings_provider::validate_draftarea_configfile($itemid);
        $this->assertEmpty($errors);
    }

    /**
     * Test the validation of a missing seb config file.
     */
    public function test_validate_draftarea_configfile_failure(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $xml = "This is not a config file.";
        $itemid = $this->create_test_draftarea_file($xml);
        $errors = settings_provider::validate_draftarea_configfile($itemid);
        $this->assertEquals($errors, new \lang_string('fileparsefailed', 'quizaccess_seb'));
    }

    /**
     * Test obtaining the draftarea content.
     */
    public function test_get_current_user_draft_file(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'unencrypted.seb'));
        $itemid = $this->create_test_draftarea_file($xml);
        $file = settings_provider::get_current_user_draft_file($itemid);
        $content = $file->get_content();

        $this->assertEquals($xml, $content);
    }

    /**
     * Test saving files from the user draft area into the quiz context area storage.
     */
    public function test_save_filemanager_sebconfigfile_draftarea(): void {
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->set_up_user_and_role();

        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'unencrypted.seb'));

        $draftitemid = $this->create_test_draftarea_file($xml);

        settings_provider::save_filemanager_sebconfigfile_draftarea($draftitemid, $this->quiz->cmid);

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'quizaccess_seb', 'filemanager_sebconfigfile');

        $this->assertCount(2, $files);
    }

    /**
     * Test deleting the $this->quiz->cmid itemid from the file area.
     */
    public function test_delete_uploaded_config_file(): void {
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->set_up_user_and_role();

        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'unencrypted.seb'));
        $draftitemid = $this->create_test_draftarea_file($xml);

        settings_provider::save_filemanager_sebconfigfile_draftarea($draftitemid, $this->quiz->cmid);

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'quizaccess_seb', 'filemanager_sebconfigfile');
        $this->assertCount(2, $files);

        settings_provider::delete_uploaded_config_file($this->quiz->cmid);

        $files = $fs->get_area_files($this->context->id, 'quizaccess_seb', 'filemanager_sebconfigfile');
        // The '.' directory.
        $this->assertCount(1, $files);
    }

    /**
     * Test getting the file from the context module id file area.
     */
    public function test_get_module_context_sebconfig_file(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        $this->set_up_user_and_role();

        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'unencrypted.seb'));
        $draftitemid = $this->create_test_draftarea_file($xml);

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'quizaccess_seb', 'filemanager_sebconfigfile');
        $this->assertCount(0, $files);

        settings_provider::save_filemanager_sebconfigfile_draftarea($draftitemid, $this->quiz->cmid);

        $settings = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('requiresafeexambrowser', settings_provider::USE_SEB_UPLOAD_CONFIG);
        $settings->save();

        $file = settings_provider::get_module_context_sebconfig_file($this->quiz->cmid);

        $this->assertSame($file->get_content(), $xml);
    }

    /**
     * Test file manager options.
     */
    public function test_get_filemanager_options(): void {
        $expected = [
            'subdirs' => 0,
            'maxfiles' => 1,
            'accepted_types' => ['.seb']
        ];
        $this->assertSame($expected, settings_provider::get_filemanager_options());
    }

    /**
     * Test that users can or can not configure seb settings.
     */
    public function test_can_configure_seb(): void {
        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->setAdminUser();

        $this->assertTrue(settings_provider::can_configure_seb($this->context));

        $this->set_up_user_and_role();

        $this->assertFalse(settings_provider::can_configure_seb($this->context));

        assign_capability('quizaccess/seb:manage_seb_requiresafeexambrowser', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertTrue(settings_provider::can_configure_seb($this->context));
    }

    /**
     * Test that users can or can not use seb template.
     */
    public function test_can_use_seb_template(): void {
        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->setAdminUser();

        $this->assertTrue(settings_provider::can_use_seb_template($this->context));

        $this->set_up_user_and_role();

        $this->assertFalse(settings_provider::can_use_seb_template($this->context));

        assign_capability('quizaccess/seb:manage_seb_templateid', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertTrue(settings_provider::can_use_seb_template($this->context));
    }

    /**
     * Test that users can or can not upload seb config file.
     */
    public function test_can_upload_seb_file(): void {
        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->setAdminUser();

        $this->assertTrue(settings_provider::can_upload_seb_file($this->context));

        $this->set_up_user_and_role();

        $this->assertFalse(settings_provider::can_upload_seb_file($this->context));

        assign_capability('quizaccess/seb:manage_filemanager_sebconfigfile', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertTrue(settings_provider::can_upload_seb_file($this->context));
    }

    /**
     * Test that users can or can not change Show Safe Exam Browser download button setting.
     */
    public function test_can_change_seb_showsebdownloadlink(): void {
        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->setAdminUser();
        $this->assertTrue(settings_provider::can_change_seb_showsebdownloadlink($this->context));

        $this->set_up_user_and_role();

        $this->assertFalse(settings_provider::can_change_seb_showsebdownloadlink($this->context));

        assign_capability('quizaccess/seb:manage_seb_showsebdownloadlink', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertTrue(settings_provider::can_change_seb_showsebdownloadlink($this->context));
    }

    /**
     * Test that users can or can not change Allowed Browser Exam Keys setting.
     */
    public function test_can_change_seb_allowedbrowserexamkeys(): void {
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->setAdminUser();
        $this->assertTrue(settings_provider::can_change_seb_allowedbrowserexamkeys($this->context));

        $this->set_up_user_and_role();

        $this->assertFalse(settings_provider::can_change_seb_allowedbrowserexamkeys($this->context));

        assign_capability('quizaccess/seb:manage_seb_allowedbrowserexamkeys', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertTrue(settings_provider::can_change_seb_allowedbrowserexamkeys($this->context));
    }

    /**
     * Test that users can or can not Configure SEb manually
     *
     * @param string $settingcapability Setting capability to check manual option against.
     *
     * @dataProvider settings_capability_data_provider
     */
    public function test_can_configure_manually($settingcapability): void {
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();

        $this->quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $this->course->id]);
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->setAdminUser();

        $this->assertTrue(settings_provider::can_configure_manually($this->context));

        $this->set_up_user_and_role();

        $this->assertFalse(settings_provider::can_configure_manually($this->context));

        assign_capability('quizaccess/seb:manage_seb_configuremanually', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertFalse(settings_provider::can_configure_manually($this->context));

        assign_capability($settingcapability, CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertTrue(settings_provider::can_configure_manually($this->context));
    }

    /**
     * Test that we can check if the seb settings are locked.
     */
    public function test_is_seb_settings_locked(): void {
        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->create_test_quiz($this->course);
        $user = $this->getDataGenerator()->create_user();

        $this->assertFalse(settings_provider::is_seb_settings_locked($this->quiz->id));

        $this->attempt_quiz($this->quiz, $user);
        $this->assertTrue(settings_provider::is_seb_settings_locked($this->quiz->id));
    }

    /**
     * Test that we can check identify conflicting permissions if set to use template.
     */
    public function test_is_conflicting_permissions_for_manage_templates(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        // Create a template.
        $template = $this->create_template();
        $settings = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('templateid', $template->get('id'));
        $settings->set('requiresafeexambrowser', settings_provider::USE_SEB_TEMPLATE);
        $settings->save();

        $this->assertFalse(settings_provider::is_conflicting_permissions($this->context));

        $this->set_up_user_and_role();

        $this->assertTrue(settings_provider::is_conflicting_permissions($this->context));

        assign_capability('quizaccess/seb:manage_seb_templateid', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertFalse(settings_provider::is_conflicting_permissions($this->context));
    }

    /**
     * Test that we can check identify conflicting permissions if set to use own seb file.
     */
    public function test_is_conflicting_permissions_for_upload_seb_file(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        // Save file.
        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'unencrypted.seb'));
        $draftitemid = $this->create_test_draftarea_file($xml);
        settings_provider::save_filemanager_sebconfigfile_draftarea($draftitemid, $this->quiz->cmid);
        $settings = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('requiresafeexambrowser', settings_provider::USE_SEB_UPLOAD_CONFIG);
        $settings->save();

        $this->assertFalse(settings_provider::is_conflicting_permissions($this->context));

        $this->set_up_user_and_role();

        assign_capability('quizaccess/seb:manage_filemanager_sebconfigfile', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertFalse(settings_provider::is_conflicting_permissions($this->context));
    }

    /**
     * Test that we can check identify conflicting permissions if set to use own configure manually.
     *
     * @param string $settingcapability Setting capability to check manual option against.
     *
     * @dataProvider settings_capability_data_provider
     */
    public function test_is_conflicting_permissions_for_configure_manually($settingcapability): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $this->context = \context_module::instance($this->quiz->cmid);

        $this->assertFalse(settings_provider::is_conflicting_permissions($this->context));

        $this->set_up_user_and_role();

        $this->assertTrue(settings_provider::is_conflicting_permissions($this->context));

        assign_capability('quizaccess/seb:manage_seb_configuremanually', CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertTrue(settings_provider::is_conflicting_permissions($this->context));

        assign_capability($settingcapability, CAP_ALLOW, $this->roleid, $this->context->id);
        $this->assertFalse(settings_provider::is_conflicting_permissions($this->context));
    }

    /**
     * Test add_prefix helper method.
     */
    public function test_add_prefix(): void {
        $this->assertEquals('seb_one', settings_provider::add_prefix('one'));
        $this->assertEquals('seb_two', settings_provider::add_prefix('seb_two'));
        $this->assertEquals('seb_seb_three', settings_provider::add_prefix('seb_seb_three'));
        $this->assertEquals('seb_', settings_provider::add_prefix('seb_'));
        $this->assertEquals('seb_', settings_provider::add_prefix(''));
        $this->assertEquals('seb_one_seb', settings_provider::add_prefix('one_seb'));
    }

    /**
     * Test filter_plugin_settings helper method.
     */
    public function test_filter_plugin_settings(): void {
        $test = new \stdClass();
        $test->one = 'one';
        $test->seb_two = 'two';
        $test->seb_seb_three = 'three';
        $test->four = 'four';

        $newsettings = (array)settings_provider::filter_plugin_settings($test);

        $this->assertFalse(key_exists('one', $newsettings));
        $this->assertFalse(key_exists('four', $newsettings));

        $this->assertCount(2, $newsettings);
        $this->assertEquals('two', $newsettings['two']);
        $this->assertEquals('three', $newsettings['seb_three']);
    }

    /**
     * Helper method to get a list of settings.
     *
     * @return \stdClass
     */
    protected function get_settings() {
        $allsettings = new \stdClass();
        $allsettings->seb_showsebdownloadlink = 0;
        $allsettings->seb_linkquitseb = 2;
        $allsettings->seb_userconfirmquit = 3;
        $allsettings->seb_allowuserquitseb = 4;
        $allsettings->seb_quitpassword = 5;
        $allsettings->seb_allowreloadinexam = 6;
        $allsettings->seb_showsebtaskbar = 7;
        $allsettings->seb_showreloadbutton = 8;
        $allsettings->seb_showtime = 9;
        $allsettings->seb_showkeyboardlayout = 10;
        $allsettings->seb_showwificontrol = 11;
        $allsettings->seb_enableaudiocontrol = 12;
        $allsettings->seb_muteonstartup = 13;
        $allsettings->seb_allowspellchecking = 14;
        $allsettings->seb_activateurlfiltering = 15;
        $allsettings->seb_filterembeddedcontent = 16;
        $allsettings->seb_expressionsallowed = 17;
        $allsettings->seb_regexallowed = 18;
        $allsettings->seb_expressionsblocked = 19;
        $allsettings->seb_regexblocked = 20;
        $allsettings->seb_templateid = 21;
        $allsettings->seb_allowedbrowserexamkeys = 22;
        $allsettings->seb_allowcapturecamera = 23;
        $allsettings->seb_allowcapturemicrophone = 24;

        return $allsettings;
    }

    /**
     * Helper method to assert results of filter_plugin_settings
     *
     * @param int $type Type of SEB usage.
     * @param array $notnulls A list of expected not null settings.
     */
    protected function assert_filter_plugin_settings(int $type, array $notnulls) {
        $allsettings = $this->get_settings();
        $allsettings->seb_requiresafeexambrowser = $type;
        $actual = settings_provider::filter_plugin_settings($allsettings);

        $expected = (array)$allsettings;
        foreach ($actual as $name => $value) {
            if (in_array($name, $notnulls)) {
                $this->assertEquals($expected['seb_' . $name], $value);
            } else {
                $this->assertNull($value);
            }
        }
    }

    /**
     * Test filter_plugin_settings method for no SEB case.
     */
    public function test_filter_plugin_settings_for_no_seb(): void {
        $notnulls = ['requiresafeexambrowser'];
        $this->assert_filter_plugin_settings(settings_provider::USE_SEB_NO, $notnulls);
    }

    /**
     * Test filter_plugin_settings method for using uploaded config.
     */
    public function test_filter_plugin_settings_for_uploaded_config(): void {
        $notnulls = ['requiresafeexambrowser', 'showsebdownloadlink', 'allowedbrowserexamkeys'];
        $this->assert_filter_plugin_settings(settings_provider::USE_SEB_UPLOAD_CONFIG, $notnulls);
    }

    /**
     * Test filter_plugin_settings method for using template.
     */
    public function test_filter_plugin_settings_for_template(): void {
        $notnulls = ['requiresafeexambrowser', 'showsebdownloadlink', 'allowuserquitseb', 'quitpassword', 'templateid'];
        $this->assert_filter_plugin_settings(settings_provider::USE_SEB_TEMPLATE, $notnulls);
    }

    /**
     * Test filter_plugin_settings method for using client config.
     */
    public function test_filter_plugin_settings_for_client_config(): void {
        $notnulls = ['requiresafeexambrowser', 'showsebdownloadlink', 'allowedbrowserexamkeys'];
        $this->assert_filter_plugin_settings(settings_provider::USE_SEB_CLIENT_CONFIG, $notnulls);
    }

    /**
     * Test filter_plugin_settings method for manually configured SEB.
     */
    public function test_filter_plugin_settings_for_configure_manually(): void {
        $allsettings = $this->get_settings();
        $allsettings->seb_requiresafeexambrowser = settings_provider::USE_SEB_CONFIG_MANUALLY;
        $actual = settings_provider::filter_plugin_settings($allsettings);

        // For manual it's easier to check nulls, as most of the settings are not null.
        $nulls = ['templateid', 'allowedbrowserexamkeys'];

        $expected = (array)$allsettings;
        foreach ($actual as $name => $value) {
            if (in_array($name, $nulls)) {
                $this->assertNull($value);
            } else {
                $this->assertEquals($expected['seb_' . $name], $value);
            }
        }
    }

    /**
     * Test settings map.
     */
    public function test_get_seb_settings_map(): void {
        $expected = [
            settings_provider::USE_SEB_NO => [

            ],
            settings_provider::USE_SEB_CONFIG_MANUALLY => [
                'seb_showsebdownloadlink' => [],
                'seb_linkquitseb' => [],
                'seb_userconfirmquit' => [],
                'seb_allowuserquitseb' => [
                    'seb_quitpassword' => []
                ],
                'seb_allowreloadinexam' => [],
                'seb_showsebtaskbar' => [
                    'seb_showreloadbutton' => [],
                    'seb_showtime' => [],
                    'seb_showkeyboardlayout' => [],
                    'seb_showwificontrol' => [],
                ],
                'seb_enableaudiocontrol' => [
                    'seb_muteonstartup' => [],
                ],
                'seb_allowcapturecamera' => [],
                'seb_allowcapturemicrophone' => [],
                'seb_allowspellchecking' => [],
                'seb_activateurlfiltering' => [
                    'seb_filterembeddedcontent' => [],
                    'seb_expressionsallowed' => [],
                    'seb_regexallowed' => [],
                    'seb_expressionsblocked' => [],
                    'seb_regexblocked' => [],
                ],
            ],
            settings_provider::USE_SEB_TEMPLATE => [
                'seb_templateid' => [],
                'seb_showsebdownloadlink' => [],
                'seb_allowuserquitseb' => [
                    'seb_quitpassword' => [],
                ],
            ],
            settings_provider::USE_SEB_UPLOAD_CONFIG => [
                'filemanager_sebconfigfile' => [],
                'seb_showsebdownloadlink' => [],
                'seb_allowedbrowserexamkeys' => [],
            ],
            settings_provider::USE_SEB_CLIENT_CONFIG => [
                'seb_showsebdownloadlink' => [],
                'seb_allowedbrowserexamkeys' => [],
            ],
        ];

        $this->assertEquals($expected, settings_provider::get_seb_settings_map());
    }

}
