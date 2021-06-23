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
 * Atto preview library functions
 *
 * @package    atto_preview
 * @copyright  2015 Daniel Thies <dthies@ccal.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Set params for this plugin.
 *
 * @param string $elementid
 * @param stdClass $options - the options for the editor, including the context.
 * @param stdClass $fpoptions - unused.
 */
function atto_preview_params_for_js($elementid, $options, $fpoptions) {
    global $CFG;
    $context = $options['context'];
    if (!$context) {
        $context = context_system::instance();
    }
    return array('contextid' => $context->id,
                 'previewurl' => $CFG->wwwroot . '/lib/editor/atto/plugins/preview/preview.php',
                 'sesskey' => sesskey());

}

/**
 * Initialise the js strings required for this module.
 */
function atto_preview_strings_for_js() {
    global $PAGE;

    $PAGE->requires->strings_for_js(array('pluginname'), 'atto_preview');
}

