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
 * Test phpunit_dataset features.
 *
 * @package    core
 * @category   tests
 * @copyright  2020 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core;

use advanced_testcase;
use phpunit_dataset;
use org\bovigo\vfs\vfsStream;

/**
 * Test phpunit_dataset features.
 *
 * @coversDefaultClass \phpunit_dataset
 */
class phpunit_dataset_test extends advanced_testcase {


    /**
     * @covers ::from_files
     */
    public function test_from_files() {

        $ds = new phpunit_dataset();

        $files = [
            __DIR__ . '/fixtures/sample_dataset.xml',
            'user2' => __DIR__ . '/fixtures/sample_dataset.csv',
        ];

        // We need public properties to check the basis.
        $dsref = new \ReflectionClass($ds);
        $dstables = $dsref->getProperty('tables');
        $dscolumns = $dsref->getProperty('columns');
        $dsrows = $dsref->getProperty('rows');

        // Expectations.
        $exptables = ['user', 'user2'];
        $expcolumns = ['id', 'username', 'email'];
        $exprows = [
            ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
            ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
        ];

        $ds->from_files($files);

        $this->assertIsArray($dstables->getValue($ds));
        $this->assertSame($exptables, $dstables->getValue($ds));
        $this->assertIsArray($dscolumns->getValue($ds));
        $this->assertSame($expcolumns, $dscolumns->getValue($ds)['user']);
        $this->assertSame($expcolumns, $dscolumns->getValue($ds)['user2']);
        $this->assertIsArray($dsrows->getValue($ds));
        $this->assertEquals($exprows, $dsrows->getValue($ds)['user']); // Equals because of stringified integers on load.
        $this->assertEquals($exprows, $dsrows->getValue($ds)['user2']); // Equals because of stringified integers on load.
    }

