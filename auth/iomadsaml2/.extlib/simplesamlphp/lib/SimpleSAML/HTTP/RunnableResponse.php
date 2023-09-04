<?php

declare(strict_types=1);

namespace SimpleSAML\HTTP;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class modelling a response that consists on running some function.
 *
 * This is a helper class that allows us to have the new and the old architecture coexist. This way, classes and files
 * that aren't PSR-7-aware can still be plugged into a PSR-7-compatible environment.
 *
 * @package SimpleSAML
 */
class RunnableResponse extends Response
{
    /** @var array */
    protected $arguments;

    /** @var callable */
    protected $callable;


    /**
     * RunnableResponse constructor.
     *
     * @param callable $callable A callable that we should run as part of this response.
     * @param array $args An array of arguments to be passed to the callable. Note that each element of the array
     */
    public function __construct(callable $callable, $args = [])
    {
        $this->arguments = $args;
        $this->callable = $callable;
        $this->charset = 'UTF-8';
        parent::__construct();
    }


    /**
     * Get the callable for this response.
     *
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }


    /**
     * Get the arguments to the callable.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }


    /**
     * "Send" this response by actually running the callable.
     *
     * @return $this
     *
     * Note: No return-type possible due to upstream limitations
     */
    public function sendContent()
    {
        return call_user_func_array($this->callable, $this->arguments);
    }
}
