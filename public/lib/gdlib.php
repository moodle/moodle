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
 * Stores optimised icon images in icon file area.
 *
 * Since 2.9 this function will generate an icon in the same format as the original file when possible.
 * To counter that behaviour, you can use the argument $preferpng to generate a PNG icon.
 *
 * @param context $context
 * @param string $component
 * @param string filearea
 * @param int $itemid
 * @param string $originalfile
 * @param boolean $preferpng When true, it will try to generate a PNG file regardless of the original file.
 * @return mixed new unique revision number or false if not saved
 */
function process_new_icon($context, $component, $filearea, $itemid, $originalfile, $preferpng = false) {
    global $CFG;

    if (!is_file($originalfile)) {
        return false;
    }

    $imageinfo = getimagesize($originalfile);
    $imagefnc = '';

    if (empty($imageinfo)) {
        return false;
    }

    $image = new stdClass();
    $image->width  = $imageinfo[0];
    $image->height = $imageinfo[1];
    $image->type   = $imageinfo[2];

    $t = null;
    switch ($image->type) {
        case IMAGETYPE_GIF:
            if (function_exists('imagecreatefromgif')) {
                $im = imagecreatefromgif($originalfile);
            } else {
                debugging('GIF not supported on this server');
                return false;
            }
            // Guess transparent colour from GIF.
            $transparent = imagecolortransparent($im);
            if ($transparent != -1) {
                $t = imagecolorsforindex($im, $transparent);
            }
            break;
        case IMAGETYPE_JPEG:
            if (function_exists('imagecreatefromjpeg')) {
                $im = imagecreatefromjpeg($originalfile);
            } else {
                debugging('JPEG not supported on this server');
                return false;
            }
            // If the user uploads a jpeg them we should process as a jpeg if possible.
            if (!$preferpng && function_exists('imagejpeg')) {
                $imagefnc = 'imagejpeg';
                $imageext = '.jpg';
                $filters = null; // Not used.
                $quality = 90;
            }
            break;
        case IMAGETYPE_PNG:
            if (function_exists('imagecreatefrompng')) {
                $im = imagecreatefrompng($originalfile);
            } else {
                debugging('PNG not supported on this server');
                return false;
            }
            break;
        default:
            return false;
    }

    // The conversion has not been decided yet, let's apply defaults (png with fallback to jpg).
    if (empty($imagefnc)) {
        if (function_exists('imagepng')) {
            $imagefnc = 'imagepng';
            $imageext = '.png';
            $filters = PNG_NO_FILTER;
            $quality = 1;
        } else if (function_exists('imagejpeg')) {
            $imagefnc = 'imagejpeg';
            $imageext = '.jpg';
            $filters = null; // Not used.
            $quality = 90;
        } else {
            debugging('Jpeg and png not supported on this server, please fix server configuration');
            return false;
        }
    }

    if (function_exists('imagecreatetruecolor')) {
        $im1 = imagecreatetruecolor(100, 100);
        $im2 = imagecreatetruecolor(35, 35);
        $im3 = imagecreatetruecolor(512, 512);
        if ($image->type != IMAGETYPE_JPEG and $imagefnc === 'imagepng') {
            if ($t) {
                // Transparent GIF hacking...
                $transparentcolour = imagecolorallocate($im1 , $t['red'] , $t['green'] , $t['blue']);
                imagecolortransparent($im1 , $transparentcolour);
                $transparentcolour = imagecolorallocate($im2 , $t['red'] , $t['green'] , $t['blue']);
                imagecolortransparent($im2 , $transparentcolour);
                $transparentcolour = imagecolorallocate($im3 , $t['red'] , $t['green'] , $t['blue']);
                imagecolortransparent($im3 , $transparentcolour);
            }

            imagealphablending($im1, false);
            $color = imagecolorallocatealpha($im1, 0, 0,  0, 127);
            imagefill($im1, 0, 0,  $color);
            imagesavealpha($im1, true);

            imagealphablending($im2, false);
            $color = imagecolorallocatealpha($im2, 0, 0,  0, 127);
            imagefill($im2, 0, 0,  $color);
            imagesavealpha($im2, true);

            imagealphablending($im3, false);
            $color = imagecolorallocatealpha($im3, 0, 0,  0, 127);
            imagefill($im3, 0, 0,  $color);
            imagesavealpha($im3, true);
        }
    } else {
        $im1 = imagecreate(100, 100);
        $im2 = imagecreate(35, 35);
        $im3 = imagecreate(512, 512);
    }

    $cx = floor($image->width / 2);
    $cy = floor($image->height / 2);

    if ($image->width < $image->height) {
        $half = floor($image->width / 2.0);
    } else {
        $half = floor($image->height / 2.0);
    }

    imagecopyresampled($im1, $im, 0, 0, $cx - $half, $cy - $half, 100, 100, $half * 2, $half * 2);
    imagecopyresampled($im2, $im, 0, 0, $cx - $half, $cy - $half, 35, 35, $half * 2, $half * 2);
    imagecopyresampled($im3, $im, 0, 0, $cx - $half, $cy - $half, 512, 512, $half * 2, $half * 2);

    $fs = get_file_storage();

    $icon = array('contextid'=>$context->id, 'component'=>$component, 'filearea'=>$filearea, 'itemid'=>$itemid, 'filepath'=>'/');

    if ($imagefnc === 'imagejpeg') {
        // Function imagejpeg() accepts less arguments than imagepng() but we need to make $imagefnc accept the same
        // number of arguments, otherwise PHP8 throws an error.
        $imagefnc = function($image, $filename, $quality, $unused) {
            return imagejpeg($image, $filename, $quality);
        };
    }

    ob_start();
    if (!$imagefnc($im1, NULL, $quality, $filters)) {
        // keep old icons
        ob_end_clean();
        return false;
    }
    $data = ob_get_clean();
    imagedestroy($im1);
    $icon['filename'] = 'f1'.$imageext;
    $fs->delete_area_files($context->id, $component, $filearea, $itemid);
    $file1 = $fs->create_file_from_string($icon, $data);

    ob_start();
    if (!$imagefnc($im2, NULL, $quality, $filters)) {
        ob_end_clean();
        $fs->delete_area_files($context->id, $component, $filearea, $itemid);
        return false;
    }
    $data = ob_get_clean();
    imagedestroy($im2);
    $icon['filename'] = 'f2'.$imageext;
    $fs->create_file_from_string($icon, $data);

    ob_start();
    if (!$imagefnc($im3, NULL, $quality, $filters)) {
        ob_end_clean();
        $fs->delete_area_files($context->id, $component, $filearea, $itemid);
        return false;
    }
    $data = ob_get_clean();
    imagedestroy($im3);
    $icon['filename'] = 'f3'.$imageext;
    $fs->create_file_from_string($icon, $data);

    return $file1->get_id();
}


