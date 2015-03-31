<?php
/*

@version V5.19  23-Apr-2014  (c) 2000-2014 John Lim (jlim#natsoft.com). All rights reserved.
  Latest version is available at http://adodb.sourceforge.net

  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.

  Active Record implementation. Superset of Zend Framework's.

  Version 0.92

  See http://www-128.ibm.com/developerworks/java/library/j-cb03076/?ca=dgr-lnxw01ActiveRecord
  	for info on Ruby on Rails Active Record implementation
*/


global $_ADODB_ACTIVE_DBS;
global $ADODB_ACTIVE_CACHESECS; // set to true to enable caching of metadata such as field info
global $ACTIVE_RECORD_SAFETY; // set to false to disable safety checks
global $ADODB_ACTIVE_DEFVALS; // use default values of table definition when creating new active record.

// array of ADODB_Active_DB's, indexed by ADODB_Active_Record->_dbat
$_ADODB_ACTIVE_DBS = array();
$ACTIVE_RECORD_SAFETY = true;
$ADODB_ACTIVE_DEFVALS = false;
$ADODB_ACTIVE_CACHESECS = 0;

class ADODB_Active_DB {
	var $db; // ADOConnection
	var $tables; // assoc array of ADODB_Active_Table objects, indexed by tablename
}

class ADODB_Active_Table {
	var $name; // table name
	var $flds; // assoc array of adofieldobjs, indexed by fieldname
	var $keys; // assoc array of primary keys, indexed by fieldname
	var $_created; // only used when stored as a cached file
	var $_belongsTo = array();
	var $_hasMany = array();
}

// $db = database connection
// $index = name of index - can be associative, for an example see
//    http://phplens.com/lens/lensforum/msgs.php?id=17790
// returns index into $_ADODB_ACTIVE_DBS
function ADODB_SetDatabaseAdapter(&$db, $index=false)
{
	global $_ADODB_ACTIVE_DBS;

		foreach($_ADODB_ACTIVE_DBS as $k => $d) {
			if (PHP_VERSION >= 5) {
				if ($d->db === $db) return $k;
			} else {
				if ($d->db->_connectionID === $db->_connectionID && $db->database == $d->db->database)
					return $k;
			}
		}

		$obj = new ADODB_Active_DB();
		$obj->db = $db;
		$obj->tables = array();

		if ($index == false) $index = sizeof($_ADODB_ACTIVE_DBS);


		$_ADODB_ACTIVE_DBS[$index] = $obj;

		return sizeof($_ADODB_ACTIVE_DBS)-1;
}


class ADODB_Active_Record {
	static $_changeNames = true; // dynamically pluralize table names
	static $_quoteNames = false;

	static $_foreignSuffix = '_id'; //
	var $_dbat; // associative index pointing to ADODB_Active_DB eg. $ADODB_Active_DBS[_dbat]
	var $_table; // tablename, if set in class definition then use it as table name
	var $_tableat; // associative index pointing to ADODB_Active_Table, eg $ADODB_Active_DBS[_dbat]->tables[$this->_tableat]
	var $_where; // where clause set in Load()
	var $_saved = false; // indicates whether data is already inserted.
	var $_lasterr = false; // last error message
	var $_original = false; // the original values loaded or inserted, refreshed on update

	var $foreignName; // CFR: class name when in a relationship

	var $lockMode = ' for update '; // you might want to change to

	static function UseDefaultValues($bool=null)
	{
	global $ADODB_ACTIVE_DEFVALS;
		if (isset($bool)) $ADODB_ACTIVE_DEFVALS = $bool;
		return $ADODB_ACTIVE_DEFVALS;
	}

	// should be static
	static function SetDatabaseAdapter(&$db, $index=false)
	{
		return ADODB_SetDatabaseAdapter($db, $index);
	}


	public function __set($name, $value)
	{
		$name = str_replace(' ', '_', $name);
		$this->$name = $value;
	}

