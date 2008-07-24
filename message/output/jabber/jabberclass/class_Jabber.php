<?php
/* Jabber Client Library
 * Version 0.9rc1
 * Copyright 2002-2007, Centova Technologies Inc.
 * http://www.centova.com
 *
 * Portions Copyright 2002, Carlo Zottmann
 * ============================================================================
 *
 * This file was contributed (in part or whole) by a third party, and is
 * released under the GNU LGPL.  Please see the CREDITS and LICENSE sections
 * below for details.
 * 
 *****************************************************************************
 *
 * DETAILS
 *
 * This is an event-driven Jabber client class implementation.  This library
 * allows PHP scripts to connect to and communicate with Jabber servers.
 *
 *
 * HISTORY
 *
 *	v0.9rc1 (Unreleased)
 *	     - Fixed problem with _split_incoming() method that would incorrectly
 *		   parse packets starting with reserved element names
 *		 - Added support for obtaining nicknames from vCard updates
 *		 - Added default value to constructor's debug argument
 *		 - Various minor code cleanups and typo fixes
 *		 - Added support for PHP 5
 *
 *	v0.8 - Internal release
 *	v0.7 - Internal release
 *	v0.6 - Internal release
 *	v0.5 - Initial port from class.jabber.php
 *
 *
 * CREDITS & COPYRIGHTS
 *
 * This class was originally based on Class.Jabber.PHP v0.4 (Copyright 2002,
 * Carlo "Gossip" Zottmann).
 *
 * The code for this class has since been nearly completely rewritten by Steve
 * Blinch for Centova Technologies Inc.  All such modified code is Copyright
 * 2002-2007, Centova Technologies Inc.
 *
 * The original Class.Jabber.PHP was released under the GNU General Public
 * License (GPL); however, we have received written permission from the
 * original author and copyright holder, Carlo Zottmann, to relicense our
 * version of this class and release it under the GNU Lesser General Public
 * License (LGPL).
 *
 *
 * LICENSE
 *
 * class_Jabber.php - Jabber Client Library
 * Copyright (C) 2002-2007, Centova Technologies Inc.
 * Copyright (C) 2002, Carlo Zottmann
 *
 * This library is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as published by the
 * Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version.
 * 
 * This library is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this library; if not, write to the Free Software Foundation,
 * Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 * JABBER is a registered trademark of Jabber Inc.
 *
 */

/*

The following events are available to be handled by user applications
(use ::set_handler($handler_name...) to assign a handler method to an event).

$this->_call_handler("authenticated");
$this->_call_handler("authfailure",-1,"No authentication method available","");
$this->_call_handler("deregistered",$this->jid);
$this->_call_handler("deregfailure",-2,"Unrecognized response from server");
$this->_call_handler("error",$code,$msg,$xmlns,$packet);
$this->_call_handler("heartbeat");
$this->_call_handler("message_chat",$from,$to,$body,$subject,$thread,$id,$extended,$packet);
$this->_call_handler("message_groupchat",$packet);
$this->_call_handler("message_headline",$from,$to,$body,$subject,$extended,$packet);
$this->_call_handler("message_normal",$from,$to,$body,$subject,$thread,$id,$extended,$packet);
$this->_call_handler("msgevent_composing_start",$from);
$this->_call_handler("msgevent_composing_stop",$from);
$this->_call_handler("msgevent_delivered",$from);
$this->_call_handler("msgevent_displayed",$from);
$this->_call_handler("msgevent_offline",$from);
$this->_call_handler("passwordchanged");
$this->_call_handler("passwordfailure",-2,"Unrecognized response from server");
$this->_call_handler("regfailure",-1,"Username already registered","");
$this->_call_handler("registered",$this->jid);
$this->_call_handler("rosteradded");
$this->_call_handler("rosteraddfailure",-2,"Unrecognized response from server");
$this->_call_handler("rosterremoved");
$this->_call_handler("rosterremovefailure",-2,"Unrecognized response from server");
$this->_call_handler("rosterupdate",$jid,$is_new);
$this->_call_handler("servicefields",&$fields,$packet_id,$reg_key,$reg_instructions,&$reg_x);
$this->_call_handler("servicefieldsfailure",-2,"Unrecognized response from server");
$this->_call_handler("serviceregfailure",-2,"Unrecognized response from server");
$this->_call_handler("serviceregistered",$jid);
$this->_call_handler('servicederegfailure",-2,"Unrecognized response from server");
$this->_call_handler('servicederegistered");
$this->_call_handler("serviceupdate",$jid,$is_new);
$this->_call_handler("terminated");
$this->_call_handler('connected');
$this->_call_handler('disconnected'); // called when the connection to the Jabber server is lost unexpectedly
$this->_call_handler('probe',$packet);
$this->_call_handler('stream_error',$packet);
$this->_call_handler('subscribe',$packet);
$this->_call_handler('subscribed',$packet);
$this->_call_handler('unsubscribe',$packet);
$this->_call_handler('unsubscribed',$packet);
$this->_call_handler("privatedata",$packetid,$namespace,$values);
$this->_call_handler('debug_log',$msg);
$this->_call_handler("contactupdated",$packetid);
$this->_call_handler("contactupdatefailure",-2,"Unrecognized response from server");

*/

require_once(dirname(__FILE__)."/class_ConnectionSocket.php");
require_once(dirname(__FILE__)."/class_XMLParser.php");

// Version string
define("CLASS_JABBER_VERSION","0.9rc1");

// Default connection timeout
define("DEFAULT_CONNECT_TIMEOUT",15);

// Default Jabber resource
define("DEFAULT_RESOURCE","JabberClass");

// Minimum/Maximum callback frequencies
define("MIN_CALLBACK_FREQ",1);	// more than once per second is dangerous
define("MAX_CALLBACK_FREQ",10); // less than once every 10 seconds will be very, very slow

// Make sure we have SHA1 support, one way or another, such that we can
// perform encrypted logins.
if (!function_exists('sha1')) {  // PHP v4.3.0+ supports sha1 internally

	if (function_exists('mhash')) { // is the Mhash extension installed?

		// implement the sha1() function using mhash
		function sha1($str) {
			return bin2hex(mhash(MHASH_SHA1, $str));
		}

	} else {

		// implement the sha1() function in native PHP using the SHA1Library class;
		// this is slow, but it's better than plaintext.
		require_once(dirname(__FILE__)."/class_SHA1Library.php");
	}
	
}

// Jabber communication class
class Jabber {
	var $jid				= "";
	var $use_msg_composing	= true;
	var $use_msg_delivered	= false;
	var $use_msg_displayed	= false;
	var $use_msg_offline	= false;

	var $_server_host		= "";
	var $_server_ip			= "";
	var $_server_port		= 5222;
	var $_connect_timeout	= DEFAULT_CONNECT_TIMEOUT;
	var $_username			= "";
	var $_password			= "";
	var $_resource			= "";

	var $_iq_version_name	= "class_Jabber.php - http://www.centova.com - Copyright 2003-2007, Centova Technologies Inc.";
	var $_iq_version_version= CLASS_JABBER_VERSION;
	//var	$_iq_version_os	= $_SERVER['SERVER_SOFTWARE'];

	var $_connector			= "ConnectionSocket";
	var $_authenticated		= false;
	
	var $_packet_queue		= array();
	var $_last_ping_time	= 0;

	var $_iq_handlers		= array();
	var $_event_handlers	= array();
	
	var $execute_loop		= true;
	
	// DEBUGGING ONLY - causes the log file to be closed/flushed after each write
	var $_log_flush			= true;

	// if true, roster updates generate only one "rosterupdate" event,
	// regardless of how many contacts were actually updated/added;
	// useful for the initial roster download
	var $roster_single_update = false;

	// if true, service updates generate only one "serviceupdate" event,
	// regardless of how many services were actually updated/added;
	// useful for retrieving a service list
	var $service_single_update = false;
	
	// if true, contacts without "@"'s in their name will be assumed
	// to be services and will not be listed in the roster; if the
	// corresponding JID is found in the $this->services array, its
	// "status" and "show" elements will be updated to reflect the
	// presence/availability of the service (and the "serviceupdate"
	// event will be fired)
	var $handle_services_internally = false;

	// If true, the server software name and version will automatically be queried
	// and stored in $this->server_software and $this->server_version at login
	var $auto_server_identify = true;
	
	var $server_software = "";
	var $server_version = "";
	var $server_os = "";
	
	var $protocol_version = false; // set this to an XMPP protocol revision to include it in the <stream:stream> tag
	
	// Constructor
	function Jabber($enable_logging = false) {
		$this->_use_log = $enable_logging;
		$this->_unique_counter = 0;
		$this->_log_open();
		
		$this->xml = &new XMLParser();
	}

	// ==== General Methods ==================================================================
	
