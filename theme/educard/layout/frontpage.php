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
 * A one column layout for the educard theme.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');

if (isloggedin()) {
    $userlogin = true;
} else {
    $userlogin = false;
}

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

$extraclasses = [];
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions()  && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'headercontent' => $headercontent,
    'overflow' => $overflow,
    'addblockbutton' => $addblockbutton,
];
// Frontpage dark checkbox.
$PAGE->requires->js('/theme/educard/js/themedarklight.min.js');
// Seskey.
$templatecontext['sesskey'] = sesskey();
// User login control.
$templatecontext['userlogin'] = $userlogin;
$templatecontext['frontpage'] = true;
if (isloggedin()) {
    $templatecontext['currentuser'] = format_string(fullname($USER));
    if (isset($USER->editing)) {
        $templatecontext['editinguser'] = $USER->editing;
    }
}
$homeid = optional_param('home', '', PARAM_RAW);
// Front page section.
if ($homeid == "p1") {
    $PAGE->requires->css('/theme/educard/style/leaflet.css');
    $PAGE->requires->jquery();
    $PAGE->requires->js('/theme/educard/js/leaflet.js');
    $PAGE->requires->js('/theme/educard/js/map.js');
}
$theme = theme_config::load('educard');
$templatecontext['frontpageimglink'] = $theme->settings->frontpageimglink;
$templatecontext = array_merge($templatecontext, theme_educard_frontpage_section($homeid));
echo $OUTPUT->render_from_template('theme_educard/frontpage/frontpage', $templatecontext);
