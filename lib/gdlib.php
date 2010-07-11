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
 * @package moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
function ImageCopyBicubic ($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {

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
 * Delete profile images associated with user or group
 *
 * @global object
 * @param int $id user or group id
 * @param string $dir type of entity - 'groups' or 'users'
 * @return boolean success
 */
function delete_profile_image($id, $dir='users') {
    global $CFG;

//TODO: deprecate

    require_once $CFG->libdir.'/filelib.php';
    $location = $CFG->dataroot .'/'. $dir .'/'. $id;

    if (file_exists($location)) {
        return fulldelete($location);
    }

    return true;
}

/**
 * Given an upload manager with the right settings, this function performs a virus scan, and then scales and crops
 * it and saves it in the right place to be a "user" or "group" image.
 *
 * @global object
 * @param int $id user or group id
 * @param string $dir type of entity - groups, user, ...
 * @return string $destination (profile image destination path) or false on error
 */
function create_profile_image_destination($id, $dir='user') {
    global $CFG;

//TODO: deprecate

    umask(0000);

    if (!file_exists($CFG->dataroot .'/'. $dir)) {
        if (!mkdir($CFG->dataroot .'/'. $dir, $CFG->directorypermissions)) {
            return false;
        }
    }

    if ($dir == 'user') {
        $destination = make_user_directory($id, true);
    } else {
        $destination = "$CFG->dataroot/$dir/$id";
    }

    if (!file_exists($destination)) {
        if (!make_upload_directory(str_replace($CFG->dataroot . '/', '', $destination))) {
            return false;
        }
    }
    return $destination;
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


    if (function_exists('ImageCreateTrueColor') and $CFG->gdversion >= 2) {
        $im1 = ImageCreateTrueColor(100,100);
        $im2 = ImageCreateTrueColor(35,35);
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

    if (!function_exists('ImageJpeg')) {
        debugging('Jpeg not supported on this server, please fix server configuration');
        return false;
    }

    $fs = get_file_storage();

    $icon = array('contextid'=>$context->id, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>'/');

    ob_start();
    if (!ImageJpeg($im1, NULL, 90)) {
        // keep old icons
        ob_end_clean();
        return false;
    }
    $data = ob_get_clean();
    ImageDestroy($im1);
    $icon['filename'] = 'f1.jpg';
    $fs->delete_area_files($context->id, $component, $filearea, $itemid);
    $fs->create_file_from_string($icon, $data);

    ob_start();
    if (!ImageJpeg($im2, NULL, 95)) {
        ob_end_clean();
        $fs->delete_area_files($context->id, $component, $filearea, $itemid);
        return false;
    }
    $data = ob_get_clean();
    ImageDestroy($im2);
    $icon['filename'] = 'f2.jpg';
    $fs->create_file_from_string($icon, $data);

    return true;
}

/**
 * Given an upload manager with the right settings, this function performs a virus scan, and then scales and crops
 * it and saves it in the right place to be a "user" or "group" image.
 *
 * @param int $id user or group id
 * @param object $userform with imagefile upload field
 * @param string $dir type of entity - groups, user, ...
 * @return boolean success
 */
function save_profile_image($id, $userform, $dir='user') {

//TODO: deprecate

    $destination = create_profile_image_destination($id, $dir);
    if ($destination === false) {
        return false;
    }

    $filename = $userform->get_new_filename('imagefile');
    $pathname = $destination.'/'.$filename;

    if (!$userform->save_file('imagefile', $pathname, true)) {
        return false;
    }

    return process_profile_image($pathname, $destination);
}

/**
 * Given a path to an image file this function scales and crops it and saves it in
 * the right place to be a "user" or "group" image.
 *
 * @global object
 * @param string $originalfile the path of the original image file
 * @param string $destination the final destination directory of the profile image
 * @return boolean
 */
function process_profile_image($originalfile, $destination) {
    global $CFG, $OUTPUT;

//TODO: deprecate

    if(!(is_file($originalfile) && is_dir($destination))) {
        return false;
    }

    if (empty($CFG->gdversion)) {
        return false;
    }

    $imageinfo = GetImageSize($originalfile);

    if (empty($imageinfo)) {
        if (file_exists($originalfile)) {
            unlink($originalfile);
        }
        return false;
    }

    $image->width  = $imageinfo[0];
    $image->height = $imageinfo[1];
    $image->type   = $imageinfo[2];

    switch ($image->type) {
        case IMAGETYPE_GIF:
            if (function_exists('ImageCreateFromGIF')) {
                $im = ImageCreateFromGIF($originalfile);
            } else {
                notice('GIF not supported on this server');
                unlink($originalfile);
                return false;
            }
            break;
        case IMAGETYPE_JPEG:
            if (function_exists('ImageCreateFromJPEG')) {
                $im = ImageCreateFromJPEG($originalfile);
            } else {
                notice('JPEG not supported on this server');
                unlink($originalfile);
                return false;
            }
            break;
        case IMAGETYPE_PNG:
            if (function_exists('ImageCreateFromPNG')) {
                $im = ImageCreateFromPNG($originalfile);
            } else {
                notice('PNG not supported on this server');
                unlink($originalfile);
                return false;
            }
            break;
        default:
            unlink($originalfile);
            return false;
    }

    unlink($originalfile);

    if (function_exists('ImageCreateTrueColor') and $CFG->gdversion >= 2) {
        $im1 = ImageCreateTrueColor(100,100);
        $im2 = ImageCreateTrueColor(35,35);
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

    if (function_exists('ImageJpeg')) {
        @touch($destination .'/f1.jpg');  // Helps in Safe mode
        @touch($destination .'/f2.jpg');  // Helps in Safe mode
        if (ImageJpeg($im1, $destination .'/f1.jpg', 90) and
            ImageJpeg($im2, $destination .'/f2.jpg', 95) ) {
            @chmod($destination .'/f1.jpg', 0666);
            @chmod($destination .'/f2.jpg', 0666);
            return 1;
        }
    } else {
        echo $OUTPUT->notification('PHP has not been configured to support JPEG images.  Please correct this.');
    }
    return 0;
}

/**
 * Given a user id this function scales and crops the user images to remove
 * the one pixel black border.
 *
 * @global object
 * @param int $id
 * @param string $dir
 * @return boolean
 */
function upgrade_profile_image($id, $dir='users') {
    global $CFG, $OUTPUT;

//TODO: deprecate

    $im = ImageCreateFromJPEG($CFG->dataroot .'/'. $dir .'/'. $id .'/f1.jpg');

    if (function_exists('ImageCreateTrueColor') and $CFG->gdversion >= 2) {
        $im1 = ImageCreateTrueColor(100,100);
        $im2 = ImageCreateTrueColor(35,35);
    } else {
        $im1 = ImageCreate(100,100);
        $im2 = ImageCreate(35,35);
    }

    if (function_exists('ImageCopyResampled') and $CFG->gdversion >= 2) {
        ImageCopyBicubic($im1, $im, 0, 0, 2, 2, 100, 100, 96, 96);
    } else {
        imagecopy($im1, $im, 0, 0, 0, 0, 100, 100);
                $c = ImageColorsForIndex($im1,ImageColorAt($im1,2,2));
                $color = ImageColorClosest ($im1, $c['red'], $c['green'], $c['blue']);
                ImageSetPixel ($im1, 0, 0, $color);
                $c = ImageColorsForIndex($im1,ImageColorAt($im1,2,97));
                $color = ImageColorClosest ($im1, $c['red'], $c['green'], $c['blue']);
                ImageSetPixel ($im1, 0, 99, $color);
                $c = ImageColorsForIndex($im1,ImageColorAt($im1,97,2));
                $color = ImageColorClosest ($im1, $c['red'], $c['green'], $c['blue']);
                ImageSetPixel ($im1, 99, 0, $color);
                $c = ImageColorsForIndex($im1,ImageColorAt($im1,97,97));
                $color = ImageColorClosest ($im1, $c['red'], $c['green'], $c['blue']);
                ImageSetPixel ($im1, 99, 99, $color);
        for ($x = 1; $x < 99; $x++) {
                $c1 = ImageColorsForIndex($im1,ImageColorAt($im,$x,1));
                $color = ImageColorClosest ($im, $c1['red'], $c1['green'], $c1['blue']);
                ImageSetPixel ($im1, $x, 0, $color);
                $c2 = ImageColorsForIndex($im1,ImageColorAt($im1,$x,98));
                $color = ImageColorClosest ($im, $c2['red'], $c2['green'], $c2['blue']);
                ImageSetPixel ($im1, $x, 99, $color);
        }
        for ($y = 1; $y < 99; $y++) {
                $c3 = ImageColorsForIndex($im1,ImageColorAt($im, 1, $y));
                $color = ImageColorClosest ($im, $c3['red'], $c3['green'], $c3['blue']);
                ImageSetPixel ($im1, 0, $y, $color);
                $c4 = ImageColorsForIndex($im1,ImageColorAt($im1, 98, $y));
                $color = ImageColorClosest ($im, $c4['red'], $c4['green'], $c4['blue']);
                ImageSetPixel ($im1, 99, $y, $color);
        }
    }
    ImageCopyBicubic($im2, $im, 0, 0, 2, 2, 35, 35, 96, 96);

    if (function_exists('ImageJpeg')) {
        if (ImageJpeg($im1, $CFG->dataroot .'/'. $dir .'/'. $id .'/f1.jpg', 90) and
            ImageJpeg($im2, $CFG->dataroot .'/'. $dir .'/'. $id .'/f2.jpg', 95) ) {
            @chmod($CFG->dataroot .'/'. $dir .'/'. $id .'/f1.jpg', 0666);
            @chmod($CFG->dataroot .'/'. $dir .'/'. $id .'/f2.jpg', 0666);
            return 1;
        }
    } else {
        echo $OUTPUT->notification('PHP has not been configured to support JPEG images.  Please correct this.');
    }
    return 0;
}
