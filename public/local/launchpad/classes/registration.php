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
 * Fellowship self-registration helpers.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_launchpad;

defined('MOODLE_INTERNAL') || die();

/**
 * Enable email self-registration and track Fellowship signup portal.
 *
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class registration {
    /** User preference: which portal the account registered for. */
    public const PREF_PORTAL = 'toba_portal';

    /** Session key while completing signup. */
    public const SESSION_PORTAL = 'toba_signup_portal';

    /** Only Fellowship may self-register. */
    public const PORTAL_FELLOWSHIP = 'fellowship';

    /**
     * Enable auth_email and set Self registration to Email-based.
     *
     * @param bool $force When true, set registerauth=email even if another value is set
     *                    but signup is currently disabled / not usable.
     */
    public static function ensure_email_self_registration(bool $force = false): void {
        global $CFG;

        require_once($CFG->libdir . '/authlib.php');

        \core\plugininfo\auth::enable_plugin('email', 1);

        $needset = empty($CFG->registerauth);
        if ($force && !signup_is_enabled()) {
            $needset = true;
        }
        if ($force && !empty($CFG->registerauth) && $CFG->registerauth !== 'email') {
            // Fellowship self-register requires auth_email.
            $needset = true;
        }

        if ($needset) {
            set_config('registerauth', 'email');
            $CFG->registerauth = 'email';
            // Config cache can lag; purge the application config cache entry.
            if (class_exists('\cache')) {
                \cache_helper::purge_by_definition('core', 'config');
            }
        }
    }

    /**
     * Normalise a portal query/value to fellowship or empty.
     *
     * @param string $portal Raw portal value.
     * @return string
     */
    public static function normalise_portal(string $portal): string {
        $portal = strtolower(trim($portal));
        if ($portal === 'fellow' || $portal === 'fellowship') {
            return self::PORTAL_FELLOWSHIP;
        }
        return $portal;
    }

    /**
     * Stash Fellowship portal on the session for signup + confirmation.
     */
    public static function prepare_signup_session(): void {
        global $SESSION;

        $rawportal = optional_param('portal', '', PARAM_ALPHA);
        $portal = self::normalise_portal($rawportal);
        if ($portal === '' && !empty($SESSION->{self::SESSION_PORTAL})) {
            $portal = self::normalise_portal((string) $SESSION->{self::SESSION_PORTAL});
        }
        // Self-registration is Fellowship-only.
        if ($portal !== self::PORTAL_FELLOWSHIP) {
            $portal = self::PORTAL_FELLOWSHIP;
        }
        $SESSION->{self::SESSION_PORTAL} = $portal;

        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = (new \moodle_url('/my/'))->out(false);
        }

        // Keep ?portal=fellowship on the signup URL for bookmarks / cancel flows (GET only).
        if ($rawportal === '' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect(new \moodle_url('/login/signup.php', ['portal' => self::PORTAL_FELLOWSHIP]));
        }
    }

    /**
     * Public Fellowship registration entry URL (enables signup, then Moodle form).
     *
     * Always returns the LaunchPad register entry so the CTA works even before
     * Self registration has been enabled in site config.
     *
     * @return string
     */
    public static function fellowship_signup_url(): string {
        return (new \moodle_url('/local/launchpad/register.php'))->out(false);
    }

    /**
     * Persist portal preference after the Moodle user row exists.
     *
     * @param int $userid New user id.
     */
    public static function apply_portal_preference(int $userid): void {
        global $SESSION;

        if ($userid <= 0) {
            return;
        }

        $portal = self::PORTAL_FELLOWSHIP;
        if (!empty($SESSION->{self::SESSION_PORTAL})) {
            $portal = self::normalise_portal((string) $SESSION->{self::SESSION_PORTAL});
        }
        if ($portal !== self::PORTAL_FELLOWSHIP) {
            $portal = self::PORTAL_FELLOWSHIP;
        }

        set_user_preference(self::PREF_PORTAL, $portal, $userid);
        unset($SESSION->{self::SESSION_PORTAL});
    }
}
