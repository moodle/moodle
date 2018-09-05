<?php
// security - hide paths
if (!defined('ADODB_DIR')) die();

global $ADODB_INCLUDED_LIB;
$ADODB_INCLUDED_LIB = 1;

/*
  @version   v5.20.9  21-Dec-2016
  @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
  @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence. See License.txt.
  Set tabs to 4 for best viewing.

  Less commonly used functions are placed here to reduce size of adodb.inc.php.
*/

function adodb_strip_order_by($sql)
{
	$rez = preg_match('/(\sORDER\s+BY\s(?:[^)](?!LIMIT))*)/is', $sql, $arr);
	if ($arr)
		if (strpos($arr[1], '(') !== false) {
			$at = strpos($sql, $arr[1]);
			$cntin = 0;
			for ($i=$at, $max=strlen($sql); $i < $max; $i++) {
				$ch = $sql[$i];
				if ($ch == '(') {
					$cntin += 1;
				} elseif($ch == ')') {
					$cntin -= 1;
					if ($cntin < 0) {
						break;
					}
				}
			}
			$sql = substr($sql,0,$at).substr($sql,$i);
		} else {
			$sql = str_replace($arr[1], '', $sql);
		}
	return $sql;
}

if (false) {
	$sql = 'select * from (select a from b order by a(b),b(c) desc)';
	$sql = '(select * from abc order by 1)';
	die(adodb_strip_order_by($sql));
}

function adodb_probetypes(&$array,&$types,$probe=8)
{
// probe and guess the type
	$types = array();
	if ($probe > sizeof($array)) $max = sizeof($array);
	else $max = $probe;


	for ($j=0;$j < $max; $j++) {
		$row = $array[$j];
		if (!$row) break;
		$i = -1;
		foreach($row as $v) {
			$i += 1;

			if (isset($types[$i]) && $types[$i]=='C') continue;

			//print " ($i ".$types[$i]. "$v) ";
			$v = trim($v);

			if (!preg_match('/^[+-]{0,1}[0-9\.]+$/',$v)) {
				$types[$i] = 'C'; // once C, always C

				continue;
			}
			if ($j == 0) {
			// If empty string, we presume is character
			// test for integer for 1st row only
			// after that it is up to testing other rows to prove
			// that it is not an integer
				if (strlen($v) == 0) $types[$i] = 'C';
				if (strpos($v,'.') !== false) $types[$i] = 'N';
				else  $types[$i] = 'I';
				continue;
			}

			if (strpos($v,'.') !== false) $types[$i] = 'N';

		}
	}

}

function  adodb_transpose(&$arr, &$newarr, &$hdr, &$fobjs)
{
	$oldX = sizeof(reset($arr));
	$oldY = sizeof($arr);

	if ($hdr) {
		$startx = 1;
		$hdr = array('Fields');
		for ($y = 0; $y < $oldY; $y++) {
			$hdr[] = $arr[$y][0];
		}
	} else
		$startx = 0;

	for ($x = $startx; $x < $oldX; $x++) {
		if ($fobjs) {
			$o = $fobjs[$x];
			$newarr[] = array($o->name);
		} else
			$newarr[] = array();

		for ($y = 0; $y < $oldY; $y++) {
			$newarr[$x-$startx][] = $arr[$y][$x];
		}
	}
}

