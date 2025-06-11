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
 * This file defines a class with rubric grading strategy logic
 *
 * @package    workshopform_rubric
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');        // Interface definition.
require_once($CFG->libdir . '/gradelib.php'); // To handle float vs decimal issues.

/**
 * Server workshop files
 *
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 */
function workshopform_rubric_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);

    if ($filearea !== 'description') {
        return false;
    }

    $itemid = (int)array_shift($args); // the id of the assessment form dimension
    if (!$workshop = $DB->get_record('workshop', array('id' => $cm->instance))) {
        send_file_not_found();
    }

    if (!$dimension = $DB->get_record('workshopform_rubric', array('id' => $itemid ,'workshopid' => $workshop->id))) {
        send_file_not_found();
    }

    // TODO now make sure the user is allowed to see the file
    // (media embedded into the dimension description)
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/workshopform_rubric/$filearea/$itemid/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // finally send the file
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Rubric grading strategy logic.
 */
class workshop_rubric_strategy implements workshop_strategy {

    /** @var default number of dimensions to show */
    const MINDIMS = 3;

    /** @var number of dimensions to add */
    const ADDDIMS = 2;

    /** @var workshop the parent workshop instance */
    protected $workshop;

    /** @var array definition of the assessment form fields */
    protected $dimensions = null;

    /** @var array options for dimension description fields */
    protected $descriptionopts;

    /** @var array options for level definition fields */
    protected $definitionopts;

    /** @var object rubric configuration */
    protected $config;

