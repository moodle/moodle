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
 * Steps definitions related with the dataformfield_time.
 *
 * @package    dataformfield_time
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
 * Dataform time field steps definitions.
 *
 * @package    dataformfield_time
 * @category   tests
 * @copyright  2015 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_dataformfield_time extends behat_base {

    /**
     * Creates a Dataform instance.
     *
     * @Given /^the following dataformfield time exists:$/
     * @param TableNode $data
     */
    public function the_following_dataformfield_time_exists(TableNode $data) {
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
            $field = $df->field_manager->add_field('dataformview');
        } else {
            $field = $df->field_manager->get_field($instance);
        }

        // Date only.
        $field->param1 = null;
        if (!empty($datahash['date only'])) {
            $field->param1 = 1;
        }

        // Masked.
        $field->param5 = null;
        if (!empty($datahash['masked'])) {
            $field->param5 = 1;
        }

        // Start year.
        $field->param2 = null;
        if (!empty($datahash['start year'])) {
            $field->param2 = $datahash['start year'];
        }

        // stop year.
        $field->param3 = null;
        if (!empty($datahash['stop year'])) {
            $field->param3 = $datahash['stop year'];
        }

        // Display format.
        $field->param4 = null;
        if (!empty($datahash['display format'])) {
            $field->param4 = $datahash['display format'];
        }

        // Default content.
        $field->default = null;
        if (!empty($datahash['default content'])) {
            $field->default = $datahash['default content'];
        }

        $field->update($field->data);
    }

}
