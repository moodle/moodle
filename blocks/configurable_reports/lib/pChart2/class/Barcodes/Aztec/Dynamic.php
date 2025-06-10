<?php

namespace pChart\Barcodes\Aztec;

class Dynamic
{
	private $states;
	private $charMap;
	private $shiftTable;
	private $latchTable;

	private $MODE_UPPER = 0;
	private $MODE_LOWER = 1;
	private $MODE_DIGIT = 2;
	private $MODE_MIXED = 3;
	private $MODE_PUNCT = 4;

	function __construct()
	{
		$this->charMap = $this->genCharMapping();
		$this->shiftTable = $this->genShiftTable();
		$this->latchTable = [
			[0,327708,327710,327709,656318],
			[590318,0,327710,327709,656318],
			[262158,590300,0,590301,932798],
			[327709,327708,656318,0,327710],
			[327711,656380,656382,656381,0]
		];
	}

	private function genShiftTable()
	{
		$shiftTable = [];
		for ($i = 0; $i < 6; $i++) {
			$shiftTable[] = array_fill(0, 6, -1);
		}
		$shiftTable[0][4] = 0;
		$shiftTable[1][4] = 0;
		$shiftTable[1][0] = 28;
		$shiftTable[3][4] = 0;
		$shiftTable[2][4] = 0;
		$shiftTable[2][0] = 15;

		return $shiftTable;
	}

	private function getLatch($fromMode, $toMode)
	{
		return $this->latchTable[$fromMode][$toMode];
	}

	private function getShift($fromMode, $toMode)
	{
		return $this->shiftTable[$fromMode][$toMode];
	}

	private function genCharMapping()
	{
		$charMap = [];
		for ($i = 0; $i < 5; $i++) {
			$charMap[] = array_fill(0, 256, 0);
		}

		# ord(' ') = 32
		# ord('A') = 65
		# ord('Z') = 90
		# ord('a') = 97
		# ord('z') = 122
		# ord('0') = 48
		# ord('9') = 57
		# ord(',') = 44
		# ord('.') = 46

		$charMap[0][32] = 1;
		for ($c = 65; $c <= 90; $c++) {
			$charMap[0][$c] = $c - 65 + 2;
		}

		$charMap[1][32] = 1;
		for ($c = 97; $c <= 122; $c++) {
			$charMap[1][$c] = $c - 97 + 2;
		}

		$charMap[2][32] = 1;
		for ($c = 48; $c <= 57; $c++) {
			$charMap[2][$c] = $c - 48 + 2;
		}
		$charMap[2][44] = 12;
		$charMap[2][46] = 13;

		//	'\0', ' ', '\1', '\2', '\3', '\4', '\5', '\6', '\7', '\b', '\t', '\n',
		//	'\13', '\f', '\r', '\33', '\34', '\35', '\36', '\37', '@', '\\', '^',
		//	'_', '`', '|', '~', '\177',
		$mixedTable = [32 => 1,	64 => 20, 92 => 27,	94 => 22, 95 => 23, 96 => 24, 124 => 25, 126 => 26];

		foreach($mixedTable as $i => $val)
		{
			$charMap[3][$val] = $i;
		}

		// '\0', '\r', '\0', '\0', '\0', '\0', '!', '\'', '#', '$', '%', '&', '\'',
		// '(', ')', '*', '+', ',', '-', '.', '/', ':', ';', '<', '=', '>', '?',
		// '[', ']', '{', '}',
		$punctTable = [
			33 => 6, 35 => 8, 36 => 9, 37 => 10,
			38 => 11, 39 => 12,	40 => 13, 41 => 14,
			42 => 15, 43 => 16,	44 => 17, 45 => 18,
			46 => 19, 47 => 20,	58 => 21, 59 => 22,
			60 => 23, 61 => 24,	62 => 25, 63 => 26,
			91 => 27, 92 => 5, 93 => 28, 123 => 29,
			125 => 30
		];

		foreach($punctTable as $i => $val){
			$charMap[4][$i] = $val;
		}

		return $charMap;
	}

	private function getCharMapping($mode, $char)
	{
		return $this->charMap[$mode][$char];
	}

	public function encode($data)
	{
		# ord('\r') = 92
		# ord('.') = 46
		# ord(',') = 44
		# ord(':') = 58
		# ord('\n') = 92
		# ord(' ') = 32
		# ord('') = 0

		$textCodes = array_values(unpack('C*', $data));
		$textCount = count($textCodes);

		$this->states = [new Token()];

		for ($index = 0; $index < $textCount; $index++) {

			$pairCode = 0;
			if ($index + 1 < $textCount){
				$nextChar = $textCodes[$index + 1];
				switch ($textCodes[$index]) {
					case 92:
						if ($nextChar == 92) {
							$pairCode = 2;
						}
						break;
					case 46:
						if ($nextChar == 32) {
							$pairCode = 3;
						}
						break;
					case 44:
						if ($nextChar == 32) {
							$pairCode = 4;
						}
						break;
					case 58:
						if ($nextChar == 32) {
							$pairCode = 5;
						}
						break;
				}
			}

			if ($pairCode > 0) {
				$this->updateStateListForPair($pairCode);
				$index++;
			} else {
				$this->updateStateListForChar($textCodes[$index]);
			}

			$this->simplifyStates();
		}

		$minState = $this->states[0];
		foreach ($this->states as $state) {
			if (count($state) < count($minState)) {
				$minState = $state;
			}
		}

		# isBetterThanOrEqualTo guarantees binaryShifts are gone 
		return $minState->getHistory();
	}

