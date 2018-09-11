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
 * Header layout
 * @package    theme_academi
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author   LMSACE Dev Team
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


$custommenu = $OUTPUT->custom_menu();

if ($custommenu == "") {
    $navbarclass = "navbar-toggler hidden-lg-up nocontent-navbar";
} else {
    $navbarclass = "navbar-toggler hidden-lg-up";
}
// Header Content.
$logourl = get_logo_url();
$phoneno = theme_academi_get_setting('phoneno');
$emailid = theme_academi_get_setting('emailid');
$scallus = get_string('callus', 'theme_academi');
$semail = get_string('email', 'theme_academi');

// Footer Content.
$logourl = get_logo_url();
$footnote = theme_academi_get_setting('footnote', 'format_html');
$fburl = theme_academi_get_setting('fburl');
$pinurl = theme_academi_get_setting('pinurl');
$twurl = theme_academi_get_setting('twurl');
$gpurl = theme_academi_get_setting('gpurl');
$address = theme_academi_get_setting('address');
$emailid = theme_academi_get_setting('emailid');
$phoneno = theme_academi_get_setting('phoneno');
$copyrightfooter = theme_academi_get_setting('copyright_footer');
$infolink = theme_academi_get_setting('infolink');
$sinfo = get_string('info', 'theme_academi');
$scontactus = get_string('contact_us', 'theme_academi');
$phone = get_string('phone', 'theme_academi');
$email = get_string('email', 'theme_academi');
$sfollowus = get_string('followus', 'theme_academi');
$url = ($fburl != '' || $pinurl != '' || $twurl != '' || $gpurl != '') ? 1 : 0;
$infolink = theme_academi_infolink();


$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    "logourl" => $logourl,
    "phoneno" => $phoneno,
    "emailid" => $emailid,
    "s_callus" => $scallus,
    "s_email" => $semail,
    "logourl" => $logourl,
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
    "phone" => $phone,
    "email" => $email,
    "s_followus" => $sfollowus,
    "url" => $url,
    "infolink" => $infolink,
    "navbarclass" => $navbarclass,
];


$templatecontext['flatnavigation'] = $PAGE->flatnav;
$flatnavbar = $OUTPUT->render_from_template('theme_boost/nav-drawer', $templatecontext);
echo $OUTPUT->render_from_template('theme_academi/header', $templatecontext);

