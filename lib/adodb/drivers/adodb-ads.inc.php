<?php
/*
  (c) 2000-2014 John Lim (jlim#natsoft.com.my). All rights reserved.
  Portions Copyright (c) 2007-2009, iAnywhere Solutions, Inc.
  All rights reserved. All unpublished rights reserved.

  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.

Set tabs to 4 for best viewing.


NOTE: This driver requires the Advantage PHP client libraries, which
      can be downloaded for free via:
      http://devzone.advantagedatabase.com/dz/content.aspx?key=20

DELPHI FOR PHP USERS:
      The following steps can be taken to utilize this driver from the
      CodeGear Delphi for PHP product:
        1 - See note above, download and install the Advantage PHP client.
        2 - Copy the following files to the Delphi for PHP\X.X\php\ext directory:
              ace32.dll
              axcws32.dll
              adsloc32.dll
              php_advantage.dll (rename the existing php_advantage.dll.5.x.x file)
        3 - Add the following line to the Delphi for PHP\X.X\php\php.ini.template file:
              extension=php_advantage.dll
        4 - To use: enter "ads" as the DriverName on a connection component, and set
            a Host property similar to "DataDirectory=c:\". See the Advantage PHP
            help file topic for ads_connect for details on connection path options
            and formatting.
        5 - (optional) - Modify the Delphi for PHP\X.X\vcl\packages\database.packages.php
            file and add ads to the list of strings returned when registering the
            Database object's DriverName property.

*/
// security - hide paths
if (!defined('ADODB_DIR')) die();

  define("_ADODB_ADS_LAYER", 2 );

/*--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------*/


class ADODB_ads extends ADOConnection {
  var $databaseType = "ads";
  var $fmt = "'m-d-Y'";
  var $fmtTimeStamp = "'Y-m-d H:i:s'";
        var $concat_operator = '';
  var $replaceQuote = "''"; // string to use to replace quotes
  var $dataProvider = "ads";
  var $hasAffectedRows = true;
  var $binmode = ODBC_BINMODE_RETURN;
  var $useFetchArray = false; // setting this to true will make array elements in FETCH_ASSOC mode case-sensitive
                        // breaking backward-compat
  //var $longreadlen = 8000; // default number of chars to return for a Blob/Long field
  var $_bindInputArray = false;
  var $curmode = SQL_CUR_USE_DRIVER; // See sqlext.h, SQL_CUR_DEFAULT == SQL_CUR_USE_DRIVER == 2L
  var $_genSeqSQL = "create table %s (id integer)";
  var $_autocommit = true;
  var $_haserrorfunctions = true;
  var $_has_stupid_odbc_fetch_api_change = true;
  var $_lastAffectedRows = 0;
  var $uCaseTables = true; // for meta* functions, uppercase table names


  function ADODB_ads()
  {
    $this->_haserrorfunctions = ADODB_PHPVER >= 0x4050;
    $this->_has_stupid_odbc_fetch_api_change = ADODB_PHPVER >= 0x4200;
  }

  // returns true or false
  function _connect($argDSN, $argUsername, $argPassword, $argDatabasename)
  {
          global $php_errormsg;

    if (!function_exists('ads_connect')) return null;

    if ($this->debug && $argDatabasename && $this->databaseType != 'vfp') {
      ADOConnection::outp("For Advantage Connect(), $argDatabasename is not used. Place dsn in 1st parameter.");
    }
    if (isset($php_errormsg)) $php_errormsg = '';
    if ($this->curmode === false) $this->_connectionID = ads_connect($argDSN,$argUsername,$argPassword);
    else $this->_connectionID = ads_connect($argDSN,$argUsername,$argPassword,$this->curmode);
    $this->_errorMsg = isset($php_errormsg) ? $php_errormsg : '';
    if (isset($this->connectStmt)) $this->Execute($this->connectStmt);

    return $this->_connectionID != false;
  }

