<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Exception\StateErrorException;
use PhpXmlRpc\Exception\TypeErrorException;
use PhpXmlRpc\Exception\ValueErrorException;
use PhpXmlRpc\Traits\CharsetEncoderAware;
use PhpXmlRpc\Traits\DeprecationLogger;

/**
 * This class enables the creation of values for XML-RPC, by encapsulating plain php values.
 *
 * @property Value[]|mixed $me deprecated - public access left in purely for BC. Access via scalarVal()/__construct()
 * @property int $params $mytype - public access left in purely for BC. Access via kindOf()/__construct()
 * @property string|null $_php_class deprecated - public access left in purely for BC.
 */
class Value implements \Countable, \IteratorAggregate, \ArrayAccess
{
    use CharsetEncoderAware;
    use DeprecationLogger;

    public static $xmlrpcI4 = "i4";
    public static $xmlrpcI8 = "i8";
    public static $xmlrpcInt = "int";
    public static $xmlrpcBoolean = "boolean";
    public static $xmlrpcDouble = "double";
    public static $xmlrpcString = "string";
    public static $xmlrpcDateTime = "dateTime.iso8601";
    public static $xmlrpcBase64 = "base64";
    public static $xmlrpcArray = "array";
    public static $xmlrpcStruct = "struct";
    public static $xmlrpcValue = "undefined";
    public static $xmlrpcNull = "null";

    public static $xmlrpcTypes = array(
        "i4" => 1,
        "i8" => 1,
        "int" => 1,
        "boolean" => 1,
        "double" => 1,
        "string" => 1,
        "dateTime.iso8601" => 1,
        "base64" => 1,
        "array" => 2,
        "struct" => 3,
        "null" => 1,
    );

    /** @var Value[]|mixed */
    protected $me = array();
    /**
     * @var int 0 for undef, 1 for scalar, 2 for array, 3 for struct
     */
    protected $mytype = 0;
    /** @var string|null */
    protected $_php_class = null;

    /**
     * Build an xml-rpc value.
     *
     * When no value or type is passed in, the value is left uninitialized, and the value can be added later.
     *
     * @param Value[]|mixed $val if passing in an array, all array elements should be PhpXmlRpc\Value themselves
     * @param string $type any valid xml-rpc type name (lowercase): i4, int, boolean, string, double, dateTime.iso8601,
     *                     base64, array, struct, null.
     *                     If null, 'string' is assumed.
     *                     You should refer to http://xmlrpc.com/spec.md for more information on what each of these mean.
     */
    public function __construct($val = -1, $type = '')
    {
        // optimization creep - do not call addXX, do it all inline.
        // downside: booleans will not be coerced anymore
        if ($val !== -1 || $type != '') {
            switch ($type) {
                case '':
                    $this->mytype = 1;
                    $this->me['string'] = $val;
                    break;
                case 'i4':
                case 'i8':
                case 'int':
                case 'double':
                case 'string':
                case 'boolean':
                case 'dateTime.iso8601':
                case 'base64':
                case 'null':
                    $this->mytype = 1;
                    $this->me[$type] = $val;
                    break;
                case 'array':
                    $this->mytype = 2;
                    $this->me['array'] = $val;
                    break;
                case 'struct':
                    $this->mytype = 3;
                    $this->me['struct'] = $val;
                    break;
                default:
                    $this->getLogger()->error("XML-RPC: " . __METHOD__ . ": not a known type ($type)");
            }
        }
    }

