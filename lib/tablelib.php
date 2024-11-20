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
 * @package    core
 * @subpackage lib
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**#@+
 * These constants relate to the table's handling of URL parameters.
 */
define('TABLE_VAR_SORT',   1);
define('TABLE_VAR_HIDE',   2);
define('TABLE_VAR_SHOW',   3);
define('TABLE_VAR_IFIRST', 4);
define('TABLE_VAR_ILAST',  5);
define('TABLE_VAR_PAGE',   6);
define('TABLE_VAR_RESET',  7);
define('TABLE_VAR_DIR',    8);
/**#@-*/

/**#@+
 * Constants that indicate whether the paging bar for the table
 * appears above or below the table.
 */
define('TABLE_P_TOP',    1);
define('TABLE_P_BOTTOM', 2);
/**#@-*/

/**
 * Constant that defines the 'Show all' page size.
 */
define('TABLE_SHOW_ALL_PAGE_SIZE', 5000);
