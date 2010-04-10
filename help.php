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

define('NO_MOODLE_COOKIES', true);

require_once('config.php');

$identifier = required_param('identifier', PARAM_SAFEDIR);
$component  = required_param('component', PARAM_SAFEDIR);
$lang       = required_param('component', PARAM_LANG);

if (!$lang) {
    $lang = 'en';
}

$SESSION->lang = $lang; // does not actually modify session because we do not use cookies here

// send basic headers only, we do not need full html page here
@header('Content-Type: text/plain; charset=utf-8');

if (strpos('_hlp', $identifier) === false) {
    echo '<strong>Old 1.9 style help files need to be converted to standard strings with "_hlp" suffix: '.$component.'/'.$identifier.'</strong>';
    die;
}

echo get_string($identifier, $component);