	// set a handler method for a specific Jabber event; valid handler names
	// are listed at the top of this script
	function set_handler($handler_name,&$handler_object,$method_name) {
		$this->_event_handlers[$handler_name] = array(&$handler_object,$method_name);
	}
	
	function set_handler_object(&$handler_object,$handlers) {
		foreach ($handlers as $handler_name=>$method_name) {
			$this->set_handler(
				$handler_name,
				$handler_object,
				$method_name
			);
		}
	}
	
	// same as above, but accepts a plain ol' function instead of a method
	function set_handler_function($handler_name,$method_name) {
		$this->_event_handlers[$handler_name] = $method_name;
	}
	
	// calls the specified handler with the specified parameters; accepts:
	//
	// $handler_name - the name of the handler (as defined with ::set_handler())
	//                 to call
	// (optional) other parameters - the parameters to pass to the handler method
	function _call_handler() {

		$numargs = func_num_args(); 
		if ($numargs<1) return false;

		$arg_list = func_get_args(); 
		$handler_name = array_shift($arg_list);

		if (($handler_name!="debug_log") && ($handler_name!="heartbeat")) $this->_log("Calling handler: $handler_name");
		
		
		if ($this->_event_handlers[$handler_name]) {
			
			// ---- REMOVE THIS AFTER BENCHMARKING! ----
			if (defined('JX_HANDLER_BENCHMARK')) $GLOBALS["jxeh"]->t_start('event.'.$handler_name);
			// ---- REMOVE THIS AFTER BENCHMARKING! ----
			
			call_user_func_array(&$this->_event_handlers[$handler_name],$arg_list);
			
			// ---- REMOVE THIS AFTER BENCHMARKING! ----
			if (defined('JX_HANDLER_BENCHMARK')) $GLOBALS["jxeh"]->t_end('event.'.$handler_name);
			// ---- REMOVE THIS AFTER BENCHMARKING! ----
			
		}
	}
	
	// posix platforms support usleep(), to sleep for a specific number of
	// microseonds; we use that when possible, as it allows for a more responsive
	// interface
	function posix_sleep() {
		$micro_seconds = 250000;
		usleep($micro_seconds);
		
		return round($micro_seconds/1000000,2);
	}
	
	// Windows doesn't support usleep(), so we have to sleep for minimum 1-second
	// intervals.. this makes the interface a bit more sluggish, but allows for 
	// Win32 compatibility
	function win32_sleep() {
		$secs = 1;
		sleep($secs);
		
		return $secs;
	}
	

	// returns a unique ID to be sent with packets
	function _unique_id($prefix) {
		$this->_unique_counter++;
		return $prefix."_" . md5(time() . $_SERVER['REMOTE_ADDR'] . $this->_unique_counter);
	}
	
	// public method for creating a log entry
	function log($msg,$level=1) {
		$this->_log($msg,$level);
	}
	
	// private method for creating a log entry
	function _log($msg,$level = 1) {
		if ($this->_use_log) {
			if ($this->_log_flush) $this->_log_file = @fopen(dirname(__FILE__)."/log/logfile.txt","a");
			
			if ($this->_log_file) {
				fwrite($this->_log_file,"$msg\n");
				if ($this->_log_flush) fclose($this->_log_file);
			}

			$this->_call_handler("debug_log",$msg,$level);
		}
	}
	
	// debug method for creating a log entry (for ease of commenting-out :) )
	function dlog($msg,$level=1) {
		$this->_log($msg,$level);
	}

	
	function _log_open() {
		if ($this->_use_log) {
			$this->_log_file = @fopen(dirname(__FILE__)."/log/logfile.txt","w");
			if ($this->_log_file && $this->_log_flush) {
				fclose($this->_log_file);
			}
		}
	}

	function _log_close() {
		if ($this->_use_log && $this->_log_file && !$this->_log_flush) {
			fclose($this->_log_file);
		}
	}

	// splits a JID into its three components; returns an array
	// of (username,domain,resource)
	function _split_jid($jid) {
		preg_match("/(([^\@]+)\@)?([^\/]+)(\/(.*))?$/",$jid,$matches);
		return array($matches[2],$matches[3],$matches[5]);
	}
	
	function _bare_jid($jid) {
		list($u_username,$u_domain,$u_resource) = $this->_split_jid($jid);
		return ($u_username?$u_username."@":"").$u_domain;
	}

	

	// ==== Core Jabber Methods ==============================================================

	// Connects to the specified Jabber server.
	//
	// Returns true if the socket was opened, otherwise false.
	// A "connected" event is also fired when the server responds to our <stream> packet.
	//
	// $server_host     - Hostname of your Jabber server (portion after the "@" in your JID)
	// $server_port     - Port for your Jabber server
	// $connect_timeout - Maximum number of seconds to wait for a connection
	// $alternate_ip    - If $server_host does not resolve to your Jabber server's IP,
	//                    specify the correct IP to connect to here
	//	
	//
	function connect($server_host,$server_port=5222,$connect_timeout=null,$alternate_ip=false) {
		
		if (is_null($connect_timeout)) $connect_timeout = DEFAULT_CONNECT_TIMEOUT;
		$connector = $this->_connector;
		
		$this->_connection = &new $connector();
		$this->_server_host = $server_host;
		$this->_server_port = $server_port;
		$this->_server_ip = $alternate_ip ? $alternate_ip : $server_host;
		$this->_connect_timeout = $connect_timeout;
		
		$this->roster = array();
		$this->services = array();
		
		$this->_is_win32 = (substr(strtolower(php_uname()),0,3)=="win");
		$this->_sleep_func = $this->_is_win32 ? "win32_sleep" : "posix_sleep";
		
		return $this->_connect_socket();
	}
	
	function _connect_socket() {
		$this->log('connecting: '.$this->_server_ip.' '.$this->_server_port.' '.$this->_connect_timeout);
		if ($this->_connection->socket_open($this->_server_ip,$this->_server_port,$this->_connect_timeout)) {
			$this->_send("<?xml version='1.0' encoding='UTF-8' ?" . ">\n");
			
			$xmpp_version = ($this->protocol_version) ? " version='{$this->protocol_version}'" : '';
			
			$this->_send("<stream:stream to='{$this->_server_host}' xmlns='jabber:client' xmlns:stream='http://etherx.jabber.org/streams'{$xmpp_version}>\n");
			return true;
		} else {
			$this->error = $this->_connection->error;
			return false;
		}
	}
	
	// disconnect from the server
	function disconnect() {
		
		$this->_send("</stream:stream>");
		
		$this->_log_close();
		return $this->_connection->socket_close();
	}
	
	
	// logs in to the server
	function login($username,$password,$resource=NULL) {
		if (!$username || !$password) return false;
		
		// setup handler to automatically respond to the request
		$auth_id	= $this->_unique_id("auth");
		$this->_set_iq_handler("_on_authentication_methods",$auth_id,"result");
		$this->_set_iq_handler("_on_authentication_result",$auth_id,"error");
		
		// prepare our shiny new JID
		$this->_username = $username;
		$this->_password = $password;
		$this->_resource = !is_null($resource) ? $resource : DEFAULT_RESOURCE;
		$this->jid		= "{$this->_username}@{$this->_server_host}/{$this->_resource}";
		
		// request available authentication methods
		$payload	= "<username>{$this->_username}</username>";
		$packet		= $this->_send_iq(NULL, 'get', $auth_id, "jabber:iq:auth", $payload);
		
		return true;
	}
	
	// browse the services (transports) available on the server
	function browse() {
		$browse_id = $this->_unique_id("browse");
		$this->_set_iq_handler("_on_browse_result",$browse_id);
		
		return $this->_send_iq($this->_server_host, 'get', $browse_id, "jabber:iq:browse");
	}
	
	// retrieve the user's roster from the jabber server
	function get_roster() {
		$roster_id = $this->_unique_id("roster");
		$this->_set_iq_handler("_on_roster_result",$roster_id);
		
		return $this->_send_iq(NULL, 'get', $roster_id, "jabber:iq:roster");
	}
	
	// sets a user's presence (when simply used to set your availability, it's more convenient
	// to call this way, as usually only the first 2 fields are necessary)
	function set_presence($show = NULL, $status = NULL, $to = NULL, $priority = NULL) {
		return $this->send_presence(NULL,$to,$show,$status,$priority);
	}
	
	// sends presence to another contact/entity
	function send_presence($type = NULL, $to = NULL, $show = NULL, $status = NULL, $priority = NULL) {
		$xml = "<presence";
		$xml .= ($to) ? " to='$to'" : '';
		$xml .= ($type) ? " type='$type'" : '';
		$xml .= ($status || $show || $priority) ? ">\n" : " />\n";

		$xml .= ($status) ? "	<status>$status</status>\n" : '';
		$xml .= ($show) ? "	<show>$show</show>\n" : '';
		$xml .= ($priority) ? "	<priority>$priority</priority>\n" : '';

		$xml .= ($status || $show || $priority) ? "</presence>\n" : '';

		if ($this->_send($xml)) {
			return true;
		} else {
			$this->_log("ERROR: send_presence() #1");
			return false;
		}
		
	}
	
