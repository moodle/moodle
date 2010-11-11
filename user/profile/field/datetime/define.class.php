<?php

/**
 * Define datetime fields
 *
 * @author Mark Nelson <mark@moodle.com.au>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version 20101022
 */

class profile_define_datetime extends profile_define_base {

    /**
     * Define the setting for a datetime custom field
     *
     * @param object $form the user form
     */
    function define_form_specific($form) {
        // Create variables to store start and end
        $currentyear = date('Y');
        $startyear = $currentyear - 100;
        $endyear = $currentyear + 20;

        // Create array for the years
        $arryears = array();
        for ($i = $startyear; $i <= $endyear; $i++) {
            $arryears[$i] = $i;
        }

        // Add elements
        $form->addElement('select', 'param1', get_string('startyear', 'profilefield_datetime'), $arryears);
        $form->setType('param1', PARAM_INT);
        $form->setDefault('param1', $currentyear);

        $form->addElement('select', 'param2', get_string('endyear', 'profilefield_datetime'), $arryears);
        $form->setType('param2', PARAM_INT);
        $form->setDefault('param2', $currentyear + 20);

        $form->addElement('checkbox', 'param3', get_string('wanttime', 'profilefield_datetime'));
        $form->setType('param3', PARAM_INT);

        $form->addElement('hidden', 'defaultdata', '0');
        $form->setType('defaultdata', PARAM_INT);
    }

    /**
     * Validate the data from the profile field form
     *
     * @param   object   data from the add/edit profile field form
     * @return  array    associative array of error messages
     */
    function define_validate_specific($data) {
        $errors = array();

        // Make sure the start year is not greater than the end year
        if ($data->param1 > $data->param2) {
            $errors['param1'] = get_string('startyearafterend', 'profilefield_datetime');
        }

        return $errors;
    }

    /**
     * Preprocess data from the profile field form before
     * it is saved.
     *
     * @param   object   data from the add/edit profile field form
     * @return  object   processed data object
     */
    function define_save_preprocess($data) {
        if (empty($data->param3)) {
            $data->param3 = NULL;
        }

        // No valid value in the default data column needed
        $data->defaultdata = '0';

        return $data;
    }
}
