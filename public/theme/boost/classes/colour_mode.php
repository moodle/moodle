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
 * Helper for the Boost light and dark colour modes.
 *
 * Boost renders the colour mode using the Bootstrap 5.3 colour modes API. The resolved mode is written to the
 * data-bs-theme attribute of the html tag, and the mode chosen by the user is written to data-colourmode so that
 * the "auto" mode can be resolved in the browser.
 *
 * The mode is stored as a user preference, and mirrored into a cookie of the same name so that a page which nobody
 * is logged in to can be rendered in it. The preference is authoritative whenever it can be read.
 *
 * @package    theme_boost
 * @copyright  2026 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class colour_mode {
    /** @var string Always use the light colour mode. */
    public const LIGHT = 'light';

    /** @var string Always use the dark colour mode. */
    public const DARK = 'dark';

    /** @var string Follow the colour scheme reported by the device or browser. */
    public const AUTO = 'auto';

    /**
     * @var string The name of the user preference storing the chosen colour mode.
     *
     * The cookie which mirrors it for pages where no preference can be read carries the same name.
     */
    public const PREFERENCE = 'theme_boost_colourmode';

    /** @var array<string, string> The Font Awesome icon representing each colour mode. */
    private const ICONS = [
        self::LIGHT => 'fa-sun',
        self::DARK => 'fa-moon',
        self::AUTO => 'fa-circle-half-stroke',
    ];

    /**
     * The colour modes a user can choose from.
     *
     * @return string[]
     */
    public static function get_modes(): array {
        return [self::LIGHT, self::DARK, self::AUTO];
    }

    /**
     * Whether a value is one of the supported colour modes.
     *
     * @param string|null $mode The value to check.
     * @return bool
     */
    public static function is_valid_mode(?string $mode): bool {
        return $mode !== null && in_array($mode, self::get_modes(), true);
    }

    /**
     * Whether users are allowed to switch between colour modes on this site.
     *
     * Colour modes are an experimental feature which a site opts in to, so they are off until an admin turns them on.
     * A Behat run started with --colourmode turns them on for the run, as the option exists in order to exercise the
     * whole suite in one mode.
     *
     * @return bool
     */
    public static function is_enabled(): bool {
        // The output hooks that this method gates run on the installer's own pages, where there is no configuration to read yet:
        // get_config() throws until the database has been installed, and that exception is how core detects that it
        // still needs to be. Every other entry point to this class goes through here.
        if (during_initial_install()) {
            return false;
        }

        if (
            defined('BEHAT_SITE_RUNNING')
            && function_exists('behat_get_colour_mode')
            && self::is_valid_mode(behat_get_colour_mode())
        ) {
            return true;
        }

        return (bool) get_config('theme_boost', 'enablecolourmodes');
    }

    /**
     * The colour mode this browser was last given by somebody who could choose one.
     *
     * A page which nobody is logged in to has no preference to read, so without this the login page, and everything
     * else seen while logged out, would revert to the site default: choose dark, log out, and the login page is
     * white. The cookie is written by the browser rather than by PHP, because the mode can be changed without
     * loading a page and the copy has to keep up; see the colourmode AMD module.
     *
     * The value comes from the browser and so is not trusted: anything which is not a mode this theme knows about is
     * discarded.
     *
     * @return string|null The stored mode, or null when there is nothing usable to read.
     */
    protected static function get_browser_mode(): ?string {
        // A run started with --colourmode is sweeping the whole suite in one mode, so a cookie left by a scenario
        // which logged somebody in would quietly render the pages after it in another. Each scenario gets a fresh
        // browser session, so a cookie cannot reach the next one and a run which forces nothing can be trusted.
        if (
            defined('BEHAT_SITE_RUNNING')
            && function_exists('behat_get_colour_mode')
            && self::is_valid_mode(behat_get_colour_mode())
        ) {
            return null;
        }

        // A request can present this as an array, so the type is checked before the value is.
        $mode = $_COOKIE[self::PREFERENCE] ?? null;
        if (!is_string($mode) || !self::is_valid_mode($mode)) {
            return null;
        }

        return $mode;
    }

    /**
     * The attributes the browser copy of the colour mode is stored with.
     *
     * Decided here, rather than in the browser, so that the cookie follows the same configuration as the ones core
     * sets: the site's cookie path, domain and secure settings.
     *
     * @return string The attribute list to append to the cookie, beginning with a separator.
     */
    public static function get_cookie_attributes(): string {
        global $CFG;

        $attributes = [
            'Max-Age=' . YEARSECS,
            // Only ever set on a page of this site, and only ever read when one is loaded.
            'Path=' . ($CFG->sessioncookiepath ?? '/'),
            'SameSite=Lax',
        ];

        if (!empty($CFG->sessioncookiedomain)) {
            $attributes[] = 'Domain=' . $CFG->sessioncookiedomain;
        }

        if (is_moodle_cookie_secure()) {
            $attributes[] = 'Secure';
        }

        return '; ' . implode('; ', $attributes);
    }

    /**
     * The colour mode used for users who have not chosen one.
     *
     * A Behat run started with --colourmode takes precedence over the site setting, so that the whole suite can be
     * exercised in one mode without every feature having to set a user preference. A preference set by a scenario
     * still wins, so a feature can pin itself to the mode it is about.
     *
     * @return string One of the self::LIGHT, self::DARK or self::AUTO constants.
     */
    public static function get_site_default(): string {
        // Reached through is_enabled() in every path there is today, so this only guards against a future caller
        // reaching it directly on an installer page, where get_config() would throw.
        if (during_initial_install()) {
            return self::LIGHT;
        }

        if (defined('BEHAT_SITE_RUNNING')) {
            $behatmode = behat_get_colour_mode();
            if (self::is_valid_mode($behatmode)) {
                return $behatmode;
            }
        }

        $default = get_config('theme_boost', 'defaultcolourmode');
        return self::is_valid_mode($default) ? $default : self::AUTO;
    }

    /**
     * The colour mode to render the page with.
     *
     * The user preference is authoritative whenever it can be read. Where it cannot, the mode this browser was last
     * given stands in for it, so that logging out does not change the colours. Falls back to the site default when
     * neither is available, and to light mode when colour modes have not been turned on for the site.
     *
     * @return string One of the self::LIGHT, self::DARK or self::AUTO constants.
     */
    public static function get_current_mode(): string {
        if (!self::is_enabled()) {
            return self::LIGHT;
        }

        if (self::can_choose_mode()) {
            $preference = get_user_preferences(self::PREFERENCE);
            if (self::is_valid_mode($preference)) {
                return $preference;
            }
        } else {
            $browsermode = self::get_browser_mode();
            if ($browsermode !== null) {
                return $browsermode;
            }
        }

        return self::get_site_default();
    }

    /**
     * Whether the current user can choose their own colour mode.
     *
     * Guests and users who are not logged in cannot store user preferences, so they always get the site default.
     *
     * @return bool
     */
    public static function can_choose_mode(): bool {
        return self::is_enabled() && isloggedin() && !isguestuser();
    }

    /**
     * Whether the colour mode features apply to the theme currently in use.
     *
     * The hook callbacks in this plugin are called whatever the current theme is, so they must check that Boost is
     * actually involved in rendering the page.
     *
     * @return bool
     */
    public static function is_boost_theme(): bool {
        global $PAGE;

        $theme = $PAGE->theme;
        return $theme->name === 'boost' || in_array('boost', $theme->parents, true);
    }

    /**
     * Render the navbar menu for switching between the colour modes.
     *
     * Nothing is rendered when colour modes are not turned on for the site, or for people who cannot store a user
     * preference (guests and users who are not logged in), as they always get the site default.
     *
     * @param \renderer_base $output The renderer to render the menu with.
     * @return string HTML for the colour mode menu, or an empty string.
     */
    public static function render_menu(\renderer_base $output): string {
        if (!self::can_choose_mode()) {
            return '';
        }

        $current = self::get_current_mode();
        $modes = [];
        foreach (self::get_modes() as $mode) {
            $label = get_string('colourmode:' . $mode, 'theme_boost');
            $modes[] = [
                'mode' => $mode,
                'label' => $label,
                'icon' => self::ICONS[$mode],
                'togglelabel' => get_string('colourmodeselected', 'theme_boost', $label),
                'isactive' => $mode === $current,
            ];
        }

        return $output->render_from_template('theme_boost/colour_mode_menu', [
            'cookieattributes' => self::get_cookie_attributes(),
            'currenticon' => self::ICONS[$current],
            'currenttogglelabel' => get_string(
                'colourmodeselected',
                'theme_boost',
                get_string('colourmode:' . $current, 'theme_boost'),
            ),
            'modes' => $modes,
        ]);
    }
}
