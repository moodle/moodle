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
class workshop_accumulative_strategy extends workshop_base_strategy implements workshop_strategy {

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

        // prepare the embeded files
        for ($i = 0; $i < $this->nodimensions; $i++) {
            // prepare all editor elements
            $fields = file_prepare_standard_editor($fields, 'description__idx_'.$i, $this->descriptionopts,
                $PAGE->context, 'workshop_dimension_description', $fields->{'dimensionid__idx_'.$i});
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

        $sql = 'SELECT master.id,dim.description,dim.descriptionformat,dim.grade,dim.weight
                FROM {workshop_forms} master
                JOIN {workshop_forms_accumulative} dim ON (dim.id=master.localid)
                WHERE master.workshopid = ?
                ORDER BY master.sort';
        $params[0] = $this->workshop->id;

        $dims = $DB->get_records_sql($sql, $params);
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

        $formdata = new stdClass();
        $key = 0;
        foreach ($raw as $dimension) {
            $formdata->{'dimensionid__idx_' . $key}             = $dimension->id;
            $formdata->{'description__idx_' . $key}             = $dimension->description;
            $formdata->{'description__idx_' . $key.'format'}    = $dimension->descriptionformat;
            $formdata->{'grade__idx_' . $key}                   = $dimension->grade;
            $formdata->{'weight__idx_' . $key}                  = $dimension->weight;
            $key++;
        }
        return $formdata;
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
        global $DB, $PAGE;

        if (!isset($data->strategyname) || ($data->strategyname != $this->name())) {
            // the workshop strategy has changed since the form was opened for editing
            throw new moodle_exception('strategyhaschanged', 'workshop');
        }
        $workshopid = $data->workshopid;
        $norepeats  = $data->norepeats;

        $data           = $this->_cook_edit_form_data($data);
        $masterrecords  = $data->forms;
        $localrecords   = $data->forms_accumulative;
        $todeletelocal  = array(); // local ids to be deleted
        $todeletemaster = array(); // master ids to be deleted

        for ($i=0; $i < $norepeats; $i++) {
            $local  = $localrecords[$i];
            $master = $masterrecords[$i];
            if (empty($local->description_editor['text'])) {
                if (!empty($local->id)) {
                    // existing record with empty description - to be deleted
                    $todeletelocal[]    = $local->id;
                    $todeletemaster[]   = $this->dimension_master_id($local->id);
                }
                continue;
            }
            if (empty($local->id)) {
                // new field
                $local->id          = $DB->insert_record('workshop_forms_accumulative', $local);
                $master->localid    = $local->id;
                $master->id         = $DB->insert_record('workshop_forms', $master);
            } else {
                // exiting field
                $master->id = $this->dimension_master_id($local->id);
                $DB->update_record('workshop_forms', $master);
            }
            // $local record now has its id, let us re-save it with correct path to embeded media files
            $local = file_postupdate_standard_editor($local, 'description', $this->descriptionopts,
                $PAGE->context, 'workshop_dimension_description', $local->id);
            $DB->update_record('workshop_forms_accumulative', $local);
        }
        // unlink embedded files and delete emptied dimensions
        $fs = get_file_storage();
        foreach ($todeletelocal as $itemid) {
            $fs->delete_area_files($PAGE->context->id, 'workshop_dimension_description', $itemid);
        }
        $DB->delete_records_list('workshop_forms_accumulative', 'id', $todeletelocal);
        $DB->delete_records_list('workshop_forms', 'id', $todeletemaster);
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

        $cook                       = new stdClass();   // to be returned
        $cook->forms                = array();          // to be stored in {workshop_forms}
        $cook->forms_accumulative   = array();          // to be stored in {workshop_forms_accumulative}

        for ($i = 0; $i < $raw->norepeats; $i++) {
            $cook->forms_accumulative[$i] = new stdClass();
            $fieldname = 'dimensionid__idx_'.$i;
            $cook->forms_accumulative[$i]->id                   = isset($raw->$fieldname) ? $raw->$fieldname : null;
            $fieldname = 'description__idx_'.$i.'_editor';
            $cook->forms_accumulative[$i]->description_editor   = isset($raw->$fieldname) ? $raw->$fieldname : null;
            $fieldname = 'grade__idx_'.$i;
            $cook->forms_accumulative[$i]->grade                = isset($raw->$fieldname) ? $raw->$fieldname : null;
            $fieldname = 'weight__idx_'.$i;
            $cook->forms_accumulative[$i]->weight               = isset($raw->$fieldname) ? $raw->$fieldname : null;

            $cook->forms[$i]                = new stdClass();
            $cook->forms[$i]->id            = null; // will be checked later, considered unknown at the moment
            $cook->forms[$i]->workshopid    = $this->workshop->id;
            $cook->forms[$i]->sort          = $i + 1;
            $cook->forms[$i]->strategy      = 'accumulative';
            $cook->forms[$i]->dimensionid   = $cook->forms_accumulative[$i]->id;
        }
        return $cook;
    }

