<?php
/*
pStock - class to draw stock charts

Version     : 2.1.4
Made by     : Jean-Damien POGOLOTTI
Last Update : 19/01/2014

This file can be distributed under the license you can find at :

http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

define("STOCK_MISSING_SERIE", 180001);

/* pStock class definition */
class pStock
{
	var $pChartObject;
	var $pDataObject;
	
	/* Class creator */
	function __construct($pChartObject, $pDataObject)
	{
		$this->pChartObject = $pChartObject;
		$this->pDataObject = $pDataObject;
	}

	/* Draw a stock chart */
	function drawStockChart(array $Format = [])
	{
		$SerieOpen = "Open";
		$SerieClose = "Close";
		$SerieMin = "Min";
		$SerieMax = "Max";
		$SerieMedian = NULL;
		$LineWidth = 1;
		$LineR = 0;
		$LineG = 0;
		$LineB = 0;
		$LineAlpha = 100;
		$ExtremityWidth = 1;
		$ExtremityLength = 3;
		$ExtremityR = 0;
		$ExtremityG = 0;
		$ExtremityB = 0;
		$ExtremityAlpha = 100;
		$BoxWidth = 8;
		$BoxUpR = isset($Format["BoxUpR"]) ? $Format["BoxUpR"] : 188;
		$BoxUpG = isset($Format["BoxUpG"]) ? $Format["BoxUpG"] : 224;
		$BoxUpB = isset($Format["BoxUpB"]) ? $Format["BoxUpB"] : 46;
		$BoxUpAlpha = 100;
		$BoxUpSurrounding = NULL;
		$BoxUpBorderR = $BoxUpR - 20;
		$BoxUpBorderG = $BoxUpG - 20;
		$BoxUpBorderB = $BoxUpB - 20;
		$BoxUpBorderAlpha = 100;
		$BoxDownR = isset($Format["BoxDownR"]) ? $Format["BoxDownR"] : 224;
		$BoxDownG = isset($Format["BoxDownG"]) ? $Format["BoxDownG"] : 100;
		$BoxDownB = isset($Format["BoxDownB"]) ? $Format["BoxDownB"] : 46;
		$BoxDownAlpha = 100;
		$BoxDownSurrounding = NULL;
		$BoxDownBorderR = $BoxDownR - 20;
		$BoxDownBorderG = $BoxDownG - 20;
		$BoxDownBorderB = $BoxDownB - 20;
		$BoxDownBorderAlpha = 100;
		$ShadowOnBoxesOnly = TRUE;
		$MedianR = 255;
		$MedianG = 0;
		$MedianB = 0;
		$MedianAlpha = 100;
		$RecordImageMap = FALSE;
		$ImageMapTitle = "Stock Chart";
		
		/* Override defaults */
		extract($Format);
		
		/* Data Processing */
		$Data = $this->pDataObject->getData();
		$Palette = $this->pDataObject->getPalette();
		
		if ($BoxUpSurrounding != NULL) {
			$BoxUpBorderR = $BoxUpR + $BoxUpSurrounding;
			$BoxUpBorderG = $BoxUpG + $BoxUpSurrounding;
			$BoxUpBorderB = $BoxUpB + $BoxUpSurrounding;
		}

		if ($BoxDownSurrounding != NULL) {
			$BoxDownBorderR = $BoxDownR + $BoxDownSurrounding;
			$BoxDownBorderG = $BoxDownG + $BoxDownSurrounding;
			$BoxDownBorderB = $BoxDownB + $BoxDownSurrounding;
		}

		if ($LineWidth != 1) {
			$LineOffset = $LineWidth / 2;
		}

		$BoxOffset = $BoxWidth / 2;
		$Data = $this->pChartObject->DataSet->getData();
		list($XMargin, $XDivs) = $this->pChartObject->scaleGetXSettings();
		if (!isset($Data["Series"][$SerieOpen]) || !isset($Data["Series"][$SerieClose]) || !isset($Data["Series"][$SerieMin]) || !isset($Data["Series"][$SerieMax])) {
			return (STOCK_MISSING_SERIE);
		}

		$Plots = [];
		foreach($Data["Series"][$SerieOpen]["Data"] as $Key => $Value) {
			$Point = [];
			if (isset($Data["Series"][$SerieClose]["Data"][$Key]) || isset($Data["Series"][$SerieMin]["Data"][$Key]) || isset($Data["Series"][$SerieMax]["Data"][$Key])) {
				$Point = array($Value,$Data["Series"][$SerieClose]["Data"][$Key],$Data["Series"][$SerieMin]["Data"][$Key],$Data["Series"][$SerieMax]["Data"][$Key]);
			}

			if ($SerieMedian != NULL && isset($Data["Series"][$SerieMedian]["Data"][$Key])) {
				$Point[] = $Data["Series"][$SerieMedian]["Data"][$Key];
			}

			$Plots[] = $Point;
		}

		$AxisID = $Data["Series"][$SerieOpen]["Axis"];
		$Mode = $Data["Axis"][$AxisID]["Display"];
		$Format = $Data["Axis"][$AxisID]["Format"];
		$Unit = $Data["Axis"][$AxisID]["Unit"];
		$YZero = $this->pChartObject->scaleComputeY(0, ["AxisID" => $AxisID]);
		$XStep = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $XMargin * 2) / $XDivs;
		$X = $this->pChartObject->GraphAreaX1 + $XMargin;
		$Y = $this->pChartObject->GraphAreaY1 + $XMargin;
		$LineSettings = ["R" => $LineR,"G" => $LineG,"B" => $LineB,"Alpha" => $LineAlpha];
		$ExtremitySettings = ["R" => $ExtremityR,"G" => $ExtremityG,"B" => $ExtremityB,	"Alpha" => $ExtremityAlpha];
		$BoxUpSettings = ["R" => $BoxUpR,"G" => $BoxUpG,"B" => $BoxUpB,"Alpha" => $BoxUpAlpha,"BorderR" => $BoxUpBorderR,"BorderG" => $BoxUpBorderG,"BorderB" => $BoxUpBorderB,"BorderAlpha" => $BoxUpBorderAlpha];
		$BoxDownSettings = ["R" => $BoxDownR,"G" => $BoxDownG,"B" => $BoxDownB,"Alpha" => $BoxDownAlpha,"BorderR" => $BoxDownBorderR,"BorderG" => $BoxDownBorderG,"BorderB" => $BoxDownBorderB,"BorderAlpha" => $BoxDownBorderAlpha];
		$MedianSettings = ["R" => $MedianR,"G" => $MedianG,"B" => $MedianB,"Alpha" => $MedianAlpha];
		
