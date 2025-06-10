<?php

namespace pChart\Barcodes\PDF417;

/**
* Converts a byte array to code words.
*
* Can encode: ASCII 0-255
* Rate: 1.2 bytes per code word.
*
* Encoding process converts chunks of 6 bytes to 5 code words in base 900.
*/
class EncoderByte
{
	public function canEncode($char)
	{
		return is_string($char) && strlen($char) === 1;
	}

	public function encode(string $bytes)
	{
		$enc = array_values(unpack('C*', $bytes));
		$chunks = array_chunk($enc, 6);

		$codeWords = [];

		/* 901 - Code word used to switch to Byte mode.
		*  924 = Alternate code word used to switch to Byte mode; used when number of bytes to encode is divisible by 6.*/
		$codeWords[] = (strlen($bytes) % 6 === 0) ? 924 : 901;

		// Encode in chunks of 6 bytes
		foreach($chunks as $chunk){

			if (count($chunk) === 6) {
				if (PHP_INT_SIZE == 8){ # x64
					$cws = $this->encodeChunk64($chunk);
				} else {
					$cws = $this->encodeChunk86($chunk);
				}
			} else {
				// incomplete chunk
				$cws = $chunk;
			}

			$codeWords = array_merge($codeWords, $cws);
		}

		return $codeWords;
	}

	/**
	* Takes a chunk of 6 bytes and encodes it to 5 code words.
	* The calculation consists of switching from base 256 to base 900.
	* BC math is used to perform large number arithmetic.
	*/
	private function encodeChunk86($chunk)
	{
		$sum = "0";
		for ($i = 0; $i < 6; $i++) {
			$val = bcmul(bcpow(256, $i), $chunk[5 - $i]);
			$sum = bcadd($sum, $val);
		}

		$cws = [];
		while(bccomp($sum, 0) > 0) {
			$cws[] = intval(bcmod($sum, 900));
			$sum = bcdiv($sum, 900, 0); // Integer division
		}

		return array_reverse($cws);
	}

	private function encodeChunk64($chunk)
	{
		$sum = 0;
		for ($i = 0; $i < 6; $i++) {
			$sum += pow(256, $i) * $chunk[5 - $i];
		}

		$cws = [];
		while($sum > 0) {
			$cws[] = intval($sum % 900);
			$sum = intval($sum / 900);
		}

		return array_reverse($cws);
	}
}
