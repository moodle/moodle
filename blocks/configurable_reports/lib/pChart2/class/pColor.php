<?php 
/*
pColor - Data structure for colors

Version     : 2.4.0-dev
Made by     : Momchil Bozhinov
Last Update : 21/07/2021

This file can be distributed under the MIT license

*/

namespace pChart;

class pColor
{
	private $R;
	private $G;
	private $B;
	private $Alpha;

	/* Floats are required for pGradient */
	public function __construct(float $R = 0, float $G = 0, float $B = 0, float $Alpha = 100)
	{
		switch (func_num_args()){
			case 1:
			case 2:
				$this->R = $R;
				$this->G = $R;
				$this->B = $R;
				$this->Alpha = 100;
				$this->validateRGB();
				break;
			case 3:
			case 4:
				$this->R = $R;
				$this->G = $G;
				$this->B = $B;
				$this->Alpha = $Alpha;
				$this->validateRGB();
				break;
			case 0: # random
				$this->R = rand(0, 255);
				$this->G = rand(0, 255);
				$this->B = rand(0, 255);
				$this->Alpha = 100;
		}
	}

	private function validateRGB()
	{
		($this->R < 0) AND $this->R = 0;
		($this->G < 0) AND $this->G = 0;
		($this->B < 0) AND $this->B = 0;
		($this->R > 255) AND $this->R = 255;
		($this->G > 255) AND $this->G = 255;
		($this->B > 255) AND $this->B = 255;
	}

	public function toHex()
	{
		$R = dechex(intval($this->R));
		$G = dechex(intval($this->G));
		$B = dechex(intval($this->B));

		return  "#".(strlen($R) < 2 ? '0' : '').$R.(strlen($G) < 2 ? '0' : '').$G.(strlen($B) < 2 ? '0' : '').$B;
	}

	public function fromHex(string $hex, int $Alpha = 100)
	{
		if ($hex[0] == '#'){
			$hex = substr($hex, 1);
		}

		if (strlen($hex) == 6) {
			list($R, $G, $B) = str_split($hex, 2);
			$this->R = hexdec($R);
			$this->G = hexdec($G);
			$this->B = hexdec($B);
			$this->Alpha = $Alpha;
		} else {
			throw pException::InvalidInput("Hex color is the wrong format - ".$hex);
		}

		return $this;
	}

	public function Slide(array $Offsets, float $Percent)
	{
		$this->R += $Offsets["R"] * $Percent;
		$this->G += $Offsets["G"] * $Percent;
		$this->B += $Offsets["B"] * $Percent;
		$this->AlphaChange($Offsets["Alpha"] * $Percent);

		$this->validateRGB();

		return $this;
	}

	public function RGBChange(float $howmuch)
	{
		$this->R += $howmuch;
		$this->G += $howmuch;
		$this->B += $howmuch;

		$this->validateRGB();

		return $this;
	}

	public function AlphaSet(float $howmuch)
	{
		$this->Alpha = $howmuch;

		return $this;
	}

	public function AlphaChange(float $howmuch)
	{
		$this->Alpha += $howmuch;

		return $this;
	}

	public function AlphaSlash(float $howmuch)
	{
		$this->Alpha = $this->Alpha / $howmuch;

		return $this;
	}

	public function AlphaMultiply(float $howmuch)
	{
		$this->Alpha = $this->Alpha * $howmuch;

		return $this;
	}

	public function AlphaGet()
	{
		return $this->Alpha;
	}

	public function get()
	{
		return [intval($this->R), intval($this->G), intval($this->B), intval($this->Alpha)];
	}

	public function newOne()
	{
		return (clone $this);
	}

}
