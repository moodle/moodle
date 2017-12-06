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
 * Steps definitions related with the dataformfield_entrystate.
 *
 * @package    dataformfield_entrystate
 * @category   tests
 * @copyright  2015 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given;
use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Gherkin\Node\PyStringNode as PyStringNode;

/**
 * Dataform entrystate field steps definitions.
 *
 * @package    dataformfield_entrystate
 * @category   tests
 * @copyright  2015 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_dataformfield_entrystate extends behat_base {

    /**
     * Creates a dataformfield entrystate instance.
     *
     * @Given /^the following dataformfield entrystate exists:$/
     * @param TableNode $data
     */
    public function the_following_dataformfield_entrystate_exists(TableNode $data) {
        global $DB;

        $datahash = $data->getRowsHash();

        // Get the dataform id.
        $idnumber = $datahash['dataform'];
        if (!$dataformid = $DB->get_field('course_modules', 'instance', array('idnumber' => $idnumber))) {
            throw new Exception('The specified dataform with idnumber "' . $idnumber . '" does not exist');
        }

        $df = new \mod_dataform_dataform($dataformid);

        // Get the field or create it if does not exist.
        $params = array('dataid' => $dataformid, 'name' => $datahash['name']);
        if (!$instance = $DB->get_record('dataform_fields', $params)) {
            $field = $df->field_manager->add_field('entrystate');
        } else {
            $field = $df->field_manager->get_field($instance);
        }

        $field->name = $datahash['name'];

        // Set config (param1).
        $config = array();

        // Must have states.
        if (!empty($datahash['states'])) {
            $config['states'] = implode("\n", explode('#', trim($datahash['states'])));

            // Transitions.
            $transitions = array();
            $i = 0;
            while (isset($datahash["to$i"])) {
                if ($datahash["to$i"] === '') {
                    $i++;
                    continue;
                }

                $from = "from$i";
                $to = "to$i";
                $permission = "permission$i";
                $notification = "notification$i";

                $trans = array();
                $trans['from'] = $datahash[$from];
                $trans['to'] = $datahash[$to];

                if (!empty($datahash[$permission])) {
                    $trans['permission'] = $datahash[$permission];
                }
                if (!empty($datahash[$notification])) {
                    $trans['notification'] = $datahash[$notification];
                }
                if ($trans) {
                    $transitions[] = $trans;
                }
                $i++;
            }
            if ($transitions) {
                $config['transitions'] = $transitions;
            }
        }
        // Set param1.
        $field->param1 = $config ? base64_encode(serialize($config)) : null;

        $field->update($field->data);
    }

}
