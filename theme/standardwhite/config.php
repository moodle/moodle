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
 * Configuration for Moodle's standard theme.
 *
 * There is documentation of all the things that can be configured here at
 * http://phpdocs.moodle.org/HEAD/moodlecore/theme_config.html
 *
 * For an overview of how Moodle themes work, Please see
 * http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$THEME->sheets = array('gradients');

$THEME->parent = null;
$THEME->parentsheets = false;

$THEME->standardsheets = true;
$THEME->pluginsheets = array('mod', 'block', 'format', 'gradereport');

$THEME->metainclude = false;
$THEME->parentmetainclude = false;
$THEME->standardmetainclude = true;

$THEME->custompix = false;

$THEME->layouttemplates = array(
    'normal' => 'layout.php',
    'home' => 'layout-home.php',
);
$THEME->blockregions = array('side-pre', 'side-post');
$THEME->defaultblockregion = 'side-post';

$THEME->resource_mp3player_colors = 
 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
 'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
 'font=Arial&fontColour=3333FF&buffer=10&waitForPlay=no&autoPlay=yes';
$THEME->filter_mediaplugin_colors = 
 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
 'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
 'waitForPlay=yes';

//$THEME->rarrow = '&#x25BA;' //OR '&rarr;';
//$THEME->larrow = '&#x25C4;' //OR '&larr;';
//$CFG->block_search_button = link_arrow_right(get_string('search'), $url='', $accesshide=true);

$THEME->navmenuwidth = 50;
// You can use this to control the cutoff point for strings 
// in the navmenus (list of activities in popup menu etc)
// Default is 50 characters wide.

$THEME->makenavmenulist = false;  
// By setting this to true, then you will have access to a
// new variable in your header.html and footer.html called
// $navmenulist ... this contains a simple XHTML menu of 
// all activities in the current course, mostly useful for 
// creating popup navigation menus and so on.

$THEME->modsheets = true;