<?php
/**
 * @author Gaetano Giunta
 * @copyright (C) 2006-2023 G. Giunta
 * @license code licensed under the BSD License: see file license.txt
 */

namespace PhpXmlRpc;

use PhpXmlRpc\Exception\ValueErrorException;
use PhpXmlRpc\Traits\LoggerAware;

/**
 * PHPXMLRPC "wrapper" class - generate stubs to transparently access xml-rpc methods as php functions and vice-versa.
 * Note: this class implements the PROXY pattern, but it is not named so to avoid confusion with http proxies.
 *
 * @todo use some better templating system for code generation?
 * @todo implement method wrapping with preservation of php objs in calls
 * @todo add support for 'epivals' mode
 * @todo allow setting custom namespace for generated wrapping code
 */
class Wrapper
{
    use LoggerAware;

    /**
     * @var object[]
     * Used to hold a reference to object instances whose methods get wrapped by wrapPhpFunction(), in 'create source' mode
     * @internal this property will become protected in the future
     */
    public static $objHolder = array();

    /** @var string */
    protected static $namespace = '\\PhpXmlRpc\\';

    /**
     * Given a string defining a php type or phpxmlrpc type (loosely defined: strings
     * accepted come from javadoc blocks), return corresponding phpxmlrpc type.
     * Notes:
     * - for php 'resource' types returns empty string, since resources cannot be serialized;
     * - for php class names returns 'struct', since php objects can be serialized as xml-rpc structs
     * - for php arrays always return array, even though arrays sometimes serialize as structs...
     * - for 'void' and 'null' returns 'undefined'
     *
     * @param string $phpType
     * @return string
     *
     * @todo support notation `something[]` as 'array'
     * @todo check if nil support is enabled when finding null
     */
    public function php2XmlrpcType($phpType)
    {
        switch (strtolower($phpType)) {
            case 'string':
                return Value::$xmlrpcString;
            case 'integer':
            case Value::$xmlrpcInt: // 'int'
            case Value::$xmlrpcI4:
            case Value::$xmlrpcI8:
                return Value::$xmlrpcInt;
            case Value::$xmlrpcDouble: // 'double'
                return Value::$xmlrpcDouble;
            case 'bool':
            case Value::$xmlrpcBoolean: // 'boolean'
            case 'false':
            case 'true':
                return Value::$xmlrpcBoolean;
            case Value::$xmlrpcArray: // 'array':
            case 'array[]';
                return Value::$xmlrpcArray;
            case 'object':
            case Value::$xmlrpcStruct: // 'struct'
                return Value::$xmlrpcStruct;
            case Value::$xmlrpcBase64:
                return Value::$xmlrpcBase64;
            case 'resource':
                return '';
            default:
                if (class_exists($phpType)) {
                    // DateTimeInterface is not present in php 5.4...
                    if (is_a($phpType, 'DateTimeInterface') || is_a($phpType, 'DateTime')) {
                        return Value::$xmlrpcDateTime;
                    }
                    return Value::$xmlrpcStruct;
                } else {
                    // unknown: might be any 'extended' xml-rpc type
                    return Value::$xmlrpcValue;
                }
        }
    }

    /**
     * Given a string defining a phpxmlrpc type return the corresponding php type.
     *
     * @param string $xmlrpcType
     * @return string
     */
    public function xmlrpc2PhpType($xmlrpcType)
    {
        switch (strtolower($xmlrpcType)) {
            case 'base64':
            case 'datetime.iso8601':
            case 'string':
                return Value::$xmlrpcString;
            case 'int':
            case 'i4':
            case 'i8':
                return 'integer';
            case 'struct':
            case 'array':
                return 'array';
            case 'double':
                return 'float';
            case 'undefined':
                return 'mixed';
            case 'boolean':
            case 'null':
            default:
                // unknown: might be any xml-rpc type
                return strtolower($xmlrpcType);
        }
    }

    /**
     * Given a user-defined PHP function, create a PHP 'wrapper' function that can be exposed as xml-rpc method from an
     * xml-rpc server object and called from remote clients (as well as its corresponding signature info).
     *
     * Since php is a typeless language, to infer types of input and output parameters, it relies on parsing the
     * javadoc-style comment block associated with the given function. Usage of xml-rpc native types (such as
     * datetime.dateTime.iso8601 and base64) in the '@param' tag is also allowed, if you need the php function to
     * receive/send data in that particular format (note that base64 encoding/decoding is transparently carried out by
     * the lib, while datetime values are passed around as strings)
     *
     * Known limitations:
     * - only works for user-defined functions, not for PHP internal functions (reflection does not support retrieving
     *   number/type of params for those)
     * - functions returning php objects will generate special structs in xml-rpc responses: when the xml-rpc decoding of
     *   those responses is carried out by this same lib, using the appropriate param in php_xmlrpc_decode, the php
     *   objects will be rebuilt.
     *   In short: php objects can be serialized, too (except for their resource members), using this function.
     *   Other libs might choke on the very same xml that will be generated in this case (i.e. it has a nonstandard
     *   attribute on struct element tags)
     *
     * Note that since rel. 2.0RC3 the preferred method to have the server call 'standard' php functions (i.e. functions
     * not expecting a single Request obj as parameter) is by making use of the $functions_parameters_type and
     * $exception_handling properties.
     *
     * @param \Callable $callable the PHP user function to be exposed as xml-rpc method: a closure, function name, array($obj, 'methodname') or array('class', 'methodname') are ok
     * @param string $newFuncName (optional) name for function to be created. Used only when return_source in $extraOptions is true
     * @param array $extraOptions (optional) array of options for conversion. valid values include:
     *                            - bool return_source     when true, php code w. function definition will be returned, instead of a closure
     *                            - bool encode_nulls      let php objects be sent to server using <nil> elements instead of empty strings
     *                            - bool encode_php_objs   let php objects be sent to server using the 'improved' xml-rpc notation, so server can deserialize them as php objects
     *                            - bool decode_php_objs   --- WARNING !!! possible security hazard. only use it with trusted servers ---
     *                            - bool suppress_warnings remove from produced xml any warnings generated at runtime by the php function being invoked
     * @return array|false false on error, or an array containing the name of the new php function,
     *                     its signature and docs, to be used in the server dispatch map
     *
     * @todo decide how to deal with params passed by ref in function definition: bomb out or allow?
     * @todo finish using phpdoc info to build method sig if all params are named but out of order
     * @todo add a check for params of 'resource' type
     * @todo add some error logging when returning false?
     * @todo what to do when the PHP function returns NULL? We are currently returning an empty string value...
     * @todo add an option to suppress php warnings in invocation of user function, similar to server debug level 3?
     * @todo add a verbatim_object_copy parameter to allow avoiding usage the same obj instance?
     * @todo add an option to allow generated function to skip validation of number of parameters, as that is done by the server anyway
     */
    public function wrapPhpFunction($callable, $newFuncName = '', $extraOptions = array())
    {
        $buildIt = isset($extraOptions['return_source']) ? !($extraOptions['return_source']) : true;

        if (is_string($callable) && strpos($callable, '::') !== false) {
            $callable = explode('::', $callable);
        }
        if (is_array($callable)) {
            if (count($callable) < 2 || (!is_string($callable[0]) && !is_object($callable[0]))) {
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': syntax for function to be wrapped is wrong');
                return false;
            }
            if (is_string($callable[0])) {
                $plainFuncName = implode('::', $callable);
            } elseif (is_object($callable[0])) {
                $plainFuncName = get_class($callable[0]) . '->' . $callable[1];
            }
            $exists = method_exists($callable[0], $callable[1]);
        } else if ($callable instanceof \Closure) {
            // we do not support creating code which wraps closures, as php does not allow to serialize them
            if (!$buildIt) {
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': a closure can not be wrapped in generated source code');
                return false;
            }

