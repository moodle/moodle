<?php

global $ADODB_INCLUDED_LIB;
$ADODB_INCLUDED_LIB = 1;

/* 
V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. See License.txt. 
  Set tabs to 4 for best viewing.
  
  Less commonly used functions are placed here to reduce size of adodb.inc.php. 
*/ 


// Force key to upper. 
// See also http://www.php.net/manual/en/function.array-change-key-case.php
function _array_change_key_case($an_array)
{
	if (is_array($an_array)) {
		foreach($an_array as $key=>$value)
        	$new_array[strtoupper($key)] = $value;

       	return $new_array;
   }

	return $an_array;
}

function _adodb_replace(&$zthis, $table, $fieldArray, $keyCol, $autoQuote, $has_autoinc)
{
		if (count($fieldArray) == 0) return 0;
		$first = true;
		$uSet = '';
		
		if (!is_array($keyCol)) {
			$keyCol = array($keyCol);
		}
		foreach($fieldArray as $k => $v) {
			if ($autoQuote && !is_numeric($v) and strncmp($v,"'",1) !== 0 and strcasecmp($v,'null')!=0) {
				$v = $zthis->qstr($v);
				$fieldArray[$k] = $v;
			}
			if (in_array($k,$keyCol)) continue; // skip UPDATE if is key
			
			if ($first) {
				$first = false;			
				$uSet = "$k=$v";
			} else
				$uSet .= ",$k=$v";
		}
		 
		$where = false;
		foreach ($keyCol as $v) {
			if ($where) $where .= " and $v=$fieldArray[$v]";
			else $where = "$v=$fieldArray[$v]";
		}
		
		if ($uSet && $where) {
			$update = "UPDATE $table SET $uSet WHERE $where";
			
			$rs = $zthis->Execute($update);
			if ($rs) {
				if ($zthis->poorAffectedRows) {
				/*
				 The Select count(*) wipes out any errors that the update would have returned. 
				http://phplens.com/lens/lensforum/msgs.php?id=5696
				*/
					if ($zthis->ErrorNo()<>0) return 0;
					
				# affected_rows == 0 if update field values identical to old values
				# for mysql - which is silly. 
			
					$cnt = $zthis->GetOne("select count(*) from $table where $where");
					if ($cnt > 0) return 1; // record already exists
				} else
					 if (($zthis->Affected_Rows()>0)) return 1;
			}
		}
	//	print "<p>Error=".$this->ErrorNo().'<p>';
		$first = true;
		foreach($fieldArray as $k => $v) {
			if ($has_autoinc && in_array($k,$keyCol)) continue; // skip autoinc col
			
			if ($first) {
				$first = false;			
				$iCols = "$k";
				$iVals = "$v";
			} else {
				$iCols .= ",$k";
				$iVals .= ",$v";
			}				
		}
		$insert = "INSERT INTO $table ($iCols) VALUES ($iVals)"; 
		$rs = $zthis->Execute($insert);
		return ($rs) ? 2 : 0;
}

