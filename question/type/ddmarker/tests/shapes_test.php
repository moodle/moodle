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
 * Unit tests for the drag-and-drop words shape code.
 *
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/type/ddmarker/shapes.php');


/**
 * Unit tests for shape code
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_shapes_test extends basic_testcase {

    public function test_polygon_valdiation_test_ok() {
        $shape = new qtype_ddmarker_shape_polygon('10, 10; 20, 10; 20, 20; 10, 20');
        $this->assertFalse($shape->get_coords_interpreter_error()); // No errors.
    }

    public function test_polygon_valdiation_test_only_two_points() {
        $shape = new qtype_ddmarker_shape_polygon('10, 10; 20, 10');
        $this->assertEquals(get_string('formerror_polygonmusthaveatleastthreepoints', 'qtype_ddmarker',
                        array('shape' => 'polygon', 'coordsstring' => get_string('shape_polygon_coords', 'qtype_ddmarker'))),
                $shape->get_coords_interpreter_error());
    }

    public function test_polygon_valdiation_test_invalid_point() {
        $shape = new qtype_ddmarker_shape_polygon('10, 10; 20, ; 20, 20; 10, 20');
        $this->assertEquals(get_string('formerror_onlyusewholepositivenumbers', 'qtype_ddmarker',
                        array('shape' => 'polygon', 'coordsstring' => get_string('shape_polygon_coords', 'qtype_ddmarker'))),
                $shape->get_coords_interpreter_error());
    }

    public function test_polygon_valdiation_test_repeated_point() {
        $shape = new qtype_ddmarker_shape_polygon('70,220;90,200;95,150;120,150;140,200;150,230;150,230;150,240;120,240;110,240;90,240');
        $this->assertEquals(get_string('formerror_repeatedpoint', 'qtype_ddmarker',
                        array('shape' => 'polygon', 'coordsstring' => get_string('shape_polygon_coords', 'qtype_ddmarker'))),
                $shape->get_coords_interpreter_error());
    }

    public function test_polygon_hit_test() {
        $shape = new qtype_ddmarker_shape_polygon('10, 10; 20, 10; 20, 20; 10, 20');
        $this->assertTrue($shape->is_point_in_shape(array(15, 15)));
        $this->assertFalse($shape->is_point_in_shape(array(5, 5)));
        $this->assertFalse($shape->is_point_in_shape(array(5, 15)));
        $this->assertFalse($shape->is_point_in_shape(array(15, 25)));
        $this->assertFalse($shape->is_point_in_shape(array(25, 15)));
        $this->assertTrue($shape->is_point_in_shape(array(11, 11)));
        $this->assertTrue($shape->is_point_in_shape(array(19, 19)));

        // Should accept closed polygon coords or unclosed and it will model a closed polygon.
        $shape = new qtype_ddmarker_shape_polygon('10, 10; 20, 10; 20, 20; 10, 20; 10, 10');
        $this->assertTrue($shape->is_point_in_shape(array(15, 15)));
        $this->assertFalse($shape->is_point_in_shape(array(5, 5)));
        $this->assertFalse($shape->is_point_in_shape(array(5, 15)));
        $this->assertFalse($shape->is_point_in_shape(array(15, 25)));
        $this->assertFalse($shape->is_point_in_shape(array(25, 15)));
        $this->assertTrue($shape->is_point_in_shape(array(11, 11)));
        $this->assertTrue($shape->is_point_in_shape(array(19, 19)));

        $shape = new qtype_ddmarker_shape_polygon('10, 10; 15, 5; 20, 10; 20, 20; 10, 20');
        $this->assertTrue($shape->is_point_in_shape(array(15, 15)));
        $this->assertFalse($shape->is_point_in_shape(array(5, 5)));
        $this->assertFalse($shape->is_point_in_shape(array(5, 15)));
        $this->assertFalse($shape->is_point_in_shape(array(15, 25)));
        $this->assertFalse($shape->is_point_in_shape(array(25, 15)));
        $this->assertTrue($shape->is_point_in_shape(array(11, 11)));
        $this->assertTrue($shape->is_point_in_shape(array(19, 19)));
        $this->assertTrue($shape->is_point_in_shape(array(15, 9)));
        $this->assertTrue($shape->is_point_in_shape(array(15, 10)));

        $shape = new qtype_ddmarker_shape_polygon('15, 5; 20, 10; 20, 20; 10, 20; 10, 10');
        $this->assertTrue($shape->is_point_in_shape(array(15, 10)));

        $shape = new qtype_ddmarker_shape_polygon('15, 5; 20, 10; 20, 20; 10, 20; 10, 10');
        $this->assertFalse($shape->is_point_in_shape(array(25, 10)));

        $shape = new qtype_ddmarker_shape_polygon('0, 0; 500, 0; 600, 1000; 0, 1200; 10, 10');
        $this->assertTrue($shape->is_point_in_shape(array(25, 10)));
    }

    public function test_circle_valdiation_test() {
        $shape = new qtype_ddmarker_shape_circle('10, 10; 10');
        $this->assertFalse($shape->get_coords_interpreter_error()); // No errors.
    }

    public function test_circle_hit_test() {
        $shape = new qtype_ddmarker_shape_circle('10, 10; 10');
        $this->assertTrue($shape->is_point_in_shape(array(19, 10)));
        $this->assertFalse($shape->is_point_in_shape(array(20, 10)));
        $this->assertTrue($shape->is_point_in_shape(array(10, 1)));
        $this->assertFalse($shape->is_point_in_shape(array(15, 25)));
        $this->assertFalse($shape->is_point_in_shape(array(25, 15)));
        $this->assertTrue($shape->is_point_in_shape(array(11, 11)));
        $this->assertTrue($shape->is_point_in_shape(array(1, 10)));
        $this->assertTrue($shape->is_point_in_shape(array(17, 17)));
        $this->assertTrue($shape->is_point_in_shape(array(3, 3)));
        $this->assertFalse($shape->is_point_in_shape(array(2, 2)));
    }

    public function test_rectangle_valdiation_test() {
        $shape = new qtype_ddmarker_shape_rectangle('1000, 4000; 500, 400');
        $this->assertFalse($shape->get_coords_interpreter_error()); // No errors.
    }

    public function test_rectangle_hit_test() {
        $shape = new qtype_ddmarker_shape_rectangle('1000, 4000; 500, 400');
        $this->assertTrue($shape->is_point_in_shape(array(1001, 4001)));
        $this->assertFalse($shape->is_point_in_shape(array(1000, 4000)));
        $this->assertFalse($shape->is_point_in_shape(array(501, 3601)));
        $this->assertTrue($shape->is_point_in_shape(array(1499, 4399)));
        $this->assertFalse($shape->is_point_in_shape(array(25, 15)));
        $this->assertTrue($shape->is_point_in_shape(array(1001, 4399)));
        $this->assertTrue($shape->is_point_in_shape(array(1499, 4001)));
    }
}
