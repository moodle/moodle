<?php

namespace PhpXmlRpc;

use PhpXmlRpc\Helper\Charset;
use PhpXmlRpc\Helper\Logger;

/**
 * This class enables the creation of values for XML-RPC, by encapsulating plain php values.
 */
class Value implements \Countable, \IteratorAggregate, \ArrayAccess
{
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

    protected static $logger;
    protected static $charsetEncoder;

    /// @todo: do these need to be public?
    /** @var Value[]|mixed */
    public $me = array();
    /**
     * @var int $mytype
     * @internal
     */
    public $mytype = 0;
    /** @var string|null $_php_class */
    public $_php_class = null;

    public function getLogger()
    {
        if (self::$logger === null) {
            self::$logger = Logger::instance();
        }
        return self::$logger;
    }

    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    public function getCharsetEncoder()
    {
        if (self::$charsetEncoder === null) {
            self::$charsetEncoder = Charset::instance();
        }
        return self::$charsetEncoder;
    }

    public function setCharsetEncoder($charsetEncoder)
    {
        self::$charsetEncoder = $charsetEncoder;
    }

    /**
     * Build an xmlrpc value.
     *
     * When no value or type is passed in, the value is left uninitialized, and the value can be added later.
     *
     * @param Value[]|mixed $val if passing in an array, all array elements should be PhpXmlRpc\Value themselves
     * @param string $type any valid xmlrpc type name (lowercase): i4, int, boolean, string, double, dateTime.iso8601,
     *                     base64, array, struct, null.
     *                     If null, 'string' is assumed.
     *                     You should refer to http://www.xmlrpc.com/spec for more information on what each of these mean.
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
                    $this->getLogger()->errorLog("XML-RPC: " . __METHOD__ . ": not a known type ($type)");
            }
        }
    }

    /**
     * Add a single php value to an xmlrpc value.
     *
     * If the xmlrpc value is an array, the php value is added as its last element.
     * If the xmlrpc value is empty (uninitialized), this method makes it a scalar value, and sets that value.
     * Fails if the xmlrpc value is not an array and already initialized.
     *
     * @param mixed $val
     * @param string $type allowed values: i4, i8, int, boolean, string, double, dateTime.iso8601, base64, null.
     *
     * @return int 1 or 0 on failure
     */
    public function addScalar($val, $type = 'string')
    {
        $typeOf = null;
        if (isset(static::$xmlrpcTypes[$type])) {
            $typeOf = static::$xmlrpcTypes[$type];
        }

        if ($typeOf !== 1) {
            $this->getLogger()->errorLog("XML-RPC: " . __METHOD__ . ": not a scalar type ($type)");
            return 0;
        }

        // coerce booleans into correct values
        // NB: we should either do it for datetimes, integers, i8 and doubles, too,
        // or just plain remove this check, implemented on booleans only...
        if ($type == static::$xmlrpcBoolean) {
            if (strcasecmp($val, 'true') == 0 || $val == 1 || ($val == true && strcasecmp($val, 'false'))) {
                $val = true;
            } else {
                $val = false;
            }
        }

        switch ($this->mytype) {
            case 1:
                $this->getLogger()->errorLog('XML-RPC: ' . __METHOD__ . ': scalar xmlrpc value can have only one value');
                return 0;
            case 3:
                $this->getLogger()->errorLog('XML-RPC: ' . __METHOD__ . ': cannot add anonymous scalar to struct xmlrpc value');
                return 0;
            case 2:
                // we're adding a scalar value to an array here
                $this->me['array'][] = new Value($val, $type);

                return 1;
            default:
                // a scalar, so set the value and remember we're scalar
                $this->me[$type] = $val;
                $this->mytype = $typeOf;

                return 1;
        }
    }