  // returns true or false
  function _pconnect($argDSN, $argUsername, $argPassword, $argDatabasename)
  {
  global $php_errormsg;

    if (!function_exists('ads_connect')) return null;

    if (isset($php_errormsg)) $php_errormsg = '';
    $this->_errorMsg = isset($php_errormsg) ? $php_errormsg : '';
    if ($this->debug && $argDatabasename) {
            ADOConnection::outp("For PConnect(), $argDatabasename is not used. Place dsn in 1st parameter.");
    }
  //  print "dsn=$argDSN u=$argUsername p=$argPassword<br>"; flush();
    if ($this->curmode === false) $this->_connectionID = ads_connect($argDSN,$argUsername,$argPassword);
    else $this->_connectionID = ads_pconnect($argDSN,$argUsername,$argPassword,$this->curmode);

    $this->_errorMsg = isset($php_errormsg) ? $php_errormsg : '';
    if ($this->_connectionID && $this->autoRollback) @ads_rollback($this->_connectionID);
    if (isset($this->connectStmt)) $this->Execute($this->connectStmt);

    return $this->_connectionID != false;
  }

  // returns the Server version and Description
  function ServerInfo()
  {

    if (!empty($this->host) && ADODB_PHPVER >= 0x4300) {
      $stmt = $this->Prepare('EXECUTE PROCEDURE sp_mgGetInstallInfo()');
                        $res =  $this->Execute($stmt);
                        if(!$res)
                                print $this->ErrorMsg();
                        else{
                                $ret["version"]= $res->fields[3];
                                $ret["description"]="Advantage Database Server";
                                return $ret;
                        }
                }
                else {
            return ADOConnection::ServerInfo();
    }
  }


        // returns true or false
        function CreateSequence( $seqname,$start=1)
  {
                $res =  $this->Execute("CREATE TABLE $seqname ( ID autoinc( 1 ) ) IN DATABASE");
                if(!$res){
                        print $this->ErrorMsg();
                        return false;
                }
                else
                        return true;

        }

        // returns true or false
        function DropSequence($seqname)
  {
                $res = $this->Execute("DROP TABLE $seqname");
                if(!$res){
                        print $this->ErrorMsg();
                        return false;
                }
                else
                        return true;
        }


  // returns the generated ID or false
        // checks if the table already exists, else creates the table and inserts a record into the table
        // and gets the ID number of the last inserted record.
        function GenID($seqname,$start=1)
        {
                $go = $this->Execute("select * from $seqname");
                if (!$go){
                        $res = $this->Execute("CREATE TABLE $seqname ( ID autoinc( 1 ) ) IN DATABASE");
                        if(!res){
                                print $this->ErrorMsg();
                                return false;
                        }
                }
                $res = $this->Execute("INSERT INTO $seqname VALUES( DEFAULT )");
                if(!$res){
                        print $this->ErrorMsg();
                        return false;
                }
                else{
                        $gen = $this->Execute("SELECT LastAutoInc( STATEMENT ) FROM system.iota");
                        $ret = $gen->fields[0];
                        return $ret;
                }

        }




  function ErrorMsg()
  {
    if ($this->_haserrorfunctions) {
      if ($this->_errorMsg !== false) return $this->_errorMsg;
      if (empty($this->_connectionID)) return @ads_errormsg();
      return @ads_errormsg($this->_connectionID);
    } else return ADOConnection::ErrorMsg();
  }


  function ErrorNo()
  {

                if ($this->_haserrorfunctions) {
      if ($this->_errorCode !== false) {
        // bug in 4.0.6, error number can be corrupted string (should be 6 digits)
        return (strlen($this->_errorCode)<=2) ? 0 : $this->_errorCode;
      }

      if (empty($this->_connectionID)) $e = @ads_error();
      else $e = @ads_error($this->_connectionID);

       // bug in 4.0.6, error number can be corrupted string (should be 6 digits)
       // so we check and patch
      if (strlen($e)<=2) return 0;
      return $e;
    } else return ADOConnection::ErrorNo();
  }



  function BeginTrans()
  {
    if (!$this->hasTransactions) return false;
    if ($this->transOff) return true;
    $this->transCnt += 1;
    $this->_autocommit = false;
    return ads_autocommit($this->_connectionID,false);
  }

