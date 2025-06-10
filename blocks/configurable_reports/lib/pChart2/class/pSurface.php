<?php
/*
pSurface - class to draw surface charts

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

define("UNKNOWN", 0.123456789);
define("IGNORED", -1);
define("LABEL_POSITION_LEFT", 880001);
define("LABEL_POSITION_RIGHT", 880002);
define("LABEL_POSITION_TOP", 880003);
define("LABEL_POSITION_BOTTOM", 880004);

/* pStock class definition */
class pSurface
{
	private $GridSizeX;
	private $GridSizeY;
	private $Points = [];
	private $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Define the grid size and initialize the 2D matrix */
	public function setGrid(int $XSize = 10, int $YSize = 10)
	{
		$this->Points = array_fill(0, $XSize + 1, array_fill(0, $YSize + 1, UNKNOWN));

		$this->GridSizeX = $XSize;
		$this->GridSizeY = $YSize;
	}

	/* Add a point on the grid */
	public function addPoint(int $X, int $Y, $Value, $Force = TRUE)
	{
		if ($X < 0 || $X > $this->GridSizeX) {
			throw pException::SurfaceInvalidInputException("Point out of range");
		}

		if ($Y < 0 || $Y > $this->GridSizeY) {
			throw pException::SurfaceInvalidInputException("Point out of range");
		}

		if ($Force) {
			$this->Points[$X][$Y] = $Value;
		} elseif ($this->Points[$X][$Y] == UNKNOWN) {
			$this->Points[$X][$Y] = $Value;
		} else {
			$this->Points[$X][$Y] = ($this->Points[$X][$Y] + $Value) / 2;
		}
	}

	/* Write the X labels */
	public function writeXLabels(array $Format = [])
	{
		$fontProperties = $this->myPicture->getFont();

		$Color = $fontProperties['Color'];
		$Angle = 0;
		$Padding = 5;
		$Position = LABEL_POSITION_TOP;
		$Labels = [];
		$CountOffset = 0;

		/* Override defaults */
		extract($Format);

		list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		$X0 = $GraphAreaCoordinates["L"];
		$XSize = $Xdiff / ($this->GridSizeX + 1);
		$Settings = ["Angle" => $Angle,"Color" => $Color];
		if ($Position == LABEL_POSITION_TOP) {
			$YPos = $GraphAreaCoordinates["T"] - $Padding;
			$Settings["Align"] = ($Angle == 0) ? TEXT_ALIGN_BOTTOMMIDDLE : TEXT_ALIGN_MIDDLELEFT;
		} elseif ($Position == LABEL_POSITION_BOTTOM) {
			$YPos = $GraphAreaCoordinates["B"] + $Padding;
			$Settings["Align"] = ($Angle == 0) ? TEXT_ALIGN_TOPMIDDLE : TEXT_ALIGN_MIDDLERIGHT;
		} else {
			throw pException::SurfaceInvalidInputException("Invalid label position");
		}

		for ($X = 0; $X <= $this->GridSizeX; $X++) {
			$XPos = floor($X0 + $X * $XSize + $XSize / 2);
			$Value = (empty($Labels) || !isset($Labels[$X])) ? $X + $CountOffset : $Labels[$X];
			$this->myPicture->drawText($XPos, $YPos, $Value, $Settings);
		}
	}

