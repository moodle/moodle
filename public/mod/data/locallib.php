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
 * @package   mod_data
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/data/lib.php');
require_once($CFG->libdir . '/portfolio/caller.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * The class to handle entry exports of a database module
 */
class data_portfolio_caller extends portfolio_module_caller_base {

    /** @var int the single record to export */
    protected $recordid;

    /** @var object the record from the data table */
    private $data;

    /**#@+ @var array the fields used and their fieldtypes */
    private $fields;
    private $fieldtypes;

    /** @var object the records to export */
    private $records;

    /** @var int how many records are 'mine' */
    private $minecount;

    /**
     * the required callback arguments for a single-record export
     *
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'id'       => true,
            'recordid' => false,
        );
    }

    /**
     * @param array $callbackargs the arguments passed through
     */
    public function __construct($callbackargs) {
        parent::__construct($callbackargs);
    }

    /**
     * load up the data needed for the export
     *
     * @global object $DB
     */
    public function load_data() {
        global $DB, $USER;
        if (!$this->cm = get_coursemodule_from_id('data', $this->id)) {
            throw new portfolio_caller_exception('invalidid', 'data');
        }
        if (!$this->data = $DB->get_record('data', array('id' => $this->cm->instance))) {
            throw new portfolio_caller_exception('invalidid', 'data');
        }
        $fieldrecords = $DB->get_records('data_fields', array('dataid' => $this->cm->instance), 'id');
        // populate objets for this databases fields
        $this->fields = array();
        foreach ($fieldrecords as $fieldrecord) {
            $tmp = data_get_field($fieldrecord, $this->data);
            $this->fields[] = $tmp;
            $this->fieldtypes[]  = $tmp->type;
        }

        $this->records = array();
        if ($this->recordid) {
            $tmp = $DB->get_record('data_records', array('id' => $this->recordid));
            $tmp->content = $DB->get_records('data_content', array('recordid' => $this->recordid));
            $this->records[] = $tmp;
        } else {
            $where = array('dataid' => $this->data->id);
            if (!has_capability('mod/data:exportallentries', context_module::instance($this->cm->id))) {
                $where['userid'] = $USER->id; // get them all in case, we'll unset ones that aren't ours later if necessary
            }
            $tmp = $DB->get_records('data_records', $where);
            foreach ($tmp as $t) {
                $t->content = $DB->get_records('data_content', array('recordid' => $t->id));
                $this->records[] = $t;
            }
            $this->minecount = $DB->count_records('data_records', array('dataid' => $this->data->id, 'userid' => $USER->id));
        }

        if ($this->recordid) {
            list($formats, $files) = self::formats($this->fields, $this->records[0]);
            $this->set_file_and_format_data($files);
        }
    }

    /**
     * How long we think the export will take
     * Single entry is probably not too long.
     * But we check for filesizes
     * Else base it on the number of records
     *
     * @return one of PORTFOLIO_TIME_XX constants
     */
    public function expected_time() {
        if ($this->recordid) {
            return $this->expected_time_file();
        } else {
            return portfolio_expected_time_db(count($this->records));
        }
    }

    /**
     * Calculate the shal1 of this export
     * Dependent on the export format.
     * @return string
     */
    public function get_sha1() {
        // in the case that we're exporting a subclass of 'file' and we have a singlefile,
        // then we're not exporting any metadata, just the file by itself by mimetype.
        if ($this->exporter->get('format') instanceof portfolio_format_file && $this->singlefile) {
            return $this->get_sha1_file();
        }
        // otherwise we're exporting some sort of multipart content so use the data
        $str = '';
        foreach ($this->records as $record) {
            foreach ($record as $data) {
                if (is_array($data) || is_object($data)) {
                    $keys = array_keys($data);
                    $testkey = array_pop($keys);
                    if (is_array($data[$testkey]) || is_object($data[$testkey])) {
                        foreach ($data as $d) {
                            $str .= implode(',', (array)$d);
                        }
                    } else {
                        $str .= implode(',', (array)$data);
                    }
                } else {
                    $str .= $data;
                }
            }
        }
        return sha1($str . ',' . $this->exporter->get('formatclass'));
    }

    /**
     * Prepare the package for export
     *
     * @return stored_file object
     */
    public function prepare_package() {
        global $DB;
        $leapwriter = null;
        $content = '';
        $filename = '';
        $uid = $this->exporter->get('user')->id;
        $users = array(); //cache
        $onlymine = $this->get_export_config('mineonly');
        if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
            $leapwriter = $this->exporter->get('format')->leap2a_writer();
            $ids = array();
        }

        if ($this->exporter->get('format') instanceof portfolio_format_file && $this->singlefile) {
            return $this->get('exporter')->copy_existing_file($this->singlefile);
        }
        foreach ($this->records  as $key => $record) {
            if ($onlymine && $record->userid != $uid) {
                unset($this->records[$key]); // sha1
                continue;
            }
            list($tmpcontent, $files)  = $this->exportentry($record);
            $content .= $tmpcontent;
            if ($leapwriter) {
                $entry = new portfolio_format_leap2a_entry('dataentry' . $record->id, $this->data->name, 'resource', $tmpcontent);
                $entry->published = $record->timecreated;
                $entry->updated = $record->timemodified;
                if ($record->userid != $uid) {
                    if (!array_key_exists($record->userid, $users)) {
                        $users[$record->userid] = $DB->get_record('user', array('id' => $record->userid), 'id,firstname,lastname');
                    }
                    $entry->author = $users[$record->userid];
                }
                $ids[] = $entry->id;
                $leapwriter->link_files($entry, $files, 'dataentry' . $record->id . 'file');
                $leapwriter->add_entry($entry);
            }
        }
        if ($leapwriter) {
            if (count($this->records) > 1) { // make a selection element to tie them all together
                $selection = new portfolio_format_leap2a_entry('datadb' . $this->data->id,
                    get_string('entries', 'data') . ': ' . $this->data->name, 'selection');
                $leapwriter->add_entry($selection);
                $leapwriter->make_selection($selection, $ids, 'Grouping');
            }
            $filename = $this->exporter->get('format')->manifest_name();
            $content = $leapwriter->to_xml();
        } else {
            if (count($this->records) == 1) {
                $filename = clean_filename($this->cm->name . '-entry.html');
            } else {
                $filename = clean_filename($this->cm->name . '-full.html');
            }
        }
        return $this->exporter->write_new_file(
            $content,
            $filename,
            ($this->exporter->get('format') instanceof PORTFOLIO_FORMAT_RICH) // if we have associate files, this is a 'manifest'
        );
    }

    /**
     * Verify the user can still export this entry
     *
     * @return bool
     */
    public function check_permissions() {
        if ($this->recordid) {
            if (data_isowner($this->recordid)) {
                return has_capability('mod/data:exportownentry', context_module::instance($this->cm->id));
            }
            return has_capability('mod/data:exportentry', context_module::instance($this->cm->id));
        }
        if ($this->has_export_config() && !$this->get_export_config('mineonly')) {
            return has_capability('mod/data:exportallentries', context_module::instance($this->cm->id));
        }
        return has_capability('mod/data:exportownentry', context_module::instance($this->cm->id));
    }

    /**
     *  @return string
     */
    public static function display_name() {
        return get_string('modulename', 'data');
    }

    /**
     * @global object
     * @return bool|void
     */
    public function __wakeup() {
        global $CFG;
        if (empty($CFG)) {
            return true; // too early yet
        }
        foreach ($this->fieldtypes as $key => $field) {
            $filepath = $CFG->dirroot . '/mod/data/field/' . $field .'/field.class.php';
            if (!file_exists($filepath)) {
                continue;
            }
            require_once($filepath);
            $this->fields[$key] = unserialize(serialize($this->fields[$key]));
        }
    }

    /**
     * Prepare a single entry for export, replacing all the content etc
     *
     * @param stdclass $record the entry to export
     *
     * @return array with key 0 = the html content, key 1 = array of attachments
     */
    private function exportentry($record) {
    // Replacing tags
        $patterns = array();
        $replacement = array();

        $files = array();
    // Then we generate strings to replace for normal tags
        $format = $this->get('exporter')->get('format');
        foreach ($this->fields as $field) {
            $patterns[]='[['.$field->field->name.']]';
            if (is_callable(array($field, 'get_file'))) {
                if (!$file = $field->get_file($record->id)) {
                    $replacement[] = '';
                    continue; // probably left empty
                }
                $replacement[] = $format->file_output($file);
                $this->get('exporter')->copy_existing_file($file);
                $files[] = $file;
            } else {
                $replacement[] = $field->display_browse_field($record->id, 'singletemplate');
            }
        }

    // Replacing special tags (##Edit##, ##Delete##, ##More##)
        $patterns[]='##edit##';
        $patterns[]='##delete##';
        $patterns[]='##export##';
        $patterns[]='##more##';
        $patterns[]='##moreurl##';
        $patterns[]='##user##';
        $patterns[]='##approve##';
        $patterns[]='##disapprove##';
        $patterns[]='##comments##';
        $patterns[] = '##timeadded##';
        $patterns[] = '##timemodified##';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = '';
        $replacement[] = userdate($record->timecreated);
        $replacement[] = userdate($record->timemodified);

        if (empty($this->data->singletemplate)) {
            // Use default template if the template is not created.
            $this->data->singletemplate = data_generate_default_template($this->data, 'singletemplate', 0, false, false);
        }

        // actual replacement of the tags
        return array(str_ireplace($patterns, $replacement, $this->data->singletemplate), $files);
    }

    /**
     * Given the fields being exported, and the single record,
     * work out which export format(s) we can use
     *
     * @param array $fields array of field objects
     * @param object $record The data record object
     *
     * @uses PORTFOLIO_FORMAT_PLAINHTML
     * @uses PORTFOLIO_FORMAT_RICHHTML
     *
     * @return array of PORTFOLIO_XX constants
     */
    public static function formats($fields, $record) {
        $formats = array(PORTFOLIO_FORMAT_PLAINHTML);
        $includedfiles = array();
        foreach ($fields as $singlefield) {
            if (is_callable(array($singlefield, 'get_file'))) {
                if ($file = $singlefield->get_file($record->id)) {
                    $includedfiles[] = $file;
                }
            }
        }
        if (count($includedfiles) == 1 && count($fields) == 1) {
            $formats = array(portfolio_format_from_mimetype($includedfiles[0]->get_mimetype()));
        } else if (count($includedfiles) > 0) {
            $formats = array(PORTFOLIO_FORMAT_RICHHTML);
        }
        return array($formats, $includedfiles);
    }

    public static function has_files($data) {
        global $DB;
        $fieldrecords = $DB->get_records('data_fields', array('dataid' => $data->id), 'id');
        // populate objets for this databases fields
        foreach ($fieldrecords as $fieldrecord) {
            $field = data_get_field($fieldrecord, $data);
            if (is_callable(array($field, 'get_file'))) {
                return true;
            }
        }
        return false;
    }

    /**
     * base supported formats before we know anything about the export
     */
    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_RICHHTML, PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_LEAP2A);
    }

    public function has_export_config() {
        // if we're exporting more than just a single entry,
        // and we have the capability to export all entries,
        // then ask whether we want just our own, or all of them
        return (empty($this->recordid) // multi-entry export
            && $this->minecount > 0    // some of them are mine
            && $this->minecount != count($this->records) // not all of them are mine
            && has_capability('mod/data:exportallentries', context_module::instance($this->cm->id))); // they actually have a choice in the matter
    }

    public function export_config_form(&$mform, $instance) {
        if (!$this->has_export_config()) {
            return;
        }
        $mform->addElement('selectyesno', 'mineonly', get_string('exportownentries', 'data', (object)array('mine' => $this->minecount, 'all' => count($this->records))));
        $mform->setDefault('mineonly', 1);
    }

    public function get_allowed_export_config() {
        return array('mineonly');
    }
}


