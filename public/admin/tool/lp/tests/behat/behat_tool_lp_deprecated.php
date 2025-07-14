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

require_once(__DIR__ . '/../../../../../lib/behat/behat_deprecated_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Tester\Exception\PendingException as PendingException;
use core_competency\competency;
use core_competency\competency_framework;
use core_competency\plan;
use core_competency\user_evidence;

/**
 * Step definition to generate database fixtures for learning plan system.
 *
 * @package    tool_lp
 * @category   test
 * @copyright  2016 Issam Taboubi <issam.taboubi@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_tool_lp_deprecated extends behat_deprecated_base {

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
        ),
        'userevidence' => array(
            'datagenerator' => 'user_evidence',
            'required' => array('user')
        ),
        'plancompetencies' => array(
            'datagenerator' => 'plan_competency',
            'required' => array('plan', 'competency')
        ),
        'userevidencecompetencies' => array(
            'datagenerator' => 'user_evidence_competency',
            'required' => array('userevidence', 'competency')
        ),
        'usercompetencies' => array(
            'datagenerator' => 'user_competency',
            'required' => array('user', 'competency')
        ),
        'usercompetencyplans' => array(
            'datagenerator' => 'user_competency_plan',
            'required' => array('user', 'competency', 'plan')
        )
    );

    /**
     * Creates the specified element. More info about available elements in https://moodledev.io/general/development/tools/behat.
     *
     * @Given /^the following lp "(?P<element_string>(?:[^"]|\\")*)" exist:$/
     *
     * @todo MDL-78077 This will be deleted in Moodle 6.0.
     * @deprecated since 5.0
     *
     * @throws Exception
     * @throws PendingException
     * @param string    $elementname The name of the entity to add
     * @param TableNode $data
     */
    #[\core\attribute\deprecated('behat_tool_lp_data_generators::the_following_lp_exist', since: '5.0')]
    public function the_following_lp_exist($elementname, TableNode $data) {
        $this->deprecated_message([
            'behat_tool_lp_data_generators::the_following_lp_exist is deprecated',
            'Use: the following "core_competency > [competency|framework|plan...]" exist:',

        ]);

        // Now that we need them require the data generators.
        require_once(__DIR__.'/../../../../../lib/phpunit/classes/util.php');

        if (empty(self::$elements[$elementname])) {
            throw new PendingException($elementname . ' data generator is not implemented');
        }

        $datagenerator = testing_util::get_data_generator();
        $this->datageneratorlp = $datagenerator->get_plugin_generator('core_competency');

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
                $data['competencyframeworkid'] = $framework->get('id');
            } else {
                $framework = competency_framework::get_record(array('id' => $data['framework']));
                if ($framework) {
                    $data['competencyframeworkid'] = $framework->get('id');
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

        if (isset($data['reviewer'])) {
            if (is_number($data['reviewer'])) {
                $data['reviewerid'] = $data['reviewer'];
            } else {
                $user = $DB->get_record('user', array('username' => $data['reviewer']), '*', MUST_EXIST);
                $data['reviewerid'] = $user->id;
            }
            unset($data['reviewer']);
        }

        if (isset($data['status'])) {
            switch ($data['status']) {
                case 'draft':
                    $status = plan::STATUS_DRAFT;
                    break;
                case 'in review':
                    $status = plan::STATUS_IN_REVIEW;
                    break;
                case 'waiting for review':
                    $status = plan::STATUS_WAITING_FOR_REVIEW;
                    break;
                case 'active':
                    $status = plan::STATUS_ACTIVE;
                    break;
                case 'complete':
                    $status = plan::STATUS_COMPLETE;
                    break;
                default:
                    throw new Exception('Could not resolve plan status with: "' . $data['status'] . '"');
                    break;
            }

            $data['status'] = $status;
        }

        return $data;
    }

    /**
     * Adapt creating user_evidence from user username.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_user_evidence($data) {
        global $DB;

        if (isset($data['user'])) {
            $user = $DB->get_record('user', array('username' => $data['user']), '*', MUST_EXIST);
            $data['userid'] = $user->id;
        }
        unset($data['user']);
        return $data;
    }

    /**
     * Adapt creating plan_competency from plan name and competency shortname.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_plan_competency($data) {
        global $DB;

        if (isset($data['plan'])) {
            $plan = $DB->get_record(plan::TABLE, array('name' => $data['plan']), '*', MUST_EXIST);
            $data['planid'] = $plan->id;
        }
        unset($data['plan']);

        if (isset($data['competency'])) {
            $competency = $DB->get_record(competency::TABLE, array('shortname' => $data['competency']), '*', MUST_EXIST);
            $data['competencyid'] = $competency->id;
        }
        unset($data['competency']);
        return $data;
    }

    /**
     * Adapt creating plan_competency from user evidence name and competency shortname.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_user_evidence_competency($data) {
        global $DB;

        if (isset($data['userevidence'])) {
            $userevidence = $DB->get_record(user_evidence::TABLE, array('name' => $data['userevidence']), '*', MUST_EXIST);
            $data['userevidenceid'] = $userevidence->id;
        }
        unset($data['userevidence']);

        if (isset($data['competency'])) {
            $competency = $DB->get_record(competency::TABLE, array('shortname' => $data['competency']), '*', MUST_EXIST);
            $data['competencyid'] = $competency->id;
        }
        unset($data['competency']);
        return $data;
    }

    /**
     * Adapt creating user_competency from user name and competency shortname.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_user_competency($data) {
        global $DB;

        if (isset($data['user'])) {
            $user = $DB->get_record('user', array('username' => $data['user']), '*', MUST_EXIST);
            $data['userid'] = $user->id;
        }
        unset($data['user']);

        if (isset($data['competency'])) {
            $competency = $DB->get_record(competency::TABLE, array('shortname' => $data['competency']), '*', MUST_EXIST);
            $data['competencyid'] = $competency->id;
        }
        unset($data['competency']);

        return $data;
    }

    /**
     * Adapt creating user_competency_plan from user name, competency shortname and plan name.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_user_competency_plan($data) {
        global $DB;

        if (isset($data['user'])) {
            $user = $DB->get_record('user', array('username' => $data['user']), '*', MUST_EXIST);
            $data['userid'] = $user->id;
        }
        unset($data['user']);

        if (isset($data['competency'])) {
            $competency = $DB->get_record(competency::TABLE, array('shortname' => $data['competency']), '*', MUST_EXIST);
            $data['competencyid'] = $competency->id;
        }
        unset($data['competency']);

        if (isset($data['plan'])) {
            $plan = $DB->get_record(plan::TABLE, array('name' => $data['plan']), '*', MUST_EXIST);
            $data['planid'] = $plan->id;
        }
        unset($data['plan']);

        return $data;
    }

}