    /**
     * test_from_file() data provider.
     */
    public function from_file_provider() {
        // Create an unreadable file with vfsStream.
        $vfsfile = vfsStream::newFile('unreadable', 0222);
        vfsStream::setup('root')->addChild($vfsfile);

        return [
            'file not found' => [
                'fullpath' => '/this/does/not/exist',
                'tablename' => 'user',
                'exception' => 'from_file, file not found: /this/does/not/exist',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'file not readable' => [
                'fullpath' => $vfsfile->url(),
                'tablename' => 'user',
                'exception' => 'from_file, file not readable: ' . $vfsfile->url(),
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'wrong extension' => [
                'fullpath' => __DIR__ . '/fixtures/sample_dataset.txt',
                'tablename' => 'user',
                'exception' => 'from_file, cannot handle files with extension: txt',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'csv loads ok' => [
                'fullpath' => __DIR__ . '/fixtures/sample_dataset.csv',
                'tablename' => 'user',
                'exception' => null,
                'tables' => ['user'],
                'columns' => ['user' =>
                    ['id', 'username', 'email']
                ],
                'rows' => ['user' =>
                    [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ]
                ],
            ],
            'xml loads ok' => [
                'fullpath' => __DIR__ . '/fixtures/sample_dataset.xml',
                'tablename' => 'user',
                'exception' => null,
                'tables' => ['user'],
                'columns' => ['user' =>
                    ['id', 'username', 'email']
                ],
                'rows' => ['user' =>
                    [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @dataProvider from_file_provider
     * @covers ::from_file
     */
    public function test_from_file(string $fullpath, string $tablename, ?string $exception,
        array $tables, array $columns, array $rows) {

        $ds = new phpunit_dataset();

        // We need public properties to check the basis.
        $dsref = new \ReflectionClass($ds);
        $dstables = $dsref->getProperty('tables');
        $dscolumns = $dsref->getProperty('columns');
        $dsrows = $dsref->getProperty('rows');

        // We are expecting an exception.
        if (!empty($exception)) {
            $this->expectException('coding_exception');
            $this->expectExceptionMessage($exception);
        }

        $ds->from_file($fullpath, $tablename);

        $this->assertIsArray($dstables->getValue($ds));
        $this->assertSame($tables, $dstables->getValue($ds));
        $this->assertIsArray($dscolumns->getValue($ds));
        $this->assertSame($columns, $dscolumns->getValue($ds));
        $this->assertIsArray($dsrows->getValue($ds));
        $this->assertEquals($rows, $dsrows->getValue($ds)); // Equals because of stringified integers on load.
    }

    /**
     * test_from_string() data provider.
     */
    public function from_string_provider() {

        return [
            'wrong type' => [
                'content' => file_get_contents(__DIR__ . '/fixtures/sample_dataset.xml'),
                'type' => 'txt',
                'tablename' => 'user',
                'exception' => 'from_string, cannot handle contents of type: txt',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'missing cvs table' => [
                'content' => file_get_contents(__DIR__ . '/fixtures/sample_dataset.csv'),
                'type' => 'csv',
                'tablename' => '',
                'exception' => 'from_string, contents of type "cvs" require a $table to be passed, none found',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'csv loads ok' => [
                'fullpath' => file_get_contents(__DIR__ . '/fixtures/sample_dataset.csv'),
                'type' => 'csv',
                'tablename' => 'user',
                'exception' => null,
                'tables' => ['user'],
                'columns' => ['user' =>
                    ['id', 'username', 'email']
                ],
                'rows' => ['user' =>
                    [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ]
                ],
            ],
            'xml loads ok' => [
                'fullpath' => file_get_contents(__DIR__ . '/fixtures/sample_dataset.xml'),
                'type' => 'xml',
                'tablename' => 'user',
                'exception' => null,
                'tables' => ['user'],
                'columns' => ['user' =>
                    ['id', 'username', 'email']
                ],
                'rows' => ['user' =>
                    [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @dataProvider from_string_provider
     * @covers ::from_string
     */
    public function test_from_string(string $content, string $type, string $tablename, ?string $exception,
        array $tables, array $columns, array $rows) {

        $ds = new phpunit_dataset();

        // We need public properties to check the basis.
        $dsref = new \ReflectionClass($ds);
        $dstables = $dsref->getProperty('tables');
        $dscolumns = $dsref->getProperty('columns');
        $dsrows = $dsref->getProperty('rows');

        // We are expecting an exception.
        if (!empty($exception)) {
            $this->expectException('coding_exception');
            $this->expectExceptionMessage($exception);
        }

        $ds->from_string($content, $type, $tablename);

        $this->assertIsArray($dstables->getValue($ds));
        $this->assertSame($tables, $dstables->getValue($ds));
        $this->assertIsArray($dscolumns->getValue($ds));
        $this->assertSame($columns, $dscolumns->getValue($ds));
        $this->assertIsArray($dsrows->getValue($ds));
        $this->assertEquals($rows, $dsrows->getValue($ds)); // Equals because of stringified integers on load.
    }

    /**
     * test_from_array() data provider.
     */
    public function from_array_provider() {
        return [
            'repeated array table many structures' => [
                'structure' => [
                    'user' => [
                        ['id' => 5, 'name' => 'John'],
                        ['id' => 6, 'name' => 'Jane'],
                    ],
                ],
                'exception' => 'from_array, table already added to dataset: user',
                'tables' => [],
                'columns' => [],
                'rows' => [],
                'repeated' => true, // To force the table already exists exception.
            ],
            'wrong number of columns' => [
                'structure' => [
                    'user' => [
                        ['id' => 5, 'name' => 'John'],
                        ['id' => 6],
                    ],
                ],
                'exception' => 'from_array, number of columns must match number of values, found: 2 vs 1',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'wrong not matching names of columns' => [
                'structure' => [
                    'user' => [
                        ['id' => 5, 'name' => 'John'],
                        ['id' => 6, 'noname' => 'Jane'],
                    ],
                ],
                'exception' => 'from_array, columns in all elements must match first one, found: id, noname',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'ok non-associative format' => [
                'structure' => [
                    'user' => [
                        ['id', 'name'],
                        [5, 'John'],
                        [6, 'Jane'],
                    ],
                ],
                'exception' => null,
                'tables' => ['user'],
                'columns' => ['user' =>
                    ['id', 'name'],
                ],
                'rows' => ['user' =>
                    [
                        ['id' => 5, 'name' => 'John'],
                        ['id' => 6, 'name' => 'Jane'],
                    ],
                ],
            ],
            'ok associative format' => [
                'structure' => [
                    'user' => [
                        ['id' => 5, 'name' => 'John'],
                        ['id' => 6, 'name' => 'Jane'],
                    ],
                ],
                'exception' => null,
                'tables' => ['user'],
                'columns' => ['user' =>
                    ['id', 'name'],
                ],
                'rows' => ['user' =>
                    [
                        ['id' => 5, 'name' => 'John'],
                        ['id' => 6, 'name' => 'Jane'],
                    ],
                ],
            ],
            'ok multiple' => [
                'structure' => [
                    'user' => [
                        ['id' => 5, 'name' => 'John'],
                        ['id' => 6, 'name' => 'Jane'],
                    ],
                    'course' => [
                        ['id' => 7, 'name' => '101'],
                        ['id' => 8, 'name' => '102'],
                    ],
                ],
                'exception' => null,
                'tables' => ['user', 'course'],
                'columns' => [
                    'user' => ['id', 'name'],
                    'course' => ['id', 'name'],
                ],
                'rows' => [
                    'user' => [
                        ['id' => 5, 'name' => 'John'],
                        ['id' => 6, 'name' => 'Jane'],
                    ],
                    'course' => [
                        ['id' => 7, 'name' => '101'],
                        ['id' => 8, 'name' => '102'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider from_array_provider
     * @covers ::from_array
     */
    public function test_from_array(array $structure, ?string $exception,
        array $tables, array $columns, array $rows, ?bool $repeated = false) {

        $ds = new phpunit_dataset();

        // We need public properties to check the basis.
        $dsref = new \ReflectionClass($ds);
        $dstables = $dsref->getProperty('tables');
        $dscolumns = $dsref->getProperty('columns');
        $dsrows = $dsref->getProperty('rows');

        // We are expecting an exception.
        if (!empty($exception)) {
            $this->expectException('coding_exception');
            $this->expectExceptionMessage($exception);
        }

        $ds->from_array($structure);
        if ($repeated) {
            $ds->from_array($structure);
        }

        $this->assertIsArray($dstables->getValue($ds));
        $this->assertSame($tables, $dstables->getValue($ds));
        $this->assertIsArray($dscolumns->getValue($ds));
        $this->assertSame($columns, $dscolumns->getValue($ds));
        $this->assertIsArray($dsrows->getValue($ds));
        $this->assertEquals($rows, $dsrows->getValue($ds)); // Equals because of stringified integers on load.
    }

    /**
     * test_load_csv() data provider.
     */
    public function load_csv_provider() {

        return [
            'repeated csv table many files' => [
                'files' => [
                    __DIR__ . '/fixtures/sample_dataset.xml',
                    'user' => __DIR__ . '/fixtures/sample_dataset.csv',
                ],
                'exception' => 'csv_dataset_format, table already added to dataset: user',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'ok one csv file' => [
                'files' => [
                    'user' => __DIR__ . '/fixtures/sample_dataset.csv',
                ],
                'exception' => null,
                'tables' => ['user'],
                'columns' => ['user' =>
                    ['id', 'username', 'email']
                ],
                'rows' => ['user' =>
                    [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ]
                ],
            ],
            'ok multiple csv files' => [
                'files' => [
                    'user1' => __DIR__ . '/fixtures/sample_dataset.csv',
                    'user2' => __DIR__ . '/fixtures/sample_dataset.csv',
                ],
                'exception' => null,
                'tables' => ['user1', 'user2'],
                'columns' => [
                    'user1' => ['id', 'username', 'email'],
                    'user2' => ['id', 'username', 'email'],
                ],
                'rows' => [
                    'user1' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                    'user2' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider load_csv_provider
     * @covers ::load_csv
     */
    public function test_load_csv(array $files, ?string $exception,
        array $tables, array $columns, array $rows) {

        $ds = new phpunit_dataset();

        // We need public properties to check the basis.
        $dsref = new \ReflectionClass($ds);
        $dstables = $dsref->getProperty('tables');
        $dscolumns = $dsref->getProperty('columns');
        $dsrows = $dsref->getProperty('rows');

        // We are expecting an exception.
        if (!empty($exception)) {
            $this->expectException('coding_exception');
            $this->expectExceptionMessage($exception);
        }

        $ds->from_files($files);

        $this->assertIsArray($dstables->getValue($ds));
        $this->assertSame($tables, $dstables->getValue($ds));
        $this->assertIsArray($dscolumns->getValue($ds));
        $this->assertSame($columns, $dscolumns->getValue($ds));
        $this->assertIsArray($dsrows->getValue($ds));
        $this->assertEquals($rows, $dsrows->getValue($ds)); // Equals because of stringified integers on load.
    }

    /**
     * test_load_xml() data provider.
     */
    public function load_xml_provider() {

        return [
            'repeated xml table multiple files' => [
                'files' => [
                    'user' => __DIR__ . '/fixtures/sample_dataset.csv',
                    __DIR__ . '/fixtures/sample_dataset.xml',
                ],
                'exception' => 'xml_dataset_format, table already added to dataset: user',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'repeated xml table one file' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_repeated.xml'],
                'exception' => 'xml_dataset_format, table already added to dataset: user',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'wrong dataset element' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_wrong_dataset.xml'],
                'exception' => 'xml_dataset_format, main xml element must be "dataset", found: nodataset',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'wrong table element' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_wrong_table.xml'],
                'exception' => 'xml_dataset_format, only "table" elements allowed, found: notable',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'wrong table name attribute' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_wrong_attribute.xml'],
                'exception' => 'xml_dataset_format, "table" element only allows "name" attribute',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'only col and row allowed' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_only_colrow.xml'],
                'exception' => 'xml_dataset_format, only "column or "row" elements allowed, found: nocolumn',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'wrong value element' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_wrong_value.xml'],
                'exception' => 'xml_dataset_format, only "value" elements allowed, found: novalue',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'column before row' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_col_before_row.xml'],
                'exception' => 'xml_dataset_format, "column" elements always must be before "row" ones',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'row after column' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_row_after_col.xml'],
                'exception' => 'xml_dataset_format, "row" elements always must be after "column" ones',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'number of columns' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_number_of_columns.xml'],
                'exception' => 'xml_dataset_format, number of columns must match number of values, found: 4 vs 3',
                'tables' => [],
                'columns' => [],
                'rows' => [],
            ],
            'ok one xml file' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset.xml'],
                'exception' => null,
                'tables' => ['user'],
                'columns' => ['user' =>
                    ['id', 'username', 'email']
                ],
                'rows' => ['user' =>
                    [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ]
                ],
            ],
            'ok multiple xml files' => [
                'files' => [
                    'user1' => __DIR__ . '/fixtures/sample_dataset.csv',
                    __DIR__ . '/fixtures/sample_dataset.xml',
                ],
                'exception' => null,
                'tables' => ['user1', 'user'],
                'columns' => [
                    'user1' => ['id', 'username', 'email'],
                    'user' => ['id', 'username', 'email'],
                ],
                'rows' => [
                    'user1' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                    'user' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                ],
            ],
            'ok many tables in one xml' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many.xml'],
                'exception' => null,
                'tables' => ['user', 'course'],
                'columns' => [
                    'user' => ['id', 'username', 'email'],
                    'course' => ['id', 'shortname', 'fullname'],
                ],
                'rows' => [
                    'user' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                    'course' => [
                        ['id' => 6, 'shortname' => '101', 'fullname' => '1-0-1'],
                        ['id' => 8, 'shortname' => '202', 'fullname' => '2-0-2'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider load_xml_provider
     * @covers ::load_xml
     */
    public function test_load_xml(array $files, ?string $exception,
        array $tables, array $columns, array $rows) {

        $ds = new phpunit_dataset();

        // We need public properties to check the basis.
        $dsref = new \ReflectionClass($ds);
        $dstables = $dsref->getProperty('tables');
        $dscolumns = $dsref->getProperty('columns');
        $dsrows = $dsref->getProperty('rows');

        // We are expecting an exception.
        if (!empty($exception)) {
            $this->expectException('coding_exception');
            $this->expectExceptionMessage($exception);
        }

        $ds->from_files($files);

        $this->assertIsArray($dstables->getValue($ds));
        $this->assertSame($tables, $dstables->getValue($ds));
        $this->assertIsArray($dscolumns->getValue($ds));
        $this->assertSame($columns, $dscolumns->getValue($ds));
        $this->assertIsArray($dsrows->getValue($ds));
        $this->assertEquals($rows, $dsrows->getValue($ds)); // Equals because of stringified integers on load.
    }

    /**
     * test_to_database() data provider.
     */
    public function to_database_provider() {

        return [
            'wrong table requested' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_insert.xml'],
                'filter' => ['wrongtable'],
                'exception' => 'dataset_to_database, table is not in the dataset: wrongtable',
                'columns' => [],
                'rows' => [],
            ],
            'one table insert' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_insert.xml'],
                'filter' => [],
                'exception' => null,
                'columns' => [
                    'user' => ['username', 'email'],
                ],
                'rows' => ['user' =>
                    [
                        (object)['username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        (object)['username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ]
                ],
            ],
            'one table import' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset.xml'],
                'filter' => [],
                'exception' => null,
                'columns' => [
                    'user' => ['id', 'username', 'email'],
                ],
                'rows' => ['user' =>
                    [
                        (object)['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        (object)['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ]
                ],
            ],
            'multiple table many files import' => [
                'files' => [
                    __DIR__ . '/fixtures/sample_dataset.xml',
                    __DIR__ . '/fixtures/sample_dataset2.xml',
                ],
                'filter' => [],
                'exception' => null,
                'columns' => [
                    'user' => ['id', 'username', 'email'],
                    'course' => ['id', 'shortname', 'fullname'],
                ],
                'rows' => [
                    'user' => [
                        (object)['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        (object)['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                    'course' => [
                        (object)['id' => 6, 'shortname' => '101', 'fullname' => '1-0-1'],
                        (object)['id' => 8, 'shortname' => '202', 'fullname' => '2-0-2'],
                    ],
                ],
            ],
            'multiple table one file import' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many.xml'],
                'filter' => [],
                'exception' => null,
                'columns' => [
                    'user' => ['id', 'username', 'email'],
                    'course' => ['id', 'shortname', 'fullname'],
                ],
                'rows' => [
                    'user' => [
                        (object)['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        (object)['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                    'course' => [
                        (object)['id' => 6, 'shortname' => '101', 'fullname' => '1-0-1'],
                        (object)['id' => 8, 'shortname' => '202', 'fullname' => '2-0-2'],
                    ],
                ],
            ],
            'filtering tables' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many.xml'],
                'filter' => ['course'],
                'exception' => null,
                'columns' => [
                    'user' => ['id', 'username', 'email'],
                    'course' => ['id', 'shortname', 'fullname'],
                ],
                'rows' => [
                    'user' => [], // Table user is being excluded via filter, expect no rows sent to database.
                    'course' => [
                        (object)['id' => 6, 'shortname' => '101', 'fullname' => '1-0-1'],
                        (object)['id' => 8, 'shortname' => '202', 'fullname' => '2-0-2'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider to_database_provider
     * @covers ::to_database
     */
    public function test_to_database(array $files, ?array $filter, ?string $exception, array $columns, array $rows) {
        global $DB;

        $this->resetAfterTest();

        // Grab the status before loading to database.
        $before = [];
        foreach ($columns as $tablename => $tablecolumns) {
            if (!isset($before[$tablename])) {
                $before[$tablename] = [];
            }
            $before[$tablename] = $DB->get_records($tablename, null, '', implode(', ', $tablecolumns));
        }

        $ds = new phpunit_dataset();

        // We are expecting an exception.
        if (!empty($exception)) {
            $this->expectException('coding_exception');
            $this->expectExceptionMessage($exception);
        }

        $ds->from_files($files);
        $ds->to_database($filter);

        // Grab the status after loading to database.
        $after = [];
        foreach ($columns as $tablename => $tablecolumns) {
            if (!isset($after[$tablename])) {
                $after[$tablename] = [];
            }
            $sortandcol = implode(', ', $tablecolumns);
            $after[$tablename] = $DB->get_records($tablename, null, $sortandcol, $sortandcol);
        }

        // Differences must match the expectations.
        foreach ($rows as $tablename => $expectedrows) {
            $changes = array_udiff($after[$tablename], $before[$tablename], function ($b, $a) {
                if ((array)$b > (array)$a) {
                    return 1;
                } else if ((array)$b < (array)$a) {
                    return -1;
                } else {
                    return 0;
                }
            });
            $this->assertEquals(array_values($expectedrows), array_values($changes));
        }
    }

    /**
     * test_get_rows() data provider.
     */
    public function get_rows_provider() {

        return [
            'wrong table requested' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many.xml'],
                'filter' => ['wrongtable'],
                'exception' => 'dataset_get_rows, table is not in the dataset: wrongtable',
                'rows' => [],
            ],
            'ok get rows from empty tables' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many_with_empty.xml'],
                'filter' => ['empty1', 'empty2'],
                'exception' => null,
                'rows' => [
                    'empty1' => [],
                    'empty2' => [],
                ],
            ],
            'ok get rows from one table' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many_with_empty.xml'],
                'filter' => ['user'],
                'exception' => null,
                'rows' => [
                    'user' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                ],
            ],
            'ok get rows from two tables' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many_with_empty.xml'],
                'filter' => ['user', 'course'],
                'exception' => null,
                'rows' => [
                    'user' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                    'course' => [
                        ['id' => 6, 'shortname' => '101', 'fullname' => '1-0-1'],
                        ['id' => 8, 'shortname' => '202', 'fullname' => '2-0-2'],
                    ],
                ],
            ],
            'ok get rows from three tables' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many_with_empty.xml'],
                'filter' => ['user', 'empty1', 'course'],
                'exception' => null,
                'rows' => [
                    'user' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                    'empty1' => [],
                    'course' => [
                        ['id' => 6, 'shortname' => '101', 'fullname' => '1-0-1'],
                        ['id' => 8, 'shortname' => '202', 'fullname' => '2-0-2'],
                    ],
                ],
            ],
            'ok no filter returns all' => [
                'files' => [__DIR__ . '/fixtures/sample_dataset_many_with_empty.xml'],
                'filter' => [],
                'exception' => null,
                'rows' => [
                    'user' => [
                        ['id' => 5, 'username' => 'bozka.novakova', 'email' => 'bozka@example.com'],
                        ['id' => 7, 'username' => 'pepa.novak', 'email' => 'pepa@example.com'],
                    ],
                    'empty1' => [],
                    'empty2' => [],
                    'course' => [
                        ['id' => 6, 'shortname' => '101', 'fullname' => '1-0-1'],
                        ['id' => 8, 'shortname' => '202', 'fullname' => '2-0-2'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider get_rows_provider
     * @covers ::get_rows
     */
    public function test_get_rows(array $files, array $filter, ?string $exception, array $rows) {

        $ds = new phpunit_dataset();

        // We are expecting an exception.
        if (!empty($exception)) {
            $this->expectException('coding_exception');
            $this->expectExceptionMessage($exception);
        }

        $ds->from_files($files);
        $this->assertEquals($rows, $ds->get_rows($filter));
    }
}
