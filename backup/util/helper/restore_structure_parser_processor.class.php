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
 * @package moodlecore
 * @subpackage backup-helper
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/backup/util/xml/parser/processors/grouped_parser_processor.class.php');

/**
 * helper implementation of grouped_parser_processor that will
 * support the process of all the moodle2 backup files, with
 * complete specs about what to load (grouped or no), dispatching
 * to corresponding methods and basic decoding of contents
 * (NULLs and legacy file.php uses)
 *
 * TODO: Complete phpdocs
 */
class restore_structure_parser_processor extends grouped_parser_processor {

    protected $courseid; // Course->id we are restoring to
    protected $step;     // @restore_structure_step using this processor

    public function __construct($courseid, $step) {
        $this->courseid = $courseid;
        $this->step     = $step;
        parent::__construct();
    }

    /**
     * Provide NULL and legacy file.php uses decoding
     */
    public function process_cdata($cdata) {
        global $CFG;
        if ($cdata === '$@NULL@$') {  // Some cases we know we can skip complete processing
            return null;
        } else if ($cdata === '') {
            return '';
        } else if (is_numeric($cdata)) {
            return $cdata;
        } else if (strlen($cdata) < 32) { // Impossible to have one link in 32cc
            return $cdata;                // (http://10.0.0.1/file.php/1/1.jpg, http://10.0.0.1/mod/url/view.php?id=)
        } else if (strpos($cdata, '$@FILEPHP@$') === false) { // No $@FILEPHP@$, nothing to convert
            return $cdata;
        }
        // Decode file.php calls
        $search = array ("$@FILEPHP@$");
        $replace = array(moodle_url::make_legacyfile_url($this->courseid, null));
        $result = str_replace($search, $replace, $cdata);
        // Now $@SLASH@$ and $@FORCEDOWNLOAD@$ MDL-18799
        $search = array('$@SLASH@$', '$@FORCEDOWNLOAD@$');
        if ($CFG->slasharguments) {
            $replace = array('/', '?forcedownload=1');
        } else {
            $replace = array('%2F', '&amp;forcedownload=1');
        }
        return str_replace($search, $replace, $result);
    }

    /**
     * Override this method so we'll be able to skip
     * dispatching some well-known chunks, like the
     * ones being 100% part of subplugins stuff. Useful
     * for allowing development without having all the
     * possible restore subplugins defined
     */
    protected function postprocess_chunk($data) {

        // Iterate over all the data tags, if any of them is
        // not 'subplugin_XXXX' or has value, then it's a valid chunk,
        // pass it to standard (parent) processing of chunks.
        foreach ($data['tags'] as $key => $value) {
            if (trim($value) !== '' || strpos($key, 'subplugin_') !== 0) {
                parent::postprocess_chunk($data);
                return;
            }
        }
        // Arrived here, all the tags correspond to sublplugins and are empty,
        // skip the chunk, and debug_developer notice
        $this->chunks--; // not counted
        debugging('Missing support on restore for ' . clean_param($data['path'], PARAM_PATH) .
                  ' subplugin (' . implode(', ', array_keys($data['tags'])) .')', DEBUG_DEVELOPER);
    }

    protected function dispatch_chunk($data) {
        $this->step->process($data);
    }

    protected function notify_path_start($path) {
        // nothing to do
    }

    protected function notify_path_end($path) {
        // nothing to do
    }
}