// Requires $ADODB_FETCH_MODE = ADODB_FETCH_NUM
function _adodb_getmenu(&$zthis, $name,$defstr='',$blank1stItem=true,$multiple=false,
			$size=0, $selectAttr='',$compareFields0=true)
{
	$hasvalue = false;

	if ($multiple or is_array($defstr)) {
		if ($size==0) $size=5;
		$attr = " multiple size=$size";
		if (!strpos($name,'[]')) $name .= '[]';
	} else if ($size) $attr = " size=$size";
	else $attr ='';

	$s = "<select name=\"$name\"$attr $selectAttr>";
	if ($blank1stItem) 
		if (is_string($blank1stItem))  {
			$barr = explode(':',$blank1stItem);
			if (sizeof($barr) == 1) $barr[] = '';
			$s .= "\n<option value=\"".$barr[0]."\">".$barr[1]."</option>";
		} else $s .= "\n<option></option>";

	if ($zthis->FieldCount() > 1) $hasvalue=true;
	else $compareFields0 = true;
	
	$value = '';
	while(!$zthis->EOF) {
		$zval = trim(reset($zthis->fields));
		if (sizeof($zthis->fields) > 1) {
			if (isset($zthis->fields[1]))
				$zval2 = trim($zthis->fields[1]);
			else
				$zval2 = trim(next($zthis->fields));
		}
		$selected = ($compareFields0) ? $zval : $zval2;
		
		if ($blank1stItem && $zval=="") {
			$zthis->MoveNext();
			continue;
		}
		if ($hasvalue) 
			$value = ' value="'.htmlspecialchars($zval2).'"';
		
		if (is_array($defstr))  {
			
			if (in_array($selected,$defstr)) 
				$s .= "<option selected$value>".htmlspecialchars($zval).'</option>';
			else 
				$s .= "\n<option".$value.'>'.htmlspecialchars($zval).'</option>';
		}
		else {
			if (strcasecmp($selected,$defstr)==0) 
				$s .= "<option selected$value>".htmlspecialchars($zval).'</option>';
			else
				$s .= "\n<option".$value.'>'.htmlspecialchars($zval).'</option>';
		}
		$zthis->MoveNext();
	} // while
	
	return $s ."\n</select>\n";
}

/*
	Count the number of records this sql statement will return by using
	query rewriting techniques...
	
	Does not work with UNIONs.
*/
function _adodb_getcount(&$zthis, $sql,$inputarr=false,$secs2cache=0) 
{
	$qryRecs = 0;
	
	 if (preg_match("/^\s*SELECT\s+DISTINCT/is", $sql) || preg_match('/\s+GROUP\s+BY\s+/is',$sql)) {
		// ok, has SELECT DISTINCT or GROUP BY so see if we can use a table alias
		// but this is only supported by oracle and postgresql...
		if ($zthis->dataProvider == 'oci8') {
			
			$rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','',$sql);
			$rewritesql = "SELECT COUNT(*) FROM ($rewritesql)"; 
			
		} else if ( $zthis->databaseType == 'postgres' || $zthis->databaseType == 'postgres7')  {
			
			$info = $zthis->ServerInfo();
			if (substr($info['version'],0,3) >= 7.1) { // good till version 999
				$rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','',$sql);
				$rewritesql = "SELECT COUNT(*) FROM ($rewritesql) _ADODB_ALIAS_";
			}
		}
	} else { 
		// now replace SELECT ... FROM with SELECT COUNT(*) FROM
		
		$rewritesql = preg_replace(
					'/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) FROM ',$sql);
		
		// fix by alexander zhukov, alex#unipack.ru, because count(*) and 'order by' fails 
		// with mssql, access and postgresql. Also a good speedup optimization - skips sorting!
		$rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','',$rewritesql); 
	}
	
	if (isset($rewritesql) && $rewritesql != $sql) {
		if ($secs2cache) {
			// we only use half the time of secs2cache because the count can quickly
			// become inaccurate if new records are added
			$qryRecs = $zthis->CacheGetOne($secs2cache/2,$rewritesql,$inputarr);
			
		} else {
			$qryRecs = $zthis->GetOne($rewritesql,$inputarr);
	  	}
		if ($qryRecs !== false) return $qryRecs;
	}
	
	//--------------------------------------------
	// query rewrite failed - so try slower way...
	
	// strip off unneeded ORDER BY
	$rewritesql = preg_replace('/(\sORDER\s+BY\s.*)/is','',$sql); 
	$rstest = &$zthis->Execute($rewritesql,$inputarr);
	if ($rstest) {
   		$qryRecs = $rstest->RecordCount();
		if ($qryRecs == -1) { 
		global $ADODB_EXTENSION;
		// some databases will return -1 on MoveLast() - change to MoveNext()
			if ($ADODB_EXTENSION) {
				while(!$rstest->EOF) {
					adodb_movenext($rstest);
				}
			} else {
				while(!$rstest->EOF) {
					$rstest->MoveNext();
				}
			}
			$qryRecs = $rstest->_currentRow;
		}
		$rstest->Close();
		if ($qryRecs == -1) return 0;
	}

	return $qryRecs;
}