	// php5 constructor
	function __construct($table = false, $pkeyarr=false, $db=false)
	{
	global $ADODB_ASSOC_CASE,$_ADODB_ACTIVE_DBS;

		if ($db == false && is_object($pkeyarr)) {
			$db = $pkeyarr;
			$pkeyarr = false;
		}

		if (!$table) {
			if (!empty($this->_table)) $table = $this->_table;
			else $table = $this->_pluralize(get_class($this));
		}
		$this->foreignName = strtolower(get_class($this)); // CFR: default foreign name
		if ($db) {
			$this->_dbat = ADODB_Active_Record::SetDatabaseAdapter($db);
		} else if (!isset($this->_dbat)) {
			if (sizeof($_ADODB_ACTIVE_DBS) == 0) $this->Error("No database connection set; use ADOdb_Active_Record::SetDatabaseAdapter(\$db)",'ADODB_Active_Record::__constructor');
			end($_ADODB_ACTIVE_DBS);
			$this->_dbat = key($_ADODB_ACTIVE_DBS);
		}

		$this->_table = $table;
		$this->_tableat = $table; # reserved for setting the assoc value to a non-table name, eg. the sql string in future

		$this->UpdateActiveTable($pkeyarr);
	}

	function __wakeup()
	{
  		$class = get_class($this);
  		new $class;
	}

	function _pluralize($table)
	{
		if (!ADODB_Active_Record::$_changeNames) return $table;

		$ut = strtoupper($table);
		$len = strlen($table);
		$lastc = $ut[$len-1];
		$lastc2 = substr($ut,$len-2);
		switch ($lastc) {
		case 'S':
			return $table.'es';
		case 'Y':
			return substr($table,0,$len-1).'ies';
		case 'X':
			return $table.'es';
		case 'H':
			if ($lastc2 == 'CH' || $lastc2 == 'SH')
				return $table.'es';
		default:
			return $table.'s';
		}
	}

	// CFR Lamest singular inflector ever - @todo Make it real!
	// Note: There is an assumption here...and it is that the argument's length >= 4
	function _singularize($tables)
	{

		if (!ADODB_Active_Record::$_changeNames) return $table;

		$ut = strtoupper($tables);
		$len = strlen($tables);
		if($ut[$len-1] != 'S')
			return $tables; // I know...forget oxen
		if($ut[$len-2] != 'E')
			return substr($tables, 0, $len-1);
		switch($ut[$len-3])
		{
			case 'S':
			case 'X':
				return substr($tables, 0, $len-2);
			case 'I':
				return substr($tables, 0, $len-3) . 'y';
			case 'H';
				if($ut[$len-4] == 'C' || $ut[$len-4] == 'S')
					return substr($tables, 0, $len-2);
			default:
				return substr($tables, 0, $len-1); // ?
		}
	}

	function hasMany($foreignRef, $foreignKey = false, $foreignClass = 'ADODB_Active_Record')
	{
		$ar = new $foreignClass($foreignRef);
		$ar->foreignName = $foreignRef;
		$ar->UpdateActiveTable();
		$ar->foreignKey = ($foreignKey) ? $foreignKey : $foreignRef.ADODB_Active_Record::$_foreignSuffix;
		$table =& $this->TableInfo();
		$table->_hasMany[$foreignRef] = $ar;
	#	$this->$foreignRef = $this->_hasMany[$foreignRef]; // WATCHME Removed assignment by ref. to please __get()
	}

	// use when you don't want ADOdb to auto-pluralize tablename
	static function TableHasMany($table, $foreignRef, $foreignKey = false, $foreignClass = 'ADODB_Active_Record')
	{
		$ar = new ADODB_Active_Record($table);
		$ar->hasMany($foreignRef, $foreignKey, $foreignClass);
	}

