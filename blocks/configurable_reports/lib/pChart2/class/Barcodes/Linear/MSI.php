<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class MSI {

	public function encode(string $code, array $opts)
	{
		$orig = $code;
		$code = strtoupper($code);
		if (!preg_match('/^[0-9A-F]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded");
		}

		$chr = [
				'0' => [1,1,1,1],
				'1' => [1,1,1,2],
				'2' => [1,1,2,1],
				'3' => [1,1,2,2],
				'4' => [1,2,1,1],
				'5' => [1,2,1,2],
				'6' => [1,2,2,1],
				'7' => [1,2,2,2],
				'8' => [2,1,1,1],
				'9' => [2,1,1,2],
				'A' => [2,1,2,1],
				'B' => [2,1,2,2],
				'C' => [2,2,1,1],
				'D' => [2,2,1,2],
				'E' => [2,2,2,1],
				'F' => [2,2,2,2]
			];

		$code = str_split($code);

		if ($opts['mode'] == "+") {
			// add checksum
			$p = 2;
			$check = 0;
			foreach(array_reverse($code) as $i){
				$check += (hexdec($i) * $p);
				++$p;
				if ($p > 7) {
					$p = 2;
				}
			}
			$check %= 11;
			if ($check > 0) {
				$check = 11 - $check;
			}
			$code[] = $check;
		}

		$block = [
			[1, 2, 1], // left guard
			[0, 1, 1]
		];

		foreach($code as $i){
			foreach($chr[$i] as $chr_a){
				$block[] = [1, $chr_a, 1];
				$block[] = [0, ($chr_a & 1) + 1, 1];
			}
		}

		$block[] = [1, 1, 1]; // right guard
		$block[] = [0, 2, 1];
		$block[] = [1, 1, 1];

		return [
			[
				'm' => $block,
				'l' => [$orig]
			]
		];
	}
}