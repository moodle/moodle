<?php
/**
 * Class for generating a 23-character random ID string. This string uses all
 * characters in the class [-_0-9a-zA-Z].
 *
 * <code>
 * $id = (string)new Horde_Support_Randomid();
 * </code>
 *
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/bsd BSD
 * @package  Support
 */
class Horde_Support_Randomid
{
    /**
     * Generated ID.
     *
     * @var string
     */
    private $_id;

    /**
     * New random ID.
     */
    public function __construct()
    {
        $this->_id = $this->generate();
    }

    /**
     * Generate a random ID.
     */
    public function generate()
    {
        $elts = array(
            uniqid(),
            mt_rand(),
            getmypid(),
            spl_object_hash($this)
        );
        if (function_exists('zend_thread_id')) {
            $elts[] = zend_thread_id();
        }
        if (function_exists('sys_getloadavg') &&
            ($loadavg = sys_getloadavg())) {
            $elts = array_merge($elts, $loadavg);
        }
        if (function_exists('memory_get_usage')) {
            $elts[] = memory_get_usage();
            $elts[] = memory_get_peak_usage();
        }

        shuffle($elts);

        /* Base64 can have /, +, and = characters. Restrict to URL-safe
         * characters. */
        return substr(str_replace(
            array('/', '+', '='),
            array('-', '_', ''),
            base64_encode(hash('sha1', serialize($elts), true))
        ), 0, 23);
    }

    /**
     * Cooerce to string.
     *
     * @return string  The random ID.
     */
    public function __toString()
    {
        return $this->_id;
    }
}
