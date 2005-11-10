<?php


/*
V4.66 28 Sept 2005  (c) 2000-2005 John Lim (jlim@natsoft.com.my). All rights reserved.
         Contributed by Ross Smith (adodb@netebb.com). 
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
	  Set tabs to 4 for best viewing.
*/

/*
	You may want to rename the 'data' field to 'session_data' as
	'data' appears to be a reserved word for one or more of the following:
		ANSI SQL
		IBM DB2
		MS SQL Server
		Postgres
		SAP

	If you do, then execute:

		ADODB_Session::dataFieldName('session_data');

*/

if (!defined('_ADODB_LAYER')) {
	require_once realpath(dirname(__FILE__) . '/../adodb.inc.php');
}

if (defined('ADODB_SESSION')) return 1;

define('ADODB_SESSION', dirname(__FILE__));


/* 
	Unserialize session data manually. See http://phplens.com/lens/lensforum/msgs.php?id=9821 
	
	From Kerr Schere, to unserialize session data stored via ADOdb. 
	1. Pull the session data from the db and loop through it. 
	2. Inside the loop, you will need to urldecode the data column. 
	3. After urldecode, run the serialized string through this function:

*/
function adodb_unserialize( $serialized_string ) 
{
	$variables = array( );
	$a = preg_split( "/(\w+)\|/", $serialized_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
	for( $i = 0; $i < count( $a ); $i = $i+2 ) {
		$variables[$a[$i]] = unserialize( $a[$i+1] );
	}
	return( $variables );
}

/*
	Thanks Joe Li. See http://phplens.com/lens/lensforum/msgs.php?id=11487&x=1
	Since adodb 4.61.
*/
function adodb_session_regenerate_id() 
{
	$conn =& ADODB_Session::_conn();
	if (!$conn) return false;

	$old_id = session_id();
	if (function_exists('session_regenerate_id')) {
		session_regenerate_id();
	} else {
		session_id(md5(uniqid(rand(), true)));
		$ck = session_get_cookie_params();
		setcookie(session_name(), session_id(), false, $ck['path'], $ck['domain'], $ck['secure']);
		//@session_start();
	}
	$new_id = session_id();
	$ok =& $conn->Execute('UPDATE '. ADODB_Session::table(). ' SET sesskey='. $conn->qstr($new_id). ' WHERE sesskey='.$conn->qstr($old_id));
	
	/* it is possible that the update statement fails due to a collision */
	if (!$ok) {
		session_id($old_id);
		if (empty($ck)) $ck = session_get_cookie_params();
		setcookie(session_name(), session_id(), false, $ck['path'], $ck['domain'], $ck['secure']);
		return false;
	}
	
	return true;
}

/*
    Generate database table for session data
    @see http://phplens.com/lens/lensforum/msgs.php?id=12280
    @return 0 if failure, 1 if errors, 2 if successful.
	@author Markus Staab http://www.public-4u.de
*/
function adodb_session_create_table($schemaFile=null,$conn = null)
{
    // set default values
    if ($schemaFile===null) $schemaFile = ADODB_SESSION . '/session_schema.xml';
    if ($conn===null) $conn =& ADODB_Session::_conn();

	if (!$conn) return 0;

    $schema = new adoSchema($conn);
    $schema->ParseSchema($schemaFile);
    return $schema->ExecuteSchema();
}

/*!
	\static
*/
class ADODB_Session {
	/////////////////////
	// getter/setter methods
	/////////////////////
	
	/*
	
	function Lock($lock=null)
	{
	static $_lock = false;
	
		if (!is_null($lock)) $_lock = $lock;
		return $lock;
	}
	*/
	/*!
	*/
	function driver($driver = null) {
		static $_driver = 'mysql';
		static $set = false;

		if (!is_null($driver)) {
			$_driver = trim($driver);
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESSION_DRIVER'])) {
				return $GLOBALS['ADODB_SESSION_DRIVER'];
			}
		}

		return $_driver;
	}

	/*!
	*/
	function host($host = null) {
		static $_host = 'localhost';
		static $set = false;

		if (!is_null($host)) {
			$_host = trim($host);
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESSION_CONNECT'])) {
				return $GLOBALS['ADODB_SESSION_CONNECT'];
			}
		}

		return $_host;
	}

	/*!
	*/
	function user($user = null) {
		static $_user = 'root';
		static $set = false;

		if (!is_null($user)) {
			$_user = trim($user);
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESSION_USER'])) {
				return $GLOBALS['ADODB_SESSION_USER'];
			}
		}

		return $_user;
	}

	/*!
	*/
	function password($password = null) {
		static $_password = '';
		static $set = false;

		if (!is_null($password)) {
			$_password = $password;
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESSION_PWD'])) {
				return $GLOBALS['ADODB_SESSION_PWD'];
			}
		}

		return $_password;
	}

	/*!
	*/
	function database($database = null) {
		static $_database = 'xphplens_2';
		static $set = false;

		if (!is_null($database)) {
			$_database = trim($database);
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESSION_DB'])) {
				return $GLOBALS['ADODB_SESSION_DB'];
			}
		}

		return $_database;
	}

	/*!
	*/
	function persist($persist = null) 
	{
		static $_persist = true;

		if (!is_null($persist)) {
			$_persist = trim($persist);
		}

		return $_persist;
	}

	/*!
	*/
	function lifetime($lifetime = null) {
		static $_lifetime;
		static $set = false;

		if (!is_null($lifetime)) {
			$_lifetime = (int) $lifetime;
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESS_LIFE'])) {
				return $GLOBALS['ADODB_SESS_LIFE'];
			}
		}
		if (!$_lifetime) {
			$_lifetime = ini_get('session.gc_maxlifetime');
			if ($_lifetime <= 1) {
				// bug in PHP 4.0.3 pl 1  -- how about other versions?
				//print "<h3>Session Error: PHP.INI setting <i>session.gc_maxlifetime</i>not set: $lifetime</h3>";
				$_lifetime = 1440;
			}
		}

		return $_lifetime;
	}

	/*!
	*/
	function debug($debug = null) {
		static $_debug = false;
		static $set = false;

		if (!is_null($debug)) {
			$_debug = (bool) $debug;

			$conn = ADODB_Session::_conn();
			if ($conn) {
				$conn->debug = $_debug;
			}
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESS_DEBUG'])) {
				return $GLOBALS['ADODB_SESS_DEBUG'];
			}
		}

		return $_debug;
	}

	/*!
	*/
	function expireNotify($expire_notify = null) {
		static $_expire_notify;
		static $set = false;

		if (!is_null($expire_notify)) {
			$_expire_notify = $expire_notify;
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESSION_EXPIRE_NOTIFY'])) {
				return $GLOBALS['ADODB_SESSION_EXPIRE_NOTIFY'];
			}
		}

		return $_expire_notify;
	}

	/*!
	*/
	function table($table = null) {
		static $_table = 'sessions';
		static $set = false;

		if (!is_null($table)) {
			$_table = trim($table);
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESSION_TBL'])) {
				return $GLOBALS['ADODB_SESSION_TBL'];
			}
		}

		return $_table;
	}

	/*!
	*/
	function optimize($optimize = null) {
		static $_optimize = false;
		static $set = false;

		if (!is_null($optimize)) {
			$_optimize = (bool) $optimize;
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (defined('ADODB_SESSION_OPTIMIZE')) {
				return true;
			}
		}

		return $_optimize;
	}

	/*!
	*/
	function syncSeconds($sync_seconds = null) {
		static $_sync_seconds = 60;
		static $set = false;

		if (!is_null($sync_seconds)) {
			$_sync_seconds = (int) $sync_seconds;
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (defined('ADODB_SESSION_SYNCH_SECS')) {
				return ADODB_SESSION_SYNCH_SECS;
			}
		}

		return $_sync_seconds;
	}

	/*!
	*/
	function clob($clob = null) {
		static $_clob = false;
		static $set = false;

		if (!is_null($clob)) {
			$_clob = strtolower(trim($clob));
			$set = true;
		} elseif (!$set) {
			// backwards compatibility
			if (isset($GLOBALS['ADODB_SESSION_USE_LOBS'])) {
				return $GLOBALS['ADODB_SESSION_USE_LOBS'];
			}
		}

		return $_clob;
	}

	/*!
	*/
	function dataFieldName($data_field_name = null) {
		static $_data_field_name = 'data';

		if (!is_null($data_field_name)) {
			$_data_field_name = trim($data_field_name);
		}

		return $_data_field_name;
	}

	/*!
	*/
	function filter($filter = null) {
		static $_filter = array();

		if (!is_null($filter)) {
			if (!is_array($filter)) {
				$filter = array($filter);
			}
			$_filter = $filter;
		}

		return $_filter;
	}

	/*!
	*/
	function encryptionKey($encryption_key = null) {
		static $_encryption_key = 'CRYPTED ADODB SESSIONS ROCK!';

		if (!is_null($encryption_key)) {
			$_encryption_key = $encryption_key;
		}

		return $_encryption_key;
	}

	/////////////////////
	// private methods
	/////////////////////

	/*!
	*/
	function &_conn($conn=null) {
		return $GLOBALS['ADODB_SESS_CONN'];
	}

	/*!
	*/
	function _crc($crc = null) {
		static $_crc = false;

		if (!is_null($crc)) {
			$_crc = $crc;
		}

		return $_crc;
	}

	/*!
	*/
	function _init() {
		session_module_name('user');
		session_set_save_handler(
			array('ADODB_Session', 'open'),
			array('ADODB_Session', 'close'),
			array('ADODB_Session', 'read'),
			array('ADODB_Session', 'write'),
			array('ADODB_Session', 'destroy'),
			array('ADODB_Session', 'gc')
		);
	}


	/*!
	*/
	function _sessionKey() {
		// use this function to create the encryption key for crypted sessions
		// crypt the used key, ADODB_Session::encryptionKey() as key and session_id() as salt
		return crypt(ADODB_Session::encryptionKey(), session_id());
	}

	/*!
	*/
	function _dumprs($rs) {
		$conn	=& ADODB_Session::_conn();
		$debug	= ADODB_Session::debug();

		if (!$conn) {
			return;
		}

		if (!$debug) {
			return;
		}

		if (!$rs) {
			echo "<br />\$rs is null or false<br />\n";
			return;
		}

		//echo "<br />\nAffected_Rows=",$conn->Affected_Rows(),"<br />\n";

		if (!is_object($rs)) {
			return;
		}

		require_once ADODB_SESSION.'/../tohtml.inc.php';
		rs2html($rs);
	}

	/////////////////////
	// public methods
	/////////////////////

	/*!
		Create the connection to the database.

		If $conn already exists, reuse that connection
	*/
	function open($save_path, $session_name, $persist = null) {
		$conn =& ADODB_Session::_conn();

		if ($conn) {
			return true;
		}

		$database	= ADODB_Session::database();
		$debug		= ADODB_Session::debug();
		$driver		= ADODB_Session::driver();
		$host		= ADODB_Session::host();
		$password	= ADODB_Session::password();
		$user		= ADODB_Session::user();

		if (!is_null($persist)) {
			ADODB_Session::persist($persist);
		} else {
			$persist = ADODB_Session::persist();
		}

# these can all be defaulted to in php.ini
#		assert('$database');
#		assert('$driver');
#		assert('$host');

		// cannot use =& below - do not know why...
		$conn =& ADONewConnection($driver);

		if ($debug) {
			$conn->debug = true;
//			ADOConnection::outp( " driver=$driver user=$user pwd=$password db=$database ");
		}

		if ($persist) {
			switch($persist) {
			default:
			case 'P': $ok = $conn->PConnect($host, $user, $password, $database); break;
			case 'C': $ok = $conn->Connect($host, $user, $password, $database); break;
			case 'N': $ok = $conn->NConnect($host, $user, $password, $database); break;
			}
		} else {
			$ok = $conn->Connect($host, $user, $password, $database);
		}

		if ($ok) $GLOBALS['ADODB_SESS_CONN'] =& $conn;
		else
			ADOConnection::outp('<p>Session: connection failed</p>', false);
		

		return $ok;
	}

	/*!
		Close the connection
	*/
	function close() {
/*
		$conn =& ADODB_Session::_conn();
		if ($conn) $conn->Close();
*/
		return true;
	}

	/*
		Slurp in the session variables and return the serialized string
	*/
	function read($key) {
		$conn	=& ADODB_Session::_conn();
		$data	= ADODB_Session::dataFieldName();
		$filter	= ADODB_Session::filter();
		$table	= ADODB_Session::table();

		if (!$conn) {
			return '';
		}

		assert('$table');

		$qkey = $conn->quote($key);
		$binary = $conn->dataProvider === 'mysql' ? '/*! BINARY */' : '';
	
		$sql = "SELECT $data FROM $table WHERE $binary sesskey = $qkey AND expiry >= " . time();
		/* Lock code does not work as it needs to hold transaction within whole page, and we don't know if 
		  developer has commited elsewhere... :(
		 */
		#if (ADODB_Session::Lock())
		#	$rs =& $conn->RowLock($table, "$binary sesskey = $qkey AND expiry >= " . time(), $data);
		#else
		
			$rs =& $conn->Execute($sql);
		//ADODB_Session::_dumprs($rs);
		if ($rs) {
			if ($rs->EOF) {
				$v = '';
			} else {
				$v = reset($rs->fields);
				$filter = array_reverse($filter);
				foreach ($filter as $f) {
					if (is_object($f)) {
						$v = $f->read($v, ADODB_Session::_sessionKey());
					}
				}
				$v = rawurldecode($v);
			}

			$rs->Close();

			ADODB_Session::_crc(strlen($v) . crc32($v));
			return $v;
		}

		return '';
	}

	/*!
		Write the serialized data to a database.

		If the data has not been modified since the last read(), we do not write.
	*/
	function write($key, $val) {
		$clob			= ADODB_Session::clob();
		$conn			=& ADODB_Session::_conn();
		$crc			= ADODB_Session::_crc();
		$data			= ADODB_Session::dataFieldName();
		$debug			= ADODB_Session::debug();
		$driver			= ADODB_Session::driver();
		$expire_notify	= ADODB_Session::expireNotify();
		$filter			= ADODB_Session::filter();
		$lifetime		= ADODB_Session::lifetime();
		$table			= ADODB_Session::table();
	
		if (!$conn) {
			return false;
		}
		$qkey = $conn->qstr($key);
	
		assert('$table');

		$expiry = time() + $lifetime;

		$binary = $conn->dataProvider === 'mysql' ? '/*! BINARY */' : '';

		// crc32 optimization since adodb 2.1
		// now we only update expiry date, thx to sebastian thom in adodb 2.32
		if ($crc !== false && $crc == (strlen($val) . crc32($val))) {
			if ($debug) {
				echo '<p>Session: Only updating date - crc32 not changed</p>';
			}
			$sql = "UPDATE $table SET expiry = ".$conn->Param('0')." WHERE $binary sesskey = ".$conn->Param('1')." AND expiry >= ".$conn->Param('2');
			$rs =& $conn->Execute($sql,array($expiry,$key,time()));
			ADODB_Session::_dumprs($rs);
			if ($rs) {
				$rs->Close();
			}
			return true;
		}
		$val = rawurlencode($val);
		foreach ($filter as $f) {
			if (is_object($f)) {
				$val = $f->write($val, ADODB_Session::_sessionKey());
			}
		}

		$arr = array('sesskey' => $key, 'expiry' => $expiry, $data => $val, 'expireref' => '');
		if ($expire_notify) {
			$var = reset($expire_notify);
			global $$var;
			if (isset($$var)) {
				$arr['expireref'] = $$var;
			}
		}

		if (!$clob) {	// no lobs, simply use replace()
			$arr[$data] = $conn->qstr($val);
			$rs = $conn->Replace($table, $arr, 'sesskey', $autoQuote = true);
			ADODB_Session::_dumprs($rs);
		} else {
			// what value shall we insert/update for lob row?
			switch ($driver) {
				// empty_clob or empty_lob for oracle dbs
				case 'oracle':
				case 'oci8':
				case 'oci8po':
				case 'oci805':
					$lob_value = sprintf('empty_%s()', strtolower($clob));
					break;

				// null for all other
				default:
					$lob_value = 'null';
					break;
			}
			
			// do we insert or update? => as for sesskey
			$rs =& $conn->Execute("SELECT COUNT(*) AS cnt FROM $table WHERE $binary sesskey = $qkey");
			ADODB_Session::_dumprs($rs);
			if ($rs && reset($rs->fields) > 0) {
				$sql = "UPDATE $table SET expiry = $expiry, $data = $lob_value WHERE  sesskey = $qkey";
			} else {
				$sql = "INSERT INTO $table (expiry, $data, sesskey) VALUES ($expiry, $lob_value, $qkey)";
			}
			if ($rs) {
				$rs->Close();
			}

			$err = '';
			$rs1 =& $conn->Execute($sql);
			ADODB_Session::_dumprs($rs1);
			if (!$rs1) {
				$err = $conn->ErrorMsg()."\n";
			}
			$rs2 =& $conn->UpdateBlob($table, $data, $val, " sesskey=$qkey", strtoupper($clob));
			ADODB_Session::_dumprs($rs2);
			if (!$rs2) {
				$err .= $conn->ErrorMsg()."\n";
			}
			$rs = ($rs && $rs2) ? true : false;
			if ($rs1) {
				$rs1->Close();
			}
			if (is_object($rs2)) {
				$rs2->Close();
			}
		}

		if (!$rs) {
			ADOConnection::outp('<p>Session Replace: ' . $conn->ErrorMsg() . '</p>', false);
			return false;
		}  else {
			// bug in access driver (could be odbc?) means that info is not committed
			// properly unless select statement executed in Win2000
			if ($conn->databaseType == 'access') {
				$sql = "SELECT sesskey FROM $table WHERE $binary sesskey = $qkey";
				$rs =& $conn->Execute($sql);
				ADODB_Session::_dumprs($rs);
				if ($rs) {
					$rs->Close();
				}
			}
		}/*
		if (ADODB_Session::Lock()) {
			$conn->CommitTrans();
		}*/
		return $rs ? true : false;
	}

	/*!
	*/
	function destroy($key) {
		$conn			=& ADODB_Session::_conn();
		$table			= ADODB_Session::table();
		$expire_notify	= ADODB_Session::expireNotify();

		if (!$conn) {
			return false;
		}

		assert('$table');

		$qkey = $conn->quote($key);
		$binary = $conn->dataProvider === 'mysql' ? '/*! BINARY */' : '';

		if ($expire_notify) {
			reset($expire_notify);
			$fn = next($expire_notify);
			$savem = $conn->SetFetchMode(ADODB_FETCH_NUM);
			$sql = "SELECT expireref, sesskey FROM $table WHERE $binary sesskey = $qkey";
			$rs =& $conn->Execute($sql);
			ADODB_Session::_dumprs($rs);
			$conn->SetFetchMode($savem);
			if (!$rs) {
				return false;
			}
			if (!$rs->EOF) {
				$ref = $rs->fields[0];
				$key = $rs->fields[1];
				//assert('$ref');
				//assert('$key');
				$fn($ref, $key);
			}
			$rs->Close();
		}

		$sql = "DELETE FROM $table WHERE $binary sesskey = $qkey";
		$rs =& $conn->Execute($sql);
		ADODB_Session::_dumprs($rs);
		if ($rs) {
			$rs->Close();
		}

		return $rs ? true : false;
	}

	/*!
	*/
	function gc($maxlifetime) {
		$conn			=& ADODB_Session::_conn();
		$debug			= ADODB_Session::debug();
		$expire_notify	= ADODB_Session::expireNotify();
		$optimize		= ADODB_Session::optimize();
		$sync_seconds	= ADODB_Session::syncSeconds();
		$table			= ADODB_Session::table();

		if (!$conn) {
			return false;
		}

		assert('$table');

		$time			= time();

		$binary = $conn->dataProvider === 'mysql' ? '/*! BINARY */' : '';

		if ($expire_notify) {
			reset($expire_notify);
			$fn = next($expire_notify);
			$savem = $conn->SetFetchMode(ADODB_FETCH_NUM);
			$sql = "SELECT expireref, sesskey FROM $table WHERE expiry < $time";
			$rs =& $conn->Execute($sql);
			ADODB_Session::_dumprs($rs);
			$conn->SetFetchMode($savem);
			if ($rs) {
				$conn->BeginTrans();
				$keys = array();
				while (!$rs->EOF) {
					$ref = $rs->fields[0];
					$key = $rs->fields[1];
					$fn($ref, $key);
					$del = $conn->Execute("DELETE FROM $table WHERE sesskey='$key'");
					$rs->MoveNext();
				}
				$rs->Close();
				
				$conn->CommitTrans();
			}
		} else {
		
			if (1) {
				$sql = "SELECT sesskey FROM $table WHERE expiry < $time";
				$arr =& $conn->GetAll($sql);
				foreach ($arr as $row) {
					$sql2 = "DELETE FROM $table WHERE sesskey='$row[0]'";
					$conn->Execute($sql2);
				}
			} else {
				$sql = "DELETE FROM $table WHERE expiry < $time";
				$rs =& $conn->Execute($sql);
				ADODB_Session::_dumprs($rs);
				if ($rs) $rs->Close();
			}
			if ($debug) {
				ADOConnection::outp("<p><b>Garbage Collection</b>: $sql</p>");
			}
		}

		// suggested by Cameron, "GaM3R" <gamr@outworld.cx>
		if ($optimize) {
			$driver = ADODB_Session::driver();

			if (preg_match('/mysql/i', $driver)) {
				$sql = "OPTIMIZE TABLE $table";
			}
			if (preg_match('/postgres/i', $driver)) {
				$sql = "VACUUM $table";
			}
			if (!empty($sql)) {
				$conn->Execute($sql);
			}
		}

		if ($sync_seconds) {
			$sql = 'SELECT ';
			if ($conn->dataProvider === 'oci8') {
				$sql .= "TO_CHAR({$conn->sysTimeStamp}, 'RRRR-MM-DD HH24:MI:SS')";
			} else {
				$sql .= $conn->sysTimeStamp;
			}
			$sql .= " FROM $table";

			$rs =& $conn->SelectLimit($sql, 1);
			if ($rs && !$rs->EOF) {
				$dbts = reset($rs->fields);
				$rs->Close();
				$dbt = $conn->UnixTimeStamp($dbts);
				$t = time();

				if (abs($dbt - $t) >= $sync_seconds) {
					$msg = __FILE__ .
						": Server time for webserver {$_SERVER['HTTP_HOST']} not in synch with database: " .
						" database=$dbt ($dbts), webserver=$t (diff=". (abs($dbt - $t) / 60) . ' minutes)';
					error_log($msg);
					if ($debug) {
						ADOConnection::outp("<p>$msg</p>");
					}
				}
			}
		}

		return true;
	}
}

ADODB_Session::_init();


// for backwards compatability only
function adodb_sess_open($save_path, $session_name, $persist = true) {
	return ADODB_Session::open($save_path, $session_name, $persist);
}

// for backwards compatability only
function adodb_sess_gc($t)
{	
	return ADODB_Session::gc($t);
}

?>
