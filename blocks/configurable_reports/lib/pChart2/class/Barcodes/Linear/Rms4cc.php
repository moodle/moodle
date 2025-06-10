<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class Rms4cc {

	// bar mode
	// 1 = pos 1, length 2
	// 2 = pos 1, length 3
	// 3 = pos 2, length 1
	// 4 = pos 2, length 2
	private $barmode = [
			'0' => [3, 3, 2, 2],
			'1' => [3, 4, 1, 2],
			'2' => [3, 4, 2, 1],
			'3' => [4, 3, 1, 2],
			'4' => [4, 3, 2, 1],
			'5' => [4, 4, 1, 1],
			'6' => [3, 1, 4, 2],
			'7' => [3, 2, 3, 2],
			'8' => [3, 2, 4, 1],
			'9' => [4, 1, 3, 2],
			'A' => [4, 1, 4, 1],
			'B' => [4, 2, 3, 1],
			'C' => [3, 1, 2, 4],
			'D' => [3, 2, 1, 4],
			'E' => [3, 2, 2, 3],
			'F' => [4, 1, 1, 4],
			'G' => [4, 1, 2, 3],
			'H' => [4, 2, 1, 3],
			'I' => [1, 3, 4, 2],
			'J' => [1, 4, 3, 2],
			'K' => [1, 4, 4, 1],
			'L' => [2, 3, 3, 2],
			'M' => [2, 3, 4, 1],
			'N' => [2, 4, 3, 1],
			'O' => [1, 3, 2, 4],
			'P' => [1, 4, 1, 4],
			'Q' => [1, 4, 2, 3],
			'R' => [2, 3, 1, 4],
			'S' => [2, 3, 2, 3],
			'T' => [2, 4, 1, 3],
			'U' => [1, 1, 4, 4],
			'V' => [1, 2, 3, 4],
			'W' => [1, 2, 4, 3],
			'X' => [2, 1, 3, 4],
			'Y' => [2, 1, 4, 3],
			'Z' => [2, 2, 3, 3]
		];

		private $checktable = [
				'0' => [1, 1],
				'1' => [1, 2],
				'2' => [1, 3],
				'3' => [1, 4],
				'4' => [1, 5],
				'5' => [1, 0],
				'6' => [2, 1],
				'7' => [2, 2],
				'8' => [2, 3],
				'9' => [2, 4],
				'A' => [2, 5],
				'B' => [2, 0],
				'C' => [3, 1],
				'D' => [3, 2],
				'E' => [3, 3],
				'F' => [3, 4],
				'G' => [3, 5],
				'H' => [3, 0],
				'I' => [4, 1],
				'J' => [4, 2],
				'K' => [4, 3],
				'L' => [4, 4],
				'M' => [4, 5],
				'N' => [4, 0],
				'O' => [5, 1],
				'P' => [5, 2],
				'Q' => [5, 3],
				'R' => [5, 4],
				'S' => [5, 5],
				'T' => [5, 0],
				'U' => [0, 1],
				'V' => [0, 2],
				'W' => [0, 3],
				'X' => [0, 4],
				'Y' => [0, 5],
				'Z' => [0, 0]
			];

	public function encode(string $code, array $opts)
	{
		$orig = $code;
		$notkix = (strtoupper($opts['mode']) != "KIX");
		$code = strtoupper($code);

		if (!preg_match('/^[\w]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded by Rms4cc");
		}

		$code = str_split($code);
		$block = [];

		if ($notkix) {
			// table for checksum calculation (row,col)
			$row = 0;
			$col = 0;
			foreach($code as $i){
				$row += $this->checktable[$i][0];
				$col += $this->checktable[$i][1];
			}
			$row %= 6;
			$col %= 6;
			$chk = array_keys($this->checktable, [$row, $col]);
			$code[] = $chk[0];

			// start bar
			$block[] = [1, 1, 1, 2, 0];
			$block[] = [0, 1, 1, 2, 0];
		}

		foreach($code as $i){
			for ($j = 0; $j < 4; ++$j) {
				switch ($this->barmode[$i][$j]) {
					case 1: {
							$p = 0;
							$h = 2;
							break;
						}
					case 2: {
							$p = 0;
							$h = 3;
							break;
						}
					case 3: {
							$p = 1;
							$h = 1;
							break;
						}
					case 4: {
							$p = 1;
							$h = 2;
							break;
						}
				}
				$block[] = [1, 1, 1, $h, $p];
				$block[] = [0, 1, 1, 2, 0];
			}
		}

		if ($notkix) {
			// stop bar
			$block[] = [0, 1, 1, 3, 0];
		}

		return [
			[
				'm' => $block,
				'l' => [$orig]
			]
		];
	}
}