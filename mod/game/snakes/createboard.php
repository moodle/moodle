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
 * This file creates a board for "Snakes and Ladders".
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Create snakes board
 *
 * @param string $imageasstring
 * @param int $colsx
 * @param int $colsy
 * @param int $ofstop
 * @param int $ofsbottom
 * @param int $ofsright
 * @param int $ofsleft
 * @param stdClass $board
 * @param int $setwidth
 * @param int $setheight
 */
function game_createsnakesboard($imageasstring, $colsx, $colsy, $ofstop, $ofsbottom,
        $ofsright, $ofsleft, $board, $setwidth, $setheight) {
    global $CFG;

    $dir = $CFG->dirroot.'/mod/game/snakes/1';

    $im = imagecreatefromstring($imageasstring);

    // Check if need resize.
    if ( $setwidth > 0 or $setheight > 0) {
        $source = $im;
        $width = imagesx($source);
        $height = imagesy($source);
        $factorx = $setwidth / $width;
        $factory = $setheight / $height;
        $factor = ($factorx < $factory || $factory == 0 ? $factorx : $factory);

        $newwidth = $width * $factor;
        $newheight = $height * $factor;

        $im = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresized($im, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    }

    $cx = imagesx($im) - $ofsright - $ofsleft;
    $cy = imagesy($im) - $ofstop - $ofsbottom;

    $color = 0xFF0000;
    for ($i = 0; $i <= $colsx; $i++) {
        imageline( $im, $ofsleft + $i * $cx / $colsx, $ofstop, $ofsleft + $i * $cx / $colsx, $cy + $ofstop, $color);
    }

    for ($i = 0; $i <= $colsy; $i++) {
        imageline( $im, $ofsleft, $ofstop + $i * $cy / $colsy, $cx + $ofsleft, $ofstop + $i * $cy / $colsy, $color);
    }

    $filenamenumbers = $dir.'/numbers.png';
    $imgnumbers = imagecreatefrompng( $filenamenumbers);
    $sizenumbers = getimagesize ($filenamenumbers);

    for ($iy = 0; $iy < $colsy; $iy++) {
        if ($iy % 2 == 0) {
            $inc = false;
            $num = ($colsy - $iy) * $colsy;
        } else {
            $inc = true;
            $num = ($colsy - $iy) * $colsy - ($colsy - 1);
        }
        $ypos = $iy * $cy / $colsy + $ofstop;
        for ($ix = 0; $ix < $colsx; $ix++) {
            $xpos = $ix * $cx / $colsx + $ofsleft;
            shownumber( $im, $imgnumbers, $num, $xpos, $ypos, $cx / 4, $cy / 4, $sizenumbers);
            $num = ($inc ? $num + 1 : $num - 1);
        }
    }

    makeboard( $im, $dir, $cx, $cy, $board, $colsx, $colsy, $ofsleft, $ofstop);

    return $im;
}

/**
 * Compute coordinates
 *
 * @param int $pos
 * @param int $x
 * @param int $y
 * @param int $colsx
 * @param int $colsy
 */
function computexy( $pos, &$x, &$y, $colsx, $colsy) {
    $x = ($pos - 1) % $colsx;
    $y = ($colsy - 1) - floor( ($pos - 1) / $colsy);
    if ($y % 2 == 0) {
        $x = ($colsx - 1) - $x;
    }
}

/**
 * Make board
 *
 * @param object $im
 * @param int $dir
 * @param int $cx
 * @param int $cy
 * @param object $board
 * @param int $colsx
 * @param int $colsy
 * @param int $ofsleft
 * @param int $ofstop
 */
function makeboard( $im, $dir, $cx, $cy, $board, $colsx, $colsy, $ofsleft, $ofstop) {
    $a = explode( ',', $board);
    foreach ($a as $s) {
        if (substr( $s, 0, 1) == 'L') {
            makeboardL( $im, $dir, $cx, $cy, substr( $s, 1), $colsx, $colsy, $ofsleft, $ofstop);
        } else {
            makeboardS( $im, $dir, $cx, $cy, substr( $s, 1), $colsx, $colsy, $ofsleft, $ofstop);
        }
    }
}


/**
 * Make board ladders
 *
 * @param object $im
 * @param string $dir
 * @param int $cx
 * @param int $cy
 * @param string $s
 * @param int $colsx
 * @param int $colsy
 * @param int $ofsleft
 * @param int $ofstop
 */
function makeboardl( $im, $dir, $cx, $cy, $s, $colsx, $colsy, $ofsleft, $ofstop) {
    $pos = strpos( $s, '-');
    $from = substr( $s, 0, $pos);
    $to = substr( $s, $pos + 1);

    computexy( $from, $startx, $starty, $colsx, $colsy);
    computexy( $to, $x2, $y2, $colsx, $colsy);
    if (($x2 < $startx) and ($y2 < $starty)) {
        $temp = $x2; $x2 = $startx; $startx = $temp;
        $temp = $y2; $y2 = $starty; $starty = $temp;
    }
    $movex = $x2 - $startx;
    $movey = $y2 - $starty;

    $letter = ( $movex * $movey < 0 ? 'b' : 'a');

    $oldstartx = $startx; $oldmovex = $movex; $oldstarty = $starty; $oldmovey = $movey;

    if ($movex < 0) {
        $startx += $movex;
        $movex = -$movex;
    }
    if ($movey < 0) {
        $starty += $movey;
        $movey = -$movey;
    }
    $stamp = 0;
    if ($letter == 'b') {
        $file = $dir.'/l'.$letter.$movey.$movex.'.png';
        if (file_exists( $file)) {
            $stamp = game_imagecreatefrompng( $file);
        } else {
            $file = $dir.'/la'.$movey.$movex.'.png';

            $source = game_imagecreatefrompng( $file);
            if ( $source != 0) {
                $stamp = imagerotate($source, 90, 0);
            }
        }
    } else {
        $file = $dir.'/la'.$movex.$movey.'.png';
        $stamp = game_imagecreatefrompng( $file);
    }

    $dstx = $startx * $cx / $colsx;
    $dsty = $starty * $cy / $colsy;
    $dstw = ($movex + 1) * $cx / $colsx;
    $dsth = ($movey + 1) * $cy / $colsy;

    if ($stamp == 0) {
        game_printladder( $im, $file, $dstx + $ofsleft, $dsty + $ofstop, $dstw, $dsth, $cx / $colsx, $cy / $colsy);
    } else {
        imagecopyresampled( $im, $stamp, $ofsleft + $dstx, $ofstop + $dsty, 0, 0, $dstw, $dsth,
            100 * $movex + 100, 100 * $movey + 100);
    }
}

/**
 * Make board snakes
 *
 * @param object $im
 * @param string $dir
 * @param int $cx
 * @param int $cy
 * @param string $s
 * @param int $colsx
 * @param int $colsy
 * @param int $ofsleft
 * @param int $ofstop
 */
function makeboards( $im, $dir, $cx, $cy, $s, $colsx, $colsy, $ofsleft, $ofstop) {
    $pos = strpos( $s, '-');
    $from = substr( $s, 0, $pos);
    $to = substr( $s, $pos + 1);

    computexy( $from, $startx, $starty, $colsx, $colsy);
    computexy( $to, $x2, $y2, $colsx, $colsy);
    $swap = 0;
    if (($x2 < $startx) and ($y2 < $starty)) {
        $temp = $x2; $x2 = $startx; $startx = $temp;
        $temp = $y2; $y2 = $starty; $starty = $temp;
        $swap = 1;
    }
    $movex = $x2 - $startx;
    $movey = $y2 - $starty;

    /*  a*d
     *
     *  b*c
     */
    $stamp = $rotate = 0;
    if ($movex >= 0 and $movey < 0) {
        $letter = 'b';
        $file = $dir.'/sa'.$movey.$movex.'.png';
        $source = game_imagecreatefrompng( $file);
        if ($source != 0) {
            $stamp = imagerotate($source, 270, 0);
            $starty += $movey; $movey = -$movey;
        } else {
            $rotate = 270;
        }
    } else if ($movex < 0 and $movey < 0) {
        $letter = 'c';
        $file = $dir.'/sa'.$movey.$movex.'.png';
        $source = game_imagecreatefrompng( $file);
        if ($source != 0) {
            $stamp = imagerotate($source, 180, 0);
            $startx += $movex; $movex = -$movex;
            $starty += $movey; $movey = -$movey;
        } else {
            $rotate = 180;
        }
    } else if (($movex < 0) and ($movey >= 0)) {
        $letter = 'd';
        $file = $dir.'/sa'.$movey.$movex.'.png';
        $source = game_imagecreatefrompng( $file);
        if ($source != 0) {
            $stamp = imagerotate($source, 270, 0);
            $startx += $movex; $movex = -$movex;
        } else {
            $rotate = 270;
        }
    } else {
        $file = $dir.'/sa'.$movex.$movey.'.png';
        $stamp = game_imagecreatefrompng( $file);
    }

    if (($swap != 0) and ($stamp == 0)) {
        $temp = $x2; $x2 = $startx; $startx = $temp;
        $temp = $y2; $y2 = $starty; $starty = $temp;
        $movex = $x2 - $startx;
        $movey = $y2 - $starty;
    }

    $dstx = $startx * $cx / $colsx;
    $dsty = $starty * $cy / $colsy;
    $dstw = ($movex + 1) * $cx / $colsx;
    $dsth = ($movey + 1) * $cy / $colsy;

    if ($stamp == 0) {
        game_printsnake( $im, $file, $dstx + $ofsleft, $dsty + $ofstop, $dstw, $dsth, $cx / $colsx, $cy / $colsy);
    } else {
        imagecopyresampled( $im, $stamp, $dstx + $ofsleft, $dsty + $ofstop, 0, 0, $dstw, $dsth,
            100 * $movex + 100, 100 * $movey + 100);
    }
}

/**
 * Image create from png
 *
 * @param string $file
 */
function game_imagecreatefrompng( $file) {
    if (file_exists( $file)) {
        return imagecreatefrompng( $file);
    }

    return 0;
}

/**
 * Show number
 *
 * @param int $imghandle
 * @param string $imgnumbers
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
        $dstx = $x1 + $width / 10;
        $dsty = $y1 + $height / 10;
        $srcx = $number * $sizenumbers[ 0] / 10;
        $srcw = $sizenumbers[ 0] / 10;
        $srch = $sizenumbers[ 1];
        $dstw = $width / 10;
        $dsth = $dstw * $srch / $srcw;
        imagecopyresampled( $imghandle, $imgnumbers, $dstx, $dsty, $srcx, 0, $dstw, $dsth, $srcw, $srch);
    } else {
        $number1 = floor( $number / 10);
        $number2 = $number % 10;
        shownumber( $imghandle, $imgnumbers, $number1, $x1 - $width / 20, $y1, $width, $height, $sizenumbers);
        shownumber( $imghandle, $imgnumbers, $number2, $x1 + $width / 20, $y1, $width, $height, $sizenumbers);
    }
}

/**
 * Return rotated point
 *
 * @param int $x
 * @param int $y
 * @param int $cx
 * @param int $cy
 * @param float $a
 */
function returnrotatedpoint($x, $y, $cx, $cy, $a) {
    // Radius using distance formula.
    $r = sqrt(pow(($x - $cx), 2) + pow(($y - $cy), 2));

    // Initial angle in relation to center.
    $ia = rad2deg(atan2(($y - $cy), ($x - $cx)));

    $nx = $r * cos(deg2rad($a + $ia));
    $ny = $r * sin(deg2rad($a + $ia));

    return array("x" => $cx + $nx, "y" => $cy + $ny);
}


/**
 * Print ladder
 *
 * @param int $im
 * @param string $file
 * @param int $x
 * @param int $y
 * @param int $width
 * @param int $height
 * @param int $cellx
 * @param int $celly
 */
function game_printladder( $im, $file, $x, $y, $width, $height, $cellx, $celly) {
    $color = imagecolorallocate($im, 0, 0, 255);
    $x2 = $x + $width - $cellx / 2;
    $y2 = $y + $height - $celly / 2;
    $x1 = $x + $cellx / 2;
    $y1 = $y + $celly / 2;
    imageline( $im, $x1, $y1, $x2, $y2, $color);
    $r = sqrt(pow(($x2 - $x1), 2) + pow(($y2 - $y1), 2));
    $mul = 100 / $r;
    $x1 = $x2 - ($x2 - $x1) * $mul;
    $y1 = $y2 - ($y2 - $y1) * $mul;
    $a = returnRotatedPoint( $x1, $y1, $x2, $y2, 20);
    imageline( $im, $x2, $y2, $a[ 'x'], $a[ 'y'], $color);
    $a = returnRotatedPoint( $x1, $y1, $x2, $y2, -20);
    imageline( $im, $x2, $y2, $a[ 'x'], $a[ 'y'], $color);
}

/**
 * Print snake
 *
 * @param int $im
 * @param string $file
 * @param int $x
 * @param int $y
 * @param int $width
 * @param int $height
 * @param int $cellx
 * @param int $celly
 */
function game_printsnake( $im, $file, $x, $y, $width, $height, $cellx, $celly) {
    $color = imagecolorallocate($im, 0, 255, 0);
    $x2 = $x + $width - $cellx / 2;
    $y2 = $y + $height - $celly / 2;
    $x1 = $x + $cellx / 2;
    $y1 = $y + $celly / 2;
    imageline( $im, $x1, $y1, $x2, $y2, $color);

    $r = sqrt(pow(($x2 - $x1), 2) + pow(($y2 - $y1), 2));
    $mul = 100 / $r;
    $x2 = $x1 + ($x2 - $x1) * $mul;
    $y2 = $y1 + ($y2 - $y1) * $mul;
    $a = returnRotatedPoint( $x1, $y1, $x2, $y2, 80);
    imageline( $im, $x1, $y1, $a[ 'x'], $a[ 'y'], $color);
    $a = returnRotatedPoint( $x1, $y1, $x2, $y2, -80);
    imageline( $im, $x1, $y1, $a[ 'x'], $a[ 'y'], $color);
}
