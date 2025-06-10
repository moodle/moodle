<?php 
/*
pColorGradient - Data structure for gradient color

Version     : 2.4.0-dev
Made by     : Momchil Bozhinov
Last Update : 14/10/2019

This file can be distributed under the MIT license

*/

namespace pChart;

class pColorGradient
{
	private $StartColor;
	private $EndColor;
	private $Offsets = NULL;
	private $Segments;

	function __construct(pColor $Start, pColor $End)
	{
		$this->StartColor = $Start;
		$this->EndColor = $End;
	}

	private function getOffsets()
	{
		list($eR, $eG, $eB, $eA) = $this->EndColor->get();
		list($sR, $sG, $sB, $sA) = $this->StartColor->get();

		return ["R" => ($eR - $sR), "G" => ($eG - $sG), "B" => ($eB - $sB), "Alpha" => ($eA - $sA)];
	}

	public function findStep()
	{
		$this->Offsets = $this->getOffsets();

		list($oR, $oG, $oB, ) = array_values($this->Offsets);

		return max(abs($oR), abs($oG), abs($oB), 1);
	}

	public function setSegments(int $Segments)
	{
		if (is_null($this->Offsets)){
			$this->Offsets = $this->getOffsets();
		}
		$this->Segments = $Segments;
	}

	public function moveNext()
	{
		$this->StartColor->Slide($this->Offsets, 1/$this->Segments);
	}

	public function getStep(float $j = 1)
	{
		return $this->StartColor->newOne()->Slide($this->Offsets, abs($j)/$this->Segments);
	}

	public function isGradient()
	{
		return ($this->StartColor != $this->EndColor);
	}

	public function getLatest()
	{
		return $this->StartColor;
	}
}
