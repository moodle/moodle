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
 * Search area base class for activities.
 *
 * @package    core_search
 * @copyright  2016 Dan Poltawski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Base implementation for activity modules.
 *
 * @package    core_search
 * @copyright  2016 Dan Poltawski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_activity extends base_mod {

    /**
     * @var string The time modified field name.
     *
     * Activities not using timemodified as field name
     * can overwrite this constant.
     */
    const MODIFIED_FIELD_NAME = 'timemodified';

    /**
     * Activities with a time created field can overwrite this constant.
     */
    const CREATED_FIELD_NAME = '';

    /**
     * The context levels the search area is working on.
     * @var array
     */
    protected static $levels = [CONTEXT_MODULE];

    /** @var array activity data instance. */
    public $activitiesdata = [];

    /**
     * Returns recordset containing all activities within the given context.
     *
     * @param \context|null $context Context
     * @param int $modifiedfrom Return only records modified after this date
     * @return \moodle_recordset|null Recordset, or null if no possible activities in given context
     */
    public function get_document_recordset($modifiedfrom = 0, \context $context = null) {
        global $DB;
        list ($contextjoin, $contextparams) = $this->get_context_restriction_sql(
                $context, $this->get_module_name(), 'modtable');
        if ($contextjoin === null) {
            return null;
        }
        return $DB->get_recordset_sql('SELECT modtable.* FROM {' . $this->get_module_name() .
                '} modtable ' . $contextjoin . ' WHERE modtable.' . static::MODIFIED_FIELD_NAME .
                ' >= ? ORDER BY modtable.' . static::MODIFIED_FIELD_NAME . ' ASC',
                array_merge($contextparams, [$modifiedfrom]));
    }

    /**
     * Returns the document associated with this activity.
     *
     * This default implementation for activities sets the activity name to title and the activity intro to
     * content. Any activity can overwrite this function if it is interested in setting other fields than the
     * default ones, or to fill description optional fields with extra stuff.
     *
     * @param \stdClass $record
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = array()) {

        try {
            $cm = $this->get_cm($this->get_module_name(), $record->id, $record->course);
            $context = \context_module::instance($cm->id);
        } catch (\dml_missing_record_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document, not all required data is available: ' .
                $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        } catch (\dml_exception $ex) {
            // Notify it as we run here as admin, we should see everything.
            debugging('Error retrieving ' . $this->areaid . ' ' . $record->id . ' document: ' . $ex->getMessage(), DEBUG_DEVELOPER);
            return false;
        }

        // Prepare associative array with data from DB.
        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->name, false));
        $doc->set('content', content_to_text($record->intro, $record->introformat));
        $doc->set('contextid', $context->id);
        $doc->set('courseid', $record->course);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->{static::MODIFIED_FIELD_NAME});

        // Check if this document should be considered new.
        if (isset($options['lastindexedtime'])) {
            $createdfield = static::CREATED_FIELD_NAME;
            if (!empty($createdfield) && ($options['lastindexedtime'] < $record->{$createdfield})) {
                // If the document was created after the last index time, it must be new.
                $doc->set_is_new(true);
            }
        }

        return $doc;
    }

    /**
     * Whether the user can access the document or not.
     *
     * @throws \dml_missing_record_exception
     * @throws \dml_exception
     * @param int $id The activity instance id.
     * @return bool
     */
    public function check_access($id) {
        global $DB;

        try {
            $activity = $this->get_activity($id);
            $cminfo = $this->get_cm($this->get_module_name(), $activity->id, $activity->course);
            $cminfo->get_course_module_record();
        } catch (\dml_missing_record_exception $ex) {
            return \core_search\manager::ACCESS_DELETED;
        } catch (\dml_exception $ex) {
            return \core_search\manager::ACCESS_DENIED;
        }

        // Recheck uservisible although it should have already been checked in core_search.
        if ($cminfo->uservisible === false) {
            return \core_search\manager::ACCESS_DENIED;
        }

        return \core_search\manager::ACCESS_GRANTED;
    }

    /**
     * Link to the module instance.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_doc_url(\core_search\document $doc) {
        return $this->get_context_url($doc);
    }

    /**
     * Link to the module instance.
     *
     * @param \core_search\document $doc
     * @return \moodle_url
     */
    public function get_context_url(\core_search\document $doc) {
        $cminfo = $this->get_cm($this->get_module_name(), strval($doc->get('itemid')), $doc->get('courseid'));
        return new \moodle_url('/mod/' . $this->get_module_name() . '/view.php', array('id' => $cminfo->id));
    }

    /**
     * Returns an activity instance. Internally uses the class component to know which activity module should be retrieved.
     *
     * @param int $instanceid
     * @return stdClass
     */
    protected function get_activity($instanceid) {
        global $DB;

        if (empty($this->activitiesdata[$this->get_module_name()][$instanceid])) {
            $this->activitiesdata[$this->get_module_name()][$instanceid] = $DB->get_record($this->get_module_name(),
                array('id' => $instanceid), '*', MUST_EXIST);
        }
        return $this->activitiesdata[$this->get_module_name()][$instanceid];

    }

    /**
     * Return the context info required to index files for
     * this search area.
     *
     * Should be onerridden by each search area.
     *
     * @return array
     */
    public function get_search_fileareas() {
        $fileareas = array(
                'intro' // Fileareas.
        );

        return $fileareas;
    }

    /**
     * Files related to the current document are attached,
     * to the document object ready for indexing by
     * Global Search.
     *
     * The default implementation retrieves all files for
     * the file areas returned by get_search_fileareas().
     * If you need to filter files to specific items per
     * file area, you will need to override this method
     * and explicitly provide the items.
     *
     * @param document $document The current document
     * @return void
     */
    public function attach_files($document) {
        $fileareas = $this->get_search_fileareas();

        if (!empty($fileareas)) {
            $cm = $this->get_cm($this->get_module_name(), $document->get('itemid'), $document->get('courseid'));

            $context = \context_module::instance($cm->id);
            $contextid = $context->id;

            $fs = get_file_storage();
            $files = $fs->get_area_files($contextid, $this->get_component_name(), $fileareas, false, '', false);

            foreach ($files as $file) {
                $document->add_stored_file($file);
            }
        }

        return;
    }
}
