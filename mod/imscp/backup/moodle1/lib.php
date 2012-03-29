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
class moodle1_mod_imscp_handler extends moodle1_resource_successor_handler {

    /** @var moodle1_file_manager the file manager instance */
    protected $fileman = null;

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     * Called by moodle1_mod_resource_handler::process_resource()
     */
    public function process_legacy_resource(array $data, array $raw = null) {

        $instanceid    = $data['id'];
        $currentcminfo = $this->get_cminfo($instanceid);
        $moduleid      = $currentcminfo['id'];
        $contextid     = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // prepare the new imscp instance record
        $imscp                  = array();
        $imscp['id']            = $data['id'];
        $imscp['name']          = $data['name'];
        $imscp['intro']         = $data['intro'];
        $imscp['introformat']   = $data['introformat'];
        $imscp['revision']      = 1;
        $imscp['keepold']       = 1;
        $imscp['structure']     = null;
        $imscp['timemodified']  = $data['timemodified'];

        // prepare a fresh new file manager for this instance
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_imscp');

        // convert course files embedded into the intro
        $this->fileman->filearea = 'intro';
        $this->fileman->itemid   = 0;
        $imscp['intro'] = moodle1_converter::migrate_referenced_files($imscp['intro'], $this->fileman);

        // migrate package backup file
        if ($data['reference']) {
            $packagename = basename($data['reference']);
            $packagepath = $this->converter->get_tempdir_path().'/moddata/resource/'.$data['id'].'/'.$packagename;
            if (file_exists($packagepath)) {
                $this->fileman->filearea = 'backup';
                $this->fileman->itemid   = 1;
                $this->fileman->migrate_file('moddata/resource/'.$data['id'].'/'.$packagename);
            } else {
                $this->log('missing imscp package', backup::LOG_WARNING, 'moddata/resource/'.$data['id'].'/'.$packagename);
            }
        }

        // migrate extracted package data
        $this->fileman->filearea = 'content';
        $this->fileman->itemid   = 1;
        $this->fileman->migrate_directory('moddata/resource/'.$data['id']);

        // parse manifest
        $structure = $this->parse_structure($this->converter->get_tempdir_path().'/moddata/resource/'.$data['id'].'/imsmanifest.xml');
        $imscp['structure'] = is_array($structure) ? serialize($structure) : null;

        // write imscp.xml
        $this->open_xml_writer("activities/imscp_{$moduleid}/imscp.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'imscp', 'contextid' => $contextid));
        $this->write_xml('imscp', $imscp, array('/imscp/id'));
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/imscp_{$moduleid}/inforef.xml");
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
     *
     * @param string $manifestfilepath the full path to the manifest file to parse
     */
    protected function parse_structure($manifestfilepath) {
        global $CFG;

        if (!file_exists($manifestfilepath)) {
            $this->log('missing imscp manifest file', backup::LOG_WARNING);
            return null;
        }
        $manifestfilecontents = file_get_contents($manifestfilepath);
        if (empty($manifestfilecontents)) {
            $this->log('empty imscp manifest file', backup::LOG_WARNING);
            return null;
        }

        require_once($CFG->dirroot.'/mod/imscp/locallib.php');
        return imscp_parse_manifestfile($manifestfilecontents);
    }
}
