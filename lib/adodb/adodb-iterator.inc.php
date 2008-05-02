<?php

/*
  V5.04a 25 Mar 2008   (c) 2000-2008 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  
  Set tabs to 4.
  
  Declares the ADODB Base Class for PHP5 "ADODB_BASE_RS", and supports iteration with 
  the ADODB_Iterator class.
  
  		$rs = $db->Execute("select * from adoxyz");
		foreach($rs as $k => $v) {
			echo $k; print_r($v); echo "<br>";
		}
		
		
	Iterator code based on http://cvs.php.net/cvs.php/php-src/ext/spl/examples/cachingiterator.inc?login=2
	
	
	Moved to adodb.inc.php to improve performance.
 */
 




?>