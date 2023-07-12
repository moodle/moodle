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
 * Tile photo class for format tiles.
 * @package    format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles;

defined('MOODLE_INTERNAL') || die();
/**
 * Tile photo class for format tiles.
 * @package    format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tile_photo {

    /**
     * Course id for this course.
     * @var int
     */
    private $courseid;

    /**
     * Section id we are concerned with.
     * @var int
     */
    private $sectionid;

    /**
     * Context we are concerned with (will be course context).
     * @var \context_course
     */
    private $context;

    /**
     * The course format option reflecting this tile_photo object (from course_format_options table).
     * @var mixed
     */
    private $courseformatoption;

    /**
     * The filename relating to this tile_photo object.
     * @var
     */
    private $filename;

    /**
     * The file object to which this tile_photo object relates.
     * @var
     */
    private $file;

    /**
     * Creates a new instance of class
     *
     * @param int $courseid
     * @param int $sectionid
     * @throws \dml_exception
     */
    public function __construct($courseid, $sectionid) {
        global $DB;
        $this->courseid = $courseid;
        $this->sectionid = $sectionid;
        $this->context = \context_course::instance($courseid);
        $this->courseformatoption = $this->get_course_format_option();
        if (isset($this->courseformatoption->value)) {
            $this->filename = $this->courseformatoption->value;
        } else {
            // As no course format option is set, we should not have any files set either.  Make sure.
            self::delete_file_from_ids($courseid, $sectionid);
        }
        // Ensure that this section really exists in this course.
        $DB->get_record('course_sections', array('course' => $this->courseid, 'id' => $this->sectionid), "id", MUST_EXIST);
    }

    /**
     * Get the data from the course_format_options table for this tile_photo object.
     * @return mixed
     * @throws \dml_exception
     */
    private function get_course_format_option() {
        global $DB;
        return $DB->get_record('course_format_options', array(
                'courseid' => $this->courseid,
                'format' => 'tiles',
                'sectionid' => $this->sectionid,
                'name' => 'tilephoto'
            )
        );
    }

    /**
     * Get the data from the course_format_options table for this tile_photo object.
     * @param int $sectionid the section id that the format option relates to.
     * @param int $courseid the course id that the format option relates to.
     * @return mixed
     * @throws \dml_exception
     */
    public static function get_course_format_option_value($sectionid, $courseid) {
        global $DB;
        $field = $DB->get_field('course_format_options', 'value', array(
                'courseid' => $courseid,
                'format' => 'tiles',
                'sectionid' => $sectionid,
                'name' => 'tilephoto'
            )
        );
        return($field);
    }

    /**
     * Set the data in the course_format_options table for this tile_photo object.
     * @throws \dml_exception
     * @throws \required_capability_exception
     */
    public function set_course_format_option() {
        global $DB;
        require_capability('moodle/course:update', \context_course::instance($this->courseid));

        $record = $this->get_course_format_option();
        if (!$record) {
            $record = new \stdClass();
            $record->courseid = $this->courseid;
            $record->format = 'tiles';
            $record->sectionid = $this->sectionid;
            $record->name = 'tilephoto';
            $record->value = $this->filename;
            $record->id = $DB->insert_record('course_format_options', $record, true);
        } else {
            $record->value = $this->filename;
            $DB->update_record('course_format_options', $record);
        }
        $this->courseformatoption = $record;
    }




    /**
     * Get the image url associated with this tile_photo object.
     * @return bool|\moodle_url
     */
    public function get_image_url () {
        $config = self::file_api_params();
        if (!$this->filename) {
            return false;
        } else {
            return \moodle_url::make_pluginfile_url(
                $this->context->id,
                $config['component'],
                $config['filearea'],
                $this->sectionid,
                $config['filepath'],
                $this->filename,
                false
            )->out();
        }
    }

    /**
     * Given a course context id, section id and a filename, get the related photo file.
     * @param int $contextid the context id.
     * @param int $sectionid the section id.
     * @param string $filename the file name.
     * @return bool|\stored_file
     */
    public static function get_file_from_ids($contextid, $sectionid, $filename) {
        $fs = get_file_storage();
        $config = self::file_api_params();
        return $fs->get_file(
            $contextid,
            $config['component'],
            $config['filearea'],
            $sectionid,
            $config['filepath'],
            $filename
        );
    }

    /**
     * Get the image file associated with this tile_photo object.
     * @return bool|\stored_file
     */
    public function get_file() {
        if (!isset($this->file)) {
            $this->file = self::get_file_from_ids($this->context->id, $this->sectionid, $this->filename);
        }
        return $this->file;
    }

    /**
     * When course_section_deleted is trigger we remove related files.
     * @param int $courseid the course id.
     * @param int $sectionid the section id.
     * @return bool
     */
    public static function delete_file_from_ids($courseid, $sectionid) {
        $params = self::file_api_params();
        $fs = get_file_storage();
        $contextid = \context_course::instance($courseid)->id;
        if ($contextid) {
            return $fs->delete_area_files(
                $contextid,
                $params['component'],
                $params['filearea'],
                $sectionid
            );
        } else {
            return false;
        }
    }

    /**
     * Used if we already have a stored file that we want to set as the file for this object.
     * E.g. we are converting from Grid format and the file is already saved.
     * @param \stored_file $file
     * @throws \dml_exception
     * @throws \required_capability_exception
     */
    public function set_file($file) {
        $this->file = $file;
        $this->filename = $file->get_filename();
        $this->set_course_format_option();
    }

    /**
     * Handle an existing stored file (e.g. a user draft file or a file used in another course).
     * Scale the image to suit this plugin and then save it and update this object.
     * @param \stored_file $sourcefile
     * @param string $newfilename
     * @return bool|\stored_file
     * @throws \dml_exception
     * @throws \file_exception
     * @throws \moodle_exception
     * @throws \required_capability_exception
     * @throws \stored_file_creation_exception
     */
    public function set_file_from_stored_file($sourcefile, $newfilename) {
        if ($sourcefile) {
            if ($sourcefile->get_itemid() == $this->sectionid
                && $sourcefile->get_contextid() == $this->context->id
                && $sourcefile->get_filename() == $this->filename
                && $sourcefile->get_filepath() == self::file_api_params()['filepath']) {
                debugging("File is already set for this section");
                return false;
            }
            $sourceimageinfo = $sourcefile->get_imageinfo();
            $newwidth = self::get_max_image_width();

            // In case the new file has the same name as the old one, delete it early.
            // Otherwise we do it in a few lines' time when we know we have the new one.
            if ($this->filename == $sourcefile->get_filename()) {
                $this->delete_stored_file();
            }

            $newfile = image_processor::adjust_and_copy_file(
                $sourcefile,
                $newfilename,
                $this->context,
                $this->sectionid,
                $newwidth,
                $sourceimageinfo['height'] * $newwidth / $sourceimageinfo['width']
            );
            if ($newfile) {
                if ($this->filename != $sourcefile->get_filename()) {
                    // We didn't delete the file a few lines ago so do it now.
                    $this->delete_stored_file();
                }
                $this->set_file($newfile);
                return $newfile;
            } else {
                debugging('Failed to set file from details - filename ' . $newfilename);

                // Restore the original file name of the original file.
                debugging("New file could not be created");
                debugging($newfile);
                $this->get_file()->rename(self::file_api_params()['filepath'], $this->filename);
                return false;
            }
        } else {
            debugging('Failed to set file from details - filename ' . $newfilename);
            return false;
        }
    }

    /**
     * Check if the aspect ratio is a normal landscape one or not.
     * @return array message as to whether it is or not.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function verify_aspect_ratio() {
        $file = $this->get_file();
        if (!$file) {
            debugging("No stored file found");
            $this->clear();
            return array('status' => false);
        }

        $requiredratio = 0.666; // Landscape is 2:3 ratio height:width.
        // We allow 5% error without warning.
        // Beyond 5% we accept incorrect aspect ratios but warn the user.
        $tolerance = 0.05;

        $imageinfo = $file->get_imageinfo();
        $ratio = $imageinfo['height'] / $imageinfo['width'];
        $messageshort = get_string('imagesize', 'format_tiles') . ": ";
        if (abs($ratio - $requiredratio) > $tolerance) {
            if ($ratio > $requiredratio) {
                $tallorwide = array(
                    'tallorwide' => get_string('tootall', 'format_tiles')
                );
                $messageshort .= get_string('tootall', 'format_tiles');
            } else {
                $tallorwide = array(
                    'tallorwide' => get_string('toowide', 'format_tiles'),
                );
                $messageshort .= get_string('toowide', 'format_tiles');
            }
            return array(
                'status' => false,
                'message' => get_string(
                    'aspectratiotootallorwide',
                    'format_tiles',
                    $tallorwide
                ),
                'messageshort' => $messageshort
            );
        }
        $messageshort .= get_string('ok', 'format_tiles');
        return array('status' => true, 'message' => $messageshort, 'messageshort' => $messageshort);
    }

    /**
     * Clear the data associated with this tile_photo object.
     * @throws \dml_exception
     */
    public function clear() {
        global $DB;
        if (isset($this->courseid) && isset($this->sectionid)) {
            $DB->delete_records(
                'course_format_options',
                array(
                    'courseid' => $this->courseid,
                    'format' => 'tiles',
                    'sectionid' => $this->sectionid,
                    'name' => 'tilephoto'
                )
            );
        }
        $this->courseformatoption = null;
        $this->delete_stored_file();
    }

    /**
     * When a course is switched in to "Tiles" we may have Tiles images sitting in the database.
     * This would happen if the course was once in tiles but was switched to something else.
     * We delete them so that we can start again.
     * Really we should run this when we switch *out* of tiles too, as a clean up exercise (later release).
     * @param int $courseid the id for this course.
     * @return bool whether successful.
     */
    public static function delete_all_tile_photos_course($courseid) {
        $fs = get_file_storage();
        $fileapiparams = self::file_api_params();
        return $fs->delete_area_files(
            \context_course::instance($courseid)->id,
            $fileapiparams['component'],
            $fileapiparams['filearea']
        );
    }

    /**
     * Delete the file stored for this object from file storage, and from this object.
     * @return bool
     */
    private function delete_stored_file() {
        $file = $this->get_file();
        if ($file) {
            return $file->delete();
        } else {
            return true;
        }
    }

    /**
     * For a given course, find out the IDs of all tiles which have photos instead of icons.
     * @param int $courseid the course we are interested in.
     * @return array the array of relevant tile ids.
     * @throws \dml_exception
     */
    public static function get_photo_tile_ids($courseid) {
        global $DB;
        $records = $DB->get_records(
            'course_format_options',
            array('courseid' => $courseid, 'format' => 'tiles', 'name' => 'tilephoto'),
            'sectionid',
            'sectionid'
        );
        return(array_keys($records));
    }

    /**
     * Types of files that we allow to be uploaded as tile backgrounds.
     * @return array
     */
    public static function allowed_file_types() {
        return array('image/gif', 'image/jpeg', 'image/png');
    }

    /**
     * Verify a particular file against allowed types.
     * @param \stored_file $file the file to check
     * @return bool whether file type is allowed.
     */
    public static function verify_file_type($file) {
        $mime = $file->get_mimetype();
        if (array_search($mime, self::allowed_file_types()) === false) {
            debugging("File type not allowed " . $mime);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the most recent x number of photos ($maxnumberphotos) that I uploaded.
     * Or that exist in this course (even if someone else uploaded).
     * Ignore any more than a certain time old.  Used to populate my photo library.
     * @param int $contextid the id for this context.
     * @param int $maxnumberphotos how many to return maximum.
     * @return array details of photos incl filename and details for path to make URL.
     * @throws \dml_exception
     */
    public static function get_photo_library_photos($contextid, $maxnumberphotos = 20) {
        // Did not use (new \file_storage())->get_area_files() for this as it requires context id.
        // We want to filter by user id instead.
        global $DB, $USER;
        $params['contextid'] = $contextid;
        $params['userid'] = $USER->id;
        $params['cutofftime'] = strtotime("-12 months");
        $params['filesizecutoff'] = get_real_size("700K"); // Don't want to try to display really large draft files in library.
        $fileapiparams = self::file_api_params();
        $params['component'] = $fileapiparams['component'];
        $params['filearea'] = $fileapiparams['filearea'];
        $params['filepath'] = $fileapiparams['filepath'];

        $sql = "SELECT id, component, filearea, contextid, itemid, filepath, filename, filesize, mimetype
            FROM {files}
            WHERE component = :component AND filearea = :filearea AND (contextid = :contextid OR userid = :userid)
            AND filename != '.' AND filepath = :filepath
            AND timemodified > :cutofftime
            AND filesize < :filesizecutoff AND filesize > 0";

        try {
            $records = $DB->get_records_sql($sql, $params, 0, $maxnumberphotos);
        } catch (\Exception $ex) {
            debugging('Failed to run query to get files for library. ' . $ex->getMessage());
            $records = [];
        }

        // If the teacher has nothing in their library, add a sample image.
        if (count($records) == 0) {
            $params['contextid'] = \context_system::instance()->id;
            $sql = "SELECT id, component, filearea, contextid, itemid, filepath, filename, filesize, mimetype
            FROM {files}
            WHERE component = :component AND filearea = :filearea AND contextid = :contextid
            AND filename = 'sample_image.jpg'
            AND filepath = :filepath
            AND filesize > 0";
            $records = $DB->get_records_sql($sql, $params, 0, 1);
        }

        // Reduce to a set (ignore items with same filename/type and *roughly* same size as already existing).
        $set = [];
        $filesizetolerance = 2000; // If file size is within 2kb of another file, we treat that as same size.

        foreach ($records as $record) {
            $setkey = $record->filename . '|' . $record->mimetype;
            if (!isset($set[$setkey]) || abs($set[$setkey]->filesize - $record->filesize) > $filesizetolerance) {
                // Seems like we don't already have this file in the set - don't have to be precise here given purpose.
                unset($record->mimetype);  // Don't need to keep this.
                $set[$setkey] = $record;
            }
        }
        return array_values($set);
    }

    /**
     * When we store a new tile photo as a file, the config should we use for the Moodle File API.
     * @return array the config data.
     */
    public static function file_api_params() {
        return array(
            'component' => 'format_tiles',
            'filearea' => 'tilephoto',
            'filepath' => '/tilephoto/',
            'tempfilearea' => 'temptilephoto'
        );
    }

    /**
     * The maximum width of photos that we want to save (somewhat larger than tile size).
     * @return int
     */
    public static function get_max_image_width() {
        return 360;
    }

    /**
     * The sample image file in the database for this Moodle instance.
     * There is only one and it is shown to teacher as a sample if their library is empty.
     * @return bool|\stored_file
     * @throws \dml_exception
     */
    public static function get_sample_image_file() {
        return self::get_file_from_ids(\context_system::instance()->id, 0, 'sample_image.jpg');
    }
}