// Force key to upper.
// See also http://www.php.net/manual/en/function.array-change-key-case.php
function _array_change_key_case($an_array)
{
	if (is_array($an_array)) {
		$new_array = array();
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
			if ($v === null) {
				$v = 'NULL';
				$fieldArray[$k] = $v;
			} else if ($autoQuote && /*!is_numeric($v) /*and strncmp($v,"'",1) !== 0 -- sql injection risk*/ strcasecmp($v,$zthis->null2null)!=0) {
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
			if (isset($fieldArray[$v])) {
				if ($where) $where .= ' and '.$v.'='.$fieldArray[$v];
				else $where = $v.'='.$fieldArray[$v];
			}
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
				} else {
					if (($zthis->Affected_Rows()>0)) return 1;
				}
			} else
				return 0;
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
		$attr = ' multiple size="'.$size.'"';
		if (!strpos($name,'[]')) $name .= '[]';
	} else if ($size) $attr = ' size="'.$size.'"';
	else $attr ='';

	$s = '<select name="'.$name.'"'.$attr.' '.$selectAttr.'>';
	if ($blank1stItem)
		if (is_string($blank1stItem))  {
			$barr = explode(':',$blank1stItem);
			if (sizeof($barr) == 1) $barr[] = '';
			$s .= "\n<option value=\"".$barr[0]."\">".$barr[1]."</option>";
		} else $s .= "\n<option></option>";

	if ($zthis->FieldCount() > 1) $hasvalue=true;
	else $compareFields0 = true;

	$value = '';
    $optgroup = null;
    $firstgroup = true;
    $fieldsize = $zthis->FieldCount();
	while(!$zthis->EOF) {
		$zval = rtrim(reset($zthis->fields));

		if ($blank1stItem && $zval=="") {
			$zthis->MoveNext();
			continue;
		}

        if ($fieldsize > 1) {
			if (isset($zthis->fields[1]))
				$zval2 = rtrim($zthis->fields[1]);
			else
				$zval2 = rtrim(next($zthis->fields));
		}
		$selected = ($compareFields0) ? $zval : $zval2;

        $group = '';
		if ($fieldsize > 2) {
            $group = rtrim($zthis->fields[2]);
        }
/*
        if ($optgroup != $group) {
            $optgroup = $group;
            if ($firstgroup) {
                $firstgroup = false;
                $s .="\n<optgroup label='". htmlspecialchars($group) ."'>";
            } else {
                $s .="\n</optgroup>";
                $s .="\n<optgroup label='". htmlspecialchars($group) ."'>";
            }
		}
*/
		if ($hasvalue)
			$value = " value='".htmlspecialchars($zval2)."'";

		if (is_array($defstr))  {

			if (in_array($selected,$defstr))
				$s .= "\n<option selected='selected'$value>".htmlspecialchars($zval).'</option>';
			else
				$s .= "\n<option".$value.'>'.htmlspecialchars($zval).'</option>';
		}
		else {
			if (strcasecmp($selected,$defstr)==0)
				$s .= "\n<option selected='selected'$value>".htmlspecialchars($zval).'</option>';
			else
				$s .= "\n<option".$value.'>'.htmlspecialchars($zval).'</option>';
		}
		$zthis->MoveNext();
	} // while

    // closing last optgroup
    if($optgroup != null) {
        $s .= "\n</optgroup>";
	}
	return $s ."\n</select>\n";
}

// Requires $ADODB_FETCH_MODE = ADODB_FETCH_NUM
function _adodb_getmenu_gp(&$zthis, $name,$defstr='',$blank1stItem=true,$multiple=false,
			$size=0, $selectAttr='',$compareFields0=true)
{
	$hasvalue = false;

	if ($multiple or is_array($defstr)) {
		if ($size==0) $size=5;
		$attr = ' multiple size="'.$size.'"';
		if (!strpos($name,'[]')) $name .= '[]';
	} else if ($size) $attr = ' size="'.$size.'"';
	else $attr ='';

	$s = '<select name="'.$name.'"'.$attr.' '.$selectAttr.'>';
	if ($blank1stItem)
		if (is_string($blank1stItem))  {
			$barr = explode(':',$blank1stItem);
			if (sizeof($barr) == 1) $barr[] = '';
			$s .= "\n<option value=\"".$barr[0]."\">".$barr[1]."</option>";
		} else $s .= "\n<option></option>";

	if ($zthis->FieldCount() > 1) $hasvalue=true;
	else $compareFields0 = true;

	$value = '';
    $optgroup = null;
    $firstgroup = true;
    $fieldsize = sizeof($zthis->fields);
	while(!$zthis->EOF) {
		$zval = rtrim(reset($zthis->fields));

		if ($blank1stItem && $zval=="") {
			$zthis->MoveNext();
			continue;
		}

        if ($fieldsize > 1) {
			if (isset($zthis->fields[1]))
				$zval2 = rtrim($zthis->fields[1]);
			else
				$zval2 = rtrim(next($zthis->fields));
		}
		$selected = ($compareFields0) ? $zval : $zval2;

        $group = '';
		if (isset($zthis->fields[2])) {
            $group = rtrim($zthis->fields[2]);
        }

        if ($optgroup != $group) {
            $optgroup = $group;
            if ($firstgroup) {
                $firstgroup = false;
                $s .="\n<optgroup label='". htmlspecialchars($group) ."'>";
            } else {
                $s .="\n</optgroup>";
                $s .="\n<optgroup label='". htmlspecialchars($group) ."'>";
            }
		}

		if ($hasvalue)
			$value = " value='".htmlspecialchars($zval2)."'";

		if (is_array($defstr))  {

			if (in_array($selected,$defstr))
				$s .= "\n<option selected='selected'$value>".htmlspecialchars($zval).'</option>';
			else
				$s .= "\n<option".$value.'>'.htmlspecialchars($zval).'</option>';
		}
		else {
			if (strcasecmp($selected,$defstr)==0)
				$s .= "\n<option selected='selected'$value>".htmlspecialchars($zval).'</option>';
			else
				$s .= "\n<option".$value.'>'.htmlspecialchars($zval).'</option>';
		}
		$zthis->MoveNext();
	} // while

    // closing last optgroup
    if($optgroup != null) {
        $s .= "\n</optgroup>";
	}
	return $s ."\n</select>\n";
}


