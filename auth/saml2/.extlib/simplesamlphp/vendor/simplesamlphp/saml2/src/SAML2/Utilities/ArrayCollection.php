<?php

declare(strict_types=1);

namespace SAML2\Utilities;

use ArrayIterator;
use Closure;

use SAML2\Exception\RuntimeException;

/**
 * Simple Array implementation of Collection.
 */
class ArrayCollection implements Collection
{
    /**
     * @var array
     */
    protected $elements;


    /**
     * ArrayCollection constructor.
     *
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }


    /**
     * @param mixed $key
     *
     * @return void
     */
    public function add($key) : void
    {
        $this->elements[] = $key;
    }


    /**
     * @param mixed $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        return isset($this->elements[$key]) ? $this->elements[$key] : null;
    }


    /**
     * @param \Closure $f
     *
     * @return ArrayCollection
     */
    public function filter(Closure $filterFunction) : Collection
    {
        return new self(array_filter($this->elements, $filterFunction));
    }


    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value) : void
    {
        $this->elements[$key] = $value;
    }


    /**
     * @param mixed $key
     *
     * @return void
     */
    public function remove($key) : void
    {
        $elt = array_search($key, $this->elements);
        if ($elt === false) {
            return;
        }
        unset($this->elements[$elt]);
    }


    /**
     * @throws RuntimeException
     * @return bool|mixed
     */
    public function getOnlyElement()
    {
        if ($this->count() !== 1) {
            throw new RuntimeException(sprintf(
                __CLASS__.'::'.__METHOD__.' requires that the collection has exactly one element, '
                . '"%d" elements found',
                $this->count()
            ));
        }

        return reset($this->elements);
    }


    /**
     * @return bool|mixed
     */
    public function first()
    {
        return reset($this->elements);
    }


    /**
     * @return bool|mixed
     */
    public function last()
    {
        return end($this->elements);
    }


    /**
     * @param \Closure $function
     *
     * @return ArrayCollection
     */
    public function map(Closure $function) : ArrayCollection
    {
        return new self(array_map($function, $this->elements));
    }


    /**
     * @return int
     */
    public function count() : int
    {
        return count($this->elements);
    }


    /**
     * @return \ArrayIterator
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->elements);
    }


    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->elements[$offset]);
    }


    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->elements[$offset];
    }


    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value) : void
    {
        $this->elements[$offset] = $value;
    }


    /**
     * @param $offset
     * @return void
     */
    public function offsetUnset($offset) : void
    {
        unset($this->elements[$offset]);
    }
}
