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
 * This file contains all the defined constants to do with portfolios.
 *
 * @package core_portfolio
 * @copyright 2008 Penny Leach <penny@catalyst.net.nz>, Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//EXPORT STAGE CONSTANTS


/**
 * PORTFOLIO_STAGE_CONFIG - display a form to the user this one might not be
 *                          used if neither the plugin, or the caller has any config.
 */
define('PORTFOLIO_STAGE_CONFIG', 1);

/**
 * PORTFOLIO_STAGE_CONFIRM - summarise the form and ask for confirmation
 *                           if we skipped PORTFOLIO_STAGE_CONFIG,
 *                           just confirm the send.
 */
define('PORTFOLIO_STAGE_CONFIRM', 2);

/**
 * PORTFOLIO_STAGE_QUEUEORWAIT - either queue the event and skip to PORTFOLIO_STAGE_FINISHED
 */
define('PORTFOLIO_STAGE_QUEUEORWAIT', 3);

/**
 * PORTFOLIO_STAGE_PACKAGE - package up the various bits during this stage both the caller
 *                           and the plugin get their package methods called
 */
define('PORTFOLIO_STAGE_PACKAGE', 4);

/**
 * PORTFOLIO_STAGE_SEND - the portfolio plugin must send the file
 */
define('PORTFOLIO_STAGE_SEND', 5);

/**
 * PORTFOLIO_STAGE_CLEANUP - cleanup the temporary area
 */
define('PORTFOLIO_STAGE_CLEANUP', 6);

/**
 * PORTFOLIO_STAGE_FINISHED - display the "finished notification"
 */
define('PORTFOLIO_STAGE_FINISHED', 7);




// EXPORT FORMAT CONSTANTS
// These should always correspond to a string in the portfolio module, called format_{$value}


/**
 * PORTFOLIO_FORMAT_FILE - the most basic fallback format. this should always be supported
 *                         in remote system.s
 */
define('PORTFOLIO_FORMAT_FILE', 'file');

/**
 * PORTFOLIO_FORMAT_MBKP - the plugin needs to be able to write a complete backup
 *                         the caller need to be able to export the particular XML bits to insert
 *                         into moodle.xml (?and the file bits if necessary)
 */
define('PORTFOLIO_FORMAT_MBKP', 'mbkp');

/**
 * PORTFOLIO_FORMAT_RICHHTML - like html but with attachments.
 */
define('PORTFOLIO_FORMAT_RICHHTML', 'richhtml');

/**
 * PORTFOLIO_FORMAT_PLAINHTML - a single html representation - no attachments
 */
define('PORTFOLIO_FORMAT_PLAINHTML', 'plainhtml');

/**
 * PORTFOLIO_FORMAT_IMAGE - subtype of file
 */
define('PORTFOLIO_FORMAT_IMAGE', 'image');

/**
 * PORTFOLIO_FORMAT_VIDEO - subtype of file
 */
define('PORTFOLIO_FORMAT_VIDEO', 'video');

/**
 * PORTFOLIO_FORMAT_TEXT - subtype of file
 */
define('PORTFOLIO_FORMAT_TEXT', 'text');

/**
 * PORTFOLIO_FORMAT_PDF - subtype of file
 */
define('PORTFOLIO_FORMAT_PDF', 'pdf');

/**
 * PORTFOLIO_FORMAT_DOCUMENT - subtype of file
 */
define('PORTFOLIO_FORMAT_DOCUMENT', 'document');

/**
 * PORTFOLIO_FORMAT_SPREADSHEET - subtype of file
 */
define('PORTFOLIO_FORMAT_SPREADSHEET', 'spreadsheet');

/**
 * PORTFOLIO_FORMAT_PRESENTATION - subtype of file
 */
define('PORTFOLIO_FORMAT_PRESENTATION', 'presentation');

/**
 * PORTFOLIO_FORMAT_RICH - just used to say, "we support all these"
 */
define('PORTFOLIO_FORMAT_RICH', 'rich');

/**
 * PORTFOLIO_FORMAT_LEAP2A - supported by mahara and and others {http://wiki.cetis.ac.uk/LEAP_2.0}
 */
define('PORTFOLIO_FORMAT_LEAP2A', 'leap2a');

// EXPORT TIME LEVELS
// These should correspond to a string in the portfolio module, called time_{$value}

/**
 * PORTFOLIO_TIME_LOW - no delay. don't even offer the user the option
 *                      of not waiting for the transfer
 */
define('PORTFOLIO_TIME_LOW', 'low');

/**
 * PORTFOLIO_TIME_MODERATE - a small delay. user can still easily opt to
 *                           watch this transfer and wait.
 */
define('PORTFOLIO_TIME_MODERATE', 'moderate');

/**
 * PORTFOLIO_TIME_HIGH - slow. the user really should not be given the option
 *                       to choose this.
 */
define('PORTFOLIO_TIME_HIGH', 'high');

/**
 * PORTFOLIO_TIME_FORCEQUEUE - very slow, or immediate transfers not supported
 */
define('PORTFOLIO_TIME_FORCEQUEUE', 'queue');

 // BUTTON FORMATS
 // Available ways to add the portfolio export to a page

/**
 * PORTFOLIO_ADD_FULL_FORM - a whole form, containing a drop down menu (where necessary)
 *                           and a submit button
 */
define('PORTFOLIO_ADD_FULL_FORM', 1);


/**
 * PORTFOLIO_ADD_ICON_FORM - a whole form, containing a drop down menu (where necessary)
 *                           but has an icon instead of a button to submit
 */
define('PORTFOLIO_ADD_ICON_FORM', 2);

/**
 * PORTFOLIO_ADD_ICON_LINK - just an icon with a link around it (yuk, as will result in a long url
 *                           only use where necessary)
 */
define('PORTFOLIO_ADD_ICON_LINK', 3);

/**
 * PORTFOLIO_ADD_TEXT_LINK - just some text with a link around it (yuk, as will result in a long url
 * only use where necessary)
 */
define('PORTFOLIO_ADD_TEXT_LINK', 4);

/**
 * PORTFOLIO_ADD_FAKE_URL - hacky way to turn the button class into a url to redirect to
 *                          this replaces the old portfolio_fake_add_url function
 */
define('PORTFOLIO_ADD_FAKE_URL', 5);

/**
 * PORTFOLIO_ADD_MOODULE_URL - hacky way to turn the button class into a moodle_url to redirect to
 *                             this replaces the old portfolio_fake_add_url function
 */
define('PORTFOLIO_ADD_MOODLE_URL', 6);
