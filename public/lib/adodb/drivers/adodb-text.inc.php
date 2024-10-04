<?php
/**
 * ADOdb Plain Text driver
 *
 * @deprecated
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

if (! defined("_ADODB_TEXT_LAYER")) {
 define("_ADODB_TEXT_LAYER", 1 );

// for sorting in _query()
function adodb_cmp($a, $b) {
	if ($a[0] == $b[0]) return 0;
	return ($a[0] < $b[0]) ? -1 : 1;
}
// for sorting in _query()
function adodb_cmpr($a, $b) {
	if ($a[0] == $b[0]) return 0;
	return ($a[0] > $b[0]) ? -1 : 1;
}
class ADODB_text extends ADOConnection {
	var $databaseType = 'text';

	var $_origarray; // original data
	var $_types;
	var $_proberows = 8;
	var $_colnames;
	var $_skiprow1=false;
	var $readOnly = true;
	var $hasTransactions = false;

	var $_rezarray;
	var $_reznames;
	var $_reztypes;

	function RSRecordCount()
	{
		if (!empty($this->_rezarray)) return sizeof($this->_rezarray);

		return sizeof($this->_origarray);
	}

	function _affectedrows()
	{
			return false;
	}

		// returns true or false
	function PConnect(&$array, $types = false, $colnames = false)
	{
		return $this->Connect($array, $types, $colnames);
	}
		// returns true or false
	function Connect(&$array, $types = false, $colnames = false)
	{
		if (is_string($array) and $array === 'iluvphplens') return 'me2';

		if (!$array) {
			$this->_origarray = false;
			return true;
		}
		$row = $array[0];
		$cols = sizeof($row);


		if ($colnames) $this->_colnames = $colnames;
		else {
			$this->_colnames = $array[0];
			$this->_skiprow1 = true;
		}
		if (!$types) {
		// probe and guess the type
			$types = array();
			$firstrow = true;
			if ($this->_proberows > sizeof($array)) $max = sizeof($array);
			else $max = $this->_proberows;
			for ($j=($this->_skiprow1)?1:0;$j < $max; $j++) {
				$row = $array[$j];
				if (!$row) break;
				$i = -1;
				foreach($row as $v) {
					$i += 1;
					//print " ($i ".$types[$i]. "$v) ";
					$v = trim($v);
	 				if (!preg_match('/^[+-]{0,1}[0-9\.]+$/',$v)) {
						$types[$i] = 'C'; // once C, always C
						continue;
					}
					if (isset($types[$i]) && $types[$i]=='C') continue;
					if ($firstrow) {
					// If empty string, we presume is character
					// test for integer for 1st row only
					// after that it is up to testing other rows to prove
					// that it is not an integer
						if (strlen($v) == 0) $types[0] = 'C';
						if (strpos($v,'.') !== false) $types[0] = 'N';
						else  $types[$i] = 'I';
						continue;
					}

					if (strpos($v,'.') !== false) $types[$i] = 'N';

				}
				$firstrow = false;
			}
		}
		//print_r($types);
		$this->_origarray = $array;
		$this->_types = $types;
		return true;
	}

	/**
	 * Execute a query.
	 *
	 * We presume that the select statement is on the same table (what else?),
	 * with the only difference being the order by.
	 * You can filter by using $eval and each clause is stored in $arr e.g. $arr[1] == 'name'
	 * also supports SELECT [DISTINCT] COL FROM ... -- only 1 col supported
	 *
	 * @param string|array $sql      Query to execute.
	 * @param array        $inputarr An optional array of parameters.
	 * @param string       $eval     Optional eval string
	 *
	 * @return mixed|bool Query identifier or true if execution successful, false if failed.
	 */
	function _query($sql, $inputarr=false, $eval=false)
	{
		if ($this->_origarray === false) return false;

		$eval = $this->evalAll;
		$usql = strtoupper(trim($sql));
		$usql = preg_replace("/[\t\n\r]/",' ',$usql);
		$usql = preg_replace('/ *BY/i',' BY',strtoupper($usql));

		$eregword ='([A-Z_0-9]*)';
		//print "<BR> $sql $eval ";
		if ($eval) {
			$i = 0;
			foreach($this->_colnames as $n) {
				$n = strtoupper(trim($n));
				$eval = str_replace("\$$n","\$arr[$i]",$eval);

				$i += 1;
			}

			$i = 0;
			$eval = "\$rez=($eval);";
			//print "<p>Eval string = $eval </p>";
			$where_arr = array();

			reset($this->_origarray);
			foreach ($this->_origarray as $arr) {

				if ($i == 0 && $this->_skiprow1)
					$where_arr[] = $arr;
				else {
					eval($eval);
					//print " $i: result=$rez arr[0]={$arr[0]} arr[1]={$arr[1]} <BR>\n ";
					if ($rez) $where_arr[] = $arr;
				}
				$i += 1;
			}
			$this->_rezarray = $where_arr;
		}else
			$where_arr = $this->_origarray;

		// THIS PROJECTION CODE ONLY WORKS FOR 1 COLUMN,
		// OTHERWISE IT RETURNS ALL COLUMNS
		if (substr($usql,0,7) == 'SELECT ') {
			$at = strpos($usql,' FROM ');
			$sel = trim(substr($usql,7,$at-7));

			$distinct = false;
			if (substr($sel,0,8) == 'DISTINCT') {
				$distinct = true;
				$sel = trim(substr($sel,8,$at));
			}

			// $sel holds the selection clause, comma delimited
			// currently we only project if one column is involved
			// this is to support popups in PHPLens
			if (strpos(',',$sel)===false) {
				$colarr = array();

				preg_match("/$eregword/",$sel,$colarr);
				$col = $colarr[1];
				$i = 0;
				$n = '';
				reset($this->_colnames);
				foreach ($this->_colnames as $n) {

					if ($col == strtoupper(trim($n))) break;
					$i += 1;
				}

				if ($n && $col) {
					$distarr = array();
					$projarray = array();
					$projtypes = array($this->_types[$i]);
					$projnames = array($n);

					foreach ($where_arr as $a) {
						if ($i == 0 && $this->_skiprow1) {
							$projarray[] = array($n);
							continue;
						}

						if ($distinct) {
							$v = strtoupper($a[$i]);
							if (! $distarr[$v]) {
								$projarray[] = array($a[$i]);
								$distarr[$v] = 1;
							}
						} else
							$projarray[] = array($a[$i]);

					} //foreach
					//print_r($projarray);
				}
			} // check 1 column in projection
		}  // is SELECT

		if (empty($projarray)) {
			$projtypes = $this->_types;
			$projarray = $where_arr;
			$projnames = $this->_colnames;
		}
		$this->_rezarray = $projarray;
		$this->_reztypes = $projtypes;
		$this->_reznames = $projnames;


		$pos = strpos($usql,' ORDER BY ');
		if ($pos === false) return $this;
		$orderby = trim(substr($usql,$pos+10));

		preg_match("/$eregword/",$orderby,$arr);
		if (sizeof($arr) < 2) return $this; // actually invalid sql
		$col = $arr[1];
		$at = (integer) $col;
		if ($at == 0) {
			$i = 0;
			reset($projnames);
			foreach ($projnames as $n) {
				if (strtoupper(trim($n)) == $col) {
					$at = $i+1;
					break;
				}
				$i += 1;
			}
		}

		if ($at <= 0 || $at > sizeof($projarray[0])) return $this; // cannot find sort column
		$at -= 1;

		// generate sort array consisting of (sortval1, row index1) (sortval2, row index2)...
		$sorta = array();
		$t = $projtypes[$at];
		$num = ($t == 'I' || $t == 'N');
		for ($i=($this->_skiprow1)?1:0, $max = sizeof($projarray); $i < $max; $i++) {
			$row = $projarray[$i];
			$val = ($num)?(float)$row[$at]:$row[$at];
			$sorta[]=array($val,$i);
		}

		// check for desc sort
		$orderby = substr($orderby,strlen($col)+1);
		$arr = array();
		preg_match('/([A-Z_0-9]*)/i',$orderby,$arr);

		if (trim($arr[1]) == 'DESC') $sortf = 'adodb_cmpr';
		else $sortf = 'adodb_cmp';

		// hasta la sorta babe
		usort($sorta, $sortf);

		// rearrange original array
		$arr2 = array();
		if ($this->_skiprow1) $arr2[] = $projarray[0];
		foreach($sorta as $v) {
			$arr2[] = $projarray[$v[1]];
		}

		$this->_rezarray = $arr2;
		return $this;
	}

	/*	Returns: the last error message from previous database operation	*/
	function ErrorMsg()
	{
			return '';
	}

	/*	Returns: the last error number from previous database operation	*/
	function ErrorNo()
	{
		return 0;
	}

	// returns true or false
	function _close()
	{
	}


}

/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/


class ADORecordSet_text extends ADORecordSet_array
{

	var $databaseType = "text";

	function __construct( $conn,$mode=false)
	{
		parent::__construct();
		$this->InitArray($conn->_rezarray,$conn->_reztypes,$conn->_reznames);
		$conn->_rezarray = false;
	}

} // class ADORecordSet_text


} // defined
