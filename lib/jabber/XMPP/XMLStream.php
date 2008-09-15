<?php
/**
 * XMPPHP: The PHP XMPP Library
 * Copyright (C) 2008  Nathanael C. Fritz
 * This file is part of SleekXMPP.
 * 
 * XMPPHP is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * XMPPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with XMPPHP; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   xmpphp 
 * @package	XMPPHP
 * @author	 Nathanael C. Fritz <JID: fritzy@netflint.net>
 * @author	 Stephan Wentz <JID: stephan@jabber.wentz.it>
 * @copyright  2008 Nathanael C. Fritz
 */

/** XMPPHP_Exception */
require_once 'Exception.php';

/** XMPPHP_XMLObj */
require_once 'XMLObj.php';

/** XMPPHP_Log */
require_once 'Log.php';

/**
 * XMPPHP XML Stream
 * 
 * @category   xmpphp 
 * @package	XMPPHP
 * @author	 Nathanael C. Fritz <JID: fritzy@netflint.net>
 * @author	 Stephan Wentz <JID: stephan@jabber.wentz.it>
 * @copyright  2008 Nathanael C. Fritz
 * @version	$Id$
 */
class XMPPHP_XMLStream {
	/**
	 * @var resource
	 */
	protected $socket;
	/**
	 * @var resource
	 */
	protected $parser;
	/**
	 * @var string
	 */
	protected $buffer;
	/**
	 * @var integer
	 */
	protected $xml_depth = 0;
	/**
	 * @var string
	 */
	protected $host;
	/**
	 * @var integer
	 */
	protected $port;
	/**
	 * @var string
	 */
	protected $stream_start = '<stream>';
	/**
	 * @var string
	 */
	protected $stream_end = '</stream>';
	/**
	 * @var boolean
	 */
	protected $disconnected = false;
	/**
	 * @var boolean
	 */
	protected $sent_disconnect = false;
	/**
	 * @var array
	 */
	protected $ns_map = array();
	/**
	 * @var array
	 */
	protected $current_ns = array();
	/**
	 * @var array
	 */
	protected $xmlobj = null;
	/**
	 * @var array
	 */
	protected $nshandlers = array();
	/**
	 * @var array
	 */
	protected $idhandlers = array();
	/**
	 * @var array
	 */
	protected $eventhandlers = array();
	/**
	 * @var integer
	 */
	protected $lastid = 0;
	/**
	 * @var string
	 */
	protected $default_ns;
	/**
	 * @var string
	 */
	protected $until = '';
	/**
	 * @var array
	 */
	protected $until_happened = false;
	/**
	 * @var array
	 */
	protected $until_payload = array();
	/**
	 * @var XMPPHP_Log
	 */
	protected $log;
	/**
	 * @var boolean
	 */
	protected $reconnect = true;
	/**
	 * @var boolean
	 */
	protected $been_reset = false;
	/**
	 * @var boolean
	 */
	protected $is_server;
	/**
	 * @var float
	 */
	protected $last_send = 0;

	/**
	 * Constructor
	 *
	 * @param string  $host
	 * @param string  $port
	 * @param boolean $printlog
	 * @param string  $loglevel
	 * @param boolean $is_server
	 */
	public function __construct($host = null, $port = null, $printlog = false, $loglevel = null, $is_server = false) {
		$this->reconnect = !$is_server;
		$this->is_server = $is_server;
		$this->host = $host;
		$this->port = $port;
		$this->setupParser();
		$this->log = new XMPPHP_Log($printlog, $loglevel);
	}

	/**
	 * Destructor
	 * Cleanup connection
	 */
	public function __destruct() {
		if(!$this->disconnected && $this->socket) {
			$this->disconnect();
		}
	}
	
	/**
	 * Return the log instance
	 *
	 * @return XMPPHP_Log
	 */
	public function getLog() {
		return $this->log;
	}
	
	/**
	 * Get next ID
	 *
	 * @return integer
	 */
	public function getId() {
		$this->lastid++;
		return $this->lastid;
	}