/*
	Count the number of records this sql statement will return by using
	query rewriting heuristics...

	Does not work with UNIONs, except with postgresql and oracle.

	Usage:

	$conn->Connect(...);
	$cnt = _adodb_getcount($conn, $sql);

*/
function _adodb_getcount(&$zthis, $sql,$inputarr=false,$secs2cache=0)
{
	$qryRecs = 0;

	 if (!empty($zthis->_nestedSQL) || preg_match("/^\s*SELECT\s+DISTINCT/is", $sql) ||
	 	preg_match('/\s+GROUP\s+BY\s+/is',$sql) ||
		preg_match('/\s+UNION\s+/is',$sql)) {

		$rewritesql = adodb_strip_order_by($sql);

		// ok, has SELECT DISTINCT or GROUP BY so see if we can use a table alias
		// but this is only supported by oracle and postgresql...
		if ($zthis->dataProvider == 'oci8') {
			// Allow Oracle hints to be used for query optimization, Chris Wrye
			if (preg_match('#/\\*+.*?\\*\\/#', $sql, $hint)) {
				$rewritesql = "SELECT ".$hint[0]." COUNT(*) FROM (".$rewritesql.")";
			} else
				$rewritesql = "SELECT COUNT(*) FROM (".$rewritesql.")";

		} else if (strncmp($zthis->databaseType,'postgres',8) == 0 || strncmp($zthis->databaseType,'mysql',5) == 0)  {
			$rewritesql = "SELECT COUNT(*) FROM ($rewritesql) _ADODB_ALIAS_";
		} else {
			$rewritesql = "SELECT COUNT(*) FROM ($rewritesql)";
		}
	} else {
		// now replace SELECT ... FROM with SELECT COUNT(*) FROM
		if ( strpos($sql, '_ADODB_COUNT') !== FALSE ) {
			$rewritesql = preg_replace('/^\s*?SELECT\s+_ADODB_COUNT(.*)_ADODB_COUNT\s/is','SELECT COUNT(*) ',$sql);
		} else {
			$rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) FROM ',$sql);
		}
		// fix by alexander zhukov, alex#unipack.ru, because count(*) and 'order by' fails
		// with mssql, access and postgresql. Also a good speedup optimization - skips sorting!
		// also see http://phplens.com/lens/lensforum/msgs.php?id=12752
		$rewritesql = adodb_strip_order_by($rewritesql);
	}

	if (isset($rewritesql) && $rewritesql != $sql) {
		if (preg_match('/\sLIMIT\s+[0-9]+/i',$sql,$limitarr)) $rewritesql .= $limitarr[0];

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


	// strip off unneeded ORDER BY if no UNION
	if (preg_match('/\s*UNION\s*/is', $sql)) $rewritesql = $sql;
	else $rewritesql = $rewritesql = adodb_strip_order_by($sql);

	if (preg_match('/\sLIMIT\s+[0-9]+/i',$sql,$limitarr)) $rewritesql .= $limitarr[0];

	if ($secs2cache) {
		$rstest = $zthis->CacheExecute($secs2cache,$rewritesql,$inputarr);
		if (!$rstest) $rstest = $zthis->CacheExecute($secs2cache,$sql,$inputarr);
	} else {
		$rstest = $zthis->Execute($rewritesql,$inputarr);
		if (!$rstest) $rstest = $zthis->Execute($sql,$inputarr);
	}
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

	This code might not work with SQL that has UNION in it

	Also if you are using CachePageExecute(), there is a strong possibility that
	data will get out of synch. use CachePageExecute() only with tables that
	rarely change.
*/
function _adodb_pageexecute_all_rows(&$zthis, $sql, $nrows, $page,
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



	// ***** Here we check whether $page is the last page or
	// whether we are trying to retrieve
	// a page number greater than the last page number.
	if ($page >= $lastpageno) {
		$page = $lastpageno;
		$atlastpage = true;
	}

	// If page number <= 1, then we are at the first page
	if (empty($page) || $page <= 1) {
		$page = 1;
		$atfirstpage = true;
	}

	// We get the data we want
	$offset = $nrows * ($page-1);
	if ($secs2cache > 0)
		$rsreturn = $zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $offset, $inputarr);
	else
		$rsreturn = $zthis->SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);


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
function _adodb_pageexecute_no_last_page(&$zthis, $sql, $nrows, $page, $inputarr=false, $secs2cache=0)
{

	$atfirstpage = false;
	$atlastpage = false;

	if (!isset($page) || $page <= 1) {
		// If page number <= 1, then we are at the first page
		$page = 1;
		$atfirstpage = true;
	}
	if ($nrows <= 0) {
		// If an invalid nrows is supplied, we assume a default value of 10 rows per page
		$nrows = 10;
	}

	$pagecounteroffset = ($page * $nrows) - $nrows;

	// To find out if there are more pages of rows, simply increase the limit or
	// nrows by 1 and see if that number of records was returned. If it was,
	// then we know there is at least one more page left, otherwise we are on
	// the last page. Therefore allow non-Count() paging with single queries
	// rather than three queries as was done before.
	$test_nrows = $nrows + 1;
	if ($secs2cache > 0) {
		$rsreturn = $zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $pagecounteroffset, $inputarr);
	} else {
		$rsreturn = $zthis->SelectLimit($sql, $test_nrows, $pagecounteroffset, $inputarr, $secs2cache);
	}

	// Now check to see if the number of rows returned was the higher value we asked for or not.
	if ( $rsreturn->_numOfRows == $test_nrows ) {
		// Still at least 1 more row, so we are not on last page yet...
		// Remove the last row from the RS.
		$rsreturn->_numOfRows = ( $rsreturn->_numOfRows - 1 );
	} elseif ( $rsreturn->_numOfRows == 0 && $page > 1 ) {
		// Likely requested a page that doesn't exist, so need to find the last
		// page and return it. Revert to original method and loop through pages
		// until we find some data...
		$pagecounter = $page + 1;
		$pagecounteroffset = ($pagecounter * $nrows) - $nrows;

		$rstest = $rsreturn;
		if ($rstest) {
			while ($rstest && $rstest->EOF && $pagecounter > 0) {
				$atlastpage = true;
				$pagecounter--;
				$pagecounteroffset = $nrows * ($pagecounter - 1);
				$rstest->Close();
				if ($secs2cache>0) {
					$rstest = $zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $pagecounteroffset, $inputarr);
				}
				else {
					$rstest = $zthis->SelectLimit($sql, $nrows, $pagecounteroffset, $inputarr, $secs2cache);
				}
			}
			if ($rstest) $rstest->Close();
		}
		if ($atlastpage) {
			// If we are at the last page or beyond it, we are going to retrieve it
			$page = $pagecounter;
			if ($page == 1) {
				// We have to do this again in case the last page is the same as
				// the first page, that is, the recordset has only 1 page.
				$atfirstpage = true;
			}
		}
		// We get the data we want
		$offset = $nrows * ($page-1);
		if ($secs2cache > 0) {
			$rsreturn = $zthis->CacheSelectLimit($secs2cache, $sql, $nrows, $offset, $inputarr);
		}
		else {
			$rsreturn = $zthis->SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);
		}
	} elseif ( $rsreturn->_numOfRows < $test_nrows ) {
		// Rows is less than what we asked for, so must be at the last page.
		$atlastpage = true;
	}

	// Before returning the RecordSet, we set the pagination properties we need
	if ($rsreturn) {
		$rsreturn->rowsPerPage = $nrows;
		$rsreturn->AbsolutePage($page);
		$rsreturn->AtFirstPage($atfirstpage);
		$rsreturn->AtLastPage($atlastpage);
	}
	return $rsreturn;
}

