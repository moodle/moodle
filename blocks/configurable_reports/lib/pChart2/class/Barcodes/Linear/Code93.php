<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class Code93 {

	public function encode(string $code, array $opts)
	{
		// Extended
		if (substr($opts['mode'], 0, 1) == "E") {
			$code = $this->gen_ext_code($code);
		}

		return $this->do93($code);
	}

	private function do93($code)
	{
		$code = strtoupper($code);
		if (!preg_match('/^[0-9A-Z |=#*.\/:+$&-]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded");
		}

		$modules = [
			[1, 1, 1], [0, 1, 1], [1, 1, 1],
			[0, 1, 1], [1, 4, 1], [0, 1, 1]
		];
		/* Data */
		$values = [];
		foreach(str_split($code) as $char){
			$block = $this->chars[$char];
			$modules[] = [1, $block[0], 1];
			$modules[] = [0, $block[1], 1];
			$modules[] = [1, $block[2], 1];
			$modules[] = [0, $block[3], 1];
			$modules[] = [1, $block[4], 1];
			$modules[] = [0, $block[5], 1];
			$values[] = $block[6];
		}

		/* Check Digits */
		for ($i = 0; $i < 2; $i++) {
			$index = count($values);
			$weight = 0;
			$checksum = 0;
			while ($index) {
				$index--;
				$weight++;
				$checksum += $weight * $values[$index];
				$checksum %= 47;
				$weight %= ($i ? 15 : 20);
			}
			$values[] = $checksum;
		}
		$alphabet = array_values($this->chars);
		foreach(array_slice($values, -2, 2) as $v){
			$block = $alphabet[$v];
			$modules[] = [1, $block[0], 1];
			$modules[] = [0, $block[1], 1];
			$modules[] = [1, $block[2], 1];
			$modules[] = [0, $block[3], 1];
			$modules[] = [1, $block[4], 1];
			$modules[] = [0, $block[5], 1];
		}
		/* End */
		$modules[] = [1, 1, 1];
		$modules[] = [0, 1, 1];
		$modules[] = [1, 1, 1];
		$modules[] = [0, 1, 1];
		$modules[] = [1, 4, 1];
		$modules[] = [0, 1, 1];
		$modules[] = [1, 1, 1];

		return [['m' => $modules, 'l' => [$code]]];
	}

	private function gen_ext_code($code)
	{
		$newCode = '';
		foreach(str_split($code) as $char){
			$ch = ord($char);
			if ($ch < 128) {
				$newCode .= ($this->ext_table[$ch]);
			}
		}

		return $newCode;
	}

	private $chars = [
		'0' => [1, 3, 1, 1, 1, 2,  0],
		'1' => [1, 1, 1, 2, 1, 3,  1],
		'2' => [1, 1, 1, 3, 1, 2,  2],
		'3' => [1, 1, 1, 4, 1, 1,  3],
		'4' => [1, 2, 1, 1, 1, 3,  4],
		'5' => [1, 2, 1, 2, 1, 2,  5],
		'6' => [1, 2, 1, 3, 1, 1,  6],
		'7' => [1, 1, 1, 1, 1, 4,  7],
		'8' => [1, 3, 1, 2, 1, 1,  8],
		'9' => [1, 4, 1, 1, 1, 1,  9],
		'A' => [2, 1, 1, 1, 1, 3, 10],
		'B' => [2, 1, 1, 2, 1, 2, 11],
		'C' => [2, 1, 1, 3, 1, 1, 12],
		'D' => [2, 2, 1, 1, 1, 2, 13],
		'E' => [2, 2, 1, 2, 1, 1, 14],
		'F' => [2, 3, 1, 1, 1, 1, 15],
		'G' => [1, 1, 2, 1, 1, 3, 16],
		'H' => [1, 1, 2, 2, 1, 2, 17],
		'I' => [1, 1, 2, 3, 1, 1, 18],
		'J' => [1, 2, 2, 1, 1, 2, 19],
		'K' => [1, 3, 2, 1, 1, 1, 20],
		'L' => [1, 1, 1, 1, 2, 3, 21],
		'M' => [1, 1, 1, 2, 2, 2, 22],
		'N' => [1, 1, 1, 3, 2, 1, 23],
		'O' => [1, 2, 1, 1, 2, 2, 24],
		'P' => [1, 3, 1, 1, 2, 1, 25],
		'Q' => [2, 1, 2, 1, 1, 2, 26],
		'R' => [2, 1, 2, 2, 1, 1, 27],
		'S' => [2, 1, 1, 1, 2, 2, 28],
		'T' => [2, 1, 1, 2, 2, 1, 29],
		'U' => [2, 2, 1, 1, 2, 1, 30],
		'V' => [2, 2, 2, 1, 1, 1, 31],
		'W' => [1, 1, 2, 1, 2, 2, 32],
		'X' => [1, 1, 2, 2, 2, 1, 33],
		'Y' => [1, 2, 2, 1, 2, 1, 34],
		'Z' => [1, 2, 3, 1, 1, 1, 35],
		'-' => [1, 2, 1, 1, 3, 1, 36],
		'.' => [3, 1, 1, 1, 1, 2, 37],
		' ' => [3, 1, 1, 2, 1, 1, 38],
		'$' => [3, 2, 1, 1, 1, 1, 39],
		'/' => [1, 1, 2, 1, 3, 1, 40],
		'+' => [1, 1, 3, 1, 2, 1, 41],
		'%' => [2, 1, 1, 1, 3, 1, 42],
		'#' => [1, 2, 1, 2, 2, 1, 43], /* ($] */
		'&' => [3, 1, 2, 1, 1, 1, 44], /* (%] */
		'|' => [3, 1, 1, 1, 2, 1, 45], /* (/] */
		'=' => [1, 2, 2, 2, 1, 1, 46], /* (+] */
		'*' => [1, 1, 1, 1, 4, 1,  0]
	];

	private $ext_table = [
		'&U', '#A', '#B', '#C', '#D', '#E', '#F', '#G',
		'#H', '#I', '#J', '#K', '#L', '#M', '#N', '#O',
		'#P', '#Q', '#R', '#S', '#T', '#U', '#V', '#W',
		'#X', '#Y', '#Z', '&A', '&B', '&C', '&D', '&E',
		' ' , '|A', '|B', '|C', '$' , '%' , '|F', '|G',
		'|H', '|I', '|J', '+' , '|L', '-' , '.' , '/' ,
		'0' , '1' , '2' , '3' , '4' , '5' , '6' , '7' ,
		'8' , '9' , '|Z', '&F', '&G', '&H', '&I', '&J',
		'&V', 'A' , 'B' , 'C' , 'D' , 'E' , 'F' , 'G' ,
		'H' , 'I' , 'J' , 'K' , 'L' , 'M' , 'N' , 'O' ,
		'P' , 'Q' , 'R' , 'S' , 'T' , 'U' , 'V' , 'W' ,
		'X' , 'Y' , 'Z' , '&K', '&L', '&M', '&N', '&O',
		'&W', '=A', '=B', '=C', '=D', '=E', '=F', '=G',
		'=H', '=I', '=J', '=K', '=L', '=M', '=N', '=O',
		'=P', '=Q', '=R', '=S', '=T', '=U', '=V', '=W',
		'=X', '=Y', '=Z', '&P', '&Q', '&R', '&S', '&T'
	];
}
