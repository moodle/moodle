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

require_once($CFG->libdir . '/portfolio/caller.php');

/**
 * Library of functions and constants for module glossary
 * outside of what is required for the core moodle api
 *
 * @package   mod-glossary
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class glossary_full_portfolio_caller extends portfolio_module_caller_base {

    private $glossary;
    private $exportdata;

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
    }

    /**
     * how long might we expect this export to take
     *
     * @return constant one of PORTFOLIO_TIME_XX
     */
    public function expected_time() {
        return portfolio_expected_time_db(count($this->exportdata['entries']));
    }

    /**
     * return the sha1 of this content
     *
     * @return string
     */
    public function get_sha1() {
        return sha1(serialize($this->exportdata));
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
        // TODO detect format here
        $csv = glossary_generate_export_csv($entries, $aliases, $categories);
        $this->exporter->write_new_file($csv, clean_filename($this->cm->name) . '.csv', false);
        // TODO when csv, what do we do with attachments?!
    }

    /**
     * make sure that the current user is allowed to do this
     *
     * @return boolean
     */
    public function check_permissions() {
        return has_capability('mod/glossary:export', get_context_instance(CONTEXT_MODULE, $this->cm->id));
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
 * @package   mod-glossary
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class glossary_entry_portfolio_caller extends portfolio_module_caller_base { // TODO files support

    private $glossary;
    private $entry;
    protected $entryid;
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
     * @global object
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
        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        if ($this->entry->sourceglossaryid == $this->cm->instance) {
            if ($maincm = get_coursemodule_from_instance('glossary', $this->entry->glossaryid)) {
                $context = get_context_instance(CONTEXT_MODULE, $maincm->id);
            }
        }
        $fs = get_file_storage();
        $this->multifiles = $fs->get_area_files($context->id, 'glossary_attachment', $this->entry->id, "timemodified", false);
    }
    /**
     * @return string
     */
    public function expected_time() {
        return PORTFOLIO_TIME_LOW;
    }
    /**
     * @return bool
     */
    public function check_permissions() {
        $context = get_context_instance(CONTEXT_MODULE, $this->cm->id);
        return has_capability('mod/glossary:exportentry', $context)
            || ($this->entry->userid == $this->user->id && has_capability('mod/glossary:exportownentry', $context));
    }
    /**
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'glossary');
    }
    /**
     * @return object
     */
    public function prepare_package() {
        define('PORTFOLIO_INTERNAL', true);
        ob_start();
        $entry = clone $this->entry;
        glossary_print_entry($this->get('course'), $this->cm, $this->glossary, $entry, null, null, false);
        $content = ob_get_clean();
        if ($this->multifiles) {
            foreach ($this->multifiles as $file) {
                $this->exporter->copy_existing_file($file);
            }
        }
        return $this->exporter->write_new_file($content, clean_filename($this->entry->concept) . '.html', !empty($files));
    }
    /**
     * @return string
     */
    public function get_sha1() {
        if ($this->multifiles) {
            return sha1(serialize($this->entry) . $this->get_sha1_file());
        }
        return sha1(serialize($this->entry));
    }

    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_RICHHTML);
    }
}