	// use when you don't want ADOdb to auto-pluralize tablename
	static function TableKeyHasMany($table, $tablePKey, $foreignRef, $foreignKey = false, $foreignClass = 'ADODB_Active_Record')
	{
		if (!is_array($tablePKey)) $tablePKey = array($tablePKey);
		$ar = new ADODB_Active_Record($table,$tablePKey);
		$ar->hasMany($foreignRef, $foreignKey, $foreignClass);
	}


	// use when you want ADOdb to auto-pluralize tablename for you. Note that the class must already be defined.
	// e.g. class Person will generate relationship for table Persons
	static function ClassHasMany($parentclass, $foreignRef, $foreignKey = false, $foreignClass = 'ADODB_Active_Record')
	{
		$ar = new $parentclass();
		$ar->hasMany($foreignRef, $foreignKey, $foreignClass);
	}


	function belongsTo($foreignRef,$foreignKey=false, $parentKey='', $parentClass = 'ADODB_Active_Record')
	{
		global $inflector;

		$ar = new $parentClass($this->_pluralize($foreignRef));
		$ar->foreignName = $foreignRef;
		$ar->parentKey = $parentKey;
		$ar->UpdateActiveTable();
		$ar->foreignKey = ($foreignKey) ? $foreignKey : $foreignRef.ADODB_Active_Record::$_foreignSuffix;

		$table =& $this->TableInfo();
		$table->_belongsTo[$foreignRef] = $ar;
	#	$this->$foreignRef = $this->_belongsTo[$foreignRef];
	}

	static function ClassBelongsTo($class, $foreignRef, $foreignKey=false, $parentKey='', $parentClass = 'ADODB_Active_Record')
	{
		$ar = new $class();
		$ar->belongsTo($foreignRef, $foreignKey, $parentKey, $parentClass);
	}

	static function TableBelongsTo($table, $foreignRef, $foreignKey=false, $parentKey='', $parentClass = 'ADODB_Active_Record')
	{
		$ar = new ADOdb_Active_Record($table);
		$ar->belongsTo($foreignRef, $foreignKey, $parentKey, $parentClass);
	}

	static function TableKeyBelongsTo($table, $tablePKey, $foreignRef, $foreignKey=false, $parentKey='', $parentClass = 'ADODB_Active_Record')
	{
		if (!is_array($tablePKey)) $tablePKey = array($tablePKey);
		$ar = new ADOdb_Active_Record($table, $tablePKey);
		$ar->belongsTo($foreignRef, $foreignKey, $parentKey, $parentClass);
	}


	/**
	 * __get Access properties - used for lazy loading
	 *
	 * @param mixed $name
	 * @access protected
	 * @return mixed
	 */
	 function __get($name)
	{
		return $this->LoadRelations($name, '', -1, -1);
	}

	/**
	 * @param string $name
	 * @param string $whereOrderBy : eg. ' AND field1 = value ORDER BY field2'
	 * @param offset
	 * @param limit
	 * @return mixed
	 */
	function LoadRelations($name, $whereOrderBy='', $offset=-1,$limit=-1)
	{
		$extras = array();
		$table = $this->TableInfo();
		if ($limit >= 0) $extras['limit'] = $limit;
		if ($offset >= 0) $extras['offset'] = $offset;

		if (strlen($whereOrderBy))
			if (!preg_match('/^[ \n\r]*AND/i',$whereOrderBy))
				if (!preg_match('/^[ \n\r]*ORDER[ \n\r]/i',$whereOrderBy))
					$whereOrderBy = 'AND '.$whereOrderBy;

		if(!empty($table->_belongsTo[$name]))
		{
			$obj = $table->_belongsTo[$name];
			$columnName = $obj->foreignKey;
			if(empty($this->$columnName))
				$this->$name = null;
			else
			{
				if ($obj->parentKey) $key = $obj->parentKey;
				else $key = reset($table->keys);

				$arrayOfOne = $obj->Find($key.'='.$this->$columnName.' '.$whereOrderBy,false,false,$extras);
				if ($arrayOfOne) {
					$this->$name = $arrayOfOne[0];
					return $arrayOfOne[0];
				}
			}
		}
		if(!empty($table->_hasMany[$name]))
		{
			$obj = $table->_hasMany[$name];
			$key = reset($table->keys);
			$id = @$this->$key;
			if (!is_numeric($id)) {
				$db = $this->DB();
				$id = $db->qstr($id);
			}
			$objs = $obj->Find($obj->foreignKey.'='.$id. ' '.$whereOrderBy,false,false,$extras);
			if (!$objs) $objs = array();
			$this->$name = $objs;
			return $objs;
		}

		return array();
	}
	//////////////////////////////////