/**
 * Resize an image from an image path.
 *
 * This maintains the aspect ratio of the image.
 * This will not enlarge the image.
 *
 * @param string $filepath The full path to the original image file.
 * @param int|null $width The max width of the resized image, or null to only use the height.
 * @param int|null $height The max height of the resized image, or null to only use the width.
 * @param bool $forcecanvas Whether the final dimensions should be set to $width and $height.
 * @return string|bool False if a problem occurs, else the resized image data.
 */
function resize_image($filepath, $width, $height, $forcecanvas = false) {
    if (empty($filepath)) {
        return false;
    }

    // Fetch the image information for this image.
    $imageinfo = @getimagesize($filepath);
    if (empty($imageinfo)) {
        return false;
    }

    // Create a new image from the file.
    $original = @imagecreatefromstring(file_get_contents($filepath));

    // Generate the thumbnail.
    return resize_image_from_image($original, $imageinfo, $width, $height, $forcecanvas);
}

/**
 * Resize an image from an image object.
 *
 * @param resource|\GdImage $original The image to work on.
 * @param array $imageinfo Contains [0] => originalwidth, [1] => originalheight.
 * @param int|null $width The max width of the resized image, or null to only use the height.
 * @param int|null $height The max height of the resized image, or null to only use the width.
 * @param bool $forcecanvas Whether the final dimensions should be set to $width and $height.
 * @return string|bool False if a problem occurs, else the resized image data.
 */
