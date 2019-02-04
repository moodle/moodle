<?php
/*
pScatter - class to draw scatter charts

Version     : 2.1.4
Made by     : Jean-Damien POGOLOTTI
Last Update : 19/01/2014

This file can be distributed under the license you can find at :

http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/
define("SCATTER_MISSING_X_SERIE", 190001);
define("SCATTER_MISSING_Y_SERIE", 190002);

/* pScatter class definition */
class pScatter
{
	var $pChartObject;
	var $pDataObject;
	/* Class creator */
	function __construct($pChartObject, $pDataObject)
	{
		$this->pChartObject = $pChartObject;
		$this->pDataObject = $pDataObject;
	}

	/* Prepare the scale */
	function drawScatterScale(array $Format = [])
	{

		/* Check if we have at least both one X and Y axis */
		$GotXAxis = FALSE;
		$GotYAxis = FALSE;
		foreach($this->pDataObject->Data["Axis"] as $AxisID => $AxisSettings) {
			($AxisSettings["Identity"] == AXIS_X) AND $GotXAxis = TRUE;
			($AxisSettings["Identity"] == AXIS_Y) AND $GotYAxis = TRUE;
		}

		if (!$GotXAxis) {
			return (SCATTER_MISSING_X_SERIE);
		}

		if (!$GotYAxis) {
			return (SCATTER_MISSING_Y_SERIE);
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
		$DrawXLines = ALL;
		$DrawYLines = ALL;
		$GridTicks = 4;
		$GridR = 255;
		$GridG = 255;
		$GridB = 255;
		$GridAlpha = 40;
		$AxisRo = isset($Format["AxisR"]) ? $Format["AxisR"] : 0;
		$AxisGo = isset($Format["AxisG"]) ? $Format["AxisG"] : 0;
		$AxisBo = isset($Format["AxisB"]) ? $Format["AxisB"] : 0;
		$AxisAlpha = 100;
		$TickRo = isset($Format["TickR"]) ? $Format["TickR"] : 0;
		$TickGo = isset($Format["TickG"]) ? $Format["TickG"] : 0;
		$TickBo = isset($Format["TickB"]) ? $Format["TickB"] : 0;
		$TickAlpha = 100;
		$DrawSubTicks = FALSE;
		$InnerSubTickWidth = 0;
		$OuterSubTickWidth = 2;
		$SubTickR = 255;
		$SubTickG = 0;
		$SubTickB = 0;
		$SubTickAlpha = 100;
		$XReleasePercent = 1;
		$DrawArrows = FALSE;
		$ArrowSize = 8;
		$CycleBackground = FALSE;
		$BackgroundR1 = 255;
		$BackgroundG1 = 255;
		$BackgroundB1 = 255;
		$BackgroundAlpha1 = 10;
		$BackgroundR2 = 230;
		$BackgroundG2 = 230;
		$BackgroundB2 = 230;
		$BackgroundAlpha2 = 10;

		/* Override defaults */
		extract($Format);

		$BG1 = ["R" => $BackgroundR1,"G" => $BackgroundG1,"B" => $BackgroundB1,"Alpha" => $BackgroundAlpha1];
		$BG2 = ["R" => $BackgroundR2,"G" => $BackgroundG2,"B" => $BackgroundB2,"Alpha" => $BackgroundAlpha2];

		/* Skip a NOTICE event in case of an empty array */
		($DrawYLines == NONE) AND $DrawYLines = ["zarma" => "31"];

		$Data = $this->pDataObject->getData();
		foreach($Data["Axis"] as $AxisID => $AxisSettings) {
			if ($AxisSettings["Identity"] == AXIS_X) {
				$Width = $this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $XMargin * 2;
			} else {
				$Width = $this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1 - $YMargin * 2;
			}

			$AxisMin = ABSOLUTE_MAX;
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
					echo "Manual scale boundaries not set.";
					exit();
				}
			}

			/* Full manual scale */
			if (isset($ManualScale[$AxisID]["Rows"]) && isset($ManualScale[$AxisID]["RowHeight"])) {
				$Scale = ["Rows" => $ManualScale[$AxisID]["Rows"],"RowHeight" => $ManualScale[$AxisID]["RowHeight"],"XMin" => $ManualScale[$AxisID]["Min"],"XMax" => $ManualScale[$AxisID]["Max"]];
			} else {
				$MaxDivs = floor($Width / $MinDivHeight);
				$Scale = $this->pChartObject->computeScale($Data["Axis"][$AxisID]["Min"], $Data["Axis"][$AxisID]["Max"], $MaxDivs, $Factors, $AxisID);
			}

			$Data["Axis"][$AxisID]["Margin"] = $AxisSettings["Identity"] == AXIS_X ? $XMargin : $YMargin;
			$Data["Axis"][$AxisID]["ScaleMin"] = $Scale["XMin"];
			$Data["Axis"][$AxisID]["ScaleMax"] = $Scale["XMax"];
			$Data["Axis"][$AxisID]["Rows"] = $Scale["Rows"];
			$Data["Axis"][$AxisID]["RowHeight"] = $Scale["RowHeight"];
			(isset($Scale["Format"])) AND $Data["Axis"][$AxisID]["Format"] = $Scale["Format"];
			(!isset($Data["Axis"][$AxisID]["Display"])) AND $Data["Axis"][$AxisID]["Display"] = NULL;
			(!isset($Data["Axis"][$AxisID]["Format"])) AND $Data["Axis"][$AxisID]["Format"] = NULL;
			(!isset($Data["Axis"][$AxisID]["Unit"])) AND $Data["Axis"][$AxisID]["Unit"] = NULL;
		}

		/* Set the original boundaries */
		$AxisPos = [
			"L" => $this->pChartObject->GraphAreaX1,
			"R" => $this->pChartObject->GraphAreaX2,
			"T" => $this->pChartObject->GraphAreaY1,
			"B" => $this->pChartObject->GraphAreaY2
		];

