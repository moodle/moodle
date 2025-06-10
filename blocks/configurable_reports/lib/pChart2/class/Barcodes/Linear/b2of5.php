<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class b2of5 {

	private $s_chr = [
			'0' => [1,1,3,3,1],
			'1' => [3,1,1,1,3],
			'2' => [1,3,1,1,3],
			'3' => [3,3,1,1,1],
			'4' => [1,1,3,1,3],
			'5' => [3,1,3,1,1],
			'6' => [1,3,3,1,1],
			'7' => [1,1,1,3,3],
			'8' => [1,1,3,3,1],
			'9' => [1,3,1,3,1]
		];

	private $i_chr = [
			'0' => [1,1,2,2,1],
			'1' => [2,1,1,1,2],
			'2' => [1,2,1,1,2],
			'3' => [2,2,1,1,1],
			'4' => [1,1,2,1,2],
			'5' => [2,1,2,1,1],
			'6' => [1,2,2,1,1],
			'7' => [1,1,1,2,2],
			'8' => [2,1,1,2,1],
			'9' => [1,2,1,2,1],
			'A' => [1,1],
			'Z' => [2,1]
		];

	public function encode(string $code, array $opts)
	{
		if (!preg_match('/^[\d]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded");
		}
		$orig = $code;
		$code = str_split($code);

		if (substr($opts['mode'], -1) == '+') {
			$code[] = $this->checksum_s25($code);
		}

		if ((count($code) % 2) != 0) {
			array_unshift($code, '0');
		}

		switch(strtolower(substr($opts['mode'], 0 , 5))){
			case "stand": # standard
				$block = $this->encode_s25($code);
				break;
			case "inter": # interleaved
				$block = $this->encode_i25($code);
				break;
			default:
				$block = [];
		}

		return [
			[
				'm' => $block,
				'l' => [$orig]
			]
		];
	}

	private function checksum_s25($code) 
	{
		$sum = 0;
		foreach($code as $i => $chr){
			$sum += ($i & 1) ? intval($chr) : intval($chr) * 3;
		}

		$r = $sum % 10;
		if ($r > 0) {
			$r = (10 - $r);
		}

		return $r;
	}

	private function encode_s25($code)
	{
		$seq = [2 ,2, 1];
		foreach($code as $i){
			$seq = array_merge($seq, $this->s_chr[$i]);
		}
		$seq[] = 2;
		$seq[] = 1;
		$seq[] = 2;

		$block = [];

		foreach($seq as $i){
			$block[] = [1, $i, 1];
			$block[] = [0, 1, 1];
		}

		array_pop($block);

		return $block;
	}

	private function encode_i25($code)
	{
		// add start and stop codes
		array_unshift($code, 'A', 'A');
		array_push($code, 'Z', 'A');

		$block = [];
		foreach(array_chunk($code, 2) as $c){
			$chrlen = (is_numeric($c[0])) ? 5 : 2;
			for ($s = 0; $s < $chrlen; $s++) {
				$block[] = [1, $this->i_chr[$c[0]][$s], 1];
				$block[] = [0, $this->i_chr[$c[1]][$s], 1];
			}
		}

		return $block;
	}

}