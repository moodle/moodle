<?php

namespace pChart\Barcodes\PDF417;

/**
* Converts numbers to code words.
*
* Can encode: digits 0-9
* Rate: 2.9 digits per code word.
*/
class EncoderNumber
{
	public function canEncode($char)
	{
		return is_string($char) && is_numeric($char);
	}

	/**
	* The "Numeric" mode is a conversion from base 10 to base 900.
	*
	* - numbers are taken in groups of 44 (or less)
	* - digit "1" is added to the beginning of the group
	*  (it will later beremoved by the decoding procedure)
	* - base is changed from 10 to 900
	*/
	public function encode(string $digits)
	{
		// Count the number of 44 character chunks
		$chunks = str_split($digits, 44);

		// 902 - Code word used to switch to Numeric mode.
		$codeWords = [902];

		// Encode in chunks of 44 digits
		foreach($chunks as $chunk){
			$codeWords = array_merge($codeWords, $this->encodeChunk($chunk));
		}

		return $codeWords;
	}

	private function encodeChunk($chunk)
	{
		$chunk = "1" . $chunk;

		$cws = [];
		while(bccomp($chunk, 0) > 0) {
			$cws[] = intval(bcmod($chunk, 900));
			$chunk = bcdiv($chunk, 900, 0); // Integer division
		}

		return array_reverse($cws);
	}
}
