<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\task;

/**
 * Tests for the task that builds and caches all installed themes.
 *
 * @package    core
 * @copyright  2026 Catalyst IT Australia Pty Ltd
 * @author     Cameron Ball <cameronball@catalyst-au.net>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(build_installed_themes_task::class)]
final class build_installed_themes_task_test extends \advanced_testcase {
    /**
     * Test that executing the task builds and caches CSS for every installed theme.
     */
    public function test_execute(): void {
        global $CFG;

        $this->resetAfterTest();
        require_once("{$CFG->libdir}/outputlib.php");

        theme_reset_all_caches();
        $themenames = array_keys(\core_component::get_plugin_list('theme'));

        $task = new build_installed_themes_task();
        $task->execute();

        $themerevision = theme_get_revision();
        foreach ($themenames as $themename) {
            $theme = \theme_config::load($themename);
            $theme->force_svg_use(true);
            $themesubrevision = theme_get_sub_revision_for_theme($themename);

            foreach (['rtl', 'ltr'] as $direction) {
                $theme->set_rtl_mode($direction === 'rtl');
                $cachedcss = $theme->get_css_cached_content();
                $filename = theme_get_css_filename($themename, $themerevision, $themesubrevision, $direction);

                $this->assertNotFalse($cachedcss, "CSS for {$themename} ({$direction}) was not cached.");
                $this->assertFileExists($filename);
                $this->assertSame($cachedcss, file_get_contents($filename));
            }
        }
    }
}
