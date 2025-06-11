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

namespace theme_snap;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/gdlib.php');

/**
 * Image processing.
 *
 * Provides image resizing functionality.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class image {
    /**
     * Shame that this was nicked from gdlib.php and that there isn't a function I could have used from there.
     * Creates a resized version of image and stores copy in file area
     *
     * @param context $context
     * @param string $component
     * @param string filearea
     * @param int $itemid
     * @param stored_file $originalfile
     * @param int $newwidth;
     * @param int $newheight;
     * @return stored_file
     */
    public static function resize (
        \stored_file $originalfile,
        $resizefilename = false,
        $newwidth = false,
        $newheight = false,
        $jpgquality = 90
    ) {

        if ($resizefilename === false) {
            $resizefilename = $originalfile->get_filename();
        }

        if (!$newwidth && !$newheight) {
            return false;
        }

        $contextid = $originalfile->get_contextid();
        $component = $originalfile->get_component();
        $filearea = $originalfile->get_filearea();
        $itemid = $originalfile->get_itemid();

        $imageinfo = (object) $originalfile->get_imageinfo();
        $imagefnc = '';
        if (empty($imageinfo)) {
            return false;
        }

        // Create temporary image for processing.
        $tmpimage = tempnam(sys_get_temp_dir(), 'tmpimg');
        \file_put_contents($tmpimage, $originalfile->get_content());

        if (!$newheight && (isset($imageinfo->height) && isset($imageinfo->width))) {
            $m = $imageinfo->height / $imageinfo->width; // Multiplier to work out $newheight.
            $newheight = round($newwidth * $m);
        } else if (!$newwidth && (isset($imageinfo->height) && isset($imageinfo->width))) {
            $m = $imageinfo->width / $imageinfo->height; // Multiplier to work out $newwidth.
            $newwidth = round($newheight * $m);
        }
        $t = null;

        if (!isset($imageinfo->mimetype)) {
            unlink ($tmpimage);
            return false;
        }
        switch ($imageinfo->mimetype) {
            case 'image/gif':
                if (\function_exists('imagecreatefromgif')) {
                    $im = \imagecreatefromgif($tmpimage);
                } else {
                    \debugging('GIF not supported on this server');
                    unlink ($tmpimage);
                    return false;
                }
                // Guess transparent colour from GIF.
                $transparent = \imagecolortransparent($im);
                if ($transparent != -1) {
                    $t = \imagecolorsforindex($im, $transparent);
                }
                break;
            case 'image/jpeg':
                if (\function_exists('imagecreatefromjpeg')) {
                    $im = \imagecreatefromjpeg($tmpimage);
                } else {
                    \debugging('JPEG not supported on this server');
                    unlink ($tmpimage);
                    return false;
                }
                // If the user uploads a jpeg them we should process as a jpeg if possible.
                if (\function_exists('imagejpeg')) {
                    $imagefnc = 'imagejpeg';
                    $filters = null; // Not used.
                    $quality = $jpgquality;
                } else if (\function_exists('imagepng')) {
                    $imagefnc = 'imagepng';
                    $filters = PNG_NO_FILTER;
                    $quality = 1;
                } else {
                    \debugging('Jpeg and png not supported on this server, please fix server configuration');
                    unlink ($tmpimage);
                    return false;
                }
                break;
            case 'image/png':
                if (\function_exists('imagecreatefrompng')) {
                    $im = \imagecreatefrompng($tmpimage);
                } else {
                    \debugging('PNG not supported on this server');
                    unlink ($tmpimage);
                    return false;
                }
                break;
            default:
                unlink ($tmpimage);
                return false;
        }
        unlink ($tmpimage);

        // The default for all images other than jpegs is to try imagepng first.
        if (empty($imagefnc)) {
            if (\function_exists('imagepng')) {
                $imagefnc = 'imagepng';
                $filters = PNG_NO_FILTER;
                $quality = 1;
            } else if (\function_exists('imagejpeg')) {
                $imagefnc = 'imagejpeg';
                $filters = null; // Not used.
                $quality = $jpgquality;
            } else {
                \debugging('Jpeg and png not supported on this server, please fix server configuration');
                return false;
            }
        }

        if (\function_exists('imagecreatetruecolor')) {
            $newimage = \imagecreatetruecolor($newwidth, $newheight);
            if ($imageinfo->mimetype != 'image/jpeg' && $imagefnc === 'imagepng') {
                if ($t) {
                    // Transparent GIF hacking...
                    $transparentcolour = \imagecolorallocate($newimage , $t['red'] , $t['green'] , $t['blue']);
                    \imagecolortransparent($newimage , $transparentcolour);
                }

                \imagealphablending($newimage, false);
                $color = \imagecolorallocatealpha($newimage, 0, 0,  0, 127);
                \imagefill($newimage, 0, 0,  $color);
                \imagesavealpha($newimage, true);

            }
        } else {
            $newimage = \imagecreate($newwidth, $newheight);
        }

        \imagecopybicubic($newimage, $im, 0, 0, 0, 0, $newwidth, $newheight, $imageinfo->width, $imageinfo->height);

        $fs = \get_file_storage();
        $newimageparams = array(
            'contextid' => $contextid,
            'component' => $component,
            'filearea' => $filearea,
            'itemid' => $itemid,
            'filepath' => '/',
        );

        \ob_start();
        if ($imagefnc == 'imagejpeg') {
            if (!$imagefnc($newimage, null, $quality)) {
                return false;
            }
        } else {
            if (!$imagefnc($newimage, null, $quality, $filters)) {
                return false;
            }
        }

        $data = \ob_get_clean();
        \imagedestroy($newimage);
        $newimageparams['filename'] = $resizefilename;
        if ($resizefilename == $originalfile->get_filename()) {
            $originalfile->delete();
        }
        $file1 = $fs->create_file_from_string($newimageparams, $data);
        return $file1;
    }
}
