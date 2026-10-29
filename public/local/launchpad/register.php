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
 * Fellowship registration entry — enables email signup, then opens Moodle signup.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/authlib.php');

global $CFG, $SESSION;

// Guests / visitors only.
if (isloggedin() && !isguestuser()) {
    redirect(new moodle_url('/my/'));
}

// Load helper without relying on a possibly stale component classmap.
require_once(__DIR__ . '/classes/registration.php');

\local_launchpad\registration::ensure_email_self_registration(true);

if (!signup_is_enabled()) {
    // Last-resort direct write if helper could not enable signup.
    \core\plugininfo\auth::enable_plugin('email', 1);
    set_config('registerauth', 'email');
    $CFG->registerauth = 'email';
}

if (!signup_is_enabled()) {
    throw new \moodle_exception(
        'notlocalisederrormessage',
        'error',
        new moodle_url('/login/index.php', ['portal' => 'fellowship']),
        'Fellowship registration is unavailable. Ask an admin to set Self registration to Email-based self-registration.'
    );
}

$SESSION->toba_signup_portal = 'fellowship';
if (empty($SESSION->wantsurl)) {
    $SESSION->wantsurl = (new moodle_url('/my/'))->out(false);
}

redirect(new moodle_url('/login/signup.php', ['portal' => 'fellowship']));
