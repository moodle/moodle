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
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class verifies all the data introduced when editing a field for correctness,
 * performing changes / displaying errors depending of the results.
 *
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_field_save extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

        // Set own custom attributes

        // Get needed strings
        $this->loadStrings(array(
            'fieldnameempty' => 'tool_xmldb',
            'incorrectfieldname' => 'tool_xmldb',
            'duplicatefieldname' => 'tool_xmldb',
            'integerincorrectlength' => 'tool_xmldb',
            'numberincorrectlength' => 'tool_xmldb',
            'floatincorrectlength' => 'tool_xmldb',
            'charincorrectlength' => 'tool_xmldb',
            'numberincorrectdecimals' => 'tool_xmldb',
            'numberincorrectwholepart' => 'tool_xmldb',
            'floatincorrectdecimals' => 'tool_xmldb',
            'defaultincorrect' => 'tool_xmldb',
            'back' => 'tool_xmldb',
            'administration' => ''
        ));
    }

    /**
     * Invoke method, every class will have its own
     * returns true/false on completion, setting both
     * errormsg and output as necessary
     */
    function invoke() {
        parent::invoke();

        $result = true;

        // Set own core attributes
        //$this->does_generate = ACTION_NONE;
        $this->does_generate = ACTION_GENERATE_HTML;

        // These are always here
        global $CFG, $XMLDB;

        // Do the job, setting result as needed

        if (!data_submitted()) { // Basic prevention
            print_error('wrongcall', 'error');
        }

        // Get parameters
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dirpath;

        $tableparam = strtolower(required_param('table', PARAM_PATH));
        $fieldparam = strtolower(required_param('field', PARAM_PATH));
        $name = substr(trim(strtolower(optional_param('name', $fieldparam, PARAM_PATH))),0,xmldb_field::NAME_MAX_LENGTH);

        $comment = required_param('comment', PARAM_CLEAN);
        $comment = trim($comment);

        $type       = required_param('type', PARAM_INT);
        $length     = strtolower(optional_param('length', NULL, PARAM_ALPHANUM));
        $decimals   = optional_param('decimals', NULL, PARAM_INT);
        $notnull    = optional_param('notnull', false, PARAM_BOOL);
        $sequence   = optional_param('sequence', false, PARAM_BOOL);
        $default    = optional_param('default', NULL, PARAM_PATH);
        $default    = trim($default);

        $editeddir = $XMLDB->editeddirs[$dirpath];
        $structure = $editeddir->xml_file->getStructure();
        $table = $structure->getTable($tableparam);
        $field = $table->getField($fieldparam);
        $oldhash = $field->getHash();

        $errors = array(); // To store all the errors found

        // Perform some automatic assumptions
        if ($sequence) {
            $notnull  = true;
            $default  = NULL;
        }
        if ($type != XMLDB_TYPE_NUMBER && $type != XMLDB_TYPE_FLOAT) {
            $decimals = NULL;
        }
        if ($type == XMLDB_TYPE_BINARY) {
            $default = NULL;
        }
        if ($default === '') {
            $default = NULL;
        }

        // Perform some checks
        // Check empty name
        if (empty($name)) {
            $errors[] = $this->str['fieldnameempty'];
        }
        // Check incorrect name
        if ($name == 'changeme') {
            $errors[] = $this->str['incorrectfieldname'];
        }
        // Check duplicate name
        if ($fieldparam != $name && $table->getField($name)) {
            $errors[] = $this->str['duplicatefieldname'];
        }
        // Integer checks
        if ($type == XMLDB_TYPE_INTEGER) {
            if (!(is_numeric($length) && !empty($length) && intval($length)==floatval($length) &&
                  $length > 0 && $length <= xmldb_field::INTEGER_MAX_LENGTH)) {
                $errors[] = $this->str['integerincorrectlength'];
            }
            if (!(empty($default) || (is_numeric($default) &&
                                       !empty($default) &&
                                       intval($default)==floatval($default)))) {
                $errors[] = $this->str['defaultincorrect'];
            }
        }
        // Number checks
        if ($type == XMLDB_TYPE_NUMBER) {
            if (!(is_numeric($length) && !empty($length) && intval($length)==floatval($length) &&
                  $length > 0 && $length <= xmldb_field::NUMBER_MAX_LENGTH)) {
                $errors[] = $this->str['numberincorrectlength'];
            }
            if (!(empty($decimals) || (is_numeric($decimals) &&
                                       !empty($decimals) &&
                                       intval($decimals)==floatval($decimals) &&
                                       $decimals >= 0 &&
                                       $decimals < $length))) {
                $errors[] = $this->str['numberincorrectdecimals'];
            }
            if (!empty($decimals) && ($length - $decimals > xmldb_field::INTEGER_MAX_LENGTH)) {
                $errors[] = $this->str['numberincorrectwholepart'];
            }
            if (!(empty($default) || (is_numeric($default) &&
                                       !empty($default)))) {
                $errors[] = $this->str['defaultincorrect'];
            }
        }
        // Float checks
        if ($type == XMLDB_TYPE_FLOAT) {
            if (!(empty($length) || (is_numeric($length) &&
                                     !empty($length) &&
                                     intval($length)==floatval($length) &&
                                     $length > 0 &&
                                     $length <= xmldb_field::FLOAT_MAX_LENGTH))) {
                $errors[] = $this->str['floatincorrectlength'];
            }
            if (!(empty($decimals) || (is_numeric($decimals) &&
                                       !empty($decimals) &&
                                       intval($decimals)==floatval($decimals) &&
                                       $decimals >= 0 &&
                                       $decimals < $length))) {
                $errors[] = $this->str['floatincorrectdecimals'];
            }
            if (!(empty($default) || (is_numeric($default) &&
                                       !empty($default)))) {
                $errors[] = $this->str['defaultincorrect'];
            }
        }
        // Char checks
        if ($type == XMLDB_TYPE_CHAR) {
            if (!(is_numeric($length) && !empty($length) && intval($length)==floatval($length) &&
                  $length > 0 && $length <= xmldb_field::CHAR_MAX_LENGTH)) {
                $errors[] = $this->str['charincorrectlength'];
            }
            if ($default !== NULL && $default !== '') {
                if (substr($default, 0, 1) == "'" ||
                    substr($default, -1, 1) == "'") {
                    $errors[] = $this->str['defaultincorrect'];
                }
            }
        }
        // No text checks
        // No binary checks

        if (!empty($errors)) {
            $tempfield = new xmldb_field($name);
            $tempfield->setType($type);
            $tempfield->setLength($length);
            $tempfield->setDecimals($decimals);
            $tempfield->setNotNull($notnull);
            $tempfield->setSequence($sequence);
            $tempfield->setDefault($default);
            // Prepare the output
            $o = '<p>' .implode(', ', $errors) . '</p>
                  <p>' . $name . ': ' . $tempfield->readableInfo() . '</p>';
            $o.= '<a href="index.php?action=edit_field&amp;field=' . $field->getName() . '&amp;table=' . $table->getName() .
                 '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)) . '">[' . $this->str['back'] . ']</a>';
            $this->output = $o;
        }

        // Continue if we aren't under errors
        if (empty($errors)) {
            // If there is one name change, do it, changing the prev and next
            // atributes of the adjacent fields
            if ($fieldparam != $name) {
                $field->setName($name);
                if ($field->getPrevious()) {
                    $prev = $table->getField($field->getPrevious());
                    $prev->setNext($name);
                    $prev->setChanged(true);
                }
                if ($field->getNext()) {
                    $next = $table->getField($field->getNext());
                    $next->setPrevious($name);
                    $next->setChanged(true);
                }
            }

            // Set comment
            $field->setComment($comment);

            // Set the rest of fields
            $field->setType($type);
            $field->setLength($length);
            $field->setDecimals($decimals);
            $field->setNotNull($notnull);
            $field->setSequence($sequence);
            $field->setDefault($default);

            // If the hash has changed from the old one, change the version
            // and mark the structure as changed
            $field->calculateHash(true);
            if ($oldhash != $field->getHash()) {
                $field->setChanged(true);
                $table->setChanged(true);
                // Recalculate the structure hash
                $structure->calculateHash(true);
                $structure->setVersion(userdate(time(), '%Y%m%d', 99, false));
                // Mark as changed
                $structure->setChanged(true);
            }

            // Launch postaction if exists (leave this here!)
            if ($this->getPostAction() && $result) {
                return $this->launch($this->getPostAction());
            }
        }

        // Return ok if arrived here
        return $result;
    }
}

