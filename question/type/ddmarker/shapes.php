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
 * Drag-and-drop markers classes for dealing with shapes on the server side.
 *
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Base class to represent a shape.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_ddmarker_shape {
    /** @var bool Indicates if there is an error */
    protected $error = false;

    /** @var string The shape class prefix */
    protected static $classnameprefix = 'qtype_ddmarker_shape_';

    public function __construct($coordsstring) {

    }
    public function inside_width_height($widthheight) {
        foreach ($this->outlying_coords_to_test() as $coordsxy) {
            if ($coordsxy[0] > $widthheight[0] || $coordsxy[1] > $widthheight[1]) {
                return false;
            }
        }
        return true;
    }

    abstract protected function outlying_coords_to_test();

    /**
     * Returns the center location of the shape.
     *
     * @return array X and Y location
     */
    abstract public function center_point();

    /**
     * Test if all passed parameters consist of only numbers.
     *
     * @return bool True if only numbers
     */
    protected function is_only_numbers() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (0 === preg_match('!^[0-9]+$!', $arg)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if the point is within the bounding box made by top left and bottom right
     *
     * @param array $pointxy Array of the point (x, y)
     * @param array $xleftytop Top left point of bounding box
     * @param array $xrightybottom Bottom left point of bounding box
     * @return bool
     */
    protected function is_point_in_bounding_box($pointxy, $xleftytop, $xrightybottom) {
        if ($pointxy[0] < $xleftytop[0]) {
            return false;
        } else if ($pointxy[0] > $xrightybottom[0]) {
            return false;
        } else if ($pointxy[1] < $xleftytop[1]) {
            return false;
        } else if ($pointxy[1] > $xrightybottom[1]) {
            return false;
        }
        return true;
    }

    /**
     * Gets any coordinate error
     *
     * @return string|bool String of the error or false if there is no error
     */
    public function get_coords_interpreter_error() {
        if ($this->error) {
            $a = new stdClass();
            $a->shape = self::human_readable_name(true);
            $a->coordsstring = self::human_readable_coords_format();
            return get_string('formerror_'.$this->error, 'qtype_ddmarker', $a);
        } else {
            return false;
        }
    }

    /**
     * Check if the location is within the shape.
     *
     * @param array $xy $xy[0] is x, $xy[1] is y
     * @return boolean is point inside shape
     */
    abstract public function is_point_in_shape($xy);

    /**
     * Returns the name of the shape.
     *
     * @return string
     */
    public static function name() {
        return substr(get_called_class(), strlen(self::$classnameprefix));
    }

    /**
     * Return a human readable name of the shape.
     *
     * @param bool $lowercase True if it should be lowercase.
     * @return string
     */
    public static function human_readable_name($lowercase = false) {
        $stringid = 'shape_'.self::name();
        if ($lowercase) {
            $stringid .= '_lowercase';
        }
        return get_string($stringid, 'qtype_ddmarker');
    }

    public static function human_readable_coords_format() {
        return get_string('shape_'.self::name().'_coords', 'qtype_ddmarker');
    }


    public static function shape_options() {
        $grepexpression = '!^'.preg_quote(self::$classnameprefix, '!').'!';
        $shapes = preg_grep($grepexpression, get_declared_classes());
        $shapearray = array();
        foreach ($shapes as $shape) {
            $shapearray[$shape::name()] = $shape::human_readable_name();
        }
        asort($shapearray);
        return $shapearray;
    }

    /**
     * Checks if the passed shape exists.
     *
     * @param string $shape The shape name
     * @return bool
     */
    public static function exists($shape) {
        return class_exists((self::$classnameprefix).$shape);
    }

    /**
     * Creates a new shape of the specified type.
     *
     * @param string $shape The shape to create
     * @param string $coordsstring The string describing the coordinates
     * @return object
     */
    public static function create($shape, $coordsstring) {
        $classname = (self::$classnameprefix).$shape;
        return new $classname($coordsstring);
    }
}


/**
 * Class to represent a rectangle.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_shape_rectangle extends qtype_ddmarker_shape {
    /** @var int Width of shape */
    protected $width;

    /** @var int Height of shape */
    protected $height;

    /** @var int Left location */
    protected $xleft;

    /** @var int Top location */
    protected $ytop;

    public function __construct($coordsstring) {
        $coordstring = preg_replace('!^\s*!', '', $coordsstring);
        $coordstring = preg_replace('!\s*$!', '', $coordsstring);
        $coordsstringparts = preg_split('!;!', $coordsstring);

        if (count($coordsstringparts) > 2) {
            $this->error = 'toomanysemicolons';

        } else if (count($coordsstringparts) < 2) {
            $this->error = 'nosemicolons';

        } else {
            $xy = explode(',', $coordsstringparts[0]);
            $widthheightparts = explode(',', $coordsstringparts[1]);
            if (count($xy) !== 2) {
                $this->error = 'unrecognisedxypart';
            } else if (count($widthheightparts) !== 2) {
                $this->error = 'unrecognisedwidthheightpart';
            } else {
                $this->width  = trim($widthheightparts[0]);
                $this->height = trim($widthheightparts[1]);
                $this->xleft  = trim($xy[0]);
                $this->ytop   = trim($xy[1]);
            }
            if (!$this->is_only_numbers($this->width, $this->height, $this->ytop, $this->xleft)) {
                $this->error = 'onlyusewholepositivenumbers';
            }
            $this->width  = (int) $this->width;
            $this->height = (int) $this->height;
            $this->xleft  = (int) $this->xleft;
            $this->ytop   = (int) $this->ytop;
        }

    }
    protected function outlying_coords_to_test() {
        return array($this->xleft + $this->width, $this->ytop + $this->height);
    }
    public function is_point_in_shape($xy) {
        return $this->is_point_in_bounding_box($xy, array($this->xleft, $this->ytop),
                                  array($this->xleft + $this->width, $this->ytop + $this->height));
    }
    public function center_point() {
        return array($this->xleft + round($this->width / 2),
                        $this->ytop + round($this->height / 2));
    }
}


