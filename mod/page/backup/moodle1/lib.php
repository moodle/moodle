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
class moodle1_mod_page_handler extends moodle1_mod_handler {
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
        global $CFG;

        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid, 'resource');
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        //we now only support html intros
        if ($data['type'] == 'text') {
            $data['intro']       = text_to_html($data['intro'], false, false, true);
            $data['introformat'] = FORMAT_HTML;
        }

        $data['contentformat'] = (int)$data['reference'];
        if ($data['contentformat'] < 0 || $data['contentformat'] > 4) {
            $data['contentformat'] = FORMAT_MOODLE;
        }

        $data['content']      = $data['alltext'];
        $data['revision']     = 1;
        $data['timemodified'] = time();

        // convert links to old course files
        $originalcourseinfo = $this->converter->get_stash('original_course_info');
        if (!empty($originalcourseinfo) && array_key_exists('original_course_id', $originalcourseinfo)) {
            $courseid = $originalcourseinfo['original_course_id'];

            $usedfiles = array("$CFG->wwwroot/file.php/$courseid/", "$CFG->wwwroot/file.php?file=/$courseid/");
            $data['content'] = str_ireplace($usedfiles, '@@PLUGINFILE@@/', $data['content']);
            if (strpos($data['content'], '@@PLUGINFILE@@/') === false) {
                $data['legacyfiles'] = RESOURCELIB_LEGACYFILES_NO;
            } else {
                $data['legacyfiles'] = RESOURCELIB_LEGACYFILES_ACTIVE;
            }
        } else {
            $data['legacyfiles'] = RESOURCELIB_LEGACYFILES_NO;
        }

        $options = array('printheading'=>0, 'printintro'=>0);
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

        // prepare file manager for migrating the folder
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_page', 'content', 0);
        $this->fileman->migrate_directory('moddata/page/'.$data['id']);

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/page_{$moduleid}/page.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'page', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('page', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }
    }

    public function on_resource_end($data) {
        // close page.xml
        $this->xmlwriter->end_tag('page');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
    }
}