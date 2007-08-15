<?php

/**
 * gdlib.php - Collection of routines in Moodle related to
 * processing images using GD
 *
 * @author ?
 * @version  $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/**
 * short description (optional)
 *
 * long description
 * @uses $CFG
 * @param type? $dst_img description?
 * @param type? $src_img description?
 * @param type? $dst_x description?
 * @param type? $dst_y description?
 * @param type? $src_x description?
 * @param type? $src_y description?
 * @param type? $dst_w description?
 * @param type? $dst_h description?
 * @param type? $src_w description?
 * @param type? $src_h description?
 * @return ?
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
 * @param int $id user or group id
 * @param string $dir type of entity - 'groups' or 'users'
 * @return boolean success
 */
function delete_profile_image($id, $dir='users') {
    global $CFG;

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
 * @param int $id user or group id
 * @param object $uploadmanager object referencing the image
 * @param string $dir type of entity - groups, users, ...
 * @return boolean success
 */
function save_profile_image($id, $uploadmanager, $dir='users') {
//

    global $CFG;

    if (empty($CFG->gdversion)) {
        return false;
    }

    if (!$uploadmanager) {
        return false;
    }

    umask(0000);

    if (!file_exists($CFG->dataroot .'/'. $dir)) {
        if (! mkdir($CFG->dataroot .'/'. $dir, $CFG->directorypermissions)) {
            return false;
        }
    }

    if (!file_exists($CFG->dataroot .'/'. $dir .'/'. $id)) {
        if (! mkdir($CFG->dataroot .'/'. $dir .'/'. $id, $CFG->directorypermissions)) {
            return false;
        }
    }

    $destination = $CFG->dataroot .'/'. $dir .'/'. $id;
    if (!$uploadmanager->save_files($destination)) {
        return false;
    }

    $originalfile = $uploadmanager->get_new_filepath();

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
        case 1:
            if (function_exists('ImageCreateFromGIF')) {
                $im = ImageCreateFromGIF($originalfile);
            } else {
                notice('GIF not supported on this server');
                unlink($originalfile);
                return false;
            }
            break;
        case 2:
            if (function_exists('ImageCreateFromJPEG')) {
                $im = ImageCreateFromJPEG($originalfile);
            } else {
                notice('JPEG not supported on this server');
                unlink($originalfile);
                return false;
            }
            break;
        case 3:
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
        @touch($CFG->dataroot .'/'. $dir .'/'. $id .'/f1.jpg');  // Helps in Safe mode
        @touch($CFG->dataroot .'/'. $dir .'/'. $id .'/f2.jpg');  // Helps in Safe mode
        if (ImageJpeg($im1, $CFG->dataroot .'/'. $dir .'/'. $id .'/f1.jpg', 90) and
            ImageJpeg($im2, $CFG->dataroot .'/'. $dir .'/'. $id .'/f2.jpg', 95) ) {
            @chmod($CFG->dataroot .'/'. $dir .'/'. $id .'/f1.jpg', 0666);
            @chmod($CFG->dataroot .'/'. $dir .'/'. $id .'/f2.jpg', 0666);
            return 1;
        }
    } else {
        notify('PHP has not been configured to support JPEG images.  Please correct this.');
    }
    return 0;
}

/**
 * Given a user id this function scales and crops the user images to remove
 * the one pixel black border.
 *
 * @uses $CFG
 * @param int $id description?
 * @return boolean
 */
function upgrade_profile_image($id, $dir='users') {
    global $CFG;

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
        notify('PHP has not been configured to support JPEG images.  Please correct this.');
    }
    return 0;
}
?>
