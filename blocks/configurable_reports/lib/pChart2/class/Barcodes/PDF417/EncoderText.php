<?php

namespace pChart\Barcodes\PDF417;

/**
* Converts text to code words.
*
* Can encode: ASCII 9, 10, 13 and 32-126
* Rate: 2 characters per code word.
*
* TODO: Currently doesn't support switching to a submode for just one
* character (see T_PUN, T_UPP in
* http://grandzebu.net/informatique/codbar-en/pdf417.htm).
*/

use pChart\pException;

class EncoderText
{
	/** Character codes per submode. */
	private $characterTables = [
		"SUBMODE_UPPER" => [
			"A", "B", "C", "D", "E", "F", "G", "H", "I",
			"J", "K", "L", "M", "N", "O", "P", "Q", "R",
			"S", "T", "U", "V", "W", "X", "Y", "Z", " ",
			"SWITCH_LOWER",
			"SWITCH_MIXED",
			"SWITCH_PUNCT_SINGLE"
		],

		"SUBMODE_LOWER" => [
			"a", "b", "c", "d", "e", "f", "g", "h", "i",
			"j", "k", "l", "m", "n", "o", "p", "q", "r",
			"s", "t", "u", "v", "w", "x", "y", "z", " ",
			"SWITCH_UPPER_SINGLE",
			"SWITCH_MIXED",
			"SWITCH_PUNCT_SINGLE"
		],

		"SUBMODE_MIXED" => [
			"0", "1", "2", "3", "4", "5", "6", "7", "8",
			"9", "&", "\r", "\t", ",", ":", "#", "-", ".",
			"$", "/", "+", "%", "*", "=", "^",
			"SWITCH_PUNCT", " ",
			"SWITCH_LOWER",
			"SWITCH_UPPER",
			"SWITCH_PUNCT_SINGLE"
		],

	"SUBMODE_PUNCT" => [
		";", "<", ">", "@", "[", "\\", "]", "_", "`",
		"~", "!", "\r", "\t", ",", ":", "\n", "-", ".",
		"$", "/", "\"", "|", "*", "(", ")", "?", "{", "}", "'",
		"SWITCH_UPPER"
		]
	];

	/** Describes how to switch between submodes (can require two switches). */
	private $switching = [
		"SUBMODE_UPPER" => [
			"SUBMODE_LOWER" => ["SWITCH_LOWER"],
			"SUBMODE_MIXED" => ["SWITCH_MIXED"],
			"SUBMODE_PUNCT" => ["SWITCH_MIXED", "SWITCH_PUNCT"]
		],
		"SUBMODE_LOWER" => [
			"SUBMODE_UPPER" => ["SWITCH_MIXED", "SWITCH_UPPER"],
			"SUBMODE_MIXED" => ["SWITCH_MIXED"],
			"SUBMODE_PUNCT" => ["SWITCH_MIXED", "SWITCH_PUNCT"]
		],
		"SUBMODE_MIXED" => [
			"SUBMODE_UPPER" => ["SWITCH_UPPER"],
			"SUBMODE_LOWER" => ["SWITCH_LOWER"],
			"SUBMODE_PUNCT" => ["SWITCH_PUNCT"]
		],
		"SUBMODE_PUNCT" => [
			"SUBMODE_UPPER" => ["SWITCH_UPPER"],
			"SUBMODE_LOWER" => ["SWITCH_UPPER", "SWITCH_LOWER"],
			"SUBMODE_MIXED" => ["SWITCH_UPPER", "SWITCH_MIXED"]
		]
	];

	/** Describes which switch changes to which submode. */
	private $switchSubmode = [
		"SWITCH_UPPER" => "SUBMODE_UPPER",
		"SWITCH_LOWER" => "SUBMODE_LOWER",
		"SWITCH_PUNCT" => "SUBMODE_PUNCT",
		"SWITCH_MIXED" => "SUBMODE_MIXED"
	];

	/**
	* Reverse lookup array. Indexed by $charater, then by $submode, gives the
	* code (row) of the character in that submode.
	*/
	private $reverseLookup;

	public function __construct()
	{
		// Builds `$this->lookup` based on data in `$this->characterTables`.
		foreach ($this->characterTables as $submode => $codes) {
			foreach ($codes as $row => $char) {
				if (!isset($this->reverseLookup[$char])) {
					$this->reverseLookup[$char] = [];
				}
				$this->reverseLookup[$char][$submode] = $row;
			}
		}
	}

	public function canEncode($char)
	{
		return isset($this->reverseLookup[$char]);
	}

	public function encode(string $text)
	{
		$interim = $this->encodeInterim($text);
		return $this->encodeFinal($interim);
	}

	/**
	* Converts the given text to interim codes from the character tables.
	*/
	private function encodeInterim($text)
	{
		// The default sub-mode is uppercase
		$submode = "SUBMODE_UPPER";

		$codes = [];

		// Iterate byte-by-byte, non-ascii encoding will be encoded in bytes sub-mode.
		$textA = str_split($text);
		foreach($textA as $char) {
			// TODO: detect when to use _SINGLE switches for encoding just one character
			if (!$this->existsInSubmode($char, $submode)) {
				$prevSubmode = $submode;

				$source = $this->reverseLookup[$char];
				$submode = array_keys($source)[0];

				$codes = array_merge($codes, $this->getSwitchCodes($prevSubmode, $submode));
			}

			$codes[] = $this->getCharacterCode($char, $submode);
		}

		return $codes;
	}

	/**
	* Converts the interim code to code words.
	*/
	private function encodeFinal($codes)
	{
		$count = count($codes);

		// 900 - Code word used to switch to Text mode.
		$codeWords = [900];

		if ($count % 2 != 0){
			/* 29 is the switch in all 4 submodes and doesn't add any data. */
			$codes[] = 29;
			$count++;
		}

		for ($i = 0; $i < $count; $i += 2){
			$codeWords[] = 30 * $codes[$i] + $codes[$i+1];
		}

		return $codeWords;
	}

	/** Returns code for given character in given submode. */
	private function getCharacterCode($char, $submode)
	{
		if (!isset($this->reverseLookup[$char])) {
		$ord = ord($char);
			throw pException::PDF417EncoderError("Character [$char] (ASCII $ord) cannot be encoded.");
		}

		if (!isset($this->reverseLookup[$char][$submode])) {
		$ord = ord($char);
			throw pException::PDF417EncoderError("Character [$char] (ASCII $ord) cannot be encoded in submode [$submode].");
		}

		return $this->reverseLookup[$char][$submode];
	}

	/**
	* Returns true if given character can be encoded in given submode.
	*/
	private function existsInSubmode($char, $submode)
	{
		return isset($this->reverseLookup[$char][$submode]);
	}

	/**
	* Returns an array of one or two code for switching between given submodes.
	*/
	private function getSwitchCodes($from, $to)
	{
		if (!isset($this->switching[$from][$to])) {
			throw pException::PDF417EncoderError("Cannot find switching codes from [$from] to [$to].");
		}

		$switches = $this->switching[$from][$to];

		$codes = [];
		foreach ($switches as $switch) {
			$codes[] = $this->getCharacterCode($switch, $from);
			$from = $this->switchSubmode[$switch];
		}

		return $codes;
	}
}