/**
 * Class representing the virtual node with all itemids in the file browser
 *
 * @category  files
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_file_info_container extends file_info {
    /** @var file_browser */
    protected $browser;
    /** @var stdClass */
    protected $course;
    /** @var stdClass */
    protected $cm;
    /** @var string */
    protected $component;
    /** @var stdClass */
    protected $context;
    /** @var array */
    protected $areas;
    /** @var string */
    protected $filearea;

    /**
     * Constructor (in case you did not realize it ;-)
     *
     * @param file_browser $browser
     * @param stdClass $course
     * @param stdClass $cm
     * @param stdClass $context
     * @param array $areas
     * @param string $filearea
     */
    public function __construct($browser, $course, $cm, $context, $areas, $filearea) {
        parent::__construct($browser, $context);
        $this->browser = $browser;
        $this->course = $course;
        $this->cm = $cm;
        $this->component = 'mod_data';
        $this->context = $context;
        $this->areas = $areas;
        $this->filearea = $filearea;
    }

    /**
     * @return array with keys contextid, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array(
            'contextid' => $this->context->id,
            'component' => $this->component,
            'filearea' => $this->filearea,
            'itemid' => null,
            'filepath' => null,
            'filename' => null,
        );
    }

    /**
     * Can new files or directories be added via the file browser
     *
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Should this node be considered as a folder in the file browser
     *
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns localised visible name of this node
     *
     * @return string
     */
    public function get_visible_name() {
        return $this->areas[$this->filearea];
    }

    /**
     * Returns list of children nodes
     *
     * @return array of file_info instances
     */
    public function get_children() {
        return $this->get_filtered_children('*', false, true);
    }

    /**
     * Help function to return files matching extensions or their count
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @param bool|int $countonly if false returns the children, if an int returns just the
     *    count of children but stops counting when $countonly number of children is reached
     * @param bool $returnemptyfolders if true returns items that don't have matching files inside
     * @return array|int array of file_info instances or the count
     */
    private function get_filtered_children($extensions = '*', $countonly = false, $returnemptyfolders = false) {
        global $DB;
        $params = array('contextid' => $this->context->id,
            'component' => $this->component,
            'filearea' => $this->filearea);
        $sql = 'SELECT DISTINCT itemid
                    FROM {files}
                    WHERE contextid = :contextid
                    AND component = :component
                    AND filearea = :filearea';
        if (!$returnemptyfolders) {
            $sql .= ' AND filename <> :emptyfilename';
            $params['emptyfilename'] = '.';
        }
        list($sql2, $params2) = $this->build_search_files_sql($extensions);
        $sql .= ' '.$sql2;
        $params = array_merge($params, $params2);
        if ($countonly === false) {
            $sql .= ' ORDER BY itemid DESC';
        }

        $rs = $DB->get_recordset_sql($sql, $params);
        $children = array();
        foreach ($rs as $record) {
            if ($child = $this->browser->get_file_info($this->context, 'mod_data', $this->filearea, $record->itemid)) {
                $children[] = $child;
            }
            if ($countonly !== false && count($children) >= $countonly) {
                break;
            }
        }
        $rs->close();
        if ($countonly !== false) {
            return count($children);
        }
        return $children;
    }

    /**
     * Returns list of children which are either files matching the specified extensions
     * or folders that contain at least one such file.
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        return $this->get_filtered_children($extensions, false);
    }

    /**
     * Returns the number of children which are either files matching the specified extensions
     * or folders containing at least one such file.
     *
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @param int $limit stop counting after at least $limit non-empty children are found
     * @return int
     */
    public function count_non_empty_children($extensions = '*', $limit = 1) {
        return $this->get_filtered_children($extensions, $limit);
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info($this->context);
    }
}

