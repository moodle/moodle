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
 * Icon set class for format tiles.
 * @package    format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk} in respect of modifications to format_grid versions by G J Barnard.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles;

defined('MOODLE_INTERNAL') || die();
global $CFG;

// PHP image processing library.
require_once($CFG->libdir . '/gdlib.php');

/**
 * Class image_processor
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk} in respect of modifications to format_grid versions by G J Barnard.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class image_processor
{

    /**
     * When the user uploads a new file, it is saved as tempfile which may be large.
     * This takes the temp file and adds an adjusted version to the tile_photo object.
     * @param \stored_file $tempfile
     * @param string $newfilename
     * @param \context_course $coursecontext
     * @param int $sectionid
     * @param int $width
     * @param int $height
     * @return \stored_file|bool
     * @throws \file_exception
     * @throws \required_capability_exception
     * @throws \stored_file_creation_exception
     */
    public static function adjust_and_copy_file($tempfile, $newfilename, $coursecontext, $sectionid, $width, $height) {
        require_capability('moodle/course:update', $coursecontext);

        $newfilename = str_replace(' ', '_', $newfilename);
        $storedfilerecord = self::stored_file_record($coursecontext->id, $sectionid, $newfilename);
        $fs = get_file_storage();
        try {
            // Ensure the right quality setting...
            $mime = $tempfile->get_mimetype();
            $storedfilerecord['mimetype'] = $mime;

            $tmproot = make_temp_directory('formattilesphoto');
            $tmpfilepath = $tmproot . '/' . $tempfile->get_contenthash();
            $tempfile->copy_content_to($tmpfilepath);

            $data = self::process_image($tmpfilepath, $width, $height, $mime);
            if (!empty($data)) {
                // If a file exists with the same details (e.g. teacher uploading new file with same name), delete it.
                $existingfile = $fs->get_file(
                    $storedfilerecord['contextid'],
                    $storedfilerecord['component'],
                    $storedfilerecord['filearea'],
                    $storedfilerecord['itemid'],
                    $storedfilerecord['filepath'],
                    $storedfilerecord['filename']
                );
                if ($existingfile) {
                    debugging('Deleted old photo' . $existingfile->get_id(), DEBUG_DEVELOPER);
                    $existingfile->delete();
                }
                // Create new file.
                $newfile = $fs->create_file_from_string($storedfilerecord, $data);
                unlink($tmpfilepath);

                unset($tempfile);
                return $newfile;
            } else {
                debugging('imagecannotbeused', 'format_tiles', DEBUG_DEVELOPER);
            }
        } catch (\Exception $e) {
            if (isset($tempfile)) {
                unset($tempfile);
            }
            debugging('Format tiles image exception:...', DEBUG_DEVELOPER);
            debugging($e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
        return false;
    }

    /**
     * For a given image, which may be very large, scale it so it's size is correct for use as a tile background.
     * This is based on generate_image() in format_grid.
     *
     * If the GD library has at least version 2 and PNG support is available, the returned data
     * is the content of a transparent PNG file containing the thumbnail. Otherwise, the function
     * returns contents of a JPEG file with black background containing the thumbnail.
     *
     * @param string $filepath the full path to the original image file
     * @param int $requestedwidth the width of the requested image.
     * @param int $requestedheight the height of the requested image.
     * @param string $mime The mime type.
     * @return string|bool false if a problem occurs or the image data.
     */
    private static function process_image($filepath, $requestedwidth, $requestedheight, $mime) {
        $imagecontainerbgcolour = array('r' => 255, 'g' => 255, 'b' => 255);
        if (empty($filepath) or empty($requestedwidth) or empty($requestedheight)) {
            return false;
        }

        $imageinfo = getimagesize($filepath);

        if (empty($imageinfo)) {
            return false;
        }

        $originalwidth = $imageinfo[0];
        $originalheight = $imageinfo[1];

        if (empty($originalwidth) or empty($originalheight)) {
            return false;
        }

        $original = imagecreatefromstring(file_get_contents($filepath));

        $imageparams = self::get_image_params($mime);

        if (function_exists('imagecreatetruecolor')) {
            $tempimage = imagecreatetruecolor($requestedwidth, $requestedheight);
            if ($imageparams['function'] === 'imagepng') {
                imagealphablending($tempimage, false);
                imagefill($tempimage, 0, 0, imagecolorallocatealpha(
                    $tempimage,
                    0,
                    0,
                    0,
                    127
                ));
                imagesavealpha($tempimage, true);
            } else if (array_search($imageparams['function'], array('imagejpeg', 'imagewebp', 'imagegif')) !== false) {
                imagealphablending($tempimage, false);
                imagefill(
                    $tempimage,
                    0,
                    0,
                    imagecolorallocate(
                        $tempimage,
                        $imagecontainerbgcolour['r'],
                        $imagecontainerbgcolour['g'],
                        $imagecontainerbgcolour['b']
                    )
                );
            }
        } else {
            $tempimage = imagecreate($requestedwidth, $requestedheight);
        }

        $finalimage = $tempimage;
        $ratio = min($requestedwidth / $originalwidth, $requestedheight / $originalheight);

        $targetwidth = floor($originalwidth * $ratio);
        $targetheight = floor($originalheight * $ratio);

        $dstx = floor(($requestedwidth - $targetwidth) / 2);
        $dsty = floor(($requestedheight - $targetheight) / 2);

        imagecopybicubic($finalimage, $original, $dstx, $dsty, 0, 0, $targetwidth, $targetheight, $originalwidth,
            $originalheight);

        ob_start();
        switch($imageparams['function']) {
            case 'imagejpeg':
                if (!imagejpeg($finalimage, null, $imageparams['quality'])) {
                    ob_end_clean();
                    return false;
                }
                break;
            case 'imagepng':
                if (!imagepng($finalimage, null, $imageparams['quality'], $imageparams['filters'])) {
                    ob_end_clean();
                    return false;
                }
                break;
            case 'imagegif':
                if (!imagegif($finalimage)) {
                    ob_end_clean();
                    return false;
                }
                break;
            case 'imagewebp':
                if (!imagewebp($finalimage, null, $imageparams['quality'])) {
                    ob_end_clean();
                    return false;
                }
                break;
            default:
                if (!$imageparams['function']($finalimage, null, $imageparams['quality'], $imageparams['filters'])) {
                    ob_end_clean();
                    return false;
                }
        }
        $data = ob_get_clean();

        imagedestroy($original);
        imagedestroy($finalimage);

        return $data;
    }

    /**
     * When we are storing a new image as a file for this object, the data we should use for the Moodle File API.
     * @param int $contextid the context id (course context) for the file.
     * @param int $sectionid the section id for the file.
     * @param string $filename the filename we are storing as
     * @return array the data to use with the API.
     */
    private static function stored_file_record($contextid, $sectionid, $filename) {
        global $USER;
        $created = time();
        return array_merge(
            tile_photo::file_api_params(),
            array(
                'contextid' => $contextid,
                'itemid' => $sectionid,
                'filename' => $filename,
                'timecreated' => $created,
                'timemodified' => $created,
                'userid' => $USER->id
            )
        );
    }

    /**
     * For a given image mimetype, get the parameters we need to process the image.
     * Also check that the gd function exists in this environment.
     * I.e. what gd function do we use and what quality level.
     * This is based on generate_image() in format_grid.
     * @param string $mime
     * @return array|bool
     */
    private static function get_image_params($mime) {
        switch ($mime) {
            case 'image/png':
                if (function_exists('imagepng')) {
                    return array(
                        'function' => 'imagepng',
                        'filters' => PNG_NO_FILTER,
                        'quality' => 1
                    );
                }
                break;
            case 'image/jpeg':
                if (function_exists('imagejpeg')) {
                    return array(
                        'function' => 'imagejpeg',
                        'filters' => null,
                        'quality' => 90
                    );
                }
                break;
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    return array(
                        'function' => 'imagewebp',
                        'filters' => null,
                        'quality' => 90
                    );
                }
                break;
            case 'image/gif':
                if (function_exists('imagegif')) {
                    return array(
                        'function' => 'imagegif',
                        'filters' => null,
                        'quality' => null
                    );
                }
                break;
            default:
                break;
        }
        debugging('Mime type \''.$mime.'\' is not supported as an image format. PNG, JPEG and GIF are supported. '
            . 'The GD PHP extension should be installed.');
        return false;
    }
}