function _adodb_getupdatesql(&$zthis,&$rs, $arrFields,$forceUpdate=false,$magicq=false,$force=2)
{
	global $ADODB_QUOTE_FIELDNAMES;

		if (!$rs) {
			printf(ADODB_BAD_RS,'GetUpdateSQL');
			return false;
		}

		$fieldUpdatedCount = 0;
		$arrFields = _array_change_key_case($arrFields);

		$hasnumeric = isset($rs->fields[0]);
		$setFields = '';

		// Loop through all of the fields in the recordset
		for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++) {
			// Get the field from the recordset
			$field = $rs->FetchField($i);

			// If the recordset field is one
			// of the fields passed in then process.
			$upperfname = strtoupper($field->name);
			if (adodb_key_exists($upperfname,$arrFields,$force)) {

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
					$type = $rs->MetaType($field->type);


					if ($type == 'null') {
						$type = 'C';
					}

					if ((strpos($upperfname,' ') !== false) || ($ADODB_QUOTE_FIELDNAMES)) {
						switch ($ADODB_QUOTE_FIELDNAMES) {
						case 'LOWER':
							$fnameq = $zthis->nameQuote.strtolower($field->name).$zthis->nameQuote;break;
						case 'NATIVE':
							$fnameq = $zthis->nameQuote.$field->name.$zthis->nameQuote;break;
						case 'UPPER':
						default:
							$fnameq = $zthis->nameQuote.$upperfname.$zthis->nameQuote;break;
						}
					} else
						$fnameq = $upperfname;

                //********************************************************//
                if (is_null($arrFields[$upperfname])
					|| (empty($arrFields[$upperfname]) && strlen($arrFields[$upperfname]) == 0)
                    || $arrFields[$upperfname] === $zthis->null2null
                    )
                {
                    switch ($force) {

                        //case 0:
                        //    //Ignore empty values. This is allready handled in "adodb_key_exists" function.
                        //break;

                        case 1:
                            //Set null
                            $setFields .= $field->name . " = null, ";
                        break;

                        case 2:
                            //Set empty
                            $arrFields[$upperfname] = "";
                            $setFields .= _adodb_column_sql($zthis, 'U', $type, $upperfname, $fnameq,$arrFields, $magicq);
                        break;
						default:
                        case 3:
                            //Set the value that was given in array, so you can give both null and empty values
                            if (is_null($arrFields[$upperfname]) || $arrFields[$upperfname] === $zthis->null2null) {
                                $setFields .= $field->name . " = null, ";
                            } else {
                                $setFields .= _adodb_column_sql($zthis, 'U', $type, $upperfname, $fnameq,$arrFields, $magicq);
                            }
                        break;
                    }
                //********************************************************//
                } else {
						//we do this so each driver can customize the sql for
						//DB specific column types.
						//Oracle needs BLOB types to be handled with a returning clause
						//postgres has special needs as well
						$setFields .= _adodb_column_sql($zthis, 'U', $type, $upperfname, $fnameq,
														  $arrFields, $magicq);
					}
				}
			}
		}

		// If there were any modified fields then build the rest of the update query.
		if ($fieldUpdatedCount > 0 || $forceUpdate) {
					// Get the table name from the existing query.
			if (!empty($rs->tableName)) $tableName = $rs->tableName;
			else {
				preg_match("/FROM\s+".ADODB_TABLE_REGEX."/is", $rs->sql, $tableName);
				$tableName = $tableName[1];
			}
			// Get the full where clause excluding the word "WHERE" from
			// the existing query.
			preg_match('/\sWHERE\s(.*)/is', $rs->sql, $whereClause);

			$discard = false;
			// not a good hack, improvements?
			if ($whereClause) {
			#var_dump($whereClause);
				if (preg_match('/\s(ORDER\s.*)/is', $whereClause[1], $discard));
				else if (preg_match('/\s(LIMIT\s.*)/is', $whereClause[1], $discard));
				else if (preg_match('/\s(FOR UPDATE.*)/is', $whereClause[1], $discard));
				else preg_match('/\s.*(\) WHERE .*)/is', $whereClause[1], $discard); # see http://sourceforge.net/tracker/index.php?func=detail&aid=1379638&group_id=42718&atid=433976
			} else
				$whereClause = array(false,false);

			if ($discard)
				$whereClause[1] = substr($whereClause[1], 0, strlen($whereClause[1]) - strlen($discard[1]));

			$sql = 'UPDATE '.$tableName.' SET '.substr($setFields, 0, -2);
			if (strlen($whereClause[1]) > 0)
				$sql .= ' WHERE '.$whereClause[1];

			return $sql;

		} else {
			return false;
	}
}

