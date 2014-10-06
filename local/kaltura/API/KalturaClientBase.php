<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================

/**
 * @package Kaltura
 * @subpackage Client
 */
class MultiRequestSubResult implements ArrayAccess
{
    function __construct($value)
	{
        $this->value = $value;
	}

    function __toString()
	{
        return '{' . $this->value . '}';
	}

    function __get($name)
	{
        return new MultiRequestSubResult($this->value . ':' . $name);
	}
	
	public function offsetExists($offset)
	{
		return true;
	}

	public function offsetGet($offset)
	{
        return new MultiRequestSubResult($this->value . ':' . $offset);
	}

	public function offsetSet($offset, $value)
	{
	}
	
	public function offsetUnset($offset)
	{
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaNull
{
	private static $instance;

	private function __construct()
	{

	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	function __toString()
	{
        return '';
	}

}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaClientBase
{
	const KALTURA_SERVICE_FORMAT_JSON = 1;
	const KALTURA_SERVICE_FORMAT_XML  = 2;
	const KALTURA_SERVICE_FORMAT_PHP  = 3;

	// KS V2 constants
	const RANDOM_SIZE = 16;

	const FIELD_EXPIRY =              '_e';
	const FIELD_TYPE =                '_t';
	const FIELD_USER =                '_u';

	const METHOD_POST 	= 'POST';
	const METHOD_GET 	= 'GET';

	/**
	 * @var string
	 */
	protected $apiVersion = null;

	/**
	 * @var KalturaConfiguration
	 */
	protected $config;

	/**
	 * @var string
	 */
	private $ks;

	/**
	 * @var boolean
	 */
	private $shouldLog = false;

	/**
	 * @var bool
	 */
	private $isMultiRequest = false;

	/**
	 * @var unknown_type
	 */
	private $callsQueue = array();

	/**
	 * Array of all plugin services
	 *
	 * @var array<KalturaServiceBase>
	 */
	protected $pluginServices = array();

	/**
	* @var Array of response headers
	*/
	private $responseHeaders = array();
	
	/**
	 * path to save served results
	 * @var string
	 */
	protected $destinationPath = null;
	
	/**
	 * return served results without unserializing them
	 * @var boolean
	 */
	protected $returnServedResult = null;

	public function __get($serviceName)
	{
		if(isset($this->pluginServices[$serviceName]))
			return $this->pluginServices[$serviceName];

		return null;
	}

	/**
	 * Kaltura client constructor
	 *
	 * @param KalturaConfiguration $config
	 */
	public function __construct(KalturaConfiguration $config)
	{
	    $this->config = $config;

	    $logger = $this->config->getLogger();
		if ($logger)
		{
			$this->shouldLog = true;
		}

		// load all plugins
		$pluginsFolder = realpath(dirname(__FILE__)) . '/KalturaPlugins';
		if(is_dir($pluginsFolder))
		{
			$dir = dir($pluginsFolder);
			while (false !== $fileName = $dir->read())
			{
				$matches = null;
				if(preg_match('/^([^.]+).php$/', $fileName, $matches))
				{
					require_once("$pluginsFolder/$fileName");

					$pluginClass = $matches[1];
					if(!class_exists($pluginClass) || !in_array('IKalturaClientPlugin', class_implements($pluginClass)))
						continue;

					$plugin = call_user_func(array($pluginClass, 'get'), $this);
					if(!($plugin instanceof IKalturaClientPlugin))
						continue;

					$pluginName = $plugin->getName();
					$services = $plugin->getServices();
					foreach($services as $serviceName => $service)
					{
						$service->setClient($this);
						$this->pluginServices[$serviceName] = $service;
					}
				}
			}
		}
	}

	/* Store response headers into array */
	public function readHeader($ch, $string)
	{
		array_push($this->responseHeaders, $string);
		return strlen($string);
	}

	/* Retrive response headers */
	public function getResponseHeaders()
	{
		return $this->responseHeaders;
	}

	public function getServeUrl()
	{
		if (count($this->callsQueue) != 1)
			return null;

		$params = array();
		$files = array();
		$this->log("service url: [" . $this->config->serviceUrl . "]");

		// append the basic params
		$this->addParam($params, "apiVersion", $this->apiVersion);
		$this->addParam($params, "format", $this->config->format);
		$this->addParam($params, "clientTag", $this->config->clientTag);

		$call = $this->callsQueue[0];
		$this->resetRequest();

		$params = array_merge($params, $call->params);
		$signature = $this->signature($params);
		$this->addParam($params, "kalsig", $signature);

		$url = $this->config->serviceUrl . "/api_v3/index.php?service={$call->service}&action={$call->action}";
		$url .= '&' . http_build_query($params);
		$this->log("Returned url [$url]");
		return $url;
	}

	public function queueServiceActionCall($service, $action, $params = array(), $files = array())
	{
		// in start session partner id is optional (default -1). if partner id was not set, use the one in the config
		if ((!isset($params["partnerId"]) || $params["partnerId"] === -1) && !is_null($this->config->partnerId))
			$params["partnerId"] = $this->config->partnerId;

		$this->addParam($params, "ks", $this->ks);

		$call = new KalturaServiceActionCall($service, $action, $params, $files);
		$this->callsQueue[] = $call;
	}

	protected function resetRequest()
	{
		$this->destinationPath = null;
		$this->returnServedResult = false;
		$this->isMultiRequest = false;
		$this->callsQueue = array();
	}

	/**
	 * Call all API service that are in queue
	 *
	 * @return unknown
	 */
	public function doQueue()
	{
		if($this->isMultiRequest && ($this->destinationPath || $this->returnServedResult))
		{
			$this->resetRequest();
			throw new KalturaClientException("Downloading files is not supported as part of multi-request.", KalturaClientException::ERROR_DOWNLOAD_IN_MULTIREQUEST);
		}
		
		if (count($this->callsQueue) == 0)
		{
			$this->resetRequest();
			return null;
		}

		$startTime = microtime(true);

		$params = array();
		$files = array();
		$this->log("service url: [" . $this->config->serviceUrl . "]");

		// append the basic params
		$this->addParam($params, "apiVersion", $this->apiVersion);
		$this->addParam($params, "format", $this->config->format);
		$this->addParam($params, "clientTag", $this->config->clientTag);
		$this->addParam($params, "ignoreNull", true);

		$url = $this->config->serviceUrl."/api_v3/index.php?service=";
		if ($this->isMultiRequest)
		{
			$url .= "multirequest";
			$i = 1;
			foreach ($this->callsQueue as $call)
			{
				$callParams = $call->getParamsForMultiRequest($i);
				$callFiles = $call->getFilesForMultiRequest($i);
				$params = array_merge($params, $callParams);
				$files = array_merge($files, $callFiles);
				$i++;
			}
		}
		else
		{
			$call = $this->callsQueue[0];
			$url .= $call->service."&action=".$call->action;
			$params = array_merge($params, $call->params);
			$files = $call->files;
		}

		$signature = $this->signature($params);
		$this->addParam($params, "kalsig", $signature);

		try
		{
			list($postResult, $error) = $this->doHttpRequest($url, $params, $files);
		}
		catch(Exception $e)
		{
			$this->resetRequest();
			throw $e;
		}

		if ($error)
		{
			$this->resetRequest();
			throw new KalturaClientException($error, KalturaClientException::ERROR_GENERIC);
		}
		else
		{
			// print server debug info to log
			$serverName = null;
			$serverSession = null;
			foreach ($this->responseHeaders as $curHeader)
			{
				$splittedHeader = explode(':', $curHeader, 2);
				if ($splittedHeader[0] == 'X-Me')
					$serverName = trim($splittedHeader[1]);
				else if ($splittedHeader[0] == 'X-Kaltura-Session')
					$serverSession = trim($splittedHeader[1]);
			}
			if (!is_null($serverName) || !is_null($serverSession))
				$this->log("server: [{$serverName}], session: [{$serverSession}]");
			
			$this->log("result (serialized): " . $postResult);

			if($this->returnServedResult)
			{
				$result = $postResult;
			}
			elseif($this->destinationPath)
			{
				if(!$postResult)
				{
					$this->resetRequest();
					throw new KalturaClientException("failed to download file", KalturaClientException::ERROR_READ_FAILED);
				}
			}
			elseif ($this->config->format == self::KALTURA_SERVICE_FORMAT_PHP)
			{
				$result = @unserialize($postResult);

				if ($result === false && serialize(false) !== $postResult)
				{
					$this->resetRequest();
					throw new KalturaClientException("failed to unserialize server result\n$postResult", KalturaClientException::ERROR_UNSERIALIZE_FAILED);
				}
				$dump = print_r($result, true);
				$this->log("result (object dump): " . $dump);
			}
			else
			{
				$this->resetRequest();
				throw new KalturaClientException("unsupported format: $postResult", KalturaClientException::ERROR_FORMAT_NOT_SUPPORTED);
			}
		}
		$this->resetRequest();

		$endTime = microtime (true);

		$this->log("execution time for [".$url."]: [" . ($endTime - $startTime) . "]");

		return $result;
	}

	/**
	 * Sign array of parameters
	 *
	 * @param array $params
	 * @return string
	 */
	private function signature($params)
	{
		ksort($params);
		$str = "";
		foreach ($params as $k => $v)
		{
			$str .= $k.$v;
		}
		return md5($str);
	}

	/**
	 * Send http request by using curl (if available) or php stream_context
	 *
	 * @param string $url
	 * @param parameters $params
	 * @return array of result and error
	 */
	protected function doHttpRequest($url, $params = array(), $files = array())
	{
		if (function_exists('curl_init'))
			return $this->doCurl($url, $params, $files);
			
		if($this->destinationPath || $this->returnServedResult)
			throw new KalturaClientException("Downloading files is not supported with stream context http request, please use curl.", KalturaClientException::ERROR_DOWNLOAD_NOT_SUPPORTED);
				
		return $this->doPostRequest($url, $params, $files);
	}

	/**
	 * Curl HTTP POST Request
	 *
	 * @param string $url
	 * @param array $params
	 * @param array $files
	 * @return array of result and error
	 */
	private function doCurl($url, $params = array(), $files = array())
	{
		$opt = http_build_query($params, null, "&");
		// Force POST in case we have files
		if(count($files) > 0) {
			$this->config->method = self::METHOD_POST;
		}
		// Check for GET and append params to url
		if( $this->config->method == self::METHOD_GET ) {
			$url = $url . '&' . $opt;
		}
		$this->responseHeaders = array();
		$cookies = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if( $this->config->method == self::METHOD_POST ) {
			curl_setopt($ch, CURLOPT_POST, 1);
			if (count($files) > 0)
			{
				foreach($files as &$file)
					$file = "@".$file; // let curl know its a file
				curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($params, $files));
			}
			else
			{
				$this->log("curl: $url&$opt");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $opt);
			}
		}
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_USERAGENT, $this->config->userAgent);
		if (count($files) > 0)
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
		else
			curl_setopt($ch, CURLOPT_TIMEOUT, $this->config->curlTimeout);

		if ($this->config->startZendDebuggerSession === true)
		{
			$zendDebuggerParams = $this->getZendDebuggerParams($url);
		 	$cookies = array_merge($cookies, $zendDebuggerParams);
		}

		if (count($cookies) > 0)
		{
			$cookiesStr = http_build_query($cookies, null, '; ');
			curl_setopt($ch, CURLOPT_COOKIE, $cookiesStr);
		}

		if (isset($this->config->proxyHost)) {
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch, CURLOPT_PROXY, $this->config->proxyHost);
			if (isset($this->config->proxyPort)) {
				curl_setopt($ch, CURLOPT_PROXYPORT, $this->config->proxyPort);
			}
			if (isset($this->config->proxyUser)) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->config->proxyUser.':'.$this->config->proxyPassword);
			}
			if (isset($this->config->proxyType) && $this->config->proxyType === 'SOCKS5') {
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
			}
		}

		// Set SSL verification
		if(!$this->getConfig()->verifySSL)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		}
		elseif($this->getConfig()->sslCertificatePath)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_CAINFO, $this->getConfig()->sslCertificatePath);
		}

		// Set custom headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->config->requestHeaders );

		// Save response headers
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'readHeader') );

		$destinationResource = null;
		if($this->destinationPath)
		{
			$destinationResource = fopen($this->destinationPath, "wb");
			curl_setopt($ch, CURLOPT_FILE, $destinationResource);
		}
		else
		{
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}
		
		$result = curl_exec($ch);
		
		if($destinationResource)
			fclose($destinationResource);
			
		$curlError = curl_error($ch);
		curl_close($ch);
		return array($result, $curlError);
	}

	/**
	 * HTTP stream context request
	 *
	 * @param string $url
	 * @param array $params
	 * @return array of result and error
	 */
	private function doPostRequest($url, $params = array(), $files = array())
	{
		if (count($files) > 0)
			throw new KalturaClientException("Uploading files is not supported with stream context http request, please use curl.", KalturaClientException::ERROR_UPLOAD_NOT_SUPPORTED);

		$formattedData = http_build_query($params , "", "&");
		$this->log("post: $url&$formattedData");

		$params = array('http' => array(
					"method" => "POST",
					"User-Agent: " . $this->config->userAgent . "\r\n".
					"Accept-language: en\r\n".
					"Content-type: application/x-www-form-urlencoded\r\n",
					"content" => $formattedData
		          ));

		if (isset($this->config->proxyType) && $this->config->proxyType === 'SOCKS5') {
			throw new KalturaClientException("Cannot use SOCKS5 without curl installed.", KalturaClientException::ERROR_CONNECTION_FAILED);
		}
		if (isset($this->config->proxyHost)) {
			$proxyhost = 'tcp://' . $this->config->proxyHost;
			if (isset($this->config->proxyPort)) {
				$proxyhost = $proxyhost . ":" . $this->config->proxyPort;
			}
			$params['http']['proxy'] = $proxyhost;
			$params['http']['request_fulluri'] = true;
			if (isset($this->config->proxyUser)) {
				$auth = base64_encode($this->config->proxyUser.':'.$this->config->proxyPassword);
				$params['http']['header'] = 'Proxy-Authorization: Basic ' . $auth;
			}
		}

		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			$phpErrorMsg = "";
			throw new KalturaClientException("Problem with $url, $phpErrorMsg", KalturaClientException::ERROR_CONNECTION_FAILED);
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
		   throw new KalturaClientException("Problem reading data from $url, $phpErrorMsg", KalturaClientException::ERROR_READ_FAILED);
		}
		return array($response, '');
	}

	/**
	 * @return string
	 */
	public function getKs()
	{
		return $this->ks;
	}

	/**
	 * @param string $ks
	 */
	public function setKs($ks)
	{
		$this->ks = $ks;
	}

	/**
	 * @param boolean $returnServedResult
	 */
	public function setReturnServedResult($returnServedResult)
	{
		$this->returnServedResult = $returnServedResult;
	}

	/**
	 * @return boolean
	 */
	public function getReturnServedResult()
	{
		return $this->returnServedResult;
	}

	/**
	 * @param string $destinationPath
	 */
	public function setDestinationPath($destinationPath)
	{
		$this->destinationPath = $destinationPath;
	}

	/**
	 * @return string
	 */
	public function getDestinationPath()
	{
		return $this->destinationPath;
	}

	/**
	 * @return KalturaConfiguration
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @param KalturaConfiguration $config
	 */
	public function setConfig(KalturaConfiguration $config)
	{
		$this->config = $config;

		$logger = $this->config->getLogger();
		if ($logger instanceof IKalturaLogger)
		{
			$this->shouldLog = true;
		}
	}

	/**
	 * Add parameter to array of parameters that is passed by reference
	 *
	 * @param arrat $params
	 * @param string $paramName
	 * @param string $paramValue
	 */
	public function addParam(&$params, $paramName, $paramValue)
	{
		if ($paramValue === null)
			return;

		if ($paramValue instanceof KalturaNull) {
			$params[$paramName . '__null'] = '';
			return;
		}

		if(is_object($paramValue) && $paramValue instanceof KalturaObjectBase)
		{
			$this->addParam($params, "$paramName:objectType", get_class($paramValue));
		    foreach($paramValue as $prop => $val)
				$this->addParam($params, "$paramName:$prop", $val);

			return;
		}

		if(!is_array($paramValue))
		{
			$params[$paramName] = (string)$paramValue;
			return;
		}

		if ($paramValue)
		{
			foreach($paramValue as $subParamName => $subParamValue)
				$this->addParam($params, "$paramName:$subParamName", $subParamValue);
		}
		else
		{
			$this->addParam($params, "$paramName:-", "");
		}
	}

	/**
	 * Validate the result object and throw exception if its an error
	 *
	 * @param object $resultObject
	 */
	public function throwExceptionIfError($resultObject)
	{
		if ($this->isError($resultObject))
		{
			throw new KalturaException($resultObject["message"], $resultObject["code"]);
		}
	}

	/**
	 * Checks whether the result object is an error
	 *
	 * @param object $resultObject
	 */
	public function isError($resultObject)
	{
		return (is_array($resultObject) && isset($resultObject["message"]) && isset($resultObject["code"]));
	}

	/**
	 * Validate that the passed object type is of the expected type
	 *
	 * @param any $resultObject
	 * @param string $objectType
	 */
	public function validateObjectType($resultObject, $objectType)
	{
		if (is_object($resultObject))
		{
			if (!($resultObject instanceof $objectType))
				throw new KalturaClientException("Invalid object type", KalturaClientException::ERROR_INVALID_OBJECT_TYPE);
		}
		else if (gettype($resultObject) !== "NULL" && gettype($resultObject) !== $objectType)
		{
			throw new KalturaClientException("Invalid object type", KalturaClientException::ERROR_INVALID_OBJECT_TYPE);
		}
	}

	public function startMultiRequest()
	{
		$this->isMultiRequest = true;
	}

	public function doMultiRequest()
	{
		return $this->doQueue();
	}

	public function isMultiRequest()
	{
		return $this->isMultiRequest;
	}

	public function getMultiRequestQueueSize()
	{
		return count($this->callsQueue);
	}

    public function getMultiRequestResult()
	{
        return new MultiRequestSubResult($this->getMultiRequestQueueSize() . ':result');
	}

	/**
	 * @param string $msg
	 */
	protected function log($msg)
	{
		if ($this->shouldLog)
			$this->config->getLogger()->log($msg);
	}

	/**
	 * Return a list of parameter used to a new start debug on the destination server api
	 * @link http://kb.zend.com/index.php?View=entry&EntryID=434
	 * @param $url
	 */
	protected function getZendDebuggerParams($url)
	{
		$params = array();
		$passThruParams = array('debug_host',
			'debug_fastfile',
			'debug_port',
			'start_debug',
			'send_debug_header',
			'send_sess_end',
			'debug_jit',
			'debug_stop',
			'use_remote');

		foreach($passThruParams as $param)
		{
			if (isset($_COOKIE[$param]))
				$params[$param] = $_COOKIE[$param];
		}

		$params['original_url'] = $url;
		$params['debug_session_id'] = microtime(true); // to create a new debug session

		return $params;
	}

	public function generateSession($adminSecretForSigning, $userId, $type, $partnerId, $expiry = 86400, $privileges = '')
	{
		$rand = rand(0, 32000);
		$expiry = time()+$expiry;
		$fields = array (
			$partnerId ,
			$partnerId ,
			$expiry ,
			$type,
			$rand ,
			$userId ,
			$privileges
		);
		$info = implode ( ";" , $fields );

		$signature = $this->hash ( $adminSecretForSigning , $info );
		$strToHash =  $signature . "|" . $info ;
		$encoded_str = base64_encode( $strToHash );

		return $encoded_str;
	}

	public static function generateSessionV2($adminSecretForSigning, $userId, $type, $partnerId, $expiry, $privileges)
	{
		// build fields array
		$fields = array();
		foreach (explode(',', $privileges) as $privilege)
		{
			$privilege = trim($privilege);
			if (!$privilege)
				continue;
			if ($privilege == '*')
				$privilege = 'all:*';
			$splittedPrivilege = explode(':', $privilege, 2);
			if (count($splittedPrivilege) > 1)
				$fields[$splittedPrivilege[0]] = $splittedPrivilege[1];
			else
				$fields[$splittedPrivilege[0]] = '';
		}
		$fields[self::FIELD_EXPIRY] = time() + $expiry;
		$fields[self::FIELD_TYPE] = $type;
		$fields[self::FIELD_USER] = $userId;

		// build fields string
		$fieldsStr = http_build_query($fields, '', '&');
		$rand = '';
		for ($i = 0; $i < self::RANDOM_SIZE; $i++)
			$rand .= chr(rand(0, 0xff));
		$fieldsStr = $rand . $fieldsStr;
		$fieldsStr = sha1($fieldsStr, true) . $fieldsStr;

		// encrypt and encode
		$encryptedFields = self::aesEncrypt($adminSecretForSigning, $fieldsStr);
		$decodedKs = "v2|{$partnerId}|" . $encryptedFields;
		return str_replace(array('+', '/'), array('-', '_'), base64_encode($decodedKs));
	}

	protected static function aesEncrypt($key, $message)
	{
		return mcrypt_encrypt(
			MCRYPT_RIJNDAEL_128,
			substr(sha1($key, true), 0, 16),
			$message,
			MCRYPT_MODE_CBC,
			str_repeat("\0", 16)	// no need for an IV since we add a random string to the message anyway
		);
	}

	private function hash ( $salt , $str )
	{
		return sha1($salt.$str);
	}

	/**
	 * @return KalturaNull
	 */
	public static function getKalturaNullValue()
	{

        return KalturaNull::getInstance();
	}

}