/**
 * This creates new calendar events given as timeavailablefrom and timeclose by $data.
 *
 * @param stdClass $data
 * @return void
 */
function data_set_events($data) {
    global $DB, $CFG;

    require_once($CFG->dirroot.'/calendar/lib.php');

    // Get CMID if not sent as part of $data.
    if (!isset($data->coursemodule)) {
        $cm = get_coursemodule_from_instance('data', $data->id, $data->course);
        $data->coursemodule = $cm->id;
    }
    // Data start calendar events.
    $event = new stdClass();
    $event->eventtype = DATA_EVENT_TYPE_OPEN;
    // The DATA_EVENT_TYPE_OPEN event should only be an action event if no close time was specified.
    $event->type = empty($data->timeavailableto) ? CALENDAR_EVENT_TYPE_ACTION : CALENDAR_EVENT_TYPE_STANDARD;
    if ($event->id = $DB->get_field('event', 'id',
            array('modulename' => 'data', 'instance' => $data->id, 'eventtype' => $event->eventtype))) {
        if ($data->timeavailablefrom > 0) {
            // Calendar event exists so update it.
            $event->name         = get_string('calendarstart', 'data', $data->name);
            $event->description  = format_module_intro('data', $data, $data->coursemodule, false);
            $event->format       = FORMAT_HTML;
            $event->timestart    = $data->timeavailablefrom;
            $event->timesort     = $data->timeavailablefrom;
            $event->visible      = instance_is_visible('data', $data);
            $event->timeduration = 0;
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event, false);
        } else {
            // Calendar event is on longer needed.
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->delete();
        }
    } else {
        // Event doesn't exist so create one.
        if (isset($data->timeavailablefrom) && $data->timeavailablefrom > 0) {
            $event->name         = get_string('calendarstart', 'data', $data->name);
            $event->description  = format_module_intro('data', $data, $data->coursemodule, false);
            $event->format       = FORMAT_HTML;
            $event->courseid     = $data->course;
            $event->groupid      = 0;
            $event->userid       = 0;
            $event->modulename   = 'data';
            $event->instance     = $data->id;
            $event->timestart    = $data->timeavailablefrom;
            $event->timesort     = $data->timeavailablefrom;
            $event->visible      = instance_is_visible('data', $data);
            $event->timeduration = 0;
            calendar_event::create($event, false);
        }
    }

    // Data end calendar events.
    $event = new stdClass();
    $event->type = CALENDAR_EVENT_TYPE_ACTION;
    $event->eventtype = DATA_EVENT_TYPE_CLOSE;
    if ($event->id = $DB->get_field('event', 'id',
            array('modulename' => 'data', 'instance' => $data->id, 'eventtype' => $event->eventtype))) {
        if ($data->timeavailableto > 0) {
            // Calendar event exists so update it.
            $event->name         = get_string('calendarend', 'data', $data->name);
            $event->description  = format_module_intro('data', $data, $data->coursemodule, false);
            $event->format       = FORMAT_HTML;
            $event->timestart    = $data->timeavailableto;
            $event->timesort     = $data->timeavailableto;
            $event->visible      = instance_is_visible('data', $data);
            $event->timeduration = 0;
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event, false);
        } else {
            // Calendar event is on longer needed.
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->delete();
        }
    } else {
        // Event doesn't exist so create one.
        if (isset($data->timeavailableto) && $data->timeavailableto > 0) {
            $event->name         = get_string('calendarend', 'data', $data->name);
            $event->description  = format_module_intro('data', $data, $data->coursemodule, false);
            $event->format       = FORMAT_HTML;
            $event->courseid     = $data->course;
            $event->groupid      = 0;
            $event->userid       = 0;
            $event->modulename   = 'data';
            $event->instance     = $data->id;
            $event->timestart    = $data->timeavailableto;
            $event->timesort     = $data->timeavailableto;
            $event->visible      = instance_is_visible('data', $data);
            $event->timeduration = 0;
            calendar_event::create($event, false);
        }
    }
}

