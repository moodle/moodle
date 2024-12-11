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
 * Exporter testcase.
 *
 * @package    core
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use core_external\external_format_value;
use core_external\external_multiple_structure;
use core_external\external_settings;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\util;

/**
 * Exporter testcase.
 *
 * @package    core
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class exporter_test extends \advanced_testcase {

    protected $validrelated = null;
    protected $invalidrelated = null;
    protected $validdata = null;
    protected $invaliddata = null;

    public function setUp(): void {
        parent::setUp();
        $s = new \stdClass();
        $this->validrelated = array(
            'simplestdClass' => $s,
            'arrayofstdClass' => array($s, $s),
            'context' => null,
            'aint' => 5,
            'astring' => 'valid string',
            'abool' => false,
            'ints' => []
        );
        $this->invalidrelated = array(
            'simplestdClass' => 'a string',
            'arrayofstdClass' => 5,
            'context' => null,
            'aint' => false,
            'astring' => 4,
            'abool' => 'not a boolean',
            'ints' => null
        );

        $this->validdata = array('stringA' => 'A string', 'stringAformat' => FORMAT_HTML, 'intB' => 4);

        $this->invaliddata = array('stringA' => 'A string');
    }

    public function test_get_read_structure(): void {
        $structure = core_testable_exporter::get_read_structure();

        $this->assertInstanceOf(external_single_structure::class, $structure);
        $this->assertInstanceOf(external_value::class, $structure->keys['stringA']);
        $this->assertInstanceOf(external_format_value::class, $structure->keys['stringAformat']);
        $this->assertInstanceOf(external_value::class, $structure->keys['intB']);
        $this->assertInstanceOf(external_value::class, $structure->keys['otherstring']);
        $this->assertInstanceOf(external_multiple_structure::class, $structure->keys['otherstrings']);
    }

    public function test_get_create_structure(): void {
        $structure = core_testable_exporter::get_create_structure();

        $this->assertInstanceOf(external_single_structure::class, $structure);
        $this->assertInstanceOf(external_value::class, $structure->keys['stringA']);
        $this->assertInstanceOf(external_format_value::class, $structure->keys['stringAformat']);
        $this->assertInstanceOf(external_value::class, $structure->keys['intB']);
        $this->assertArrayNotHasKey('otherstring', $structure->keys);
        $this->assertArrayNotHasKey('otherstrings', $structure->keys);
    }

    public function test_get_update_structure(): void {
        $structure = core_testable_exporter::get_update_structure();

        $this->assertInstanceOf(external_single_structure::class, $structure);
        $this->assertInstanceOf(external_value::class, $structure->keys['stringA']);
        $this->assertInstanceOf(external_format_value::class, $structure->keys['stringAformat']);
        $this->assertInstanceOf(external_value::class, $structure->keys['intB']);
        $this->assertArrayNotHasKey('otherstring', $structure->keys);
        $this->assertArrayNotHasKey('otherstrings', $structure->keys);
    }

    public function test_invalid_data(): void {
        global $PAGE;
        $exporter = new core_testable_exporter($this->invaliddata, $this->validrelated);
        $output = $PAGE->get_renderer('core');

        // The exception message is a bit misleading, it actually indicates an expected property wasn't found.
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Unexpected property stringAformat');
        $result = $exporter->export($output);
    }

    public function test_invalid_related(): void {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('Exporter class is missing required related data: (core\core_testable_exporter) ' .
            'simplestdClass => stdClass');
        $exporter = new core_testable_exporter($this->validdata, $this->invalidrelated);
    }

    public function test_invalid_related_all_cases(): void {
        global $PAGE;

        foreach ($this->invalidrelated as $key => $value) {
            $data = $this->validrelated;
            $data[$key] = $value;

            try {
                $exporter = new core_testable_exporter($this->validdata, $data);
                $output = $PAGE->get_renderer('core');
                $result = $exporter->export($output);
            } catch (\coding_exception $e) {
                $this->assertNotFalse(strpos($e->getMessage(), $key));
            }
        }
    }

    public function test_valid_data_and_related(): void {
        global $PAGE;
        $output = $PAGE->get_renderer('core');
        $exporter = new core_testable_exporter($this->validdata, $this->validrelated);
        $result = $exporter->export($output);
        $this->assertSame('>Another string', $result->otherstring);
        $this->assertSame(array('String &gt;a', 'String b'), $result->otherstrings);
    }

    public function test_format_text(): void {
        global $PAGE;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $syscontext = \context_system::instance();
        $coursecontext = \context_course::instance($course->id);

        external_settings::get_instance()->set_filter(true);
        filter_set_global_state('urltolink', TEXTFILTER_OFF);
        filter_set_local_state('urltolink', $coursecontext->id, TEXTFILTER_ON);
        set_config('formats', FORMAT_MARKDOWN, 'filter_urltolink');
        \filter_manager::reset_caches();

        $data = [
            'stringA' => '__Watch out:__ https://moodle.org @@PLUGINFILE@@/test.pdf',
            'stringAformat' => FORMAT_MARKDOWN,
            'intB' => 1
        ];

        // Export simulated in the system context.
        $output = $PAGE->get_renderer('core');
        $exporter = new core_testable_exporter($data, ['context' => $syscontext] + $this->validrelated);
        $result = $exporter->export($output);

        $youtube = 'https://moodle.org';
        $fileurl = (new \moodle_url('/webservice/pluginfile.php/' . $syscontext->id . '/test/area/9/test.pdf'))->out(false);
        $expected = "<p><strong>Watch out:</strong> $youtube $fileurl</p>\n";
        $this->assertEquals($expected, $result->stringA);
        $this->assertEquals(FORMAT_HTML, $result->stringAformat);

        // Export simulated in the course context where the filter is enabled.
        $exporter = new core_testable_exporter($data, ['context' => $coursecontext] + $this->validrelated);
        $result = $exporter->export($output);
        $youtube = '<a href="https://moodle.org" class="_blanktarget">https://moodle.org</a>';
        $fileurl = (new \moodle_url('/webservice/pluginfile.php/' . $coursecontext->id . '/test/area/9/test.pdf'))->out(false);
        $expected = "<p><strong>Watch out:</strong> $youtube <a href=\"$fileurl\" class=\"_blanktarget\">$fileurl</a></p>\n";
        $this->assertEquals($expected, $result->stringA);
        $this->assertEquals(FORMAT_HTML, $result->stringAformat);
    }

    public function test_properties_description(): void {
        $properties = core_testable_exporter::read_properties_definition();
        // Properties default description.
        $this->assertEquals('stringA', $properties['stringA']['description']);
        $this->assertEquals('stringAformat', $properties['stringAformat']['description']);
        // Properties custom description.
        $this->assertEquals('intB description', $properties['intB']['description']);
        // Other properties custom description.
        $this->assertEquals('otherstring description', $properties['otherstring']['description']);
        // Other properties default description.
        $this->assertEquals('otherstrings', $properties['otherstrings']['description']);
        // Assert nested elements are formatted correctly.
        $this->assertEquals('id', $properties['nestedarray']['type']['id']['description']);
    }

    /**
     * Tests for the handling of the default attribute of format properties in exporters.
     *
     * @covers \core\external\exporter::export
     * @return void
     */
    public function test_export_format_no_default(): void {
        global $PAGE;
        $output = $PAGE->get_renderer('core');
        $syscontext = \context_system::instance();
        $related = [
            'context' => $syscontext,
        ] + $this->validrelated;

        // Pass a data that does not have the format property for stringA.
        $data = [
            'stringA' => '__Go to:__ [Moodle.org](https://moodle.org)',
            'intB' => 1,
        ];

        // Note: For testing purposes only. Never extend exporter implementation. Only extend from the base exporter class!
        $testablexporterclass = new class($data, $related) extends core_testable_exporter {
            /**
             * Properties definition.
             */
            public static function define_properties(): array {
                $properties = parent::define_properties();
                $properties['stringAformat']['default'] = FORMAT_MARKDOWN;
                return $properties;
            }
        };
        // For a property format with default set, it should be able to export a data even if the property format is not passed.
        $result = $testablexporterclass->export($output);
        $expected = '<strong>Go to:</strong> <a href="https://moodle.org">Moodle.org</a>';
        $this->assertStringContainsString($expected, $result->stringA);
        $this->assertEquals(FORMAT_HTML, $result->stringAformat);

        // Passing data to an exporter with a required property format will throw an exception.
        $exporter = new core_testable_exporter($data, $related);
        $this->expectException(\coding_exception::class);
        $exporter->export($output);
    }

    /**
     * Test the processing of format properties.
     *
     * @covers \core\external\exporter::get_read_structure
     * @return void
     */
    public function test_format_properties_with_optional(): void {
        $testable = new class([]) extends \core\external\exporter {
            /**
             * Properties definition.
             *
             * @return array[]
             */
            public static function define_properties(): array {
                return [
                    'content' => [
                        'type' => PARAM_RAW,
                    ],
                    'contentformat' => [
                        'type' => PARAM_INT,
                        'optional' => true,
                    ],
                    'description' => [
                        'type' => PARAM_RAW,
                        'optional' => true,
                    ],
                    'descriptionformat' => [
                        'type' => PARAM_INT,
                        'default' => FORMAT_MARKDOWN,
                    ],
                    'summary' => [
                        'type' => PARAM_RAW,
                    ],
                    'summaryformat' => [
                        'type' => PARAM_INT,
                        'default' => null,
                    ],
                ];
            }
        };

        $definition = $testable::get_read_structure();
        // Check content and its format.
        $this->assertEquals(VALUE_REQUIRED, $definition->keys['content']->required);
        $this->assertEquals(VALUE_OPTIONAL, $definition->keys['contentformat']->required);
        $this->assertEquals(null, $definition->keys['contentformat']->default);

        // Check description and its format.
        $this->assertEquals(VALUE_OPTIONAL, $definition->keys['description']->required);
        $this->assertEquals(VALUE_DEFAULT, $definition->keys['descriptionformat']->required);
        $this->assertEquals(FORMAT_MARKDOWN, $definition->keys['descriptionformat']->default);

        // Check summary and its format.
        $this->assertEquals(VALUE_REQUIRED, $definition->keys['summary']->required);
        $this->assertEquals(null, $definition->keys['summary']->default);
        $this->assertEquals(VALUE_DEFAULT, $definition->keys['summaryformat']->required);
        $this->assertEquals(FORMAT_HTML, $definition->keys['summaryformat']->default);
    }

    /**
     * Test the processing of format properties when an invalid default format is passed.
     *
     * @covers \core\external\exporter::get_read_structure
     * @return void
     */
    public function test_optional_format_property_with_invalid_default(): void {
        $testable = new class([]) extends \core\external\exporter {
            /**
             * Properties definition.
             *
             * @return array[]
             */
            public static function define_properties(): array {
                return [
                    'description' => [
                        'type' => PARAM_RAW,
                    ],
                    'descriptionformat' => [
                        'type' => PARAM_INT,
                        'default' => 999,
                    ],
                ];
            }
        };

        $definition = $testable::get_read_structure();
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);

        // Check description and its format.
        $this->assertEquals(VALUE_REQUIRED, $definition->keys['description']->required);
        $this->assertEquals(VALUE_DEFAULT, $definition->keys['descriptionformat']->required);
        $this->assertEquals(FORMAT_HTML, $definition->keys['descriptionformat']->default);
    }
}

