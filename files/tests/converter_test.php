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
 * PHPUnit tests for fileconverter API.
 *
 * @package    core_files
 * @copyright  2017 Andrew nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

use core_files\conversion;
use core_files\converter;

/**
 * PHPUnit tests for fileconverter API.
 *
 * @package    core_files
 * @copyright  2017 Andrew nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class converter_test extends advanced_testcase {

    /**
     * Get a testable mock of the abstract files_converter class.
     *
     * @param   array   $mockedmethods A list of methods you intend to override
     *                  If no methods are specified, only abstract functions are mocked.
     * @return  \core_files\converter
     */
    protected function get_testable_mock($mockedmethods = []) {
        $converter = $this->getMockBuilder(\core_files\converter::class)
            ->onlyMethods($mockedmethods)
            ->getMockForAbstractClass();

        return $converter;
    }

    /**
     * Get a testable mock of the conversion.
     *
     * @param   array   $mockedmethods A list of methods you intend to override
     * @return  \core_files\conversion
     */
    protected function get_testable_conversion($mockedmethods = []) {
        $conversion = $this->getMockBuilder(\core_files\conversion::class)
            ->onlyMethods($mockedmethods)
            ->setConstructorArgs([0, (object) []])
            ->getMock();

        return $conversion;
    }

    /**
     * Get a testable mock of the abstract files_converter class.
     *
     * @param   array   $mockedmethods A list of methods you intend to override
     *                  If no methods are specified, only abstract functions are mocked.
     * @return  \core_files\converter_interface
     */
    protected function get_mocked_converter($mockedmethods = []) {
        $converter = $this->getMockBuilder(\core_files\converter_interface::class)
            ->onlyMethods($mockedmethods)
            ->getMockForAbstractClass();

        return $converter;
    }

    /**
     * Helper to create a stored file objectw with the given supplied content.
     *
     * @param   string  $filecontent The content of the mocked file
     * @param   string  $filename The file name to use in the stored_file
     * @param   array   $mockedmethods A list of methods you intend to override
     *                  If no methods are specified, only abstract functions are mocked.
     * @return  stored_file
     */
    protected function get_stored_file($filecontent = 'content', $filename = null, $filerecord = [], $mockedmethods = []) {
        global $CFG;

        $contenthash = sha1($filecontent);
        if (empty($filename)) {
            $filename = $contenthash;
        }

        $filerecord['contenthash'] = $contenthash;
        $filerecord['filesize'] = strlen($filecontent);
        $filerecord['filename'] = $filename;
        $filerecord['id'] = 42;

        $file = $this->getMockBuilder(stored_file::class)
            ->onlyMethods($mockedmethods)
            ->setConstructorArgs([get_file_storage(), (object) $filerecord])
            ->getMock();

        return $file;
    }

    /**
     * Helper to create a stored file object with the given supplied content.
     *
     * @param   string $filecontent The content of the mocked file
     * @param   string $filename The file name to use in the stored_file
     * @param   string $filerecord Any overrides to the filerecord
     * @return  stored_file
     */
    protected function create_stored_file($filecontent = 'content', $filename = 'testfile.txt', $filerecord = []) {
        $filerecord = array_merge([
                'contextid' => context_system::instance()->id,
                'component' => 'core',
                'filearea'  => 'unittest',
                'itemid'    => 0,
                'filepath'  => '/',
                'filename'  => $filename,
            ], $filerecord);

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $filecontent);

        return $file;
    }

    /**
     * Get a mock of the file_storage API.
     *
     * @param   array   $mockedmethods A list of methods you intend to override
     * @return  file_storage
     */
    protected function get_file_storage_mock($mockedmethods = []) {
        $fs = $this->getMockBuilder(\file_storage::class)
            ->onlyMethods($mockedmethods)
            ->disableOriginalConstructor()
            ->getMock();

        return $fs;
    }

    /**
     * Test the start_conversion function.
     */
    public function test_start_conversion_existing_single(): void {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();

        $first = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
            ]);
        $first->create();

        $converter = $this->get_testable_mock(['poll_conversion']);
        $conversion = $converter->start_conversion($sourcefile, 'pdf', false);

        // The old conversions should still be present and match the one returned.
        $this->assertEquals($first->get('id'), $conversion->get('id'));
    }

    /**
     * Test the start_conversion function.
     */
    public function test_start_conversion_existing_multiple(): void {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();

        $first = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
            ]);
        $first->create();

        $second = new conversion(0, (object) [
                'sourcefileid' => $sourcefile->get_id(),
                'targetformat' => 'pdf',
            ]);
        $second->create();

        $converter = $this->get_testable_mock(['poll_conversion']);
        $conversion = $converter->start_conversion($sourcefile, 'pdf', false);

        // The old conversions should have been removed.
        $this->assertFalse(conversion::get_record(['id' => $first->get('id')]));
        $this->assertFalse(conversion::get_record(['id' => $second->get('id')]));
    }

    /**
     * Test the start_conversion function.
     */
    public function test_start_conversion_no_existing(): void {
        $this->resetAfterTest();

        $sourcefile = $this->create_stored_file();

        $converter = $this->get_testable_mock(['poll_conversion']);
        $conversion = $converter->start_conversion($sourcefile, 'pdf', false);

        $this->assertInstanceOf(\core_files\conversion::class, $conversion);
    }

    /**
     * Test the get_document_converter_classes function with no enabled plugins.
     */
    public function test_get_document_converter_classes_no_plugins(): void {
        $converter = $this->get_testable_mock(['get_enabled_plugins']);
        $converter->method('get_enabled_plugins')->willReturn([]);

        $method = new ReflectionMethod(\core_files\converter::class, 'get_document_converter_classes');
        $result = $method->invokeArgs($converter, ['docx', 'pdf']);
        $this->assertEmpty($result);
    }

    /**
     * Test the get_document_converter_classes function when no class was found.
     */
    public function test_get_document_converter_classes_plugin_class_not_found(): void {
        $converter = $this->get_testable_mock(['get_enabled_plugins']);
        $converter->method('get_enabled_plugins')->willReturn([
                'noplugin' => '\not\a\real\plugin',
            ]);

        $method = new ReflectionMethod(\core_files\converter::class, 'get_document_converter_classes');
        $result = $method->invokeArgs($converter, ['docx', 'pdf']);
        $this->assertEmpty($result);
    }

    /**
     * Test the get_document_converter_classes function when the returned classes do not meet requirements.
     */
    public function test_get_document_converter_classes_plugin_class_requirements_not_met(): void {
        $plugin = $this->getMockBuilder(\core_file_converter_requirements_not_met::class)
            ->onlyMethods([])
            ->getMock();

        $converter = $this->get_testable_mock(['get_enabled_plugins']);
        $converter->method('get_enabled_plugins')->willReturn([
                'test_plugin' => get_class($plugin),
            ]);

        $method = new ReflectionMethod(\core_files\converter::class, 'get_document_converter_classes');
        $result = $method->invokeArgs($converter, ['docx', 'pdf']);
        $this->assertEmpty($result);
    }

    /**
     * Test the get_document_converter_classes function when the returned classes do not meet requirements.
     */
    public function test_get_document_converter_classes_plugin_class_met_not_supported(): void {
        $plugin = $this->getMockBuilder(\core_file_converter_type_not_supported::class)
            ->onlyMethods([])
            ->getMock();

        $converter = $this->get_testable_mock(['get_enabled_plugins']);
        $converter->method('get_enabled_plugins')->willReturn([
                'test_plugin' => get_class($plugin),
            ]);

        $method = new ReflectionMethod(\core_files\converter::class, 'get_document_converter_classes');
        $result = $method->invokeArgs($converter, ['docx', 'pdf']);
        $this->assertEmpty($result);
    }

    /**
     * Test the get_document_converter_classes function when the returned classes do not meet requirements.
     */
    public function test_get_document_converter_classes_plugin_class_met_and_supported(): void {
        $plugin = $this->getMockBuilder(\core_file_converter_type_supported::class)
            ->onlyMethods([])
            ->getMock();
        $classname = get_class($plugin);

        $converter = $this->get_testable_mock(['get_enabled_plugins']);
        $converter->method('get_enabled_plugins')->willReturn([
                'test_plugin' => $classname,
            ]);

        $method = new ReflectionMethod(\core_files\converter::class, 'get_document_converter_classes');
        $result = $method->invokeArgs($converter, ['docx', 'pdf']);
        $this->assertCount(1, $result);
        $this->assertNotFalse(array_search($classname, $result));
    }

    /**
     * Test the can_convert_storedfile_to function with a directory.
     */
    public function test_can_convert_storedfile_to_directory(): void {
        $converter = $this->get_testable_mock();

        // A file with filename '.' is a directory.
        $file = $this->get_stored_file('', '.');

        $this->assertFalse($converter->can_convert_storedfile_to($file, 'target'));
    }

    /**
     * Test the can_convert_storedfile_to function with an empty file.
     */
    public function test_can_convert_storedfile_to_emptyfile(): void {
        $converter = $this->get_testable_mock();

        // A file with filename '.' is a directory.
        $file = $this->get_stored_file('');

        $this->assertFalse($converter->can_convert_storedfile_to($file, 'target'));
    }

    /**
     * Test the can_convert_storedfile_to function with a file with indistinguished mimetype.
     */
    public function test_can_convert_storedfile_to_no_mimetype(): void {
        $converter = $this->get_testable_mock();

        // A file with filename '.' is a directory.
        $file = $this->get_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $this->assertFalse($converter->can_convert_storedfile_to($file, 'target'));
    }

    /**
     * Test the can_convert_storedfile_to function with a file with a known mimetype and extension.
     */
    public function test_can_convert_storedfile_to_docx(): void {
        $returnvalue = (object) [];

        $converter = $this->get_testable_mock([
                'can_convert_format_to'
            ]);

        $types = \core_filetypes::get_types();

        $file = $this->get_stored_file('example content', 'example.docx', [
                'mimetype' => $types['docx']['type'],
            ]);

        $converter->expects($this->once())
            ->method('can_convert_format_to')
            ->willReturn($returnvalue);

        $result = $converter->can_convert_storedfile_to($file, 'target');
        $this->assertEquals($returnvalue, $result);
    }


    /**
     * Test the can_convert_format_to function.
     */
    public function test_can_convert_format_to_found(): void {
        $converter = $this->get_testable_mock(['get_document_converter_classes']);

        $mock = $this->get_mocked_converter();

        $converter->method('get_document_converter_classes')
            ->willReturn([$mock]);

        $result = $converter->can_convert_format_to('from', 'to');
        $this->assertTrue($result);
    }

    /**
     * Test the can_convert_format_to function.
     */
    public function test_can_convert_format_to_not_found(): void {
        $converter = $this->get_testable_mock(['get_document_converter_classes']);

        $converter->method('get_document_converter_classes')
            ->willReturn([]);

        $result = $converter->can_convert_format_to('from', 'to');
        $this->assertFalse($result);
    }

    /**
     * Test the can_convert_storedfile_to function with an empty file.
     */
    public function test_poll_conversion_in_progress(): void {
        $this->resetAfterTest();

        $converter = $this->get_testable_mock([
                'get_document_converter_classes',
                'get_next_converter',
            ]);

        $converter->method('get_document_converter_classes')->willReturn([]);
        $converter->method('get_next_converter')->willReturn(false);
        $file = $this->create_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $conversion = $this->get_testable_conversion([
                'get_converter_instance',
            ]);
        $conversion->set_sourcefile($file);
        $conversion->set('targetformat', 'target');
        $conversion->set('status', conversion::STATUS_IN_PROGRESS);
        $conversion->create();

        $converterinstance = $this->get_mocked_converter([
                'poll_conversion_status',
            ]);
        $converterinstance->expects($this->once())
            ->method('poll_conversion_status');
        $conversion->method('get_converter_instance')->willReturn($converterinstance);

        $converter->poll_conversion($conversion);

        $this->assertEquals(conversion::STATUS_IN_PROGRESS, $conversion->get('status'));
    }

    /**
     * Test poll_conversion with an in-progress conversion where we are
     * unable to instantiate the converter instance.
     */
    public function test_poll_conversion_in_progress_fail(): void {
        $this->resetAfterTest();

        $converter = $this->get_testable_mock([
                'get_document_converter_classes',
                'get_next_converter',
            ]);

        $converter->method('get_document_converter_classes')->willReturn([]);
        $converter->method('get_next_converter')->willReturn(false);
        $file = $this->create_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $conversion = $this->get_testable_conversion([
                'get_converter_instance',
            ]);
        $conversion->set_sourcefile($file);
        $conversion->set('targetformat', 'target');
        $conversion->set('status', conversion::STATUS_IN_PROGRESS);
        $conversion->create();

        $conversion->method('get_converter_instance')->willReturn(false);

        $converter->poll_conversion($conversion);

        $this->assertEquals(conversion::STATUS_FAILED, $conversion->get('status'));
    }

    /**
     * Test the can_convert_storedfile_to function with an empty file.
     */
    public function test_poll_conversion_none_supported(): void {
        $this->resetAfterTest();

        $converter = $this->get_testable_mock([
                'get_document_converter_classes',
                'get_next_converter',
            ]);

        $converter->method('get_document_converter_classes')->willReturn([]);
        $converter->method('get_next_converter')->willReturn(false);
        $file = $this->create_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $conversion = new conversion(0, (object) [
            'sourcefileid' => $file->get_id(),
            'targetformat' => 'target',
        ]);
        $conversion->create();

        $converter->poll_conversion($conversion);

        $this->assertEquals(conversion::STATUS_FAILED, $conversion->get('status'));
    }

    /**
     * Test the can_convert_storedfile_to function with an empty file.
     */
    public function test_poll_conversion_pick_first(): void {
        $this->resetAfterTest();

        $converterinstance = $this->get_mocked_converter([
                'start_document_conversion',
                'poll_conversion_status',
            ]);
        $converter = $this->get_testable_mock([
                'get_document_converter_classes',
                'get_next_converter',
            ]);

        $converter->method('get_document_converter_classes')->willReturn([]);
        $converter->method('get_next_converter')->willReturn(get_class($converterinstance));
        $file = $this->create_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $conversion = $this->get_testable_conversion([
                'get_converter_instance',
            ]);
        $conversion->set_sourcefile($file);
        $conversion->set('targetformat', 'target');
        $conversion->set('status', conversion::STATUS_PENDING);
        $conversion->create();

        $conversion->method('get_converter_instance')->willReturn($converterinstance);

        $converterinstance->expects($this->once())
            ->method('start_document_conversion');
        $converterinstance->expects($this->never())
            ->method('poll_conversion_status');

        $converter->poll_conversion($conversion);

        $this->assertEquals(conversion::STATUS_IN_PROGRESS, $conversion->get('status'));
    }

    /**
     * Test the can_convert_storedfile_to function with an empty file.
     */
    public function test_poll_conversion_pick_subsequent(): void {
        $this->resetAfterTest();

        $converterinstance = $this->get_mocked_converter([
                'start_document_conversion',
                'poll_conversion_status',
            ]);
        $converterinstance2 = $this->get_mocked_converter([
                'start_document_conversion',
                'poll_conversion_status',
            ]);
        $converter = $this->get_testable_mock([
                'get_document_converter_classes',
                'get_next_converter',
            ]);

        $converter->method('get_document_converter_classes')->willReturn([]);
        $getinvocations = $this->any();
        $converter
            ->expects($getinvocations)
            ->method('get_next_converter')
            ->willReturnCallback(fn (): string => match (self::getInvocationCount($getinvocations)) {
                1 => get_class($converterinstance),
                default => get_class($converterinstance2),
            });

        $file = $this->create_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $conversion = $this->get_testable_conversion([
                'get_converter_instance',
                'get_status',
            ]);
        $conversion->set_sourcefile($file);
        $conversion->set('targetformat', 'target');
        $conversion->set('status', conversion::STATUS_PENDING);
        $conversion->create();

        $statusinvocations = $this->atLeast(4);
        $conversion
            ->expects($statusinvocations)
            ->method('get_status')
            ->willReturnCallback(fn (): int => match (self::getInvocationCount($statusinvocations)) {
                // Initial status check.
                1 => conversion::STATUS_PENDING,
                // Second check to make sure it's still pending after polling.
                2 => conversion::STATUS_PENDING,
                // First one fails.
                3 => conversion::STATUS_FAILED,
                // Second one succeeds.
                4 => conversion::STATUS_COMPLETE,
                // And the final result checked in this unit test.
                default => conversion::STATUS_COMPLETE,
            });

        $instanceinvocations = $this->any();
        $conversion
            ->expects($instanceinvocations)
            ->method('get_converter_instance')
            ->willReturnCallback(fn (): object => match (self::getInvocationCount($instanceinvocations)) {
                1 => $converterinstance,
                default => $converterinstance2,
            });

        $converterinstance->expects($this->once())
            ->method('start_document_conversion');
        $converterinstance->expects($this->never())
            ->method('poll_conversion_status');
        $converterinstance2->expects($this->once())
            ->method('start_document_conversion');
        $converterinstance2->expects($this->never())
            ->method('poll_conversion_status');

        $converter->poll_conversion($conversion);

        $this->assertEquals(conversion::STATUS_COMPLETE, $conversion->get('status'));
    }

    /**
     * Test the start_conversion with a single converter which succeeds.
     */
    public function test_start_conversion_one_supported_success(): void {
        $this->resetAfterTest();

        $converter = $this->get_testable_mock([
                'get_document_converter_classes',
            ]);

        $converter->method('get_document_converter_classes')
            ->willReturn([\core_file_converter_type_successful::class]);

        $file = $this->create_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $conversion = $converter->start_conversion($file, 'target');

        $this->assertEquals(conversion::STATUS_COMPLETE, $conversion->get('status'));
    }

    /**
     * Test the start_conversion with a single converter which failes.
     */
    public function test_start_conversion_one_supported_failure(): void {
        $this->resetAfterTest();

        $converter = $this->get_testable_mock([
                'get_document_converter_classes',
            ]);

        $mock = $this->get_mocked_converter(['start_document_conversion']);
        $converter->method('get_document_converter_classes')
            ->willReturn([\core_file_converter_type_failed::class]);

        $file = $this->create_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $conversion = $converter->start_conversion($file, 'target');

        $this->assertEquals(conversion::STATUS_FAILED, $conversion->get('status'));
    }

    /**
     * Test the start_conversion with two converters - fail, then succeed.
     */
    public function test_start_conversion_two_supported(): void {
        $this->resetAfterTest();

        $converter = $this->get_testable_mock([
                'get_document_converter_classes',
            ]);

        $mock = $this->get_mocked_converter(['start_document_conversion']);
        $converter->method('get_document_converter_classes')
            ->willReturn([
                \core_file_converter_type_failed::class,
                \core_file_converter_type_successful::class,
            ]);

        $file = $this->create_stored_file('example content', 'example', [
                'mimetype' => null,
            ]);

        $conversion = $converter->start_conversion($file, 'target');

        $this->assertEquals(conversion::STATUS_COMPLETE, $conversion->get('status'));
    }

    /**
     * Ensure that get_next_converter returns false when no converters are available.
     */
    public function test_get_next_converter_no_converters(): void {
        $rcm = new \ReflectionMethod(converter::class, 'get_next_converter');

        $converter = new \core_files\converter();
        $result = $rcm->invoke($converter, [], null);
        $this->assertFalse($result);
    }

    /**
     * Ensure that get_next_converter returns false when already on the
     * only converter.
     */
    public function test_get_next_converter_only_converters(): void {
        $rcm = new \ReflectionMethod(converter::class, 'get_next_converter');

        $converter = new converter();
        $result = $rcm->invoke($converter, ['example'], 'example');
        $this->assertFalse($result);
    }

    /**
     * Ensure that get_next_converter returns false when already on the
     * last converter.
     */
    public function test_get_next_converter_last_converters(): void {
        $rcm = new \ReflectionMethod(converter::class, 'get_next_converter');

        $converter = new converter();
        $result = $rcm->invoke($converter, ['foo', 'example'], 'example');
        $this->assertFalse($result);
    }

    /**
     * Ensure that get_next_converter returns the next vlaue when in a
     * current converter.
     */
    public function test_get_next_converter_middle_converters(): void {
        $rcm = new \ReflectionMethod(converter::class, 'get_next_converter');

        $converter = new converter();
        $result = $rcm->invoke($converter, ['foo', 'bar', 'baz', 'example'], 'bar');
        $this->assertEquals('baz', $result);
    }
    /**
     *
     * Ensure that get_next_converter returns the next vlaue when in a
     * current converter.
     */
    public function test_get_next_converter_first(): void {
        $rcm = new \ReflectionMethod(converter::class, 'get_next_converter');

        $converter = new converter();
        $result = $rcm->invoke($converter, ['foo', 'bar', 'baz', 'example']);
        $this->assertEquals('foo', $result);
    }
}

