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
 * header.php
 *
 * @package    theme_klass
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
} else {
    $navdraweropen = false;
}
$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
// Header content.
$logourl = get_logo_url();
$surl = new moodle_url('/course/search.php');
if (! $PAGE->url->compare($surl, URL_MATCH_BASE)) {
    $compare = 1;
} else {
    $compare = 0;
}
$surl = new moodle_url('/course/search.php');
$ssearchcourses = get_string('searchcourses');
$shome = get_string('home', 'theme_klass');

$custom = $OUTPUT->custom_menu();

if ($custom == '') {
    $class = "navbar-toggler hidden-lg-up nocontent-navbar";
} else {
    $class = "navbar-toggler hidden-lg-up";
}


// Footer Content.
$logourlfooter = get_logo_url('footer');
$footnote = theme_klass_get_setting('footnote', 'format_html');
$fburl    = theme_klass_get_setting('fburl');
$pinurl   = theme_klass_get_setting('pinurl');
$twurl    = theme_klass_get_setting('twurl');
$gpurl    = theme_klass_get_setting('gpurl');
$address  = theme_klass_get_setting('address');
$emailid  = theme_klass_get_setting('emailid');
$phoneno  = theme_klass_get_setting('phoneno');
$copyrightfooter = theme_klass_get_setting('copyright_footer');
$infolink = theme_klass_get_setting('infolink');
$sinfo = get_string('info', 'theme_klass');
$scontactus = get_string('contact_us', 'theme_klass');
$sphone = get_string('phone', 'theme_klass');
$semail = get_string('email', 'theme_klass');
$sgetsocial = get_string('get_social', 'theme_klass');
$infolink = theme_klass_infolink();


$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    "surl" => $surl,
    "s_searchcourses" => $ssearchcourses,
    "s_home" => $shome,
    "logourl" => $logourl,
    "compare" => $compare,
    "logourl_footer" => $logourlfooter,
    "footnote" => $footnote,
    "fburl" => $fburl,
    "pinurl" => $pinurl,
    "twurl" => $twurl,
    "gpurl" => $gpurl,
    "address" => $address,
    "emailid" => $emailid,
    "phoneno" => $phoneno,
    "copyright_footer" => $copyrightfooter,
    "infolink" => $infolink,
    "s_info" => $sinfo,
    "s_contact_us" => $scontactus,
    "s_phone" => $sphone,
    "s_email" => $semail,
    "s_get_social" => $sgetsocial,
    "customclass" => $class
];

$templatecontext['flatnavigation'] = $PAGE->flatnav;
$flatnavbar = $OUTPUT->render_from_template('theme_boost/nav-drawer', $templatecontext);
echo $OUTPUT->render_from_template('theme_klass/header', $templatecontext);