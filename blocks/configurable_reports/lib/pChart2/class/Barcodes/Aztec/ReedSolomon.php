<?php

namespace pChart\Barcodes\Aztec;

use pChart\pException;

class ReedSolomon
{
	private $size;
	private $expTable;
	private $logTable;
	# galois field
	private $gfTable = [
			4 => 19,
			6 => 67,
			8 => 301,
			10 => 1033,
			12 => 4201
		];

	public function __construct($wordSize)
	{
		$this->size = pow(2, $wordSize);
		$this->initialize($this->gfTable[$wordSize]);
	}

	# generate logarithm and anti-log tables
	private function initialize($primitive)
	{
		$this->expTable = array_fill(0, $this->size, 0);
		$this->logTable = $this->expTable;
		$x = 1;
		for ($i = 0; $i < $this->size; $i++) {
			$this->expTable[$i] = $x;
			$x <<= 1;
			if ($x >= $this->size) {
				$x ^= $primitive;
				$x &= ($this->size - 1);
			}
		}
		for ($i = 0; $i < $this->size; $i++) {
			$this->logTable[$this->expTable[$i]] = $i;
		}
	}

	private function field_multiply($a, $b)
	{
		if ($a == 0 || $b == 0) {
			return 0;
		}

		return $this->expTable[($this->logTable[$a] + $this->logTable[$b]) % ($this->size - 1)];
	}

	private function getPoly($coefficients)
	{
		while (!empty($coefficients) && $coefficients[0] == 0) {
			array_shift($coefficients);
		}

		return $coefficients;
	}

	private function buildGenerator($ecBytes)
	{
		$lastGenerator = [1];

		for ($d = 1; $d <= $ecBytes; $d++) {
			$lastGenerator = $this->multiply([1, $this->expTable[$d]], $lastGenerator);
		}

		return $lastGenerator;
	}

	private function multiply($bCoefficients, $aCoefficients)
	{
		# Coefficients are prepended 1 so can't be 0
		$aLength = count($aCoefficients);
		$bLength = count($bCoefficients);
		$product = array_fill(0, ($aLength + $bLength - 1), 0);

		for ($i = 0; $i < $aLength; $i++) {
			for ($j = 0; $j < $bLength; $j++) {
				$product[$i + $j] ^= ($this->field_multiply($aCoefficients[$i], $bCoefficients[$j]));
			}
		}

		if ($this->isZero($product)) {
			throw pException::AztecEncoderError('Divide by 0');
		}

		return $this->getPoly($product);
	}

	private function isZero($coefficients)
	{
		return $coefficients[0] == 0;
	}

	private function addOrSubtract($largerCoefficients, $smallerCoefficients)
	{
		if ($this->isZero($smallerCoefficients)) {
			return $largerCoefficients;
		}
		if ($this->isZero($largerCoefficients)) {
			return $smallerCoefficients;
		}

		if (count($smallerCoefficients) > count($largerCoefficients)) {
			list($smallerCoefficients, $largerCoefficients) = [$largerCoefficients, $smallerCoefficients];
		}

		$largerCoefficientsCount = count($largerCoefficients);
		$lengthDiff =$largerCoefficientsCount - count($smallerCoefficients);
		$sumDiff = array_slice($largerCoefficients, 0, $lengthDiff);


		for ($i = $lengthDiff; $i < $largerCoefficientsCount; $i++) {
			$sumDiff[$i] = $smallerCoefficients[$i - $lengthDiff] ^ $largerCoefficients[$i];
		}

		return $this->getPoly($sumDiff);
	}

	private function multiplyByMonomial($degree, $coefficient, $coefficients)
	{
		if ($coefficient == 0) {
			return [0];
		}

		$count = count($coefficients);
		$product = array_fill(0, ($count + $degree), 0);

		for ($i = 0; $i < $count; $i++) {
			$product[$i] = $this->field_multiply($coefficients[$i], $coefficient);
		}

		return $this->getPoly($product);
	}

	private function divide($ecBytes, $data)
	{
		$otherDegree = $ecBytes + 1;
		$otherCoefficient = $this->buildGenerator($ecBytes);

		$one = $this->multiplyByMonomial($ecBytes, 1, $data);

		while (count($one) >= $otherDegree && !$this->isZero($one)) {
			$degreeDifference = count($one) - $otherDegree;
			$scale = $this->field_multiply($one[0], 1);
			$largerCoefficients = $this->multiplyByMonomial($degreeDifference, $scale, $otherCoefficient);

			$one = $this->addOrSubtract($largerCoefficients, $one);
		}

		return $one;
	}

	public function encodePadded(array $paddedData, $ecBytes)
	{
		array_splice($paddedData, -$ecBytes);

		$coefficients = $this->divide($ecBytes, $paddedData);
		$paddedCoefficients = array_pad($coefficients, -$ecBytes, 0);

		return array_merge($paddedData, $paddedCoefficients);
	}
}
