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
 * This file defines a class with accumulative grading strategy logic
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)) . '/lib.php');  // interface definition

/**
 * Accumulative grading strategy logic.
 */
class workshop_accumulative_strategy implements workshop_strategy {

    /** @var workshop the parent workshop instance */
    protected $workshop;

    /** @var int number of dimensions defined in database, must be set in {@link load_fields()} */
    protected $nodimensions=null;

    /** @var array options for dimension description fields */
    protected $descriptionopts;

    /**
     * Constructor
     *
     * @param object $workshop The workshop instance record
     * @return void
     */
    public function __construct($workshop) {
        $this->workshop         = $workshop;
        $this->descriptionopts  = array('trusttext' => true, 'subdirs' => true, 'maxfiles' => -1);
    }

    /**
     * @return string
     */
    public function name() {
        return 'accumulative';
    }

    /**
     * Factory method returning an instance of an assessment form editor class
     *
     * @param $actionurl URL of form handler, defaults to auto detect the current url
     */
    public function get_edit_strategy_form($actionurl=null) {
        global $CFG;    // needed because the included files use it
        global $PAGE;

        require_once(dirname(__FILE__) . '/edit_form.php');

        $fields = $this->load_fields();
        if (is_null($this->nodimensions)) {
            throw new coding_exception('You forgot to set the number of dimensions in load_fields()');
        }
        $norepeatsdefault   = max($this->nodimensions + WORKSHOP_STRATEGY_ADDDIMS, WORKSHOP_STRATEGY_MINDIMS);
        $norepeats          = optional_param('norepeats', $norepeatsdefault, PARAM_INT);    // number of dimensions
        $noadddims          = optional_param('noadddims', '', PARAM_ALPHA);                 // shall we add more?
        if ($noadddims) {
            $norepeats += WORKSHOP_STRATEGY_ADDDIMS;
        }

        for ($i = 0; $i < $norepeats; $i++) {
            // prepare all editor elements
            $fields = file_prepare_standard_editor($fields, 'description__idx_'.$i, $this->descriptionopts,
                                                   $PAGE->context, 'workshop_dimension_description', 0);
        }
        $customdata = array();
        $customdata['workshop'] = $this->workshop;
        $customdata['strategy'] = $this;
        $customdata['norepeats'] = $norepeats;
        $customdata['descriptionopts'] = $this->descriptionopts;
        $customdata['current']  = $fields;
        $attributes = array('class' => 'editstrategyform');

        return new workshop_edit_accumulative_strategy_form($actionurl, $customdata, 'post', '', $attributes);
    }

    /**
     * Returns the fields of the assessment form and sets {@var nodimensions}
     */
    protected function load_fields() {
        global $DB;

        $dims = $DB->get_records('workshop_forms_' . $this->name(), array('workshopid' => $this->workshop->id), 'sort');
        $this->nodimensions = count($dims);
        $fields = $this->_cook_dimension_records($dims);
        return $fields;
    }

    /**
     * Maps the data from DB to their form fields
     *
     * Called internally from load_form(). Could be private but keeping protected
     * for unit testing purposes.
     *
     * @param array $raw Array of raw dimension records as fetched by get_record()
     * @return array Array of fields data to be used by the mform set_data
     */
    protected function _cook_dimension_records(array $raw) {

        $formdata = array();
        $key = 0;
        foreach ($raw as $dimension) {
            $formdata['dimensionid__idx_' . $key]       = $dimension->id;
            $formdata['description__idx_' . $key]       = $dimension->description;
            $formdata['descriptionformat__idx_' . $key] = $dimension->descriptionformat;
            $formdata['grade__idx_' . $key]             = $dimension->grade;
            $formdata['weight__idx_' . $key]            = $dimension->weight;
            $key++;
        }
        $cooked = (object)$formdata;
        return $cooked;
    }

    /**
     * Save the assessment dimensions into database
     *
     * Saves data into the main strategy form table. If the record->id is null or zero,
     * new record is created. If the record->id is not empty, the existing record is updated. Records with
     * empty 'description' field are removed from database.
     * The passed data object are the raw data returned by the get_data().
     *
     * @uses $DB
     * @param object $data Raw data returned by the dimension editor form
     * @return void
     */
    public function save_edit_strategy_form(stdClass $data) {
        global $DB;

        if (!isset($data->strategyname) || ($data->strategyname != $this->name())) {
            // the workshop strategy has changed since the form was opened for editing
            throw new moodle_exception('strategyhaschanged', 'workshop');
        }

        $data = $this->_cook_edit_form_data($data);
        $todelete = array();
        foreach ($data as $record) {
            if (empty($record->description)) {
                if (!empty($record->id)) {
                    // existing record with empty description - to be deleted
                    $todelete[] = $record->id;
                }
                continue;
            }
            if (empty($record->id)) {
                // new field
                $record->id = $DB->insert_record('workshop_forms_' . $this->name(), $record);
            } else {
                // exiting field
                $DB->update_record('workshop_forms_' . $this->name(), $record);
            }
        }
        $DB->delete_records_list('workshop_forms_' . $this->name(), 'id', $todelete);
    }