		foreach($Data["Axis"] as $AxisID => $AxisSettings) {
			if (isset($AxisSettings["Color"])) {
				$AxisR = $AxisSettings["Color"]["R"];
				$AxisG = $AxisSettings["Color"]["G"];
				$AxisB = $AxisSettings["Color"]["B"];
				$TickR = $AxisSettings["Color"]["R"];
				$TickG = $AxisSettings["Color"]["G"];
				$TickB = $AxisSettings["Color"]["B"];
				$this->pChartObject->setFontProperties(["R" => $AxisR,"G" => $AxisG,"B" => $AxisB]);
			} else {
				$AxisR = $AxisRo;
				$AxisG = $AxisGo;
				$AxisB = $AxisBo;
				$TickR = $TickRo;
				$TickG = $TickGo;
				$TickB = $TickBo;
				/* Get the default font color */
				$this->pChartObject->setFontProperties(["R" => $this->pChartObject->FontColorR,"G" => $this->pChartObject->FontColorG,"B" => $this->pChartObject->FontColorB]);
			}

			$LastValue = "w00t";
			$ID = 1;
			if ($AxisSettings["Identity"] == AXIS_X) {
				if ($AxisSettings["Position"] == AXIS_POSITION_BOTTOM) {
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
						$this->pChartObject->drawLine($this->pChartObject->GraphAreaX1 + $AxisSettings["Margin"], $AxisPos["B"], $this->pChartObject->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["B"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
					} else {
						$FloatingOffset = 0;
						$this->pChartObject->drawLine($this->pChartObject->GraphAreaX1, $AxisPos["B"], $this->pChartObject->GraphAreaX2, $AxisPos["B"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
					}

					if ($DrawArrows) {
						$this->pChartObject->drawArrow($this->pChartObject->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["B"], $this->pChartObject->GraphAreaX2 + ($ArrowSize * 2) , $AxisPos["B"], ["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
					}

					$Width = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) - $AxisSettings["Margin"] * 2;
					$Step = $Width / $AxisSettings["Rows"];
					$SubTicksSize = $Step / 2;
					$MaxBottom = $AxisPos["B"];
					$LastX = NULL;
					for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
						$XPos = $this->pChartObject->GraphAreaX1 + $AxisSettings["Margin"] + $Step * $i;
						$YPos = $AxisPos["B"];
						$Value = $this->pChartObject->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);
						$BGColor = ($i % 2 == 1) ? $BG1 : $BG2;

						if ($LastX != NULL && $CycleBackground && ($DrawXLines == ALL || in_array($AxisID, $DrawXLines))) {
							$this->pChartObject->drawFilledRectangle($LastX, $this->pChartObject->GraphAreaY1 + $FloatingOffset, $XPos, $this->pChartObject->GraphAreaY2 - $FloatingOffset, $BGColor);
						}

						if ($DrawXLines == ALL || in_array($AxisID, $DrawXLines)) {
							$this->pChartObject->drawLine($XPos, $this->pChartObject->GraphAreaY1 + $FloatingOffset, $XPos, $this->pChartObject->GraphAreaY2 - $FloatingOffset, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisSettings["Rows"]){
							$this->pChartObject->drawLine($XPos + $SubTicksSize, $YPos - $InnerSubTickWidth, $XPos + $SubTicksSize, $YPos + $OuterSubTickWidth, ["R" => $SubTickR,"G" => $SubTickG,"B" => $SubTickB,"Alpha" => $SubTickAlpha]);
						}

						$this->pChartObject->drawLine($XPos, $YPos - $InnerTickWidth, $XPos, $YPos + $OuterTickWidth, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
						$Bounds = $this->pChartObject->drawText($XPos, $YPos + $OuterTickWidth + $LabelOffset, $Value, ["Angle" => $XLabelsRotation,"Align" => $LabelAlign]);
						$TxtBottom = $YPos + 2 + $OuterTickWidth + 2 + ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
						$MaxBottom = max($MaxBottom, $TxtBottom);
						$LastX = $XPos;
					}

					if (isset($AxisSettings["Name"])) {
						$YPos = $MaxBottom + 2;
						$XPos = $this->pChartObject->GraphAreaX1 + ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) / 2;
						$Bounds = $this->pChartObject->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]);
						$MaxBottom = $Bounds[0]["Y"];
						$this->pDataObject->Data["GraphArea"]["Y2"] = $MaxBottom + $this->pChartObject->FontSize;
					}

					$AxisPos["B"] = $MaxBottom + $ScaleSpacing;

				} elseif ($AxisSettings["Position"] == AXIS_POSITION_TOP) {

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

					if ($XLabelsRotation > 180 && $SLabelxRotation < 360) {
						$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
						$LabelOffset = 5;
					}

					if ($Floating) {
						$FloatingOffset = $YMargin;
						$this->pChartObject->drawLine($this->pChartObject->GraphAreaX1 + $AxisSettings["Margin"], $AxisPos["T"], $this->pChartObject->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["T"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
					} else {
						$FloatingOffset = 0;
						$this->pChartObject->drawLine($this->pChartObject->GraphAreaX1, $AxisPos["T"], $this->pChartObject->GraphAreaX2, $AxisPos["T"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
					}

					if ($DrawArrows) {
						$this->pChartObject->drawArrow($this->pChartObject->GraphAreaX2 - $AxisSettings["Margin"], $AxisPos["T"], $this->pChartObject->GraphAreaX2 + ($ArrowSize * 2) , $AxisPos["T"], ["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
					}

					$Width = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) - $AxisSettings["Margin"] * 2;
					$Step = $Width / $AxisSettings["Rows"];
					$SubTicksSize = $Step / 2;
					$MinTop = $AxisPos["T"];
					$LastX = NULL;
					for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
						$XPos = $this->pChartObject->GraphAreaX1 + $AxisSettings["Margin"] + $Step * $i;
						$YPos = $AxisPos["T"];
						$Value = $this->pChartObject->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);
						$BGColor = ($i % 2 == 1) ? $BG1 : $BG2;

						if ($LastX != NULL && $CycleBackground && ($DrawXLines == ALL || in_array($AxisID, $DrawXLines))) {
							$this->pChartObject->drawFilledRectangle($LastX, $this->pChartObject->GraphAreaY1 + $FloatingOffset, $XPos, $this->pChartObject->GraphAreaY2 - $FloatingOffset, $BGColor);
						}

						if ($DrawXLines == ALL || in_array($AxisID, $DrawXLines)) {
							$this->pChartObject->drawLine($XPos, $this->pChartObject->GraphAreaY1 + $FloatingOffset, $XPos, $this->pChartObject->GraphAreaY2 - $FloatingOffset, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
							$this->pChartObject->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, ["R" => $SubTickR,"G" => $SubTickG,"B" => $SubTickB,"Alpha" => $SubTickAlpha]);
						}

						$this->pChartObject->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
						$Bounds = $this->pChartObject->drawText($XPos, $YPos - $OuterTickWidth - $LabelOffset, $Value, ["Angle" => $XLabelsRotation,"Align" => $LabelAlign]);
						$TxtBox = $YPos - $OuterTickWidth - 4 - ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
						$MinTop = min($MinTop, $TxtBox);
						$LastX = $XPos;
					}

					if (isset($AxisSettings["Name"])) {
						$YPos = $MinTop - 2;
						$XPos = $this->pChartObject->GraphAreaX1 + ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) / 2;
						$Bounds = $this->pChartObject->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
						$MinTop = $Bounds[2]["Y"];
						$this->pDataObject->Data["GraphArea"]["Y1"] = $MinTop;
					}

					$AxisPos["T"] = $MinTop - $ScaleSpacing;
				}

			} elseif ($AxisSettings["Identity"] == AXIS_Y) {
				if ($AxisSettings["Position"] == AXIS_POSITION_LEFT) {
					if ($Floating) {
						$FloatingOffset = $XMargin;
						$this->pChartObject->drawLine($AxisPos["L"], $this->pChartObject->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["L"], $this->pChartObject->GraphAreaY2 - $AxisSettings["Margin"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
					} else {
						$FloatingOffset = 0;
						$this->pChartObject->drawLine($AxisPos["L"], $this->pChartObject->GraphAreaY1, $AxisPos["L"], $this->pChartObject->GraphAreaY2, ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
					}

					if ($DrawArrows) {
						$this->pChartObject->drawArrow($AxisPos["L"], $this->pChartObject->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["L"], $this->pChartObject->GraphAreaY1 - ($ArrowSize * 2) , ["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
					}

					$Height = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) - $AxisSettings["Margin"] * 2;
					$Step = $Height / $AxisSettings["Rows"];
					$SubTicksSize = $Step / 2;
					$MinLeft = $AxisPos["L"];
					$LastY = NULL;
					for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
						$YPos = $this->pChartObject->GraphAreaY2 - $AxisSettings["Margin"] - $Step * $i;
						$XPos = $AxisPos["L"];
						$Value = $this->pChartObject->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);
						$BGColor = ($i % 2 == 1) ? $BG1 : $BG2;

						if ($LastY != NULL && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
							$this->pChartObject->drawFilledRectangle($this->pChartObject->GraphAreaX1 + $FloatingOffset, $LastY, $this->pChartObject->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
						}

						if (($YPos != $this->pChartObject->GraphAreaY1 && $YPos != $this->pChartObject->GraphAreaY2) && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
							$this->pChartObject->drawLine($this->pChartObject->GraphAreaX1 + $FloatingOffset, $YPos, $this->pChartObject->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
							 $this->pChartObject->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, ["R" => $SubTickR,"G" => $SubTickG,"B" => $SubTickB,"Alpha" => $SubTickAlpha]);
						}

						$this->pChartObject->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
						$Bounds = $this->pChartObject->drawText($XPos - $OuterTickWidth - 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
						$TxtLeft = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
						$MinLeft = min($MinLeft, $TxtLeft);
						$LastY = $YPos;
					}

					if (isset($AxisSettings["Name"])) {
						$XPos = $MinLeft - 2;
						$YPos = $this->pChartObject->GraphAreaY1 + ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) / 2;
						$Bounds = $this->pChartObject->drawText($XPos, $YPos, $AxisSettings["Name"],["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 90]);
						$MinLeft = $Bounds[2]["X"];
						$this->pDataObject->Data["GraphArea"]["X1"] = $MinLeft;
					}

					$AxisPos["L"] = $MinLeft - $ScaleSpacing;
				} elseif ($AxisSettings["Position"] == AXIS_POSITION_RIGHT) {
					if ($Floating) {
						$FloatingOffset = $XMargin;
						$this->pChartObject->drawLine($AxisPos["R"], $this->pChartObject->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["R"], $this->pChartObject->GraphAreaY2 - $AxisSettings["Margin"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
					} else {
						$FloatingOffset = 0;
						$this->pChartObject->drawLine($AxisPos["R"], $this->pChartObject->GraphAreaY1, $AxisPos["R"], $this->pChartObject->GraphAreaY2, ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
					}

					if ($DrawArrows) {
						$this->pChartObject->drawArrow($AxisPos["R"], $this->pChartObject->GraphAreaY1 + $AxisSettings["Margin"], $AxisPos["R"], $this->pChartObject->GraphAreaY1 - ($ArrowSize * 2), ["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
					}

					$Height = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) - $AxisSettings["Margin"] * 2;
					$Step = $Height / $AxisSettings["Rows"];
					$SubTicksSize = $Step / 2;
					$MaxLeft = $AxisPos["R"];
					$LastY = NULL;
					for ($i = 0; $i <= $AxisSettings["Rows"]; $i++) {
						$YPos = $this->pChartObject->GraphAreaY2 - $AxisSettings["Margin"] - $Step * $i;
						$XPos = $AxisPos["R"];
						$Value = $this->pChartObject->scaleFormat($AxisSettings["ScaleMin"] + $AxisSettings["RowHeight"] * $i, $AxisSettings["Display"], $AxisSettings["Format"], $AxisSettings["Unit"]);
						$BGColor = ($i % 2 == 1) ? $BG1 : $BG2;

						if ($LastY != NULL && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
							$this->pChartObject->drawFilledRectangle($this->pChartObject->GraphAreaX1 + $FloatingOffset, $LastY, $this->pChartObject->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
						}

						if (($YPos != $this->pChartObject->GraphAreaY1 && $YPos != $this->pChartObject->GraphAreaY2) && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
							$this->pChartObject->drawLine($this->pChartObject->GraphAreaX1 + $FloatingOffset, $YPos, $this->pChartObject->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
						}

						if ($DrawSubTicks && $i != $AxisSettings["Rows"]) {
							$this->pChartObject->drawLine($XPos - $InnerSubTickWidth, $YPos - $SubTicksSize, $XPos + $OuterSubTickWidth, $YPos - $SubTicksSize, ["R" => $SubTickR,"G" => $SubTickG,"B" => $SubTickB,"Alpha" => $SubTickAlpha]);
						}

						$this->pChartObject->drawLine($XPos - $InnerTickWidth, $YPos, $XPos + $OuterTickWidth, $YPos, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
						$Bounds = $this->pChartObject->drawText($XPos + $OuterTickWidth + 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
						$TxtLeft = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
						$MaxLeft = max($MaxLeft, $TxtLeft);
						$LastY = $YPos;
					}

					if (isset($AxisSettings["Name"])) {
						$XPos = $MaxLeft + 6;
						$YPos = $this->pChartObject->GraphAreaY1 + ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) / 2;
						$Bounds = $this->pChartObject->drawText($XPos, $YPos, $AxisSettings["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 270]);
						$MaxLeft = $Bounds[2]["X"];
						$this->pDataObject->Data["GraphArea"]["X2"] = $MaxLeft + $this->pChartObject->FontSize;
					}

					$AxisPos["R"] = $MaxLeft + $ScaleSpacing;
				}
			}
		}

		$this->pDataObject->saveAxisConfig($Data["Axis"]);
	}

	/* Draw a scatter plot chart */
	function drawScatterPlotChart(array $Format = [])
	{
		$PlotSize = 3;
		$PlotBorder = FALSE;
		$BorderR = 250;
		$BorderG = 250;
		$BorderB = 250;
		$BorderAlpha = 30;
		$BorderSize = 1;
		$Surrounding = NULL;
		$RecordImageMap = FALSE;
		$ImageMapTitle = NULL;
		$ImageMapPrecision = 2;

		/* Override defaults */
		extract($Format);

		$Data = $this->pDataObject->getData();
		$Palette = $this->pDataObject->getPalette();
		$BorderColor =["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha];
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE) {
				$SerieX = $Series["X"];
				$SerieValuesX = $Data["Series"][$SerieX]["Data"];
				$SerieXAxis = $Data["Series"][$SerieX]["Axis"];
				$SerieY = $Series["Y"];
				$SerieValuesY = $Data["Series"][$SerieY]["Data"];
				$SerieYAxis = $Data["Series"][$SerieY]["Axis"];
				$Description = ($ImageMapTitle == NULL) ? $Data["Series"][$Series["X"]]["Description"] . " / " . $Data["Series"][$Series["Y"]]["Description"] : $ImageMapTitle;

				if (isset($Series["Picture"]) && $Series["Picture"] != "") {
					$Picture = $Series["Picture"];
					list($PicWidth, $PicHeight, $PicType) = $this->pChartObject->getPicInfo($Picture);
				} else {
					$Picture = NULL;
				}

				$PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
				(!is_array($PosArrayX)) AND $PosArrayX = [0 => $PosArrayX];

				$PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);
				(!is_array($PosArrayY)) AND $PosArrayY = [0 => $PosArrayY];

				$Color = array("R" => $Series["Color"]["R"],"G" => $Series["Color"]["G"],"B" => $Series["Color"]["B"],"Alpha" => $Series["Color"]["Alpha"]);

				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					if ($X != VOID && $Y != VOID) {
						$RealValue = round($Data["Series"][$Series["X"]]["Data"][$Key], 2) . " / " . round($Data["Series"][$Series["Y"]]["Data"][$Key], 2);
						if ($RecordImageMap) {
							$this->pChartObject->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . floor($PlotSize + $BorderSize) , $this->pChartObject->toHTMLColor($Series["Color"]["R"], $Series["Color"]["G"], $Series["Color"]["B"]) , $Description, $RealValue);
						}

						if (isset($Series["Shape"])) {
							$this->pChartObject->drawShape($X, $Y, $Series["Shape"], $PlotSize, $PlotBorder, $BorderSize, $Series["Color"]["R"], $Series["Color"]["G"], $Series["Color"]["B"], $Series["Color"]["Alpha"], $BorderR, $BorderG, $BorderB, $BorderAlpha);
						} elseif ($Picture == NULL) {
							if ($PlotBorder) {
								$this->pChartObject->drawFilledCircle($X, $Y, $PlotSize + $BorderSize, $BorderColor);
							}

							$this->pChartObject->drawFilledCircle($X, $Y, $PlotSize, $Color);
						} else {
							$this->pChartObject->drawFromPicture($PicType, $Picture, $X - $PicWidth / 2, $Y - $PicHeight / 2);
						}
					}
				}
			}
		}
	}

	/* Draw a scatter line chart */
	function drawScatterLineChart(array $Format = [])
	{
		$Data = $this->pDataObject->getData();
		$Palette = $this->pDataObject->getPalette();
		$RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : FALSE;
		$ImageMapTitle = isset($Format["ImageMapTitle"]) ? $Format["ImageMapTitle"] : NULL;
		$ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 10;
		$ImageMapPrecision = isset($Format["ImageMapPrecision"]) ? $Format["ImageMapPrecision"] : 2;
		/* Parse all the series to draw */
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE) {
				$SerieX = $Series["X"];
				$SerieValuesX = $Data["Series"][$SerieX]["Data"];
				$SerieXAxis = $Data["Series"][$SerieX]["Axis"];
				$SerieY = $Series["Y"];
				$SerieValuesY = $Data["Series"][$SerieY]["Data"];
				$SerieYAxis = $Data["Series"][$SerieY]["Axis"];
				$Ticks = $Series["Ticks"];
				$Weight = $Series["Weight"];
				$Description = ($ImageMapTitle == NULL) ? $Data["Series"][$Series["X"]]["Description"] . " / " . $Data["Series"][$Series["Y"]]["Description"] : $ImageMapTitle;

				$PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
				(!is_array($PosArrayX)) AND $PosArrayX = [0 => $PosArrayX];

				$PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);
				(!is_array($PosArrayY)) AND $PosArrayY = [0 => $PosArrayY];

				$Color = ["R" => $Series["Color"]["R"],"G" => $Series["Color"]["G"],"B" => $Series["Color"]["B"],"Alpha" => $Series["Color"]["Alpha"]];
				($Ticks != 0) AND $Color["Ticks"] = $Ticks;
				($Weight != 0) AND $Color["Weight"] = $Weight;

				$LastX = VOID;
				$LastY = VOID;
				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					if ($X != VOID && $Y != VOID) {
						$RealValue = round($Data["Series"][$Series["X"]]["Data"][$Key], 2) . " / " . round($Data["Series"][$Series["Y"]]["Data"][$Key], 2);
						if ($RecordImageMap) {
							$this->pChartObject->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->pChartObject->toHTMLColor($Series["Color"]["R"], $Series["Color"]["G"], $Series["Color"]["B"]) , $Description, $RealValue);
						}

						if ($LastX != VOID && $LastY != VOID){
							$this->pChartObject->drawLine($LastX, $LastY, $X, $Y, $Color);
						}
					}

					$LastX = $X;
					$LastY = $Y;
				}
			}
		}
	}

	/* Draw a scatter spline chart */
	function drawScatterSplineChart(array $Format = [])
	{
		$Data = $this->pDataObject->getData();
		$Palette = $this->pDataObject->getPalette();
		$RecordImageMap = isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : FALSE;
		$ImageMapTitle = isset($Format["ImageMapTitle"]) ? $Format["ImageMapTitle"] : NULL;
		$ImageMapPlotSize = isset($Format["ImageMapPlotSize"]) ? $Format["ImageMapPlotSize"] : 10;
		$ImageMapPrecision = isset($Format["ImageMapPrecision"]) ? $Format["ImageMapPrecision"] : 2;
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE) {
				$SerieX = $Series["X"];
				$SerieValuesX = $Data["Series"][$SerieX]["Data"];
				$SerieXAxis = $Data["Series"][$SerieX]["Axis"];
				$SerieY = $Series["Y"];
				$SerieValuesY = $Data["Series"][$SerieY]["Data"];
				$SerieYAxis = $Data["Series"][$SerieY]["Axis"];
				$Ticks = $Series["Ticks"];
				$Weight = $Series["Weight"];
				$Description = ($ImageMapTitle == NULL) ? $Data["Series"][$Series["X"]]["Description"] . " / " . $Data["Series"][$Series["Y"]]["Description"] : $ImageMapTitle;

				$PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
				if (!is_array($PosArrayX)) {
					$Value = $PosArrayX;
					$PosArrayX = [];
					$PosArrayX[0] = $Value;
				}

				$PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);
				if (!is_array($PosArrayY)) {
					$Value = $PosArrayY;
					$PosArrayY = [0 => $Value];
				}

				$SplineSettings = ["R" => $Series["Color"]["R"],"G" => $Series["Color"]["G"],"B" => $Series["Color"]["B"],"Alpha" => $Series["Color"]["Alpha"]];
				($Ticks != 0) AND $SplineSettings["Ticks"] = $Ticks;
				($Weight != 0) AND $SplineSettings["Weight"] = $Weight;

				$LastX = VOID;
				$LastY = VOID;
				$WayPoints = [];
				$Forces = [];
				foreach($PosArrayX as $Key => $Value) {
					$X = $Value;
					$Y = $PosArrayY[$Key];
					$Force = $this->pChartObject->getLength($LastX, $LastY, $X, $Y) / 5;
					if ($X != VOID && $Y != VOID) {
						$RealValue = round($Data["Series"][$Series["X"]]["Data"][$Key], 2) . " / " . round($Data["Series"][$Series["Y"]]["Data"][$Key], 2);
						if ($RecordImageMap) {
							$this->pChartObject->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->pChartObject->toHTMLColor($Series["Color"]["R"], $Series["Color"]["G"], $Series["Color"]["B"]) , $Description, $RealValue);
						}
					}

					if ($X != VOID && $Y != VOID) {
						$WayPoints[] = [$X,$Y];
						$Forces[] = $Force;
					}

					if ($Y == VOID || $X == VOID) {
						$SplineSettings["Forces"] = $Forces;
						$this->pChartObject->drawSpline($WayPoints, $SplineSettings);
						$WayPoints = [];
						$Forces = [];
					}

					$LastX = $X;
					$LastY = $Y;
				}

				$SplineSettings["Forces"] = $Forces;
				$this->pChartObject->drawSpline($WayPoints, $SplineSettings);
			}
		}
	}

	/* Return the scaled plot position */
	function getPosArray($Values, $AxisID)
	{
		$Data = $this->pDataObject->getData();
		$ScaleHeight = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];

		(!is_array($Values)) AND $Values = [$Values];
		$Result = [];

		if ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
			$Height = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1) - $Data["Axis"][$AxisID]["Margin"] * 2;
			$Step = $Height / $ScaleHeight;

			foreach($Values as $Key => $Value) {
				$Result[] = ($Value == VOID) ? VOID : $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] + ($Step * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]));
			}

		} else {
			$Height = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1) - $Data["Axis"][$AxisID]["Margin"] * 2;
			$Step = $Height / $ScaleHeight;

			foreach($Values as $Key => $Value) {
				$Result[] = ($Value == VOID) ? VOID : $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"] - ($Step * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]));
			}
		}

		return (count($Result) == 1) ? $Result[0] : $Result;

	}

	/* Draw the legend of the active series */
	function drawScatterLegend($X, $Y, array $Format = [])
	{
		$Family = LEGEND_FAMILY_BOX;
		$FontName = $this->pChartObject->FontName;
		$FontSize = $this->pChartObject->FontSize;
		$FontR = $this->pChartObject->FontColorR;
		$FontG = $this->pChartObject->FontColorG;
		$FontB = $this->pChartObject->FontColorB;
		$BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 5;
		$BoxHeight = isset($Format["BoxHeight"]) ? $Format["BoxHeight"] : 5;
		$IconAreaWidth = $BoxWidth;
		$IconAreaHeight = $BoxHeight;
		$XSpacing = 5;
		$Margin = 5;
		$R = 200;
		$G = 200;
		$B = 200;
		$Alpha = 100;
		$BorderR = 255;
		$BorderG = 255;
		$BorderB = 255;
		$Surrounding = NULL;
		$Style = LEGEND_ROUND;
		$Mode = LEGEND_VERTICAL;

		/* Override defaults */
		extract($Format);

		if ($Surrounding != NULL) {
			$BorderR = $R + $Surrounding;
			$BorderG = $G + $Surrounding;
			$BorderB = $B + $Surrounding;
		}

		$Data = $this->pDataObject->getData();
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE && isset($Series["Picture"])) {
				list($PicWidth, $PicHeight) = $this->pChartObject->getPicInfo($Series["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$YStep = max($this->pChartObject->FontSize, $IconAreaHeight) + 5;
		$XStep = $IconAreaWidth + 5;
		$XStep = $XSpacing;
		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE) {
				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->pChartObject->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Series["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
					$Lines = preg_split("/\n/", $Series["Description"]);
					$vY = $vY + max($this->pChartObject->FontSize * count($Lines) , $IconAreaHeight) + 5;

				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Lines = preg_split("/\n/", $Series["Description"]);
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->pChartObject->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + (($this->pChartObject->FontSize + 3) * $Key) , $FontName, $FontSize, 0, $Value);
						($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
						($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
						($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

						$Width[] = $BoxArray[1]["X"];
					}

					$vX = max($Width) + $XStep;
				}
			}
		}

		$vY = $vY - $YStep;
		$vX = $vX - $XStep;
		$TopOffset = $Y - $Boundaries["T"];
		if ($Boundaries["B"] - ($vY + $IconAreaHeight) < $TopOffset) {
			$Boundaries["B"] = $vY + $IconAreaHeight + $TopOffset;
		}

		if ($Style == LEGEND_ROUND) {
			$this->pChartObject->drawRoundedFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Margin, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"BorderR" => $BorderR,"BorderG" => $BorderG,"BorderB" => $BorderB]);
		} elseif ($Style == LEGEND_BOX) {
			$this->pChartObject->drawFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"BorderR" => $BorderR,"BorderG" => $BorderG,"BorderB" => $BorderB]);
		}

		$RestoreShadow = $this->pChartObject->Shadow;
		$this->Shadow = FALSE;
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE) {
				$R = $Series["Color"]["R"];
				$G = $Series["Color"]["G"];
				$B = $Series["Color"]["B"];
				$Ticks = $Series["Ticks"];
				$Weight = $Series["Weight"];
				if (isset($Series["Picture"])) {
					$Picture = $Series["Picture"];
					list($PicWidth, $PicHeight) = $this->pChartObject->getPicInfo($Picture);
					$PicX = $X + $IconAreaWidth / 2;
					$PicY = $Y + $IconAreaHeight / 2;
					$this->pChartObject->drawFromPNG($PicX - $PicWidth / 2, $PicY - $PicHeight / 2, $Picture);

				} else {
					if ($Family == LEGEND_FAMILY_BOX) {
						$XOffset = ($BoxWidth != $IconAreaWidth) ? floor(($IconAreaWidth - $BoxWidth) / 2) : 0;
						$YOffset = ($BoxHeight != $IconAreaHeight) ? floor(($IconAreaHeight - $BoxHeight) / 2) : 0;

						$this->pChartObject->drawFilledRectangle($X + 1 + $XOffset, $Y + 1 + $YOffset, $X + $BoxWidth + $XOffset + 1, $Y + $BoxHeight + 1 + $YOffset, ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 20]);
						$this->pChartObject->drawFilledRectangle($X + $XOffset, $Y + $YOffset, $X + $BoxWidth + $XOffset, $Y + $BoxHeight + $YOffset, ["R" => $R,"G" => $G,"B" => $B,"Surrounding" => 20]);
					} elseif ($Family == LEGEND_FAMILY_CIRCLE) {
						$this->pChartObject->drawFilledCircle($X + 1 + $IconAreaWidth / 2, $Y + 1 + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 20]);
						$this->pChartObject->drawFilledCircle($X + $IconAreaWidth / 2, $Y + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["R" => $R,"G" => $G,"B" => $B,"Surrounding" => 20]);
					} elseif ($Family == LEGEND_FAMILY_LINE) {
						$this->pChartObject->drawLine($X + 1, $Y + 1 + $IconAreaHeight / 2, $X + 1 + $IconAreaWidth, $Y + 1 + $IconAreaHeight / 2, ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 20,"Ticks" => $Ticks,	"Weight" => $Weight]);
						$this->pChartObject->drawLine($X, $Y + $IconAreaHeight / 2, $X + $IconAreaWidth, $Y + $IconAreaHeight / 2, ["R" => $R,"G" => $G,"B" => $B,"Ticks" => $Ticks,"Weight" => $Weight]);
					}
				}

				if ($Mode == LEGEND_VERTICAL) {
					$Lines = preg_split("/\n/", $Series["Description"]);
					foreach($Lines as $Key => $Value) $this->pChartObject->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + (($this->pChartObject->FontSize + 3) * $Key), $Value, ["R" => $FontR,"G" => $FontG,"B" => $FontB,"Align" => TEXT_ALIGN_MIDDLELEFT]);
					$Y = $Y + max($this->pChartObject->FontSize * count($Lines) , $IconAreaHeight) + 5;
				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Lines = preg_split("/\n/", $Series["Description"]);
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->pChartObject->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + (($this->pChartObject->FontSize + 3) * $Key) , $Value, ["R" => $FontR,"G" => $FontG,"B" => $FontB,"Align" => TEXT_ALIGN_MIDDLELEFT]);
						$Width[] = $BoxArray[1]["X"];
					}
					$X = max($Width) + 2 + $XStep;
				}
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Get the legend box size */
	function getScatterLegendSize(array $Format = [])
	{
		$FontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->pChartObject->FontName;
		$FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->pChartObject->FontSize;
		$BoxSize = isset($Format["BoxSize"]) ? $Format["BoxSize"] : 5;
		$Margin = isset($Format["Margin"]) ? $Format["Margin"] : 5;
		$Style = isset($Format["Style"]) ? $Format["Style"] : LEGEND_ROUND;
		$Mode = isset($Format["Mode"]) ? $Format["Mode"] : LEGEND_VERTICAL;
		$YStep = max($this->pChartObject->FontSize, $BoxSize) + 5;
		$XStep = $BoxSize + 5;
		$X = 100;
		$Y = 100;
		$Data = $this->pDataObject->getData();
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE && isset($Series["Picture"])) {
				list($PicWidth, $PicHeight) = $this->pChartObject->getPicInfo($Series["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$YStep = max($this->pChartObject->FontSize, $IconAreaHeight) + 5;
		$XStep = $IconAreaWidth + 5;
		$XStep = $XSpacing;
		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE) {
				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->pChartObject->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Series["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

					$Lines = preg_split("/\n/", $Series["Description"]);
					$vY = $vY + max($this->pChartObject->FontSize * count($Lines) , $IconAreaHeight) + 5;

				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Lines = preg_split("/\n/", $Series["Description"]);
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->pChartObject->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + (($this->pChartObject->FontSize + 3) * $Key) , $FontName, $FontSize, 0, $Value);
						($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
						($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
						($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;

						$Width[] = $BoxArray[1]["X"];
					}

					$vX = max($Width) + $XStep;
				}
			}
		}

		$vY = $vY - $YStep;
		$vX = $vX - $XStep;
		$TopOffset = $Y - $Boundaries["T"];
		($Boundaries["B"] - ($vY + $BoxSize) < $TopOffset) AND $Boundaries["B"] = $vY + $BoxSize + $TopOffset;
		$Width = ($Boundaries["R"] + $Margin) - ($Boundaries["L"] - $Margin);
		$Height = ($Boundaries["B"] + $Margin) - ($Boundaries["T"] - $Margin);

		return ["Width" => $Width,"Height" => $Height];
	}

	/* Draw the line of best fit */
	function drawScatterBestFit(array $Format = [])
	{
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 0;
		$Data = $this->pDataObject->getData();
		foreach($Data["ScatterSeries"] as $Key => $Series) {
			if ($Series["isDrawable"] == TRUE) {
				$SerieX = $Series["X"];
				$SerieValuesX = $Data["Series"][$SerieX]["Data"];
				$SerieXAxis = $Data["Series"][$SerieX]["Axis"];
				$SerieY = $Series["Y"];
				$SerieValuesY = $Data["Series"][$SerieY]["Data"];
				$SerieYAxis = $Data["Series"][$SerieY]["Axis"];
				$Color = ["R" => $Series["Color"]["R"],"G" => $Series["Color"]["G"],"B" => $Series["Color"]["B"],"Alpha" => $Series["Color"]["Alpha"]];
				$Color["Ticks"] = $Ticks;
				$PosArrayX = $Data["Series"][$Series["X"]]["Data"];
				$PosArrayY = $Data["Series"][$Series["Y"]]["Data"];
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
				if ((($n * $Sxx) == ($Sx * $Sx))) {
					$X1 = $this->getPosArray($Data["Axis"][$SerieXAxis]["ScaleMin"], $SerieXAxis);
					$X2 = $X1;
					$Y1 = $this->pChartObject->GraphAreaY1;
					$Y2 = $this->pChartObject->GraphAreaY2;
				} else {
					$M = (($n * $Sxy) - ($Sx * $Sy)) / (($n * $Sxx) - ($Sx * $Sx));
					$B = (($Sy) - ($M * $Sx)) / ($n);
					$X1 = $this->getPosArray($Data["Axis"][$SerieXAxis]["ScaleMin"], $SerieXAxis);
					$Y1 = $this->getPosArray($M * $Data["Axis"][$SerieXAxis]["ScaleMin"] + $B, $SerieYAxis);
					$X2 = $this->getPosArray($Data["Axis"][$SerieXAxis]["ScaleMax"], $SerieXAxis);
					$Y2 = $this->getPosArray($M * $Data["Axis"][$SerieXAxis]["ScaleMax"] + $B, $SerieYAxis);
					$RealM = - ($Y2 - $Y1) / ($X2 - $X1);
					if ($Y1 < $this->pChartObject->GraphAreaY1) {
						$X1 = $X1 + ($this->pChartObject->GraphAreaY1 - $Y1 / $RealM);
						$Y1 = $this->pChartObject->GraphAreaY1;
					}

					if ($Y1 > $this->pChartObject->GraphAreaY2) {
						$X1 = $X1 + ($Y1 - $this->pChartObject->GraphAreaY2) / $RealM;
						$Y1 = $this->pChartObject->GraphAreaY2;
					}

					if ($Y2 < $this->pChartObject->GraphAreaY1) {
						$X2 = $X2 - ($this->pChartObject->GraphAreaY1 - $Y2) / $RealM;
						$Y2 = $this->pChartObject->GraphAreaY1;
					}

					if ($Y2 > $this->pChartObject->GraphAreaY2) {
						$X2 = $X2 - ($Y2 - $this->pChartObject->GraphAreaY2) / $RealM;
						$Y2 = $this->pChartObject->GraphAreaY2;
					}
				}

				$this->pChartObject->drawLine($X1, $Y1, $X2, $Y2, $Color);
			}
		}
	}

	function writeScatterLabel($ScatterSerieID, $Points, array $Format = [])
	{
		$Data = $this->pDataObject->getData();
		$Palette = $this->pDataObject->getPalette();

		if (!isset($Data["ScatterSeries"][$ScatterSerieID])) {
			return (0);
		}

		(!is_array($Points)) AND $Points = [$Points];
		$OverrideTitle = isset($Format["OverrideTitle"]) ? $Format["OverrideTitle"] : NULL;
		$DrawPoint = isset($Format["DrawPoint"]) ? $Format["DrawPoint"] : LABEL_POINT_BOX;
		$Decimals = isset($Format["Decimals"]) ? $Format["Decimals"] : NULL;

		$Series = $Data["ScatterSeries"][$ScatterSerieID];
		$SerieX = $Series["X"];
		$SerieValuesX = $Data["Series"][$SerieX]["Data"];
		$SerieXAxis = $Data["Series"][$SerieX]["Axis"];
		$SerieY = $Series["Y"];
		$SerieValuesY = $Data["Series"][$SerieY]["Data"];
		$SerieYAxis = $Data["Series"][$SerieY]["Axis"];

		$PosArrayX = $this->getPosArray($SerieValuesX, $SerieXAxis);
		(!is_array($PosArrayX)) AND $PosArrayX = [0 => $PosArrayX];

		$PosArrayY = $this->getPosArray($SerieValuesY, $SerieYAxis);
		(!is_array($PosArrayY)) AND $PosArrayY = [0 => $PosArrayY];

		foreach($Points as $Key => $Point) {
			if (isset($PosArrayX[$Point]) && isset($PosArrayY[$Point])) {
				$X = floor($PosArrayX[$Point]);
				$Y = floor($PosArrayY[$Point]);
				if ($DrawPoint == LABEL_POINT_CIRCLE) {
					$this->pChartObject->drawFilledCircle($X, $Y, 3, ["R" => 255,"G" => 255,"B" => 255,"BorderR" => 0,"BorderG" => 0,"BorderB" => 0]);
				} elseif ($DrawPoint == LABEL_POINT_BOX) {
					$this->pChartObject->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["R" => 255,"G" => 255,"B" => 255,"BorderR" => 0,"BorderG" => 0,"BorderB" => 0]);
				}

				$XValue = ($Decimals == NULL) ? $SerieValuesX[$Point] : round($SerieValuesX[$Point], $Decimals);
				$XValue = $this->pChartObject->scaleFormat($XValue, $Data["Axis"][$SerieXAxis]["Display"], $Data["Axis"][$SerieXAxis]["Format"], $Data["Axis"][$SerieXAxis]["Unit"]);

				$YValue = ($Decimals == NULL) ? $SerieValuesY[$Point] : round($SerieValuesY[$Point], $Decimals);
				$YValue = $this->pChartObject->scaleFormat($YValue, $Data["Axis"][$SerieYAxis]["Display"], $Data["Axis"][$SerieYAxis]["Format"], $Data["Axis"][$SerieYAxis]["Unit"]);

				$Description = (isset($Series["Description"])) ? $Series["Description"] : "No description";
				$Series = ["Format" => ["R" => $Series["Color"]["R"],"G" => $Series["Color"]["G"],"B" => $Series["Color"]["B"],"Alpha" => $Series["Color"]["Alpha"]],"Caption" => $XValue . " / " . $YValue];
				$this->pChartObject->drawLabelBox($X, $Y - 3, $Description, $Series, $Format);
			}
		}
	}

	/* Draw a Scatter threshold */
	function drawScatterThreshold($Value, array $Format = [])
	{

		$AxisID = 0;
		$R = 255;
		$G = 0;
		$B = 0;
		$Alpha = 50;
		$Weight = NULL;
		$Ticks = 3;
		$Wide = FALSE;
		$WideFactor = 5;
		$WriteCaption = FALSE;
		$Caption = NULL;
		$CaptionAlign = CAPTION_LEFT_TOP;
		$CaptionOffset = 10;
		$CaptionR = 255;
		$CaptionG = 255;
		$CaptionB = 255;
		$CaptionAlpha = 100;
		$DrawBox = TRUE;
		$DrawBoxBorder = FALSE;
		$BorderOffset = 5;
		$BoxRounded = TRUE;
		$RoundedRadius = 3;
		$BoxR = 0;
		$BoxG = 0;
		$BoxB = 0;
		$BoxAlpha = 20;
		$BoxSurrounding = "";
		$BoxBorderR = 255;
		$BoxBorderG = 255;
		$BoxBorderB = 255;
		$BoxBorderAlpha = 100;

		/* Override defaults */
		extract($Format);

		$Data = $this->pDataObject->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			return (-1);
		}

		($Caption == NULL) AND $Caption = $Value;
		$CaptionSettings = [
			"DrawBox" => $DrawBox,
			"DrawBoxBorder" => $DrawBoxBorder,
			"BorderOffset" => $BorderOffset,
			"BoxRounded" => $BoxRounded,
			"RoundedRadius" => $RoundedRadius,
			"BoxR" => $BoxR,
			"BoxG" => $BoxG,
			"BoxB" => $BoxB,
			"BoxAlpha" => $BoxAlpha,
			"BoxSurrounding" => $BoxSurrounding,
			"BoxBorderR" => $BoxBorderR,
			"BoxBorderG" => $BoxBorderG,
			"BoxBorderB" => $BoxBorderB,
			"BoxBorderAlpha" => $BoxBorderAlpha,
			"R" => $CaptionR,
			"G" => $CaptionG,
			"B" => $CaptionB,
			"Alpha" => $CaptionAlpha
		];

		if ($Data["Axis"][$AxisID]["Identity"] == AXIS_Y) {
			$X1 = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
			$X2 = $this->pChartObject->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
			$Y = $this->getPosArray($Value, $AxisID);
			$this->pChartObject->drawLine($X1, $Y, $X2, $Y, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
			if ($Wide) {
				$this->pChartObject->drawLine($X1, $Y - 1, $X2, $Y - 1, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
				$this->pChartObject->drawLine($X1, $Y + 1, $X2, $Y + 1, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
			}

			if ($WriteCaption) {
				if ($CaptionAlign == CAPTION_LEFT_TOP) {
					$X = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] + $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
				} else {
					$X = $this->pChartObject->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"] - $CaptionOffset;
					$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
				}

				$this->pChartObject->drawText($X, $Y, $Caption, $CaptionSettings);
			}

			return ["Y" => $Y];

		} elseif ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
			$X = $this->getPosArray($Value, $AxisID);
			$Y1 = $this->pChartObject->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
			$Y2 = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
			$this->pChartObject->drawLine($X, $Y1, $X, $Y2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
			if ($Wide) {
				$this->pChartObject->drawLine($X - 1, $Y1, $X - 1, $Y2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
				$this->pChartObject->drawLine($X + 1, $Y1, $X + 1, $Y2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
			}

			if ($WriteCaption) {
				if ($CaptionAlign == CAPTION_LEFT_TOP) {
					$Y = $this->pChartObject->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"] + $CaptionOffset;
					#$CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE; # POINTLESS
				} else {
					$Y = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"] - $CaptionOffset;
					#$CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE; # POINTLESS
				}

				$CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
				$this->pChartObject->drawText($X, $Y, $Caption, $CaptionSettings);
			}

			return ["X" => $X];
		}
	}

	/* Draw a Scatter threshold area */
	function drawScatterThresholdArea($Value1, $Value2, array $Format = [])
	{
		$AxisID = 0;
		$R = isset($Format["R"]) ? $Format["R"] : 255;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 20;
		$Border = TRUE;
		$BorderR = $R;
		$BorderG = $G;
		$BorderB = $B;
		$BorderAlpha = $Alpha + 20;
		$BorderTicks = 2;
		$AreaName = "La ouate de phoque"; //NULL;
		$NameAngle = ZONE_NAME_ANGLE_AUTO;
		$NameR = 255;
		$NameG = 255;
		$NameB = 255;
		$NameAlpha = 100;
		$DisableShadowOnArea = TRUE;

		/* Override defaults */
		extract($Format);

		$Data = $this->pDataObject->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			return (-1);
		}

		($BorderAlpha > 100) AND $BorderAlpha = 100;

		if ($Value1 > $Value2) {
			list($Value1, $Value2) = [$Value2,$Value1];
		}

		$RestoreShadow = $this->pChartObject->Shadow;
		if ($DisableShadowOnArea && $this->pChartObject->Shadow) {
			$this->pChartObject->Shadow = FALSE;
		}

		if ($Data["Axis"][$AxisID]["Identity"] == AXIS_X) {
			$Y1 = $this->pChartObject->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
			$Y2 = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
			$X1 = $this->getPosArray($Value1, $AxisID);
			$X2 = $this->getPosArray($Value2, $AxisID);
			if ($X1 <= $this->pChartObject->GraphAreaX1) {
				$X1 = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
			}

			if ($X2 >= $this->pChartObject->GraphAreaX2) {
				$X2 = $this->pChartObject->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
			}

			$this->pChartObject->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha]);

			if ($Border) {
				$this->pChartObject->drawLine($X1, $Y1, $X1, $Y2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
				$this->pChartObject->drawLine($X2, $Y1, $X2, $Y2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
			}

			if ($AreaName != NULL) {
				$XPos = ($X2 - $X1) / 2 + $X1;
				$YPos = ($Y2 - $Y1) / 2 + $Y1;
				if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
					$TxtPos = $this->pChartObject->getTextBox($XPos, $YPos, $this->pChartObject->FontName, $this->pChartObject->FontSize, 0, $AreaName);
					$TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
					$NameAngle = (abs($X2 - $X1) > $TxtWidth) ? 0 : 90;
				}

				$this->pChartObject->Shadow = $RestoreShadow;
				$this->pChartObject->drawText($XPos, $YPos, $AreaName, ["R" => $NameR,"G" => $NameG,"B" => $NameB,"Alpha" => $NameAlpha,"Angle" => $NameAngle,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				($DisableShadowOnArea) AND $this->pChartObject->Shadow = FALSE;
			}

			$this->pChartObject->Shadow = $RestoreShadow;

			return ["X1" => $X1,"X2" => $X2];

		} elseif ($Data["Axis"][$AxisID]["Identity"] == AXIS_Y) {

			$X1 = $this->pChartObject->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
			$X2 = $this->pChartObject->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
			$Y1 = $this->getPosArray($Value1, $AxisID);
			$Y2 = $this->getPosArray($Value2, $AxisID);
			if ($Y1 >= $this->pChartObject->GraphAreaY2) {
				$Y1 = $this->pChartObject->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
			}

			if ($Y2 <= $this->pChartObject->GraphAreaY1) {
				$Y2 = $this->pChartObject->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
			}

			$this->pChartObject->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha]);
			if ($Border) {
				$this->pChartObject->drawLine($X1, $Y1, $X2, $Y1, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
				$this->pChartObject->drawLine($X1, $Y2, $X2, $Y2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
			}

			if ($AreaName != NULL) {
				$XPos = ($X2 - $X1) / 2 + $X1;
				$YPos = ($Y2 - $Y1) / 2 + $Y1;
				$this->pChartObject->Shadow = $RestoreShadow;
				$this->pChartObject->drawText($YPos, $XPos, $AreaName, ["R" => $NameR,"G" => $NameG,"B" => $NameB,"Alpha" => $NameAlpha,"Angle" => 0,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				($DisableShadowOnArea) AND $this->Shadow = FALSE;
			}

			$this->pChartObject->Shadow = $RestoreShadow;

			return ["Y1" => $Y1,"Y2" => $Y2];
		}
	}
}

?>