<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Atto ClickView plugin library functions.
 *
 * @package     atto_clickview
 * @copyright   2021 ClickView Pty. Limited <info@clickview.com.au>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_clickview\Utils;

/**
 * Initialise the js strings required for this module.
 *
 * @throws moodle_exception
 */
function atto_clickview_strings_for_js() {
    global $PAGE;

    $strings = [
            'pluginname',
    ];

    $PAGE->requires->strings_for_js($strings, 'atto_clickview');
    $PAGE->requires->js(Utils::get_eventsapi_url());
}

/**
 * Return the js params required for this module.
 *
 * @return array of additional params to pass to javascript init function for this module.
 */
function atto_clickview_params_for_js(): array {
    $params = [];

    $params['iframe'] = Utils::get_iframe_html();
    $params['iframeurl'] = Utils::get_iframe_url();
    $params['consumerkey'] = Utils::get_consumerkey();

    $config = get_config('local_clickview');

    foreach ($config as $key => $value) {
        $params[$key] = $value;
    }

    return $params;
}
