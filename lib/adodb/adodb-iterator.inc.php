<?php

/*
  V5.14 8 Sept 2011   (c) 2000-2011 John Lim (jlim#natsoft.com). All rights reserved.
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