	/* Write the Y labels */
	public function writeYLabels(array $Format = [])
	{
		$fontProperties = $this->myPicture->getFont();

		$Color = isset($Format["Color"]) ? $Format["Color"] : $fontProperties['Color'];
		$Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
		$Padding = isset($Format["Padding"]) ? $Format["Padding"] : 5;
		$Position = isset($Format["Position"]) ? $Format["Position"] : LABEL_POSITION_LEFT;
		$Labels = isset($Format["Labels"]) ? $Format["Labels"] : [];
		$CountOffset = isset($Format["CountOffset"]) ? $Format["CountOffset"] : 0;

		list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		$Y0 = $GraphAreaCoordinates["T"];
		$YSize = $Ydiff / ($this->GridSizeY + 1);
		$Settings = ["Angle" => $Angle,"Color" => $Color];

		if ($Position == LABEL_POSITION_LEFT) {
			$XPos = $GraphAreaCoordinates["L"] - $Padding;
			$Settings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
		} elseif ($Position == LABEL_POSITION_RIGHT) {
			$XPos = $GraphAreaCoordinates["R"] + $Padding;
			$Settings["Align"] = TEXT_ALIGN_MIDDLELEFT;
		} else {
			throw pException::SurfaceInvalidInputException("Invalid label position");
		}

		for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
			$YPos = floor($Y0 + $Y * $YSize + $YSize / 2);
			$Value = (empty($Labels) || !isset($Labels[$Y])) ? $Y + $CountOffset : $Labels[$Y];
			$this->myPicture->drawText($XPos, $YPos, $Value, $Settings);
		}
	}

	/* Draw the area around the specified Threshold */
	public function drawContour(int $Threshold, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 3;
		$Padding = isset($Format["Padding"]) ? $Format["Padding"] : 0;

		list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		$X0 = $GraphAreaCoordinates["L"];
		$Y0 = $GraphAreaCoordinates["T"];
		$XSize = $Xdiff / ($this->GridSizeX + 1);
		$YSize = $Ydiff / ($this->GridSizeY + 1);
		$Settings = ["Color" => $Color,"Ticks" => $Ticks];
		for ($X = 0; $X <= $this->GridSizeX; $X++) {
			for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
				$Value = $this->Points[$X][$Y];
				if ($Value != UNKNOWN && $Value != IGNORED && $Value >= $Threshold) {
					$X1 = floor($X0 + $X * $XSize) + $Padding;
					$Y1 = floor($Y0 + $Y * $YSize) + $Padding;
					$X2 = floor($X0 + $X * $XSize + $XSize);
					$Y2 = floor($Y0 + $Y * $YSize + $YSize);
					if ($X > 0 && $this->Points[$X - 1][$Y] != UNKNOWN && $this->Points[$X - 1][$Y] != IGNORED && $this->Points[$X - 1][$Y] < $Threshold){
						$this->myPicture->drawLine($X1, $Y1, $X1, $Y2, $Settings);
					}
					if ($Y > 0 && $this->Points[$X][$Y - 1] != UNKNOWN && $this->Points[$X][$Y - 1] != IGNORED && $this->Points[$X][$Y - 1] < $Threshold){
						$this->myPicture->drawLine($X1, $Y1, $X2, $Y1, $Settings);
					}
					if ($X < $this->GridSizeX && $this->Points[$X + 1][$Y] != UNKNOWN && $this->Points[$X + 1][$Y] != IGNORED && $this->Points[$X + 1][$Y] < $Threshold){
						$this->myPicture->drawLine($X2, $Y1, $X2, $Y2, $Settings);
					}
					if ($Y < $this->GridSizeY && $this->Points[$X][$Y + 1] != UNKNOWN && $this->Points[$X][$Y + 1] != IGNORED && $this->Points[$X][$Y + 1] < $Threshold){
						$this->myPicture->drawLine($X1, $Y2, $X2, $Y2, $Settings);
					}
				}
			}
		}
	}

	/* Draw the surface chart */
	public function drawSurface(array $Format = [])
	{
		$Palette = isset($Format["Palette"]) ? $Format["Palette"] : [];
		$ShadeColor1 = isset($Format["ShadeColor1"]) ? $Format["ShadeColor1"] : new pColor(77,205,21,40);
		$ShadeColor2 = isset($Format["ShadeColor2"]) ? $Format["ShadeColor2"] : new pColor(227,135,61,100);
		$Border = isset($Format["Border"]) ? $Format["Border"] : FALSE;
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : new pColor(0);
		$Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : NULL;
		$Padding = isset($Format["Padding"]) ? $Format["Padding"] : 1;

		list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		$X0 = $GraphAreaCoordinates["L"];
		$Y0 = $GraphAreaCoordinates["T"];
		$XSize = $Xdiff / ($this->GridSizeX + 1);
		$YSize = $Ydiff / ($this->GridSizeY + 1);

		$Gradient = new pColorGradient($ShadeColor1->newOne(), $ShadeColor2->newOne());
		$Gradient->setSegments(100);

		for ($X = 0; $X <= $this->GridSizeX; $X++) {
			for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
				$Value = $this->Points[$X][$Y];
				if ($Value != UNKNOWN && $Value != IGNORED) {

					if (!empty($Palette)) {
						$Settings = ["Color" => (isset($Palette[$Value])) ? $Palette[$Value] : new pColor(0)];
					} else {
						$Settings = ["Color" => $Gradient->getStep($Value)];
					}

					($Border) AND $Settings["BorderColor"] = $BorderColor;
					(!is_null($Surrounding)) AND $Settings["BorderColor"] = $Settings["Color"]->newOne()->RGBChange($Surrounding);

					$this->myPicture->drawFilledRectangle(
						floor($X0 + $X * $XSize) + $Padding,
						floor($Y0 + $Y * $YSize) + $Padding, 
						floor($X0 + $X * $XSize + $XSize) - 1,
						floor($Y0 + $Y * $YSize + $YSize) - 1,
						$Settings
					);
				}
			}
		}
	}

	/* Compute the missing points */
	public function computeMissing()
	{
		$Missing = [];
		for ($X = 0; $X <= $this->GridSizeX; $X++) {
			for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
				if ($this->Points[$X][$Y] == UNKNOWN) {
					$Missing[] = [$X, $Y];
				}
			}
		}

		shuffle($Missing);
		foreach($Missing as $Pos) {
			$X = $Pos[0];
			$Y = $Pos[1];
			if ($this->Points[$X][$Y] == UNKNOWN) {
				$NearestNeighbor = $this->getNearestNeighbor($X, $Y);
				$Value = 0;
				$Points = 0;
				for ($Xi = $X - $NearestNeighbor; $Xi <= $X + $NearestNeighbor; $Xi++) {
					for ($Yi = $Y - $NearestNeighbor; $Yi <= $Y + $NearestNeighbor; $Yi++) {
						if ($Xi >= 0 && $Yi >= 0 && $Xi <= $this->GridSizeX && $Yi <= $this->GridSizeY && $this->Points[$Xi][$Yi] != UNKNOWN && $this->Points[$Xi][$Yi] != IGNORED) {
							$Value = $Value + $this->Points[$Xi][$Yi];
							$Points++;
						}
					}
				}

				if ($Points != 0) {
					$this->Points[$X][$Y] = $Value / $Points;
				}
			}
		}
	}

	/* Return the nearest Neighbor distance of a point */
	private function getNearestNeighbor($Xp, $Yp)
	{
		$Nearest = UNKNOWN;
		for ($X = 0; $X <= $this->GridSizeX; $X++) {
			for ($Y = 0; $Y <= $this->GridSizeY; $Y++) {
				if ($this->Points[$X][$Y] != UNKNOWN && $this->Points[$X][$Y] != IGNORED) {
					$DistanceX = max($Xp, $X) - min($Xp, $X);
					$DistanceY = max($Yp, $Y) - min($Yp, $Y);
					$Distance = max($DistanceX, $DistanceY);
					if ($Distance < $Nearest || $Nearest == UNKNOWN) {
						$Nearest = $Distance;
					}
				}
			}
		}

		return $Nearest;
	}
}
