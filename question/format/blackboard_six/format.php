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
 * Blackboard V5 and V6 question importer.
 *
 * @package    qformat_blackboard_six
 * @copyright  2005 Michael Penney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/xmlize.php');
require_once($CFG->dirroot . '/question/format/blackboard_six/formatbase.php');
require_once($CFG->dirroot . '/question/format/blackboard_six/formatqti.php');
require_once($CFG->dirroot . '/question/format/blackboard_six/formatpool.php');

class qformat_blackboard_six extends qformat_blackboard_six_base {
    /** @var int Blackboard assessment qti files were always imported by the blackboard_six plugin. */
    const FILETYPE_QTI = 1;
    /** @var int Blackboard question pool files were previously handled by the blackboard plugin. */
    const FILETYPE_POOL = 2;
    /** @var int type of file being imported, one of the constants FILETYPE_QTI or FILETYPE_POOL. */
    public $filetype;

    public function get_filecontent($path) {
        $fullpath = $this->tempdir . '/' . $path;
        if (is_file($fullpath) && is_readable($fullpath)) {
            return file_get_contents($fullpath);
        }
        return false;
    }

    /**
     * Set the file type being imported
     * @param int $type the imported file's type
     */
    public function set_filetype($type) {
        $this->filetype = $type;
    }

    /**
     * Return content of all files containing questions,
     * as an array one element for each file found,
     * For each file, the corresponding element is an array of lines.
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
    public function readdata($filename) {
        global $CFG;

        // Find if we are importing a .dat file.
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) == 'dat') {
            if (!is_readable($filename)) {
                $this->error(get_string('filenotreadable', 'error'));
                return false;
            }
            // As we are not importing a .zip file,
            // there is no imsmanifest, and it is not possible
            // to parse it to find the file type.
            // So we need to guess the file type by looking at the content.
            // For now we will do that searching for a required tag.
            // This is certainly not bullet-proof but works for all usual files.
            $text = file_get_contents($filename);
            if (strpos($text, '<questestinterop>')) {
                $this->set_filetype(self::FILETYPE_QTI);
            }
            if (strpos($text, '<POOL>')) {
                $this->set_filetype(self::FILETYPE_POOL);
            }
            // In all other cases we are not able to handle this question file.

            // Readquestions is now expecting an array of strings.
            return array($text);
        }
        // We are importing a zip file.
        // Create name for temporary directory.
        $unique_code = time();
        $this->tempdir = make_temp_directory('bbquiz_import/' . $unique_code);
        if (is_readable($filename)) {
            if (!copy($filename, $this->tempdir . '/bboard.zip')) {
                $this->error(get_string('cannotcopybackup', 'question'));
                fulldelete($this->tempdir);
                return false;
            }
            if (unzip_file($this->tempdir . '/bboard.zip', '', false)) {
                $dom = new DomDocument();

                if (!$dom->load($this->tempdir . '/imsmanifest.xml')) {
                    $this->error(get_string('errormanifest', 'qformat_blackboard_six'));
                    fulldelete($this->tempdir);
                    return false;
                }

                $xpath = new DOMXPath($dom);

                // We starts from the root element.
                $query = '//resources/resource';
                $this->filebase = $this->tempdir;
                $q_file = array();

                $examfiles = $xpath->query($query);
                foreach ($examfiles as $examfile) {
                    if ($examfile->getAttribute('type') == 'assessment/x-bb-qti-test'
                            || $examfile->getAttribute('type') == 'assessment/x-bb-qti-pool') {

                        if ($content = $this->get_filecontent($examfile->getAttribute('bb:file'))) {
                            $this->set_filetype(self::FILETYPE_QTI);
                            $q_file[] = $content;
                        }
                    }
                    if ($examfile->getAttribute('type') == 'assessment/x-bb-pool') {
                        if ($examfile->getAttribute('baseurl')) {
                            $this->filebase = $this->tempdir. '/' . $examfile->getAttribute('baseurl');
                        }
                        if ($content = $this->get_filecontent($examfile->getAttribute('file'))) {
                            $this->set_filetype(self::FILETYPE_POOL);
                            $q_file[] = $content;
                        }
                    }
                }

                if ($q_file) {
                    return $q_file;
                } else {
                    $this->error(get_string('cannotfindquestionfile', 'question'));
                    fulldelete($this->tempdir);
                }
            } else {
                $this->error(get_string('cannotunzip', 'question'));
                fulldelete($this->temp_dir);
            }
        } else {
            $this->error(get_string('cannotreaduploadfile', 'error'));
            fulldelete($this->tempdir);
        }
        return false;
    }

    /**
     * Parse the array of strings into an array of questions.
     * Each string is the content of a .dat questions file.
     * This *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array of strings from the input file.
     * @param stdClass $context
     * @return array (of objects) question objects.
     */
    public function readquestions($lines) {

        // Set up array to hold all our questions.
        $questions = array();
        if ($this->filetype == self::FILETYPE_QTI) {
            $importer = new qformat_blackboard_six_qti();
        } else if ($this->filetype == self::FILETYPE_POOL) {
            $importer = new qformat_blackboard_six_pool();
        } else {
            // In all other cases we are not able to import the file.
            return false;
        }
        $importer->set_filebase($this->filebase);

        // Each element of $lines is a string containing a complete xml document.
        foreach ($lines as $text) {
                $questions = array_merge($questions, $importer->readquestions($text));
        }
        return $questions;
    }
}
