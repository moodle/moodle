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

namespace local_launchpad;

/**
 * Persona home routing policy tests (no DB).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\persona_home
 */
final class persona_home_test extends \basic_testcase {

    public function test_should_override_default_home_paths(): void {
        global $CFG;

        // Use PHPUnit's wwwroot — do not assign $CFG->wwwroot (Moodle forbids it).
        $www = rtrim((string) $CFG->wwwroot, '/');

        $this->assertTrue(persona_home::should_override_default_home(null));
        $this->assertTrue(persona_home::should_override_default_home(''));
        $this->assertTrue(persona_home::should_override_default_home($www . '/'));
        $this->assertTrue(persona_home::should_override_default_home($www));
        $this->assertTrue(persona_home::should_override_default_home($www . '/my/'));
        $this->assertTrue(persona_home::should_override_default_home($www . '/my'));
        $this->assertTrue(persona_home::should_override_default_home('/my/'));
        $this->assertTrue(persona_home::should_override_default_home('/my/courses.php'));
        $this->assertTrue(persona_home::should_override_default_home($www . '/my/courses.php'));
    }

    public function test_should_not_override_deep_wantsurl(): void {
        global $CFG;

        $www = rtrim((string) $CFG->wwwroot, '/');

        $this->assertFalse(persona_home::should_override_default_home(
            $www . '/course/view.php?id=3'
        ));
        $this->assertFalse(persona_home::should_override_default_home('/local/launchpad/employee.php'));
        $this->assertFalse(persona_home::should_override_default_home('/mod/assign/view.php?id=15'));
        $this->assertFalse(persona_home::should_override_default_home('https://evil.example/phish'));
    }

    public function test_home_url_for_invalid_user_is_null(): void {
        $this->assertNull(persona_home::home_url_for(0));
        $this->assertNull(persona_home::home_url_for(-1));
    }

    public function test_list_mentor_courses_invalid_user_empty(): void {
        $this->assertSame([], persona_home::list_mentor_courses(0));
    }

    public function test_path_constants(): void {
        $this->assertSame('/local/launchpad/mentor.php', persona_home::MENTOR_PATH);
        $this->assertSame('/local/launchpad/employee.php', persona_home::EMPLOYEE_PATH);
        $this->assertSame('/local/launchpad/fellow.php', persona_home::FELLOW_PATH);
    }

    public function test_is_persona_shell_path(): void {
        $this->assertTrue(persona_home::is_persona_shell_path('/local/launchpad/fellow.php'));
        $this->assertTrue(persona_home::is_persona_shell_path('/local/launchpad/employee.php'));
        $this->assertTrue(persona_home::is_persona_shell_path('/local/launchpad/mentor.php'));
        $this->assertFalse(persona_home::is_persona_shell_path('/course/view.php'));
        $this->assertFalse(persona_home::is_persona_shell_path('/my/index.php'));
    }

    public function test_should_override_includes_my_index_php(): void {
        global $CFG;
        $www = rtrim((string) $CFG->wwwroot, '/');
        $this->assertTrue(persona_home::should_override_default_home($www . '/my/index.php'));
        $this->assertTrue(persona_home::should_override_default_home('/my/index.php'));
    }

    public function test_should_rewrite_wantsurl_wrong_shell_without_user_home(): void {
        // Invalid user → no persona home → never rewrite non-default URLs.
        $this->assertFalse(persona_home::should_rewrite_wantsurl(0, '/local/launchpad/fellow.php'));
        $this->assertTrue(persona_home::should_rewrite_wantsurl(0, '/my/'));
        $this->assertTrue(persona_home::should_rewrite_wantsurl(0, null));
    }

    public function test_should_honor_noredirect_policy(): void {
        // Admins / no persona home may keep stock /my/.
        $this->assertTrue(persona_home::should_honor_noredirect(null));

        // Employees always use Employer portal — noredirect cannot bypass.
        $employeehome = new \moodle_url(persona_home::EMPLOYEE_PATH);
        $this->assertFalse(persona_home::should_honor_noredirect($employeehome));
        $this->assertFalse(persona_home::should_honor_noredirect(
            new \moodle_url('/local/launchpad/employee.php')
        ));

        // Fellows / mentors may still opt into stock /my/ via noredirect.
        $this->assertTrue(persona_home::should_honor_noredirect(
            new \moodle_url(persona_home::FELLOW_PATH)
        ));
        $this->assertTrue(persona_home::should_honor_noredirect(
            new \moodle_url(persona_home::MENTOR_PATH)
        ));
    }
}