		foreach($Plots as $Key => $Points) {
			
			$PosArray = $this->pChartObject->scaleComputeY($Points, ["AxisID" => $AxisID]);
			$Values = "Open :" . $Data["Series"][$SerieOpen]["Data"][$Key] . "<br />Close : " . $Data["Series"][$SerieClose]["Data"][$Key] . "<br />Min : " . $Data["Series"][$SerieMin]["Data"][$Key] . "<br />Max : " . $Data["Series"][$SerieMax]["Data"][$Key] . "<br />";
			
			if ($SerieMedian != NULL) {
				$Values = $Values . "Median : " . $Data["Series"][$SerieMedian]["Data"][$Key] . "<br />";
			}

			if ($PosArray[0] > $PosArray[1]) {
				$ImageMapColor = $this->pChartObject->toHTMLColor($BoxUpR, $BoxUpG, $BoxUpB);
			} else {
				$ImageMapColor = $this->pChartObject->toHTMLColor($BoxDownR, $BoxDownG, $BoxDownB);
			}

			if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
				if ($YZero > $this->pChartObject->GraphAreaY2 - 1) {
					$YZero = $this->pChartObject->GraphAreaY2 - 1;
				}

				if ($YZero < $this->pChartObject->GraphAreaY1 + 1) {
					$YZero = $this->pChartObject->GraphAreaY1 + 1;
				}

				if ($XDivs == 0) {
					$XStep = 0;
				} else {
					$XStep = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $XMargin * 2) / $XDivs;
				}

				if ($ShadowOnBoxesOnly) {
					$RestoreShadow = $this->pChartObject->Shadow;
					$this->pChartObject->Shadow = FALSE;
				}

				if ($LineWidth == 1) {
					$this->pChartObject->drawLine($X, $PosArray[2], $X, $PosArray[3], $LineSettings);
				} else {
					$this->pChartObject->drawFilledRectangle($X - $LineOffset, $PosArray[2], $X + $LineOffset, $PosArray[3], $LineSettings);
				}

				if ($ExtremityWidth == 1) {
					$this->pChartObject->drawLine($X - $ExtremityLength, $PosArray[2], $X + $ExtremityLength, $PosArray[2], $ExtremitySettings);
					$this->pChartObject->drawLine($X - $ExtremityLength, $PosArray[3], $X + $ExtremityLength, $PosArray[3], $ExtremitySettings);
					if ($RecordImageMap) {
						$this->pChartObject->addToImageMap("RECT", floor($X - $ExtremityLength) . "," . floor($PosArray[2]) . "," . floor($X + $ExtremityLength) . "," . floor($PosArray[3]) , $ImageMapColor, $ImageMapTitle, $Values);
					}
				} else {
					$this->pChartObject->drawFilledRectangle($X - $ExtremityLength, $PosArray[2], $X + $ExtremityLength, $PosArray[2] - $ExtremityWidth, $ExtremitySettings);
					$this->pChartObject->drawFilledRectangle($X - $ExtremityLength, $PosArray[3], $X + $ExtremityLength, $PosArray[3] + $ExtremityWidth, $ExtremitySettings);
					if ($RecordImageMap) {
						$this->pChartObject->addToImageMap("RECT", floor($X - $ExtremityLength) . "," . floor($PosArray[2] - $ExtremityWidth) . "," . floor($X + $ExtremityLength) . "," . floor($PosArray[3] + $ExtremityWidth) , $ImageMapColor, $ImageMapTitle, $Values);
					}
				}

