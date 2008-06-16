<?php

/**
 * Parses string representations into their corresponding native PHP
 * variable type. The base implementation does a simple type-check.
 */
class HTMLPurifier_VarParser
{
    
    /**
     * Lookup table of allowed types.
     */
    static public $types = array(
        'string'    => true,
        'istring'   => true,
        'text'      => true,
        'itext'     => true,
        'int'       => true,
        'float'     => true,
        'bool'      => true,
        'lookup'    => true,
        'list'      => true,
        'hash'      => true,
        'mixed'     => true
    );
    
    /**
     * Lookup table of types that are string, and can have aliases or
     * allowed value lists.
     */
    static public $stringTypes = array(
        'string'    => true,
        'istring'   => true,
        'text'      => true,
        'itext'     => true,
    );
    
    /**
     * Validate a variable according to type. Throws
     * HTMLPurifier_VarParserException if invalid.
     * It may return NULL as a valid type if $allow_null is true.
     *
     * @param $var Variable to validate
     * @param $type Type of variable, see HTMLPurifier_VarParser->types
     * @param $allow_null Whether or not to permit null as a value
     * @return Validated and type-coerced variable
     */
    final public function parse($var, $type, $allow_null = false) {
        if (!isset(HTMLPurifier_VarParser::$types[$type])) {
            throw new HTMLPurifier_VarParserException("Invalid type '$type'");
        }
        $var = $this->parseImplementation($var, $type, $allow_null);
        if ($allow_null && $var === null) return null;
        // These are basic checks, to make sure nothing horribly wrong
        // happened in our implementations.
        switch ($type) {
            case 'string':
            case 'istring':
            case 'text':
            case 'itext':
                if (!is_string($var)) break;
                if ($type[0] == 'i') $var = strtolower($var);
                return $var;
            case 'int':
                if (!is_int($var)) break;
                return $var;
            case 'float':
                if (!is_float($var)) break;
                return $var;
            case 'bool':
                if (!is_bool($var)) break;
                return $var;
            case 'lookup':
            case 'list':
            case 'hash':
                if (!is_array($var)) break;
                if ($type === 'lookup') {
                    foreach ($var as $k) if ($k !== true) $this->error('Lookup table contains value other than true');
                } elseif ($type === 'list') {
                    $keys = array_keys($var);
                    if (array_keys($keys) !== $keys) $this->error('Indices for list are not uniform');
                }
                return $var;
            case 'mixed':
                return $var;
            default:
                $this->errorInconsistent(get_class($this), $type);
        }
        $this->errorGeneric($var, $type);
    }
    
    /**
     * Actually implements the parsing. Base implementation is to not
     * do anything to $var. Subclasses should overload this!
     */
    protected function parseImplementation($var, $type, $allow_null) {
        return $var;
    }
    
    /**
     * Throws an exception.
     */
    protected function error($msg) {
        throw new HTMLPurifier_VarParserException($msg);
    }
    
    /**
     * Throws an inconsistency exception.
     * @note This should not ever be called. It would be called if we
     *       extend the allowed values of HTMLPurifier_VarParser without
     *       updating subclasses.
     */
    protected function errorInconsistent($class, $type) {
        throw new HTMLPurifier_Exception("Inconsistency in $class: $type not implemented");
    }
    
    /**
     * Generic error for if a type didn't work.
     */
    protected function errorGeneric($var, $type) {
        $vtype = gettype($var);
        $this->error("Expected type $type, got $vtype");
    }
    
}
