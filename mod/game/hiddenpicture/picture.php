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
 * Creates an image dynamically.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require( '../../../config.php');
require_login();

$id = required_param('id', PARAM_INT); // Course Module ID.
$attemptid = required_param('id2', PARAM_INT); // Course Module ID.

$foundcells = required_param('f', PARAM_SEQUENCE); // CSV.
$cells = required_param('cells', PARAM_SEQUENCE); // CSV.
$filehash = required_param('p', PARAM_PATH);
$cols = required_param('cols', PARAM_INT);
$rows = required_param('rows', PARAM_INT);
$filenamenumbers = required_param('n', PARAM_PATH); // Path to numbers picture.
create_image( $id, $attemptid, $foundcells, $cells, $filehash, $cols, $rows, $filenamenumbers);

/**
 * Create an image.
 *
 * @param int $id
 * @param int $attemptid
 * @param boolean $foundcells
 * @param stdClass $cells
 * @param string $filehash
 * @param int $cols
 * @param int $rows
 * @param string $filenamenumbers
 */
function create_image( $id, $attemptid, $foundcells, $cells, $filehash, $cols, $rows, $filenamenumbers) {
    global $CFG;

    $a = explode( ',', $foundcells);
    $found = array();
    foreach ($a as $s) {
        $found[ $s] = 1;
    }

    $a = explode( ',', $cells);
    $cells = array();
    foreach ($a as $s) {
        $cells[ $s] = 1;
    }

    $file = get_file_storage()->get_file_by_hash( $filehash);
    $image = $file->get_imageinfo();

    if ($image === false) {
        die("Aknown filehash $filehash");
        return false;
    }
    $imghandle = imagecreatefromstring($file->get_content());

    $mime = $image[ 'mimetype'];

    $imgnumbers = imagecreatefrompng( $filenamenumbers);
    $sizenumbers = getimagesize ($filenamenumbers);

    header("Content-type: $mime");

    $color = imagecolorallocate( $imghandle, 100, 100, 100);

    $width = $image[ 'width'];
    $height = $image[ 'height'];
    $pos = 0;

    $font = 1;

    for ($y = 0; $y < $rows; $y++) {
        for ($x = 0; $x < $cols; $x++) {
            $pos++;
            if (!array_key_exists( $pos, $found)) {
                $x1 = $x * $width / $cols;
                $y1 = $y * $height / $rows;
                imagefilledrectangle( $imghandle, $x1, $y1, $x1 + $width / $cols, $y1 + $height / $rows, $color);

                if (array_key_exists( $pos, $cells)) {
                    shownumber( $imghandle, $imgnumbers, $pos, $x1 , $y1, $width / $cols, $height / $rows, $sizenumbers);
                }
            }
        }
    }

    switch ($mime) {
        case 'image/png':
            imagepng ($imghandle);
            break;
        case 'image/jpeg':
            imagejpeg ($imghandle);
            break;
        case 'image/gif':
            imagegif ($imghandle);
            break;
        default:
            die('Aknown mime type $mime');
            return false;
    }

    imagedestroy ($imghandle);
}

/**
 * Show number.
 *
 * @param object $imghandle
 * @param object $imgnumbers
 * @param int $number
 * @param int $x1
 * @param int $y1
 * @param int $width
 * @param int $height
 * @param int $sizenumbers
 */
function shownumber( $imghandle, $imgnumbers, $number, $x1 , $y1, $width, $height, $sizenumbers) {
    if ($number < 10) {
        $widthnumber = $sizenumbers[ 0] / 10;
        $dstx = $x1 + $width / 3;
        $dsty = $y1 + $height / 3;
        $srcx = $number * $sizenumbers[ 0] / 10;
        $srcw = $sizenumbers[ 0] / 10;
        $srch = $sizenumbers[ 1];
        $dstw = $width / 10;
        $dsth = $dstw * $srch / $srcw;
        imagecopyresized( $imghandle, $imgnumbers, $dstx, $dsty, $srcx, 0, $dstw, $dsth, $srcw, $srch);
    } else {
        $number1 = floor( $number / 10);
        $number2 = $number % 10;
        shownumber( $imghandle, $imgnumbers, $number1, $x1 - $width / 20, $y1, $width, $height, $sizenumbers);
        shownumber( $imghandle, $imgnumbers, $number2, $x1 + $width / 20, $y1, $width, $height, $sizenumbers);
    }
}