	/**
	 * Add ID Handler
	 *
	 * @param integer $id
	 * @param string  $pointer
	 * @param string  $obj
	 */
	public function addIdHandler($id, $pointer, $obj = null) {
		$this->idhandlers[$id] = array($pointer, $obj);
	}

	/**
	 * Add Handler
	 *
	 * @param integer $id
	 * @param string  $ns
	 * @param string  $pointer
	 * @param string  $obj
	 * @param integer $depth
	 */
	public function addHandler($name, $ns, $pointer, $obj = null, $depth = 1) {
		$this->nshandlers[] = array($name,$ns,$pointer,$obj, $depth);
	}

	/**
	 * Add Evemt Handler
	 *
	 * @param integer $id
	 * @param string  $pointer
	 * @param string  $obj
	 */
	public function addEventHandler($name, $pointer, $obj) {
		$this->eventhanders[] = array($name, $pointer, $obj);
	}

	/**
	 * Connect to XMPP Host
	 *
	 * @param integer $timeout
	 * @param boolean $persistent
	 * @param boolean $sendinit
	 */
	public function connect($timeout = 30, $persistent = false, $sendinit = true) {
		$this->disconnected = false;
		$this->sent_disconnect = false;
		if($persistent) {
			$conflag = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
		} else {
			$conflag = STREAM_CLIENT_CONNECT;
		}
		$this->log->log("Connecting to tcp://{$this->host}:{$this->port}");
		try {
			$this->socket = @stream_socket_client("tcp://{$this->host}:{$this->port}", $errno, $errstr, $timeout, $conflag);
		} catch (Exception $e) {
			throw new XMPPHP_Exception($e->getMessage());
		}
		if(!$this->socket) {
			$this->log->log("Could not connect.",  XMPPHP_Log::LEVEL_ERROR);
			$this->disconnected = true;
			
			throw new XMPPHP_Exception('Could not connect.');
		}
		stream_set_blocking($this->socket, 1);
		if($sendinit) $this->send($this->stream_start);
	}

	/**
	 * Reconnect XMPP Host
	 */
	public function doReconnect() {
		if(!$this->is_server) {
			$this->log->log("Reconnecting...",  XMPPHP_Log::LEVEL_WARNING);
			$this->connect(30, false, false);
			$this->reset();
		}
	}

	/**
	 * Disconnect from XMPP Host
	 */
	public function disconnect() {
		$this->log->log("Disconnecting...",  XMPPHP_Log::LEVEL_VERBOSE);
		$this->reconnect = false;
		$this->send($this->stream_end);
		$this->sent_disconnect = true;
		$this->processUntil('end_stream', 5);
		$this->disconnected = true;
	}

	/**
	 * Are we are disconnected?
	 *
	 * @return boolean
	 */
	public function isDisconnected() {
		return $this->disconnected;
	}

	private function __process() {
		$read = array($this->socket);
		$write = null;
		$except = null;
		$updated = @stream_select($read, $write, $except, 1);
		if ($updated > 0) {
			$buff = @fread($this->socket, 1024);
			if(!$buff) { 
				if($this->reconnect) {
					$this->doReconnect();
				} else {
					fclose($this->socket);
					return false;
				}
			}
			$this->log->log("RECV: $buff",  XMPPHP_Log::LEVEL_VERBOSE);
			xml_parse($this->parser, $buff, false);
		}
	}
	
	/**
	 * Process
	 *
	 * @return string
	 */
	public function process() {
		$updated = '';
		while(!$this->disconnect) {
			$this->__process();
		}
	}

	/**
	 * Process until a timeout occurs
	 *
	 * @param integer $timeout
	 * @return string
	 */
	public function processTime($timeout = -1) {
		$start = time();
		$updated = '';
		while(!$this->disconnected and ($timeout == -1 or time() - $start < $timeout)) {
			$this->__process();
		}
	}

