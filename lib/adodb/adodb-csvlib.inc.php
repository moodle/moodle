<?php

/* 
V2.12 12 June 2002 (c) 2000-2002 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. See License.txt. 
  Set tabs to 4 for best viewing.
  
  Latest version is available at http://php.weblogs.com/
  
  Library for CSV serialization. This is used by the csv/proxy driver and is the 
  CacheExecute() serialization format. 
  
  ==== NOTE ====
  Format documented at http://php.weblogs.com/ADODB_CSV
  ==============
*/

	/**
 	 * convert a recordset into special format
	 *
	 * @param rs	the recordset
	 *
	 * @return	the CSV formated data
	 */
	function _rs2serialize(&$rs,$conn=false,$sql='')
	{
		$max = ($rs) ? $rs->FieldCount() : 0;
		
		if ($sql) $sql = urlencode($sql);
		/*  metadata setup */
		
		if ($max <= 0 || $rs->dataProvider == 'empty') { /*  is insert/update/delete */
			if (is_object($conn)) {
        		$sql .= ','.$conn->Affected_Rows();
				$sql .= ','.$conn->Insert_ID();
			} else
				$sql .= ',,';
			
			$text = "====-1,0,$sql\n";
			return $text;
		} else {
			$tt = ($rs->timeCreated) ? $rs->timeCreated : time();
			$line = "====0,$tt,$sql\n";
		}
		/*  column definitions */
		for($i=0; $i < $max; $i++) {
			$o = $rs->FetchField($i);
			$line .= urlencode($o->name).':'.$rs->MetaType($o->type,$o->max_length).":$o->max_length,";
		}
		$text = substr($line,0,strlen($line)-1)."\n";
		
		
		/*  get data */
		if ($rs->databaseType == 'array') {
			$text .= serialize($rs->_array);
		} else {
			$rows = array();
			while (!$rs->EOF) {	
				$rows[] = $rs->fields;
				$rs->MoveNext();
			} 
			$text .= serialize($rows);
		}
		$rs->MoveFirst();
		return $text;
	}

	
/**
* Open CSV file and convert it into Data. 
*
* @param url  		file/ftp/http url
* @param err		returns the error message
* @param timeout	dispose if recordset has been alive for $timeout secs
*
* @return		recordset, or false if error occured. If no
*			error occurred in sql INSERT/UPDATE/DELETE, 
*			empty recordset is returned
*/
	function &csv2rs($url,&$err,$timeout=0)
	{
		$fp = @fopen($url,'r');
		$err = false;
		if (!$fp) {
			$err = $url.'file/URL not found';
			return false;
		}
		flock($fp, LOCK_SH);
		$arr = array();
		$ttl = 0;
		
		if ($meta = fgetcsv ($fp, 8192, ",")) {
			/*  check if error message */
			if (substr($meta[0],0,4) === '****') {
				$err = trim(substr($meta[0],4,1024));
				fclose($fp);
				return false;
			}
			/*  check for meta data */
			/*  $meta[0] is -1 means return an empty recordset */
			/*  $meta[1] contains a time  */
	
			if (substr($meta[0],0,4) ===  '====') {
			
				if ($meta[0] == "====-1") {
					if (sizeof($meta) < 5) {
						$err = "Corrupt first line for format -1";
						fclose($fp);
						return false;
					}
					fclose($fp);
					
					if ($timeout > 0) {
						$err = " Illegal Timeout $timeout ";
						return false;
					}
					$rs->fields = array();
					$rs->timeCreated = $meta[1];
					$rs = new ADORecordSet($val=true);
					$rs->EOF = true;
					$rs->_numOfFields=0;
					$rs->sql = urldecode($meta[2]);
					$rs->affectedrows = (integer)$meta[3];
					$rs->insertid = $meta[4];	
					return $rs;
				}
			# Under high volume loads, we want only 1 thread/process to _write_file
			# so that we don't have 50 processes queueing to write the same data.
			# Would require probabilistic blocking write 
			#
			# -2 sec before timeout, give processes 1/16 chance of writing to file with blocking io
			# -1 sec after timeout give processes 1/4 chance of writing with blocking
			# +0 sec after timeout, give processes 100% chance writing with blocking
				if (sizeof($meta) > 1) {
					if($timeout >0){ 
						$tdiff = $meta[1]+$timeout - time();
						if ($tdiff <= 2) {
							switch($tdiff) {
							case 2: 
								if ((rand() & 15) == 0) {
									fclose($fp);
									$err = "Timeout 2";
									return false;
								}
								break;
							case 1:
								if ((rand() & 3) == 0) {
									fclose($fp);
									$err = "Timeout 1";
									return false;
								}
								break;
							default: 
								fclose($fp);
								$err = "Timeout 0";
								return false;
							} /*  switch */
							
						} /*  if check flush cache */
					}/*  (timeout>0) */
					$ttl = $meta[1];
				}
				$meta = fgetcsv($fp, 8192, ",");
				if (!$meta) {
					fclose($fp);
					$err = "Unexpected EOF 1";
					return false;
				}
			}

			/*  Get Column definitions */
			$flds = array();
			foreach($meta as $o) {
				$o2 = explode(':',$o);
				if (sizeof($o2)!=3) {
					$arr[] = $meta;
					$flds = false;
					break;
				}
				$fld = new ADOFieldObject();
				$fld->name = urldecode($o2[0]);
				$fld->type = $o2[1];
				$fld->max_length = $o2[2];
				$flds[] = $fld;
			}
		} else {
			fclose($fp);
			$err = "Recordset had unexpected EOF 2";
			/* print "$url ";print_r($meta); */
			/* die(); */
			return false;
		}
		
		/*  slurp in the data */
		$MAXSIZE = 128000;
		$text = fread($fp,$MAXSIZE);
		$cnt = 1;
		while (strlen($text) == $MAXSIZE*$cnt) {
			$text .= fread($fp,$MAXSIZE);
			$cnt += 1;
		}
			
		fclose($fp);
		$arr = @unserialize($text);
		
		/* var_dump($arr); */
		if (!is_array($arr)) {
			$err = "Recordset had unexpected EOF 3";
			return false;
		}
		$rs = new ADORecordSet_array();
		$rs->timeCreated = $ttl;
		$rs->InitArrayFields($arr,$flds);
		return $rs;
	}
	
	/*
	# The following code was an alternative method of saving 
	# recordsets and  is experimental and was never used.
	# It is faster, but provides very little error checking.
	
	//High speed rs2csv 10% faster 
	function & xrs2csv(&$rs)
	{
		return time()."\n".serialize($rs);
	}
	function & xcsv2rs($url,&$err,$timeout)
	{
		$t = filemtime($url);// this is cached - should we clearstatcache() ?
		if ($t === false) {
			$err = 'File not found 1';
			return false;
		}
		
		if (time() > $t + $timeout){
			$err = " Timeout 1";
			return false;
		}
		
		$fp = @fopen($url,'r');
		if (!$fp) {
			$err = ' file not found ';
			return false;
		}
		
		flock($fp,LOCK_SH);
		$t = fgets($fp,100);
		if ($t === false){
			fclose($fp);
			$err =  " EOF 1 ";
			return false;
		}
		/*
		if (time() > ((integer)$t) + $timeout){
			fclose($fp);
			$err = " Timeout 2";
			return false;
		}*   /
		
		$txt = &fread($fp,1999999); // Increase if EOF 2 error returned
		fclose($fp);
		$o = @unserialize($txt);
		if (!is_object($o)) {
			$err = " EOF 2";
			return false;
		}
		$o->timeCreated = $t;
		return $o;
	}
	*/
?>