/**
 * Check if a database is available for the current user.
 *
 * @param  stdClass  $data            database record
 * @param  boolean $canmanageentries  optional, if the user can manage entries
 * @param  stdClass  $context         Module context, required if $canmanageentries is not set
 * @return array                      status (available or not and possible warnings)
 * @since  Moodle 3.3
 */
function data_get_time_availability_status($data, $canmanageentries = null, $context = null) {
    $open = true;
    $closed = false;
    $warnings = array();

    if ($canmanageentries === null) {
        $canmanageentries = has_capability('mod/data:manageentries', $context);
    }

    if (!$canmanageentries) {
        $timenow = time();

        if (!empty($data->timeavailablefrom) and $data->timeavailablefrom > $timenow) {
            $open = false;
        }
        if (!empty($data->timeavailableto) and $timenow > $data->timeavailableto) {
            $closed = true;
        }

        if (!$open or $closed) {
            if (!$open) {
                $warnings['notopenyet'] = userdate($data->timeavailablefrom);
            }
            if ($closed) {
                $warnings['expired'] = userdate($data->timeavailableto);
            }
            return array(false, $warnings);
        }
    }

    // Database is available.
    return array(true, $warnings);
}

/**
 * Requires a database to be available for the current user.
 *
 * @param  stdClass  $data            database record
 * @param  boolean $canmanageentries  optional, if the user can manage entries
 * @param  stdClass  $context          Module context, required if $canmanageentries is not set
 * @throws moodle_exception
 * @since  Moodle 3.3
 */
function data_require_time_available($data, $canmanageentries = null, $context = null) {

    list($available, $warnings) = data_get_time_availability_status($data, $canmanageentries, $context);

    if (!$available) {
        $reason = current(array_keys($warnings));
        throw new moodle_exception($reason, 'data', '', $warnings[$reason]);
    }
}

/**
 * Return the number of entries left to add to complete the activity.
 *
 * @param  stdClass $data           database object
 * @param  int $numentries          the number of entries the current user has created
 * @param  bool $canmanageentries   whether the user can manage entries (teachers, managers)
 * @return int the number of entries left, 0 if no entries left or if is not required
 * @since  Moodle 3.3
 */
function data_get_entries_left_to_add($data, $numentries, $canmanageentries) {
    if ($data->requiredentries > 0 && $numentries < $data->requiredentries && !$canmanageentries) {
        return $data->requiredentries - $numentries;
    }
    return 0;
}

/**
 * Return the number of entires left to add to view other users entries..
 *
 * @param  stdClass $data           database object
 * @param  int $numentries          the number of entries the current user has created
 * @param  bool $canmanageentries   whether the user can manage entries (teachers, managers)
 * @return int the number of entries left, 0 if no entries left or if is not required
 * @since  Moodle 3.3
 */
function data_get_entries_left_to_view($data, $numentries, $canmanageentries) {
    if ($data->requiredentriestoview > 0 && $numentries < $data->requiredentriestoview && !$canmanageentries) {
        return $data->requiredentriestoview - $numentries;
    }
    return 0;
}

/**
 * Returns data records tagged with a specified tag.
 *
 * This is a callback used by the tag area mod_data/data_records to search for data records
 * tagged with a specific tag.
 *
 * @param core_tag_tag $tag
 * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
 *             are displayed on the page and the per-page limit may be bigger
 * @param int $fromctx context id where the link was displayed, may be used by callbacks
 *            to display items in the same context first
 * @param int $ctx context id where to search for records
 * @param bool $rec search in subcontexts as well
 * @param int $page 0-based number of page being displayed
 * @return \core_tag\output\tagindex
 */
