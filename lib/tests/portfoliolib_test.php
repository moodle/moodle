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
 * Portfolio lib tests.
 *
 * @package    core
 * @subpackage phpunit
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->libdir.'/portfoliolib.php');
require_once($CFG->libdir.'/portfolio/formats.php');

/**
 * Portfolio lib testcase.
 *
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class portfoliolib_test extends advanced_testcase {

    public function test_portfolio_rewrite_pluginfile_urls() {
        $this->resetAfterTest();

        // File info.
        $context = context_system::instance();
        $component = 'core_test';
        $filearea = 'fixture';
        $filepath = '/';
        $itemid = 0;
        $filenameimg = 'file.png';
        $filenamepdf = 'file.pdf';

        // Store 2 test files in the pool.
        $fs = get_file_storage();
        $filerecord = array(
            'contextid' => $context->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => $filepath,
            'filename'  => $filenameimg,
        );
        $fileimg = $fs->create_file_from_string($filerecord, 'test');

        $filerecord['filename']  = $filenamepdf;
        $filepdf = $fs->create_file_from_string($filerecord, 'test');

        // Test that nothing is matching.
        $format = '';
        $options = null;
        $input = '<div>Here, the <a href="nowhere">@@PLUGINFILE@@' . $filepath . $filenamepdf .
            ' is</a> not supposed to be an actual URL placeholder.</div>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($input, $output);

        $input = '<div>Here, the <img src="nowhere" />@@PLUGINFILE@@' . $filepath . $filenameimg .
            ' is</a> not supposed to be an actual URL placeholder.</div>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($input, $output);

        // Now use our dummy format.
        $format = new core_portfolio_format_dummy();
        $options = null;

        // Test that the link is matching.
        $input = '<p>Come and <a href="@@PLUGINFILE@@' . $filepath . $filenamepdf . '">join us!</a>?</p>';
        $expected = '<p>Come and <a href="files/' . $filenamepdf . '">' . $filenamepdf . '</a>?</p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        $input = '<p>Come and <a href="@@PLUGINFILE@@' . $filepath . $filenamepdf . '"><em>join us!</em></a>?</p>';
        $expected = '<p>Come and <a href="files/' . $filenamepdf . '">' . $filenamepdf . '</a>?</p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        // Test that the image is matching.
        $input = '<p>Here is an image <img src="@@PLUGINFILE@@' . $filepath . $filenameimg . '"></p>'; // No trailing slash.
        $expected = '<p>Here is an image <img src="files/' . $filenameimg . '"/></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        $input = '<p>Here is an image <img src="@@PLUGINFILE@@' . $filepath . $filenameimg . '" /></p>'; // Trailing slash.
        $expected = '<p>Here is an image <img src="files/' . $filenameimg . '"/></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        // Test that the attributes are kept.
        $input = '<p><a title="hurray!" href="@@PLUGINFILE@@' . $filepath . $filenamepdf . '" target="_blank">join us!</a></p>';
        $expected = '<p><a title="hurray!" href="files/' . $filenamepdf . '" target="_blank">' . $filenamepdf . '</a></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        $input = '<p><img alt="before" src="@@PLUGINFILE@@' . $filepath . $filenameimg . '" title="after"/></p>';
        $expected = '<p><img alt="before" src="files/' . $filenameimg . '" title="after"/></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        // Test with more tags around.
        $input = '<p><span title="@@PLUGINFILE/a.txt"><a href="@@PLUGINFILE@@' . $filepath . $filenamepdf . '">' .
            '<em>join</em> <b>us!</b></a></span></p>';
        $expected = '<p><span title="@@PLUGINFILE/a.txt"><a href="files/' . $filenamepdf . '">' . $filenamepdf . '</a></span></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        $input = '<p><span title="@@PLUGINFILE/a.txt"><img src="@@PLUGINFILE@@' . $filepath . $filenameimg . '"/></span></p>';
        $expected = '<p><span title="@@PLUGINFILE/a.txt"><img src="files/' . $filenameimg . '"/></span></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        // Test multiple on same line.
        $input = '<p><a rel="1" href="@@PLUGINFILE@@' . $filepath . $filenamepdf . '">join us!</a>' .
            '<a rel="2" href="@@PLUGINFILE@@' . $filepath . $filenamepdf . '">join us!</a></p>';
        $expected = '<p><a rel="1" href="files/' . $filenamepdf . '">' . $filenamepdf . '</a>' .
            '<a rel="2" href="files/' . $filenamepdf . '">' . $filenamepdf . '</a></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        $input = '<p><img rel="1" src="@@PLUGINFILE@@' . $filepath . $filenameimg . '"/>' .
            '<img rel="2" src="@@PLUGINFILE@@' . $filepath . $filenameimg . '"/></p>';
        $expected = '<p><img rel="1" src="files/' . $filenameimg . '"/><img rel="2" src="files/' . $filenameimg . '"/></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);

        $input = '<p><a href="@@PLUGINFILE@@' . $filepath . $filenamepdf . '">join us!</a>' .
            '<img src="@@PLUGINFILE@@' . $filepath . $filenameimg . '"/></p>';
        $expected = '<p><a href="files/' . $filenamepdf . '">' . $filenamepdf . '</a>' .
            '<img src="files/' . $filenameimg . '"/></p>';
        $output = portfolio_rewrite_pluginfile_urls($input, $context->id, $component, $filearea, $itemid, $format, $options);
        $this->assertSame($expected, $output);
    }
}

/**
 * Dummy portfolio format.
 *
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_portfolio_format_dummy extends portfolio_format {

    public static function file_output($file, $options = null) {
        if (isset($options['attributes']) && is_array($options['attributes'])) {
            $attributes = $options['attributes'];
        } else {
            $attributes = array();
        }
        $path = 'files/' . $file->get_filename();
        return self::make_tag($file, $path, $attributes);
    }

}