function adodb_key_exists($key, &$arr,$force=2)
{
	if ($force<=0) {
		// the following is the old behaviour where null or empty fields are ignored
		return (!empty($arr[$key])) || (isset($arr[$key]) && strlen($arr[$key])>0);
	}

	if (isset($arr[$key])) return true;
	## null check below
	if (ADODB_PHPVER >= 0x4010) return array_key_exists($key,$arr);
	return false;
}

/**
 * There is a special case of this function for the oci8 driver.
 * The proper way to handle an insert w/ a blob in oracle requires
 * a returning clause with bind variables and a descriptor blob.
 *
 *
 */
function _adodb_getinsertsql(&$zthis,&$rs,$arrFields,$magicq=false,$force=2)
{
static $cacheRS = false;
static $cacheSig = 0;
static $cacheCols;
	global $ADODB_QUOTE_FIELDNAMES;

	$tableName = '';
	$values = '';
	$fields = '';
	$recordSet = null;
	$arrFields = _array_change_key_case($arrFields);
	$fieldInsertedCount = 0;

	if (is_string($rs)) {
		//ok we have a table name
		//try and get the column info ourself.
		$tableName = $rs;

		//we need an object for the recordSet
		//because we have to call MetaType.
		//php can't do a $rsclass::MetaType()
		$rsclass = $zthis->rsPrefix.$zthis->databaseType;
		$recordSet = new $rsclass(-1,$zthis->fetchMode);
		$recordSet->connection = $zthis;

		if (is_string($cacheRS) && $cacheRS == $rs) {
			$columns = $cacheCols;
		} else {
			$columns = $zthis->MetaColumns( $tableName );
			$cacheRS = $tableName;
			$cacheCols = $columns;
		}
	} else if (is_subclass_of($rs, 'adorecordset')) {
		if (isset($rs->insertSig) && is_integer($cacheRS) && $cacheRS == $rs->insertSig) {
			$columns = $cacheCols;
		} else {
			for ($i=0, $max=$rs->FieldCount(); $i < $max; $i++)
				$columns[] = $rs->FetchField($i);
			$cacheRS = $cacheSig;
			$cacheCols = $columns;
			$rs->insertSig = $cacheSig++;
		}
		$recordSet = $rs;

	} else {
		printf(ADODB_BAD_RS,'GetInsertSQL');
		return false;
	}

	// Loop through all of the fields in the recordset
	foreach( $columns as $field ) {
		$upperfname = strtoupper($field->name);
		if (adodb_key_exists($upperfname,$arrFields,$force)) {
			$bad = false;
			if ((strpos($upperfname,' ') !== false) || ($ADODB_QUOTE_FIELDNAMES)) {
				switch ($ADODB_QUOTE_FIELDNAMES) {
				case 'LOWER':
					$fnameq = $zthis->nameQuote.strtolower($field->name).$zthis->nameQuote;break;
				case 'NATIVE':
					$fnameq = $zthis->nameQuote.$field->name.$zthis->nameQuote;break;
				case 'UPPER':
				default:
					$fnameq = $zthis->nameQuote.$upperfname.$zthis->nameQuote;break;
				}
			} else
				$fnameq = $upperfname;

			$type = $recordSet->MetaType($field->type);

            /********************************************************/
            if (is_null($arrFields[$upperfname])
                || (empty($arrFields[$upperfname]) && strlen($arrFields[$upperfname]) == 0)
                || $arrFields[$upperfname] === $zthis->null2null
				)
               {
                    switch ($force) {

                        case 0: // we must always set null if missing
							$bad = true;
							break;

                        case 1:
                            $values  .= "null, ";
                        break;

                        case 2:
                            //Set empty
                            $arrFields[$upperfname] = "";
                            $values .= _adodb_column_sql($zthis, 'I', $type, $upperfname, $fnameq,$arrFields, $magicq);
                        break;

						default:
                        case 3:
                            //Set the value that was given in array, so you can give both null and empty values
							if (is_null($arrFields[$upperfname]) || $arrFields[$upperfname] === $zthis->null2null) {
								$values  .= "null, ";
							} else {
                        		$values .= _adodb_column_sql($zthis, 'I', $type, $upperfname, $fnameq, $arrFields, $magicq);
             				}
              			break;
             		} // switch

            /*********************************************************/
			} else {
				//we do this so each driver can customize the sql for
				//DB specific column types.
				//Oracle needs BLOB types to be handled with a returning clause
				//postgres has special needs as well
				$values .= _adodb_column_sql($zthis, 'I', $type, $upperfname, $fnameq,
											   $arrFields, $magicq);
			}

			if ($bad) continue;
			// Set the counter for the number of fields that will be inserted.
			$fieldInsertedCount++;


			// Get the name of the fields to insert
			$fields .= $fnameq . ", ";
		}
	}


	// If there were any inserted fields then build the rest of the insert query.
	if ($fieldInsertedCount <= 0)  return false;

	// Get the table name from the existing query.
	if (!$tableName) {
		if (!empty($rs->tableName)) $tableName = $rs->tableName;
		else if (preg_match("/FROM\s+".ADODB_TABLE_REGEX."/is", $rs->sql, $tableName))
			$tableName = $tableName[1];
		else
			return false;
	}

	// Strip off the comma and space on the end of both the fields
	// and their values.
	$fields = substr($fields, 0, -2);
	$values = substr($values, 0, -2);

	// Append the fields and their values to the insert query.
	return 'INSERT INTO '.$tableName.' ( '.$fields.' ) VALUES ( '.$values.' )';
}


