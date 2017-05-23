<?php

declare(strict_types=1);

namespace Phpml\Math;

class Set implements \IteratorAggregate
{
    /**
     * @var string[]|int[]|float[]
     */
    private $elements;

    /**
     * @param string[]|int[]|float[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = self::sanitize($elements);
    }

    /**
     * Creates the union of A and B.
     *
     * @param Set $a
     * @param Set $b
     *
     * @return Set
     */
    public static function union(Set $a, Set $b) : Set
    {
        return new self(array_merge($a->toArray(), $b->toArray()));
    }

    /**
     * Creates the intersection of A and B.
     *
     * @param Set $a
     * @param Set $b
     *
     * @return Set
     */
    public static function intersection(Set $a, Set $b) : Set
    {
        return new self(array_intersect($a->toArray(), $b->toArray()));
    }

    /**
     * Creates the difference of A and B.
     *
     * @param Set $a
     * @param Set $b
     *
     * @return Set
     */
    public static function difference(Set $a, Set $b) : Set
    {
        return new self(array_diff($a->toArray(), $b->toArray()));
    }

    /**
     * Creates the Cartesian product of A and B.
     *
     * @param Set $a
     * @param Set $b
     *
     * @return Set[]
     */
    public static function cartesian(Set $a, Set $b) : array
    {
        $cartesian = [];

        foreach ($a as $multiplier) {
            foreach ($b as $multiplicand) {
                $cartesian[] = new self(array_merge([$multiplicand], [$multiplier]));
            }
        }

        return $cartesian;
    }

    /**
     * Creates the power set of A.
     *
     * @param Set $a
     *
     * @return Set[]
     */
    public static function power(Set $a) : array
    {
        $power = [new self()];

        foreach ($a as $multiplicand) {
            foreach ($power as $multiplier) {
                $power[] = new self(array_merge([$multiplicand], $multiplier->toArray()));
            }
        }

        return $power;
    }

    /**
     * Removes duplicates and rewrites index.
     *
     * @param string[]|int[]|float[] $elements
     *
     * @return string[]|int[]|float[]
     */
    private static function sanitize(array $elements) : array
    {
        sort($elements, SORT_ASC);

        return array_values(array_unique($elements, SORT_ASC));
    }

    /**
     * @param string|int|float $element
     *
     * @return Set
     */
    public function add($element) : Set
    {
        return $this->addAll([$element]);
    }

    /**
     * @param string[]|int[]|float[] $elements
     *
     * @return Set
     */
    public function addAll(array $elements) : Set
    {
        $this->elements = self::sanitize(array_merge($this->elements, $elements));

        return $this;
    }

    /**
     * @param string|int|float $element
     *
     * @return Set
     */
    public function remove($element) : Set
    {
        return $this->removeAll([$element]);
    }

    /**
     * @param string[]|int[]|float[] $elements
     *
     * @return Set
     */
    public function removeAll(array $elements) : Set
    {
        $this->elements = self::sanitize(array_diff($this->elements, $elements));

        return $this;
    }

    /**
     * @param string|int|float $element
     *
     * @return bool
     */
    public function contains($element) : bool
    {
        return $this->containsAll([$element]);
    }

    /**
     * @param string[]|int[]|float[] $elements
     *
     * @return bool
     */
    public function containsAll(array $elements) : bool
    {
        return !array_diff($elements, $this->elements);
    }

    /**
     * @return string[]|int[]|float[]
     */
    public function toArray() : array
    {
        return $this->elements;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return $this->cardinality() == 0;
    }

    /**
     * @return int
     */
    public function cardinality() : int
    {
        return count($this->elements);
    }
}
