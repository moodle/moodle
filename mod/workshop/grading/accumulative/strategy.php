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

    /** @var array definition of the assessment form fields */
    protected $dimensions = null;

    /** @var array options for dimension description fields */
    protected $descriptionopts;

    /**
     * Constructor
     *
     * @param workshop $workshop The workshop instance record
     * @return void
     */
    public function __construct(workshop $workshop) {
        $this->workshop         = $workshop;
        $this->dimensions       = $this->load_fields();
        $this->descriptionopts  = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => -1);
    }

/// Public API

    /**
     * Factory method returning an instance of an assessment form editor class
     *
     * @param $actionurl URL of form handler, defaults to auto detect the current url
     */
    public function get_edit_strategy_form($actionurl=null) {
        global $CFG;    // needed because the included files use it
        global $PAGE;

        require_once(dirname(__FILE__) . '/edit_form.php');

        $fields             = $this->prepare_form_fields($this->dimensions);
        $nodimensions       = count($this->dimensions);
        $norepeatsdefault   = max($nodimensions + WORKSHOP_STRATEGY_ADDDIMS, WORKSHOP_STRATEGY_MINDIMS);
        $norepeats          = optional_param('norepeats', $norepeatsdefault, PARAM_INT);    // number of dimensions
        $noadddims          = optional_param('noadddims', '', PARAM_ALPHA);                 // shall we add more?
        if ($noadddims) {
            $norepeats += WORKSHOP_STRATEGY_ADDDIMS;
        }

        // prepare the embeded files
        for ($i = 0; $i < $nodimensions; $i++) {
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
     * Save the assessment dimensions into database
     *
     * Saves data into the main strategy form table. If the record->id is null or zero,
     * new record is created. If the record->id is not empty, the existing record is updated. Records with
     * empty 'description' field are removed from database.
     * The passed data object are the raw data returned by the get_data().
     *
     * @uses $DB
     * @param stdClass $data Raw data returned by the dimension editor form
     * @return void
     */
    public function save_edit_strategy_form(stdClass $data) {
        global $DB, $PAGE;

        $workshopid = $data->workshopid;
        $norepeats  = $data->norepeats;

        $data       = $this->prepare_database_fields($data);
        $masters    = $data->forms;         // data to be saved into workshop_forms
        $locals     = $data->accumulative;  // data to be saved into workshop_forms_accumulative
        $todelete   = array();              // master ids to be deleted

        for ($i=0; $i < $norepeats; $i++) {
            $local  = $locals[$i];
            $master = $masters[$i];
            if (empty($local->description_editor['text'])) {
                if (!empty($master->id)) {
                    // existing record with empty description - to be deleted
                    $todelete[] = $master->id;
                }
                continue;
            }
            if (empty($master->id)) {
                // new field
                $local->id          = $DB->insert_record("workshop_forms_accumulative", $local);
                $master->localid    = $local->id;
                $master->id         = $DB->insert_record("workshop_forms", $master);
            } else {
                // exiting field
                $DB->update_record("workshop_forms", $master);
                $local->id = $DB->get_field("workshop_forms", "localid", array("id" => $master->id), MUST_EXIST);
            }
            // re-save with correct path to embeded media files
            $local = file_postupdate_standard_editor($local, 'description', $this->descriptionopts,
                $PAGE->context, 'workshop_dimension_description', $master->id);
            $DB->update_record("workshop_forms_accumulative", $local);
        }
        $this->delete_dimensions($todelete);
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

        $fields         = $this->prepare_form_fields($this->dimensions);
        $nodimensions   = count($this->dimensions);

        // rewrite URLs to the embeded files
        for ($i = 0; $i < $nodimensions; $i++) {
            $fields->{'description__idx_'.$i} = file_rewrite_pluginfile_urls($fields->{'description__idx_'.$i},
                'pluginfile.php', $PAGE->context->id, 'workshop_dimension_description', $fields->{'dimensionid__idx_'.$i});
        }

        if ('assessment' === $mode and !empty($assessment)) {
            // load the previously saved assessment data
            $grades = $this->reindex_grades_by_dimension($this->get_current_assessment_data($assessment));
            $current = new stdClass();
            for ($i = 0; $i < $nodimensions; $i++) {
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
        $customdata['nodims']   = $nodimensions;
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
     * @param stdClass $assessment Assessment being filled
     * @param stdClass $data       Raw data as returned by the assessment form
     * @return float|null        Percentual grade for submission as suggested by the peer
     */
    public function save_assessment(stdClass $assessment, stdClass $data) {
        global $DB;

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
        return $this->update_peer_grade($assessment);
    }

/// Internal methods

    /**
     * Loads the fields of the assessment form currently used in this workshop
     *
     * @return array definition of assessment dimensions
     */
    protected function load_fields() {
        global $DB;

        $sql = "SELECT master.id,dim.description,dim.descriptionformat,dim.grade,dim.weight
                  FROM {workshop_forms} master
            INNER JOIN {workshop_forms_accumulative} dim ON (dim.id=master.localid)
                 WHERE master.workshopid = :workshopid AND master.strategy = :strategy
                 ORDER BY master.sort";
        $params = array("workshopid" => $this->workshop->id, "strategy" => $this->workshop->strategy);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Maps the dimension data from DB to the form fields
     *
     * @param array $raw Array of raw dimension records as returned by {@link load_fields()}
     * @return array Array of fields data to be used by the mform set_data
     */
    protected function prepare_form_fields(array $raw) {

        $formdata = new stdClass();
        $key = 0;
        foreach ($raw as $dimension) {
            $formdata->{'dimensionid__idx_' . $key}             = $dimension->id; // master id, not the local one!
            $formdata->{'description__idx_' . $key}             = $dimension->description;
            $formdata->{'description__idx_' . $key.'format'}    = $dimension->descriptionformat;
            $formdata->{'grade__idx_' . $key}                   = $dimension->grade;
            $formdata->{'weight__idx_' . $key}                  = $dimension->weight;
            $key++;
        }
        return $formdata;
    }

    /**
     * Deletes dimensions and removes embedded media from its descriptions
     *
     * todo we may check that there are no assessments done using these dimensions and probably remove them
     *
     * @param array $masterids
     * @return void
     */
    protected function delete_dimensions($masterids) {
        global $DB, $PAGE;

        $masters    = $DB->get_records_list("workshop_forms", "id", $masterids, "", "id,localid");
        $masterids  = array_keys($masters);  // now contains only those really existing
        $localids   = array();
        $fs         = get_file_storage();

        foreach ($masters as $itemid => $master) {
            $fs->delete_area_files($PAGE->context->id, 'workshop_dimension_description', $itemid);
            $localids[] = $master->localid;
        }
        $DB->delete_records_list("workshop_forms_accumulative", "id", $localids);
        $DB->delete_records_list("workshop_forms", "id", $masterids);
    }

    /**
     * Prepares data returned by {@link workshop_edit_accumulative_strategy_form} so they can be saved into database
     *
     * It automatically adds some columns into every record. The sorting is
     * done by the order of the returned array and starts with 1.
     * Called internally from {@link save_edit_strategy_form()} only. Could be private but
     * keeping protected for unit testing purposes.
     *
     * @param stdClass $raw Raw data returned by mform
     * @return array Array of objects to be inserted/updated in DB
     */
    protected function prepare_database_fields(stdClass $raw) {
        global $PAGE;

        $cook               = new stdClass(); // to be returned
        $cook->forms        = array();      // to be stored in {workshop_forms}
        $cook->accumulative = array();      // to be stored in {workshop_forms_accumulative}

        for ($i = 0; $i < $raw->norepeats; $i++) {
            $cook->forms[$i]                = new stdClass();
            $cook->forms[$i]->id            = $raw->{'dimensionid__idx_'.$i};
            $cook->forms[$i]->workshopid    = $this->workshop->id;
            $cook->forms[$i]->sort          = $i + 1;
            $cook->forms[$i]->strategy      = 'accumulative';

            $cook->accumulative[$i]         = new stdClass();
            $cook->accumulative[$i]->description_editor = $raw->{'description__idx_'.$i.'_editor'};
            $cook->accumulative[$i]->grade  = $raw->{'grade__idx_'.$i};
            $cook->accumulative[$i]->weight = $raw->{'weight__idx_'.$i};
        }
        return $cook;
    }

    /**
     * Returns the list of current grades filled by the reviewer
     *
     * @param stdClass $assessment Assessment record
     * @return array of filtered records from the table workshop_grades
     */
    protected function get_current_assessment_data(stdClass $assessment) {
        global $DB;

        // fetch all grades accociated with this assessment
        $grades = $DB->get_records("workshop_grades", array("assessmentid" => $assessment->id));

        // filter grades given under an other strategy or assessment form
        foreach ($grades as $grade) {
            if (!isset($this->dimensions[$grade->dimensionid])) {
                unset ($grades[$grade->id]);
            }
        }
        return $grades;
    }

    /**
     * Reindexes the records returned by {@link get_current_assessment_data} by dimensionid
     *
     * @param mixed $grades
     * @return array
     */
    protected function reindex_grades_by_dimension($grades) {
        $reindexed = array();
        foreach ($grades as $grade) {
            $reindexed[$grade->dimensionid] = $grade;
        }
        return $reindexed;
    }

    /**
     * Aggregates the assessment form data and sets the grade for the submission given by the peer
     *
     * @param stdClass $assessment Assessment record
     * @return float|null          Percentual grade for submission as suggested by the peer
     */
    protected function update_peer_grade(stdClass $assessment) {
        $grades     = $this->get_current_assessment_data($assessment);
        $suggested  = $this->calculate_peer_grade($grades);
        if (!is_null($suggested)) {
            // todo save into workshop_assessments
        }
        return $suggested;
    }

    /**
     * Calculates the aggregated grade given by the reviewer
     *
     * @param array $grades Grade records as returned by {@link get_current_assessment_data}
     * @uses $this->dimensions
     * @return float|null   Percentual grade for submission as suggested by the peer
     */
    protected function calculate_peer_grade(array $grades) {

        if (empty($grades)) {
            return null;
        }
        $sumgrades  = 0;
        $sumweights = 0;
        foreach ($grades as $grade) {
            $dimension = $this->dimensions[$grade->dimensionid];
            if ($dimension->weight < 0) {
                throw new coding_exception('Negative weights are not supported any more. Something is wrong with your data');
            }
            if ($dimension->weight == 0 or $dimension->grade == 0) {
                // does not influence the final grade
                continue;
            }
            if ($dimension->grade < 0) {
                // this is a scale
                $scaleid    = -$dimension->grade;
                $sumgrades  += $this->scale_to_grade($scaleid, $grade->grade) * $dimension->weight;
                $sumweights += $dimension->weight;
            } else {
                // regular grade
                $sumgrades  += ($grade->grade / $dimension->grade) * $dimension->weight;
                $sumweights += $dimension->weight;
            }
        }

        if ($sumweights === 0) {
            return 0;
        }
        return $sumgrades / $sumweights;
    }

    /**
     * Convert scale grade to numerical grades
     *
     * In accumulative grading strategy, scales are considered as grades from 0 to M-1, where M is the number of scale items.
     *
     * @throws coding_exception
     * @param string $scaleid Scale identifier
     * @param int    $item    Selected scale item number, numbered 1, 2, 3, ... M
     * @return float
     */
    protected function scale_to_grade($scaleid, $item) {
        global $DB;

        /** @var cache of numbers of scale items */
        static $numofscaleitems = array();

        if (!isset($numofscaleitems[$scaleid])) {
            $scale = $DB->get_field("scales", "scale", array("id" => $scaleid), MUST_EXIST);
            $items = explode(',', $scale);
            $numofscaleitems[$scaleid] = count($items);
            unset($scale);
            unset($items);
        }

        if ($numofscaleitems[$scaleid] <= 1) {
            throw new coding_exception('Invalid scale definition, no scale items found');
        }

        if ($item <= 0 or $numofscaleitems[$scaleid] < $item) {
            throw new coding_exception('Invalid scale item number');
        }

        return ($item - 1) / ($numofscaleitems[$scaleid] - 1);
    }
}
