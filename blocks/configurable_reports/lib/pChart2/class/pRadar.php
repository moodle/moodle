<?php
/*
pRadar - class to draw radar charts

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 17/10/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

define("SEGMENT_HEIGHT_AUTO", 690001);
define("RADAR_LAYOUT_STAR", 690011);
define("RADAR_LAYOUT_CIRCLE", 690012);
define("RADAR_LABELS_ROTATED", 690021);
define("RADAR_LABELS_HORIZONTAL", 690022);

/* pRadar class definition */
class pRadar
{
	private $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Draw a radar chart */
	public function drawRadar(array $Format = [])
	{
		$fontProperties = $this->myPicture->getFont();
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		$FixedMax = VOID;
		$AxisColor = new pColor(60,60,60,50);
		$AxisRotation = 0;
		$DrawTicks =  TRUE;
		$TicksLength = 2;
		$DrawAxisValues = TRUE;
		$AxisBoxRounded = TRUE;
		$AxisFontName = $fontProperties['Name'];
		$AxisFontSize = $fontProperties['Size'];
		$DrawBackground = TRUE;
		$BackgroundColor = new pColor(255,255,255,50);
		$BackgroundGradient = NULL;
		$Layout = RADAR_LAYOUT_STAR;
		$SegmentHeight = SEGMENT_HEIGHT_AUTO;
		$Segments = 4;
		$WriteLabels = TRUE;
		$SkipLabels = 1;
		$LabelMiddle = FALSE;
		$LabelsBackground = TRUE;
		$LabelsBackgroundColor = new pColor(255,255,255,50);
		$LabelPos = RADAR_LABELS_ROTATED;
		$LabelPadding = 4;
		$LineLoopStart = TRUE;
		$PolyAlpha = 40;
		$FontSize = $fontProperties['Size'];
		$X1 = $GraphAreaCoordinates["L"];
		$Y1 = $GraphAreaCoordinates["T"];
		$X2 = $GraphAreaCoordinates["R"];
		$Y2 = $GraphAreaCoordinates["B"];

		/* Override defaults */
		extract($Format);

		$Format["PolyAlpha"] = $PolyAlpha;
		$Format["LineLoopStart"] = $LineLoopStart;

		/* Cancel default tick length if ticks not enabled */
		($DrawTicks == FALSE) AND $TicksLength = 0;

		/* Data Processing */
		$Data = $this->myPicture->myData->getData();

		/* Catch the number of required axis */
		$LabelSerie = $Data["Abscissa"];
		if (!is_null($LabelSerie)) {
			$Points = count($Data["Series"][$LabelSerie]["Data"]);
		} else {
			$Points = 0;
			foreach($Data["Series"] as $SerieName => $DataArray) {
				if (count($DataArray["Data"]) > $Points) {
					$Points = count($DataArray["Data"]);
				}
			}
		}

		$Step = 360 / $Points;

		/* Draw the axis */
		$CenterX = ($X2 + $X1) / 2;
		$CenterY = ($Y2 + $Y1) / 2;
		$EdgeHeight = min(($X2 - $X1) / 2, ($Y2 - $Y1) / 2);
		if ($WriteLabels) {
			$EdgeHeight = $EdgeHeight - $FontSize - $LabelPadding - $TicksLength;
		}

		/* Determine the scale if set to automatic */
		if ($SegmentHeight == SEGMENT_HEIGHT_AUTO) {
			if ($FixedMax != VOID) {
				$Max = $FixedMax;
			} else {
				$Max = 0;
				foreach($Data["Series"] as $SerieName => $DataArray) {
					if ($SerieName != $LabelSerie) {
						if (max($DataArray["Data"]) > $Max) {
							$Max = max($DataArray["Data"]);
						}
					}
				}
			}

			$MaxSegments = $EdgeHeight / 20;
			$Scale = $this->myPicture->computeScale(0, $Max, $MaxSegments, [1,2,5]);
			$Segments = $Scale["Rows"];
			$SegmentHeight = $Scale["RowHeight"];
		}

		$Axisoffset = ($LabelMiddle) ? ($Step * $SkipLabels) / 2 : 0;

		/* Background processing */
		if ($DrawBackground) {

			$ShadowSpec = $this->myPicture->getShadow();
			$this->myPicture->setShadow(FALSE);

			if (!is_array($BackgroundGradient)) {
				if ($Layout == RADAR_LAYOUT_STAR) {

					$PointArray = [];
					for ($i = 0; $i <= 360; $i += $Step) {
						$PointArray[] = cos(deg2rad($i + $AxisRotation)) * $EdgeHeight + $CenterX;
						$PointArray[] = sin(deg2rad($i + $AxisRotation)) * $EdgeHeight + $CenterY;
					}

					$this->myPicture->drawPolygon($PointArray, ["Color" => $BackgroundColor]);

				} elseif ($Layout == RADAR_LAYOUT_CIRCLE) {;
					$this->myPicture->drawFilledCircle($CenterX, $CenterY, $EdgeHeight, ["Color" => $BackgroundColor]);
				}
			} else {

				$GradientColor = new pColorGradient($BackgroundGradient["StartColor"], $BackgroundGradient["EndColor"]);
				$GradientColor->setSegments($Segments);

				if ($Layout == RADAR_LAYOUT_STAR) {
					for ($j = $Segments; $j >= 1; $j--) {
						$PointArray = [];
						for ($i = 0; $i <= 360; $i += $Step) {
							$PointArray[] = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
							$PointArray[] = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
						}
						$this->myPicture->drawPolygon($PointArray, ["Color" => $GradientColor->getStep($j)]);
					}
				} elseif ($Layout == RADAR_LAYOUT_CIRCLE) {
					for ($j = $Segments; $j >= 1; $j--) {
						$this->myPicture->drawFilledCircle($CenterX, $CenterY, ($EdgeHeight / $Segments) * $j, ["Color" => $GradientColor->getStep($j)]);
					}
				}
			}

			$this->myPicture->restoreShadow($ShadowSpec);
		}

		/* Axis to axis lines */
		$Color = ["Color" => $AxisColor];
		$ColorDotted = ["Color" => $AxisColor->newOne()->AlphaMultiply(.8),"Ticks" => 2];

		if ($Layout == RADAR_LAYOUT_STAR) {
			for ($j = 1; $j <= $Segments; $j++) {
				for ($i = 0; $i < 360; $i += $Step) {
					$EdgeX1 = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY1 = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
					$EdgeX2 = cos(deg2rad($i + $AxisRotation + $Step)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY2 = sin(deg2rad($i + $AxisRotation + $Step)) * ($EdgeHeight / $Segments) * $j + $CenterY;
					$this->myPicture->drawLine($EdgeX1, $EdgeY1, $EdgeX2, $EdgeY2, $Color);
				}
			}
		} elseif ($Layout == RADAR_LAYOUT_CIRCLE) {
			for ($j = 1; $j <= $Segments; $j++) {
				$Radius = ($EdgeHeight / $Segments) * $j;
				$this->myPicture->drawCircle($CenterX, $CenterY, $Radius, $Radius, $Color);
			}
		}

		if ($DrawAxisValues) {
			if ($LabelsBackground) {
				$Options = ["DrawBox" => TRUE,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"BoxColor" => $LabelsBackgroundColor];
			} else {
				$Options = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE];
			}

			if ($AxisBoxRounded) {
				$Options["BoxRounded"] = TRUE;
			}

			$Options["FontName"] = $AxisFontName;
			$Options["FontSize"] = $AxisFontSize;
			$Angle = $Step / 2;

			for ($j = 1; $j <= $Segments; $j++) {
				$Label = $j * $SegmentHeight;
				if ($Layout == RADAR_LAYOUT_CIRCLE) {
					$EdgeX1 = cos(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY1 = sin(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
				} elseif ($Layout == RADAR_LAYOUT_STAR) {
					$EdgeX1 = cos(deg2rad($AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY1 = sin(deg2rad($AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
					$EdgeX2 = cos(deg2rad($Step + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
					$EdgeY2 = sin(deg2rad($Step + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
					$EdgeX1 = ($EdgeX2 + $EdgeX1) / 2;
					$EdgeY1 = ($EdgeY2 + $EdgeY1) / 2;
				}

				$this->myPicture->drawText($EdgeX1, $EdgeY1, $Label, $Options);
			}
		}

		/* Axis lines */
		$ID = 0;
		for ($i = 0; $i < 360; $i += $Step) {
			$EdgeX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterX;
			$EdgeY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterY;
			if ($ID % $SkipLabels == 0) {
				$this->myPicture->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $Color);
			} else {
				$this->myPicture->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $ColorDotted);
			}

			if ($WriteLabels) {
				$LabelX = cos(deg2rad($i + $AxisRotation + $Axisoffset)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterX;
				$LabelY = sin(deg2rad($i + $AxisRotation + $Axisoffset)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterY;
				if (!is_null($LabelSerie)) {
					$Label = isset($Data["Series"][$LabelSerie]["Data"][$ID]) ? $Data["Series"][$LabelSerie]["Data"][$ID] : "";
				} else {
					$Label = $ID;
				}

				if ($ID % $SkipLabels == 0) {
					if ($LabelPos == RADAR_LABELS_ROTATED) {
						$Align = ["Angle" => (360 - ($i + $AxisRotation + $Axisoffset)) - 90,"Align" => TEXT_ALIGN_BOTTOMMIDDLE];
					} else { # RADAR_LABELS_HORIZONTAL
						switch (TRUE) {
							case ((floor($LabelX) == floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_BOTTOMMIDDLE];
								break;
							case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_BOTTOMLEFT];
								break;
							case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) == floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_MIDDLELEFT];
								break;
							case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_TOPLEFT];
								break;
							case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_BOTTOMRIGHT];
								break;
							case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) == floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_MIDDLERIGHT];
								break;
							case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_TOPRIGHT];
								break;
							case ((floor($LabelX) == floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
								$Align = ["Align" => TEXT_ALIGN_TOPMIDDLE];
								break;
						}
					}

					$this->myPicture->drawText($LabelX, $LabelY, $Label, $Align);
				}
			}

			$ID++;
		}

		/* Compute the plots position */
		$ID = 0;
		$Plot = [];
		foreach($Data["Series"] as $SerieName => $DataSet) {
			if ($SerieName != $LabelSerie) {

				foreach($DataSet["Data"] as $Key => $Value) {
					$Angle = $Step * $Key;
					$Length = ($EdgeHeight / ($Segments * $SegmentHeight)) * $Value;
					$X = cos(deg2rad($Angle + $AxisRotation)) * $Length + $CenterX;
					$Y = sin(deg2rad($Angle + $AxisRotation)) * $Length + $CenterY;
					$Plot[$ID][] = [$X,$Y,$Value];
				}

				$ID++;
			}
		}

		/* Draw all that stuff! */
		$this->ProcessPoints($Plot, $Format);
	}

	/* Draw a radar chart */
	public function drawPolar(array $Format = [])
	{
		$fontProperties = $this->myPicture->getFont();
		$GraphAreaCoordinates = $this->myPicture->getGraphAreaCoordinates();

		$FixedMax = VOID;
		$AxisColor = new pColor(60,60,60,50);
		$AxisRotation = -90;
		$DrawTicks = TRUE;
		$TicksLength = 2;
		$DrawAxisValues = TRUE;
		$AxisBoxRounded = TRUE;
		$AxisFontName = isset($Format["FontName"]) ? $Format["FontName"] : $fontProperties['Name'];
		$AxisFontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $fontProperties['Size'];
		$DrawBackground = TRUE;
		$BackgroundColor = new pColor(255,255,255,50);
		$BackgroundGradient = NULL;
		$AxisSteps = 20;
		$SegmentHeight = SEGMENT_HEIGHT_AUTO;
		$Segments = 4;
		$WriteLabels = TRUE;
		$LabelsBackground = TRUE;
		$LabelsBackgroundColor = new pColor(255,255,255,50);
		$LabelPos = RADAR_LABELS_ROTATED;
		$LabelPadding = 4;
		$LineLoopStart = FALSE;
		$PolyAlpha = NULL;
		$FontSize = $fontProperties['Size'];
		$X1 = $GraphAreaCoordinates["L"];
		$Y1 = $GraphAreaCoordinates["T"];
		$X2 = $GraphAreaCoordinates["R"];
		$Y2 = $GraphAreaCoordinates["B"];

		/* Override defaults */
		extract($Format);

		$Format["PolyAlpha"] = $PolyAlpha;
		$Format["LineLoopStart"] = $LineLoopStart;

		($AxisBoxRounded) AND $DrawAxisValues = TRUE;

		/* Cancel default tick length if ticks not enabled */
		($DrawTicks == FALSE) AND $TicksLength = 0;

		/* Data Processing */
		$Data = $this->myPicture->myData->getData();

		/* Catch the number of required axis */
		$LabelSerie = $Data["Abscissa"];
		if (!is_null($LabelSerie)) {
			$Points = count($Data["Series"][$LabelSerie]["Data"]);
		} else {
			$Points = 0;
			foreach($Data["Series"] as $SerieName => $DataArray) {
				(count($DataArray["Data"]) > $Points) AND $Points = count($DataArray["Data"]);
			}
		}

		/* Draw the axis */
		$CenterX = ($X2 + $X1) / 2;
		$CenterY = ($Y2 + $Y1) / 2;
		$EdgeHeight = min(($X2 - $X1) / 2, ($Y2 - $Y1) / 2);
		if ($WriteLabels) {
			$EdgeHeight = $EdgeHeight - $FontSize - $LabelPadding - $TicksLength;
		}

		/* Determine the scale if set to automatic */
		if ($SegmentHeight == SEGMENT_HEIGHT_AUTO) {
			if ($FixedMax != VOID) {
				$Max = $FixedMax;
			} else {
				$Max = 0;
				foreach($Data["Series"] as $SerieName => $DataArray) {
					if ($SerieName != $LabelSerie) {
						if (max($DataArray["Data"]) > $Max) {
							$Max = max($DataArray["Data"]);
						}
					}
				}
			}

			$MaxSegments = $EdgeHeight / 20;
			$Scale = $this->myPicture->computeScale(0, $Max, $MaxSegments, [1,2,5]);
			$Segments = $Scale["Rows"];
			$SegmentHeight = $Scale["RowHeight"];
		}

		/* Background processing */
		if ($DrawBackground) {

			$ShadowSpec = $this->myPicture->getShadow();
			$this->myPicture->setShadow(FALSE);

			if (!is_array($BackgroundGradient)) {
				$this->myPicture->drawFilledCircle($CenterX, $CenterY, $EdgeHeight, ["Color" => $BackgroundColor]);
			} else {
				$GradientColor = new pColorGradient($BackgroundGradient["StartColor"], $BackgroundGradient["EndColor"]);
				$GradientColor->setSegments($Segments);
				for ($j = $Segments; $j >= 1; $j--) {
					$this->myPicture->drawFilledCircle($CenterX, $CenterY, ($EdgeHeight / $Segments) * $j, ["Color" => $GradientColor->getStep($j)]);
				}
			}

			$this->myPicture->restoreShadow($ShadowSpec);
		}

		/* Axis to axis lines */
		$Color = ["Color" => $AxisColor];
		for ($j = 1; $j <= $Segments; $j++) {
			$Radius = ($EdgeHeight / $Segments) * $j;
			$this->myPicture->drawCircle($CenterX, $CenterY, $Radius, $Radius, $Color);
		}

		if ($DrawAxisValues) {
			if ($LabelsBackground) {
				$Options = ["DrawBox" => TRUE,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"BoxColor" => $LabelsBackgroundColor];
			} else {
				$Options = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE];
			}

			($AxisBoxRounded) AND $Options["BoxRounded"] = TRUE;

			$Options["FontName"] = $AxisFontName;
			$Options["FontSize"] = $AxisFontSize;
			$Angle = 360 / ($Points * 2);
			for ($j = 1; $j <= $Segments; $j++) {
				$EdgeX1 = cos(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterX;
				$EdgeY1 = sin(deg2rad($Angle + $AxisRotation)) * ($EdgeHeight / $Segments) * $j + $CenterY;
				$Label = $j * $SegmentHeight;
				$this->myPicture->drawText($EdgeX1, $EdgeY1, $Label, $Options);
			}
		}

		/* Axis lines */
		$ID = 0;
		for ($i = 0; $i <= 359; $i = $i + $AxisSteps) {
			$EdgeX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterX;
			$EdgeY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $TicksLength) + $CenterY;
			$this->myPicture->drawLine($CenterX, $CenterY, $EdgeX, $EdgeY, $Color);
			if ($WriteLabels) {
				$LabelX = cos(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterX;
				$LabelY = sin(deg2rad($i + $AxisRotation)) * ($EdgeHeight + $LabelPadding + $TicksLength) + $CenterY;
				$Label = $i . "°";

				if ($LabelPos == RADAR_LABELS_ROTATED) {
					$Align = ["Angle" => (360 - $i),"Align" => TEXT_ALIGN_BOTTOMMIDDLE];
				} else { # RADAR_LABELS_HORIZONTAL
					switch (TRUE) {
						case ((floor($LabelX) == floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_BOTTOMMIDDLE];
							break;
						case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_BOTTOMLEFT];
							break;
						case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) == floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_MIDDLELEFT];
							break;
						case ((floor($LabelX) > floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_TOPLEFT];
							break;
						case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) < floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_BOTTOMRIGHT];
							break;
						case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) == floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_MIDDLERIGHT];
							break;
						case ((floor($LabelX) < floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_TOPRIGHT];
							break;
						case ((floor($LabelX) == floor($CenterX)) && (floor($LabelY) > floor($CenterY))):
							$Align = ["Align" => TEXT_ALIGN_TOPMIDDLE];
							break;
					}
				}

				$this->myPicture->drawText($LabelX, $LabelY, $Label, $Align);
			}

			$ID++;
		}

		/* Compute the plots position */
		$ID = 0;
		$Plot = [];
		foreach($Data["Series"] as $SerieName => $DataSet) {
			if ($SerieName != $LabelSerie) {

				foreach($DataSet["Data"] as $Key => $Value) {
					$Angle = $Data["Series"][$LabelSerie]["Data"][$Key];
					$Length = ($EdgeHeight / ($Segments * $SegmentHeight)) * $Value;
					$X = cos(deg2rad($Angle + $AxisRotation)) * $Length + $CenterX;
					$Y = sin(deg2rad($Angle + $AxisRotation)) * $Length + $CenterY;
					$Plot[$ID][] = [$X,$Y,$Value];
				}

				$ID++;
			}
		}

		/* Draw all that stuff! */
		$this->ProcessPoints($Plot, $Format);
	}

	private function ProcessPoints($Plot, $Format)
	{
		$fontProperties = $this->myPicture->getFont();

		$PointRadius = isset($Format["PointRadius"]) ? $Format["PointRadius"] : 4;
		$PointSurrounding = isset($Format["PointSurrounding"]) ? $Format["PointSurrounding"] : -30;
		$ValueFontName = isset($Format["ValueFontName"]) ? $Format["ValueFontName"] : $fontProperties['Name'];
		$ValueFontSize = isset($Format["ValueFontSize"]) ? $Format["ValueFontSize"] : $fontProperties['Size'];
		$ValuePadding = isset($Format["ValuePadding"]) ? $Format["ValuePadding"] : 4;
		$OuterBubbleRadius = isset($Format["OuterBubbleRadius"]) ? $Format["OuterBubbleRadius"] : 2;
		$OuterBubbleColor = isset($Format["OuterBubbleColor"]) ? $Format["OuterBubbleColor"] : NULL;
		$InnerBubbleColor = isset($Format["InnerBubbleColor"]) ? $Format["InnerBubbleColor"] : new pColor(255);
		$DrawLines = isset($Format["DrawLines"]) ? $Format["DrawLines"] : TRUE;
		$DrawPoints = isset($Format["DrawPoints"]) ? $Format["DrawPoints"] : TRUE;
		$LineLoopStart = isset($Format["LineLoopStart"]) ? $Format["LineLoopStart"] : FALSE;
		$DrawPoly = isset($Format["DrawPoly"]) ? $Format["DrawPoly"] : FALSE;
		$PolyAlpha = isset($Format["PolyAlpha"]) ? $Format["PolyAlpha"] : NULL;
		$WriteValues = isset($Format["WriteValues"]) ? $Format["WriteValues"] : FALSE;
		$WriteValuesInBubble = isset($Format["WriteValuesInBubble"]) ? $Format["WriteValuesInBubble"] : TRUE;

		$Palette = $this->myPicture->myData->getPalette();

		foreach($Plot as $ID => $Points) {

			$PolygonSettings = ["Color" => $Palette[$ID],"Surrounding" => $PointSurrounding];
			$PointCount = count($Points);

			/* Draw the polygons */
			if ($DrawPoly) {
				if (!is_null($PolyAlpha)) {
					$PolygonSettings["Color"] = $Palette[$ID]->newOne()->AlphaSet($PolyAlpha);
				}

				$PointsArray = [];
				for ($i = 0; $i < $PointCount; $i++) {
					$PointsArray[] = $Points[$i][0];
					$PointsArray[] = $Points[$i][1];
				}

				$this->myPicture->drawPolygon($PointsArray, $PolygonSettings);
			}

			/* Bubble and labels settings */
			$PolygonSettings["Color"] = $Palette[$ID];
			$TextSettings = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontName" => $ValueFontName,"FontSize" => $ValueFontSize,"Color" => $Palette[$ID]];
			$InnerColor = ["Color" => $InnerBubbleColor];
			$OuterColor = ["Color" => (!is_null($OuterBubbleColor)) ? $OuterBubbleColor : $Palette[$ID]->newOne()->RGBChange(20)];

			/* Loop to the starting points if asked */
			if ($LineLoopStart && $DrawLines) {
				$this->myPicture->drawLine($Points[$PointCount - 1][0], $Points[$PointCount - 1][1], $Points[0][0], $Points[0][1], $PolygonSettings);
			}

			/* Draw the lines & points */
			for ($i = 0; $i < $PointCount; $i++) {
				if ($DrawLines && $i < $PointCount - 1) {
					$this->myPicture->drawLine($Points[$i][0], $Points[$i][1], $Points[$i + 1][0], $Points[$i + 1][1], $PolygonSettings);
				}

				if ($DrawPoints) {
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $PointRadius, $PolygonSettings);
				}

				if ($WriteValuesInBubble && $WriteValues) {
					$TxtPos = $this->myPicture->getTextBox($Points[$i][0], $Points[$i][1], $ValueFontName, $ValueFontSize, 0, $Points[$i][2]);
					$Radius = floor(($TxtPos[1]["X"] - $TxtPos[0]["X"] + $ValuePadding * 2) / 2);
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius + $OuterBubbleRadius, $OuterColor);
					$this->myPicture->drawFilledCircle($Points[$i][0], $Points[$i][1], $Radius, $InnerColor);
				}

				if ($WriteValues) {
					#Momchil: Visual fix applied
					$this->myPicture->drawText($Points[$i][0] + 1, $Points[$i][1], $Points[$i][2], $TextSettings);
				}
			}
		}

	}
}