/**
 * Class to represent a circle.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_shape_circle extends qtype_ddmarker_shape {
    /** @var int X center */
    protected $xcentre;

    /** @var int Y center */
    protected $ycentre;

    /** @var int Radius of circle */
    protected $radius;

    public function __construct($coordsstring) {
        $coordstring = preg_replace('!\s!', '', $coordsstring);
        $coordsstringparts = explode(';', $coordsstring);

        if (count($coordsstringparts) > 2) {
            $this->error = 'toomanysemicolons';

        } else if (count($coordsstringparts) < 2) {
            $this->error = 'nosemicolons';

        } else {
            $xy = explode(',', $coordsstringparts[0]);
            if (count($xy) !== 2) {
                $this->error = 'unrecognisedxypart';
            } else {
                $this->radius = trim($coordsstringparts[1]);
                $this->xcentre = trim($xy[0]);
                $this->ycentre = trim($xy[1]);
            }

            if (!$this->is_only_numbers($this->xcentre, $this->ycentre, $this->radius)) {
                $this->error = 'onlyusewholepositivenumbers';
            }

            $this->xcentre = (int) $this->xcentre;
            $this->ycentre = (int) $this->ycentre;
            $this->radius  = (int) $this->radius;
        }
    }

    protected function outlying_coords_to_test() {
        return array($this->xcentre + $this->radius, $this->ycentre + $this->radius);
    }

    public function is_point_in_shape($xy) {
        $distancefromcentre = sqrt(pow(($xy[0] - $this->xcentre), 2) + pow(($xy[1] - $this->ycentre), 2));
        return $distancefromcentre <= $this->radius;
    }

    public function center_point() {
        return array($this->xcentre, $this->ycentre);
    }
}


