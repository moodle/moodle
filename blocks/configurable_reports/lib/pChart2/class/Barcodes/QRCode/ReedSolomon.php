<?php

namespace pChart\Barcodes\QRCode;

use pChart\pException;

class ReedSolomon {

	#private $mm;		// Bits per symbol = 8
	#private $nn;		// Symbols per block (= (1<<mm)-1)  = 255
	private $alpha_to;	// log lookup table 
	private $index_of;	// Antilog lookup table 
	private $genpoly;	// Generator polynomial 
	private $nroots;	// Number of generator roots = number of parity symbols 
	private $pad;		// Padding bytes in shortened block 
	private $parity;

	// RawCode
	private $blocks;
	private $b1;
	private $rsblocks = [];

	function __construct(array $dataCode, int $dataLength, int $b1, int $b2, int $blocks,int $nroots)
	{
		$this->b1 = $b1;
		$this->pad = intval($dataLength / $blocks);
		$this->nroots = $nroots;
		$this->blocks = $blocks;

		// Check parameter ranges
		if($this->nroots >= 256){
			throw pException::QRCodeEncoderError("version estimation failed");
		}

		// Common code for intializing a Reed-Solomon control block (char or int symbols)
		// Copyright 2004 Phil Karn, KA9Q
		// May be used under the terms of the GNU Lesser General Public License (LGPL)
		$this->parity = array_fill(0, $this->nroots, 0);
		$this->genpoly = $this->parity;
		array_unshift($this->genpoly,1);
		$this->alpha_to = array_fill(0, 256, 0);
		$this->index_of = $this->alpha_to;

		// Generate Galois field lookup tables
		$this->index_of[0] = 255; // log(zero) = -inf
		$sr = 1;

		for($i = 0; $i < 255; $i++) {
			$this->index_of[$sr] = $i;
			$this->alpha_to[$i] = $sr;
			$sr <<= 1;
			if($sr & 256) {
				$sr ^= 285; # gfpoly
			}
			$sr &= 255;
		}

		if($sr != 1){
			throw pException::QRCodeEncoderError('field generator polynomial is not primitive!');
		}

		/* Form RS code generator polynomial from its roots */
		for ($i = 0; $i < $this->nroots; $i++) {

			$this->genpoly[$i+1] = 1;

			// Multiply rs->genpoly[] by  @**(root + x)
			for ($j = $i; $j > 0; $j--) {
				if ($this->genpoly[$j] != 0) {
					$this->genpoly[$j] = $this->genpoly[$j-1] ^ $this->alpha_to[($this->index_of[$this->genpoly[$j]] + $i) % 255];
				} else {
					$this->genpoly[$j] = $this->genpoly[$j-1];
				}
			}
			// rs->genpoly[0] can never be zero
			$this->genpoly[0] = $this->alpha_to[($this->index_of[$this->genpoly[0]] + $i) % 255];
		}

		// convert rs->genpoly[] to index form for quicker encoding
		for ($i = 0; $i <= $this->nroots; $i++){
			$this->genpoly[$i] = $this->index_of[$this->genpoly[$i]];
		}

		for($i = 0; $i < $this->b1; $i++) { # rsBlockNum1
			$data = array_slice($dataCode, $this->pad * $i);
			$this->rsblocks[$i] = [$data, $this->encode_rs_char($data, $this->pad)];
		}

		if($b2 != 0) {
			for($i = 0; $i < $b2; $i++) {
				$inc = $this->pad + (($i == 0) ? 0 : 1);
				$data = array_slice($data, $inc);
				$this->rsblocks[$this->b1 + $i] = [$data, $this->encode_rs_char($data, $this->pad + 1)];
			}
		}
	}

	private function encode_rs_char($data, $pad)
	{
		$parity = $this->parity;

		for($i=0; $i< $pad; $i++) {
			$feedback = $this->index_of[$data[$i] ^ $parity[0]];
			if($feedback != 255) {
				for($j=1; $j < $this->nroots; $j++) {
					$parity[$j] ^= $this->alpha_to[($feedback + $this->genpoly[$this->nroots-$j]) % 255];
				}
				$parity[] = $this->alpha_to[($feedback + $this->genpoly[0]) % 255];
			} else {
				$parity[] = 0;
			}
			array_shift($parity);
		}

		return $parity;
	}

	public function getDataCode($i)
	{
		$blockNo = $i % $this->blocks;
		$col = intval($i / $this->blocks);
		if($col >= $this->pad) {
			$blockNo += $this->b1;
		}
		return $this->rsblocks[$blockNo][0][$col];
	}

	public function getEccCode($i)
	{
		return $this->rsblocks[intval($i % $this->blocks)][1][intval($i / $this->blocks)];
	}
}
