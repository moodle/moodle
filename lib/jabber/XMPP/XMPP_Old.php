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
 * @author	 Michael Garvin <JID: gar@netflint.net>
 * @copyright  2008 Nathanael C. Fritz
 */

/** XMPPHP_XMPP 
 *
 * This file is unnecessary unless you need to connect to older, non-XMPP-compliant servers like Dreamhost's.
 * In this case, use instead of XMPPHP_XMPP, otherwise feel free to delete it.
 * The old Jabber protocol wasn't standardized, so use at your own risk.
 *
 */
require_once "XMPP.php";

	class XMPPHP_XMPPOld extends XMPPHP_XMPP {
		/**
		 *
		 * @var string
		 */
		protected $session_id;

		public function __construct($host, $port, $user, $password, $resource, $server = null, $printlog = false, $loglevel = null) {
			parent::__construct($host, $port, $user, $password, $resource, $server, $printlog, $loglevel);
			if(!$server) $server = $host;
			$this->stream_start = '<stream:stream to="' . $server . '" xmlns:stream="http://etherx.jabber.org/streams" xmlns="jabber:client">';
			$this->fulljid = "{$user}@{$server}/{$resource}";
		}
	
		/**
		 * Override XMLStream's startXML
		 *
		 * @param parser $parser
		 * @param string $name
		 * @param array $attr
		 */
		public function startXML($parser, $name, $attr) {
			if($this->xml_depth == 0) {
				$this->session_id = $attr['ID'];
				$this->authenticate();
			}
			parent::startXML($parser, $name, $attr);
		}

		/**
		 * Send Authenticate Info Request
		 *
		 */
		public function authenticate() {
			$id = $this->getId();
			$this->addidhandler($id, 'authfieldshandler');
			$this->send("<iq type='get' id='$id'><query xmlns='jabber:iq:auth'><username>{$this->user}</username></query></iq>");
		}

		/**
		 * Retrieve auth fields and send auth attempt
		 *
		 * @param XMLObj $xml
		 */
		public function authFieldsHandler($xml) {
			$id = $this->getId();
			$this->addidhandler($id, 'oldAuthResultHandler');
			if($xml->sub('query')->hasSub('digest')) {
				$hash = sha1($this->session_id . $this->password);
				print "{$this->session_id} {$this->password}\n";
				$out = "<iq type='set' id='$id'><query xmlns='jabber:iq:auth'><username>{$this->user}</username><digest>{$hash}</digest><resource>{$this->resource}</resource></query></iq>";
			} else {
				$out = "<iq type='set' id='$id'><query xmlns='jabber:iq:auth'><username>{$this->user}</username><password>{$this->password}</password><resource>{$this->resource}</resource></query></iq>";
			}
			$this->send($out);

		}
		
		/**
		 * Determine authenticated or failure
		 *
		 * @param XMLObj $xml
		 */
		public function oldAuthResultHandler($xml) {
			if($xml->attrs['type'] != 'result') {
				$this->log->log("Auth failed!",  XMPPHP_Log::LEVEL_ERROR);
				$this->disconnect();
				throw new XMPPHP_Exception('Auth failed!');
			} else {
				$this->log->log("Session started");
				$this->event('session_start');
			}
		}
	}


?>
