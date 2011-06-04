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
 * @subpackage page
 * @copyright  2011 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Page conversion handler. This resource handler is called by moodle1_mod_resource_handler
 */
class moodle1_mod_page_handler extends moodle1_resource_successor_handler {

    /** @var moodle1_file_manager instance */
    protected $fileman = null;

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     * Called by moodle1_mod_resource_handler::process_resource()
     */
    public function process_resource($data) {

        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid, 'resource');
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // just in case there is some rubbish
        $data['contentformat'] = (int)$data['reference'];
        if ($data['contentformat'] < 0 || $data['contentformat'] > 4) {
            $data['contentformat'] = FORMAT_MOODLE;
        }

        // we can't use recipes in resource successors so add and rename fields manually
        $data['content']         = $data['alltext'];
        $data['revision']        = 1;
        $data['timemodified']    = time();
        $data['legacyfiles']     = RESOURCELIB_LEGACYFILES_ACTIVE;
        $data['legacyfileslast'] = null;

        // convert embedded course files
        if (is_null($this->fileman)) {
            $this->fileman = $this->converter->get_file_manager(null, 'mod_page', 'content');
        }
        $this->fileman->reset_fileids();
        $files = moodle1_converter::find_referenced_files($data['content']);
        if (!empty($files)) {
            $this->fileman->contextid = $contextid;
            $this->fileman->itemid = $instanceid;
            foreach ($files as $file) {
                $this->fileman->migrate_file('course_files'.$file);
            }
            $data['content'] = moodle1_converter::rewrite_filephp_usage($data['content'], $files);
        }

        // populate display and displayoptions fields
        $options = array('printheading' => 0, 'printintro' => 0);
        if ($data['popup']) {
            $data['display'] = RESOURCELIB_DISPLAY_POPUP;
            if ($data['popup']) {
                $rawoptions = explode(',', $data['popup']);
                foreach ($rawoptions as $rawoption) {
                    list($name, $value) = explode('=', trim($rawoption), 2);
                    if ($value > 0 and ($name == 'width' or $name == 'height')) {
                        $options['popup'.$name] = $value;
                        continue;
                    }
                }
            }
        } else {
            $data['display'] = RESOURCELIB_DISPLAY_OPEN;
        }
        $data['displayoptions'] = serialize($options);

        unset($data['alltext']);
        unset($data['type']);
        unset($data['reference']);
        unset($data['popup']);
        unset($data['options']);

        // write page.xml
        $this->open_xml_writer("activities/page_{$moduleid}/page.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'page', 'contextid' => $contextid));
        $this->write_xml('page', $data, array('/page/id'));
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml for migrated resource file.
        $this->open_xml_writer("activities/page_{$moduleid}/inforef.xml");
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