            $plainFuncName = 'Closure';
            $exists = true;
        } else {
            $plainFuncName = $callable;
            $exists = function_exists($callable);
        }

        if (!$exists) {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': function to be wrapped is not defined: ' . $plainFuncName);
            return false;
        }

        $funcDesc = $this->introspectFunction($callable, $plainFuncName);
        if (!$funcDesc) {
            return false;
        }

        $funcSigs = $this->buildMethodSignatures($funcDesc);

        if ($buildIt) {
            $callable = $this->buildWrapFunctionClosure($callable, $extraOptions, $plainFuncName, $funcDesc);
        } else {
            $newFuncName = $this->newFunctionName($callable, $newFuncName, $extraOptions);
            $code = $this->buildWrapFunctionSource($callable, $newFuncName, $extraOptions, $plainFuncName, $funcDesc);
        }

        $ret = array(
            'function' => $callable,
            'signature' => $funcSigs['sigs'],
            'docstring' => $funcDesc['desc'],
            'signature_docs' => $funcSigs['sigsDocs'],
        );
        if (!$buildIt) {
            $ret['function'] = $newFuncName;
            $ret['source'] = $code;
        }
        return $ret;
    }

    /**
     * Introspect a php callable and its phpdoc block and extract information about its signature
     *
     * @param callable $callable
     * @param string $plainFuncName
     * @return array|false
     */
    protected function introspectFunction($callable, $plainFuncName)
    {
        // start to introspect PHP code
        if (is_array($callable)) {
            $func = new \ReflectionMethod($callable[0], $callable[1]);
            if ($func->isPrivate()) {
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': method to be wrapped is private: ' . $plainFuncName);
                return false;
            }
            if ($func->isProtected()) {
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': method to be wrapped is protected: ' . $plainFuncName);
                return false;
            }
            if ($func->isConstructor()) {
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': method to be wrapped is the constructor: ' . $plainFuncName);
                return false;
            }
            if ($func->isDestructor()) {
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': method to be wrapped is the destructor: ' . $plainFuncName);
                return false;
            }
            if ($func->isAbstract()) {
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': method to be wrapped is abstract: ' . $plainFuncName);
                return false;
            }
            /// @todo add more checks for static vs. nonstatic?
        } else {
            $func = new \ReflectionFunction($callable);
        }
        if ($func->isInternal()) {
            /// @todo from PHP 5.1.0 onward, we should be able to use invokeargs instead of getparameters to fully
            ///       reflect internal php functions
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': function to be wrapped is internal: ' . $plainFuncName);
            return false;
        }

        // retrieve parameter names, types and description from javadoc comments

        // function description
        $desc = '';
        // type of return val: by default 'any'
        $returns = Value::$xmlrpcValue;
        // desc of return val
        $returnsDocs = '';
        // type + name of function parameters
        $paramDocs = array();

        $docs = $func->getDocComment();
        if ($docs != '') {
            $docs = explode("\n", $docs);
            $i = 0;
            foreach ($docs as $doc) {
                $doc = trim($doc, " \r\t/*");
                if (strlen($doc) && strpos($doc, '@') !== 0 && !$i) {
                    if ($desc) {
                        $desc .= "\n";
                    }
                    $desc .= $doc;
                } elseif (strpos($doc, '@param') === 0) {
                    // syntax: @param type $name [desc]
                    if (preg_match('/@param\s+(\S+)\s+(\$\S+)\s*(.+)?/', $doc, $matches)) {
                        $name = strtolower(trim($matches[2]));
                        //$paramDocs[$name]['name'] = trim($matches[2]);
                        $paramDocs[$name]['doc'] = isset($matches[3]) ? $matches[3] : '';
                        $paramDocs[$name]['type'] = $matches[1];
                    }
                    $i++;
                } elseif (strpos($doc, '@return') === 0) {
                    // syntax: @return type [desc]
                    if (preg_match('/@return\s+(\S+)(\s+.+)?/', $doc, $matches)) {
                        $returns = $matches[1];
                        if (isset($matches[2])) {
                            $returnsDocs = trim($matches[2]);
                        }
                    }
                }
            }
        }

        // execute introspection of actual function prototype
        $params = array();
        $i = 0;
        foreach ($func->getParameters() as $paramObj) {
            $params[$i] = array();
            $params[$i]['name'] = '$' . $paramObj->getName();
            $params[$i]['isoptional'] = $paramObj->isOptional();
            $i++;
        }

        return array(
            'desc' => $desc,
            'docs' => $docs,
            'params' => $params, // array, positionally indexed
            'paramDocs' => $paramDocs, // array, indexed by name
            'returns' => $returns,
            'returnsDocs' =>$returnsDocs,
        );
    }

    /**
     * Given the method description given by introspection, create method signature data
     *
     * @param array $funcDesc as generated by self::introspectFunction()
     * @return array
     *
     * @todo support better docs with multiple types separated by pipes by creating multiple signatures
     *       (this is questionable, as it might produce a big matrix of possible signatures with many such occurrences)
     */
    protected function buildMethodSignatures($funcDesc)
    {
        $i = 0;
        $parsVariations = array();
        $pars = array();
        $pNum = count($funcDesc['params']);
        foreach ($funcDesc['params'] as $param) {
            /* // match by name real param and documented params
            $name = strtolower($param['name']);
            if (!isset($funcDesc['paramDocs'][$name])) {
                $funcDesc['paramDocs'][$name] = array();
            }
            if (!isset($funcDesc['paramDocs'][$name]['type'])) {
                $funcDesc['paramDocs'][$name]['type'] = 'mixed';
            }*/

            if ($param['isoptional']) {
                // this particular parameter is optional. save as valid previous list of parameters
                $parsVariations[] = $pars;
            }

            $pars[] = "\$p$i";
            $i++;
            if ($i == $pNum) {
                // last allowed parameters combination
                $parsVariations[] = $pars;
            }
        }

        if (count($parsVariations) == 0) {
            // only known good synopsis = no parameters
            $parsVariations[] = array();
        }

        $sigs = array();
        $sigsDocs = array();
        foreach ($parsVariations as $pars) {
            // build a signature
            $sig = array($this->php2XmlrpcType($funcDesc['returns']));
            $pSig = array($funcDesc['returnsDocs']);
            for ($i = 0; $i < count($pars); $i++) {
                $name = strtolower($funcDesc['params'][$i]['name']);
                if (isset($funcDesc['paramDocs'][$name]['type'])) {
                    $sig[] = $this->php2XmlrpcType($funcDesc['paramDocs'][$name]['type']);
                } else {
                    $sig[] = Value::$xmlrpcValue;
                }
                $pSig[] = isset($funcDesc['paramDocs'][$name]['doc']) ? $funcDesc['paramDocs'][$name]['doc'] : '';
            }
            $sigs[] = $sig;
            $sigsDocs[] = $pSig;
        }

        return array(
            'sigs' => $sigs,
            'sigsDocs' => $sigsDocs
        );
    }

    /**
     * Creates a closure that will execute $callable
     *
     * @param $callable
     * @param array $extraOptions
     * @param string $plainFuncName
     * @param array $funcDesc
     * @return \Closure
     *
     * @todo validate params? In theory all validation is left to the dispatch map...
     * @todo add support for $catchWarnings
     */
    protected function buildWrapFunctionClosure($callable, $extraOptions, $plainFuncName, $funcDesc)
    {
        /**
         * @param Request $req
         *
         * @return mixed
         */
        $function = function($req) use($callable, $extraOptions, $funcDesc)
        {
            $encoderClass = static::$namespace.'Encoder';
            $responseClass = static::$namespace.'Response';
            $valueClass = static::$namespace.'Value';

            // validate number of parameters received
            // this should be optional really, as we assume the server does the validation
            $minPars = count($funcDesc['params']);
            $maxPars = $minPars;
            foreach ($funcDesc['params'] as $i => $param) {
                if ($param['isoptional']) {
                    // this particular parameter is optional. We assume later ones are as well
                    $minPars = $i;
                    break;
                }
            }
            $numPars = $req->getNumParams();
            if ($numPars < $minPars || $numPars > $maxPars) {
                return new $responseClass(0, 3, 'Incorrect parameters passed to method');
            }

            $encoder = new $encoderClass();
            $options = array();
            if (isset($extraOptions['decode_php_objs']) && $extraOptions['decode_php_objs']) {
                $options[] = 'decode_php_objs';
            }
            $params = $encoder->decode($req, $options);

            $result = call_user_func_array($callable, $params);

            if (! is_a($result, $responseClass)) {
                // q: why not do the same for int, float, bool, string?
                if ($funcDesc['returns'] == Value::$xmlrpcDateTime || $funcDesc['returns'] == Value::$xmlrpcBase64) {
                    $result = new $valueClass($result, $funcDesc['returns']);
                } else {
                    $options = array();
                    if (isset($extraOptions['encode_php_objs']) && $extraOptions['encode_php_objs']) {
                        $options[] = 'encode_php_objs';
                    }
                    if (isset($extraOptions['encode_nulls']) && $extraOptions['encode_nulls']) {
                        $options[] = 'null_extension';
                    }

                    $result = $encoder->encode($result, $options);
                }
                $result = new $responseClass($result);
            }

            return $result;
        };

        return $function;
    }

    /**
     * Return a name for a new function, based on $callable, insuring its uniqueness
     * @param mixed $callable a php callable, or the name of an xml-rpc method
     * @param string $newFuncName when not empty, it is used instead of the calculated version
     * @return string
     */
    protected function newFunctionName($callable, $newFuncName, $extraOptions)
    {
        // determine name of new php function

        $prefix = isset($extraOptions['prefix']) ? $extraOptions['prefix'] : 'xmlrpc';

        if ($newFuncName == '') {
            if (is_array($callable)) {
                if (is_string($callable[0])) {
                    $xmlrpcFuncName = "{$prefix}_" . implode('_', $callable);
                } else {
                    $xmlrpcFuncName = "{$prefix}_" . get_class($callable[0]) . '_' . $callable[1];
                }
            } else {
                if ($callable instanceof \Closure) {
                    $xmlrpcFuncName = "{$prefix}_closure";
                } else {
                    $callable = preg_replace(array('/\./', '/[^a-zA-Z0-9_\x7f-\xff]/'),
                        array('_', ''), $callable);
                    $xmlrpcFuncName = "{$prefix}_$callable";
                }
            }
        } else {
            $xmlrpcFuncName = $newFuncName;
        }

        while (function_exists($xmlrpcFuncName)) {
            $xmlrpcFuncName .= 'x';
        }

        return $xmlrpcFuncName;
    }

    /**
     * @param $callable
     * @param string $newFuncName
     * @param array $extraOptions
     * @param string $plainFuncName
     * @param array $funcDesc
     * @return string
     */
    protected function buildWrapFunctionSource($callable, $newFuncName, $extraOptions, $plainFuncName, $funcDesc)
    {
        $encodeNulls = isset($extraOptions['encode_nulls']) ? (bool)$extraOptions['encode_nulls'] : false;
        $encodePhpObjects = isset($extraOptions['encode_php_objs']) ? (bool)$extraOptions['encode_php_objs'] : false;
        $decodePhpObjects = isset($extraOptions['decode_php_objs']) ? (bool)$extraOptions['decode_php_objs'] : false;
        $catchWarnings = isset($extraOptions['suppress_warnings']) && $extraOptions['suppress_warnings'] ? '@' : '';

        $i = 0;
        $parsVariations = array();
        $pars = array();
        $pNum = count($funcDesc['params']);
        foreach ($funcDesc['params'] as $param) {

            if ($param['isoptional']) {
                // this particular parameter is optional. save as valid previous list of parameters
                $parsVariations[] = $pars;
            }

            $pars[] = "\$params[$i]";
            $i++;
            if ($i == $pNum) {
                // last allowed parameters combination
                $parsVariations[] = $pars;
            }
        }

        if (count($parsVariations) == 0) {
            // only known good synopsis = no parameters
            $parsVariations[] = array();
            $minPars = 0;
            $maxPars = 0;
        } else {
            $minPars = count($parsVariations[0]);
            $maxPars = count($parsVariations[count($parsVariations)-1]);
        }

        // build body of new function

        $innerCode = "  \$paramCount = \$req->getNumParams();\n";
        $innerCode .= "  if (\$paramCount < $minPars || \$paramCount > $maxPars) return new " . static::$namespace . "Response(0, " . PhpXmlRpc::$xmlrpcerr['incorrect_params'] . ", '" . PhpXmlRpc::$xmlrpcstr['incorrect_params'] . "');\n";

        $innerCode .= "  \$encoder = new " . static::$namespace . "Encoder();\n";
        if ($decodePhpObjects) {
            $innerCode .= "  \$params = \$encoder->decode(\$req, array('decode_php_objs'));\n";
        } else {
            $innerCode .= "  \$params = \$encoder->decode(\$req);\n";
        }

        // since we are building source code for later use, if we are given an object instance,
        // we go out of our way and store a pointer to it in a static class var...
        if (is_array($callable) && is_object($callable[0])) {
            static::holdObject($newFuncName, $callable[0]);
            $class = get_class($callable[0]);
            if ($class[0] !== '\\') {
                $class = '\\' . $class;
            }
            $innerCode .= "  /// @var $class \$obj\n";
            $innerCode .= "  \$obj = PhpXmlRpc\\Wrapper::getHeldObject('$newFuncName');\n";
            $realFuncName = '$obj->' . $callable[1];
        } else {
            $realFuncName = $plainFuncName;
        }
        foreach ($parsVariations as $i => $pars) {
            $innerCode .= "  if (\$paramCount == " . count($pars) . ") \$retVal = {$catchWarnings}$realFuncName(" . implode(',', $pars) . ");\n";
            if ($i < (count($parsVariations) - 1))
                $innerCode .= "  else\n";
        }
        $innerCode .= "  if (is_a(\$retVal, '" . static::$namespace . "Response'))\n    return \$retVal;\n  else\n";
        /// q: why not do the same for int, float, bool, string?
        if ($funcDesc['returns'] == Value::$xmlrpcDateTime || $funcDesc['returns'] == Value::$xmlrpcBase64) {
            $innerCode .= "    return new " . static::$namespace . "Response(new " . static::$namespace . "Value(\$retVal, '{$funcDesc['returns']}'));";
        } else {
            $encodeOptions = array();
            if ($encodeNulls) {
                $encodeOptions[] = 'null_extension';
            }
            if ($encodePhpObjects) {
                $encodeOptions[] = 'encode_php_objs';
            }

            if ($encodeOptions) {
                $innerCode .= "    return new " . static::$namespace . "Response(\$encoder->encode(\$retVal, array('" .
                    implode("', '", $encodeOptions) . "')));";
            } else {
                $innerCode .= "    return new " . static::$namespace . "Response(\$encoder->encode(\$retVal));";
            }
        }
        // shall we exclude functions returning by ref?
        // if ($func->returnsReference())
        //     return false;

        $code = "/**\n * @param \PhpXmlRpc\Request \$req\n * @return \PhpXmlRpc\Response\n * @throws \\Exception\n */\n" .
            "function $newFuncName(\$req)\n{\n" . $innerCode . "\n}";

        return $code;
    }

    /**
     * Given a user-defined PHP class or php object, map its methods onto a list of
     * PHP 'wrapper' functions that can be exposed as xml-rpc methods from an xml-rpc server
     * object and called from remote clients (as well as their corresponding signature info).
     *
     * @param string|object $className the name of the class whose methods are to be exposed as xml-rpc methods, or an object instance of that class
     * @param array $extraOptions see the docs for wrapPhpFunction for basic options, plus
     *                            - string method_type    'static', 'nonstatic', 'all' and 'auto' (default); the latter will switch between static and non-static depending on whether $className is a class name or object instance
     *                            - string method_filter  a regexp used to filter methods to wrap based on their names
     *                            - string prefix         used for the names of the xml-rpc methods created.
     *                            - string replace_class_name use to completely replace the class name with the prefix in the generated method names. e.g. instead of \Some\Namespace\Class.method use prefixmethod
     * @return array|false false on failure, or on array useable for the dispatch map
     *
     * @todo allow the generated function to be able to reuse an external Encoder instance instead of creating one on
     *       each invocation, for the case where all the generated functions will be saved as methods of a class
     */
    public function wrapPhpClass($className, $extraOptions = array())
    {
        $methodFilter = isset($extraOptions['method_filter']) ? $extraOptions['method_filter'] : '';
        $methodType = isset($extraOptions['method_type']) ? $extraOptions['method_type'] : 'auto';

        $results = array();
        $mList = get_class_methods($className);
        foreach ($mList as $mName) {
            if ($methodFilter == '' || preg_match($methodFilter, $mName)) {
                $func = new \ReflectionMethod($className, $mName);
                if (!$func->isPrivate() && !$func->isProtected() && !$func->isConstructor() && !$func->isDestructor() && !$func->isAbstract()) {
                    if (($func->isStatic() && ($methodType == 'all' || $methodType == 'static' || ($methodType == 'auto' && is_string($className)))) ||
                        (!$func->isStatic() && ($methodType == 'all' || $methodType == 'nonstatic' || ($methodType == 'auto' && is_object($className))))
                    ) {
                        $methodWrap = $this->wrapPhpFunction(array($className, $mName), '', $extraOptions);

                        if ($methodWrap) {
                            $results[$this->generateMethodNameForClassMethod($className, $mName, $extraOptions)] = $methodWrap;
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * @param string|object $className
     * @param string $classMethod
     * @param array $extraOptions
     * @return string
     *
     * @todo php allows many more characters in identifiers than the xml-rpc spec does. We should make sure to
     *       replace those (while trying to make sure we are not running in collisions)
     */
    protected function generateMethodNameForClassMethod($className, $classMethod, $extraOptions = array())
    {
        if (isset($extraOptions['replace_class_name']) && $extraOptions['replace_class_name']) {
            return (isset($extraOptions['prefix']) ?  $extraOptions['prefix'] : '') . $classMethod;
        }

        if (is_object($className)) {
            $realClassName = get_class($className);
        } else {
            $realClassName = $className;
        }
        return (isset($extraOptions['prefix']) ?  $extraOptions['prefix'] : '') . "$realClassName.$classMethod";
    }

    /**
     * Given an xml-rpc client and a method name, register a php wrapper function that will call it and return results
     * using native php types for both arguments and results. The generated php function will return a Response
     * object for failed xml-rpc calls.
     *
     * Known limitations:
     * - server must support system.methodSignature for the target xml-rpc method
     * - for methods that expose many signatures, only one can be picked (we could in principle check if signatures
     *   differ only by number of params and not by type, but it would be more complication than we can spare time for)
     * - nested xml-rpc params: the caller of the generated php function has to encode on its own the params passed to
     *   the php function if these are structs or arrays whose (sub)members include values of type base64
     *
     * Notes: the connection properties of the given client will be copied and reused for the connection used during
     * the call to the generated php function.
     * Calling the generated php function 'might' be slightly slow: a new xml-rpc client is created on every invocation
     * and an xmlrpc-connection opened+closed.
     * An extra 'debug' argument, defaulting to 0, is appended to the argument list of the generated function, useful
     * for debugging purposes.
     *
     * @param Client $client an xml-rpc client set up correctly to communicate with target server
     * @param string $methodName the xml-rpc method to be mapped to a php function
     * @param array $extraOptions array of options that specify conversion details. Valid options include
     *                            - integer signum              the index of the method signature to use in mapping (if
     *                                                          method exposes many sigs)
     *                            - integer timeout             timeout (in secs) to be used when executing function/calling remote method
     *                            - string  protocol            'http' (default), 'http11', 'https', 'h2' or 'h2c'
     *                            - string  new_function_name   the name of php function to create, when return_source is used.
     *                                                          If unspecified, lib will pick an appropriate name
     *                            - string  return_source       if true return php code w. function definition instead of
     *                                                          the function itself (closure)
     *                            - bool    encode_nulls        if true, use `<nil/>` elements instead of empty string xml-rpc
     *                                                          values for php null values
     *                            - bool    encode_php_objs     let php objects be sent to server using the 'improved' xml-rpc
     *                                                          notation, so server can deserialize them as php objects
     *                            - bool    decode_php_objs     --- WARNING !!! possible security hazard. only use it with
     *                                                          trusted servers ---
     *                            - mixed   return_on_fault     a php value to be returned when the xml-rpc call fails/returns
     *                                                          a fault response (by default the Response object is returned
     *                                                          in this case).  If a string is used, '%faultCode%' and
     *                                                          '%faultString%' tokens  will be substituted with actual error values
     *                            - bool    throw_on_fault      if true, throw an exception instead of returning a Response
     *                                                          in case of errors/faults;
     *                                                          if a string, do the same and assume it is the exception class to throw
     *                            - bool    debug               set it to 1 or 2 to see debug results of querying server for
     *                                                          method synopsis
     *                            - int     simple_client_copy  set it to 1 to have a lightweight copy of the $client object
     *                                                          made in the generated code (only used when return_source = true)
     * @return \Closure|string[]|false false on failure, closure by default and array for return_source = true
     *
     * @todo allow caller to give us the method signature instead of querying for it, or just say 'skip it'
     * @todo if we can not retrieve method signature, create a php function with varargs
     * @todo if caller did not specify a specific sig, shall we support all of them?
     *       It might be hard (hence slow) to match based on type and number of arguments...
     * @todo when wrapping methods without obj rebuilding, use return_type = 'phpvals' (faster)
     * @todo allow creating functions which have an extra `$debug=0` parameter
     */
    public function wrapXmlrpcMethod($client, $methodName, $extraOptions = array())
    {
        $newFuncName = isset($extraOptions['new_function_name']) ? $extraOptions['new_function_name'] : '';

        $buildIt = isset($extraOptions['return_source']) ? !($extraOptions['return_source']) : true;

        $mSig = $this->retrieveMethodSignature($client, $methodName, $extraOptions);
        if (!$mSig) {
            return false;
        }

        if ($buildIt) {
            return $this->buildWrapMethodClosure($client, $methodName, $extraOptions, $mSig);
        } else {
            // if in 'offline' mode, retrieve method description too.
            // in online mode, favour speed of operation
            $mDesc = $this->retrieveMethodHelp($client, $methodName, $extraOptions);

            $newFuncName = $this->newFunctionName($methodName, $newFuncName, $extraOptions);

            $results = $this->buildWrapMethodSource($client, $methodName, $extraOptions, $newFuncName, $mSig, $mDesc);

            $results['function'] = $newFuncName;

            return $results;
        }
    }

    /**
     * Retrieves an xml-rpc method signature from a server which supports system.methodSignature
     * @param Client $client
     * @param string $methodName
     * @param array $extraOptions
     * @return false|array
     */
    protected function retrieveMethodSignature($client, $methodName, array $extraOptions = array())
    {
        $reqClass = static::$namespace . 'Request';
        $valClass = static::$namespace . 'Value';
        $decoderClass = static::$namespace . 'Encoder';

        $debug = isset($extraOptions['debug']) ? ($extraOptions['debug']) : 0;
        $timeout = isset($extraOptions['timeout']) ? (int)$extraOptions['timeout'] : 0;
        $protocol = isset($extraOptions['protocol']) ? $extraOptions['protocol'] : '';
        $sigNum = isset($extraOptions['signum']) ? (int)$extraOptions['signum'] : 0;

        $req = new $reqClass('system.methodSignature');
        $req->addParam(new $valClass($methodName));
        $origDebug = $client->getOption(Client::OPT_DEBUG);
        $client->setDebug($debug);
        /// @todo move setting of timeout, protocol to outside the send() call
        $response = $client->send($req, $timeout, $protocol);
        $client->setDebug($origDebug);
        if ($response->faultCode()) {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': could not retrieve method signature from remote server for method ' . $methodName);
            return false;
        }

        $mSig = $response->value();
        /// @todo what about return xml?
        if ($client->getOption(Client::OPT_RETURN_TYPE) != 'phpvals') {
            $decoder = new $decoderClass();
            $mSig = $decoder->decode($mSig);
        }

        if (!is_array($mSig) || count($mSig) <= $sigNum) {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': could not retrieve method signature nr.' . $sigNum . ' from remote server for method ' . $methodName);
            return false;
        }

        return $mSig[$sigNum];
    }

    /**
     * @param Client $client
     * @param string $methodName
     * @param array $extraOptions
     * @return string in case of any error, an empty string is returned, no warnings generated
     */
    protected function retrieveMethodHelp($client, $methodName, array $extraOptions = array())
    {
        $reqClass = static::$namespace . 'Request';
        $valClass = static::$namespace . 'Value';

        $debug = isset($extraOptions['debug']) ? ($extraOptions['debug']) : 0;
        $timeout = isset($extraOptions['timeout']) ? (int)$extraOptions['timeout'] : 0;
        $protocol = isset($extraOptions['protocol']) ? $extraOptions['protocol'] : '';

        $mDesc = '';

        $req = new $reqClass('system.methodHelp');
        $req->addParam(new $valClass($methodName));
        $origDebug = $client->getOption(Client::OPT_DEBUG);
        $client->setDebug($debug);
        /// @todo move setting of timeout, protocol to outside the send() call
        $response = $client->send($req, $timeout, $protocol);
        $client->setDebug($origDebug);
        if (!$response->faultCode()) {
            $mDesc = $response->value();
            if ($client->getOption(Client::OPT_RETURN_TYPE) != 'phpvals') {
                $mDesc = $mDesc->scalarVal();
            }
        }

        return $mDesc;
    }

    /**
     * @param Client $client
     * @param string $methodName
     * @param array $extraOptions @see wrapXmlrpcMethod
     * @param array $mSig
     * @return \Closure
     *
     * @todo should we allow usage of parameter simple_client_copy to mean 'do not clone' in this case?
     */
    protected function buildWrapMethodClosure($client, $methodName, array $extraOptions, $mSig)
    {
        // we clone the client, so that we can modify it a bit independently of the original
        $clientClone = clone $client;
        $function = function() use($clientClone, $methodName, $extraOptions, $mSig)
        {
            $timeout = isset($extraOptions['timeout']) ? (int)$extraOptions['timeout'] : 0;
            $protocol = isset($extraOptions['protocol']) ? $extraOptions['protocol'] : '';
            $encodePhpObjects = isset($extraOptions['encode_php_objs']) ? (bool)$extraOptions['encode_php_objs'] : false;
            $decodePhpObjects = isset($extraOptions['decode_php_objs']) ? (bool)$extraOptions['decode_php_objs'] : false;
            $encodeNulls = isset($extraOptions['encode_nulls']) ? (bool)$extraOptions['encode_nulls'] : false;
            $throwFault = false;
            $decodeFault = false;
            $faultResponse = null;
            if (isset($extraOptions['throw_on_fault'])) {
                $throwFault = $extraOptions['throw_on_fault'];
            } else if (isset($extraOptions['return_on_fault'])) {
                $decodeFault = true;
                $faultResponse = $extraOptions['return_on_fault'];
            }

            $reqClass = static::$namespace . 'Request';
            $encoderClass = static::$namespace . 'Encoder';
            $valueClass = static::$namespace . 'Value';

            $encoder = new $encoderClass();
            $encodeOptions = array();
            if ($encodePhpObjects) {
                $encodeOptions[] = 'encode_php_objs';
            }
            if ($encodeNulls) {
                $encodeOptions[] = 'null_extension';
            }
            $decodeOptions = array();
            if ($decodePhpObjects) {
                $decodeOptions[] = 'decode_php_objs';
            }

            /// @todo check for insufficient nr. of args besides excess ones? note that 'source' version does not...

            // support one extra parameter: debug
            $maxArgs = count($mSig)-1; // 1st element is the return type
            $currentArgs = func_get_args();
            if (func_num_args() == ($maxArgs+1)) {
                $debug = array_pop($currentArgs);
                $clientClone->setDebug($debug);
            }

            $xmlrpcArgs = array();
            foreach ($currentArgs as $i => $arg) {
                if ($i == $maxArgs) {
                    break;
                }
                $pType = $mSig[$i+1];
                if ($pType == 'i4' || $pType == 'i8' || $pType == 'int' || $pType == 'boolean' || $pType == 'double' ||
                    $pType == 'string' || $pType == 'dateTime.iso8601' || $pType == 'base64' || $pType == 'null'
                ) {
                    // by building directly xml-rpc values when type is known and scalar (instead of encode() calls),
                    // we make sure to honour the xml-rpc signature
                    $xmlrpcArgs[] = new $valueClass($arg, $pType);
                } else {
                    $xmlrpcArgs[] = $encoder->encode($arg, $encodeOptions);
                }
            }

            $req = new $reqClass($methodName, $xmlrpcArgs);
            // use this to get the maximum decoding flexibility
            $clientClone->setOption(Client::OPT_RETURN_TYPE, 'xmlrpcvals');
            $resp = $clientClone->send($req, $timeout, $protocol);
            if ($resp->faultcode()) {
                if ($throwFault) {
                    if (is_string($throwFault)) {
                        throw new $throwFault($resp->faultString(), $resp->faultCode());
                    } else {
                        throw new \PhpXmlRpc\Exception($resp->faultString(), $resp->faultCode());
                    }
                } else if ($decodeFault) {
                    if (is_string($faultResponse) && ((strpos($faultResponse, '%faultCode%') !== false) ||
                            (strpos($faultResponse, '%faultString%') !== false))) {
                        $faultResponse = str_replace(array('%faultCode%', '%faultString%'),
                            array($resp->faultCode(), $resp->faultString()), $faultResponse);
                    }
                    return $faultResponse;
                } else {
                    return $resp;
                }
            } else {
                return $encoder->decode($resp->value(), $decodeOptions);
            }
        };

        return $function;
    }

    /**
     * @internal made public just for Debugger usage
     *
     * @param Client $client
     * @param string $methodName
     * @param array $extraOptions @see wrapXmlrpcMethod
     * @param string $newFuncName
     * @param array $mSig
     * @param string $mDesc
     * @return string[] keys: source, docstring
     */
    public function buildWrapMethodSource($client, $methodName, array $extraOptions, $newFuncName, $mSig, $mDesc='')
    {
        $timeout = isset($extraOptions['timeout']) ? (int)$extraOptions['timeout'] : 0;
        $protocol = isset($extraOptions['protocol']) ? $extraOptions['protocol'] : '';
        $encodePhpObjects = isset($extraOptions['encode_php_objs']) ? (bool)$extraOptions['encode_php_objs'] : false;
        $decodePhpObjects = isset($extraOptions['decode_php_objs']) ? (bool)$extraOptions['decode_php_objs'] : false;
        $encodeNulls = isset($extraOptions['encode_nulls']) ? (bool)$extraOptions['encode_nulls'] : false;
        $clientCopyMode = isset($extraOptions['simple_client_copy']) ? (int)($extraOptions['simple_client_copy']) : 0;
        $prefix = isset($extraOptions['prefix']) ? $extraOptions['prefix'] : 'xmlrpc';
        $throwFault = false;
        $decodeFault = false;
        $faultResponse = null;
        if (isset($extraOptions['throw_on_fault'])) {
            $throwFault = $extraOptions['throw_on_fault'];
        } else if (isset($extraOptions['return_on_fault'])) {
            $decodeFault = true;
            $faultResponse = $extraOptions['return_on_fault'];
        }

        $code = "function $newFuncName(";
        if ($clientCopyMode < 2) {
            // client copy mode 0 or 1 == full / partial client copy in emitted code
            $verbatimClientCopy = !$clientCopyMode;
            $innerCode = '  ' . str_replace("\n", "\n  ", $this->buildClientWrapperCode($client, $verbatimClientCopy, $prefix, static::$namespace));
            $innerCode .= "\$client->setDebug(\$debug);\n";
            $this_ = '';
        } else {
            // client copy mode 2 == no client copy in emitted code
            $innerCode = '';
            $this_ = 'this->';
        }
        $innerCode .= "  \$req = new " . static::$namespace . "Request('$methodName');\n";

        if ($mDesc != '') {
            // take care that PHP comment is not terminated unwillingly by method description
            /// @todo according to the spec, method desc can have html in it. We should run it through strip_tags...
            $mDesc = "/**\n * " . str_replace(array("\n", '*/'), array("\n * ", '* /'), $mDesc) . "\n";
        } else {
            $mDesc = "/**\n * Function $newFuncName.\n";
        }

        // param parsing
        $innerCode .= "  \$encoder = new " . static::$namespace . "Encoder();\n";
        $plist = array();
        $pCount = count($mSig);
        for ($i = 1; $i < $pCount; $i++) {
            $plist[] = "\$p$i";
            $pType = $mSig[$i];
            if ($pType == 'i4' || $pType == 'i8' || $pType == 'int' || $pType == 'boolean' || $pType == 'double' ||
                $pType == 'string' || $pType == 'dateTime.iso8601' || $pType == 'base64' || $pType == 'null'
            ) {
                // only build directly xml-rpc values when type is known and scalar
                $innerCode .= "  \$p$i = new " . static::$namespace . "Value(\$p$i, '$pType');\n";
            } else {
                if ($encodePhpObjects || $encodeNulls) {
                    $encOpts = array();
                    if ($encodePhpObjects) {
                        $encOpts[] = 'encode_php_objs';
                    }
                    if ($encodeNulls) {
                        $encOpts[] = 'null_extension';
                    }

                    $innerCode .= "  \$p$i = \$encoder->encode(\$p$i, array( '" . implode("', '", $encOpts) . "'));\n";
                } else {
                    $innerCode .= "  \$p$i = \$encoder->encode(\$p$i);\n";
                }
            }
            $innerCode .= "  \$req->addParam(\$p$i);\n";
            $mDesc .= " * @param " . $this->xmlrpc2PhpType($pType) . " \$p$i\n";
        }
        if ($clientCopyMode < 2) {
            $plist[] = '$debug = 0';
            $mDesc .= " * @param int \$debug when 1 (or 2) will enable debugging of the underlying {$prefix} call (defaults to 0)\n";
        }
        $plist = implode(', ', $plist);
        $mDesc .= ' * @return ' . $this->xmlrpc2PhpType($mSig[0]);
        if ($throwFault) {
            $mDesc .= "\n * @throws " . (is_string($throwFault) ? $throwFault : '\\PhpXmlRpc\\Exception');
        } else if ($decodeFault) {
            $mDesc .= '|' . gettype($faultResponse) . " (a " . gettype($faultResponse) . " if call fails)";
        } else {
            $mDesc .= '|' . static::$namespace . "Response (a " . static::$namespace . "Response obj instance if call fails)";
        }
        $mDesc .= "\n */\n";

        /// @todo move setting of timeout, protocol to outside the send() call
        $innerCode .= "  \$res = \${$this_}client->send(\$req, $timeout, '$protocol');\n";
        if ($throwFault) {
            if (!is_string($throwFault)) {
                $throwFault = '\\PhpXmlRpc\\Exception';
            }
            $respCode = "throw new $throwFault(\$res->faultString(), \$res->faultCode())";
        } else if ($decodeFault) {
            if (is_string($faultResponse) && ((strpos($faultResponse, '%faultCode%') !== false) || (strpos($faultResponse, '%faultString%') !== false))) {
                $respCode = "return str_replace(array('%faultCode%', '%faultString%'), array(\$res->faultCode(), \$res->faultString()), '" . str_replace("'", "''", $faultResponse) . "')";
            } else {
                $respCode = 'return ' . var_export($faultResponse, true);
            }
        } else {
            $respCode = 'return $res';
        }
        if ($decodePhpObjects) {
            $innerCode .= "  if (\$res->faultCode()) $respCode; else return \$encoder->decode(\$res->value(), array('decode_php_objs'));";
        } else {
            $innerCode .= "  if (\$res->faultCode()) $respCode; else return \$encoder->decode(\$res->value());";
        }

        $code = $code . $plist . ")\n{\n" . $innerCode . "\n}\n";

        return array('source' => $code, 'docstring' => $mDesc);
    }

    /**
     * Similar to wrapXmlrpcMethod, but will generate a php class that wraps all xml-rpc methods exposed by the remote
     * server as own methods.
     * For a slimmer alternative, see the code in demo/client/proxy.php.
     * Note that unlike wrapXmlrpcMethod, we always have to generate php code here. Since php 7 anon classes exist, but
     * we do not support them yet...
     *
     * @see wrapXmlrpcMethod for more details.
     *
     * @param Client $client the client obj all set to query the desired server
     * @param array $extraOptions list of options for wrapped code. See the ones from wrapXmlrpcMethod, plus
     *                            - string method_filter      regular expression
     *                            - string new_class_name
     *                            - string prefix
     *                            - bool   simple_client_copy set it to true to avoid copying all properties of $client into the copy made in the new class
     * @return string|array|false false on error, the name of the created class if all ok or an array with code, class name and comments (if the appropriate option is set in extra_options)
     *
     * @todo add support for anonymous classes in the 'buildIt' case for php > 7
     * @todo add method setDebug() to new class, to enable/disable debugging
     * @todo optimization - move the generated Encoder instance to be a property of the created class, instead of creating
     *                      it on every generated method invocation
     */
    public function wrapXmlrpcServer($client, $extraOptions = array())
    {
        $methodFilter = isset($extraOptions['method_filter']) ? $extraOptions['method_filter'] : '';
        $timeout = isset($extraOptions['timeout']) ? (int)$extraOptions['timeout'] : 0;
        $protocol = isset($extraOptions['protocol']) ? $extraOptions['protocol'] : '';
        $newClassName = isset($extraOptions['new_class_name']) ? $extraOptions['new_class_name'] : '';
        $encodeNulls = isset($extraOptions['encode_nulls']) ? (bool)$extraOptions['encode_nulls'] : false;
        $encodePhpObjects = isset($extraOptions['encode_php_objs']) ? (bool)$extraOptions['encode_php_objs'] : false;
        $decodePhpObjects = isset($extraOptions['decode_php_objs']) ? (bool)$extraOptions['decode_php_objs'] : false;
        $verbatimClientCopy = isset($extraOptions['simple_client_copy']) ? !($extraOptions['simple_client_copy']) : true;
        $throwOnFault = isset($extraOptions['throw_on_fault']) ? (bool)$extraOptions['throw_on_fault'] : false;
        $buildIt = isset($extraOptions['return_source']) ? !($extraOptions['return_source']) : true;
        $prefix = isset($extraOptions['prefix']) ? $extraOptions['prefix'] : 'xmlrpc';

        $reqClass = static::$namespace . 'Request';
        $decoderClass = static::$namespace . 'Encoder';

        // retrieve the list of methods
        $req = new $reqClass('system.listMethods');
        /// @todo move setting of timeout, protocol to outside the send() call
        $response = $client->send($req, $timeout, $protocol);
        if ($response->faultCode()) {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': could not retrieve method list from remote server');

            return false;
        }
        $mList = $response->value();
        /// @todo what about return_type = xml?
        if ($client->getOption(Client::OPT_RETURN_TYPE) != 'phpvals') {
            $decoder = new $decoderClass();
            $mList = $decoder->decode($mList);
        }
        if (!is_array($mList) || !count($mList)) {
            $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': could not retrieve meaningful method list from remote server');

            return false;
        }

        // pick a suitable name for the new function, avoiding collisions
        if ($newClassName != '') {
            $xmlrpcClassName = $newClassName;
        } else {
            /// @todo direct access to $client->server is now deprecated
            $xmlrpcClassName = $prefix . '_' . preg_replace(array('/\./', '/[^a-zA-Z0-9_\x7f-\xff]/'), array('_', ''),
                $client->server) . '_client';
        }
        while ($buildIt && class_exists($xmlrpcClassName)) {
            $xmlrpcClassName .= 'x';
        }

        $source = "class $xmlrpcClassName\n{\n  public \$client;\n\n";
        $source .= "  function __construct()\n  {\n";
        $source .= '    ' . str_replace("\n", "\n    ", $this->buildClientWrapperCode($client, $verbatimClientCopy, $prefix, static::$namespace));
        $source .= "\$this->client = \$client;\n  }\n\n";
        $opts = array(
            'return_source' => true,
            'simple_client_copy' => 2, // do not produce code to copy the client object
            'timeout' => $timeout,
            'protocol' => $protocol,
            'encode_nulls' => $encodeNulls,
            'encode_php_objs' => $encodePhpObjects,
            'decode_php_objs' => $decodePhpObjects,
            'throw_on_fault' => $throwOnFault,
            'prefix' => $prefix,
        );

        /// @todo build phpdoc for class definition, too
        foreach ($mList as $mName) {
            if ($methodFilter == '' || preg_match($methodFilter, $mName)) {
                /// @todo this will fail if server exposes 2 methods called f.e. do.something and do_something
                $opts['new_function_name'] = preg_replace(array('/\./', '/[^a-zA-Z0-9_\x7f-\xff]/'),
                    array('_', ''), $mName);
                $methodWrap = $this->wrapXmlrpcMethod($client, $mName, $opts);
                if ($methodWrap) {
                    if ($buildIt) {
                        $source .= $methodWrap['source'] . "\n";

                    } else {
                        $source .= '  ' . str_replace("\n", "\n  ", $methodWrap['docstring']);
                        $source .= str_replace("\n", "\n  ", $methodWrap['source']). "\n";
                    }

                } else {
                    $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': will not create class method to wrap remote method ' . $mName);
                }
            }
        }
        $source .= "}\n";
        if ($buildIt) {
            $allOK = 0;
            eval($source . '$allOK=1;');
            if ($allOK) {
                return $xmlrpcClassName;
            } else {
                /// @todo direct access to $client->server is now deprecated
                $this->getLogger()->error('XML-RPC: ' . __METHOD__ . ': could not create class ' . $xmlrpcClassName .
                    ' to wrap remote server ' . $client->server);
                return false;
            }
        } else {
            return array('class' => $xmlrpcClassName, 'code' => $source, 'docstring' => '');
        }
    }

    /**
     * Given necessary info, generate php code that will build a client object just like the given one.
     * Take care that no full checking of input parameters is done to ensure that valid php code is emitted.
     * @param Client $client
     * @param bool $verbatimClientCopy when true, copy the whole options of the client, except for 'debug' and 'return_type'
     * @param string $prefix used for the return_type of the created client
     * @param string $namespace
     * @return string
     */
    protected function buildClientWrapperCode($client, $verbatimClientCopy, $prefix = 'xmlrpc', $namespace = '\\PhpXmlRpc\\')
    {
        $code = "\$client = new {$namespace}Client('" . str_replace(array("\\", "'"), array("\\\\", "\'"), $client->getUrl()) .
            "');\n";

        // copy all client fields to the client that will be generated runtime
        // (this provides for future expansion or subclassing of client obj)
        if ($verbatimClientCopy) {
            foreach ($client->getOptions() as $opt => $val) {
                if ($opt != 'debug' && $opt != 'return_type') {
                    $val = var_export($val, true);
                    $code .= "\$client->setOption('$opt', $val);\n";
                }
            }
        }
        // only make sure that client always returns the correct data type
        $code .= "\$client->setOption(\PhpXmlRpc\Client::OPT_RETURN_TYPE, '{$prefix}vals');\n";
        return $code;
    }

    /**
     * @param string $index
     * @param object $object
     * @return void
     */
    public static function holdObject($index, $object)
    {
        self::$objHolder[$index] = $object;
    }

    /**
     * @param string $index
     * @return object
     * @throws ValueErrorException
     */
    public static function getHeldObject($index)
    {
        if (isset(self::$objHolder[$index])) {
            return self::$objHolder[$index];
        }

        throw new ValueErrorException("No object held for index '$index'");
    }
}
