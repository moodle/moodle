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
 *
 * @noinspection PhpUnused
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

global $ADODB_INCLUDED_MEMCACHE;
$ADODB_INCLUDED_MEMCACHE = 1;

global $ADODB_INCLUDED_CSV;
if (empty($ADODB_INCLUDED_CSV)) {
	include_once(ADODB_DIR . '/adodb-csvlib.inc.php');
}

class ADODB_Cache_MemCache
{
	/**
	 * @var bool Prevents parent class calling non-existant function
	 */
	public $createdir = false;

	/**
	 * @var array of hosts
	 */
	private $hosts;

	/**
	 * @var int Connection Port, uses default
	 */
	private $port;

	/**
	 * @var bool memcache compression with zlib
	 */
	private $compress;

	/**
	 * @var array of options for memcached only
	 */
	private $options;

	/**
	 * @var bool Internal flag indicating successful connection
	 */
	private $isConnected = false;

	/**
	 * @var Memcache|Memcached Handle for the Memcache library
	 *
	 * Populated with the proper library on connect, used later when
	 * there are differences in specific calls between memcache and memcached
	 */
	private $memcacheLibrary = false;

	/**
	 * @var array New server feature controller lists available servers
	 */
	private $serverControllers = array();

	/**
	 * @var array New server feature template uses granular server controller
	 */
	private $serverControllerTemplate = array(
		'host' => '',
		'port' => 11211,
		'weight' => 0,
	);

	/**
	 * An integer index into the libraries
	 * @see $libraries
	 */
	const MCLIB = 1;
	const MCLIBD = 2;

	/**
	 * @var array Xrefs the library flag to the actual class name
	 */
	private $libraries = array(
		self::MCLIB => 'Memcache',
		self::MCLIBD => 'Memcached'
	);

	/**
	 * @var int An indicator of which library we are using
	 */
	private $libraryFlag;

	/**
	 * Class Constructor.
	 *
	 * @param ADOConnection $db
	 */
	public function __construct($db)
	{
		$this->hosts = $db->memCacheHost;
		$this->port = $this->serverControllerTemplate['port'] = $db->memCachePort;
		$this->compress = $db->memCacheCompress;
		$this->options = $db->memCacheOptions;
	}

	/**
	 * Return true if the current library is Memcached.
	 * @return bool
	 */
	public function isLibMemcached(): bool
	{
		return $this->libraryFlag == self::MCLIBD;
	}

	/**
	 * Lazy connection.
	 *
	 * The connection only occurs on CacheExecute call.
	 *
	 * @param string $err
	 *
	 * @return bool success of connecting to a server
	 */
	public function connect(&$err)
	{
		// do we have memcache or memcached? see the note at adodb.org on memcache
		if (class_exists('Memcache')) {
			$this->libraryFlag = self::MCLIB;
		} elseif (class_exists('Memcached')) {
			$this->libraryFlag = self::MCLIBD;
		} else {
			$err = 'Neither the Memcache nor Memcached PECL extensions were found!';
			return false;
		}

		$usedLibrary = $this->libraries[$this->libraryFlag];

		/** @var Memcache|Memcached $memCache */
		$memCache = new $usedLibrary;
		if (!$memCache) {
			$err = 'Memcache library failed to initialize';
			return false;
		}

		// Convert simple compression flag for memcached
		if ($this->isLibMemcached()) {
			$this->options[Memcached::OPT_COMPRESSION] = $this->compress;
		}

		// Are there any options available for memcached
		if ($this->isLibMemcached() && count($this->options) > 0) {
			$optionSuccess = $memCache->setOptions($this->options);
			if (!$optionSuccess) {
				$err = 'Invalid option parameters passed to Memcached';
				return false;
			}
		}

		// Have we passed a controller array
		if (!is_array($this->hosts)) {
			$this->hosts = array($this->hosts);
		}

		if (!is_array($this->hosts[0])) {
			// Old way, convert to controller
			foreach ($this->hosts as $ipAddress) {
				$connector = $this->serverControllerTemplate;
				$connector['host'] = $ipAddress;
				$connector['port'] = $this->port;

				$this->serverControllers[] = $connector;
			}
		} else {
			// New way, must validate port, etc
			foreach ($this->hosts as $controller) {
				$connector = array_merge($this->serverControllerTemplate, $controller);
				if ($this->isLibMemcached()) {
					$connector['weight'] = (int)$connector['weight'];
				} else {
					// Cannot use weight in memcache, simply discard
					$connector['weight'] = 0;
				}

				$this->serverControllers[] = $connector;
			}
		}

		// Checks for existing connections ( but only for memcached )
		if ($this->isLibMemcached() && !empty($memCache->getServerList())) {
			// Use the existing configuration
			$this->isConnected = true;
			$this->memcacheLibrary = $memCache;
			return true;
		}

		$failcnt = 0;
		foreach ($this->serverControllers as $controller) {
			if ($this->isLibMemcached()) {
				if (!@$memCache->addServer($controller['host'], $controller['port'], $controller['weight'])) {
					$failcnt++;
				}
			} else {
				if (!@$memCache->addServer($controller['host'], $controller['port'])) {
					$failcnt++;
				}
			}
		}
		if ($failcnt == sizeof($this->serverControllers)) {
			$err = 'Can\'t connect to any memcache server';
			return false;
		}

		$this->memcacheLibrary = $memCache;

		// A valid memcache connection is available
		$this->isConnected = true;
		return true;
	}