/**
 * @package Kaltura
 * @subpackage Client
 */
interface IKalturaClientPlugin
{
	/**
	 * @return KalturaClientPlugin
	 */
	public static function get(KalturaClient $client);

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices();

	/**
	 * @return string
	 */
	public function getName();
}

/**
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaClientPlugin implements IKalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{

	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaServiceActionCall
{
	/**
	 * @var string
	 */
	public $service;

	/**
	 * @var string
	 */
	public $action;


	/**
	 * @var array
	 */
	public $params;

	/**
	 * @var array
	 */
	public $files;

	/**
	 * Contruct new Kaltura service action call, if params array contain sub arrays (for objects), it will be flattened
	 *
	 * @param string $service
	 * @param string $action
	 * @param array $params
	 * @param array $files
	 */
	public function __construct($service, $action, $params = array(), $files = array())
	{
		$this->service = $service;
		$this->action = $action;
		$this->params = $this->parseParams($params);
		$this->files = $files;
	}

	/**
	 * Parse params array and sub arrays (for objects)
	 *
	 * @param array $params
	 */
	public function parseParams(array $params)
	{
		$newParams = array();
		foreach($params as $key => $val)
		{
			if (is_array($val))
			{
				$newParams[$key] = $this->parseParams($val);
			}
			else
			{
				$newParams[$key] = $val;
			}
		}
		return $newParams;
	}

	/**
	 * Return the parameters for a multi request
	 *
	 * @param int $multiRequestIndex
	 */
	public function getParamsForMultiRequest($multiRequestIndex)
	{
		$multiRequestParams = array();
		$multiRequestParams[$multiRequestIndex.":service"] = $this->service;
		$multiRequestParams[$multiRequestIndex.":action"] = $this->action;
		foreach($this->params as $key => $val)
		{
			$multiRequestParams[$multiRequestIndex.":".$key] = $val;
		}
		return $multiRequestParams;
	}

		/**
	 * Return the parameters for a multi request
	 *
	 * @param int $multiRequestIndex
	 */
	public function getFilesForMultiRequest($multiRequestIndex)
	{
		$multiRequestParams = array();
		foreach($this->files as $key => $val)
		{
			$multiRequestParams[$multiRequestIndex.":".$key] = $val;
		}
		return $multiRequestParams;
	}
}