/**
 * This private method is used to help construct
 * the update/sql which is generated by GetInsertSQL and GetUpdateSQL.
 * It handles the string construction of 1 column -> sql string based on
 * the column type.  We want to do 'safe' handling of BLOBs
 *
 * @param string the type of sql we are trying to create
 *                'I' or 'U'.
 * @param string column data type from the db::MetaType() method
 * @param string the column name
 * @param array the column value
 *
 * @return string
 *
 */
function _adodb_column_sql_oci8(&$zthis,$action, $type, $fname, $fnameq, $arrFields, $magicq)
{
    $sql = '';

    // Based on the datatype of the field
    // Format the value properly for the database
    switch($type) {
    case 'B':
        //in order to handle Blobs correctly, we need
        //to do some magic for Oracle

        //we need to create a new descriptor to handle
        //this properly
        if (!empty($zthis->hasReturningInto)) {
            if ($action == 'I') {
                $sql = 'empty_blob(), ';
            } else {
                $sql = $fnameq. '=empty_blob(), ';
            }
            //add the variable to the returning clause array
            //so the user can build this later in
            //case they want to add more to it
            $zthis->_returningArray[$fname] = ':xx'.$fname.'xx';
        } else if (empty($arrFields[$fname])){
            if ($action == 'I') {
                $sql = 'empty_blob(), ';
            } else {
                $sql = $fnameq. '=empty_blob(), ';
            }
        } else {
            //this is to maintain compatibility
            //with older adodb versions.
            $sql = _adodb_column_sql($zthis, $action, $type, $fname, $fnameq, $arrFields, $magicq,false);
        }
        break;

    case "X":
        //we need to do some more magic here for long variables
        //to handle these correctly in oracle.

        //create a safe bind var name
        //to avoid conflicts w/ dupes.
       if (!empty($zthis->hasReturningInto)) {
            if ($action == 'I') {
                $sql = ':xx'.$fname.'xx, ';
            } else {
                $sql = $fnameq.'=:xx'.$fname.'xx, ';
            }
            //add the variable to the returning clause array
            //so the user can build this later in
            //case they want to add more to it
            $zthis->_returningArray[$fname] = ':xx'.$fname.'xx';
        } else {
            //this is to maintain compatibility
            //with older adodb versions.
            $sql = _adodb_column_sql($zthis, $action, $type, $fname, $fnameq, $arrFields, $magicq,false);
        }
        break;

    default:
        $sql = _adodb_column_sql($zthis, $action, $type, $fname, $fnameq,  $arrFields, $magicq,false);
        break;
    }

    return $sql;
}