function mod_data_get_tagged_records($tag, $exclusivemode = false, $fromctx = 0, $ctx = 0, $rec = true, $page = 0) {
    global $DB, $OUTPUT, $USER;
    $perpage = $exclusivemode ? 20 : 5;

    // Build the SQL query.
    $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
    $query = "SELECT dr.id, dr.dataid, dr.approved, d.timeviewfrom, d.timeviewto, dr.groupid, d.approval, dr.userid,
                     d.requiredentriestoview, cm.id AS cmid, c.id AS courseid, c.shortname, c.fullname, $ctxselect
                FROM {data_records} dr
                JOIN {data} d
                  ON d.id = dr.dataid
                JOIN {modules} m
                  ON m.name = 'data'
                JOIN {course_modules} cm
                  ON cm.module = m.id AND cm.instance = d.id
                JOIN {tag_instance} tt
                  ON dr.id = tt.itemid
                JOIN {course} c
                  ON cm.course = c.id
                JOIN {context} ctx
                  ON ctx.instanceid = cm.id AND ctx.contextlevel = :coursemodulecontextlevel
               WHERE tt.itemtype = :itemtype
                 AND tt.tagid = :tagid
                 AND tt.component = :component
                 AND cm.deletioninprogress = 0
                 AND dr.id %ITEMFILTER%
                 AND c.id %COURSEFILTER%";

    $params = array(
        'itemtype' => 'data_records',
        'tagid' => $tag->id,
        'component' => 'mod_data',
        'coursemodulecontextlevel' => CONTEXT_MODULE
    );

    if ($ctx) {
        $context = $ctx ? context::instance_by_id($ctx) : context_system::instance();
        $query .= $rec ? ' AND (ctx.id = :contextid OR ctx.path LIKE :path)' : ' AND ctx.id = :contextid';
        $params['contextid'] = $context->id;
        $params['path'] = $context->path . '/%';
    }

    $query .= " ORDER BY ";
    if ($fromctx) {
        // In order-clause specify that modules from inside "fromctx" context should be returned first.
        $fromcontext = context::instance_by_id($fromctx);
        $query .= ' (CASE WHEN ctx.id = :fromcontextid OR ctx.path LIKE :frompath THEN 0 ELSE 1 END),';
        $params['fromcontextid'] = $fromcontext->id;
        $params['frompath'] = $fromcontext->path . '/%';
    }
    $query .= ' c.sortorder, cm.id, dr.id';

    $totalpages = $page + 1;

    // Use core_tag_index_builder to build and filter the list of items.
    $builder = new core_tag_index_builder('mod_data', 'data_records', $query, $params, $page * $perpage, $perpage + 1);
    $now = time();
    $entrycount = [];
    $activitygroupmode = [];
    $usergroups = [];
    $titlefields = [];
    while ($item = $builder->has_item_that_needs_access_check()) {
        context_helper::preload_from_record($item);
        $modinfo = get_fast_modinfo($item->courseid);
        $cm = $modinfo->get_cm($item->cmid);
        $context = \context_module::instance($cm->id);
        $courseid = $item->courseid;

        if (!$builder->can_access_course($courseid)) {
            $builder->set_accessible($item, false);
            continue;
        }

        if (!$cm->uservisible) {
            $builder->set_accessible($item, false);
            continue;
        }

        if (!has_capability('mod/data:viewentry', $context)) {
            $builder->set_accessible($item, false);
            continue;
        }

        if ($USER->id != $item->userid && (($item->timeviewfrom && $now < $item->timeviewfrom)
                || ($item->timeviewto && $now > $item->timeviewto))) {
            $builder->set_accessible($item, false);
            continue;
        }

        if ($USER->id != $item->userid && $item->approval && !$item->approved) {
            $builder->set_accessible($item, false);
            continue;
        }

        if ($item->requiredentriestoview) {
            if (!isset($entrycount[$item->dataid])) {
                $entrycount[$item->dataid] = $DB->count_records('data_records', array('dataid' => $item->dataid));
            }
            $sufficiententries = $item->requiredentriestoview > $entrycount[$item->dataid];
            $builder->set_accessible($item, $sufficiententries);
        }

        if (!isset($activitygroupmode[$cm->id])) {
            $activitygroupmode[$cm->id] = groups_get_activity_groupmode($cm);
        }

        if (!isset($usergroups[$item->groupid])) {
            $usergroups[$item->groupid] = groups_is_member($item->groupid, $USER->id);
        }

        if ($activitygroupmode[$cm->id] == SEPARATEGROUPS && !$usergroups[$item->groupid]) {
            $builder->set_accessible($item, false);
            continue;
        }

        $builder->set_accessible($item, true);
    }

    $items = $builder->get_items();
    if (count($items) > $perpage) {
        $totalpages = $page + 2; // We don't need exact page count, just indicate that the next page exists.
        array_pop($items);
    }

    // Build the display contents.
    if ($items) {
        $tagfeed = new core_tag\output\tagfeed();
        foreach ($items as $item) {
            context_helper::preload_from_record($item);
            $modinfo = get_fast_modinfo($item->courseid);
            $cm = $modinfo->get_cm($item->cmid);
            $pageurl = new moodle_url('/mod/data/view.php', array(
                    'rid' => $item->id,
                    'd' => $item->dataid
            ));

            if (!isset($titlefields[$item->dataid])) {
                $titlefields[$item->dataid] = data_get_tag_title_field($item->dataid);
            }

            $pagename = data_get_tag_title_for_entry($titlefields[$item->dataid], $item);
            $pagename = html_writer::link($pageurl, $pagename);
            $courseurl = course_get_url($item->courseid, $cm->sectionnum);
            $cmname = html_writer::link($cm->url, $cm->get_formatted_name());
            $coursename = format_string($item->fullname, true, array('context' => context_course::instance($item->courseid)));
            $coursename = html_writer::link($courseurl, $coursename);
            $icon = html_writer::link($pageurl, html_writer::empty_tag('img', array('src' => $cm->get_icon_url())));
            $tagfeed->add($icon, $pagename, $cmname . '<br>' . $coursename);
        }
        $content = $OUTPUT->render_from_template('core_tag/tagfeed', $tagfeed->export_for_template($OUTPUT));

        return new core_tag\output\tagindex($tag, 'mod_data', 'data_records', $content, $exclusivemode,
            $fromctx, $ctx, $rec, $page, $totalpages);
    }
}

/**
 * Get the title of a field to show when displaying tag results.
 *
 * @param int $dataid The id of the data field
 * @return stdClass The field data from the 'data_fields' table as well as it's priority
 */
function data_get_tag_title_field($dataid) {
    global $DB, $CFG;

    $validfieldtypes = array('text', 'textarea', 'menu', 'radiobutton', 'checkbox', 'multimenu', 'url');
    $fields = $DB->get_records('data_fields', ['dataid' => $dataid]);
    $template = $DB->get_field('data', 'addtemplate', ['id' => $dataid]);
    if (empty($template)) {
        $data = $DB->get_record('data', ['id' => $dataid]);
        $template = data_generate_default_template($data, 'addtemplate', 0, false, false);
    }

    $filteredfields = [];

    foreach ($fields as $field) {
        if (!in_array($field->type, $validfieldtypes)) {
            continue;
        }
        $field->addtemplateposition = strpos($template, '[['.$field->name.']]');
        if ($field->addtemplateposition === false) {
            continue;
        }
        $field->type = clean_param($field->type, PARAM_ALPHA);
        $filepath = $CFG->dirroot . '/mod/data/field/' . $field->type . '/field.class.php';
        if (!file_exists($filepath)) {
            continue;
        }
        require_once($filepath);
        $classname = 'data_field_' . $field->type;
        $field->priority = $classname::get_priority();
        $filteredfields[] = $field;
    }

    $sort = function($record1, $record2) {
        // If a content's fieldtype is compulsory in the database than it would have priority than any other non-compulsory content.
        if (($record1->required && $record2->required) || (!$record1->required && !$record2->required)) {
            if ($record1->priority === $record2->priority) {
                return $record1->id < $record2->id ? 1 : -1;
            }

            return $record1->priority < $record2->priority ? -1 : 1;
        } else if ($record1->required && !$record2->required) {
            return 1;
        } else {
            return -1;
        }
    };

    usort($filteredfields, $sort);

    return array_shift($filteredfields);
}

