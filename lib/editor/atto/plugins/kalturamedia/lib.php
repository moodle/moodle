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
 * Atto text editor integration version file.
 *
 * @package    atto_media
 * @copyright  2013 Damyon Wiese  <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialise the js strings required for this plugin
 */
function atto_kalturamedia_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array('popuptitle', 'embedbuttontext', 'browse_and_embed'), 'atto_kalturamedia');
}

function atto_kalturamedia_params_for_js($elementid, $options, $fpoptions) {
    global $CFG;
    require_once($CFG->dirroot.'/local/kaltura/locallib.php');

    $context = $options['context'];
    if (!$context) {
        $context = context_system::instance();
    }
    
    return array(
        'kalturauritoken' => KALTURA_URI_TOKEN,
        'contextid' => $context->id,
        'kafuri' => local_kaltura_get_config()->kaf_uri
        );
}
