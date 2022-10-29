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

declare(strict_types=1);

namespace core\content\export\exportable_items;

use advanced_testcase;
use context;
use context_module;
use context_system;
use core\content\export\zipwriter;
use moodle_url;
use stdClass;

/**
 * Unit tests for the `exportable_textarea` export item class.
 *
 * @package     core
 * @category    test
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \core\content\exportable_items\exportable_textarea
 */
class exportable_textarea_test extends advanced_testcase {

    /**
     * Ensure that an exportable textarea which does not relate to any content, does not attempt to export any content.
     */
    public function test_valid_table_without_content(): void {
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();

        $context = context_system::instance();
        $component = 'page';
        $uservisiblename = 'Page content';
        $tablename = 'page';
        $fieldname = 'content';
        $fieldid = -1;
        $formatfieldname = 'contentformat';
        $filearea = 'content';

        $exportable = new exportable_textarea(
            $context,
            $component,
            $uservisiblename,
            $tablename,
            $fieldname,
            $fieldid,
            $formatfieldname
        );

        $this->assertInstanceOf(exportable_textarea::class, $exportable);

        $this->assert_exportable_empty($component, $user, $context, $exportable);
    }

    /**
     * Ensure that the an exportable textarea exports content from the appropriate locations, but without any files.
     */
    public function test_valid_table_with_content_no_filearea_specified(): void {
        $this->resetAfterTest(true);

        $content = '<h1>Hello</h1><p>World!</p>';

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', (object) [
            'course' => $course,
            'content' => $content,
            'contentformat' => FORMAT_HTML,
        ]);

        $context = context_module::instance($page->cmid);
        $expectedfiles = $this->create_files($context, 'mod_page', 'content', (int) $page->id, 5);

        // Unexpected files.
        $this->create_files($context, 'mod_page', 'content', (int) $page->id + 1, 5);
        $this->create_files($context, 'mod_page', 'othercontent', (int) $page->id, 5);
        $this->create_files($context, 'mod_foo', 'content', (int) $page->id, 5);

        $component = 'page';
        $uservisiblename = 'Page content';
        $tablename = 'page';
        $fieldname = 'content';
        $fieldid = (int) $page->id;
        $formatfieldname = 'contentformat';

        $exportable = new exportable_textarea(
            $context,
            $component,
            $uservisiblename,
            $tablename,
            $fieldname,
            $fieldid,
            $formatfieldname
        );

        $this->assertInstanceOf(exportable_textarea::class, $exportable);

        // Although files exist, the filearea and itemid were not included.
        $this->assert_exportable_matches_file($component, $user, $context, null, $content, [], '', $exportable);
    }

    /**
     * Ensure that the an exportable textarea exports content from the appropriate locations, but without any files.
     */
    public function test_valid_table_with_content_no_itemid_specified(): void {
        $this->resetAfterTest(true);

        $content = '<h1>Hello</h1><p>World!</p>';

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', (object) [
            'course' => $course,
            'content' => $content,
            'contentformat' => FORMAT_HTML,
        ]);

        $context = context_module::instance($page->cmid);
        $expectedfiles = $this->create_files($context, 'mod_page', 'content', (int) $page->id, 5);

        // Unexpected files.
        $this->create_files($context, 'mod_page', 'content', (int) $page->id + 1, 5);
        $this->create_files($context, 'mod_page', 'othercontent', (int) $page->id, 5);
        $this->create_files($context, 'mod_foo', 'content', (int) $page->id, 5);

        $component = 'page';
        $uservisiblename = 'Page content';
        $tablename = 'page';
        $fieldname = 'content';
        $fieldid = (int) $page->id;
        $formatfieldname = 'contentformat';
        $filearea = 'content';

        $exportable = new exportable_textarea(
            $context,
            $component,
            $uservisiblename,
            $tablename,
            $fieldname,
            $fieldid,
            $formatfieldname,
            $filearea
        );

        $this->assertInstanceOf(exportable_textarea::class, $exportable);

        // Although files exist, the filearea and itemid were not included.
        $this->assert_exportable_matches_file($component, $user, $context, null, $content, [], '', $exportable);
    }

    /**
     * Ensure that the an exportable textarea exports content from the appropriate locations, with files.
     */
    public function test_valid_table_with_content_and_files(): void {
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();

        $contentin = <<<EOF
<h1>Hello</h1><p>World!</p>
<img src='@@PLUGINFILE@@/file.txt'>
<img src='@@PLUGINFILE@@/other/file.txt'>
EOF;
        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', (object) [
            'course' => $course,
            'content' => $contentin,
            'contentformat' => FORMAT_HTML,
        ]);

        $this->setUser($user);

        $context = context_module::instance($page->cmid);
        $expectedfiles = $this->create_files(
            $context,
            'mod_page',
            'content',
            (int) $page->id,
            5,
            'contentformat',
            'content',
            (int) $page->id,
            5
        );

        // Unexpected files.
        $this->create_files($context, 'mod_page', 'content', (int) $page->id + 1, 5);
        $this->create_files($context, 'mod_page', 'othercontent', (int) $page->id, 5);
        $this->create_files($context, 'mod_foo', 'content', (int) $page->id, 5);

        $component = 'mod_page';
        $uservisiblename = 'Page content';
        $tablename = 'page';
        $fieldname = 'content';
        $fieldid = (int) $page->id;
        $formatfieldname = 'contentformat';
        $filearea = 'content';
        $itemid = (int) $page->id;

        $exportable = new exportable_textarea(
            $context,
            $component,
            $uservisiblename,
            $tablename,
            $fieldname,
            $fieldid,
            $formatfieldname,
            $filearea,
            $itemid,
            null
        );

        $this->assertInstanceOf(exportable_textarea::class, $exportable);

        $pluginfilebase = moodle_url::make_pluginfile_url(
            $context->id, $component, $filearea, null, '', '', false, true
        )->out(false);
        $expectedcontent = <<<EOF