/**
 * Get the title of an entry to show when displaying tag results.
 *
 * @param stdClass $field The field from the 'data_fields' table
 * @param stdClass $entry The entry from the 'data_records' table
 * @return string|null It will return the title of the entry or null if the field type is not available.
 */
function data_get_tag_title_for_entry($field, $entry) {
    global $CFG, $DB;

    if (!isset($field->type)) {
        return null;
    }
    $field->type = clean_param($field->type, PARAM_ALPHA);
    $filepath = $CFG->dirroot . '/mod/data/field/' . $field->type . '/field.class.php';
    if (!file_exists($filepath)) {
        return null;
    }
    require_once($filepath);

    $classname = 'data_field_' . $field->type;
    $sql = "SELECT dc.*
              FROM {data_content} dc
        INNER JOIN {data_fields} df
                ON dc.fieldid = df.id
             WHERE df.id = :fieldid
               AND dc.recordid = :recordid";
    $fieldcontents = $DB->get_record_sql($sql, array('recordid' => $entry->id, 'fieldid' => $field->id));

    return $classname::get_content_value($fieldcontents);
}

/**
 * Search entries in a database.
 *
 * @param  stdClass  $data         database object
 * @param  stdClass  $cm           course module object
 * @param  stdClass  $context      context object
 * @param  stdClass  $mode         in which mode we are viewing the database (list, single)
 * @param  int  $currentgroup      the current group being used
 * @param  str  $search            search for this text in the entry data
 * @param  str  $sort              the field to sort by
 * @param  str  $order             the order to use when sorting
 * @param  int $page               for pagination, the current page
 * @param  int $perpage            entries per page
 * @param  bool  $advanced         whether we are using or not advanced search
 * @param  array  $searcharray     when using advanced search, the advanced data to use
 * @param  stdClass  $record       if we jsut want this record after doing all the access checks
 * @return array the entries found among other data related to the search
 * @since  Moodle 3.3
 */
