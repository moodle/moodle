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

namespace core;

/**
 * Tests for outputlib.php.
 *
 * @package    core
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversFunction('theme_build_editor_css_for_themes')]
final class outputlib_test extends \advanced_testcase {
    /**
     * Test that the editor content CSS is built and cached without changing the theme revisions.
     */
    public function test_theme_build_editor_css_for_themes(): void {
        global $CFG;

        $this->resetAfterTest();
        require_once("{$CFG->libdir}/outputlib.php");

        $themerevision = theme_get_revision();
        $subrevision = theme_get_sub_revision_for_theme('boost');
        $theme = \theme_config::load('boost');

        $built = theme_build_editor_css_for_themes([$theme], ['ltr']);

        $this->assertSame(['boost'], array_keys($built));
        $this->assertSame(['ltr'], array_keys($built['boost']));
        $this->assertNotEmpty($built['boost']['ltr']);
        $this->assertSame($themerevision, theme_get_revision());
        $this->assertSame($subrevision, theme_get_sub_revision_for_theme('boost'));

        $theme->force_svg_use(true);
        $theme->set_rtl_mode(false);
        $this->assertSame($built['boost']['ltr'], $theme->get_editor_css_cached_content());

        // The other direction was not built.
        $theme->set_rtl_mode(true);
        $this->assertFalse($theme->get_editor_css_cached_content());
    }

    /**
     * Test that nothing is cached when caching is disabled.
     */
    public function test_theme_build_editor_css_for_themes_without_cache(): void {
        global $CFG;

        $this->resetAfterTest();
        require_once("{$CFG->libdir}/outputlib.php");

        $theme = \theme_config::load('boost');
        $built = theme_build_editor_css_for_themes([$theme], ['ltr'], false);

        $this->assertNotEmpty($built['boost']['ltr']);
        $theme->set_rtl_mode(false);
        $this->assertFalse($theme->get_editor_css_cached_content());
    }
}