  function CommitTrans($ok=true)
  {
    if ($this->transOff) return true;
    if (!$ok) return $this->RollbackTrans();
    if ($this->transCnt) $this->transCnt -= 1;
    $this->_autocommit = true;
    $ret = ads_commit($this->_connectionID);
    ads_autocommit($this->_connectionID,true);
    return $ret;
  }

  function RollbackTrans()
  {
    if ($this->transOff) return true;
    if ($this->transCnt) $this->transCnt -= 1;
    $this->_autocommit = true;
    $ret = ads_rollback($this->_connectionID);
    ads_autocommit($this->_connectionID,true);
    return $ret;
  }


  // Returns tables,Views or both on succesfull execution. Returns
        // tables by default on succesfull execustion.
  function &MetaTables($ttype)
  {
          $recordSet1 = $this->Execute("select * from system.tables");
                if(!$recordSet1){
                        print $this->ErrorMsg();
                        return false;
                }
                $recordSet2 = $this->Execute("select * from system.views");
                if(!$recordSet2){
                        print $this->ErrorMsg();
                        return false;
                }
                $i=0;
                while (!$recordSet1->EOF){
                                 $arr["$i"] = $recordSet1->fields[0];
                                 $recordSet1->MoveNext();
                                 $i=$i+1;
                }
                if($ttype=='FALSE'){
                        while (!$recordSet2->EOF){
                                $arr["$i"] = $recordSet2->fields[0];
                                $recordSet2->MoveNext();
                                $i=$i+1;
                        }
                        return $arr;
                }
                elseif($ttype=='VIEWS'){
                        while (!$recordSet2->EOF){
                                $arrV["$i"] = $recordSet2->fields[0];
                                $recordSet2->MoveNext();
                                $i=$i+1;
                        }
                        return $arrV;
                }
                else{
                        return $arr;
                }

  }

        function &MetaPrimaryKeys($table)
  {
          $recordSet = $this->Execute("select table_primary_key from system.tables where name='$table'");
                if(!$recordSet){
                        print $this->ErrorMsg();
                        return false;
                }
                $i=0;
                while (!$recordSet->EOF){
                                 $arr["$i"] = $recordSet->fields[0];
                                 $recordSet->MoveNext();
                                 $i=$i+1;
                }
                return $arr;
        }

/*
See http://msdn.microsoft.com/library/default.asp?url=/library/en-us/odbc/htm/odbcdatetime_data_type_changes.asp
/ SQL data type codes /
#define SQL_UNKNOWN_TYPE  0
#define SQL_CHAR      1
#define SQL_NUMERIC    2
#define SQL_DECIMAL    3
#define SQL_INTEGER    4
#define SQL_SMALLINT    5
#define SQL_FLOAT      6
#define SQL_REAL      7
#define SQL_DOUBLE      8
#if (ODBCVER >= 0x0300)
#define SQL_DATETIME    9
#endif
#define SQL_VARCHAR   12


/ One-parameter shortcuts for date/time data types /
#if (ODBCVER >= 0x0300)
#define SQL_TYPE_DATE   91
#define SQL_TYPE_TIME   92
#define SQL_TYPE_TIMESTAMP 93

#define SQL_UNICODE                             (-95)
#define SQL_UNICODE_VARCHAR                     (-96)
#define SQL_UNICODE_LONGVARCHAR                 (-97)
*/
  function ODBCTypes($t)
  {
    switch ((integer)$t) {
    case 1:
    case 12:
    case 0:
    case -95:
    case -96:
      return 'C';
    case -97:
    case -1: //text
      return 'X';
    case -4: //image
      return 'B';

    case 9:
    case 91:
      return 'D';

    case 10:
    case 11:
    case 92:
    case 93:
      return 'T';

    case 4:
    case 5:
    case -6:
      return 'I';

    case -11: // uniqidentifier
      return 'R';
    case -7: //bit
      return 'L';

    default:
      return 'N';
    }
  }