function data_search_entries($data, $cm, $context, $mode, $currentgroup, $search = '', $sort = null, $order = null, $page = 0,
        $perpage = 0, $advanced = null, $searcharray = null, $record = null) {
    global $DB, $USER;

    if ($sort === null) {
        $sort = $data->defaultsort;
    }
    if ($order === null) {
        $order = ($data->defaultsortdir == 0) ? 'ASC' : 'DESC';
    }
    if ($searcharray === null) {
        $searcharray = array();
    }

    if (core_text::strlen($search) < 2) {
        $search = '';
    }

    $approvecap = has_capability('mod/data:approve', $context);
    $canmanageentries = has_capability('mod/data:manageentries', $context);

    // If a student is not part of a group and seperate groups is enabled, we don't
    // want them seeing all records.
    $groupmode = groups_get_activity_groupmode($cm);
    $canviewallrecords = $groupmode != SEPARATEGROUPS || has_capability('moodle/site:accessallgroups', $context);

    $numentries = data_numentries($data);
    $requiredentriesallowed = true;
    if (data_get_entries_left_to_view($data, $numentries, $canmanageentries)) {
        $requiredentriesallowed = false;
    }

    // Initialise the first group of params for advanced searches.
    $initialparams   = array();
    $params = array(); // Named params array.

    // Setup group and approve restrictions.
    if (!$approvecap && $data->approval) {
        if (isloggedin()) {
            $approveselect = ' AND (r.approved=1 OR r.userid=:myid1) ';
            $params['myid1'] = $USER->id;
            $initialparams['myid1'] = $params['myid1'];
        } else {
            $approveselect = ' AND r.approved=1 ';
        }
    } else {
        $approveselect = ' ';
    }

    if ($currentgroup) {
        $groupselect = " AND (r.groupid = :currentgroup OR r.groupid = 0)";
        $params['currentgroup'] = $currentgroup;
        $initialparams['currentgroup'] = $params['currentgroup'];
    } else {
        if ($canviewallrecords) {
            $groupselect = ' ';
        } else {
            // If separate groups are enabled and the user isn't in a group or
            // a teacher, manager, admin etc, then just show them entries for 'All participants'.
            $groupselect = " AND r.groupid = 0";
        }
    }

    // Init some variables to be used by advanced search.
    $advsearchselect = '';
    $advwhere        = '';
    $advtables       = '';
    $advparams       = array();
    // This is used for the initial reduction of advanced search results with required entries.
    $entrysql        = '';
    $userfieldsapi = \core_user\fields::for_userpic()->excluding('id');
    $namefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;

    // Find the field we are sorting on.
    if ($sort <= 0 || !($sortfield = data_get_field_from_id($sort, $data))) {

        switch ($sort) {
            case DATA_LASTNAME:
                $ordering = "u.lastname $order, u.firstname $order";
                break;
            case DATA_FIRSTNAME:
                $ordering = "u.firstname $order, u.lastname $order";
                break;
            case DATA_APPROVED:
                $ordering = "r.approved $order, r.timecreated $order";
                break;
            case DATA_TIMEMODIFIED:
                $ordering = "r.timemodified $order";
                break;
            case DATA_TIMEADDED:
            default:
                $sort     = 0;
                $ordering = "r.timecreated $order";
        }

        $what = ' DISTINCT r.id, r.approved, r.timecreated, r.timemodified, r.userid, r.groupid, r.dataid, ' . $namefields;
        $count = ' COUNT(DISTINCT c.recordid) ';
        $tables = '{data_content} c,{data_records} r, {user} u ';
        $where = 'WHERE c.recordid = r.id
                     AND r.dataid = :dataid
                     AND r.userid = u.id ';
        $params['dataid'] = $data->id;
        $sortorder = " ORDER BY $ordering, r.id $order";
        $searchselect = '';

        // If requiredentries is not reached, only show current user's entries.
        if (!$requiredentriesallowed) {
            $where .= ' AND u.id = :myid2 ';
            $entrysql = ' AND r.userid = :myid3 ';
            $params['myid2'] = $USER->id;
            $initialparams['myid3'] = $params['myid2'];
        }

        if ($search) {
            $searchselect = " AND (".$DB->sql_like('c.content', ':search1', false)."
                              OR ".$DB->sql_like('u.firstname', ':search2', false)."
                              OR ".$DB->sql_like('u.lastname', ':search3', false)." ) ";
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "%$search%";
        } else {
            $searchselect = ' ';
        }

    } else {

        $sortcontent = $DB->sql_compare_text('s.' . $sortfield->get_sort_field());
        $sortcontentfull = $sortfield->get_sort_sql($sortcontent);

        $what = ' DISTINCT r.id, r.approved, r.timecreated, r.timemodified, r.userid, r.groupid, r.dataid, ' . $namefields . ',
                ' . $sortcontentfull . ' AS sortorder ';
        $count = ' COUNT(DISTINCT c.recordid) ';
        $tables = '{data_content} c, {data_records} r, {user} u ';
        $where = 'WHERE c.recordid = r.id
                     AND r.dataid = :dataid
                     AND r.userid = u.id ';
        if (!$advanced) {
            $where .= 'AND s.fieldid = :sort AND s.recordid = r.id';
            $tables .= ',{data_content} s ';
        }
        $params['dataid'] = $data->id;
        $params['sort'] = $sort;
        $sortorder = ' ORDER BY sortorder '.$order.' , r.id ASC ';
        $searchselect = '';

        // If requiredentries is not reached, only show current user's entries.
        if (!$requiredentriesallowed) {
            $where .= ' AND u.id = :myid2';
            $entrysql = ' AND r.userid = :myid3';
            $params['myid2'] = $USER->id;
            $initialparams['myid3'] = $params['myid2'];
        }

        if ($search) {
            $searchselect = " AND (".$DB->sql_like('c.content', ':search1', false)." OR
                ".$DB->sql_like('u.firstname', ':search2', false)." OR
                ".$DB->sql_like('u.lastname', ':search3', false)." ) ";
            $params['search1'] = "%$search%";
            $params['search2'] = "%$search%";
            $params['search3'] = "%$search%";
        } else {
            $searchselect = ' ';
        }
    }

    // To actually fetch the records.

    $fromsql    = "FROM $tables $advtables $where $advwhere $groupselect $approveselect $searchselect $advsearchselect";
    $allparams  = array_merge($params, $advparams);

    // Provide initial sql statements and parameters to reduce the number of total records.
    $initialselect = $groupselect . $approveselect . $entrysql;

    $recordids = data_get_all_recordids($data->id, $initialselect, $initialparams);
    $newrecordids = data_get_advance_search_ids($recordids, $searcharray, $data->id);
    $selectdata = $where . $groupselect . $approveselect;

    if (!empty($advanced)) {
        $advancedsearchsql = data_get_advanced_search_sql($sort, $data, $newrecordids, $selectdata, $sortorder);
        $sqlselect = $advancedsearchsql['sql'];
        $allparams = array_merge($allparams, $advancedsearchsql['params']);
        $totalcount = count($newrecordids);
    } else {
        $sqlselect  = "SELECT $what $fromsql $sortorder";
        $sqlcountselect  = "SELECT $count $fromsql";
        $totalcount = $DB->count_records_sql($sqlcountselect, $allparams);
    }

    // Work out the paging numbers and counts.
    if (empty($searchselect) && empty($advsearchselect)) {
        $maxcount = $totalcount;
    } else {
        $maxcount = count($recordids);
    }

    if ($record) {     // We need to just show one, so where is it in context?
        $nowperpage = 1;
        $mode = 'single';
        $page = 0;
        // TODO MDL-33797 - Reduce this or consider redesigning the paging system.
        if ($allrecordids = $DB->get_fieldset_sql($sqlselect, $allparams)) {
            $page = (int)array_search($record->id, $allrecordids);
            unset($allrecordids);
        }
    } else if ($mode == 'single') {  // We rely on ambient $page settings
        $nowperpage = 1;

    } else {
        $nowperpage = $perpage;
    }

    // Get the actual records.
    if (!$records = $DB->get_records_sql($sqlselect, $allparams, $page * $nowperpage, $nowperpage)) {
        // Nothing to show!
        if ($record) {         // Something was requested so try to show that at least (bug 5132)
            if (data_can_view_record($data, $record, $currentgroup, $canmanageentries)) {
                // OK, we can show this one
                $records = array($record->id => $record);
                $totalcount = 1;
            }
        }

    }

    return [$records, $maxcount, $totalcount, $page, $nowperpage, $sort, $mode];
}

/**
 * Check if the current user can view the given record.
 *
 * @param  stdClass $data           database record
 * @param  stdClass $record         the record (entry) to check
 * @param  int $currentgroup        current group
 * @param  bool $canmanageentries   if the user can manage entries
 * @return bool true if the user can view the entry
 * @since  Moodle 3.3
 */
function data_can_view_record($data, $record, $currentgroup, $canmanageentries) {
    global $USER;

    if ($canmanageentries || empty($data->approval) ||
             $record->approved || (isloggedin() && $record->userid == $USER->id)) {

        if (!$currentgroup || $record->groupid == $currentgroup || $record->groupid == 0) {
            return true;
        }
    }
    return false;
}

/**
 * Return all the field instances for a given database.
 *
 * @param  stdClass $data database object
 * @return array field instances
 * @since  Moodle 3.3
 */
function data_get_field_instances($data) {
    global $DB;

    $instances = [];
    if ($fields = $DB->get_records('data_fields', array('dataid' => $data->id), 'id')) {
        foreach ($fields as $field) {
            $instances[] = data_get_field($field, $data);
        }
    }
    return $instances;
}

/**
 * Build the search array.
 *
 * @param  stdClass $data      the database object
 * @param  bool $paging        if paging is being used
 * @param  array $searcharray  the current search array (saved by session)
 * @param  array $defaults     default values for the searchable fields
 * @param  str $fn             the first name to search (optional)
 * @param  str $ln             the last name to search (optional)
 * @return array               the search array and plain search build based on the different elements
 * @since  Moodle 3.3
 */