	// indicate (to another contact) that the user is composing a message
	function composing($to,$id,$start=true) {
		$payload = "<x xmlns='jabber:x:event'><composing/>".($start?"<id>$id</id>":"")."</x>";
		return $this->message($to,"normal",NULL,NULL,NULL,NULL,$payload);
	}
	
	function xmlentities($string, $quote_style=ENT_QUOTES) {
		return htmlspecialchars($string,$quote_style);
		
	   $trans = get_html_translation_table(HTML_ENTITIES, $quote_style);
	   foreach ($trans as $key => $value)
	       $trans[$key] = '&#'.ord($key).';';
	   return strtr($string, $trans);
	}	
	
	function message($to, $type = "normal", $id = NULL, $body = NULL, $thread = NULL, $subject = NULL, $payload = NULL, $raw = false) {
		if ($to && ($body || $subject || $payload)) {
			if (!$id) $id = $this->_unique_id("msg");

//			$body = htmlspecialchars($body);
//			$subject = htmlspecialchars($subject);

			if (!$raw) {
				$body = $this->xmlentities($body);
				$subject = $this->xmlentities($subject);
				$thread = $this->xmlentities($thread);
			}

			//$body = str_replace("&ccedil;","&#0231;",$body);

			$xml = "<message to='$to' type='$type' id='$id'>\n";

			if ($subject)	$xml .= "<subject>$subject</subject>\n";
			if ($thread)	$xml .= "<thread>$thread</thread>\n";
			if ($body)		$xml .= "<body>$body</body>\n";
			
			if ($body || $subject) {
				$jabber_x_event = "";
				if ($this->use_msg_composing) $jabber_x_event .= "<composing/>";
				if ($this->use_msg_delivered) $jabber_x_event .= "<delivered/>";
				if ($this->use_msg_displayed) $jabber_x_event .= "<displayed/>";
				if ($this->use_msg_offline) $jabber_x_event .= "<offline/>";
				if ($jabber_x_event) $xml .= "<x xmlns='jabber:x:event'>$jabber_x_event</x>";
			}
						
			$xml .= $payload;
			$xml .= "</message>\n";

			if ($this->_send($xml)) {
				return true;
			} else {
				$this->_log("ERROR: message() #1");
				return false;
			}
		} else {
			$this->_log("ERROR: message() #2");
			return false;
		}
	}
	
	// create a new Jabber account
	function register($username, $password, $reg_email = NULL, $reg_name = NULL) {
		if (!$username || !$password) return false;
		
		$reg_id = $this->_unique_id("reg");
		$this->_set_iq_handler("_on_register_get_result",$reg_id);
		
		$this->_username = $this->xmlentities($username);
		$this->_password = $this->xmlentities($password);
		$this->_reg_email = $this->xmlentities($reg_email);
		$this->_reg_name = $this->xmlentities($reg_name);
		
		return $this->_send_iq($this->_server_host, 'get', $reg_id, 'jabber:iq:register');
	}
	
	// cancels an existing Jabber account, removing it from the server (careful!)
	//
	// Note: on jabberd 1.4.2 this always seems to return 503 Service Unavailable for me;
	// not sure if this is a problem with this method, a problem with my server, or a
	// problem with jabberd 1.4.2.
	function deregister() {
		$dereg_id = $this->_unique_id("dereg");
		$this->_set_iq_handler("_on_deregister_result",$dereg_id);

		$payload = "<remove/>";
		return $this->_send_iq($this->_server_host, 'set', $dereg_id, "jabber:iq:register", $payload, $this->jid);
	}
	
	
	// changes the user's password
	function change_password($newpassword) {
		if (!$newpassword) return false;
		
		$chg_id = $this->_unique_id("chg");
		$this->_set_iq_handler("_on_chgpassword_result",$chg_id);
		
		$newpassword = $this->xmlentities($newpassword);
		
		$payload = "<username>{$this->_username}</username><password>$newpassword</password>";
		return $this->_send_iq($this->_server_host, 'set', $chg_id, "jabber:iq:register", $payload);
	}
	
	// subscribes to an entity's presence ($request_message specifies the "reason for requesting subscription" message)
	function subscribe($to,$request_message=NULL) {
		return $this->send_presence("subscribe", $to, NULL, $request_message);
	}

	// unsubscribes from an entity's presence
	function unsubscribe($to) {
		return $this->send_presence("unsubscribe", $to);
	}
	
	// accepts a subscription request from an entity
	function subscription_request_accept($to) {
		return $this->send_presence("subscribed", $to);
	}

	// denies a subscription request from an entity
	function subscription_request_deny($to) {
		return $this->send_presence("unsubscribed", $to);
	}
	
	// get the registration fields for a service/transport
	function query_service_fields($transport)
	{
		$reg_id = $this->_unique_id("reg");
		$this->_set_iq_handler("_on_servicefields_result",$reg_id);
		
		if ($this->_send_iq($transport, 'get', $reg_id, "jabber:iq:register", NULL, $this->jid)) {
			return $reg_id;
		} else {
			return false;
		}
	}
	

	// register with a service/transport
	function register_service($transport,$reg_id,$reg_key = NULL,$fields)
	{
		if (!$transport || !$reg_id || !$fields) return false;
		
		$this->_set_iq_handler("_on_serviceregister_result",$reg_id);
		

		$payload = ($reg_key) ? "<key>$reg_key</key>\n" : '';
		foreach ($fields as $element => $value) {
			$payload .= "<$element>".$this->xmlentities($value)."</$element>\n";
		}

		return $this->_send_iq($transport, 'set', $reg_id, "jabber:iq:register", $payload);
	}
	
	function deregister_service($transport,$reg_id,$reg_key = NULL) {
		if (!$transport || !$reg_id) return false;

		$this->_set_iq_handler("_on_servicedereg_initial_result",$reg_id);

		$payload = "<remove/>";
		return $this->_send_iq($transport, 'set', $reg_id, "jabber:iq:register", $payload);
	}
	
	// adds a contact to the roster
	function roster_add($jid, $name = NULL, $group = NULL) {
		if (!$jid) return false;
		$add_id = $this->_unique_id("add");
		
		$this->_set_iq_handler("_on_rosteradd_result",$add_id);

		$payload = "<item jid='$jid'";
		$payload .= ($name) ? " name='" . $this->xmlentities($name) . "'" : '';
		$payload .= (($group) ? "><group>". $this->xmlentities($group). "</group>\n</item": "/") . ">\n";

		if ($this->_send_iq(NULL, 'set', $add_id, "jabber:iq:roster", $payload)) {
			return $add_id;
		} else {
			return false;
		}
	}
	
	function roster_remove($jid) {
		if (!$jid) return false;
		$rem_id = $this->_unique_id("remove");
		
		$this->_set_iq_handler("_on_rosterremove_result",$rem_id);

		$payload = "<item jid='$jid' subscription='remove'/>";

		if ($this->_send_iq(NULL, 'set', $rem_id, "jabber:iq:roster", $payload)) {
			return $rem_id;
		} else {
			return false;
		}
	}
	
	// updates a roster contact's name and/or group
	function roster_update($jid,$name = NULL,$group = NULL) {
		if (!$jid) return false;
		$update_id = $this->_unique_id("update");
		
		$this->_set_iq_handler("_on_rosterupdate_result",$update_id);

		$payload = "<item jid='$jid'";
		$payload .= ($name) ? " name='" . $this->xmlentities($name) . "'" : '';
		$payload .= (($group) ? "><group>". $this->xmlentities($group) . "</group>\n</item": "/") . ">\n";

		if ($this->_send_iq(NULL, 'set', $update_id, "jabber:iq:roster", $payload)) {
			return $add_id;
		} else {
			return false;
		}				
	}
	
	// adds a contact to the roster and subscribes to his presence in one step;
	// simply a time saver.
	function add_contact($jid,$name = NULL,$group = NULL) {
		if ($this->roster_add($jid,$name,$group)) {
			return $this->subscribe($jid);
		} else {
			return false;
		}
	}

	// alias for roster_remove()
	function remove_contact($jid) {
		return $this->roster_remove($jid);
	}
	
	function set_private_data($namespace,$rootelement,$values) {
		if ((!$namespace) || (!$rootelement) || (!$values)) return false;

		$data_id = $this->_unique_id("privdata");

//		$this->_set_iq_handler("_on_xxx_result",$data_id); // we don't really need the result from this... do we?

		$payload = "<$rootelement xmlns='$namespace'>";
		foreach ($values as $key=>$value) {
			$payload .= "<$key>$value</$key>";
		}
		$payload .= "</$rootelement>";

		if ($this->_send_iq(NULL, 'set', $data_id, "jabber:iq:private", $payload)) {
			return $data_id;
		} else {
			return false;
		}
	}

