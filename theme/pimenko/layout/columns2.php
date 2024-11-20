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
 * @package   theme_pimenko
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index'));
    $blockdraweropen = (get_user_preferences('drawer-open-block'));
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

require_once($CFG->dirroot . '/course/lib.php');

$extraclasses = [];

$PAGE->requires->js_call_amd('theme_pimenko/pimenko', 'init');
$PAGE->requires->js_call_amd('theme_pimenko/completion', 'init');

if (theme_config::load('pimenko')->settings->enablecatalog) {
    $PAGE->requires->js_call_amd('theme_pimenko/catalog', 'init');
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

// Handle blockDrawer.
$addblockbutton = $OUTPUT->addblockbutton();

$blockshtml = $OUTPUT->blocks('side-pre');

$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}

$courseindex = core_course_drawer();

if (!$courseindex) {
    $courseindexopen = false;
}
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions()  && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

// Remove some primary navigation items.
$PAGE->theme->removedprimarynavitems = $OUTPUT->removedprimarynavitems();

$renderer = $PAGE->get_renderer('core');

$primary = new theme_pimenko\output\core\navigation\primary($PAGE);
$primarymenu = $primary->export_for_template($renderer);

$secondarynavigation = false;
$overflow = '';

// Secondary navigation.
if ($PAGE->has_secondary_navigation() &&
    (!strpos($PAGE->bodyclasses, 'path-enrol')
        || strpos($PAGE->bodyclasses, 'pagelayout-admin') > 0)) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $customnav = new \theme_pimenko\output\core\navigation\views\secondary($PAGE);
    $customnav->initialise();
    $PAGE->set_secondarynav($customnav);
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$theme = theme_config::load('pimenko');

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

if ($this->page->pagelayout == 'course') {
    $moodlecompletion = true;
} else {
    $moodlecompletion = $theme->settings->moodleactivitycompletion;

    // Remove completion if we use pimenko completion.
    if (!$moodlecompletion) {
        $headercontent['completion'] = '';
    }
}

$templatecontext = [
    'sitename' => format_string(
        $SITE->shortname,
        true,
        ['context' => context_course::instance(SITEID), "escape" => false]
    ),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'blockdraweropen' => $blockdraweropen,
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'hasfrontpageregions' => !empty($hasfrontpageregions),
    'courseindexopen' => $courseindexopen,
    'courseindex' => $courseindex,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'headercontent' => $headercontent,
    'forceblockdraweropen' => $forceblockdraweropen,
    'addblockbutton' => $addblockbutton,
    'overflow' => $overflow,
    'hidesitename' => $theme->settings->hidesitename,
    'moodlecompletion' => $moodlecompletion
];

echo $OUTPUT->render_from_template('theme_boost/columns2', $templatecontext);
