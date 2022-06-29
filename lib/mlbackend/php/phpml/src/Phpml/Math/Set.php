<?php

declare(strict_types=1);

namespace Phpml\Math;

use ArrayIterator;
use IteratorAggregate;

class Set implements IteratorAggregate
{
    /**
     * @var string[]|int[]|float[]|bool[]
     */
    private $elements = [];

    /**
     * @param string[]|int[]|float[]|bool[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = self::sanitize($elements);
    }

    /**
     * Creates the union of A and B.
     */
    public static function union(self $a, self $b): self
    {
        return new self(array_merge($a->toArray(), $b->toArray()));
    }

    /**
     * Creates the intersection of A and B.
     */
    public static function intersection(self $a, self $b): self
    {
        return new self(array_intersect($a->toArray(), $b->toArray()));
    }

    /**
     * Creates the difference of A and B.
     */
    public static function difference(self $a, self $b): self
    {
        return new self(array_diff($a->toArray(), $b->toArray()));
    }

    /**
     * Creates the Cartesian product of A and B.
     *
     * @return Set[]
     */
    public static function cartesian(self $a, self $b): array
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
     * @return Set[]
     */
    public static function power(self $a): array
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
     * @param string|int|float|bool $element
     */
    public function add($element): self
    {
        return $this->addAll([$element]);
    }

    /**
     * @param string[]|int[]|float[]|bool[] $elements
     */
    public function addAll(array $elements): self
    {
        $this->elements = self::sanitize(array_merge($this->elements, $elements));

        return $this;
    }

    /**
     * @param string|int|float $element
     */
    public function remove($element): self
    {
        return $this->removeAll([$element]);
    }

    /**
     * @param string[]|int[]|float[] $elements
     */
    public function removeAll(array $elements): self
    {
        $this->elements = self::sanitize(array_diff($this->elements, $elements));

        return $this;
    }

    /**
     * @param string|int|float $element
     */
    public function contains($element): bool
    {
        return $this->containsAll([$element]);
    }

    /**
     * @param string[]|int[]|float[] $elements
     */
    public function containsAll(array $elements): bool
    {
        return count(array_diff($elements, $this->elements)) === 0;
    }

    /**
     * @return string[]|int[]|float[]|bool[]
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }

    public function isEmpty(): bool
    {
        return $this->cardinality() === 0;
    }

    public function cardinality(): int
    {
        return count($this->elements);
    }

    /**
     * Removes duplicates and rewrites index.
     *
     * @param string[]|int[]|float[]|bool[] $elements
     *
     * @return string[]|int[]|float[]|bool[]
     */
    private static function sanitize(array $elements): array
    {
        sort($elements, SORT_ASC);

        return array_values(array_unique($elements, SORT_ASC));
    }
}