/**
 * Class to represent a polygon.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_shape_polygon extends qtype_ddmarker_shape {
    /**
     * @var array Arrary of xy coords where xy coords are also in a two element array [x,y].
     */
    public $coords;
    /**
     * @var array min x and y coords in a two element array [x,y].
     */
    protected $minxy;
    /**
     * @var array max x and y coords in a two element array [x,y].
     */
    protected $maxxy;

    public function __construct($coordsstring) {
        $this->coords = array();
        $coordstring = preg_replace('!\s!', '', $coordsstring);
        $coordsstringparts = explode(';', $coordsstring);
        if (count($coordsstringparts) < 3) {
            $this->error = 'polygonmusthaveatleastthreepoints';
        } else {
            $lastxy = null;
            foreach ($coordsstringparts as $coordsstringpart) {
                $xy = explode(',', $coordsstringpart);
                if (count($xy) !== 2) {
                    $this->error = 'unrecognisedxypart';
                }
                if (!$this->is_only_numbers(trim($xy[0]), trim($xy[1]))) {
                    $this->error = 'onlyusewholepositivenumbers';
                }
                $xy[0] = (int) $xy[0];
                $xy[1] = (int) $xy[1];
                if ($lastxy !== null && $lastxy[0] == $xy[0] && $lastxy[1] == $xy[1]) {
                    $this->error = 'repeatedpoint';
                }
                $this->coords[] = $xy;
                $lastxy = $xy;
                if (isset($this->minxy)) {
                    $this->minxy[0] = min($this->minxy[0], $xy[0]);
                    $this->minxy[1] = min($this->minxy[1], $xy[1]);
                } else {
                    $this->minxy[0] = $xy[0];
                    $this->minxy[1] = $xy[1];
                }
                if (isset($this->maxxy)) {
                    $this->maxxy[0] = max($this->maxxy[0], $xy[0]);
                    $this->maxxy[1] = max($this->maxxy[1], $xy[1]);
                } else {
                    $this->maxxy[0] = $xy[0];
                    $this->maxxy[1] = $xy[1];
                }
            }
            // Make sure polygon is not closed.
            if ($this->coords[count($this->coords) - 1][0] == $this->coords[0][0] &&
                                $this->coords[count($this->coords) - 1][1] == $this->coords[0][1]) {
                unset($this->coords[count($this->coords) - 1]);
            }
        }
    }

    protected function outlying_coords_to_test() {
        return array($this->minxy, $this->maxxy);
    }

    public function is_point_in_shape($xy) {
        // This code is based on the winding number algorithm from
        // http://geomalgorithms.com/a03-_inclusion.html
        // which comes with the following copyright notice:

        // Copyright 2000 softSurfer, 2012 Dan Sunday
        // This code may be freely used, distributed and modified for any purpose
        // providing that this copyright notice is included with it.
        // SoftSurfer makes no warranty for this code, and cannot be held
        // liable for any real or imagined damage resulting from its use.
        // Users of this code must verify correctness for their application.

        $point = new qtype_ddmarker_point($xy[0], $xy[1]);
        $windingnumber = 0;
        foreach ($this->coords as $index => $coord) {
            $start = new qtype_ddmarker_point($this->coords[$index][0], $this->coords[$index][1]);
            if ($index < count($this->coords) - 1) {
                $endindex = $index + 1;
            } else {
                $endindex = 0;
            }
            $end = new qtype_ddmarker_point($this->coords[$endindex][0], $this->coords[$endindex][1]);

            if ($start->y <= $point->y) {
                if ($end->y >= $point->y) { // An upward crossing.
                    $isleft = $this->is_left($start, $end, $point);
                    if ($isleft == 0) {
                        return true; // The point is on the line.
                    } else if ($isleft > 0) {
                        // A valid up intersect.
                        $windingnumber += 1;
                    }
                }
            } else {
                if ($end->y <= $point->y) { // A downward crossing.
                    $isleft = $this->is_left($start, $end, $point);
                    if ($isleft == 0) {
                        return true; // The point is on the line.
                    } else if ($this->is_left($start, $end, $point) < 0) {
                        // A valid down intersect.
                        $windingnumber -= 1;
                    }
                }
            }
        }
        return $windingnumber != 0;
    }

    /**
     * Tests if a point is left / on / right of an infinite line.
     *
     * @param qtype_ddmarker_point $start first of two points on the infinite line.
     * @param qtype_ddmarker_point $end second of two points on the infinite line.
     * @param qtype_ddmarker_point $point the oint to test.
     * @return number > 0 if the point is left of the line.
     *                 = 0 if the point is on the line.
     *                 < 0 if the point is right of the line.
     */
    protected function is_left(qtype_ddmarker_point $start, qtype_ddmarker_point $end,
            qtype_ddmarker_point $point) {
        return ($end->x - $start->x) * ($point->y - $start->y)
                - ($point->x -  $start->x) * ($end->y - $start->y);
    }

    public function center_point() {
        $center = array(round(($this->minxy[0] + $this->maxxy[0]) / 2),
                        round(($this->minxy[1] + $this->maxxy[1]) / 2));
        if ($this->is_point_in_shape($center)) {
            return $center;
        } else {
            return null;
        }
    }
}


/**
 * Class to represent a point.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_point {
    /** @var int X location */
    public $x;

    /** @var int Y location */
    public $y;
    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Return the distance between this point and another
     */
    public function dist($other) {
        return sqrt(pow($this->x - $other->x, 2) + pow($this->y - $other->y, 2));
    }
}
