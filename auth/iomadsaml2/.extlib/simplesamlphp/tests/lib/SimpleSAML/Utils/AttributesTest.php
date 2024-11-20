<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Error;
use SimpleSAML\Utils\Attributes;

/**
 * Tests for SimpleSAML\Utils\Attributes.
 *
 * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
 */
class AttributesTest extends TestCase
{
    /**
     * Test the getExpectedAttribute() method with invalid attributes array.
     * @return void
     * @psalm-suppress InvalidArgument
     * @deprecated Can be removed as soon as the codebase is fully typehinted
     */
    public function testGetExpectedAttributeInvalidAttributesArray()
    {
        // check with empty array as input
        $attributes = 'string';
        $expected = 'string';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The attributes array is not an array, it is: ' . print_r($attributes, true) . '.'
        );
        Attributes::getExpectedAttribute($attributes, $expected);
    }


    /**
     * Test the getExpectedAttributeMethod() method with invalid expected attribute parameter.
     * @deprecated Remove this test as soon as the codebase is fully typehinted
     * @psalm-suppress PossiblyFalseArgument
     * @return void
     */
    public function testGetExpectedAttributeInvalidAttributeName()
    {
        // check with invalid attribute name
        $attributes = [];
        $expected = false;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The expected attribute is not a string, it is: ' . print_r($expected, true) . '.'
        );
        Attributes::getExpectedAttribute($attributes, $expected);
    }


    /**
     * Test the getExpectedAttributeMethod() method with a non-normalized attributes array.
     * @return void
     */
    public function testGetExpectedAttributeNonNormalizedArray(): void
    {
        // check with non-normalized attributes array
        $attributes = [
            'attribute' => 'value',
        ];
        $expected = 'attribute';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The attributes array is not normalized, values should be arrays.'
        );
        Attributes::getExpectedAttribute($attributes, $expected);
    }


    /**
     * Test the getExpectedAttribute() method with valid input but missing expected attribute.
     * @return void
     */
    public function testGetExpectedAttributeMissingAttribute(): void
    {
        // check missing attribute
        $attributes = [
            'attribute' => ['value'],
        ];
        $expected = 'missing';
        $this->expectException(Error\Exception::class);
        $this->expectExceptionMessage("No such attribute '" . $expected . "' found.");
        Attributes::getExpectedAttribute($attributes, $expected);
    }


    /**
     * Test the getExpectedAttribute() method with an empty attribute.
     * @return void
     */
    public function testGetExpectedAttributeEmptyAttribute(): void
    {
        // check empty attribute
        $attributes = [
            'attribute' => [],
        ];
        $expected = 'attribute';
        $this->expectException(Error\Exception::class);
        $this->expectExceptionMessage("Empty attribute '" . $expected . "'.'");
        Attributes::getExpectedAttribute($attributes, $expected);
    }


    /**
     * Test the getExpectedAttributeMethod() method with multiple values (not being allowed).
     * @return void
     */
    public function testGetExpectedAttributeMultipleValues(): void
    {
        // check attribute with more than value, that being not allowed
        $attributes = [
            'attribute' => [
                'value1',
                'value2',
            ],
        ];
        $expected = 'attribute';
        $this->expectException(Error\Exception::class);
        $this->expectExceptionMessage(
            'More than one value found for the attribute, multiple values not allowed.'
        );
        Attributes::getExpectedAttribute($attributes, $expected);
    }


    /**
     * Test that the getExpectedAttribute() method successfully obtains values from the attributes array.
     * @return void
     */
    public function testGetExpectedAttribute(): void
    {
        // check one value
        $value = 'value';
        $attributes = [
            'attribute' => [$value],
        ];
        $expected = 'attribute';
        $this->assertEquals($value, Attributes::getExpectedAttribute($attributes, $expected));

        // check multiple (allowed) values
        $value = 'value';
        $attributes = [
            'attribute' => [$value, 'value2', 'value3'],
        ];
        $expected = 'attribute';
        $this->assertEquals($value, Attributes::getExpectedAttribute($attributes, $expected, true));
    }


    /**
     * Test the normalizeAttributesArray() function with input not being an array
     * @return void
     * @psalm-suppress InvalidArgument
     * @deprecated Can be removed as soon as the codebase is fully typehinted
     */
    public function testNormalizeAttributesArrayBadInput()
    {
        $this->expectException(InvalidArgumentException::class);
        Attributes::normalizeAttributesArray('string');
    }


    /**
     * Test the normalizeAttributesArray() function with an array with non-string attribute names.
     * @return void
     */
    public function testNormalizeAttributesArrayBadKeys(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Attributes::normalizeAttributesArray(['attr1' => 'value1', 1 => 'value2']);
    }


    /**
     * Test the normalizeAttributesArray() function with an array with non-string attribute values.
     * @return void
     */
    public function testNormalizeAttributesArrayBadValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Attributes::normalizeAttributesArray(['attr1' => 'value1', 'attr2' => 0]);
    }


    /**
     * Test the normalizeAttributesArray() function.
     * @return void
     */
    public function testNormalizeAttributesArray(): void
    {
        $attributes = [
            'key1' => 'value1',
            'key2' => ['value2', 'value3'],
            'key3' => 'value1'
        ];
        $expected = [
            'key1' => ['value1'],
            'key2' => ['value2', 'value3'],
            'key3' => ['value1']
        ];
        $this->assertEquals(
            $expected,
            Attributes::normalizeAttributesArray($attributes),
            'Attribute array normalization failed'
        );
    }


    /**
     * Test the getAttributeNamespace() function.
     * @return void
     */
    public function testNamespacedAttributes(): void
    {
        // test for only the name
        $this->assertEquals(
            ['default', 'name'],
            Attributes::getAttributeNamespace('name', 'default')
        );

        // test for a given namespace and multiple '/'
        $this->assertEquals(
            ['some/namespace', 'name'],
            Attributes::getAttributeNamespace('some/namespace/name', 'default')
        );
    }
}