/**
 * Example persistent class.
 *
 * @package    core
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_testable_exporter extends \core\external\exporter {

    protected static function define_related() {
        // We cache the context so it does not need to be retrieved from the course.
        return array('simplestdClass' => 'stdClass', 'arrayofstdClass' => 'stdClass[]', 'context' => 'context?',
            'astring' => 'string', 'abool' => 'bool', 'aint' => 'int', 'ints' => 'int[]');
    }

    protected function get_other_values(\renderer_base $output) {
        return array(
            'otherstring' => '>Another <strong>string</strong>',
            'otherstrings' => array('String >a', 'String <strong>b</strong>')
        );
    }

    public static function define_properties() {
        return array(
            'stringA' => array(
                'type' => PARAM_RAW,
            ),
            'stringAformat' => array(
                'type' => PARAM_INT,
            ),
            'intB' => array(
                'type' => PARAM_INT,
                'description' => 'intB description',
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'otherstring' => array(
                'type' => PARAM_TEXT,
                'description' => 'otherstring description',
            ),
            'otherstrings' => array(
                'type' => PARAM_TEXT,
                'multiple' => true
            ),
            'nestedarray' => array(
                'multiple' => true,
                'optional' => true,
                'type' => [
                    'id' => ['type' => PARAM_INT]
                ]
            )
        );
    }

    protected function get_format_parameters_for_stringA() {
        return [
            // For testing use the passed context if any.
            'context' => isset($this->related['context']) ? $this->related['context'] : \context_system::instance(),
            'component' => 'test',
            'filearea' => 'area',
            'itemid' => 9,
        ];
    }

    protected function get_format_parameters_for_otherstring() {
        return [
            'context' => \context_system::instance(),
            'options' => ['escape' => false]
        ];
    }

    protected function get_format_parameters_for_otherstrings() {
        return [
            'context' => \context_system::instance(),
        ];
    }
}
