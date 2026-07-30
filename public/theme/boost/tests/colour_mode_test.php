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

namespace theme_boost;

/**
 * Unit tests for the colour mode helper.
 *
 * @package   theme_boost
 * @copyright 2026 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(colour_mode::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(hook_listener::class)]
final class colour_mode_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        set_config('enablecolourmodes', 1, 'theme_boost');
    }

    /**
     * The site default is used by people who have not chosen a mode of their own.
     */
    public function test_get_current_mode_falls_back_to_site_default(): void {
        $this->setUser($this->getDataGenerator()->create_user());

        set_config('defaultcolourmode', colour_mode::DARK, 'theme_boost');
        $this->assertEquals(colour_mode::DARK, colour_mode::get_current_mode());

        set_config('defaultcolourmode', colour_mode::LIGHT, 'theme_boost');
        $this->assertEquals(colour_mode::LIGHT, colour_mode::get_current_mode());
    }

    /**
     * Auto is used when the site default is missing or has been given a value which is not a colour mode.
     */
    public function test_get_site_default_with_invalid_config(): void {
        unset_config('defaultcolourmode', 'theme_boost');
        $this->assertEquals(colour_mode::AUTO, colour_mode::get_site_default());

        set_config('defaultcolourmode', 'chartreuse', 'theme_boost');
        $this->assertEquals(colour_mode::AUTO, colour_mode::get_site_default());
    }

    /**
     * The user preference wins over the site default.
     */
    public function test_get_current_mode_uses_user_preference(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        set_config('defaultcolourmode', colour_mode::LIGHT, 'theme_boost');

        set_user_preference(colour_mode::PREFERENCE, colour_mode::DARK, $user);

        $this->assertEquals(colour_mode::DARK, colour_mode::get_current_mode());
    }

    /**
     * A preference which is not a colour mode is ignored rather than written to the html tag.
     */
    public function test_get_current_mode_ignores_invalid_user_preference(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        set_config('defaultcolourmode', colour_mode::LIGHT, 'theme_boost');

        set_user_preference(colour_mode::PREFERENCE, 'chartreuse', $user);

        $this->assertEquals(colour_mode::LIGHT, colour_mode::get_current_mode());
    }

    /**
     * Turning colour modes off pins the site to light mode, whatever anybody has chosen.
     */
    public function test_disabled_colour_modes(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        set_user_preference(colour_mode::PREFERENCE, colour_mode::DARK, $user);
        set_config('enablecolourmodes', 0, 'theme_boost');

        $this->assertFalse(colour_mode::is_enabled());
        $this->assertFalse(colour_mode::can_choose_mode());
        $this->assertEquals(colour_mode::LIGHT, colour_mode::get_current_mode());
    }

    /**
     * Guests and users who are not logged in cannot store a preference, so they get the site default.
     */
    public function test_can_choose_mode(): void {
        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertTrue(colour_mode::can_choose_mode());

        $this->setGuestUser();
        $this->assertFalse(colour_mode::can_choose_mode());

        $this->setUser(null);
        $this->assertFalse(colour_mode::can_choose_mode());
    }

    /**
     * The html tag carries the resolved mode, plus the chosen mode so that the browser can resolve "auto".
     */
    public function test_html_attributes(): void {
        global $PAGE;

        $this->setUser($this->getDataGenerator()->create_user());
        $PAGE->set_url('/');
        $PAGE->force_theme('boost');
        set_config('defaultcolourmode', colour_mode::DARK, 'theme_boost');

        $hook = new \core\hook\output\before_html_attributes($PAGE->get_renderer('core'));
        hook_listener::before_html_attributes_listener($hook);

        $this->assertEquals([
            'data-bs-theme' => colour_mode::DARK,
            'data-colourmode' => colour_mode::DARK,
        ], $hook->get_attributes());
    }

    /**
     * Auto renders as light on the server, and is corrected in the browser against the device colour scheme.
     */
    public function test_html_attributes_for_auto(): void {
        global $PAGE;

        $this->setUser($this->getDataGenerator()->create_user());
        $PAGE->set_url('/');
        $PAGE->force_theme('boost');
        set_config('defaultcolourmode', colour_mode::AUTO, 'theme_boost');

        $hook = new \core\hook\output\before_html_attributes($PAGE->get_renderer('core'));
        hook_listener::before_html_attributes_listener($hook);

        $this->assertEquals([
            'data-bs-theme' => colour_mode::LIGHT,
            'data-colourmode' => colour_mode::AUTO,
        ], $hook->get_attributes());
    }

    /**
     * Test `colour_mode::is_boost_theme()` Boost theme. We can only test this method on Boost for now since Moodle now only ships
     * Boost as the default theme.
     */
    public function test_is_boost_theme(): void {
        global $PAGE;

        $PAGE->set_url('/');
        $PAGE->force_theme('boost');
        $this->assertTrue(colour_mode::is_boost_theme());
    }

    /**
     * The switcher is only rendered for people who can store a choice, since nobody else can act on it.
     */
    public function test_render_menu(): void {
        global $PAGE;

        $PAGE->set_url('/');
        $PAGE->force_theme('boost');
        $output = $PAGE->get_renderer('core');

        $this->setUser($this->getDataGenerator()->create_user());
        $this->assertStringContainsString('colourmode-menu', colour_mode::render_menu($output));

        $this->setGuestUser();
        $this->assertSame('', colour_mode::render_menu($output));

        $this->setUser(null);
        $this->assertSame('', colour_mode::render_menu($output));

        $this->setUser($this->getDataGenerator()->create_user());
        set_config('enablecolourmodes', 0, 'theme_boost');
        $this->assertSame('', colour_mode::render_menu($output));
    }

    /**
     * With colour modes turned off the html tag is left exactly as it was before the feature existed.
     */
    public function test_html_attributes_when_disabled(): void {
        global $PAGE;

        $this->setUser($this->getDataGenerator()->create_user());
        $PAGE->set_url('/');
        $PAGE->force_theme('boost');
        set_config('enablecolourmodes', 0, 'theme_boost');

        $hook = new \core\hook\output\before_html_attributes($PAGE->get_renderer('core'));
        hook_listener::before_html_attributes_listener($hook);

        $this->assertSame([], $hook->get_attributes());
    }
}
