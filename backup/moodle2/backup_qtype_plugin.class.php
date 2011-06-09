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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class extending standard backup_plugin in order to implement some
 * helper methods related with the questions (qtype plugin)
 *
 * TODO: Finish phpdocs
 */
abstract class backup_qtype_plugin extends backup_plugin {

    /**
     * Attach to $element (usually questions) the needed backup structures
     * for question_answers for a given question
     * Used by various qtypes (calculated, essay, multianswer,
     * multichoice, numerical, shortanswer, truefalse)
     */
    protected function add_question_question_answers($element) {
        // Check $element is one nested_backup_element
        if (! $element instanceof backup_nested_element) {
            throw new backup_step_exception('question_answers_bad_parent_element', $element);
        }

        // Define the elements
        $answers = new backup_nested_element('answers');
        $answer = new backup_nested_element('answer', array('id'), array(
            'answertext', 'answerformat', 'fraction', 'feedback',
            'feedbackformat'));

        // Build the tree
        $element->add_child($answers);
        $answers->add_child($answer);

        // Set the sources
        $answer->set_source_sql('
                SELECT *
                FROM {question_answers}
                WHERE question = :question
                ORDER BY id',
                array('question' => backup::VAR_PARENTID));

        // Aliases
        $answer->set_source_alias('answer', 'answertext');

        // don't need to annotate ids nor files
    }

    /**
     * Attach to $element (usually questions) the needed backup structures
     * for question_numerical_units for a given question
     * Used both by calculated and numerical qtypes
     */
    protected function add_question_numerical_units($element) {
        // Check $element is one nested_backup_element
        if (! $element instanceof backup_nested_element) {
            throw new backup_step_exception('question_numerical_units_bad_parent_element', $element);
        }

        // Define the elements
        $units = new backup_nested_element('numerical_units');
        $unit = new backup_nested_element('numerical_unit', array('id'), array(
            'multiplier', 'unit'));

        // Build the tree
        $element->add_child($units);
        $units->add_child($unit);

        // Set the sources
        $unit->set_source_sql('
                SELECT *
                FROM {question_numerical_units}
                WHERE question = :question
                ORDER BY id',
                array('question' => backup::VAR_PARENTID));

        // don't need to annotate ids nor files
    }

    /**
     * Attach to $element (usually questions) the needed backup structures
     * for question_numerical_options for a given question
     * Used both by calculated and numerical qtypes
     */
    protected function add_question_numerical_options($element) {
        // Check $element is one nested_backup_element
        if (! $element instanceof backup_nested_element) {
            throw new backup_step_exception('question_numerical_options_bad_parent_element', $element);
        }

        // Define the elements
        $options = new backup_nested_element('numerical_options');
        $option = new backup_nested_element('numerical_option', array('id'), array(
            'showunits', 'unitsleft', 'unitgradingtype', 'unitpenalty'));

        // Build the tree
        $element->add_child($options);
        $options->add_child($option);

        // Set the sources
        $option->set_source_table('question_numerical_options', array('question' => backup::VAR_PARENTID));

        // don't need to annotate ids nor files
    }

    /**
     * Attach to $element (usually questions) the needed backup structures
     * for question_datasets for a given question
     * Used by calculated qtypes
     */
    protected function add_question_datasets($element) {
        // Check $element is one nested_backup_element
        if (! $element instanceof backup_nested_element) {
            throw new backup_step_exception('question_datasets_bad_parent_element', $element);
        }

        // Define the elements
        $definitions = new backup_nested_element('dataset_definitions');
        $definition = new backup_nested_element('dataset_definition', array('id'), array(
            'category', 'name', 'type', 'options',
            'itemcount'));

        $items = new backup_nested_element('dataset_items');
        $item = new backup_nested_element('dataset_item', array('id'), array(
            'number', 'value'));

        // Build the tree
        $element->add_child($definitions);
        $definitions->add_child($definition);

        $definition->add_child($items);
        $items->add_child($item);

        // Set the sources
        $definition->set_source_sql('SELECT qdd.*
                                       FROM {question_dataset_definitions} qdd
                                       JOIN {question_datasets} qd ON qd.datasetdefinition = qdd.id
                                      WHERE qd.question = ?', array(backup::VAR_PARENTID));

        $item->set_source_table('question_dataset_items', array('definition' => backup::VAR_PARENTID));

        // Aliases
        $item->set_source_alias('itemnumber', 'number');

        // don't need to annotate ids nor files
    }

    /**
     * Returns all the components and fileareas used by all the installed qtypes
     *
     * The method introspects each qtype, asking it about fileareas used. Then,
     * one 2-level array is returned. 1st level is the component name (qtype_xxxx)
     * and 2nd level is one array of filearea => mappings to look
     *
     * Note that this function is used both in backup and restore, so it is important
     * to use the same mapping names (usually, name of the table in singular) always
     *
     * TODO: Surely this can be promoted to backup_plugin easily and make it to
     * work for ANY plugin, not only qtypes (but we don't need it for now)
     */
    public static function get_components_and_fileareas($filter = null) {
        $components = array();
        // Get all the plugins of this type
        $qtypes = get_plugin_list('qtype');
        foreach ($qtypes as $name => $path) {
            // Apply filter if specified
            if (!is_null($filter) && $filter != $name) {
                continue;
            }
            // Calculate the componentname
            $componentname = 'qtype_' . $name;
            // Get the plugin fileareas (all them MUST belong to the same component)
            $classname = 'backup_qtype_' . $name . '_plugin';
            if (class_exists($classname)) {
                $elements = call_user_func(array($classname, 'get_qtype_fileareas'));
                if ($elements) {
                    // If there are elements, add them to $components
                    $components[$componentname] = $elements;
                }
            }
        }
        return $components;
    }

    /**
     * Returns one array with filearea => mappingname elements for the qtype
     *
     * Used by {@link get_components_and_fileareas} to know about all the qtype
     * files to be processed both in backup and restore.
     */
    public static function get_qtype_fileareas() {
        // By default, return empty array, only qtypes having own fileareas will override this
        return array();
    }
}
