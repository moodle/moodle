<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class Code11 {

	private function calc_check_digit($code, $max_p)
	{
		$p = 1;
		$check = 0;
		foreach(array_reverse($code) as $i){
			$dval = ($i == '-') ? 10 : intval($i);
			$check += ($dval * $p);
			++$p;
			if ($p > $max_p) {
				$p = 1;
			}
		}

		return $check %= 11;
	}

	public function encode(string $code, array $opts)
	{
		if (!preg_match('/^[\d-]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded");
		}

		$chr = [
			'0' => '111121',
			'1' => '211121',
			'2' => '121121',
			'3' => '221111',
			'4' => '112121',
			'5' => '212111',
			'6' => '122111',
			'7' => '111221',
			'8' => '211211',
			'9' => '211111',
			'-' => '112111',
			'S' => '112211'
		];

		$orig = "  ".$code." ";
		$code = str_split($code);
		$count = count($code);

		// calculate check digit C
		$check = $this->calc_check_digit($code, 10);
		if ($check == 10) {
			$check = '-';
		}
		
		$code[] = $check;
		if ($count > 10) {
			// calculate check digit K
			$check = $this->calc_check_digit($code, 9);
			$code []= $check;
			$orig .= " ";
		}

		array_unshift($code, 'S');
		array_push($code, 'S');

		$block = [];
		foreach($code as $i){
			for ($j = 0; $j < 6; ++$j) {
				$t = (($j % 2) == 0); // bar : space
				$block[] = [$t, $chr[$i][$j], 1];
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