	private function updateStateListForChar($ch)
	{
		$result = [];

		foreach ($this->states as $state) {
			$current_mode = $state->getMode();
			$notInCurrentTable = ($this->getCharMapping($current_mode, $ch) == 0);
			$binary = TRUE;
			for ($mode = 0; $mode <= 4; $mode++) {
				$charInMode = $this->getCharMapping($mode, $ch);
				if ($charInMode > 0) {
					if ($binary) {
						$state->endBinaryShift();
						$binary = FALSE;
					}
					if ($notInCurrentTable || $mode == $current_mode || $mode == 2) {
						$result[] = $this->latchAndAppend($state, $mode, $charInMode);
					}
					if ($notInCurrentTable && $this->getShift($current_mode, $mode) >= 0) {
						$result[] = $this->shiftAndAppend($state, $mode, $charInMode);
					}
				}
			}

			if ($state->getShiftByteCount() > 0 || $notInCurrentTable) {
				# can safely change the last one
				$this->addBinaryShiftChar($state);
				$result[] = $state;
			}
		}

		$this->states = $result;
	}

	private function updateStateListForPair($pairCode)
	{
		$result = [];
		foreach ($this->states as $state) {
			$state->endBinaryShift();

			$result[] = $this->latchAndAppend($state, 4, $pairCode);
			if ($state->getMode() != 4) {
				$result[] = $this->shiftAndAppend($state, 4, $pairCode);
			}
			if ($pairCode == 3 || $pairCode == 4) {
				$interm = $this->latchAndAppend($state, 2, 16 - $pairCode);
				$result[] = $this->latchAndAppend($interm, 2, 1);
			}
			if ($state->getShiftByteCount() > 0) {
				$this->addBinaryShiftChar($state);
				$this->addBinaryShiftChar($state);
				$result[] = $state;
			}
		}

		$this->states = $result;
	}

	private function simplifyStates()
	{
		$result = [];

		foreach ($this->states as $state) {

			$result_count = count($result);
			for ($i = 0; $i < $result_count; $i++) {
				if ($this->isBetterThanOrEqualTo($result[$i], $state)) {
					continue 2;
				}
			}

			$result[] = $state;
		}

		$this->states = $result;
	}

	private function isBetterThanOrEqualTo($one, $other)
	{
		$mySize = count($one) + ($this->getLatch($one->getMode(), $other->getMode()) >> 16);
		if ($other->getShiftByteCount() > 0 && ($one->getShiftByteCount() == 0 || $one->getShiftByteCount() > $other->getShiftByteCount())) {
			$mySize += 10;
		}

		return $mySize <= count($other);
	}

	private function shiftAndAppend($token, $mode, $value)
	{
		$token = clone $token;
		$current_mode = $token->getMode();

		$thisModeBitCount = ($current_mode == $this->MODE_DIGIT ? 4 : 5);
		$token->add($this->getShift($current_mode, $mode), $thisModeBitCount);
		$token->add($value, 5);
		$token->endBinaryShift();

		return $token;
	}

	private function latchAndAppend($token, $mode, $value)
	{
		$token = clone $token;
		$current_mode = $token->getMode();

		if ($mode != $current_mode) {
			$latch = $this->getLatch($current_mode, $mode);
			$token->add(($latch & 0xFFFF), ($latch >> 16));
		}

		$thisModeBitCount = ($mode == $this->MODE_DIGIT ? 4 : 5);
		$token->add($value, $thisModeBitCount);
		$token->setState($mode, 0);

		return $token;
	}

	private function addBinaryShiftChar(&$token)
	{
		$current_mode = $token->getMode();

		if ($current_mode == $this->MODE_PUNCT || $current_mode == $this->MODE_DIGIT) {
			$latch = $this->getLatch($current_mode, $this->MODE_UPPER);
			$token->add(($latch & 0xFFFF), ($latch >> 16));
			$current_mode = $this->MODE_UPPER;
		}

		$shiftByteCount = $token->getShiftByteCount();
		if ($shiftByteCount == 0 || $shiftByteCount == 31) {
			$deltaBitCount = 18;
		} elseif ($shiftByteCount == 62) {
			$deltaBitCount = 9;
		} else {
			$deltaBitCount = 8;
		}

		$token->setState($current_mode, $shiftByteCount + 1, $deltaBitCount);
	}
}
