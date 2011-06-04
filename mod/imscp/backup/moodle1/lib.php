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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage imscp
 * @copyright  2011 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * imscp conversion handler. This resource handler is called by moodle1_mod_resource_handler
 */
class moodle1_mod_imscp_handler extends moodle1_mod_handler {
    /** @var array in-memory cache for the course module information for the current imscp  */
    protected $currentcminfo = null;

    //there are two file manager instances as we need to put files in two file areas

    /** @var moodle1_file_manager the file manager instance */
    protected $fileman = null;

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array();
    }

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     * Called by moodle1_mod_resource_handler::process_resource()
     */
    public function process_resource($data) {
        global $CFG;

        $instanceid          = $data['id'];
        $this->currentcminfo = $this->get_cminfo($instanceid);
        $moduleid            = $this->currentcminfo['id'];
        $contextid           = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        if ($CFG->texteditors !== 'textarea') {
            $data['intro']       = text_to_html($data['intro'], false, false, true);
            $data['introformat'] = FORMAT_HTML;
        } else {
            $data['intro']       = $data['intro'];
            $data['introformat'] = FORMAT_MOODLE;
        }

        $data['revision'] = 1;
        $data['keepold']  = 1;

        //Prepare to migrate the deployed (ie extracted) version of the content package
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_imscp', 'content', $data['revision']);
        $this->fileman->migrate_directory('moddata/resource/'.$data['id']);

        // parse manifest
        $structure = $this->parse_structure($data, $contextid);
        $data['structure'] = is_array($structure) ? serialize($structure) : null;

        // we now have all information needed to start writing into the module file

        $this->open_xml_writer("activities/imscp_{$moduleid}/imscp.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'imscp', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('imscp', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        /* We currently do not support undeployed IMS packages
         * They need to be deployed (unzipped) to the mod data area then have the ims structure figured out
        */
    }

    public function on_resource_end() {
        //close imscp.xml
        $this->xmlwriter->end_tag('imscp');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml for migrated imscp files
        $this->open_xml_writer("activities/imscp_{$this->currentcminfo['id']}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }

    /// internal implementation details follow /////////////////////////////////

    /**
     * Parse the IMS package structure for the $imscp->structure field
     * @param array $data the array of ims package data
     */
    protected function parse_structure($data, $contextid) {
        global $CFG;

        $temppath = $this->converter->get_tempdir_path();
        $manifestfilecontents = file_get_contents($temppath.'/moddata/resource/'.$data['id'].'/imsmanifest.xml');
        if (empty($manifestfilecontents)) {
            return null;
        }

        require_once($CFG->dirroot.'/mod/imscp/locallib.php');
        return imscp_parse_manifestfile($manifestfilecontents);
    }
}