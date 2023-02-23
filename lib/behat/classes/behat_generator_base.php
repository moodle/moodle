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
 * Base class for data generators component support for acceptance testing.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Tester\Exception\PendingException as PendingException;

/**
 * Class to quickly create Behat test data using component data generators.
 *
 * There is a subclass of class for each component that wants to be able to
 * generate entities using the Behat step
 *     Given the following "entity types" exist:
 *       | test | data |
 *
 * For core entities, the entity type is like "courses" or "users" and
 * generating those is handled by behat_core_generator. For other components
 * the entity type is like "mod_quiz > User override" and that is handled by
 * behat_mod_quiz_generator defined in mod/quiz/tests/generator/behat_mod_quiz_generator.php.
 *
 * The types of entities that can be generated are described by the array returned
 * by the {@link get_generateable_entities()} method. The list in
 * {@link behat_core_generator} is a good (if complex) example.
 *
 * How things work is best explained with a few examples. All this is implemented
 * in the {@link generate_items()} method below, if you want to see every detail of
 * how it works.
 *
 * Simple example from behat_core_generator:
 * 'users' => [
 *     'datagenerator' => 'user',
 *     'required' => ['username'],
 * ],
 * The steps performed are:
 *
 * 1. 'datagenerator' => 'user' means that the word used in the method names below is 'user'.
 *
 * 2. Because 'required' is present, check the supplied data exists 'username' column is present
 *    in the supplied data table and if not display an error.
 *
 * 3. Then for each row in the table as an array $elementdata (array keys are column names)
 *    and process it as follows
 *
 * 4. (Not used in this example.)
 *
 * 5. If the method 'preprocess_user' exists, then call it to update $elementdata.
 *    (It does, in this case it sets the password to the username, if password was not given.)
 *
 * We then do one of 4 things:
 *
 * 6a. If there is a method 'process_user' we call it. (It doesn't for user,
 *     but there are other examples like process_enrol_user() in behat_core_generator.)
 *
 * 6b. (Not used in this example.)
 *
 * 6c. Else, if testing_data_generator::create_user exists, we call it with $elementdata. (it does.)
 *
 * 6d. If none of these three things work. an error is thrown.
 *
 * To understand the missing steps above, consider the example from behat_mod_quiz_generator:
 * 'group override' => [
 *      'datagenerator' => 'override',
 *      'required' => ['quiz', 'group'],
 *      'switchids' => ['quiz' => 'quiz', 'group' => 'groupid'],
 * ],
 * Processing is as above, except that:
 *
 * 1. Note 'datagenerator' is 'override' (not group_override). 'user override' maps to the
 *    same datagenerator. This works fine.
 *
 * 4. Because 'switchids' is present, human-readable data in the table gets converted to ids.
 *    They array key 'group' refers to a column which may be present in the table (it will be
 *    here because it is required, but it does not have to be in general). If that column
 *    is present and contains a value, then the method matching name like get_group_id() is
 *    called with the value from that column in the data table. You must implement this
 *    method. You can see several examples of this sort of method below.
 *
 *    If that method returns a group id, then $elementdata['group'] is unset and
 *    $elementdata['groupid'] is set to the result of the get_group_id() call. 'groupid' here
 *    because of the definition is 'switchids' => [..., 'group' => 'groupid'].
 *    If get_group_id() cannot find the group, it should throw a helpful exception.
 *
 *    Similarly, 'quiz' (the quiz name) is looked up with a call to get_quiz_id(). Here, the
 *    new array key set matches the old one removed. This is fine.
 *
 * 6b. We are in a plugin, so before checking whether testing_data_generator::create_override
 *     exists we first check whether mod_quiz_generator::create_override() exists. It does,
 *     and this is what gets called.
 *
 * This second example shows why the get_..._id methods for core entities are in this base
 * class, not in behat_core_generator. Plugins may need to look up the ids of
 * core entities.
 *
 * behat_core_generator is defined in lib/behat/classes/behat_core_generator.php
 * and for components, behat_..._generator is defined in tests/generator/behat_..._generator.php
 * inside the plugin. For example behat_mod_quiz_generator is defined in
 * mod/quiz/tests/generator/behat_mod_quiz_generator.php.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class behat_generator_base {

    /**
     * @var string the name of the component we belong to.
     *
     * This should probably only be used to make error messages clearer.
     */
    protected $component;

    /**
     * @var testing_data_generator the core data generator
     */
    protected $datagenerator;

    /**
     * @var testing_data_generator the data generator for this component.
     */
    protected $componentdatagenerator;

    /**
     * Constructor.
     *
     * @param string $component component name, to make error messages more readable.
     */
    public function __construct(string $component) {
        $this->component = $component;
    }

    /**
     * Get a list of the entities that can be created for this component.
     *
     * This function must be overridden in subclasses. See class comment
     * above for a description of the data structure.
     * See {@link behat_core_generator} for an example.
     *
     * @return array entity name => information about how to generate.
     */
    protected abstract function get_creatable_entities(): array;

    /**
     * Get the list of available generators for this class.
     *
     * @return array
     */
    final public function get_available_generators(): array {
        return $this->get_creatable_entities();
    }

    /**
     * Do the work to generate an entity.
     *
     * This is called by {@link behat_data_generators::the_following_entities_exist()}.
     *
     * @param string    $generatortype The name of the entity to create.
     * @param TableNode $data from the step.
     * @param bool      $singular Whether there is only one record and it is pivotted
     */
    public function generate_items(string $generatortype, TableNode $data, bool $singular = false) {
        // Now that we need them require the data generators.
        require_once(__DIR__ . '/../../testing/generator/lib.php');

        $elements = $this->get_creatable_entities();

        foreach ($elements as $key => $configuration) {
            if (array_key_exists('singular', $configuration)) {
                $singularverb = $configuration['singular'];
                unset($configuration['singular']);
                unset($elements[$key]['singular']);
                $elements[$singularverb] = $configuration;
            }
        }

        if (!isset($elements[$generatortype])) {
            throw new PendingException($this->name_for_errors($generatortype) .
                    ' is not a known type of entity that can be generated.');
        }
        $entityinfo = $elements[$generatortype];

        $this->datagenerator = testing_util::get_data_generator();
        if ($this->component === 'core') {
            $this->componentdatagenerator = $this->datagenerator;
        } else {
            $this->componentdatagenerator = $this->datagenerator->get_plugin_generator($this->component);
        }

        $generatortype = $entityinfo['datagenerator'];

        if ($singular) {
            // There is only one record to generate, and the table has been pivotted.
            // The rows each represent a single field.
            $rows = [$data->getRowsHash()];
        } else {
            // There are multiple records to generate.
            // The rows represent an item to create.
            $rows = $data->getHash();
        }

        foreach ($rows as $elementdata) {
            // Check if all the required fields are there.
            foreach ($entityinfo['required'] as $requiredfield) {
                if (!isset($elementdata[$requiredfield])) {
                    throw new Exception($this->name_for_errors($generatortype) .
                            ' requires the field ' . $requiredfield . ' to be specified');
                }
            }

            // Switch from human-friendly references to ids.
            if (!empty($entityinfo['switchids'])) {
                foreach ($entityinfo['switchids'] as $element => $field) {
                    $methodname = 'get_' . $element . '_id';

                    // Not all the switch fields are required, default vars will be assigned by data generators.
                    if (isset($elementdata[$element])) {
                        if (!method_exists($this, $methodname)) {
                            throw new coding_exception('The generator for ' .
                                    $this->name_for_errors($generatortype) .
                                    ' entities specifies \'switchids\' => [..., \'' . $element .
                                    '\' => \'' . $field . '\', ...] but the required method ' .
                                    $methodname . '() has not been defined in ' .
                                    get_class($this) . '.');
                        }
                        // Temp $id var to avoid problems when $element == $field.
                        $id = $this->{$methodname}($elementdata[$element]);
                        unset($elementdata[$element]);
                        $elementdata[$field] = $id;
                    }
                }
            }

            // Preprocess the entities that requires a special treatment.
            if (method_exists($this, 'preprocess_' . $generatortype)) {
                $elementdata = $this->{'preprocess_' . $generatortype}($elementdata);
            }

            // Creates element.
            if (method_exists($this, 'process_' . $generatortype)) {
                // Use a method on this class to do the work.
                $this->{'process_' . $generatortype}($elementdata);

            } else if (method_exists($this->componentdatagenerator, 'create_' . $generatortype)) {
                // Using the component't own data generator if it exists.
                $this->componentdatagenerator->{'create_' . $generatortype}($elementdata);

            } else if (method_exists($this->datagenerator, 'create_' . $generatortype)) {
                // Use a method on the core data geneator, if there is one.
                $this->datagenerator->{'create_' . $generatortype}($elementdata);

            } else {
                // Give up.
                throw new PendingException($this->name_for_errors($generatortype) .
                        ' data generator is not implemented');
            }
        }
    }

    /**
     * Helper for formatting error messages.
     *
     * @param string $entitytype entity type without prefix, e.g. 'frog'.
     * @return string either 'frog' for core entities, or 'mod_mymod > frog' for components.
     */
    protected function name_for_errors(string $entitytype): string {
        if ($this->component === 'core') {
            return '"' . $entitytype . '"';
        } else {
            return '"' . $this->component . ' > ' . $entitytype . '"';
        }
    }

    /**
     * Gets the grade category id from the grade category fullname
     *
     * @param string $fullname the grade category name.
     * @return int corresponding id.
     */
    protected function get_gradecategory_id($fullname) {
        global $DB;

        if (!$id = $DB->get_field('grade_categories', 'id', array('fullname' => $fullname))) {
            throw new Exception('The specified grade category with fullname "' . $fullname . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the user id from it's username.
     * @throws Exception
     * @param string $username
     * @return int
     */
    protected function get_user_id($username) {
        global $DB;

        if (!$id = $DB->get_field('user', 'id', array('username' => $username))) {
            throw new Exception('The specified user with username "' . $username . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the user id from it's username.
     * @throws Exception
     * @param string $username
     * @return int
     */
    protected function get_userfrom_id(string $username) {
        global $DB;

        if (!$id = $DB->get_field('user', 'id', ['username' => $username])) {
            throw new Exception('The specified user with username "' . $username . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the user id from it's username.
     * @throws Exception
     * @param string $username
     * @return int
     */
    protected function get_userto_id(string $username) {
        global $DB;

        if (!$id = $DB->get_field('user', 'id', ['username' => $username])) {
            throw new Exception('The specified user with username "' . $username . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the role id from it's shortname.
     * @throws Exception
     * @param string $roleshortname
     * @return int
     */
    protected function get_role_id($roleshortname) {
        global $DB;

        if (!$id = $DB->get_field('role', 'id', array('shortname' => $roleshortname))) {
            throw new Exception('The specified role with shortname "' . $roleshortname . '" does not exist');
        }

        return $id;
    }

    /**
     * Gets the category id from it's idnumber.
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_category_id($idnumber) {
        global $DB;

        // If no category was specified use the data generator one.
        if ($idnumber == false) {
            return null;
        }

        if (!$id = $DB->get_field('course_categories', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified category with idnumber "' . $idnumber . '" does not exist');
        }

        return $id;
    }

    /**
     * Gets the course id from it's shortname.
     * @throws Exception
     * @param string $shortname
     * @return int
     */
    protected function get_course_id($shortname) {
        global $DB;

        if (!$id = $DB->get_field('course', 'id', array('shortname' => $shortname))) {
            throw new Exception('The specified course with shortname "' . $shortname . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the course cmid for the specified activity based on the activity's idnumber.
     *
     * Note: this does not check the module type, only the idnumber.
     *
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_activity_id(string $idnumber) {
        global $DB;

        if (!$id = $DB->get_field('course_modules', 'id', ['idnumber' => $idnumber])) {
            throw new Exception('The specified activity with idnumber "' . $idnumber . '" could not be found.');
        }

        return $id;
    }

    /**
     * Gets the group id from it's idnumber.
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_group_id($idnumber) {
        global $DB;

        if (!$id = $DB->get_field('groups', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified group with idnumber "' . $idnumber . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the grouping id from it's idnumber.
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_grouping_id($idnumber) {
        global $DB;

        // Do not fetch grouping ID for empty grouping idnumber.
        if (empty($idnumber)) {
            return null;
        }

        if (!$id = $DB->get_field('groupings', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified grouping with idnumber "' . $idnumber . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the cohort id from it's idnumber.
     * @throws Exception
     * @param string $idnumber
     * @return int
     */
    protected function get_cohort_id($idnumber) {
        global $DB;

        if (!$id = $DB->get_field('cohort', 'id', array('idnumber' => $idnumber))) {
            throw new Exception('The specified cohort with idnumber "' . $idnumber . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the outcome item id from its shortname.
     * @throws Exception
     * @param string $shortname
     * @return int
     */
    protected function get_outcome_id($shortname) {
        global $DB;

        if (!$id = $DB->get_field('grade_outcomes', 'id', array('shortname' => $shortname))) {
            throw new Exception('The specified outcome with shortname "' . $shortname . '" does not exist');
        }
        return $id;
    }

    /**
     * Get the id of a named scale.
     * @param string $name the name of the scale.
     * @return int the scale id.
     */
    protected function get_scale_id($name) {
        global $DB;

        if (!$id = $DB->get_field('scale', 'id', array('name' => $name))) {
            throw new Exception('The specified scale with name "' . $name . '" does not exist');
        }
        return $id;
    }

    /**
     * Get the id of a named question category (must be globally unique).
     * Note that 'Top' is a special value, used when setting the parent of another
     * category, meaning top-level.
     *
     * @param string $name the question category name.
     * @return int the question category id.
     */
    protected function get_questioncategory_id($name) {
        global $DB;

        if ($name == 'Top') {
            return 0;
        }

        if (!$id = $DB->get_field('question_categories', 'id', array('name' => $name))) {
            throw new Exception('The specified question category with name "' . $name . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the internal context id from the context reference.
     *
     * The context reference changes depending on the context
     * level, it can be the system, a user, a category, a course or
     * a module.
     *
     * @throws Exception
     * @param string $levelname The context level string introduced by the test writer
     * @param string $contextref The context reference introduced by the test writer
     * @return context
     */
    protected function get_context($levelname, $contextref) {
        return behat_base::get_context($levelname, $contextref);
    }

    /**
     * Gets the contact id from it's username.
     * @throws Exception
     * @param string $username
     * @return int
     */
    protected function get_contact_id($username) {
        global $DB;

        if (!$id = $DB->get_field('user', 'id', array('username' => $username))) {
            throw new Exception('The specified user with username "' . $username . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the external backpack id from it's backpackweburl.
     * @param string $backpackweburl
     * @return mixed
     * @throws dml_exception
     */
    protected function get_externalbackpack_id($backpackweburl) {
        global $DB;
        if (!$id = $DB->get_field('badge_external_backpack', 'id', ['backpackweburl' => $backpackweburl])) {
            throw new Exception('The specified external backpack with backpackweburl "' . $username . '" does not exist');
        }
        return $id;
    }

    /**
     * Get a coursemodule from an activity name or idnumber.
     *
     * @param string $activity
     * @param string $identifier
     * @return cm_info
     */
    protected function get_cm_by_activity_name(string $activity, string $identifier): cm_info {
        global $DB;

        $coursetable = new \core\dml\table('course', 'c', 'c');
        $courseselect = $coursetable->get_field_select();
        $coursefrom = $coursetable->get_from_sql();

        $cmtable = new \core\dml\table('course_modules', 'cm', 'cm');
        $cmfrom = $cmtable->get_from_sql();

        $acttable = new \core\dml\table($activity, 'a', 'a');
        $actselect = $acttable->get_field_select();
        $actfrom = $acttable->get_from_sql();

        $sql = <<<EOF
    SELECT cm.id as cmid, {$courseselect}, {$actselect}
      FROM {$cmfrom}
INNER JOIN {$coursefrom} ON c.id = cm.course
INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
INNER JOIN {$actfrom} ON cm.instance = a.id
     WHERE cm.idnumber = :idnumber OR a.name = :name
EOF;

        $result = $DB->get_record_sql($sql, [
            'modname' => $activity,
            'idnumber' => $identifier,
            'name' => $identifier,
        ], MUST_EXIST);

        $course = $coursetable->extract_from_result($result);
        $instancedata = $acttable->extract_from_result($result);

        return get_fast_modinfo($course)->get_cm($result->cmid);
    }
}
