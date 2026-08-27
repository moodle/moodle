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

namespace core_course;

use core\exception\moodle_exception;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Unit tests for the drag and drop upload AJAX processor.
 *
 * @package    core_course
 * @category   test
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(dndupload_ajax_processor::class)]
final class dndupload_ajax_processor_test extends \advanced_testcase {
    /**
     * A non-decorative image keeps its alternative text, trimmed and capped to the maximum length.
     */
    public function test_sanitise_image_details_keeps_alt(): void {
        $details = dndupload_ajax_processor::sanitise_image_details([
            'alt' => 'A photo of a beach',
            'presentation' => 0,
            'width' => 320,
            'height' => 240,
        ]);

        $this->assertSame('A photo of a beach', $details['alt']);
        $this->assertFalse($details['presentation']);
        $this->assertSame(320, $details['width']);
        $this->assertSame(240, $details['height']);
    }

    /**
     * Leading and trailing whitespace is trimmed from the stored alternative text.
     */
    public function test_sanitise_image_details_trims_alt(): void {
        $details = dndupload_ajax_processor::sanitise_image_details([
            'alt' => "  A photo of a beach  \n",
            'presentation' => 0,
        ]);

        $this->assertSame('A photo of a beach', $details['alt']);
    }

    /**
     * Alternative text longer than the maximum length is truncated server-side.
     */
    public function test_sanitise_image_details_caps_alt_length(): void {
        $longalt = str_repeat('a', dndupload_ajax_processor::IMAGE_ALT_MAXLENGTH + 100);

        $details = dndupload_ajax_processor::sanitise_image_details([
            'alt' => $longalt,
            'presentation' => 0,
        ]);

        $this->assertSame(dndupload_ajax_processor::IMAGE_ALT_MAXLENGTH, \core_text::strlen($details['alt']));
    }

    /**
     * A decorative image needs no alternative text and is stored with an empty alt.
     */
    public function test_sanitise_image_details_decorative_allows_empty_alt(): void {
        $details = dndupload_ajax_processor::sanitise_image_details([
            'alt' => '',
            'presentation' => 1,
        ]);

        $this->assertTrue($details['presentation']);
        $this->assertSame('', $details['alt']);
    }

    /**
     * A decorative image ignores any supplied alternative text.
     */
    public function test_sanitise_image_details_decorative_clears_alt(): void {
        $details = dndupload_ajax_processor::sanitise_image_details([
            'alt' => 'This should be dropped for a decorative image',
            'presentation' => 1,
        ]);

        $this->assertSame('', $details['alt']);
    }

    /**
     * A non-decorative image with no alternative text is rejected, mirroring the client-side rule.
     */
    public function test_sanitise_image_details_requires_alt_when_not_decorative(): void {
        $this->expectException(moodle_exception::class);
        dndupload_ajax_processor::sanitise_image_details([
            'alt' => '   ',
            'presentation' => 0,
        ]);
    }
}
