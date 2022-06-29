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
 * PHPUnit tests for the access manager.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use quizaccess_seb\access_manager;
use quizaccess_seb\quiz_settings;
use quizaccess_seb\settings_provider;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/test_helper_trait.php');

/**
 * PHPUnit tests for the access manager.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizacces_seb_access_manager_testcase extends advanced_testcase {
    use quizaccess_seb_test_helper_trait;

    /**
     * Called before every test.
     */
    public function setUp(): void {
        parent::setUp();

        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
    }

    /**
     * Test access_manager private property quizsettings is null.
     */
    public function test_access_manager_quizsettings_null() {
        $this->quiz = $this->create_test_quiz($this->course);

        $accessmanager = $this->get_access_manager();

        $this->assertFalse($accessmanager->seb_required());

        $reflection = new \ReflectionClass('\quizaccess_seb\access_manager');
        $property = $reflection->getProperty('quizsettings');
        $property->setAccessible(true);

        $this->assertFalse($property->getValue($accessmanager));
    }

    /**
     * Test that SEB is not required.
     */
    public function test_seb_required_false() {
        $this->quiz = $this->create_test_quiz($this->course);

        $accessmanager = $this->get_access_manager();
        $this->assertFalse($accessmanager->seb_required());
    }

    /**
     * Test that SEB is required.
     */
    public function test_seb_required_true() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);

        $accessmanager = $this->get_access_manager();
        $this->assertTrue($accessmanager->seb_required());
    }

    /**
     * Test that user has capability to bypass SEB check.
     */
    public function test_user_can_bypass_seb_check() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Set the bypass SEB check capability to $USER.
        $this->assign_user_capability('quizaccess/seb:bypassseb', context_module::instance($this->quiz->cmid)->id);

        $accessmanager = $this->get_access_manager();
        $this->assertTrue($accessmanager->can_bypass_seb());
    }

    /**
     * Test user does not have capability to bypass SEB check.
     */
    public function test_user_cannot_bypass_seb_check() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $accessmanager = $this->get_access_manager();
        $this->assertFalse($accessmanager->can_bypass_seb());
    }

    /**
     * Test we can detect SEB usage.
     */
    public function test_is_using_seb() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);

        $accessmanager = $this->get_access_manager();

        $this->assertFalse($accessmanager->is_using_seb());

        $_SERVER['HTTP_USER_AGENT'] = 'Test';
        $this->assertFalse($accessmanager->is_using_seb());

        $_SERVER['HTTP_USER_AGENT'] = 'SEB';
        $this->assertTrue($accessmanager->is_using_seb());
    }

    /**
     * Test that the quiz Config Key matches the incoming request header.
     */
    public function test_access_keys_validate_with_config_key() {
        global $FULLME;
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);

        $accessmanager = $this->get_access_manager();

        $configkey = quiz_settings::get_record(['quizid' => $this->quiz->id])->get_config_key();

        // Set up dummy request.
        $FULLME = 'https://example.com/moodle/mod/quiz/attempt.php?attemptid=123&page=4';
        $expectedhash = hash('sha256', $FULLME . $configkey);
        $_SERVER['HTTP_X_SAFEEXAMBROWSER_CONFIGKEYHASH'] = $expectedhash;

        $this->assertTrue($accessmanager->validate_browser_exam_keys());
        $this->assertTrue($accessmanager->validate_config_key());
    }

    /**
     * Test that the quiz Config Key does not match the incoming request header.
     */
    public function test_access_keys_fail_to_validate_with_config_key() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $accessmanager = $this->get_access_manager();

        $this->assertFalse($accessmanager->validate_config_key());
        $this->assertTrue($accessmanager->validate_browser_exam_keys());
    }

    /**
     * Test that config key is not checked when using client configuration with SEB.
     */
    public function test_config_key_not_checked_if_client_requirement_is_selected() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);
        $accessmanager = $this->get_access_manager();
        $this->assertTrue($accessmanager->validate_config_key());
        $this->assertTrue($accessmanager->validate_browser_exam_keys());
    }

    /**
     * Test that if there are no browser exam keys for quiz, check is skipped.
     */
    public function test_no_browser_exam_keys_cause_check_to_be_skipped() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);

        $settings = quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('allowedbrowserexamkeys', '');
        $settings->save();
        $accessmanager = $this->get_access_manager();
        $this->assertTrue($accessmanager->validate_config_key());
        $this->assertTrue($accessmanager->validate_browser_exam_keys());
    }

    /**
     * Test that access fails if there is no hash in header.
     */
    public function test_access_keys_fail_if_browser_exam_key_header_does_not_exist() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);

        $settings = quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('allowedbrowserexamkeys', hash('sha256', 'one') . "\n" . hash('sha256', 'two'));
        $settings->save();
        $accessmanager = $this->get_access_manager();
        $this->assertTrue($accessmanager->validate_config_key());
        $this->assertFalse($accessmanager->validate_browser_exam_keys());
    }

    /**
     * Test that access fails if browser exam key doesn't match hash in header.
     */
    public function test_access_keys_fail_if_browser_exam_key_header_does_not_match_provided_hash() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);

        $settings = quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $settings->set('allowedbrowserexamkeys', hash('sha256', 'one') . "\n" . hash('sha256', 'two'));
        $settings->save();
        $accessmanager = $this->get_access_manager();
        $_SERVER['HTTP_X_SAFEEXAMBROWSER_REQUESTHASH'] = hash('sha256', 'notwhatyouwereexpectinghuh');
        $this->assertTrue($accessmanager->validate_config_key());
        $this->assertFalse($accessmanager->validate_browser_exam_keys());
    }

    /**
     * Test that browser exam key matches hash in header.
     */
    public function test_browser_exam_keys_match_header_hash() {
        global $FULLME;

        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);
        $settings = quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $browserexamkey = hash('sha256', 'browserexamkey');
        $settings->set('allowedbrowserexamkeys', $browserexamkey); // Add a hashed BEK.
        $settings->save();
        $accessmanager = $this->get_access_manager();

        // Set up dummy request.
        $FULLME = 'https://example.com/moodle/mod/quiz/attempt.php?attemptid=123&page=4';
        $expectedhash = hash('sha256', $FULLME . $browserexamkey);
        $_SERVER['HTTP_X_SAFEEXAMBROWSER_REQUESTHASH'] = $expectedhash;
        $this->assertTrue($accessmanager->validate_config_key());
        $this->assertTrue($accessmanager->validate_browser_exam_keys());
    }

    /**
     * Test can get received config key.
     */
    public function test_get_received_config_key() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);
        $accessmanager = $this->get_access_manager();

        $this->assertNull($accessmanager->get_received_config_key());

        $_SERVER['HTTP_X_SAFEEXAMBROWSER_CONFIGKEYHASH'] = 'Test key';
        $this->assertEquals('Test key', $accessmanager->get_received_config_key());
    }

    /**
     * Test can get received browser key.
     */
    public function get_received_browser_exam_key() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);
        $accessmanager = $this->get_access_manager();

        $this->assertNull($accessmanager->get_received_browser_exam_key());

        $_SERVER['HTTP_X_SAFEEXAMBROWSER_REQUESTHASH'] = 'Test browser key';
        $this->assertEquals('Test browser key', $accessmanager->get_received_browser_exam_key());
    }

    /**
     * Test can correctly get type of SEB usage for the quiz.
     */
    public function test_get_seb_use_type() {
        // No SEB.
        $this->quiz = $this->create_test_quiz($this->course);
        $accessmanager = $this->get_access_manager();
        $this->assertEquals(settings_provider::USE_SEB_NO, $accessmanager->get_seb_use_type());

        // Manually.
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $accessmanager = $this->get_access_manager();
        $this->assertEquals(settings_provider::USE_SEB_CONFIG_MANUALLY, $accessmanager->get_seb_use_type());

        // Use template.
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $quizsettings = quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $quizsettings->set('requiresafeexambrowser', settings_provider::USE_SEB_TEMPLATE);
        $quizsettings->set('templateid', $this->create_template()->get('id'));
        $quizsettings->save();
        $accessmanager = $this->get_access_manager();
        $this->assertEquals(settings_provider::USE_SEB_TEMPLATE, $accessmanager->get_seb_use_type());

        // Use uploaded config.
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);
        $quizsettings = quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $quizsettings->set('requiresafeexambrowser', settings_provider::USE_SEB_UPLOAD_CONFIG); // Doesn't check basic header.
        $xml = file_get_contents(__DIR__ . '/fixtures/unencrypted.seb');
        $this->create_module_test_file($xml, $this->quiz->cmid);
        $quizsettings->save();
        $accessmanager = $this->get_access_manager();
        $this->assertEquals(settings_provider::USE_SEB_UPLOAD_CONFIG, $accessmanager->get_seb_use_type());

        // Use client config.
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CLIENT_CONFIG);
        $accessmanager = $this->get_access_manager();
        $this->assertEquals(settings_provider::USE_SEB_CLIENT_CONFIG, $accessmanager->get_seb_use_type());
    }

    /**
     * Data provider for self::test_should_validate_basic_header.
     *
     * @return array
     */
    public function should_validate_basic_header_data_provider() {
        return [
            [settings_provider::USE_SEB_NO, false],
            [settings_provider::USE_SEB_CONFIG_MANUALLY, false],
            [settings_provider::USE_SEB_TEMPLATE, false],
            [settings_provider::USE_SEB_UPLOAD_CONFIG, false],
            [settings_provider::USE_SEB_CLIENT_CONFIG, true],
        ];
    }

    /**
     * Test we know when we should validate basic header.
     *
     * @param int $type Type of SEB usage.
     * @param bool $expected Expected result.
     *
     * @dataProvider should_validate_basic_header_data_provider
     */
    public function test_should_validate_basic_header($type, $expected) {
        $accessmanager = $this->getMockBuilder(access_manager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_seb_use_type'])
            ->getMock();
        $accessmanager->method('get_seb_use_type')->willReturn($type);

        $this->assertEquals($expected, $accessmanager->should_validate_basic_header());

    }

    /**
     * Data provider for self::test_should_validate_config_key.
     *
     * @return array
     */
    public function should_validate_config_key_data_provider() {
        return [
            [settings_provider::USE_SEB_NO, false],
            [settings_provider::USE_SEB_CONFIG_MANUALLY, true],
            [settings_provider::USE_SEB_TEMPLATE, true],
            [settings_provider::USE_SEB_UPLOAD_CONFIG, true],
            [settings_provider::USE_SEB_CLIENT_CONFIG, false],
        ];
    }

    /**
     * Test we know when we should validate config key.
     *
     * @param int $type Type of SEB usage.
     * @param bool $expected Expected result.
     *
     * @dataProvider should_validate_config_key_data_provider
     */
    public function test_should_validate_config_key($type, $expected) {
        $accessmanager = $this->getMockBuilder(access_manager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_seb_use_type'])
            ->getMock();
        $accessmanager->method('get_seb_use_type')->willReturn($type);

        $this->assertEquals($expected, $accessmanager->should_validate_config_key());
    }

    /**
     * Data provider for self::test_should_validate_browser_exam_key.
     *
     * @return array
     */
    public function should_validate_browser_exam_key_data_provider() {
        return [
            [settings_provider::USE_SEB_NO, false],
            [settings_provider::USE_SEB_CONFIG_MANUALLY, false],
            [settings_provider::USE_SEB_TEMPLATE, false],
            [settings_provider::USE_SEB_UPLOAD_CONFIG, true],
            [settings_provider::USE_SEB_CLIENT_CONFIG, true],
        ];
    }

    /**
     * Test we know when we should browser exam key.
     *
     * @param int $type Type of SEB usage.
     * @param bool $expected Expected result.
     *
     * @dataProvider should_validate_browser_exam_key_data_provider
     */
    public function test_should_validate_browser_exam_key($type, $expected) {
        $accessmanager = $this->getMockBuilder(access_manager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_seb_use_type'])
            ->getMock();
        $accessmanager->method('get_seb_use_type')->willReturn($type);

        $this->assertEquals($expected, $accessmanager->should_validate_browser_exam_key());
    }

    /**
     * Test that access manager uses cached Config Key.
     */
    public function test_access_manager_uses_cached_config_key() {
        global $FULLME;
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_CONFIG_MANUALLY);

        $accessmanager = $this->get_access_manager();

        $configkey = $accessmanager->get_valid_config_key();

        // Set up dummy request.
        $FULLME = 'https://example.com/moodle/mod/quiz/attempt.php?attemptid=123&page=4';
        $expectedhash = hash('sha256', $FULLME . $configkey);
        $_SERVER['HTTP_X_SAFEEXAMBROWSER_CONFIGKEYHASH'] = $expectedhash;

        $this->assertTrue($accessmanager->validate_config_key());

        // Change settings (but don't save) and check that still can validate config key.
        $quizsettings = quiz_settings::get_record(['quizid' => $this->quiz->id]);
        $quizsettings->set('showsebtaskbar', 0);
        $this->assertNotEquals($quizsettings->get_config_key(), $configkey);
        $this->assertTrue($accessmanager->validate_config_key());

        // Now save settings which should purge caches but access manager still has config key.
        $quizsettings->save();
        $this->assertNotEquals($quizsettings->get_config_key(), $configkey);
        $this->assertTrue($accessmanager->validate_config_key());

        // Initialise a new access manager. Now validation should fail.
        $accessmanager = $this->get_access_manager();
        $this->assertFalse($accessmanager->validate_config_key());
    }

    /**
     * Check that valid SEB config key is null if quiz doesn't have SEB settings.
     */
    public function test_valid_config_key_is_null_if_no_settings() {
        $this->quiz = $this->create_test_quiz($this->course, settings_provider::USE_SEB_NO);
        $accessmanager = $this->get_access_manager();

        $this->assertEmpty(quiz_settings::get_record(['quizid' => $this->quiz->id]));
        $this->assertNull($accessmanager->get_valid_config_key());

    }

}
