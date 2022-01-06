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
 * This file contains the editor class for the assignfeedback_editpdfplus plugin
 * 
 * This class performs crud operations on stamp using font FontAwesome.
 *
 * No capability checks are done - they should be done by the calling class.
 * 
 * Inspired by https://github.com/exiang/php-font-awesome-to-png
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2018 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdfplus;

use \assignfeedback_editpdfplus\utils_color;

class utils_stamp {

    /** Path of fontawesome font scss */
    const FA_SCSS_PATH = "/mod/assign/feedback/editpdfplus/fonts/fa/_variables.scss";

    /** Path of fontawesome font */
    const FA_PATH = '/lib/fonts/fontawesome-webfont.ttf';

    /** File area for the stamps */
    const STAMPS_FILEAREA = '/assignfeedback_editpdfplus/stamps/';

    /** Number of months when the stamps should be renewed */
    const REFRESH_TIME = 1;

    /**
     * Convert a FontAwesome icon into a PNG file
     * Main method 
     * 
     * @param String $iconName The name of the FontAwesome icon, for example if you need fa-user, give "user"
     * @param String $color The color in which you want to display the PNG, default black
     * @param type $fontSize The size of the font in which you want to display the PNG, default 48
     * @return String The path of the PNG file generate, or null if the convertion failed
     */
    public static function getPngFromFont($iconName, $color = "black", $fontSize = 48) {
        global $CFG;

        //get hexa code from the key iconName
        $charFAHexaCode = self::getUniCode($iconName);
        //get RGB color
        $colorRGB = utils_color::hex2RGB($color);
        if (!$charFAHexaCode || !$colorRGB) {
            return null;
        }

        //create final file
        $outputDirectory = $CFG->tempdir . self::STAMPS_FILEAREA;
        $fileName = sprintf("%s%s_%s-%s-%s_%s.png", $outputDirectory, $iconName, $colorRGB['red'], $colorRGB['green'], $colorRGB['blue'], $fontSize);

        $dirPath = dirname($fileName);
        if (!is_dir($dirPath) || !file_exists($dirPath)) {
            //try to create the directory
            self::mkdirRecursive($dirPath, 0777);
        } else if (file_exists($fileName)) {
            //check if the file is not too old
            $lastDateRefresh = mktime(0, 0, 0, date("m") - self::REFRESH_TIME, date("d"), date("Y"));
            $dateCreation = filemtime($fileName);
            if ($lastDateRefresh < $dateCreation) {
                return $fileName;
            }
        }

        //put image into file and return
        return self::putIconIntoFile($charFAHexaCode, $fileName, $colorRGB, $fontSize);
    }

    /**
     * Create a final directory with its path
     * 
     * @param String $pathname Path to final directory to create
     * @param String $mode Linux right
     * @return Boolean If directory is created
     */
    public static function mkdirRecursive($pathname, $mode) {
        is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }

    /**
     * Write a chart into a PNG file
     * 
     * @param String $charFACode Chart to represent (unicode)
     * @param String $fileName The PNG final file
     * @param array $colorRGB The color in which you want to display the PNG, default black
     * @param int $fontSize The size of the font in which you want to display the PNG
     * @return String The path of the PNG file generate, or null if the convertion failed
     */
    public static function putIconIntoFile($charFACode, $fileName, $colorRGB, $fontSize) {
        global $CFG;

        //Calculate sizes
        $width = $height = $fontSize * 3;
        $padding = (int) ceil(($fontSize / 25));

        // Create the image
        $imageTmp = imagecreatetruecolor($width, $height);
        imagealphablending($imageTmp, false);

        // Create font colors
        $fontColor = imagecolorallocate($imageTmp, $colorRGB['red'], $colorRGB['green'], $colorRGB['blue']);
        $background = imagecolorallocatealpha($imageTmp, 255, 0, 255, 127);

        // Draw rectangle
        imagefilledrectangle($imageTmp, 0, 0, $width, $height, $background);
        imagealphablending($imageTmp, true);

        // Add the text using the Font
        if (!file_exists($CFG->dirroot . self::FA_PATH)) {
            return null;
        }
        $fontPath = $CFG->dirroot . self::FA_PATH;
        list($fontX, $fontY) = self::imageTTFCenter($imageTmp, $charFACode, $fontPath, $fontSize);
        imagettftext($imageTmp, $fontSize, 0, $fontX, $fontY, $fontColor, $fontPath, $charFACode);
        imagealphablending($imageTmp, false);
        imagesavealpha($imageTmp, true);

        //Center the image
        self::imageTrim($imageTmp, $background, $padding);

        //Resize the image
        self::imageCanvas($imageTmp, $fontSize, $background, $padding);

        //convert image to PNG
        imagepng($imageTmp, $fileName);

        //purge memory
        imagedestroy($imageTmp);

        return $fileName;
    }

    /**
     * Calculate and set the center position of an image given by the size of a text in its font representation
     * 
     * @param resource $image Reference image
     * @param String $text Text to insert into the image
     * @param String $font Font path
     * @param int $size Font size into display the text
     * @param int $angle Angle into display the text, default 45°
     * @return array the center position
     */
    public static function imageTTFCenter($image, $text, $font, $size, $angle = 45) {
        $xi = imagesx($image);
        $yi = imagesy($image);

        // create a bounding box for the first text
        $box = imagettfbbox($size, $angle, $font, $text);

        // get dimension of the box 
        $xr = abs(max($box[2], $box[4]));
        $yr = abs(max($box[5], $box[7]));

        // compute centering
        $x = intval(($xi - $xr) / 2);
        $y = intval(($yi + $yr) / 2);

        return array($x, $y);
    }

