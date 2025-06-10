<?php

class com_wiris_util_telemetry_TelemetryServiceImpl implements com_wiris_util_sys_HttpConnectionListener, com_wiris_util_telemetry_TelemetryService{
	public function __construct($url, $apiKey, $deploymentId) {
		if(!php_Boot::$skip_constructor) {
		$this->url = $url;
		$this->apiKey = $apiKey;
		$this->deploymentId = $deploymentId;
		$this->messages = new _hx_array(array());
		$this->pendingRequests = new Hash();
		$this->requestData = new Hash();
		$this->requestMessages = com_wiris_util_telemetry_TelemetryServiceImpl::populateRequestErrors();
		$this->sender = new com_wiris_util_telemetry_Sender($deploymentId, null, $this->getUserId());
		$this->session = new com_wiris_util_telemetry_Session(null);
		$this->logger = new com_wiris_util_telemetry_TelemetryLoggerImpl($this, com_wiris_util_telemetry_LoggingLevel::$DEBUG);
	}}
	public function getSender() {
		return $this->sender;
	}
	public function onHTTPStatus($status, $id) {
		$connection = $this->pendingRequests->get($id);
		$reqData = $this->requestData->get($id);
		$text = $this->requestMessages->get(_hx_string_rec($status, "") . "");
		if($text === null) {
			$text = com_wiris_util_telemetry_TelemetryServiceImpl::$MESSAGE_UNKNOWN;
		}
		if($status >= com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_SERVER_ERROR || $status === com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_LIMIT_EXCEEDED) {
			$this->logger->log($text, com_wiris_util_telemetry_LoggingLevel::$WARNING);
			$connection->request(true);
		} else {
			if($status >= com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_CLIENT_ERROR) {
				if($status === com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_PAYLOAD_TOO_LARGE) {
					$messages = com_wiris_util_json_JSon::getArray($reqData->get(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_MESSAGES_KEY));
					{
						$_g = 0;
						while($_g < $messages->length) {
							$obj = $messages[$_g];
							++$_g;
							$message = $obj;
							$this->sendMessage($message);
							unset($obj,$message);
						}
					}
				}
				$this->pendingRequests->remove($id);
				$this->requestData->remove($id);
				$this->logger->log($text, com_wiris_util_telemetry_LoggingLevel::$ERROR);
			} else {
				if($status > com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_SUCCESS && $status < com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_REDIRECTION) {
					$this->pendingRequests->remove($id);
					$this->requestData->remove($id);
				}
			}
		}
	}
	public function onHTTPError($error, $service) {
	}
	public function onHTTPData($res, $id) {
	}
	public function makeRequestImpl($connection, $postData) {
		$connection->setPostData($postData);
		$connection->request(true);
	}
	public function makeRequest($body) {
		if($this->testMode) {
			$body->set(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_TEST_KEY, com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_TEST_VALUE);
		}
		$hash = $this->newHttpConnection();
		$id = _hx_array_get(com_wiris_util_type_HashUtils::getKeys($hash), 0);
		$connection = $hash->get($id);
		$serializedBody = com_wiris_util_json_JSon::encode($body);
		$this->makeRequestImpl($connection, $serializedBody);
		$this->requestData->set($id, $body);
		$this->pendingRequests->set($id, $connection);
	}
	public function newHttpConnection() {
		$hash = new Hash();
		$uuid = com_wiris_system_UUIDUtils::generateV4(null, null);
		$httpConnection = new com_wiris_util_sys_HttpConnection($this->url, $uuid, $this);
		$httpConnection->setHeader(com_wiris_util_telemetry_TelemetryServiceImpl::$HEADER_API_KEY_KEY, $this->apiKey);
		$httpConnection->setHeader(com_wiris_util_telemetry_TelemetryServiceImpl::$HEADER_CONTENT_TYPE_KEY, com_wiris_util_telemetry_TelemetryServiceImpl::$HEADER_CONTENT_TYPE_VALUE);
		$hash->set($uuid, $httpConnection);
		return $hash;
	}
	public function getBatch() {
		if($this->messages->length > 0) {
			$req = new Hash();
			$options = new Hash();
			$options->set("url", $this->url);
			$headers = new Hash();
			$headers->set(com_wiris_util_telemetry_TelemetryServiceImpl::$HEADER_API_KEY_KEY, $this->apiKey);
			$headers->set(com_wiris_util_telemetry_TelemetryServiceImpl::$HEADER_CONTENT_TYPE_KEY, com_wiris_util_telemetry_TelemetryServiceImpl::$HEADER_CONTENT_TYPE_VALUE);
			$body = new Hash();
			$body->set(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_MESSAGES_KEY, com_wiris_util_telemetry_Message::toHashArray($this->messages));
			$body->set(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_SENDER_KEY, $this->sender->toHash());
			$body->set(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_SESSION_KEY, $this->session->toHash());
			$serializedBody = com_wiris_util_json_JSon::encode($body);
			$req->set(com_wiris_util_telemetry_TelemetryServiceImpl::$OPTIONS_KEY, $options);
			$req->set(com_wiris_util_telemetry_TelemetryServiceImpl::$HEADERS_KEY, $headers);
			$req->set(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_KEY, $serializedBody);
			return $req;
		} else {
			return null;
		}
	}
	public function sendBatch($batch) {
		$body = new Hash();
		$body->set(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_MESSAGES_KEY, com_wiris_util_telemetry_Message::toHashArray($batch));
		$body->set(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_SENDER_KEY, $this->sender->toHash());
		$body->set(com_wiris_util_telemetry_TelemetryServiceImpl::$BODY_SESSION_KEY, $this->session->toHash());
		$this->makeRequest($body);
	}
	public function getUserId() {
		$localUserId = com_wiris_system_LocalStorage::getItem(com_wiris_util_telemetry_TelemetryServiceImpl::$LOCAL_USER_ID);
		if($localUserId === null) {
			$localUserId = com_wiris_system_UUIDUtils::generateV4(null, null);
			com_wiris_system_LocalStorage::setItem(com_wiris_util_telemetry_TelemetryServiceImpl::$LOCAL_USER_ID, $localUserId);
		}
		return $localUserId;
	}
	public function getBatchSize() {
		return ($this->getBaseSize() + $this->batchSize) * 1.1;
	}
	public function getBaseSize() {
		return strlen($this->sender->serialize()) + strlen($this->session->serialize());
	}
	public function sendMessage($message) {
		$messageSize = strlen($message->serialize());
		$totalSize = $messageSize + $this->getBaseSize();
		$actualSize = $messageSize + $this->getBatchSize();
		if($totalSize < com_wiris_util_telemetry_TelemetryServiceImpl::$BATCH_MAX_SIZE) {
			if($this->lazyMode) {
				if($actualSize > com_wiris_util_telemetry_TelemetryServiceImpl::$BATCH_MAX_SIZE) {
					$this->sendBatch($this->messages);
					com_wiris_util_type_Arrays::clear($this->messages);
					$this->session->incrementPageBy(1);
					$this->batchSize = 0;
				}
				$this->messages->push($message);
				$this->batchSize += $messageSize;
			} else {
				$array = new _hx_array(array());
				$array->push($message);
				$this->sendBatch($array);
			}
		}
	}
	public function setLazyMode($lazyMode) {
		$this->lazyMode = $lazyMode;
	}
	public function setTestMode($testMode) {
		$this->testMode = $testMode;
	}
	public $lazyMode = false;
	public $testMode = false;
	public $messageMaxSize;
	public $batchSize = 0;
	public $deploymentId;
	public $url;
	public $apiKey;
	public $logger;
	public $session;
	public $sender;
	public $messages;
	public $requestMessages;
	public $requestData;
	public $pendingRequests;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static $HEADER_API_KEY_KEY = "x-api-key";
	static $HEADER_ACCEPT_VERSION_KEY = "accept-version";
	static $HEADER_ACCEPT_VERSION_VALUE = "1.0.0";
	static $HEADER_CONTENT_TYPE_KEY = "Content-Type";
	static $HEADER_CONTENT_TYPE_VALUE = "application/json";
	static $BODY_MESSAGES_KEY = "messages";
	static $BODY_SENDER_KEY = "sender";
	static $BODY_SESSION_KEY = "session";
	static $BODY_TEST_KEY = "test";
	static $BODY_TEST_VALUE = "200";
	static $OPTIONS_KEY = "options";
	static $HEADERS_KEY = "headers";
	static $BODY_KEY = "body";
	static $STATUS_SERVER_ERROR = 500;
	static $STATUS_LIMIT_EXCEEDED = 429;
	static $STATUS_PAYLOAD_TOO_LARGE = 413;
	static $STATUS_CLIENT_ERROR = 400;
	static $STATUS_REDIRECTION = 300;
	static $STATUS_SUCCESS = 200;
	static $MESSAGE_UNKNOWN = "UNKNOWN ERROR";
	static $MESSAGE_SERVER_ERROR = "SERVER_ERROR";
	static $MESSAGE_LIMIT_EXCEEDED = "LIMIT EXCEEDED";
	static $MESSAGE_PAYLOAD_TOO_LARGE = "PAYLOAD TOO LARGE";
	static $MESSAGE_CLIENT_ERROR = "CLIENT ERROR";
	static $MESSAGE_REDIRECTION = "STATUS REDIRECTION";
	static $MESSAGE_SUCCESS = "SUCCESS";
	static $LOCAL_USER_ID = "localUserId";
	static $BATCH_MAX_SIZE = 1000000;
	static function populateRequestErrors() {
		$hash = new Hash();
		$hash->set(_hx_string_rec(com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_SUCCESS, "") . "", com_wiris_util_telemetry_TelemetryServiceImpl::$MESSAGE_SUCCESS);
		$hash->set(_hx_string_rec(com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_CLIENT_ERROR, "") . "", com_wiris_util_telemetry_TelemetryServiceImpl::$MESSAGE_CLIENT_ERROR);
		$hash->set(_hx_string_rec(com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_REDIRECTION, "") . "", com_wiris_util_telemetry_TelemetryServiceImpl::$MESSAGE_REDIRECTION);
		$hash->set(_hx_string_rec(com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_PAYLOAD_TOO_LARGE, "") . "", com_wiris_util_telemetry_TelemetryServiceImpl::$MESSAGE_PAYLOAD_TOO_LARGE);
		$hash->set(_hx_string_rec(com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_LIMIT_EXCEEDED, "") . "", com_wiris_util_telemetry_TelemetryServiceImpl::$MESSAGE_LIMIT_EXCEEDED);
		$hash->set(_hx_string_rec(com_wiris_util_telemetry_TelemetryServiceImpl::$STATUS_SERVER_ERROR, "") . "", com_wiris_util_telemetry_TelemetryServiceImpl::$MESSAGE_SERVER_ERROR);
		return $hash;
	}
	function __toString() { return 'com.wiris.util.telemetry.TelemetryServiceImpl'; }
}
