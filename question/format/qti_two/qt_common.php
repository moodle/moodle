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
 * This contains code common to mediagonal-modified questions.
 *
 * @package    qformat
 * @subpackage qti_two
 * @copyright  2005 brian@mediagonal.ch
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * gets a list of all the media files for the given course
 *
 * @param int courseid
 * @return array containing filenames
 * @calledfrom type/<typename>/editquestion.php
 */
function get_course_media_files($courseid) {
throw new coding_exception('qti_two not converted to new fil api yet, sorry');
    /*global $CFG;
    $images = null;

    make_upload_directory("$course->id");    // Just in case
    $coursefiles = get_directory_list("$CFG->dataroot/$courseid", $CFG->moddata);
    foreach ($coursefiles as $filename) {
        if (is_media_by_extension($filename)) {
            $images["$filename"] = $filename;
        }
    }
    return $images;*/
}

/**
 * determines whether or not a file is an image, based on the file extension
 *
 * @param string $file the filename
 * @return bool
 */
function is_image_by_extension($file) {
    $extensionsregex = '/\.(gif|jpg|jpeg|jpe|png|tif|tiff|bmp|xbm|rgb|svf)$/';
    if (preg_match($extensionsregex, $file)) {
        return true;
    }
    return false;
}


/**
 * determines whether or not a file is a media file, based on the file extension
 *
 * @param string $file the filename
 * @return bool
 */
function is_media_by_extension($file) {
    $extensionsregex = '/\.(gif|jpg|jpeg|jpe|png|tif|tiff|bmp|xbm|rgb|svf|swf|mov|mpg|mpeg|wmf|avi|mpe|flv|mp3|ra|ram)$/';
    if (preg_match($extensionsregex, $file)) {
        return true;
    }
    return false;
}

/**
 * determines whether or not a file is a multimedia file, based on the file extension
 *
 * @param string $file the filename
 * @return bool
 */
function is_multimedia_by_extension($file) {
    $extensionsregex = '/\.(swf|mov|mpg|mpeg|wmf|avi|mpe|flv)$/';
    if (preg_match($extensionsregex, $file)) {
        return true;
    }
    return false;
}

/**
 * determines whether or not a file is a multimedia file of a type php can get the dimension for, based on the file extension
 *
 * @param string $file the filename
 * @return bool
 */
function is_sizable_multimedia($file) {
    $extensionsregex = '/\.(swf)$/';
    if (preg_match($extensionsregex, $file)) {
        return true;
    }
    return false;
}

/**
 * creates a media tag to use for choice media
 *
 * @param string $file the filename
 * @param string $courseid the course id
 * @param string $alt to specify the alt tag
 * @return string either an image tag, or html for an embedded object
 */
function get_media_tag($file, $courseid = 0, $alt = 'media file', $width = 0, $height = 0) {
    global $CFG;

    // if it's a moodle library file, it will be served through file.php
    if (substr(strtolower($file), 0, 7) == 'http://') {
        $media = $file;
    } else if ($CFG->slasharguments) {        // Use this method if possible for better caching
        $media = "{$CFG->wwwroot}/file.php/$courseid/$file";
    } else {
        $media = "{$CFG->wwwroot}/file.php?file=/$courseid/$file";
    }

    $ismultimedia = false;
    if (!$isimage = is_image_by_extension($file)) {
           $ismultimedia = is_multimedia_by_extension($file);
    }

    // if there is no known width and height, try to get one
    if ($width == 0) {
         if ($isimage || is_sizable_multimedia($file)) {

         }

    }
    // create either an image link or a generic link.
    // if the moodle multimedia filter is turned on, it'll catch multimedia content in the generic link
    if (is_image_by_extension($file)) {
        return "<img src=\"$media\" alt=\"$alt\" width=\"$width\" height=\"$height\" />";
    }
    else {
        require_once("$CFG->dirroot/question/format/qti/custommediafilter.php");
        return custom_mediaplugin_filter('<a href="' . $media . '"></a>', $courseid, $width, $height);
    }
}

/**
 * determines the x and y size of the given file
 *
 * @param string $file the filename
 * @return array looks like array('x'=>171, 'y'=>323), or array('x'=>0, 'y'=>0) if size can't be determined
 */
function get_file_dimensions($file) {
    $imginfo = @getimagesize($file);
    if ($imginfo !== FALSE) {
        return array('x'=>$imginfo[0], 'y'=>$imginfo[1]);
    } else {
        return array('x'=> 0, 'y'=> 0);
    }
}