    /**
     * Prepares data returned by {@link workshop_edit_accumulative_strategy_form} so they can be saved into database
     *
     * It automatically adds some columns into every record. The sorting is
     * done by the order of the returned array and starts with 1.
     * Called internally from {@link save_edit_strategy_form()} only. Could be private but
     * keeping protected for unit testing purposes.
     *
     * @param object $raw Raw data returned by mform
     * @return array Array of objects to be inserted/updated in DB
     */
    protected function _cook_edit_form_data(stdClass $raw) {
        global $PAGE;

        $cook = array();
        for ($i = 0; $i < $raw->norepeats; $i++) {
            $raw = file_postupdate_standard_editor($raw, 'description__idx_'.$i, $this->descriptionopts,
                                                    $PAGE->context, 'workshop_dimension_description', 0);
            $cook[$i]                    = new stdClass();
            $fieldname                   = 'dimensionid__idx_'.$i;
            $cook[$i]->id                = isset($raw->$fieldname) ? $raw->$fieldname : null;
            $cook[$i]->workshopid        = $this->workshop->id;
            $cook[$i]->sort              = $i + 1;
            $fieldname                   = 'description__idx_'.$i;
            $cook[$i]->description       = isset($raw->$fieldname) ? $raw->$fieldname : null;
            $fieldname                   = 'description__idx_'.$i.'format';
            $cook[$i]->descriptionformat = isset($raw->$fieldname) ? $raw->$fieldname : FORMAT_HTML;
            $fieldname                   = 'grade__idx_'.$i;
            $cook[$i]->grade             = isset($raw->$fieldname) ? $raw->$fieldname : null;
            $fieldname                   = 'weight__idx_'.$i;
            $cook[$i]->weight            = isset($raw->$fieldname) ? $raw->$fieldname : null;
        }
        return $cook;
    }

    /**
     * Factory method returning an instance of an assessment form
     *
     * @param moodle_url $actionurl URL of form handler, defaults to auto detect the current url
     * @param string $mode          Mode to open the form in: preview/assessment
     */
    public function get_assessment_form(moodle_url $actionurl=null, $mode='preview') {
        global $CFG;    // needed because the included files use it
        require_once(dirname(__FILE__) . '/assessment_form.php');

        $fields = $this->load_fields();
        if (is_null($this->nodimensions)) {
            throw new coding_exception('You forgot to set the number of dimensions in load_fields()');
        }

        // set up the required custom data common for all strategies
        $customdata['strategy'] = $this;
        $customdata['mode']     = $mode;

        // set up strategy-specific custom data
        $customdata['nodims']   = $this->nodimensions;
        $customdata['fields']   = $fields;
        $attributes = array('class' => 'assessmentform accumulative');

        return new workshop_accumulative_assessment_form($actionurl, $customdata, 'post', '', $attributes);
    }

    /**
     * Saves the filled assessment
     *
     * This method processes data submitted using the form returned by {@link get_assessment_form()}
     *
     * @param object $assessment Assessment being filled
     * @param object $data       Raw data as returned by the assessment form
     * @return void
     */
    public function save_assessment(stdClass $assessment, stdClass $data) {
        global $DB;

        if (!isset($data->strategyname) || ($data->strategyname != $this->name())) {
            // the workshop strategy has changed since the form was opened for editing
            throw new moodle_exception('strategyhaschanged', 'workshop');
        }
        if (!isset($data->nodims)) {
            throw coding_expection('You did not send me the number of assessment dimensions to process');
        }

        foreach ($this->_cook_assessment_form_data($assessment, $data) as $cooked) {
            $cooked->id = $DB->get_field('workshop_grades', 'id', array('assessmentid' => $cooked->assessmentid,
                                                                        'strategy' => 'accumulative',
                                                                        'dimensionid' => $cooked->dimensionid));
            if (false === $cooked->id) {
                // not found - new grade
                $cooked->id = $DB->insert_record('workshop_grades', $cooked);
            } else {
                 // update existing grade
                $DB->update_record('workshop_grades', $cooked);
            }
        }
        // todo recalculate grades
    }

    /**
     * Prepares data returned by {@link workshop_accumulative_assessment_form} so they can be saved into database
     *
     * Called internally from {@link save_assessment()} only. Could be private but
     * keeping protected for unit testing purposes.
     *
     * @param object $raw Raw data returned by mform
     * @return array Array of objects to be inserted/updated in DB
     */
    protected function _cook_assessment_form_data(stdClass $assessment, stdClass $raw) {
        $raw = (array)$raw;
        $cooked = array();
        for ($i = 0; $i < $raw['nodims']; $i++) {
            $grade = new stdClass();
            $grade->assessmentid = $assessment->id;
            $grade->strategy = $raw['strategyname'];
            $grade->dimensionid = $raw['dimensionid__idx_' . $i];
            $grade->grade = $raw['grade__idx_' . $i];
            $grade->peercomment = $raw['peercomment__idx_' . $i];
            $grade->peercommentformat = FORMAT_HTML;
            $cooked[$i] = $grade;
        }
        return $cooked;
    }

}
