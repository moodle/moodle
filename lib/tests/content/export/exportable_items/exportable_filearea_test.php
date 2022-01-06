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
 * Unit tests for core\content\exportable_items\exportable_filearea.
 *
 * @package     core
 * @category    test
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core\content\export\exportable_items;

use advanced_testcase;
use context;
use context_system;
use core\content\export\zipwriter;
use core\content\export\exported_item;
use moodle_url;
use stdClass;

/**
 * Unit tests for the `exportable_filearea` export item class.
 *
 * @coversDefaultClass core\content\exportable_items\exportable_filearea
 */
class exportable_filearea_test extends advanced_testcase {

    /**
     * Ensure that the the exportable_filearea does not fetch files when none exist.
     */
    public function test_no_files(): void {
        $exportable = new exportable_filearea(
            context_system::instance(),
            'fake',
            'Some fake filearea',
            'filearea',
            1
        );

        $this->assertInstanceOf(exportable_filearea::class, $exportable);
    }

    /**
     * Ensure that the exportable_filearea returns all stored_file items for only the specified itemid, but those which
     * are not included in the archive receive a pluginfile URL.
     */
    public function test_specified_itemid_excluded_from_zip(): void {
        $this->resetAfterTest(true);

        // Setup for test.
        $user = $this->getDataGenerator()->create_user();
        $context = context_system::instance();
        $component = 'fake';
        $filearea = 'myfirstfilearea';

        $files1 = $this->create_files(context_system::instance(), $component, $filearea, 1);
        $files2 = $this->create_files(context_system::instance(), $component, $filearea, 2);
        $files3 = $this->create_files(context_system::instance(), $component, $filearea, 3);
        $otherfiles2 = $this->create_files(context_system::instance(), $component, "other{$filearea}", 2);

        $exportable = new exportable_filearea(
            $context,
            $component,
            'Some filearea description',
            $filearea,
            2
        );

        // There is only one exportable.
        $this->assertInstanceOf(exportable_filearea::class, $exportable);

        $file2 = reset($files2);
        $item = $this->assert_exportable_matches_file($component, $user, $context, $filearea, '', $files2, false, $exportable);
        $this->assertCount(count($files2), $item->get_all_files());
        $comparisonurl = new moodle_url('/tokenpluginfile.php/');
        foreach ($item->get_all_files() as $url) {
            $this->assertStringStartsWith($comparisonurl->out(false), $url->filepath);
        }
    }

    /**
     * Ensure that the exportable_filearea returns all stored_file items for only the specified itemid.
     */
    public function test_specified_itemid(): void {
        $this->resetAfterTest(true);

        // Setup for test.
        $user = $this->getDataGenerator()->create_user();
        $context = context_system::instance();
        $component = 'fake';
        $filearea = 'myfirstfilearea';

        $files1 = $this->create_files(context_system::instance(), $component, $filearea, 1);
        $files2 = $this->create_files(context_system::instance(), $component, $filearea, 2);
        $files3 = $this->create_files(context_system::instance(), $component, $filearea, 3);
        $otherfiles2 = $this->create_files(context_system::instance(), $component, "other{$filearea}", 2);

        $exportable = new exportable_filearea(
            $context,
            $component,
            'Some filearea description',
            $filearea,
            2
        );

        // There is only one exportable.
        $this->assertInstanceOf(exportable_filearea::class, $exportable);

        $file2 = reset($files2);
        $item = $this->assert_exportable_matches_file($component, $user, $context, $filearea, '', $files2, true, $exportable);
        $this->assertCount(count($files2), $item->get_all_files());
    }

    /**
     * Ensure that the exportable_filearea returns all stored_files into the correct file location.
     */
    public function test_in_subdir(): void {
        $this->resetAfterTest(true);

        // Setup for test.
        $user = $this->getDataGenerator()->create_user();
        $context = context_system::instance();
        $component = 'fake';
        $filearea = 'myfirstfilearea';
        $subdir = 'a/path/to/my/subdir';

        $files1 = $this->create_files(context_system::instance(), $component, $filearea, 1);
        $files2 = $this->create_files(context_system::instance(), $component, $filearea, 2);
        $files3 = $this->create_files(context_system::instance(), $component, $filearea, 3);

        $exportable = new exportable_filearea(
            $context,
            $component,
            'Some filearea description',
            $filearea,
            2,
            2,
            $subdir
        );

        // There is only one exportable.
        $this->assertInstanceOf(exportable_filearea::class, $exportable);

        $item = $this->assert_exportable_matches_file($component, $user, $context, $filearea, $subdir, $files2, true, $exportable);
        $this->assertCount(count($files2), $item->get_all_files());
    }

    /**
     * Create files for use in testing.
     *
     * @param   context $context
     * @param   string $component
     * @param   string $filearea
     * @param   int $itemid
     * @param   int $count
     * @return  filearea[]
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
     * @param   string $subdir
     * @param   stored_file[] $expectedfiles
     * @param   bool $addfilestozip Whether to allow files to be added to the archive
     * @param   exportable_filearea $exportable
     * @return  exported_item
     */
    protected function assert_exportable_matches_file(
        string $component,
        stdClass $user,
        context $context,
        string $filearea,
        string $subdir,
        array $expectedfiles,
        bool $addfilestozip,
        exportable_filearea $exportable
    ): exported_item {
        $archive = $this->getMockBuilder(zipwriter::class)
            ->setConstructorArgs([$this->getMockBuilder(\ZipStream\ZipStream::class)->getmock()])
            ->setMethods([
                'add_file_from_stored_file',
                'is_file_in_archive',
            ])
            ->getMock();

        $archive->expects($this->any())
            ->method('is_file_in_archive')
            ->willReturn($addfilestozip);

        $storedfileargs = [];
        foreach ($expectedfiles as $file) {
            $filepathinzip = $subdir . '/' . $file->get_filearea() . '/' . $file->get_filepath() . $file->get_filename();
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

        return $exportable->add_to_archive($archive);
    }
}
