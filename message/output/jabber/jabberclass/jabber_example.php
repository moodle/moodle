#!/usr/local/bin/php -q
<?php
/* Jabber Class Example
 * Copyright 2002-2007, Steve Blinch
 * http://code.blitzaffe.com
 * ============================================================================
 *
 * DETAILS
 *
 * Provides a very basic example of how to use class_Jabber.php.
 *
 * This example connects to a Jabber server, logs in, fetches (and displays)
 * the roster, and then waits until a message is received from another contact.
 *
 * It then starts a countdown which, in sequence:
 * 
 * 1) sends a "composing" event to the other contact (eg: "so-and-so is typing a message"),
 * 2) sends a "composing stopped" event,
 * 3) sends another "composing" event",
 * 4) sends a message to the other contact,
 * 5) logs out
 *
 */
 
// set your Jabber server hostname, username, and password here
define("JABBER_SERVER","jabber.blitzaffe.com");
define("JABBER_USERNAME","helium");
define("JABBER_PASSWORD","zv4.5k1p8");

define("RUN_TIME",30);	// set a maximum run time of 30 seconds
define("CBK_FREQ",1);	// fire a callback event every second


// This class handles all events fired by the Jabber client class; you
// can optionally use individual functions instead of a class, but this
// method is a bit cleaner.
class TestMessenger {
	
	function TestMessenger(&$jab) {
		$this->jab = &$jab;
		$this->first_roster_update = true;
		
		echo "Created!\n";
		$this->countdown = 0;
	}
	
	// called when a connection to the Jabber server is established
	function handleConnected() {
		echo "Connected!\n";
		
		// now that we're connected, tell the Jabber class to login
		echo "Authenticating ...\n";
		$this->jab->login(JABBER_USERNAME,JABBER_PASSWORD);
	}
	
	// called after a login to indicate the the login was successful
	function handleAuthenticated() {
		echo "Authenticated!\n";
		
		
		echo "Fetching service list and roster ...\n";
		
		// browser for transport gateways
		$this->jab->browse();
		
		// retrieve this user's roster
		$this->jab->get_roster();
		
		// set this user's presence
		$this->jab->set_presence("","Ya, I'm online... so what?");
	}
	
	// called after a login to indicate that the login was NOT successful
	function handleAuthFailure($code,$error) {
		echo "Authentication failure: $error ($code)\n";
		
		// set terminated to TRUE in the Jabber class to tell it to exit
		$this->jab->terminated = true;
	}
	
	// called periodically by the Jabber class to allow us to do our own
	// processing
	function handleHeartbeat() {
		echo "Heartbeat - ";
		
		// if the countdown is in progress, determine if we need to take any action
		if ($this->countdown>0) {
			$this->countdown--;

			// display our countdown progress
			echo "Countdown: {$this->countdown}\n";

			
			// first, we want to fire our composing event
			if ($this->countdown==7) {
				echo "Composing start\n";
				$this->jab->composing($this->last_msg_from,$this->last_msg_id);
			}
			
			// next, we want to indicate that we've stopped composing a message
			if ($this->countdown==5) {
				echo "Composing stop\n";
				$this->jab->composing($this->last_msg_from,$this->last_msg_id,false);
			}
			
			// next, we indicate that we're composing again
			if ($this->countdown==3) {
				echo "Composing start\n";
				$this->jab->composing($this->last_msg_from,$this->last_msg_id);
			}
			
			// and finally, we send a message to the remote user and tell the Jabber class
			// to exit
			if ($this->countdown==1) {
				echo "Send message\n";
				$this->jab->message($this->last_msg_from,"chat",NULL,"Hello! You said: ".$this->last_message);
				$this->jab->terminated = true;
			}
		} else {
			echo "Waiting for incoming message ...\n";
		}
		/*
		reset($this->jab->roster);
		foreach ($this->jab->roster as $jid=>$details) {
			echo "$jid\t\t\t".$details["transport"]."\t".$details["show"]."\t".$details["status"]."\n";
		}
		*/
	}
	
	// called when an error is received from the Jabber server
	function handleError($code,$error,$xmlns) {
		echo "Error: $error ($code)".($xmlns?" in $xmlns":"")."\n";
	}
	
	// called when a message is received from a remote contact
	function handleMessage($from,$to,$body,$subject,$thread,$id,$extended) {
		echo "Incoming message!\n";
		echo "From: $from\t\tTo: $to\n";
		echo "Subject: $subject\tThread; $thread\n";
		echo "Body: $body\n";
		echo "ID: $id\n";
		var_dump($extended);
		echo "\n";
		
		$this->last_message = $body;
		
		$this->last_msg_id = $id;
		$this->last_msg_from = $from;
		
		// for the purposes of our example, we start a countdown here to do some
		// random events, just for the sake of demonstration
		echo "Starting countdown\n";
		$this->countdown = 10;
	}
	
	function _contact_info($contact) {
		return sprintf("Contact %s (JID %s) has status %s and message %s\n",$contact['name'],$contact['jid'],$contact['show'],$contact['status']);
	}
	
	function handleRosterUpdate($jid) {
		if ($this->first_roster_update) {
			// the first roster update indicates that the entire roster has been
			// downloaded for the first time
			echo "Roster downloaded:\n";
			
			foreach ($this->jab->roster as $k=>$contact) {
				echo $this->_contact_info($contact);
			}	
			$this->first_roster_update = false;
		} else {
			// subsequent roster updates indicate changes for individual roster items
			$contact = $this->jab->roster[$jid];
			echo "Contact updated: " . $this->_contact_info($contact);
		}
	}
	
	function handleDebug($msg,$level) {
		echo "DBG: $msg\n";
	}
	
}

// include the Jabber class
require_once("class_Jabber.php");

// create an instance of the Jabber class
$display_debug_info = false;
$jab = new Jabber($display_debug_info);

// create an instance of our event handler class
$test = new TestMessenger($jab);

// set handlers for the events we wish to be notified about
$jab->set_handler("connected",$test,"handleConnected");
$jab->set_handler("authenticated",$test,"handleAuthenticated");
$jab->set_handler("authfailure",$test,"handleAuthFailure");
$jab->set_handler("heartbeat",$test,"handleHeartbeat");
$jab->set_handler("error",$test,"handleError");
$jab->set_handler("message_normal",$test,"handleMessage");
$jab->set_handler("message_chat",$test,"handleMessage");
$jab->set_handler("debug_log",$test,"handleDebug");
$jab->set_handler("rosterupdate",$test,"handleRosterUpdate");

echo "Connecting ...\n";

// connect to the Jabber server
if (!$jab->connect(JABBER_SERVER)) {
	die("Could not connect to the Jabber server!\n");
}

// now, tell the Jabber class to begin its execution loop
$jab->execute(CBK_FREQ,RUN_TIME);

// Note that we will not reach this point (and the execute() method will not
// return) until $jab->terminated is set to TRUE.  The execute() method simply
// loops, processing data from (and to) the Jabber server, and firing events
// (which are handled by our TestMessenger class) until we tell it to terminate.
//
// This event-based model will be familiar to programmers who have worked on
// desktop applications, particularly in Win32 environments.

// disconnect from the Jabber server
$jab->disconnect();
?>