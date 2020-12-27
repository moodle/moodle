<?php
/**
 * Copyright 2009-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2009-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Util
 */

/**
 * An OO-way to access form variables.
 *
 * @todo $_expected and $_vars are used inconsistently. $_expected is used in
 * exists(), but not in getExists(). And both are expected to be of the same
 * format, while Horde_Form submits $_expected as a flat list and $_vars as a
 * multi-dimensional array, if the the form elements are or array type (like
 * object[] in Turba).
 * @todo The sanitized feature doesn't seem to be used anywhere at all.
 *
 * @author    Robert E. Coyle <robertecoyle@hotmail.com>
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2009-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Util
 */
class Horde_Variables implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The list of expected variables.
     *
     * @var array
     */
    protected $_expected = array();

    /**
     * Has the input been sanitized?
     *
     * @var boolean
     */
    protected $_sanitized = false;

    /**
     * Array of form variables.
     *
     * @var array
     */
    protected $_vars;

    /**
     * Returns a Horde_Variables object populated with the form input.
     *
     * @param string $sanitize  Sanitize the input variables?
     *
     * @return Horde_Variables  Variables object.
     */
    public static function getDefaultVariables($sanitize = false)
    {
        return new self(null, $sanitize);
    }

    /**
     * Constructor.
     *
     * @param array $vars       The list of form variables (if null, defaults
     *                          to PHP's $_REQUEST value). If '_formvars'
     *                          exists, it must be a JSON encoded array that
     *                          contains the list of allowed form variables.
     * @param string $sanitize  Sanitize the input variables?
     */
    public function __construct($vars = array(), $sanitize = false)
    {
        if (is_null($vars)) {
            $request_copy = $_REQUEST;
            $vars = Horde_Util::dispelMagicQuotes($request_copy);
        }

        if (isset($vars['_formvars'])) {
            $this->_expected = @json_decode($vars['_formvars'], true);
            unset($vars['_formvars']);
        }

        $this->_vars = $vars;

        if ($sanitize) {
            $this->sanitize();
        }
    }

    /**
     * Sanitize the form input.
     */
    public function sanitize()
    {
        if (!$this->_sanitized) {
            foreach (array_keys($this->_vars) as $key) {
                $this->$key = $this->filter($key);
            }
            $this->_sanitized = true;
        }
    }

    /**
     * Alias of isset().
     *
     * @see __isset()
     */
    public function exists($varname)
    {
        return $this->__isset($varname);
    }

    /**
     * isset() implementation.
     *
     * @param string $varname  The form variable name.
     *
     * @return boolean  Does $varname form variable exist?
     */
    public function __isset($varname)
    {
        return count($this->_expected)
            ? $this->_getExists($this->_expected, $varname, $value)
            : $this->_getExists($this->_vars, $varname, $value);
    }

    /**
     * Implements isset() for ArrayAccess interface.
     *
     * @see __isset()
     */
    public function offsetExists($field)
    {
        return $this->__isset($field);
    }

    /**
     * Returns the value of a given form variable.
     *
     * @param string $varname  The form variable name.
     * @param string $default  The default form variable value.
     *
     * @return mixed  The form variable, or $default if it doesn't exist.
     */
    public function get($varname, $default = null)
    {
        return $this->_getExists($this->_vars, $varname, $value)
            ? $value
            : $default;
    }

    /**
     * Returns the value of a given form variable.
     *
     * @param string $varname  The form variable name.
     *
     * @return mixed  The form variable, or null if it doesn't exist.
     */
    public function __get($varname)
    {
        $this->_getExists($this->_vars, $varname, $value);
        return $value;
    }

    /**
     * Implements getter for ArrayAccess interface.
     *
     * @see __get()
     */
    public function offsetGet($field)
    {
        return $this->__get($field);
    }

    /**
     * Given a variable name, returns the value and sets a variable indicating
     * whether the value exists in the form data.
     *
     * @param string $varname   The form variable name.
     * @param boolean &$exists  Reference to variable that will indicate
     *                          whether $varname existed in form data.
     *
     * @return mixed  The form variable, or null if it doesn't exist.
     */
    public function getExists($varname, &$exists)
    {
        $exists = $this->_getExists($this->_vars, $varname, $value);
        return $value;
    }

    /**
     * Sets the value of a given form variable.
     *
     * @see __set()
     */
    public function set($varname, $value)
    {
        $this->$varname = $value;
    }

    /**
     * Sets the value of a given form variable.
     *
     * @param string $varname  The form variable name.
     * @param mixed $value     The value to set.
     */
    public function __set($varname, $value)
    {
        $keys = array();

        if (Horde_Array::getArrayParts($varname, $base, $keys)) {
            array_unshift($keys, $base);
            $place = &$this->_vars;
            $i = count($keys);

            while ($i--) {
                $key = array_shift($keys);
                if (!isset($place[$key])) {
                    $place[$key] = array();
                }
                $place = &$place[$key];
            }

            $place = $value;
        } else {
            $this->_vars[$varname] = $value;
        }
    }

    /**
     * Implements setter for ArrayAccess interface.
     *
     * @see __set()
     */
    public function offsetSet($field, $value)
    {
        $this->__set($field, $value);
    }

    /**
     * Deletes a given form variable.
     *
     * @see __unset()
     */
    public function remove($varname)
    {
        unset($this->$varname);
    }

    /**
     * Deletes a given form variable.
     *
     * @param string $varname  The form variable name.
     */
    public function __unset($varname)
    {
        Horde_Array::getArrayParts($varname, $base, $keys);

        if (is_null($base)) {
            unset($this->_vars[$varname]);
        } else {
            $ptr = &$this->_vars[$base];
            $end = count($keys) - 1;
            foreach ($keys as $key => $val) {
                if (!isset($ptr[$val])) {
                    break;
                }
                if ($end == $key) {
                    array_splice($ptr, array_search($val, array_keys($ptr)), 1);
                } else {
                    $ptr = &$ptr[$val];
                }
            }
        }
    }

    /**
     * Implements unset() for ArrayAccess interface.
     *
     * @see __unset()
     */
    public function offsetUnset($field)
    {
        $this->__unset($field);
    }

    /**
     * Merges a list of variables into the current form variable list.
     *
     * @param array $vars  Form variables.
     */
    public function merge($vars)
    {
        foreach ($vars as $varname => $value) {
            $this->$varname = $value;
        }
    }

    /**
     * Set $varname to $value ONLY if it's not already present.
     *
     * @param string $varname  The form variable name.
     * @param mixed $value     The value to set.
     *
     * @return boolean  True if the value was altered.
     */
    public function add($varname, $value)
    {
        if ($this->exists($varname)) {
            return false;
        }

        $this->_vars[$varname] = $value;
        return true;
    }

    /**
     * Filters a form value so that it can be used in HTML output.
     *
     * @param string $varname  The form variable name.
     *
     * @return mixed  The filtered variable, or null if it doesn't exist.
     */
    public function filter($varname)
    {
        $val = $this->$varname;

        if (is_null($val) || $this->_sanitized) {
            return $val;
        }

        return is_array($val)
            ? filter_var_array($val, FILTER_SANITIZE_STRING)
            : filter_var($val, FILTER_SANITIZE_STRING);
    }

    /* Protected methods. */

    /**
     * Fetch the requested variable ($varname) into $value, and return
     * whether or not the variable was set in $array.
     *
     * @param array $array     The array to search in (usually either
     *                         $this->_vars or $this->_expected).
     * @param string $varname  The name of the variable to look for.
     * @param mixed &$value    $varname's value gets assigned to this variable.
     *
     * @return boolean  Whether or not the variable was set (or, if we've
     *                  checked $this->_expected, should have been set).
     */
    protected function _getExists($array, $varname, &$value)
    {
        if (Horde_Array::getArrayParts($varname, $base, $keys)) {
            if (!isset($array[$base])) {
                $value = null;
                return false;
            }

            $searchspace = &$array[$base];
            $i = count($keys);

            while ($i--) {
                $key = array_shift($keys);
                if (!isset($searchspace[$key])) {
                    $value = null;
                    return false;
                }
                $searchspace = &$searchspace[$key];
            }
            $value = $searchspace;

            return true;
        }

        $value = isset($array[$varname])
            ? $array[$varname]
            : null;

        return !is_null($value);
    }

    /* Countable methods. */

    /**
     */
    public function count()
    {
        return count($this->_vars);
    }

    /* IteratorAggregate method. */

    public function getIterator()
    {
        return new ArrayIterator($this->_vars);
    }

}
