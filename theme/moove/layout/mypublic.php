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
 * A drawer based layout for the moove theme.
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB, $USER, $OUTPUT, $SITE, $PAGE;

// Get the profile userid.
$courseid = optional_param('course', 1, PARAM_INT);
$userid = optional_param('id', $USER->id, PARAM_INT);
$user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$bodyattributes = $OUTPUT->body_attributes([]);

$userimg = new \user_picture($user);
$userimg->size = 100;

$context = context_course::instance(SITEID);

$usercanviewprofile = user_can_view_profile($user);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'primarymoremenu' => $primarymenu['moremenu'],
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'userpicture' => $userimg->get_url($PAGE),
    'userfullname' => fullname($user),
    'headerbuttons' => \theme_moove\util\extras::get_mypublic_headerbuttons($context, $user),
    'editprofileurl' => \theme_moove\util\extras::get_mypublic_editprofile_url($user, $courseid),
    'usercanviewprofile' => $usercanviewprofile
];

if ($usercanviewprofile) {
    $countries = get_string_manager()->get_list_of_countries(true);

    $templatecontext['userdescription'] = format_text($user->description, $user->descriptionformat, ['overflowdiv' => true]);
    $templatecontext['useremail'] = $user->email;
    $templatecontext['usercountry'] = $user->country ? $countries[$user->country] : '';
    $templatecontext['usercity'] = $user->city;
}

$themesettings = new \theme_moove\util\settings();

$templatecontext = array_merge($templatecontext, $themesettings->footer());

echo $OUTPUT->render_from_template('theme_moove/mypublic', $templatecontext);
