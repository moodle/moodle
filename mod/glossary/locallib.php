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
 * Library of functions and constants for module glossary
 * outside of what is required for the core moodle api
 *
 * @package   mod_glossary
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/portfolio/caller.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * class to handle exporting an entire glossary database
 */
class glossary_full_portfolio_caller extends portfolio_module_caller_base {

    private $glossary;
    private $exportdata;
    private $keyedfiles = array(); // keyed on entry

    /**
     * return array of expected call back arguments
     * and whether they are required or not
     *
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'id' => true,
        );
    }

    /**
     * load up all data required for this export.
     *
     * @return void
     */
    public function load_data() {
        global $DB;
        if (!$this->cm = get_coursemodule_from_id('glossary', $this->id)) {
            throw new portfolio_caller_exception('invalidid', 'glossary');
        }
        if (!$this->glossary = $DB->get_record('glossary', array('id' => $this->cm->instance))) {
            throw new portfolio_caller_exception('invalidid', 'glossary');
        }
        $entries = $DB->get_records('glossary_entries', array('glossaryid' => $this->glossary->id));
        list($where, $params) = $DB->get_in_or_equal(array_keys($entries));

        $aliases = $DB->get_records_select('glossary_alias', 'entryid ' . $where, $params);
        $categoryentries = $DB->get_records_sql('SELECT ec.entryid, c.name FROM {glossary_entries_categories} ec
            JOIN {glossary_categories} c
            ON c.id = ec.categoryid
            WHERE ec.entryid ' . $where, $params);

        $this->exportdata = array('entries' => $entries, 'aliases' => $aliases, 'categoryentries' => $categoryentries);
        $fs = get_file_storage();
        $context = context_module::instance($this->cm->id);
        $this->multifiles = array();
        foreach (array_keys($entries) as $entry) {
            $this->keyedfiles[$entry] = array_merge(
                $fs->get_area_files($context->id, 'mod_glossary', 'attachment', $entry, "timemodified", false),
                $fs->get_area_files($context->id, 'mod_glossary', 'entry', $entry, "timemodified", false)
            );
            $this->multifiles = array_merge($this->multifiles, $this->keyedfiles[$entry]);
        }
    }

    /**
     * how long might we expect this export to take
     *
     * @return constant one of PORTFOLIO_TIME_XX
     */
    public function expected_time() {
        $filetime = portfolio_expected_time_file($this->multifiles);
        $dbtime   = portfolio_expected_time_db(count($this->exportdata['entries']));
        return ($filetime > $dbtime) ? $filetime : $dbtime;
    }

    /**
     * return the sha1 of this content
     *
     * @return string
     */
    public function get_sha1() {
        $file = '';
        if ($this->multifiles) {
            $file = $this->get_sha1_file();
        }
        return sha1(serialize($this->exportdata) . $file);
    }

    /**
     * prepare the package ready to be passed off to the portfolio plugin
     *
     * @return void
     */
    public function prepare_package() {
        $entries = $this->exportdata['entries'];
        $aliases = array();
        $categories = array();
        if (is_array($this->exportdata['aliases'])) {
            foreach ($this->exportdata['aliases'] as $alias) {
                if (!array_key_exists($alias->entryid, $aliases)) {
                    $aliases[$alias->entryid] = array();
                }
                $aliases[$alias->entryid][] = $alias->alias;
            }
        }
        if (is_array($this->exportdata['categoryentries'])) {
            foreach ($this->exportdata['categoryentries'] as $cat) {
                if (!array_key_exists($cat->entryid, $categories)) {
                    $categories[$cat->entryid] = array();
                }
                $categories[$cat->entryid][] = $cat->name;
            }
        }
        if ($this->get('exporter')->get('formatclass') == PORTFOLIO_FORMAT_SPREADSHEET) {
            $csv = glossary_generate_export_csv($entries, $aliases, $categories);
            $this->exporter->write_new_file($csv, clean_filename($this->cm->name) . '.csv', false);
            return;
        } else if ($this->get('exporter')->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
            $ids = array(); // keep track of these to make into a selection later
            global $USER, $DB;
            $writer = $this->get('exporter')->get('format')->leap2a_writer($USER);
            $format = $this->exporter->get('format');
            $filename = $this->get('exporter')->get('format')->manifest_name();
            foreach ($entries as $e) {
                $content = glossary_entry_portfolio_caller::entry_content(
                    $this->course,
                    $this->cm,
                    $this->glossary,
                    $e,
                    (array_key_exists($e->id, $aliases) ? $aliases[$e->id] : array()),
                    $format
                );
                $entry = new portfolio_format_leap2a_entry('glossaryentry' . $e->id, $e->concept, 'entry', $content);
                $entry->author    = $DB->get_record('user', array('id' => $e->userid), 'id,firstname,lastname,email');
                $entry->published = $e->timecreated;
                $entry->updated   = $e->timemodified;
                if (!empty($this->keyedfiles[$e->id])) {
                    $writer->link_files($entry, $this->keyedfiles[$e->id], 'glossaryentry' . $e->id . 'file');
                    foreach ($this->keyedfiles[$e->id] as $file) {
                        $this->exporter->copy_existing_file($file);
                    }
                }
                if (!empty($categories[$e->id])) {
                    foreach ($categories[$e->id] as $cat) {
                        // this essentially treats them as plain tags
                        // leap has the idea of category schemes
                        // but I think this is overkill here
                        $entry->add_category($cat);
                    }
                }
                $writer->add_entry($entry);
                $ids[] = $entry->id;
            }
            $selection = new portfolio_format_leap2a_entry('wholeglossary' . $this->glossary->id, get_string('modulename', 'glossary'), 'selection');
            $writer->add_entry($selection);
            $writer->make_selection($selection, $ids, 'Grouping');
            $content = $writer->to_xml();
        }
        $this->exporter->write_new_file($content, $filename, true);
    }

    /**
     * make sure that the current user is allowed to do this
     *
     * @return boolean
     */
    public function check_permissions() {
        return has_capability('mod/glossary:export', context_module::instance($this->cm->id));
    }

    /**
     * return a nice name to be displayed about this export location
     *
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'glossary');
    }

    /**
     * what formats this function *generally* supports
     *
     * @return array
     */
    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_SPREADSHEET, PORTFOLIO_FORMAT_LEAP2A);
    }
}

