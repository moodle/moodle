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
 * Tiny text editor import of Microsoft Word files.
 *
 * This if forked from atto_wordimport.
 *
 * @package    tiny_wordimport
 * @copyright  2015 Eoin Campbell
 *             2023 André Menrath <andre.menrath@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_wordimport;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filestorage/file_storage.php');
require_once($CFG->dirroot . '/repository/lib.php');

use booktool_wordimport\wordconverter;

/**
 * See the only static function this class contains.
 */
class converter {
    /**
     * Extract the WordProcessingML XML files from the .docx file, and use a sequence of XSLT
     * steps to convert it into XHTML
     *
     * @param string  $wordfilename name of file uploaded to file repository as a draft
     * @param int     $usercontextid ID of draft file area where images should be stored
     * @param int     $draftitemid ID of particular group in draft file area where images should be stored
     * @return string XHTML content extracted from Word file
     */
    public static function docx_to_xhtml(string $wordfilename, int $usercontextid, int $draftitemid) {
        global $CFG, $USER;

        // Convert the Word file content into XHTML and an array of images.
        $imagesforzipping = [];
        $word2xml = new wordconverter();
        $word2xml->set_heading1styleoffset((int) get_config('tiny_wordimport', 'heading1stylelevel'));
        $xsltoutput = $word2xml->import($wordfilename, $imagesforzipping);
        $htmlcontent = $word2xml->htmlbody($xsltoutput);

        // Prepare filerecord array to create each new image file in the user/draft file area for the current user.
        $fileinfo = [
            'contextid' => $usercontextid,
            'component' => 'user',
            'filearea' => 'draft',
            'userid' => $USER->id,
            'itemid' => $draftitemid,
            'filepath' => '/',
            'filename' => '',
            ];
        $fs = get_file_storage();

        // Store the image files into the file area.
        foreach ($imagesforzipping as $imagename => $imagedata) {
            // Try saving with unique name until successful.
            $imagenameunique = $imagename;
            $imagesuffix = strtolower(substr(strrchr($imagename, "."), 0)); // Suffix is e.g. ".png".
            $file = $fs->get_file($usercontextid, 'user', 'draft', $draftitemid, '/', $imagenameunique);
            while ($file) {
                $imagenameunique = basename($imagename, $imagesuffix) . '_' . substr(uniqid(), 8, 4) . $imagesuffix;
                $file = $fs->get_file($usercontextid, 'user', 'draft', $draftitemid, '/', $imagenameunique);
            }

            // Found a unique name that Moodle is happy with, so keep it.
            $fileinfo['filename'] = $imagenameunique;
            $fs->create_file_from_string($fileinfo, $imagedata);
            $imageurl = "$CFG->wwwroot/draftfile.php/$usercontextid/user/draft/$draftitemid/$imagenameunique";

            // Replace the image name in the HTML content with the full Moodle file path.
            $htmlcontent = str_replace(' src="' . $imagename . '"', ' src="' . $imageurl . '"', $htmlcontent);
        }

        return $htmlcontent;
    }
}