  function &MetaColumns($table)
  {
  global $ADODB_FETCH_MODE;

    $false = false;
    if ($this->uCaseTables) $table = strtoupper($table);
    $schema = '';
    $this->_findschema($table,$schema);

    $savem = $ADODB_FETCH_MODE;
    $ADODB_FETCH_MODE = ADODB_FETCH_NUM;

    /*if (false) { // after testing, confirmed that the following does not work becoz of a bug
      $qid2 = ads_tables($this->_connectionID);
      $rs = new ADORecordSet_ads($qid2);
      $ADODB_FETCH_MODE = $savem;
      if (!$rs) return false;
      $rs->_has_stupid_odbc_fetch_api_change = $this->_has_stupid_odbc_fetch_api_change;
      $rs->_fetch();

      while (!$rs->EOF) {
        if ($table == strtoupper($rs->fields[2])) {
          $q = $rs->fields[0];
          $o = $rs->fields[1];
          break;
        }
        $rs->MoveNext();
      }
      $rs->Close();

      $qid = ads_columns($this->_connectionID,$q,$o,strtoupper($table),'%');
    } */

    switch ($this->databaseType) {
    case 'access':
    case 'vfp':
      $qid = ads_columns($this->_connectionID);#,'%','',strtoupper($table),'%');
      break;


    case 'db2':
            $colname = "%";
            $qid = ads_columns($this->_connectionID, "", $schema, $table, $colname);
            break;

    default:
      $qid = @ads_columns($this->_connectionID,'%','%',strtoupper($table),'%');
      if (empty($qid)) $qid = ads_columns($this->_connectionID);
      break;
    }
    if (empty($qid)) return $false;

    $rs = new ADORecordSet_ads($qid);
    $ADODB_FETCH_MODE = $savem;

    if (!$rs) return $false;
    $rs->_has_stupid_odbc_fetch_api_change = $this->_has_stupid_odbc_fetch_api_change;
    $rs->_fetch();

    $retarr = array();

    /*
    $rs->fields indices
    0 TABLE_QUALIFIER
    1 TABLE_SCHEM
    2 TABLE_NAME
    3 COLUMN_NAME
    4 DATA_TYPE
    5 TYPE_NAME
    6 PRECISION
    7 LENGTH
    8 SCALE
    9 RADIX
    10 NULLABLE
    11 REMARKS
    */
    while (!$rs->EOF) {
    //  adodb_pr($rs->fields);
      if (strtoupper(trim($rs->fields[2])) == $table && (!$schema || strtoupper($rs->fields[1]) == $schema)) {
        $fld = new ADOFieldObject();
        $fld->name = $rs->fields[3];
        $fld->type = $this->ODBCTypes($rs->fields[4]);

        // ref: http://msdn.microsoft.com/library/default.asp?url=/archive/en-us/dnaraccgen/html/msdn_odk.asp
        // access uses precision to store length for char/varchar
        if ($fld->type == 'C' or $fld->type == 'X') {
          if ($this->databaseType == 'access')
            $fld->max_length = $rs->fields[6];
          else if ($rs->fields[4] <= -95) // UNICODE
            $fld->max_length = $rs->fields[7]/2;
          else
            $fld->max_length = $rs->fields[7];
        } else
          $fld->max_length = $rs->fields[7];
        $fld->not_null = !empty($rs->fields[10]);
        $fld->scale = $rs->fields[8];
        $retarr[strtoupper($fld->name)] = $fld;
      } else if (sizeof($retarr)>0)
        break;
      $rs->MoveNext();
    }
    $rs->Close(); //-- crashes 4.03pl1 -- why?

    if (empty($retarr)) $retarr = false;
    return $retarr;
  }

        // Returns an array of columns names for a given table
        function &MetaColumnNames($table)
        {
                $recordSet = $this->Execute("select name from system.columns where parent='$table'");
                if(!$recordSet){
                        print $this->ErrorMsg();
                        return false;
                }
                else{
                        $i=0;
                        while (!$recordSet->EOF){
                                $arr["FIELD$i"] = $recordSet->fields[0];
                                $recordSet->MoveNext();
                                $i=$i+1;
                        }
                        return $arr;
                }
        }


