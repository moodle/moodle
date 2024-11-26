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
 * Lib
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/theme/boost/lib.php');

use core\output\theme_config;
use core\url;

define('THEME_ADAPTABLE_DEFAULT_ALERTCOUNT', '1');
define('THEME_ADAPTABLE_DEFAULT_ANALYTICSCOUNT', '1');
define('THEME_ADAPTABLE_DEFAULT_TOPMENUSCOUNT', '1');
define('THEME_ADAPTABLE_DEFAULT_TOOLSMENUSCOUNT', '1');
define('THEME_ADAPTABLE_DEFAULT_NEWSTICKERCOUNT', '1');
define('THEME_ADAPTABLE_DEFAULT_SLIDERCOUNT', '3');

/**
 * Gets the pre SCSS for the theme.
 *
 * @param theme_config $theme The theme configuration object.
 * @return string SCSS.
 */
function theme_adaptable_pre_scss($theme) {
    $regionmaincolor = \theme_adaptable\toolbox::get_setting('regionmaincolor', false, $theme->name, '#ffffff');
    $fontcolor = \theme_adaptable\toolbox::get_setting('fontcolor', false, $theme->name, '#333333');
    $fontcolorrgba = \theme_adaptable\toolbox::hex2rgba($fontcolor, 0.25);
    $prescss = '$body-bg: ' . $regionmaincolor . ';' . PHP_EOL;
    $prescss = '$body-color: ' . $fontcolor . ';' . PHP_EOL;
    $prescss .= '$primary: ' . \theme_adaptable\toolbox::get_setting('primarycolour', false, $theme->name, '#00796b') . ';' . PHP_EOL;
    $prescss .= '$secondary: ' . \theme_adaptable\toolbox::get_setting('secondarycolour', false, $theme->name, '#009688') . ';' . PHP_EOL;
    $prescss .= '$loadingcolor: ' . \theme_adaptable\toolbox::get_setting('loadingcolor', false, $theme->name, '#00B3A1') . ';' . PHP_EOL;
    $loadingcolor = \theme_adaptable\toolbox::get_setting('loadingcolor', false, $theme->name, '#00B3A1');
    $loadingcolorrgba = \theme_adaptable\toolbox::hex2rgba($loadingcolor, 0.2);
    $prescss .= '$loadingcolor: ' . $loadingcolor . ';' . PHP_EOL;
    $prescss .= '$loadingcolorrgba: ' . $loadingcolorrgba . ';' . PHP_EOL;
    $prescss .= '$nav-tabs-border-color: $secondary;' . PHP_EOL;
    $prescss .= '$dialogue-base-bg: ' . $regionmaincolor . ';' . PHP_EOL;
    $prescss .= '$nav-tabs-link-active-border-color: ' . $fontcolorrgba .' ' . $fontcolorrgba . ' transparent;' . PHP_EOL;
    $prescss .= '$nav-tabs-link-hover-border-color: transparent transparent '. $fontcolor . ';' . PHP_EOL;
    $prescss .= '$courseindex-link-color: ' .
        \theme_adaptable\toolbox::get_setting('courseindexitemcolor', false, $theme->name, '#495057') . ';' . PHP_EOL;
    $prescss .= '$courseindex-link-hover-color: ' .
        \theme_adaptable\toolbox::get_setting('courseindexitemhovercolor', false, $theme->name, '#e6e6e6') . ';' . PHP_EOL;
    $prescss .= '$courseindex-link-color-selected: ' .
        \theme_adaptable\toolbox::get_setting('courseindexpageitemcolor', false, $theme->name, '#ffffff') . ';' . PHP_EOL;
    $prescss .= '$courseindex-item-page-bg: ' .
        \theme_adaptable\toolbox::get_setting('courseindexpageitembgcolor', false, $theme->name, '#0f6cbf') . ';' . PHP_EOL;
    $prescss .= '$drawer-bg-color: #fff;';  // Currently no setting for 'block region' background.
    $prescss .= '$input-btn-focus-color: rgba(' .
        \theme_adaptable\toolbox::get_setting('inputbuttonfocuscolour', false, $theme->name, '#0f6cc0') . ', ' .
        \theme_adaptable\toolbox::get_setting('inputbuttonfocuscolouropacity', false, $theme->name, '0.75') . ');' . PHP_EOL;

    return $prescss;
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string SCSS.
 */
function theme_adaptable_get_main_scss_content($theme) {
    global $CFG;

    static $boosttheme = null;
    if (empty($boosttheme)) {
        $boosttheme = theme_config::load('boost'); // Needs to be the Boost theme so that we get its settings.
    }

    $scss = '$enable-rounded: false !default;'; // Todo: A setting?

    $scss .= theme_boost_get_main_scss_content($boosttheme);

    $basedir = ((!empty($CFG->themedir)) && (is_dir($CFG->themedir . '/adaptable'))) ? $CFG->themedir : $CFG->dirroot . '/theme';
    $basedir .= '/adaptable';

    if (!empty(\theme_adaptable\toolbox::get_setting('fav'))) {
        $scss .= '// Import Theme FontAwesome.' . PHP_EOL;
        $scss .= '@import "fontawesome/fontawesome";' . PHP_EOL;
        $scss .= '@import "fontawesome/brands";' . PHP_EOL;
        $scss .= '@import "fontawesome/regular";' . PHP_EOL;
        $scss .= '@import "fontawesome/solid";' . PHP_EOL;
        if (!empty(\theme_adaptable\toolbox::get_setting('faiv'))) {
            $scss .= '@import "fontawesome/v4-compatibility";' . PHP_EOL;
            $scss .= '@import "fontawesome/v4-shims";' . PHP_EOL;
        }
    }

    $scss .= file_get_contents($basedir . '/scss/main.scss');

    $settingssheets = [
        'adaptable',
        'admin',
        'blocks',
        'button',
        'core',
        'course',
        'extras',
        'header',
        'login',
        'menu',
        'modal',
        'responsive',
        'search',
        'secondarynavigation',
        'tabs',
        'print',
        'categorycustom',
    ];

    $settingsscss = '';
    foreach ($settingssheets as $settingsheet) {
        $settingsscss .= file_get_contents($basedir . '/scss/settings/' . $settingsheet . '.scss');
    }

    $scss .= theme_adaptable_process_scss($settingsscss, $theme);

    return $scss;
}

/**
 * Parses SCSS before it is parsed by the SCSS compiler.
 *
 * This function can make alterations and replace patterns within the SCSS.
 *
 * @param string $scss The SCSS.
 * @param theme_config $theme The theme config object.
 * @return string The parsed SCSS.
 */
function theme_adaptable_process_scss($scss, $theme) {

    // Set category custom CSS.
    $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
    if (is_object($localtoolbox)) {
        $scss = $localtoolbox->set_categorycustomcss($scss, $theme->settings);
    }

    // Collapsed Topics colours.
    if (empty($theme->settings->collapsedtopicscoloursenabled)) {
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content .toggle span.the_toggle h3.sectionname,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content .toggle span.the_toggle h3.sectionname a,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content .toggle span.the_toggle h3.sectionname a:hover,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content .toggle span.the_toggle h3.sectionname a:focus,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content.sectionhidden h3.sectionname' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content.sectionhidden h3.sectionname a,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content.sectionhidden h3.sectionname a:hover,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content.sectionhidden h3.sectionname a:focus {' . PHP_EOL;
        $scss .= '    color: [[setting:sectionheadingcolor]];' . PHP_EOL;
        $scss .= '}' . PHP_EOL;

        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content div.toggle,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content div.toggle:hover,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content div.toggle:focus {' . PHP_EOL;
        $scss .= '    background-color: [[setting:coursesectionheaderbg]];' . PHP_EOL;
        $scss .= '}' . PHP_EOL;

        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content .toggle span,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content .toggle span:hover,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content .toggle span:focus,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content.sectionhidden,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content.sectionhidden:hover,' . PHP_EOL;
        $scss .= '.theme_adaptable .course-content ul.ctopics li.section .content.sectionhidden:focus {' . PHP_EOL;
        $scss .= '    color: inherit;' . PHP_EOL;
        $scss .= '}' . PHP_EOL;
    }

    // Define the default settings for the theme in case they've not been set.
    $defaults = [
        '[[setting:linkcolor]]' => '#51666C',
        '[[setting:linkhover]]' => '#009688',
        '[[setting:dimmedtextcolor]]' => '#6a737b',
        '[[setting:maincolor]]' => '#ffffff',
        '[[setting:backcolor]]' => '#FFFFFF',
        '[[setting:primarycolour]]' => '#00796b',
        '[[setting:secondarycolour]]' => '#009688',
        '[[setting:regionmaincolor]]' => '#ffffff',
        '[[setting:regionmaintextcolor]]' => '#000000',
        '[[setting:rendereroverlaycolor]]' => '#3A454b',
        '[[setting:rendereroverlayfontcolor]]' => '#FFFFFF',
        '[[setting:buttoncolor]]' => '#51666C',
        '[[setting:buttontextcolor]]' => '#ffffff',
        '[[setting:buttonhovercolor]]' => '#009688',
        '[[setting:buttontexthovercolor]]' => '#eeeeee',
        '[[setting:buttonfocuscolour]]' => '#0f6cc0',
        '[[setting:buttontextfocuscolour]]' => '#eeeeee',
        '[[setting:buttoncolorscnd]]' => '#51666C',
        '[[setting:buttontextcolorscnd]]' => '#ffffff',
        '[[setting:buttonhovercolorscnd]]' => '#009688',
        '[[setting:buttoncolorcancel]]' => '#c64543',
        '[[setting:buttontextcolorcancel]]' => '#ffffff',
        '[[setting:buttonhovercolorcancel]]' => '#e53935',
        '[[setting:buttonlogincolor]]' => '#c64543',
        '[[setting:buttonloginhovercolor]]' => '#e53935',
        '[[setting:buttonlogintextcolor]]' => '#0084c2',
        '[[setting:buttonloginpadding]]' => '0',
        '[[setting:buttonloginheight]]' => '24px',
        '[[setting:buttonloginmargintop]]' => '2px',
        '[[setting:buttonradius]]' => '5px',
        '[[setting:buttondropshadow]]' => '0',
        '[[setting:dividingline]]' => '#ffffff',
        '[[setting:dividingline2]]' => '#ffffff',
        '[[setting:breadcrumb]]' => '#b4bbbf',
        '[[setting:breadcrumbtextcolor]]' => '#444444',
        '[[setting:breadcrumbseparator]]' => 'angle-right',
        '[[setting:loadingcolor]]' => '#00B3A1',
        '[[setting:messagepopupbackground]]' => '#fff000',
        '[[setting:messagepopupcolor]]' => '#333333',
        '[[setting:messagingbackgroundcolor]]' => '#FFFFFF',
        '[[setting:footerbkcolor]]' => '#424242',
        '[[setting:footertextcolor]]' => '#ffffff',
        '[[setting:footertextcolor2]]' => '#ffffff',
        '[[setting:footerlinkcolor]]' => '#ffffff',
        '[[setting:headerbkcolor]]' => '#00796B',
        '[[setting:headerbkcolor2]]' => '#009688',
        '[[setting:headerbgimagetextcolour]]' => '#ffffff',
        '[[setting:headertextcolor]]' => '#ffffff',
        '[[setting:headertextcolor2]]' => '#ffffff',
        '[[setting:msgbadgecolor]]' => '#E53935',
        '[[setting:blockbackgroundcolor]]' => '#FFFFFF',
        '[[setting:blockheaderbackgroundcolor]]' => '#FFFFFF',
        '[[setting:blockbordercolor]]' => '#59585D',
        '[[setting:blockregionbackgroundcolor]]' => 'transparent',
        '[[setting:selectiontext]]' => '#000000',
        '[[setting:selectionbackground]]' => '#00B3A1',
        '[[setting:marketblockbordercolor]]' => '#e8eaeb',
        '[[setting:marketblocksbackgroundcolor]]' => 'transparent',
        '[[setting:blockheaderbordertop]]' => '1px',
        '[[setting:blockheaderborderleft]]' => '0',
        '[[setting:blockheaderborderright]]' => '0',
        '[[setting:blockheaderborderbottom]]' => '0',
        '[[setting:blockmainbordertop]]' => '0',
        '[[setting:blockmainborderleft]]' => '0',
        '[[setting:blockmainborderright]]' => '0',
        '[[setting:blockmainborderbottom]]' => '0',
        '[[setting:blockheaderbordertopstyle]]' => 'dashed',
        '[[setting:blockmainbordertopstyle]]' => 'solid',
        '[[setting:blockheadertopradius]]' => '0',
        '[[setting:blockheaderbottomradius]]' => '0',
        '[[setting:blockmaintopradius]]' => '0',
        '[[setting:blockmainbottomradius]]' => '0',
        '[[setting:coursesectionbgcolor]]' => '#FFFFFF',
        '[[setting:coursesectionheaderbg]]' => '#FFFFFF',
        '[[setting:coursesectionheaderbordercolor]]' => '#F3F3F3',
        '[[setting:coursesectionheaderborderstyle]]' => 'none',
        '[[setting:coursesectionheaderborderwidth]]' => '0px',
        '[[setting:coursesectionheaderborderradiustop]]' => '0px',
        '[[setting:coursesectionheaderborderradiusbottom]]' => '0px',
        '[[setting:coursesectionborderstyle]]' => '1px',
        '[[setting:coursesectionborderwidth]]' => '1px',
        '[[setting:coursesectionbordercolor]]' => '#e8eaeb',
        '[[setting:coursesectionborderradius]]' => '0px',
        '[[setting:coursesectionactivityiconsize]]' => '24px',
        '[[setting:coursesectionactivityheadingcolour]]' => '#0066cc',
        '[[setting:coursesectionactivityborderwidth]]' => '2px',
        '[[setting:coursesectionactivityborderstyle]]' => 'dashed',
        '[[setting:coursesectionactivitybordercolor]]' => '#eeeeee',
        '[[setting:coursesectionactivityleftborderwidth]]' => '3px',
        '[[setting:coursesectionactivityassignleftbordercolor]]' => '#0066cc',
        '[[setting:coursesectionactivityassignbgcolor]]' => '#FFFFFF',
        '[[setting:coursesectionactivityforumleftbordercolor]]' => '#990099',
        '[[setting:coursesectionactivityforumbgcolor]]' => '#FFFFFF',
        '[[setting:coursesectionactivityquizleftbordercolor]]' => '#FF3333',
        '[[setting:coursesectionactivityquizbgcolor]]' => '#FFFFFF',
        '[[setting:coursesectionactivitymargintop]]' => '2px',
        '[[setting:coursesectionactivitymarginbottom]]' => '2px',
        '[[setting:tilesbordercolor]]' => '#3A454b',
        '[[setting:slidermargintop]]' => '20px',
        '[[setting:slidermarginbottom]]' => '20px',
        '[[setting:currentcolor]]' => '#d9edf7',
        '[[setting:sectionheadingcolor]]' => '#3A454b',
        '[[setting:menufontsize]]' => '14px',
        '[[setting:menufontpadding]]' => '20px',
        '[[setting:topmenufontsize]]' => '14px',
        '[[setting:menubkcolor]]' => '#ffffff',
        '[[setting:menufontcolor]]' => '#444444',
        '[[setting:menubkhovercolor]]' => '#00B3A1',
        '[[setting:menufonthovercolor]]' => '#ffffff',
        '[[setting:menubordercolor]]' => '#00B3A1',
        '[[setting:mobilemenubkcolor]]' => '#F9F9F9',
        '[[setting:navbardropdownborderradius]]' => '0',
        '[[setting:navbardropdownhovercolor]]' => '#EEE',
        '[[setting:navbardropdowntextcolor]]' => '#007',
        '[[setting:navbardropdowntexthovercolor]]' => '#000',
        '[[setting:navbardropdowntransitiontime]]' => '0.0s',
        '[[setting:covbkcolor]]' => '#3A454b',
        '[[setting:covfontcolor]]' => '#ffffff',
        '[[setting:editonbk]]' => '#4caf50',
        '[[setting:editoffbk]]' => '#f44336',
        '[[setting:edithorizontalpadding]]' => '4px',
        '[[setting:editfont]]' => '#ffffff',
        '[[setting:sliderh3color]]' => '#ffffff',
        '[[setting:sliderh4color]]' => '#ffffff',
        '[[setting:slidersubmitbgcolor]]' => '#51666C',
        '[[setting:slidersubmitcolor]]' => '#ffffff',
        '[[setting:slider2h3color]]' => '#000000',
        '[[setting:slider2h4color]]' => '#000000',
        '[[setting:slider2h3bgcolor]]' => '#000000',
        '[[setting:slider2h4bgcolor]]' => '#ffffff',
        '[[setting:slideroption2color]]' => '#51666C',
        '[[setting:slideroption2submitcolor]]' => '#ffffff',
        '[[setting:slideroption2a]]' => '#51666C',
        '[[setting:socialsize]]' => '37px',
        '[[setting:mobile]]' => '22',
        '[[setting:socialpaddingside]]' => 16,
        '[[setting:socialpaddingtop]]' => '0%',
        '[[setting:fontname]]' => 'sans-serif',
        '[[setting:fontsize]]' => '95%',
        '[[setting:fontheadername]]' => 'sans-serif',
        '[[setting:fontcolor]]' => '#333333',
        '[[setting:fontheadercolor]]' => '#333333',
        '[[setting:fontweight]]' => '400',
        '[[setting:fontheaderweight]]' => '400',
        '[[setting:fonttitlename]]' => 'sans-serif',
        '[[setting:fonttitleweight]]' => '400',
        '[[setting:fonttitlesize]]' => '48px',
        '[[setting:fonttitlecolor]]' => '#ffffff',
        '[[setting:fonttitlecolorcourse]]' => '#ffffff',
        '[[setting:searchboxpadding]]' => '0 0 0 0',
        '[[setting:enablesavecanceloverlay]]' => true,
        '[[setting:pageheaderheight]]' => '72px',
        '[[setting:emoticonsize]]' => '16px',
        '[[setting:fullscreenwidth]]' => '98%',
        '[[setting:coursetitlemaxwidth]]' => '20',
        '[[setting:responsiveheader]]' => 'd-none d-lg-block',
        '[[setting:responsivesocial]]' => 'd-none d-lg-block',
        '[[setting:responsivesocialsize]]' => '34px',
        '[[setting:responsivelogo]]' => 'd-none d-lg-inline-block',
        '[[setting:responsivecoursetitle]]' => 'd-none d-lg-inline-block',
        '[[setting:responsivesectionnav]]' => '1',
        '[[setting:responsiveticker]]' => 'd-none d-lg-block',
        '[[setting:responsivebreadcrumb]]' => 'd-none d-md-flex',
        '[[setting:responsiveslider]]' => 'd-none d-lg-block',
        '[[setting:responsivepagefooter]]' => 'd-none d-lg-block',
        '[[setting:hidefootersocial]]' => 1,
        '[[setting:enableavailablecourses]]' => 'display',
        '[[setting:enableticker]]' => true,
        '[[setting:enabletickermy]]' => true,
        '[[setting:tickerwidth]]' => '',
        '[[setting:tickerheaderbackgroundcolour]]' => '#00796b',
        '[[setting:tickerheadertextcolour]]' => '#eee',
        '[[setting:tickerconstainerbackgroundcolour]]' => '#009688',
        '[[setting:tickerconstainertextcolour]]' => '#eee',
        '[[setting:onetopicactivetabbackgroundcolor]]' => '#d9edf7',
        '[[setting:onetopicactivetabtextcolor]]' => '#000000',
        '[[setting:fontblockheaderweight]]' => '400',
        '[[setting:fontblockheadersize]]' => '22px',
        '[[setting:fontblockheadercolor]]' => '#3A454b',
        '[[setting:blockiconsheadersize]]' => '20px',
        '[[setting:alertcolorinfo]]' => '#3a87ad',
        '[[setting:alertbackgroundcolorinfo]]' => '#d9edf7',
        '[[setting:alertbordercolorinfo]]' => '#bce8f1',
        '[[setting:alertcolorsuccess]]' => '#468847',
        '[[setting:alertbackgroundcolorsuccess]]' => '#dff0d8',
        '[[setting:alertbordercolorsuccess]]' => '#d6e9c6',
        '[[setting:alertcolorwarning]]' => '#8a6d3b',
        '[[setting:alertbackgroundcolorwarning]]' => '#fcf8e3',
        '[[setting:alertbordercolorwarning]]' => '#fbeed5',
        '[[setting:forumheaderbackgroundcolor]]' => '#ffffff',
        '[[setting:forumbodybackgroundcolor]]' => '#ffffff',
        '[[setting:introboxbackgroundcolor]]' => '#ffffff',
        '[[setting:tabbedlayoutdashboardcolorselected]]' => '#06c',
        '[[setting:tabbedlayoutdashboardcolorunselected]]' => '#eee',
        '[[setting:tabbedlayoutcoursepagetabcolorselected]]' => '#06c',
        '[[setting:tabbedlayoutcoursepagetabcolorunselected]]' => '#eee',
        '[[setting:frontpagenumbertiles]]' => '4',
        '[[setting:gdprbutton]]' => 1,
        '[[setting:infoiconcolor]]' => '#5bc0de',
        '[[setting:dangericoncolor]]' => '#d9534f',
        '[[setting:loginheader]]' => 0,
        '[[setting:loginfooter]]' => 0,
        '[[setting:printpageorientation]]' => 'landscape',
        '[[setting:printbodyfontsize]]' => '11pt',
        '[[setting:printmargin]]' => '2cm 1cm 2cm 2cm',
        '[[setting:printlineheight]]' => '1.2',
    ];

    // Get all the defined settings for the theme and replace defaults.
    foreach ($theme->settings as $key => $val) {
        if (((!empty($val)) || (strlen($val) > 0)) && (array_key_exists('[[setting:' . $key . ']]', $defaults))) {
            $defaults['[[setting:' . $key . ']]'] = $val;
        }
    }

    $homebkg = '';
    if (!empty($theme->settings->homebk)) {
        $homebkg = $theme->setting_file_url('homebk', 'homebk');
        $homebkg = 'background-image: url("' . $homebkg . '");';
    }
    $defaults['[[setting:homebkg]]'] = $homebkg;

    $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
    if (is_object($localtoolbox)) {
        $retr = $localtoolbox->login_style($theme);
        $defaults['[[setting:loginbgimage]]'] = $retr->loginbgimage;
        $defaults['[[setting:loginbgstyle]]'] = $retr->loginbgstyle;
        $defaults['[[setting:loginbgopacity]]'] = $retr->loginbgopacity;
    } else {
        $defaults['[[setting:loginbgimage]]'] = '';
        $defaults['[[setting:loginbgstyle]]'] = '';
        $defaults['[[setting:loginbgopacity]]'] = '';
    }

    $socialpaddingsidehalf = '16';
    if (!empty($theme->settings->socialpaddingside)) {
        $socialpaddingsidehalf = '' . $theme->settings->socialpaddingside / 2;
    }
    $defaults['[[setting:socialpaddingsidehalf]]'] = $socialpaddingsidehalf;

    // Add in rgba colours.
    $defaults['[[setting:fontcolorrgba]]'] = \theme_adaptable\toolbox::hex2rgba($defaults['[[setting:fontcolor]]'], 0.25);
    $defaults['[[setting:regionmaincolorrgba]]'] = \theme_adaptable\toolbox::hex2rgba($defaults['[[setting:regionmaincolor]]'], 0.75);
    $defaults['[[setting:linkcolorrgba]]'] = \theme_adaptable\toolbox::hex2rgba($defaults['[[setting:linkcolor]]'], 0.75);
    $defaults['[[setting:linkhoverrgba]]'] = \theme_adaptable\toolbox::hex2rgba($defaults['[[setting:linkhover]]'], 0.75);

    // Replace the CSS with values from the $defaults array.
    $scss = strtr($scss, $defaults);
    if (empty($theme->settings->tilesshowallcontacts) || $theme->settings->tilesshowallcontacts == false) {
        $scss = theme_adaptable_set_tilesshowallcontacts($scss, false);
    } else {
        $scss = theme_adaptable_set_tilesshowallcontacts($scss, true);
    }

    return $scss;
}

/**
 * Parses CSS before it is cached.
 *
 * This function can make alterations and replace patterns within the CSS.
 *
 * @param string $css The CSS
 * @param theme_config $theme The theme config object.
 * @return string The parsed CSS The parsed CSS.
 */
function theme_adaptable_process_customcss($css, $theme) {

    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_adaptable_set_customcss($css, $customcss);

    return $css;
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_adaptable_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

/**
 * Serves the H5P Custom CSS.
 *
 * @param string $filename The filename.
 * @param theme_config $theme The theme config object.
 */
function theme_adaptable_serve_hvp_css($filename, $theme) {
    global $CFG, $PAGE;
    require_once($CFG->dirroot . '/lib/configonlylib.php'); // For 'min_enable_zlib_compression' function.

    $PAGE->set_context(context_system::instance());
    $themename = $theme->name;

    $content = \theme_adaptable\toolbox::get_setting('hvpcustomcss');
    $md5content = md5($content);
    $md5stored = get_config('theme_' . $themename, 'hvpccssmd5');
    if ((empty($md5stored)) || ($md5stored != $md5content)) {
        // Content changed, so the last modified time needs to change.
        set_config('hvpccssmd5', $md5content, 'theme_' . $themename);
        $lastmodified = time();
        set_config('hvpccsslm', $lastmodified, 'theme_' . $themename);
    } else {
        $lastmodified = get_config('theme_' . $themename, 'hvpccsslm');
        if (empty($lastmodified)) {
            $lastmodified = time();
        }
    }

    // Sixty days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;

    header('HTTP/1.1 200 OK');

    header('Etag: "' . $md5content . '"');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . strlen($content));
    }

    echo $content;

    die;
}

/**
 * Set display of course contacts on front page tiles
 * @param string $css
 * @param string $display
 * @return $string
 */
function theme_adaptable_set_tilesshowallcontacts($css, $display) {
    $tag = '[[setting:tilesshowallcontacts]]';
    if ($display) {
        $replacement = 'block';
    } else {
        $replacement = 'none';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Get the current user preferences that are available
 *
 * @return array[]
 */
function theme_adaptable_user_preferences(): array {
    return [
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}
/**
 * Get the user preference for the zoom (show / hide block) function.
 */
function theme_adaptable_get_zoom() {
    return get_user_preferences('theme_adaptable_zoom', '');
}

/**
 * Set user preferences for zoom (show / hide block) function
 * @return void
 */
function theme_adaptable_initialise_zoom() {
    global $USER;
    $USER->adaptable_user_pref['theme_adaptable_zoom'] = PARAM_TEXT;
}

/**
 * Set the user preference for full screen
 * @return void
 */
function theme_adaptable_initialise_full() {
    if (\theme_adaptable\toolbox::get_setting('enablezoom')) {
        global $USER;
        $USER->adaptable_user_pref['theme_adaptable_full'] = PARAM_TEXT;
    }
}

/**
 * Get the user preference for the zoom function.
 */
function theme_adaptable_get_full() {
    $fullpref = '';
    if ((isloggedin()) && (\theme_adaptable\toolbox::get_setting('enablezoom'))) {
        $fullpref = get_user_preferences('theme_adaptable_full', '');
    }

    if (empty($fullpref)) { // Zoom disabled, not logged in or user not chosen preference.
        $defaultzoom = \theme_adaptable\toolbox::get_setting('defaultzoom');
        if (empty($defaultzoom)) {
            $defaultzoom = 'normal';
        }
        if ($defaultzoom == 'normal') {
            $fullpref = 'nofull';
        } else {
            $fullpref = 'fullin';
        }
    }

    return $fullpref;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_adaptable_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('adaptable');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        // By default, theme files must be cache-able by both browsers and proxies.  From 'More' theme.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'customjsfiles') {
            return $theme->setting_file_serve('customjsfiles', $args, $forcedownload, $options);
        } else if ($filearea === 'homebk') {
            return $theme->setting_file_serve('homebk', $args, $forcedownload, $options);
        } else if ($filearea === 'pagebackground') {
            return $theme->setting_file_serve('pagebackground', $args, $forcedownload, $options);
        } else if ($filearea === 'frontpagerendererdefaultimage') {
            return $theme->setting_file_serve('frontpagerendererdefaultimage', $args, $forcedownload, $options);
        } else if ($filearea === 'headerbgimage') {
            return $theme->setting_file_serve('headerbgimage', $args, $forcedownload, $options);
        } else if ($filearea === 'hvp') {
            theme_adaptable_serve_hvp_css($args[1], $theme);
        } else if ($filearea === 'loginbgimage') {
            return $theme->setting_file_serve('loginbgimage', $args, $forcedownload, $options);
        } else if (preg_match("/^p[1-9][0-9]?$/", $filearea)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if (preg_match("/^categoryheaderbgimage[1-9][0-9]*$/", $filearea)) { // Link: http://regexpal.com/ useful.
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if (preg_match("/^categoryheaderlogo[1-9][0-9]*$/", $filearea)) { // Link: http://regexpal.com/ useful.
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else if ($filearea === 'adaptablemarkettingimages') {
            return $theme->setting_file_serve('adaptablemarkettingimages', $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Get course activities for this course menu
 */
function theme_adaptable_get_course_activities() {
    global $PAGE;
    // A copy of block_activity_modules.
    $course = $PAGE->course;
    $modinfo = get_fast_modinfo($course);
    $modfullnames = [];

    $archetypes = [];

    foreach ($modinfo->cms as $cm) {
        // Exclude activities which are not visible or have no link (=label).
        if (!$cm->uservisible || !$cm->has_view()) {
            continue;
        }
        if (array_key_exists($cm->modname, $modfullnames)) {
            continue;
        }
        if (!array_key_exists($cm->modname, $archetypes)) {
            $archetypes[$cm->modname] = plugin_supports('mod', $cm->modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
        }
        if ($archetypes[$cm->modname] == MOD_ARCHETYPE_RESOURCE) {
            if (!array_key_exists('resources', $modfullnames)) {
                $modfullnames['resources'] = get_string('resources');
            }
        } else {
            $modfullnames[$cm->modname] = $cm->modplural;
        }
    }
    core_collator::asort($modfullnames);

    return $modfullnames;
}

/**
 * Initialize page
 * @param moodle_page $page
 */
function theme_adaptable_page_init(moodle_page $page) {
    global $CFG;

    if (
        (isloggedin()) && (\theme_adaptable\toolbox::get_setting('enableaccesstool')) &&
        (file_exists($CFG->dirroot . "/local/accessibilitytool/lib.php"))
    ) {
        require_once($CFG->dirroot . "/local/accessibilitytool/lib.php");
        local_accessibilitytool_page_init($page);
    }
}

/**
 *
 * Get the current page to allow us to check if the block is allowed to display.
 *
 * @return string The page name, which is either "frontpage", "dashboard", "coursepage", "coursesectionpage" or empty string.
 *
 */
function theme_adaptable_get_current_page() {
    global $PAGE;

    // This will store the kind of activity page type we find. E.g. It will get populated with 'section' or similar.
    $currentpage = '';

    // We expect $PAGE->url to exist.  It should!
    $url = $PAGE->url;

    if ($PAGE->pagetype == 'site-index') {
        $currentpage = 'frontpage';
    } else if ($PAGE->pagetype == 'my-index') {
        $currentpage = 'dashboard';
    }
    // Check if course home page.
    if (empty($currentpage)) {
        if ($url !== null) {
            // Check if this is the course view page.
            if (strstr($url->raw_out(), 'course/view.php')) {
                $currentpage = 'coursepage';

                // Check url paramaters.  Count should be 1 if course home page. Use this to check if section page.
                $urlparams = $url->params();

                // Allow the block to display on course sections too if the relevant setting is on.
                if ((count($urlparams) > 1) && (array_key_exists('section', $urlparams))) {
                    $currentpage = 'coursesectionpage';
                }
            }
        }
    }

    return $currentpage;
}

/**
 * Extend the course navigation.
 *
 * Ref: MDL-69249.
 *
 * @param navigation_node $coursenode The navigation node.
 * @param stdClass $course The course.
 * @param context_course $coursecontext The course context.
 */
function theme_adaptable_extend_navigation_course($coursenode, $course, $coursecontext) {
    global $PAGE;

    if (($PAGE->theme->name == 'adaptable') && ($PAGE->user_allowed_editing())) {
        // Add the turn on/off settings.
        if ($PAGE->pagetype == 'grade-report-grader-index') {
            $editurl = clone($PAGE->url);
            $editurl->param('plugin', 'grader');

            // From /grade/report/grader/index.php ish.
            $edit = optional_param('edit', -1, PARAM_BOOL); // Sticky editing mode.
            if (($edit != - 1) && (has_capability('moodle/grade:edit', $coursecontext))) {
                $editing = $edit;
            } else {
                $editing = 0;
            }
            /* Note: The 'single_button' will still use the Moodle core strings because of the
               way /grade/report/grader/index.php is written. */
            if ($editing) {
                $editstring = get_string('turngradereditingoff', 'theme_adaptable');
            } else {
                $editstring = get_string('turngradereditingon', 'theme_adaptable');
            }
        } else {
            if ($PAGE->url->compare(new url('/course/view.php'), URL_MATCH_BASE)) {
                // We are on the course page, retain the current page params e.g. section.
                $editurl = clone($PAGE->url);
            } else {
                // Edit on the main course page.
                $editurl = new url(
                    '/course/view.php',
                    ['id' => $course->id, 'return' => $PAGE->url->out_as_local_url(false)]
                );
            }
            $editing = $PAGE->user_is_editing();
            if ($editing) {
                $editstring = get_string('turneditingoff');
            } else {
                $editstring = get_string('turneditingon');
            }
        }
        $editurl->param('sesskey', sesskey());

        if ($editing) {
            $editurl->param('edit', '0');
        } else {
            $editurl->param('edit', '1');
        }

        $childnode = navigation_node::create(
            $editstring,
            $editurl,
            navigation_node::TYPE_SETTING,
            null,
            'turneditingonoff',
            new pix_icon('i/edit', '')
        );
        $keylist = $coursenode->get_children_key_list();
        if (!empty($keylist)) {
            if (count($keylist) > 1) {
                $beforekey = $keylist[1];
            } else {
                $beforekey = $keylist[0];
            }
        } else {
            $beforekey = null;
        }
        $coursenode->add_node($childnode, $beforekey);
    }
}
