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
 * @package    theme_pimenko
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');

$extraclasses = [];

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

// Handle blockDrawer.
$blockshtml = $OUTPUT->blocks('side-pre');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index') == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;
$hasfrontpageregions = $OUTPUT->get_block_regions();
$iscarouselenabled = $OUTPUT->is_carousel_enabled();

// Remove some primary navigation items.
$PAGE->theme->removedprimarynavitems = $OUTPUT->removedprimarynavitems();

$renderer = $PAGE->get_renderer('core');
$primary = new theme_pimenko\output\core\navigation\primary($PAGE);
$primarymenu = $primary->export_for_template($renderer);

$secondarynavigation = false;
if ($PAGE->has_secondary_navigation()) {
    $customnav = new \theme_pimenko\output\core\navigation\views\secondary($PAGE);
    $customnav->initialise();
    $PAGE->set_secondarynav($customnav);
    $moremenu =
        new core\navigation\output\more_menu($PAGE->secondarynav,
            'nav-tabs');
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
}

$theme = theme_config::load('pimenko');

$templatecontext = [
    'sitename' => format_string(
        $SITE->shortname,
        true,
        [
            'context' => context_course::instance(SITEID),
            "escape" => false
        ]
    ),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'blockdraweropen' => $blockdraweropen,
    'bodyattributes' => $bodyattributes,
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasfrontpageregions' => !empty($hasfrontpageregions),
    'iscarouselenabled' => $iscarouselenabled,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'courseindexopen' => $courseindexopen,
    'courseindex' => $courseindex,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'addblockbutton' => $addblockbutton,
    'hidesitename' => $theme->settings->hidesitename
];

// Include js module.
$PAGE->requires->js_call_amd('theme_pimenko/pimenko', 'init');
$PAGE->requires->js_call_amd('theme_pimenko/completion', 'init');

echo $OUTPUT->render_from_template(
    'theme_pimenko/frontpage',
    $templatecontext
);
