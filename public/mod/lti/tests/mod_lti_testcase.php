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

use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/lti/locallib.php');

/**
 * Abstract base testcase for mod_lti unit tests.
 *
 * @package    mod_lti
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class mod_lti_testcase extends \core_external\tests\externallib_testcase {
    /**
     * Generate a tool type.
     *
     * @param string $uniqueid Each tool type needs a different base url. Provide a unique string for every tool type created.
     * @param int|null $toolproxyid Optional proxy to associate with tool type.
     * @return stdClass A tool type.
     */
    protected function generate_tool_type(string $uniqueid, ?int $toolproxyid = null): stdClass {
        // Create a tool type.
        $type = new stdClass();
        $type->state = LTI_TOOL_STATE_CONFIGURED;
        $type->name = "Test tool $uniqueid";
        $type->description = "Example description $uniqueid";
        $type->toolproxyid = $toolproxyid;
        $type->baseurl = $this->getExternalTestFileUrl("/test$uniqueid.html");
        $type->coursevisible = LTI_COURSEVISIBLE_ACTIVITYCHOOSER;
        $config = new stdClass();
        $config->lti_coursevisible = LTI_COURSEVISIBLE_ACTIVITYCHOOSER;

        $type->id = lti_add_type($type, $config);
        return $type;
    }

    /**
     * Generate a tool proxy.
     *
     * @param string $uniqueid Each tool proxy needs a different reg url. Provide a unique string for every tool proxy created.
     * @return stdClass A tool proxy.
     */
    protected function generate_tool_proxy(string $uniqueid): stdClass {
        // Create a tool proxy.
        $proxy = mod_lti_external::create_tool_proxy("Test proxy $uniqueid",
            $this->getExternalTestFileUrl("/proxy$uniqueid.html"), [], []);
        return (object)external_api::clean_returnvalue(mod_lti_external::create_tool_proxy_returns(), $proxy);
    }
}