    /**
     * Add an array of xmlrpc value objects to an xmlrpc value.
     *
     * If the xmlrpc value is an array, the elements are appended to the existing ones.
     * If the xmlrpc value is empty (uninitialized), this method makes it an array value, and sets that value.
     * Fails otherwise.
     *
     * @param Value[] $values
     *
     * @return int 1 or 0 on failure
     *
     * @todo add some checking for $values to be an array of xmlrpc values?
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
            $this->getLogger()->errorLog('XML-RPC: ' . __METHOD__ . ': already initialized as a [' . $this->kindOf() . ']');
            return 0;
        }
    }

    /**
     * Merges an array of named xmlrpc value objects into an xmlrpc value.
     *
     * If the xmlrpc value is a struct, the elements are merged with the existing ones (overwriting existing ones).
     * If the xmlrpc value is empty (uninitialized), this method makes it a struct value, and sets that value.
     * Fails otherwise.
     *
     * @param Value[] $values
     *
     * @return int 1 or 0 on failure
     *
     * @todo add some checking for $values to be an array?
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
            $this->getLogger()->errorLog('XML-RPC: ' . __METHOD__ . ': already initialized as a [' . $this->kindOf() . ']');
            return 0;
        }
    }

    /**
     * Returns a string containing either "struct", "array", "scalar" or "undef", describing the base type of the value.
     *
     * @return string
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
     * @param string $typ
     * @param Value[]|mixed $val
     * @param string $charsetEncoding
     * @return string
     */
    protected function serializedata($typ, $val, $charsetEncoding = '')
    {
        $rs = '';

        if (!isset(static::$xmlrpcTypes[$typ])) {
            return $rs;
        }

        switch (static::$xmlrpcTypes[$typ]) {
            case 1:
                switch ($typ) {
                    case static::$xmlrpcBase64:
                        $rs .= "<${typ}>" . base64_encode($val) . "</${typ}>";
                        break;
                    case static::$xmlrpcBoolean:
                        $rs .= "<${typ}>" . ($val ? '1' : '0') . "</${typ}>";
                        break;
                    case static::$xmlrpcString:
                        // Do NOT use htmlentities, since it will produce named html entities, which are invalid xml
                        $rs .= "<${typ}>" . $this->getCharsetEncoder()->encodeEntities($val, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "</${typ}>";
                        break;
                    case static::$xmlrpcInt:
                    case static::$xmlrpcI4:
                    case static::$xmlrpcI8:
                        $rs .= "<${typ}>" . (int)$val . "</${typ}>";
                        break;
                    case static::$xmlrpcDouble:
                        // avoid using standard conversion of float to string because it is locale-dependent,
                        // and also because the xmlrpc spec forbids exponential notation.
                        // sprintf('%F') could be most likely ok but it fails eg. on 2e-14.
                        // The code below tries its best at keeping max precision while avoiding exp notation,
                        // but there is of course no limit in the number of decimal places to be used...
                        $rs .= "<${typ}>" . preg_replace('/\\.?0+$/', '', number_format((double)$val, PhpXmlRpc::$xmlpc_double_precision, '.', '')) . "</${typ}>";
                        break;
                    case static::$xmlrpcDateTime:
                        if (is_string($val)) {
                            $rs .= "<${typ}>${val}</${typ}>";
                        } elseif (is_a($val, 'DateTime') || is_a($val, 'DateTimeInterface')) {
                            $rs .= "<${typ}>" . $val->format('Ymd\TH:i:s') . "</${typ}>";
                        } elseif (is_int($val)) {
                            $rs .= "<${typ}>" . date('Ymd\TH:i:s', $val) . "</${typ}>";
                        } else {
                            // not really a good idea here: but what should we output anyway? left for backward compat...
                            $rs .= "<${typ}>${val}</${typ}>";
                        }
                        break;
                    case static::$xmlrpcNull:
                        if (PhpXmlRpc::$xmlrpc_null_apache_encoding) {
                            $rs .= "<ex:nil/>";
                        } else {
                            $rs .= "<nil/>";
                        }
                        break;
                    default:
                        // no standard type value should arrive here, but provide a possibility
                        // for xmlrpc values of unknown type...
                        $rs .= "<${typ}>${val}</${typ}>";
                }
                break;
            case 3:
                // struct
                if ($this->_php_class) {
                    $rs .= '<struct php_class="' . $this->_php_class . "\">\n";
                } else {
                    $rs .= "<struct>\n";
                }
                $charsetEncoder = $this->getCharsetEncoder();
                /** @var Value $val2 */
                foreach ($val as $key2 => $val2) {
                    $rs .= '<member><name>' . $charsetEncoder->encodeEntities($key2, PhpXmlRpc::$xmlrpc_internalencoding, $charsetEncoding) . "</name>\n";
                    //$rs.=$this->serializeval($val2);
                    $rs .= $val2->serialize($charsetEncoding);
                    $rs .= "</member>\n";
                }
                $rs .= '</struct>';
                break;
            case 2:
                // array
                $rs .= "<array>\n<data>\n";
                /** @var Value $element */
                foreach ($val as $element) {
                    //$rs.=$this->serializeval($val[$i]);
                    $rs .= $element->serialize($charsetEncoding);
                }
                $rs .= "</data>\n</array>";
                break;
            default:
                break;
        }

        return $rs;
    }

    /**
     * Returns the xml representation of the value. XML prologue not included.
     *
     * @param string $charsetEncoding the charset to be used for serialization. if null, US-ASCII is assumed
     *
     * @return string
     */
    public function serialize($charsetEncoding = '')
    {
        $val = reset($this->me);
        $typ = key($this->me);

        return '<value>' . $this->serializedata($typ, $val, $charsetEncoding) . "</value>\n";
    }

    /**
     * Checks whether a struct member with a given name is present.
     *
     * Works only on xmlrpc values of type struct.
     *
     * @param string $key the name of the struct member to be looked up
     *
     * @return boolean
     *
     * @deprecated use array access, e.g. isset($val[$key])
     */
    public function structmemexists($key)
    {
        //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        return array_key_exists($key, $this->me['struct']);
    }

    /**
     * Returns the value of a given struct member (an xmlrpc value object in itself).
     * Will raise a php warning if struct member of given name does not exist.
     *
     * @param string $key the name of the struct member to be looked up
     *
     * @return Value
     *
     * @deprecated use array access, e.g. $val[$key]
     */
    public function structmem($key)
    {
        //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        return $this->me['struct'][$key];
    }

    /**
     * Reset internal pointer for xmlrpc values of type struct.
     * @deprecated iterate directly over the object using foreach instead
     */
    public function structreset()
    {
        //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        reset($this->me['struct']);
    }

    /**
     * Return next member element for xmlrpc values of type struct.
     *
     * @return Value
     * @throws \Error starting with php 8.0, this function should not be used, as it will always throw
     *
     * @deprecated iterate directly over the object using foreach instead
     */
    public function structeach()
    {
        //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        return @each($this->me['struct']);
    }

    /**
     * Returns the value of a scalar xmlrpc value (base 64 decoding is automatically handled here)
     *
     * @return mixed
     */
    public function scalarval()
    {
        $b = reset($this->me);

        return $b;
    }

    /**
     * Returns the type of the xmlrpc value.
     *
     * For integers, 'int' is always returned in place of 'i4'. 'i8' is considered a separate type and returned as such
     *
     * @return string
     */
    public function scalartyp()
    {
        reset($this->me);
        $a = key($this->me);
        if ($a == static::$xmlrpcI4) {
            $a = static::$xmlrpcInt;
        }

        return $a;
    }

    /**
     * Returns the m-th member of an xmlrpc value of array type.
     *
     * @param integer $key the index of the value to be retrieved (zero based)
     *
     * @return Value
     *
     * @deprecated use array access, e.g. $val[$key]
     */
    public function arraymem($key)
    {
        //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        return $this->me['array'][$key];
    }

    /**
     * Returns the number of members in an xmlrpc value of array type.
     *
     * @return integer
     *
     * @deprecated use count() instead
     */
    public function arraysize()
    {
        //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        return count($this->me['array']);
    }

    /**
     * Returns the number of members in an xmlrpc value of struct type.
     *
     * @return integer
     *
     * @deprecated use count() instead
     */
    public function structsize()
    {
        //trigger_error('Method ' . __METHOD__ . ' is deprecated', E_USER_DEPRECATED);

        return count($this->me['struct']);
    }

    /**
     * Returns the number of members in an xmlrpc value:
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
     *
     * @return \ArrayIterator
     * @internal required to be public to implement an Interface
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
     * @param mixed $offset
     * @param mixed $value
     * @throws \Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        switch ($this->mytype) {
            case 3:
                if (!($value instanceof \PhpXmlRpc\Value)) {
                    throw new \Exception('It is only possible to add Value objects to an XML-RPC Struct');
                }
                if (is_null($offset)) {
                    // disallow struct members with empty names
                    throw new \Exception('It is not possible to add anonymous members to an XML-RPC Struct');
                } else {
                    $this->me['struct'][$offset] = $value;
                }
                return;
            case 2:
                if (!($value instanceof \PhpXmlRpc\Value)) {
                    throw new \Exception('It is only possible to add Value objects to an XML-RPC Array');
                }
                if (is_null($offset)) {
                    $this->me['array'][] = $value;
                } else {
                    // nb: we are not checking that $offset is above the existing array range...
                    $this->me['array'][$offset] = $value;
                }
                return;
            case 1:
// todo: handle i4 vs int
                reset($this->me);
                $type = key($this->me);
                if ($type != $offset) {
                    throw new \Exception('');
                }
                $this->me[$type] = $value;
                return;
            default:
                // it would be nice to allow empty values to be be turned into non-empty ones this way, but we miss info to do so
                throw new \Exception("XML-RPC Value is of type 'undef' and its value can not be set using array index");
        }
    }

    /**
     * @internal required to be public to implement an Interface
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
// todo: handle i4 vs int
                return $offset == $this->scalartyp();
            default:
                return false;
        }
    }

    /**
     * @internal required to be public to implement an Interface
     * @param mixed $offset
     * @throws \Exception
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
                throw new \Exception("XML-RPC Value is of type 'scalar' and its value can not be unset using array index");
            default:
                throw new \Exception("XML-RPC Value is of type 'undef' and its value can not be unset using array index");
        }
    }

    /**
     * @internal required to be public to implement an Interface
     * @param mixed $offset
     * @return mixed|Value|null
     * @throws \Exception
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
// on bad type: null or exception?
                $value = reset($this->me);
                $type = key($this->me);
                return $type == $offset ? $value : null;
            default:
// return null or exception?
                throw new \Exception("XML-RPC Value is of type 'undef' and can not be accessed using array index");
        }
    }
}
