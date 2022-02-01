<?php
/**
 * Memory caching.
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2000-2013 John Lim
 * @copyright 2014 Damien Regad, Mark Newnham and the ADOdb community
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

global $ADODB_INCLUDED_MEMCACHE;
$ADODB_INCLUDED_MEMCACHE = 1;

global $ADODB_INCLUDED_CSV;
if (empty($ADODB_INCLUDED_CSV)) include_once(ADODB_DIR.'/adodb-csvlib.inc.php');

	class ADODB_Cache_MemCache {
		var $createdir = false; // create caching directory structure?

		// $library will be populated with the proper library on connect
		// and is used later when there are differences in specific calls
		// between memcache and memcached
		var $library = false;

		//-----------------------------
		// memcache specific variables

		var $hosts;	// array of hosts
		var $port = 11211;
		var $compress = false; // memcache compression with zlib

		var $_connected = false;
		var $_memcache = false;

		function __construct(&$obj)
		{
			$this->hosts = $obj->memCacheHost;
			$this->port = $obj->memCachePort;
			$this->compress = $obj->memCacheCompress;
		}

		// implement as lazy connection. The connection only occurs on CacheExecute call
		function connect(&$err)
		{
			// do we have memcache or memcached?
			if (class_exists('Memcache')) {
				$this->library='Memcache';
				$memcache = new MemCache;
			} elseif (class_exists('Memcached')) {
				$this->library='Memcached';
				$memcache = new MemCached;
			} else {
				$err = 'Neither the Memcache nor Memcached PECL extensions were found!';
				return false;
			}

			if (!is_array($this->hosts)) $this->hosts = array($this->hosts);

			$failcnt = 0;
			foreach($this->hosts as $host) {
				if (!@$memcache->addServer($host,$this->port)) {
					$failcnt += 1;
				}
			}
			if ($failcnt == sizeof($this->hosts)) {
				$err = 'Can\'t connect to any memcache server';
				return false;
			}
			$this->_connected = true;
			$this->_memcache = $memcache;
			return true;
		}

		// returns true or false. true if successful save
		function writecache($filename, $contents, $debug, $secs2cache)
		{
			if (!$this->_connected) {
				$err = '';
				if (!$this->connect($err) && $debug) ADOConnection::outp($err);
			}
			if (!$this->_memcache) return false;

			$failed=false;
			switch ($this->library) {
				case 'Memcache':
					if (!$this->_memcache->set($filename, $contents, $this->compress ? MEMCACHE_COMPRESSED : 0, $secs2cache)) {
						$failed=true;
					}
					break;
				case 'Memcached':
					if (!$this->_memcache->set($filename, $contents, $secs2cache)) {
						$failed=true;
					}
					break;
				default:
					$failed=true;
					break;
			}

			if($failed) {
				if ($debug) ADOConnection::outp(" Failed to save data at the memcache server!<br>\n");
				return false;
			}

			return true;
		}

		// returns a recordset
		function readcache($filename, &$err, $secs2cache, $rsClass)
		{
			$false = false;
			if (!$this->_connected) $this->connect($err);
			if (!$this->_memcache) return $false;

			$rs = $this->_memcache->get($filename);
			if (!$rs) {
				$err = 'Item with such key doesn\'t exist on the memcache server.';
				return $false;
			}

			// hack, should actually use _csv2rs
			$rs = explode("\n", $rs);
            unset($rs[0]);
            $rs = join("\n", $rs);
 			$rs = unserialize($rs);
			if (! is_object($rs)) {
				$err = 'Unable to unserialize $rs';
				return $false;
			}
			if ($rs->timeCreated == 0) return $rs; // apparently have been reports that timeCreated was set to 0 somewhere

			$tdiff = intval($rs->timeCreated+$secs2cache - time());
			if ($tdiff <= 2) {
				switch($tdiff) {
					case 2:
						if ((rand() & 15) == 0) {
							$err = "Timeout 2";
							return $false;
						}
						break;
					case 1:
						if ((rand() & 3) == 0) {
							$err = "Timeout 1";
							return $false;
						}
						break;
					default:
						$err = "Timeout 0";
						return $false;
				}
			}
			return $rs;
		}

		function flushall($debug=false)
		{
			if (!$this->_connected) {
				$err = '';
				if (!$this->connect($err) && $debug) ADOConnection::outp($err);
			}
			if (!$this->_memcache) return false;

			$del = $this->_memcache->flush();

			if ($debug)
				if (!$del) ADOConnection::outp("flushall: failed!<br>\n");
				else ADOConnection::outp("flushall: succeeded!<br>\n");

			return $del;
		}

		function flushcache($filename, $debug=false)
		{
			if (!$this->_connected) {
  				$err = '';
  				if (!$this->connect($err) && $debug) ADOConnection::outp($err);
			}
			if (!$this->_memcache) return false;

			$del = $this->_memcache->delete($filename);

			if ($debug)
				if (!$del) ADOConnection::outp("flushcache: $key entry doesn't exist on memcache server!<br>\n");
				else ADOConnection::outp("flushcache: $key entry flushed from memcache server!<br>\n");

			return $del;
		}

		// not used for memcache
		function createdir($dir, $hash)
		{
			return true;
		}
	}
