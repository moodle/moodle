<?php
// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

/**
 * IPEMIS.
 *
 * @package    theme_IPEMIS
 * @copyright  2022 DSi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// This is the version of the plugin.
$plugin->version = '2022061700';

// This is the version of Moodle this plugin requires.
$plugin->requires = '2022041200';

// This is the component name of the plugin - it always starts with 'theme_'
// for themes and should be the same as the name of the folder.
$plugin->component = 'theme_ipemis_classic';

// This is a list of plugins, this plugin depends on (and their versions).
$plugin->dependencies = [
    'theme_classic' => '2022041900'
];