    /**
     * Constructor
     *
     * @param workshop $workshop The workshop instance record
     * @return void
     */
    public function __construct(workshop $workshop) {
        $this->workshop         = $workshop;
        $this->dimensions       = $this->load_fields();
        $this->config           = $this->load_config();
        $this->descriptionopts  = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => -1);
        //one day the definitions may become proper wysiwyg fields - not used yet
        $this->definitionopts   = array('trusttext' => true, 'subdirs' => false, 'maxfiles' => -1);
    }

    /**
     * Factory method returning an instance of an assessment form editor class
     *
     * @param $actionurl URL of form handler, defaults to auto detect the current url
     */
    public function get_edit_strategy_form($actionurl=null) {
        global $CFG;    // needed because the included files use it

        require_once(__DIR__ . '/edit_form.php');

        $fields             = $this->prepare_form_fields($this->dimensions);
        $fields->config_layout = $this->config->layout;

        $nodimensions       = count($this->dimensions);
        $norepeatsdefault   = max($nodimensions + self::ADDDIMS, self::MINDIMS);
        $norepeats          = optional_param('norepeats', $norepeatsdefault, PARAM_INT);    // number of dimensions
        $adddims            = optional_param('adddims', '', PARAM_ALPHA);                   // shall we add more dimensions?
        if ($adddims) {
            $norepeats += self::ADDDIMS;
        }

        // Append editor context to editor options, giving preference to existing context.
        $this->descriptionopts = array_merge(array('context' => $this->workshop->context), $this->descriptionopts);

        // prepare the embeded files
        for ($i = 0; $i < $nodimensions; $i++) {
            // prepare all editor elements
            $fields = file_prepare_standard_editor($fields, 'description__idx_'.$i, $this->descriptionopts,
                $this->workshop->context, 'workshopform_rubric', 'description', $fields->{'dimensionid__idx_'.$i});
        }

        $customdata = array();
        $customdata['workshop'] = $this->workshop;
        $customdata['strategy'] = $this;
        $customdata['norepeats'] = $norepeats;
        $customdata['descriptionopts'] = $this->descriptionopts;
        $customdata['current']  = $fields;
        $attributes = array('class' => 'editstrategyform');

        return new workshop_edit_rubric_strategy_form($actionurl, $customdata, 'post', '', $attributes);
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
    public function save_edit_strategy_form(stdclass $data) {
        global $DB;

        $norepeats  = $data->norepeats;
        $layout     = $data->config_layout;
        $data       = $this->prepare_database_fields($data);
        $deletedims = array();  // dimension ids to be deleted
        $deletelevs = array();  // level ids to be deleted

        if ($DB->record_exists('workshopform_rubric_config', array('workshopid' => $this->workshop->id))) {
            $DB->set_field('workshopform_rubric_config', 'layout', $layout, array('workshopid' => $this->workshop->id));
        } else {
            $record = new stdclass();
            $record->workshopid = $this->workshop->id;
            $record->layout     = $layout;
            $DB->insert_record('workshopform_rubric_config', $record, false);
        }

        foreach ($data as $record) {
            if (0 == strlen(trim($record->description_editor['text']))) {
                if (!empty($record->id)) {
                    // existing record with empty description - to be deleted
                    $deletedims[] = $record->id;
                    foreach ($record->levels as $level) {
                        if (!empty($level->id)) {
                            $deletelevs[] = $level->id;
                        }
                    }
                }
                continue;
            }
            if (empty($record->id)) {
                // new field
                $record->id = $DB->insert_record('workshopform_rubric', $record);
            } else {
                // exiting field
                $DB->update_record('workshopform_rubric', $record);
            }
            // re-save with correct path to embeded media files
            $record = file_postupdate_standard_editor($record, 'description', $this->descriptionopts,
                                                      $this->workshop->context, 'workshopform_rubric', 'description', $record->id);
            $DB->update_record('workshopform_rubric', $record);

            // create/update the criterion levels
            foreach ($record->levels as $level) {
                $level->dimensionid = $record->id;
                if (0 == strlen(trim($level->definition))) {
                    if (!empty($level->id)) {
                        $deletelevs[] = $level->id;
                    }
                    continue;
                }
                if (empty($level->id)) {
                    // new field
                    $level->id = $DB->insert_record('workshopform_rubric_levels', $level);
                } else {
                    // exiting field
                    $DB->update_record('workshopform_rubric_levels', $level);
                }
            }
        }
        $DB->delete_records_list('workshopform_rubric_levels', 'id', $deletelevs);
        $this->delete_dimensions($deletedims);
    }

    /**
     * Factory method returning an instance of an assessment form
     *
     * @param moodle_url $actionurl URL of form handler, defaults to auto detect the current url
     * @param string $mode          Mode to open the form in: preview/assessment/readonly
     * @param stdClass $assessment  The current assessment
     * @param bool $editable
     * @param array $options
     */
    public function get_assessment_form(?moodle_url $actionurl=null, $mode='preview', ?stdclass $assessment=null, $editable=true, $options=array()) {
        global $CFG;    // needed because the included files use it
        global $DB;
        require_once(__DIR__ . '/assessment_form.php');

        $fields         = $this->prepare_form_fields($this->dimensions);
        $nodimensions   = count($this->dimensions);

        // rewrite URLs to the embeded files
        for ($i = 0; $i < $nodimensions; $i++) {
            $fields->{'description__idx_'.$i} = file_rewrite_pluginfile_urls($fields->{'description__idx_'.$i},
                'pluginfile.php', $this->workshop->context->id, 'workshopform_rubric', 'description',
                $fields->{'dimensionid__idx_'.$i});

        }

        if ('assessment' === $mode and !empty($assessment)) {
            // load the previously saved assessment data
            $grades = $this->get_current_assessment_data($assessment);
            $current = new stdclass();
            for ($i = 0; $i < $nodimensions; $i++) {
                $dimid = $fields->{'dimensionid__idx_'.$i};
                if (isset($grades[$dimid])) {
                    $givengrade = $grades[$dimid]->grade;
                    // find a level with this grade
                    $levelid = null;
                    foreach ($this->dimensions[$dimid]->levels as $level) {
                        if (grade_floats_equal($level->grade, $givengrade)) {
                            $levelid = $level->id;
                            break;
                        }
                    }
                    $current->{'gradeid__idx_'.$i}       = $grades[$dimid]->id;
                    $current->{'chosenlevelid__idx_'.$i} = $levelid;
                }
            }
        }

        // set up the required custom data common for all strategies
        $customdata['strategy'] = $this;
        $customdata['workshop'] = $this->workshop;
        $customdata['mode']     = $mode;
        $customdata['options']  = $options;

        // set up strategy-specific custom data
        $customdata['nodims']   = $nodimensions;
        $customdata['fields']   = $fields;
        $customdata['current']  = isset($current) ? $current : null;
        $attributes = array('class' => 'assessmentform rubric ' . $this->config->layout);

        $formclassname = 'workshop_rubric_' . $this->config->layout . '_assessment_form';
        return new $formclassname($actionurl, $customdata, 'post', '', $attributes, $editable);
    }

    /**
     * Saves the filled assessment
     *
     * This method processes data submitted using the form returned by {@link get_assessment_form()}
     *
     * @param stdClass $assessment Assessment being filled
     * @param stdClass $data       Raw data as returned by the assessment form
     * @return float|null          Raw grade (0.00000 to 100.00000) for submission as suggested by the peer
     */
    public function save_assessment(stdclass $assessment, stdclass $data) {
        global $DB;

        for ($i = 0; isset($data->{'dimensionid__idx_' . $i}); $i++) {
            $grade = new stdclass();
            $grade->id = $data->{'gradeid__idx_' . $i};
            $grade->assessmentid = $assessment->id;
            $grade->strategy = 'rubric';
            $grade->dimensionid = $data->{'dimensionid__idx_' . $i};
            $chosenlevel = $data->{'chosenlevelid__idx_'.$i};
            $grade->grade = $this->dimensions[$grade->dimensionid]->levels[$chosenlevel]->grade;

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

    /**
     * Has the assessment form been defined and is ready to be used by the reviewers?
     *
     * @return boolean
     */
    public function form_ready() {
        if (count($this->dimensions) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @see parent::get_assessments_recordset()
     */
    public function get_assessments_recordset($restrict=null) {
        global $DB;

        $sql = 'SELECT s.id AS submissionid,
                       a.id AS assessmentid, a.weight AS assessmentweight, a.reviewerid, a.gradinggrade,
                       g.dimensionid, g.grade
                  FROM {workshop_submissions} s
                  JOIN {workshop_assessments} a ON (a.submissionid = s.id)
                  JOIN {workshop_grades} g ON (g.assessmentid = a.id AND g.strategy = :strategy)
                 WHERE s.example=0 AND s.workshopid=:workshopid'; // to be cont.
        $params = array('workshopid' => $this->workshop->id, 'strategy' => $this->workshop->strategy);

        if (is_null($restrict)) {
            // update all users - no more conditions
        } elseif (!empty($restrict)) {
            list($usql, $uparams) = $DB->get_in_or_equal($restrict, SQL_PARAMS_NAMED);
            $sql .= " AND a.reviewerid $usql";
            $params = array_merge($params, $uparams);
        } else {
            throw new coding_exception('Empty value is not a valid parameter here');
        }

        $sql .= ' ORDER BY s.id'; // this is important for bulk processing

        return $DB->get_recordset_sql($sql, $params);
    }

    /**
     * @see parent::get_dimensions_info()
     */
    public function get_dimensions_info() {
        global $DB;

        $sql = 'SELECT d.id AS id, MIN(l.grade) AS min, MAX(l.grade) AS max, 1 AS weight
                  FROM {workshopform_rubric} d
            INNER JOIN {workshopform_rubric_levels} l ON (d.id = l.dimensionid)
                 WHERE d.workshopid = :workshopid
              GROUP BY d.id';
        $params = array('workshopid' => $this->workshop->id);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Is a given scale used by the instance of workshop?
     *
     * This grading strategy does not use scales.
     *
     * @param int $scaleid id of the scale to check
     * @param int|null $workshopid id of workshop instance to check, checks all in case of null
     * @return bool
     */
    public static function scale_used($scaleid, $workshopid=null) {
        return false;
    }

    /**
     * Delete all data related to a given workshop module instance
     *
     * @see workshop_delete_instance()
     * @param int $workshopid id of the workshop module instance being deleted
     * @return void
     */
    public static function delete_instance($workshopid) {
        global $DB;

        $dimensions = $DB->get_records('workshopform_rubric', array('workshopid' => $workshopid), '', 'id');
        $DB->delete_records_list('workshopform_rubric_levels', 'dimensionid', array_keys($dimensions));
        $DB->delete_records('workshopform_rubric', array('workshopid' => $workshopid));
        $DB->delete_records('workshopform_rubric_config', array('workshopid' => $workshopid));
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Internal methods                                                           //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Loads the fields of the assessment form currently used in this workshop
     *
     * @return array definition of assessment dimensions
     */
    protected function load_fields() {
        global $DB;

        $sql = "SELECT r.id AS rid, r.sort, r.description, r.descriptionformat,
                       l.id AS lid, l.grade, l.definition, l.definitionformat
                  FROM {workshopform_rubric} r
             LEFT JOIN {workshopform_rubric_levels} l ON (l.dimensionid = r.id)
                 WHERE r.workshopid = :workshopid
                 ORDER BY r.sort, l.grade";
        $params = array('workshopid' => $this->workshop->id);

        $rs = $DB->get_recordset_sql($sql, $params);
        $fields = array();
        foreach ($rs as $record) {
            if (!isset($fields[$record->rid])) {
                $fields[$record->rid] = new stdclass();
                $fields[$record->rid]->id = $record->rid;
                $fields[$record->rid]->sort = $record->sort;
                $fields[$record->rid]->description = $record->description;
                $fields[$record->rid]->descriptionformat = $record->descriptionformat;
                $fields[$record->rid]->levels = array();
            }
            if (!empty($record->lid)) {
                $fields[$record->rid]->levels[$record->lid] = new stdclass();
                $fields[$record->rid]->levels[$record->lid]->id = $record->lid;
                $fields[$record->rid]->levels[$record->lid]->grade = $record->grade;
                $fields[$record->rid]->levels[$record->lid]->definition = $record->definition;
                $fields[$record->rid]->levels[$record->lid]->definitionformat = $record->definitionformat;
            }
        }
        $rs->close();

        return $fields;
    }

    /**
     * Get the configuration for the current rubric strategy
     *
     * @return object
     */
    protected function load_config() {
        global $DB;

        if (!$config = $DB->get_record('workshopform_rubric_config', array('workshopid' => $this->workshop->id), 'layout')) {
            $config = (object)array('layout' => 'list');
        }
        return $config;
    }

    /**
     * Maps the dimension data from DB to the form fields
     *
     * @param array $fields Array of dimensions definition as returned by {@link load_fields()}
     * @return stdclass of fields data to be used by the mform set_data
     */
    protected function prepare_form_fields(array $fields) {

        $formdata = new stdclass();
        $key = 0;
        foreach ($fields as $field) {
            $formdata->{'dimensionid__idx_' . $key}             = $field->id;
            $formdata->{'description__idx_' . $key}             = $field->description;
            $formdata->{'description__idx_' . $key.'format'}    = $field->descriptionformat;
            $formdata->{'numoflevels__idx_' . $key}             = count($field->levels);
            $lev = 0;
            foreach ($field->levels as $level) {
                $formdata->{'levelid__idx_' . $key . '__idy_' . $lev} = $level->id;
                $formdata->{'grade__idx_' . $key . '__idy_' . $lev} = $level->grade;
                $formdata->{'definition__idx_' . $key . '__idy_' . $lev} = $level->definition;
                $formdata->{'definition__idx_' . $key . '__idy_' . $lev . 'format'} = $level->definitionformat;
                $lev++;
            }
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
    protected function delete_dimensions(array $ids) {
        global $DB;

        $fs = get_file_storage();
        foreach ($ids as $id) {
            if (!empty($id)) {   // to prevent accidental removal of all files in the area
                $fs->delete_area_files($this->workshop->context->id, 'workshopform_rubric', 'description', $id);
            }
        }
        $DB->delete_records_list('workshopform_rubric', 'id', $ids);
    }

    /**
     * Prepares data returned by {@link workshop_edit_rubric_strategy_form} so they can be saved into database
     *
     * It automatically adds some columns into every record. The sorting is
     * done by the order of the returned array and starts with 1.
     * Called internally from {@link save_edit_strategy_form()} only. Could be private but
     * keeping protected for unit testing purposes.
     *
     * @param stdClass $raw Raw data returned by mform
     * @return array Array of objects to be inserted/updated in DB
     */
    protected function prepare_database_fields(stdclass $raw) {

        $cook = array();

        for ($i = 0; $i < $raw->norepeats; $i++) {
            $cook[$i]                       = new stdclass();
            $cook[$i]->id                   = $raw->{'dimensionid__idx_'.$i};
            $cook[$i]->workshopid           = $this->workshop->id;
            $cook[$i]->sort                 = $i + 1;
            $cook[$i]->description_editor   = $raw->{'description__idx_'.$i.'_editor'};
            $cook[$i]->levels               = array();

            $j = 0;
            while (isset($raw->{'levelid__idx_' . $i . '__idy_' . $j})) {
                $level                      = new stdclass();
                $level->id                  = $raw->{'levelid__idx_' . $i . '__idy_' . $j};
                $level->grade               = $raw->{'grade__idx_'.$i.'__idy_'.$j};
                $level->definition          = $raw->{'definition__idx_'.$i.'__idy_'.$j};
                $level->definitionformat    = FORMAT_HTML;
                $cook[$i]->levels[]         = $level;
                $j++;
            }
        }

        return $cook;
    }

    /**
     * Returns the list of current grades filled by the reviewer indexed by dimensionid
     *
     * @param stdClass $assessment Assessment record
     * @return array [int dimensionid] => stdclass workshop_grades record
     */
    protected function get_current_assessment_data(stdclass $assessment) {
        global $DB;

        if (empty($this->dimensions)) {
            return array();
        }
        list($dimsql, $dimparams) = $DB->get_in_or_equal(array_keys($this->dimensions), SQL_PARAMS_NAMED);
        // beware! the caller may rely on the returned array is indexed by dimensionid
        $sql = "SELECT dimensionid, wg.*
                  FROM {workshop_grades} wg
                 WHERE assessmentid = :assessmentid AND strategy= :strategy AND dimensionid $dimsql";
        $params = array('assessmentid' => $assessment->id, 'strategy' => 'rubric');
        $params = array_merge($params, $dimparams);

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Aggregates the assessment form data and sets the grade for the submission given by the peer
     *
     * @param stdClass $assessment Assessment record
     * @return float|null          Raw grade (from 0.00000 to 100.00000) for submission as suggested by the peer
     */
    protected function update_peer_grade(stdclass $assessment) {
        $grades     = $this->get_current_assessment_data($assessment);
        $suggested  = $this->calculate_peer_grade($grades);
        if (!is_null($suggested)) {
            $this->workshop->set_peer_grade($assessment->id, $suggested);
        }
        return $suggested;
    }

    /**
     * Calculates the aggregated grade given by the reviewer
     *
     * @param array $grades Grade records as returned by {@link get_current_assessment_data}
     * @uses $this->dimensions
     * @return float|null   Raw grade (from 0.00000 to 100.00000) for submission as suggested by the peer
     */
    protected function calculate_peer_grade(array $grades) {

        if (empty($grades)) {
            return null;
        }

        // summarize the grades given in rubrics
        $sumgrades  = 0;
        foreach ($grades as $grade) {
            $sumgrades += $grade->grade;
        }

        // get the minimal and maximal possible grade (sum of minimal/maximal grades across all dimensions)
        $mingrade = 0;
        $maxgrade = 0;
        foreach ($this->dimensions as $dimension) {
            $mindimensiongrade = null;
            $maxdimensiongrade = null;
            foreach ($dimension->levels as $level) {
                if (is_null($mindimensiongrade) or $level->grade < $mindimensiongrade) {
                    $mindimensiongrade = $level->grade;
                }
                if (is_null($maxdimensiongrade) or $level->grade > $maxdimensiongrade) {
                    $maxdimensiongrade = $level->grade;
                }
            }
            $mingrade += $mindimensiongrade;
            $maxgrade += $maxdimensiongrade;
        }

        if ($maxgrade - $mingrade > 0) {
            return grade_floatval(100 * ($sumgrades - $mingrade) / ($maxgrade - $mingrade));
        } else {
            return null;
        }
    }
}
