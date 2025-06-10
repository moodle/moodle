<?php

namespace pChart\Barcodes\Linear;

use pChart\pException;

class IMB {

	private $table2of13 = [];
	private $table5of13 = [];

	private $asc_chr = [
		4, 0, 2, 6, 3, 5, 1, 9, 8, 7, 1, 2, 0, 6, 4, 8, 2, 9, 5, 3, 0, 1, 3, 7, 4, 6, 8, 9, 2, 0, 5, 1, 9, 4,
		3, 8, 6, 7, 1, 2, 4, 3, 9, 5, 7, 8, 3, 0, 2, 1, 4, 0, 9, 1, 7, 0, 2, 4, 6, 3, 7, 1, 9, 5, 8
	];

	private $dsc_chr = [
		7, 1, 9, 5, 8, 0, 2, 4, 6, 3, 5, 8, 9, 7, 3, 0, 6, 1, 7, 4, 6, 8, 9, 2, 5, 1, 7, 5, 4, 3, 8, 7, 6, 0, 2,
		5, 4, 9, 3, 0, 1, 6, 8, 2, 0, 4, 5, 9, 6, 7, 5, 2, 6, 3, 8, 5, 1, 9, 8, 7, 4, 0, 2, 6, 3
	];

	private $asc_pos = [
		3, 0, 8, 11, 1, 12, 8, 11, 10, 6, 4, 12, 2, 7, 9, 6, 7, 9, 2, 8, 4, 0, 12, 7, 10, 9, 0, 7, 10, 5, 7, 9, 6,
		8, 2, 12, 1, 4, 2, 0, 1, 5, 4, 6, 12, 1, 0, 9, 4, 7, 5, 10, 2, 6, 9, 11, 2, 12, 6, 7, 5, 11, 0, 3, 2
	];

	private $dsc_pos = [
		2, 10, 12, 5, 9, 1, 5, 4, 3, 9, 11, 5, 10, 1, 6, 3, 4, 1, 10, 0, 2, 11, 8, 6, 1, 12, 3, 8, 6, 4, 4, 11, 0,
		6, 1, 9, 11, 5, 3, 7, 3, 10, 7, 11, 8, 2, 10, 3, 5, 8, 0, 3, 12, 11, 8, 4, 5, 1, 3, 0, 7, 12, 9, 8, 10
	];

	public function encode(string $code, array $opts)
	{
		if (strtoupper($opts['mode']) == 'PRE') {
			$blocks = $this->encode_pre($code);
		} else {
			if (empty($this->table2of13)){
				// generate lookup tables
				$this->table2of13 = $this->imb_tables(2, 78);
				$this->table5of13 = $this->imb_tables(5, 1287);
			}
			$blocks = $this->encode_raw($code);
		}

		return [
			[
				'm' => $blocks,
				'l' => [$code]
			]
		];
	}

	// IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200- pre-processed
	private function encode_pre($code) 
	{
		if (!preg_match('/^[fadtFADT]{65}$/', $code) == 1) {
			throw pException::InvalidInput("Text can not be encoded by IMB");
		}

		$blocks = [];

		foreach(str_split(strtolower($code)) as $char){
			switch($char) {
				case 'f': {
					// full bar
					$p = 0;
					$h = 3;
					break;
				}
				case 'a': {
					// ascender
					$p = 0;
					$h = 2;
					break;
				}
				case 'd': {
					// descender
					$p = 1;
					$h = 2;
					break;
				}
				case 't': {
					// tracker (short)
					$p = 1;
					$h = 1;
					break;
				}
			}
			$blocks[] = [1, 1, $h, $p];
			$blocks[] = [0, 1, 2, 0];
		}

		array_pop($blocks);

		return $blocks;
	}

