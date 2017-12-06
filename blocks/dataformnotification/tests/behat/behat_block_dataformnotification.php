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
 * Steps definitions related with the dataform activity.
 *
 * @package    mod_dataform
 * @category   tests
 * @copyright  2013 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given;
use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Gherkin\Node\PyStringNode as PyStringNode;

/**
 * Dataform-related steps definitions.
 *
 * @package    block_dataformnotification
 * @category   tests
 * @copyright  2015 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_dataformnotification extends behat_base {

    /**
     * Creates a dataform notification rule.
     *
     * @Given /^the following dataform notification rule exists:$/
     * @param TableNode $data
     */
    public function the_following_dataform_notification_rule_exists(TableNode $data) {
        global $DB;

        $datahash = $data->getRowsHash();

        // Get the dataform.
        $idnumber = $datahash['dataform'];
        if (!$dataformid = $DB->get_field('course_modules', 'instance', array('idnumber' => $idnumber))) {
            throw new Exception('The specified dataform with idnumber "' . $idnumber . '" does not exist');
        }
        $df = new \mod_dataform_dataform($dataformid);

        $blockname = 'dataformnotification';

        // Compile the rule config.
        $config = new stdClass;
        $config->name = $datahash['name'];
        $config->enabled = (int) !empty($datahash['enabled']);

        $config->timefrom = !empty($datahash['from']) ? strtotime($datahash['from']) : 0;
        $config->timeto = !empty($datahash['to']) ? strtotime($datahash['to']) : 0;

        if (!empty($datahash['views'])) {
            $config->viewselection = 1;
            $config->views = explode(',', $datahash['views']);
        }

        $config->events = explode(',', $datahash['events']);

        $config->messagetype = (int) !empty($datahash['messagetype']);
        $config->subject = !empty($datahash['subject']) ? $datahash['subject'] : null;
        $config->contenttext = !empty($datahash['contenttext']) ? $datahash['contenttext'] : null;
        $config->contentview = !empty($datahash['contentview']) ? $datahash['contentview'] : null;
        $config->messageformat = !empty($datahash['messageformat']) ? $datahash['messageformat'] : FORMAT_PLAIN;
        $config->sender = !empty($datahash['sender']) ? $datahash['sender'] : \core_user::NOREPLY_USER;
        $config->recipient = array(
            'admin' => (int) !empty($datahash['recipientadmin']),
            'support' => (int) !empty($datahash['recipientsupport']),
            'author' => (int) !empty($datahash['recipientauthor']),
            'role' => (int) !empty($datahash['recipientrole']),
            'username' => !empty($datahash['recipientusername']) ? $datahash['recipientusername'] : null,
            'email' => !empty($datahash['recipientemail']) ? $datahash['recipientemail'] : null,
        );

        // Custom search.
        $filterformhelper = '\mod_dataform\helper\filterform';
        $searchies = array();
        $i = 0;
        foreach ($datahash as $key => $searchoption) {
            if (strpos($key, 'search') !== 0) {
                continue;
            }
            if (empty($searchoption)) {
                continue;
            }
            $keys = array("searchandor$i", "searchfield$i", "searchnot$i", "searchoperator$i", "searchvalue$i");
            $criterion = array_combine($keys, explode('#', $searchoption));
            $searchies = array_merge($searchies, $criterion);
            $i++;
        }
        $customsearch = $filterformhelper::get_custom_search_from_form((object) $searchies, $df->id);
        if ($customsearch) {
            $config->customsearch = $customsearch;
        }

        $configdata = base64_encode(serialize($config));

        // Create the rule block.
        $bi = new stdClass;
        $bi->blockname = $blockname;
        $bi->parentcontextid = $df->context->id;
        $bi->showinsubcontexts = 0;
        $bi->pagetypepattern = 'mod-dataform-notification-index';
        $bi->defaultregion = 'side-pre';
        $bi->defaultweight = 0;
        $bi->configdata = $configdata;
        $bi->id = $DB->insert_record('block_instances', $bi);

        // Ensure the block context is created.
        $blockcontext = context_block::instance($bi->id);

        // If the new instance was created, allow it to do additional setup.
        if ($block = block_instance($blockname, $bi)) {
            $block->instance_create();
        }

        // Set permissions.
        foreach ($datahash as $key => $value) {
            if (strpos($key, 'permission') === 0 and $value) {
                list($role, $perm, $capability) = array_map('trim', explode(' ', trim($value)));
                // Get the role id.
                $roleid = $DB->get_field('role', 'id', array('shortname' => $role));
                $permission = constant('CAP_'. strtoupper($perm));
                assign_capability($capability, $permission, $roleid, $blockcontext->id);
                $blockcontext->mark_dirty();
            }
        }
    }

}
