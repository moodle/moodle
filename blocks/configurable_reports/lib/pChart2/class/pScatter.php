<?php
/*
pScatter - class to draw scatter charts

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

/* pScatter class definition */
class pScatter
{
	private $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Prepare the scale */
	public function drawScatterScale(array $Format = [])
	{
		/* Check if we have at least both one X and Y axis */
		$GotXAxis = FALSE;
		$GotYAxis = FALSE;

		$Data = $this->myPicture->myData->getData();

		foreach($Data["Axis"] as $AxisProps) {
			($AxisProps["Identity"] == AXIS_X) AND $GotXAxis = TRUE;
			($AxisProps["Identity"] == AXIS_Y) AND $GotYAxis = TRUE;
		}

		if (!$GotXAxis) {
			throw pException::ScatterInvalidInputException("Missing XAxis!");
		}

		if (!$GotYAxis) {
			throw pException::ScatterInvalidInputException("Missing YAxis!");
		}

		$Mode = SCALE_MODE_FLOATING;
		$Floating = FALSE;
		$XLabelsRotation = 90;
		$MinDivHeight = 20;
		$Factors = [1,2,5];
		$ManualScale = array("0" => ["Min" => - 100,"Max" => 100]);
		$XMargin = 0;
		$YMargin = 0;
		$ScaleSpacing = 15;
		$InnerTickWidth = 2;
		$OuterTickWidth = 2;
		$DrawXLines = [ALL];
		$DrawYLines = [ALL];
		$GridTicks = 4;
		$GridColor = new pColor(255,255,255,40);
		$AxisoColor = isset($Format["AxisColor"]) ? $Format["AxisColor"] : new pColor(0);
		$TickoColor = isset($Format["TickColor"]) ? $Format["TickColor"] : new pColor(0);
		$DrawSubTicks = FALSE;
		$InnerSubTickWidth = 0;
		$OuterSubTickWidth = 2;
		$SubTickColor = new pColor(255,0,0,100);
		$XReleasePercent = 1;
		$DrawArrows = FALSE;
		$ArrowSize = 8;
		$CycleBackground = FALSE;
		$BackgroundColor1 = new pColor(255,255,255,10);
		$BackgroundColor2 = new pColor(230,230,230,10);

		/* Override defaults */
		extract($Format);

		($DrawYLines == NONE || $DrawYLines == [NONE]) AND $DrawYLines = [];
		($DrawYLines == ALL) AND $DrawYLines = [ALL];

		list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();
		foreach($Data["Axis"] as $AxisID => $AxisProps) {
			if ($AxisProps["Identity"] == AXIS_X) {
				$Width = $Xdiff - $XMargin * 2;
			} else {
				$Width = $Ydiff - $YMargin * 2;
			}

			$AxisMin = PHP_INT_MAX;
			$AxisMax = OUT_OF_SIGHT;
			if ($Mode == SCALE_MODE_FLOATING) {
				foreach($Data["Series"] as $SerieID => $SerieParameter) {
					if ($SerieParameter["Axis"] == $AxisID && $Data["Series"][$SerieID]["isDrawable"]) {
						$AxisMax = max($AxisMax, $Data["Series"][$SerieID]["Max"]);
						$AxisMin = min($AxisMin, $Data["Series"][$SerieID]["Min"]);
					}
				}

				$AutoMargin = (($AxisMax - $AxisMin) / 100) * $XReleasePercent;
				$Data["Axis"][$AxisID]["Min"] = $AxisMin - $AutoMargin;
				$Data["Axis"][$AxisID]["Max"] = $AxisMax + $AutoMargin;
			} elseif ($Mode == SCALE_MODE_MANUAL) {
				if (isset($ManualScale[$AxisID]["Min"]) && isset($ManualScale[$AxisID]["Max"])) {
					$Data["Axis"][$AxisID]["Min"] = $ManualScale[$AxisID]["Min"];
					$Data["Axis"][$AxisID]["Max"] = $ManualScale[$AxisID]["Max"];
				} else {
					throw pException::ScatterInvalidInputException("Manual scale boundaries not set");
				}
			}

			/* Full manual scale */
			if (isset($ManualScale[$AxisID]["Rows"]) && isset($ManualScale[$AxisID]["RowHeight"])) {
				$Scale = ["Rows" => $ManualScale[$AxisID]["Rows"],"RowHeight" => $ManualScale[$AxisID]["RowHeight"],"XMin" => $ManualScale[$AxisID]["Min"],"XMax" => $ManualScale[$AxisID]["Max"]];
			} else {
				$MaxDivs = floor($Width / $MinDivHeight);
				$Scale = $this->myPicture->computeScale($Data["Axis"][$AxisID]["Min"], $Data["Axis"][$AxisID]["Max"], $MaxDivs, $Factors, $AxisID);
			}

			$Data["Axis"][$AxisID]["Margin"] = $AxisProps["Identity"] == AXIS_X ? $XMargin : $YMargin;
			$Data["Axis"][$AxisID]["ScaleMin"] = $Scale["XMin"];
			$Data["Axis"][$AxisID]["ScaleMax"] = $Scale["XMax"];
			$Data["Axis"][$AxisID]["Rows"] = $Scale["Rows"];
			$Data["Axis"][$AxisID]["RowHeight"] = $Scale["RowHeight"];
			(isset($Scale["Format"])) AND $Data["Axis"][$AxisID]["Format"] = $Scale["Format"];
		}

		/* Set the original boundaries */
		$AxisPos = $this->myPicture->getGraphAreaCoordinates();
		$StartPos = $AxisPos;

		foreach($Data["Axis"] as $AxisID => $AxisProps) {
			if (isset($AxisProps["Color"])) {
				$AxisColor = $AxisProps["Color"];
				$TickColor = $AxisProps["Color"];
				$this->myPicture->setFontProperties(["Color" => $AxisColor]);
			} else {
				$AxisColor = $AxisoColor;
				$TickColor = $TickoColor;
				/* Get the default font color */
				$fontProperties = $this->myPicture->getFont();
				$this->myPicture->setFontProperties(["Color" => $fontProperties['Color']]);
			}

			if ($AxisProps["Identity"] == AXIS_X) {
				if ($AxisProps["Position"] == AXIS_POSITION_BOTTOM) {
					if ($XLabelsRotation == 0) {
						$LabelAlign = TEXT_ALIGN_TOPMIDDLE;
						$LabelOffset = 2;
					}

					if ($XLabelsRotation > 0 && $XLabelsRotation < 190) {
						$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
						$LabelOffset = 5;
					}

					if ($XLabelsRotation == 180) {
						$LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
						$LabelOffset = 5;
					}

					if ($XLabelsRotation > 180 && $XLabelsRotation < 360) {
						$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
						$LabelOffset = 2;
					}

					if ($Floating) {
						$FloatingOffset = $YMargin;
						$this->myPicture->drawLine($StartPos["L"] + $AxisProps["Margin"], $AxisPos["B"], $StartPos["R"] - $AxisProps["Margin"], $AxisPos["B"], ["Color" => $AxisColor]);
					} else {
						$FloatingOffset = 0;
						$this->myPicture->drawLine($StartPos["L"], $AxisPos["B"], $StartPos["R"], $AxisPos["B"], ["Color" => $AxisColor]);
					}

					if ($DrawArrows) {
						$this->myPicture->drawArrow($StartPos["R"] - $AxisProps["Margin"], $AxisPos["B"], $StartPos["R"] + ($ArrowSize * 2), $AxisPos["B"], ["FillColor" => $AxisColor,"Size" => $ArrowSize]);
					}

					$Width = $Xdiff - $AxisProps["Margin"] * 2;
					$Step = $Width / $AxisProps["Rows"];
					$SubTicksSize = $Step / 2;
					$MaxBottom = $AxisPos["B"];
					$LastX = NULL;
					for ($i = 0; $i <= $AxisProps["Rows"]; $i++) {
						$XPos = $StartPos["L"] + $AxisProps["Margin"] + $Step * $i;
						$YPos = $AxisPos["B"];
						$Value = $this->myPicture->scaleFormat($AxisProps["ScaleMin"] + $AxisProps["RowHeight"] * $i, $AxisProps);

						if (!is_null($LastX) && $CycleBackground && ($DrawXLines == [ALL] || in_array($AxisID, $DrawXLines))) {
							$this->myPicture->drawFilledRectangle($LastX, $StartPos["T"] + $FloatingOffset, $XPos, $StartPos["B"] - $FloatingOffset, ["Color" => ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2]);
						}

						if ($DrawXLines == [ALL] || in_array($AxisID, $DrawXLines)) {
							$this->myPicture->drawLine($XPos, $StartPos["T"] + $FloatingOffset, $XPos, $StartPos["B"] - $FloatingOffset, ["Color" => $GridColor,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisProps["Rows"]){
							$this->myPicture->drawLine($XPos + $SubTicksSize, $YPos - $InnerSubTickWidth, $XPos + $SubTicksSize, $YPos + $OuterSubTickWidth, ["Color" => $SubTickColor]);
						}

						$this->myPicture->drawLine($XPos, $YPos - $InnerTickWidth, $XPos, $YPos + $OuterTickWidth, ["Color" => $TickColor]);
						$Bounds = $this->myPicture->drawText($XPos, $YPos + $OuterTickWidth + $LabelOffset, $Value, ["Angle" => $XLabelsRotation,"Align" => $LabelAlign]);
						$TxtBottom = $YPos + 2 + $OuterTickWidth + 2 + ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
						$MaxBottom = max($MaxBottom, $TxtBottom);
						$LastX = $XPos;
					}

					if (isset($AxisProps["Name"])) {
						$YPos = $MaxBottom + 2;
						$XPos = $StartPos["L"] + $Xdiff / 2;
						$Bounds = $this->myPicture->drawText($XPos, $YPos, $AxisProps["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]);
						$MaxBottom = $Bounds[0]["Y"];
					}

					$AxisPos["B"] = $MaxBottom + $ScaleSpacing;

				} elseif ($AxisProps["Position"] == AXIS_POSITION_TOP) {

					if ($XLabelsRotation == 0) {
						$LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
						$LabelOffset = 2;
					}

					if ($XLabelsRotation > 0 && $XLabelsRotation < 190) {
						$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
						$LabelOffset = 2;
					}

					if ($XLabelsRotation == 180) {
						$LabelAlign = TEXT_ALIGN_TOPMIDDLE;
						$LabelOffset = 5;
					}

					if ($XLabelsRotation > 180 && $XLabelsRotation < 360) {
						$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
						$LabelOffset = 5;
					}

					if ($Floating) {
						$FloatingOffset = $YMargin;
						$this->myPicture->drawLine($StartPos["L"] + $AxisProps["Margin"], $AxisPos["T"], $StartPos["R"] - $AxisProps["Margin"], $AxisPos["T"], ["Color" => $AxisColor]);
					} else {
						$FloatingOffset = 0;
						$this->myPicture->drawLine($StartPos["L"], $AxisPos["T"], $StartPos["R"], $AxisPos["T"], ["Color" => $AxisColor]);
					}

					if ($DrawArrows) {
						$this->myPicture->drawArrow($StartPos["R"] - $AxisProps["Margin"], $AxisPos["T"], $StartPos["R"] + ($ArrowSize * 2), $AxisPos["T"], ["FillColor" => $AxisColor,"Size" => $ArrowSize]);
					}

					$Width = $Xdiff - $AxisProps["Margin"] * 2;
					$Step = $Width / $AxisProps["Rows"];
					$SubTicksSize = $Step / 2;
					$MinTop = $AxisPos["T"];
					$LastX = NULL;
					for ($i = 0; $i <= $AxisProps["Rows"]; $i++) {
						$XPos = $StartPos["L"] + $AxisProps["Margin"] + $Step * $i;
						$YPos = $AxisPos["T"];
						$Value = $this->myPicture->scaleFormat($AxisProps["ScaleMin"] + $AxisProps["RowHeight"] * $i, $AxisProps);

						if (!is_null($LastX) && $CycleBackground && ($DrawXLines == [ALL] || in_array($AxisID, $DrawXLines))) {
							$this->myPicture->drawFilledRectangle($LastX, $StartPos["T"] + $FloatingOffset, $XPos, $StartPos["B"] - $FloatingOffset, ["Color" => ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2]);
						}

						if ($DrawXLines == [ALL] || in_array($AxisID, $DrawXLines)) {
							$this->myPicture->drawLine($XPos, $StartPos["T"] + $FloatingOffset, $XPos, $StartPos["B"] - $FloatingOffset, ["Color" => $GridColor,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisProps["Rows"]) {
							$this->myPicture->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, ["Color" => $SubTickColor]);
						}

						$this->myPicture->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["Color" => $TickColor]);
						$Bounds = $this->myPicture->drawText($XPos, $YPos - $OuterTickWidth - $LabelOffset, $Value, ["Angle" => $XLabelsRotation,"Align" => $LabelAlign]);
						$TxtBox = $YPos - $OuterTickWidth - 4 - ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
						$MinTop = min($MinTop, $TxtBox);
						$LastX = $XPos;
					}

					if (isset($AxisProps["Name"])) {
						$YPos = $MinTop - 2;
						$XPos = $StartPos["L"] + $Xdiff / 2;
						$Bounds = $this->myPicture->drawText($XPos, $YPos, $AxisProps["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
						$MinTop = $Bounds[2]["Y"];
					}

					$AxisPos["T"] = $MinTop - $ScaleSpacing;
				}

			} elseif ($AxisProps["Identity"] == AXIS_Y) {
				if ($AxisProps["Position"] == AXIS_POSITION_LEFT) {
					if ($Floating) {
						$FloatingOffset = $XMargin;
						$this->myPicture->drawLine($AxisPos["L"], $StartPos["T"] + $AxisProps["Margin"], $AxisPos["L"], $StartPos["B"] - $AxisProps["Margin"], ["Color" => $AxisColor]);
					} else {
						$FloatingOffset = 0;
						$this->myPicture->drawLine($AxisPos["L"], $StartPos["T"], $AxisPos["L"], $StartPos["B"], ["Color" => $AxisColor]);
					}

					if ($DrawArrows) {
						$this->myPicture->drawArrow($AxisPos["L"], $StartPos["T"] + $AxisProps["Margin"], $AxisPos["L"], $StartPos["T"] - ($ArrowSize * 2), ["FillColor" => $AxisColor,"Size" => $ArrowSize]);
					}

					$Height = $Ydiff - $AxisProps["Margin"] * 2;
					$Step = $Height / $AxisProps["Rows"];
					$SubTicksSize = $Step / 2;
					$MinLeft = $AxisPos["L"];
					$LastY = NULL;
					for ($i = 0; $i <= $AxisProps["Rows"]; $i++) {
						$YPos = $StartPos["B"] - $AxisProps["Margin"] - $Step * $i;
						$XPos = $AxisPos["L"];
						$Value = $this->myPicture->scaleFormat($AxisProps["ScaleMin"] + $AxisProps["RowHeight"] * $i, $AxisProps);

						if (!is_null($LastY) && $CycleBackground && ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines))) {
							$this->myPicture->drawFilledRectangle($StartPos["L"] + $FloatingOffset, $LastY, $StartPos["R"] - $FloatingOffset, $YPos, ["Color" => ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2]);
						}

						if (($YPos != $StartPos["T"] && $YPos != $StartPos["B"]) && ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines))) {
							$this->myPicture->drawLine($StartPos["L"] + $FloatingOffset, $YPos, $StartPos["R"] - $FloatingOffset, $YPos, ["Color" => $GridColor,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisProps["Rows"]) {
							 $this->myPicture->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, ["Color" => $SubTickColor]);
						}

						$this->myPicture->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["Color" => $TickColor]);
						$Bounds = $this->myPicture->drawText($XPos - $OuterTickWidth - 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
						$TxtLeft = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
						$MinLeft = min($MinLeft, $TxtLeft);
						$LastY = $YPos;
					}

					if (isset($AxisProps["Name"])) {
						$XPos = $MinLeft - 2;
						$YPos = $StartPos["T"] + $Ydiff / 2;
						$Bounds = $this->myPicture->drawText($XPos, $YPos, $AxisProps["Name"],["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 90]);
						$MinLeft = $Bounds[2]["X"];
					}

					$AxisPos["L"] = $MinLeft - $ScaleSpacing;
				} elseif ($AxisProps["Position"] == AXIS_POSITION_RIGHT) {
					if ($Floating) {
						$FloatingOffset = $XMargin;
						$this->myPicture->drawLine($AxisPos["R"], $StartPos["T"] + $AxisProps["Margin"], $AxisPos["R"], $StartPos["B"] - $AxisProps["Margin"], ["Color" => $AxisColor]);
					} else {
						$FloatingOffset = 0;
						$this->myPicture->drawLine($AxisPos["R"], $StartPos["T"], $AxisPos["R"], $StartPos["B"], ["Color" => $AxisColor]);
					}

					if ($DrawArrows) {
						$this->myPicture->drawArrow($AxisPos["R"], $StartPos["T"] + $AxisProps["Margin"], $AxisPos["R"], $StartPos["T"] - ($ArrowSize * 2), ["FillColor" => $AxisColor,"Size" => $ArrowSize]);
					}

					$Height = $Ydiff - $AxisProps["Margin"] * 2;
					$Step = $Height / $AxisProps["Rows"];
					$SubTicksSize = $Step / 2;
					$MaxLeft = $AxisPos["R"];
					$LastY = NULL;
					for ($i = 0; $i <= $AxisProps["Rows"]; $i++) {
						$YPos = $StartPos["B"] - $AxisProps["Margin"] - $Step * $i;
						$XPos = $AxisPos["R"];
						$Value = $this->myPicture->scaleFormat($AxisProps["ScaleMin"] + $AxisProps["RowHeight"] * $i, $AxisProps);

						if (!is_null($LastY) && $CycleBackground && ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines))) {
							$this->myPicture->drawFilledRectangle($StartPos["L"] + $FloatingOffset, $LastY, $StartPos["R"] - $FloatingOffset, $YPos, ["Color" => ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2]);
						}

						if (($YPos != $StartPos["T"] && $YPos != $StartPos["B"]) && ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines))) {
							$this->myPicture->drawLine($StartPos["L"] + $FloatingOffset, $YPos, $StartPos["R"] - $FloatingOffset, $YPos, ["Color" => $GridColor,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisProps["Rows"]) {
							$this->myPicture->drawLine($XPos - $InnerSubTickWidth, $YPos - $SubTicksSize, $XPos + $OuterSubTickWidth, $YPos - $SubTicksSize, ["Color" => $SubTickColor]);
						}
						
						$this->myPicture->drawLine($XPos - $InnerTickWidth, $YPos, $XPos + $OuterTickWidth, $YPos, ["Color" => $TickColor]);
						$Bounds = $this->myPicture->drawText($XPos + $OuterTickWidth + 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
						$TxtLeft = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
						$MaxLeft = max($MaxLeft, $TxtLeft);
						$LastY = $YPos;
					}

					if (isset($AxisProps["Name"])) {
						$XPos = $MaxLeft + 6;
						$YPos = $StartPos["T"] + $Ydiff / 2;
						$Bounds = $this->myPicture->drawText($XPos, $YPos, $AxisProps["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 270]);
						$MaxLeft = $Bounds[2]["X"];
					}

					$AxisPos["R"] = $MaxLeft + $ScaleSpacing;
				}
			}
		}

		$this->myPicture->myData->saveData(["Axis" => $Data["Axis"]]);
	}

	/* Draw a scatter plot chart */
	public function drawScatterPlotChart(array $Format = [])
	{
		$PlotSize = 3;
		$PlotBorder = FALSE;
		$BorderColor = new pColor(250,250,250,30);
		$BorderSize = 1;

		/* Override defaults */
		extract($Format);

		$Data = $this->myPicture->myData->getData();

		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				if (isset($Series["Picture"]) && $Series["Picture"] != "") {
					$Picture = $Series["Picture"];
					$PicInfo = $this->myPicture->getPicInfo($Picture);
					list($PicWidth, $PicHeight, $PicType) = $PicInfo;
				} else {
					$Picture = NULL;
				}

				$PosArrayX = $this->getPosArray($Data["Series"][$Series["X"]]["Data"], $Data["Series"][$Series["X"]]["Axis"]);
				$PosArrayY = $this->getPosArray($Data["Series"][$Series["Y"]]["Data"], $Data["Series"][$Series["Y"]]["Axis"]);

				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					if ($X != VOID && $Y != VOID) {

						if (isset($Series["Shape"])) {
							$this->myPicture->drawShape($X, $Y, $Series["Shape"], $PlotSize, $PlotBorder, $BorderSize, $Series["Color"], $BorderColor);
						} elseif (is_null($Picture)) {
							if ($PlotBorder) {
								$this->myPicture->drawFilledCircle($X, $Y, $PlotSize + $BorderSize, ["Color" => $BorderColor]);
							}

							$this->myPicture->drawFilledCircle($X, $Y, $PlotSize, ["Color" => $Series["Color"]]);
						} else {
							$this->myPicture->drawFromPicture($PicInfo, $Picture, $X - $PicWidth / 2, $Y - $PicHeight / 2);
						}
					}
				}
			}
		}
	}

	/* Draw a scatter line chart */
	public function drawScatterLineChart()
	{
		$Data = $this->myPicture->myData->getData();

		/* Parse all the series to draw */
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$PosArrayX = $this->getPosArray($Data["Series"][$Series["X"]]["Data"], $Data["Series"][$Series["X"]]["Axis"]);
				$PosArrayY = $this->getPosArray($Data["Series"][$Series["Y"]]["Data"], $Data["Series"][$Series["Y"]]["Axis"]);

				$Settings = ["Color" => $Series["Color"]];
				(!is_null($Series["Ticks"])) AND $Settings["Ticks"] = $Series["Ticks"];
				(!is_null($Series["Weight"])) AND $Settings["Weight"] = $Series["Weight"];

				$LastX = VOID;
				$LastY = VOID;
				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					if ($X != VOID && $Y != VOID) {

						if ($LastX != VOID && $LastY != VOID){
							$this->myPicture->drawLine($LastX, $LastY, $X, $Y, $Settings);
						}
					}

					$LastX = $X;
					$LastY = $Y;
				}
			}
		}
	}

