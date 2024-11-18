<?php

declare(strict_types=1);

namespace SAML2\Utilities;

use Closure;

interface Collection extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Add an element to the collection
     *
     * @param mixed $key
     *
     * @return void
     */
    public function add($key) : void;


    /**
     * Shorthand for getting a single element that also must be the only element in the collection.
     *
     * @throws \SAML2\Exception\RuntimeException if the element was not the only element
     *
     * @return mixed
     */
    public function getOnlyElement();


    /**
     * Return the first element from the collection
     *
     * @return mixed
     */
    public function first();


    /**
     * Return the last element from the collection
     *
     * @return mixed
     */
    public function last();


    /**
     * Applies the given function to each element in the collection and returns a new collection with the elements
     * returned by the function.
     *
     * @param \Closure $function
     *
     * @return mixed
     */
    public function map(Closure $function);


    /**
     * @param \Closure $filterFunction
     *
     * @return \SAML2\Utilities\Collection
     */
    public function filter(Closure $filterFunction): Collection;


    /**
     * Get the element at index
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key);


    /**
     * @param mixed $element
     * @return void
     */
    public function remove($key) : void;


    /**
     * Set the value for index
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value) : void;
}
