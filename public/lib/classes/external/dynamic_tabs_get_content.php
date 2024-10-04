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

declare(strict_types=1);

namespace core\external;

use coding_exception;
use context_system;
use core\output\dynamic_tabs\base;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External method for getting tab contents
 *
 * @package     core
 * @copyright   2021 David Matamoros <davidmc@moodle.com> based on code from Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dynamic_tabs_get_content extends external_api {

    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tab' => new external_value(PARAM_RAW_TRIMMED, 'Tab class', VALUE_REQUIRED),
            'jsondata' => new external_value(PARAM_RAW, 'Json-encoded data', VALUE_REQUIRED),
        ]);
    }

    /**
     * Tab content
     *
     * @param string $tabclass class of the tab
     * @param string $jsondata
     * @return array
     */
    public static function execute(string $tabclass, string $jsondata): array {
        global $PAGE, $OUTPUT;

        [
            'tab' => $tabclass,
            'jsondata' => $jsondata,
        ] = self::validate_parameters(self::execute_parameters(), [
            'tab' => $tabclass,
            'jsondata' => $jsondata,
        ]);

        $data = @json_decode($jsondata, true);

        $context = context_system::instance();
        self::validate_context($context);

        // This call is needed to avoid debug messages on webserver log.
        $PAGE->set_url('/');
        // This call is needed to initiate moodle page.
        $OUTPUT->header();

        if (!class_exists($tabclass) || !is_subclass_of($tabclass, base::class)) {
            throw new coding_exception('unknown dynamic tab class', $tabclass);
        }

        /** @var base $tab */
        $tab = new $tabclass($data);
        $tab->require_access();
        $PAGE->start_collecting_javascript_requirements();

        $content = $tab->export_for_template($PAGE->get_renderer('core'));
        $jsfooter = $PAGE->requires->get_end_code();
        return [
            'template' => $tab->get_template(),
            'content' => json_encode($content),
            'javascript' => $jsfooter,
        ];
    }

    /**
     * External method return value
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'template' => new external_value(PARAM_PATH, 'Template name'),
            'content' => new external_value(PARAM_RAW, 'JSON-encoded data for template'),
            'javascript' => new external_value(PARAM_RAW, 'JavaScript fragment'),
        ]);
    }
}
