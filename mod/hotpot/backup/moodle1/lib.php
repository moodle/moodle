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
 * @subpackage hotpot
 * @copyright  2012 Gordon Bateson <gordonbateson@gmail.com>
 *             credit and thanks to Robin de vries <robin@celp.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** include required files */
require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

/**
 * HotPot conversion handler
 *
 * methods available in this class:
 * - get_paths
 * - process_hotpot
 * - on_hotpot_end
 * - __construct, get_modname, get_cminfo, get_component_name
 * - open_xml_writer, close_xml_writer, has_xml_writer, write_xml, make_sure_xml_exists
 * - get_converter, log
 */
class moodle1_mod_hotpot_handler extends moodle1_mod_handler {

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances. For each path returned,
     * at least one of on_xxx_start(), process_xxx() and on_xxx_end() methods must be
     * defined. The method process_xxx() is not executed if the associated path element is
     * empty (i.e. it contains none elements or sub-paths only).
     *
     * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/HOTPOT does not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path('hotpot', '/MOODLE_BACKUP/COURSE/MODULES/MOD/HOTPOT',
                array(
                    'renamefields' => array(
                        'reference' => 'sourcefile',
                        'location'  => 'sourcelocation',
                        'summary'   => 'entrytext',
                        'review'    => 'reviewoptions',
                        'attempts'  => 'attemptlimit',
                        'grade'     => 'gradeweighting'
                    ),
                    'newfields' => array(
                        'sourcetype'   => '',
                        'configfile'   => '',
                        'configlocation' => '0',
                        'entrycm'      => '0',
                        'entrygrade'   => '100',
                        'entrypage'    => '0',
                        'entryformat'  => '0',
                        'entryoptions' => '0',
                        'exitpage'     => '0',
                        'exittext'     => '',
                        'exitformat'   => '0',
                        'exitoptions'  => '0',
                        'exitcm'       => '0',
                        'exitgrade'    => '0',
                        'title'        => '3',
                        'stopbutton'   => '0',
                        'stoptext'     => '',
                        'usefilters'   => '0',
                        'useglossary'  => '0',
                        'usemediafilter' => '',
                        'timelimit'    => '0',
                        'delay1'       => '0',
                        'delay2'       => '0',
                        'delay3 '      => '0',
                        'discarddetails' =>'0'
                    ),
                    'dropfields' => array(
                         //'forceplugins', // needed to set usemediafilter
                         //'shownextquiz', // needed to set exitcm
                        'modtype',
                        'course'
                    )
                )
            )
        );
    }

    /**
     * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/HOTPOT
     * data available
     */
    public function process_hotpot($data) {
        global $CFG;

        // get the course module id and context id
        $instanceid     = $data['id'];
        $currentcminfo  = $this->get_cminfo($instanceid);
        $this->moduleid = $currentcminfo['id'];
        $contextid      = $this->converter->get_contextid(CONTEXT_MODULE, $this->moduleid);

        // set $path, $filepath and $filename
        $is_url = preg_match('|^https?://|', $data['sourcefile']);
        if ($is_url) {

            $backupinfo = $this->converter->get_stash('backup_info');
            $originalcourseinfo = $this->converter->get_stash('original_course_info');

            $original_baseurl = $backupinfo['original_wwwroot'].'/'.$originalcourseinfo['original_course_id'].'/';
            unset($backupinfo, $originalcourseinfo);

            // if the URL is for a file in the original course files folder
            // then convert it to a simple path, by removing the original base url
            $search = '/^'.preg_quote($original_baseurl, '/').'/';
            if (preg_match($search, $data['sourcefile'])) {
                $data['sourcefile'] = substr($data['sourcefile'], strlen($original_baseurl));
                $is_url = false;
            }
        }

        if ($is_url) {
            $data['sourcetype'] = $this->get_hotpot_sourcetype($data['sourcefile']);
        } else {
            $filename = basename($data['sourcefile']);
            $filepath = dirname($data['sourcefile']);
            $filepath = trim($filepath, './');
            if ($filepath=='') {
                $filepath = '/';
            } else {
                $filepath = '/'.$filepath.'/';
            }
            $data['sourcefile'] = $filepath.$filename;
            $path = 'course_files'.$filepath.$filename;

            // get a fresh new file manager for this instance
            $this->fileman = $this->converter->get_file_manager($contextid, 'mod_hotpot');

            // migrate hotpot file
            $this->fileman->filearea = 'sourcefile';
            $this->fileman->itemid   = 0;
            $id = $this->fileman->migrate_file($path, $filepath, $filename);

            // get stashed hotpot $filerecord
            $filerecord = $this->fileman->converter->get_stash('files', $id);

            // seems like there should be a way to get the file content
            // using the $filerecord, but I can't see how to do it,
            // so for now we determine the $fullpath and read from that

            // set sourcetype
            $fullpath = $this->fileman->converter->get_tempdir_path().'/'.$path;
            $data['sourcetype'] = $this->get_hotpot_sourcetype($fullpath, $filerecord);
        }

        // set outputformat
        if ($data['outputformat']==14 && ($data['sourcetype']=='hp_6_jmatch_xml' || $data['sourcetype']=='hp_6_jmix_xml')) {
            $data['outputformat'] = $data['sourcetype'].'_v6';
        } else {
            $data['outputformat'] = ''; //  = "best" output format
        }

        // set usemediafilter (and remove forceplugins)
        if ($data['forceplugins']=='1') {
            $data['usemediafilter'] = 'moodle';
        } else {
            $data['usemediafilter'] = '';
        }
        unset($data['forceplugins']);

        // set exitcm (and remove shownextquiz)
        if ($data['shownextquiz']=='1') {
            $data['exitcm'] = '-4';
        } else {
            $data['exitcm'] = '0';
        }
        unset($data['shownextquiz']);

        // set navigation and stopbutton
        if ($data['navigation']=='5') {
            $data['stopbutton'] = '1';
        }
        if ($data['navigation']=='5' || $data['navigation']=='6') {
            $data['navigation'] = '0';
        }

        // start writing hotpot.xml
        $this->open_xml_writer("activities/hotpot_{$this->moduleid}/hotpot.xml");
        $this->xmlwriter->begin_tag('activity', array('id'=> $instanceid, 'moduleid' => $this->moduleid, 'modulename' => 'hotpot', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('hotpot', array('id' => $instanceid));

        // write out all $data fields to the xml file
        foreach ($data as $field => $value) {
            if ($field <> 'id') {
                $this->xmlwriter->full_tag($field, $value);
            }
        }

        return $data;
    }

    /**
     * given $fullpath to temporary imported Hot Potatoes file
     * this function returns the HotPot sourcetype of the file
     *
     * Where possible, the sourcetype will be determined from the file name extension
     * but in some cases, notably html files, it may be necessary to read the file
     * and analyze its contents in order to determine the sourcetype
     */
    public function get_hotpot_sourcetype($fullpath, $filerecord=null) {
        if ($pos = strrpos($fullpath, '.')) {
            $filetype = substr($fullpath, $pos+1);
            switch ($filetype) {
                case 'jcl': return 'hp_6_jcloze_xml';
                case 'jcw': return 'hp_6_jcross_xml';
                case 'jmt': return 'hp_6_jmatch_xml';
                case 'jmx': return 'hp_6_jmix_xml';
                case 'jqz': return 'hp_6_jquiz_xml';
                case 'rhb': return 'hp_6_rhubarb_xml';
                case 'sqt': return 'hp_6_sequitur_xml';
            }
        }

        // cannot detect sourcetype from filename alone
        // so we must open the file and examine the contents
        if ($filerecord) {
            $fs = get_file_storage();
            $sourcefile = $fs->create_file_from_pathname($filerecord, $fullpath);
            $sourcetype = hotpot::get_sourcetype($sourcefile);
            $sourcefile->delete();
            return $sourcetype;
        }

        // could not detect sourcetype
        return '';
    }

    /**
     * This is executed when we reach the closing </MOD> tag of our 'hotpot' path
     */
    public function on_hotpot_end() {
        // close hotpot.xml
        $this->xmlwriter->end_tag('hotpot');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/hotpot_{$this->moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        if (isset($this->fileman) && $this->fileman) {
            foreach ($this->fileman->get_fileids() as $fileid) {
                $this->write_xml('file', array('id' => $fileid));
            }
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}
