<?php

/*
  V4.20 22 Feb 2004  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  
  Set tabs to 4.
  
  PHP5 Iterator Class:
  
  		$rs = $db->Execute("select * from adoxyz");
		foreach($rs as $k => $v) {
			echo $k; print_r($v); echo "<br>";
		}
 */
 
class ADODB_Iterator implements Iterator {

    private $rs;

    function __construct($rs) 
	{
        $this->rs = $rs;
    }
    function rewind() 
	{
        $this->rs->MoveFirst();
    }
    function hasMore() 
	{
        return !$this->rs->EOF;
    }
    function key() 
	{
        return $this->rs->_currentRow;
    }
    function current() 
	{
        return $this->rs->fields;
    }
    function next() 
	{
        $this->rs->MoveNext();
    }
}


class ADODB_BASE_RS implements IteratorAggregate {
    function getIterator() {
        return new ADODB_Iterator($this);
    }
} 

?>