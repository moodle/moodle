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

require_once('../../../../config.php');

$id = required_param('id', PARAM_INT);

$PAGE->set_url('/mod/feedback/item/captcha/print_captcha.php', array('id'=>$id));

if ($id) {
    if (! $cm = get_coursemodule_from_id('feedback', $id)) {
        throw new \moodle_exception('invalidcoursemodule');
    }

    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        throw new \moodle_exception('coursemisconf');
    }

    if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
        throw new \moodle_exception('invalidcoursemodule');
    }
}

if (!isset($SESSION->feedback->item->captcha)) {
    throw new \moodle_exception('captchanotset', 'feedback');
}

$height = 40;
$charcount = $SESSION->feedback->item->captcha->charcount;
$fontfile = $CFG->libdir.'/default.ttf';

$ttfbox = imagettfbbox ( 30, 0, $fontfile, 'H' );//the text to measure
$charwidth = $ttfbox[2];

$width = $charcount * $charwidth;

$scale = 0.3;
$elipsesize = intval((($width + $height)/2) / 5);
$factor_x = intval($width * $scale);
$factor_y = intval($height * $scale);

//I split the colors in three ranges
//given are the max-min-values
$colors = array(array(0, 40), array(50, 200), array(210, 255));
list($col_text1, $col_el, $col_text2) = $colors;

//if the text is in color_1 so the elipses can be in color_2 or color_3
//if the text is in color_2 so the elipses can be in color_1 or color_3
//and so on.
$textcolnum = rand(1, 3);

//create the numbers to print out
$nums = array();
for ($i = 0; $i < $charcount; $i++) {
    $nums[] = rand(0, 9); //Ziffern von 0-
}

//to draw enough elipses so I draw 0.2 * width and 0.2 * height
//we need th colors for that
$properties = array();
for ($x = 0; $x < $factor_x; $x++) {
    for ($y = 0; $y < $factor_y; $y++) {
        $propobj = new stdClass();
        $propobj->x = intval($x / $scale);
        $propobj->y = intval($y / $scale);
        $propobj->red = get_random_color($col_el[0], $col_el[1]);
        $propobj->green = get_random_color($col_el[0], $col_el[1]);
        $propobj->blue = get_random_color($col_el[0], $col_el[1]);
        $properties[] = $propobj;
    }
}
shuffle($properties);

// create a blank image
$image = imagecreatetruecolor($width, $height);
$bg = imagecolorallocate($image, 0, 0, 0);
for ($i = 0; $i < ($factor_x * $factor_y); $i++) {
    $propobj = $properties[$i];
    // choose a color for the ellipse
    $col_ellipse = imagecolorallocate($image, $propobj->red, $propobj->green, $propobj->blue);
    // draw the white ellipse
    imagefilledellipse($image, $propobj->x, $propobj->y, $elipsesize, $elipsesize, $col_ellipse);
}

$checkchar = '';
for ($i = 0; $i < $charcount; $i++) {
    $colnum = rand(1, 2);
    $textcol = new stdClass();
    $textcol->red = get_random_color(${'col_text'.$colnum}[0], ${'col_text'.$colnum}[1]);
    $textcol->green = get_random_color(${'col_text'.$colnum}[0], ${'col_text'.$colnum}[1]);
    $textcol->blue = get_random_color(${'col_text'.$colnum}[0], ${'col_text'.$colnum}[1]);
    $color_text = imagecolorallocate($image, $textcol->red, $textcol->green, $textcol->blue);
    $angle_text = rand(-20, 20);
    $left_text = $i * $charwidth;
    $text = $nums[$i];
    $checkchar .= $text;
    imagettftext($image, 30, $angle_text, $left_text, 35, $color_text, $fontfile, $text);
}

$SESSION->feedback->item->captcha->checkchar = $checkchar;

// output the picture
header("Content-type: image/png");
imagepng($image);

function get_random_color($val1 = 0, $val2 = 255) {
    $min = $val1 < $val2 ? $val1 : $val2;
    $max = $val1 > $val2 ? $val1 : $val2;

    return rand($min, $max);
}