  function Prepare($sql)
  {
    if (! $this->_bindInputArray) return $sql; // no binding
    $stmt = ads_prepare($this->_connectionID,$sql);
    if (!$stmt) {
      // we don't know whether odbc driver is parsing prepared stmts, so just return sql
      return $sql;
    }
    return array($sql,$stmt,false);
  }

  /* returns queryID or false */
  function _query($sql,$inputarr=false)
  {
  GLOBAL $php_errormsg;
    if (isset($php_errormsg)) $php_errormsg = '';
    $this->_error = '';

                if ($inputarr) {
      if (is_array($sql)) {
        $stmtid = $sql[1];
      } else {
        $stmtid = ads_prepare($this->_connectionID,$sql);

        if ($stmtid == false) {
          $this->_errorMsg = isset($php_errormsg) ? $php_errormsg : '';
          return false;
        }
      }

      if (! ads_execute($stmtid,$inputarr)) {
        //@ads_free_result($stmtid);
        if ($this->_haserrorfunctions) {
          $this->_errorMsg = ads_errormsg();
          $this->_errorCode = ads_error();
        }
        return false;
      }

    } else if (is_array($sql)) {
      $stmtid = $sql[1];
      if (!ads_execute($stmtid)) {
        //@ads_free_result($stmtid);
        if ($this->_haserrorfunctions) {
          $this->_errorMsg = ads_errormsg();
          $this->_errorCode = ads_error();
        }
        return false;
      }
    } else
                        {

      $stmtid = ads_exec($this->_connectionID,$sql);

                        }

                $this->_lastAffectedRows = 0;

    if ($stmtid)
                {

      if (@ads_num_fields($stmtid) == 0) {
        $this->_lastAffectedRows = ads_num_rows($stmtid);
        $stmtid = true;

      } else {

        $this->_lastAffectedRows = 0;
        ads_binmode($stmtid,$this->binmode);
        ads_longreadlen($stmtid,$this->maxblobsize);

      }

      if ($this->_haserrorfunctions)
                        {

        $this->_errorMsg = '';
        $this->_errorCode = 0;
      }
                        else
        $this->_errorMsg = isset($php_errormsg) ? $php_errormsg : '';
    }
                else
                {
      if ($this->_haserrorfunctions) {
        $this->_errorMsg = ads_errormsg();
        $this->_errorCode = ads_error();
      } else
        $this->_errorMsg = isset($php_errormsg) ? $php_errormsg : '';
    }

    return $stmtid;

  }

  /*
    Insert a null into the blob field of the table first.
    Then use UpdateBlob to store the blob.

    Usage:

    $conn->Execute('INSERT INTO blobtable (id, blobcol) VALUES (1, null)');
    $conn->UpdateBlob('blobtable','blobcol',$blob,'id=1');
   */
  function UpdateBlob($table,$column,$val,$where,$blobtype='BLOB')
  {
                $sql = "UPDATE $table SET $column=? WHERE $where";
                $stmtid = ads_prepare($this->_connectionID,$sql);
                if ($stmtid == false){
                  $this->_errorMsg = isset($php_errormsg) ? $php_errormsg : '';
                  return false;
          }
                if (! ads_execute($stmtid,array($val),array(SQL_BINARY) )){
                        if ($this->_haserrorfunctions){
                                $this->_errorMsg = ads_errormsg();
                    $this->_errorCode = ads_error();
            }
                        return false;
           }
                 return TRUE;
        }

  // returns true or false
  function _close()
  {
    $ret = @ads_close($this->_connectionID);
    $this->_connectionID = false;
    return $ret;
  }

  function _affectedrows()
  {
    return $this->_lastAffectedRows;
  }

}