/**
 * Abstract base class for all client services
 *
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaServiceBase
{
	/**
	 * @var KalturaClient
	 */
	protected $client;

	/**
	 * Initialize the service keeping reference to the KalturaClient
	 *
	 * @param KalturaClient $client
	 */
	public function __construct(KalturaClient $client = null)
	{
		$this->client = $client;
	}

	/**
	 * @param KalturaClient $client
	 */
	public function setClient(KalturaClient $client)
	{
		$this->client = $client;
	}
}

/**
 * Abstract base class for all client objects
 *
 * @package Kaltura
 * @subpackage Client
 */
abstract class KalturaObjectBase
{
	public function __construct($params = array())
	{
		foreach ($params as $key => $value)
		{
			if (!property_exists($this, $key))
				throw new KalturaClientException("property [{$key}] does not exist on object [".get_class($this)."]", KalturaClientException::ERROR_INVALID_OBJECT_FIELD);
			$this->$key = $value;
		}
	}

	protected function addIfNotNull(&$params, $paramName, $paramValue)
	{
		if ($paramValue !== null)
		{
			if($paramValue instanceof KalturaObjectBase)
			{
				$params[$paramName] = $paramValue->toParams();
			}
			else
			{
				$params[$paramName] = $paramValue;
			}
		}
	}

