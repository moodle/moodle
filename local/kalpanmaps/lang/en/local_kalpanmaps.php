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
 * @package   local_kalpanmaps
 * @copyright 2021 onwards LSUOnline & Continuing Education
 * @copyright 2021 onwards Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = "Kaltura to Panopto";
$string['pluginname_desc'] = "Periodically converts remaining Kaltura items to their Panopto counterparts.";

// Task.
$string['import_kalvidmap'] = 'Import KalVidMap data';
$string['general'] = 'General settings';
$string['convert_kalvidres'] = 'Convert KalVidRes to URLs';
$string['convert_kalvidres_help'] = 'Converts Kaltura Video Resources to Moodle URL resources.';
$string['convert_kalvids'] = 'Convert Kaltura to Panopto';
$string['convert_kalembeds'] = 'Convert Kaltura embeds';
$string['convert_kalembeds_help'] = 'Converts Kaltura embeds to Panopto embeds.';

// Conversion Settings.
$string['hide_kaltura_items'] = 'Hide kalvidres';
$string['hide_kaltura_items_help'] = 'Hides kaltura Video Resource items on conversion.';
$string['hide_kaltura_items2'] = 'Hide converted';
$string['hide_kaltura_items2_help'] = 'Hides previously converted kaltura Video Resource items when found.';

// Embed Settings.
$string['kalembeds_width'] = 'Width';
$string['kalembeds_width_help'] = 'Default iframe width when none is specified.';
$string['kalembeds_height'] = 'Height';
$string['kalembeds_height_help'] = 'Default iframe height when none is specified.';
$string['kalembeds_studentdata'] = 'Student Data';
$string['kalembeds_studentdata_help'] = 'Replace Kaltura iframes with corresponding panopto iframes for student submitted data?';
$string['kalembeds_showtitle'] = 'Show Video Title';
$string['kalembeds_showtitle_help'] = 'Shows the video title in the top left of the video.';
$string['kalembeds_captions'] = 'Show Video Captions';
$string['kalembeds_captions_help'] = 'Shows the video captions by default, if they exist.';
$string['kalembeds_autoplay'] = 'Autoplay Video';
$string['kalembeds_autoplay_help'] = 'Automatically starts the video on load. This may not work on all browsers.';
$string['kalembeds_offerviewer'] = 'Show "Watch in Panopto" link';
$string['kalembeds_offerviewer_help'] = 'Shows the "Watch in Panopto" link in the lower right portion of the video.';
$string['kalembeds_showbrand'] = 'Show Brand';
$string['kalembeds_showbrand_help'] = 'Shows a custom brand if applicable.';
$string['kalembeds_interactivity'] = 'Allow Interactivity';
$string['kalembeds_interactivity_help'] = 'Enables the public to access the table of contents, post public comments, add notes, view video info, and search for other videos within the video interface.';

// Import Settings.
$string['kalpanmapfile'] = 'File location';
$string['kalpanmapfile_help'] = 'Location of the csv file with kaltura, panopto ids provided by Panopto and edited to ONLY have those two fields in that order.';
$string['verbose'] = 'Verbose';
$string['verbose_help'] = 'Enabling verbose logging will give an output of EVERY imported line and converted kalvidres resource. This may prove too large for storing as a task log. Please use with caution.';
$string['purge'] = 'Purge data';
$string['purge_help'] = 'Truncate the local_kalpanmaps table prior to importing.';

// Strings for category limiting
$string['categorylimit'] = 'Category Limiting';
$string['categorylimit_help'] = 'Limit processing to specific course categories.';
$string['categories'] = 'Categories';
$string['categories_help'] = 'Limit to the selected course categories.';
