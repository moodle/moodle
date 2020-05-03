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
 * External API to generate and return the URL of the feedback site.
 *
 * @package    core
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\external\userfeedback;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;

/**
 * The external API to generate and return the feedback url.
 *
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generate_url extends external_api {
    /**
     * Returns description of parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id of the page the user is in'),
        ]);
    }

    /**
     * Prepare and return the URL of the feedback site
     *
     * @param int $contextid The context id
     * @return \stdClass
     */
    public static function execute(int $contextid) {
        global $PAGE;

        external_api::validate_parameters(self::execute_parameters(), ['contextid' => $contextid]);

        $context = \context::instance_by_id($contextid);
        self::validate_context($context);
        $PAGE->set_context($context);

        return \core_userfeedback::make_link()->out(false);
    }

    /**
     * Returns description of method result value
     *
     * @return external_value
     */
    public static function execute_returns() {
        return new external_value(PARAM_URL, 'Feedback site\'s URL');
    }
}
