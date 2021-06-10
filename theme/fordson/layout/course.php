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
 * A two column layout for the boost theme.
 *
 * @package   theme_boost
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
require_once($CFG->libdir . '/behat/lib.php');

$hasfhsdrawer = isset($PAGE->theme->settings->shownavdrawer) && $PAGE->theme->settings->shownavdrawer == 1;
if (isloggedin() && $hasfhsdrawer && isset($PAGE->theme->settings->shownavclosed) && $PAGE->theme->settings->shownavclosed == 0) {
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

$blockshtmla = $OUTPUT->blocks('fp-a');
$blockshtmlb = $OUTPUT->blocks('fp-b');
$blockshtmlc = $OUTPUT->blocks('fp-c');
$checkblocka = strpos($blockshtmla, 'data-block=') !== false;
$checkblockb = strpos($blockshtmlb, 'data-block=') !== false;
$checkblockc = strpos($blockshtmlc, 'data-block=') !== false;
$hasfpblockregion = ($PAGE->theme->settings->blockdisplay == 1) !== false;

$hasactivitynav = ($PAGE->theme->settings->showactivitynav ==1) !== false;

$hascourseblocks = false;
if ($checkblocka || $checkblockb || $checkblockc) {
    $hascourseblocks = true;
}

$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID) , "escape" => false]) , 
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'fpablocks' => $blockshtmla,
    'fpbblocks' => $blockshtmlb,
    'fpcblocks' => $blockshtmlc,
    'hasblocks' => $hasblocks,
    'hascourseblocks' => $hascourseblocks,
    'hasfpblockregion' => $hasfpblockregion,
    'bodyattributes' => $bodyattributes,
    'navdraweropen' => $navdraweropen,
    'hasfhsdrawer' => $hasfhsdrawer,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'hasactivitynav' => $hasactivitynav
];

$PAGE->requires->jquery();
$PAGE->requires->js('/theme/fordson/javascript/scrolltotop.js');
$PAGE->requires->js('/theme/fordson/javascript/scrollspy.js');
$PAGE->requires->js('/theme/fordson/javascript/blockslider.js');

if (isset($PAGE->theme->settings->preset) && $PAGE->theme->settings->preset != 'Spectrum-Achromatic') {
    $PAGE->requires->js('/theme/fordson/javascript/courseblock.js');
}

$nav = $PAGE->flatnav;
$templatecontext['flatnavigation'] = $nav;
$templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();
echo $OUTPUT->render_from_template('theme_fordson/columns2', $templatecontext);