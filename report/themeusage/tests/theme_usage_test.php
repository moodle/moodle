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

namespace report_themeusage;

use testing_data_generator;
use core\output\theme_usage;

/**
 * Unit tests for theme usage.
 *
 * @package    report_themeusage
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_usage_test extends \advanced_testcase {

    /** @var testing_data_generator Data generator. */
    private testing_data_generator $generator;

    /**
     * Set up function for tests.
     */
    protected function setUp(): void {
        parent::setUp();

        $this->resetAfterTest();
        $this->generator = $this->getDataGenerator();
    }

    /**
     * Test is_theme_used_in_any_context method.
     *
     * @covers ::is_theme_used_in_any_context
     * @covers ::theme_purge_used_in_context_caches
     */
    public function test_is_theme_used_in_any_context(): void {
        // Enable theme overrides.
        set_config('allowuserthemes', 1);
        set_config('allowcoursethemes', 1);
        set_config('allowcohortthemes', 1);
        set_config('allowcategorythemes', 1);

        $theme = 'boost';

        // Check there are no contexts using 'boost' as their preferred theme yet.
        $usedinanycontext = theme_usage::is_theme_used_in_any_context($theme);
        $this->assertEquals(theme_usage::THEME_IS_NOT_USED, $usedinanycontext);

        // Create a user and set its theme preference to 'boost'.
        // The outcome of this test should be the same if we use a cohort/course/category.
        $this->generator->create_user(['theme' => $theme]);

        // Because we have already checked and cached a response, purge this cache.
        theme_purge_used_in_context_caches();

        $usedinanycontext = theme_usage::is_theme_used_in_any_context($theme);
        $this->assertEquals(theme_usage::THEME_IS_USED, $usedinanycontext);

        // Double-check the the cache is set for the theme.
        $cache = \cache::make('core', 'theme_usedincontext')->get($theme);
        $this->assertEquals(theme_usage::THEME_IS_USED, $cache);
    }

    /**
     * Test the deleting of cache using theme_delete_used_in_context_cache.
     *
     * @covers ::theme_delete_used_in_context_cache
     */
    public function test_theme_delete_used_in_context_cache(): void {
        // Enable theme override.
        set_config('allowuserthemes', 1);

        // Create a user and set its theme preference to 'boost'.
        $theme = 'boost';
        $user = $this->generator->create_user(['theme' => $theme]);

        // Check for theme usage. This will create a cached result.
        theme_usage::is_theme_used_in_any_context($theme);
        $cache = \cache::make('core', 'theme_usedincontext')->get($theme);
        $this->assertEquals(theme_usage::THEME_IS_USED, $cache);

        // Delete the cache by switching themes.
        theme_delete_used_in_context_cache('classic', $user->theme);
        $cache = \cache::make('core', 'theme_usedincontext')->get($theme);
        $this->assertFalse($cache);
    }
}
