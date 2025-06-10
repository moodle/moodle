<?php
/*
pPie - class to draw pie charts

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

/* Class return codes */
define("PIE_LABEL_COLOR_AUTO", 140010);
define("PIE_LABEL_COLOR_MANUAL", 140011);
define("PIE_VALUE_NATURAL", 140020);
define("PIE_VALUE_PERCENTAGE", 140021);
define("PIE_VALUE_INSIDE", 140030);
define("PIE_VALUE_OUTSIDE", 140031);

/* pPie class definition */
class pPie
{
	private $LabelPos = [];
	private $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Draw a pie chart */
	public function draw2DPie(int $X, int $Y, array $Format = [])
	{
		$Precision = 0;
		$SecondPass = TRUE;
		$Border = FALSE;
		$BorderColor = new pColor(255);
		$Shadow = FALSE;
		$DrawLabels = FALSE;
		$LabelStacked = FALSE;
		$LabelColorType = PIE_LABEL_COLOR_MANUAL;
		$LabelColor = new pColor(0);
		$WriteValues = NULL;
		$ValuePosition = PIE_VALUE_OUTSIDE;
		$ValueSuffix = "";
		$ValueColor = new pColor(255);
		$Radius = 60;
		$DataGapAngle = 0;
		$DataGapRadius = 0;
		$ValuePadding = 15;

		/* Override defaults */
		extract($Format);

		/* Data Processing */
		list($AbscissaData, $Values, $Palette) = $this->myPicture->myData->getPieParams();

		/* Compute the wasted angular space between series */
		$WastedAngular = (count($Values) == 1) ? 0 : count($Values) * $DataGapAngle;

		/* Compute the scale */
		$SerieSum = array_sum($Values);
		$ScaleFactor = (360 - $WastedAngular) / $SerieSum;
		$ShadowSpec = $this->myPicture->getShadow();
		if ($ShadowSpec['Enabled']) {
			$this->myPicture->setShadow(FALSE);
			$ShadowFormat = $Format;
			$ShadowFormat["Shadow"] = TRUE;
			$this->draw2DPie($X + $ShadowSpec['X'], $Y + $ShadowSpec['Y'], $ShadowFormat);
		}

		/* Draw the polygon pie elements */
		$Step = rad2deg(1/$Radius);
		$Offset = 0;

		foreach($Values as $Key => $Value) {

			if ($Shadow) {
				$Settings = ["Color" => $ShadowSpec['Color']];
			} else {
				$Settings = ["Color" => $Palette[$Key]];
			}

			if (!$SecondPass && !$Shadow) {
				if (!$Border) {
					$Settings["Surrounding"] = 10;
				} else {
					$Settings["BorderColor"] = $BorderColor;
				}
			}

			$EndAngle = $Offset + ($Value * $ScaleFactor);
			($EndAngle > 360) AND $EndAngle = 360;

			$Angle = ($EndAngle - $Offset) / 2 + $Offset;
			if ($DataGapAngle == 0) {
				$X0 = $X;
				$Y0 = $Y;
			} else {
				$X0 = cos(deg2rad($Angle - 90)) * $DataGapRadius + $X;
				$Y0 = sin(deg2rad($Angle - 90)) * $DataGapRadius + $Y;
			}

			$Plots = [$X0, $Y0];
			for ($i = $Offset; $i <= $EndAngle; $i += $Step) {

				$Xc = cos(deg2rad($i - 90)) * $Radius + $X;
				$Yc = sin(deg2rad($i - 90)) * $Radius + $Y;

				if ($SecondPass){

					if ($i < 90) {
						$Yc++;
					}

					if ($i > 180 && $i < 270) {
						$Xc++;
					}
				}

				$Plots[] = $Xc;
				$Plots[] = round($Yc);

			}

			$this->myPicture->drawPolygon($Plots, $Settings);

			if ($DrawLabels && !$Shadow && !$SecondPass) {
				if ($LabelColorType == PIE_LABEL_COLOR_AUTO) {
					$Settings = ["FillColor" => $Palette[$Key]];
				} else {
					$Settings = ["FillColor" => $LabelColor];
				}

				$Angle = ($EndAngle - $Offset) / 2 + $Offset;
				$Xc = cos(deg2rad($Angle - 90)) * $Radius + $X;
				$Yc = sin(deg2rad($Angle - 90)) * $Radius + $Y;
				$Label = $AbscissaData[$Key];

				if ($LabelStacked) {
					$this->writePieLabel($Xc, $Yc, $Label, $Angle, $Settings, TRUE, $X, $Radius);
				} else {
					$this->writePieLabel($Xc, $Yc, $Label, $Angle, $Settings, FALSE);
				}
			}

			$Offset = $i + $DataGapAngle;
		}

		/* Second pass to smooth the angles */
		if ($SecondPass) {
			$Step = rad2deg(1/$Radius);
			$Offset = 0;

			foreach($Values as $Key => $Value) {

				if ($Shadow) {
					$Settings = ["Color" => $ShadowSpec['Color']];
				} else {
					if ($Border) {
						$Settings = ["Color" => $BorderColor];
					} else {
						$Settings = ["Color" => $Palette[$Key]];
					}
				}

				$EndAngle = $Offset + ($Value * $ScaleFactor);
				($EndAngle > 360) AND $EndAngle = 360;

				if ($DataGapAngle == 0) {
					$X0 = $X;
					$Y0 = $Y;
				} else {
					$Angle = ($EndAngle - $Offset) / 2 + $Offset;
					$X0 = cos(deg2rad($Angle - 90)) * $DataGapRadius + $X;
					$Y0 = sin(deg2rad($Angle - 90)) * $DataGapRadius + $Y;
				}

				$Plots[] = $X0;
				$Plots[] = $Y0;
				for ($i = $Offset; $i <= $EndAngle; $i += $Step) {
					$Xc = cos(deg2rad($i - 90)) * $Radius + $X - 1;
					$Yc = sin(deg2rad($i - 90)) * $Radius + $Y;
					if ($i == $Offset) {
						# Momchil: visual fix
						$this->myPicture->drawLine($Xc+0.2, $Yc, $X0+0.4, $Y0, $Settings);
					}
					$this->myPicture->drawAntialiasPixel($Xc, $Yc, $Settings["Color"]);
					# Momchil: visual fix
					$this->myPicture->drawAntialiasPixel($Xc + 1, $Yc, $Settings["Color"]);
				}

				$this->myPicture->drawLine($Xc, $Yc, $X0, $Y0, $Settings);
				if ($DrawLabels && !$Shadow) {

					if ($LabelColorType == PIE_LABEL_COLOR_AUTO) {
						$Settings = ["FillColor" => $Palette[$Key]];
					} else {
						$Settings = ["FillColor" => $LabelColor];
					}

					$Angle = ($EndAngle - $Offset) / 2 + $Offset;
					$Xc = cos(deg2rad($Angle - 90)) * $Radius + $X;
					$Yc = sin(deg2rad($Angle - 90)) * $Radius + $Y;
					$Label = $AbscissaData[$Key];

					if ($LabelStacked) {
						$this->writePieLabel($Xc, $Yc, $Label, $Angle, $Settings, TRUE, $X, $Radius);
					} else {
						$this->writePieLabel($Xc, $Yc, $Label, $Angle, $Settings, FALSE);
					}
				}

				$Offset = $i + $DataGapAngle;
			}
		}

		if (!is_null($WriteValues) && !$Shadow) {

			$Offset = 0;
			$Settings = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE,"Color" => $ValueColor];

			if ($ValuePosition == PIE_VALUE_OUTSIDE) {
				$Radius = $Radius + $ValuePadding;
			} else {
				$Radius = $Radius / 2;
			}

			foreach($Values as $Value) {
				$EndAngle = ($Value * $ScaleFactor) + $Offset;
			    ((int)$EndAngle > 360) AND $EndAngle = 0;
				$Angle = ($EndAngle - $Offset) / 2 + $Offset;
				$Xc = cos(deg2rad($Angle - 90)) * $Radius + $X;
				$Yc = sin(deg2rad($Angle - 90)) * $Radius + $Y;

				if ($WriteValues == PIE_VALUE_PERCENTAGE) {
					$Display = round((100 / $SerieSum) * $Value, $Precision) . "%";
				} elseif ($WriteValues == PIE_VALUE_NATURAL) {
					$Display = strval($Value) . $ValueSuffix;
				}

				$this->myPicture->drawText($Xc, $Yc, $Display, $Settings);
				$Offset = $EndAngle + $DataGapAngle;
			}
		}