	/**
	 * Process until a specified event or a timeout occurs
	 *
	 * @param string|array $event
	 * @param integer $timeout
	 * @return string
	 */
	public function processUntil($event, $timeout=-1) {
		$start = time();
		if(!is_array($event)) $event = array($event);
		$this->until[] = $event;
		end($this->until);
		$event_key = key($this->until);
		reset($this->until);
		$updated = '';
		while(!$this->disconnected and $this->until[$event_key] and (time() - $start < $timeout or $timeout == -1)) {
			$this->__process();
		}
		if(array_key_exists($event_key, $this->until_payload)) {
			$payload = $this->until_payload[$event_key];
		} else {
			$payload = array();
		}
		unset($this->until_payload[$event_key]);
		return $payload;
	}

	/**
	 * Obsolete?
	 */
	public function Xapply_socket($socket) {
		$this->socket = $socket;
	}

	/**
	 * XML start callback
	 * 
	 * @see xml_set_element_handler
	 *
	 * @param resource $parser
	 * @param string   $name
	 */
	public function startXML($parser, $name, $attr) {
		if($this->been_reset) {
			$this->been_reset = false;
			$this->xml_depth = 0;
		}
		$this->xml_depth++;
		if(array_key_exists('XMLNS', $attr)) {
			$this->current_ns[$this->xml_depth] = $attr['XMLNS'];
		} else {
			$this->current_ns[$this->xml_depth] = $this->current_ns[$this->xml_depth - 1];
			if(!$this->current_ns[$this->xml_depth]) $this->current_ns[$this->xml_depth] = $this->default_ns;
		}
		$ns = $this->current_ns[$this->xml_depth];
		foreach($attr as $key => $value) {
			if(strstr($key, ":")) {
				$key = explode(':', $key);
				$key = $key[1];
				$this->ns_map[$key] = $value;
			}
		}
		if(!strstr($name, ":") === false)
		{
			$name = explode(':', $name);
			$ns = $this->ns_map[$name[0]];
			$name = $name[1];
		}
		$obj = new XMPPHP_XMLObj($name, $ns, $attr);
		if($this->xml_depth > 1) {
			$this->xmlobj[$this->xml_depth - 1]->subs[] = $obj;
		}
		$this->xmlobj[$this->xml_depth] = $obj;
	}

	/**
	 * XML end callback
	 * 
	 * @see xml_set_element_handler
	 *
	 * @param resource $parser
	 * @param string   $name
	 */
	public function endXML($parser, $name) {
		#$this->log->log("Ending $name",  XMPPHP_Log::LEVEL_DEBUG);
		#print "$name\n";
		if($this->been_reset) {
			$this->been_reset = false;
			$this->xml_depth = 0;
		}
		$this->xml_depth--;
		if($this->xml_depth == 1) {
			#clean-up old objects
			$found = false;
			foreach($this->nshandlers as $handler) {
				if($handler[4] != 1 and $this->xmlobj[2]->hasSub($handler[0])) {
					$searchxml = $this->xmlobj[2]->sub($handler[0]);
				} elseif(is_array($this->xmlobj) and array_key_exists(2, $this->xmlobj)) {
					$searchxml = $this->xmlobj[2];
				}
				if($searchxml !== null and $searchxml->name == $handler[0] and ($searchxml->ns == $handler[1] or (!$handler[1] and $searchxml->ns == $this->default_ns))) {
					if($handler[3] === null) $handler[3] = $this;
					$this->log->log("Calling {$handler[2]}",  XMPPHP_Log::LEVEL_DEBUG);
					$handler[3]->$handler[2]($this->xmlobj[2]);
				}
			}
			foreach($this->idhandlers as $id => $handler) {
				if(array_key_exists('id', $this->xmlobj[2]->attrs) and $this->xmlobj[2]->attrs['id'] == $id) {
					if($handler[1] === null) $handler[1] = $this;
					$handler[1]->$handler[0]($this->xmlobj[2]);
					#id handlers are only used once
					unset($this->idhandlers[$id]);
					break;
				}
			}
			if(is_array($this->xmlobj)) {
				$this->xmlobj = array_slice($this->xmlobj, 0, 1);
				if(isset($this->xmlobj[0]) && $this->xmlobj[0] instanceof XMPPHP_XMLObj) {
					$this->xmlobj[0]->subs = null;
				}
			}
			unset($this->xmlobj[2]);
		}
		if($this->xml_depth == 0 and !$this->been_reset) {
			if(!$this->disconnected) {
				if(!$this->sent_disconnect) {
					$this->send($this->stream_end);
				}
				$this->disconnected = true;
				$this->sent_disconnect = true;
				fclose($this->socket);
				if($this->reconnect) {
					$this->doReconnect();
				}
			}
			$this->event('end_stream');
		}
	}

