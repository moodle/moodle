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
 * Unit tests for core_table\local\filter\filterset.
 *
 * @package   core_table
 * @category  test
 * @copyright 2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

declare(strict_types=1);

namespace core_table\local\filter;

use InvalidArgumentException;
use UnexpectedValueException;
use advanced_testcase;
use moodle_exception;

/**
 * Unit tests for core_table\local\filter\filterset.
 *
 * @package   core_table
 * @category  test
 * @copyright 2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filterset_test extends advanced_testcase {
    /**
     * Ensure that it is possibly to set the join type.
     */
    public function test_set_join_type(): void {
        $filterset = $this->get_mocked_filterset();

        // Initial set with the default type should just work.
        // The setter should be chainable.
        $this->assertEquals($filterset, $filterset->set_join_type(filterset::JOINTYPE_DEFAULT));
        $this->assertEquals(filterset::JOINTYPE_DEFAULT, $filterset->get_join_type());

        // It should be possible to update the join type later.
        $this->assertEquals($filterset, $filterset->set_join_type(filterset::JOINTYPE_NONE));
        $this->assertEquals(filterset::JOINTYPE_NONE, $filterset->get_join_type());

        $this->assertEquals($filterset, $filterset->set_join_type(filterset::JOINTYPE_ANY));
        $this->assertEquals(filterset::JOINTYPE_ANY, $filterset->get_join_type());

        $this->assertEquals($filterset, $filterset->set_join_type(filterset::JOINTYPE_ALL));
        $this->assertEquals(filterset::JOINTYPE_ALL, $filterset->get_join_type());
    }

    /**
     * Ensure that it is not possible to provide a value out of bounds when setting the join type.
     */
    public function test_set_join_type_invalid_low(): void {
        $filterset = $this->get_mocked_filterset();

        // Valid join types are current 0, 1, or 2.
        // A value too low should be rejected.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid join type specified");
        $filterset->set_join_type(-1);
    }

    /**
     * Ensure that it is not possible to provide a value out of bounds when setting the join type.
     */
    public function test_set_join_type_invalid_high(): void {
        $filterset = $this->get_mocked_filterset();

        // Valid join types are current 0, 1, or 2.
        // A value too low should be rejected.
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid join type specified");
        $filterset->set_join_type(4);
    }

    /**
     * Ensure that adding filter values works as expected.
     */
    public function test_add_filter_value(): void {
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                'name' => filter::class,
                'species' => filter::class,
            ]));

        // Initially an empty list.
        $this->assertEmpty($filterset->get_filters());

        // Test data.
        $speciesfilter = new filter('species', null, ['canine']);
        $namefilter = new filter('name', null, ['rosie']);

        // Add a filter to the list.
        $filterset->add_filter($speciesfilter);
        $this->assertSame([
            $speciesfilter,
        ], array_values($filterset->get_filters()));

        // Adding a second value should add that value.
        // The values should sorted.
        $filterset->add_filter($namefilter);
        $this->assertSame([
            $namefilter,
            $speciesfilter,
        ], array_values($filterset->get_filters()));

        // Adding an existing filter again should be ignored.
        $filterset->add_filter($speciesfilter);
        $this->assertSame([
            $namefilter,
            $speciesfilter,
        ], array_values($filterset->get_filters()));
    }

    /**
     * Ensure that it is possible to add a filter of a validated filter type.
     */
    public function test_add_filter_validated_type(): void {
        $namefilter = $this->getMockBuilder(filter::class)
            ->setConstructorArgs(['name'])
            ->onlyMethods([])
            ->getMock();
        $namefilter->add_filter_value('rosie');

        // Mock the get_optional_filters function.
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                 'name' => get_class($namefilter),
             ]));

        // Add a filter to the list.
        // This is the 'name' filter.
        $filterset->add_filter($namefilter);

        $this->assertNull($filterset->check_validity());
    }

    /**
     * Ensure that it is not possible to add a type which is not expected.
     */
    public function test_add_filter_unexpected_key(): void {
        // Mock the get_optional_filters function.
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([]));

        // Add a filter to the list.
        // This is the 'name' filter.
        $namefilter = new filter('name');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The filter 'name' was not recognised.");
        $filterset->add_filter($namefilter);
    }

    /**
     * Ensure that it is not possible to add a validated type where the type is incorrect.
     */
    public function test_add_filter_validated_type_incorrect(): void {
        $filtername = "name";
        $otherfilter = $this->createMock(filter::class);

        // Mock the get_optional_filters function.
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                $filtername => get_class($otherfilter),
            ]));

        // Add a filter to the list.
        // This is the 'name' filter.
        $namefilter = $this->getMockBuilder(filter::class)
            ->onlyMethods([])
            ->setConstructorArgs([$filtername])
            ->getMock();

        $actualtype = get_class($namefilter);
        $requiredtype = get_class($otherfilter);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "The filter '{$filtername}' was incorrectly specified as a {$actualtype}. It must be a {$requiredtype}."
        );
        $filterset->add_filter($namefilter);
    }

    /**
     * Ensure that a filter can be added from parameters provided to a web service.
     */
    public function test_add_filter_from_params(): void {
        $filtername = "name";
        $otherfilter = $this->getMockBuilder(filter::class)
            ->onlyMethods([])
            ->setConstructorArgs([$filtername])
            ->getMock();

        // Mock the get_optional_filters function.
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                $filtername => get_class($otherfilter),
            ]));

        $result = $filterset->add_filter_from_params($filtername, filter::JOINTYPE_DEFAULT, ['kevin']);

        // The function is chainable.
        $this->assertEquals($filterset, $result);

        // Get the filter back.
        $filter = $filterset->get_filter($filtername);
        $this->assertEquals($filtername, $filter->get_name());
        $this->assertEquals(filter::JOINTYPE_DEFAULT, $filter->get_join_type());
        $this->assertEquals(['kevin'], $filter->get_filter_values());
    }

    /**
     * Ensure that an unknown filter is not added.
     */
    public function test_add_filter_from_params_unable_to_autoload(): void {
        // Mock the get_optional_filters function.
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                'name' => '\\moodle\\this\\is\\a\\fake\\class\\name',
            ]));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "The filter class '\\moodle\\this\\is\\a\\fake\\class\\name' for filter 'name' could not be found."
        );
        $filterset->add_filter_from_params('name', filter::JOINTYPE_DEFAULT, ['kevin']);
    }

    /**
     * Ensure that an unknown filter is not added.
     */
    public function test_add_filter_from_params_invalid(): void {
        $filtername = "name";
        $otherfilter = $this->getMockBuilder(filter::class)
            ->onlyMethods([])
            ->setConstructorArgs([$filtername])
            ->getMock();

        // Mock the get_optional_filters function.
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                $filtername => get_class($otherfilter),
            ]));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The filter 'unknownfilter' was not recognised.");
        $filterset->add_filter_from_params('unknownfilter', filter::JOINTYPE_DEFAULT, ['kevin']);
    }

    /**
     * Ensure that adding a different filter with a different object throws an Exception.
     */
    public function test_duplicate_filter_value(): void {
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                'name' => filter::class,
                'species' => filter::class,
            ]));

        // Add a filter to the list.
        // This is the 'name' filter.
        $namefilter = new filter('name', null, ['rosie']);
        $filterset->add_filter($namefilter);

        // Add another filter to the list.
        // This one has been incorrectly called the 'name' filter when it should be 'species'.
        $this->expectException(UnexpectedValueException::Class);
        $this->expectExceptionMessage("A filter of type 'name' has already been added. Check that you have the correct filter.");

        $speciesfilter = new filter('name', null, ['canine']);
        $filterset->add_filter($speciesfilter);
    }

    /**
     * Ensure that validating a filterset correctly compares filter types.
     */
    public function test_check_validity_optional_filters_not_specified(): void {
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                'name' => filter::class,
                'species' => filter::class,
            ]));

        $this->assertNull($filterset->check_validity());
    }

    /**
     * Ensure that validating a filterset correctly requires required filters.
     */
    public function test_check_validity_required_filter(): void {
        $filterset = $this->get_mocked_filterset(['get_required_filters']);
        $filterset->expects($this->any())
            ->method('get_required_filters')
            ->willReturn([
                'name' => filter::class
            ]);

        // Add a filter to the list.
        // This is the 'name' filter.
        $filterset->add_filter(new filter('name'));

        $this->assertNull($filterset->check_validity());
    }

    /**
     * Ensure that validating a filterset excepts correctly when a required fieldset is missing.
     */
    public function test_check_validity_filter_missing_required(): void {
        $filterset = $this->get_mocked_filterset(['get_required_filters']);
        $filterset->expects($this->any())
             ->method('get_required_filters')
             ->willReturn([
                 'name' => filter::class,
                 'species' => filter::class,
             ]);

        $this->expectException(moodle_exception::Class);
        $this->expectExceptionMessage("One or more required filters were missing (name, species)");
        $filterset->check_validity();
    }

    /**
     * Ensure that getting the filters returns a sorted list of filters.
     */
    public function test_get_filters(): void {
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                // Filters are not defined lexically.
                'd' => filter::class,
                'b' => filter::class,
                'a' => filter::class,
                'c' => filter::class,
            ]));

        // Filters are added in a different non-lexical order.
        $c = new filter('c');
        $filterset->add_filter($c);

        $b = new filter('b');
        $filterset->add_filter($b);

        $d = new filter('d');
        $filterset->add_filter($d);

        $a = new filter('a');
        $filterset->add_filter($a);

        // But they are returned lexically sorted.
        $this->assertEquals([
            'a' => $a,
            'b' => $b,
            'c' => $c,
            'd' => $d,
        ], $filterset->get_filters());
    }

    /**
     * Ensure that getting a singlel filter returns the correct filter.
     */
    public function test_get_filter(): void {
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                // Filters are not defined lexically.
                'd' => filter::class,
                'b' => filter::class,
                'a' => filter::class,
                'c' => filter::class,
            ]));

        // Filters are added in a different non-lexical order.
        $c = new filter('c');
        $filterset->add_filter($c);

        $b = new filter('b');
        $filterset->add_filter($b);

        $d = new filter('d');
        $filterset->add_filter($d);

        $a = new filter('a');
        $filterset->add_filter($a);

        // Filters can be individually retrieved in any order.
        $this->assertEquals($d, $filterset->get_filter('d'));
        $this->assertEquals($a, $filterset->get_filter('a'));
        $this->assertEquals($b, $filterset->get_filter('b'));
        $this->assertEquals($c, $filterset->get_filter('c'));
    }

    /**
     * Ensure that it is not possible to retrieve an unknown filter.
     */
    public function test_get_filter_unknown(): void {
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                'a' => filter::class,
            ]));

        $a = new filter('a');
        $filterset->add_filter($a);

        $this->expectException(UnexpectedValueException::Class);
        $this->expectExceptionMessage("The filter specified (d) is invalid.");
        $filterset->get_filter('d');
    }

    /**
     * Ensure that it is not possible to retrieve a valid filter before it is created.
     */
    public function test_get_filter_not_yet_added(): void {
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                'a' => filter::class,
            ]));

        $this->expectException(UnexpectedValueException::Class);
        $this->expectExceptionMessage("The filter specified (a) has not been created.");
        $filterset->get_filter('a');
    }

    /**
     * Ensure that the get_all_filtertypes function correctly returns the combined filterset.
     */
    public function test_get_all_filtertypes(): void {
        $otherfilter = $this->createMock(filter::class);

        $filterset = $this->get_mocked_filterset([
            'get_optional_filters',
            'get_required_filters',
        ]);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                'a' => filter::class,
                'c' => get_class($otherfilter),
            ]));
        $filterset->method('get_required_filters')
            ->will($this->returnValue([
                'b' => get_class($otherfilter),
                'd' => filter::class,
            ]));

        $this->assertEquals([
            'a' => filter::class,
            'b' => get_class($otherfilter),
            'c' => get_class($otherfilter),
            'd' => filter::class,
        ], $filterset->get_all_filtertypes());
    }

    /**
     * Ensure that the get_all_filtertypes function correctly returns the combined filterset.
     */
    public function test_get_all_filtertypes_conflict(): void {
        $otherfilter = $this->createMock(filter::class);

        $filterset = $this->get_mocked_filterset([
            'get_optional_filters',
            'get_required_filters',
        ]);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                'a' => filter::class,
                'b' => get_class($otherfilter),
                'd' => filter::class,
            ]));
        $filterset->method('get_required_filters')
            ->will($this->returnValue([
                'b' => get_class($otherfilter),
                'c' => filter::class,
                'd' => filter::class,
            ]));

        $this->expectException(InvalidArgumentException::Class);
        $this->expectExceptionMessage("Some filter types are both required, and optional: b, d");
        $filterset->get_all_filtertypes();

    }

    /**
     * Ensure that the has_filter function works as expected.
     */
    public function test_has_filter(): void {
        $filterset = $this->get_mocked_filterset(['get_optional_filters']);
        $filterset->method('get_optional_filters')
            ->will($this->returnValue([
                // Define filters 'a', and 'b'.
                'a' => filter::class,
                'b' => filter::class,
            ]));

        // Only add filter 'a'.
        $a = new filter('a');
        $filterset->add_filter($a);

        // Filter 'a' should exist.
        $this->assertTrue($filterset->has_filter('a'));

        // Filter 'b' is defined, but has not been added.
        $this->assertFalse($filterset->has_filter('b'));

        // Filter 'c' is not defined.
        // No need to throw any kind of exception - this is an existence check.
        $this->assertFalse($filterset->has_filter('c'));
    }

    /**
     * Get a mocked copy of the filterset, mocking the specified methods.
     *
     * @param array $mockedmethods anonymous array containing the list of mocked methods
     * @return filterset Mock of the filterset
     */
    protected function get_mocked_filterset(array $mockedmethods = []): filterset {

        return $this->getMockForAbstractClass(filterset::class, [], '', true, true, true, $mockedmethods);
    }
}
