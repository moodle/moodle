<?php

namespace pChart\Barcodes\Aztec;

use pChart\pException;

class Encoder
{
	private $MATRIX;

	private function mSet($x, $y)
	{
		$this->MATRIX[$x][$y] = 1;
	}

	private function toByte($bstream)
	{
		$data = [];
		foreach($bstream as $d){
			for ($i = $d[1] - 1; $i >= 0; $i--) {
				$data[] = ($d[0] >> $i) & 1;
			}
		}
		return $data;
	}

	private function appendBstream(&$bstream, $data, $bits)
	{
		for ($i = $bits - 1; $i >= 0; $i--) {
			$bstream[] = ($data >> $i) & 1;
		}
	}

	public function encode($content, $options)
	{
		$LAYERS_COMPACT = 5;
		$LAYERS_FULL = 33;

		$wordSizeDict = [
			4,  6,  6,  8,  8,  8,  8,  8,  8, 10, 10,
			10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10,
			10, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12
		];

		if ($options['hint']) {
			$bstream = (new Dynamic())->encode($content);
		} else {
			$bstream = (new Binary())->encode($content);
		}

		$bits = $this->toByte($bstream);
		$bitCount = count($bits);

		$eccBits = intval($bitCount * $options['eccPercent'] / 100 + 11);
		$totalSizeBits = $bitCount + $eccBits; 
		$compact = ($totalSizeBits <= 608); # 4 layers

		$wordSize = 0;
		$stuffedBits = [];

		if ($compact) {
			$UPTO = $LAYERS_COMPACT;
			$bitsPer = function($layers) {
				return (88 + 16 * $layers) * $layers;
			};
		} else {
			$UPTO = $LAYERS_FULL;
			$bitsPer = function($layers) {
				return (112 + 16 * $layers) * $layers;
			};
		}

		for ($layers = 1; $layers < $UPTO; $layers++) {
			$bitsPerLayer = $bitsPer($layers);
			if ($bitsPerLayer >= $totalSizeBits) {
				if ($wordSize != $wordSizeDict[$layers]) {
					$wordSize = $wordSizeDict[$layers];
					$stuffedBits = $this->stuffBits($bits, $wordSize);
				}
				if (count($stuffedBits) + $eccBits <= $bitsPerLayer) {
					break;
				}
			}
		}

		if ($layers == $LAYERS_FULL) {
			throw pException::AztecEncoderError('Data too large');
		}

		// generate check words
		$messageBits = $this->generateCheckWords($stuffedBits, $bitsPerLayer, $wordSize);

		// allocate symbol
		if ($compact) {
			$matrixSize = $baseMatrixSize = 11 + $layers * 4;
			$center = intval($matrixSize / 2);
			$alignmentMap = range(0, $matrixSize - 1);
		} else {
			$baseMatrixSize = 14 + $layers * 4;
			$origCenter = intval($baseMatrixSize / 2);
			$matrixSize = $baseMatrixSize + 1 + 2 * intval(($origCenter - 1) / 15);
			$alignmentMap = array_fill(0, $baseMatrixSize, 0);
			$center = intval($matrixSize / 2);
			for ($i = 0; $i < $origCenter; $i++) {
				$newOffset = $i + intval($i / 15);
				$alignmentMap[$origCenter - $i - 1] = $center - $newOffset - 1;
				$alignmentMap[$origCenter + $i] = $center + $newOffset + 1;
			}
		}

		$this->MATRIX = array_fill(0, $matrixSize, array_fill(0, $matrixSize, 0));;

		// draw mode and data bits
		for ($i = 0, $rowOffset = 0; $i < $layers; $i++) {
			if ($compact) {
				$rowSize = ($layers - $i) * 4 + 9;
			} else {
				$rowSize = ($layers - $i) * 4 + 12;
			}
			for ($j = 0; $j < $rowSize; $j++) {
				$columnOffset = $j * 2;
				for ($k = 0; $k < 2; $k++) {
					if ($messageBits[$rowOffset + $columnOffset + $k]) {
						$this->mSet($alignmentMap[$i * 2 + $k], $alignmentMap[$i * 2 + $j]);
					}
					if ($messageBits[$rowOffset + $rowSize * 2 + $columnOffset + $k]) {
						$this->mSet($alignmentMap[$i * 2 + $j], $alignmentMap[$baseMatrixSize - 1 - $i * 2 - $k]);
					}
					if ($messageBits[$rowOffset + $rowSize * 4 + $columnOffset + $k]) {
						$this->mSet($alignmentMap[$baseMatrixSize - 1 - $i * 2 - $k], $alignmentMap[$baseMatrixSize - 1 - $i * 2 - $j]);
					}
					if ($messageBits[$rowOffset + $rowSize * 6 + $columnOffset + $k]) {
						$this->mSet($alignmentMap[$baseMatrixSize - 1 - $i * 2 - $j], $alignmentMap[$i * 2 + $k]);
					}
				}
			}
			$rowOffset += $rowSize * 8;
		}

		// generate mode message
		$messageSizeInWords = intval((count($stuffedBits) + $wordSize - 1) / $wordSize);
		$modeMessage = [];
		if ($compact) {
			$this->appendBstream($modeMessage, $layers - 1, 2);
			$this->appendBstream($modeMessage, $messageSizeInWords - 1, 6);
			$modeMessage = $this->generateCheckWords($modeMessage, 28, 4);

			for ($i = 0; $i < 7; $i++) {
				if ($modeMessage[$i]) {
					$this->mSet($center - 3 + $i, $center - 5);
				}
				if ($modeMessage[$i + 7]) {
					$this->mSet($center + 5, $center - 3 + $i);
				}
				if ($modeMessage[20 - $i]) {
					$this->mSet($center - 3 + $i, $center + 5);
				}
				if ($modeMessage[27 - $i]) {
					$this->mSet($center - 5, $center - 3 + $i);
				}
			}
			// draw alignment marks
			$this->drawBullsEye($center, 5);

		} else {
			$this->appendBstream($modeMessage, $layers - 1, 5);
			$this->appendBstream($modeMessage, $messageSizeInWords - 1, 11);
			$modeMessage = $this->generateCheckWords($modeMessage, 40, 4);

			for ($i = 0; $i < 10; $i++) {
				if ($modeMessage[$i]) {
					$this->mSet($center - 5 + $i + intval($i / 5), $center - 7);
				}
				if ($modeMessage[$i + 10]) {
					$this->mSet($center + 7, $center - 5 + $i + intval($i / 5));
				}
				if ($modeMessage[29 - $i]) {
					$this->mSet($center - 5 + $i + intval($i / 5), $center + 7);
				}
				if ($modeMessage[39 - $i]) {
					$this->mSet($center - 7, $center - 5 + $i + intval($i / 5));
				}
			}

			// draw alignment marks
			$this->drawBullsEye($center, 7);
			for ($i = 0, $j = 0; $i < intval($baseMatrixSize / 2) - 1; $i += 15, $j += 16) {
				for ($k = $center & 1; $k < $matrixSize; $k += 2) {
					$this->mSet($center - $j, $k);
					$this->mSet($center + $j, $k);
					$this->mSet($k, $center - $j);
					$this->mSet($k, $center + $j);
				}
			}
		}

		return $this->MATRIX;
	}

