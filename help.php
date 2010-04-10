<?php
/**
 * help.php - Displays help page.
 *
 * Prints a very simple page and includes
 * page content or a string from elsewhere.
 * Usually this will appear in a popup
 * See {@link helpbutton()} in {@link lib/moodlelib.php}
 *
 * @author Martin Dougiamas
 * @package moodlecore
 */
require_once('config.php');


// Legacy url parameters - just dispaply error
$file = optional_param('file', '', PARAM_PATH);
$text = optional_param('text', 'No text to display', PARAM_CLEAN);
$module = optional_param('module', 'moodle', PARAM_ALPHAEXT);

// New get_string() parameters
// $identifier =
// $component =

die('TODO: help files will be soon reimplemented by using normal get_string().');
