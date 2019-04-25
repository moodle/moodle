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
 * Data generators for acceptance testing.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Behat\Tester\Exception\PendingException as PendingException;

/**
 * Class to set up quickly a Given environment.
 *
 * The entry point is the Behat steps:
 *     the following "entity types" exist:
 *       | test | data |
 *
 * Entity type will either look like "users" or "activities" for core entities, or
 * "mod_forum > subscription" or "core_message > message" for entities belonging
 * to components.
 *
 * Generally, you only need to specify properties relevant to your test,
 * and everything else gets set to sensible defaults.
 *
 * The actual generation of entities is done by {@link behat_generator_base}.
 * There is one subclass for each component, e.g. {@link behat_core_generator}
 * or {@link behat_mod_quiz_generator}. To see the types of entity
 * that can be created for each component, look at the arrays returned
 * by the get_creatable_entities() method in each class.
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_data_generators extends behat_base {

    /**
     * Convert legacy entity names to the new component-specific form.
     *
     * In the past, there was no support for plugins, and everything that
     * could be created was handled by the core generator. Now, we can
     * support plugins, and so some thing should probably be moved.
     *
     * For example, in the future we should probably add
     * 'message contacts' => 'core_message > contact'] to
     * this array, and move generation of message contact
     * from core to core_message.
     *
     * @var array old entity type => new entity type.
     */
    protected $movedentitytypes = [
    ];

    /**
     * Creates the specified element.
     *
     * See the class comment for an overview.
     *
     * @Given /^the following "(?P<element_string>(?:[^"]|\\")*)" exist:$/
     *
     * @param string    $entitytype The name of the type entity to add
     * @param TableNode $data
     */
    public function the_following_entities_exist($entitytype, TableNode $data) {
        if (isset($this->movedentitytypes[$entitytype])) {
            $entitytype = $this->movedentitytypes[$entitytype];
        }
        list($component, $entity) = $this->parse_entity_type($entitytype);
        $this->get_instance_for_component($component)->generate_items($entity, $data);
    }

    /**
     * Parse a full entity type like 'users' or 'mod_forum > subscription'.
     *
     * E.g. parsing 'course' gives ['core', 'course'] and
     * parsing 'core_message > message' gives ['core_message', 'message'].
     *
     * @param string $entitytype the entity type
     * @return string[] with two elements, component and entity type.
     */
    protected function parse_entity_type(string $entitytype): array {
        $dividercount = substr_count($entitytype, ' > ');
        if ($dividercount === 0) {
            return ['core', $entitytype];
        } else if ($dividercount === 1) {
            list($component, $type) = explode(' > ', $entitytype);
            if ($component === 'core') {
                throw new coding_exception('Do not specify the component "core > ..." for entity types.');
            }
            return [$component, $type];
        } else {
            throw new coding_exception('The entity type must be in the form ' .
                    '"{entity-type}" for core entities, or "{component} > {entity-type}" ' .
                    'for entities belonging to other components. ' .
                    'For example "users" or "mod_forum > subscriptions".');
        }
    }

    /**
     * Get an instance of the appropriate subclass of this class for a given component.
     *
     * @param string $component The name of the component to generate entities for.
     * @return behat_generator_base the subclass of this class for the requested component.
     */
    protected function get_instance_for_component(string $component): behat_generator_base {
        global $CFG;

        // Ensure the generator class is loaded.
        require_once($CFG->libdir . '/behat/classes/behat_generator_base.php');
        if ($component === 'core') {
            $lib = $CFG->libdir . '/behat/classes/behat_core_generator.php';
        } else {
            $dir = core_component::get_component_directory($component);
            $lib = $dir . '/tests/generator/behat_' . $component . '_generator.php';
            if (!$dir || !is_readable($lib)) {
                throw new coding_exception("Component {$component} does not support " .
                        "behat generators yet. Missing {$lib}.");
            }
        }
        require_once($lib);

        // Create an instance.
        $componentclass = "behat_{$component}_generator";
        if (!class_exists($componentclass)) {
            throw new PendingException($component .
                    ' does not yet support the Behat data generator mechanism. Class ' .
                    $componentclass . ' not found in file ' . $lib . '.');
        }
        $instance = new $componentclass($component);
        return $instance;
    }
}