/**
 * class to export a single glossary entry
 *
 * @package   mod_glossary
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class glossary_entry_portfolio_caller extends portfolio_module_caller_base {

    private $glossary;
    private $entry;
    protected $entryid;

    /** @var array Array that contains all aliases for the given glossary entry. */
    private array $aliases = [];

    /** @var array categories. */
    private array $categories = [];

    /*
     * @return array
     */
    public static function expected_callbackargs() {
        return array(
            'entryid' => true,
            'id'      => true,
        );
    }

    /**
     * load up all data required for this export.
     *
     * @return void
     */
    public function load_data() {
        global $DB;
        if (!$this->cm = get_coursemodule_from_id('glossary', $this->id)) {
            throw new portfolio_caller_exception('invalidid', 'glossary');
        }
        if (!$this->glossary = $DB->get_record('glossary', array('id' => $this->cm->instance))) {
            throw new portfolio_caller_exception('invalidid', 'glossary');
        }
        if ($this->entryid) {
            if (!$this->entry = $DB->get_record('glossary_entries', array('id' => $this->entryid))) {
                throw new portfolio_caller_exception('noentry', 'glossary');
            }
            // in case we don't have USER this will make the entry be printed
            $this->entry->approved = true;
        }
        $this->categories = $DB->get_records_sql('SELECT ec.entryid, c.name FROM {glossary_entries_categories} ec
            JOIN {glossary_categories} c
            ON c.id = ec.categoryid
            WHERE ec.entryid = ?', array($this->entryid));
        $context = context_module::instance($this->cm->id);
        if ($this->entry->sourceglossaryid == $this->cm->instance) {
            if ($maincm = get_coursemodule_from_instance('glossary', $this->entry->glossaryid)) {
                $context = context_module::instance($maincm->id);
            }
        }
        $this->aliases = $DB->get_records('glossary_alias', ['entryid' => $this->entryid]);
        $fs = get_file_storage();
        $this->multifiles = array_merge(
            $fs->get_area_files($context->id, 'mod_glossary', 'attachment', $this->entry->id, "timemodified", false),
            $fs->get_area_files($context->id, 'mod_glossary', 'entry', $this->entry->id, "timemodified", false)
        );

        if (!empty($this->multifiles)) {
            $this->add_format(PORTFOLIO_FORMAT_RICHHTML);
        } else {
            $this->add_format(PORTFOLIO_FORMAT_PLAINHTML);
        }
    }

    /**
     * how long might we expect this export to take
     *
     * @return constant one of PORTFOLIO_TIME_XX
     */
    public function expected_time() {
        return PORTFOLIO_TIME_LOW;
    }

    /**
     * make sure that the current user is allowed to do this
     *
     * @return boolean
     */
    public function check_permissions() {
        $context = context_module::instance($this->cm->id);
        return has_capability('mod/glossary:exportentry', $context)
            || ($this->entry->userid == $this->user->id && has_capability('mod/glossary:exportownentry', $context));
    }

    /**
     * return a nice name to be displayed about this export location
     *
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'glossary');
    }

    /**
     * prepare the package ready to be passed off to the portfolio plugin
     *
     * @return void
     */
    public function prepare_package() {
        global $DB;

        $format = $this->exporter->get('format');
        $content = self::entry_content($this->course, $this->cm, $this->glossary, $this->entry, $this->aliases, $format);

        if ($this->exporter->get('formatclass') === PORTFOLIO_FORMAT_PLAINHTML) {
            $filename = clean_filename($this->entry->concept) . '.html';
            $this->exporter->write_new_file($content, $filename);

        } else if ($this->exporter->get('formatclass') === PORTFOLIO_FORMAT_RICHHTML) {
            if ($this->multifiles) {
                foreach ($this->multifiles as $file) {
                    $this->exporter->copy_existing_file($file);
                }
            }
            $filename = clean_filename($this->entry->concept) . '.html';
            $this->exporter->write_new_file($content, $filename);

        } else if ($this->exporter->get('formatclass') === PORTFOLIO_FORMAT_LEAP2A) {
            $writer = $this->get('exporter')->get('format')->leap2a_writer();
            $entry = new portfolio_format_leap2a_entry('glossaryentry' . $this->entry->id, $this->entry->concept, 'entry', $content);
            $entry->author = $DB->get_record('user', array('id' => $this->entry->userid), 'id,firstname,lastname,email');
            $entry->published = $this->entry->timecreated;
            $entry->updated = $this->entry->timemodified;
            if ($this->multifiles) {
                $writer->link_files($entry, $this->multifiles);
                foreach ($this->multifiles as $file) {
                    $this->exporter->copy_existing_file($file);
                }
            }
            if ($this->categories) {
                foreach ($this->categories as $cat) {
                    // this essentially treats them as plain tags
                    // leap has the idea of category schemes
                    // but I think this is overkill here
                    $entry->add_category($cat->name);
                }
            }
            $writer->add_entry($entry);
            $content = $writer->to_xml();
            $filename = $this->get('exporter')->get('format')->manifest_name();
            $this->exporter->write_new_file($content, $filename);

        } else {
            throw new portfolio_caller_exception('unexpected_format_class', 'glossary');
        }
    }

    /**
     * return the sha1 of this content
     *
     * @return string
     */
    public function get_sha1() {
        if ($this->multifiles) {
            return sha1(serialize($this->entry) . $this->get_sha1_file());
        }
        return sha1(serialize($this->entry));
    }

    /**
     * what formats this function *generally* supports
     *
     * @return array
     */
    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_RICHHTML, PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_LEAP2A);
    }

    /**
     * helper function to get the html content of an entry
     * for both this class and the full glossary exporter
     * this is a very simplified version of the dictionary format output,
     * but with its 500 levels of indirection removed
     * and file rewriting handled by the portfolio export format.
     *
     * @param stdclass $course
     * @param stdclass $cm
     * @param stdclass $glossary
     * @param stdclass $entry
     *
     * @return string
     */
    public static function entry_content($course, $cm, $glossary, $entry, $aliases, $format) {
        global $OUTPUT, $DB;
        $entry = clone $entry;
        $context = context_module::instance($cm->id);
        $options = portfolio_format_text_options();
        $options->trusted = $entry->definitiontrust;
        $options->context = $context;

        $output = '<table class="glossarypost dictionary table-reboot" cellspacing="0">' . "\n";
        $output .= '<tr valign="top">' . "\n";
        $output .= '<td class="entry">' . "\n";

        $output .= '<div class="concept">';
        $output .= format_text($OUTPUT->heading($entry->concept, 3), FORMAT_MOODLE, $options);
        $output .= '</div> ' . "\n";

        $entry->definition = format_text($entry->definition, $entry->definitionformat, $options);
        $output .= portfolio_rewrite_pluginfile_urls($entry->definition, $context->id, 'mod_glossary', 'entry', $entry->id, $format);

        if (isset($entry->footer)) {
            $output .= $entry->footer;
        }

        $output .= '</td></tr>' . "\n";

        if (!empty($aliases)) {
            $output .= '<tr valign="top"><td class="entrylowersection">';
            $key = (count($aliases) == 1) ? 'alias' : 'aliases';
            $output .= get_string($key, 'glossary') . ': ';
            foreach ($aliases as $alias) {
                $output .= s($alias->alias) . ',';
            }
            $output = substr($output, 0, -1);
            $output .= '</td></tr>' . "\n";
        }

        if ($entry->sourceglossaryid == $cm->instance) {
            if (!$maincm = get_coursemodule_from_instance('glossary', $entry->glossaryid)) {
                return '';
            }
            $filecontext = context_module::instance($maincm->id);

        } else {
            $filecontext = $context;
        }
        $fs = get_file_storage();
        if ($files = $fs->get_area_files($filecontext->id, 'mod_glossary', 'attachment', $entry->id, "timemodified", false)) {
            $output .= '<table border="0" width="100%" class="table-reboot"><tr><td>' . "\n";

            foreach ($files as $file) {
                $output .= $format->file_output($file);
            }
            $output .= '</td></tr></table>' . "\n";
        }

        $output .= '</table>' . "\n";

        return $output;
    }
}


