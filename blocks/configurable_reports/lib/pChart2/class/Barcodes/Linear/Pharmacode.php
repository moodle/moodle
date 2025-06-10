<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class Pharmacode {

	public function encode(string $code, array $opts)
	{
		if (!preg_match('/^[\d]+$/', $code)){
			throw pException::InvalidInput("Text can not be encoded");
		}

		$orig = $code;

		if (strtoupper($opts['mode']) == "2T"){
			$block = $this->pharmacode2t(intval($code));
		} else {
			$block = $this->pharmacode(intval($code));
		}

		return [
			[
				'm' => $block,
				'l' => [$orig]
			]
		];
	}

	private function pharmacode($code)
	{
		$block = [];

		while ($code > 0) {
			if (($code % 2) == 0) {
				$w = 3;
				$code -= 2;
			} else {
				$w = 1;
				$code -= 1;
			}
			$code /= 2;

			$block[] = [1, $w, 1];
			$block[] = [0, 2, 1];
		}

		array_pop($block);

		return array_reverse($block);
	}

	private function pharmacode2t($code)
	{
		$block = [];
		do {
			$c = $code % 3;
			if ($c == 0){
				$rev = 3;
				$h = 2;
			} else {
				$rev = $c;
				$h = 1;
			}
			$code = ($code - $rev) / 3;
			$block[] = [0, 1, 1, 2, 0];
			$block[] = [1, 1, 1, $h, $c % 2];

		} while ($code != 0);

		return array_reverse($block);
	}
}
