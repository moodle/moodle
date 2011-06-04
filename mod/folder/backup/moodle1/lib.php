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
 * @subpackage folder
 * @copyright  2011 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Folder conversion handler. This resource handler is called by moodle1_mod_resource_handler
 */
class moodle1_mod_folder_handler extends moodle1_mod_handler {
    /** @var array in-memory cache for the course module information  */
    protected $currentcminfo = null;
    /** @var moodle1_file_manager instance */
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
        // get the course module id and context id
        $instanceid             = $data['id'];
        $this->currentcminfo    = $this->get_cminfo($instanceid);
        $moduleid               = $this->currentcminfo['id'];
        $contextid              = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/folder_{$moduleid}/folder.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'folder', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('folder', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        // prepare file manager for migrating the folder
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_folder', 'content');
        $this->fileman->migrate_directory('course_files/'.$data['reference']);
    }

    public function on_resource_end() {
        // close folder.xml
        $this->xmlwriter->end_tag('folder');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml for migrated folder
        $this->open_xml_writer("activities/folder_{$this->currentcminfo['id']}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}