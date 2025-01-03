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
 * Purpose imggen methods
 *
 * @package    aipurpose_imggen
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace aipurpose_imggen;

use coding_exception;
use local_ai_manager\base_purpose;
use local_ai_manager\local\connector_factory;
use local_ai_manager\local\userinfo;

/**
 * Purpose imggen methods
 *
 * @package    aipurpose_imggen
 * @copyright  ISB Bayern, 2024
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class purpose extends base_purpose {

    #[\Override]
    public function get_additional_purpose_options(): array {
        global $USER;
        $userinfo = new userinfo($USER->id);
        $factory = \core\di::get(connector_factory::class);
        $connector = $factory->get_connector_by_purpose($this->get_plugin_name(), $userinfo->get_role());
        $instance = $connector->get_instance();
        if (!in_array($this->get_plugin_name(), $instance->supported_purposes())) {
            // Currently selected instance does not support tts, so we do not add any options.
            return [];
        }

        // In this case we do not only provide additional purpose options, but also get them from the currently used connector.
        $allowedoptionkeys = ['sizes' => []];
        $connectoroptions = $connector->get_available_options();
        foreach ($connectoroptions as $key => $value) {
            if (!in_array($key, array_keys($allowedoptionkeys))) {
                throw new coding_exception('You must not define the option ' . $key . ' in the connector class');
            }
        }
        $returnoptions = ['filename' => PARAM_TEXT] + $connectoroptions;
        return $returnoptions;
    }

    #[\Override]
    public function format_output(string $output): string {
        // We do not want any formatting.
        // The clean_param is only to be extra safe, there shouldn't be any tags in the output anyway.
        return clean_param($output, PARAM_NOTAGS);
    }

}