/*
 	Code originally from "Cornel G" <conyg@fx.ro>

	This code will not work with SQL that has UNION in it	
	
	Also if you are using CachePageExecute(), there is a strong possibility that
	data will get out of synch. use CachePageExecute() only with tables that
	rarely change.
*/
function &_adodb_pageexecute_all_rows(&$zthis, $sql, $nrows, $page, 
						$inputarr=false, $secs2cache=0) 
{
	$atfirstpage = false;
	$atlastpage = false;
	$lastpageno=1;

	// If an invalid nrows is supplied, 
	// we assume a default value of 10 rows per page
	if (!isset($nrows) || $nrows <= 0) $nrows = 10;

	$qryRecs = false; //count records for no offset
	
	$qryRecs = _adodb_getcount($zthis,$sql,$inputarr,$secs2cache);
	$lastpageno = (int) ceil($qryRecs / $nrows);
	$zthis->_maxRecordCount = $qryRecs;
	
	// If page number <= 1, then we are at the first page
	if (!isset($page) || $page <= 1) {	
		$page = 1;
		$atfirstpage = true;
	}

	// ***** Here we check whether $page is the last page or 
	// whether we are trying to retrieve 
	// a page number greater than the last page number.
	if ($page >= $lastpageno) {
		$page = $lastpageno;
		$atlastpage = true;
	}
	
	// We get the data we want
	$offset = $nrows * ($page-1);
	if ($secs2cache > 0) 
		$rsreturn = &$zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $offset, $inputarr);
	else 
		$rsreturn = &$zthis->SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);

	
	// Before returning the RecordSet, we set the pagination properties we need
	if ($rsreturn) {
		$rsreturn->_maxRecordCount = $qryRecs;
		$rsreturn->rowsPerPage = $nrows;
		$rsreturn->AbsolutePage($page);
		$rsreturn->AtFirstPage($atfirstpage);
		$rsreturn->AtLastPage($atlastpage);
		$rsreturn->LastPageNo($lastpageno);
	}
	return $rsreturn;
}

// Iván Oliva version
function &_adodb_pageexecute_no_last_page(&$zthis, $sql, $nrows, $page, $inputarr=false, $secs2cache=0) 
{

	$atfirstpage = false;
	$atlastpage = false;
	
	if (!isset($page) || $page <= 1) {	// If page number <= 1, then we are at the first page
		$page = 1;
		$atfirstpage = true;
	}
	if ($nrows <= 0) $nrows = 10;	// If an invalid nrows is supplied, we assume a default value of 10 rows per page
	
	// ***** Here we check whether $page is the last page or whether we are trying to retrieve a page number greater than 
	// the last page number.
	$pagecounter = $page + 1;
	$pagecounteroffset = ($pagecounter * $nrows) - $nrows;
	if ($secs2cache>0) $rstest = &$zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $pagecounteroffset, $inputarr);
	else $rstest = &$zthis->SelectLimit($sql, $nrows, $pagecounteroffset, $inputarr, $secs2cache);
	if ($rstest) {
		while ($rstest && $rstest->EOF && $pagecounter>0) {
			$atlastpage = true;
			$pagecounter--;
			$pagecounteroffset = $nrows * ($pagecounter - 1);
			$rstest->Close();
			if ($secs2cache>0) $rstest = &$zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $pagecounteroffset, $inputarr);
			else $rstest = &$zthis->SelectLimit($sql, $nrows, $pagecounteroffset, $inputarr, $secs2cache);
		}
		if ($rstest) $rstest->Close();
	}
	if ($atlastpage) {	// If we are at the last page or beyond it, we are going to retrieve it
		$page = $pagecounter;
		if ($page == 1) $atfirstpage = true;	// We have to do this again in case the last page is the same as the first
			//... page, that is, the recordset has only 1 page.
	}
	
	// We get the data we want
	$offset = $nrows * ($page-1);
	if ($secs2cache > 0) $rsreturn = &$zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $offset, $inputarr);
	else $rsreturn = &$zthis->SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);
	
	// Before returning the RecordSet, we set the pagination properties we need
	if ($rsreturn) {
		$rsreturn->rowsPerPage = $nrows;
		$rsreturn->AbsolutePage($page);
		$rsreturn->AtFirstPage($atfirstpage);
		$rsreturn->AtLastPage($atlastpage);
	}
	return $rsreturn;
}

