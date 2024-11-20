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
 * Footer layout
 * @package    theme_academi
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot."/theme/academi/classes/helper.php");

/**
 * Return the soical media content for the theme academi footer.
 *
 * @return array $social.
 */
function socialmedia() {
    $numofsocialmedia = theme_academi_get_setting('numofsocialmedia');
    $social = [];
    for ($sm = 1; $sm <= $numofsocialmedia; $sm++) {
        $status = theme_academi_get_setting('socialmedia'.$sm.'_status');
        $icon = theme_academi_get_setting('socialmedia'.$sm.'_icon');
        $sicon = (!empty($icon)) ? $icon : '';
        $url = theme_academi_get_setting('socialmedia'.$sm.'_url');
        $iconcolorval = theme_academi_get_setting('socialmedia'.$sm.'_iconcolor');
        $iconcolor = (!empty($iconcolorval)) ? $iconcolorval : '';
        $socialmedia[] = [
            'socialstatus' => $status,
            'sicon' => $sicon,
            'surl' => $url,
            'siconcolor' => $iconcolor,
            'sno' => $sm,
        ];
        $social['socialmedia'] = $socialmedia;
    }
    return $social;
}

/**
 * Manage the footer content for the theme academi footer.
 *
 * @return array $templatecontext footer template contents.
 */
function footer() {
    global $OUTPUT, $CFG, $USER;
    $footerlogourl = theme_academi_get_logo_url('footer');
    $footlogostatus = theme_academi_get_setting('footlogostatus');
    $footerbgimg = theme_academi_get_setting('footerbgimg', 'file');
    $footerbgimgclass = (!empty($footerbgimg)) ? 'footer-image' : '';
    $footnote = theme_academi_lang(theme_academi_get_setting('footnote', 'format_html'));
    $infolink = $OUTPUT->footer_infolinks();
    $address = theme_academi_get_setting('address');
    $emailid = theme_academi_get_setting('emailid');
    $phoneno = theme_academi_get_setting('phoneno');
    $copyrightfooter = theme_academi_get_setting('copyright_footer', 'format_html');
    $fstatus1 = theme_academi_get_setting('footerb1_status');
    $fstatus2 = theme_academi_get_setting('footerb2_status');
    $fstatus3 = theme_academi_get_setting('footerb3_status');
    $fstatus4 = theme_academi_get_setting('footerb4_status');

    $ftitle1 = theme_academi_get_setting('footerbtitle1');
    $ftitle2 = theme_academi_get_setting('footerbtitle2');
    $ftitle3 = theme_academi_get_setting('footerbtitle3');
    $ftitle4 = theme_academi_get_setting('footerbtitle4');

    $phone = get_string('phone', 'theme_academi');
    $email = get_string('emailid', 'theme_academi');

    $backtotopbtn = theme_academi_get_setting('backToTop_status');

    $totalstatus = $fstatus1 + $fstatus2 + $fstatus3 + $fstatus4;

    switch ($totalstatus) {
        case 4:
            $colclass = 'col-lg-3 col-md-6';
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
            $colclass = 'col-md-4';
            break;
    }

    $footerstatus = ($totalstatus == 0) ? false : true;
    $footerbottomstatus = ((empty($copyrightfooter)) && (!is_siteadmin($USER->id) || $CFG->debug == 0)) ? false : true;
    $templatecontext = [
        "footerlogourl" => $footerlogourl,
        "footlogostatus" => $footlogostatus,
        "footnote" => $footnote,
        "infolink" => $infolink,
        "address" => $address,
        "emailid" => $emailid,
        "phoneno" => $phoneno,
        "phone" => $phone,
        "email" => $email,
        "copyrightfooter" => $copyrightfooter,
        "fstatus1" => $fstatus1,
        "fstatus2" => $fstatus2,
        "fstatus3" => $fstatus3,
        "fstatus4" => $fstatus4,
        "ftitle1" => $ftitle1,
        "ftitle2" => $ftitle2,
        "ftitle3" => $ftitle3,
        "ftitle4" => $ftitle4,
        "colclass" => $colclass,
        'footerbgimgclass' => $footerbgimgclass,
        'backtotopbtn' => $backtotopbtn,
        'footerstatus' => $footerstatus,
        'footerbottomstatus' => $footerbottomstatus,
    ];
    $templatecontext += socialmedia();
    return $templatecontext;
}
