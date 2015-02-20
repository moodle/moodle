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

    protected $error = false;

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

    abstract public function center_point();

    protected function is_only_numbers() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (0 === preg_match('!^[0-9]+$!', $arg)) {
                return false;
            }
        }
        return true;
    }

    protected function is_point_in_bounding_box($pointxy, $xleftytop, $xrightybottom) {
        if ($pointxy[0] <= $xleftytop[0]) {
            return false;
        } else if ($pointxy[0] >= $xrightybottom[0]) {
            return false;
        } else if ($pointxy[1] <= $xleftytop[1]) {
            return false;
        } else if ($pointxy[1] >= $xrightybottom[1]) {
            return false;
        }
        return true;
    }

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
     * @param array $xy $xy[0] is x, $xy[1] is y
     * @return boolean is point inside shape
     */
    abstract public function is_point_in_shape($xy);

    public static function name() {
        return substr(get_called_class(), strlen(self::$classnameprefix));
    }

    protected static $classnameprefix = 'qtype_ddmarker_shape_';

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
    public static function exists($shape) {
        return class_exists((self::$classnameprefix).$shape);
    }
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
    protected $width;
    protected $height;
    protected $xleft;
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

    protected $xcentre;
    protected $ycentre;
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
        return $distancefromcentre < $this->radius;
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
        $pointatinfinity = new qtype_ddmarker_point(-1000000, $xy[1] + 1);
        $pointtotest = new qtype_ddmarker_point($xy[0], $xy[1]);
        $testsegment = new qtype_ddmarker_segment($pointatinfinity, $pointtotest);
        $windingnumber = 0;
        foreach ($this->coords as $index => $coord) {
            if ($index != 0) {
                $a = new qtype_ddmarker_point($this->coords[$index - 1][0],
                                                $this->coords[$index - 1][1]);
            } else {
                $a = new qtype_ddmarker_point($this->coords[count($this->coords) - 1][0],
                                                $this->coords[count($this->coords) - 1][1]);
            }
            $b = new qtype_ddmarker_point($this->coords[$index][0],
                                            $this->coords[$index][1]);
            $segment = new qtype_ddmarker_segment($a, $b);
            $intersects = $segment->intersects($testsegment);
            if ($intersects === null) {
                list($perturbedsegment, $testsegment) = $this->perturb($segment, $testsegment);
                if ($index !== 0) {
                    $this->coords[$index - 1][0] = $perturbedsegment->a->x;
                    $this->coords[$index - 1][1] = $perturbedsegment->a->y;
                } else {
                    $this->coords[count($this->coords) - 1][0] = $perturbedsegment->a->x;
                    $this->coords[count($this->coords) - 1][1] = $perturbedsegment->a->y;
                }
                $this->coords[$index][0] = $perturbedsegment->b->x;
                $this->coords[$index][1] = $perturbedsegment->b->y;
                $intersects = $perturbedsegment->intersects($testsegment);
                if ($intersects === null) {
                    throw new coding_exception('Polygon hit test code failed '.
                                                   '- Still touching end point after perturbation');
                } else if ($intersects) {
                    $windingnumber++;
                }
            } else if ($intersects) {
                $windingnumber++;
            }
        }
        return ($windingnumber % 2) ? true : false;
    }

    /**
     * $v segment and this touch, move one of them slightly.
     * @param qtype_ddmarker_segment $v
     * @param int $ua
     * @param int $ub
     */
    public function perturb($p, $q) {
        list(, $ua, $ub) = $p->intersection_point($q);
        $pt = 0.00001; // Perturbation factor.
        $h = $p->a->dist($p->b);
        if ($ua == 0) {
            // ... q1, q2 intersects p1 exactly, move vertex p1 closer to p2.
            $a = ($pt * $p->a->dist(new qtype_ddmarker_point($p->b->x, $p->a->y))) / $h;
            $b = ($pt * $p->b->dist(new qtype_ddmarker_point($p->b->x, $p->a->y))) / $h;
            $p->a->x = $p->a->x + $a;
            $p->a->y = $p->a->y + $b;
        } else if ($ua == 1) {
            // ... q1, q2 intersects p2 exactly, move vertex p2 closer to p1.
            $a = ($pt * $p->a->dist(new qtype_ddmarker_point($p->b->x, $p->a->y))) / $h;
            $b = ($pt * $p->b->dist(new qtype_ddmarker_point($p->b->x, $p->a->y))) / $h;
            $p->b->x = $p->b->x - $a;
            $p->b->y = $p->b->y - $b;
        } else if ($ub == 0) {
            // ... p1, p2 intersects q1 exactly, move vertex q1 closer to q2.
            $a = ($pt * $q->a->dist(new qtype_ddmarker_point($q->b->x, $q->a->y))) / $h;
            $b = ($pt * $q->b->dist(new qtype_ddmarker_point($q->b->x, $q->a->y))) / $h;
            $q->a->x = $q->a->x + $a;
            $q->a->y = $q->a->y + $b;
        } else if ($ub == 1) {
            // ... p1, p2 intersects q2 exactly, move vertex q2 closer to q1.
            $a = ($pt * $q->a->dist(new qtype_ddmarker_point($q->b->x, $q->a->y))) / $h;
            $b = ($pt * $q->b->dist(new qtype_ddmarker_point($q->b->x, $q->a->y))) / $h;
            $q->b->x = $q->b->x - $a;
            $q->b->y = $q->b->y - $b;
        }
        return array($p, $q);
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
    public $x;
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


/**
 * Defines a segment between two end points a and b.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_segment {
    public $a;
    public $b;

    public function __construct(qtype_ddmarker_point $a, qtype_ddmarker_point $b) {
        $this->a = $a;
        $this->b = $b;
    }
    /**
     * Find if this segment intersects another segment $v.
     * @param segment $v
     * @return boolean does it intersect?
     */
    public function intersects(qtype_ddmarker_segment $v) {
        // Algorithm from: http://astronomy.swin.edu.au/~pbourke/geometry/lineline2d/
        // $this is P1 to P2 and $v is P3 to P4.
        list($d, $ua, $ub) = $this->intersection_point($v);
        if ($d !== 0) { // The lines intersect at a point somewhere
            // The values of $ua and $ub tell us where the intersection occurred.
            if ( (($ua == 0 || $ua == 1 )&&($ub >= 0 && $ub <= 1))
                                            || (($ub == 0 || $ub == 1) && ($ua >= 0 && $ua <= 1))) {
                // A value of exactly 0 or 1 means the intersection occurred right at the
                // start or end of the line segment. For our purposes we will consider this
                // NOT to be an intersection away from the intersecting line.
                // Degenerate case - segment exactly touches a line.
                return null;
            } else if (($ua > 0 && $ua < 1) && ($ub > 0 && $ub < 1)) {
                // A value between 0 and 1 means the intersection occurred within the
                // line segment.
                // Intersection occurs on both line segments.
                return true;
            } else {
                // The lines do not intersect within the line segments.
                return false;
            }
        } else { // The lines do not intersect.
            return false;
        }
    }

    public function intersection_point(qtype_ddmarker_segment $v) {
        $d = (($v->b->y - $v->a->y) * ($this->b->x - $this->a->x)) -
                (($v->b->x - $v->a->x) * ($this->b->y - $this->a->y));
        if ($d != 0) { // The lines intersect at a point somewhere.
            $ua = (($v->b->x - $v->a->x) * ($this->a->y - $v->a->y) -
                    ($v->b->y - $v->a->y) * ($this->a->x - $v->a->x)) / $d;
            $ub = (($this->b->x - $this->a->x) * ($this->a->y - $v->a->y) -
                    ($this->b->y - $this->a->y) * ($this->a->x - $v->a->x)) / $d;
        } else {
            $ua = null;
            $ub = null;
        }
        return array($d, $ua, $ub);
    }
}
