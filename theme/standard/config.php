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
 * DO NOT COPY THIS INTO NEW THEMES! Instead use some other theme as a base
 * for your experiments.
 *
 * Options related to theme customisations can be found at
 * http://phpdocs.moodle.org/HEAD/moodlecore/theme_config.html
 *
 * For an overview of how Moodle themes work, Please see
 * http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$THEME->parents = array();

// TODO: All old styles are now moved into this standard theme because
//       we need to go through all these and fix them.
//       This means we will gradually put these back into plugins
//       directories
$THEME->sheets = array('styles_layout', 'styles_fonts', 'styles_color', 'styles_moz',
    'block_blog_tags',
    'block_calendar_month',
    'block_calendar_upcoming',
    'block_course_summary',
    'block_login',
    'block_news_items',
    'block_quiz_results',
    'block_rss_client',
    'block_search_forums',
    'block_tags',
    'blog_tags',
    'gradebook',
    'mod_assignment',
    'mod_chat',
    'mod_choice',
    'mod_data',
    'mod_feedback',
    'mod_folder',
    'mod_forum',
    'mod_glossary',
    'mod_lesson',
    'mod_page',
    'mod_quiz',
    'mod_resource',
    'mod_scorm',
    'mod_survey',
    'mod_wiki',
);

$THEME->editor_sheets = array('styles_tinymce');

$THEME->layouts = array(
    // Most pages - if we encounter an unknown or a missing page type, this one is used.
    'normal' => array(
        'theme' => 'standard',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // Course page
    'course' => array(
        'theme' => 'standard',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // The site home page.
    'home' => array(
        'theme' => 'standard',
        'file' => 'home.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // Server administration scripts.
    'admin' => array(
        'theme' => 'standard',
        'file' => 'normal.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre'
    ),
    // My moodle page
    'my' => array(
        'theme' => 'standard',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),

    // Settings form pages, like course of module settings.
    'form' => array(
        'theme' => 'standard',
        'file' => 'normal.php',
        'regions' => array(),
    ),
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => array(
        'theme' => 'standard',
        'file' => 'minimal.php',
        'regions' => array(),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'theme' => 'standard',
        'file' => 'frametop.php',
        'regions' => array(),
    ),
    // Embeded pages, like iframe embeded in moodleform
    'embedded' => array(
        'theme' => 'standard',
        'file' => 'embedded.php',
        'regions' => array(),
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, and it is good idea if it does not have links to
    // other places - for example there should not be a home link in the footer...
    'maintenance' => array(
        'theme' => 'standard',
        'file' => 'minimal.php',
        'regions' => array(),
    ),
);