function _adodb_getupdatesql(&$zthis,&$rs, $arrFields,$forceUpdate=false,$magicq=false)
{
		if (!$rs) {
			printf(ADODB_BAD_RS,'GetUpdateSQL');
			return false;
		}
	
		$fieldUpdatedCount = 0;
		$arrFields = _array_change_key_case($arrFields);

		$hasnumeric = isset($rs->fields[0]);
		$updateSQL = '';
		
		// Loop through all of the fields in the recordset
		for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++) {
		
			// Get the field from the recordset
			$field = $rs->FetchField($i);

			// If the recordset field is one
			// of the fields passed in then process.
			$upperfname = strtoupper($field->name);
			if (adodb_key_exists($upperfname,$arrFields)) {

				// If the existing field value in the recordset
				// is different from the value passed in then
				// go ahead and append the field name and new value to
				// the update query.
				
				if ($hasnumeric) $val = $rs->fields[$i];
				else if (isset($rs->fields[$upperfname])) $val = $rs->fields[$upperfname];
				else if (isset($rs->fields[$field->name])) $val =  $rs->fields[$field->name];
				else if (isset($rs->fields[strtolower($upperfname)])) $val =  $rs->fields[strtolower($upperfname)];
				else $val = '';
				
				if ($forceUpdate || strcmp($val, $arrFields[$upperfname])) {
					// Set the counter for the number of fields that will be updated.
					$fieldUpdatedCount++;

					// Based on the datatype of the field
					// Format the value properly for the database
					$mt = $rs->MetaType($field->type);
					
					// "mike" <mike@partner2partner.com> patch and "Ryan Bailey" <rebel@windriders.com> 
					//PostgreSQL uses a 't' or 'f' and therefore needs to be processed as a string ('C') type field.
					if ((strncmp($zthis->databaseType,"postgres",8) === 0) && ($mt == "L")) $mt = "C";
					// is_null requires php 4.0.4
					if ((defined('ADODB_FORCE_NULLS') && is_null($arrFields[$upperfname])) || $arrFields[$upperfname] === 'null') 
						$updateSQL .= $field->name . " = null, ";
					else		
					switch($mt) {
						case 'null':
						case "C":
						case "X":
						case 'B':
							$updateSQL .= $field->name . " = " . $zthis->qstr($arrFields[$upperfname],$magicq) . ", ";
							break;
						case "D":
							$updateSQL .= $field->name . " = " . $zthis->DBDate($arrFields[$upperfname]) . ", ";
	   						break;
						case "T":
							$updateSQL .= $field->name . " = " . $zthis->DBTimeStamp($arrFields[$upperfname]) . ", ";
							break;
						default:
							$val = $arrFields[$upperfname];
							/*if (!is_numeric($val)) {
								if (strncmp($val,'=',1) == 0) $val = substr($val,1);
								else $val = (float) $val;
							}*/
							if (empty($val)) $val = '0';

							$updateSQL .= $field->name . " = " . $val  . ", ";
							break;
					};
				};
			};
		};

		// If there were any modified fields then build the rest of the update query.
		if ($fieldUpdatedCount > 0 || $forceUpdate) {
		
					// Get the table name from the existing query.
			preg_match("/FROM\s+".ADODB_TABLE_REGEX."/is", $rs->sql, $tableName);
	
			// Get the full where clause excluding the word "WHERE" from
			// the existing query.
			preg_match('/\sWHERE\s(.*)/is', $rs->sql, $whereClause);
			
			$discard = false;
			// not a good hack, improvements?
			if ($whereClause)
				preg_match('/\s(LIMIT\s.*)/is', $whereClause[1], $discard);
			else
				$whereClause = array(false,false);
				
			if ($discard)
				$whereClause[1] = substr($whereClause[1], 0, strlen($whereClause[1]) - strlen($discard[1]));
			
			// updateSQL will contain the full update query when all
			// processing has completed.
			$updateSQL = "UPDATE " . $tableName[1] . " SET ".substr($updateSQL, 0, -2);

			// If the recordset has a where clause then use that same where clause
			// for the update.
			if ($whereClause[1]) $updateSQL .= " WHERE " . $whereClause[1];

			return $updateSQL;
		} else {
			return false;
   		};
}