	private function encode_raw($code)
	{
		$code_arr = explode('-', $code);
		$tracking_number = $code_arr[0];

		if (isset($code_arr[1])) {
			$routing_code = $code_arr[1];
		} else {
			$routing_code = '';
		}

		// Conversion of Routing Code
		switch (strlen($routing_code)) {
			case 0:
				$bin = 0;
				break;
			case 5:
				$bin = bcadd($routing_code, '1');
				break;
			case 9:
				$bin = bcadd($routing_code, '100001');
				break;
			case 11:
				$bin = bcadd($routing_code, '1000100001');
				break;
			default:
				throw pException::InvalidInput("Text can not be encoded by IMB");
				break;
		}

		$bin = bcmul($bin, 10);
		$bin = bcadd($bin, $tracking_number[0]);
		$bin = bcmul($bin, 5);
		$bin = bcadd($bin, $tracking_number[1]);
		$bin .= substr($tracking_number, 2, 18);
		// calculate frame check sequence
		$fcs = $this->imb_crc11fcs($bin);
		// convert binary data to codewords
		$codewords = [];
		$codewords[0] = bcmod($bin, 636) * 2;
		$bin = bcdiv($bin, 636);
		for ($i = 1; $i < 9; ++$i) {
			$codewords[$i] = bcmod($bin, 1365);
			$bin = bcdiv($bin, 1365);
		}
		$codewords[9] = $bin;
		if (($fcs >> 10) == 1) {
			$codewords[9] += 659;
		}

		// convert codewords to characters
		$characters = [];
		$bitmask = 512;
		foreach ($codewords as $val) {
			if ($val <= 1286) {
				$chrcode = $this->table5of13[$val];
			} else {
				$chrcode = $this->table2of13[($val - 1287)];
			}
			if (($fcs & $bitmask) > 0) {
				// bitwise invert
				$chrcode = ((~$chrcode) & 8191);
			}
			$characters[] = $chrcode;
			$bitmask /= 2;
		}
		$characters = array_reverse($characters);
		// build bars
		$blocks = [];
		for ($i = 0; $i < 65; ++$i) {

			$asc = (bool)(($characters[$this->asc_chr[$i]] & pow(2, $this->asc_pos[$i])) > 0);
			$dsc = (bool)(($characters[$this->dsc_chr[$i]] & pow(2, $this->dsc_pos[$i])) > 0);

			switch (true) {
				case ($asc AND $dsc): // full bar (F)
					$p = 0;
					$h = 3;
					break;
				case $asc: // ascender (A)
					$p = 0;
					$h = 2;
					break;
				case $dsc: // descender (D)
					$p = 1;
					$h = 2;
					break;
				default: // tracker (T)
					$p = 1;
					$h = 1;
			}

			$blocks[] = [1, 1, 1, $h, $p];
			$blocks[] = [0, 1, 1, 2, 0];
		}

		unset($blocks[129]);

		return $blocks;
	}

	private function imb_crc11fcs($binary_code)
	{
		// convert to hexadecimal
		$binary_code = dechex(intval($binary_code));
		// pad to get 13 bytes;
		$binary_code = str_pad($binary_code, 26, '0', STR_PAD_LEFT);
		// convert string to array of bytes
		$code_arr = str_split($binary_code, 2);
		$genpoly = 0x0F35; // generator polynomial
		$fcs = 0x07FF; // Frame Check Sequence
		// do most significant byte skipping the 2 most significant bits
		$data = hexdec($code_arr[0]) << 5;
		for ($bit = 2; $bit < 8; ++$bit) {
			if (($fcs ^ $data) & 0x400) {
				$fcs = ($fcs << 1) ^ $genpoly;
			} else {
				$fcs = ($fcs << 1);
			}
			$fcs &= 0x7FF;
			$data <<= 1;
		}
		// do rest of bytes
		for ($byte = 1; $byte < 13; ++$byte) {
			$data = hexdec($code_arr[$byte]) << 3;
			for ($bit = 0; $bit < 8; ++$bit) {
				if (($fcs ^ $data) & 0x400) {
					$fcs = ($fcs << 1) ^ $genpoly;
				} else {
					$fcs = ($fcs << 1);
				}
				$fcs &= 0x7FF;
				$data <<= 1;
			}
		}
		return $fcs;
	}

	private function imb_reverse_us($num) 
	{
		# Human readable version.
		$rev = strrev(decbin($num));
		return bindec(str_pad($rev, 16, "0"));

		#$rev = 0;
		#for ($i = 0; $i < 16; ++$i) {
		#	$rev <<= 1;
		#	$rev |= ($num & 1);
		#	$num >>= 1;
		#}
		#return $rev;
	}

	# https://stackoverflow.com/questions/16848931/how-to-fastest-count-the-number-of-set-bits-in-php
	private function bitsCount(int $integer)
	{
		$count = $integer - (($integer >> 1) & 0x55555555);
		$count = (($count >> 2) & 0x33333333) + ($count & 0x33333333);
		return ((((($count >> 4) + $count) & 0x0F0F0F0F) * 0x01010101) >> 24) & 0xFF;
	}

	private function imb_tables($n, $size) 
	{
		$table = [];
		$lli = 0; // LUT lower index
		$lui = $size - 1; // LUT upper index
		# for $n = 5 & $n = 2 -> $count does not exceed 7937
		for ($count = 0; $count < 8192; ++$count) {
			// if we don't have the right number of bits on, go on to the next value
			if ($this->bitsCount($count) == $n) {
				$reverse = ($this->imb_reverse_us($count) >> 3);
				// if the reverse is less than count, we have already visited this pair before
				if ($reverse >= $count) {
					// If count is symmetric, place it at the first free slot from the end of the list.
					// Otherwise, place it at the first free slot from the beginning of the list AND place $reverse ath the next free slot from the beginning of the list
					if ($reverse == $count) {
						$table[$lui] = $count;
						--$lui;
					} else {
						$table[$lli] = $count;
						++$lli;
						$table[$lli] = $reverse;
						++$lli;
					}
				}
			}
		}
		return $table;
	}
}