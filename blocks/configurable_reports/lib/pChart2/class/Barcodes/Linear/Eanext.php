<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class Eanext {

	//Convert digits to bars
	private $codes = [
			'A' => [// left odd parity
				'0001101',
				'0011001',
				'0010011',
				'0111101',
				'0100011',
				'0110001',
				'0101111',
				'0111011',
				'0110111',
				'0001011'
				],
			'B' => [// left even parity
				'0100111',
				'0110011',
				'0011011',
				'0100001',
				'0011101',
				'0111001',
				'0000101',
				'0010001',
				'0001001',
				'0010111'
				]
		];


	private $parities = [
			2 => [
				['A', 'A'],
				['A', 'B'],
				['B', 'A'],
				['B', 'B']
			],
			5 => [
				['B', 'B', 'A', 'A', 'A'],
				['B', 'A', 'B', 'A', 'A'],
				['B', 'A', 'A', 'B', 'A'],
				['B', 'A', 'A', 'A', 'B'],
				['A', 'B', 'B', 'A', 'A'],
				['A', 'A', 'B', 'B', 'A'],
				['A', 'A', 'A', 'B', 'B'],
				['A', 'B', 'A', 'B', 'A'],
				['A', 'B', 'A', 'A', 'B'],
				['A', 'A', 'B', 'A', 'B']
			]
		];

	public function encode(string $code, array $opts)
	{
		if (!preg_match('/^[\d]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded by Eanext");
		}

		$orig = $code;
		$len = (strtoupper($opts['mode']) == "EAN5") ? 5 : 2;
		$code = str_pad($code, $len, '0', STR_PAD_LEFT); //Padding
		$code_array = array_map(fn(string $d): int => (int) $d, str_split($code));

		// calculate check digit
		if ($len == 2) {
			$chkd = intval($code) % 4;
		} elseif ($len == 5) {
			$chkd = (3 * ($code_array[0] + $code_array[2] + $code_array[4])) + (9 * ($code_array[1] + $code_array[3]));
			$chkd %= 10;
		}

		$p = $this->parities[$len][$chkd];
		$codes = [];
		for ($i = 0; $i < $len; ++$i) {
			$codes[] = $this->codes[$p[$i]][$code_array[$i]];
		}
		// left guard bar + the codes
		$seq = '1011' . implode("01", $codes);

		$clen = strlen($seq);
		$w = 0;
		$block = [];
		for ($i = 0; $i < $clen; ++$i) {
			$w += 1;
			if (($i == ($clen - 1)) OR (($i < ($clen - 1)) AND ($seq[$i] != $seq[$i + 1]))) {
				$t = ($seq[$i] == '1'); // bar : space
				$block[] = [$t, $w, 1];
				$w = 0;
			}
		}

		return [
			[
				'm' => $block,
				'l' => [$orig]
			]
		];
	}
}