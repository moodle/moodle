<?php

namespace BirknerAlex\XMPPHP;

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
	 * @package    XMPPHP
	 * @author     Nathanael C. Fritz <JID: fritzy@netflint.net>
	 * @author     Stephan Wentz <JID: stephan@jabber.wentz.it>
	 * @author     Michael Garvin <JID: gar@netflint.net>
	 * @author     Alexander Birkner (https://github.com/BirknerAlex)
	 * @copyright  2008 Nathanael C. Fritz
	 */

/**
 * XMPPHP Main Class
 *
 * @category   xmpphp
 * @package    XMPPHP
 * @author     Nathanael C. Fritz <JID: fritzy@netflint.net>
 * @author     Stephan Wentz <JID: stephan@jabber.wentz.it>
 * @author     Michael Garvin <JID: gar@netflint.net>
 * @copyright  2008 Nathanael C. Fritz
 * @version    $Id$
 */
class Log {
	
	const LEVEL_ERROR   = 0;
	const LEVEL_WARNING = 1;
	const LEVEL_INFO	= 2;
	const LEVEL_DEBUG   = 3;
	const LEVEL_VERBOSE = 4;
	
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var array
	 */
	protected $names = array('ERROR', 'WARNING', 'INFO', 'DEBUG', 'VERBOSE');

	/**
	 * @var integer
	 */
	protected $runlevel;

	/**
	 * @var boolean
	 */
	protected $printout;

	/**
	 * Constructor
	 *
	 * @param boolean $printout
	 * @param string  $runlevel
	 */
	public function __construct($printout = false, $runlevel = self::LEVEL_INFO) {
		$this->printout = (boolean)$printout;
		$this->runlevel = (int)$runlevel;
	}

	/**
	 * Add a message to the log data array
	 * If printout in this instance is set to true, directly output the message
	 *
	 * @param string  $msg
	 * @param integer $runlevel
	 */
	public function log($msg, $runlevel = self::LEVEL_INFO) {
		$time = time();
		#$this->data[] = array($this->runlevel, $msg, $time);
		if($this->printout and $runlevel <= $this->runlevel) {
			$this->writeLine($msg, $runlevel, $time);
		}
	}

	/**
	 * Output the complete log.
	 * Log will be cleared if $clear = true
	 *
	 * @param boolean $clear
	 * @param integer $runlevel
	 */
	public function printout($clear = true, $runlevel = null) {
		if($runlevel === null) {
			$runlevel = $this->runlevel;
		}
		foreach($this->data as $data) {
			if($runlevel <= $data[0]) {
				$this->writeLine($data[1], $runlevel, $data[2]);
			}
		}
		if($clear) {
			$this->data = array();
		}
	}
	
	protected function writeLine($msg, $runlevel, $time) {
		//echo date('Y-m-d H:i:s', $time)." [".$this->names[$runlevel]."]: ".$msg."\n";
		echo $time." [".$this->names[$runlevel]."]: ".$msg."\n";
		flush();
	}
}
