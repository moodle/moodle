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
 * The maintenance layout.
 *
 * @package   theme_eguru
 * @copyright 2015 LMSACE Dev Team,lmsace.com
 * @author    LMSACE Dev Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

$footerlogo = !empty(theme_eguru_get_setting('footerblklogo')) ? 1 : 0;

$footnote = !empty(theme_eguru_get_setting('footnote')) ? theme_eguru_get_setting('footnote', 'format_html') : '';
$footnote = theme_eguru_lang($footnote);

$footerbtitle2 = !empty(theme_eguru_get_setting('footerbtitle2')) ? theme_eguru_get_setting('footerbtitle2', 'format_html') : '';
$footerbtitle2 = theme_eguru_lang($footerbtitle2);

$footerbtitle3 = !empty(theme_eguru_get_setting('footerbtitle3')) ? theme_eguru_get_setting('footerbtitle3', 'format_html') : '';
$footerbtitle3 = theme_eguru_lang($footerbtitle3);

$footerbtitle4 = !empty(theme_eguru_get_setting('footerbtitle4')) ? theme_eguru_get_setting('footerbtitle4', 'format_html') : '';
$footerbtitle4 = theme_eguru_lang($footerbtitle4);

$footerlinks = theme_eguru_generate_links('footerblink2');
$logourl = theme_eguru_get_logo_url();

$fburl    = theme_eguru_get_setting('fburl');
$fburl    = trim($fburl);
$twurl    = theme_eguru_get_setting('twurl');
$twurl    = trim($twurl);
$gpurl    = theme_eguru_get_setting('gpurl');
$gpurl    = trim($gpurl);
$pinurl   = theme_eguru_get_setting('pinurl');
$pinurl   = trim($pinurl);

$socialurl = ($fburl != '' || $pinurl != '' || $twurl != '' || $gpurl != '') ? 1 : 0;
$fb = get_string('mediaicon1', 'theme_eguru');
$tw = get_string('mediaicon2', 'theme_eguru');
$gp = get_string('mediaicon3', 'theme_eguru');
$pi = get_string('mediaicon4', 'theme_eguru');

$fbn = get_string('medianame1', 'theme_eguru');
$twn = get_string('medianame2', 'theme_eguru');
$gpn = get_string('medianame3', 'theme_eguru');
$pin = get_string('medianame4', 'theme_eguru');

$address = theme_eguru_get_setting('address') ? theme_eguru_get_setting('address') : '';
$emailid  = theme_eguru_get_setting('emailid');
$phoneno  = theme_eguru_get_setting('phoneno');
$mail = get_string('footeremail', 'theme_eguru');
$phone = get_string('phone', 'theme_eguru');

$copyright = theme_eguru_get_setting('copyright', 'format_html');


$block1 = ($footerlogo != '' || $footnote != '') ? 1 : 0;
$block2 = ($footerbtitle2 != '' || $footerlinks != '') ? 1 : 0;
$block3 = ($footerbtitle3 != '' || $socialurl != 0) ? 1 : 0;
$block4 = ($address != '' || $emailid != '' || $phoneno != '') ? 1 : 0;

$blockarrange = $block1 + $block2 + $block3 + $block4;

switch ($blockarrange) {
    case 4:
        $colclass = 'col-md-3';
        break;
    case 3:
        $colclass = 'col-md-4';
        break;
    case 2:
        $colclass = 'col-md-6';
        break;
    case 1:
        $colclass = 'col-md-12';
        break;
    case 0:
        $colclass = '';
        break;
    default:
        $colclass = 'col-md-3';
    break;
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),

    'footerlogo' => $footerlogo,
    'footnote' => $footnote,
    'footerbtitle2' => $footerbtitle2,
    'footerbtitle3' => $footerbtitle3,
    'footerbtitle4' => $footerbtitle4,
    'footerlinks' => $footerlinks,
    'logourl' => $logourl,
    'fburl' => $fburl,
    'pinurl' => $pinurl,
    'twurl' => $twurl,
    'gpurl' => $gpurl,
    'fb' => $fb,
    'pi' => $pi,
    'tw' => $tw,
    'gp' => $gp,
    'fbn' => $fbn,
    'pin' => $pin,
    'twn' => $twn,
    'gpn' => $gpn,
    'socialurl' => $socialurl,
    'address' => $address,
    'phoneno' => $phoneno,
    'emailid' => $emailid,
    'phone' => $phone,
    'mail' => $mail,
    'copyright' => $copyright,
    'block1' => $block1,
    'block2' => $block2,
    'block3' => $block3,
    'block4' => $block4,
    'colclass' => $colclass,
    'blockarrange' => $blockarrange
    ];

    $templatecontext['flatnavigation'] = $PAGE->flatnav;
    $footerlayout = $OUTPUT->render_from_template('theme_eguru/footer', $templatecontext);