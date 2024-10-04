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

namespace core;

/**
 * Tests for the attribute_helper.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\attribute_helper
 */
final class attribute_helper_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        require_once(__DIR__ . '/fixtures/attribute_helper_example.php');
        parent::setUpBeforeClass();
    }

    /**
     * @dataProvider get_attributes_provider
     */
    public function test_get_attributes(
        int $expectedcount,
        array $args,
    ): void {
        $attributes = attribute_helper::from(...$args);
        $instances = attribute_helper::instances(...$args);
        if ($expectedcount) {
            $this->assertNotEmpty($attributes);
            $this->assertCount($expectedcount, $attributes);
            $this->assertCount($expectedcount, $instances);

        } else {
            $this->assertEmpty($attributes);
            $this->assertEmpty($instances);
        }
    }

    public static function get_attributes_provider(): array {
        return [
            [3, [[attribute_helper_example::class]]],
            [0, [[attribute_helper_example_without::class]]],
            [3, [[attribute_helper_example::class, 'WITH_ATTRIBUTES']]],
            [0, [[attribute_helper_example::class, 'WITHOUT_ATTRIBUTE']]],
            [3, [[attribute_helper_example::class, 'withattributes']]],
            [0, [[attribute_helper_example::class, 'withoutattributes']]],
            [3, [[attribute_helper_example::class, 'with_attributes']]],
            [0, [[attribute_helper_example::class, 'without_attributes']]],
            [3, [[attribute_helper_enum::class, 'WITH_ATTRIBUTES']]],
            [0, [[attribute_helper_enum::class, 'WITHOUT_ATTRIBUTE']]],
            [3, [__NAMESPACE__ . '\\attribute_helper_method_with']],
            [0, [__NAMESPACE__ . '\\attribute_helper_method_without']],
            [0, [__NAMESPACE__ . '\\function_not_exists']],

            [3, [attribute_helper_example::class]],
            [0, [attribute_helper_example_without::class]],
            [3, [attribute_helper_example::class . '::WITH_ATTRIBUTES']],
            [0, [attribute_helper_example::class . '::WITHOUT_ATTRIBUTE']],
            [3, [attribute_helper_example::class . '::withattributes']],
            [0, [attribute_helper_example::class . '::withoutattributes']],
            [3, [attribute_helper_example::class . '::with_attributes']],
            [0, [attribute_helper_example::class . '::without_attributes']],
            [3, [attribute_helper_enum::class . '::WITH_ATTRIBUTES']],
            [0, [attribute_helper_enum::class . '::WITHOUT_ATTRIBUTE']],

            [2, [[attribute_helper_example::class], attribute_helper_attribute_a::class]],
            [0, [[attribute_helper_example_without::class], attribute_helper_attribute_a::class]],
            [2, [[attribute_helper_example::class, 'WITH_ATTRIBUTES'], attribute_helper_attribute_a::class]],
            [0, [[attribute_helper_example::class, 'WITHOUT_ATTRIBUTE'], attribute_helper_attribute_a::class]],
            [2, [[attribute_helper_example::class, 'withattributes'], attribute_helper_attribute_a::class]],
            [0, [[attribute_helper_example::class, 'withoutattributes'], attribute_helper_attribute_a::class]],
            [2, [[attribute_helper_example::class, 'with_attributes'], attribute_helper_attribute_a::class]],
            [0, [[attribute_helper_example::class, 'without_attributes'], attribute_helper_attribute_a::class]],
            [2, [[attribute_helper_enum::class, 'WITH_ATTRIBUTES'], attribute_helper_attribute_a::class]],
            [0, [[attribute_helper_enum::class, 'WITHOUT_ATTRIBUTE'], attribute_helper_attribute_a::class]],
            [2, [__NAMESPACE__ . '\\attribute_helper_method_with', attribute_helper_attribute_a::class]],
            [0, [__NAMESPACE__ . '\\attribute_helper_method_without', attribute_helper_attribute_a::class]],
        ];
    }

    public function test_get_attributes_references(): void {
        $attributes = attribute_helper::from(new attribute_helper_example());
        $this->assertCount(3, $attributes);

        $attributes = attribute_helper::from(
            new attribute_helper_example(),
            attribute_helper_attribute_a::class,
        );
        $this->assertCount(2, $attributes);

        $attributes = attribute_helper::from(attribute_helper_enum::WITH_ATTRIBUTES);
        $this->assertCount(3, $attributes);
        $instances = attribute_helper::instances(attribute_helper_enum::WITH_ATTRIBUTES);
        $this->assertCount(3, $instances);

        $attributes = attribute_helper::from(
            attribute_helper_enum::WITH_ATTRIBUTES,
            attribute_helper_attribute_a::class,
        );
        $this->assertCount(2, $attributes);
        $instances = attribute_helper::instances(
            attribute_helper_enum::WITH_ATTRIBUTES,
            attribute_helper_attribute_a::class,
        );
        $this->assertCount(2, $instances);
        array_map(
            fn($instance) => $this->assertInstanceOf(attribute_helper_attribute_a::class, $instance),
            $instances,
        );

        // Singular fetches.
        $attribute = attribute_helper::one_from(
            attribute_helper_enum::WITH_ATTRIBUTES,
            attribute_helper_attribute_b::class,
        );
        $this->assertInstanceOf(\ReflectionAttribute::class, $attribute);
        $instance = attribute_helper::instance(
            attribute_helper_enum::WITH_ATTRIBUTES,
            attribute_helper_attribute_b::class,
        );
        $this->assertInstanceOf(attribute_helper_attribute_b::class, $instance);
    }

    public function test_get_attributes_not_valid(): void {
        $this->assertNull(attribute_helper::from(non_existent_class::class));
        $this->assertNull(attribute_helper::from([non_existent_class::class]));
        $this->assertNull(attribute_helper::from([attribute_helper_example::class, 'non_existent']));
        $this->assertNull(attribute_helper::from([non_existent_class::class, 'non_existent']));
        $this->assertNull(attribute_helper::instances(non_existent_class::class));
        $this->assertNull(attribute_helper::instances([non_existent_class::class]));
        $this->assertNull(attribute_helper::instances([attribute_helper_example::class, 'non_existent']));
        $this->assertNull(attribute_helper::instances([non_existent_class::class, 'non_existent']));

        // Test singular fetches.
        $this->assertNull(attribute_helper::one_from(non_existent_class::class));
        $this->assertNull(attribute_helper::one_from([non_existent_class::class]));
        $this->assertNull(attribute_helper::one_from([attribute_helper_example::class, 'non_existent']));
        $this->assertNull(attribute_helper::one_from([non_existent_class::class, 'non_existent']));
        $this->assertNull(attribute_helper::instance(non_existent_class::class));
        $this->assertNull(attribute_helper::instance([non_existent_class::class]));
        $this->assertNull(attribute_helper::instance([attribute_helper_example::class, 'non_existent']));
        $this->assertNull(attribute_helper::instance([non_existent_class::class, 'non_existent']));
    }

    public function test_get_attribute_too_many(): void {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('More than one attribute found');
        attribute_helper::one_from(attribute_helper_example::class);
    }

    public function test_get_instance_too_many(): void {
        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage('More than one attribute found');
        attribute_helper::instance(attribute_helper_example::class);
    }
}
