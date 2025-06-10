<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class CodaBar {

	private $chars = [
			'0' => '11111221',
			'1' => '11112211',
			'2' => '11121121',
			'3' => '22111111',
			'4' => '11211211',
			'5' => '21111211',
			'6' => '12111121',
			'7' => '12112111',
			'8' => '12211111',
			'9' => '21121111',
			'-' => '11122111',
			'$' => '11221111',
			':' => '21112121',
			'/' => '21211121',
			'.' => '21212111',
			'+' => '11222221',
			'A' => '11221211',
			'B' => '12121121',
			'C' => '11121221',
			'D' => '11122211'
		];

	public function encode(string $code, array $opts)
	{
		$orig = $code;
		if (!preg_match('/^[0-9A-Da-d*.\/:+$-]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded");
		}
		$code = 'A'.strtoupper($code).'A';

		$len = strlen($code);
		$blocks = [];
		for ($i = 0; $i < $len; ++$i) {
			$seq = $this->chars[$code[$i]];
			for ($j = 0; $j < 8; ++$j) {
				$t = (($j % 2) == 0);
				$blocks[] = [$t, $seq[$j], 1];
			}
		}

		return [
			[
				'm' => $blocks,
				'l' => [$orig]
			]
		];
	}
}