	// update metadata
	function UpdateActiveTable($pkeys=false,$forceUpdate=false)
	{
	global $ADODB_ASSOC_CASE,$_ADODB_ACTIVE_DBS , $ADODB_CACHE_DIR, $ADODB_ACTIVE_CACHESECS;
	global $ADODB_ACTIVE_DEFVALS,$ADODB_FETCH_MODE;

		$activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];

		$table = $this->_table;
		$tables = $activedb->tables;
		$tableat = $this->_tableat;
		if (!$forceUpdate && !empty($tables[$tableat])) {

			$acttab = $tables[$tableat];
			foreach($acttab->flds as $name => $fld) {
			if ($ADODB_ACTIVE_DEFVALS && isset($fld->default_value))
				$this->$name = $fld->default_value;
			else
				$this->$name = null;
			}
			return;
		}
		$db = $activedb->db;
		$fname = $ADODB_CACHE_DIR . '/adodb_' . $db->databaseType . '_active_'. $table . '.cache';
		if (!$forceUpdate && $ADODB_ACTIVE_CACHESECS && $ADODB_CACHE_DIR && file_exists($fname)) {
			$fp = fopen($fname,'r');
			@flock($fp, LOCK_SH);
			$acttab = unserialize(fread($fp,100000));
			fclose($fp);
			if ($acttab->_created + $ADODB_ACTIVE_CACHESECS - (abs(rand()) % 16) > time()) {
				// abs(rand()) randomizes deletion, reducing contention to delete/refresh file
				// ideally, you should cache at least 32 secs

				foreach($acttab->flds as $name => $fld) {
					if ($ADODB_ACTIVE_DEFVALS && isset($fld->default_value))
						$this->$name = $fld->default_value;
					else
						$this->$name = null;
				}

				$activedb->tables[$table] = $acttab;

				//if ($db->debug) ADOConnection::outp("Reading cached active record file: $fname");
			  	return;
			} else if ($db->debug) {
				ADOConnection::outp("Refreshing cached active record file: $fname");
			}
		}
		$activetab = new ADODB_Active_Table();
		$activetab->name = $table;

		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		if ($db->fetchMode !== false) $savem = $db->SetFetchMode(false);

		$cols = $db->MetaColumns($table);

		if (isset($savem)) $db->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;

		if (!$cols) {
			$this->Error("Invalid table name: $table",'UpdateActiveTable');
			return false;
		}
		$fld = reset($cols);
		if (!$pkeys) {
			if (isset($fld->primary_key)) {
				$pkeys = array();
				foreach($cols as $name => $fld) {
					if (!empty($fld->primary_key)) $pkeys[] = $name;
				}
			} else
				$pkeys = $this->GetPrimaryKeys($db, $table);
		}
		if (empty($pkeys)) {
			$this->Error("No primary key found for table $table",'UpdateActiveTable');
			return false;
		}

		$attr = array();
		$keys = array();