    /**
     * Add a single php value to an xml-rpc value.
     *
     * If the xml-rpc value is an array, the php value is added as its last element.
     * If the xml-rpc value is empty (uninitialized), this method makes it a scalar value, and sets that value.
     * Fails if the xml-rpc value is not an array (i.e. a struct or a scalar) and already initialized.
     *
     * @param mixed $val
     * @param string $type allowed values: i4, i8, int, boolean, string, double, dateTime.iso8601, base64, null.
     * @return int 1 or 0 on failure
     *
     * @todo arguably, as we have addArray to add elements to an Array value, and addStruct to add elements to a Struct
     *       value, we should not allow this method to add values to an Array. The 'scalar' in the method name refers to
     *       the expected state of the target object, not to the type of $val. Also, this works differently from
     *       addScalar/addStruct in that, when adding an element to an array, it wraps it into a new Value
     * @todo rename?
     */
    public function addScalar($val, $type = 'string')
    {
        $typeOf = null;
        if (isset(static::$xmlrpcTypes[$type])) {
            $typeOf = static::$xmlrpcTypes[$type];
        }

        if ($typeOf !== 1) {
            $this->getLogger()->error("XML-RPC: " . __METHOD__ . ": not a scalar type ($type)");
            return 0;
        }

        // coerce booleans into correct values
        /// @todo we should either do it for datetimes, integers, i8 and doubles, too, or just plain remove this check,
        ///       implemented on booleans only...
        if ($type == static::$xmlrpcBoolean) {
            if (strcasecmp($val, 'true') == 0 || $val == 1 || ($val == true && strcasecmp($val, 'false'))) {
                $val = true;
            } else {
                $val = false;
            }
        }

        switch ($this->mytype) {
            case 1:
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': scalar xmlrpc value can have only one value');
                return 0;
            case 3:
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': cannot add anonymous scalar to struct xmlrpc value');
                return 0;
            case 2:
                // we're adding a scalar value to an array here
                /// @todo should we try avoiding re-wrapping Value objects?
                $class = get_class($this);
                $this->me['array'][] = new $class($val, $type);

                return 1;
            default:
                // a scalar, so set the value and remember we're scalar
                $this->me[$type] = $val;
                $this->mytype = $typeOf;

