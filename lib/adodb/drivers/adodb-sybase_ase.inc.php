<?php
/**
 * SAP Adaptive Server Enterprise driver (formerly Sybase ASE)
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
 * @author Cristian Marin, Interakt Online <cristic@interaktonline.com>
 */

require_once ADODB_DIR."/drivers/adodb-sybase.inc.php";

class ADODB_sybase_ase extends ADODB_sybase {
 	var $databaseType = "sybase_ase";

	 var $metaTablesSQL="SELECT sysobjects.name FROM sysobjects, sysusers WHERE sysobjects.type='U' AND sysobjects.uid = sysusers.uid";
	 var $metaColumnsSQL = "SELECT syscolumns.name AS field_name, systypes.name AS type, systypes.length AS width FROM sysobjects, syscolumns, systypes WHERE sysobjects.name='%s' AND syscolumns.id = sysobjects.id AND systypes.type=syscolumns.type";
	 var $metaDatabasesSQL ="SELECT a.name FROM master.dbo.sysdatabases a, master.dbo.syslogins b WHERE a.suid = b.suid and a.name like '%' and a.name != 'tempdb' and a.status3 != 256  order by 1";

	// split the Views, Tables and procedures.
	function MetaTables($ttype=false,$showSchema=false,$mask=false)
	{
		$false = false;
		if ($this->metaTablesSQL) {
			// complicated state saving by the need for backward compat

			if ($ttype == 'VIEWS'){
						$sql = str_replace('U', 'V', $this->metaTablesSQL);
			}elseif (false === $ttype){
						$sql = str_replace('U',"U' OR type='V", $this->metaTablesSQL);
			}else{ // TABLES OR ANY OTHER
						$sql = $this->metaTablesSQL;
			}
			$rs = $this->Execute($sql);

			if ($rs === false || !method_exists($rs, 'GetArray')){
					return $false;
			}
			$arr = $rs->GetArray();

			$arr2 = array();
			foreach($arr as $key=>$value){
					$arr2[] = trim($value['name']);
			}
			return $arr2;
		}
		return $false;
	}

	function MetaDatabases()
	{
			$arr = array();
			if ($this->metaDatabasesSQL!='') {
				$rs = $this->Execute($this->metaDatabasesSQL);
				if ($rs && !$rs->EOF){
					while (!$rs->EOF){
						$arr[] = $rs->Fields('name');
						$rs->MoveNext();
					}
					return $arr;
				}
			}
			return false;
	}

	// fix a bug which prevent the metaColumns query to be executed for Sybase ASE
	function MetaColumns($table,$upper=false)
	{
		$false = false;
		if (!empty($this->metaColumnsSQL)) {

			$rs = $this->Execute(sprintf($this->metaColumnsSQL,$table));
			if ($rs === false) return $false;

			$retarr = array();
			while (!$rs->EOF) {
				$fld = new ADOFieldObject();
				$fld->name = $rs->Fields('field_name');
				$fld->type = $rs->Fields('type');
				$fld->max_length = $rs->Fields('width');
				$retarr[strtoupper($fld->name)] = $fld;
				$rs->MoveNext();
			}
			$rs->Close();
			return $retarr;
		}
		return $false;
	}

	function getProcedureList($schema)
	{
			return false;
	}

	function ErrorMsg()
	{
		if (!function_exists('sybase_connect')){
				return 'Your PHP doesn\'t contain the Sybase connection module!';
		}
		return parent::ErrorMsg();
	}
}

class adorecordset_sybase_ase extends ADORecordset_sybase {
	var $databaseType = "sybase_ase";
}
