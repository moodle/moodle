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
 * @package   moodlecore
 * @subpackage backup-imscc
 * @copyright 2011 Darko Miletic (dmiletic@moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

require_once($CFG->dirroot . '/backup/cc/entities.class.php');

class entities11 extends entities {

    public function get_external_xml($identifier) {
        $xpath = cc2moodle::newx_path(cc112moodle::$manifest, cc112moodle::$namespaces);
        $files = $xpath->query('/imscc:manifest/imscc:resources/imscc:resource[@identifier="' .
                 $identifier . '"]/imscc:file/@href');
        $response = empty($files) || ($files->length == 0) ? '' : $files->item(0)->nodeValue;
        return $response;
    }

    protected function get_all_files () {
        global $CFG;
        $all_files = array();
        $xpath = cc2moodle::newx_path(cc112moodle::$manifest, cc112moodle::$namespaces);
        foreach (cc112moodle::$restypes as $type) {
            $files = $xpath->query('/imscc:manifest/imscc:resources/imscc:resource[@type="' .
                                    $type . '"]/imscc:file/@href');
            if (empty($files) || ($files->length == 0)) {
                continue;
            }
            foreach ($files as $file) {
                //omit html files
                //this is a bit too simplistic
                $ext = strtolower(pathinfo($file->nodeValue, PATHINFO_EXTENSION));
                if (in_array($ext, array('html', 'htm', 'xhtml'))) {
                    continue;
                }
                $all_files[] = $file->nodeValue;
            }
            unset($files);
        }

        //are there any labels?
        $xquery = "//imscc:item/imscc:item/imscc:item[imscc:title][not(@identifierref)]";
        $labels = $xpath->query($xquery);
        if (!empty($labels) && ($labels->length > 0)) {
            $tname = 'course_files';
            $dpath = cc2moodle::$path_to_manifest_folder . DIRECTORY_SEPARATOR . $tname;
            $rfpath = 'files.gif';
            $fpath = $dpath . DIRECTORY_SEPARATOR . 'files.gif';
            if (!file_exists($dpath)) {
                mkdir($dpath, $CFG->directorypermissions, true);
            }
            //copy the folder.gif file
            $folder_gif = "{$CFG->dirroot}/pix/i/files.gif";
            copy($folder_gif, $fpath);
            $all_files[] = $rfpath;
        }
        $all_files = empty($all_files) ? '' : $all_files;

        return $all_files;
    }

}

