<?php
/*
pBubble - class to draw bubble charts

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

define("BUBBLE_SHAPE_ROUND", 700001);
define("BUBBLE_SHAPE_SQUARE", 700002);

/* pBubble class definition */
class pBubble
{
	private $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Prepare the scale */
	public function bubbleScale(array $DataSeries, array $WeightSeries)
	{
		/* Parse each data series to find the new min & max boundaries to scale */
		$NewPositiveSerie = [];
		$NewNegativeSerie = [];
		$MaxValues = 0;
		$LastPositive = 0;
		$LastNegative = 0;

		$Data = $this->myPicture->myData->getData();

		foreach($DataSeries as $idx => $SerieName) {
			$SerieWeightName = $WeightSeries[$idx];
			$this->myPicture->myData->setSerieProperties($SerieWeightName,["isDrawable" => FALSE]);
			if (count($Data["Series"][$SerieName]["Data"]) > $MaxValues) {
				$MaxValues = count($Data["Series"][$SerieName]["Data"]);
			}

			foreach($Data["Series"][$SerieName]["Data"] as $Key => $Value) {
				if ($Value >= 0) {
					$BubbleBounds = $Value + $Data["Series"][$SerieWeightName]["Data"][$Key];
					if (!isset($NewPositiveSerie[$Key])) {
						$NewPositiveSerie[$Key] = $BubbleBounds;
					} elseif ($NewPositiveSerie[$Key] < $BubbleBounds) {
						$NewPositiveSerie[$Key] = $BubbleBounds;
					}

					$LastPositive = $BubbleBounds;
				} else {
					$BubbleBounds = $Value - $Data["Series"][$SerieWeightName]["Data"][$Key];
					if (!isset($NewNegativeSerie[$Key])) {
						$NewNegativeSerie[$Key] = $BubbleBounds;
					} elseif ($NewNegativeSerie[$Key] > $BubbleBounds) {
						$NewNegativeSerie[$Key] = $BubbleBounds;
					}

					$LastNegative = $BubbleBounds;
				}
			}
		}

		/* Check for missing values and all the fake positive serie */
		if (!empty($NewPositiveSerie))
		{
			for ($i = 0; $i < $MaxValues; $i++) {
				if (!isset($NewPositiveSerie[$i])) {
					$NewPositiveSerie[$i] = $LastPositive;
				}
			}

			$this->myPicture->myData->addPoints($NewPositiveSerie, "BubbleFakePositiveSerie");
		}

		/* Check for missing values and all the fake negative serie */
		if (!empty($NewNegativeSerie))
		{
			for ($i = 0; $i < $MaxValues; $i++) {
				if (!isset($NewNegativeSerie[$i])) {
					$NewNegativeSerie[$i] = $LastNegative;
				}
			}

			$this->myPicture->myData->addPoints($NewNegativeSerie, "BubbleFakeNegativeSerie");
		}
	}