function _adodb_column_sql(&$zthis, $action, $type, $fname, $fnameq, $arrFields, $magicq, $recurse=true)
{

	if ($recurse) {
		switch($zthis->dataProvider)  {
		case 'postgres':
			if ($type == 'L') $type = 'C';
			break;
		case 'oci8':
			return _adodb_column_sql_oci8($zthis, $action, $type, $fname, $fnameq, $arrFields, $magicq);

		}
	}

	switch($type) {
		case "C":
		case "X":
		case 'B':
			$val = $zthis->qstr($arrFields[$fname],$magicq);
			break;

		case "D":
			$val = $zthis->DBDate($arrFields[$fname]);
			break;

		case "T":
			$val = $zthis->DBTimeStamp($arrFields[$fname]);
			break;

		case "N":
		    $val = $arrFields[$fname];
			if (!is_numeric($val)) $val = str_replace(',', '.', (float)$val);
		    break;

		case "I":
		case "R":
		    $val = $arrFields[$fname];
			if (!is_numeric($val)) $val = (integer) $val;
		    break;

		default:
			$val = str_replace(array("'"," ","("),"",$arrFields[$fname]); // basic sql injection defence
			if (empty($val)) $val = '0';
			break;
	}

	if ($action == 'I') return $val . ", ";


	return $fnameq . "=" . $val  . ", ";

}



function _adodb_debug_execute(&$zthis, $sql, $inputarr)
{
	$ss = '';
	if ($inputarr) {
		foreach($inputarr as $kk=>$vv) {
			if (is_string($vv) && strlen($vv)>64) $vv = substr($vv,0,64).'...';
			if (is_null($vv)) $ss .= "($kk=>null) ";
			else $ss .= "($kk=>'$vv') ";
		}
		$ss = "[ $ss ]";
	}
	$sqlTxt = is_array($sql) ? $sql[0] : $sql;
	/*str_replace(', ','##1#__^LF',is_array($sql) ? $sql[0] : $sql);
	$sqlTxt = str_replace(',',', ',$sqlTxt);
	$sqlTxt = str_replace('##1#__^LF', ', ' ,$sqlTxt);
	*/
	// check if running from browser or command-line
	$inBrowser = isset($_SERVER['HTTP_USER_AGENT']);

	$dbt = $zthis->databaseType;
	if (isset($zthis->dsnType)) $dbt .= '-'.$zthis->dsnType;
	if ($inBrowser) {
		if ($ss) {
			$ss = '<code>'.htmlspecialchars($ss).'</code>';
		}
		if ($zthis->debug === -1)
			ADOConnection::outp( "<br>\n($dbt): ".htmlspecialchars($sqlTxt)." &nbsp; $ss\n<br>\n",false);
		else if ($zthis->debug !== -99)
			ADOConnection::outp( "<hr>\n($dbt): ".htmlspecialchars($sqlTxt)." &nbsp; $ss\n<hr>\n",false);
	} else {
		$ss = "\n   ".$ss;
		if ($zthis->debug !== -99)
			ADOConnection::outp("-----<hr>\n($dbt): ".$sqlTxt." $ss\n-----<hr>\n",false);
	}

	$qID = $zthis->_query($sql,$inputarr);

	/*
		Alexios Fakios notes that ErrorMsg() must be called before ErrorNo() for mssql
		because ErrorNo() calls Execute('SELECT @ERROR'), causing recursion
	*/
	if ($zthis->databaseType == 'mssql') {
	// ErrorNo is a slow function call in mssql, and not reliable in PHP 4.0.6

		if($emsg = $zthis->ErrorMsg()) {
			if ($err = $zthis->ErrorNo()) {
				if ($zthis->debug === -99)
					ADOConnection::outp( "<hr>\n($dbt): ".htmlspecialchars($sqlTxt)." &nbsp; $ss\n<hr>\n",false);

				ADOConnection::outp($err.': '.$emsg);
			}
		}
	} else if (!$qID) {

		if ($zthis->debug === -99)
				if ($inBrowser) ADOConnection::outp( "<hr>\n($dbt): ".htmlspecialchars($sqlTxt)." &nbsp; $ss\n<hr>\n",false);
				else ADOConnection::outp("-----<hr>\n($dbt): ".$sqlTxt."$ss\n-----<hr>\n",false);

		ADOConnection::outp($zthis->ErrorNo() .': '. $zthis->ErrorMsg());
	}

	if ($zthis->debug === 99) _adodb_backtrace(true,9999,2);
	return $qID;
}

