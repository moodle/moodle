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
 * gdlib.php - Collection of routines in Moodle related to
 * processing images using GD
 *
 * @package   core
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 *
 * long description
 * @global object
 * @param object $dst_img
 * @param object $src_img
 * @param int $dst_x
 * @param int $dst_y
 * @param int $src_x
 * @param int $src_y
 * @param int $dst_w
 * @param int $dst_h
 * @param int $src_w
 * @param int $src_h
 * @return bool
 * @todo Finish documenting this function
 */
function ImageCopyBicubic($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {

    global $CFG;

    if (function_exists('ImageCopyResampled') and $CFG->gdversion >= 2) {
       return ImageCopyResampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y,
                                 $dst_w, $dst_h, $src_w, $src_h);
    }

    $totalcolors = imagecolorstotal($src_img);
    for ($i=0; $i<$totalcolors; $i++) {
        if ($colors = ImageColorsForIndex($src_img, $i)) {
            ImageColorAllocate($dst_img, $colors['red'], $colors['green'], $colors['blue']);
        }
    }

    $scaleX = ($src_w - 1) / $dst_w;
    $scaleY = ($src_h - 1) / $dst_h;

    $scaleX2 = $scaleX / 2.0;
    $scaleY2 = $scaleY / 2.0;

    for ($j = 0; $j < $dst_h; $j++) {
        $sY = $j * $scaleY;

        for ($i = 0; $i < $dst_w; $i++) {
            $sX = $i * $scaleX;

            $c1 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX,(int)$sY+$scaleY2));
            $c2 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX,(int)$sY));
            $c3 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX+$scaleX2,(int)$sY+$scaleY2));
            $c4 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX+$scaleX2,(int)$sY));

            $red = (int) (($c1['red'] + $c2['red'] + $c3['red'] + $c4['red']) / 4);
            $green = (int) (($c1['green'] + $c2['green'] + $c3['green'] + $c4['green']) / 4);
            $blue = (int) (($c1['blue'] + $c2['blue'] + $c3['blue'] + $c4['blue']) / 4);

            $color = ImageColorClosest ($dst_img, $red, $green, $blue);
            ImageSetPixel ($dst_img, $i + $dst_x, $j + $dst_y, $color);
        }
    }
}

/**
 * Stores optimised icon images in icon file area
 *
 * @param $context
 * @param component
 * @param $itemid
 * @param $originalfile
 * @return success
 */
function process_new_icon($context, $component, $filearea, $itemid, $originalfile) {
    global $CFG;

    if (empty($CFG->gdversion)) {
        return false;
    }

    if (!is_file($originalfile)) {
        return false;
    }

    $imageinfo = GetImageSize($originalfile);

    if (empty($imageinfo)) {
        return false;
    }

    $image = new stdClass();
    $image->width  = $imageinfo[0];
    $image->height = $imageinfo[1];
    $image->type   = $imageinfo[2];

    switch ($image->type) {
        case IMAGETYPE_GIF:
            if (function_exists('ImageCreateFromGIF')) {
                $im = ImageCreateFromGIF($originalfile);
            } else {
                debugging('GIF not supported on this server');
                return false;
            }
            break;
        case IMAGETYPE_JPEG:
            if (function_exists('ImageCreateFromJPEG')) {
                $im = ImageCreateFromJPEG($originalfile);
            } else {
                debugging('JPEG not supported on this server');
                return false;
            }
            break;
        case IMAGETYPE_PNG:
            if (function_exists('ImageCreateFromPNG')) {
                $im = ImageCreateFromPNG($originalfile);
            } else {
                debugging('PNG not supported on this server');
                return false;
            }
            break;
        default:
            return false;
    }

    if (function_exists('ImagePng')) {
        $imagefnc = 'ImagePng';
        $imageext = '.png';
        $filters = PNG_NO_FILTER;
        $quality = 1;
    } else if (function_exists('ImageJpeg')) {
        $imagefnc = 'ImageJpeg';
        $imageext = '.jpg';
        $filters = null; // not used
        $quality = 90;
    } else {
        debugging('Jpeg and png not supported on this server, please fix server configuration');
        return false;
    }

    if (function_exists('ImageCreateTrueColor') and $CFG->gdversion >= 2) {
        $im1 = ImageCreateTrueColor(100,100);
        $im2 = ImageCreateTrueColor(35,35);
        if ($image->type == IMAGETYPE_PNG and $imagefnc === 'ImagePng') {
            imagealphablending($im1, false);
            $color = imagecolorallocatealpha($im1, 0, 0,  0, 127);
            imagefill($im1, 0, 0,  $color);
            imagesavealpha($im1, true);
            imagealphablending($im2, false);
            $color = imagecolorallocatealpha($im2, 0, 0,  0, 127);
            imagefill($im2, 0, 0,  $color);
            imagesavealpha($im2, true);
        }
    } else {
        $im1 = ImageCreate(100,100);
        $im2 = ImageCreate(35,35);
    }

    $cx = $image->width / 2;
    $cy = $image->height / 2;

    if ($image->width < $image->height) {
        $half = floor($image->width / 2.0);
    } else {
        $half = floor($image->height / 2.0);
    }

    ImageCopyBicubic($im1, $im, 0, 0, $cx-$half, $cy-$half, 100, 100, $half*2, $half*2);
    ImageCopyBicubic($im2, $im, 0, 0, $cx-$half, $cy-$half, 35, 35, $half*2, $half*2);

    $fs = get_file_storage();

    $icon = array('contextid'=>$context->id, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>'/');

    ob_start();
    if (!$imagefnc($im1, NULL, $quality, $filters)) {
        // keep old icons
        ob_end_clean();
        return false;
    }
    $data = ob_get_clean();
    ImageDestroy($im1);
    $icon['filename'] = 'f1'.$imageext;
    $fs->delete_area_files($context->id, $component, $filearea, $itemid);
    $fs->create_file_from_string($icon, $data);

    ob_start();
    if (!$imagefnc($im2, NULL, $quality, $filters)) {
        ob_end_clean();
        $fs->delete_area_files($context->id, $component, $filearea, $itemid);
        return false;
    }
    $data = ob_get_clean();
    ImageDestroy($im2);
    $icon['filename'] = 'f2'.$imageext;
    $fs->create_file_from_string($icon, $data);

    return true;
}

