<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class Code39 {

	private $chars = [
		'0' => '111331311',	'1' => '311311113',	'2' => '113311113',	'3' => '313311111',
		'4' => '111331113',	'5' => '311331111',	'6' => '113331111',	'7' => '111311313',
		'8' => '311311311',	'9' => '113311311',	'A' => '311113113',	'B' => '113113113',
		'C' => '313113111',	'D' => '111133113',	'E' => '311133111',	'F' => '113133111',
		'G' => '111113313',	'H' => '311113311',	'I' => '113113311',	'J' => '111133311',
		'K' => '311111133',	'L' => '113111133',	'M' => '313111131',	'N' => '111131133',
		'O' => '311131131',	'P' => '113131131',	'Q' => '111111333',	'R' => '311111331',
		'S' => '113111331',	'T' => '111131331',	'U' => '331111113',	'V' => '133111113',
		'W' => '333111111',	'X' => '131131113',	'Y' => '331131111',	'Z' => '133131111',
		'-' => '131111313',	'.' => '331111311',	' ' => '133111311',	'$' => '131313111',
		'/' => '131311131',	'+' => '131113131',	'%' => '111313131',	'*' => '131131311'
	];

	private $ext_table = [];

	private function gen_ext_table()
	{
		$this->ext_table = [
			chr(0) => '%U',		chr(1) => '$A',		chr(2) => '$B',		chr(3) => '$C',
			chr(4) => '$D',		chr(5) => '$E',		chr(6) => '$F',		chr(7) => '$G',
			chr(8) => '$H',		chr(9) => '$I',		chr(10) => '$J',	chr(11) => '£K',
			chr(12) => '$L',	chr(13) => '$M',	chr(14) => '$N',	chr(15) => '$O',
			chr(16) => '$P',	chr(17) => '$Q',	chr(18) => '$R',	chr(19) => '$S',
			chr(20) => '$T',	chr(21) => '$U',	chr(22) => '$V',	chr(23) => '$W',
			chr(24) => '$X',	chr(25) => '$Y',	chr(26) => '$Z',	chr(27) => '%A',
			chr(28) => '%B',	chr(29) => '%C',	chr(30) => '%D',	chr(31) => '%E',
			' ' =>		' ',	'!' =>		'/A',	'"' =>		'/B',	'#' =>		'/C',
			'$' =>		'/D',	'%' =>		 '/E',	'&' =>		'/F',	'\'' =>		'/G',
			'(' => 		'/H',	')' =>		'/I',	'*' =>		'/J',	'+' =>		'/K',
			',' =>		'/L',	'-' =>		'-',	'.' =>		'.',	'/' =>		'/O',
			'0' =>		'0',	'1' =>		'1',	'2' =>		'2',	'3' =>		'3',
			'4' =>		'4',	'5' =>		'5',	'6' =>		'6',	'7' =>		'7',
			'8' =>		'8',	'9' =>		'9',	':' =>		'/Z',	';' =>		'%F',
			'<' =>		'%G',	'=' =>		'%H',	'>' =>		'%I',	'?' =>		'%J',
			'@' =>		'%V',	'A' =>		'A',	'B' =>		'B',	'C' =>		'C',
			'D' =>		'D',	'E' =>		'E',	'F' =>		'F',	'G' =>		'G',
			'H' =>		'H',	'I' =>		'I',	'J' =>		'J',	'K' =>		'K',
			'L' =>		'L',	'M' =>		'M',	'N' =>		'N',	'O' =>		'O',
			'P' =>		'P',	'Q' =>		'Q',	'R' =>		'R',	'S' =>		'S',
			'T' =>		'T',	'U' =>		'U',	'V' =>		'V',	'W' =>		'W',
			'X' =>		'X',	'Y' =>		'Y',	'Z' =>		'Z',	'[' =>		'%K',
			'\\' =>		'%L',	']' =>		'%M',	'^' =>		'%N',	'_' =>		'%O',
			'`' =>		'%W',	'a' =>		'+A',	'b' =>		'+B',	'c' =>		'+C',
			'd' =>		'+D',	'e' =>		'+E',	'f' =>		'+F',	'g' =>		'+G',
			'h' =>		'+H',	'i' =>		'+I',	'j' =>		'+J',	'k' =>		'+K',
			'l' =>		'+L',	'm' =>		'+M',	'n' =>		'+N',	'o' =>		'+O',
			'p' =>		'+P',	'q' =>		'+Q',	'r' =>		'+R',	's' =>		'+S',
			't' =>		'+T',	'u' =>		'+U',	'v' =>		'+V',	'w' =>		'+W',
			'x' =>		'+X',	'y' =>		'+Y',	'z' =>		'+Z',	'{' =>		'%P',
			'|' =>		'%Q',	'}' =>		'%R',	'~' =>		'%S',	chr(127) =>	'%T'
		];
	}

	public function encode(string $code, array $opts)
	{
		$orig = $code;

		// Extended
		if (substr($opts['mode'], 0, 1) == "E") {
			$code = $this->gen_ext_code($code);
		}

		if (!preg_match('/[0-9A-Za-z%$\/+ .-]/', $code)){
			throw pException::InvalidInput("Text can not be encoded");
		}

		$code = str_split(strtoupper($code));

		// Checksum
		if (substr($opts['mode'], -1) == '+') {
			$this->gen_checksum($code);
		}

		$blocks = $this->do39($code);

		return [
			[
				'm' => $blocks,
				'l' => [$orig]
			]
		];
	}

	private function do39($code)
	{
		// add start and stop codes
		array_unshift($code, "*");
		array_push($code, "*");

		$blocks = [];
		foreach($code as $char){
			for ($j = 0; $j < 9; ++$j) {
				$blocks[] = [
					(($j % 2) == 0),
					$this->chars[$char][$j],
					1
				];
			}
			// intercharacter gap
			$blocks[] = [0, 1, 1];
		}

		return $blocks;
	}

	private function gen_ext_code($code)
	{
		if(empty($this->ext_table)){
			$this->gen_ext_table();
		}
		$newCode = '';
		$len = strlen($code);
		for($i = 0; $i < $len; $i++){
			if (ord($code[$i]) > 127) {
				throw pException::InvalidInput("Text can not be encoded");
			}
			$newCode .= $this->ext_table[$code[$i]];
		}

		return $newCode;
	}

	private function gen_checksum(&$code)
	{
		$sum = 0;
		$chars = array_keys($this->chars);
		$char_keys = array_flip($chars);
		foreach($code as $char){
			$sum += $char_keys[$char];
		}
		$code[] = $chars[$sum % 43];
	}

}