	/* Prepare the scale */
	public function drawBubbleChart(array $DataSeries, array $WeightSeries, array $Format = [])
	{
		$ForceAlpha = NULL;
		$DrawBorder = TRUE;
		$BorderWidth = 1;
		$Shape = BUBBLE_SHAPE_ROUND;
		$Surrounding = NULL;
		$BorderColor = new pColor(0,0,0,30);

		/* Override defaults */
		extract($Format);

		$Data = $this->myPicture->myData->getData();
		$Palette = $this->myPicture->myData->getPalette();

		$Orientation = $Data["Orientation"];
		$Data = $Data["Series"];

		if (isset($Data["BubbleFakePositiveSerie"])) {
			$this->myPicture->myData->setSerieProperties("BubbleFakePositiveSerie",["isDrawable" => FALSE]);
		}

		if (isset($Data["BubbleFakeNegativeSerie"])) {
			$this->myPicture->myData->setSerieProperties("BubbleFakeNegativeSerie",["isDrawable" => FALSE]);
		}

		list($XMargin, $XDivs) = $this->myPicture->myData->scaleGetXSettings();

		if ($XDivs == 0) {
			$XStep = 0;
		} else {
			list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();
			if ($Orientation == SCALE_POS_LEFTRIGHT) {
				$XStep = ($Xdiff - $XMargin * 2) / $XDivs;
			} elseif ($Orientation == SCALE_POS_TOPBOTTOM) {
				$XStep = ($Ydiff - $XMargin * 2) / $XDivs;
			}
		}

		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		foreach($DataSeries as $Key => $SerieName) {

			$X = $GraphAreaCoordinates["L"] + $XMargin;
			$Y = $GraphAreaCoordinates["T"] + $XMargin;

			$ColorSettings = ["Color" => $Palette[$Key]];

			(!is_null($ForceAlpha)) AND $ColorSettings["Color"]->AlphaSet($ForceAlpha);

			if ($DrawBorder) {
				if ($BorderWidth != 1) {
					(!is_null($Surrounding)) AND $BorderColor = $ColorSettings["Color"]->newOne()->RGBChange($Surrounding);
					(!is_null($ForceAlpha))  AND $BorderColor->AlphaSet($ForceAlpha / 2);
					$BorderColorSettings = ["Color" => $BorderColor];

				} else {
					(!is_null($Surrounding)) AND $BorderColor->RGBChange($Surrounding);
					(!is_null($ForceAlpha))  AND $BorderColor->AlphaSet($ForceAlpha / 2);
					$ColorSettings["BorderColor"] = $BorderColor;
				}
			}

			foreach($Data[$SerieName]["Data"] as $iKey => $Point) {

				$DataWeightSeries = $Data[$WeightSeries[$Key]]["Data"][$iKey];
				$Weight = $this->myPicture->scaleComputeYSingle($Point + $DataWeightSeries, $Data[$SerieName]["Axis"]);
				$Pos = $this->myPicture->scaleComputeYSingle($Point, $Data[$SerieName]["Axis"]);
				$Radius = floor(abs($Pos - $Weight) / 2);

				if ($Orientation == SCALE_POS_LEFTRIGHT) {

					$Y = floor($Pos);
					if ($Shape == BUBBLE_SHAPE_SQUARE) {
						($BorderWidth != 1) AND	$this->myPicture->drawFilledRectangle($X - $Radius - $BorderWidth, $Y - $Radius - $BorderWidth, $X + $Radius + $BorderWidth, $Y + $Radius + $BorderWidth, $BorderColorSettings);
						$this->myPicture->drawFilledRectangle($X - $Radius, $Y - $Radius, $X + $Radius, $Y + $Radius, $ColorSettings);
					} elseif ($Shape == BUBBLE_SHAPE_ROUND) {
						($BorderWidth != 1) AND	$this->myPicture->drawFilledCircle($X, $Y, $Radius + $BorderWidth, $BorderColorSettings);
						$this->myPicture->drawFilledCircle($X, $Y, $Radius, $ColorSettings);
					}

					$X += $XStep;
					
				} elseif ($Orientation == SCALE_POS_TOPBOTTOM) {

					$X = floor($Pos);
					if ($Shape == BUBBLE_SHAPE_SQUARE) {
						($BorderWidth != 1) AND	$this->myPicture->drawFilledRectangle($X - $Radius - $BorderWidth, $Y - $Radius - $BorderWidth, $X + $Radius + $BorderWidth, $Y + $Radius + $BorderWidth, $BorderColorSettings);
						$this->myPicture->drawFilledRectangle($X - $Radius, $Y - $Radius, $X + $Radius, $Y + $Radius, $ColorSettings);
					} elseif ($Shape == BUBBLE_SHAPE_ROUND) {
						($BorderWidth != 1) AND	$this->myPicture->drawFilledCircle($X, $Y, $Radius + $BorderWidth, $BorderColorSettings);
						$this->myPicture->drawFilledCircle($X, $Y, $Radius, $ColorSettings);
					}

					$Y += $XStep;
				}
			}
		}
	}

	public function writeBubbleLabel(string $SerieName, string $SerieWeightName, int $Point, array $Format = [])
	{
		$Data = $this->myPicture->myData->getData();

		if (!isset($Data["Series"][$SerieName]) || !isset($Data["Series"][$SerieWeightName])) {
			throw pException::BubbleInvalidInputException("Serie name or Weight is invalid!");
		}

		list($XMargin, $XDivs) = $this->myPicture->myData->scaleGetXSettings();

		$AxisID = $Data["Series"][$SerieName]["Axis"];
		$Value = $Data["Series"][$SerieName]["Data"][$Point];
		$Pos = $this->myPicture->scaleComputeYSingle($Value, $AxisID);
		$Value = $this->myPicture->scaleFormat($Value, $Data["Axis"][$AxisID]);
		$Description = (isset($Data["Series"][$SerieName]["Description"])) ? $Data["Series"][$SerieName]["Description"] : "No description";
		$Abscissa = (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Point])) ? $Data["Series"][$Data["Abscissa"]]["Data"][$Point]." : " : "";
		$Series = ["Color" => $Data["Series"][$SerieName]["Color"],"Caption" => $Abscissa . $Value . " / " . $Data["Series"][$SerieWeightName]["Data"][$Point]];

		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();
		$X = $GraphAreaCoordinates["L"] + $XMargin;
		$Y = $GraphAreaCoordinates["T"] + $XMargin;

		list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$XStep = ($XDivs == 0) ? 0 : ($Xdiff - $XMargin * 2) / $XDivs;
			$X = floor($X + $Point * $XStep);
			$Y = floor($Pos);
		} else {
			$YStep = ($XDivs == 0) ? 0 : ($Ydiff - $XMargin * 2) / $XDivs;
			$X = floor($Pos);
			$Y = floor($Y + $Point * $YStep);
		}

		$DrawPoint = isset($Format["DrawPoint"]) ? $Format["DrawPoint"] : LABEL_POINT_BOX;
		if ($DrawPoint == LABEL_POINT_CIRCLE) {
			$this->myPicture->drawFilledCircle($X, $Y, 3, ["Color" => new pColor(255), "BorderColor" => new pColor(0)]);
		} elseif ($DrawPoint == LABEL_POINT_BOX) {
			$this->myPicture->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["Color" => new pColor(255), "BorderColor" => new pColor(0)]);
		}

		$this->myPicture->drawLabelBox($X, $Y - 3, $Description, $Series, $Format);
	}
}