<h1>Hello</h1><p>World!</p>
<img src='content/file.txt'>
<img src='{$pluginfilebase}/other/file.txt'>
EOF;

        // Although files exist, the filearea and itemid were not included.
        $this->assert_exportable_matches_file(
            $component, $user, $context, $filearea, $expectedcontent, $expectedfiles, '', $exportable
        );
    }

    /**
     * Create files for use in testing.
     *
     * @param   context $context
     * @param   string $component
     * @param   string $filearea
     * @param   int $itemid
     * @param   int $count
     * @return  stored_file[]
     */
    protected function create_files(context $context, string $component, string $filearea, int $itemid, int $count = 1): array {
        $fs = get_file_storage();

        $files = [];
        for ($i = 0; $i < $count; $i++) {

            $filepath = '/';
            for ($j = 0; $j < $i; $j++) {
                $filepath .= "{$j}/";
            }

            $files[] = $fs->create_file_from_string(
                (object) [
                    'contextid' => $context->id,
                    'component' => $component,
                    'filearea' => $filearea,
                    'filepath' => $filepath,
                    'filename' => "file.txt",
                    'itemid' => $itemid,
                ],
                "File content: {$i}"
            );
        }

        return $files;
    }

    /**
     * Assert that the supplied expotable matches the supplied file.
     *
     * @param   string $component
     * @param   stdClass $user
     * @param   context $context
     * @param   string $filearea
     * @param   string $content
     * @param   stored_file[] $expectedfiles
     * @param   string $subdir
     * @param   exportable_textarea $exportable
     */
    protected function assert_exportable_matches_file(
        string $component,
        stdClass $user,
        context $context,
        ?string $filearea,
        string $content,
        array $expectedfiles,
        string $subdir,
        exportable_textarea $exportable
    ): void {
        $archive = $this->getMockBuilder(zipwriter::class)
            ->setConstructorArgs([$this->getMockBuilder(\ZipStream\ZipStream::class)->getmock()])
            ->onlyMethods([
                'is_file_in_archive',
                'add_file_from_string',
                'add_file_from_stored_file',
            ])
            ->getMock();

        $archive->expects($this->any())
            ->method('is_file_in_archive')
            ->willReturn(true);

        $storedfileargs = [];
        foreach ($expectedfiles as $file) {
            $filepathinzip = dirname($subdir) . $file->get_filearea() . '/' . $file->get_filepath() . $file->get_filename();
            $filepathinzip = ltrim(preg_replace('#/+#', '/', $filepathinzip), '/');
            $storedfileargs[] = [
                $this->equalTo($context),
                $this->equalTo($filepathinzip),
                $this->equalTo($file),
            ];
        }

        $archive->expects($this->exactly(count($expectedfiles)))
            ->method('add_file_from_stored_file')
            ->withConsecutive(...$storedfileargs);

        $archive->expects($this->never())
            ->method('add_file_from_string');

        $exportable->add_to_archive($archive);
    }

    /**
     * Assert that the supplied expotable matches the supplied file.
     *
     * @param   string $component
     * @param   stdClass $user
     * @param   context $context
     * @param   exportable_textarea $exportable
     */
    protected function assert_exportable_empty(
        string $component,
        stdClass $user,
        context $context,
        exportable_textarea $exportable
    ): void {
        $archive = $this->getMockBuilder(zipwriter::class)
            ->setConstructorArgs([$this->getMockBuilder(\ZipStream\ZipStream::class)->getmock()])
            ->onlyMethods([
                'add_file_from_stored_file',
                'add_file_from_string',
                'add_file_from_template',
            ])
            ->getMock();

        $archive->expects($this->never())
            ->method('add_file_from_stored_file');
        $archive->expects($this->never())
            ->method('add_file_from_string');
        $archive->expects($this->never())
            ->method('add_file_from_template');

        $exportable->add_to_archive($archive);
    }
}
