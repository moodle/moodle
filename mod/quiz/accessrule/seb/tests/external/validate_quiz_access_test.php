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

namespace quizaccess_seb\external;

use quizaccess_seb\seb_quiz_settings;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../test_helper_trait.php');

/**
 * PHPUnit tests for external function.
 *
 * @package    quizaccess_seb
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \quizaccess_seb\external\validate_quiz_access
 */
class validate_quiz_access_test extends \advanced_testcase {
    use \quizaccess_seb_test_helper_trait;

    /**
     * This method runs before every test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        // Generate data objects.
        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->create_test_quiz($this->course);
        $this->user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id, 'student');
        $this->setUser($this->user);
    }

    /**
     * Bad parameter provider.
     *
     * @return array
     */
    public function bad_parameters_provider(): array {
        return [
            'no params' => [
                'cmid' => null,
                'url' => null,
                'configkey' => null,
                '/Invalid parameter value detected \(Missing required key in single structure: cmid\)/'
            ],
            'no course module id' => [
                'cmid' => null,
                'url' => 'https://www.example.com/moodle',
                'configkey' => hash('sha256', 'configkey'),
                '/Invalid parameter value detected \(Missing required key in single structure: cmid\)/'
            ],
            'no url' => [
                'cmid' => 123,
                'url' => null,
                'configkey' => hash('sha256', 'configkey'),
                '/Invalid parameter value detected \(Missing required key in single structure: url\)/'
            ],
            'cmid is not an int' => [
                'cmid' => 'test',
                'url' => 'https://www.example.com/moodle',
                'configkey' => null,
                '/Invalid external api parameter: the value is "test", the server was expecting "int" type/'
            ],
            'url is not a url' => [
                'cmid' => 123,
                'url' => 123,
                'configkey' => hash('sha256', 'configkey'),
                '/Invalid external api parameter: the value is "123", the server was expecting "url" type/'
            ],
        ];
    }

    /**
     * Test exception thrown for bad parameters.
     *
     * @param mixed $cmid Course module id.
     * @param mixed $url Page URL.
     * @param mixed $configkey SEB config key.
     * @param mixed $messageregex Error message regex to check.
     *
     * @dataProvider bad_parameters_provider
     */
    public function test_invalid_parameters($cmid, $url, $configkey, $messageregex) {
        $params = [];
        if (!empty($cmid)) {
            $params['cmid'] = $cmid;
        }
        if (!empty($url)) {
            $params['url'] = $url;
        }
        if (!empty($configkey)) {
            $params['configkey'] = $configkey;
        }

        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessageMatches($messageregex);
        \core_external\external_api::validate_parameters(validate_quiz_keys::execute_parameters(), $params);
    }

    /**
     * Test that the user has permissions to access context.
     */
    public function test_context_is_not_valid_for_user() {
        // Set user as user not enrolled in course and quiz.
        $this->user = $this->getDataGenerator()->create_user();
        $this->setUser($this->user);

        $this->expectException(\require_login_exception::class);
        $this->expectExceptionMessage('Course or activity not accessible. (Not enrolled)');
        validate_quiz_keys::execute($this->quiz->cmid, 'https://www.example.com/moodle', 'configkey');
    }

    /**
     * Test exception thrown when no key provided.
     */
    public function test_no_keys_provided() {
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage('At least one Safe Exam Browser key must be provided.');
        validate_quiz_keys::execute($this->quiz->cmid, 'https://www.example.com/moodle');
    }

    /**
     * Test exception thrown if cmid doesn't match a quiz.
     */
    public function test_quiz_does_not_exist() {
        $this->setAdminUser();
        $forum = $this->getDataGenerator()->create_module('forum', ['course' => $this->course->id]);
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage('Quiz not found matching course module ID: ' . $forum->cmid);
        validate_quiz_keys::execute($forum->cmid, 'https://www.example.com/moodle', 'configkey');
    }

