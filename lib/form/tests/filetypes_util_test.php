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
 * Provides the {@link core_form\filetypes_util_testcase} class.
 *
 * @package     core_form
 * @category    test
 * @copyright   2017 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_form;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Test cases for the {@link core_form\filetypes_util} class.
 *
 * @copyright 2017 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filetypes_util_testcase extends advanced_testcase {

    /**
     * Test normalizing list of extensions.
     */
    public function test_normalize_file_types() {

        $this->resetAfterTest(true);
        $util = new filetypes_util();

        $this->assertSame(['.odt'], $util->normalize_file_types('.odt'));
        $this->assertSame(['.odt'], $util->normalize_file_types('odt'));
        $this->assertSame(['.odt'], $util->normalize_file_types('.ODT'));
        $this->assertSame(['.doc', '.jpg', '.mp3'], $util->normalize_file_types('doc, jpg, mp3'));
        $this->assertSame(['.doc', '.jpg', '.mp3'], $util->normalize_file_types(['.doc', '.jpg', '.mp3']));
        $this->assertSame(['.doc', '.jpg', '.mp3'], $util->normalize_file_types('doc, *.jpg, mp3'));
        $this->assertSame(['.doc', '.jpg', '.mp3'], $util->normalize_file_types(['doc ', ' JPG ', '.mp3']));
        $this->assertSame(['.rtf', '.pdf', '.docx'],
            $util->normalize_file_types("RTF,.pdf\n...DocX,,,;\rPDF\trtf ...Rtf"));
        $this->assertSame(['.tgz', '.tar.gz'], $util->normalize_file_types('tgz,TAR.GZ tar.gz .tar.gz tgz TGZ'));
        $this->assertSame(['.notebook'], $util->normalize_file_types('"Notebook":notebook;NOTEBOOK;,\'NoTeBook\''));
        $this->assertSame([], $util->normalize_file_types(''));
        $this->assertSame([], $util->normalize_file_types([]));
        $this->assertSame(['.0'], $util->normalize_file_types(0));
        $this->assertSame(['.0'], $util->normalize_file_types('0'));
        $this->assertSame(['.odt'], $util->normalize_file_types('*.odt'));
        $this->assertSame([], $util->normalize_file_types('.'));
        $this->assertSame(['.foo'], $util->normalize_file_types('. foo'));
        $this->assertSame(['*'], $util->normalize_file_types('*'));
        $this->assertSame([], $util->normalize_file_types('*~'));
        $this->assertSame(['.pdf', '.ps'], $util->normalize_file_types('pdf *.ps foo* *bar .r??'));
        $this->assertSame(['*'], $util->normalize_file_types('pdf *.ps foo* * *bar .r??'));
    }

    /**
     * Test MIME type formal recognition.
     */
    public function test_looks_like_mimetype() {

        $this->resetAfterTest(true);
        $util = new filetypes_util();

        $this->assertTrue($util->looks_like_mimetype('type/subtype'));
        $this->assertTrue($util->looks_like_mimetype('type/x-subtype'));
        $this->assertTrue($util->looks_like_mimetype('type/x-subtype+xml'));
        $this->assertTrue($util->looks_like_mimetype('type/vnd.subtype.xml'));
        $this->assertTrue($util->looks_like_mimetype('type/vnd.subtype+xml'));

        $this->assertFalse($util->looks_like_mimetype('.gif'));
        $this->assertFalse($util->looks_like_mimetype('audio'));
        $this->assertFalse($util->looks_like_mimetype('foo/bar/baz'));
    }

    /**
     * Test getting/checking group.
     */
    public function test_is_filetype_group() {

        $this->resetAfterTest(true);
        $util = new filetypes_util();

        $audio = $util->is_filetype_group('audio');
        $this->assertNotFalse($audio);
        $this->assertInternalType('array', $audio->extensions);
        $this->assertInternalType('array', $audio->mimetypes);

        $this->assertFalse($util->is_filetype_group('.gif'));
        $this->assertFalse($util->is_filetype_group('somethingveryunlikelytoeverexist'));
    }


    /**
     * Test describing list of extensions.
     */
    public function test_describe_file_types() {

        $this->resetAfterTest(true);
        $util = new filetypes_util();

        force_current_language('en');

        // Check that it is able to describe individual file extensions.
        $desc = $util->describe_file_types('jpg .jpeg *.jpe PNG;.gif,  mudrd8mz');
        $this->assertTrue($desc->hasdescriptions);

        $desc = $desc->descriptions;
        $this->assertEquals(4, count($desc));

        $this->assertEquals('File', $desc[0]->description);
        $this->assertEquals('.mudrd8mz', $desc[0]->extensions);

        $this->assertEquals('Image (JPEG)', $desc[2]->description);
        $this->assertContains('.jpg', $desc[2]->extensions);
        $this->assertContains('.jpeg', $desc[2]->extensions);
        $this->assertContains('.jpe', $desc[2]->extensions);

        // Check that it can describe groups and mimetypes too.
        $desc = $util->describe_file_types('audio text/plain');
        $this->assertTrue($desc->hasdescriptions);

        $desc = $desc->descriptions;
        $this->assertEquals(2, count($desc));

        $this->assertEquals('Audio files', $desc[0]->description);
        $this->assertContains('.mp3', $desc[0]->extensions);
        $this->assertContains('.wav', $desc[0]->extensions);
        $this->assertContains('.ogg', $desc[0]->extensions);

        $this->assertEquals('Text file', $desc[1]->description);
        $this->assertContains('.txt', $desc[1]->extensions);

        // Empty.
        $desc = $util->describe_file_types('');
        $this->assertFalse($desc->hasdescriptions);
        $this->assertEmpty($desc->descriptions);

        // Any.
        $desc = $util->describe_file_types('*');
        $this->assertTrue($desc->hasdescriptions);
        $this->assertNotEmpty($desc->descriptions[0]->description);
        $this->assertEmpty($desc->descriptions[0]->extensions);

        // Unknown mimetype.
        $desc = $util->describe_file_types('application/x-something-really-unlikely-ever-exist');
        $this->assertTrue($desc->hasdescriptions);
        $this->assertEquals('application/x-something-really-unlikely-ever-exist', $desc->descriptions[0]->description);
        $this->assertEmpty($desc->descriptions[0]->extensions);
    }
}
