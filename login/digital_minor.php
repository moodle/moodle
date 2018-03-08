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
 * Display page to a digital minor. Display support contact details.
 *
 * @package    core
 * @subpackage auth
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->libdir . '/authlib.php');

$authplugin = signup_is_enabled();

if (!$authplugin || !\core_auth\digital_consent::is_age_digital_consent_verification_enabled()) {
    // Redirect user if signup or digital age of consent verification is disabled.
    redirect(new moodle_url('/'), get_string('verifyagedigitalconsentnotpossible', 'error'));
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/login/digital_minor.php');

if (isloggedin() and !isguestuser()) {
    // Prevent signing up when already logged in.
    redirect(new moodle_url('/'), get_string('cannotsignup', 'error', fullname($USER)));
}

$cache = cache::make('core', 'presignup');
$isminor = $cache->get('isminor');
if ($isminor !== 'yes') {
    // Redirect when the signup session does not exists, minor check has not been done or the user is not a minor.
    redirect(new moodle_url('/login/index.php'));
}

$PAGE->navbar->add(get_string('login'));
$PAGE->navbar->add(get_string('digitalminor'));

$PAGE->set_pagelayout('login');
$PAGE->set_title(get_string('digitalminor'));
$sitename = format_string($SITE->fullname);
$PAGE->set_heading($sitename);

$page = new \core_auth\output\digital_minor_page();

echo $OUTPUT->header();
echo $OUTPUT->render($page);
echo $OUTPUT->footer();

