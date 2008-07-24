<?
/*
 * This file was contributed (in part or whole) by a third party, and is
 * released under a BSD-compatible free software license.  Please see the
 * CREDITS and LICENSE sections below for details.
 * 
 *****************************************************************************
 *
 * DETAILS
 *
 * A PHP implementation of the Secure Hash Algorithm, SHA-1, as defined in
 * FIPS PUB 180-1.  This is used by Centova only when using PHP
 * versions older than 4.3.0 (which did not support the sha1() function) and
 * the server does not have the mhash extension installed.
 *
 *
 * CREDITS/LICENSE
 *
 * Adjusted from the Javascript implementation by Joror (daan@parse.nl).
 *
 * Javascript Version 2.1 Copyright Paul Johnston 2000 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for details.
 *
 */

class SHA1Library
{
	/*
	 * Configurable variables. You may need to tweak these to be compatible with
	 * the server-side, but the defaults work in most cases.
	 */
	var $hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
	var $b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
	var $chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */
	
	/*
	 * These are the functions you'll usually want to call
	 * They take string arguments and return either hex or base-64 encoded strings
	 */
	function hex_sha1($s){return $this->binb2hex($this->core_sha1($this->str2binb($s),strlen($s) * $this->chrsz));}
	function b64_sha1($s){return $this->binb2b64($this->core_sha1($this->str2binb($s),strlen($s) * $this->chrsz));}
	function str_sha1($s){return $this->binb2str($this->core_sha1($this->str2binb($s),strlen($s) * $this->chrsz));}
	function hex_hmac_sha1($key, $data){ return $this->binb2hex($this->core_hmac_sha1($key, $data));}
	function b64_hmac_sha1($key, $data){ return $this->binb2b64($this->core_hmac_sha1($key, $data));}
	function str_hmac_sha1($key, $data){ return $this->binb2str($this->core_hmac_sha1($key, $data));}
	
	/*
	 * Perform a simple self-test to see if the VM is working
	 */
	function sha1_vm_test()
	{
		return $this->hex_sha1("abc") == "a9993e364706816aba3e25717850c26c9cd0d89d";
	}
	
	/*
	 * Calculate the SHA-1 of an array of big-endian words, and a bit $length
	 */
	function core_sha1($x, $len)
	{
		/* append padding */
		$x[$len >> 5] |= 0x80 << (24 - $len % 32);
		$x[(($len + 64 >> 9) << 4) + 15] = $len;
	
		$w = Array();
		$a =  1732584193;
		$b = -271733879;
		$c = -1732584194;
		$d =  271733878;
		$e = -1009589776;
	
		for($i = 0; $i < sizeof($x); $i += 16)
		{
			$olda = $a;
			$oldb = $b;
			$oldc = $c;
			$oldd = $d;
			$olde = $e;
	
			for($j = 0; $j < 80; $j++)
			{
				if ($j < 16) 
					$w[$j] = $x[$i + $j];
				else 
					$w[$j] = $this->rol($w[$j-3] ^ $w[$j-8] ^ $w[$j-14] ^ $w[$j-16], 1);
					
				$t = $this->safe_add(	$this->safe_add($this->rol($a, 5), $this->sha1_ft($j, $b, $c, $d)), 
										$this->safe_add($this->safe_add($e, $w[$j]), $this->sha1_kt($j)));
				$e = $d;
				$d = $c;
				$c = $this->rol($b, 30);
				$b = $a;
				$a = $t;
			}

			$a = $this->safe_add($a, $olda);
			$b = $this->safe_add($b, $oldb);
			$c = $this->safe_add($c, $oldc);
			$d = $this->safe_add($d, $oldd);
			$e = $this->safe_add($e, $olde);
		}
		
		return Array($a, $b, $c, $d, $e);
	}
	
	/*
	 * Joror: PHP does not have the java(script) >>> operator, so this is a 
	 * replacement function. Credits to Terium.
	 */
	function zerofill_rightshift($a, $b) 
	{ 
		$z = hexdec(80000000); 
		if ($z & $a) 
		{ 
			$a >>= 1; 
			$a &= (~ $z); 
			$a |= 0x40000000; 
			$a >>= ($b-1); 
		} 
		else 
		{ 
			$a >>= $b; 
		} 
		return $a; 
	}
	
	/*
	 * Perform the appropriate triplet combination function for the current
	 * iteration
	 */
	function sha1_ft($t, $b, $c, $d)
	{
		if($t < 20) return ($b & $c) | ((~$b) & $d);
		if($t < 40) return $b ^ $c ^ $d;
		if($t < 60) return ($b & $c) | ($b & $d) | ($c & $d);
		return $b ^ $c ^ $d;
	}
	