# pretty print the debug_backtrace function
function _adodb_backtrace($printOrArr=true,$levels=9999,$skippy=0,$ishtml=null)
{
	if (!function_exists('debug_backtrace')) return '';

	if ($ishtml === null) $html =  (isset($_SERVER['HTTP_USER_AGENT']));
	else $html = $ishtml;

	$fmt =  ($html) ? "</font><font color=#808080 size=-1> %% line %4d, file: <a href=\"file:/%s\">%s</a></font>" : "%% line %4d, file: %s";

	$MAXSTRLEN = 128;

	$s = ($html) ? '<pre align=left>' : '';

	if (is_array($printOrArr)) $traceArr = $printOrArr;
	else $traceArr = debug_backtrace();
	array_shift($traceArr);
	array_shift($traceArr);
	$tabs = sizeof($traceArr)-2;

	foreach ($traceArr as $arr) {
		if ($skippy) {$skippy -= 1; continue;}
		$levels -= 1;
		if ($levels < 0) break;

		$args = array();
		for ($i=0; $i < $tabs; $i++) $s .=  ($html) ? ' &nbsp; ' : "\t";
		$tabs -= 1;
		if ($html) $s .= '<font face="Courier New,Courier">';
		if (isset($arr['class'])) $s .= $arr['class'].'.';
		if (isset($arr['args']))
		 foreach($arr['args'] as $v) {
			if (is_null($v)) $args[] = 'null';
			else if (is_array($v)) $args[] = 'Array['.sizeof($v).']';
			else if (is_object($v)) $args[] = 'Object:'.get_class($v);
			else if (is_bool($v)) $args[] = $v ? 'true' : 'false';
			else {
				$v = (string) @$v;
				$str = htmlspecialchars(str_replace(array("\r","\n"),' ',substr($v,0,$MAXSTRLEN)));
				if (strlen($v) > $MAXSTRLEN) $str .= '...';
				$args[] = $str;
			}
		}
		$s .= $arr['function'].'('.implode(', ',$args).')';


		$s .= @sprintf($fmt, $arr['line'],$arr['file'],basename($arr['file']));

		$s .= "\n";
	}
	if ($html) $s .= '</pre>';
	if ($printOrArr) print $s;

	return $s;
}
/*
function _adodb_find_from($sql)
{

	$sql = str_replace(array("\n","\r"), ' ', $sql);
	$charCount = strlen($sql);

	$inString = false;
	$quote = '';
	$parentheseCount = 0;
	$prevChars = '';
	$nextChars = '';


	for($i = 0; $i < $charCount; $i++) {

    	$char = substr($sql,$i,1);
	    $prevChars = substr($sql,0,$i);
    	$nextChars = substr($sql,$i+1);

		if((($char == "'" || $char == '"' || $char == '`') && substr($prevChars,-1,1) != '\\') && $inString === false) {
			$quote = $char;
			$inString = true;
		}

		elseif((($char == "'" || $char == '"' || $char == '`') && substr($prevChars,-1,1) != '\\') && $inString === true && $quote == $char) {
			$quote = "";
			$inString = false;
		}

		elseif($char == "(" && $inString === false)
			$parentheseCount++;

		elseif($char == ")" && $inString === false && $parentheseCount > 0)
			$parentheseCount--;

		elseif($parentheseCount <= 0 && $inString === false && $char == " " && strtoupper(substr($prevChars,-5,5)) == " FROM")
			return $i;

	}
}
*/
