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
 * Step definition to generate database fixtures for learning plan system.
 *
 * @package    tool_lp
 * @category   test
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Exception\PendingException as PendingException;
use tool_lp\competency_framework;

/**
 * Step definition to generate database fixtures for learning plan system.
 *
 * @package    tool_lp
 * @category   test
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_lp_data_generators extends behat_base {

    /**
     * @var tool_lp data generator
     */
    protected $datageneratorlp;

    /**
     * Each element specifies:
     * - The data generator sufix used.
     * - The required fields.
     * - The mapping between other elements references and database field names.
     * @var array
     */
    protected static $elements = array(
        'frameworks' => array(
            'datagenerator' => 'framework',
            'required' => array()
        ),
        'templates' => array(
            'datagenerator' => 'template',
            'required' => array()
        ),
        'plans' => array(
            'datagenerator' => 'plan',
            'required' => array('user')
        ),
        'competencies' => array(
            'datagenerator' => 'competency',
            'required' => array('framework')
        )
    );

    /**
     * Creates the specified element. More info about available elements in http://docs.moodle.org/dev/Acceptance_testing#Fixtures.
     *
     * @Given /^the following lp "(?P<element_string>(?:[^"]|\\")*)" exist:$/
     *
     * @throws Exception
     * @throws PendingException
     * @param string    $elementname The name of the entity to add
     * @param TableNode $data
     */
    public function the_following_lp_exist($elementname, TableNode $data) {

        // Now that we need them require the data generators.
        require_once(__DIR__.'/../../../../../lib/phpunit/classes/util.php');

        if (empty(self::$elements[$elementname])) {
            throw new PendingException($elementname . ' data generator is not implemented');
        }

        $datagenerator = testing_util::get_data_generator();
        $this->datageneratorlp = $datagenerator->get_plugin_generator('tool_lp');

        $elementdatagenerator = self::$elements[$elementname]['datagenerator'];
        $requiredfields = self::$elements[$elementname]['required'];
        if (!empty(self::$elements[$elementname]['switchids'])) {
            $switchids = self::$elements[$elementname]['switchids'];
        }

        foreach ($data->getHash() as $elementdata) {

            // Check if all the required fields are there.
            foreach ($requiredfields as $requiredfield) {
                if (!isset($elementdata[$requiredfield])) {
                    throw new Exception($elementname . ' requires the field ' . $requiredfield . ' to be specified');
                }
            }

            // Switch from human-friendly references to ids.
            if (isset($switchids)) {
                foreach ($switchids as $element => $field) {
                    $methodname = 'get_' . $element . '_id';

                    // Not all the switch fields are required, default vars will be assigned by data generators.
                    if (isset($elementdata[$element])) {
                        // Temp $id var to avoid problems when $element == $field.
                        $id = $this->{$methodname}($elementdata[$element]);
                        unset($elementdata[$element]);
                        $elementdata[$field] = $id;
                    }
                }
            }

            // Preprocess the entities that requires a special treatment.
            if (method_exists($this, 'preprocess_' . $elementdatagenerator)) {
                $elementdata = $this->{'preprocess_' . $elementdatagenerator}($elementdata);
            }

            // Creates element.
            $methodname = 'create_' . $elementdatagenerator;
            if (method_exists($this->datageneratorlp, $methodname)) {
                // Using data generators directly.
                $this->datageneratorlp->{$methodname}($elementdata);

            } else if (method_exists($this, 'process_' . $elementdatagenerator)) {
                // Using an alternative to the direct data generator call.
                $this->{'process_' . $elementdatagenerator}($elementdata);
            } else {
                throw new PendingException($elementname . ' data generator is not implemented');
            }
        }
    }

    /**
     * Adapt creating competency from framework idnumber or frameworkid.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_competency($data) {
        if (isset($data['framework'])) {
            $framework = competency_framework::get_record(array('idnumber' => $data['framework']));
            if ($framework) {
                $data['competencyframeworkid'] = $framework->get_id();
            } else {
                $framework = competency_framework::get_record(array('id' => $data['framework']));
                if ($framework) {
                    $data['competencyframeworkid'] = $framework->get_id();
                } else {
                    throw new Exception('Could not resolve framework with idnumber or id : "' . $data['category'] . '"');
                }
            }
        }
        unset($data['framework']);
        return $data;
    }

    /**
     * Adapt creating plan from user username.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_plan($data) {
        global $DB;

        if (isset($data['user'])) {
            $user = $DB->get_record('user', array('username' => $data['user']), '*', MUST_EXIST);
            $data['userid'] = $user->id;
        }
        unset($data['user']);
        return $data;
    }

}