                return 1;
        }
    }

    /**
     * Add an array of xml-rpc value objects to an xml-rpc value.
     *
     * If the xml-rpc value is an array, the elements are appended to the existing ones.
     * If the xml-rpc value is empty (uninitialized), this method makes it an array value, and sets that value.
     * Fails otherwise.
     *
     * @param Value[] $values
     * @return int 1 or 0 on failure
     *
     * @todo add some checking for $values to be an array of xml-rpc values?
     * @todo rename to addToArray?
     */
    public function addArray($values)
    {
        if ($this->mytype == 0) {
            $this->mytype = static::$xmlrpcTypes['array'];
            $this->me['array'] = $values;

            return 1;
        } elseif ($this->mytype == 2) {
            // we're adding to an array here
            $this->me['array'] = array_merge($this->me['array'], $values);

            return 1;
        } else {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': already initialized as a [' . $this->kindOf() . ']');
            return 0;
        }
    }

    /**
     * Merges an array of named xml-rpc value objects into an xml-rpc value.
     *
     * If the xml-rpc value is a struct, the elements are merged with the existing ones (overwriting existing ones).
     * If the xml-rpc value is empty (uninitialized), this method makes it a struct value, and sets that value.
     * Fails otherwise.
     *
     * @param Value[] $values
     * @return int 1 or 0 on failure
     *
     * @todo add some checking for $values to be an array of xml-rpc values?
     * @todo rename to addToStruct?
     */
    public function addStruct($values)
    {
        if ($this->mytype == 0) {
            $this->mytype = static::$xmlrpcTypes['struct'];
            $this->me['struct'] = $values;

            return 1;
        } elseif ($this->mytype == 3) {
            // we're adding to a struct here
            $this->me['struct'] = array_merge($this->me['struct'], $values);

            return 1;
        } else {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': already initialized as a [' . $this->kindOf() . ']');
            return 0;
        }
    }

    /**
     * Returns a string describing the base type of the value.
     *
     * @return string either "struct", "array", "scalar" or "undef"
     */
    public function kindOf()
    {
        switch ($this->mytype) {
            case 3:
                return 'struct';
            case 2:
                return 'array';
            case 1:
                return 'scalar';
            default:
                return 'undef';
        }
    }


    /**
     * Returns the value of a scalar xml-rpc value (base 64 decoding is automatically handled here)
     *
     * @return mixed
     */
    public function scalarVal()
    {
        $b = reset($this->me);

        return $b;
    }

    /**
     * Returns the type of the xml-rpc value.
     *
     * @return string For integers, 'int' is always returned in place of 'i4'. 'i8' is considered a separate type and
     *                returned as such
     */
    public function scalarTyp()
    {
        reset($this->me);
        $a = key($this->me);
        if ($a == static::$xmlrpcI4) {
            $a = static::$xmlrpcInt;
        }

        return $a;
    }

    /**
     * Returns the xml representation of the value. XML prologue not included.
     *
     * @param string $charsetEncoding the charset to be used for serialization. If null, US-ASCII is assumed
     * @return string
     */
    public function serialize($charsetEncoding = '')
    {
        $val = reset($this->me);
        $typ = key($this->me);

        return '<value>' . $this->serializeData($typ, $val, $charsetEncoding) . "</value>\n";
    }

    /**
     * @param string $typ
     * @param Value[]|mixed $val
     * @param string $charsetEncoding
     * @return string
     *
     * @deprecated this should be folded back into serialize()
     */
    protected function serializeData($typ, $val, $charsetEncoding = '')
    {
        $this->logDeprecationUnlessCalledBy('serialize');

        if (!isset(static::$xmlrpcTypes[$typ])) {
            return '';
        }

        switch (static::$xmlrpcTypes[$typ]) {
            case 1:
                switch ($typ) {
                    case static::$xmlrpcBase64:
                        $rs = "<{$typ}>" . base64_encode($val) . "</{$typ}>";
                        break;
                    case static::$xmlrpcBoolean:
                        $rs = "<{$typ}>" . ($val ? '1' : '0') . "</{$typ}>";
                        break;
                    case static::$xmlrpcString:
                        // Do NOT use htmlentities, since it will produce named html entities, which are invalid xml
                        $rs = "<{$typ}>" . $this->getCharsetEncoder()->encodeEntities($val, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "</{$typ}>";
                        break;
                    case static::$xmlrpcInt:
                    case static::$xmlrpcI4:
                    case static::$xmlrpcI8:
                        $rs = "<{$typ}>" . (int)$val . "</{$typ}>";
                        break;
                    case static::$xmlrpcDouble:
                        // avoid using standard conversion of float to string because it is locale-dependent,
                        // and also because the xml-rpc spec forbids exponential notation.
                        // sprintf('%F') could be most likely ok, but it fails e.g. on 2e-14.
                        // The code below tries its best at keeping max precision while avoiding exp notation,
                        // but there is of course no limit in the number of decimal places to be used...
                        $rs = "<{$typ}>" . preg_replace('/\\.?0+$/', '', number_format((double)$val, PhpXmlRpc::$xmlpc_double_precision, '.', '')) . "</{$typ}>";
                        break;
                    case static::$xmlrpcDateTime:
                        if (is_string($val)) {
                            $rs = "<{$typ}>{$val}</{$typ}>";
                        // DateTimeInterface is not present in php 5.4...
                        } elseif (is_a($val, 'DateTimeInterface') || is_a($val, 'DateTime')) {
                            $rs = "<{$typ}>" . $val->format('Ymd\TH:i:s') . "</{$typ}>";
                        } elseif (is_int($val)) {
                            $rs = "<{$typ}>" . date('Ymd\TH:i:s', $val) . "</{$typ}>";
                        } else {
                            // not really a good idea here: but what should we output anyway? left for backward compat...
                            $rs = "<{$typ}>{$val}</{$typ}>";
                        }
                        break;
                    case static::$xmlrpcNull:
                        if (PhpXmlRpc::$xmlrpc_null_apache_encoding) {
                            $rs = "<ex:nil/>";
                        } else {
                            $rs = "<nil/>";
                        }
                        break;
                    default:
                        // no standard type value should arrive here, but provide a possibility
                        // for xml-rpc values of unknown type...
                        $rs = "<{$typ}>{$val}</{$typ}>";
                }
                break;
            case 3:
                // struct
                if ($this->_php_class) {
                    $rs = '<struct php_class="' . $this->_php_class . "\">\n";
                } else {
                    $rs = "<struct>\n";
                }
                $charsetEncoder = $this->getCharsetEncoder();
                /** @var Value $val2 */
                foreach ($val as $key2 => $val2) {
                    $rs .= '<member><name>' . $charsetEncoder->encodeEntities($key2, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "</name>\n";
                    $rs .= $val2->serialize($charsetEncoding);
                    $rs .= "</member>\n";
                }
                $rs .= '</struct>';
                break;
            case 2:
                // array
                $rs = "<array>\n<data>\n";
                /** @var Value $element */
                foreach ($val as $element) {
                    $rs .= $element->serialize($charsetEncoding);
                }
                $rs .= "</data>\n</array>";
                break;
            default:
                /// @todo log a warning?
                $rs = '';
                break;
        }

        return $rs;
    }

    /**
     * Returns the number of members in an xml-rpc value:
     * - 0 for uninitialized values
     * - 1 for scalar values
     * - the number of elements for struct and array values
     *
     * @return integer
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        switch ($this->mytype) {
            case 3:
                return count($this->me['struct']);
            case 2:
                return count($this->me['array']);
            case 1:
                return 1;
            default:
                return 0;
        }
    }

    /**
     * Implements the IteratorAggregate interface
     * @internal required to be public to implement an Interface
     *
     * @return \ArrayIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        switch ($this->mytype) {
            case 3:
                return new \ArrayIterator($this->me['struct']);
            case 2:
                return new \ArrayIterator($this->me['array']);
            case 1:
                return new \ArrayIterator($this->me);
            default:
                return new \ArrayIterator();
        }
    }

    /**
     * @internal required to be public to implement an Interface
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws ValueErrorException|TypeErrorException
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        switch ($this->mytype) {
            case 3:
                if (!($value instanceof Value)) {
                    throw new TypeErrorException('It is only possible to add Value objects to an XML-RPC Struct');
                }
                if (is_null($offset)) {
                    // disallow struct members with empty names
                    throw new ValueErrorException('It is not possible to add anonymous members to an XML-RPC Struct');
                } else {
                    $this->me['struct'][$offset] = $value;
                }
                return;
            case 2:
                if (!($value instanceof Value)) {
                    throw new TypeErrorException('It is only possible to add Value objects to an XML-RPC Array');
                }
                if (is_null($offset)) {
                    $this->me['array'][] = $value;
                } else {
                    // nb: we are not checking that $offset is above the existing array range...
                    $this->me['array'][$offset] = $value;
                }
                return;
            case 1:
                /// @todo: should we handle usage of i4 to retrieve int (in both set/unset/isset)? After all we consider
                ///        'int' to be the preferred form, as evidenced in scalarTyp()
                reset($this->me);
                $type = key($this->me);
                if ($type != $offset && ($type != 'i4' || $offset != 'int')) {
                    throw new ValueErrorException('...');
                }
                $this->me[$type] = $value;
                return;
            default:
                // it would be nice to allow empty values to be turned into non-empty ones this way, but we miss info to do so
                throw new ValueErrorException("XML-RPC Value is of type 'undef' and its value can not be set using array index");
        }
    }

    /**
     * @internal required to be public to implement an Interface
     *
     * @param mixed $offset
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        switch ($this->mytype) {
            case 3:
                return isset($this->me['struct'][$offset]);
            case 2:
                return isset($this->me['array'][$offset]);
            case 1:
                // handle i4 vs int
                if ($offset == 'i4') {
                    // to be consistent with set and unset, we disallow usage of i4 to check for int
                    reset($this->me);
                    return $offset == key($this->me);
                } else {
                    return $offset == $this->scalarTyp();
                }
            default:
                return false;
        }
    }

    /**
     * @internal required to be public to implement an Interface
     *
     * @param mixed $offset
     * @return void
     * @throws ValueErrorException|StateErrorException
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        switch ($this->mytype) {
            case 3:
                unset($this->me['struct'][$offset]);
                return;
            case 2:
                unset($this->me['array'][$offset]);
                return;
            case 1:
                // can not remove value from a scalar
                /// @todo feature creep - allow this to move back the value to 'undef' state?
                throw new StateErrorException("XML-RPC Value is of type 'scalar' and its value can not be unset using array index");
            default:
                throw new StateErrorException("XML-RPC Value is of type 'undef' and its value can not be unset using array index");
        }
    }

    /**
     * @internal required to be public to implement an Interface
     *
     * @param mixed $offset
     * @return mixed|Value|null
     * @throws StateErrorException
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        switch ($this->mytype) {
            case 3:
                return isset($this->me['struct'][$offset]) ? $this->me['struct'][$offset] : null;
            case 2:
                return isset($this->me['array'][$offset]) ? $this->me['array'][$offset] : null;
            case 1:
                /// @todo what to return on bad type: null or exception?
                $value = reset($this->me);
                $type = key($this->me);
                return $type == $offset ? $value : (($type == 'i4' && $offset == 'int') ? $value : null);
            default:
                // return null or exception?
                throw new StateErrorException("XML-RPC Value is of type 'undef' and can not be accessed using array index");
        }
    }

    // *** BC layer ***

    /**
     * Checks whether a struct member with a given name is present.
     *
     * Works only on xml-rpc values of type struct.
     *
     * @param string $key the name of the struct member to be looked up
     * @return boolean
     *
     * @deprecated use array access, e.g. isset($val[$key])
     */
    public function structMemExists($key)
    {
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        return array_key_exists($key, $this->me['struct']);
    }

    /**
     * Returns the value of a given struct member (an xml-rpc value object in itself).
     * Will raise a php warning if struct member of given name does not exist.
     *
     * @param string $key the name of the struct member to be looked up
     * @return Value
     *
     * @deprecated use array access, e.g. $val[$key]
     */
    public function structMem($key)
    {
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        return $this->me['struct'][$key];
    }

    /**
     * Reset internal pointer for xml-rpc values of type struct.
     * @return void
     *
     * @deprecated iterate directly over the object using foreach instead
     */
    public function structReset()
    {
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        reset($this->me['struct']);
    }

    /**
     * Return next member element for xml-rpc values of type struct.
     *
     * @return array having the same format as PHP's `each` method
     *
     * @deprecated iterate directly over the object using foreach instead
     */
    public function structEach()
    {
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        $key = key($this->me['struct']);
        $value = current($this->me['struct']);
        next($this->me['struct']);
        return array(1 => $value, 'value' => $value, 0 => $key, 'key' => $key);
    }

    /**
     * Returns the n-th member of an xml-rpc value of array type.
     *
     * @param integer $key the index of the value to be retrieved (zero based)
     *
     * @return Value
     *
     * @deprecated use array access, e.g. $val[$key]
     */
    public function arrayMem($key)
    {
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        return $this->me['array'][$key];
    }

    /**
     * Returns the number of members in an xml-rpc value of array type.
     *
     * @return integer
     *
     * @deprecated use count() instead
     */
    public function arraySize()
    {
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        return count($this->me['array']);
    }

    /**
     * Returns the number of members in an xml-rpc value of struct type.
     *
     * @return integer
     *
     * @deprecated use count() instead
     */
    public function structSize()
    {
        $this->logDeprecation('Method ' . __METHOD__ . ' is deprecated');

        return count($this->me['struct']);
    }

    // we have to make this return by ref in order to allow calls such as `$resp->_cookies['name'] = ['value' => 'something'];`
    public function &__get($name)
    {
        switch ($name) {
            case 'me':
            case 'mytype':
            case '_php_class':
                $this->logDeprecation('Getting property Value::' . $name . ' is deprecated');
                return $this->$name;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
                $result = null;
                return $result;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'me':
            case 'mytype':
            case '_php_class':
                $this->logDeprecation('Setting property Value::' . $name . ' is deprecated');
                $this->$name = $value;
                break;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __set(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }

    public function __isset($name)
    {
        switch ($name) {
            case 'me':
            case 'mytype':
            case '_php_class':
                $this->logDeprecation('Checking property Value::' . $name . ' is deprecated');
                return isset($this->$name);
            default:
                return false;
        }
    }

    public function __unset($name)
    {
        switch ($name) {
            case 'me':
            case 'mytype':
            case '_php_class':
                $this->logDeprecation('Unsetting property Value::' . $name . ' is deprecated');
                unset($this->$name);
                break;
            default:
                /// @todo throw instead? There are very few other places where the lib trigger errors which can potentially reach stdout...
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
                trigger_error('Undefined property via __unset(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_WARNING);
        }
    }
}
