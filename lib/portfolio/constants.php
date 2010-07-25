<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    core
 * @subpackage portfolio
 * @author     Penny Leach <penny@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This file contains all the defined constants to do with portfolios.
 */

defined('MOODLE_INTERNAL') || die();

// ************************************************** //
// EXPORT STAGE CONSTANTS
// ************************************************** //

/**
* display a form to the user
* this one might not be used if neither
* the plugin, or the caller has any config.
*/
define('PORTFOLIO_STAGE_CONFIG', 1);

/**
* summarise the form and ask for confirmation
* if we skipped PORTFOLIO_STAGE_CONFIG,
* just confirm the send.
*/
define('PORTFOLIO_STAGE_CONFIRM', 2);

/**
* either queue the event and skip to PORTFOLIO_STAGE_FINISHED
* or continue to PORTFOLIO_STAGE_PACKAGE
*/

define('PORTFOLIO_STAGE_QUEUEORWAIT', 3);

/**
* package up the various bits
* during this stage both the caller
* and the plugin get their package methods called
*/
define('PORTFOLIO_STAGE_PACKAGE', 4);

/*
* the portfolio plugin must send the file
*/
define('PORTFOLIO_STAGE_SEND', 5);

/**
* cleanup the temporary area
*/
define('PORTFOLIO_STAGE_CLEANUP', 6);

/**
* display the "finished notification"
*/
define('PORTFOLIO_STAGE_FINISHED', 7);



// ************************************************** //
// EXPORT FORMAT CONSTANTS
// these should always correspond to a string
// in the portfolio module, called format_{$value}
// ************************************************** //


/**
* file - the most basic fallback format.
* this should always be supported
* in remote system.s
*/
define('PORTFOLIO_FORMAT_FILE', 'file');

/**
* moodle backup - the plugin needs to be able to write a complete backup
* the caller need to be able to export the particular XML bits to insert
* into moodle.xml (?and the file bits if necessary)
*/
define('PORTFOLIO_FORMAT_MBKP', 'mbkp');

/**
* richhtml - like html but with attachments.
*/
define('PORTFOLIO_FORMAT_RICHHTML', 'richhtml');


/**
* plainhtml - a single html representation - no attachments
*/
define('PORTFOLIO_FORMAT_PLAINHTML', 'plainhtml');

/**
* image - subtype of file
*/
define('PORTFOLIO_FORMAT_IMAGE', 'image');

/**
* video - subtype of file
*/
define('PORTFOLIO_FORMAT_VIDEO', 'video');

/**
* text - subtype of file
*/
define('PORTFOLIO_FORMAT_TEXT', 'text');

/**
* pdf - subtype of file
*/
define('PORTFOLIO_FORMAT_PDF', 'pdf');

/**
* document - subtype of file
*/
define('PORTFOLIO_FORMAT_DOCUMENT', 'document');

/**
* document - subtype of file
*/
define('PORTFOLIO_FORMAT_SPREADSHEET', 'spreadsheet');

/**
* document - subtype of file
*/
define('PORTFOLIO_FORMAT_PRESENTATION', 'presentation');

/**
 * abstract - just used to say, "we support all these"
 */
define('PORTFOLIO_FORMAT_RICH', 'rich');

/**
 * leap2a http://wiki.cetis.ac.uk/LEAP_2.0
 * supported by mahara and and others
 */
define('PORTFOLIO_FORMAT_LEAP2A', 'leap2a');

// ************************************************** //
//  EXPORT TIME LEVELS
// these should correspond to a string
// in the portfolio module, called time_{$value}
// ************************************************** //


/**
* no delay. don't even offer the user the option
* of not waiting for the transfer
*/
define('PORTFOLIO_TIME_LOW', 'low');

/**
* a small delay. user can still easily opt to
* watch this transfer and wait.
*/
define('PORTFOLIO_TIME_MODERATE', 'moderate');

/**
* slow. the user really should not be given the option
* to choose this.
*/
define('PORTFOLIO_TIME_HIGH', 'high');

/**
* very slow, or immediate transfers not supported
*/
define('PORTFOLIO_TIME_FORCEQUEUE', 'queue');

// ************************************************** //
// BUTTON FORMATS
// available ways to add the portfolio export to a page
// ************************************************** //

/**
* a whole form, containing a drop down menu (where necessary)
* and a submit button
*/
define('PORTFOLIO_ADD_FULL_FORM', 1);


/**
* a whole form, containing a drop down menu (where necessary)
* but has an icon instead of a button to submit
*/
define('PORTFOLIO_ADD_ICON_FORM', 2);

/**
* just an icon with a link around it (yuk, as will result in a long url
* only use where necessary)
*/
define('PORTFOLIO_ADD_ICON_LINK', 3);

/**
* just some text with a link around it (yuk, as will result in a long url
* only use where necessary)
*/
define('PORTFOLIO_ADD_TEXT_LINK', 4);

/**
 * hacky way to turn the button class into a url to redirect to
 * this replaces the old portfolio_fake_add_url function
 */
define('PORTFOLIO_ADD_FAKE_URL', 5);
