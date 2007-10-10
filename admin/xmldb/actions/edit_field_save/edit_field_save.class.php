<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This class will save the changes performed to one field

class edit_field_save extends XMLDBAction {

    /**
     * Init method, every subclass will have its own
     */
    function init() {
        parent::init();

    /// Set own custom attributes

    /// Get needed strings
        $this->loadStrings(array(
            'fieldnameempty' => 'xmldb',
            'incorrectfieldname' => 'xmldb',
            'duplicatefieldname' => 'xmldb',
            'integerincorrectlength' => 'xmldb',
            'numberincorrectlength' => 'xmldb',
            'floatincorrectlength' => 'xmldb',
            'charincorrectlength' => 'xmldb',
            'textincorrectlength' => 'xmldb',
            'binaryincorrectlength' => 'xmldb',
            'numberincorrectdecimals' => 'xmldb',
            'floatincorrectdecimals' => 'xmldb',
            'enumvaluesincorrect' => 'xmldb',
            'wronglengthforenum' => 'xmldb',
            'defaultincorrect' => 'xmldb',
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

    /// Set own core attributes
        $this->does_generate = ACTION_NONE;
        //$this->does_generate = ACTION_GENERATE_HTML;

    /// These are always here
        global $CFG, $XMLDB;

    /// Do the job, setting result as needed

        if (!data_submitted('nomatch')) { ///Basic prevention
            error('Wrong action call');
        }

    /// Get parameters
        $dirpath = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . stripslashes_safe($dirpath);

        $tableparam = strtolower(required_param('table', PARAM_PATH));
        $fieldparam = strtolower(required_param('field', PARAM_PATH));
        $name = substr(trim(strtolower(optional_param('name', $fieldparam, PARAM_PATH))),0,30);

        $comment = required_param('comment', PARAM_CLEAN);
        $comment = trim(stripslashes_safe($comment));

        $type       = required_param('type', PARAM_INT);
        $length     = strtolower(optional_param('length', NULL, PARAM_ALPHANUM));
        $decimals   = optional_param('decimals', NULL, PARAM_INT);
        $unsigned   = optional_param('unsigned', false, PARAM_BOOL);
        $notnull    = optional_param('notnull', false, PARAM_BOOL);
        $sequence   = optional_param('sequence', false, PARAM_BOOL);
        $enum       = optional_param('enum', false, PARAM_BOOL);
        $enumvalues = optional_param('enumvalues', 0, PARAM_CLEAN);
        $enumvalues = trim(stripslashes_safe($enumvalues));
        $default    = optional_param('default', NULL, PARAM_PATH);
        $default    = trim(stripslashes_safe($default));

        $editeddir =& $XMLDB->editeddirs[$dirpath];
        $structure =& $editeddir->xml_file->getStructure();
        $table =& $structure->getTable($tableparam);
        $field =& $table->getField($fieldparam);
        $oldhash = $field->getHash();

        $errors = array();    /// To store all the errors found

    /// Perform some automatic asumptions
        if ($sequence) {
            $unsigned = true;
            $notnull  = true;
            $enum     = false;
            $default  = NULL;
        }
        if ($type != XMLDB_TYPE_NUMBER && $type != XMLDB_TYPE_FLOAT) {
            $decimals = NULL;
        }
        if ($type != XMLDB_TYPE_CHAR && $type != XMLDB_TYPE_TEXT) {
            $enum = false;
        }
        if ($type == XMLDB_TYPE_BINARY) {
            $default = NULL;
        }
        if (!$enum) {
            $enumvalues = NULL;
        }
        if ($default === '') {
            $default = NULL;
        }

    /// Perform some checks
    /// Check empty name
        if (empty($name)) {
            $errors[] = $this->str['fieldnameempty'];
        }
    /// Check incorrect name
        if ($name == 'changeme') {
            $errors[] = $this->str['incorrectfieldname'];
        }
    /// Check duplicate name
        if ($fieldparam != $name && $table->getField($name)) {
            $errors[] = $this->str['duplicatefieldname'];
        }
    /// Integer checks
        if ($type == XMLDB_TYPE_INTEGER) {
            if (!(is_numeric($length) && !empty($length) && intval($length)==floatval($length) &&
                  $length > 0 && $length <= 20)) {
                $errors[] = $this->str['integerincorrectlength'];
            }
            if (!(empty($default) || (is_numeric($default) &&
                                       !empty($default) &&
                                       intval($default)==floatval($default)))) {
                $errors[] = $this->str['defaultincorrect'];
            }
        }
    /// Number checks
        if ($type == XMLDB_TYPE_NUMBER) {
            if (!(is_numeric($length) && !empty($length) && intval($length)==floatval($length) &&
                  $length > 0 && $length <= 20)) {
                $errors[] = $this->str['numberincorrectlength'];
            }
            if (!(empty($decimals) || (is_numeric($decimals) &&
                                       !empty($decimals) &&
                                       intval($decimals)==floatval($decimals) &&
                                       $decimals >= 0 &&
                                       $decimals < $length))) {
                $errors[] = $this->str['numberincorrectdecimals'];
            }
            if (!(empty($default) || (is_numeric($default) &&
                                       !empty($default)))) {
                $errors[] = $this->str['defaultincorrect'];
            }
        }
    /// Float checks
        if ($type == XMLDB_TYPE_FLOAT) {
            if (!(empty($length) || (is_numeric($length) &&
                                     !empty($length) &&
                                     intval($length)==floatval($length) &&
                                     $length > 0 &&
                                     $length <= 20))) {
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
    /// Char checks
        if ($type == XMLDB_TYPE_CHAR) {
            if (!(is_numeric($length) && !empty($length) && intval($length)==floatval($length) &&
                  $length > 0 && $length <= 255)) {
                $errors[] = $this->str['charincorrectlength'];
            }
            if ($default !== NULL && $default !== '') {
                if (substr($default, 0, 1) == "'" ||
                    substr($default, -1, 1) == "'") {
                    $errors[] = $this->str['defaultincorrect'];
                }
            }
        }
    /// Text checks
        if ($type == XMLDB_TYPE_TEXT) {
            if ($length != 'small' &&
                $length != 'medium' &&
                $length != 'big') {
                $errors[] = $this->str['textincorrectlength'];
            }
            if ($default !== NULL && $default !== '') {
                if (substr($default, 0, 1) == "'" ||
                    substr($default, -1, 1) == "'") {
                    $errors[] = $this->str['defaultincorrect'];
                }
            }
        }
    /// Binary checks
        if ($type == XMLDB_TYPE_BINARY) {
            if ($length != 'small' &&
                $length != 'medium' &&
                $length != 'big') {
                $errors[] = $this->str['binaryincorrectlength'];
            }
        }
    /// Enum checks
        if ($enum) {
            $enumerr = false;
            $enumarr = explode(',',$enumvalues);
            $maxlength = 0;
            if ($enumarr) {
                foreach ($enumarr as $key => $enumelement) {
                /// Clear some spaces
                    $enumarr[$key] = trim($enumelement);
                    $enumelement = trim($enumelement);
                /// Calculate needed length
                    $le = strlen(str_replace("'", '', $enumelement));
                    if ($le > $maxlength) {
                        $maxlength = $le;
                    }
                /// Skip if under error
                    if ($enumerr) {
                        continue;
                    }
                /// Look for quoted strings
                    if (substr($enumelement, 0, 1) != "'" ||
                        substr($enumelement, -1, 1) != "'") {
                        $enumerr = true;
                    }
                }
            } else {
                $enumerr = true;
            }
            if ($enumerr) {
                $errors[] = $this->str['enumvaluesincorrect'];
            } else {
                $enumvalues = $enumarr;
            }
            if ($length < $maxlength) {
                $errors[] = $this->str['wronglengthforenum'];
            }
        }

        if (!empty($errors)) {
            $tempfield = new XMLDBField($name);
            $tempfield->setType($type);
            $tempfield->setLength($length);
            $tempfield->setDecimals($decimals);
            $tempfield->setUnsigned($unsigned);
            $tempfield->setNotNull($notnull);
            $tempfield->setSequence($sequence);
            $tempfield->setEnum($enum);
            $tempfield->setEnumValues($enumvalues);
            $tempfield->setDefault($default);
        /// Prepare the output
            $site = get_site();
            $navlinks = array();
            $navlinks[] = array('name' => $this->str['administration'], 'link' => '../index.php', 'type' => 'misc');
            $navlinks[] = array('name' => 'XMLDB', 'link' => 'index.php', 'type' => 'misc');
            $navigation = build_navigation($navlinks);
            print_header("$site->shortname: XMLDB", "$site->fullname", $navigation);
            notice ('<p>' .implode(', ', $errors) . '</p>
                     <p>' . $tempfield->readableInfo(),
                    'index.php?action=edit_field&amp;field=' .$field->getName() . '&amp;table=' . $table->getName()
                    . '&amp;dir=' . urlencode(str_replace($CFG->dirroot, '', $dirpath)));
            die; /// re-die :-P
        }

    /// Continue if we aren't under errors
        if (empty($errors)) {
        /// If there is one name change, do it, changing the prev and next
        /// atributes of the adjacent fields
            if ($fieldparam != $name) {
                $field->setName($name);
                if ($field->getPrevious()) {
                    $prev =& $table->getField($field->getPrevious());
                    $prev->setNext($name);
                    $prev->setChanged(true);
                }
                if ($field->getNext()) {
                    $next =& $table->getField($field->getNext());
                    $next->setPrevious($name);
                    $next->setChanged(true);
                }
            }

        /// Set comment
            $field->setComment($comment);

        /// Set the rest of fields
            $field->setType($type);
            $field->setLength($length);
            $field->setDecimals($decimals);
            $field->setUnsigned($unsigned);
            $field->setNotNull($notnull);
            $field->setSequence($sequence);
            $field->setEnum($enum);
            $field->setEnumValues($enumvalues);
            $field->setDefault($default);

        /// If the hash has changed from the old one, change the version
        /// and mark the structure as changed
            $field->calculateHash(true);
            if ($oldhash != $field->getHash()) {
                $field->setChanged(true);
                $table->setChanged(true);
            /// Recalculate the structure hash
                $structure->calculateHash(true);
                $structure->setVersion(userdate(time(), '%Y%m%d', 99, false));
            /// Mark as changed
                $structure->setChanged(true);
            }

        /// Launch postaction if exists (leave this here!)
            if ($this->getPostAction() && $result) {
                return $this->launch($this->getPostAction());
            }
        }

    /// Return ok if arrived here
        return $result;
    }
}
?>
