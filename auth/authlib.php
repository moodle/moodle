<?php
/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Multiple plugin authentication
 * Support library
 *
 * 2006-08-28  File created, AUTH return values defined.
 */

/**
 * Returned when the login was successful.
 */
define('AUTH_OK',     0);

/**
 * Returned when the login was unsuccessful.
 */
define('AUTH_FAIL',   1);

/**
 * Returned when the login was denied (a reason for AUTH_FAIL).
 */
define('AUTH_DENIED', 2);

/**
 * Returned when some error occurred (a reason for AUTH_FAIL).
 */
define('AUTH_ERROR',  4);

?>