    /**
     * Test config key is valid.
     */
    public function test_config_key_valid() {
        $sink = $this->redirectEvents();
        // Test settings to populate the quiz.
        $settings = $this->get_test_settings([
            'quizid' => $this->quiz->id,
            'cmid' => $this->quiz->cmid,
        ]);
        $url = 'https://www.example.com/moodle';

        // Create the quiz settings.
        $quizsettings = new seb_quiz_settings(0, $settings);
        $quizsettings->save();

        $fullconfigkey = hash('sha256', $url . $quizsettings->get_config_key());
        $result = validate_quiz_keys::execute($this->quiz->cmid, $url, $fullconfigkey);
        $this->assertTrue($result['configkey']);
        $this->assertTrue($result['browserexamkey']);

        $events = $sink->get_events();
        $this->assertCount(0, $events);
    }

    /**
     * Test config key is not valid.
     */
    public function test_config_key_not_valid() {
        $sink = $this->redirectEvents();
        // Test settings to populate the quiz.
        $settings = $this->get_test_settings([
            'quizid' => $this->quiz->id,
            'cmid' => $this->quiz->cmid,
        ]);

        // Create the quiz settings.
        $quizsettings = new seb_quiz_settings(0, $settings);
        $quizsettings->save();

        $result = validate_quiz_keys::execute($this->quiz->cmid, 'https://www.example.com/moodle', 'badconfigkey');
        $this->assertFalse($result['configkey']);
        $this->assertTrue($result['browserexamkey']);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\quizaccess_seb\event\access_prevented', $event);
        $this->assertStringContainsString('Invalid SEB config key', $event->get_description());
    }

    /**
     * Test browser exam key is valid.
     */
    public function test_browser_exam_key_valid() {
        $sink = $this->redirectEvents();
        // Test settings to populate the quiz.
        $url = 'https://www.example.com/moodle';
        $validbrowserexamkey = hash('sha256', 'validbrowserexamkey');
        $settings = $this->get_test_settings([
            'quizid' => $this->quiz->id,
            'cmid' => $this->quiz->cmid,
            'requiresafeexambrowser' => \quizaccess_seb\settings_provider::USE_SEB_CLIENT_CONFIG,
            'allowedbrowserexamkeys' => $validbrowserexamkey,
        ]);

        // Create the quiz settings.
        $quizsettings = new seb_quiz_settings(0, $settings);
        $quizsettings->save();

        $fullbrowserexamkey = hash('sha256', $url . $validbrowserexamkey);
        $result = validate_quiz_keys::execute($this->quiz->cmid, $url, null, $fullbrowserexamkey);
        $this->assertTrue($result['configkey']);
        $this->assertTrue($result['browserexamkey']);
        $events = $sink->get_events();
        $this->assertCount(0, $events);
    }

    /**
     * Test browser exam key is not valid.
     */
    public function test_browser_exam_key_not_valid() {
        $sink = $this->redirectEvents();
        // Test settings to populate the quiz.
        $validbrowserexamkey = hash('sha256', 'validbrowserexamkey');
        $settings = $this->get_test_settings([
            'quizid' => $this->quiz->id,
            'cmid' => $this->quiz->cmid,
            'requiresafeexambrowser' => \quizaccess_seb\settings_provider::USE_SEB_CLIENT_CONFIG,
            'allowedbrowserexamkeys' => $validbrowserexamkey,
        ]);

        // Create the quiz settings.
        $quizsettings = new seb_quiz_settings(0, $settings);
        $quizsettings->save();

        $result = validate_quiz_keys::execute($this->quiz->cmid, 'https://www.example.com/moodle', null,
                hash('sha256', 'badbrowserexamkey'));
        $this->assertTrue($result['configkey']);
        $this->assertFalse($result['browserexamkey']);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\quizaccess_seb\event\access_prevented', $event);
        $this->assertStringContainsString('Invalid SEB browser key', $event->get_description());
    }

}