function resize_image_from_image($original, $imageinfo, $width, $height, $forcecanvas = false) {
    global $CFG;

    if (empty($width) && empty($height) || ($forcecanvas && (empty($width) || empty($height)))) {
        // We need do not have the required ddimensions to work with.
        return false;
    }

    if (empty($imageinfo)) {
        return false;
    }

    $originalwidth  = $imageinfo[0];
    $originalheight = $imageinfo[1];
    if (empty($originalwidth) or empty($originalheight)) {
        return false;
    }

    if (function_exists('imagepng')) {
        $imagefnc = 'imagepng';
        $filters = PNG_NO_FILTER;
        $quality = 1;
    } else if (function_exists('imagejpeg')) {
        $imagefnc = 'imagejpeg';
        $filters = null;
        $quality = 90;
    } else {
        debugging('Neither JPEG nor PNG are supported at this server, please fix the system configuration.');
        return false;
    }

    if (empty($height)) {
        $ratio = $width / $originalwidth;
    } else if (empty($width)) {
        $ratio = $height / $originalheight;
    } else {
        $ratio = min($width / $originalwidth, $height / $originalheight);
    }

    if ($ratio < 1) {
        $targetwidth    = round($originalwidth * $ratio);
        $targetheight   = round($originalheight * $ratio);
    } else {
        // Do not enlarge the original file if it is smaller than the requested thumbnail size.
        $targetwidth    = $originalwidth;
        $targetheight   = $originalheight;
    }

    $canvaswidth = $targetwidth;
    $canvasheight = $targetheight;
    $dstx = 0;
    $dsty = 0;

    if ($forcecanvas) {
        $canvaswidth = $width;
        $canvasheight = $height;
        $dstx = floor(($width - $targetwidth) / 2);
        $dsty = floor(($height - $targetheight) / 2);
    }

    if (function_exists('imagecreatetruecolor')) {
        $newimage = imagecreatetruecolor($canvaswidth, $canvasheight);
        if ($imagefnc === 'imagepng') {
            imagealphablending($newimage, false);
            imagefill($newimage, 0, 0, imagecolorallocatealpha($newimage, 0, 0, 0, 127));
            imagesavealpha($newimage, true);
        }
    } else {
        $newimage = imagecreate($canvaswidth, $canvasheight);
    }

    imagecopyresampled($newimage, $original, $dstx, $dsty, 0, 0, $targetwidth, $targetheight, $originalwidth, $originalheight);

    if ($imagefnc === 'imagejpeg') {
        // Function imagejpeg() accepts less arguments than imagepng() but we need to make $imagefnc accept the same
        // number of arguments, otherwise PHP8 throws an error.
        $imagefnc = function($image, $filename, $quality, $unused) {
            return imagejpeg($image, $filename, $quality);
        };
    }

    // Capture the image as a string object, rather than straight to file.
    ob_start();
    if (!$imagefnc($newimage, null, $quality, $filters)) {
        ob_end_clean();
        return false;
    }
    $data = ob_get_clean();
    imagedestroy($original);
    imagedestroy($newimage);

    return $data;
}

/**
 * Generates a thumbnail for the given image
 *
 * If the GD library has at least version 2 and PNG support is available, the returned data
 * is the content of a transparent PNG file containing the thumbnail. Otherwise, the function
 * returns contents of a JPEG file with black background containing the thumbnail.
 *
 * @param string $filepath the full path to the original image file
 * @param int $width the width of the requested thumbnail
 * @param int $height the height of the requested thumbnail
 * @return string|bool false if a problem occurs, the thumbnail image data otherwise
 */
function generate_image_thumbnail($filepath, $width, $height) {
    return resize_image($filepath, $width, $height, true);
}

/**
 * Generates a thumbnail for the given image string.
 *
 * If the GD library has at least version 2 and PNG support is available, the returned data
 * is the content of a transparent PNG file containing the thumbnail. Otherwise, the function
 * returns contents of a JPEG file with black background containing the thumbnail.
 *
 * @param   string $filedata The image content as a string
 * @param   int $width the width of the requested thumbnail
 * @param   int $height the height of the requested thumbnail
 * @return  string|bool false if a problem occurs, the thumbnail image data otherwise
 */
function generate_image_thumbnail_from_string($filedata, $width, $height) {
    if (empty($filedata) or empty($width) or empty($height)) {
        return false;
    }

    // Fetch the image information for this image.
    $imageinfo = @getimagesizefromstring($filedata);
    if (empty($imageinfo)) {
        return false;
    }

    // Create a new image from the file.
    $original = @imagecreatefromstring($filedata);

    // Generate the thumbnail.
    return generate_image_thumbnail_from_image($original, $imageinfo, $width, $height);
}

/**
 * Generates a thumbnail for the given image string.
 *
 * If the GD library has at least version 2 and PNG support is available, the returned data
 * is the content of a transparent PNG file containing the thumbnail. Otherwise, the function
 * returns contents of a JPEG file with black background containing the thumbnail.
 *
 * @param   resource $original The image to work on.
 * @param   array $imageinfo Contains [0] => originalwidth, [1] => originalheight.
 * @param   int $width The width of the requested thumbnail.
 * @param   int $height The height of the requested thumbnail.
 * @return  string|bool False if a problem occurs, the thumbnail image data otherwise.
 */
function generate_image_thumbnail_from_image($original, $imageinfo, $width, $height) {
    return resize_image_from_image($original, $imageinfo, $width, $height, true);
}