	/**
	 * XML character callback
	 * @see xml_set_character_data_handler
	 *
	 * @param resource $parser
	 * @param string   $data
	 */
	public function charXML($parser, $data) {
		if(array_key_exists($this->xml_depth, $this->xmlobj)) {
			$this->xmlobj[$this->xml_depth]->data .= $data;
		}
	}

	/**
	 * Event?
	 *
	 * @param string $name
	 * @param string $payload
	 */
	public function event($name, $payload = null) {
		$this->log->log("EVENT: $name",  XMPPHP_Log::LEVEL_DEBUG);
		foreach($this->eventhandlers as $handler) {
			if($name == $handler[0]) {
				if($handler[2] === null) {
					$handler[2] = $this;
				}
				$handler[2]->$handler[1]($payload);
			}
		}
		foreach($this->until as $key => $until) {
			if(is_array($until)) {
				if(in_array($name, $until)) {
					$this->until_payload[$key][] = array($name, $payload);
					$this->until[$key] = false;
				}
			}
		}
	}

	/**
	 * Read from socket
	 */
	public function read() {
		$buff = @fread($this->socket, 1024);
		if(!$buff) { 
			if($this->reconnect) {
				$this->doReconnect();
			} else {
				fclose($this->socket);
				return false;
			}
		}
		$this->log->log("RECV: $buff",  XMPPHP_Log::LEVEL_VERBOSE);
		xml_parse($this->parser, $buff, false);
	}

	/**
	 * Send to socket
	 *
	 * @param string $msg
	 */
	public function send($msg, $rec=false) {
		if($this->time() - $this->last_send < .1) {
			usleep(100000);
		}
		$wait = true;
		while($wait) {
			$read = null;
			$write = array($this->socket);
			$except = null;
			$select = @stream_select($read, $write, $except, 0, 0);
			if($select === False) {
				$this->doReconnect();
				return false;
			} elseif ($select > 0) {
				$wait = false;
			} else {
				usleep(100000);
				//$this->processTime(.25);
			}
		}
		$sentbytes = @fwrite($this->socket, $msg, 1024);
		$this->last_send = $this->time();
		$this->log->log("SENT: " . mb_substr($msg, 0, $sentbytes, '8bit'),  XMPPHP_Log::LEVEL_VERBOSE);
		if($sentbytes === FALSE) {
			$this->doReconnect();
		} elseif ($sentbytes != mb_strlen($msg, '8bit')) {
			$this->send(mb_substr($msg, $sentbytes, mb_strlen($msg, '8bit'), '8bit'), true);
		}
	}

	public function time() {
		list($usec, $sec) = explode(" ", microtime());
		return (float)$sec + (float)$usec;
	}

	/**
	 * Reset connection
	 */
	public function reset() {
		$this->xml_depth = 0;
		unset($this->xmlobj);
		$this->xmlobj = array();
		$this->setupParser();
		if(!$this->is_server) {
			$this->send($this->stream_start);
		}
		$this->been_reset = true;
	}

	/**
	 * Setup the XML parser
	 */
	public function setupParser() {
		$this->parser = xml_parser_create('UTF-8');
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, 'startXML', 'endXML');
		xml_set_character_data_handler($this->parser, 'charXML');
	}
}
