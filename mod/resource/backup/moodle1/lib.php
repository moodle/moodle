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
 * @subpackage forum
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Forum conversion handler
 */
class moodle1_mod_resource_handler extends moodle1_mod_handler {
    /** @var array in-memory cache for the course module information  */
    protected $currentcminfo = null;
    /** @var moodle1_file_manager instance */
    protected $fileman = null;

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the paths /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE do not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'resource', '/MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE',
                array(
                    'renamefields' => array(
                        'summary' => 'intro',
                    ),
                    'newfields' => array(
                        'introformat' => 0,
                    ),
                    'dropfields' => array(
                        'modtype',
                    ),
                )
            )
        );
    }

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     */
    public function process_resource(array $data, array $raw) {
        global $CFG;
        require_once("$CFG->libdir/resourcelib.php");

        //if this is a file or URL resource we need to deal with the options
        //before possibly branching out to the URL successor
        if ($data['type'] == 'file') {
            $options = array('printheading'=>0, 'printintro'=>1);
            if ($data['options'] == 'frame') {
                $data['display'] = RESOURCELIB_DISPLAY_FRAME;

            } else if ($data['options'] == 'objectframe') {
                $data['display'] = RESOURCELIB_DISPLAY_EMBED;

            } else if ($data['options'] == 'forcedownload') {
                $data['display'] = RESOURCELIB_DISPLAY_DOWNLOAD;

            } else if ($data['popup']) {
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
                $data['display'] = RESOURCELIB_DISPLAY_AUTO;
            }
            $data['displayoptions'] = serialize($options);
            unset($data['popup']);
        }

        // fix invalid NULL popup and options data in old mysql databases
        if (!array_key_exists ('popup', $data) || $data['popup'] === null) {
            $data['popup'] = '';
        }
        if (!array_key_exists ('options', $data) || $data['options'] === null) {
            $data['options'] = '';
        }

        if ($successor = $this->get_successor($data['type'], $data['reference'])) {
            // the instance id will be kept
            $instanceid = $data['id'];

            // move the instance from the resource's modinfo stash to the successor's
            // modinfo stash
            $resourcemodinfo  = $this->converter->get_stash('modinfo_resource');
            $successormodinfo = $this->converter->get_stash('modinfo_'.$successor->get_modname());
            $successormodinfo['instances'][$instanceid] = $resourcemodinfo['instances'][$instanceid];
            unset($resourcemodinfo['instances'][$instanceid]);
            $this->converter->set_stash('modinfo_resource', $resourcemodinfo);
            $this->converter->set_stash('modinfo_'.$successor->get_modname(), $successormodinfo);

            // get the course module information for the legacy resource module
            $cminfo = $this->get_cminfo($instanceid);

            // use the version of the successor instead of the current mod/resource
            // beware - the version.php declares info via $module object, do not use
            // a variable of such name here
            include $CFG->dirroot.'/mod/'.$successor->get_modname().'/version.php';
            $cminfo['version'] = $module->version;

            // stash the new course module information for this successor
            $cminfo['modulename'] = $successor->get_modname();
            $this->converter->set_stash('cminfo_'.$cminfo['modulename'], $cminfo, $instanceid);

            // delegate the processing to the successor handler
            return $successor->process_resource($data, $raw);
        }

        //only $data['type'] == "file" should get to here

        // get the course module id and context id
        $instanceid             = $data['id'];
        $this->currentcminfo    = $this->get_cminfo($instanceid);
        $moduleid               = $this->currentcminfo['id'];
        $contextid              = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        unset($data['type']);
        unset($data['alltext']);
        unset($data['popup']);
        unset($data['options']);

        $data['tobemigrated'] = 0;
        $data['mainfile'] = null;
        $data['legacyfiles'] = 0;
        $data['legacyfileslast'] = null;
        $data['display'] = 0;
        $data['displayoptions'] = null;
        $data['filterfiles'] = 0;
        $data['revision'] = 0;
        unset($data['mainfile']);

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/resource_{$moduleid}/resource.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'resource', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('resource', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        // prepare file manager for migrating the resource file
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_resource', 'content');
        $this->fileman->migrate_file('course_files/'.$data['reference']);
    }

    public function on_resource_end(array $data) {
        if ($successor = $this->get_successor($data['type'], $data['reference'])) {
            $successor->on_resource_end($data);
        } else {
            $this->xmlwriter->end_tag('resource');
            $this->xmlwriter->end_tag('activity');
            $this->close_xml_writer();

            // write inforef.xml for migrated resource file.
            $this->open_xml_writer("activities/resource_{$this->currentcminfo['id']}/inforef.xml");
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

    /// internal implementation details follow /////////////////////////////////

    /**
     * Returns the handler of the new 2.0 mod type according the given type of the legacy 1.9 resource
     *
     * @param string $type the value of the 'type' field in 1.9 resource
     * @param string $reference a file path. Necessary to differentiate files from web URLs
     * @throws moodle1_convert_exception for the unknown types
     * @return null|moodle1_mod_handler the instance of the handler, or null if the type does not have a successor
     */
    protected function get_successor($type, $reference) {
        static $successors = array();

        switch ($type) {
            case 'text':
            case 'html':
                $name = 'page';
                break;
            case 'directory':
                $name = 'folder';
                break;
            case 'ims':
                $name = 'imscp';
                break;
            case 'file':
                // if http:// https:// ftp:// OR starts with slash need to be converted to URL
                if (strpos($reference, '://') or strpos($reference, '/') === 0) {
                    $name = 'url';
                } else {
                    $name = null;
                }
                break;
            default:
                throw new moodle1_convert_exception('unknown_resource_successor', $type);
        }

        if (is_null($name)) {
            return null;
        }

        if (!isset($successors[$name])) {
            $class = 'moodle1_mod_'.$name.'_handler';
            $successors[$name] = new $class($this->converter, 'mod', $name);

            // add the successor into the modlist stash
            $modnames = $this->converter->get_stash('modnameslist');
            $modnames[] = $name;
            $modnames = array_unique($modnames); // should not be needed but just in case
            $this->converter->set_stash('modnameslist', $modnames);

            // add the successor's modinfo stash
            $modinfo = $this->converter->get_stash('modinfo_resource');
            $modinfo['name'] = $name;
            $modinfo['instances'] = array();
            $this->converter->set_stash('modinfo_'.$name, $modinfo);
        }

        return $successors[$name];
     }
}