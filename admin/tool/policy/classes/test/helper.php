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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Provides the {@link \tool_policy\test\helper} class.
 *
 * @package     tool_policy
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy\test;

use tool_policy\api;
use tool_policy\policy_version;

defined('MOODLE_INTERNAL') || die();

/**
 * Provides some helper methods for unit-tests.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Helper method that creates a new policy for testing
     *
     * @param array $params
     * @return policy_version
     */
    public static function add_policy($params = []) {
        static $counter = 0;
        $counter++;

        $defaults = [
            'name' => 'Policy '.$counter,
            'summary_editor' => ['text' => "P$counter summary", 'format' => FORMAT_HTML, 'itemid' => 0],
            'content_editor' => ['text' => "P$counter content", 'format' => FORMAT_HTML, 'itemid' => 0],
        ];

        $params = (array)$params + $defaults;
        $formdata = api::form_policydoc_data(new policy_version(0));
        foreach ($params as $key => $value) {
            $formdata->$key = $value;
        }
        return api::form_policydoc_add($formdata);
    }

    /**
     * Helper method that prepare a policy document with some versions.
     *
     * @param int $numversions The number of policy versions to create.
     * @return array Array with all the policy versions created.
     */
    public static function create_versions($numversions = 2) {
        // Prepare a policy document with some versions.
        $policy = self::add_policy([
            'name' => 'Test policy',
            'revision' => 'v1',
        ]);

        for ($i = 2; $i <= $numversions; $i++) {
            $formdata = api::form_policydoc_data($policy);
            $formdata->revision = 'v'.$i;
            api::form_policydoc_update_new($formdata);
        }

        $list = api::list_policies($policy->get('policyid'));

        return $list[0]->draftversions;
    }
}
