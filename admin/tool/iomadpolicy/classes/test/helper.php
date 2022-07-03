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
 * Provides the {@link \tool_iomadpolicy\test\helper} class.
 *
 * @package     tool_iomadpolicy
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadpolicy\test;

use tool_iomadpolicy\api;
use tool_iomadpolicy\iomadpolicy_version;

defined('MOODLE_INTERNAL') || die();

/**
 * Provides some helper methods for unit-tests.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Helper method that creates a new iomadpolicy for testing
     *
     * @param array $params
     * @return iomadpolicy_version
     */
    public static function add_iomadpolicy($params = []) {
        static $counter = 0;
        $counter++;

        $defaults = [
            'name' => 'Policy '.$counter,
            'summary_editor' => ['text' => "P$counter summary", 'format' => FORMAT_HTML, 'itemid' => 0],
            'content_editor' => ['text' => "P$counter content", 'format' => FORMAT_HTML, 'itemid' => 0],
        ];

        $params = (array)$params + $defaults;
        $formdata = api::form_iomadpolicydoc_data(new iomadpolicy_version(0));
        foreach ($params as $key => $value) {
            $formdata->$key = $value;
        }
        return api::form_iomadpolicydoc_add($formdata);
    }

    /**
     * Helper method that prepare a iomadpolicy document with some versions.
     *
     * @param int $numversions The number of iomadpolicy versions to create.
     * @return array Array with all the iomadpolicy versions created.
     */
    public static function create_versions($numversions = 2) {
        // Prepare a iomadpolicy document with some versions.
        $iomadpolicy = self::add_iomadpolicy([
            'name' => 'Test iomadpolicy',
            'revision' => 'v1',
        ]);

        for ($i = 2; $i <= $numversions; $i++) {
            $formdata = api::form_iomadpolicydoc_data($iomadpolicy);
            $formdata->revision = 'v'.$i;
            api::form_iomadpolicydoc_update_new($formdata);
        }

        $list = api::list_policies($iomadpolicy->get('iomadpolicyid'));

        return $list[0]->draftversions;
    }
}
