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

namespace core;

/**
 * A set of tests for some of the gd functionality within Moodle.
 *
 * @package    core
 * @category   test
 * @copyright  2015 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gdlib_test extends \basic_testcase {

    private $fixturepath = null;

    public function setUp(): void {
        $this->fixturepath = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR;
    }

    public function test_generate_image_thumbnail() {
        global $CFG;
        require_once($CFG->libdir . '/gdlib.php');

        // Test with meaningless data.

        // Now use a fixture.
        $pngpath = $this->fixturepath . 'gd-logo.png';
        $pngthumb = generate_image_thumbnail($pngpath, 24, 24);
        $this->assertTrue(is_string($pngthumb));

        // And check that the generated image was of the correct proportions and mimetype.
        $imageinfo = getimagesizefromstring($pngthumb);
        $this->assertEquals(24, $imageinfo[0]);
        $this->assertEquals(24, $imageinfo[1]);
        $this->assertEquals('image/png', $imageinfo['mime']);
    }

    public function test_generate_image_thumbnail_from_string() {
        global $CFG;
        require_once($CFG->libdir . '/gdlib.php');

        // Test with meaningless data.

        // First empty values.
        $this->assertFalse(generate_image_thumbnail_from_string('', 24, 24));
        $this->assertFalse(generate_image_thumbnail_from_string('invalid', 0, 24));
        $this->assertFalse(generate_image_thumbnail_from_string('invalid', 24, 0));

        // Now an invalid string.
        $this->assertFalse(generate_image_thumbnail_from_string('invalid', 24, 24));

        // Now use a fixture.
        $pngpath = $this->fixturepath . 'gd-logo.png';
        $pngdata = file_get_contents($pngpath);
        $pngthumb = generate_image_thumbnail_from_string($pngdata, 24, 24);
        $this->assertTrue(is_string($pngthumb));

        // And check that the generated image was of the correct proportions and mimetype.
        $imageinfo = getimagesizefromstring($pngthumb);
        $this->assertEquals(24, $imageinfo[0]);
        $this->assertEquals(24, $imageinfo[1]);
        $this->assertEquals('image/png', $imageinfo['mime']);
    }

    public function test_resize_image() {
        global $CFG;
        require_once($CFG->libdir . '/gdlib.php');

        $pngpath = $this->fixturepath . 'gd-logo.png';

        // Preferred height.
        $newpng = resize_image($pngpath, null, 24);
        $this->assertTrue(is_string($newpng));
        $imageinfo = getimagesizefromstring($newpng);
        $this->assertEquals(89, $imageinfo[0]);
        $this->assertEquals(24, $imageinfo[1]);
        $this->assertEquals('image/png', $imageinfo['mime']);

        // Preferred width.
        $newpng = resize_image($pngpath, 100, null);
        $this->assertTrue(is_string($newpng));
        $imageinfo = getimagesizefromstring($newpng);
        $this->assertEquals(100, $imageinfo[0]);
        $this->assertEquals(26, $imageinfo[1]);
        $this->assertEquals('image/png', $imageinfo['mime']);

        // Preferred width and height.
        $newpng = resize_image($pngpath, 50, 50);
        $this->assertTrue(is_string($newpng));
        $imageinfo = getimagesizefromstring($newpng);
        $this->assertEquals(50, $imageinfo[0]);
        $this->assertEquals(13, $imageinfo[1]);
        $this->assertEquals('image/png', $imageinfo['mime']);
    }

    public function test_resize_image_from_image() {
        global $CFG;
        require_once($CFG->libdir . '/gdlib.php');

        $pngpath = $this->fixturepath . 'gd-logo.png';
        $origimageinfo = getimagesize($pngpath);
        $imagecontent = file_get_contents($pngpath);

        // Preferred height.
        $imageresource = imagecreatefromstring($imagecontent);
        $newpng = resize_image_from_image($imageresource, $origimageinfo, null, 24);
        $this->assertTrue(is_string($newpng));
        $imageinfo = getimagesizefromstring($newpng);
        $this->assertEquals(89, $imageinfo[0]);
        $this->assertEquals(24, $imageinfo[1]);
        $this->assertEquals('image/png', $imageinfo['mime']);

        // Preferred width.
        $imageresource = imagecreatefromstring($imagecontent);
        $newpng = resize_image_from_image($imageresource, $origimageinfo, 100, null);
        $this->assertTrue(is_string($newpng));
        $imageinfo = getimagesizefromstring($newpng);
        $this->assertEquals(100, $imageinfo[0]);
        $this->assertEquals(26, $imageinfo[1]);
        $this->assertEquals('image/png', $imageinfo['mime']);

        // Preferred width and height.
        $imageresource = imagecreatefromstring($imagecontent);
        $newpng = resize_image_from_image($imageresource, $origimageinfo, 50, 50);
        $this->assertTrue(is_string($newpng));
        $imageinfo = getimagesizefromstring($newpng);
        $this->assertEquals(50, $imageinfo[0]);
        $this->assertEquals(13, $imageinfo[1]);
        $this->assertEquals('image/png', $imageinfo['mime']);
    }

}
