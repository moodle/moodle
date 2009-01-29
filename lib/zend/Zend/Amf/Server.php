<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Amf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Server_Interface */
require_once 'Zend/Server/Interface.php';

/** Zend_Server_Reflection */
require_once 'Zend/Server/Reflection.php';

/** Zend_Amf_Constants */
require_once 'Zend/Amf/Constants.php';

/** Zend_Amf_Value_MessageBody */
require_once 'Zend/Amf/Value/MessageBody.php';

/** Zend_Amf_Value_Messaging_CommandMessage */
require_once 'Zend/Amf/Value/Messaging/CommandMessage.php';

/**
 * An AMF gateway server implementation to allow the connection of the Adobe Flash Player to
 * the Zend Framework
 *
 * @todo       Make the relection methods cache and autoload.
 * @package    Zend_Amf
 * @subpackage Server
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Amf_Server implements Zend_Server_Interface
{
    /**
     * Array of dispatchables
     * @var array
     */
    protected $_methods = array();

    /**
     * Array of directories to search for loading classes dynamically
     * @var array
     */
    protected $_directories = array();

    /**
     * @var bool Production flag; whether or not to return exception messages
     */
    protected $_production = true;

    /**
     * Request processed
     * @var null|Zend_Amf_Request
     */
    protected $_request = null;

    /**
     * Class to use for responses
     * @var null|Zend_Amf_Response
     */
    protected $_response;

    /**
     * Dispatch table of name => method pairs
     * @var array
     */
    protected $_table = array();

    /**
     * Set production flag
     *
     * @param  bool $flag
     * @return Zend_Amf_Server
     */
    public function setProduction($flag)
    {
        $this->_production = (bool) $flag;
        return $this;
    }

    /**
     * Whether or not the server is in production
     *
     * @return bool
     */
    public function isProduction()
    {
        return $this->_production;
    }


    /**
     * Loads a remote class or method and executes the function and returns
     * the result
     *
     * @param  string $method Is the method to execute
     * @param  mixed $param values for the method
     * @return mixed $response the result of executing the method
     * @throws Zend_Amf_Server_Exception
     */
    protected function _dispatch($method, $params = null, $source = null)
    {
        if (!isset($this->_table[$method])) {
            // if source is null a method that was not defined was called.
            if ($source) {
                $classPath    = array();
                $path         = explode('.', $source);
                $className    = array_pop($path);
                $uriclasspath = implode('/', $path);

                // Take the user supplied directories and add the unique service path to the end.
                foreach ($this->_directories as $dir) {
                    $classPath[] = $dir . $uriclasspath;
                }

                require_once('Zend/Loader.php');
                try {
                    Zend_Loader::loadClass($className, $classPath);
                } catch (Exception $e) {
                    require_once 'Zend/Amf/Server/Exception.php';
                    throw new Zend_Amf_Server_Exception('Class "' . $className . '" does not exist');
                }
                // Add the new loaded class to the server.
                $this->setClass($className);
            } else {
                require_once 'Zend/Amf/Server/Exception.php';
                throw new Zend_Amf_Server_Exception('Method "' . $method . '" does not exist');
            }
        }

        $info = $this->_table[$method];
        $argv = $info->getInvokeArguments();
        if (0 < count($argv)) {
            $params = array_merge($params, $argv);
        }

        if ($info instanceof Zend_Server_Reflection_Function) {
            $func = $info->getName();
            $return = call_user_func_array($func, $params);
        } elseif ($info instanceof Zend_Server_Reflection_Method) {
            // Get class
            $class = $info->getDeclaringClass()->getName();
            if ('static' == $info->isStatic()) {
                // for some reason, invokeArgs() does not work the same as
                // invoke(), and expects the first argument to be an object.
                // So, using a callback if the method is static.
                $return = call_user_func_array(array($class, $info->getName()), $params);
            } else {
                // Object methods
                try {
                    $object = $info->getDeclaringClass()->newInstance();
                } catch (Exception $e) {
                    require_once 'Zend/Amf/Server/Exception.php';
                    throw new Zend_Amf_Server_Exception('Error instantiating class ' . $class . ' to invoke method ' . $info->getName(), 621);
                }
                $return = $info->invokeArgs($object, $params);
            }
        } else {
            require_once 'Zend/Amf/Server/Exception.php';
            throw new Zend_Amf_Server_Exception('Method missing implementation ' . get_class($info));
        }

        return $return;
    }

    /**
     * Handles each of the 11 different command message types.
     *
     * A command message is a flex.messaging.messages.CommandMessage
     *
     * @see    Zend_Amf_Value_Messaging_CommandMessage
     * @param  Zend_Amf_Value_Messaging_CommandMessage $message
     * @return Zend_Amf_Value_Messaging_AcknowledgeMessage
     */
    protected function _loadCommandMessage(Zend_Amf_Value_Messaging_CommandMessage $message)
    {
        switch($message->operation) {
            case Zend_Amf_Value_Messaging_CommandMessage::CLIENT_PING_OPERATION :
                require_once 'Zend/Amf/Value/Messaging/AcknowledgeMessage.php';
                $return = new Zend_Amf_Value_Messaging_AcknowledgeMessage($message);
                break;
            default :
                require_once 'Zend/Amf/Server/Exception.php';
                throw new Zend_Amf_Server_Exception('CommandMessage::' . $message->operation . ' not implemented');
                break;
        }
        return $return;
    }

    /**
     * Takes the deserialized AMF request and performs any operations.
     *
     * @todo   should implement and SPL observer pattern for custom AMF headers
     * @todo   implement AMF header authentication
     * @param  Zend_Amf_Request $request
     * @return Zend_Amf_Response
     * @throws Zend_Amf_server_Exception|Exception
     */
    protected function _handle(Zend_Amf_Request $request)
    {
        // Get the object encoding of the request.
        $objectEncoding = $request->getObjectEncoding();

        // create a response object to place the output from the services.
        $response = $this->getResponse();

        // set reponse encoding
        $response->setObjectEncoding($objectEncoding);

        $responseBody = $request->getAmfBodies();

        // Iterate through each of the service calls in the AMF request
        foreach($responseBody as $body)
        {
            try {
                if ($objectEncoding == Zend_Amf_Constants::AMF0_OBJECT_ENCODING) {
                    // AMF0 Object Encoding
                    $targetURI = $body->getTargetURI();

                    // Split the target string into its values.
                    $source = substr($targetURI, 0, strrpos($targetURI, '.'));

                    if ($source) {
                        // Break off method name from namespace into source
                        $method = substr(strrchr($targetURI, '.'), 1);
                        $return = $this->_dispatch($method, $body->getData(), $source);
                    } else {
                        // Just have a method name.
                        $return = $this->_dispatch($targetURI, $body->getData());
                    }
                } else {
                    // AMF3 read message type
                    $message = $body->getData();
                    if ($message instanceof Zend_Amf_Value_Messaging_CommandMessage) {
                        // async call with command message
                        $return = $this->_loadCommandMessage($message);
                    } elseif ($message instanceof Zend_Amf_Value_Messaging_RemotingMessage) {
                        require_once 'Zend/Amf/Value/Messaging/AcknowledgeMessage.php';
                        $return = new Zend_Amf_Value_Messaging_AcknowledgeMessage($message);
                        $return->body = $this->_dispatch($message->operation, $message->body, $message->source);
                    } else {
                        // Amf3 message sent with netConnection
                        $targetURI = $body->getTargetURI();

                        // Split the target string into its values.
                        $source = substr($targetURI, 0, strrpos($targetURI, '.'));

                        if ($source) {
                            // Break off method name from namespace into source
                            $method = substr(strrchr($targetURI, '.'), 1);
                            $return = $this->_dispatch($method, array($body->getData()), $source);
                        } else {
                            // Just have a method name.
                            $return = $this->_dispatch($targetURI, $body->getData());
                        }
                    }
                }
                $responseType = Zend_AMF_Constants::RESULT_METHOD;
            } catch (Exception $e) {
                switch ($objectEncoding) {
                    case Zend_Amf_Constants::AMF0_OBJECT_ENCODING :
                        $return = array(
                            'description' => ($this->isProduction()) ? '' : $e->getMessage(),
                            'detail'      => ($this->isProduction()) ? '' : $e->getTraceAsString(),
                            'line'        => ($this->isProduction()) ? 0  : $e->getLine(),
                            'code'        => $e->getCode(),
                        );
                        break;
                    case Zend_Amf_Constants::AMF3_OBJECT_ENCODING :
                        require_once 'Zend/Amf/Value/Messaging/ErrorMessage.php';
                        $return = new Zend_Amf_Value_Messaging_ErrorMessage($message);
                        $return->faultString = $this->isProduction() ? '' : $e->getMessage();
                        $return->faultCode   = $e->getCode();
                        $return->faultDetail = $this->isProduction() ? '' : $e->getTraceAsString();
                        break;
                }
                $responseType = Zend_AMF_Constants::STATUS_METHOD;
            }

            $responseURI = $body->getResponseURI() . $responseType;
            $newBody     = new Zend_Amf_Value_MessageBody($responseURI, null, $return);
            $response->addAmfBody($newBody);
        }

        // serialize the response and return serialized body.
        $response->finalize();
    }

    /**
     * Handle an AMF call from the gateway.
     *
     * @param  null|Zend_Amf_Request $request Optional
     * @return Zend_Amf_Response
     */
    public function handle($request = null)
    {
        // Check if request was passed otherwise get it from the server
        if (is_null($request) || !$request instanceof Zend_Amf_Request) {
            $request = $this->getRequest();
        } else {
            $this->setRequest($request);
        }

        // Check for errors that may have happend in deserialization of Request.
        try {
            // Take converted PHP objects and handle service call.
            // Serialize to Zend_Amf_response for output stream
            $this->_handle($request);
            $response = $this->getResponse();
        } catch (Exception $e) {
            // Handle any errors in the serialization and service  calls.
            require_once 'Zend/Amf/Server/Exception.php';
            throw new Zend_Amf_Server_Exception('Handle error: ' . $e->getMessage() . ' ' . $e->getLine());
        }

        // Return the Amf serialized output string
        return $response;
    }

    /**
     * Set request object
     *
     * @param  string|Zend_Amf_Request $request
     * @return Zend_Amf_Server
     */
    public function setRequest($request)
    {
        if (is_string($request) && class_exists($request)) {
            $request = new $request();
            if (!$request instanceof Zend_Amf_Request) {
                require_once 'Zend/Amf/Server/Exception.php';
                throw new Zend_Amf_Server_Exception('Invalid request class');
            }
        } elseif (!$request instanceof Zend_Amf_Request) {
            require_once 'Zend/Amf/Server/Exception.php';
            throw new Zend_Amf_Server_Exception('Invalid request object');
        }
        $this->_request = $request;
        return $this;
    }

    /**
     * Return currently registered request object
     *
     * @return null|Zend_Amf_Request
     */
    public function getRequest()
    {
        if (null === $this->_request) {
            require_once 'Zend/Amf/Request/Http.php';
            $this->setRequest(new Zend_Amf_Request_Http());
        }

        return $this->_request;
    }

    /**
     * Public access method to private Zend_Amf_Server_Response refrence
     *
     * @param  string|Zend_Amf_Server_Response $response
     * @return Zend_Amf_Server
     */
    public function setResponse($response)
    {
        if (is_string($response) && class_exists($response)) {
            $response = new $response();
            if (!$response instanceof Zend_Amf_Response) {
                require_once 'Zend/Amf/Server/Exception.php';
                throw new Zend_Amf_Server_Exception('Invalid response class');
            }
        } elseif (!$response instanceof Zend_Amf_Response) {
            require_once 'Zend/Amf/Server/Exception.php';
            throw new Zend_Amf_Server_Exception('Invalid response object');
        }
        $this->_response = $response;
        return $this;
    }

    /**
     * get a refrence to the Zend_Amf_response instance
     *
     * @return Zend_Amf_Server_Response
     */
    public function getResponse()
    {
        if (null === ($response = $this->_response)) {
            require_once 'Zend/Amf/Response/Http.php';
            $this->setResponse(new Zend_Amf_Response_Http());
        }
        return $this->_response;
    }

    /**
     * Add a file system path to a directory of services.
     * @param string|array $path
     */
    public function setClassPath($path)
    {

    }

    /**
     * Attach a class or object to the server
     *
     * Class may be either a class name or an instantiated object. Reflection
     * is done on the class or object to determine the available public
     * methods, and each is attached to the server as and available method. If
     * a $namespace has been provided, that namespace is used to prefix
     * AMF service call.
     *
     * @param  string|object $class
     * @param  string $namespace Optional
     * @param  mixed $arg Optional arguments to pass to a method
     * @return Zend_Amf_Server
     * @throws Zend_Amf_Server_Exception on invalid input
     */
    public function setClass($class, $namespace = '', $argv = null)
    {
        if (is_string($class) && !class_exists($class)){
            require_once 'Zend/Amf/Server/Exception.php';
            throw new Zend_Amf_Server_Exception('Invalid method or class');
        } elseif (!is_string($class) && !is_object($class)) {
            require_once 'Zend/Amf/Server/Exception.php';
            throw new Zend_Amf_Server_Exception('Invalid method or class; must be a classname or object');
        }

        $argv = null;
        if (2 < func_num_args()) {
            $argv = array_slice(func_get_args(), 2);
        }

        $this->_methods[] = Zend_Server_Reflection::reflectClass($class, $argv, $namespace);
        $this->_buildDispatchTable();

        return $this;
    }

    /**
     * Attach a function to the server
     *
     * Additional arguments to pass to the function at dispatch may be passed;
     * any arguments following the namespace will be aggregated and passed at
     * dispatch time.
     *
     * @param  string|array $function Valid callback
     * @param  string $namespace Optional namespace prefix
     * @return Zend_Amf_Server
     * @throws Zend_Amf_Server_Exception
     */
    public function addFunction($function, $namespace = '')
    {
        if (!is_string($function) && !is_array($function)) {
            require_once 'Zend/Amf/Server/Exception.php';
            throw new Zend_Amf_Server_Exception('Unable to attach function');
        }

        $argv = null;
        if (2 < func_num_args()) {
            $argv = array_slice(func_get_args(), 2);
        }

        $function = (array) $function;
        foreach ($function as $func) {
            if (!is_string($func) || !function_exists($func)) {
                require_once 'Zend/Amf/Server/Exception.php';
                throw new Zend_Amf_Server_Exception('Unable to attach function');
            }
            $this->_methods[] = Zend_Server_Reflection::reflectFunction($func, $argv, $namespace);
        }

        $this->_buildDispatchTable();
        return $this;
    }


    /**
     * Creates an array of directories in which services can reside.
     *
     * @param string $dir
     */
    public function addDirectory($dir)
    {
        $this->_directories[] = $dir;
    }

    /**
     * Returns an array of directories that can hold services.
     *
     * @return array
     */
    public function getDirectory()
    {
        return $_directory;
    }

    /**
     * (Re)Build the dispatch table
     *
     * The dispatch table consists of a an array of method name =>
     * Zend_Server_Reflection_Function_Abstract pairs
     *
     * @return void
     */
    protected function _buildDispatchTable()
    {
        $table = array();
        foreach ($this->_methods as $key => $dispatchable) {
            if ($dispatchable instanceof Zend_Server_Reflection_Function_Abstract) {
                $ns   = $dispatchable->getNamespace();
                $name = $dispatchable->getName();
                $name = empty($ns) ? $name : $ns . '.' . $name;

                if (isset($table[$name])) {
                    require_once 'Zend/Amf/Server/Exception.php';
                    throw new Zend_Amf_Server_Exception('Duplicate method registered: ' . $name);
                }
                $table[$name] = $dispatchable;
                continue;
            }

            if ($dispatchable instanceof Zend_Server_Reflection_Class) {
                foreach ($dispatchable->getMethods() as $method) {
                    $ns   = $method->getNamespace();
                    $name = $method->getName();
                    $name = empty($ns) ? $name : $ns . '.' . $name;

                    if (isset($table[$name])) {
                        require_once 'Zend/Amf/Server/Exception.php';
                        throw new Zend_Amf_Server_Exception('Duplicate method registered: ' . $name);
                    }
                    $table[$name] = $method;
                    continue;
                }
            }
        }
        $this->_table = $table;
    }

    /**
     * Raise a server fault
     *
     * Unimplemented
     *
     * @param  string|Exception $fault
     * @return void
     */
    public function fault($fault = null, $code = 404)
    {
    }

    /**
     * Returns a list of registered methods
     *
     * Returns an array of dispatchables (Zend_Server_Reflection_Function,
     * _Method, and _Class items).
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->_table;
    }

    /**
     * Set server persistence
     *
     * Unimplemented
     *
     * @param  mixed $mode
     * @return void
     */
    public function setPersistence($mode)
    {
    }

    /**
     * Load server definition
     *
     * Unimplemented
     *
     * @param  array $definition
     * @return void
     */
    public function loadFunctions($definition)
    {
    }

    /**
     * Map ActionScript classes to PHP classes
     *
     * @param  string $asClass
     * @param  string $phpClass
     * @return Zend_Amf_Server
     */
    public function setClassMap($asClass, $phpClass)
    {
        require_once 'Zend/Amf/Parse/TypeLoader.php';
        Zend_Amf_Parse_TypeLoader::setMapping($asClass, $phpClass);
        return $this;
    }

    /**
     * List all available methods
     *
     * Returns an array of method names.
     *
     * @return array
     */
    public function listMethods()
    {
        return array_keys($this->_table);
    }
}