	private function drawBullsEye($center, $size)
	{
		for ($i = 0; $i < $size; $i += 2) {
			for ($j = $center - $i; $j <= $center + $i; $j++) {
				$this->mSet($j, $center - $i);
				$this->mSet($j, $center + $i);
				$this->mSet($center - $i, $j);
				$this->mSet($center + $i, $j);
			}
		}
		$this->mSet($center - $size, $center - $size);
		$this->mSet($center - $size + 1, $center - $size);
		$this->mSet($center - $size, $center - $size + 1);
		$this->mSet($center + $size, $center - $size);
		$this->mSet($center + $size, $center - $size + 1);
		$this->mSet($center + $size, $center + $size - 1);
	}

	private function generateCheckWords($stuffedBits, $totalSymbolBits, $wordSize)
	{
		$messageSizeInWords = intval((count($stuffedBits) + $wordSize - 1) / $wordSize);
		for ($i = $messageSizeInWords * $wordSize - count($stuffedBits); $i > 0; $i--) {
			$stuffedBits[] = 1;
		}
		$totalWords = intval($totalSymbolBits / $wordSize);
		$ecBytes = $totalWords - $messageSizeInWords;

		$messageWords = $this->bitsToWords($stuffedBits, $wordSize, $totalWords);
		
		if ($messageSizeInWords == 0) {
			throw pException::AztecEncoderError('No data bytes provided');
		}

		if ($ecBytes == 0) {
			throw pException::AztecEncoderError('No error correction bytes');
		}

		$rs = new ReedSolomon($wordSize);
		$messageWords = $rs->encodePadded($messageWords, $ecBytes);

		$startPad = $totalSymbolBits % $wordSize;
		$messageBits = [[0, $startPad]];

		foreach ($messageWords as $messageWord) {
			$messageBits[] = [$messageWord, $wordSize];
		}

		return $this->toByte($messageBits);
	}

	private function bitsToWords($stuffedBits, $wordSize, $totalWords)
	{
		$message = array_fill(0, $totalWords, 0);
		$n = intval(count($stuffedBits) / $wordSize);
		for ($i = 0; $i < $n; $i++) {
			$value = 0;
			for ($j = 0; $j < $wordSize; $j++) {
				$value |= $stuffedBits[$i * $wordSize + $j] ? (1 << $wordSize - $j - 1) : 0;
			}
			$message[$i] = $value;
		}

		return $message;
	}

	private function stuffBits($bits, $wordSize)
	{
		$out = [];

		$n = count($bits);
		$mask = (1 << $wordSize) - 2;
		for ($i = 0; $i < $n; $i += $wordSize) {
			$word = 0;
			for ($j = 0; $j < $wordSize; $j++) {
				if ($i + $j >= $n || $bits[$i + $j]) {
					$word |= 1 << ($wordSize - 1 - $j);
				}
			}
			if (($word & $mask) == $mask) {
				$out[] = [$word & $mask, $wordSize];
				$i--;
			} elseif (($word & $mask) == 0) {
				$out[] = [$word | 1, $wordSize];
				$i--;
			} else {
				$out[] = [$word, $wordSize];
			}
		}

		$out = $this->toByte($out);

		$n = count($out);
		$remainder = $n % $wordSize;

		if ($remainder != 0) {
			$j = 1;
			for ($i = 0; $i < $remainder; $i++) {
				if (!$out[$n - 1 - $i]) {
					$j = 0;
				}
			}
			for ($i = $remainder; $i < $wordSize - 1; $i++) {
				$out[] = 1;
			}
			$out[] = ($j ^ 1);
		}

		return $out;
	}
}