	public function toParams()
	{
		$params = array();
		$params["objectType"] = get_class($this);
	    foreach($this as $prop => $val)
		{
			$this->addIfNotNull($params, $prop, $val);
		}
		return $params;
	}
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaException extends Exception
{
    public function __construct($message, $code)
    {
    	$this->code = $code;
		parent::__construct($message);
    }
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaClientException extends Exception
{
	const ERROR_GENERIC = -1;
	const ERROR_UNSERIALIZE_FAILED = -2;
	const ERROR_FORMAT_NOT_SUPPORTED = -3;
	const ERROR_UPLOAD_NOT_SUPPORTED = -4;
	const ERROR_CONNECTION_FAILED = -5;
	const ERROR_READ_FAILED = -6;
	const ERROR_INVALID_PARTNER_ID = -7;
	const ERROR_INVALID_OBJECT_TYPE = -8;
	const ERROR_INVALID_OBJECT_FIELD = -9;
	const ERROR_DOWNLOAD_NOT_SUPPORTED = -10;
	const ERROR_DOWNLOAD_IN_MULTIREQUEST = -11;
}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaConfiguration
{
	private $logger;

	public $serviceUrl    				= "http://www.kaltura.com/";
	public $partnerId    				= null;
	public $format        				= 3;
	public $clientTag 	  				= "php5:14-01-16";
	public $curlTimeout   				= 120;
	public $userAgent					= '';
	public $startZendDebuggerSession 	= false;
	public $proxyHost                   = null;
	public $proxyPort                   = null;
	public $proxyType                   = 'HTTP';
	public $proxyUser                   = null;
	public $proxyPassword               = '';
	public $verifySSL 					= true;
	public $sslCertificatePath			= null;
	public $requestHeaders				= array();
	public $method						= KalturaClientBase::METHOD_POST;




	/**
	 * Constructs new Kaltura configuration object
	 *
	 */
	public function __construct($partnerId = -1)
	{
	    if (!is_null($partnerId) && !is_numeric($partnerId))
	        throw new KalturaClientException("Invalid partner id", KalturaClientException::ERROR_INVALID_PARTNER_ID);

	    $this->partnerId = $partnerId;
	}

	/**
	 * Set logger to get kaltura client debug logs
	 *
	 * @param IKalturaLogger $log
	 */
	public function setLogger(IKalturaLogger $log)
	{
		$this->logger = $log;
	}

	/**
	 * Gets the logger (Internal client use)
	 *
	 * @return IKalturaLogger
	 */
	public function getLogger()
	{
		return $this->logger;
	}
}

/**
 * Implement to get Kaltura Client logs
 *
 * @package Kaltura
 * @subpackage Client
 */
interface IKalturaLogger
{
	function log($msg);
}


