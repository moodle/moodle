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

use invalid_parameter_exception;
use coding_exception;

/**
 * Class for describing a feedback section's feedback definition.
 *
 * @package    mod_questionnaire
 * @copyright  2018 onward Mike Churchward (mike.churchward@poetopensource.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sectionfeedback {
    /** @var int */
    public $id = 0;
    /** @var int */
    public $sectionid = 0;
    /** @var string */
    public $feedbacklabel = ''; // I don't think this is actually used?
    /** @var string */
    public $feedbacktext = '';
    /** @var string */
    public $feedbacktextformat = FORMAT_HTML;
    /** @var float */
    public $minscore = 0.0;
    /** @var float */
    public $maxscore = 0.0;

    /** The table name. */
    const TABLE = 'questionnaire_feedback';

    /**
     * Class constructor.
     * @param int $id
     * @param null|object $record
     */
    public function __construct($id = 0, $record = null) {
        // Return a new section based on the data id.
        if ($id != 0) {
            $record = $this->get_sectionfeedback($id);
            if (!$record) {
                throw new invalid_parameter_exception('No section feedback exists with that ID.');
            }
        }
        if (($id != 0) || is_object($record)) {
            $this->loadproperties($record);
        }
    }

    /**
     * Factory method to create a new sectionfeedback from the provided data and return an instance.
     * @param \stdClass $data
     * @return sectionfeedback
     */
    public static function new_sectionfeedback($data) {
        global $DB;
        $newsf = new self();
        $newsf->sectionid = $data->sectionid;
        $newsf->feedbacklabel = $data->feedbacklabel;
        $newsf->feedbacktext = $data->feedbacktext;
        $newsf->feedbacktextformat = $data->feedbacktextformat;
        $newsf->minscore = $data->minscore;
        $newsf->maxscore = $data->maxscore;
        $newsfid = $DB->insert_record(self::TABLE, $newsf);
        $newsf->id = $newsfid;
        return $newsf;
    }

    /**
     * Updates the data record with what is currently in the object instance.
     *
     * @throws \dml_exception
     * @throws coding_exception
     */
    public function update() {
        global $DB;

        $DB->update_record(self::TABLE, $this);
    }

    /**
     * Return the record specified by the id.
     * @param int $id
     * @return mixed
     */
    protected function get_sectionfeedback($id) {
        global $DB;

        return $DB->get_record(self::TABLE, ['id' => $id]);
    }

    /**
     * Load object properties from a provided record for any properties defined in that record.
     *
     * @param object $record
     */
    protected function loadproperties($record) {
        foreach ($this as $property => $value) {
            if (isset($record->$property)) {
                $this->$property = $record->$property;
            }
        }
    }

    /**
     * Get the data for this section's feedback from the database.
     *
     * @param int $id
     * @return mixed
     * @throws \dml_exception
     */
    protected function get_section($id) {
        global $DB;

        return $DB->get_record(self::TABLE, ['id' => $id]);
    }
}