		if ($DrawLabels && $LabelStacked) {
			$this->writeShiftedLabels();
		}

		$this->myPicture->restoreShadow($ShadowSpec);
	}

	/* Draw a 3D pie chart */
	public function draw3DPie(int $X, int $Y, array $Format = [])
	{
		$Precision = 0;
		$SecondPass = TRUE;
		$Border = FALSE;
		$Shadow = FALSE;
		$DrawLabels = FALSE;
		$LabelStacked = FALSE;
		$LabelColorType = PIE_LABEL_COLOR_MANUAL;
		$LabelColor = new pColor(0);
		$WriteValues = NULL;
		$ValueSuffix = "";
		$ValueColor = new pColor(255);
		$Radius = 80;
		$SkewFactor = .5;
		$SliceHeight = 20;
		$DataGapAngle = 0;
		$DataGapRadius = 0;
		$ValuePosition = PIE_VALUE_INSIDE;
		$ValuePadding = 15;

		/* Override defaults */
		extract($Format);

		/* Error correction for overlaying rounded corners */
		($SkewFactor < .5) AND $SkewFactor = .5;

		/* Data Processing */
		list($AbscissaData, $Values, $Palette) = $this->myPicture->myData->getPieParams();

		$Values = array_reverse($Values);
		$Palette = array_reverse(array_slice($Palette, 0, count($Values)));

		/* Compute the wasted angular space between series */
		$WastedAngular = (count($Values) == 1) ? 0 : count($Values) * $DataGapAngle;

		/* Compute the scale */
		$SerieSum = array_sum($Values);
		$ScaleFactor = (360 - $WastedAngular) / $SerieSum;
		$ShadowSpec = $this->myPicture->getShadow();
		if ($ShadowSpec['Enabled']) {
			$this->myPicture->setShadow(FALSE);
		}

		/* Draw the polygon pie elements */
		$Step = rad2deg(1/$Radius);
		$Offset = 360;
		$Slices = [];
		$SliceColors = [];
		$Visible = [];
		$SliceAngle = [];

		foreach($Values as $Key => $Value) {

			$SliceColors[$Key] = $Palette[$Key];
			#$StartAngle = $Offset;
			$EndAngle = $Offset - ($Value * $ScaleFactor);
			($EndAngle < 0) AND $EndAngle = 0;

			$Visible[$Key] = ["Start" => TRUE, "End" => ($EndAngle > 180)];

			if ($DataGapAngle == 0) {
				$X0 = $X;
				$Y0 = $Y;
			} else {
				$Angle = ($EndAngle - $Offset) / 2 + $Offset;
				$X0 = cos(deg2rad($Angle - 90)) * $DataGapRadius + $X;
				$Y0 = sin(deg2rad($Angle - 90)) * $DataGapRadius * $SkewFactor + $Y;
			}

			$Slices[$Key][] = $X0;
			$Slices[$Key][] = $Y0;
			$SliceAngle[$Key][] = 0;

			for ($i = $Offset; $i >= $EndAngle; $i = $i - $Step) {
				$Xc = cos(deg2rad($i - 90)) * $Radius + $X;
				$Yc = sin(deg2rad($i - 90)) * $Radius * $SkewFactor + $Y;

				if ($SecondPass || $ShadowSpec['Enabled']){
					($i < 90) AND $Yc++;
					($i > 90 && $i < 180) AND $Xc++;
					($i > 180 && $i < 270) AND $Xc++;
					if ($i >= 270) {
						$Xc++;
						$Yc++;
					}
				}

				$Slices[$Key][] = $Xc;
				$Slices[$Key][] = $Yc;
				$SliceAngle[$Key][] = $i;
			}

			$Offset = $i - $DataGapAngle;
		}

		/* Draw the bottom shadow if needed */
		if ($ShadowSpec['Enabled']) {
			foreach($Slices as $Plots) {
				$ShadowPie = [];
				$PlotCount = count($Plots);
				for ($i = 0; $i < $PlotCount; $i += 2) {
					$ShadowPie[] = $Plots[$i] + $ShadowSpec['X'];
					$ShadowPie[] = $Plots[$i + 1] + $ShadowSpec['Y'];
				}

				$Settings = ["Color" => $ShadowSpec['Color'],"NoBorder" => TRUE];
				$this->myPicture->drawPolygon($ShadowPie, $Settings);
			}

			$Step = rad2deg(1/$Radius);
			$Offset = 360;
			foreach($Values as $Value) {
				$EndAngle = $Offset - ($Value * $ScaleFactor);
				if ($EndAngle < 0) {
					$EndAngle = 0;
				}

				for ($i = $Offset; $i >= $EndAngle; $i = $i - $Step) {
					$Xc = cos(deg2rad($i - 90)) * $Radius + $X + $ShadowSpec['X'];
					$Yc = sin(deg2rad($i - 90)) * $Radius * $SkewFactor + $Y + $ShadowSpec['Y'];
					$this->myPicture->drawAntialiasPixel($Xc, $Yc, $Settings["Color"]);
				}

				$Offset = $i - $DataGapAngle;
			}
		}

		/* Draw the bottom pie splice */
		foreach($Slices as $SliceID => $Plots) {
			$this->myPicture->drawPolygon($Plots, ["Color" => $SliceColors[$SliceID]->newOne(), "NoBorder"=>TRUE]);
			if ($SecondPass) {
				$Settings = ["Color" => $SliceColors[$SliceID]->newOne()];
				if ($Border) {
					$Settings["Color"]->RGBChange(30);
				}

				if (isset($SliceAngle[$SliceID][1])) {
					 /* Empty error handling */
					$Angle = $SliceAngle[$SliceID][1];
					$Xc = cos(deg2rad($Angle - 90)) * $Radius + $X;
					$Yc = sin(deg2rad($Angle - 90)) * $Radius * $SkewFactor + $Y;
					$this->myPicture->drawLine($Plots[0], $Plots[1], $Xc, $Yc, $Settings);
					$Angle = $SliceAngle[$SliceID][count($SliceAngle[$SliceID]) - 1];
					$Xc = cos(deg2rad($Angle - 90)) * $Radius + $X;
					$Yc = sin(deg2rad($Angle - 90)) * $Radius * $SkewFactor + $Y;
					$this->myPicture->drawLine($Plots[0], $Plots[1], $Xc, $Yc, $Settings);
				}
			}
		}

		/* Draw the two vertical edges */
		$SlicesR = array_reverse($Slices);
		$SliceColorsR = array_reverse($SliceColors);
		foreach($SlicesR as $SliceID => $Plots) {
			$Settings = ["Color" => $SliceColorsR[$SliceID]->newOne()->RGBChange(10), "NoBorder"=>TRUE];
			if (isset($Plots[2])) {
				 /* $Visible[$SliceID]["Start"] &&  */
				$this->myPicture->drawLine($Plots[2], $Plots[3], $Plots[2], $Plots[3] - $SliceHeight, $Settings);
				$this->myPicture->drawPolygon([$Plots[0], $Plots[1], $Plots[0], $Plots[1] - $SliceHeight, $Plots[2], $Plots[3] - $SliceHeight, $Plots[2], $Plots[3]], $Settings);
			}
		}

		foreach($Slices as $SliceID => $Plots) {

			$Settings = ["Color" => $SliceColors[$SliceID]->newOne()->RGBChange(10), "NoBorder"=> TRUE];
			$PlotCount = count($Plots);

			if ($Visible[$SliceID]["End"]) {
				$LastP = end($Plots);
				$BeforeLastP = prev($Plots); # No need for resetting Plots
				$this->myPicture->drawLine($BeforeLastP, $LastP, $BeforeLastP, $LastP - $SliceHeight, $Settings);
				$this->myPicture->drawPolygon([$Plots[0], $Plots[1], $Plots[0], $Plots[1] - $SliceHeight, $BeforeLastP, $LastP - $SliceHeight, $BeforeLastP, $LastP], $Settings);
			}

			$SliceAngleID = $SliceAngle[$SliceID];
			$LastSliceAngleID = end($SliceAngleID);

			/* Draw the rounded edges */
			for ($j = 2; $j <$PlotCount - 2; $j += 2) {
				$Angle = $SliceAngleID[$j / 2];
				if ($Angle < 270 && $Angle > 90) {
					$this->myPicture->drawPolygon([$Plots[$j], $Plots[$j + 1], $Plots[$j + 2], $Plots[$j + 3], $Plots[$j + 2], $Plots[$j + 3] - $SliceHeight, $Plots[$j], $Plots[$j + 1] - $SliceHeight], $Settings);
				}
			}

			if ($SecondPass) {
				$SettingsSPass = ["Color" => $SliceColors[$SliceID]->newOne()];
				if ($Border) {
					$SettingsSPass["Color"]->RGBChange(30);
				}

				if (isset($SliceAngleID[1])) {
					/* Empty error handling */
					$Angle = $SliceAngleID[1];
					if ($Angle < 270 && $Angle > 90) {
						$Xc = cos(deg2rad($Angle - 90)) * $Radius + $X;
						$Yc = sin(deg2rad($Angle - 90)) * $Radius * $SkewFactor + $Y;
						$this->myPicture->drawLine($Xc, $Yc, $Xc, $Yc - $SliceHeight, $SettingsSPass);
					}
				}

				$Angle = $LastSliceAngleID;
				if ($Angle < 270 && $Angle > 90) {
					$Xc = cos(deg2rad($Angle - 90)) * $Radius + $X;
					$Yc = sin(deg2rad($Angle - 90)) * $Radius * $SkewFactor + $Y;
					$this->myPicture->drawLine($Xc, $Yc, $Xc, $Yc - $SliceHeight, $SettingsSPass);
				}

				if (isset($SliceAngleID[1]) && $SliceAngleID[1] > 270 && $LastSliceAngleID < 270) {
					$Xc = -$Radius + $X;
					$Yc = sin(M_PI) * $Radius * $SkewFactor + $Y;
					$this->myPicture->drawLine($Xc, $Yc, $Xc, $Yc - $SliceHeight, $SettingsSPass);
				}

				if (isset($SliceAngleID[1]) && $SliceAngleID[1] > 90 && $LastSliceAngleID < 90) {
					$Xc = $Radius + $X;
					$Yc = $Y;
					$this->myPicture->drawLine($Xc, $Yc, $Xc, $Yc - $SliceHeight, $SettingsSPass);
				}
			}

			/* Draw the top splice */
			$Settings = ["Color" => $SliceColors[$SliceID]->newOne()->RGBChange(20)];
			$Top = [];
			for ($j = 0; $j < $PlotCount; $j += 2) {
				$Top[] = $Plots[$j];
				$Top[] = $Plots[$j + 1] - $SliceHeight;
			}
			$this->myPicture->drawPolygon($Top, $Settings);
		}

		/* Second pass to smooth the angles */
		if ($SecondPass) {
			$Step = rad2deg(1/$Radius);
			$Offset = 360;

			foreach($Values as $Key => $Value) {

				if ($Shadow) {
					$Color = $ShadowSpec['Color'];
				} else {
					if ($Border) {
						$Color = $Palette[$Key]->newOne()->RGBChange(30);
					} else {
						# Momchil: No example goes in here
						$Color = $Palette[$Key];
					}
				}

				$EndAngle = $Offset - ($Value * $ScaleFactor);
				($EndAngle < 0) AND $EndAngle = 0;

				if ($DataGapAngle == 0) {
					$X0 = $X;
					$Y0 = $Y - $SliceHeight;
				} else {
					$Angle = ($EndAngle - $Offset) / 2 + $Offset;
					$X0 = cos(deg2rad($Angle - 90)) * $DataGapRadius + $X;
					$Y0 = sin(deg2rad($Angle - 90)) * $DataGapRadius * $SkewFactor + $Y - $SliceHeight;
				}

				$Plots[] = $X0;
				$Plots[] = $Y0;
				for ($i = $Offset; $i >= $EndAngle; $i = $i - $Step) {
					$Xc = cos(deg2rad($i - 90)) * $Radius + $X;
					$Yc = sin(deg2rad($i - 90)) * $Radius * $SkewFactor + $Y - $SliceHeight;
					if ($Key == 0) {
						$this->myPicture->drawLine($Xc, $Yc, $X0, $Y0, ["Color" => $Color]);
					}

					$this->myPicture->drawAntialiasPixel($Xc, $Yc, $Color);
					if ($i < 270 && $i > 90) {
						$this->myPicture->drawAntialiasPixel($Xc, $Yc + $SliceHeight, $Color);
					}
				}

				$this->myPicture->drawLine($Xc, $Yc, $X0, $Y0, ["Color" => $Color]);
				$Offset = $i - $DataGapAngle;
			}
		}

		if (!is_null($WriteValues)) {

			$Offset = 360;
			$Settings = ["Align" => TEXT_ALIGN_MIDDLEMIDDLE,"Color" => $ValueColor];

			foreach($Values as $Value) {

				$EndAngle = $Offset - ($Value * $ScaleFactor);
				($EndAngle < 0) AND $EndAngle = 0;

				$Angle = ($EndAngle - $Offset) / 2 + $Offset;
				$Angle = deg2rad($Angle - 90);
				if ($ValuePosition == PIE_VALUE_OUTSIDE) {
					$Xc = cos($Angle) * ($Radius + $ValuePadding) + $X;
					$Yc = sin($Angle) * (($Radius * $SkewFactor) + $ValuePadding) + $Y - $SliceHeight;
				} else {
					$Xc = cos($Angle) * ($Radius) / 2 + $X;
					$Yc = sin($Angle) * ($Radius * $SkewFactor) / 2 + $Y - $SliceHeight;
				}

				if ($WriteValues == PIE_VALUE_PERCENTAGE) {
					$Display = round((100 / $SerieSum) * $Value, $Precision) . "%";
				} elseif ($WriteValues == PIE_VALUE_NATURAL) {
					$Display = strval($Value) . $ValueSuffix;
				}

				$this->myPicture->drawText($Xc, $Yc, $Display, $Settings);
				$Offset = $EndAngle - $DataGapAngle;
			}
		}

		if ($DrawLabels) {
			$Offset = 360;
			$ID = count($Values) - 1;
			foreach($Values as $Value) {
				$Settings = ["FillColor" => ($LabelColorType == PIE_LABEL_COLOR_AUTO) ? $Palette[$ID] : $LabelColor];
				$EndAngle = $Offset - ($Value * $ScaleFactor);
				($EndAngle < 0) AND $EndAngle = 0;
				$Angle = ($EndAngle - $Offset) / 2 + $Offset;
				$Xc = cos(deg2rad($Angle - 90)) * $Radius + $X;
				$Yc = sin(deg2rad($Angle - 90)) * $Radius * $SkewFactor + $Y - $SliceHeight;
				if (isset($AbscissaData[$ID])) {
					$Label = $AbscissaData[$ID];
					if ($LabelStacked) {
						$this->writePieLabel($Xc, $Yc, $Label, $Angle, $Settings, TRUE, $X, $Radius, TRUE);
					} else {
						$this->writePieLabel($Xc, $Yc, $Label, $Angle, $Settings, FALSE);
					}
				}

				$Offset = $EndAngle - $DataGapAngle;
				$ID--;
			}
		}

		if ($DrawLabels && $LabelStacked) {
			$this->writeShiftedLabels();
		}

		$this->myPicture->restoreShadow($ShadowSpec);
	}

	public function drawPieLegend(int $X, int $Y, array $Format = [])
	{
		$fontProperties = $this->myPicture->getFont();

		$FontName = $fontProperties['Name'];
		$FontSize = $fontProperties['Size'];
		$FontColor = $fontProperties['Color'];
		$BoxSize = 5;
		$Margin = 5;
		$Color = new pColor(200);
		$BorderColor = new pColor(255);
		$Surrounding = NULL;
		$Style = LEGEND_ROUND;
		$Mode = LEGEND_VERTICAL;

		/* Override defaults */
		extract($Format);

		(!is_null($Surrounding)) AND $BorderColor = $Color->newOne()->RGBChange($Surrounding);

		$BorderColor->AlphaSet($Color->AlphaGet());

		/* Data Processing */
		list($AbscissaData, , $Palette) = $this->myPicture->myData->getPieParams($forLegend = TRUE);

		$YStep = max($fontProperties['Size'], $BoxSize) + 5;
		$XStep = $BoxSize + 5;

		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;

		foreach($AbscissaData as $Value) {
			$BoxArray = $this->myPicture->getTextBox($vX + $BoxSize + 4, $vY + $BoxSize / 2, $FontName, $FontSize, 0, $Value);
			if ($Mode == LEGEND_VERTICAL) {
				($Boundaries["T"] > $BoxArray[2]["Y"] + $BoxSize / 2) 		AND $Boundaries["T"] = $BoxArray[2]["Y"] + $BoxSize / 2;
				($Boundaries["R"] < $BoxArray[1]["X"] + 2) 					AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
				($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $BoxSize / 2) 	AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $BoxSize / 2;
				$vY = $vY + $YStep;

			} elseif ($Mode == LEGEND_HORIZONTAL) {
				($Boundaries["T"] > $BoxArray[2]["Y"] + $BoxSize / 2)	 	AND $Boundaries["T"] = $BoxArray[2]["Y"] + $BoxSize / 2;
				($Boundaries["R"] < $BoxArray[1]["X"] + 2)				 	AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
				($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $BoxSize / 2) 	AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $BoxSize / 2;
				$vX = $Boundaries["R"] + $XStep;
			}
		}

		$vY = $vY - $YStep;
		#$vX = $vX - $XStep;
		$TopOffset = $Y - $Boundaries["T"];
		if ($Boundaries["B"] - ($vY + $BoxSize) < $TopOffset) {
			$Boundaries["B"] = $vY + $BoxSize + $TopOffset;
		}

		$Settings = ["Color" => $Color,"BorderColor" => $BorderColor];

		if ($Style == LEGEND_ROUND) {
			$this->myPicture->drawRoundedFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Margin, $Settings);
		} elseif ($Style == LEGEND_BOX) {
			$this->myPicture->drawFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Settings);
		}

		$ShadowSpec = $this->myPicture->getShadow();
		$this->myPicture->setShadow(FALSE);

		foreach($AbscissaData as $Key => $Value) {
			$Settings = ["Color" => $Palette[$Key]];
			$Settings["Surrounding"] = 20;
			$this->myPicture->drawFilledRectangle($X + 1, $Y + 1, $X + $BoxSize + 1, $Y + $BoxSize + 1, ["Color" => new pColor(0,0,0,20)]);
			$this->myPicture->drawFilledRectangle($X, $Y, $X + $BoxSize, $Y + $BoxSize, $Settings);
			if ($Mode == LEGEND_VERTICAL) {
				$this->myPicture->drawText($X + $BoxSize + 4, $Y + $BoxSize / 2, $Value, ["Color" => $FontColor,"Align" => TEXT_ALIGN_MIDDLELEFT,"FontName" => $FontName,"FontSize" => $FontSize]);
				$Y = $Y + $YStep;
			} elseif ($Mode == LEGEND_HORIZONTAL) {
				$BoxArray = $this->myPicture->drawText($X + $BoxSize + 4, $Y + 2 + $BoxSize / 2, $Value, ["Color" => $FontColor,"Align" => TEXT_ALIGN_MIDDLELEFT,"FontName" => $FontName,"FontSize" => $FontSize]);
				$X = $BoxArray[1]["X"] + 2 + $XStep;
			}
		}

		$this->myPicture->restoreShadow($ShadowSpec);
	}

	/* Internally used compute the label positions */
	private function writePieLabel($X, $Y, $Label, $Angle, $Settings, $Stacked, $Xc = 0, $Radius = 0, $Reversed = FALSE)
	{
		$LabelOffset = 30;

		if (!$Stacked) {
			$Settings["Angle"] = 360 - $Angle;
			$Settings["Length"] = 25;
			$Settings["Size"] = 8;
			$this->myPicture->drawArrowLabel($X, $Y, " " . $Label . " ", $Settings);
		} else {
			$X2 = cos(deg2rad($Angle - 90)) * 20 + $X;
			$Y2 = sin(deg2rad($Angle - 90)) * 20 + $Y;
			$fontProperties = $this->myPicture->getFont();
			$TxtPos = $this->myPicture->getTextBox($X, $Y, $fontProperties['Name'], $fontProperties['Size'], 0, $Label);
			$Height = $TxtPos[0]["Y"] - $TxtPos[2]["Y"];
			$YTop = $Y2 - $Height / 2 - 2;
			$YBottom = $Y2 + $Height / 2 + 2;
			if (!empty($this->LabelPos)) {
				foreach($this->LabelPos as $LabelSettings) {
					if (($YTop >= $LabelSettings["YTop"] && $YTop <= $LabelSettings["YBottom"]) || ($YBottom >= $LabelSettings["YTop"] && $YBottom <= $LabelSettings["YBottom"])){
						switch (TRUE) {
							case ($Angle <= 90):
								$this->shift(0, 180, -($Height + 2), $Reversed);
								break 2;
							case ($Angle > 90 && $Angle <= 180):
								$this->shift(0, 180, -($Height + 2), $Reversed);
								break 2;
							case ($Angle > 180 && $Angle <= 270):
								$this->shift(180, 360, ($Height + 2), $Reversed);
								break 2;
							case ($Angle > 270 && $Angle <= 360):
								$this->shift(180, 360, ($Height + 2), $Reversed);
								break 2;
						}
					}
				}
			}

			$LabelSettings = ["YTop" => $YTop,"YBottom" => $YBottom,"Label" => $Label,"Angle" => $Angle,"X1" => $X,"Y1" => $Y,"X2" => $X2,"Y2" => $Y2];
			($Angle <= 180) AND $LabelSettings["X3"] = $Xc + $Radius + $LabelOffset;
			($Angle > 180)  AND $LabelSettings["X3"] = $Xc - $Radius - $LabelOffset;

			$this->LabelPos[] = $LabelSettings;
		}
	}

	/* Internally used to shift label positions */
	private function shift($StartAngle, $EndAngle, $Offset, $Reversed)
	{
		if ($Reversed) {
			$Offset = - $Offset;
		}

		foreach($this->LabelPos as $Key => $Settings) {
			if ($Settings["Angle"] > $StartAngle && $Settings["Angle"] <= $EndAngle) {
				$this->LabelPos[$Key]["YTop"] = $Settings["YTop"] + $Offset;
				$this->LabelPos[$Key]["YBottom"] = $Settings["YBottom"] + $Offset;
				$this->LabelPos[$Key]["Y2"] = $Settings["Y2"] + $Offset;
			}
		}
	}

	/* Internally used to write the re-computed labels */
	private function writeShiftedLabels()
	{
		if (empty($this->LabelPos)) {
			return; # Momchil: example.draw2DPie.labels
		}

		foreach($this->LabelPos as $Settings) {
			$X1 = $Settings["X1"];
			$Y1 = $Settings["Y1"];
			$X2 = $Settings["X2"];
			$Y2 = $Settings["Y2"];
			$X3 = $Settings["X3"];
			$this->myPicture->drawArrow($X2, $Y2, $X1, $Y1, ["Size" => 8]);
			if ($Settings["Angle"] <= 180) {
				$this->myPicture->drawLine($X2, $Y2, $X3, $Y2);
				$this->myPicture->drawText($X3 + 2, $Y2, $Settings["Label"], ["Align" => TEXT_ALIGN_MIDDLELEFT]);
			} else {
				$this->myPicture->drawLine($X2, $Y2, $X3, $Y2);
				$this->myPicture->drawText($X3 - 2, $Y2, $Settings["Label"], ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
			}
		}
	}

	/* Draw a ring chart */
	public function draw2DRing(int $X, int $Y, array $Format = [])
	{
		$Precision = 0;
		$Border = FALSE;
		$BorderColor = new pColor(255);
		$Shadow = FALSE;
		$DrawLabels = FALSE;
		$LabelStacked = FALSE;
		$LabelColorType = PIE_LABEL_COLOR_MANUAL;
		$LabelColor = new pColor(0);
		$WriteValues = NULL;
		$ValuePosition = PIE_VALUE_OUTSIDE;
		$ValueSuffix = "";
		$ValueColor = new pColor(255);
		$OuterRadius = 60;
		$InnerRadius = 30;
		$ValuePadding = 5;

		/* Override defaults */
		extract($Format);

		/* Data Processing */
		list($AbscissaData, $Values, $Palette) = $this->myPicture->myData->getPieParams();

		/* Shadow */
		$ShadowSpec = $this->myPicture->getShadow();
		if ($ShadowSpec['Enabled']) {
			$this->myPicture->setShadow(FALSE);
			$ShadowFormat = $Format;
			$ShadowFormat["Shadow"] = TRUE;
			$this->draw2DRing($X + $ShadowSpec['X'], $Y + $ShadowSpec['Y'], $ShadowFormat);
		}

		/* Draw the polygon pie elements */
		$SerieSum = array_sum($Values);
		$ScaleFactor = 360 / $SerieSum;
		$Step = rad2deg(1/$OuterRadius);
		$Offset = 0;

		foreach($Values as $Key => $Value) {

			if ($Shadow) {
				$Settings = ["Color" => $ShadowSpec['Color']];
				$BorderSettings = $Settings;
			} else {
				$Settings = ["Color" => $Palette[$Key]];
				$BorderSettings = ($Border) ? ["Color" => $BorderColor] : $Settings;
			}

			$Plots = [];
			$Boundaries = [];
			$AAPixels = [];
			$EndAngle = $Offset + ($Value * $ScaleFactor);
			($EndAngle > 360) AND $EndAngle = 360;

			for ($i = $Offset; $i <= $EndAngle; $i += $Step) {

				$Xc = cos(deg2rad($i - 90)) * $OuterRadius + $X;
				$Yc = sin(deg2rad($i - 90)) * $OuterRadius + $Y;

				if (!isset($Boundaries[0]["X1"])) {
					$Boundaries[0]["X1"] = $Xc;
					$Boundaries[0]["Y1"] = $Yc;
				}

				$AAPixels[] = [$Xc,$Yc];
				if ($i < 90) {
					$Yc++;
				}

				if ($i > 180 && $i < 270) {
					$Xc++;
				}

				if ($i >= 270) {
					$Xc++;
					$Yc++;
				}

				$Plots[] = $Xc;
				$Plots[] = round($Yc);
			}

			$Boundaries[1]["X1"] = $Xc;
			$Boundaries[1]["Y1"] = $Yc;
			$Lasti = $EndAngle;
			for ($i = $EndAngle; $i >= $Offset; $i = $i - $Step) {

				$Xc = cos(deg2rad($i - 90)) * ($InnerRadius - 1) + $X;
				$Yc = sin(deg2rad($i - 90)) * ($InnerRadius - 1) + $Y;

				if (!isset($Boundaries[1]["X2"])) {
					$Boundaries[1]["X2"] = $Xc;
					$Boundaries[1]["Y2"] = $Yc;
				}

				$AAPixels[] = [$Xc,$Yc];
				$Xc = cos(deg2rad($i - 90)) * $InnerRadius + $X;
				$Yc = sin(deg2rad($i - 90)) * $InnerRadius + $Y;

				if ($i < 90) {
					$Yc++;
				}

				if ($i > 180 && $i < 270) {
					$Xc++;
				}

				if ($i >= 270) {
					$Xc = round($Xc);
					$Yc++;
				}

				$Plots[] = $Xc;
				$Plots[] = round($Yc);
			}

			$Boundaries[0]["X2"] = $Xc;
			$Boundaries[0]["Y2"] = $Yc;
			/* Draw the polygon */
			$this->myPicture->drawPolygon($Plots, $Settings);

			/* Smooth the edges using AA */
			foreach($AAPixels as $Pos) {
				$this->myPicture->drawAntialiasPixel($Pos[0], $Pos[1], $BorderSettings["Color"]);
			}

			$this->myPicture->drawLine($Boundaries[0]["X1"], $Boundaries[0]["Y1"], $Boundaries[0]["X2"], $Boundaries[0]["Y2"], $BorderSettings);
			$this->myPicture->drawLine($Boundaries[1]["X1"], $Boundaries[1]["Y1"], $Boundaries[1]["X2"], $Boundaries[1]["Y2"], $BorderSettings);
			if ($DrawLabels && !$Shadow) {
				$Settings = ["FillColor" => ($LabelColorType == PIE_LABEL_COLOR_AUTO) ? $Palette[$Key] : $LabelColor];
				$Angle = ($EndAngle - $Offset) / 2 + $Offset;
				$Xc = cos(deg2rad($Angle - 90)) * $OuterRadius + $X;
				$Yc = sin(deg2rad($Angle - 90)) * $OuterRadius + $Y;
				$Label = $AbscissaData[$Key];
				if ($LabelStacked) {
					$this->writePieLabel($Xc, $Yc, $Label, $Angle, $Settings, TRUE, $X, $OuterRadius);
				} else {
					$this->writePieLabel($Xc, $Yc, $Label, $Angle, $Settings, FALSE);
				}
			}

			$Offset = $Lasti;
		}

		if ($DrawLabels && $LabelStacked) {
			$this->writeShiftedLabels();
		}

		if ($WriteValues && !$Shadow) {
			#$Step = rad2deg(1/$OuterRadius);
			$Offset = 0;
			foreach($Values as $Value) {

				$EndAngle = $Offset + ($Value * $ScaleFactor);
				($EndAngle > 360) AND $EndAngle = 360;
				$Angle = $Offset + ($Value * $ScaleFactor) / 2;

				if ($ValuePosition == PIE_VALUE_OUTSIDE) {
					$Xc = cos(deg2rad($Angle - 90)) * ($OuterRadius + $ValuePadding) + $X;
					$Yc = sin(deg2rad($Angle - 90)) * ($OuterRadius + $ValuePadding) + $Y;
					($Angle >= 0 && $Angle <= 90) AND $Align = TEXT_ALIGN_BOTTOMLEFT;
					($Angle > 90 && $Angle <= 180) AND $Align = TEXT_ALIGN_TOPLEFT;
					($Angle > 180 && $Angle <= 270) AND $Align = TEXT_ALIGN_TOPRIGHT;
					($Angle > 270) AND $Align = TEXT_ALIGN_BOTTOMRIGHT;
				} else {
					$Xc = cos(deg2rad($Angle - 90)) * (($OuterRadius - $InnerRadius) / 2 + $InnerRadius) + $X;
					$Yc = sin(deg2rad($Angle - 90)) * (($OuterRadius - $InnerRadius) / 2 + $InnerRadius) + $Y;
					$Align = TEXT_ALIGN_MIDDLEMIDDLE;
				}

				if ($WriteValues == PIE_VALUE_PERCENTAGE) {
					$Display = strval(round((100 / $SerieSum) * $Value, $Precision)) . "%";
				} elseif ($WriteValues == PIE_VALUE_NATURAL) {
					$Display = strval($Value) . $ValueSuffix;
				} else {
					$Display = "";
				}

				$this->myPicture->drawText($Xc, $Yc, $Display, ["Align" => $Align,"Color" => $ValueColor]);
				$Offset = $EndAngle;
			}
		}

		$this->myPicture->restoreShadow($ShadowSpec);
	}

	public function draw3DRing(int $X, int $Y, array $Format = [])
	{
		$Precision = 0;
		#$Shadow = FALSE;
		$DrawLabels = FALSE;
		$LabelStacked = FALSE;
		$LabelColorType = PIE_LABEL_COLOR_MANUAL;
		$LabelColor = new pColor(0);
		$OuterRadius = 100;
		$InnerRadius = 30;
		$SkewFactor = .6;
		$SliceHeight = isset($Format["SliceHeight"]) ? $Format["SliceHeight"] : 10;
		$DataGapAngle = 10;
		$DataGapRadius = 10;
		$Cf = 20;
		$WriteValues = PIE_VALUE_NATURAL;

		/* Override defaults */
		extract($Format);

		/* Error correction for overlaying rounded corners */
		($SkewFactor < .5) AND $SkewFactor = .5;

		/* Data Processing */
		list($AbscissaData, $Values, $Palette) = $this->myPicture->myData->getPieParams();

		$AbscissaData = array_reverse($AbscissaData);
		$Values = array_reverse($Values);
		$Palette = array_reverse(array_slice($Palette, 0, count($Values)));

		/* Compute the wasted angular space between series */
		$WastedAngular = (count($Values) == 1) ? 0 : count($Values) * $DataGapAngle;

		/* Compute the scale */
		$SerieSum = array_sum($Values);
		$ScaleFactor = (360 - $WastedAngular) / $SerieSum;
		$ShadowSpec = $this->myPicture->getShadow();
		if ($ShadowSpec['Enabled']) {
			$this->myPicture->setShadow(FALSE);
		}

		/* Draw the polygon ring elements */
		$Offset = 360;
		$Slices = [];
		$SliceColors = [];

		foreach($Values as $Key => $Value) {

			$SliceColors[$Key] = $Palette[$Key];
			$EndAngle = $Offset - ($Value * $ScaleFactor);
			($EndAngle < 0) AND $EndAngle = 0;
			$Step = (rad2deg(1/$OuterRadius)) / 2;
			$OutX1 = VOID;
			$OutY1 = VOID;

			for ($i = $Offset; $i >= $EndAngle; $i = $i - $Step) {

				$cos = cos(deg2rad($i - 90));
				$sin = sin(deg2rad($i - 90));

				$Xc = $cos * ($OuterRadius + $DataGapRadius - 2) + $X;
				$Yc = $sin * ($OuterRadius + $DataGapRadius - 2) * $SkewFactor + $Y;
				$Slices[$Key]["AA"][] = [$Xc,$Yc];
				$Xc = $cos * ($OuterRadius + $DataGapRadius - 1) + $X;
				$Yc = $sin * ($OuterRadius + $DataGapRadius - 1) * $SkewFactor + $Y;
				$Slices[$Key]["AA"][] = [$Xc,$Yc];
				$Xc = $cos * ($OuterRadius + $DataGapRadius) + $X;
				$Yc = $sin * ($OuterRadius + $DataGapRadius) * $SkewFactor + $Y;

				$this->myPicture->drawAntialiasPixel($Xc, $Yc, $SliceColors[$Key]);

				if ($OutX1 == VOID) {
					$OutX1 = $Xc;
					$OutY1 = $Yc;
				}

				($i < 90) AND $Yc++;
				($i > 90 && $i < 180) AND $Xc++;
				($i > 180 && $i < 270) AND $Xc++;

				if ($i >= 270) {
					$Xc++;
					$Yc++;
				}

				$Slices[$Key]["BottomPoly"][] = floor($Xc);
				$Slices[$Key]["BottomPoly"][] = floor($Yc);
				$Slices[$Key]["TopPoly"][] = floor($Xc);
				$Slices[$Key]["TopPoly"][] = floor($Yc) - $SliceHeight;
				$Slices[$Key]["Angle"][] = $i;
			}

			$OutX2 = $Xc;
			$OutY2 = $Yc;
			$Slices[$Key]["Angle"][] = VOID;
			$Lasti = $i;
			$Step = (rad2deg(1/$InnerRadius)) / 2;
			$InX1 = VOID;
			$InY1 = VOID;

			for ($i = $EndAngle; $i <= $Offset; $i += $Step) {

				$Xc = cos(deg2rad($i - 90)) * ($InnerRadius + $DataGapRadius - 1) + $X;
				$Yc = sin(deg2rad($i - 90)) * ($InnerRadius + $DataGapRadius - 1) * $SkewFactor + $Y;
				$Slices[$Key]["AA"][] = [$Xc,$Yc];
				$Xc = cos(deg2rad($i - 90)) * ($InnerRadius + $DataGapRadius) + $X;
				$Yc = sin(deg2rad($i - 90)) * ($InnerRadius + $DataGapRadius) * $SkewFactor + $Y;
				$Slices[$Key]["AA"][] = [$Xc,$Yc];

				if ($InX1 == VOID) {
					$InX1 = $Xc;
					$InY1 = $Yc;
				}

				($i < 90) AND $Yc++;
				($i > 90 && $i < 180) AND $Xc++;
				($i > 180 && $i < 270) AND $Xc++;

				if ($i >= 270) {
					$Xc++;
					$Yc++;
				}

				$Slices[$Key]["BottomPoly"][] = floor($Xc);
				$Slices[$Key]["BottomPoly"][] = floor($Yc);
				$Slices[$Key]["TopPoly"][] = floor($Xc);
				$Slices[$Key]["TopPoly"][] = floor($Yc) - $SliceHeight;
				$Slices[$Key]["Angle"][] = $i;
			}

			$InX2 = $Xc;
			$InY2 = $Yc;
			$Slices[$Key]["InX1"] = $InX1;
			$Slices[$Key]["InY1"] = $InY1;
			$Slices[$Key]["InX2"] = $InX2;
			$Slices[$Key]["InY2"] = $InY2;
			$Slices[$Key]["OutX1"] = $OutX1;
			$Slices[$Key]["OutY1"] = $OutY1;
			$Slices[$Key]["OutX2"] = $OutX2;
			$Slices[$Key]["OutY2"] = $OutY2;

			$Offset = $Lasti - $DataGapAngle;
		}

		/* Draw the bottom pie splice */
		foreach($Slices as $SliceID => $Plots) {
			$Settings = ["Color" => $SliceColors[$SliceID]->newOne(), "NoBorder" => TRUE];
			$this->myPicture->drawPolygon($Plots["BottomPoly"], $Settings);
			foreach($Plots["AA"] as $Pos){
				$this->myPicture->drawAntialiasPixel($Pos[0], $Pos[1], $Settings["Color"]);
			}
			$this->myPicture->drawLine($Plots["InX1"], $Plots["InY1"], $Plots["OutX2"], $Plots["OutY2"], $Settings);
			$this->myPicture->drawLine($Plots["InX2"], $Plots["InY2"], $Plots["OutX1"], $Plots["OutY1"], $Settings);
		}

		$Slices = array_reverse($Slices);
		$SliceColors = array_reverse($SliceColors);
		/* Draw the vertical edges (semi-visible) */
		foreach($Slices as $SliceID => $Plots) {

			$Settings = ["Color" =>  $SliceColors[$SliceID]->newOne()->RGBChange($Cf), "NoBorder" => TRUE];
			$StartAngle = $Plots["Angle"][0];

			foreach($Plots["Angle"] as $ID => $Angle) {
				if ($Angle == VOID) {
					$EndAngle = $Plots["Angle"][$ID - 1];
				}
			}

			if ($StartAngle >= 270 || $StartAngle <= 90) {
				$this->myPicture->drawLine($Plots["OutX1"], $Plots["OutY1"], $Plots["OutX1"], $Plots["OutY1"] - $SliceHeight, $Settings);
			}

			if ($StartAngle >= 270 || $StartAngle <= 90) {
				$this->myPicture->drawLine($Plots["OutX2"], $Plots["OutY2"], $Plots["OutX2"], $Plots["OutY2"] - $SliceHeight, $Settings);
			}

			$this->myPicture->drawLine($Plots["InX1"], $Plots["InY1"], $Plots["InX1"], $Plots["InY1"] - $SliceHeight, $Settings);
			$this->myPicture->drawLine($Plots["InX2"], $Plots["InY2"], $Plots["InX2"], $Plots["InY2"] - $SliceHeight, $Settings);

			/* Draw the inner vertical slices */
			$Settings = ["Color" => $SliceColors[$SliceID]->newOne()->RGBChange($Cf), "NoBorder"=>TRUE];
			$Inner = FALSE;
			$InnerPlotsA = [];

			foreach($Plots["Angle"] as $ID => $Angle) {
				if ($Angle == VOID) {
					$Inner = TRUE;
				} elseif ($Inner) {
					if (($Angle < 90 || $Angle > 270) && isset($Plots["BottomPoly"][$ID * 2])) {
						$Xo = $Plots["BottomPoly"][$ID * 2];
						$Yo = $Plots["BottomPoly"][$ID * 2 + 1];
						$InnerPlotsA += [$Xo,$Yo,$Xo,$Yo - $SliceHeight];

					}
				}
			}

			(!empty($InnerPlotsA)) AND $this->myPicture->drawPolygon($InnerPlotsA, $Settings);

			/* Draw the splice top and left poly */
			$Settings = ["Color" => $SliceColors[$SliceID]->newOne()->RGBChange($Cf * 1.5), "NoBorder" => TRUE];

			if ($StartAngle < 180) {
				$Points = [$Plots["InX2"], $Plots["InY2"], $Plots["InX2"], $Plots["InY2"] - $SliceHeight, $Plots["OutX1"], $Plots["OutY1"] - $SliceHeight, $Plots["OutX1"], $Plots["OutY1"]];
				$this->myPicture->drawPolygon($Points, $Settings);
			}

			if ($EndAngle > 180) {
				$Points = [$Plots["InX1"], $Plots["InY1"], $Plots["InX1"], $Plots["InY1"] - $SliceHeight, $Plots["OutX2"], $Plots["OutY2"] - $SliceHeight, $Plots["OutX2"], $Plots["OutY2"]];
				$this->myPicture->drawPolygon($Points, $Settings);
			}

			/* Draw the vertical edges (visible) */
			$Settings = ["Color" => $SliceColors[$SliceID]->newOne()->RGBChange($Cf), "NoBorder" => TRUE];

			if ($StartAngle <= 270 && $StartAngle >= 90) {
				$this->myPicture->drawLine($Plots["OutX1"], $Plots["OutY1"], $Plots["OutX1"], $Plots["OutY1"] - $SliceHeight, $Settings);
			}

			if ($EndAngle <= 270 && $EndAngle >= 90) {
				$this->myPicture->drawLine($Plots["OutX2"], $Plots["OutY2"], $Plots["OutX2"], $Plots["OutY2"] - $SliceHeight, $Settings);
			}

			/* Draw the outer vertical slices */
			$Settings = ["Color" => $SliceColors[$SliceID]->newOne()->RGBChange($Cf), "NoBorder"=>TRUE];
			$Outer = TRUE;
			$OuterPlotsA = [];
			$OuterPlotsB = [];

			foreach($Plots["Angle"] as $ID => $Angle) {
				if ($Angle == VOID) {
					$Outer = FALSE;
				} elseif ($Outer) {
					if (($Angle > 90 && $Angle < 270) && isset($Plots["BottomPoly"][$ID * 2])) {
						$Xo = $Plots["BottomPoly"][$ID * 2];
						$Yo = $Plots["BottomPoly"][$ID * 2 + 1];
						$OuterPlotsA[] = $Xo;
						$OuterPlotsA[] = $Yo;
						$OuterPlotsB[] = $Xo;
						$OuterPlotsB[] = $Yo - $SliceHeight;
					}
				}
			}

			(!empty($OuterPlotsA)) AND $this->myPicture->drawPolygon(array_merge($OuterPlotsA, $this->myPicture->reversePlots($OuterPlotsB)), $Settings);

		}

		$Slices = array_reverse($Slices);
		$SliceColors = array_reverse($SliceColors);
		/* Draw the top pie splice */
		foreach($Slices as $SliceID => $Plots) {
			$Settings = ["Color" => $SliceColors[$SliceID]->newOne()->RGBChange($Cf * 2), "NoBorder" => TRUE];
			$this->myPicture->drawPolygon($Plots["TopPoly"], $Settings);

			foreach($Plots["AA"] as $Key => $Pos) {
				$this->myPicture->drawAntialiasPixel($Pos[0], $Pos[1] - $SliceHeight, $Settings["Color"]);
			}
			$this->myPicture->drawLine($Plots["InX1"], $Plots["InY1"] - $SliceHeight, $Plots["OutX2"], $Plots["OutY2"] - $SliceHeight, $Settings);
			$this->myPicture->drawLine($Plots["InX2"], $Plots["InY2"] - $SliceHeight, $Plots["OutX1"], $Plots["OutY1"] - $SliceHeight, $Settings);
		}

		if ($DrawLabels) {
			$Offset = 360;
			foreach($Values as $Key => $Value) {

				$EndAngle = $Offset - ($Value * $ScaleFactor);
				($EndAngle < 0) AND $EndAngle = 0;
				$Settings = ["FillColor" => ($LabelColorType == PIE_LABEL_COLOR_AUTO) ? $Palette[$Key] : $LabelColor];
				$Angle = ($EndAngle - $Offset) / 2 + $Offset;
				$Xc = cos(deg2rad($Angle - 90)) * ($OuterRadius + $DataGapRadius) + $X;
				$Yc = sin(deg2rad($Angle - 90)) * ($OuterRadius + $DataGapRadius) * $SkewFactor + $Y;
				if ($WriteValues == PIE_VALUE_PERCENTAGE) {
					$Label = strval(round((100 / $SerieSum) * $Value, $Precision)) . "%";
				} elseif ($WriteValues == PIE_VALUE_NATURAL) {
					$Label = strval($AbscissaData[$Key]);
				} else {
					$Label = "";
				}

				if ($LabelStacked) {
					$this->writePieLabel($Xc, $Yc - $SliceHeight, $Label, $Angle, $Settings, TRUE, $X, $OuterRadius);
				} else {
					$this->writePieLabel($Xc, $Yc - $SliceHeight, $Label, $Angle, $Settings, FALSE);
				}

				$Offset = $EndAngle - $DataGapAngle;
			}
		}

		if ($DrawLabels && $LabelStacked) {
			$this->writeShiftedLabels();
		}

		$this->myPicture->restoreShadow($ShadowSpec);
	}

}