		switch($ADODB_ASSOC_CASE) {
		case 0:
			foreach($cols as $name => $fldobj) {
				$name = strtolower($name);
                if ($ADODB_ACTIVE_DEFVALS && isset($fldobj->default_value))
                    $this->$name = $fldobj->default_value;
                else
					$this->$name = null;
				$attr[$name] = $fldobj;
			}
			foreach($pkeys as $k => $name) {
				$keys[strtolower($name)] = strtolower($name);
			}
			break;

		case 1:
			foreach($cols as $name => $fldobj) {
				$name = strtoupper($name);

                if ($ADODB_ACTIVE_DEFVALS && isset($fldobj->default_value))
                    $this->$name = $fldobj->default_value;
                else
					$this->$name = null;
				$attr[$name] = $fldobj;
			}

			foreach($pkeys as $k => $name) {
				$keys[strtoupper($name)] = strtoupper($name);
			}
			break;
		default:
			foreach($cols as $name => $fldobj) {
				$name = ($fldobj->name);

                if ($ADODB_ACTIVE_DEFVALS && isset($fldobj->default_value))
                    $this->$name = $fldobj->default_value;
                else
					$this->$name = null;
				$attr[$name] = $fldobj;
			}
			foreach($pkeys as $k => $name) {
				$keys[$name] = $cols[$name]->name;
			}
			break;
		}

		$activetab->keys = $keys;
		$activetab->flds = $attr;