	function get_private_data($namespace,$rootelement) {
		if ((!$namespace) || (!$rootelement)) return false;

		$data_id = $this->_unique_id("privdata");
		$this->_set_iq_handler("_on_private_data",$data_id);

		$payload = "<$rootelement xmlns='$namespace' />";

		if ($this->_send_iq(NULL, 'get', $data_id, "jabber:iq:private", $payload)) {
			return $data_id;
		} else {
			return false;
		}
	}
	
	function adjust_callback_frequency($factor) {
		if ($this->active_cbk_freq<0) return;
		
		$this->dlog("Setting callback frequency factor to $factor");

		$this->active_cbk_freq = $this->initial_cbk_freq*$factor;

		$this->dlog("Setting active frequency to {$this->active_cbk_freq}");

		if ($this->active_cbk_freq<MIN_CALLBACK_FREQ) $this->active_cbk_freq = MIN_CALLBACK_FREQ;
		if ($this->active_cbk_freq>MAX_CALLBACK_FREQ) $this->active_cbk_freq = MAX_CALLBACK_FREQ;
	}

	
	// begin execution loop... sort of a ghetto-multithreading type thing, I guess... :)
	function execute($callback_freq = -1,$seconds = -1)
	{
		$sleepfunc = $this->_sleep_func;
		$this->active_cbk_freq = $this->initial_cbk_freq = $callback_freq;
		
		$count = 0;
		$cb_count = 0;
		
		// set terminated to true in any event handler to cause this method to exit immediately
		$this->terminated = false;

		while (($count != $seconds) && (!$this->terminated)) {
			
			// check to see if there are any packets waiting
			if ($this->_receive()) {
				
				while (count($this->_packet_queue)) {
					$packet = $this->_get_next_packet();
	
					// if a packet was available (should always be)
					if ($packet) {
						// check the packet type, and dispatch the appropriate handler
						if (!empty($packet['iq'])) {
							$this->_handle_iq($packet);
						} elseif (!empty($packet['message'])) {
							$this->_handle_message($packet);
						} elseif (!empty($packet['presence'])) {
							$this->_handle_presence($packet);
						} elseif (!empty($packet['stream:stream'])) {
							$this->_handle_stream($packet);
						} elseif (!empty($packet['stream:features'])) {
							$this->_handle_stream_features($packet);
						} elseif (!empty($packet['stream:error'])) {
							$this->_handle_stream_error($packet);
						} else {
							$this->_log("Unknown packet type!");
							$x = $this->dump($packet);
							$this->_log($x);
						}
					}
				}
			}

			
			$sleeptime = $this->$sleepfunc();

			$count += $sleeptime;
			$cb_count += $sleeptime;
			
			if ($this->_last_ping_time != date("H:i")) {
				if (!$this->_send(" ",true)) {
					// Lost connection to Jabber server!
					$this->_call_handler('disconnected');
					$this->terminated = true;
				}
				$this->_last_ping_time = date("H:i");
			}
			
			if (($this->active_cbk_freq>0) && ($cb_count>=$this->active_cbk_freq) && ($this->_authenticated)) {

				$this->dlog("Heartbeat - cbcount:{$cb_count} / active_cbk_freq:{$this->active_cbk_freq}");

				$this->_call_handler("heartbeat");
				$cb_count = 0; 	
			}
			
			if (!$this->execute_loop) break;
		}

		if ($this->execute_loop) $this->_call_handler("terminated");

		return TRUE;
	}








	// ==== Event Handlers (Raw Packets) =====================================================
	
	// Sets a handler for a particular IQ packet ID (and optionally packet type).
	// Assumes that $method is the name of a method of $this
	function _set_iq_handler($method,$id,$type=NULL) {
		if (is_null($type)) $type = "_all";
		$this->_iq_handlers[$id][$type] = array(&$this,$method);
	}
	
	
	function _node($packet,$path,$checkset = false) {
		$cursor = &$packet;
		
		$pathlength = count($path);
		for ($i=0; $i<$pathlength; $i++) {
			$last = ($i==$pathlength-1);
			
			$element = $path[$i];
			
			if (!is_array($cursor) || !isset($cursor[$element])) return ($checkset ? false : NULL);
			
			if ($last) {
				if ($checkset) {
					return isset($cursor[$element]);
				} else {
					return $cursor[$element];
				}
			} else {
				$cursor = &$cursor[$element];
			}
			
		}
		
		return ($checkset ? false : NULL);
	}
	
	function _nodeset($packet,$path) {
		return $this->_node($packet,$path,true);
	}
	
	// handle IQ packets
	function _handle_iq(&$packet) {
		$iq_id = $this->_node($packet,array('iq','@','id'));
		
		$iq_type = $this->_node($packet,array('iq','@','type'));
		
		// see if we already have a handler setup for this ID number; the vast majority of IQ
		// packets are handled by their ID number, since they are usually in response to a
		// request we submitted
		if ($this->_iq_handlers[$iq_id]) {
			
			// OK, is there a handler for this specific packet type as well?
			if ($this->_iq_handlers[$iq_id][$iq_type]) {
				// yup - try  the handler for our packet type
				$iqt = $iq_type;
			} else {
				// nope - try the catch-all handler
				$iqt = "_all";
			} 
			
			$this->dlog("Handling $iq_id [$iqt]");
			$handler_method = $this->_iq_handlers[$iq_id][$iqt];
			unset($this->_iq_handlers[$iq_id][$iqt]);
			
			if ($handler_method) {
				call_user_func($handler_method,&$packet);
			} else {
				$this->_log("Don't know what to do with packet: ".$this->dump($packet));
			}
		} else {
			// this packet didn't have an ID number (or the ID number wasn't recognized), so
			// see if we can salvage it.
			switch($iq_type) {
				case "get":
					if (!$this->_node($packet,array('iq','#','query'))) return;
					
					$xmlns = $this->_node($packet,array('iq','#','query',0,'@','xmlns'));
					switch($xmlns) {
						case "jabber:iq:version":
							// handle version inquiry/response
							$this->_handle_version_packet($packet);
							break;
						case "jabber:iq:time":
							// handle time inquiry/response
							$this->_handle_time_packet($packet);
							break;
						default:
							// unknown XML namespace; borkie borkie!
							break;
					}
					break;
					
				case "set": // handle <iq type="set"> packets
					if (!$this->_node($packet,array('iq','#','query'))) return;
					
					$xmlns = $this->_node($packet,array('iq','#','query',0,'@','xmlns'));
					switch($xmlns) {
						case "jabber:iq:roster":
							$this->_on_roster_result($packet);
							break;
						default:
							// unknown XML namespace; borkie borkie!
							break;
					}
					break;

				default:
					// don't know what to do with other types of IQ packets!
					break;

			}
		}
	}
	
	function varset($v) {
		return is_string($v) ? strlen($v)>0 : !empty($v);
	}
	
	// handle Message packets
	function _handle_message(&$packet) {
		// events that we recognize
		$events = array("composing","offine","delivered","displayed");
		
		// grab the message details
		$type = $this->_node($packet,array('message','@','type'));
		if (!$type) $type = "chat";

		$from = $this->_node($packet,array('message','@','from'));
		$to = $this->_node($packet,array('message','@','to'));
		$id = $this->_node($packet,array('message','@','id'));
		
		list($f_username,$f_domain,$f_resource) = $this->_split_jid($from);
		$from_jid = ($f_username?"{$f_username}@":"").$f_domain;
		
		$body = $this->_node($packet,array('message','#','body',0,'#'));
		$subject = $this->_node($packet,array('message','#','subject',0,'#'));
		$thread = $this->_node($packet,array('message','#','thread',0,'#'));
		
		// handle extended message info (to a certain extent, anyway)...
		// if any of the tags in $events are passed under an x element in the
		// jabber:x:event namespace, $extended[tagname] is set to TRUE
		$extended = false;
		$extended_id = NULL;
		$x = $this->_node($packet,array('message','#','x'));
		
		if (is_array($x)) {
			foreach ($x as $key=>$element) {
				if ($this->_node($element,array('@','xmlns'))=="jabber:x:event") {
					if ( !isset($element['#']) || !is_array($element['#']) ) continue;
					
					foreach ($element['#'] as $tag=>$element_content) {
						if (in_array($tag,$events)) {
							$extended[$tag] = true;
						}
						if ($tag=="id") {
							$extended_id = $this->_node($element_content,array('0','#'));
							if (!$extended) $extended = array();
						}
					}
				}
			}
		}
		
		// if a message contains an x tag in the jabber:x:event namespace,
		// and doesn't contain a body or subject, then it's an event notification
		if (!$this->varset($body) && !$this->varset($subject) && is_array($extended)) {
			
			// is this a composing event (which needs special handling)?
			if (isset($extended['composing'])) {
				$this->_call_handler("msgevent_composing_start",$from);
				$this->roster[$from_jid]["composing"] = true;
			} else {
				if ($this->roster[$from_jid]["composing"]) {
					$this->_call_handler("msgevent_composing_stop",$from);
					$this->roster[$from_jid]["composing"] = false;
				}
			}

			foreach ($extended as $event=>$value) {
				$this->_call_handler("msgevent_$event",$from);
			}
			
			// don't process the rest of the message event, as it's not really a message
			return;
		}
		
		
		// process the message
		switch($type) {
			case "error":
				$this->_handle_error(&$packet);
				break;
			case "groupchat":
				$this->_call_handler("message_groupchat",$packet);
				break;
			case "headline":
				$this->_call_handler("message_headline",$from,$to,$body,$subject,$x,$packet);
				break;
			case "chat":
			case "normal":
			default:
				if ($this->roster[$from_jid]["composing"]) $this->roster[$from_jid]["composing"] = false;
				if (($type!="chat") && ($type!="normal")) $type = "normal";
				$this->_call_handler("message_$type",$from,$to,$body,$subject,$thread,$id,$extended,$packet);
				break;
				
		}
	}
	