function data_build_search_array($data, $paging, $searcharray, $defaults = null, $fn = '', $ln = '') {
    global $DB;

    $search = '';
    $vals = array();
    $fields = $DB->get_records('data_fields', array('dataid' => $data->id));

    if (!empty($fields)) {
        foreach ($fields as $field) {
            $searchfield = data_get_field_from_id($field->id, $data);
            // Get field data to build search sql with.  If paging is false, get from user.
            // If paging is true, get data from $searcharray which is obtained from the $SESSION (see line 116).
            if (!$paging && $searchfield->type != 'unknown') {
                $val = $searchfield->parse_search_field($defaults);
            } else {
                // Set value from session if there is a value @ the required index.
                if (isset($searcharray[$field->id])) {
                    $val = $searcharray[$field->id]->data;
                } else { // If there is not an entry @ the required index, set value to blank.
                    $val = '';
                }
            }
            if (!empty($val)) {
                $searcharray[$field->id] = new stdClass();
                list($searcharray[$field->id]->sql, $searcharray[$field->id]->params) = $searchfield->generate_sql('c'.$field->id, $val);
                $searcharray[$field->id]->data = $val;
                $vals[] = $val;
            } else {
                // Clear it out.
                unset($searcharray[$field->id]);
            }
        }
    }

    $rawtagnames = optional_param_array('tags', false, PARAM_TAGLIST);

    if ($rawtagnames) {
        $searcharray[DATA_TAGS] = new stdClass();
        $searcharray[DATA_TAGS]->params = [];
        $searcharray[DATA_TAGS]->rawtagnames = $rawtagnames;
        $searcharray[DATA_TAGS]->sql = '';
    } else {
        unset($searcharray[DATA_TAGS]);
    }

    if (!$paging) {
        // Name searching.
        $fn = optional_param('u_fn', $fn, PARAM_NOTAGS);
        $ln = optional_param('u_ln', $ln, PARAM_NOTAGS);
    } else {
        $fn = isset($searcharray[DATA_FIRSTNAME]) ? $searcharray[DATA_FIRSTNAME]->data : '';
        $ln = isset($searcharray[DATA_LASTNAME]) ? $searcharray[DATA_LASTNAME]->data : '';
    }
    if (!empty($fn)) {
        $searcharray[DATA_FIRSTNAME] = new stdClass();
        $searcharray[DATA_FIRSTNAME]->sql    = '';
        $searcharray[DATA_FIRSTNAME]->params = array();
        $searcharray[DATA_FIRSTNAME]->field  = 'u.firstname';
        $searcharray[DATA_FIRSTNAME]->data   = $fn;
        $vals[] = $fn;
    } else {
        unset($searcharray[DATA_FIRSTNAME]);
    }
    if (!empty($ln)) {
        $searcharray[DATA_LASTNAME] = new stdClass();
        $searcharray[DATA_LASTNAME]->sql     = '';
        $searcharray[DATA_LASTNAME]->params = array();
        $searcharray[DATA_LASTNAME]->field   = 'u.lastname';
        $searcharray[DATA_LASTNAME]->data    = $ln;
        $vals[] = $ln;
    } else {
        unset($searcharray[DATA_LASTNAME]);
    }

    // In case we want to switch to simple search later - there might be multiple values there ;-).
    if ($vals) {
        $val = reset($vals);
        if (is_string($val)) {
            $search = $val;
        }
    }
    return [$searcharray, $search];
}

/**
 * Approves or unapproves an entry.
 *
 * @param  int $entryid the entry to approve or unapprove.
 * @param  bool $approve Whether to approve or unapprove (true for approve false otherwise).
 * @since  Moodle 3.3
 */
function data_approve_entry($entryid, $approve) {
    global $DB;

    $newrecord = new stdClass();
    $newrecord->id = $entryid;
    $newrecord->approved = $approve ? 1 : 0;
    $DB->update_record('data_records', $newrecord);
}

/**
 * Populate the field contents of a new record with the submitted data.
 * An event has been previously triggered upon the creation of the new record in data_add_record().
 *
 * @param  stdClass $data           database object
 * @param  stdClass $context        context object
 * @param  int $recordid            the new record id
 * @param  array $fields            list of fields of the database
 * @param  stdClass $datarecord     the submitted data
 * @param  stdClass $processeddata  pre-processed submitted fields
 * @since  Moodle 3.3
 */
function data_add_fields_contents_to_new_record($data, $context, $recordid, $fields, $datarecord, $processeddata) {
    global $DB;

    // Insert a whole lot of empty records to make sure we have them.
    $records = array();
    foreach ($fields as $field) {
        $content = new stdClass();
        $content->recordid = $recordid;
        $content->fieldid = $field->id;
        $records[] = $content;
    }

    // Bulk insert the records now. Some records may have no data but all must exist.
    $DB->insert_records('data_content', $records);

    // Add all provided content.
    foreach ($processeddata->fields as $fieldname => $field) {
        $field->update_content($recordid, $datarecord->$fieldname, $fieldname);
    }
}

/**
 * Updates the fields contents of an existing record.
 *
 * @param  stdClass $data           database object
 * @param  stdClass $record         record to update object
 * @param  stdClass $context        context object
 * @param  stdClass $datarecord     the submitted data
 * @param  stdClass $processeddata  pre-processed submitted fields
 * @since  Moodle 3.3
 */
function data_update_record_fields_contents($data, $record, $context, $datarecord, $processeddata) {
    global $DB;

    // Reset the approved flag after edit if the user does not have permission to approve their own entries.
    if (!has_capability('mod/data:approve', $context)) {
        $record->approved = 0;
    }

    // Update the parent record.
    $record->timemodified = time();
    $DB->update_record('data_records', $record);

    // Update all content.
    foreach ($processeddata->fields as $fieldname => $field) {
        $field->update_content($record->id, $datarecord->$fieldname, $fieldname);
    }

    // Trigger an event for updating this record.
    $event = \mod_data\event\record_updated::create(array(
        'objectid' => $record->id,
        'context' => $context,
        'courseid' => $data->course,
        'other' => array(
            'dataid' => $data->id
        )
    ));
    $event->add_record_snapshot('data', $data);
    $event->trigger();
}
