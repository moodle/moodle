<?php

namespace pChart\Barcodes\Aztec;

class Token implements \Countable
{
	private $history = [];
	private $mode = 0;
	private $shiftByteCount = 0;
	private $bitCount = 0;

	public function setState($mode, $binaryBytes, $bitCount = 0)
	{
		$this->mode = $mode;
		$this->shiftByteCount = $binaryBytes;
		$this->bitCount += $bitCount;
	}

	public function getMode()
	{
		return $this->mode;
	}

	public function getShiftByteCount()
	{
		return $this->shiftByteCount;
	}

	public function count()
	{
		return $this->bitCount;
	}

	public function getHistory()
	{
		return $this->history;
	}

	public function add($value, $bits)
	{
		$this->bitCount += $bits;
		$this->history[] = [$value, $bits];
	}

	public function endBinaryShift()
	{
		$this->shiftByteCount = 0;
	}
}