function adodb_key_exists($key, &$arr)
{
	if (!defined('ADODB_FORCE_NULLS')) {
		// the following is the old behaviour where null or empty fields are ignored
		return (!empty($arr[$key])) || (isset($arr[$key]) && strlen($arr[$key])>0);
	}

	if (isset($arr[$key])) return true;
	## null check below
	if (ADODB_PHPVER >= 0x4010) return array_key_exists($key,$arr);
	return false;
}

function _adodb_getinsertsql(&$zthis,&$rs,$arrFields,$magicq=false)
{
	$values = '';
	$fields = '';
	$arrFields = _array_change_key_case($arrFields);
	
	if (!$rs) {
		printf(ADODB_BAD_RS,'GetInsertSQL');
		return false;
	}

	$fieldInsertedCount = 0;

	// Loop through all of the fields in the recordset
	for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++) {

		// Get the field from the recordset
		$field = $rs->FetchField($i);
		// If the recordset field is one
		// of the fields passed in then process.
		$upperfname = strtoupper($field->name);
		if (adodb_key_exists($upperfname,$arrFields)) {

			// Set the counter for the number of fields that will be inserted.
			$fieldInsertedCount++;

			// Get the name of the fields to insert
			$fields .= $field->name . ", ";
			
			$mt = $rs->MetaType($field->type);
			
			// "mike" <mike@partner2partner.com> patch and "Ryan Bailey" <rebel@windriders.com> 
			//PostgreSQL uses a 't' or 'f' and therefore needs to be processed as a string ('C') type field.
			if ((strncmp($zthis->databaseType,"postgres",8) === 0) && ($mt == "L")) $mt = "C";

			// Based on the datatype of the field
			// Format the value properly for the database
			if ((defined('ADODB_FORCE_NULLS') && is_null($arrFields[$upperfname])) || $arrFields[$upperfname] === 'null') 
					$values .= "null, ";
			else		
			switch($mt) {
				case "C":
				case "X":
				case 'B':
					$values .= $zthis->qstr($arrFields[$upperfname],$magicq) . ", ";
					break;
				case "D":
					$values .= $zthis->DBDate($arrFields[$upperfname]) . ", ";
					break;
				case "T":
					$values .= $zthis->DBTimeStamp($arrFields[$upperfname]) . ", ";
					break;
				default:
					$val = $arrFields[$upperfname];
					/*if (!is_numeric($val)) {
						if (strncmp($val,'=',1) == 0) $val = substr($val,1);
						else $val = (float) $val;
					}*/
					if (empty($val)) $val = '0';
					$values .= $val . ", ";
					break;
			};
		};
  	};

	// If there were any inserted fields then build the rest of the insert query.
	if ($fieldInsertedCount <= 0)  return false;
	
	// Get the table name from the existing query.
	preg_match("/FROM\s+".ADODB_TABLE_REGEX."/is", $rs->sql, $tableName);

	// Strip off the comma and space on the end of both the fields
	// and their values.
	$fields = substr($fields, 0, -2);
	$values = substr($values, 0, -2);

	// Append the fields and their values to the insert query.
	$insertSQL = "INSERT INTO " . $tableName[1] . " ( $fields ) VALUES ( $values )";

	return $insertSQL;
}


?>