		if ($ADODB_ACTIVE_CACHESECS && $ADODB_CACHE_DIR) {
			$activetab->_created = time();
			$s = serialize($activetab);
			if (!function_exists('adodb_write_file')) include(ADODB_DIR.'/adodb-csvlib.inc.php');
			adodb_write_file($fname,$s);
		}
		if (isset($activedb->tables[$table])) {
			$oldtab = $activedb->tables[$table];

			if ($oldtab) $activetab->_belongsTo = $oldtab->_belongsTo;
			if ($oldtab) $activetab->_hasMany = $oldtab->_hasMany;
		}
		$activedb->tables[$table] = $activetab;
	}

	function GetPrimaryKeys(&$db, $table)
	{
		return $db->MetaPrimaryKeys($table);
	}

	// error handler for both PHP4+5.
	function Error($err,$fn)
	{
	global $_ADODB_ACTIVE_DBS;

		$fn = get_class($this).'::'.$fn;
		$this->_lasterr = $fn.': '.$err;

		if ($this->_dbat < 0) $db = false;
		else {
			$activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];
			$db = $activedb->db;
		}

		if (function_exists('adodb_throw')) {
			if (!$db) adodb_throw('ADOdb_Active_Record', $fn, -1, $err, 0, 0, false);
			else adodb_throw($db->databaseType, $fn, -1, $err, 0, 0, $db);
		} else
			if (!$db || $db->debug) ADOConnection::outp($this->_lasterr);

	}

	// return last error message
	function ErrorMsg()
	{
		if (!function_exists('adodb_throw')) {
			if ($this->_dbat < 0) $db = false;
			else $db = $this->DB();

			// last error could be database error too
			if ($db && $db->ErrorMsg()) return $db->ErrorMsg();
		}
		return $this->_lasterr;
	}

	function ErrorNo()
	{
		if ($this->_dbat < 0) return -9999; // no database connection...
		$db = $this->DB();

		return (int) $db->ErrorNo();
	}


	// retrieve ADOConnection from _ADODB_Active_DBs
	function DB()
	{
	global $_ADODB_ACTIVE_DBS;

		if ($this->_dbat < 0) {
			$false = false;
			$this->Error("No database connection set: use ADOdb_Active_Record::SetDatabaseAdaptor(\$db)", "DB");
			return $false;
		}
		$activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];
		$db = $activedb->db;
		return $db;
	}

	// retrieve ADODB_Active_Table
	function &TableInfo()
	{
	global $_ADODB_ACTIVE_DBS;
		$activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];
		$table = $activedb->tables[$this->_tableat];
		return $table;
	}


	// I have an ON INSERT trigger on a table that sets other columns in the table.
	// So, I find that for myTable, I want to reload an active record after saving it. -- Malcolm Cook
	function Reload()
	{
		$db =& $this->DB(); if (!$db) return false;
		$table =& $this->TableInfo();
		$where = $this->GenWhere($db, $table);
		return($this->Load($where));
	}


	// set a numeric array (using natural table field ordering) as object properties
	function Set(&$row)
	{
	global $ACTIVE_RECORD_SAFETY;

		$db = $this->DB();

		if (!$row) {
			$this->_saved = false;
			return false;
		}

		$this->_saved = true;

		$table = $this->TableInfo();
		if ($ACTIVE_RECORD_SAFETY && sizeof($table->flds) != sizeof($row)) {
            # <AP>
            $bad_size = TRUE;
            if (sizeof($row) == 2 * sizeof($table->flds)) {
                // Only keep string keys
                $keys = array_filter(array_keys($row), 'is_string');
                if (sizeof($keys) == sizeof($table->flds))
                    $bad_size = FALSE;
            }
            if ($bad_size) {
			$this->Error("Table structure of $this->_table has changed","Load");
			return false;
		}
            # </AP>
		}
        else
			$keys = array_keys($row);

        # <AP>
        reset($keys);
        $this->_original = array();
		foreach($table->flds as $name=>$fld) {
            $value = $row[current($keys)];
			$this->$name = $value;
            $this->_original[] = $value;
            next($keys);
		}

        # </AP>
		return true;
	}

	// get last inserted id for INSERT
	function LastInsertID(&$db,$fieldname)
	{
		if ($db->hasInsertID)
			$val = $db->Insert_ID($this->_table,$fieldname);
		else
			$val = false;

		if (is_null($val) || $val === false) {
			// this might not work reliably in multi-user environment
			return $db->GetOne("select max(".$fieldname.") from ".$this->_table);
		}
		return $val;
	}

	// quote data in where clause
	function doquote(&$db, $val,$t)
	{
		switch($t) {
		case 'L':
			if (strpos($db->databaseType,'postgres') !== false) return $db->qstr($val);
		case 'D':
		case 'T':
			if (empty($val)) return 'null';

		case 'B':
		case 'N':
		case 'C':
		case 'X':
			if (is_null($val)) return 'null';

			if (strlen($val)>0 &&
				(strncmp($val,"'",1) != 0 || substr($val,strlen($val)-1,1) != "'")) {
				return $db->qstr($val);
				break;
			}
		default:
			return $val;
			break;
		}
	}

	// generate where clause for an UPDATE/SELECT
	function GenWhere(&$db, &$table)
	{
		$keys = $table->keys;
		$parr = array();

		foreach($keys as $k) {
			$f = $table->flds[$k];
			if ($f) {
				$parr[] = $k.' = '.$this->doquote($db,$this->$k,$db->MetaType($f->type));
			}
		}
		return implode(' and ', $parr);
	}


	function _QName($n,$db=false)
	{
		if (!ADODB_Active_Record::$_quoteNames) return $n;
		if (!$db) $db = $this->DB(); if (!$db) return false;
		return $db->nameQuote.$n.$db->nameQuote;
	}

	//------------------------------------------------------------ Public functions below

	function Load($where=null,$bindarr=false, $lock = false)
	{
	global $ADODB_FETCH_MODE;

		$db = $this->DB(); if (!$db) return false;
		$this->_where = $where;

		$save = $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if ($db->fetchMode !== false) $savem = $db->SetFetchMode(false);

		$qry = "select * from ".$this->_table;

		if($where) {
			$qry .= ' WHERE '.$where;
		}
		if ($lock) $qry .= $this->lockMode;

		$row = $db->GetRow($qry,$bindarr);

		if (isset($savem)) $db->SetFetchMode($savem);
		$ADODB_FETCH_MODE = $save;

		return $this->Set($row);
	}

	function LoadLocked($where=null, $bindarr=false)
	{
		$this->Load($where,$bindarr,true);
	}

	# useful for multiple record inserts
	# see http://phplens.com/lens/lensforum/msgs.php?id=17795
	function Reset()
	{
        $this->_where=null;
        $this->_saved = false;
        $this->_lasterr = false;
        $this->_original = false;
        $vars=get_object_vars($this);
        foreach($vars as $k=>$v){
        	if(substr($k,0,1)!=='_'){
        		$this->{$k}=null;
        	}
        }
        $this->foreignName=strtolower(get_class($this));
        return true;
    }

	// false on error
	function Save()
	{
		if ($this->_saved) $ok = $this->Update();
		else $ok = $this->Insert();

		return $ok;
	}


	// false on error
	function Insert()
	{
		$db = $this->DB(); if (!$db) return false;
		$cnt = 0;
		$table = $this->TableInfo();

		$valarr = array();
		$names = array();
		$valstr = array();

		foreach($table->flds as $name=>$fld) {
			$val = $this->$name;
			if(!is_array($val) || !is_null($val) || !array_key_exists($name, $table->keys)) {
				$valarr[] = $val;
				$names[] = $this->_QName($name,$db);
				$valstr[] = $db->Param($cnt);
				$cnt += 1;
			}
		}

		if (empty($names)){
			foreach($table->flds as $name=>$fld) {
				$valarr[] = null;
				$names[] = $name;
				$valstr[] = $db->Param($cnt);
				$cnt += 1;
			}
		}
		$sql = 'INSERT INTO '.$this->_table."(".implode(',',$names).') VALUES ('.implode(',',$valstr).')';
		$ok = $db->Execute($sql,$valarr);

		if ($ok) {
			$this->_saved = true;
			$autoinc = false;
			foreach($table->keys as $k) {
				if (is_null($this->$k)) {
					$autoinc = true;
					break;
				}
			}
			if ($autoinc && sizeof($table->keys) == 1) {
				$k = reset($table->keys);
				$this->$k = $this->LastInsertID($db,$k);
			}
		}

		$this->_original = $valarr;
		return !empty($ok);
	}

	function Delete()
	{
		$db = $this->DB(); if (!$db) return false;
		$table = $this->TableInfo();

		$where = $this->GenWhere($db,$table);
		$sql = 'DELETE FROM '.$this->_table.' WHERE '.$where;
		$ok = $db->Execute($sql);

		return $ok ? true : false;
	}

	// returns an array of active record objects
	function Find($whereOrderBy,$bindarr=false,$pkeysArr=false,$extra=array())
	{
		$db = $this->DB(); if (!$db || empty($this->_table)) return false;
		$arr = $db->GetActiveRecordsClass(get_class($this),$this->_table, $whereOrderBy,$bindarr,$pkeysArr,$extra);
		return $arr;
	}

	// returns 0 on error, 1 on update, 2 on insert
	function Replace()
	{
	global $ADODB_ASSOC_CASE;

		$db = $this->DB(); if (!$db) return false;
		$table = $this->TableInfo();

		$pkey = $table->keys;

		foreach($table->flds as $name=>$fld) {
			$val = $this->$name;
			/*
			if (is_null($val)) {
				if (isset($fld->not_null) && $fld->not_null) {
					if (isset($fld->default_value) && strlen($fld->default_value)) continue;
					else {
						$this->Error("Cannot update null into $name","Replace");
						return false;
					}
				}
			}*/
			if (is_null($val) && !empty($fld->auto_increment)) {
            	continue;
            }

			if (is_array($val)) continue;

			$t = $db->MetaType($fld->type);
			$arr[$name] = $this->doquote($db,$val,$t);
			$valarr[] = $val;
		}

		if (!is_array($pkey)) $pkey = array($pkey);


		if ($ADODB_ASSOC_CASE == 0)
			foreach($pkey as $k => $v)
				$pkey[$k] = strtolower($v);
		elseif ($ADODB_ASSOC_CASE == 1)
			foreach($pkey as $k => $v)
				$pkey[$k] = strtoupper($v);

		$ok = $db->Replace($this->_table,$arr,$pkey);
		if ($ok) {
			$this->_saved = true; // 1= update 2=insert
			if ($ok == 2) {
				$autoinc = false;
				foreach($table->keys as $k) {
					if (is_null($this->$k)) {
						$autoinc = true;
						break;
					}
				}
				if ($autoinc && sizeof($table->keys) == 1) {
					$k = reset($table->keys);
					$this->$k = $this->LastInsertID($db,$k);
				}
			}

			$this->_original = $valarr;
		}
		return $ok;
	}

	// returns 0 on error, 1 on update, -1 if no change in data (no update)
	function Update()
	{
		$db = $this->DB(); if (!$db) return false;
		$table = $this->TableInfo();

		$where = $this->GenWhere($db, $table);

		if (!$where) {
			$this->error("Where missing for table $table", "Update");
			return false;
		}
		$valarr = array();
		$neworig = array();
		$pairs = array();
		$i = -1;
		$cnt = 0;
		foreach($table->flds as $name=>$fld) {
			$i += 1;
			$val = $this->$name;
			$neworig[] = $val;

			if (isset($table->keys[$name]) || is_array($val))
				continue;

			if (is_null($val)) {
				if (isset($fld->not_null) && $fld->not_null) {
					if (isset($fld->default_value) && strlen($fld->default_value)) continue;
					else {
						$this->Error("Cannot set field $name to NULL","Update");
						return false;
					}
				}
			}

			if (isset($this->_original[$i]) && strcmp($val,$this->_original[$i]) == 0) continue;

			if (is_null($this->_original[$i]) && is_null($val)) continue;

			$valarr[] = $val;
			$pairs[] = $this->_QName($name,$db).'='.$db->Param($cnt);
			$cnt += 1;
		}


		if (!$cnt) return -1;
		$sql = 'UPDATE '.$this->_table." SET ".implode(",",$pairs)." WHERE ".$where;
		$ok = $db->Execute($sql,$valarr);
		if ($ok) {
			$this->_original = $neworig;
			return 1;
		}
		return 0;
	}

	function GetAttributeNames()
	{
		$table = $this->TableInfo();
		if (!$table) return false;
		return array_keys($table->flds);
	}

};