/*--------------------------------------------------------------------------------------
   Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_ads extends ADORecordSet {

  var $bind = false;
  var $databaseType = "ads";
  var $dataProvider = "ads";
  var $useFetchArray;
  var $_has_stupid_odbc_fetch_api_change;

  function ADORecordSet_ads($id,$mode=false)
  {
    if ($mode === false) {
      global $ADODB_FETCH_MODE;
      $mode = $ADODB_FETCH_MODE;
    }
    $this->fetchMode = $mode;

    $this->_queryID = $id;

    // the following is required for mysql odbc driver in 4.3.1 -- why?
    $this->EOF = false;
    $this->_currentRow = -1;
    //$this->ADORecordSet($id);
  }


  // returns the field object
  function &FetchField($fieldOffset = -1)
  {

    $off=$fieldOffset+1; // offsets begin at 1

    $o= new ADOFieldObject();
    $o->name = @ads_field_name($this->_queryID,$off);
    $o->type = @ads_field_type($this->_queryID,$off);
    $o->max_length = @ads_field_len($this->_queryID,$off);
    if (ADODB_ASSOC_CASE == 0) $o->name = strtolower($o->name);
    else if (ADODB_ASSOC_CASE == 1) $o->name = strtoupper($o->name);
    return $o;
  }

  /* Use associative array to get fields array */
  function Fields($colname)
  {
    if ($this->fetchMode & ADODB_FETCH_ASSOC) return $this->fields[$colname];
    if (!$this->bind) {
      $this->bind = array();
      for ($i=0; $i < $this->_numOfFields; $i++) {
        $o = $this->FetchField($i);
        $this->bind[strtoupper($o->name)] = $i;
      }
    }

     return $this->fields[$this->bind[strtoupper($colname)]];
  }


  function _initrs()
  {
  global $ADODB_COUNTRECS;
    $this->_numOfRows = ($ADODB_COUNTRECS) ? @ads_num_rows($this->_queryID) : -1;
    $this->_numOfFields = @ads_num_fields($this->_queryID);
    // some silly drivers such as db2 as/400 and intersystems cache return _numOfRows = 0
    if ($this->_numOfRows == 0) $this->_numOfRows = -1;
    //$this->useFetchArray = $this->connection->useFetchArray;
    $this->_has_stupid_odbc_fetch_api_change = ADODB_PHPVER >= 0x4200;
  }

  function _seek($row)
  {
    return false;
  }

  // speed up SelectLimit() by switching to ADODB_FETCH_NUM as ADODB_FETCH_ASSOC is emulated
  function &GetArrayLimit($nrows,$offset=-1)
  {
    if ($offset <= 0) {
      $rs =& $this->GetArray($nrows);
      return $rs;
    }
    $savem = $this->fetchMode;
    $this->fetchMode = ADODB_FETCH_NUM;
    $this->Move($offset);
    $this->fetchMode = $savem;

    if ($this->fetchMode & ADODB_FETCH_ASSOC) {
      $this->fields =& $this->GetRowAssoc(ADODB_ASSOC_CASE);
    }

    $results = array();
    $cnt = 0;
    while (!$this->EOF && $nrows != $cnt) {
      $results[$cnt++] = $this->fields;
      $this->MoveNext();
    }

    return $results;
  }


  function MoveNext()
  {
    if ($this->_numOfRows != 0 && !$this->EOF) {
      $this->_currentRow++;
      if( $this->_fetch() ) {
          return true;
      }
    }
    $this->fields = false;
    $this->EOF = true;
    return false;
  }

  function _fetch()
  {
    $this->fields = false;
    if ($this->_has_stupid_odbc_fetch_api_change)
      $rez = @ads_fetch_into($this->_queryID,$this->fields);
    else {
      $row = 0;
      $rez = @ads_fetch_into($this->_queryID,$row,$this->fields);
    }
    if ($rez) {
      if ($this->fetchMode & ADODB_FETCH_ASSOC) {
        $this->fields =& $this->GetRowAssoc(ADODB_ASSOC_CASE);
      }
      return true;
    }
    return false;
  }

  function _close()
  {
    return @ads_free_result($this->_queryID);
  }

}
