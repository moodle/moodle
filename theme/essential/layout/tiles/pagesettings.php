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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/* Default globals */
global $CFG, $PAGE, $USER, $SITE, $COURSE;

// Body.
$bodyclasses = array();

if (isloggedin()) {
    $bodyclasses[] = 'loggedin';
}

if (\theme_essential\toolbox::get_setting('enablealternativethemecolors1') ||
    \theme_essential\toolbox::get_setting('enablealternativethemecolors2') ||
    \theme_essential\toolbox::get_setting('enablealternativethemecolors3') ||
    \theme_essential\toolbox::get_setting('enablealternativethemecolors4')
) {
    $colourswitcher = true;
    \theme_essential\toolbox::initialise_colourswitcher($PAGE);
    $bodyclasses[]  = 'essential-colours-' . \theme_essential\toolbox::get_colours();
} else {
    $colourswitcher = false;
}

$devicetype = core_useragent::get_device_type(); // In /lib/classes/useragent.php.
if ($devicetype == "mobile") {
    $bodyclasses[] = 'mobiledevice';
    $tablet = false;
} else if ($devicetype == "tablet") {
    $bodyclasses[] = 'tabletdevice';
    $tablet = true;
} else {
    $bodyclasses[] = 'desktopdevice';
    $tablet = false;
}

switch (\theme_essential\toolbox::get_setting('pagewidth')) {
    case 100:
        $bodyclasses[] = 'pagewidthvariable';
        break;
    case 960:
        $bodyclasses[] = 'pagewidthnarrow';
        break;
    case 1200:
        $bodyclasses[] = 'pagewidthnormal';
        break;
    case 1400:
        $bodyclasses[] = 'pagewidthwide';
        break;
}
if (!empty($CFG->custommenuitems)) {
    $bodyclasses[] = 'custommenuitems';
}
if (\theme_essential\toolbox::get_setting('enablecategoryicon')) {
    $bodyclasses[] = 'categoryicons';
}

if (($PAGE->pagelayout == 'course') && (get_config('core', 'modeditingmenu'))) {
    $bodyclasses[] = 'modeditingmenu';
}

if (($PAGE->pagelayout == 'login') && (\theme_essential\toolbox::get_setting('loginbackground'))) {
    $bodyclasses[] = 'loginbackground';
}

$regionbsid = 'main-and-pre';

$fontselect = \theme_essential\toolbox::get_setting('fontselect');
if ($fontselect === '2') {
    $fontcharacterset = '&subset=latin';
    if (\theme_essential\toolbox::get_setting('fontcharacterset')) {
        $fontcharacterset = '&subset=latin,'.\theme_essential\toolbox::get_setting('fontcharacterset');
    }
    $headingfont = urlencode(\theme_essential\toolbox::get_setting('fontnameheading'));
    $bodyfont = urlencode(\theme_essential\toolbox::get_setting('fontnamebody'));
}

// Header.
$hassocialnetworks = (
    \theme_essential\toolbox::get_setting('facebook') ||
    \theme_essential\toolbox::get_setting('twitter') ||
    \theme_essential\toolbox::get_setting('googleplus') ||
    \theme_essential\toolbox::get_setting('linkedin') ||
    \theme_essential\toolbox::get_setting('youtube') ||
    \theme_essential\toolbox::get_setting('flickr') ||
    \theme_essential\toolbox::get_setting('vk') ||
    \theme_essential\toolbox::get_setting('pinterest') ||
    \theme_essential\toolbox::get_setting('instagram') ||
    \theme_essential\toolbox::get_setting('skype') ||
    \theme_essential\toolbox::get_setting('website')
);
$hasmobileapps = (\theme_essential\toolbox::get_setting('ios') ||
    \theme_essential\toolbox::get_setting('android')
);

$oldnavbar = \theme_essential\toolbox::get_setting('oldnavbar');
$haslogo = \theme_essential\toolbox::get_setting('logo');

// Layout.
$hasboringlayout = \theme_essential\toolbox::get_setting('layout');
if ($hasboringlayout) {
    $bodyclasses[] = 'hasboringlayout';
}

// Floating submit buttons.
if (\theme_essential\toolbox::get_setting('floatingsubmitbuttons')) {
    $bodyclasses[] = 'floatingsubmit';
}

// Footer.
$hascopyright = \theme_essential\toolbox::get_setting('copyright', true);
$hasfootnote = \theme_essential\toolbox::get_setting('footnote', 'format_html');
