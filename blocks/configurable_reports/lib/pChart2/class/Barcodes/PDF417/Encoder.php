<?php

namespace pChart\Barcodes\PDF417;

/**
* Encodes data into PDF417 code words.
*
* Top level data encoder which assigns encoding to lower level (byte, number, text) encoders.
*/

use pChart\pException;

class Encoder
{
	private $encoders = [];
	private $columns;
	private $securityLevel;
	private $hint;
	private $_START_CHARACTER = 0x1fea8;
	private $_STOP_CHARACTER  = 0x3fa29;

	public function __construct()
	{
		$this->encoders = [
					new EncoderNumber(),
					new EncoderText(),
					new EncoderByte()
				];
	}

	/**
	* Encodes the given data to low level code words.
	*/
	public function encode($data, array $opts)
	{

		$this->columns = $opts['columns'];
		$this->securityLevel = $opts['securityLevel'];
		$this->hint = $opts['hint'];
		
		$codeWords = $this->encodeECC($data);

		// Arrange codewords into a rows and columns
		$grid = array_chunk($codeWords, $this->columns);
		$rows = count($grid);

		// Iterate over rows
		$pixelGrid = [];
		foreach ($grid as $rowNum => $row) {

			$table = $rowNum % 3;

			// Add starting code word
			$rowCodes = [$this->_START_CHARACTER];

			// Add left-side code word
			$left = $this->getLeftCodeWord($rowNum, $rows);
			$rowCodes[] = Codes::getCode($table, $left);

			// Add data code words
			foreach ($row as $word) {
				$rowCodes[] = Codes::getCode($table, $word);
			}

			// Add right-side code word
			$right = $this->getRightCodeWord($rowNum, $rows);
			$rowCodes[] = Codes::getCode($table, $right);

			// Add ending code word
			$rowCodes[] = $this->_STOP_CHARACTER;

			$pixelRow = [];
			foreach ($rowCodes as $value) {
				$bin = decbin($value);
				$len = strlen($bin);
				for ($i = 0; $i < $len; $i++) {
					$pixelRow[] = (boolean) $bin[$i];
				}
			}

			$pixelGrid[] = $pixelRow;
		}

		return $pixelGrid;
	}

	/* Encodes data to a grid of codewords for constructing the barcode. */
	private function encodeECC($data)
	{
		// Encode data to code words
		$dataWords = $this->splitAndEncode($data);

		// Number of code correction words
		$ecCount = pow(2, $this->securityLevel + 1);
		$dataCount = count($dataWords);

		// Add padding if needed
		$padWords = $this->getPadding($dataCount, $ecCount);
		$dataWords = array_merge($dataWords, $padWords);

		// Add length specifier as the first data code word
		// Length includes the data CWs, padding CWs and the specifier itself
		$length = count($dataWords) + 1;
		array_unshift($dataWords, $length);

		// Compute error correction code words
		$reedSolomon = new ReedSolomon();
		$ecWords = $reedSolomon->compute($dataWords, $this->securityLevel);

		// Combine the code words and return
		return array_merge($dataWords, $ecWords);
	}

	private function getLeftCodeWord($rowNum, $rows)
	{
		// Table used to encode this row
		$tableID = $rowNum % 3;

		switch($tableID) {
			case 0:
				$x = intval(($rows - 1) / 3);
				break;
			case 1:
				$x = $this->securityLevel * 3;
				$x += ($rows - 1) % 3;
				break;
			case 2:
				$x = $this->columns - 1;
				break;
		}

		return 30 * intval($rowNum / 3) + $x;
	}

	private function getRightCodeWord($rowNum, $rows)
	{
		$tableID = $rowNum % 3;

		switch($tableID) {
			case 0:
				$x = $this->columns - 1;
				break;
			case 1:
				$x = intval(($rows - 1) / 3);
				break;
			case 2:
				$x = $this->securityLevel * 3;
				$x += ($rows - 1) % 3;
				break;
		}

		return 30 * intval($rowNum / 3) + $x;
	}

	private function getPadding($dataCount, $ecCount)
	{
		// Total number of data words and error correction words, additionally
		// reserve 1 code word for the length descriptor
		$totalCount = $dataCount + $ecCount + 1;
		$mod = $totalCount % $this->columns;

		if ($mod > 0) {
			$padCount = $this->columns - $mod;
			$padding = array_fill(0, $padCount, 900);
		} else {
			$padding = [];
		}

		return $padding;
	}

	/**
	* Splits the input data into chains. Then encodes each chain.
	*/
	private function splitAndEncode($data)
	{
		$codes = [];
		switch($this->hint){
			case BARCODES_PDF417_HINT_NUMBERS:
			case BARCODES_PDF417_HINT_TEXT:
			case BARCODES_PDF417_HINT_BINARY:
				$codes = $this->encoders[$this->hint]->encode($data);
				break;
			case BARCODES_PDF417_HINT_NONE:
				$chains = $this->splitData($data);
				foreach ($chains as $chain) {
					$codes = array_merge($codes, $this->encoders[$chain[1]]->encode($chain[0]));
				}
		}

		return $codes;
	}

	/**
	* Splits a string into chains (sub-strings) which can be encoded with the same encoder.
	*/
	private function splitData($data)
	{
		$length = strlen($data);
		$chains = [];

		for ($i = 0; $i < $length; $i++) {

			$e = $this->getEncoder($data[$i], $i);
			$chain = $data[$i];
			$end = false;

			while($this->encoders[$e]->canEncode($data[$i])){
				$i++;
				if (isset($data[$i])){
					$chain .= $data[$i];
				} else {
					$end = TRUE;
					break;
				}
			}

			if (!$end){
				$chain = substr($chain, 0, -1);
				$i--;
			}
			$chains[] = [$chain, $e];
		}

		return $chains;
	}

	private function getEncoder($char, $pos)
	{
		foreach ($this->encoders as $id => $encoder) {
			if ($encoder->canEncode($char)) {
				return $id;
			}
		}

		throw pException::PDF417EncoderError("Cannot encode character at position ".($pos+1));
	}

}
