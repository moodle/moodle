<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
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

/**
 * A Moodle form base class for editing local filter settings.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 *//** */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

abstract class filter_local_settings_form extends moodleform {
    protected $filter;
    protected $context;

    public function __construct($submiturl, $filter, $context) {
        $this->filter = $filter;
        $this->context = $context;
        parent::moodleform($submiturl);
    }

    /**
     * Build the form definition. Rather than overriding this method, you
     * should probably override definition_inner instead.
     *
     * This method adds the necessary hidden fields and submit buttons,
     * and calls definition_inner to insert the custom controls in the appropriate place.
     */
    public function definition() {
        $mform =& $this->_form;

        $this->definition_inner($mform);

        $mform->addElement('hidden', 'contextid');
        $mform->setType('contextid', PARAM_INT);
        $mform->setDefault('contextid', $this->context->id);

        $mform->addElement('hidden', 'filter');
        $mform->setType('filter', PARAM_ALPHAEXT);
        $mform->setDefault('filter', $this->filter);

        $this->add_action_buttons();
    }

    /**
     * Override this method to add your form controls.
     * @param $mform the form we are building. $this->_form, but passed in for convinience.
     */
    abstract protected function definition_inner($mform);

    /**
     * Override this method to save the settings to the database. The default
     * implementation will probalby be sufficient for most simple cases.
     * @param object $data the form data that was submitted.
     */
    public function save_changes($data) {
        $data = (array) $data;
        unset($data['filter']);
        unset($data['contextid']);
        foreach ($data as $name => $value) {
            if ($value !== '') {
                filter_set_local_config($this->filter, $this->context->id, $name, $value);
            } else {
                filter_unset_local_config($this->filter, $this->context->id, $name);
            }
        }
    }
}