	/* Draw a scatter spline chart */
	public function drawScatterSplineChart()
	{
		$Data = $this->myPicture->myData->getData();

		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$PosArrayX = $this->getPosArray($Data["Series"][$Series["X"]]["Data"], $Data["Series"][$Series["X"]]["Axis"]);
				$PosArrayY = $this->getPosArray($Data["Series"][$Series["Y"]]["Data"], $Data["Series"][$Series["Y"]]["Axis"]);

				$SplineSettings = ["Color" => $Series["Color"]]; 
				(!is_null($Series["Ticks"])) AND $SplineSettings["Ticks"] = $Series["Ticks"];
				(!is_null($Series["Weight"])) AND $SplineSettings["Weight"] = $Series["Weight"];

				$LastX = VOID;
				$LastY = VOID;
				$WayPoints = [];
				$SplineSettings["Forces"] = [];

				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];

					if ($X != VOID && $Y != VOID) {
						$WayPoints[] = [$X,$Y];
						$SplineSettings["Forces"][] = hypot(($X - $LastX),($Y - $LastY)) / 5;
					} else {
						$this->myPicture->drawSpline($WayPoints, $SplineSettings);
						$WayPoints = [];
						$SplineSettings["Forces"] = [];
					}

					$LastX = $X;
					$LastY = $Y;
				}

				$this->myPicture->drawSpline($WayPoints, $SplineSettings);
			}
		}
	}

	/* Return the scaled plot position */
	private function getPosArray(array $Values, int $AxisID)
	{
		$Result = [];
		$Data = $this->myPicture->myData->getData();
		$AxisData = $Data["Axis"][$AxisID];

		foreach($Values as $Value) {
			$Result[] = $this->getPosArraySingle($Value, $AxisData);
		}

		return $Result;
	}

	/* Return the scaled plot position */
	private function getPosArraySingle($Value, array $Data = [])
	{
		if ($Value == VOID) {
			return VOID;
		}

		list($Xdiff, $Ydiff) = $this->myPicture->getGraphAreaDiffs();
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		if ($Data["Identity"] == AXIS_X) {
			$Height = $Xdiff - $Data["Margin"] * 2;
			$Result = $GraphAreaCoordinates['L'] + $Data["Margin"] + (($Height / ($Data["ScaleMax"] - $Data["ScaleMin"])) * ($Value - $Data["ScaleMin"]));
		} else {
			$Height = $Ydiff - $Data["Margin"] * 2;
			$Result = $GraphAreaCoordinates['B'] - $Data["Margin"] - (($Height / ($Data["ScaleMax"] - $Data["ScaleMin"])) * ($Value - $Data["ScaleMin"]));
		}

		return $Result;
	}

	/* Draw the legend of the active series */
	public function drawScatterLegend(int $X, int $Y, array $Format = [])
	{
		$fontProperties = $this->myPicture->getFont();

		$Family = LEGEND_FAMILY_BOX;
		$FontName = $fontProperties['Name'];
		$FontSize = $fontProperties['Size'];
		$FontColor = $fontProperties['Color'];
		$BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 5;
		$BoxHeight = isset($Format["BoxHeight"]) ? $Format["BoxHeight"] : 5;
		$IconAreaWidth = $BoxWidth;
		$IconAreaHeight = $BoxHeight;
		$XSpacing = 5;
		$Margin = 5;
		$Color = new pColor(200);
		$BorderColor = new pColor(255);
		$Surrounding = NULL;
		$Style = LEGEND_ROUND;
		$Mode = LEGEND_VERTICAL;

		/* Override defaults */
		extract($Format);

		if (!is_null($Surrounding)) {
			$BorderColor = $Color->newOne()->RGBChange($Surrounding);
		}

		$Data = $this->myPicture->myData->getData();
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"] && isset($Series["Picture"])) {
				list($PicWidth, $PicHeight) = $this->myPicture->getPicInfo($Series["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}
		
		$HalfIconAreaHeight = $IconAreaHeight / 2;
		$HalfIconAreaWidth  = $IconAreaWidth  / 2;

		$YStep = max($fontProperties['Size'], $IconAreaHeight) + 5;
		$XStep = $XSpacing;
		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$Lines = preg_split("/\n/", $Series["Description"]);

				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->myPicture->getTextBox($vX + $IconAreaWidth + 4, $vY + $HalfIconAreaHeight, $FontName, $FontSize, 0, $Series["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $HalfIconAreaHeight) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $HalfIconAreaHeight
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $HalfIconAreaHeight) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $HalfIconAreaHeight;

					$vY = $vY + max($fontProperties['Size'] * count($Lines), $IconAreaHeight) + 5;

				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->myPicture->getTextBox($vX + $IconAreaWidth + 6, $Y + $HalfIconAreaHeight + (($fontProperties['Size'] + 3) * $Key), $FontName, $FontSize, 0, $Value);
						($Boundaries["T"] > $BoxArray[2]["Y"] + $HalfIconAreaHeight) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $HalfIconAreaHeight;
						($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
						($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $HalfIconAreaHeight) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $HalfIconAreaHeight;

						$Width[] = $BoxArray[1]["X"];
					}

					$vX = max($Width) + $XStep;
				}
			}
		}

		$vY = $vY - $YStep;
		#$vX = $vX - $XStep; # UNUSED
		$TopOffset = $Y - $Boundaries["T"];
		if ($Boundaries["B"] - ($vY + $IconAreaHeight) < $TopOffset) {
			$Boundaries["B"] = $vY + $IconAreaHeight + $TopOffset;
		}

		if ($Style == LEGEND_ROUND) {
			$this->myPicture->drawRoundedFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Margin, ["Color" => $Color,"BorderColor" => $BorderColor]);
		} elseif ($Style == LEGEND_BOX) {
			$this->myPicture->drawFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, ["Color" => $Color,"BorderColor" => $BorderColor]);
		}

		$ShadowSpec = $this->myPicture->getShadow();
		$this->myPicture->setShadow(FALSE);
		
		$FilledColor = new pColor(0,0,0,20);

		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				if (isset($Series["Picture"])) {
					$Picture = $Series["Picture"];
					list($PicWidth, $PicHeight) = $this->myPicture->getPicInfo($Picture);
					$PicX = $X + $HalfIconAreaWidth;
					$PicY = $Y + $HalfIconAreaHeight;
					$this->myPicture->drawFromPNG($PicX - $PicWidth / 2, $PicY - $PicHeight / 2, $Picture);

				} else {
					if ($Family == LEGEND_FAMILY_BOX) {
						$XOffset = ($BoxWidth != $IconAreaWidth) ? floor(($IconAreaWidth - $BoxWidth) / 2) : 0;
						$YOffset = ($BoxHeight != $IconAreaHeight) ? floor(($IconAreaHeight - $BoxHeight) / 2) : 0;

						$this->myPicture->drawFilledRectangle($X + 1 + $XOffset, $Y + 1 + $YOffset, $X + $BoxWidth + $XOffset + 1, $Y + $BoxHeight + 1 + $YOffset, ["Color" => $FilledColor]);
						$this->myPicture->drawFilledRectangle($X + $XOffset, $Y + $YOffset, $X + $BoxWidth + $XOffset, $Y + $BoxHeight + $YOffset, ["Color" => $Series["Color"],"Surrounding" => 20]);
					} elseif ($Family == LEGEND_FAMILY_CIRCLE) {
						$this->myPicture->drawFilledCircle($X + 1 + $HalfIconAreaWidth, $Y + 1 + $HalfIconAreaHeight, min($HalfIconAreaHeight, $HalfIconAreaWidth), ["Color" => $FilledColor]);
						$this->myPicture->drawFilledCircle($X + $HalfIconAreaWidth, $Y + $HalfIconAreaHeight, min($HalfIconAreaHeight, $HalfIconAreaWidth), ["Color" => $Series["Color"],"Surrounding" => 20]);
					} elseif ($Family == LEGEND_FAMILY_LINE) {
						$this->myPicture->drawLine($X + 1, $Y + 1 + $HalfIconAreaHeight, $X + 1 + $IconAreaWidth, $Y + 1 + $HalfIconAreaHeight, ["Color" => $FilledColor,"Ticks" => $Series["Ticks"], "Weight" => $Series["Weight"]]);
						$this->myPicture->drawLine($X, $Y + $HalfIconAreaHeight, $X + $IconAreaWidth, $Y + $HalfIconAreaHeight, ["Color" => $Series["Color"],"Ticks" => $Series["Ticks"],"Weight" => $Series["Weight"]]);
					}
				}

				$Lines = preg_split("/\n/", $Series["Description"]);

				if ($Mode == LEGEND_VERTICAL) {
					foreach($Lines as $Key => $Value) {
						$this->myPicture->drawText($X + $IconAreaWidth + 4, $Y + $HalfIconAreaHeight + (($fontProperties['Size'] + 3) * $Key), $Value, ["Color" => $FontColor,"Align" => TEXT_ALIGN_MIDDLELEFT]);
					}
					$Y = $Y + max($fontProperties['Size'] * count($Lines), $IconAreaHeight) + 5;
				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->myPicture->drawText($X + $IconAreaWidth + 4, $Y + 2 + $HalfIconAreaHeight + (($fontProperties['Size'] + 3) * $Key), $Value, ["Color" => $FontColor,"Align" => TEXT_ALIGN_MIDDLELEFT]);
						$Width[] = $BoxArray[1]["X"];
					}
					$X = max($Width) + 2 + $XStep;
				}
			}
		}

		$this->myPicture->restoreShadow($ShadowSpec);
	} 

	/* Get the legend box size */
	public function getScatterLegendSize(array $Format = []) # UNUSED
	{
		$fontProperties = $this->myPicture->getFont();

		$FontName = isset($Format["FontName"]) ? $Format["FontName"] : $fontProperties['Name'];
		$FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $fontProperties['Size'];
		$BoxSize = isset($Format["BoxSize"]) ? $Format["BoxSize"] : 5;
		$Margin = isset($Format["Margin"]) ? $Format["Margin"] : 5;
		#$Style = isset($Format["Style"]) ? $Format["Style"] : LEGEND_ROUND;
		$Mode = isset($Format["Mode"]) ? $Format["Mode"] : LEGEND_VERTICAL;
		$YStep = max($fontProperties['Size'], $BoxSize) + 5;
		$XStep = $BoxSize + 5;
		$X = 100;
		$Y = 100;
		$Data = $this->myPicture->myData->getData();
		
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"] && isset($Series["Picture"])) {
				list($PicWidth, $PicHeight) = $this->myPicture->getPicInfo($Series["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {

				$Lines = preg_split("/\n/", $Series["Description"]);

				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->myPicture->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Series["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

					$vY = $vY + max($fontProperties['Size'] * count($Lines), $IconAreaHeight) + 5;

				} elseif ($Mode == LEGEND_HORIZONTAL) {

					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->myPicture->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + (($fontProperties['Size'] + 3) * $Key), $FontName, $FontSize, 0, $Value);
						($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
						($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
						($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

						$Width[] = $BoxArray[1]["X"];
					}

					$vX = max($Width) + $XStep;
				}
			}
		}

		$vY = $vY - $YStep + $BoxSize;
		$TopOffset = $Y - $Boundaries["T"];
		($Boundaries["B"] - $vY < $TopOffset) AND $Boundaries["B"] = $vY + $TopOffset;
		$Width = ($Boundaries["R"] + $Margin) - ($Boundaries["L"] - $Margin);
		$Height = ($Boundaries["B"] + $Margin) - ($Boundaries["T"] - $Margin);

		return ["Width" => $Width,"Height" => $Height];
	}

	/* Draw the line of best fit */
	public function drawScatterBestFit(array $Format = [])
	{
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$Data = $this->myPicture->myData->getData();
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		foreach($Data["ScatterSeries"] as $Series) {
			if ($Series["isDrawable"]) {
				$SerieXAxis = $Data["Series"][$Series["X"]]["Axis"];
				$SerieYAxis = $Data["Series"][$Series["Y"]]["Axis"];
				$PosArrayX = $Data["Series"][$Series["X"]]["Data"];
				$PosArrayY = $Data["Series"][$Series["Y"]]["Data"];
				$XAxisData = $Data["Axis"][$SerieXAxis];
				$YAxisData = $Data["Axis"][$SerieYAxis];
				$Sxy = 0;
				$Sx = 0;
				$Sy = 0;
				$Sxx = 0;
				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					$Sxy = $Sxy + $X * $Y;
					$Sx = $Sx + $X;
					$Sy = $Sy + $Y;
					$Sxx = $Sxx + $X * $X;
				}

				$n = count($PosArrayX);
				if ((($n * $Sxx) == ($Sx * $Sx))) { # Momchil: No example goes in here
					$X1 = $GraphAreaCoordinates['L'] + $XAxisData["Margin"];
					$X2 = $X1;
					$Y1 = $GraphAreaCoordinates["T"];
					$Y2 = $GraphAreaCoordinates["B"];
				} else {
					$M = (($n * $Sxy) - ($Sx * $Sy)) / (($n * $Sxx) - ($Sx * $Sx));
					$B = (($Sy) - ($M * $Sx)) / ($n);
					$X1 = $GraphAreaCoordinates['L'] + $XAxisData["Margin"];
					$Y1 = $this->getPosArraySingle($M * $XAxisData["ScaleMin"] + $B, $YAxisData);
					$X2 = $this->getPosArraySingle($XAxisData["ScaleMax"], $XAxisData);
					$Y2 = $this->getPosArraySingle($M * $XAxisData["ScaleMax"] + $B, $YAxisData);
					$RealM = - ($Y2 - $Y1) / ($X2 - $X1);
					if ($Y1 < $GraphAreaCoordinates["T"]) {
						$X1 = $X1 + ($GraphAreaCoordinates["T"] - $Y1 / $RealM);
						$Y1 = $GraphAreaCoordinates["T"];
					}

					if ($Y1 > $GraphAreaCoordinates["B"]) {
						$X1 = $X1 + ($Y1 - $GraphAreaCoordinates["B"]) / $RealM;
						$Y1 = $GraphAreaCoordinates["B"];
					}

					if ($Y2 < $GraphAreaCoordinates["T"]) {
						$X2 = $X2 - ($GraphAreaCoordinates["T"] - $Y2) / $RealM;
						$Y2 = $GraphAreaCoordinates["T"];
					}

					if ($Y2 > $GraphAreaCoordinates["B"]) {
						$X2 = $X2 - ($Y2 - $GraphAreaCoordinates["B"]) / $RealM;
						$Y2 = $GraphAreaCoordinates["B"];
					}
				}

				$this->myPicture->drawLine($X1, $Y1, $X2, $Y2, ["Color" => $Series["Color"], "Ticks" => $Ticks]);
			}
		}
	}

	public function writeScatterLabel(int $ScatterSerieID, int $Point, array $Format = [])
	{
		$Data = $this->myPicture->myData->getData();

		if (!isset($Data["ScatterSeries"][$ScatterSerieID])) {
			throw pException::ScatterInvalidInputException("Serie was not found!");
		}

		$DrawPoint = isset($Format["DrawPoint"]) ? $Format["DrawPoint"] : LABEL_POINT_BOX;
		$Decimals = isset($Format["Decimals"]) ? $Format["Decimals"] : NULL;

		$Series = $Data["ScatterSeries"][$ScatterSerieID];
		$SerieValuesX = $Data["Series"][$Series["X"]]["Data"];
		$SerieXAxis = $Data["Series"][$Series["X"]]["Axis"];
		$SerieValuesY = $Data["Series"][$Series["Y"]]["Data"];
		$SerieYAxis = $Data["Series"][$Series["Y"]]["Axis"];

		$PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
		$PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);

		if (isset($PosArrayX[$Point]) && isset($PosArrayY[$Point])) {
			$X = floor($PosArrayX[$Point]);
			$Y = floor($PosArrayY[$Point]);
			if ($DrawPoint == LABEL_POINT_CIRCLE) {
				$this->myPicture->drawFilledCircle($X, $Y, 3, ["Color" => new pColor(255,255,255), "BorderColor" => new pColor(0,0,0)]);
			} elseif ($DrawPoint == LABEL_POINT_BOX) {
				$this->myPicture->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["Color" => new pColor(255,255,255), "BorderColor" => new pColor(0,0,0)]);
			}

			$XValue = (is_null($Decimals)) ? $SerieValuesX[$Point] : round($SerieValuesX[$Point], $Decimals);
			$XValue = $this->myPicture->scaleFormat($XValue, $Data["Axis"][$SerieXAxis]);

			$YValue = (is_null($Decimals)) ? $SerieValuesY[$Point] : round($SerieValuesY[$Point], $Decimals);
			$YValue = $this->myPicture->scaleFormat($YValue, $Data["Axis"][$SerieYAxis]);

			$Description = (isset($Series["Description"])) ? $Series["Description"] : "No description";
			$this->myPicture->drawLabelBox($X, $Y - 3, $Description, ["Color" => $Series["Color"],"Caption" => $XValue . " / " . $YValue], $Format);
		}

	}

	/* Draw a Scatter threshold */
	public function drawScatterThreshold($Value, array $Format = [])
	{
		$AxisID = 0;
		$Color = new pColor(255,0,0,50);
		$Weight = NULL;
		$Ticks = 3;
		$Wide = FALSE;
		$WideFactor = 5;
		$WriteCaption = FALSE;
		$Caption = NULL;
		$CaptionAlign = CAPTION_LEFT_TOP;
		$CaptionOffset = 10;
		$CaptionColor = new pColor(255);
		$DrawBox = TRUE;
		$DrawBoxBorder = FALSE;
		$BorderOffset = 5;
		$BoxRounded = TRUE;
		$RoundedRadius = 3;
		$BoxColor = new pColor(0,0,0,20);
		$BoxSurrounding = 0;
		$BoxBorderColor = new pColor(255);

		/* Override defaults */
		extract($Format);

		$Data = $this->myPicture->myData->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			throw pException::ScatterInvalidInputException("Axis ID was not found!");
		}

		(is_null($Caption)) AND $Caption = strval($Value);
		$CaptionSettings = [
			"DrawBox" => $DrawBox,
			"DrawBoxBorder" => $DrawBoxBorder,
			"BorderOffset" => $BorderOffset,
			"BoxRounded" => $BoxRounded,
			"RoundedRadius" => $RoundedRadius,
			"BoxColor" => $BoxColor,
			"BoxSurrounding" => $BoxSurrounding,
			"BoxBorderColor" => $BoxBorderColor,
			"Color" => $CaptionColor
		];
		
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		if ($Data["Axis"][$AxisID]["Identity"] == AXIS_Y) {
			$X1 = $GraphAreaCoordinates['L'] + $Data["Axis"][$AxisID]["Margin"];
			$X2 = $GraphAreaCoordinates['R'] - $Data["Axis"][$AxisID]["Margin"];
			$Y = $this->getPosArraySingle($Value, $Data["Axis"][$AxisID]);
			$this->myPicture->drawLine($X1, $Y, $X2, $Y, ["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
			if ($Wide) {
				$WideColor = $Color->newOne()->AlphaSlash($WideFactor);
				$this->myPicture->drawLine($X1, $Y - 1, $X2, $Y - 1, ["Color" => $WideColor,"Ticks" => $Ticks]);
				$this->myPicture->drawLine($X1, $Y + 1, $X2, $Y + 1, ["Color" => $WideColor,"Ticks" => $Ticks]);
			}

			if ($WriteCaption) {
				if ($CaptionAlign == CAPTION_LEFT_TOP) {
					$X = $X1 + $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
				} else {
					$X = $X2 - $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
				}

				$this->myPicture->drawText($X, $Y, $Caption, $CaptionSettings);
			}

		} elseif ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
			$X = $this->getPosArraySingle($Value, $Data["Axis"][$AxisID]);
			$Y1 = $GraphAreaCoordinates['T'] + $Data["Axis"][$AxisID]["Margin"];
			$Y2 = $GraphAreaCoordinates['B'] - $Data["Axis"][$AxisID]["Margin"];
			$this->myPicture->drawLine($X, $Y1, $X, $Y2, ["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
			if ($Wide) {
				$WideColor = $Color->newOne()->AlphaSlash($WideFactor);
				$this->myPicture->drawLine($X - 1, $Y1, $X - 1, $Y2, ["Color" => $WideColor,"Ticks" => $Ticks]);
				$this->myPicture->drawLine($X + 1, $Y1, $X + 1, $Y2, ["Color" => $WideColor,"Ticks" => $Ticks]);
			}

			if ($WriteCaption) {
				if ($CaptionAlign == CAPTION_LEFT_TOP) {
					$Y = $Y1 + $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
				} else {
					$Y = $Y2 - $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
				}

				// $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE; // bug in the original code
				$this->myPicture->drawText($X, $Y, $Caption, $CaptionSettings);
			}

		}
	}

	/* Draw a Scatter threshold area */
	public function drawScatterThresholdArea($Value1, $Value2, array $Format = [])
	{
		$AxisID = 0;
		$Color = new pColor(255,0,0,20);
		$Border = TRUE;
		$BorderColor = NULL;
		$BorderTicks = 2;
		$AreaName = NULL;
		$NameAngle = ZONE_NAME_ANGLE_AUTO;
		$NameColor = new pColor(255);
		$DisableShadowOnArea = TRUE;

		/* Override defaults */
		extract($Format);

		if (is_null($BorderColor)){
			$BorderColor = $Color->newOne()->RGBChange(20);
		}
		$BorderSettings = ["Color" => $BorderColor,"Ticks" => $BorderTicks];

		$Data = $this->myPicture->myData->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			throw pException::ScatterInvalidInputException("Serie was not found!");
		}

		if ($Value1 > $Value2) {
			list($Value1, $Value2) = [$Value2,$Value1];
		}

		$ShadowSpec = $this->myPicture->getShadow();
		if ($DisableShadowOnArea && $ShadowSpec['Enabled']) {
			$this->myPicture->setShadow(FALSE);
		}

		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();
		$Margin = $Data["Axis"][$AxisID]["Margin"];

		if ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
			$Y1 = $GraphAreaCoordinates["T"] + $Margin;
			$Y2 = $GraphAreaCoordinates["B"] - $Margin;
			$X1 = $this->getPosArraySingle($Value1, $Data["Axis"][$AxisID]);
			$X2 = $this->getPosArraySingle($Value2, $Data["Axis"][$AxisID]);
			if ($X1 <= $GraphAreaCoordinates["L"]) {
				$X1 = $GraphAreaCoordinates["L"] + $Margin;
			}

			if ($X2 >= $GraphAreaCoordinates["R"]) {
				$X2 = $GraphAreaCoordinates["R"] - $Margin;
			}

			$this->myPicture->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["Color" => $Color]);

			if ($Border) {
				$this->myPicture->drawLine($X1, $Y1, $X1, $Y2, $BorderSettings);
				$this->myPicture->drawLine($X2, $Y1, $X2, $Y2, $BorderSettings);
			}

			if (!is_null($AreaName)) {
				$XPos = ($X2 - $X1) / 2 + $X1;
				$YPos = ($Y2 - $Y1) / 2 + $Y1;
				if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
					$fontProperties = $this->myPicture->getFont();
					$TxtPos = $this->myPicture->getTextBox($XPos, $YPos, $fontProperties['Name'], $fontProperties['Size'], 0, $AreaName);
					$TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
					$NameAngle = (abs($X2 - $X1) > $TxtWidth) ? 0 : 90;
				}

				$this->myPicture->restoreShadow($ShadowSpec);
				$this->myPicture->drawText($XPos, $YPos, $AreaName, ["Color" => $NameColor,"Angle" => $NameAngle,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				($DisableShadowOnArea) AND $this->myPicture->setShadow(FALSE);
			}

		} elseif ($Data["Axis"][$AxisID]["Identity"] == AXIS_Y) {

			$X1 = $GraphAreaCoordinates["L"] + $Margin;
			$X2 = $GraphAreaCoordinates["R"] - $Margin;
			$Y1 = $this->getPosArraySingle($Value1, $Data["Axis"][$AxisID]);
			$Y2 = $this->getPosArraySingle($Value2, $Data["Axis"][$AxisID]);
			if ($Y1 >= $GraphAreaCoordinates["B"]) {
				$Y1 = $GraphAreaCoordinates["B"] - $Margin;
			}

			if ($Y2 <= $GraphAreaCoordinates["T"]) {
				$Y2 = $GraphAreaCoordinates["T"] + $Margin;
			}

			$this->myPicture->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["Color" => $Color]);
			if ($Border) {
				$this->myPicture->drawLine($X1, $Y1, $X2, $Y1, $BorderSettings);
				$this->myPicture->drawLine($X1, $Y2, $X2, $Y2, $BorderSettings);
			}

			if (!is_null($AreaName)) {
				$XPos = ($X2 - $X1) / 2 + $X1;
				$YPos = ($Y2 - $Y1) / 2 + $Y1;
				$this->myPicture->restoreShadow($ShadowSpec);
				$this->myPicture->drawText($YPos, $XPos, $AreaName, ["Color" => $NameColor,"Angle" => 0,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				($DisableShadowOnArea) AND $this->myPicture->setShadow(FALSE);
			}
	
		}

		$this->myPicture->restoreShadow($ShadowSpec);
	}
}
