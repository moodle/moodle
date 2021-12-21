<?php

/**
 * Provided by Ned Andre to support sqlsrv library
 */
class ADODB_pdo_sqlsrv extends ADODB_pdo
{
	var $hasTop = 'top';
	var $sysDate = 'convert(datetime,convert(char,GetDate(),102),102)';
	var $sysTimeStamp = 'GetDate()';
	var $arrayClass = 'ADORecordSet_array_pdo_sqlsrv';

	function _init(ADODB_pdo $parentDriver)
	{
		$parentDriver->hasTransactions = true;
		$parentDriver->_bindInputArray = true;
		$parentDriver->hasInsertID = true;
		$parentDriver->fmtTimeStamp = "'Y-m-d H:i:s'";
		$parentDriver->fmtDate = "'Y-m-d'";
	}

	function BeginTrans()
	{
		$returnval = parent::BeginTrans();
		return $returnval;
	}

	function MetaColumns($table, $normalize = true)
	{
		return false;
	}

	function MetaTables($ttype = false, $showSchema = false, $mask = false)
	{
		return false;
	}

	function SelectLimit($sql, $nrows = -1, $offset = -1, $inputarr = false, $secs2cache = 0)
	{
		$ret = ADOConnection::SelectLimit($sql, $nrows, $offset, $inputarr, $secs2cache);
		return $ret;
	}

	function ServerInfo()
	{
		return ADOConnection::ServerInfo();
	}
}

class ADORecordSet_pdo_sqlsrv extends ADORecordSet_pdo
{

	public $databaseType = "pdo_sqlsrv";

	/**
	 * returns the field object
	 *
	 * @param  int $fieldOffset Optional field offset
	 *
	 * @return object The ADOFieldObject describing the field
	 */
	public function fetchField($fieldOffset = 0)
	{

		// Default behavior allows passing in of -1 offset, which crashes the method
		if ($fieldOffset == -1) {
			$fieldOffset++;
		}

		$o = new ADOFieldObject();
		$arr = @$this->_queryID->getColumnMeta($fieldOffset);

		if (!$arr) {
			$o->name = 'bad getColumnMeta()';
			$o->max_length = -1;
			$o->type = 'VARCHAR';
			$o->precision = 0;
			return $o;
		}
		$o->name = $arr['name'];
		if (isset($arr['sqlsrv:decl_type']) && $arr['sqlsrv:decl_type'] <> "null") {
			// Use the SQL Server driver specific value
			$o->type = $arr['sqlsrv:decl_type'];
		} else {
			$o->type = adodb_pdo_type($arr['pdo_type']);
		}
		$o->max_length = $arr['len'];
		$o->precision = $arr['precision'];

		switch (ADODB_ASSOC_CASE) {
			case ADODB_ASSOC_CASE_LOWER:
				$o->name = strtolower($o->name);
				break;
			case ADODB_ASSOC_CASE_UPPER:
				$o->name = strtoupper($o->name);
				break;
		}

		return $o;
	}
}

class ADORecordSet_array_pdo_sqlsrv extends ADORecordSet_array_pdo
{

	/**
	 * returns the field object
	 *
	 * Note that this is a direct copy of the ADORecordSet_pdo_sqlsrv method
	 *
	 * @param  int $fieldOffset Optional field offset
	 *
	 * @return object The ADOfieldobject describing the field
	 */
	public function fetchField($fieldOffset = 0)
	{
		// Default behavior allows passing in of -1 offset, which crashes the method
		if ($fieldOffset == -1) {
			$fieldOffset++;
		}

		$o = new ADOFieldObject();
		$arr = @$this->_queryID->getColumnMeta($fieldOffset);

		if (!$arr) {
			$o->name = 'bad getColumnMeta()';
			$o->max_length = -1;
			$o->type = 'VARCHAR';
			$o->precision = 0;
			return $o;
		}
		$o->name = $arr['name'];
		if (isset($arr['sqlsrv:decl_type']) && $arr['sqlsrv:decl_type'] <> "null") {
			// Use the SQL Server driver specific value
			$o->type = $arr['sqlsrv:decl_type'];
		} else {
			$o->type = adodb_pdo_type($arr['pdo_type']);
		}
		$o->max_length = $arr['len'];
		$o->precision = $arr['precision'];

		switch (ADODB_ASSOC_CASE) {
			case ADODB_ASSOC_CASE_LOWER:
				$o->name = strtolower($o->name);
				break;
			case ADODB_ASSOC_CASE_UPPER:
				$o->name = strtoupper($o->name);
				break;
		}

		return $o;
	}
	
	function SetTransactionMode( $transaction_mode )
	{
		$this->_transmode  = $transaction_mode;
		if (empty($transaction_mode)) {
			$this->_connectionID->query('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
			return;
		}
		if (!stristr($transaction_mode,'isolation')) $transaction_mode = 'ISOLATION LEVEL '.$transaction_mode;
		$this->_connectionID->query("SET TRANSACTION ".$transaction_mode);
	}
}
