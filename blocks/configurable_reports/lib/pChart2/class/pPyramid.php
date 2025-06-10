<?php
/*
pPyramid - class to draw pyramids

Version     : 2.4.0-dev
Made by     : Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the MIT license
*/

namespace pChart;

/* pPyramid class definition */
class pPyramid
{
	private $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	public function drawPyramid($X, $Y, $Base, $Height, $NumSegments = 4, array $Format = [])
	{
		$Color =  isset($Format["Color"])  ? $Format["Color"]  : FALSE;
		$Offset = isset($Format["Offset"]) ? $Format["Offset"] : 5;
		$NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : FALSE;

		$Settings = [
			"Color" => $Color,
			"NoFill" => $NoFill
		];

		$Palette = $this->myPicture->myData->getPalette();

		# Account for the combined heights of the offsets
		$h = ($Height - (($NumSegments - 1) * $Offset)) / $NumSegments;

		for($i=0; $i<$NumSegments; $i++){

			if ($Color == FALSE){
				if (isset($Palette[$i])){
					$Settings["Color"] = $Palette[$i]->newOne();
				} else {
					$Settings["Color"] = new pColor();
				}
			}

			if ($i != 0){
				$Base -= (2 * $h);
			}

			$Xi = $X + ($h * $i);
			$Yi = $Y - ($h * $i);
			$Oi = ($Offset * $i);
			
			$Points = [
					$Xi + $Oi, $Yi - $Oi,
					$Xi - $Oi + $Base, $Yi - $Oi,
					$Xi + $Base - $h - $Oi, $Yi - $h - $Oi,
					$Xi + $Oi + $h, $Yi - $h - $Oi,
					$Xi + $Oi, $Yi - $Oi
				];

			#print_r($Points);
			
			$this->myPicture->drawPolygon($Points, $Settings);
		}

	}

	public function drawReversePyramid($X, $Y, $Base, $Height, $NumSegments = 4, array $Format = [])
	{
		$Color =  isset($Format["Color"])  ? $Format["Color"]  : FALSE;
		$Offset = isset($Format["Offset"]) ? $Format["Offset"] : 5;
		$NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : FALSE;

		$Settings = [
			"Color" => $Color,
			"NoFill" => $NoFill
		];

		# Account for the combined heights of the offsets
		$h = ($Height - (($NumSegments - 1) * $Offset)) / $NumSegments;
		$Y -= $Height;

		$Palette = $this->myPicture->myData->getPalette();

		for($i=0; $i<$NumSegments; $i++){

			if ($Color == FALSE){
				if (isset($Palette[$i])){
					$Settings["Color"] = $Palette[$i]->newOne();
				} else {
					$Settings["Color"] = new pColor();
				}
			}

			if ($i != 0){
				$Base -= (2 * $h);
			}

			$Xi = $X + ($h * $i);
			$Yi = $Y + ($h * $i);
			$Oi = ($Offset * $i);

			$Points = [
					$Xi + $Oi, $Yi + $Oi,
					$Xi - $Oi + $Base, $Yi + $Oi,
					$Xi + $Base - $h - $Oi, $Yi + $h + $Oi,
					$Xi + $Oi + $h, $Yi + $h + $Oi,
					$Xi + $Oi, $Yi + $Oi
				];

			#print_r($Points);
			$this->myPicture->drawPolygon($Points, $Settings);
		}

	}
}
