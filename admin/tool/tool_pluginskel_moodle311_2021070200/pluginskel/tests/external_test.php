<?php
// This file is part of Moodle - https://moodle.org/
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
 * Provides the {@see tool_pluginskel_external_testcase} class.
 *
 * @package     tool_pluginskel
 * @category    test
 * @copyright   2021 David Mudrák <david@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Monolog\Logger;
use Monolog\Handler\TestHandler;
use tool_pluginskel\local\util\manager;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/pluginskel/vendor/autoload.php');

/**
 * Test case for generating the external function classes.
 *
 * @package     tool_pluginskel
 * @copyright   2021 David Mudrák <david@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_external_testcase extends advanced_testcase {

    /**
     * Returns a new instance of the manager utility class suitable for testing.
     *
     * @return \tool_pluginskel\local\util\manager
     */
    protected function get_manager_instance() {

        $logger = new Logger('externaltest');
        $logger->pushHandler(new TestHandler());
        $manager = manager::instance($logger);

        return $manager;
    }

    /**
     * Return a base recipe for a plugin.
     *
     * @return array
     */
    protected function get_base_recipe() {
        return [
            'component' => 'tool_ex',
            'name' => 'External functions test',
            'copyright' => '2021 David Mudrák <david@moodle.com>',
        ];
    }

    /**
     * Test no external files are created if not declared in the recipe.
     */
    public function test_missing() {

        $recipe = $this->get_base_recipe();

        $manager = $this->get_manager_instance();
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        foreach (array_keys($files) as $filename) {
            $this->assertStringStartsNotWith('classes/external/', $filename);
        }
    }

    /**
     * Test implementation of the {@see \tool_pluginskel\local\util\manager::prepare_external_files()}.
     */
    public function test_prepare_external_files() {

        $recipe = $this->get_base_recipe() + [
            'external' => [
                [
                    'name' => 'create_thing',
                    'desc' => 'Create new thing record in the database',
                    'type' => 'write',
                    'ajax' => 'true',
                    'loginrequired' => true,
                    'readonlysession' => 'false',
                    'parameters' => [
                        [
                            'name' => 'title',
                            'type' => 'PARAM_TEXT',
                            'desc' => 'Title of the thing',
                        ],
                    ],
                    'returns' => [
                        'name' => 'id',
                        'type' => 'PARAM_INT',
                        'desc' => 'Identifier of the newly created thing',
                    ],
                ],
                [
                    'name' => 'search_things',
                    'parameters' => [
                        [
                            'name' => 'query',
                            'type' => 'PARAM_RAW',
                            'desc' => 'Search query',
                        ],
                    ],
                    'returns' => [
                        'multiple' => [
                            'single' => [
                                [
                                    'name' => 'id',
                                    'type' => 'PARAM_INT',
                                    'desc' => 'Identifier of the thing',
                                ],
                                [
                                    'name' => 'title',
                                    'type' => 'PARAM_TEXT',
                                    'desc' => 'Title of the thing',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $manager = $this->get_manager_instance();
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('classes/external/create_thing.php', $files);
        $this->assertArrayHasKey('classes/external/search_things.php', $files);
        $this->assertArrayHasKey('db/services.php', $files);
    }

    /**
     * Test implementation of the {@see \tool_pluginskel\local\util\manager::prepare_db_services()}.
     */
    public function test_prepare_db_services() {

        $recipe = $this->get_base_recipe() + [
            'services' => [
                [
                    'name' => 'Things store',
                    'shortname' => 'things_store',
                    'functions' => [
                        'tool_ex_create_thing',
                        'tool_ex_search_things',
                    ],
                    'requiredcapability' => 'tool/ex:managethings',
                    'enabled' => true,
                    'downloadfiles' => false,
                    'uploadfiles' => false,
                ],
            ],
        ];

        $manager = $this->get_manager_instance();
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('db/services.php', $files);
    }

    /**
     * Test functionality of the generate_external_description_item() method.
     *
     * @dataProvider data_generate_external_description_item
     * @param string $result Expected output
     * @param array $yaml Input
     * @param int $indent Code indent
     * @param string $warning Expected warning
     * @param string $error Expected error
     */
    public function test_generate_external_description_item(string $result, array $yaml, int $indent = 0,
            string $warning = null, string $error = null) {

        $method = new ReflectionMethod(\tool_pluginskel\local\skel\external_function_file::class,
            'generate_external_description_item');
        $method->setAccessible(true);

        $logger = new Logger('externaltest');
        $log = new TestHandler();
        $logger->pushHandler($log);

        $generator = new \tool_pluginskel\local\skel\external_function_file();
        $generator->set_logger($logger);

        $this->assertEquals($result, $method->invoke($generator, $yaml, $indent));

        if ($warning) {
            $this->assertTrue($log->hasWarningThatContains($warning));

        } else {
            $this->assertFalse($log->hasWarningRecords(), 'Unexpected warnings: ' . json_encode($log->getRecords()));
        }

        if ($error) {
            $this->assertTrue($log->hasErrorThatContains($error));

        } else {
            $this->assertFalse($log->hasErrorRecords(), 'Unexpected errors: ' . json_encode($log->getRecords()));
        }

        $log->clear();
    }

    /**
     * Provides data sets for {@see self::test_generate_external_description_item()}.
     *
     * @return array
     */
    public function data_generate_external_description_item(): array {

        return [
            'No data produce empty string' => [
                'result' => '',
                'yaml' => [],
                'indent' => 0,
            ],

            'Named external value' => [
                'result' => "'message' => new external_value(PARAM_RAW, 'Message text')",
                'yaml' => [
                    'name' => 'message',
                    'type' => 'PARAM_RAW',
                    'desc' => 'Message text',
                ],
            ],

            'Anonymous external value' => [
                'result' => "new external_value(PARAM_TEXT, 'Text')",
                'yaml' => [
                    'type' => 'PARAM_TEXT',
                    'desc' => 'Text',
                ],
            ],

            'Missing value description' => [
                'result' => "'message' => new external_value(PARAM_RAW, '')",
                'yaml' => [
                    'name' => 'message',
                    'type' => 'PARAM_RAW',
                ],
                'indent' => 0,
                'warning' => 'External value description not specified',
            ],

            'Missing value type' => [
                'result' => '// TODO Unable to generate valid external_value from the recipe.',
                'yaml' => [
                    'name' => 'something',
                ],
                'indent' => 0,
                'warning' => '',
                'error' => 'PARAM type not specified for external value',
            ],

            'Indented external value' => [
                'result' => "    'messageformat' => new external_value(PARAM_INT, 'Message format')",
                'yaml' => [
                    'name' => 'messageformat',
                    'type' => 'PARAM_INT',
                    'desc' => 'Message format',
                ],
                'indent' => 4,
            ],

            'Anonymous optional value with default' => [
                'result' => "        new external_value(PARAM_INT, 'Identifier', VALUE_DEFAULT, -1)",
                'yaml' => [
                    'type' => 'PARAM_INT',
                    'required' => false,
                    'default' => -1,
                    'desc' => 'Identifier',
                ],
                'indent' => 8,
            ],

            // Also known as map, hash or associative array.
            'Named single structure' => [
                'result' => "'editor' => new external_single_structure([
    'message' => new external_value(PARAM_RAW, 'Message text'),
    'messageformat' => new external_value(PARAM_INT, 'Message format', VALUE_DEFAULT, FORMAT_HTML),
], '', VALUE_DEFAULT, null)",
                'yaml' => [
                    'name' => 'editor',
                    'required' => false,
                    'single' => [
                        [
                            'name' => 'message',
                            'type' => 'PARAM_RAW',
                            'desc' => 'Message text',
                        ],
                        [
                            'name' => 'messageformat',
                            'type' => 'PARAM_INT',
                            'desc' => 'Message format',
                            'required' => false,
                            'default' => 'FORMAT_HTML',
                        ],
                    ],
                ],
            ],

            'List of scalar values' => [
                'result' => "'ids' => new external_multiple_structure(
    new external_value(PARAM_INT, 'Record id')
)",
                'yaml' => [
                    'name' => 'ids',
                    'multiple' => [
                        [
                            'type' => 'PARAM_INT',
                            'desc' => 'Record id',
                        ],
                    ],
                ],
            ],

            'Optional list of scalar values' => [
                'result' => "'ids' => new external_multiple_structure(
    new external_value(PARAM_INT, 'Record id'),
    '', VALUE_DEFAULT, []
)",
                'yaml' => [
                    'name' => 'ids',
                    'required' => false,
                    'default' => '[]',
                    'multiple' => [
                        [
                            'type' => 'PARAM_INT',
                            'desc' => 'Record id',
                        ],
                    ],
                ],
            ],

            'Optional described list of scalar values' => [
                'result' => "'ids' => new external_multiple_structure(
    new external_value(PARAM_INT, 'Record id'),
    'List of records ids', VALUE_DEFAULT, null
)",
                'yaml' => [
                    'name' => 'ids',
                    'desc' => 'List of records ids',
                    'required' => false,
                    'multiple' => [
                        [
                            'type' => 'PARAM_INT',
                            'desc' => 'Record id',
                        ],
                    ],
                ],
            ],

            'List of single structures' => [
                'result' => "new external_multiple_structure(
    new external_single_structure([
        'idnumber' => new external_value(PARAM_INT, 'Student identifier'),
        'name' => new external_value(PARAM_TEXT, 'Student fullname'),
    ])
)",
                'yaml' => [
                    'multiple' => [
                        [
                            'single' => [
                                [
                                    'name' => 'idnumber',
                                    'type' => 'PARAM_INT',
                                    'desc' => 'Student identifier',
                                ],
                                [
                                    'name' => 'name',
                                    'type' => 'PARAM_TEXT',
                                    'desc' => 'Student fullname',
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            'Multiple structure of named sub-structure' => [
                'result' => "'ids' => new external_multiple_structure(
    new external_value(PARAM_INT, 'Record id'),
    '', VALUE_DEFAULT, []
)",
                'yaml' => [
                    'name' => 'ids',
                    'required' => false,
                    'default' => '[]',
                    'multiple' => [
                        [
                            'name' => 'id',
                            'type' => 'PARAM_INT',
                            'desc' => 'Record id',
                        ],
                    ],
                ],
                'ident' => 0,
                'warning' => 'External multiple structure should not contain named sub-structures',
            ],

            'Multiple structure of more than one sub-structures' => [
                'result' => '// TODO Unable to generate valid external_multiple_structure from the recipe.',
                'yaml' => [
                    'multiple' => [
                        [
                            [
                                'name' => 'first',
                                'type' => 'PARAM_INT',
                            ],
                        ],
                        [
                            [
                                'name' => 'second',
                                'type' => 'PARAM_INT',
                            ],
                        ],
                    ],
                ],
                'indent' => 0,
                'warning' => '',
                'error' => 'External multiple structure can specify only one repeating sub-structure',
            ],
        ];
    }

    /**
     * Test functionality of the generate_external_description_required_default() method.
     *
     * @dataProvider data_generate_external_description_required_default
     * @param string $result expected
     * @param array $yaml
     */
    public function test_generate_external_description_required_default(string $result, array $yaml) {

        $method = new ReflectionMethod(\tool_pluginskel\local\skel\external_function_file::class,
            'generate_external_description_required_default');
        $method->setAccessible(true);

        $generator = new \tool_pluginskel\local\skel\external_function_file();

        $this->assertEquals($result, $method->invoke($generator, $yaml));
    }

    /**
     * Provides data sets for {@see self::test_generate_external_description_required_default()}.
     *
     * @return array
     */
    public function data_generate_external_description_required_default(): array {

        return [
            'No arguments generated by default' => [
                'result' => '',
                'yaml' => [],
            ],

            'Values are required (VALUE_REQUIRED) by default' => [
                'result' => '',
                'yaml' => [
                    'required' => true,
                ],
            ],

            // Note that in non-root level, VALUE_OPTIONAL are supported by the API too, but we do not generate such.
            'Values declared as non-required defaulting to implicit null' => [
                'result' => ', VALUE_DEFAULT, null',
                'yaml' => [
                    'required' => false,
                ],
            ],

            'Numerical default value without quotes' => [
                'result' => ', VALUE_DEFAULT, 42',
                'yaml' => [
                    'required' => false,
                    'default' => 42,
                ],
            ],

            'Values are generated without quotes' => [
                'result' => ', VALUE_DEFAULT, FORMAT_HTML',
                'yaml' => [
                    'required' => false,
                    'default' => 'FORMAT_HTML',
                ],
            ],

            // Non-numerical default literals must be explicitly quoted in YAML e.g. `default: "'foo'"`.
            'String literal default values' => [
                'result' => ", VALUE_DEFAULT, 'foo'",
                'yaml' => [
                    'required' => false,
                    'default' => "'foo'",
                ],
            ],
        ];
    }
}
