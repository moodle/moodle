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

namespace mod_questionnaire\feedback;

defined('MOODLE_INTERNAL') || die();

use invalid_parameter_exception;
use coding_exception;

#[\AllowDynamicProperties]
/**
 * Class for describing a feedback section.
 *
 * @package    mod_questionnaire
 * @copyright  2018 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section {

    /** @var int */
    public $id = 0;
    /** @var int */
    public $surveyid = 0;
    /** @var int */
    public $section = 1;
    /** @var array */
    public $scorecalculation = [];
    /** @var string */
    public $sectionlabel = '';
    /** @var string */
    public $sectionheading = '';
    /** @var string */
    public $sectionheadingformat = FORMAT_HTML;
    /** @var array */
    public $sectionfeedback = [];
    /** @var array */
    public $questions = [];

    /** The table name. */
    const TABLE = 'questionnaire_fb_sections';
    /** Represents the "no score" setting. */
    const NOSCORE = -1;

    /**
     * Section constructor.
     * if $params is provided, loads the entire feedback section record from the specified parameters. Parameters can be:
     *  'id' - the id field of the fb_sections table (required if no 'surveyid' field),
     *  'surveyid' - the surveyid field of the fb_sections table (required if no 'id' field),
     *  'sectionnum' - the section field of the fb_sections table (ignored if 'id' is present; defaults to 1).
     *
     * @param array $questions Array of mod_questionnaire\question objects.
     * @param array $params As above
     * @throws \dml_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public function __construct($questions, $params = []) {

        if (!is_array($params) || !is_array($questions)) {
            throw new coding_exception('Invalid data provided.');
        }

        $this->questions = $questions;

        // Return a new section based on the data parameters if present.
        if (isset($params['id']) || isset($params['surveyid'])) {
            $this->load_section($params);
        }
    }

    /**
     * Factory method to create a new, empty section and return an instance.
     * @param int $surveyid
     * @param string $sectionlabel
     * @return section
     */
    public static function new_section($surveyid, $sectionlabel = '') {
        global $DB;

        $newsection = new self([], []);
        if (empty($sectionlabel)) {
            $sectionlabel = get_string('feedbackdefaultlabel', 'questionnaire');
        }
        $maxsection = $DB->get_field(self::TABLE, 'MAX(section)', ['surveyid' => $surveyid]);
        $newsection->surveyid = $surveyid;
        $newsection->section = $maxsection + 1;
        $newsection->sectionlabel = $sectionlabel;
        $newsection->scorecalculation = $newsection->encode_scorecalculation([]);
        $newsecid = $DB->insert_record(self::TABLE, $newsection);
        $newsection->id = $newsecid;
        $newsection->scorecalculation = [];
        return $newsection;
    }

    /**
     * Loads the entire feedback section record from the specified parameters. Parameters can be:
     *  'id' - the id field of the fb_sections table (required if no 'surveyid' field),
     *  'surveyid' - the surveyid field of the fb_sections table (required if no 'id' field),
     *  'sectionnum' - the section field of the fb_sections table (ignored if 'id' is present; defaults to 1).
     *
     * @param array $params
     * @throws \dml_exception
     * @throws coding_exception
     * @throws invalid_parameter_exception
     */
    public function load_section($params) {
        global $DB;

        if (!is_array($params)) {
            throw new coding_exception('Invalid data provided.');
        } else if (isset($params['id'])) {
            $where = 'WHERE fs.id = :id ';
        } else if (isset($params['surveyid'])) {
            $where = 'WHERE fs.surveyid = :surveyid AND fs.section = :sectionnum ';
            if (!isset($params['sectionnum'])) {
                $params['sectionnum'] = 1;
            }
        } else {
            throw new coding_exception('No valid data parameters provided.');
        }

        $select = 'SELECT f.id as fbid, fs.*, f.feedbacklabel, f.feedbacktext, f.feedbacktextformat, f.minscore, f.maxscore ';
        $from = 'FROM {' . self::TABLE . '} fs LEFT JOIN {' . sectionfeedback::TABLE . '} f ON fs.id = f.sectionid ';
        $order = 'ORDER BY minscore DESC';

        if (!($feedbackrecs = $DB->get_records_sql($select . $from . $where . $order, $params))) {
            throw new invalid_parameter_exception('No feedback sections exists for that data.');
        }
        foreach ($feedbackrecs as $fbid => $feedbackrec) {
            if (empty($this->id)) {
                $this->id = $feedbackrec->id;
                $this->surveyid = $feedbackrec->surveyid;
                $this->section = $feedbackrec->section;
                $this->scorecalculation = $this->get_valid_scorecalculation($feedbackrec->scorecalculation);
                $this->sectionlabel = $feedbackrec->sectionlabel;
                $this->sectionheading = $feedbackrec->sectionheading;
                $this->sectionheadingformat = $feedbackrec->sectionheadingformat;
            }
            if (!empty($fbid)) {
                $feedbackrec->id = $fbid;
                $this->sectionfeedback[$fbid] = new sectionfeedback(0, $feedbackrec);
            }
        }
    }

    /**
     * Loads the section feedback record into the proper array location.
     *
     * @param \stdClass $feedbackrec
     * @return int The id of the section feedback record.
     */
    public function load_sectionfeedback($feedbackrec) {
        if (!isset($feedbackrec->id) || empty($feedbackrec->id)) {
            $sectionfeedback = sectionfeedback::new_sectionfeedback($feedbackrec);
            $this->sectionfeedback[$sectionfeedback->id] = $sectionfeedback;
            return $sectionfeedback->id;
        } else {
            $this->sectionfeedback[$feedbackrec->id] = new sectionfeedback(0, $feedbackrec);
            return $feedbackrec->id;
        }
    }

    /**
     * Updates the object and data record with a new scorecalculation. If no new score provided, uses what's in the object.
     *
     * @param array $scorecalculation
     * @throws coding_exception
     */
    public function set_new_scorecalculation($scorecalculation = null) {
        global $DB;

        if ($scorecalculation == null) {
            $scorecalculation = $this->scorecalculation;
        }

        if (is_array($scorecalculation)) {
            $newscore = $this->encode_scorecalculation($scorecalculation);
            $DB->set_field(self::TABLE, 'scorecalculation', $newscore, ['id' => $this->id]);
            $this->scorecalculation = $scorecalculation;
        } else {
            throw new coding_exception('Invalid scorecalculation format.');
        }
    }

    /**
     * Removes the question from this section and updates the database.
     *
     * @param int $qid The question id and index.
     * @throws \dml_exception
     * @throws coding_exception
     */
    public function remove_question($qid) {
        if (isset($this->scorecalculation[$qid])) {
            unset($this->scorecalculation[$qid]);
            $this->set_new_scorecalculation();
        }
    }

    /**
     * Deletes this section from the database. Object is invalid after that.
     * This will also adjust the section numbers so that they are sequential and begin at 1.
     */
    public function delete() {
        global $DB;

        $this->delete_sectionfeedback();
        $DB->delete_records(self::TABLE, ['id' => $this->id]);

        // Resequence the section numbers as necessary.
        if ($allsections = $DB->get_records(self::TABLE, ['surveyid' => $this->surveyid], 'section ASC')) {
            $count = 1;
            foreach ($allsections as $id => $section) {
                if ($section->section != $count) {
                    $DB->set_field(self::TABLE, 'section', $count, ['id' => $id]);
                }
                $count++;
            }
        }
    }

    /**
     * Deletes the section feedback records from the database and clears the object array.
     *
     * @throws \dml_exception
     */
    public function delete_sectionfeedback() {
        global $DB;

        // It's quicker to delete all of the records at once then to go through the array and delete each object.
        $DB->delete_records(sectionfeedback::TABLE, ['sectionid' => $this->id]);
        $this->sectionfeedback = [];
    }

    /**
     * Updates the data record with what is currently in the object instance.
     *
     * @throws \dml_exception
     * @throws coding_exception
     */
    public function update() {
        global $DB;

        $this->scorecalculation = $this->encode_scorecalculation($this->scorecalculation);
        $DB->update_record(self::TABLE, $this);
        $this->scorecalculation = $this->get_valid_scorecalculation($this->scorecalculation);

        foreach ($this->sectionfeedback as $sectionfeedback) {
            $sectionfeedback->update();
        }
    }

    /**
     * Decode and ensure scorecalculation is what we expect.
     * @param string|null $codedstring
     * @return array
     * @throws coding_exception
     */
    public static function decode_scorecalculation(?string $codedstring): array {
        // Expect a serialized data string.
        if (($codedstring == null)) {
            $codedstring = '';
        }
        if (!is_string($codedstring)) {
            throw new coding_exception('Invalid scorecalculation format.');
        }
        if (!empty($codedstring)) {
            $scorecalculation = unserialize_array($codedstring) ?: [];
        } else {
            $scorecalculation = [];
        }

        if (!is_array($scorecalculation)) {
            throw new coding_exception('Invalid scorecalculation format.');
        }

        foreach ($scorecalculation as $score) {
            if (!empty($score) && !is_numeric($score)) {
                throw new coding_exception('Invalid scorecalculation format.');
            }
        }

        return $scorecalculation;
    }

    /**
     * Return the decoded and validated calculation array.
     * @param string $codedstring
     * @return mixed
     * @throws coding_exception
     */
    protected function get_valid_scorecalculation($codedstring) {
        $scorecalculation = static::decode_scorecalculation($codedstring);

        // Check for deleted questions and questions that don't support scores.
        foreach ($scorecalculation as $qid => $score) {
            if (!isset($this->questions[$qid])) {
                unset($scorecalculation[$qid]);
            } else if (!$this->questions[$qid]->supports_feedback_scores()) {
                $scorecalculation[$qid] = self::NOSCORE;
            }
        }

        return $scorecalculation;
    }

    /**
     * Return the encoded score array as a serialized string.
     * @param string $scorearray
     * @return mixed
     * @throws coding_exception
     */
    protected function encode_scorecalculation($scorearray) {
        // Expect an array.
        if (!is_array($scorearray)) {
            throw new coding_exception('Invalid scorearray format.');
        }

        $scorecalculation = serialize($scorearray);

        return $scorecalculation;
    }
}
