<?php

/**
 * Handles displaying and editing the datetime field
 *
 * @author Mark Nelson <mark@moodle.com.au>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version 20101022
 */

class profile_field_datetime extends profile_field_base {

    /**
     * Handles editing datetime fields
     *
     * @param object moodleform instance
     */
    function edit_field_add($mform) {
        // Check if the field is required
        if ($this->field->required) {
            $optional = false;
        } else {
            $optional = true;
        }

        $attributes = array(
            'startyear' => $this->field->param1,
            'stopyear'  => $this->field->param2,
            'optional'  => $optional
        );

        // Check if they wanted to include time as well
        if (!empty($this->field->param3)) {
            $mform->addElement('date_time_selector', $this->inputname, format_string($this->field->name), $attributes);
        } else {
            $mform->addElement('date_selector', $this->inputname, format_string($this->field->name), $attributes);
        }

        $mform->setType($this->inputname, PARAM_INT);
        $mform->setDefault($this->inputname, time());
    }

    /**
     * If timestamp is in YYYY-MM-DD or YYYY-MM-DD-HH-MM-SS format, then convert it to timestamp.
     *
     * @param string|int $datetime datetime to be converted.
     * @param stdClass $datarecord The object that will be used to save the record
     * @return int timestamp
     * @since Moodle 2.5
     */
    function edit_save_data_preprocess($datetime, $datarecord) {
        // If timestamp then explode it to check if year is within field limit.
        $isstring = strpos($datetime, '-');
        if (empty($isstring)) {
            $datetime = date('Y-m-d-H-i-s', $datetime);
        }

        $datetime = explode('-', $datetime);
        // Bound year with start and end year.
        $datetime[0] = min(max($datetime[0], $this->field->param1), $this->field->param2);
        if (!empty($this->field->param3) && count($datetime) == 6) {
            return make_timestamp($datetime[0], $datetime[1], $datetime[2], $datetime[3], $datetime[4], $datetime[5]);
        } else {
            return make_timestamp($datetime[0], $datetime[1], $datetime[2]);
        }
    }

    /**
     * Display the data for this field
     */
    function display_data() {
        // Check if time was specified
        if (!empty($this->field->param3)) {
            $format = get_string('strftimedaydatetime', 'langconfig');
        } else {
            $format = get_string('strftimedate', 'langconfig');
        }

        // Check if a date has been specified
        if (empty($this->data)) {
            return get_string('notset', 'profilefield_datetime');
        } else {
            return userdate($this->data, $format);
        }
    }
}