	// handle Presence packets
	function _handle_presence(&$packet) {
	
		$type = $this->_node($packet,array('presence','@','type'));
		if (!$type) $type = "available";

		$from = $this->_node($packet,array('presence','@','from'));
		
		list($f_username,$f_domain,$f_resource) = $this->_split_jid($from);
		$from_jid = ($f_username?"{$f_username}@":"").$f_domain;
		
		$is_service = (!strlen($f_username));

		$exists = ($is_service && $this->handle_services_internally) ? isset($this->services[$from_jid]) : isset($this->roster[$from_jid]);
		// $this->dlog("TRACE::_handle_presence() called with from=$from, exists=[$exists]");
		
		$nothing = false;
		$rosteritem = &$nothing;
		
		/*
		// Merak doesn't send roster items for gateway contacts for some reason - it just throws
		// presence packets at you all willy-nilly... so we simulate a roster update if a non-roster
		// presence packet is received and we've identified the server as Merak
		
		// This doesn't work, as internally Merak records the contacts as having no subscription,
		// but doesn't send any subscription requests to the client.  Craptacular.
		if (!$exists && $this->is_merak && !$is_service) {
			$this->roster[$from_jid] = array(
				"username"		=> $f_username,
				"domain"		=> $f_domain,
				"resource"		=> $f_resource,
				"jid"			=> $from_jid,
				"transport"		=> $this->get_transport($f_domain)
			);
			$exists = true;
		}
		*/
		
		if ($exists) {
			if ($is_service && $this->handle_services_internally) {
				// $this->dlog("SVC: rosteritem=service[{$from_jid}]");
				$use_services_array = true;
				$rosteritem = &$this->services[$from_jid];
			} else {
				// $this->dlog("SVC: rosteritem=roster[{$from_jid}]");
				$use_services_array = false;
				$rosteritem = &$this->roster[$from_jid];
				
				unset($rosteritem['customnickname']);
			}
		} else {
			// Ignore roster updates for JIDs not in our roster, except
			// for subscription requests...
			
			if ($type=="available") {
				// ... but make note of the presence of non-roster items here, in case
				// the roster item is sent AFTER the presence packet... then we can apply the
				// presence when the roster item is received
				$show = $this->_show($this->_node($packet,array('presence','#','show',0,'#')));
				$this->presence_cache[$from_jid] = array(
					"status"=>$this->_node($packet,array('presence','#','status',0,'#')),
					"show"=>$show ? $show : "on"
				);
				// $this->dlog("TRACE::_handle_presence(): Caching presence for [$from_jid]; type=available, status=[".$this->presence_cache[$from_jid]["status"]."], show=[".$this->presence_cache[$from_jid]["show"]."]");
				
				return;
			}
			
			if ($type!="subscribe") {
				// $this->dlog("TRACE::_handle_presence(): type!=subscribe; exiting _handle_presence()");
				return;
			}
			// $this->dlog("TRACE::_handle_presence(): type=subscribe; passing through");
		}
		$call_update = false;

		/*
		ob_start();
		echo "\n----PRESENCE----\n";
		echo "[$type]\n";
		var_dump($packet);
		echo "----END PRESENCE----\n\n";
		$y = ob_get_contents();
		$this->dlog($y);
		ob_end_clean();
		*/

		switch($type) {
			case "error":
				$this->_handle_error(&$packet);
				break;
			case "probe":
				$this->_call_handler('probe',$packet);
				break;
			case "subscribe":
				// note: $rosteritem is not set here
				$this->_call_handler('subscribe',$packet);
				break;
			case "subscribed":
				$this->_call_handler('subscribed',$packet);
				break;
			case "unsubscribe":
				$this->_call_handler('unsubscribe',$packet);
				break;
			case "unsubscribed":
				$this->_call_handler('unsubscribed',$packet);
				break;
			case "unavailable":
				// $this->dlog("NOTE: Setting rosteritem[status] for ".($use_services_array?"service":"roster item")." $from_jid to off (unavailable)");
				$rosteritem["show"] = "off";
				$call_update = true;
				break;
			case "available":
				$rosteritem["status"] = $this->_node($packet,array('presence','#','status',0,'#'));
				$show = $this->_show($this->_node($packet,array('presence','#','show',0,'#')));
				$rosteritem["show"] = $show ? $show : "on"; // away, chat, xa, dnd, or "" = online
				
				if ($this->_node($packet,array('presence','#','x',0,'@','xmlns'))=='vcard-temp:x:update') {
					$rosteritem['customnickname'] = $this->_node($packet,array('presence','#','x',0,'#','nickname',0,'#'));
				}

				// $this->dlog("NOTE: Setting rosteritem[status] for ".($use_services_array?"service":"roster item")." $from_jid to ".$rosteritem["status"]);
				$call_update = true;
				break;
			default:
				$this->_log("Unknown presence type: $type");
				break;
		}
		if ($call_update) {
			if ($use_services_array) {
				// $this->dlog("TRACE::_handle_presence(): calling serviceupdate for $from");
				$this->_call_handler("serviceupdate",$from,false);
			} else {
				// $this->dlog("TRACE::_handle_presence(): calling rosterupdate for $from");
				$this->_call_handler("rosterupdate",$from,false);
			}
		}
	}
	
	// handle Stream packets
	function _handle_stream(&$packet) {
		$ss = $this->_node($packet,array('stream:stream','@'));
		if (is_array($ss)) {
			if ($ss['from'] == $this->_server_host
				&& $ss['xmlns'] == "jabber:client"
				&& $ss["xmlns:stream"] == "http://etherx.jabber.org/streams")
			{
				$this->_stream_id = $this->_node($packet,array("stream:stream",'@','id'));
				$this->_call_handler('connected');
				return;
			}
		}

		$this->_log("Unrecognized stream packet");
		var_dump($packet);
	}

	// handle Stream features packets
	function _handle_stream_features(&$packet) {
		$this->features = &$packet;
	}
	
	// handle stream error
	function _handle_stream_error(&$packet) {
		$this->_call_handler('stream_error',$packet);
	}



	// ==== Event Handlers (Event Specific) ==================================================

	// receives a list of authentication methods and sends an authentication
	// request with the most appropriate one
	function _on_authentication_methods(&$packet) {
		$auth_id = $this->_node($packet,array('iq','@','id'));
		$auth_request_sent = true;
		
		// check for auth method availability in descending order (best to worst)
		
		// Note: As noted in JEP-0078 (http://www.jabber.org/jeps/jep-0078.html), the so-called
		// "zero-knowledge" authentication is no stronger than digest authentication, and is not
		// even documented in JEP-0078 anymore.  As such, it is not supported here.
		//
		// SASL authentication is not yet supported (for unrelated reasons).
		
		// digest
		if ($this->_nodeset($packet,array('iq','#','query',0,'#','digest'))) {
			$this->_sendauth_digest($auth_id);

		// plain text
		} elseif ($this->_node($packet,array('iq','#','query',0,'#','password'))) {
			$this->_sendauth_plaintext($auth_id);

		// no auth methods
		} else {
			$auth_request_sent = false;
			$this->_call_handler("authfailure",-1,"No authentication method available","");
			$this->_log("ERROR: _on_authentication_methods() #2 - No auth method available!");
		}
		
		if ($auth_request_sent) {
			$this->_set_iq_handler("_on_authentication_result",$auth_id);
		}
		
	}
	