    /**
     * Factory method returning an instance of an assessment form
     *
     * @param moodle_url $actionurl URL of form handler, defaults to auto detect the current url
     * @param string $mode          Mode to open the form in: preview/assessment
     */
    public function get_assessment_form(moodle_url $actionurl=null, $mode='preview', stdClass $assessment=null) {
        global $CFG;    // needed because the included files use it
        global $PAGE;
        global $DB;
        require_once(dirname(__FILE__) . '/assessment_form.php');

        $fields = $this->load_fields();
        if (is_null($this->nodimensions)) {
            throw new coding_exception('You forgot to set the number of dimensions in load_fields()');
        }

        // rewrite URLs to the embeded files
        for ($i = 0; $i < $this->nodimensions; $i++) {
            $fields->{'description__idx_'.$i} = file_rewrite_pluginfile_urls($fields->{'description__idx_'.$i},
                'pluginfile.php', $PAGE->context->id, 'workshop_dimension_description', $fields->{'dimensionid__idx_'.$i});
        }

        if ('assessment' === $mode and !empty($assessment)) {
            // load the previously saved assessment data
            $grades = $DB->get_records('workshop_grades', array('assessmentid' => $assessment->id), '', 'dimensionid,*');
            $current = new stdClass();
            for ($i = 0; $i < $this->nodimensions; $i++) {
                $dimid = $fields->{'dimensionid__idx_'.$i};
                if (isset($grades[$dimid])) {
                    $current->{'gradeid__idx_'.$i}      = $grades[$dimid]->id;
                    $current->{'grade__idx_'.$i}        = $grades[$dimid]->grade;
                    $current->{'peercomment__idx_'.$i}  = $grades[$dimid]->peercomment;
                }
            }
        }

        // set up the required custom data common for all strategies
        $customdata['strategy'] = $this;
        $customdata['mode']     = $mode;

        // set up strategy-specific custom data
        $customdata['nodims']   = $this->nodimensions;
        $customdata['fields']   = $fields;
        $customdata['current']  = isset($current) ? $current : null;
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
        for ($i = 0; $i < $data->nodims; $i++) {
            $grade = new stdClass();
            $grade->id = $data->{'gradeid__idx_' . $i};
            $grade->assessmentid = $assessment->id;
            $grade->dimensionid = $data->{'dimensionid__idx_' . $i};
            $grade->grade = $data->{'grade__idx_' . $i};
            $grade->peercomment = $data->{'peercomment__idx_' . $i};
            $grade->peercommentformat = FORMAT_HTML;
            if (empty($grade->id)) {
                // new grade
                $grade->id = $DB->insert_record('workshop_grades', $grade);
            } else {
                // updated grade
                $DB->update_record('workshop_grades', $grade);
            }
        }
        // todo recalculate grades immediately or by cron ?
    }

}
