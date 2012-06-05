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
 * This file contains all the constants and variables used
 * by the XMLDB interface
 *
 * @package    core_xmldb
 * @copyright  1999 onwards Martin Dougiamas     http://dougiamas.com
 *             2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


// ==== First, some constants to be used by actions ====
/** Default flags for class */
define('ACTION_NONE',             0);
/** The invoke function will return HTML */
define('ACTION_GENERATE_HTML',    1);
/** The invoke function will return HTML */
define('ACTION_GENERATE_XML',     2);
/** The class can have subaction */
define('ACTION_HAVE_SUBACTIONS',  1);

// ==== Now the allowed DB Field Types ====
/** Wrong DB Type */
define ('XMLDB_TYPE_INCORRECT',   0);
/** Integer */
define ('XMLDB_TYPE_INTEGER',     1);
/** Decimal number */
define ('XMLDB_TYPE_NUMBER',      2);
/** Floating Point number */
define ('XMLDB_TYPE_FLOAT',       3);
/** String */
define ('XMLDB_TYPE_CHAR',        4);
/** Text */
define ('XMLDB_TYPE_TEXT',        5);
/** Binary */
define ('XMLDB_TYPE_BINARY',      6);
/** Datetime */
define ('XMLDB_TYPE_DATETIME',    7);
/** Timestamp */
define ('XMLDB_TYPE_TIMESTAMP',   8);

// ==== Now the allowed DB Keys ====
/** Wrong DB Key */
define ('XMLDB_KEY_INCORRECT',     0);
/** Primary Keys */
define ('XMLDB_KEY_PRIMARY',       1);
/** Unique Keys */
define ('XMLDB_KEY_UNIQUE',        2);
/** Foreign Keys */
define ('XMLDB_KEY_FOREIGN',       3);
/** Check Constraints - NOT USED! */
define ('XMLDB_KEY_CHECK',         4);
/** Foreign Key + Unique Key */
define ('XMLDB_KEY_FOREIGN_UNIQUE',5);

// ==== Some other useful Constants ====
/** If the field is going to be unsigned @deprecated since 2.3 */
define ('XMLDB_UNSIGNED',        true);
/** If the field is going to be not null */
define ('XMLDB_NOTNULL',         true);
/** If the field is going to be a sequence */
define ('XMLDB_SEQUENCE',        true);
/** If the index is going to be unique */
define ('XMLDB_INDEX_UNIQUE',    true);
/**  If the index is NOT going to be unique */
define ('XMLDB_INDEX_NOTUNIQUE',false);

// ==== Some strings used widely ====
/** New line in xmldb generated files */
define ('XMLDB_LINEFEED', "\n");
/** Upgrade start in upgrade.php */
define ('XMLDB_PHP_HEADER', '    if ($oldversion < XXXXXXXXXX) {' . XMLDB_LINEFEED);
/** Upgrade end in upgrade.php */
define ('XMLDB_PHP_FOOTER', '    }' . XMLDB_LINEFEED);