	/*
	 * Determine the appropriate additive constant for the current iteration
	 * Silly php does not understand the inline-if operator well when nested,
	 * so that's why it's ()ed now.
	 */
	function sha1_kt($t)
	{
		return ($t < 20) ?  1518500249 : (($t < 40) ?  1859775393 :
				(($t < 60) ? -1894007588 : -899497514));
	}  
	
	/*
	 * Calculate the HMAC-SHA1 of a key and some data
	 */
	function core_hmac_sha1($key, $data)
	{
		$bkey = $this->str2binb($key);
		if(sizeof($bkey) > 16) $bkey = $this->core_sha1($bkey, sizeof($key) * $this->chrsz);
	
		$ipad = Array();
		$opad = Array();
		
		for($i = 0; $i < 16; $i++) 
		{
			$ipad[$i] = $bkey[$i] ^ 0x36363636;
			$opad[$i] = $bkey[$i] ^ 0x5C5C5C5C;
		}
	
		$hash = $this->core_sha1(array_merge($ipad,$this->str2binb($data)), 512 + sizeof($data) * $this->chrsz);
		return $this->core_sha1(array_merge($opad,$hash), 512 + 160);
	}
	
	/*
	 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
	 * to work around bugs in some JS interpreters.
	 */
	function safe_add($x, $y)
	{
		$lsw = ($x & 0xFFFF) + ($y & 0xFFFF);
		$msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);
		return ($msw << 16) | ($lsw & 0xFFFF);
	}
	
	/*
	 * Bitwise rotate a 32-bit number to the left.
	 */
	function rol($num, $cnt)
	{
		return ($num << $cnt) | $this->zerofill_rightshift($num, (32 - $cnt));
	}
	
	/*
	 * Convert an 8-bit or 16-bit string to an array of big-endian words
	 * In 8-bit function, characters >255 have their hi-byte silently ignored.
	 */
	function str2binb($str)
	{
		$bin = Array();
		$mask = (1 << $this->chrsz) - 1;
		for($i = 0; $i < strlen($str) * $this->chrsz; $i += $this->chrsz)
			$bin[$i >> 5] |= (ord($str{$i / $this->chrsz}) & $mask) << (24 - $i%32);
		
		return $bin;
	}
	
	/*
	 * Convert an array of big-endian words to a string
	 */
	function binb2str($bin)
	{
		$str = "";
		$mask = (1 << $this->chrsz) - 1;
		for($i = 0; $i < sizeof($bin) * 32; $i += $this->chrsz)
			$str .= chr($this->zerofill_rightshift($bin[$i>>5], 24 - $i%32) & $mask);
		return $str;
	}
	
	/*
	 * Convert an array of big-endian words to a hex string.
	 */
	function binb2hex($binarray)
	{
		$hex_tab = $this->hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
		$str = "";
		for($i = 0; $i < sizeof($binarray) * 4; $i++)
		{
			$str .= $hex_tab{($binarray[$i>>2] >> ((3 - $i%4)*8+4)) & 0xF} .
					$hex_tab{($binarray[$i>>2] >> ((3 - $i%4)*8  )) & 0xF};
		}
		
		return $str;
	}
	
	/*
	 * Convert an array of big-endian words to a base-64 string
	 */
	function binb2b64($binarray)
	{
		$tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		$str = "";
		for($i = 0; i < sizeof($binarray) * 4; $i += 3)
		{
			$triplet = 	((($binarray[$i   >> 2] >> 8 * (3 -  $i   %4)) & 0xFF) << 16)
						| ((($binarray[$i+1 >> 2] >> 8 * (3 - ($i+1)%4)) & 0xFF) << 8 )
						|  (($binarray[$i+2 >> 2] >> 8 * (3 - ($i+2)%4)) & 0xFF);
			for($j = 0; $j < 4; $j++)
			{
				if($i * 8 + $j * 6 > sizeof($binarray) * 32) $str .= $this->b64pad;
				else $str .= $tab{($triplet >> 6*(3-j)) & 0x3F};
			}
		}
		return $str;
	}
}

if ( !function_exists('sha1') )
{
	function sha1( $string, $raw_output = false )
	{
		$library = &new SHA1Library();
		
		return $raw_output ? $library->str_sha1($string) : $library->hex_sha1($string);
	}
}
?>