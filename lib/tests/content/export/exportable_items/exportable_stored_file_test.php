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
 * Unit tests for core\content\exportable_items\exportable_stored_file.
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
use stdClass;
use stored_file;

/**
 * Unit tests for the `exportable_stored_file` export item class.
 *
 * @coversDefaultClass \core\content\exportable_items\exportable_stored_file
 */
class exportable_stored_file_test extends advanced_testcase {

    /**
     * Ensure that the create_from_area_params function returns an array.
     */
    public function test_create_from_area_params_no_files(): void {
        $exportables = exportable_stored_file::create_from_area_params(
            context_system::instance(),
            'fake',
            'filearea',
            null
        );

        $this->assertIsArray($exportables);
        $this->assertCount(0, $exportables);
    }

    /**
     * Ensure that the create_from_area_params function returns a set of exportable_stored_file items, for all itemids.
     */
    public function test_create_from_area_params_no_itemid(): void {
        $this->resetAfterTest(true);

        // Setup for test.
        $user = $this->getDataGenerator()->create_user();
        $context = context_system::instance();
        $component = 'fake';
        $filearea = 'myfirstfilearea';

        $files1 = $this->create_files(context_system::instance(), $component, $filearea, 1);
        $files2 = $this->create_files(context_system::instance(), $component, $filearea, 2);
        $files3 = $this->create_files(context_system::instance(), $component, $filearea, 3);
        $files = array_values(array_merge($files1, $files2, $files3));

        $exportables = exportable_stored_file::create_from_area_params($context, $component, $filearea, null);

        $this->assertIsArray($exportables);
        $this->assertCount(3, $exportables);

        // There should be three exportables. These are listed in order of itemid.
        for ($i = 0; $i < 3; $i++) {
            $exportable = $exportables[$i];
            $file = $files[$i];

            $this->assertInstanceOf(exportable_stored_file::class, $exportable);
            $this->assert_exportable_matches_file($component, $user, $context, $filearea, '', $file, $exportable);
        }

    }

    /**
     * Ensure that the create_from_area_params function returns a set of exportable_stored_file items, for the requested
     * itemid
     */
    public function test_create_from_area_params_specified_itemid(): void {
        $this->resetAfterTest(true);

        // Setup for test.
        $user = $this->getDataGenerator()->create_user();
        $context = context_system::instance();
        $component = 'fake';
        $filearea = 'myfirstfilearea';

        $files1 = $this->create_files(context_system::instance(), $component, $filearea, 1);
        $files2 = $this->create_files(context_system::instance(), $component, $filearea, 2);
        $files3 = $this->create_files(context_system::instance(), $component, $filearea, 3);

        $exportables = exportable_stored_file::create_from_area_params($context, $component, $filearea, 2);

        $this->assertIsArray($exportables);
        $this->assertCount(1, $exportables);

        // There is only one exportable.
        $exportable = array_shift($exportables);
        $this->assertInstanceOf(exportable_stored_file::class, $exportable);

        $file2 = reset($files2);
        $this->assert_exportable_matches_file($component, $user, $context, $filearea, '', $file2, $exportable);
    }

    /**
     * Ensure that the create_from_area_params function returns a set of exportable_stored_file items, for the requested
     * itemid
     */
    public function test_create_from_area_params_in_subdir(): void {
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

        $exportables = exportable_stored_file::create_from_area_params($context, $component, $filearea, 2, 2, $subdir);

        $this->assertIsArray($exportables);
        $this->assertCount(1, $exportables);

        // There is only one exportable.
        $exportable = array_shift($exportables);
        $this->assertInstanceOf(exportable_stored_file::class, $exportable);

        $file2 = reset($files2);
        $this->assert_exportable_matches_file($component, $user, $context, $filearea, $subdir, $file2, $exportable);
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
     * @param   string $subdir
     * @param   stored_file $file
     * @param   exportable_stored_file $exportable
     */
    protected function assert_exportable_matches_file(
        string $component,
        stdClass $user,
        context $context,
        string $filearea,
        string $subdir,
        stored_file $file,
        exportable_stored_file $exportable
    ): void {
        $archive = $this->getMockBuilder(zipwriter::class)
            ->setConstructorArgs([$this->getMockBuilder(\ZipStream\ZipStream::class)->getmock()])
            ->onlyMethods([
                'add_file_from_stored_file',
            ])
            ->getMock();

        $this->assertEquals($file->get_filepath() . $file->get_filename(), $exportable->get_user_visible_name());

        $expectedfilepath = implode('/', array_filter([$subdir, $filearea, $file->get_filepath(), $file->get_filename()]));
        $expectedfilepath = preg_replace('#/+#', '/', $expectedfilepath);

        $archive->expects($this->once())
            ->method('add_file_from_stored_file')
            ->with(
                $this->equalTo($context),
                $this->equalTo($expectedfilepath),
                $this->equalTo($file)
            );

        $exportable->add_to_archive($archive);
    }
}