				if ($ShadowOnBoxesOnly) {
					$this->pChartObject->Shadow = $RestoreShadow;
				}

				if ($PosArray[0] > $PosArray[1]) {
					$this->pChartObject->drawFilledRectangle($X - $BoxOffset, $PosArray[0], $X + $BoxOffset, $PosArray[1], $BoxUpSettings);
				} else {
					$this->pChartObject->drawFilledRectangle($X - $BoxOffset, $PosArray[0], $X + $BoxOffset, $PosArray[1], $BoxDownSettings);
				}

				if (isset($PosArray[4])) {
					$this->pChartObject->drawLine($X - $ExtremityLength, $PosArray[4], $X + $ExtremityLength, $PosArray[4], $MedianSettings);
				}

				$X = $X + $XStep;
				
			} elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
				
				if ($YZero > $this->pChartObject->GraphAreaX2 - 1) {
					$YZero = $this->pChartObject->GraphAreaX2 - 1;
				}

				if ($YZero < $this->pChartObject->GraphAreaX1 + 1) {
					$YZero = $this->pChartObject->GraphAreaX1 + 1;
				}

				if ($XDivs == 0) {
					$XStep = 0;
				} else {
					$XStep = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1 - $XMargin * 2) / $XDivs;
				}

				if ($LineWidth == 1) {
					$this->pChartObject->drawLine($PosArray[2], $Y, $PosArray[3], $Y, $LineSettings);
				} else {
					$this->pChartObject->drawFilledRectangle($PosArray[2], $Y - $LineOffset, $PosArray[3], $Y + $LineOffset, $LineSettings);
				}

				if ($ShadowOnBoxesOnly) {
					$RestoreShadow = $this->pChartObject->Shadow;
					$this->pChartObject->Shadow = FALSE;
				}

				if ($ExtremityWidth == 1) {
					$this->pChartObject->drawLine($PosArray[2], $Y - $ExtremityLength, $PosArray[2], $Y + $ExtremityLength, $ExtremitySettings);
					$this->pChartObject->drawLine($PosArray[3], $Y - $ExtremityLength, $PosArray[3], $Y + $ExtremityLength, $ExtremitySettings);
					if ($RecordImageMap) {
						$this->pChartObject->addToImageMap("RECT", floor($PosArray[2]) . "," . floor($Y - $ExtremityLength) . "," . floor($PosArray[3]) . "," . floor($Y + $ExtremityLength) , $ImageMapColor, $ImageMapTitle, $Values);
					}
				} else {
					$this->pChartObject->drawFilledRectangle($PosArray[2], $Y - $ExtremityLength, $PosArray[2] - $ExtremityWidth, $Y + $ExtremityLength, $ExtremitySettings);
					$this->pChartObject->drawFilledRectangle($PosArray[3], $Y - $ExtremityLength, $PosArray[3] + $ExtremityWidth, $Y + $ExtremityLength, $ExtremitySettings);
					if ($RecordImageMap) {
						$this->pChartObject->addToImageMap("RECT", floor($PosArray[2] - $ExtremityWidth) . "," . floor($Y - $ExtremityLength) . "," . floor($PosArray[3] + $ExtremityWidth) . "," . floor($Y + $ExtremityLength) , $ImageMapColor, $ImageMapTitle, $Values);
					}
				}

				if ($ShadowOnBoxesOnly) {
					$this->pChartObject->Shadow = $RestoreShadow;
				}

				if ($PosArray[0] < $PosArray[1]) {
					$this->pChartObject->drawFilledRectangle($PosArray[0], $Y - $BoxOffset, $PosArray[1], $Y + $BoxOffset, $BoxUpSettings);
				} else {
					$this->pChartObject->drawFilledRectangle($PosArray[0], $Y - $BoxOffset, $PosArray[1], $Y + $BoxOffset, $BoxDownSettings);
				}

				if (isset($PosArray[4])) {
					$this->pChartObject->drawLine($PosArray[4], $Y - $ExtremityLength, $PosArray[4], $Y + $ExtremityLength, $MedianSettings);
				}

				$Y = $Y + $XStep;
			}
		}
	}
}

?>