function adodb_GetActiveRecordsClass(&$db, $class, $table,$whereOrderBy,$bindarr, $primkeyArr,
			$extra)
{
global $_ADODB_ACTIVE_DBS;


	$save = $db->SetFetchMode(ADODB_FETCH_NUM);
	$qry = "select * from ".$table;

	if (!empty($whereOrderBy))
		$qry .= ' WHERE '.$whereOrderBy;
	if(isset($extra['limit']))
	{
		$rows = false;
		if(isset($extra['offset'])) {
			$rs = $db->SelectLimit($qry, $extra['limit'], $extra['offset'],$bindarr);
		} else {
			$rs = $db->SelectLimit($qry, $extra['limit'],-1,$bindarr);
		}
		if ($rs) {
			while (!$rs->EOF) {
				$rows[] = $rs->fields;
				$rs->MoveNext();
			}
		}
	} else
		$rows = $db->GetAll($qry,$bindarr);

	$db->SetFetchMode($save);

	$false = false;

	if ($rows === false) {
		return $false;
	}


	if (!class_exists($class)) {
		$db->outp_throw("Unknown class $class in GetActiveRecordsClass()",'GetActiveRecordsClass');
		return $false;
	}
	$arr = array();
	// arrRef will be the structure that knows about our objects.
	// It is an associative array.
	// We will, however, return arr, preserving regular 0.. order so that
	// obj[0] can be used by app developpers.
	$arrRef = array();
	$bTos = array(); // Will store belongTo's indices if any
	foreach($rows as $row) {

		$obj = new $class($table,$primkeyArr,$db);
		if ($obj->ErrorNo()){
			$db->_errorMsg = $obj->ErrorMsg();
			return $false;
		}
		$obj->Set($row);
		$arr[] = $obj;
	} // foreach($rows as $row)

	return $arr;
}