class core_file_converter_requirements_base implements \core_files\converter_interface {

    /**
     * Whether the plugin is configured and requirements are met.
     *
     * @return  bool
     */
    public static function are_requirements_met() {
        return false;
    }

    /**
     * Convert a document to a new format and return a conversion object relating to the conversion in progress.
     *
     * @param   conversion $conversion The file to be converted
     * @return  conversion
     */
    public function start_document_conversion(conversion $conversion) {
    }

    /**
     * Poll an existing conversion for status update.
     *
     * @param   conversion $conversion The file to be converted
     * @return  conversion
     */
    public function poll_conversion_status(conversion $conversion) {
    }

    /**
     * Whether a file conversion can be completed using this converter.
     *
     * @param   string $from The source type
     * @param   string $to The destination type
     * @return  bool
     */
    public static function supports($from, $to) {
        return false;
    }

    /**
     * A list of the supported conversions.
     *
     * @return  string
     */
    public function get_supported_conversions() {
        return [];
    }

}

/**
 * Test class for converter support with requirements are not met.
 *
 * @package    core_files
 * @copyright  2017 Andrew nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_file_converter_requirements_not_met extends core_file_converter_requirements_base {
}

/**
 * Test class for converter support with requirements met and conversion not supported.
 *
 * @package    core_files
 * @copyright  2017 Andrew nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_file_converter_type_not_supported extends core_file_converter_requirements_base {

    /**
     * Whether the plugin is configured and requirements are met.
     *
     * @return  bool
     */
    public static function are_requirements_met() {
        return true;
    }
}

