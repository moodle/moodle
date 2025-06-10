<?php
/*
pStock - class to draw stock charts

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

/* pStock class definition */
class pStock
{
	private $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Draw a stock chart */
	public function drawStockChart(array $Format = [])
	{
		$SerieOpen = "Open";
		$SerieClose = "Close";
		$SerieMin = "Min";
		$SerieMax = "Max";
		$SerieMedian = NULL;
		$LineWidth = 1;
		$LineColor = new pColor(0);
		$ExtremityWidth = 1;
		$ExtremityLength = 3;
		$ExtremityColor = new pColor(0);
		$BoxWidth = 8;
		$BoxUpColor = new pColor(188,224,46,100);
		$BoxUpSurrounding = NULL;
		$BoxUpBorderColor = NULL;
		$BoxDownColor = new pColor(224,100,46,100);
		$BoxDownSurrounding = NULL;
		$BoxDownBorderColor = NULL;
		$ShadowOnBoxesOnly = TRUE;
		$MedianColor = new pColor(255,0,0,100);

		/* Override defaults */
		extract($Format);

		(is_null($BoxUpBorderColor)) 	AND $BoxUpBorderColor 	= $BoxUpColor->newOne()->RGBChange(-20);
		(is_null($BoxDownBorderColor)) 	AND $BoxDownBorderColor = $BoxDownColor->newOne()->RGBChange(-20);
		(!is_null($BoxUpSurrounding)) 	AND $BoxUpBorderColor 	= $BoxUpColor->newOne()->RGBChange($BoxUpSurrounding);
		(!is_null($BoxDownSurrounding)) AND $BoxDownBorderColor = $BoxDownColor->newOne()->RGBChange($BoxDownSurrounding);

		$LineSettings 		= ["Color" => $LineColor];
		$ExtremitySettings 	= ["Color" => $ExtremityColor];
		$BoxUpSettings 		= ["Color" => $BoxUpColor,	"BorderColor" => $BoxUpBorderColor];
		$BoxDownSettings 	= ["Color" => $BoxDownColor,"BorderColor" => $BoxDownBorderColor];
		$MedianSettings 	= ["Color" => $MedianColor];

		/* Data Processing */
		$Data = $this->myPicture->myData->getData();
		$Orientation = $Data["Orientation"];
		$Data = $Data["Series"];

		if (!isset($Data[$SerieOpen]) || !isset($Data[$SerieClose]) || !isset($Data[$SerieMin]) || !isset($Data[$SerieMax])) {
			throw pException::StockMissingSerieException();
		}

		($LineWidth != 1) AND $LineOffset = $LineWidth / 2;
		$BoxOffset = $BoxWidth / 2;

		list($XMargin, $XDivs) = $this->myPicture->myData->scaleGetXSettings();

		if ($XDivs == 0){
			$XStep = 0;
		} else {
			list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();
			if ($Orientation == SCALE_POS_LEFTRIGHT) {
				$XStep = ($Xdiff - $XMargin * 2) / $XDivs;
			} else {
				$XStep = ($Ydiff - $XMargin * 2) / $XDivs;
			}
		}

		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();
		$X = $GraphAreaCoordinates["L"] + $XMargin;
		$Y = $GraphAreaCoordinates["T"] + $XMargin;

		foreach($Data[$SerieOpen]["Data"] as $Key => $Value) {

			if (isset($Data[$SerieClose]["Data"][$Key]) || isset($Data[$SerieMin]["Data"][$Key]) || isset($Data[$SerieMax]["Data"][$Key])) {
				$Points = [$Value, $Data[$SerieClose]["Data"][$Key], $Data[$SerieMin]["Data"][$Key], $Data[$SerieMax]["Data"][$Key]];
			} else {
				$Points = [];
			}

			if (!is_null($SerieMedian) && isset($Data[$SerieMedian]["Data"][$Key])) {
				$Points[] = $Data[$SerieMedian]["Data"][$Key];
			}

			$PosArray = $this->myPicture->scaleComputeY($Points, $Data[$SerieOpen]["Axis"]);

			if ($ShadowOnBoxesOnly) {
				$ShadowSpec = $this->myPicture->getShadow();
				$this->myPicture->setShadow(FALSE);
			}

			if ($Orientation == SCALE_POS_LEFTRIGHT) {

				if ($LineWidth == 1) {
					$this->myPicture->drawLine($X, $PosArray[2], $X, $PosArray[3], $LineSettings);
				} else {
					$this->myPicture->drawFilledRectangle($X - $LineOffset, $PosArray[2], $X + $LineOffset, $PosArray[3], $LineSettings);
				}

				if ($ExtremityWidth == 1) {
					$this->myPicture->drawLine($X - $ExtremityLength, $PosArray[2], $X + $ExtremityLength, $PosArray[2], $ExtremitySettings);
					$this->myPicture->drawLine($X - $ExtremityLength, $PosArray[3], $X + $ExtremityLength, $PosArray[3], $ExtremitySettings);
				} else {
					$this->myPicture->drawFilledRectangle($X - $ExtremityLength, $PosArray[2], $X + $ExtremityLength, $PosArray[2] - $ExtremityWidth, $ExtremitySettings);
					$this->myPicture->drawFilledRectangle($X - $ExtremityLength, $PosArray[3], $X + $ExtremityLength, $PosArray[3] + $ExtremityWidth, $ExtremitySettings);
				}

				($ShadowOnBoxesOnly) AND $this->myPicture->restoreShadow($ShadowSpec);

				$this->myPicture->drawFilledRectangle($X - $BoxOffset, $PosArray[0], $X + $BoxOffset, $PosArray[1], ($PosArray[0] > $PosArray[1]) ? $BoxUpSettings : $BoxDownSettings);

				(isset($PosArray[4])) AND $this->myPicture->drawLine($X - $ExtremityLength, $PosArray[4], $X + $ExtremityLength, $PosArray[4], $MedianSettings);

				$X = $X + $XStep;

			} else { # SCALE_POS_TOPBOTTOM

				if ($LineWidth == 1) {
					$this->myPicture->drawLine($PosArray[2], $Y, $PosArray[3], $Y, $LineSettings);
				} else {
					$this->myPicture->drawFilledRectangle($PosArray[2], $Y - $LineOffset, $PosArray[3], $Y + $LineOffset, $LineSettings);
				}

				if ($ExtremityWidth == 1) {
					$this->myPicture->drawLine($PosArray[2], $Y - $ExtremityLength, $PosArray[2], $Y + $ExtremityLength, $ExtremitySettings);
					$this->myPicture->drawLine($PosArray[3], $Y - $ExtremityLength, $PosArray[3], $Y + $ExtremityLength, $ExtremitySettings);
				} else {
					$this->myPicture->drawFilledRectangle($PosArray[2], $Y - $ExtremityLength, $PosArray[2] - $ExtremityWidth, $Y + $ExtremityLength, $ExtremitySettings);
					$this->myPicture->drawFilledRectangle($PosArray[3], $Y - $ExtremityLength, $PosArray[3] + $ExtremityWidth, $Y + $ExtremityLength, $ExtremitySettings);
				}

				($ShadowOnBoxesOnly) AND $this->myPicture->restoreShadow($ShadowSpec);

				$this->myPicture->drawFilledRectangle($PosArray[0], $Y - $BoxOffset, $PosArray[1], $Y + $BoxOffset, ($PosArray[0] < $PosArray[1]) ? $BoxUpSettings : $BoxDownSettings);

				(isset($PosArray[4])) AND $this->myPicture->drawLine($PosArray[4], $Y - $ExtremityLength, $PosArray[4], $Y + $ExtremityLength, $MedianSettings);

				$Y = $Y + $XStep;
			}
		}
	}
}