	// receives the results of an authentication attempt
	function _on_authentication_result(&$packet) {
		$auth_id = $this->_node($packet,array('iq','@','id'));
		$result_type = $this->_node($packet,array('iq','@','type'));
		
		if ($result_type=="result") {
			if ($this->auto_server_identify) $this->request_version($this->_server_host);
			
			$this->_call_handler("authenticated");
			$this->_authenticated = true;
		} elseif ($result_type=="error") {
			$this->_handle_iq_error(&$packet,"authfailure");
		}
	}
	
	// receives the results of a service browse query
	function _on_browse_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		
//		$this->_log("BROWSE packet: ".var_export($packet,true));
		
		// did we get a result?  if so, process it, and remember the service list	
		if ($packet_type=="result") {
			
			$this->services = array();

			//$this->_log("SERVICES: ".print_r($packet,true));
			
			//$this->_log("\n\nSOFTWARE: ".$this->server_software." v".$this->server_version."\n\n");
			
			if ($this->_node($packet,array('iq','#','service'))) {
				// Jabberd uses the 'service' element
				$servicekey = $itemkey = 'service';
			} elseif ($this->_node($packet,array('iq','#','item'))) {
				// Older versions of Merak use 'item'
				$servicekey = $itemkey = 'item';
			} elseif ($this->_node($packet,array('iq','#','query'))) {
				// Newer versions of Merak use 'query'
				$servicekey = 'query';
				$itemkey = 'item';
			} else {
				// try to figure out what to use
				$k = array_keys($this->_node($packet,array('iq','#')));
				$servicekey = $k[0];
				if (!$servicekey) return;
			}
			// if the item key is incorrect, try to figure that out as well
			if ($this->_node($packet,array('iq','#',$servicekey)) && !$this->_node($packet,array('iq','#',$servicekey,0,'#',$itemkey))) {
				$k = array_keys($this->_node($packet,array('iq','#',$servicekey,0,'#')));
				$itemkey = $k[0];
			}
			
			$number_of_services = is_array($this->_node($packet,array('iq','#',$servicekey,0,'#',$itemkey))) ? count($this->_node($packet,array('iq','#',$servicekey,0,'#',$itemkey))) : 0;

			$services_updated = false;
			for ($a = 0; $a < $number_of_services; $a++)
			{
				$svc = $this->_node($packet,array('iq','#',$servicekey,0,'#',$itemkey,$a));

				$jid = strtolower($this->_node($svc,array('@','jid')));
				$is_new = !isset($this->services[$jid]);
				$this->services[$jid] = array(	
											"type"			=> strtolower($this->_node($svc,array('@','type'))),
											"status"		=> "Offline",
											"show"			=> "off",
											"name"			=> $this->_node($svc,array('@','name')),
											"namespaces"	=> array()
				);
				
				$number_of_namespaces = is_array($this->_node($packet,array('iq','#',$servicekey,0,'#',$itemkey,$a,'#','ns'))) ? count($this->_node($packet,array('iq','#',$servicekey,0,'#',$itemkey,$a,'#','ns'))) : 0;
				for ($b = 0; $b < $number_of_namespaces; $b++) {
						$this->services[$jid]['namespaces'][$b] = $this->_node($packet,array('iq','#',$servicekey,0,'#',$itemkey,$a,'#','ns',$b,'#'));
				}

				if ($this->service_single_update) {
					$services_updated = true;
				} else {
					$this->_call_handler("serviceupdate",$jid,$is_new);
				}
			}
			
			if ($this->service_single_update && $services_updated) {
				$this->_call_handler("serviceupdate",NULL,$is_new);
			}
			
			$this->_log("Received service list");
			//$this->_log("Received service list: ".print_r($this->services,true));
		// choke on error
		} elseif ($packet_type=="error") {
			$this->_handle_iq_error($packet);
			
		// confusion sets in
		} else {
			$this->_log("Don't know what to do with jabber:iq:browse packet!");
		}
	}
	
	// request software version from a JabberID
	function request_version($jid) {
		
		$this->_log('Requesting version information from '.$jid);
		// setup handler to automatically respond to the request (it would anyway,
		// because of how we handle version packets, but... hey, why not be thorough)
		$ver_id	= $this->_unique_id("ver");
		
		$this->_set_iq_handler("_handle_version_packet",$ver_id);

		return $this->_send_iq($jid, 'get', $ver_id, "jabber:iq:version");		
	}
	
	// handle a jabber:iq:version packet (either a request, or a response)
	function _handle_version_packet(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		$from = $this->_node($packet,array('iq','@','from'));
		$packetid = $this->_node($packet,array('iq','@','id'));

		if ($packet_type=="result") {
			// did we get a result?  if so, process it, and update the contact's version information
			$jid = $this->_bare_jid($from);
			
			$version = $this->_node($packet,array('iq','#','query',0,'#'));
			$this->_log("$jid/".$this->_server_host);
			if ($jid==$this->_server_host) {
				//$this->_log("\n\n\n\nVERSION: ".print_r($version,true)."\n\n\n\n".print_r($packet,true)."\n\n\n\n");
				$this->server_software = $this->_node($version,array('name',0,'#'));
				$this->server_version = $this->_node($version,array('version',0,'#'));
				$this->server_os = $this->_node($version,array('os',0,'#'));
				
				$this->is_merak = strtolower(substr($this->server_software,0,5))=="merak";
			} elseif ($this->roster[$jid]) {
				$this->roster[$jid]["version"] = $version;
			}

			// $this->dlog("TRACE::_handle_version_packet(): calling rosterupdate for $jid");
			$this->_call_handler("rosterupdate",$jid,false);

		} elseif ($packet_type=="get") {
			// did we get an inquiry?  if so, send our version info
			$payload	= "<name>{$this->_iq_version_name}</name><version>{$this->_iq_version_version}</version>";
			if ($this->_iq_version_os) $payload .= "<os>{$this->_iq_version_os}</os>";
			$packet		= $this->_send_iq($from, 'result', $packetid, "jabber:iq:version", $payload);
		}
		// other types of packets are probably just error responses (eg: the remote
		// client doesn't support jabber:iq:version requests) so we ignore those
		
		return true;
	}
	
	// handle a jabber:iq:time packet (either a request, or a response)
	function _handle_time_packet(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		$from = $this->_node($packet,array('iq','@','from'));
		$packetid = $this->_node($packet,array('iq','@','id'));

		if ($packet_type=="result") {
			// did we get a result?  if so, process it, and update the contact's time information
			$jid = $this->_bare_jid($from);
			
			$timeinfo = $this->_node($packet,array('iq','#','query',0,'#'));
			$this->roster[$jid]["time"] = $timeinfo;

			// $this->dlog("TRACE::_handle_time_packet(): calling rosterupdate for $jid");
			$this->_call_handler("rosterupdate",$jid,false);
		} elseif ($packet_type=="get") {
			// did we get an inquiry?  if so, send our time info
			$utc = gmdate('Ymd\TH:i:s');
			$tz = date("T");
			$display = date("D M d H:i:s Y");
			
			$payload	= "<utc>{$utc}</utc><tz>{$tz}</tz><display>{$display}</display>";
			$packet		= $this->_send_iq($from, 'result', $packetid, "jabber:iq:time", $payload);
		}
		// other types of packets are probably just error responses (eg: the remote
		// client doesn't support jabber:iq:time requests) so we ignore those
		
		return true;
	}
	
	// receives the results of a roster query
	//
	// Note: You should always browse services BEFORE calling get_roster(), as this
	// will ensure that the correct services get marked as "registered" in $this->services,
	// and each roster contact will automatically have its "transport" element set to the
	// correct transport.
	function _on_roster_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));

		// did we get a result?  if so, process it, and remember the service list	
		if (($packet_type=="result") || ($packet_type=="set")) {
			
			$roster_updated = false;

			$itemlist = $this->_node($packet,array('iq','#','query',0,'#','item'));
			$number_of_contacts = is_array($itemlist) ? count($itemlist) : 0;
			
			//echo "<pre>"; echo "itemlist:\n"; var_dump($itemlist); echo "</pre>";

			for ($a = 0; $a < $number_of_contacts; $a++)
			{
				if (!isset($itemlist[$a])) continue;
				
				$queryitem = &$itemlist[$a];
				//echo "<pre>"; echo "itemlist:\n"; var_dump($queryitem); echo "</pre>";
				$jid = strtolower($this->_node($queryitem,array('@','jid')));
				
				$subscription = $this->_node($queryitem,array('@','subscription'));
				
				
				list($u_username,$u_domain,$u_resource) = $this->_split_jid($jid);
				//echo "[$u_username/$u_domain/$u_resource/$jid]";
				$jid = ($u_username?"{$u_username}@":"").$u_domain;
				
				
				$is_new = !isset($this->roster[$jid]);
				
				//$x = $this->dump($this->roster[$jid]);
				// $this->dlog("TRACE::_on_roster_result(): processing roster contact [{$jid}] (is_new=={$is_new}; existing item=[{$x}])");
				
				
				// Is it a transport?
				$is_service = (!strlen($u_username)); 
				if ($is_service) {
					// are we registered with it?
					/*if ($u_resource=="registered") {*/
						if (!in_array($subscription,array("none","remove"))) { // if we're not subscribed to it, then we'll consider it unregistered
							$this->services[$jid]["registered"] = true;
						}
					/*}*/
				}
			
				// don't add the entry to the roster if it's a service, and we've been
				// configured to handle service presence internally (via $this->services)
				if (!($is_service && $this->handle_services_internally)) {
					// if not new, don't clobber the old presence/availability
					$u_jid = $u_username."@".$u_domain;
					$status = $is_new?"Offline":$this->roster[$jid]["status"];
					$show = $is_new?"off":$this->roster[$jid]["show"];
					
					// if presence was received before roster, grab the show value from the presence
					if ($this->presence_cache[$u_jid]) {
						if (!$show || $is_new) {
							$show = $this->presence_cache[$u_jid]["show"];
							// $this->dlog("TRACE::_on_roster_result: Using cached 'show' state for [{$u_jid}]; show=[{$show}]");
						}
						if (!$status || $is_new) {
							$status = $this->presence_cache[$u_jid]["status"];
							// $this->dlog("TRACE::_on_roster_result: Using cached 'status' state for [{$u_jid}]; status=[{$status}]");
						}
	
						// remove any cached presence info, as the roster item now exists
						// $this->dlog("TRACE::_on_roster_result: Clearing presence cache for {$u_jid}");
						unset($this->presence_cache[$u_jid]);
					}
					
					$nodename = $this->_node($queryitem,array('@','name'));
					$rostername = strlen($nodename) ? $nodename : $u_username;
					
					
					// prepare the roster item
					$rosteritem = array(
												"name"			=> $rostername,
												"subscription"	=> $this->_node($queryitem,array('@','subscription')),
												"ask"			=> $this->_node($queryitem,array('@','ask')),
												"group"			=> $this->_node($queryitem,array('#','group',0,'#')),
												"status"		=> $status,
												"show"			=> $show,
												"username"		=> $u_username,
												"domain"		=> $u_domain,
												"resource"		=> $u_resource,
												"jid"			=> $u_jid,
												"transport"		=> $this->get_transport($u_domain)
											);
					if ($is_new) {
						// if it's a new entry, just add it to the roster
						$this->roster[$jid] = $rosteritem;
					} else {
						// otherwise, carefully update the existing entry, preserving
						// any elements that may have been added externally
						foreach ($rosteritem as $k=>$v) {
							$this->roster[$jid][$k] = $v;
						}
					}
					
					//$this->_log('ROSTER UPDATE: '.print_r($this->roster[$jid],true));
					// you may wish to set roster_single_update to TRUE before
					// calling your initial browse(); this will allow you to
					// initialize your entire roster in one swoop, rather than
					// doing it contact-by-contact
					if ($this->roster_single_update) {
						// $this->dlog("TRACE::_on_roster_result(): updated contact for future roster_single_update; jid=$jid,is_new=[$is_new]");

						$roster_updated = true;
					} else {
						// $this->dlog("TRACE::_on_roster_result(): calling rosterupdate for jid=$jid,is_new=$is_new (individual)");
						$this->_call_handler("rosterupdate",$jid,$is_new);
					}
				}
			}
			
			if ($this->roster_single_update && $roster_updated) {
				// $this->dlog("TRACE::_on_roster_result(): calling rosterupdate for jid=NULL,is_new=false (roster_single_update==true)");
				$this->_call_handler("rosterupdate",NULL,false);
			}

			$this->_log("Received roster");
		// choke on error
		} elseif ($packet_type=="error") {
			$this->_handle_iq_error($packet);
			
		// confusion sets in
		} else {
			$this->_log("Don't know what to do with jabber:iq:roster packet!");
		}
	}
	
	function _is_error_packet($packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		return ( $packet_type == 'error' && $this->_nodeset($packet,array('iq','#','error',0,'#')) );
	}
	
	// receives the results of an account registration 'get' query (retrieving fields)
	function _on_register_get_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		$reg_id	= $this->_unique_id("reg");

		if ($packet_type=="result") {

			if ($this->_nodeset($packet,array('iq','#','query',0,'#','registered',0,'#'))) {
				$this->_call_handler("regfailure",-1,"Username already registered","");
				return;
			} 
	
			$key = $this->_node($packet,array('iq','#','query',0,'#','key',0,'#'));
			unset($packet);
	
			// Looks like CJP just hardcoded these fields, regardless of what the server sends...?!
			// FIXME: parse fields dynamically this when time permits
			$payload = "<username>{$this->_username}</username>
		<password>{$this->_password}</password>
		<email>{$this->_reg_email}</email>
		<name>{$this->_reg_name}</name>\n";
		
			$payload .= ($key) ? "<key>$key</key>\n" : '';
	
			$this->_set_iq_handler("_on_register_set_result",$reg_id);
			$this->_send_iq($this->_server_host, 'set', $reg_id, "jabber:iq:register", $payload);
		
		
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"regfailure");
		
		} else {
			$this->_call_handler("regfailure",-2,"Unrecognized response from server","");
		}
	}
	
	// receives the results of an account registration 'set' query (the actual result of
	// the account registration attempt)
	function _on_register_set_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		$error_code = 0;
		
		if ($packet_type=="result") {

			if ($this->_resource) {
				$this->jid = "{$this->_username}@{$this->_server_host}/{$this->_resource}";
			} else {
				$this->jid = "{$this->_username}@{$this->_server_host}";
			}
			$this->_call_handler("registered",$this->jid);
			
		} elseif ($this->_is_error_packet($packet)) {
			// "conflict" error, i.e. already registered
			if ($this->_node($packet,array('iq','#','error',0,'@','code')) == '409') {
				$this->_call_handler("regfailure",-1,"Username already registered","");
			} else {
				$this->_handle_iq_error(&$packet,"regfailure");
			}

		} else {
			$this->_call_handler("regfailure",-2,"Unrecognized response from server");
		}
	}
	
	function _on_deregister_result(&$packet) {

		$packet_type = $this->_node($packet,array('iq','@','type'));
		
		if ($packet_type=="result") {
			$this->_call_handler("deregistered",$this->jid);
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"deregfailure");
		} else {
			$this->_call_handler("deregfailure",-2,"Unrecognized response from server");
		}		
	}
	

	// receives the result of a password change	
	function _on_chgpassword_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		if ($packet_type=="result") {
			$this->_call_handler("passwordchanged");
			
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"passwordfailure");
		} else {
			$this->_call_handler("passwordfailure",-2,"Unrecognized response from server");
		}				
	}
	
	// receives the result of a service (transport) registration
	function _on_servicefields_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		$packet_id = $this->_node($packet,array('iq','@','id'));

		if ($packet_type=="result") {
				
			$reg_key = "";
			$reg_instructions = "";
			$reg_x = "";
			$fields = array();
			
			foreach ($this->_node($packet,array('iq','#','query',0,'#')) as $element => $data) {
				switch($element) {
					case "key":
						$reg_key = $this->_node($data,array(0,'#'));
						break;
					case "instructions":
						$reg_instructions = $this->_node($data,array(0,'#'));
						break;
					case "x":
						$reg_x = $this->_node($data,array(0,'#'));
						break;
					default:
						$fields[] = $element;
						break;
				}
			}
			$this->_call_handler("servicefields",&$fields,$packet_id,$reg_key,$reg_instructions,&$reg_x);
			
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"servicefieldsfailure");
		} else {
			$this->_call_handler("servicefieldsfailure",-2,"Unrecognized response from server");
		}				
	}
	
	function _on_serviceregister_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		$from = $this->_node($packet,array('iq','@','from'));
		if ($packet_type == 'result') {
			if ($this->_nodeset($packet,array('iq','#','query',0,'#','registered',0,'#'))) {
				$this->_call_handler("serviceregfailure",-1,"Already registered with service","");
			} else {
				$jid = $this->_bare_jid($from);
				$this->_call_handler("serviceregistered",$from);
			}
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"serviceregfailure");
		} else {
			$this->_call_handler("serviceregfailure",-2,"Unrecognized response from server");
		}				
	}
	
	function _on_servicedereg_initial_result(&$packet) {
		
		$packet_type = $this->_node($packet,array('iq','@','type'));
		$from = $this->_node($packet,array('iq','@','from'));
		
		if ($packet_type == 'result') {
			
			// we're now deregistered with the transport, but we need to remove
			// our roster subscription
			$dereg_id = $this->_unique_id("svcdereg");
			$this->_set_iq_handler("_on_servicedereg_final_result",$dereg_id);


			$this->services[$from]["registered"] = false;
			$this->services[$from]["subscription"] = "none";

			$payload = "<item jid='{$from}' subscription='remove'/>";
	
			$this->dlog("TRACE:: _on_servicedereg_initial_result() has positive result, setting handler ID#$dereg_id for final result");
	
			if ($this->_send_iq(NULL, 'set', $dereg_id, "jabber:iq:roster", $payload)) {
				$this->dlog("TRACE:: _on_deregister_initial_result() SENT, existing");

				return $dereg_id;
			} else {
				$this->dlog("TRACE:: _on_deregister_initial_result() FAILURE!!");

				return false;
			}			
			
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"servicederegfailure");
		} else {
			$this->_call_handler("servicederegfailure",-2,"Unrecognized response from server");
		}				
	}

	function _on_servicedereg_final_result(&$packet) {
		$this->dlog("TRACE:: _on_deregister_final_result() called!");

		$packet_type = $this->_node($packet,array('iq','@','type'));
		if ($packet_type == 'result') {
			$this->_call_handler("servicederegistered");
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"servicederegfailure");
		} else {
			$this->_call_handler("servicederegfailure",-2,"Unrecognized response from server");
		}				
	}
	
	function _on_rosteradd_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));;
		if ($packet_type == 'result') {

			$this->_call_handler("rosteradded",$this->_node($packet,array('iq','@','id')));
			
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"rosteraddfailure");
		} else {
			$this->_call_handler("rosteraddfailure",-2,"Unrecognized response from server");
		}				
	}

	function _on_rosterupdate_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		if ($packet_type == 'result') {
			$this->_call_handler("contactupdated",$this->_node($packet,array('iq','@','id')));
			
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"contactupdatefailure");
		} else {
			$this->_call_handler("contactupdatefailure",-2,"Unrecognized response from server");
		}				
	}
	function _on_rosterremove_result(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		if ($packet_type == 'result') {
			$this->_call_handler("rosterremoved",$this->_node($packet,array('iq','@','id')));
			
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"rosterremovefailure");
		} else {
			$this->_call_handler("rosterremovefailure",-2,"Unrecognized response from server");
		}				
	}
	
	function _on_private_data(&$packet) {
		$packet_type = $this->_node($packet,array('iq','@','type'));
		if ($packet_type == 'result') {

			$rootnode = $this->_node($packet,array('iq','#','query',0,'#'));
			unset($rootnode[0]);
			$rootnode = array_shift($rootnode);
			$data = $rootnode[0];
			$namespace = $this->_node($data,array('@','xmlns'));
			$rawvalues = $this->_node($data,array('#'));
			
			$values = array();
			if (is_array($rawvalues)) {
				foreach ($rawvalues as $k=>$v) {
					$values[$k] = $this->_node($v,array(0,'#'));
				}
			}
			
			$this->_call_handler("privatedata",$this->_node($packet,array('iq','@','id')),$namespace,$values);
			
		} elseif ($this->_is_error_packet($packet)) {
			$this->_handle_iq_error(&$packet,"privatedatafailure");
		} else {
			$this->_call_handler("privatedatafailure",-2,"Unrecognized response from server");
		}				
	}


	// handles a generic IQ error; fires the specified error handler method
	// with the error code/message retrieved from the IQ packet
	function _handle_iq_error(&$packet,$error_handler="error") {
		$error = $this->_node($packet,array('iq','#','error',0));
		$xmlns = $this->_node($packet,array('iq','#','query',0,'@','xmlns'));
		$this->_call_handler(
			$error_handler,
			$this->_node($error,array('@','code')),
			$this->_node($error,array('#')),
			$xmlns,
			$packet
		);
	}
	
	// handles a generic error; fires the specified error handler method
	// with the error code/message retrieved from the packet
	function _handle_error(&$packet,$error_handler="error") {
		$packet = array_shift($packet);
		$error = $this->_node($packet,array('#','error',0));
		$xmlns = $this->_node($packet,array('#','query',0,'@','xmlns'));
		$this->_call_handler(
			$error_handler,
			$this->_node($error,array('@','code')),
			$this->_node($error,array('#')),
			$xmlns,
			$packet
		);
	}
	
	
	
	// ==== Authentication Methods ===========================================================

	function _sendauth_digest($auth_id) {
		$this->_log("Using digest authentication");

		$payload = "<username>{$this->_username}</username>
	<resource>{$this->_resource}</resource>
	<digest>" . sha1($this->_stream_id . $this->_password) . "</digest>";

		$this->_send_iq(NULL, 'set', $auth_id, "jabber:iq:auth", $payload);
	}

	function _sendauth_plaintext($auth_id) {
		$this->_log("Using plaintext authentication");

		$payload = "<username>{$this->_username}</username>
	<password>{$this->_password}</password>
	<resource>{$this->_resource}</resource>";

		$this->_send_iq(NULL, 'set', $auth_id, "jabber:iq:auth", $payload);
	}	


	// ==== Helper Methods ===================================================================
	
	function _show($show) {
		// off is not valid, but is used internally
		$valid_shows = array("","away","chat","dnd","xa","off");
		if (!in_array($show,$valid_shows)) $show = "";
		
		return $show;
	}

	function dump(&$v) {
		ob_start();
		var_dump($v);
		$x = ob_get_contents();
		ob_end_clean();
		return $x;
		
		
		return print_r($v,true); 
	}

	
	function standardize_transport($transport,$force=true) {
		$transports = array("msn","aim","yim","icq","jab");
		if (!in_array($transport,$transports)) {
			if ($transport=="aol") {
				$transport = "aim";
			} elseif ($transport=="yahoo") {
				$transport = "yim";
			} else {
				if ($force) $transport = "jab";
			}
		}
		return $transport;
	}
		
	function get_transport($domain) {
		$transport = $this->services[$domain]["type"];
		return $this->standardize_transport($transport);
	}





	// ==== Packet Handling & Connection Methods =============================================

	// generates and transmits an IQ packet
	function _send_iq($to = NULL, $type = 'get', $id = NULL, $xmlns = NULL, $payload = NULL, $from = NULL) {
		if (!preg_match("/^(get|set|result|error)$/", $type)) {
			unset($type);

			$this->_log("ERROR: _send_iq() #2 - type must be 'get', 'set', 'result' or 'error'");
			return false;
		
		} elseif ($id && $xmlns) {
			$xml = "<iq type='$type' id='$id'";
			$xml .= ($to) ? " to='$to'" : '';
			$xml .= ($from) ? " from='$from'" : '';
			$xml .= ">
	<query xmlns='$xmlns'>
		$payload
	</query>
</iq>";

			return $this->_send($xml);
		} else {
			$this->_log("ERROR: SendIq() #1 - to, id and xmlns are mandatory");
			return false;
		}
	}	
	
	
	// writes XML data to the socket; trims and UTF8 encodes $xml before
	// sending unless $pristine is true
	function _send($xml,$pristine = false) {
	   	// need UTF8 encoding to prevent character coding issues when
	    // users enter international characters
	    /*
	    if (!$pristine) {
			$xml = trim(utf8_encode($xml));
	    	if (!$xml) return false;
	    }
	    */
		if(strlen($xml)==0) return true;
		
		if ($res = $this->_connection->socket_write($xml)) {
			$this->_log("SEND: $xml");
		} else {
			$this->_log("ERROR SENDING: $xml");
		}
		return $res;
 	}	
	
	
	
	function _receive() {
		unset($incoming);
		$packet_count = 0;

		$sleepfunc = $this->_sleep_func;

		$iterations = 0; 
		$empties = 0;
		do {
			$line = $this->_connection->socket_read(16384);
			if (strlen($line)==0) {
				$empties++;
				if ($empties>15) break;
			} else {
				$empties = 0;
			}
			
			$incoming .= $line;
			$iterations++;
			
		// the iteration limit is just a brake to prevent infinite loops if
		// something goes awry in socket_read()
		} while($iterations<200);

		$incoming = trim($incoming);

		if ($incoming != "") {
			//$this->_log("RECV: $incoming");

			$temp = $this->_split_incoming($incoming);
			
			$packet_count = count($temp);

			for ($a = 0; $a < $packet_count; $a++) {
				$this->_packet_queue[] = $this->xml->xmlize($temp[$a]);
				
				$this->_log("RECV: ".$temp[$a]);
				//.$this->_packet_queue[count($this->_packet_queue)-1]);
			}
		}

		return $packet_count;
	}	
	
	function _get_next_packet() {
		return array_shift($this->_packet_queue);
	}
	
	function _split_incoming($incoming) {
		$temp = preg_split("/<(message|iq|presence|stream)(?=[\:\s\>])/", $incoming, -1, PREG_SPLIT_DELIM_CAPTURE);
		$array = array();

		for ($a = 1; $a < count($temp); $a = $a + 2) {
			$array[] = "<" . $temp[$a] . $temp[($a + 1)];
		}

		return $array;
	}
	
	
}

?>