/**
 * Test class for converter support with requirements met and conversion supported.
 *
 * @package    core_files
 * @copyright  2017 Andrew nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_file_converter_type_supported extends core_file_converter_requirements_base {

    /**
     * Whether the plugin is configured and requirements are met.
     *
     * @return  bool
     */
    public static function are_requirements_met() {
        return true;
    }

    /**
     * Whether a file conversion can be completed using this converter.
     *
     * @param   string $from The source type
     * @param   string $to The destination type
     * @return  bool
     */
    public static function supports($from, $to) {
        return true;
    }
}

/**
 * Test class for converter support with requirements met and successful conversion.
 *
 * @package    core_files
 * @copyright  2017 Andrew nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_file_converter_type_successful extends core_file_converter_requirements_base {

    /**
     * Convert a document to a new format and return a conversion object relating to the conversion in progress.
     *
     * @param   conversion $conversion The file to be converted
     * @return  conversion
     */
    public function start_document_conversion(conversion $conversion) {
        $conversion->set('status', conversion::STATUS_COMPLETE);

        return $conversion;
    }

    /**
     * Whether a file conversion can be completed using this converter.
     *
     * @param   string $from The source type
     * @param   string $to The destination type
     * @return  bool
     */
    public static function supports($from, $to) {
        return true;
    }
}

/**
 * Test class for converter support with requirements met and failed conversion.
 *
 * @package    core_files
 * @copyright  2017 Andrew nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_file_converter_type_failed extends core_file_converter_requirements_base {

    /**
     * Whether the plugin is configured and requirements are met.
     *
     * @return  bool
     */
    public static function are_requirements_met() {
        return true;
    }

    /**
     * Convert a document to a new format and return a conversion object relating to the conversion in progress.
     *
     * @param   conversion $conversion The file to be converted
     * @return  conversion
     */
    public function start_document_conversion(conversion $conversion) {
        $conversion->set('status', conversion::STATUS_FAILED);

        return $conversion;
    }

    /**
     * Whether a file conversion can be completed using this converter.
     *
     * @param   string $from The source type
     * @param   string $to The destination type
     * @return  bool
     */
    public static function supports($from, $to) {
        return true;
    }
}
