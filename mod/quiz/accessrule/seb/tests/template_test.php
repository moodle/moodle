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
 * PHPUnit tests for template class.
 *
 * @package   quizaccess_seb
 * @author    Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template_test extends \advanced_testcase {
    use \quizaccess_seb_test_helper_trait;

    /**
     * Called before every test.
     */
    public function setUp(): void {
        parent::setUp();

        $this->resetAfterTest();
    }

    /**
     * Test that template saved with valid content.
     */
    public function test_template_is_saved(): void {
        global $DB;
        $data = new \stdClass();
        $data->name = 'Test name';
        $data->description = 'Test description';
        $data->enabled = 1;
        $data->content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">
<plist version=\"1.0\"><dict><key>showTaskBar</key><true/><key>allowWlan</key><false/><key>showReloadButton</key><true/>"
            . "<key>showTime</key><false/><key>showInputLanguage</key><true/><key>allowQuit</key><true/>"
            . "<key>quitURLConfirm</key><true/><key>audioControlEnabled</key><true/><key>audioMute</key><false/>"
            . "<key>allowSpellCheck</key><false/><key>browserWindowAllowReload</key><true/><key>URLFilterEnable</key><true/>"
            . "<key>URLFilterEnableContentFilter</key><false/><key>hashedQuitPassword</key>"
            . "<string>9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08</string><key>URLFilterRules</key>"
            . "<array><dict><key>action</key><integer>1</integer><key>active</key><true/><key>expression</key>"
            . "<string>test.com</string><key>regex</key><false/></dict></array>"
            . "<key>sendBrowserExamKey</key><true/></dict></plist>\n";
        $template = new template(0, $data);
        $template->save();

        $actual = $DB->get_record(template::TABLE, ['id' => $template->get('id')]);
        $this->assertEquals($data->name, $actual->name);
        $this->assertEquals($data->description, $actual->description);
        $this->assertEquals($data->enabled, $actual->enabled);
        $this->assertEquals($data->content, $actual->content);
        $this->assertTrue($template->can_delete());
    }

    /**
     * Test that template is not saved with invalid content.
     */
    public function test_template_is_not_saved_with_invalid_content(): void {
        $this->expectException(\core\invalid_persistent_exception::class);
        $this->expectExceptionMessage('Invalid SEB config template');

        $data = new \stdClass();
        $data->name = 'Test name';
        $data->description = 'Test description';
        $data->enabled = 1;
        $data->content = "Invalid content";
        $template = new template(0, $data);
        $template->save();
    }

    /**
     * Test that a template cannot be deleted when assigned to a quiz.
     */
    public function test_cannot_delete_template_when_assigned_to_quiz(): void {
        global $DB;

        $data = new \stdClass();
        $data->name = 'Test name';
        $data->description = 'Test description';
        $data->enabled = 1;
        $data->content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">
<plist version=\"1.0\"><dict><key>showTaskBar</key><true/><key>allowWlan</key><false/><key>showReloadButton</key><true/>"
            . "<key>showTime</key><false/><key>showInputLanguage</key><true/><key>allowQuit</key><true/>"
            . "<key>quitURLConfirm</key><true/><key>audioControlEnabled</key><true/><key>audioMute</key><false/>"
            . "<key>allowSpellCheck</key><false/><key>browserWindowAllowReload</key><true/><key>URLFilterEnable</key><true/>"
            . "<key>URLFilterEnableContentFilter</key><false/><key>hashedQuitPassword</key>"
            . "<string>9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08</string><key>URLFilterRules</key>"
            . "<array><dict><key>action</key><integer>1</integer><key>active</key><true/><key>expression</key>"
            . "<string>test.com</string><key>regex</key><false/></dict></array>"
            . "<key>sendBrowserExamKey</key><true/></dict></plist>\n";
        $template = new template(0, $data);

        $template->save();
        $this->assertTrue($template->can_delete());

        $DB->insert_record(seb_quiz_settings::TABLE, (object) [
            'quizid' => 1,
            'cmid' => 1,
            'templateid' => $template->get('id'),
            'requiresafeexambrowser' => '1',
            'sebconfigfile' => '373552893',
            'showsebtaskbar' => '1',
            'showwificontrol' => '0',
            'showreloadbutton' => '1',
            'showtime' => '0',
            'showkeyboardlayout' => '1',
            'allowuserquitseb' => '1',
            'quitpassword' => 'test',
            'linkquitseb' => '',
            'userconfirmquit' => '1',
            'enableaudiocontrol' => '1',
            'muteonstartup' => '0',
            'allowspellchecking' => '0',
            'allowreloadinexam' => '1',
            'activateurlfiltering' => '1',
            'filterembeddedcontent' => '0',
            'expressionsallowed' => 'test.com',
            'regexallowed' => '',
            'expressionsblocked' => '',
            'regexblocked' => '',
            'showsebdownloadlink' => '1',
            'config' => '',
        ]);

        $this->assertFalse($template->can_delete());
    }

    /**
     * Test that a disabled template no longer shows up in quiz SEB settings other than quizzes already using it.
     *
     * @covers \quizaccess_seb\seb_quiz_settings::get_record
     * @covers \quizaccess_seb\settings_provider::get_requiresafeexambrowser_options
     */
    public function test_disabled_template_quiz_setting_options(): void {
        // Create quiz and fetch standard SEB requirement options.
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();

        $templateoptionstr = get_string('seb_use_template', 'quizaccess_seb');

        // Create a quiz.
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $context = \context_module::instance($this->quiz->cmid);

        // Check there is no template option (as there aren't any).
        $options = settings_provider::get_requiresafeexambrowser_options($context);
        $this->assertNotContainsEquals($templateoptionstr, $options);

        // Create a template.
        $data = new \stdClass();
        $data->name = 'Test name';
        $data->description = 'Test description';
        $data->enabled = 1;
        $data->content = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'unencrypted.seb'));
        $template = new template(0, $data);
        $template->save();

        // Check options now include template option.
        $options = settings_provider::get_requiresafeexambrowser_options($context);
        $this->assertContainsEquals($templateoptionstr, $options);

        // Set SEB setting to use template for quiz.
        $settings = seb_quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('templateid', $template->get('id'));
        $settings->set('requiresafeexambrowser', settings_provider::USE_SEB_TEMPLATE);
        $settings->save();

        // Disable template.
        $template->set('enabled', 0);
        $template->save();

        // Check option still exists on current quiz.
        $options = settings_provider::get_requiresafeexambrowser_options($context);
        $this->assertContainsEquals($templateoptionstr, $options);

        // Create a new quiz.
        $newquiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $context = \context_module::instance($newquiz->cmid);

        // Check there is no template option (as the template is now disabled).
        $options = settings_provider::get_requiresafeexambrowser_options($context);
        $this->assertNotContainsEquals($templateoptionstr, $options);
    }
}
