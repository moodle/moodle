<?php

/**
 * Version file for component 'local_gws_query_practice'
 *
 * @package    local_gws_query_practice
 * @copyright  2019 onwards GWS
 * @developer  Brian kremer (greatwallstudio.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 20190721;
$local_requires  = 20140512;        // Version 2.7 - See http://docs.moodle.org/dev/Moodle_Versions
$plugin->component
                 = 'local_gws_query_practice';  //frankenstyle plugin name, strongly recommended. It is used for installation and upgrade diagnostics.
$plugin->maturity
                 = MATURITY_BETA;  //how stable the plugin is: MATURITY_ALPHA, MATURITY_BETA, MATURITY_RC, MATURITY_STABLE (Moodle 2.0 and above)
//$local->release   = '0.0 (Build 2012092700)'; //Human-readable version name
//$plugin->dependencies = array(
//   'mod_forum' => ANY_VERSION,
//   'mod_data'  => TODO
//);  //list of other plugins that are required for this plugin to work (Moodle 2.2 and above) In this example, the plugin requires any version of the forum activity and version '20100020300' (or above) of the database activity
