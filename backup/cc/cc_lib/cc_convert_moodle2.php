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
* @package    backup-convert
* @subpackage cc-library
* @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

if (!extension_loaded('fileinfo')) {
    die('You must install fileinfo extension!');
}

abstract class cc_convert_moodle2 {

    /**
     *
     * Enter description here ...
     * @param unknown_type $packagedir
     * @param unknown_type $outdir
     * @throws DOMException
     * @throws InvalidArgumentException
     */
    public static function convert($packagedir, $outdir) {
        $dir = realpath($packagedir);
        if (empty($dir)) {
            throw new InvalidArgumentException('Directory does not exist!');
        }
        $odir = realpath($outdir);
        if (empty($odir)) {
            throw new InvalidArgumentException('Directory does not exist!');
        }
        $coursefile = $dir.DIRECTORY_SEPARATOR.'course'.DIRECTORY_SEPARATOR.'course.xml';
        $doc = new XMLGenericDocument();
        if ($doc->load($coursefile)) {
            $course_name = $doc->nodeValue('/course/fullname');
            $course_desc = $doc->nodeValue('/course/summary');
            $course_language = $doc->nodeValue('/course/lang');
            $course_language = empty($course_language) ? 'en' : $course_language;
            $course_category = $doc->nodeValue('/course/category/name');

            //Initialize the manifest metadata class
            $meta = new cc_metadata_manifest();

            //Package metadata
            $metageneral = new cc_metadata_general();
            $metageneral->set_language($course_language);
            $metageneral->set_title($course_name, $course_language);
            $metageneral->set_description($course_desc, $course_language);
            $metageneral->set_catalog('category');
            $metageneral->set_entry($course_category);
            $meta->add_metadata_general($metageneral);

            // Create the manifest
            $manifest = new cc_manifest(cc_version::v11);

            $manifest->add_metadata_manifest($meta);

            $organization = null;

            //Package structure - default organization and resources
            //Get the course structure - this will be transformed into organization
            //Step 1 - Get the list and order of sections/topics
            $moodle_backup = $dir . DIRECTORY_SEPARATOR . 'moodle_backup.xml';
            $secp = new XMLGenericDocument();
            $docp = new XMLGenericDocument();
            if ($docp->load($moodle_backup)) {
                //sections
                $sections = array();
                $coursef = new XMLGenericDocument();
                $course_file = $dir . DIRECTORY_SEPARATOR .'course' . DIRECTORY_SEPARATOR . 'course.xml';
                $coursef->load($course_file);
                //$numsections = (int)$coursef->nodeValue('/course/numsections');
                // TODO MDL-35781, this is commented because numsections is now optional attribute
                $section_list = $docp->nodeList('/moodle_backup/information/contents/sections/section');
                if (!empty($section_list)) {
                    $count = 0;
                    foreach ($section_list as $node) {
                        //if ($count > $numsections) {
                        //    break;
                        //}
                        $sectionid    = $docp->nodeValue('sectionid', $node);
                        $sectiontitle = $docp->nodeValue('title'    , $node);
                        $sectionpath  = $docp->nodeValue('directory', $node);
                        $sequence = array();
                        //Get section stuff
                        $section_file = $dir .
                        DIRECTORY_SEPARATOR .
                        $sectionpath .
                        DIRECTORY_SEPARATOR .
                        'section.xml';
                        if ($secp->load($section_file)) {
                            $rawvalue = $secp->nodeValue('/section/sequence');
                            if ($rawvalue != '$@NULL@$') {
                                $sequence = explode(',', $rawvalue);
                            }
                        }
                        $sections[$sectionid] = array($sectiontitle, $sequence);
                        $count++;
                    }
                }
                //organization title
                $organization = new cc_organization();
                //Add section/topic items
                foreach ($sections as $sectionid => $values) {
                    $item = new cc_item();
                    $item->title = $values[0];
                    self::process_sequence($item, $manifest, $values[1], $dir, $odir);
                    $organization->add_item($item);
                }
                $manifest->put_nodes();
            }

            if (!empty($organization)) {
                $manifest->add_new_organization($organization);
            }

            $manifestpath = $outdir.DIRECTORY_SEPARATOR.'imsmanifest.xml';
            $manifest->saveTo($manifestpath);
        }

    }

    /**
    *
    * Process the activites and create item structure
    * @param cc_i_item $item
    * @param array $sequence
    * @param string $packageroot - directory path
    * @throws DOMException
    */
    protected static function process_sequence(cc_i_item &$item, cc_i_manifest &$manifest, array $sequence, $packageroot, $outdir) {
        $moodle_backup = $packageroot . DIRECTORY_SEPARATOR . 'moodle_backup.xml';
        $doc = new XMLGenericDocument();
        if(!$doc->load($moodle_backup)) {
            return;
        }
        $activities = $doc->nodeList('/moodle_backup/information/contents/activities/activity');
        if (!empty($activities)) {
            $dpp = new XMLGenericDocument();
            foreach ($activities as $activity) {
                $moduleid = $doc->nodeValue('moduleid', $activity);
                if (in_array($moduleid, $sequence)) {
                    //detect activity type
                    $directory = $doc->nodeValue('directory', $activity);
                    $path = $packageroot . DIRECTORY_SEPARATOR . $directory;
                    $module_file = $path . DIRECTORY_SEPARATOR . 'module.xml';
                    if ($dpp->load($module_file)) {
                        $activity_type = $dpp->nodeValue('/module/modulename');
                        $activity_indentation = $dpp->nodeValue('/module/indent');
                        $aitem = self::item_indenter($item, $activity_indentation);
                        $caller = "cc_converter_{$activity_type}";
                        if (class_exists($caller)) {
                            $obj = new $caller($aitem, $manifest, $packageroot, $path);
                            if (!$obj->convert($outdir)) {
                                throw new RuntimeException("failed to convert {$activity_type}");
                            }
                        }
                    }
                }
            }
        }
    }

    protected static function item_indenter(cc_i_item &$item, $level = 0) {
        $indent = (int)$level;
        $indent = ($indent) <= 0 ? 0 : $indent;
        $nprev = null;
        $nfirst = null;
        for ($pos = 0, $size = $indent; $pos < $size; $pos++) {
            $nitem = new cc_item();
            $nitem->title = '';
            if (empty($nfirst)) {
                $nfirst = $nitem;
            }
            if (!empty($nprev)) {
                $nprev->add_child_item($nitem);
            }
            $nprev = $nitem;
        }
        $result = $item;
        if (!empty($nfirst)) {
            $item->add_child_item($nfirst);
            $result = $nprev;
        }
        return $result;
    }

}