    /**
     * Position and assemblate image and background
     * 
     * @param resource $image Final image to arrange
     * @param int $background Color and alpha background
     * @param int $padding Space between chart and border
     */
    public static function imageTrim(&$image, $background, $padding = null) {

        // Calculate padding for each side.
        if (isset($padding)) {
            $pp = explode(' ', $padding);
            if (isset($pp[3])) {
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[2], (int) $pp[3]);
            } else if (isset($pp[2])) {
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[2], (int) $pp[1]);
            } else if (isset($pp[1])) {
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[0], (int) $pp[1]);
            } else {
                $p = array_fill(0, 4, (int) $pp[0]);
            }
        } else {
            $p = array_fill(0, 4, 0);
        }

        // Get the image width and height.
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        // Set the X variables.
        $xmin = $imageWidth;
        $xmax = 0;

        // Start scanning for the edges.
        for ($iy = 0; $iy < $imageHeight; $iy++) {
            $first = true;
            for ($ix = 0; $ix < $imageWidth; $ix++) {
                if (imagecolorat($image, $ix, $iy) == $background) {
                    continue;
                }
                $xmin = ($xmin > $ix) ? $ix : $xmin;
                $xmax = ($xmax < $ix) ? $ix : $xmax;
                $ymin = (!isset($ymin)) ? $iy : $ymin;
                $ymax = $iy;
                $ix = ($first) ? $xmax : $ix;
                $first = false;
            }
        }

        // The new width and height of the image. (not including padding)
        $imageNewWidth = 1 + $xmax - $xmin; // Image width in pixels
        $imageNewHeight = 1 + $ymax - $ymin; // Image height in pixels
        // Make another image to place the trimmed version in.
        $image2 = imagecreatetruecolor($imageNewWidth + $p[1] + $p[3], $imageNewHeight + $p[0] + $p[2]);

        // Make the background of the new image the same as the background of the old one.
        $bg2 = imagecolorallocatealpha($image2, ($background >> 16) & 0xFF, ($background >> 8) & 0xFF, $background & 0xFF, 127);
        imagefill($image2, 0, 0, $bg2);
        imagealphablending($image2, true);

        // Copy it over to the new image.
        imagecopy($image2, $image, $p[3], $p[0], $xmin, $ymin, $imageNewWidth, $imageNewHeight);

        // To finish up, we replace the old image which is referenced.
        imagealphablending($image2, false);
        imagesavealpha($image2, true);
        $image = $image2;
    }

    /**
     * Crop an image according to the size parameter
     * 
     * @param resource $image the image to resize
     * @param int $size New size
     * @param int $background color and alpha background
     * @param int $padding space between image content and border
     */
    public static function imageCanvas(&$image, $size, $background, $padding) {
        $sourceWidth = imagesx($image);
        $ssourceHeight = imagesy($image);

        //create a second image to put the result with operation on the $image
        $image2 = imagecreatetruecolor($size, $size);
        $background2 = imagecolorallocatealpha($image2, ($background >> 16) & 0xFF, ($background >> 8) & 0xFF, $background & 0xFF, 127);
        imagefill($image2, 0, 0, $background2);
        imagealphablending($image2, true);

        // init
        $sourceX = $sourceY = 0;
        $destinationWidth = $destinationtHeight = $size;

        // if source size is smaller than output size
        if ($sourceWidth < $size && $ssourceHeight < $size) {
            $destinationWidth = $sourceWidth;
            $destinationtHeight = $ssourceHeight;
        }
        // if source is bigger than output
        else {
            // use padding
            // if horizontal long
            if ($sourceWidth > $ssourceHeight) {
                $destinationWidth = $size - $padding;
                $destinationtHeight = (int) (($destinationWidth / $sourceWidth) * $ssourceHeight);
            }
            // if vertically long or equal(square)
            else {
                $destinationtHeight = $size - $padding;
                $destinationWidth = (int) (($destinationtHeight / $ssourceHeight) * $sourceWidth);
            }
        }

        //Calculate new coordonates
        $destinationX = (int) (($size - $destinationWidth) / 2);
        $destinationY = (int) (($size - $destinationtHeight) / 2);

        //Copy and resize the fist image into image2
        imagecopyresampled($image2, $image, $destinationX, $destinationY, $sourceX, $sourceY, $destinationWidth, $destinationtHeight, $sourceWidth, $ssourceHeight);

        //finalize image2
        imagealphablending($image2, false);
        imagesavealpha($image2, true);

        //replace image with image2
        $image = $image2;
    }

    /**
     * Get unicode of a icon reprented in FontAwesome, using the scss file
     * 
     * @param String $iconName
     * @return String unicode or null if no correspondance
     */
    public static function getUniCode($iconName) {
        global $CFG;

        //open scss file
        if (!file_exists($CFG->dirroot . self::FA_SCSS_PATH)) {
            return null;
        }
        $readBuffer = file_get_contents($CFG->dirroot . self::FA_SCSS_PATH);
        $lines = explode("\n", $readBuffer);

        //get all lines to search the hexa code from key
        foreach ($lines as $l) {
            if (!strstr($l, '$fa-var-')) {
                continue;
            }
            $lArray = explode(":", $l);
            $key = str_replace('$fa-var-', '', trim($lArray[0]));
            if ($key == $iconName) {
                $value = str_replace(array('"'), '', trim($lArray[1]));
                $value2Write = str_replace('\\', '&#x', $value);
                return $value2Write;
            }
        }

        return null;
    }

}