	/**
	 * Writes a cached query to the server
	 *
	 * @param string $filename The MD5 of the query to cache
	 * @param string $contents The query results
	 * @param bool $debug
	 * @param int $secs2cache
	 *
	 * @return bool true or false. true if successful save
	 */
	public function writeCache($filename, $contents, $debug, $secs2cache)
	{
		$err = '';
		if (!$this->isConnected && $debug) {
			// Call to writeCache() before connect(), try to connect
			if (!$this->connect($err)) {
				ADOConnection::outp($err);
			}
		} else {
			if (!$this->isConnected) {
				$this->connect($err);
			}
		}

		if (!$this->memcacheLibrary) {
			return false;
		}

		$failed = false;
		switch ($this->libraryFlag) {
			case self::MCLIB:
				if (!$this->memcacheLibrary->set($filename, $contents, $this->compress ? MEMCACHE_COMPRESSED : 0,
					$secs2cache)) {
					$failed = true;
				}
				break;
			case self::MCLIBD:
				if (!$this->memcacheLibrary->set($filename, $contents, $secs2cache)) {
					$failed = true;
				}
				break;
			default:
				$failed = true;
				break;
		}

		if ($failed) {
			if ($debug) {
				ADOConnection::outp(" Failed to save data at the memcache server!<br>\n");
			}
			return false;
		}

		return true;
	}

	/**
	 * Reads a cached query from the server.
	 *
	 * @param string $filename The MD5 of the query to read
	 * @param string $err The query results
	 * @param int $secs2cache
	 * @param object $rsClass **UNUSED**
	 *
	 * @return object|bool record or false.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function readCache($filename, &$err, $secs2cache, $rsClass)
	{
		if (!$this->isConnected) {
			$this->connect($err);
		}
		if (!$this->memcacheLibrary) {
			return false;
		}

		$rs = $this->memcacheLibrary->get($filename);
		if (!$rs) {
			$err = 'Item with such key doesn\'t exist on the memcache server.';
			return false;
		}

		// hack, should actually use _csv2rs
		$rs = explode("\n", $rs);
		unset($rs[0]);
		$rs = join("\n", $rs);
		$rs = unserialize($rs);
		if (!is_object($rs)) {
			$err = 'Unable to unserialize $rs';
			return false;
		}
		if ($rs->timeCreated == 0) {
			return $rs;
		} // apparently have been reports that timeCreated was set to 0 somewhere

		$tdiff = intval($rs->timeCreated + $secs2cache - time());
		if ($tdiff <= 2) {
			switch ($tdiff) {
				case 2:
					if ((rand() & 15) == 0) {
						$err = "Timeout 2";
						return false;
					}
					break;
				case 1:
					if ((rand() & 3) == 0) {
						$err = "Timeout 1";
						return false;
					}
					break;
				default:
					$err = "Timeout 0";
					return false;
			}
		}
		return $rs;
	}

	/**
	 * Flushes all of the stored memcache data
	 *
	 * @param bool $debug
	 *
	 * @return bool The response from the memcache server
	 */
	public function flushAll($debug = false)
	{
		if (!$this->isConnected) {
			$err = '';
			if (!$this->connect($err) && $debug) {
				ADOConnection::outp($err);
			}
		}
		if (!$this->memcacheLibrary) {
			return false;
		}

		$del = $this->memcacheLibrary->flush();

		if ($debug) {
			if (!$del) {
				ADOConnection::outp("flushall: failed!<br>\n");
			} else {
				ADOConnection::outp("flushall: succeeded!<br>\n");
			}
		}

		return $del;
	}

	/**
	 * Flushes the contents of a specified query
	 *
	 * @param string $filename The MD5 of the query to flush
	 * @param bool $debug
	 *
	 * @return bool The response from the memcache server
	 */
	public function flushCache($filename, $debug = false)
	{
		if (!$this->isConnected) {
			$err = '';
			if (!$this->connect($err) && $debug) {
				ADOConnection::outp($err);
			}
		}
		if (!$this->memcacheLibrary) {
			return false;
		}

		$del = $this->memcacheLibrary->delete($filename);

		if ($debug) {
			if (!$del) {
				ADOConnection::outp("flushcache: $filename entry doesn't exist on memcache server!<br>\n");
			} else {
				ADOConnection::outp("flushcache: $filename entry flushed from memcache server!<br>\n");
			}
		}

		return $del;
	}

}