/**
 * Class representing the virtual node with all itemids in the file browser
 *
 * @category  files
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class glossary_file_info_container extends file_info {
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
        $this->component = 'mod_glossary';
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
        $sql = 'SELECT DISTINCT f.itemid, ge.concept
                  FROM {files} f
                  JOIN {modules} m ON (m.name = :modulename AND m.visible = 1)
                  JOIN {course_modules} cm ON (cm.module = m.id AND cm.id = :instanceid)
                  JOIN {glossary} g ON g.id = cm.instance
                  JOIN {glossary_entries} ge ON (ge.glossaryid = g.id AND ge.id = f.itemid)
                 WHERE f.contextid = :contextid
                  AND f.component = :component
                  AND f.filearea = :filearea';
        $params = array(
            'modulename' => 'glossary',
            'instanceid' => $this->context->instanceid,
            'contextid' => $this->context->id,
            'component' => $this->component,
            'filearea' => $this->filearea);
        if (!$returnemptyfolders) {
            $sql .= ' AND f.filename <> :emptyfilename';
            $params['emptyfilename'] = '.';
        }
        list($sql2, $params2) = $this->build_search_files_sql($extensions, 'f');
        $sql .= ' '.$sql2;
        $params = array_merge($params, $params2);
        if ($countonly !== false) {
            $sql .= ' ORDER BY ge.concept, f.itemid';
        }

        $rs = $DB->get_recordset_sql($sql, $params);
        $children = array();
        foreach ($rs as $record) {
            if ($child = $this->browser->get_file_info($this->context, 'mod_glossary', $this->filearea, $record->itemid)) {
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
 * Returns glossary entries tagged with a specified tag.
 *
 * This is a callback used by the tag area mod_glossary/glossary_entries to search for glossary entries
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
function mod_glossary_get_tagged_entries($tag, $exclusivemode = false, $fromctx = 0, $ctx = 0, $rec = 1, $page = 0) {
    global $OUTPUT;
    $perpage = $exclusivemode ? 20 : 5;

    // Build the SQL query.
    $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
    $query = "SELECT ge.id, ge.concept, ge.glossaryid, ge.approved, ge.userid,
                    cm.id AS cmid, c.id AS courseid, c.shortname, c.fullname, $ctxselect
                FROM {glossary_entries} ge
                JOIN {glossary} g ON g.id = ge.glossaryid
                JOIN {modules} m ON m.name='glossary'
                JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = g.id
                JOIN {tag_instance} tt ON ge.id = tt.itemid
                JOIN {course} c ON cm.course = c.id
                JOIN {context} ctx ON ctx.instanceid = cm.id AND ctx.contextlevel = :coursemodulecontextlevel
               WHERE tt.itemtype = :itemtype AND tt.tagid = :tagid AND tt.component = :component
                 AND cm.deletioninprogress = 0
                 AND ge.id %ITEMFILTER% AND c.id %COURSEFILTER%";

    $params = array('itemtype' => 'glossary_entries', 'tagid' => $tag->id, 'component' => 'mod_glossary',
                    'coursemodulecontextlevel' => CONTEXT_MODULE);

    if ($ctx) {
        $context = $ctx ? context::instance_by_id($ctx) : context_system::instance();
        $query .= $rec ? ' AND (ctx.id = :contextid OR ctx.path LIKE :path)' : ' AND ctx.id = :contextid';
        $params['contextid'] = $context->id;
        $params['path'] = $context->path.'/%';
    }

    $query .= " ORDER BY ";
    if ($fromctx) {
        // In order-clause specify that modules from inside "fromctx" context should be returned first.
        $fromcontext = context::instance_by_id($fromctx);
        $query .= ' (CASE WHEN ctx.id = :fromcontextid OR ctx.path LIKE :frompath THEN 0 ELSE 1 END),';
        $params['fromcontextid'] = $fromcontext->id;
        $params['frompath'] = $fromcontext->path.'/%';
    }
    $query .= ' c.sortorder, cm.id, ge.id';

    $totalpages = $page + 1;

    // Use core_tag_index_builder to build and filter the list of items.
    $builder = new core_tag_index_builder('mod_glossary', 'glossary_entries', $query, $params, $page * $perpage, $perpage + 1);
    while ($item = $builder->has_item_that_needs_access_check()) {
        context_helper::preload_from_record($item);
        $courseid = $item->courseid;
        if (!$builder->can_access_course($courseid)) {
            $builder->set_accessible($item, false);
            continue;
        }
        $modinfo = get_fast_modinfo($builder->get_course($courseid));
        // Set accessibility of this item and all other items in the same course.
        $builder->walk(function ($taggeditem) use ($courseid, $modinfo, $builder) {
            global $USER;
            if ($taggeditem->courseid == $courseid) {
                $accessible = false;
                if (($cm = $modinfo->get_cm($taggeditem->cmid)) && $cm->uservisible) {
                    if ($taggeditem->approved) {
                        $accessible = true;
                    } else if ($taggeditem->userid == $USER->id) {
                        $accessible = true;
                    } else {
                        $accessible = has_capability('mod/glossary:approve', context_module::instance($cm->id));
                    }
                }
                $builder->set_accessible($taggeditem, $accessible);
            }
        });
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
            $pageurl = new moodle_url('/mod/glossary/showentry.php', array('eid' => $item->id, 'displayformat' => 'dictionary'));
            $pagename = format_string($item->concept, true, array('context' => context_module::instance($item->cmid)));
            $pagename = html_writer::link($pageurl, $pagename);
            $courseurl = course_get_url($item->courseid, $cm->sectionnum);
            $cmname = html_writer::link($cm->url, $cm->get_formatted_name());
            $coursename = format_string($item->fullname, true, array('context' => context_course::instance($item->courseid)));
            $coursename = html_writer::link($courseurl, $coursename);
            $icon = html_writer::link($pageurl, html_writer::empty_tag('img', array('src' => $cm->get_icon_url())));

            $approved = "";
            if (!$item->approved) {
                $approved = '<br>'. html_writer::span(get_string('entrynotapproved', 'mod_glossary'), 'badge bg-warning text-dark');
            }
            $tagfeed->add($icon, $pagename, $cmname.'<br>'.$coursename.$approved);
        }

        $content = $OUTPUT->render_from_template('core_tag/tagfeed',
            $tagfeed->export_for_template($OUTPUT));

        return new core_tag\output\tagindex($tag, 'mod_glossary', 'glossary_entries', $content,
            $exclusivemode, $fromctx, $ctx, $rec, $page, $totalpages);
    }
}
