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
 * Step definition for tool_iomadpolicy
 *
 * @package    tool_iomadpolicy
 * @category   test
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Step definition for tool_iomadpolicy
 *
 * @package    tool_iomadpolicy
 * @category   test
 * @copyright  2018 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_iomadpolicy extends behat_base {

    /**
     * Click on an entry in the edit menu.
     *
     * @Given /^the following policies exist:$/
     *
     * Supported table fields:
     *
     * - Name: Policy name (required).
     * - Revision: Revision name (iomadpolicy version).
     * - Status: Policy version status - 'draft', 'active' or 'archived'. Defaults to 'active'.
     * - Audience: Target users - 'guest', 'all' or 'loggedin'. Default to 'all'.
     * - Type: 0 - site iomadpolicy, 1 - privacy iomadpolicy, 2 - third party iomadpolicy, 99 - other.
     * - Summary: Policy summary text.
     * - Content: Policy full text.
     * - Agreement style (agreementstyle): 0 - On the consent page, 1 - On its own page
     * - Agreement optional (optional): 0 - Compulsory iomadpolicy, 1 - Optional iomadpolicy
     *
     * @param TableNode $data
     */
    public function the_following_policies_exist(TableNode $data) {
        global $CFG;
        if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_iomadpolicy') {
            throw new Exception('Site iomadpolicy handler is not set to "tool_iomadpolicy"');
        }

        $fields = [
            'name',
            'revision',
            'iomadpolicy',
            'status',
            'audience',
            'type',
            'content',
            'summary',
            'agreementstyle',
            'optional',
        ];

        // Associative array "iomadpolicy identifier" => id in the database .
        $policies = [];

        foreach ($data->getHash() as $elementdata) {
            $data = (object)[
                'audience' => \tool_iomadpolicy\iomadpolicy_version::AUDIENCE_ALL,
                'archived' => 0,
                'type' => 0
            ];
            $elementdata = array_change_key_case($elementdata, CASE_LOWER);
            foreach ($elementdata as $key => $value) {
                if ($key === 'iomadpolicy') {
                    if (array_key_exists($value, $policies)) {
                        $data->iomadpolicyid = $policies[$value];
                    }
                } else if ($key === 'status') {
                    $data->archived = ($value === 'archived');
                } else if ($key === 'audience') {
                    if ($value === 'guest') {
                        $data->audience = \tool_iomadpolicy\iomadpolicy_version::AUDIENCE_GUESTS;
                    } else if ($value === 'loggedin') {
                        $data->audience = \tool_iomadpolicy\iomadpolicy_version::AUDIENCE_LOGGEDIN;
                    }
                } else if (($key === 'summary' || $key === 'content') && !empty($value)) {
                    $data->{$key.'_editor'} = ['text' => $value, 'format' => FORMAT_MOODLE];
                } else if (in_array($key, $fields) && $value !== '') {
                    $data->$key = $value;
                }
            }
            if (empty($data->name) || empty($data->content_editor) || empty($data->summary_editor)) {
                throw new Exception('Policy is missing at least one of the required fields: name, content, summary');
            }

            if (!empty($data->iomadpolicyid)) {
                $version = tool_iomadpolicy\api::form_iomadpolicydoc_update_new($data);
            } else {
                $version = \tool_iomadpolicy\api::form_iomadpolicydoc_add($data);
            }

            if (!empty($elementdata['iomadpolicy'])) {
                $policies[$elementdata['iomadpolicy']] = $version->get('iomadpolicyid');
            }
            if (empty($elementdata['status']) || $elementdata['status'] === 'active') {
                \tool_iomadpolicy\api::make_current($version->get('id'));
            }
        }
    }
}
