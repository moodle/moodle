<?php
/*
pDraw - class extension with drawing methods

Version     : 2.1.4
Made by     : Jean-Damien POGOLOTTI
Last Update : 19/01/2014

This file can be distributed under the license you can find at :

http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

define("DIRECTION_VERTICAL", 690001);
define("DIRECTION_HORIZONTAL", 690002);
define("SCALE_POS_LEFTRIGHT", 690101);
define("SCALE_POS_TOPBOTTOM", 690102);
define("SCALE_MODE_FLOATING", 690201);
define("SCALE_MODE_START0", 690202);
define("SCALE_MODE_ADDALL", 690203);
define("SCALE_MODE_ADDALL_START0", 690204);
define("SCALE_MODE_MANUAL", 690205);
define("SCALE_SKIP_NONE", 690301);
define("SCALE_SKIP_SAME", 690302);
define("SCALE_SKIP_NUMBERS", 690303);
define("TEXT_ALIGN_TOPLEFT", 690401);
define("TEXT_ALIGN_TOPMIDDLE", 690402);
define("TEXT_ALIGN_TOPRIGHT", 690403);
define("TEXT_ALIGN_MIDDLELEFT", 690404);
define("TEXT_ALIGN_MIDDLEMIDDLE", 690405);
define("TEXT_ALIGN_MIDDLERIGHT", 690406);
define("TEXT_ALIGN_BOTTOMLEFT", 690407);
define("TEXT_ALIGN_BOTTOMMIDDLE", 690408);
define("TEXT_ALIGN_BOTTOMRIGHT", 690409);
define("POSITION_TOP", 690501);
define("POSITION_BOTTOM", 690502);
define("LABEL_POS_LEFT", 690601);
define("LABEL_POS_CENTER", 690602);
define("LABEL_POS_RIGHT", 690603);
define("LABEL_POS_TOP", 690604);
define("LABEL_POS_BOTTOM", 690605);
define("LABEL_POS_INSIDE", 690606);
define("LABEL_POS_OUTSIDE", 690607);
define("ORIENTATION_HORIZONTAL", 690701);
define("ORIENTATION_VERTICAL", 690702);
define("ORIENTATION_AUTO", 690703);
define("LEGEND_NOBORDER", 690800);
define("LEGEND_BOX", 690801);
define("LEGEND_ROUND", 690802);
define("LEGEND_VERTICAL", 690901);
define("LEGEND_HORIZONTAL", 690902);
define("LEGEND_FAMILY_BOX", 691051);
define("LEGEND_FAMILY_CIRCLE", 691052);
define("LEGEND_FAMILY_LINE", 691053);
define("DISPLAY_AUTO", 691001);
define("DISPLAY_MANUAL", 691002);
define("LABELING_ALL", 691011);
define("LABELING_DIFFERENT", 691012);
define("BOUND_MIN", 691021);
define("BOUND_MAX", 691022);
define("BOUND_BOTH", 691023);
define("BOUND_LABEL_POS_TOP", 691031);
define("BOUND_LABEL_POS_BOTTOM", 691032);
define("BOUND_LABEL_POS_AUTO", 691033);
define("CAPTION_LEFT_TOP", 691041);
define("CAPTION_RIGHT_BOTTOM", 691042);
define("GRADIENT_SIMPLE", 691051);
define("GRADIENT_EFFECT_CAN", 691052);
define("LABEL_TITLE_NOBACKGROUND", 691061);
define("LABEL_TITLE_BACKGROUND", 691062);
define("LABEL_POINT_NONE", 691071);
define("LABEL_POINT_CIRCLE", 691072);
define("LABEL_POINT_BOX", 691073);
define("ZONE_NAME_ANGLE_AUTO", 691081);
define("PI", 3.14159265);
define("ALL", 69);
define("NONE", 31);
define("AUTO", 690000);
define("OUT_OF_SIGHT", -10000000000000);

class pDraw
{

	var $aColorCache = [];

	/* Returns the number of drawable series */
	function countDrawableSeries()
	{
		$Results = 0;
		$Data = $this->DataSet->getData();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$Results++;
			}
		}

		return ($Results);
	}

	/* Fix box coordinates */
	function fixBoxCoordinates($Xa, $Ya, $Xb, $Yb)
	{
		$X1 = min($Xa, $Xb);
		$Y1 = min($Ya, $Yb);
		$X2 = max($Xa, $Xb);
		$Y2 = max($Ya, $Yb);
		return [$X1,$Y1,$X2,$Y2];
	}

	/* Draw a polygon */
	function drawPolygon($Points, array $Format = [])
	{
		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
		$NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : FALSE;
		$NoBorder = isset($Format["NoBorder"]) ? $Format["NoBorder"] : FALSE;
		$Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : NULL;
		$BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : $R;
		$BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : $G;
		$BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : $B;
		$BorderAlpha = isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : $Alpha / 2;
		$SkipX = isset($Format["SkipX"]) ? $Format["SkipX"] : OUT_OF_SIGHT;
		$SkipY = isset($Format["SkipY"]) ? $Format["SkipY"] : OUT_OF_SIGHT;

		#extract($Format); # Don't use is for frequently used functions

		/* Calling the ImageFilledPolygon() function over the $Points array will round it */
		$Backup = $Points;
		if ($Surrounding != NULL) {
			$BorderR = $R + $Surrounding;
			$BorderG = $G + $Surrounding;
			$BorderB = $B + $Surrounding;
		}

		($SkipX != OUT_OF_SIGHT) AND $SkipX = floor($SkipX);
		($SkipY != OUT_OF_SIGHT) AND $SkipY = floor($SkipY);

		$RestoreShadow = $this->Shadow;
		if (!$NoFill) {
			if ($this->Shadow) {
				$this->Shadow = FALSE;
				$Shadow = []; // MOMCHIL: local var missing
				for ($i = 0; $i <= count($Points) - 1; $i = $i + 2) {
					$Shadow[] = $Points[$i] + $this->ShadowX;
					$Shadow[] = $Points[$i + 1] + $this->ShadowY;
				}

				$this->drawPolygon($Shadow, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa,"NoBorder" => TRUE]);
			}

			$FillColor = $this->allocateColor($R, $G, $B, $Alpha);
			if (count($Points) >= 6) {
				ImageFilledPolygon($this->Picture, $Points, count($Points) / 2, $FillColor);
			}
		}

		if (!$NoBorder) {
			$Points = $Backup;
			if ($NoFill) {
				$BorderSettings = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha];
			} else {
				$BorderSettings = ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha];
			}

			for ($i = 0; $i <= count($Points) - 1; $i = $i + 2) {
				if (isset($Points[$i + 2])) {
					if (!($Points[$i] == $Points[$i + 2] && $Points[$i] == $SkipX) && !($Points[$i + 1] == $Points[$i + 3] && $Points[$i + 1] == $SkipY)) $this->drawLine($Points[$i], $Points[$i + 1], $Points[$i + 2], $Points[$i + 3], $BorderSettings);
				} else {
					if (!($Points[$i] == $Points[0] && $Points[$i] == $SkipX) && !($Points[$i + 1] == $Points[1] && $Points[$i + 1] == $SkipY)) {
						$this->drawLine($Points[$i], $Points[$i + 1], $Points[0], $Points[1], $BorderSettings);
					}
				}
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Apply AALias correction to the rounded box boundaries */
	function offsetCorrection($Value, $Mode) # UNUSED
	{
		$Value = round($Value, 1);

		if ($Value == 0 && $Mode == 1) {
			 $ret = .9;
		} elseif ($Value == 0) {
			 $ret = 0;
		} else {
			$matrix = [];
			$matrix[1] = [1 => .9,.1 => .9,.2 => .8,.3 => .8,.4 => .7,.5 => .5,.6 => .8,.7 => .7,.8 => .6,.9 => .9];
			$matrix[2] = [1 => .9,.1 => .1,.2 => .2,.3 => .3,.4 => .4,.5 => .5,.6 => .8,.7 => .7,.8 => .8,.9 => .9];
			$matrix[3] = [1 => .9,.1 => .1,.2 => .2,.3 => .3,.4 => .4,.5 => .9,.6 => .6,.7 => .7,.8 => .4,.9 => .5];
			$matrix[4] = [1 => -1,.1 => .1,.2 => .2,.3 => .3,.4 => .1,.5 => -.1,.6 => .8,.7 => .1,.8 => .1,.9 => .1];
			$ret = $matrix[$Mode][$Value];
		}

		return $ret;

	}

	/* Draw a rectangle with rounded corners */
	function drawRoundedRectangle($X1, $Y1, $X2, $Y2, $Radius, array $Format = [])
	{
		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;

		list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
		($X2 - $X1 < $Radius) AND $Radius = floor(($X2 - $X1) / 2);
		($Y2 - $Y1 < $Radius) AND $Radius = floor(($Y2 - $Y1) / 2);
		$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"NoBorder" => TRUE];

		if ($Radius <= 0) {
			$this->drawRectangle($X1, $Y1, $X2, $Y2, $Color);
			return (0);
		}

		if ($this->Antialias) {
			$this->drawLine($X1 + $Radius, $Y1, $X2 - $Radius, $Y1, $Color);
			$this->drawLine($X2, $Y1 + $Radius, $X2, $Y2 - $Radius, $Color);
			$this->drawLine($X2 - $Radius, $Y2, $X1 + $Radius, $Y2, $Color);
			$this->drawLine($X1, $Y1 + $Radius, $X1, $Y2 - $Radius, $Color);
		} else {
			$ColorA = $this->allocateColor($R, $G, $B, $Alpha);
			imageline($this->Picture, $X1 + $Radius, $Y1, $X2 - $Radius, $Y1, $ColorA);
			imageline($this->Picture, $X2, $Y1 + $Radius, $X2, $Y2 - $Radius, $ColorA);
			imageline($this->Picture, $X2 - $Radius, $Y2, $X1 + $Radius, $Y2, $ColorA);
			imageline($this->Picture, $X1, $Y1 + $Radius, $X1, $Y2 - $Radius, $ColorA);
		}

		$Step = 360 / (2 * PI * $Radius);
		unset($Color["NoBorder"]);
		for ($i = 0; $i <= 90; $i = $i + $Step) {
			$X = cos(($i + 180) * PI / 180) * $Radius + $X1 + $Radius;
			$Y = sin(($i + 180) * PI / 180) * $Radius + $Y1 + $Radius;
			$this->drawAntialiasPixel($X, $Y, $Color);
			$X = cos(($i + 90) * PI / 180) * $Radius + $X1 + $Radius;
			$Y = sin(($i + 90) * PI / 180) * $Radius + $Y2 - $Radius;
			$this->drawAntialiasPixel($X, $Y, $Color);
			$X = cos($i * PI / 180) * $Radius + $X2 - $Radius;
			$Y = sin($i * PI / 180) * $Radius + $Y2 - $Radius;
			$this->drawAntialiasPixel($X, $Y, $Color);
			$X = cos(($i + 270) * PI / 180) * $Radius + $X2 - $Radius;
			$Y = sin(($i + 270) * PI / 180) * $Radius + $Y1 + $Radius;
			$this->drawAntialiasPixel($X, $Y, $Color);
		}
	}

	/* Draw a rectangle with rounded corners */
	function drawRoundedFilledRectangle($X1, $Y1, $X2, $Y2, $Radius, array $Format = [])
	{
		$R = 0;
		$G = 0;
		$B = 0;
		$Alpha = 100;
		$Surrounding = NULL;
		$BorderR = -1;
		$BorderG = -1;
		$BorderB = -1;

		extract($Format);

		/* Temporary fix for AA issue */
		$Y1 = floor($Y1);
		$Y2 = floor($Y2);
		$X1 = floor($X1);
		$X2 = floor($X2);
		if ($Surrounding != NULL) {
			$BorderR = $R + $Surrounding;
			$BorderG = $G + $Surrounding;
			$BorderB = $B + $Surrounding;
		}

		if ($BorderR == - 1) {
			$BorderR = $R;
			$BorderG = $G;
			$BorderB = $B;
		}

		list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
		if ($X2 - $X1 < $Radius * 2) {
			$Radius = floor((($X2 - $X1)) / 4);
		}

		if ($Y2 - $Y1 < $Radius * 2) {
			$Radius = floor((($Y2 - $Y1)) / 4);
		}

		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawRoundedFilledRectangle($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, $Radius, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa]);
		}

		$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"NoBorder" => TRUE];
		if ($Radius <= 0) {
			$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
			return (0);
		}

		$YTop = $Y1 + $Radius;
		$YBottom = $Y2 - $Radius;
		$Step = 360 / (2 * PI * $Radius);
		$Positions = [];
		$Radius--;
		$MinY = "";
		$MaxY = "";
		for ($i = 0; $i <= 90; $i = $i + $Step) {
			$Xp1 = cos(($i + 180) * PI / 180) * $Radius + $X1 + $Radius;
			$Xp2 = cos(((90 - $i) + 270) * PI / 180) * $Radius + $X2 - $Radius;
			$Yp = floor(sin(($i + 180) * PI / 180) * $Radius + $YTop);
			($MinY == "" || $Yp > $MinY) AND $MinY = $Yp;
			($Xp1 <= floor($X1)) AND $Xp1++;
			($Xp2 >= floor($X2)) AND $Xp2--;
			$Xp1++;
			if (!isset($Positions[$Yp])) {
				$Positions[$Yp]["X1"] = $Xp1;
				$Positions[$Yp]["X2"] = $Xp2;
			} else {
				$Positions[$Yp]["X1"] = ($Positions[$Yp]["X1"] + $Xp1) / 2;
				$Positions[$Yp]["X2"] = ($Positions[$Yp]["X2"] + $Xp2) / 2;
			}

			$Xp1 = cos(($i + 90) * PI / 180) * $Radius + $X1 + $Radius;
			$Xp2 = cos((90 - $i) * PI / 180) * $Radius + $X2 - $Radius;
			$Yp = floor(sin(($i + 90) * PI / 180) * $Radius + $YBottom);
			($MaxY == "" || $Yp < $MaxY) AND $MaxY = $Yp;
			($Xp1 <= floor($X1)) AND $Xp1++;
			($Xp2 >= floor($X2)) AND $Xp2--;
			$Xp1++;
			if (!isset($Positions[$Yp])) {
				$Positions[$Yp]["X1"] = $Xp1;
				$Positions[$Yp]["X2"] = $Xp2;
			} else {
				$Positions[$Yp]["X1"] = ($Positions[$Yp]["X1"] + $Xp1) / 2;
				$Positions[$Yp]["X2"] = ($Positions[$Yp]["X2"] + $Xp2) / 2;
			}
		}

		$ManualColor = $this->allocateColor($R, $G, $B, $Alpha);
		foreach($Positions as $Yp => $Bounds) {
			$X1 = $Bounds["X1"];
			$X1Dec = $this->getFirstDecimal($X1);
			if ($X1Dec != 0) {
				$X1 = floor($X1) + 1;
			}

			$X2 = $Bounds["X2"];
			$X2Dec = $this->getFirstDecimal($X2);
			if ($X2Dec != 0) {
				$X2 = floor($X2) - 1;
			}

			imageline($this->Picture, $X1, $Yp, $X2, $Yp, $ManualColor);
		}

		$this->drawFilledRectangle($X1, $MinY + 1, floor($X2), $MaxY - 1, $Color);
		$Radius++;
		$this->drawRoundedRectangle($X1, $Y1, $X2 + 1, $Y2 - 1, $Radius, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $Alpha]);
		$this->Shadow = $RestoreShadow;
	}

	/* Draw a rectangle with rounded corners */
	function drawRoundedFilledRectangle_deprecated($X1, $Y1, $X2, $Y2, $Radius, array $Format = []) # UNUSED
	{
		$R = 0;
		$G = 0;
		$B = 0;
		$Alpha = 100;
		$Surrounding = NULL;
		$BorderR = -1;
		$BorderG = -1;
		$BorderB = -1;

		extract($Format);

		if ($Surrounding != NULL) {
			$BorderR = $R + $Surrounding;
			$BorderG = $G + $Surrounding;
			$BorderB = $B + $Surrounding;
		}

		if ($BorderR == - 1) {
			$BorderR = $R;
			$BorderG = $G;
			$BorderB = $B;
		}

		list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
		if ($X2 - $X1 < $Radius) {
			$Radius = floor((($X2 - $X1) + 2) / 2);
		}

		if ($Y2 - $Y1 < $Radius) {
			$Radius = floor((($Y2 - $Y1) + 2) / 2);
		}

		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawRoundedFilledRectangle($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, $Radius, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa]);
		}

		$XOffset2 = ($this->getFirstDecimal($X2) >= 5) ? 1 : 0;
		$XOffset1 = ($this->getFirstDecimal($X1) <= 5) ? 1 : 0;

		if (!$this->Antialias) {
			$XOffset1 = 1;
			$XOffset2 = 1;
		}

		$YTop = floor($Y1 + $Radius);
		$YBottom = floor($Y2 - $Radius);
		$this->drawFilledRectangle($X1 - $XOffset1, $YTop, $X2 + $XOffset2, $YBottom, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"NoBorder" => TRUE]);
		$Step = 360 / (2 * PI * $Radius);
		$Color = $this->allocateColor($R, $G, $B, $Alpha);
		$Color2 = $this->allocateColor(255, 0, 0, $Alpha);
		$Drawn = [];
		($Alpha < 100) AND $Drawn[$YTop] = FALSE;
		($Alpha < 100) AND $Drawn[$YBottom] = TRUE;

		for ($i = 0; $i <= 90; $i = $i + $Step) {
			$Xp1 = cos(($i + 180) * PI / 180) * $Radius + $X1 + $Radius;
			$Xp2 = cos(((90 - $i) + 270) * PI / 180) * $Radius + $X2 - $Radius;
			$Yp = sin(($i + 180) * PI / 180) * $Radius + $YTop;
			$XOffset1 = ($this->getFirstDecimal($Xp1) > 5) ? 1 : 0;
			$XOffset2 = ($this->getFirstDecimal($Xp2) > 5) ? 1 : 0;
			$YOffset = ($this->getFirstDecimal($Yp) > 5) ? 1 : 0;

			if (!isset($Drawn[$Yp + $YOffset]) || $Alpha == 100) {
				imageline($this->Picture, $Xp1 + $XOffset1, $Yp + $YOffset, $Xp2 + $XOffset2, $Yp + $YOffset, $Color);
			}

			$Drawn[$Yp + $YOffset] = $Xp2;
			$Xp1 = cos(($i + 90) * PI / 180) * $Radius + $X1 + $Radius;
			$Xp2 = cos((90 - $i) * PI / 180) * $Radius + $X2 - $Radius;
			$Yp = sin(($i + 90) * PI / 180) * $Radius + $YBottom;
			$XOffset1 = ($this->getFirstDecimal($Xp1) > 7) ? 1 : 0;
			$XOffset2 = ($this->getFirstDecimal($Xp2) > 7) ? 1 : 0;
			$YOffset = ($this->getFirstDecimal($Yp) > 5) ? 1 : 0;

			if (!isset($Drawn[$Yp + $YOffset]) || $Alpha == 100) {
				imageline($this->Picture, $Xp1 + $XOffset1, $Yp + $YOffset, $Xp2 + $XOffset2, $Yp + $YOffset, $Color);
			}

			$Drawn[$Yp + $YOffset] = $Xp2;
		}

		$this->drawRoundedRectangle($X1, $Y1, $X2, $Y2, $Radius, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $Alpha]);
		$this->Shadow = $RestoreShadow;
	}

	/* Draw a rectangle */
	function drawRectangle($X1, $Y1, $X2, $Y2, array $Format = [])
	{
		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$NoAngle = isset($Format["NoAngle"]) ? $Format["NoAngle"] : FALSE;

		($X1 > $X2) AND list($X1, $X2) = [$X2,$X1];
		($Y1 > $Y2) AND list($Y1, $Y2) = [$Y2,$Y1];

		$RGB = ["R" => $R, "G" => $G, "B" => $B, "Alpha" => $Alpha, "Ticks" => $Ticks];
		if ($this->Antialias) {
			if ($NoAngle) {
				$this->drawLine($X1 + 1, $Y1, $X2 - 1, $Y1, $RGB);
				$this->drawLine($X2, $Y1 + 1, $X2, $Y2 - 1, $RGB);
				$this->drawLine($X2 - 1, $Y2, $X1 + 1, $Y2, $RGB);
				$this->drawLine($X1, $Y1 + 1, $X1, $Y2 - 1, $RGB);
			} else {
				$this->drawLine($X1 + 1, $Y1, $X2 - 1, $Y1, $RGB);
				$this->drawLine($X2, $Y1, $X2, $Y2, $RGB);
				$this->drawLine($X2 - 1, $Y2, $X1 + 1, $Y2, $RGB);
				$this->drawLine($X1, $Y1, $X1, $Y2, $RGB);
			}
		} else {
			imagerectangle($this->Picture, $X1, $Y1, $X2, $Y2, $this->allocateColor($R, $G, $B, $Alpha));
		}
	}

	/* Draw a filled rectangle */
	function drawFilledRectangle($X1, $Y1, $X2, $Y2, array $Format = [])
	{

		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
		$NoBorder = isset($Format["NoBorder"]) ? $Format["NoBorder"] : FALSE;
		$Surrounding = isset($Format["Surrounding"]) ? $Format["Surrounding"] : NULL;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$BorderR = isset($Format["BorderR"]) ? $Format["BorderR"] : -1;
		$BorderG = isset($Format["BorderG"]) ? $Format["BorderG"] : -1;
		$BorderB = isset($Format["BorderB"]) ? $Format["BorderB"] : -1;
		$BorderAlpha = $Alpha;
		$NoAngle = isset($Format["NoAngle"]) ? $Format["NoAngle"] : NULL;
		$Dash = isset($Format["Dash"]) ? $Format["Dash"] : FALSE;
		$DashStep = isset($Format["DashStep"]) ? $Format["DashStep"] : 4;
		$DashR = isset($Format["DashR"]) ? $Format["DashR"] : 0;
		$DashG = isset($Format["DashG"]) ? $Format["DashG"] : 0;
		$DashB = isset($Format["DashB"]) ? $Format["DashB"] : 0;

		if ($Surrounding != NULL) {
			$BorderR = $R + $Surrounding;
			$BorderG = $G + $Surrounding;
			$BorderB = $B + $Surrounding;
		}

		($X1 > $X2) AND list($X1, $X2) = [$X2,$X1];
		($Y1 > $Y2) AND list($Y1, $Y2) = [$Y2,$Y1];

		$X1c = ceil($X1);
		$Y1c = ceil($Y1);
		$X2f = floor($X2);
		$Y2f = floor($Y2);

		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawFilledRectangle($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa,"Ticks" => $Ticks,"NoAngle" => $NoAngle]);
		}

		$Color = $this->allocateColor($R, $G, $B, $Alpha);
		if ($NoAngle) {
			imagefilledrectangle($this->Picture, $X1c + 1, $Y1c, $X2f - 1, $Y2f, $Color);
			imageline($this->Picture, $X1c, $Y1c + 1, $X1c, $Y2f - 1, $Color);
			imageline($this->Picture, $X2f, $Y1c + 1, $X2f, $Y2f - 1, $Color);
		} else {
			imagefilledrectangle($this->Picture, $X1c, $Y1c, $X2f, $Y2f, $Color);
		}

		if ($Dash) {
			if ($BorderR != - 1) {
				$iX1 = $X1 + 1;
				$iY1 = $Y1 + 1;
				$iX2 = $X2 - 1;
				$iY2 = $Y2 - 1;
			} else {
				$iX1 = $X1;
				$iY1 = $Y1;
				$iX2 = $X2;
				$iY2 = $Y2;
			}

			$Color = $this->allocateColor($DashR, $DashG, $DashB, $Alpha);
			$Y = $iY1 - $DashStep;
			for ($X = $iX1; $X <= $iX2 + ($iY2 - $iY1); $X = $X + $DashStep) {
				$Y = $Y + $DashStep;
				if ($X > $iX2) {
					$Xa = $X - ($X - $iX2);
					$Ya = $iY1 + ($X - $iX2);
				} else {
					$Xa = $X;
					$Ya = $iY1;
				}

				if ($Y > $iY2) {
					$Xb = $iX1 + ($Y - $iY2);
					$Yb = $Y - ($Y - $iY2);
				} else {
					$Xb = $iX1;
					$Yb = $Y;
				}

				imageline($this->Picture, $Xa, $Ya, $Xb, $Yb, $Color);
			}
		}

		if ($this->Antialias && !$NoBorder) {
			if ($X1 < $X1c) {
				$AlphaA = $Alpha * ($X1c - $X1);
				$Color = $this->allocateColor($R, $G, $B, $AlphaA);
				imageline($this->Picture, $X1c - 1, $Y1c, $X1c - 1, $Y2f, $Color);
			}

			if ($Y1 < $Y1c) {
				$AlphaA = $Alpha * ($Y1c - $Y1);
				$Color = $this->allocateColor($R, $G, $B, $AlphaA);
				imageline($this->Picture, $X1c, $Y1c - 1, $X2f, $Y1c - 1, $Color);
			}

			if ($X2 > $X2f) {
				$AlphaA = $Alpha * (.5 - ($X2 - $X2f));
				$Color = $this->allocateColor($R, $G, $B, $AlphaA);
				imageline($this->Picture, $X2f + 1, $Y1c, $X2f + 1, $Y2f, $Color);
			}

			if ($Y2 > $Y2f) {
				$AlphaA = $Alpha * (.5 - ($Y2 - $Y2f));
				$Color = $this->allocateColor($R, $G, $B, $AlphaA);
				imageline($this->Picture, $X1c, $Y2f + 1, $X2f, $Y2f + 1, $Color);
			}
		}

		if ($BorderR != - 1) {
			$this->drawRectangle($X1, $Y1, $X2, $Y2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $Ticks,"NoAngle" => $NoAngle]);
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Draw a rectangular marker of the specified size */
	function drawRectangleMarker($X, $Y, array $Format = [])
	{
		$Size = isset($Format["Size"]) ? $Format["Size"] : 4;
		$HalfSize = floor($Size / 2);
		$this->drawFilledRectangle($X - $HalfSize, $Y - $HalfSize, $X + $HalfSize, $Y + $HalfSize, $Format);
	}

	/* Drawn a spline based on the bezier function */
	function drawSpline(array $Coordinates, array $Format = []): array {

		#$R = 0; # UNUSED
		#$G = 0;
		#$B = 0;
		#$Alpha = 100;
		#$Ticks = NULL;
		$PathOnly = FALSE;
		#$Weight = NULL;
		#$ShowC = FALSE;
		$Force = 30;
		$Forces = NULL;

		extract($Format);

		#$Cpt = NULL; # UNUSED
		#$Mode = NULL;
		$Result = [];
		for ($i = 1; $i <= count($Coordinates) - 1; $i++) {
			$X1 = $Coordinates[$i - 1][0];
			$Y1 = $Coordinates[$i - 1][1];
			$X2 = $Coordinates[$i][0];
			$Y2 = $Coordinates[$i][1];
			if ($Forces != NULL) {
				$Force = $Forces[$i];
			}

			/* First segment */
			if ($i == 1) {
				$Xv1 = $X1;
				$Yv1 = $Y1;
			} else {
				$Angle1 = $this->getAngle($XLast, $YLast, $X1, $Y1);
				$Angle2 = $this->getAngle($X1, $Y1, $X2, $Y2);
				$XOff = cos($Angle2 * PI / 180) * $Force + $X1;
				$YOff = sin($Angle2 * PI / 180) * $Force + $Y1;
				$Xv1 = cos($Angle1 * PI / 180) * $Force + $XOff;
				$Yv1 = sin($Angle1 * PI / 180) * $Force + $YOff;
			}

			/* Last segment */
			if ($i == count($Coordinates) - 1) {
				$Xv2 = $X2;
				$Yv2 = $Y2;
			} else {
				$Angle1 = $this->getAngle($X2, $Y2, $Coordinates[$i + 1][0], $Coordinates[$i + 1][1]);
				$Angle2 = $this->getAngle($X1, $Y1, $X2, $Y2);
				$XOff = cos(($Angle2 + 180) * PI / 180) * $Force + $X2;
				$YOff = sin(($Angle2 + 180) * PI / 180) * $Force + $Y2;
				$Xv2 = cos(($Angle1 + 180) * PI / 180) * $Force + $XOff;
				$Yv2 = sin(($Angle1 + 180) * PI / 180) * $Force + $YOff;
			}

			$Path = $this->drawBezier($X1, $Y1, $X2, $Y2, $Xv1, $Yv1, $Xv2, $Yv2, $Format);
			if ($PathOnly) {
				$Result[] = $Path;
			}

			$XLast = $X1;
			$YLast = $Y1;
		}

		return $Result;
	}

	/* Draw a bezier curve with two controls points */
	function drawBezier($X1, $Y1, $X2, $Y2, $Xv1, $Yv1, $Xv2, $Yv2, array $Format = [])
	{

		$R = 0;
		$G = 0;
		$B = 0;
		$Alpha = 100;
		$Segments = NULL;
		$Ticks = NULL;
		$NoDraw = FALSE;
		$PathOnly = FALSE;
		$Weight = NULL;
		$ShowC = FALSE;
		$DrawArrow = FALSE;
		$ArrowSize = 10;
		$ArrowRatio = .5;
		$ArrowTwoHeads = FALSE;

		extract($Format);

		if ($Segments == NULL) {
			$Length = $this->getLength($X1, $Y1, $X2, $Y2);
			$Precision = ($Length * 125) / 1000;
		} else {
			$Precision = $Segments;
		}

		$P = [
			0 => ["X" => $X1, "Y" => $Y1],
			1 => ["X" => $Xv1, "Y" => $Yv1],
			2 => ["X" => $Xv2, "Y" => $Yv2],
			3 => ["X" => $X2, "Y" => $Y2]
		];

		/* Compute the bezier points */
		$Q = [];
		$ID = 0; //$Path = ""; # UNUSED
		for ($i = 0; $i <= $Precision; $i = $i + 1) {
			$u = $i / $Precision;
			$C = [
				0 => (1 - $u) * (1 - $u) * (1 - $u),
				1 => ($u * 3) * (1 - $u) * (1 - $u),
				2 => 3 * $u * $u * (1 - $u),
				3 => $u * $u * $u
			];
			for ($j = 0; $j <= 3; $j++) {
				(!isset($Q[$ID])) AND $Q[$ID] = [];
				(!isset($Q[$ID]["X"])) AND $Q[$ID]["X"] = 0;
				(!isset($Q[$ID]["Y"])) AND $Q[$ID]["Y"] = 0;
				$Q[$ID]["X"] = $Q[$ID]["X"] + $P[$j]["X"] * $C[$j];
				$Q[$ID]["Y"] = $Q[$ID]["Y"] + $P[$j]["Y"] * $C[$j];
			}

			$ID++;
		}

		$Q[$ID]["X"] = $X2;
		$Q[$ID]["Y"] = $Y2;
		if (!$NoDraw) {
			/* Display the control points */
			if ($ShowC && !$PathOnly) {
				$Xv1 = floor($Xv1);
				$Yv1 = floor($Yv1);
				$Xv2 = floor($Xv2);
				$Yv2 = floor($Yv2);
				$this->drawLine($X1, $Y1, $X2, $Y2, ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 30]);
				$MyMarkerSettings = ["R" => 255,"G" => 0,"B" => 0,"BorderR" => 255,"BorderB" => 255,"BorderG" => 255,"Size" => 4];
				$this->drawRectangleMarker($Xv1, $Yv1, $MyMarkerSettings);
				$this->drawText($Xv1 + 4, $Yv1, "v1");
				$MyMarkerSettings = ["R" => 0,"G" => 0,"B" => 255,"BorderR" => 255,"BorderB" => 255,"BorderG" => 255,"Size" => 4];
				$this->drawRectangleMarker($Xv2, $Yv2, $MyMarkerSettings);
				$this->drawText($Xv2 + 4, $Yv2, "v2");
			}

			/* Draw the bezier */
			$LastX = NULL;
			$LastY = NULL;
			$Cpt = NULL;
			$Mode = NULL;
			$ArrowS = [];
			$ArrowE = [];
			foreach($Q as $Key => $Point) {
				$X = $Point["X"];
				$Y = $Point["Y"];
				/* Get the first segment */
				if (count($ArrowS) == 0 && $LastX != NULL && $LastY != NULL) {
					$ArrowS["X2"] = $LastX;
					$ArrowS["Y2"] = $LastY;
					$ArrowS["X1"] = $X;
					$ArrowS["Y1"] = $Y;
				}

				if ($LastX != NULL && $LastY != NULL && !$PathOnly) {
					list($Cpt, $Mode) = $this->drawLine($LastX, $LastY, $X, $Y, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Cpt" => $Cpt,"Mode" => $Mode,"Weight" => $Weight]);
				}
				/* Get the last segment */
				$ArrowE["X1"] = $LastX;
				$ArrowE["Y1"] = $LastY;
				$ArrowE["X2"] = $X;
				$ArrowE["Y2"] = $Y;
				$LastX = $X;
				$LastY = $Y;
			}

			if ($DrawArrow && !$PathOnly) {
				$ArrowSettings = ["FillR" => $R,"FillG" => $G,"FillB" => $B,"Alpha" => $Alpha,"Size" => $ArrowSize,"Ratio" => $ArrowRatio];
				if ($ArrowTwoHeads) {
					$this->drawArrow($ArrowS["X1"], $ArrowS["Y1"], $ArrowS["X2"], $ArrowS["Y2"], $ArrowSettings);
				}
				$this->drawArrow($ArrowE["X1"], $ArrowE["Y1"], $ArrowE["X2"], $ArrowE["Y2"], $ArrowSettings);
			}
		}

		return ($Q);
	}

	/* Draw a line between two points */
	function drawLine($X1, $Y1, $X2, $Y2, array $Format = []) # FAST
	{
		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
		$Cpt = isset($Format["Cpt"]) ? $Format["Cpt"] : 1;
		$Threshold = isset($Format["Threshold"]) ? $Format["Threshold"] : NULL;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$Weight = isset($Format["Weight"]) ? $Format["Weight"] : NULL;
		$Mode = isset($Format["Mode"]) ? $Format["Mode"] : 1;

		if ($this->Antialias == FALSE && $Ticks == NULL) {
			if ($this->Shadow) {
				$ShadowColor = $this->allocateColor($this->ShadowR, $this->ShadowG, $this->ShadowB, $this->Shadowa);
				imageline($this->Picture, $X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, $ShadowColor);
			}

			$Color = $this->allocateColor($R, $G, $B, $Alpha);
			imageline($this->Picture, $X1, $Y1, $X2, $Y2, $Color);
			return (0);
		}

		$Distance = sqrt(($X2 - $X1) * ($X2 - $X1) + ($Y2 - $Y1) * ($Y2 - $Y1));
		if ($Distance == 0) {
			return (-1);
		}

		/* Derivative algorithm for overweighted lines, re-route to polygons primitives */
		if ($Weight != NULL) {
			$Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
			$PolySettings = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"BorderAlpha" => $Alpha];
			$AngleCosPlus90 = cos(deg2rad($Angle + 90)) * $Weight; // Momchil
			$AngleCosMinus90 = cos(deg2rad($Angle - 90)) * $Weight;
			$AngleSinPlus90 = sin(deg2rad($Angle + 90)) * $Weight; // Momchil
			$AngleSinMinus90 = sin(deg2rad($Angle - 90)) * $Weight;
			if ($Ticks == NULL) {
				$Points = [$AngleCosMinus90 + $X1, $AngleSinMinus90 + $Y1, $AngleCosPlus90 + $X1, $AngleSinPlus90 + $Y1, $AngleCosPlus90 + $X2, $AngleSinPlus90 + $Y2, $AngleCosMinus90 + $X2, $AngleSinMinus90 + $Y2];
				$this->drawPolygon($Points, $PolySettings);
			} else {
				for ($i = 0; $i <= $Distance; $i = $i + $Ticks * 2) {
					$Xa = (($X2 - $X1) / $Distance) * $i + $X1;
					$Ya = (($Y2 - $Y1) / $Distance) * $i + $Y1;
					$Xb = (($X2 - $X1) / $Distance) * ($i + $Ticks) + $X1;
					$Yb = (($Y2 - $Y1) / $Distance) * ($i + $Ticks) + $Y1;
					$Points = [$AngleCosMinus90 + $Xa, $AngleSinMinus90 + $Ya, $AngleCosPlus90 + $Xa, $AngleSinPlus90 + $Ya, $AngleCosPlus90 + $Xb, $AngleSinPlus90 + $Yb, $AngleCosMinus90 + $Xb, $AngleSinMinus90 + $Yb];
					$this->drawPolygon($Points, $PolySettings);
				}
			}

			return (1);
		}

		$XStep = ($X2 - $X1) / $Distance;
		$YStep = ($Y2 - $Y1) / $Distance;
		$defaultColor = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha];

		if ($Threshold == NULL && $Ticks == NULL){ # Momchil: Fast path based on my test cases

			for ($i = 0; $i <= $Distance; $i++) {
				$this->drawAntialiasPixel($i * $XStep + $X1, $i * $YStep + $Y1, $defaultColor);
			}

		} else {

			for ($i = 0; $i <= $Distance; $i++) {
				$X = $i * $XStep + $X1;
				$Y = $i * $YStep + $Y1;
				$Color = $defaultColor;

				if ($Threshold != NULL) {
					foreach($Threshold as $Key => $Parameters) {
						if ($Y <= $Parameters["MinX"] && $Y >= $Parameters["MaxX"]) {
							$RT = (isset($Parameters["R"])) ? $Parameters["R"] : 0;
							$GT = (isset($Parameters["G"])) ? $Parameters["G"] : 0;
							$BT = (isset($Parameters["B"])) ? $Parameters["B"] : 0;
							$AlphaT = (isset($Parameters["Alpha"])) ? $Parameters["Alpha"] : 0;
							$Color = ["R" => $RT,"G" => $GT,"B" => $BT,"Alpha" => $AlphaT];
						}
					}
				}

				if ($Ticks != NULL) {
					if ($Cpt % $Ticks == 0) {
						$Cpt = 0;
						$Mode = ($Mode == 1) ? 0 : 1;
					}
					($Mode == 1) AND $this->drawAntialiasPixel($X, $Y, $Color);
					$Cpt++;
				} else {
					$this->drawAntialiasPixel($X, $Y, $Color);
				}
			}

		}

		return [$Cpt,$Mode];
	}

	/* Draw a circle */
	function drawCircle($Xc, $Yc, $Height, $Width, array $Format = [])
	{

		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;

		$Height = abs($Height);
		$Width = abs($Width);
		($Height == 0) AND $Height = 1;
		($Width == 0) AND $Width = 1;
		$Xc = floor($Xc);
		$Yc = floor($Yc);
		$RestoreShadow = $this->Shadow;

		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawCircle($Xc + $this->ShadowX, $Yc + $this->ShadowY, $Height, $Width, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa,"Ticks" => $Ticks]);
		}

		($Width == 0) AND $Width = $Height;
		#($R < 0) AND $R = 0; # # Will be done in drawAntialiasPixel anyway
		#($R > 255) AND $R = 255;
		#($G < 0) AND $G = 0;
		#($G > 255) AND $G = 255;
		#($B < 0) AND $B = 0;
		#($B > 255) AND $B = 255;

		$Step = 360 / (2 * PI * max($Width, $Height));
		$Mode = 1;
		$Cpt = 1;

		for ($i = 0; $i <= 360; $i = $i + $Step) {
			$X = cos($i * PI / 180) * $Height + $Xc;
			$Y = sin($i * PI / 180) * $Width + $Yc;
			if ($Ticks != NULL) {
				if ($Cpt % $Ticks == 0) {
					$Cpt = 0;
					$Mode = ($Mode == 1) ? 0 : 1;
				}

				if ($Mode == 1) {
					$this->drawAntialiasPixel($X, $Y, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha]);
				}

				$Cpt++;
			} else {
				$this->drawAntialiasPixel($X, $Y, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha]);
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Draw a filled circle */
	function drawFilledCircle($X, $Y, $Radius, array $Format = [])
	{

		$R = 0;
		$G = 0;
		$B = 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
		$Surrounding = NULL;
		$Ticks = NULL;
		$BorderR = -1;
		$BorderG = -1;
		$BorderB = -1;
		$BorderAlpha = $Alpha;

		extract($Format);

		($Radius == 0) AND $Radius = 1;

		if ($Surrounding != NULL) {
			$BorderR = $R + $Surrounding;
			$BorderG = $G + $Surrounding;
			$BorderB = $B + $Surrounding;
		}

		$X = floor($X);
		$Y = floor($Y);
		$Radius = abs($Radius);
		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawFilledCircle($X + $this->ShadowX, $Y + $this->ShadowY, $Radius, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa,"Ticks" => $Ticks]);
		}

		$this->Mask = [];
		$Color = $this->allocateColor($R, $G, $B, $Alpha);
		for ($i = 0; $i <= $Radius * 2; $i++) {
			$Slice = sqrt($Radius * $Radius - ($Radius - $i) * ($Radius - $i));
			$XPos = floor($Slice);
			$YPos = $Y + $i - $Radius;
			$AAlias = $Slice - floor($Slice);
			$this->Mask[$X - $XPos][$YPos] = TRUE;
			$this->Mask[$X + $XPos][$YPos] = TRUE;
			imageline($this->Picture, $X - $XPos, $YPos, $X + $XPos, $YPos, $Color);
		}

		if ($this->Antialias) {
			$this->drawCircle($X, $Y, $Radius, $Radius, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks]);
		}

		$this->Mask = [];
		if ($BorderR != - 1) {
			$this->drawCircle($X, $Y, $Radius, $Radius, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $Ticks]);
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Write text */
	function drawText($X, $Y, $Text, array $Format = [])
	{
		$R = $this->FontColorR;
		$G = $this->FontColorG;
		$B = $this->FontColorB;
		$Angle = 0;
		$Align = TEXT_ALIGN_BOTTOMLEFT;
		$Alpha = $this->FontColorA;
		$FontName = $this->FontName;
		$FontSize = $this->FontSize;
		$ShowOrigine = FALSE;
		$TOffset = 2;
		$DrawBox = FALSE;
		$DrawBoxBorder = TRUE;
		$BorderOffset = 6;
		$BoxRounded = FALSE;
		$RoundedRadius = 6;
		$BoxR = 255;
		$BoxG = 255;
		$BoxB = 255;
		$BoxAlpha = 50;
		$BoxSurrounding = "";
		$BoxBorderR = isset($Format["BoxR"]) ? $Format["BoxR"] : 0;
		$BoxBorderG = isset($Format["BoxG"]) ? $Format["BoxG"] : 0;
		$BoxBorderB = isset($Format["BoxB"]) ? $Format["BoxB"] : 0;
		$BoxBorderAlpha = isset($Format["BoxAlpha"]) ? $Format["BoxAlpha"] : 50;
		$NoShadow = FALSE;

		/* Override defaults */
		extract($Format);

		$Shadow = $this->Shadow;
		($NoShadow) AND $this->Shadow = FALSE;

		if ($BoxSurrounding != "") {
			$BoxBorderR = $BoxR - $BoxSurrounding;
			$BoxBorderG = $BoxG - $BoxSurrounding;
			$BoxBorderB = $BoxB - $BoxSurrounding;
			$BoxBorderAlpha = $BoxAlpha;
		}

		if ($ShowOrigine) {
			$MyMarkerSettings = ["R" => 255,"G" => 0,"B" => 0,"BorderR" => 255,"BorderB" => 255,"BorderG" => 255,"Size" => 4];
			$this->drawRectangleMarker($X, $Y, $MyMarkerSettings);
		}

		$TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, $Angle, $Text);
		if ($DrawBox && ($Angle == 0 || $Angle == 90 || $Angle == 180 || $Angle == 270)) {
			$T = [0 => ["X" => 0, "Y" => 0]];
			#$T[0]["X"] = 0;
			#$T[0]["Y"] = 0;
			#$T[1]["X"] = 0; # Momchil: Not used
			#$T[1]["Y"] = 0;
			#$T[2]["X"] = 0;
			#$T[2]["Y"] = 0;
			#$T[3]["X"] = 0;
			#$T[3]["Y"] = 0;
			if ($Angle == 0) {
				$T = [0 => ["X" => - $TOffset, "Y" => $TOffset]];
				#$T[0]["X"] = - $TOffset;
				#$T[0]["Y"] = $TOffset;
				#$T[1]["X"] = $TOffset;
				#$T[1]["Y"] = $TOffset;
				#$T[2]["X"] = $TOffset;
				#$T[2]["Y"] = - $TOffset;
				#$T[3]["X"] = - $TOffset;
				#$T[3]["Y"] = - $TOffset;
			}

			$X1 = min($TxtPos[0]["X"], $TxtPos[1]["X"], $TxtPos[2]["X"], $TxtPos[3]["X"]) - $BorderOffset + 3;
			$Y1 = min($TxtPos[0]["Y"], $TxtPos[1]["Y"], $TxtPos[2]["Y"], $TxtPos[3]["Y"]) - $BorderOffset;
			$X2 = max($TxtPos[0]["X"], $TxtPos[1]["X"], $TxtPos[2]["X"], $TxtPos[3]["X"]) + $BorderOffset + 3;
			$Y2 = max($TxtPos[0]["Y"], $TxtPos[1]["Y"], $TxtPos[2]["Y"], $TxtPos[3]["Y"]) + $BorderOffset - 3;
			$X1 = $X1 - $TxtPos[$Align]["X"] + $X + $T[0]["X"];
			$Y1 = $Y1 - $TxtPos[$Align]["Y"] + $Y + $T[0]["Y"];
			$X2 = $X2 - $TxtPos[$Align]["X"] + $X + $T[0]["X"];
			$Y2 = $Y2 - $TxtPos[$Align]["Y"] + $Y + $T[0]["Y"];
			$Settings = ["R" => $BoxR,"G" => $BoxG,"B" => $BoxB,"Alpha" => $BoxAlpha,"BorderR" => $BoxBorderR,"BorderG" => $BoxBorderG,"BorderB" => $BoxBorderB,"BorderAlpha" => $BoxBorderAlpha];
			if ($BoxRounded) {
				$this->drawRoundedFilledRectangle($X1, $Y1, $X2, $Y2, $RoundedRadius, $Settings);
			} else {
				$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Settings);
			}
		}

		$X = $X - $TxtPos[$Align]["X"] + $X;
		$Y = $Y - $TxtPos[$Align]["Y"] + $Y;
		if ($this->Shadow) {
			$C_ShadowColor = $this->allocateColor($this->ShadowR, $this->ShadowG, $this->ShadowB, $this->Shadowa);
			imagettftext($this->Picture, $FontSize, $Angle, $X + $this->ShadowX, $Y + $this->ShadowY, $C_ShadowColor, $FontName, $Text);
		}

		$C_TextColor = $this->AllocateColor($R, $G, $B, $Alpha);
		imagettftext($this->Picture, $FontSize, $Angle, $X, $Y, $C_TextColor, $FontName, $Text);
		$this->Shadow = $Shadow;

		return ($TxtPos);
	}

	/* Draw a gradient within a defined area */
	function drawGradientArea($X1, $Y1, $X2, $Y2, $Direction, array $Format = [])
	{
		$StartR = 90;
		$StartG = 90;
		$StartB = 90;
		$EndR = 0;
		$EndG = 0;
		$EndB = 0;
		$Alpha = 100;
		$Levels = NULL;

		/* Draw a gradient within a defined area */
		extract($Format);

		$Shadow = $this->Shadow;
		$this->Shadow = FALSE;
		if ($StartR == $EndR && $StartG == $EndG && $StartB == $EndB) {
			$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["R" => $StartR,"G" => $StartG,"B" => $StartB,"Alpha" => $Alpha]);
			return (0);
		}

		if ($Levels != NULL) {
			$EndR = $StartR + $Levels;
			$EndG = $StartG + $Levels;
			$EndB = $StartB + $Levels;
		}

		($X1 > $X2) AND list($X1, $X2) = [$X2,$X1];
		($Y1 > $Y2) AND list($Y1, $Y2) = [$Y2,$Y1];
		($Direction == DIRECTION_VERTICAL) AND $Width = abs($Y2 - $Y1);
		($Direction == DIRECTION_HORIZONTAL) AND $Width = abs($X2 - $X1);

		$Step = max(abs($EndR - $StartR), abs($EndG - $StartG), abs($EndB - $StartB));
		$StepSize = $Width / $Step;
		$RStep = ($EndR - $StartR) / $Step;
		$GStep = ($EndG - $StartG) / $Step;
		$BStep = ($EndB - $StartB) / $Step;
		$R = $StartR;
		$G = $StartG;
		$B = $StartB;
		switch ($Direction) {
			case DIRECTION_VERTICAL:
				$StartY = $Y1;
				$EndY = floor($Y2) + 1;
				$LastY2 = $StartY;
				for ($i = 0; $i <= $Step; $i++) {
					$Y2 = floor($StartY + ($i * $StepSize));
					($Y2 > $EndY) AND $Y2 = $EndY;
					if (($Y1 != $Y2 && $Y1 < $Y2) || $Y2 == $EndY) {
						$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha];
						$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
						$LastY2 = max($LastY2, $Y2);
						$Y1 = $Y2 + 1;
					}

					$R = $R + $RStep;
					$G = $G + $GStep;
					$B = $B + $BStep;
				}

				if ($LastY2 < $EndY && isset($Color)) {
					for ($i = $LastY2 + 1; $i <= $EndY; $i++) {
						$this->drawLine($X1, $i, $X2, $i, $Color);
					}
				}

				break;
			case DIRECTION_HORIZONTAL:
				$StartX = $X1;
				$EndX = $X2;
				for ($i = 0; $i <= $Step; $i++) {
					$X2 = floor($StartX + ($i * $StepSize));
					if ($X2 > $EndX) {
						$X2 = $EndX;
					}

					if (($X1 != $X2 && $X1 < $X2) || $X2 == $EndX) {
						$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha];
						$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Color);
						$X1 = $X2 + 1;
					}

					$R = $R + $RStep;
					$G = $G + $GStep;
					$B = $B + $BStep;
				}

				if ($X2 < $EndX && isset($Color)) {
					$this->drawFilledRectangle($X2, $Y1, $EndX, $Y2, $Color);
				}
				break;
		}

		$this->Shadow = $Shadow;
	}

	/* Draw an aliased pixel */
	function drawAntialiasPixel($X, $Y, array $Format = [])
	{

		if ($X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize){
			return (-1);
		}

		# Momchil: This one is actually faster than extract
		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;

		($R < 0)	AND $R = 0;
		($R > 255)	AND $R = 255;
		($G < 0) 	AND $G = 0;
		($G > 255) 	AND $G = 255;
		($B < 0) 	AND $B = 0;
		($B > 255) 	AND $B = 255;

		if (!$this->Antialias) {
			if ($this->Shadow) {
				imagesetpixel($this->Picture, $X + $this->ShadowX, $Y + $this->ShadowY, $this->allocateColor($this->ShadowR, $this->ShadowG, $this->ShadowB, $this->Shadowa));
			}

			imagesetpixel($this->Picture, $X, $Y, $this->allocateColor($R, $G, $B, $Alpha));
			return (0);
		}

		// $Plot = ""; # UNUSED

		$Xi = floor($X);
		$Yi = floor($Y);

		if ($Xi == $X && $Yi == $Y) {

			$this->drawAlphaPixel($X, $Y, $Alpha, $R, $G, $B, true);

		} else {

			$Yleaf = $Y - $Yi;
			$Xleaf = $X - $Xi;

			# Momchil: That allows to skip the check in drawAlphaPixel and reuse the safe param
			if (($Xi + 1) >= $this->XSize || ($Yi + 1) >= $this->YSize){
				return (-1);
			}

			# Momchil: well worth the local var
			$AntialiasQuality = $this->AntialiasQuality;

			# Momchil: Fast path: mostly zeroes in my test cases
			# AntialiasQuality does not seem to be in use and is always 0
			# $Xleaf is always > 0 && $Yleaf > 0 => $AlphaX > 0
			if ($AntialiasQuality == 0) {
				switch(TRUE){
					case ($Yleaf == 0):
						$this->drawAlphaPixel($Xi, $Yi, (1 - $Xleaf) * $Alpha, $R, $G, $B, true);
						$this->drawAlphaPixel($Xi + 1, $Yi, $Xleaf * $Alpha, $R, $G, $B, true);
						break;
					case ($Xleaf == 0):
						$this->drawAlphaPixel($Xi, $Yi, (1 - $Yleaf) * $Alpha, $R, $G, $B, true);
						$this->drawAlphaPixel($Xi, $Yi + 1, $Yleaf * $Alpha, $R, $G, $B, true);
						break;
					default:
						$this->drawAlphaPixel($Xi, $Yi, ((1 - $Xleaf) * (1 - $Yleaf) * $Alpha), $R, $G, $B, true);
						$this->drawAlphaPixel($Xi + 1, $Yi, ($Xleaf * (1 - $Yleaf) * $Alpha), $R, $G, $B, true);
						$this->drawAlphaPixel($Xi, $Yi + 1, (1 - $Xleaf) * $Yleaf * $Alpha, $R, $G, $B, true);
						$this->drawAlphaPixel($Xi + 1, $Yi + 1, ($Xleaf * $Yleaf * $Alpha), $R, $G, $B, true);
				}
			} else { # Momchil: no changes here
				# Momchil: *100/100 seems redundand
				#$Alpha1 = (((1 - $Xleaf) * (1 - $Yleaf) * 100) / 100) * $Alpha;
				$Alpha1 = (1 - $Xleaf) * (1 - $Yleaf) * $Alpha;
				if ($Alpha1 > $AntialiasQuality) {
					$this->drawAlphaPixel($Xi, $Yi, $Alpha1, $R, $G, $B, true);
				}

				#$Alpha2 = (($Xleaf * (1 - $Yleaf) * 100) / 100) * $Alpha;
				$Alpha2 = $Xleaf * (1 - $Yleaf) * $Alpha;
				if ($Alpha2 > $AntialiasQuality) {
					$this->drawAlphaPixel($Xi + 1, $Yi, $Alpha2, $R, $G, $B, true);
				}

				#$Alpha3 = (((1 - $Xleaf) * $Yleaf * 100) / 100) * $Alpha;
				$Alpha3 = (1 - $Xleaf) * $Yleaf * $Alpha;
				if ($Alpha3 > $AntialiasQuality) {
					$this->drawAlphaPixel($Xi, $Yi + 1, $Alpha3, $R, $G, $B, true);
				}

				#$Alpha4 = (($Xleaf * $Yleaf * 100) / 100) * $Alpha;
				$Alpha4 = $Xleaf * $Yleaf * $Alpha;
				if ($Alpha4 > $AntialiasQuality) {
					$this->drawAlphaPixel($Xi + 1, $Yi + 1, $Alpha4, $R, $G, $B, true);
				}
			}

		}
	}

	/* Draw a semi-transparent pixel */
	function drawAlphaPixel($X, $Y, $Alpha, $R, $G, $B, $safe = FALSE)
	{

		if (isset($this->Mask[$X])) {
			if (isset($this->Mask[$X][$Y])) {
				return (0);
			}
		}

		if ($this->Shadow) {
			imagesetpixel($this->Picture, $X + $this->ShadowX, $Y + $this->ShadowY, $this->allocateColor($this->ShadowR, $this->ShadowG, $this->ShadowB, floor(($Alpha / 100) * $this->Shadowa)));
		}

		if (!$safe){ # Momchil: Seems to be worth the micro optimization

			if ($X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize) {
				return (-1);
			}

			($R < 0)	AND $R = 0;
			($R > 255)	AND $R = 255;
			($G < 0) 	AND $G = 0;
			($G > 255) 	AND $G = 255;
			($B < 0) 	AND $B = 0;
			($B > 255) 	AND $B = 255;
		}

		imagesetpixel($this->Picture, $X, $Y, $this->allocateColor($R, $G, $B, $Alpha));
	}

	/* Allocate a color with transparency */
	function allocateColor($R, $G, $B, $Alpha = 100)
	{
		if (!isset($this->aColorCache["$R.$G.$B.$Alpha"])){
			($R < 0)	AND $R = 0;
			($R > 255) 	AND $R = 255;
			($G < 0) 	AND $G = 0;
			($G > 255) 	AND $G = 255;
			($B < 0) 	AND $B = 0;
			($B > 255) 	AND $B = 255;
			($Alpha < 0) 	AND $Alpha = 0;
			($Alpha > 100) 	AND $Alpha = 100;

			$this->aColorCache["$R.$G.$B.$Alpha"] = imagecolorallocatealpha($this->Picture, $R, $G, $B, (1.27 * (100 - $Alpha)));
		}

		return $this->aColorCache["$R.$G.$B.$Alpha"];
	}

	/* Load a PNG file and draw it over the chart */
	function drawFromPNG($X, $Y, $FileName)
	{
		$this->drawFromPicture(1, $FileName, $X, $Y);
	}

	/* Load a GIF file and draw it over the chart */
	function drawFromGIF($X, $Y, $FileName)
	{
		$this->drawFromPicture(2, $FileName, $X, $Y);
	}

	/* Load a JPEG file and draw it over the chart */
	function drawFromJPG($X, $Y, $FileName)
	{
		$this->drawFromPicture(3, $FileName, $X, $Y);
	}

	function getPicInfo($FileName)
	{
		$Infos = getimagesize($FileName);
		$Width = $Infos[0];
		$Height = $Infos[1];
		$Type = $Infos["mime"];
		($Type == "image/png") AND $Type = 1;
		($Type == "image/gif") AND $Type = 2;
		($Type == "image/jpeg ") AND $Type = 3;

		return [$Width,$Height,$Type];
	}

	/* Generic loader function for external pictures */
	function drawFromPicture($PicType, $FileName, $X, $Y)
	{
		if (file_exists($FileName)) {
			list($Width, $Height) = $this->getPicInfo($FileName);
			if ($PicType == 1) {
				$Raster = imagecreatefrompng($FileName);
			} elseif ($PicType == 2) {
				$Raster = imagecreatefromgif($FileName);
			} elseif ($PicType == 3) {
				$Raster = imagecreatefromjpeg($FileName);
			} else {
				return (0);
			}

			$RestoreShadow = $this->Shadow;
			if ($this->Shadow) {
				$this->Shadow = FALSE;
				if ($PicType == 3) {
					$this->drawFilledRectangle($X + $this->ShadowX, $Y + $this->ShadowY, $X + $Width + $this->ShadowX, $Y + $Height + $this->ShadowY, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa]);
				} else {
					$TranparentID = imagecolortransparent($Raster);
					for ($Xc = 0; $Xc <= $Width - 1; $Xc++) {
						for ($Yc = 0; $Yc <= $Height - 1; $Yc++) {
							$RGBa = imagecolorat($Raster, $Xc, $Yc);
							$Values = imagecolorsforindex($Raster, $RGBa);
							if ($Values["alpha"] < 120) {
								$AlphaFactor = floor(($this->Shadowa / 100) * ((100 / 127) * (127 - $Values["alpha"])));
								$this->drawAlphaPixel($X + $Xc + $this->ShadowX, $Y + $Yc + $this->ShadowY, $AlphaFactor, $this->ShadowR, $this->ShadowG, $this->ShadowB);
							}
						}
					}
				}
			}

			$this->Shadow = $RestoreShadow;
			imagecopy($this->Picture, $Raster, $X, $Y, 0, 0, $Width, $Height);
			imagedestroy($Raster);
		}
	}

		/* Draw an arrow */
	function drawArrow($X1, $Y1, $X2, $Y2, array $Format = [])
	{
		$FillR = isset($Format["FillR"]) ? $Format["FillR"] : 0;
		$FillG = isset($Format["FillG"]) ? $Format["FillG"] : 0;
		$FillB = isset($Format["FillB"]) ? $Format["FillB"] : 0;
		$BorderR = $FillR;
		$BorderG = $FillG;
		$BorderB = $FillB;
		$Alpha = 100;
		$Size =10;
		$Ratio = .5;
		$TwoHeads = FALSE;
		$Ticks = FALSE;

		extract($Format);

		/* Calculate the line angle */
		$Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
		$RGB = ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $Alpha];
		/* Override Shadow support, this will be managed internally */
		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawArrow($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, ["FillR" => $this->ShadowR,"FillG" => $this->ShadowG,"FillB" => $this->ShadowB,"Alpha" => $this->Shadowa,"Size" => $Size,"Ratio" => $Ratio,"TwoHeads" => $TwoHeads,"Ticks" => $Ticks]);
		}

		/* Draw the 1st Head */
		$TailX = cos(($Angle - 180) * PI / 180) * $Size + $X2;
		$TailY = sin(($Angle - 180) * PI / 180) * $Size + $Y2;
		$Scale = $Size * $Ratio;

		$Points = [$X2, $Y2, cos(($Angle - 90) * PI / 180) * $Scale + $TailX, sin(($Angle - 90) * PI / 180) * $Scale + $TailY, cos(($Angle - 270) * PI / 180) * $Scale + $TailX, sin(($Angle - 270) * PI / 180) * $Scale + $TailY, $X2, $Y2];
		/* Visual correction */
		($Angle == 180 || $Angle == 360) AND $Points[4] = $Points[2];
		($Angle == 90 || $Angle == 270) AND $Points[5] = $Points[3];

		ImageFilledPolygon($this->Picture, $Points, 4, $this->allocateColor($FillR, $FillG, $FillB, $Alpha));
		$this->drawLine($Points[0], $Points[1], $Points[2], $Points[3], $RGB);
		$this->drawLine($Points[2], $Points[3], $Points[4], $Points[5], $RGB);
		$this->drawLine($Points[0], $Points[1], $Points[4], $Points[5], $RGB);
		/* Draw the second head */
		if ($TwoHeads) {
			$Angle = $this->getAngle($X2, $Y2, $X1, $Y1);
			$TailX2 = cos(($Angle - 180) * PI / 180) * $Size + $X1;
			$TailY2 = sin(($Angle - 180) * PI / 180) * $Size + $Y1;
			$Points = [$X1, $Y1, cos(($Angle - 90) * PI / 180) * $Scale + $TailX2, sin(($Angle - 90) * PI / 180) * $Scale + $TailY2, cos(($Angle - 270) * PI / 180) * $Scale + $TailX2, sin(($Angle - 270) * PI / 180) * $Scale + $TailY2, $X1, $Y1];
			/* Visual correction */
			($Angle == 180 || $Angle == 360) AND $Points[4] = $Points[2];
			($Angle == 90 || $Angle == 270) AND $Points[5] = $Points[3];

			ImageFilledPolygon($this->Picture, $Points, 4, $this->allocateColor($FillR, $FillG, $FillB, $Alpha));
			$this->drawLine($Points[0], $Points[1], $Points[2], $Points[3], $RGB);
			$this->drawLine($Points[2], $Points[3], $Points[4], $Points[5], $RGB);
			$this->drawLine($Points[0], $Points[1], $Points[4], $Points[5], $RGB);
			$this->drawLine($TailX, $TailY, $TailX2, $TailY2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $Alpha,"Ticks" => $Ticks]);
		} else {
			$this->drawLine($X1, $Y1, $TailX, $TailY, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $Alpha,"Ticks" => $Ticks]);
		}

		/* Re-enable shadows */
		$this->Shadow = $RestoreShadow;
	}

	/* Draw a label with associated arrow */
	function drawArrowLabel($X1, $Y1, $Text, array $Format = [])
	{
		$FillR = isset($Format["FillR"]) ? $Format["FillR"] : 0;
		$FillG = isset($Format["FillG"]) ? $Format["FillG"] : 0;
		$FillB = isset($Format["FillB"]) ? $Format["FillB"] : 0;
		$BorderR = $FillR;
		$BorderG = $FillG;
		$BorderB = $FillB;
		$FontName = $this->FontName;
		$FontSize = $this->FontSize;
		$Alpha = 100;
		$Length = 50;
		$Angle = 315;
		$Size = 10;
		$Position = POSITION_TOP;
		$RoundPos = FALSE;
		$Ticks = NULL;

		extract($Format);

		$Angle = $Angle % 360;
		$X2 = sin(($Angle + 180) * PI / 180) * $Length + $X1;
		$Y2 = cos(($Angle + 180) * PI / 180) * $Length + $Y1;
		($RoundPos && $Angle > 0 && $Angle < 180) AND $Y2 = ceil($Y2);
		($RoundPos && $Angle > 180) AND $Y2 = floor($Y2);

		$this->drawArrow($X2, $Y2, $X1, $Y1, $Format);
		$Size = imagettfbbox($FontSize, 0, $FontName, $Text);
		$TxtWidth = max(abs($Size[2] - $Size[0]), abs($Size[0] - $Size[6]));
		#$TxtHeight = max(abs($Size[1] - $Size[7]), abs($Size[3] - $Size[1])); # UNUSED
		$RGB = ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $Alpha];

		if ($Angle > 0 && $Angle < 180) {
			$TxtWidth = $X2 - $TxtWidth;
			if ($Position == POSITION_TOP) {
				$RGB["Align"] = TEXT_ALIGN_BOTTOMRIGHT;
				$Y3 = $Y2 - 2;
			} else {
				$RGB["Align"] = TEXT_ALIGN_TOPRIGHT;
				$Y3 = $Y2 + 2;
			}
		} else {
			$TxtWidth = $X2 + $TxtWidth;
			if ($Position == POSITION_TOP) {
				$Y3 = $Y2 - 2;
			} else {
				$RGB["Align"] = TEXT_ALIGN_TOPLEFT;
				$Y3 = $Y2 + 2;
			}
		}

		$this->drawLine($X2, $Y2, $TxtWidth, $Y2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $Alpha,"Ticks" => $Ticks]);
		$this->drawText($X2, $Y3, $Text, $RGB);

	}

	/* Draw a progress bar filled with specified % */
	function drawProgress($X, $Y, $Percent, array $Format = [])
	{
		($Percent > 100) AND $Percent = 100;
		($Percent < 0) AND $Percent = 0;

		$Width = 200;
		$Height = 20;
		$Orientation = ORIENTATION_HORIZONTAL;
		$ShowLabel = FALSE;
		$LabelPos = LABEL_POS_INSIDE;
		$Margin = 10;
		$R = isset($Format["R"]) ? $Format["R"] : 130;
		$G = isset($Format["G"]) ? $Format["G"] : 130;
		$B = isset($Format["B"]) ? $Format["B"] : 130;
		$RFade = -1;
		$GFade = -1;
		$BFade = -1;
		$BorderR = $R;
		$BorderG = $G;
		$BorderB = $B;
		$BoxBorderR = 0;
		$BoxBorderG = 0;
		$BoxBorderB = 0;
		$BoxBackR = 255;
		$BoxBackG = 255;
		$BoxBackB = 255;
		$Alpha = 100;
		$Surrounding = NULL;
		$BoxSurrounding = NULL;
		$NoAngle = FALSE;

		/* Override defaults */
		extract($Format);

		if ($RFade != - 1 && $GFade != - 1 && $BFade != - 1) {
			$RFade = (($RFade - $R) / 100) * $Percent + $R;
			$GFade = (($GFade - $G) / 100) * $Percent + $G;
			$BFade = (($BFade - $B) / 100) * $Percent + $B;
		}

		if ($Surrounding != NULL) {
			$BorderR = $R + $Surrounding;
			$BorderG = $G + $Surrounding;
			$BorderB = $B + $Surrounding;
		}

		if ($BoxSurrounding != NULL) {
			$BoxBorderR = $BoxBackR + $Surrounding;
			$BoxBorderG = $BoxBackG + $Surrounding;
			$BoxBorderB = $BoxBackB + $Surrounding;
		}

		if ($Orientation == ORIENTATION_VERTICAL) {
			$InnerHeight = (($Height - 2) / 100) * $Percent;
			$this->drawFilledRectangle($X, $Y, $X + $Width, $Y - $Height, ["R" => $BoxBackR,"G" => $BoxBackG,"B" => $BoxBackB,"BorderR" => $BoxBorderR,"BorderG" => $BoxBorderG,"BorderB" => $BoxBorderB,"NoAngle" => $NoAngle]);
			$RestoreShadow = $this->Shadow;
			$this->Shadow = FALSE;
			if ($RFade != - 1 && $GFade != - 1 && $BFade != - 1) {
				$this->drawGradientArea($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, DIRECTION_VERTICAL, ["StartR" => $RFade,"StartG" => $GFade,"StartB" => $BFade,"EndR" => $R,"EndG" => $G,"EndB" => $B]);
				if ($Surrounding) { # != NULL, [] && ""
					$this->drawRectangle($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, ["R" => 255,"G" => 255,"B" => 255,"Alpha" => $Surrounding]);
				}
			} else {
				$this->drawFilledRectangle($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, ["R" => $R,"G" => $G,"B" => $B,"BorderR" => $BorderR,"BorderG" => $BorderG,"BorderB" => $BorderB]);
			}
			$this->Shadow = $RestoreShadow;

			switch (TRUE) {
				case ($ShowLabel && $LabelPos == LABEL_POS_BOTTOM):
					$this->drawText($X + ($Width / 2), $Y + $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_TOPMIDDLE]);
					break;
				case ($ShowLabel && $LabelPos == LABEL_POS_TOP):
					$this->drawText($X + ($Width / 2), $Y - $Height - $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
					break;
				case ($ShowLabel && $LabelPos == LABEL_POS_INSIDE):
					$this->drawText($X + ($Width / 2), $Y - $InnerHeight - $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT,"Angle" => 90]);
					break;
				case ($ShowLabel && $LabelPos == LABEL_POS_CENTER):
					$this->drawText($X + ($Width / 2), $Y - ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLEMIDDLE,"Angle" => 90]);
					break;
			}

		} else {
			$InnerWidth = ($Percent == 100) ? $Width - 1 : (($Width - 2) / 100) * $Percent;

			$this->drawFilledRectangle($X, $Y, $X + $Width, $Y + $Height, ["R" => $BoxBackR,"G" => $BoxBackG,"B" => $BoxBackB,"BorderR" => $BoxBorderR,"BorderG" => $BoxBorderG,"BorderB" => $BoxBorderB,"NoAngle" => $NoAngle]);
			$RestoreShadow = $this->Shadow;
			$this->Shadow = FALSE;
			if ($RFade != - 1 && $GFade != - 1 && $BFade != - 1) {
				$this->drawGradientArea($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, DIRECTION_HORIZONTAL, ["StartR" => $R,"StartG" => $G,"StartB" => $B,"EndR" => $RFade,"EndG" => $GFade,"EndB" => $BFade]);
				if ($Surrounding) { # != NULL, [] && ""
					$this->drawRectangle($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, ["R" => 255,"G" => 255,"B" => 255,"Alpha" => $Surrounding]);
				}
			} else {
				$this->drawFilledRectangle($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, ["R" => $R,"G" => $G,"B" => $B,"BorderR" => $BorderR,"BorderG" => $BorderG,"BorderB" => $BorderB]);
			}

			$this->Shadow = $RestoreShadow;
			switch (TRUE) {
				case ($ShowLabel && $LabelPos == LABEL_POS_LEFT):
					$this->drawText($X - $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
					break;
				case ($ShowLabel && $LabelPos == LABEL_POS_RIGHT):
					$this->drawText($X + $Width + $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT]);
					break;
				case ($ShowLabel && $LabelPos == LABEL_POS_CENTER):
					$this->drawText($X + ($Width / 2), $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
					break;
				case ($ShowLabel && $LabelPos == LABEL_POS_INSIDE):
					$this->drawText($X + $InnerWidth + $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT]);
					break;
			}
		}

	}

	/* Get the legend box size */
	function getLegendSize(array $Format = [])
	{
		$FontName = $this->FontName;
		$FontSize = $this->FontSize;
		$BoxSize = 5;
		$Margin = 5;
		$Style = LEGEND_ROUND;
		$Mode = LEGEND_VERTICAL;
		$BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 5;
		$BoxHeight = isset($Format["BoxHeight"]) ? $Format["BoxHeight"] : 5;
		$IconAreaWidth = $BoxWidth;
		$IconAreaHeight = $BoxHeight;
		$XSpacing = 5;

		extract($Format);

		$Data = $this->DataSet->getData();

		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"] && isset($Serie["Picture"])) {
				list($PicWidth, $PicHeight) = $this->getPicInfo($Serie["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$YStep = max($this->FontSize, $IconAreaHeight) + 5;
		$XStep = $IconAreaWidth + 5;
		$XStep = $XSpacing;
		$X = 100;
		$Y = 100;
		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Serie["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
					$Lines = preg_split("/\n/", $Serie["Description"]);
					$vY = $vY + max($this->FontSize * count($Lines), $IconAreaHeight) + 5;
				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Lines = preg_split("/\n/", $Serie["Description"]);
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + (($this->FontSize + 3) * $Key), $FontName, $FontSize, 0, $Value);
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
		($Boundaries["B"] - ($vY + $IconAreaHeight) < $TopOffset) AND $Boundaries["B"] = $vY + $IconAreaHeight + $TopOffset;
		$Width = ($Boundaries["R"] + $Margin) - ($Boundaries["L"] - $Margin);
		$Height = ($Boundaries["B"] + $Margin) - ($Boundaries["T"] - $Margin);

		return ["Width" => $Width,"Height" => $Height];
	}

	/* Draw the legend of the active series */
	function drawLegend($X, $Y, array $Format = [])
	{
		$Family = LEGEND_FAMILY_BOX;
		$FontName = $this->FontName;
		$FontSize = $this->FontSize;
		$FontR = $this->FontColorR;
		$FontG = $this->FontColorG;
		$FontB = $this->FontColorB;
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

		$Data = $this->DataSet->getData();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"] && isset($Serie["Picture"])) {
				list($PicWidth, $PicHeight) = $this->getPicInfo($Serie["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$YStep = max($this->FontSize, $IconAreaHeight) + 5;
		$XStep = $IconAreaWidth + 5;
		$XStep = $XSpacing;
		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Serie["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
					$Lines = preg_split("/\n/", $Serie["Description"]);
					$vY = $vY + max($this->FontSize * count($Lines), $IconAreaHeight) + 5;
				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Lines = preg_split("/\n/", $Serie["Description"]);
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->getTextBox($vX + $IconAreaWidth + 6, $Y + $IconAreaHeight / 2 + (($this->FontSize + 3) * $Key), $FontName, $FontSize, 0, $Value);
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
		($Boundaries["B"] - ($vY + $IconAreaHeight) < $TopOffset) AND $Boundaries["B"] = $vY + $IconAreaHeight + $TopOffset;

		if ($Style == LEGEND_ROUND) {
			$this->drawRoundedFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Margin, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"BorderR" => $BorderR,"BorderG" => $BorderG,"BorderB" => $BorderB]);
		} elseif ($Style == LEGEND_BOX) {
			$this->drawFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"BorderR" => $BorderR,"BorderG" => $BorderG,"BorderB" => $BorderB]);
		}

		$RestoreShadow = $this->Shadow;
		$this->Shadow = FALSE;
		foreach($Data["Series"] as $SerieName => $Serie) {

			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {

				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Ticks = $Serie["Ticks"];
				$Weight = $Serie["Weight"];
				if (isset($Serie["Picture"])) {
					$Picture = $Serie["Picture"];
					list($PicWidth, $PicHeight) = $this->getPicInfo($Picture);
					$PicX = $X + $IconAreaWidth / 2;
					$PicY = $Y + $IconAreaHeight / 2;
					$this->drawFromPNG($PicX - $PicWidth / 2, $PicY - $PicHeight / 2, $Picture);

				} else {
					if ($Family == LEGEND_FAMILY_BOX) {

						$XOffset = ($BoxWidth != $IconAreaWidth) ? floor(($IconAreaWidth - $BoxWidth) / 2) : 0;
						$YOffset = ($BoxHeight != $IconAreaHeight) ? floor(($IconAreaHeight - $BoxHeight) / 2) : 0;

						$this->drawFilledRectangle($X + 1 + $XOffset, $Y + 1 + $YOffset, $X + $BoxWidth + $XOffset + 1, $Y + $BoxHeight + 1 + $YOffset, ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 20]);
						$this->drawFilledRectangle($X + $XOffset, $Y + $YOffset, $X + $BoxWidth + $XOffset, $Y + $BoxHeight + $YOffset, ["R" => $R,"G" => $G,"B" => $B,"Surrounding" => 20]);

					} elseif ($Family == LEGEND_FAMILY_CIRCLE) {
						$this->drawFilledCircle($X + 1 + $IconAreaWidth / 2, $Y + 1 + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 20]);
						$this->drawFilledCircle($X + $IconAreaWidth / 2, $Y + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["R" => $R,"G" => $G,"B" => $B,"Surrounding" => 20]);

					} elseif ($Family == LEGEND_FAMILY_LINE) {
						$this->drawLine($X + 1, $Y + 1 + $IconAreaHeight / 2, $X + 1 + $IconAreaWidth, $Y + 1 + $IconAreaHeight / 2, ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 20,"Ticks" => $Ticks,"Weight" => $Weight]);
						$this->drawLine($X, $Y + $IconAreaHeight / 2, $X + $IconAreaWidth, $Y + $IconAreaHeight / 2, ["R" => $R,"G" => $G,"B" => $B,"Ticks" => $Ticks,"Weight" => $Weight]);
					}
				}

				if ($Mode == LEGEND_VERTICAL) {
					$Lines = preg_split("/\n/", $Serie["Description"]);
					foreach($Lines as $Key => $Value) {
						$this->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + (($this->FontSize + 3) * $Key), $Value, ["R" => $FontR,"G" => $FontG,"B" => $FontB,"Align" => TEXT_ALIGN_MIDDLELEFT,"FontSize" => $FontSize,"FontName" => $FontName]);
					}
					$Y = $Y + max($this->FontSize * count($Lines), $IconAreaHeight) + 5;

				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Lines = preg_split("/\n/", $Serie["Description"]);
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + (($this->FontSize + 3) * $Key), $Value, ["R" => $FontR,"G" => $FontG,"B" => $FontB,"Align" => TEXT_ALIGN_MIDDLELEFT,"FontSize" => $FontSize,"FontName" => $FontName]);
						$Width[] = $BoxArray[1]["X"];
					}

					$X = max($Width) + 2 + $XStep;
				}
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	function drawScale(array $Format = [])
	{
		$Pos = SCALE_POS_LEFTRIGHT;
		$Floating = FALSE;
		$Mode = SCALE_MODE_FLOATING;
		$RemoveXAxis = FALSE;
		$MinDivHeight = 20;
		$Factors = [1,2,5];
		$ManualScale = array("0" => ["Min" => - 100,"Max" => 100]);
		$XMargin = AUTO;
		$YMargin = 0;
		$ScaleSpacing = 15;
		$InnerTickWidth = 2;
		$OuterTickWidth = 2;
		$DrawXLines = TRUE;
		$DrawYLines = isset($Format["DrawYLines"]) ? $Format["DrawYLines"] : ALL;
		$GridTicks = isset($Format["GridTicks"]) ? $Format["GridTicks"] : 4;
		$GridR = isset($Format["GridR"]) ? $Format["GridR"] : 255;
		$GridG = isset($Format["GridG"]) ? $Format["GridG"] : 255;
		$GridB = isset($Format["GridB"]) ? $Format["GridB"] : 255;
		$GridAlpha = isset($Format["GridAlpha"]) ? $Format["GridAlpha"] : 40;
		$AxisRo = isset($Format["AxisR"]) ? $Format["AxisR"] : 0;
		$AxisGo = isset($Format["AxisG"]) ? $Format["AxisG"] : 0;
		$AxisBo = isset($Format["AxisB"]) ? $Format["AxisB"] : 0;
		$AxisAlpha = 100;
		$TickRo = isset($Format["TickR"]) ? $Format["TickR"] : 0;
		$TickGo = isset($Format["TickG"]) ? $Format["TickG"] : 0;
		$TickBo = isset($Format["TickB"]) ? $Format["TickB"] : 0;
		$TickAlpha = isset($Format["TickAlpha"]) ? $Format["TickAlpha"] : 100;
		$DrawSubTicks = FALSE;
		$InnerSubTickWidth = 0;
		$OuterSubTickWidth = 2;
		$SubTickR = 255;
		$SubTickG = 0;
		$SubTickB = 0;
		$SubTickAlpha = 100;
		$AutoAxisLabels = TRUE;
		$XReleasePercent = 1;
		$DrawArrows = FALSE;
		$ArrowSize = 8;
		$CycleBackground = FALSE;
		$BackgroundR1 = 255;
		$BackgroundG1 = 255;
		$BackgroundB1 =  255;
		$BackgroundAlpha1 = 20;
		$BackgroundR2 = 230;
		$BackgroundG2 = 230;
		$BackgroundB2 = 230;
		$BackgroundAlpha2 = 20;
		$LabelingMethod = LABELING_ALL;
		$LabelSkip = 0;
		$LabelRotation = 0;
		$RemoveSkippedAxis = FALSE;
		$SkippedAxisTicks = $GridTicks + 2;
		$SkippedAxisR = $GridR;
		$SkippedAxisG = $GridG;
		$SkippedAxisB = $GridB;
		$SkippedAxisAlpha = $GridAlpha - 30;
		$SkippedTickR = $TickRo;
		$SkippedTickG = $TickGo;
		$SkippedTickB = $TickBo;
		$SkippedTickAlpha = $TickAlpha - 80;
		$SkippedInnerTickWidth = 0;
		$SkippedOuterTickWidth =  2;

		/* Override defaults */
		extract($Format);

		/* Floating scale require X & Y margins to be set manually */
		($Floating && ($XMargin == AUTO || $YMargin == 0)) AND $Floating = FALSE;

		/* Skip a NOTICE event in case of an empty array */
		($DrawYLines == NONE || $DrawYLines == FALSE) AND $DrawYLines = ["zarma" => "31"];

		/* Define the color for the skipped elements */
		$SkippedAxisColor = ["R" => $SkippedAxisR,"G" => $SkippedAxisG,"B" => $SkippedAxisB,"Alpha" => $SkippedAxisAlpha,"Ticks" => $SkippedAxisTicks];
		$SkippedTickColor = ["R" => $SkippedTickR,"G" => $SkippedTickG,"B" => $SkippedTickB,"Alpha" => $SkippedTickAlpha];
		$Data = $this->DataSet->getData();
		$Abscissa = (isset($Data["Abscissa"])) ? $Data["Abscissa"] : null;

		/* Unset the abscissa axis, needed if we display multiple charts on the same picture */
		if ($Abscissa != NULL) {
			foreach($Data["Axis"] as $AxisID => $Parameters) {
				if ($Parameters["Identity"] == AXIS_X) {
					unset($Data["Axis"][$AxisID]);
				}
			}
		}

		/* Build the scale settings */
		$GotXAxis = FALSE;
		foreach($Data["Axis"] as $AxisID => $AxisParameter) {
			if ($AxisParameter["Identity"] == AXIS_X) {
				$GotXAxis = TRUE;
			}

			if ($Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_Y) {
				$Height = $this->GraphAreaY2 - $this->GraphAreaY1 - $YMargin * 2;
			} elseif ($Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_X) {
				$Height = $this->GraphAreaX2 - $this->GraphAreaX1;
			} elseif ($Pos == SCALE_POS_TOPBOTTOM && $AxisParameter["Identity"] == AXIS_Y) {
				$Height = $this->GraphAreaX2 - $this->GraphAreaX1 - $YMargin * 2;;
			} else {
				$Height = $this->GraphAreaY2 - $this->GraphAreaY1;
			}

			$AxisMin = ABSOLUTE_MAX;
			$AxisMax = OUT_OF_SIGHT;
			if ($Mode == SCALE_MODE_FLOATING || $Mode == SCALE_MODE_START0) {
				foreach($Data["Series"] as $SerieID => $SerieParameter) {
					if ($SerieParameter["Axis"] == $AxisID && $Data["Series"][$SerieID]["isDrawable"] && $Data["Abscissa"] != $SerieID) {
						if (!is_numeric($Data["Series"][$SerieID]["Max"]) || !is_numeric($Data["Series"][$SerieID]["Min"])){
							die("Series ".$SerieID.": non-numeric input");
						}
						$AxisMax = max($AxisMax, $Data["Series"][$SerieID]["Max"]);
						$AxisMin = min($AxisMin, $Data["Series"][$SerieID]["Min"]);
					}
				}

				(!is_numeric($AxisMin)) AND $AxisMin = 1; // $Data["Series"][$SerieID]["Min"] = Bulgium # MOMCHIL
				$AutoMargin = (($AxisMax - $AxisMin) / 100) * $XReleasePercent;
				$Data["Axis"][$AxisID]["Min"] = $AxisMin - $AutoMargin;
				$Data["Axis"][$AxisID]["Max"] = $AxisMax + $AutoMargin;
				if ($Mode == SCALE_MODE_START0) {
					$Data["Axis"][$AxisID]["Min"] = 0;
				}

			} elseif ($Mode == SCALE_MODE_MANUAL) {

				if (isset($ManualScale[$AxisID]["Min"]) && isset($ManualScale[$AxisID]["Max"])) {
					$Data["Axis"][$AxisID]["Min"] = $ManualScale[$AxisID]["Min"];
					$Data["Axis"][$AxisID]["Max"] = $ManualScale[$AxisID]["Max"];
				} else {
					echo "Manual scale boundaries not set.";
					exit();
				}

			} elseif ($Mode == SCALE_MODE_ADDALL || $Mode == SCALE_MODE_ADDALL_START0) {

				$Series = [];
				foreach($Data["Series"] as $SerieID => $SerieParameter) {
					if ($SerieParameter["Axis"] == $AxisID && $SerieParameter["isDrawable"] && $Data["Abscissa"] != $SerieID) {
						$Series[$SerieID] = count($Data["Series"][$SerieID]["Data"]);
					}
				}

				for ($ID = 0; $ID <= max($Series) - 1; $ID++) {
					$PointMin = 0;
					$PointMax = 0;
					foreach($Series as $SerieID => $ValuesCount) {
						if (isset($Data["Series"][$SerieID]["Data"][$ID]) && $Data["Series"][$SerieID]["Data"][$ID] != NULL) {
							$Value = $Data["Series"][$SerieID]["Data"][$ID];
							$PointMax = ($Value > 0) ? $PointMax + $Value : $PointMin + $Value;
						}
					}

					$AxisMax = max($AxisMax, $PointMax);
					$AxisMin = min($AxisMin, $PointMin);
				}

				$AutoMargin = (($AxisMax - $AxisMin) / 100) * $XReleasePercent;
				$Data["Axis"][$AxisID]["Min"] = $AxisMin - $AutoMargin;
				$Data["Axis"][$AxisID]["Max"] = $AxisMax + $AutoMargin;
			}

			$MaxDivs = floor($Height / $MinDivHeight);
			if ($Mode == SCALE_MODE_ADDALL_START0) {
				$Data["Axis"][$AxisID]["Min"] = 0;
			}

			$Scale = $this->computeScale($Data["Axis"][$AxisID]["Min"], $Data["Axis"][$AxisID]["Max"], $MaxDivs, $Factors, $AxisID);
			$Data["Axis"][$AxisID]["Margin"] = $AxisParameter["Identity"] == AXIS_X ? $XMargin : $YMargin;
			$Data["Axis"][$AxisID]["ScaleMin"] = $Scale["XMin"];
			$Data["Axis"][$AxisID]["ScaleMax"] = $Scale["XMax"];
			$Data["Axis"][$AxisID]["Rows"] = $Scale["Rows"];
			$Data["Axis"][$AxisID]["RowHeight"] = $Scale["RowHeight"];
			(isset($Scale["Format"])) AND $Data["Axis"][$AxisID]["Format"] = $Scale["Format"];
			(!isset($Data["Axis"][$AxisID]["Display"])) AND $Data["Axis"][$AxisID]["Display"] = NULL;
			(!isset($Data["Axis"][$AxisID]["Format"])) AND 	$Data["Axis"][$AxisID]["Format"] = NULL;
			(!isset($Data["Axis"][$AxisID]["Unit"])) AND $Data["Axis"][$AxisID]["Unit"] = NULL;
		}

		/* Still no X axis */
		if ($GotXAxis == FALSE) {
			if ($Abscissa != NULL) {
				$Points = count($Data["Series"][$Abscissa]["Data"]);
				if ($AutoAxisLabels) {
					$AxisName = isset($Data["Series"][$Abscissa]["Description"]) ? $Data["Series"][$Abscissa]["Description"] : NULL;
				} else {
					$AxisName = NULL;
				}
			} else {
				$Points = 0;
				$AxisName = isset($Data["XAxisName"]) ? $Data["XAxisName"] : NULL;
				foreach($Data["Series"] as $SerieID => $SerieParameter) {
					if ($SerieParameter["isDrawable"]) {
						$Points = max($Points, count($SerieParameter["Data"]));
					}
				}
			}

			$AxisID = count($Data["Axis"]);
			$Data["Axis"][$AxisID]["Identity"] = AXIS_X;
			$Data["Axis"][$AxisID]["Position"] = ($Pos == SCALE_POS_LEFTRIGHT) ? AXIS_POSITION_BOTTOM : AXIS_POSITION_LEFT;
			(isset($Data["AbscissaName"])) AND $Data["Axis"][$AxisID]["Name"] = $Data["AbscissaName"];

			if ($XMargin == AUTO) {
				$Height = ($Pos == SCALE_POS_LEFTRIGHT) ? $this->GraphAreaX2 - $this->GraphAreaX1 : $this->GraphAreaY2 - $this->GraphAreaY1;
				$Data["Axis"][$AxisID]["Margin"] = ($Points == 1) ? ($Height / 2) : (($Height / $Points) / 2);
			} else {
				$Data["Axis"][$AxisID]["Margin"] = $XMargin;
			}

			$Data["Axis"][$AxisID]["Rows"] = $Points - 1;
			(!isset($Data["Axis"][$AxisID]["Display"])) AND $Data["Axis"][$AxisID]["Display"] = NULL;
			(!isset($Data["Axis"][$AxisID]["Format"])) AND $Data["Axis"][$AxisID]["Format"] = NULL;
			(!isset($Data["Axis"][$AxisID]["Unit"])) AND $Data["Axis"][$AxisID]["Unit"] = NULL;
		}

		/* Do we need to reverse the abscissa position? */
		if ($Pos != SCALE_POS_LEFTRIGHT) {
			$Data["AbsicssaPosition"] = ($Data["AbsicssaPosition"] == AXIS_POSITION_BOTTOM) ? AXIS_POSITION_LEFT : AXIS_POSITION_RIGHT;
		}

		$Data["Axis"][$AxisID]["Position"] = $Data["AbsicssaPosition"];
		$this->DataSet->saveOrientation($Pos);
		$this->DataSet->saveAxisConfig($Data["Axis"]);
		$this->DataSet->saveYMargin($YMargin);
		$FontColorRo = $this->FontColorR;
		$FontColorGo = $this->FontColorG;
		$FontColorBo = $this->FontColorB;
		$AxisPos["L"] = $this->GraphAreaX1;
		$AxisPos["R"] = $this->GraphAreaX2;
		$AxisPos["T"] = $this->GraphAreaY1;
		$AxisPos["B"] = $this->GraphAreaY2;
		foreach($Data["Axis"] as $AxisID => $Parameters) {
			if (isset($Parameters["Color"])) {
				$AxisR = $Parameters["Color"]["R"];
				$AxisG = $Parameters["Color"]["G"];
				$AxisB = $Parameters["Color"]["B"];
				$TickR = $Parameters["Color"]["R"];
				$TickG = $Parameters["Color"]["G"];
				$TickB = $Parameters["Color"]["B"];
				$this->setFontProperties(["R" => $Parameters["Color"]["R"],"G" => $Parameters["Color"]["G"],"B" => $Parameters["Color"]["B"]]);
			} else {
				$AxisR = $AxisRo;
				$AxisG = $AxisGo;
				$AxisB = $AxisBo;
				$TickR = $TickRo;
				$TickG = $TickGo;
				$TickB = $TickBo;
				$this->setFontProperties(["R" => $FontColorRo,"G" => $FontColorGo,"B" => $FontColorBo]);
			}

			$LastValue = "w00t";
			$ID = 1;
			if ($Parameters["Identity"] == AXIS_X) {
				if ($Pos == SCALE_POS_LEFTRIGHT) {
					if ($Parameters["Position"] == AXIS_POSITION_BOTTOM) {

						switch(TRUE){
							case ($LabelRotation == 0):
								$LabelAlign = TEXT_ALIGN_TOPMIDDLE;
								$YLabelOffset = 2;
								break;
							case ($LabelRotation > 0 && $LabelRotation < 190):
								$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
								$YLabelOffset = 5;
								break;
							case ($LabelRotation == 180):
								$LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
								$YLabelOffset = 5;
								break;
							case ($LabelRotation > 180 && $LabelRotation < 360):
								$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
								$YLabelOffset = 2;
								break;
						}

						if (!$RemoveXAxis) {
							if ($Floating) {
								$FloatingOffset = $YMargin;
								$this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
							} else {
								$FloatingOffset = 0;
								$this->drawLine($this->GraphAreaX1, $AxisPos["B"], $this->GraphAreaX2, $AxisPos["B"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
							}

							if ($DrawArrows) {
								$this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["B"], ["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
							}
						}

						$Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Parameters["Margin"] * 2;
						$Step = ($Parameters["Rows"] == 0) ? $Width : $Width / ($Parameters["Rows"]);
						$MaxBottom = $AxisPos["B"];

						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
							$YPos = $AxisPos["B"];
							if ($Abscissa != NULL) {
								if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
									$Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
								} else {
									$Value = "";
								}
							} else {
								if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
									$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
								} else {
									$Value = $i;
								}
							}

							$ID++;
							$Skipped = TRUE;
							if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
								$Bounds = $this->drawText($XPos, $YPos + $OuterTickWidth + $YLabelOffset, $Value, ["Angle" => $LabelRotation,"Align" => $LabelAlign]);
								$TxtBottom = $YPos + $OuterTickWidth + 2 + ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
								$MaxBottom = max($MaxBottom, $TxtBottom);
								$LastValue = $Value;
								$Skipped = FALSE;
							}

							($RemoveXAxis) AND $Skipped = FALSE;

							if ($Skipped) {
								if ($DrawXLines && !$RemoveSkippedAxis) {
									$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $SkippedAxisColor);
								}

								if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis && !$RemoveSkippedAxis) {
									$this->drawLine($XPos, $YPos - $SkippedInnerTickWidth, $XPos, $YPos + $SkippedOuterTickWidth, $SkippedTickColor);
								}
							} else {
								if ($DrawXLines && ($XPos != $this->GraphAreaX1 && $XPos != $this->GraphAreaX2)) {
									$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
								}

								if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis) {
									$this->drawLine($XPos, $YPos - $InnerTickWidth, $XPos, $YPos + $OuterTickWidth, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
								}
							}
						}

						if (isset($Parameters["Name"]) && !$RemoveXAxis) {
							$YPos = $MaxBottom + 2;
							$XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]);
							$MaxBottom = $Bounds[0]["Y"];
							$this->DataSet->Data["GraphArea"]["Y2"] = $MaxBottom + $this->FontSize;
						}

						$AxisPos["B"] = $MaxBottom + $ScaleSpacing;

					} elseif ($Parameters["Position"] == AXIS_POSITION_TOP) {

						switch(TRUE){
							case ($LabelRotation == 0):
								$LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE;
								$YLabelOffset = 2;
								break;
							case ($LabelRotation > 0 && $LabelRotation < 190):
								$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
								$YLabelOffset = 2;
								break;
							case ($LabelRotation == 180):
								$LabelAlign = TEXT_ALIGN_TOPMIDDLE;
								$YLabelOffset = 5;
								break;
							case ($LabelRotation > 180 && $LabelRotation < 360):
								$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
								$YLabelOffset = 5;
								break;
						}

						if (!$RemoveXAxis) {
							if ($Floating) {
								$FloatingOffset = $YMargin;
								$this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
							} else {
								$FloatingOffset = 0;
								$this->drawLine($this->GraphAreaX1, $AxisPos["T"], $this->GraphAreaX2, $AxisPos["T"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
							}

							if ($DrawArrows) {
								$this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["T"],["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
							}
						}

						$Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Parameters["Margin"] * 2;
						$Step = ($Parameters["Rows"] == 0) ? $Width : $Width / $Parameters["Rows"];
						$MinTop = $AxisPos["T"];

						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
							$YPos = $AxisPos["T"];
							if ($Abscissa != NULL) {
								if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
									$Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
								} else {
									$Value = "";
								}
							} else {
								if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
									$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
								} else {
									$Value = $i;
								}
							}

							$ID++;
							$Skipped = TRUE;
							if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
								$Bounds = $this->drawText($XPos, $YPos - $OuterTickWidth - $YLabelOffset, $Value, ["Angle" => $LabelRotation,"Align" => $LabelAlign]);
								$TxtBox = $YPos - $OuterTickWidth - 2 - ($Bounds[0]["Y"] - $Bounds[2]["Y"]);
								$MinTop = min($MinTop, $TxtBox);
								$LastValue = $Value;
								$Skipped = FALSE;
							}

							($RemoveXAxis) AND $Skipped = FALSE;

							if ($Skipped) {
								if ($DrawXLines && !$RemoveSkippedAxis) {
									$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $SkippedAxisColor);
								}

								if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis && !$RemoveSkippedAxis) {
									$this->drawLine($XPos, $YPos + $SkippedInnerTickWidth, $XPos, $YPos - $SkippedOuterTickWidth, $SkippedTickColor);
								}
							} else {
								if ($DrawXLines) {
									$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
								}

								if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis) {
									$this->drawLine($XPos, $YPos + $InnerTickWidth, $XPos, $YPos - $OuterTickWidth, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
								}
							}
						}

						if (isset($Parameters["Name"]) && !$RemoveXAxis) {
							$YPos = $MinTop - 2;
							$XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
							$MinTop = $Bounds[2]["Y"];
							$this->DataSet->Data["GraphArea"]["Y1"] = $MinTop;
						}

						$AxisPos["T"] = $MinTop - $ScaleSpacing;
					}

				} elseif ($Pos == SCALE_POS_TOPBOTTOM) {

					if ($Parameters["Position"] == AXIS_POSITION_LEFT) {

						switch(TRUE){
							case ($LabelRotation == 0):
								$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
								$XLabelOffset = - 2;
								break;
							case ($LabelRotation > 0 && $LabelRotation < 190):
								$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
								$XLabelOffset = - 6;
								break;
							case ($LabelRotation == 180):
								$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
								$XLabelOffset = - 2;
								break;
							case ($LabelRotation > 180 && $LabelRotation < 360):
								$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
								$XLabelOffset = - 5;
								break;
						}

						if (!$RemoveXAxis) {
							if ($Floating) {
								$FloatingOffset = $YMargin;
								$this->drawLine($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
							} else {
								$FloatingOffset = 0;
								$this->drawLine($AxisPos["L"], $this->GraphAreaY1, $AxisPos["L"], $this->GraphAreaY2, ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
							}

							if ($DrawArrows) {
								$this->drawArrow($AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 + ($ArrowSize * 2), ["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
							}
						}

						$Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Parameters["Margin"] * 2;
						$Step = ($Parameters["Rows"] == 0) ? $Height :  $Height / $Parameters["Rows"];
						$MinLeft = $AxisPos["L"];

						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$YPos = $this->GraphAreaY1 + $Parameters["Margin"] + $Step * $i;
							$XPos = $AxisPos["L"];
							if ($Abscissa != NULL) {
								if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
									$Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
								} else {
									$Value = "";
								}
							} else {
								if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
									$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
								} else {
									$Value = $i;
								}
							}

							$ID++;
							$Skipped = TRUE;
							if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
								$Bounds = $this->drawText($XPos - $OuterTickWidth + $XLabelOffset, $YPos, $Value, ["Angle" => $LabelRotation,"Align" => $LabelAlign]);
								$TxtBox = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
								$MinLeft = min($MinLeft, $TxtBox);
								$LastValue = $Value;
								$Skipped = FALSE;
							}

							($RemoveXAxis) AND $Skipped = FALSE;

							if ($Skipped) {
								if ($DrawXLines && !$RemoveSkippedAxis) {
									$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $SkippedAxisColor);
								}

								if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis && !$RemoveSkippedAxis) {
									$this->drawLine($XPos - $SkippedOuterTickWidth, $YPos, $XPos + $SkippedInnerTickWidth, $YPos, $SkippedTickColor);
								}
							} else {
								if ($DrawXLines && ($YPos != $this->GraphAreaY1 && $YPos != $this->GraphAreaY2)) {
									$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
								}

								if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis) {
									$this->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
								}
							}
						}

						if (isset($Parameters["Name"]) && !$RemoveXAxis) {
							$XPos = $MinLeft - 2;
							$YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 90]);
							$MinLeft = $Bounds[0]["X"];
							$this->DataSet->Data["GraphArea"]["X1"] = $MinLeft;
						}

						$AxisPos["L"] = $MinLeft - $ScaleSpacing;

					} elseif ($Parameters["Position"] == AXIS_POSITION_RIGHT) {

						switch(TRUE){
							case ($LabelRotation == 0):
								$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
								$XLabelOffset = 2;
								break;
							case ($LabelRotation > 0 && $LabelRotation < 190):
								$LabelAlign = TEXT_ALIGN_MIDDLELEFT;
								$XLabelOffset = 6;
								break;
							case ($LabelRotation == 180):
								$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
								$XLabelOffset = 5;
								break;
							case ($LabelRotation > 180 && $LabelRotation < 360):
								$LabelAlign = TEXT_ALIGN_MIDDLERIGHT;
								$XLabelOffset = 7;
								break;
						}

						if (!$RemoveXAxis) {
							if ($Floating) {
								$FloatingOffset = $YMargin;
								$this->drawLine($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
							} else {
								$FloatingOffset = 0;
								$this->drawLine($AxisPos["R"], $this->GraphAreaY1, $AxisPos["R"], $this->GraphAreaY2, ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
							}

							if ($DrawArrows) {
								$this->drawArrow($AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 + ($ArrowSize * 2), ["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
							}
						}

						$Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Parameters["Margin"] * 2;
						$Step = ($Parameters["Rows"] == 0) ? $Height : $Height / $Parameters["Rows"];
						$MaxRight = $AxisPos["R"];

						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$YPos = $this->GraphAreaY1 + $Parameters["Margin"] + $Step * $i;
							$XPos = $AxisPos["R"];
							if ($Abscissa != NULL) {
								if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
									$Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
								} else {
									$Value = "";
								}
							} else {
								if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
									$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["XAxisDisplay"], $Data["XAxisFormat"], $Data["XAxisUnit"]);
								} else {
									$Value = $i;
								}
							}

							$ID++;
							$Skipped = TRUE;
							if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
								$Bounds = $this->drawText($XPos + $OuterTickWidth + $XLabelOffset, $YPos, $Value, ["Angle" => $LabelRotation,"Align" => $LabelAlign]);
								$TxtBox = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
								$MaxRight = max($MaxRight, $TxtBox);
								$LastValue = $Value;
								$Skipped = FALSE;
							}

							($RemoveXAxis) AND $Skipped = FALSE;

							if ($Skipped) {
								if ($DrawXLines && !$RemoveSkippedAxis) {
									$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $SkippedAxisColor);
								}

								if (($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis && !$RemoveSkippedAxis) {
									$this->drawLine($XPos + $SkippedOuterTickWidth, $YPos, $XPos - $SkippedInnerTickWidth, $YPos, $SkippedTickColor);
								}
							} else {
								if ($DrawXLines) {
									$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
								}

								if (($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis) {
									$this->drawLine($XPos + $OuterTickWidth, $YPos, $XPos - $InnerTickWidth, $YPos, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
								}
							}
						}

						if (isset($Parameters["Name"]) && !$RemoveXAxis) {
							$XPos = $MaxRight + 4;
							$YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 270]);
							$MaxRight = $Bounds[1]["X"];
							$this->DataSet->Data["GraphArea"]["X2"] = $MaxRight + $this->FontSize;
						}

						$AxisPos["R"] = $MaxRight + $ScaleSpacing;
					}
				}

			} elseif ($Parameters["Identity"] == AXIS_Y) {

				if ($Pos == SCALE_POS_LEFTRIGHT) {
					if ($Parameters["Position"] == AXIS_POSITION_LEFT) {
						if ($Floating) {
							$FloatingOffset = $XMargin;
							$this->drawLine($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
						} else {
							$FloatingOffset = 0;
							$this->drawLine($AxisPos["L"], $this->GraphAreaY1, $AxisPos["L"], $this->GraphAreaY2, ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
						}

						if ($DrawArrows) {
							$this->drawArrow($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY1 - ($ArrowSize * 2), ["FillR" => $AxisR,"FillG" => $AxisG,"FillB" => $AxisB,"Size" => $ArrowSize]);
						}

						$Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Parameters["Margin"] * 2;
						$Step = $Height / $Parameters["Rows"];
						$SubTicksSize = $Step / 2;
						$MinLeft = $AxisPos["L"];
						$LastY = NULL;
						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$YPos = $this->GraphAreaY2 - $Parameters["Margin"] - $Step * $i;
							$XPos = $AxisPos["L"];
							$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters["Display"], $Parameters["Format"], $Parameters["Unit"]);
							if ($i % 2 == 1) {
								$BGColor = ["R" => $BackgroundR1,"G" => $BackgroundG1,"B" => $BackgroundB1,"Alpha" => $BackgroundAlpha1];
							} else {
								$BGColor = ["R" => $BackgroundR2,"G" => $BackgroundG2,"B" => $BackgroundB2,"Alpha" => $BackgroundAlpha2];
							}

							if ($LastY != NULL && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
								$this->drawFilledRectangle($this->GraphAreaX1 + $FloatingOffset, $LastY, $this->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
							}

							if ($DrawYLines == ALL || in_array($AxisID, $DrawYLines)) {
								$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
							}

							if ($DrawSubTicks && $i != $Parameters["Rows"]) {
								$this->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, ["R" => $SubTickR,"G" => $SubTickG,"B" => $SubTickB,"Alpha" => $SubTickAlpha]);
							}

							$this->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
							$Bounds = $this->drawText($XPos - $OuterTickWidth - 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
							$TxtLeft = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
							$MinLeft = min($MinLeft, $TxtLeft);
							$LastY = $YPos;
						}

						if (isset($Parameters["Name"])) {
							$XPos = $MinLeft - 2;
							$YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 90]);
							$MinLeft = $Bounds[2]["X"];
							$this->DataSet->Data["GraphArea"]["X1"] = $MinLeft;
						}

						$AxisPos["L"] = $MinLeft - $ScaleSpacing;

					} elseif ($Parameters["Position"] == AXIS_POSITION_RIGHT) {

						if ($Floating) {
							$FloatingOffset = $XMargin;
							$this->drawLine($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
						} else {
							$FloatingOffset = 0;
							$this->drawLine($AxisPos["R"], $this->GraphAreaY1, $AxisPos["R"], $this->GraphAreaY2, ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
						}

						if ($DrawArrows) {
							$this->drawArrow($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY1 - ($ArrowSize * 2), ["FillR" => $AxisR,"FillG" => $AxisG, "FillB" => $AxisB,"Size" => $ArrowSize]);
						}

						$Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Parameters["Margin"] * 2;
						$Step = $Height / $Parameters["Rows"];
						$SubTicksSize = $Step / 2;
						$MaxLeft = $AxisPos["R"];
						$LastY = NULL;
						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$YPos = $this->GraphAreaY2 - $Parameters["Margin"] - $Step * $i;
							$XPos = $AxisPos["R"];
							$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters["Display"], $Parameters["Format"], $Parameters["Unit"]);
							if ($i % 2 == 1) {
								$BGColor = ["R" => $BackgroundR1,"G" => $BackgroundG1,"B" => $BackgroundB1,"Alpha" => $BackgroundAlpha1];
							} else {
								$BGColor = ["R" => $BackgroundR2,"G" => $BackgroundG2,"B" => $BackgroundB2,"Alpha" => $BackgroundAlpha2];
							}

							if ($LastY != NULL && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
								$this->drawFilledRectangle($this->GraphAreaX1 + $FloatingOffset, $LastY, $this->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
							}

							if ($DrawYLines == ALL || in_array($AxisID, $DrawYLines)) {
								$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
							}

							if ($DrawSubTicks && $i != $Parameters["Rows"]) {
								$this->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, ["R" => $SubTickR,"G" => $SubTickG,"B" => $SubTickB,"Alpha" => $SubTickAlpha]);
							}
							$this->drawLine($XPos - $InnerTickWidth, $YPos, $XPos + $OuterTickWidth, $YPos, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
							$Bounds = $this->drawText($XPos + $OuterTickWidth + 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLELEFT]);
							$TxtLeft = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
							$MaxLeft = max($MaxLeft, $TxtLeft);
							$LastY = $YPos;
						}

						if (isset($Parameters["Name"])) {
							$XPos = $MaxLeft + 6;
							$YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 270]);
							$MaxLeft = $Bounds[2]["X"];
							$this->DataSet->Data["GraphArea"]["X2"] = $MaxLeft + $this->FontSize;
						}

						$AxisPos["R"] = $MaxLeft + $ScaleSpacing;
					}

				} elseif ($Pos == SCALE_POS_TOPBOTTOM) {

					if ($Parameters["Position"] == AXIS_POSITION_TOP) {
						if ($Floating) {
							$FloatingOffset = $XMargin;
							$this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
						} else {
							$FloatingOffset = 0;
							$this->drawLine($this->GraphAreaX1, $AxisPos["T"], $this->GraphAreaX2, $AxisPos["T"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
						}

						if ($DrawArrows) {
							$this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["T"], ["FillR" => $AxisR,"FillG" => $AxisG, "FillB" => $AxisB,"Size" => $ArrowSize]);
						}

						$Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Parameters["Margin"] * 2;
						$Step = $Width / $Parameters["Rows"];
						$SubTicksSize = $Step / 2;
						$MinTop = $AxisPos["T"];
						$LastX = NULL;
						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
							$YPos = $AxisPos["T"];
							$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters["Display"], $Parameters["Format"], $Parameters["Unit"]);
							if ($i % 2 == 1) {
								$BGColor = $BGColor = ["R" => $BackgroundR1,"G" => $BackgroundG1,"B" => $BackgroundB1,"Alpha" => $BackgroundAlpha1];
							} else {
								$BGColor = ["R" => $BackgroundR2,"G" => $BackgroundG2,"B" => $BackgroundB2,"Alpha" => $BackgroundAlpha2];
							}

							if ($LastX != NULL && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
								$this->drawFilledRectangle($LastX, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $BGColor);
							}

							if ($DrawYLines == ALL || in_array($AxisID, $DrawYLines)) {
								$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
							}

							if ($DrawSubTicks && $i != $Parameters["Rows"]) {
								$this->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, ["R" => $SubTickR,"G" => $SubTickG,"B" => $SubTickB,"Alpha" => $SubTickAlpha]);
							}

							$this->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
							$Bounds = $this->drawText($XPos, $YPos - $OuterTickWidth - 2, $Value, ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
							$TxtHeight = $YPos - $OuterTickWidth - 2 - ($Bounds[1]["Y"] - $Bounds[2]["Y"]);
							$MinTop = min($MinTop, $TxtHeight);
							$LastX = $XPos;
						}

						if (isset($Parameters["Name"])) {
							$YPos = $MinTop - 2;
							$XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
							$MinTop = $Bounds[2]["Y"];
							$this->DataSet->Data["GraphArea"]["Y1"] = $MinTop;
						}

						$AxisPos["T"] = $MinTop - $ScaleSpacing;

					} elseif ($Parameters["Position"] == AXIS_POSITION_BOTTOM) {
						if ($Floating) {
							$FloatingOffset = $XMargin;
							$this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
						} else {
							$FloatingOffset = 0;
							$this->drawLine($this->GraphAreaX1, $AxisPos["B"], $this->GraphAreaX2, $AxisPos["B"], ["R" => $AxisR,"G" => $AxisG,"B" => $AxisB,"Alpha" => $AxisAlpha]);
						}

						if ($DrawArrows) {
							$this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["B"], ["FillR" => $AxisR,"FillG" => $AxisG, "FillB" => $AxisB,"Size" => $ArrowSize]);
						}

						$Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Parameters["Margin"] * 2;
						$Step = $Width / $Parameters["Rows"];
						$SubTicksSize = $Step / 2;
						$MaxBottom = $AxisPos["B"];
						$LastX = NULL;
						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
							$YPos = $AxisPos["B"];
							$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters["Display"], $Parameters["Format"], $Parameters["Unit"]);
							if ($i % 2 == 1) {
								$BGColor = ["R" => $BackgroundR1,"G" => $BackgroundG1,"B" => $BackgroundB1,"Alpha" => $BackgroundAlpha1];
							} else {
								$BGColor = ["R" => $BackgroundR2,"G" => $BackgroundG2,"B" => $BackgroundB2,"Alpha" => $BackgroundAlpha2];
							}

							if ($LastX != NULL && $CycleBackground && ($DrawYLines == ALL || in_array($AxisID, $DrawYLines))) {
								$this->drawFilledRectangle($LastX, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $BGColor);
							}

							if ($DrawYLines == ALL || in_array($AxisID, $DrawYLines)) {
								$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, ["R" => $GridR,"G" => $GridG,"B" => $GridB,"Alpha" => $GridAlpha,"Ticks" => $GridTicks]);
							}

							if ($DrawSubTicks && $i != $Parameters["Rows"]) {
								$this->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, ["R" => $SubTickR,"G" => $SubTickG,"B" => $SubTickB,"Alpha" => $SubTickAlpha]);
							}

							$this->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, ["R" => $TickR,"G" => $TickG,"B" => $TickB,"Alpha" => $TickAlpha]);
							$Bounds = $this->drawText($XPos, $YPos + $OuterTickWidth + 2, $Value, ["Align" => TEXT_ALIGN_TOPMIDDLE]);
							$TxtHeight = $YPos + $OuterTickWidth + 2 + ($Bounds[1]["Y"] - $Bounds[2]["Y"]);
							$MaxBottom = max($MaxBottom, $TxtHeight);
							$LastX = $XPos;
						}

						if (isset($Parameters["Name"])) {
							$YPos = $MaxBottom + 2;
							$XPos = $this->GraphAreaX1 + ($this->GraphAreaX2 - $this->GraphAreaX1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE]);
							$MaxBottom = $Bounds[0]["Y"];
							$this->DataSet->Data["GraphArea"]["Y2"] = $MaxBottom + $this->FontSize;
						}

						$AxisPos["B"] = $MaxBottom + $ScaleSpacing;
					}
				}
			}
		}
	}

	function isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip)
	{

		$ret = TRUE;

		switch(TRUE){
			case ($LabelingMethod == LABELING_DIFFERENT && $Value != $LastValue):
				break;
			case ($LabelingMethod == LABELING_DIFFERENT && $Value == $LastValue):
				$ret = FALSE;
				break;
			case ($LabelingMethod == LABELING_ALL && $LabelSkip == 0):
				break;
			case ($LabelingMethod == LABELING_ALL && ($ID + $LabelSkip) % ($LabelSkip + 1) != 1):
				$ret = FALSE;
				break;
		}

		return $ret;

	}

	/* Compute the scale, check for the best visual factors */
	function computeScale($XMin, $XMax, $MaxDivs, $Factors, $AxisID = 0)
	{
		/* Compute each factors */
		$Results = [];
		foreach($Factors as $Key => $Factor) {
			$Results[$Factor] = $this->processScale($XMin, $XMax, $MaxDivs, [$Factor], $AxisID);
		}
		/* Remove scales that are creating to much decimals */
		$GoodScaleFactors = [];
		foreach($Results as $Key => $Result) {
			$Decimals = preg_split("/\./", $Result["RowHeight"]);
			if ((!isset($Decimals[1])) || (strlen($Decimals[1]) < 6)) {
				$GoodScaleFactors[] = $Key;
			}
		}

		/* Found no correct scale, shame,... returns the 1st one as default */

		// if ( $GoodScaleFactors == "" ) { return($Results[$Factors[0]]); }

		if (count($GoodScaleFactors) == 0) {
			return ($Results[$Factors[0]]);
		}

		/* Find the factor that cause the maximum number of Rows */
		$MaxRows = 0;
		$BestFactor = 0;
		foreach($GoodScaleFactors as $Key => $Factor) {
			if ($Results[$Factor]["Rows"] > $MaxRows) {
				$MaxRows = $Results[$Factor]["Rows"];
				$BestFactor = $Factor;
			}
		}

		/* Return the best visual scale */
		return ($Results[$BestFactor]);
	}

	/* Compute the best matching scale based on size & factors */
	function processScale($XMin, $XMax, $MaxDivs, $Factors, $AxisID)
	{
		$ScaleHeight = abs(ceil($XMax) - floor($XMin));
		$Format = (isset($this->DataSet->Data["Axis"][$AxisID]["Format"])) ?  $this->DataSet->Data["Axis"][$AxisID]["Format"] : NULL;
		$Mode = (isset($this->DataSet->Data["Axis"][$AxisID]["Display"])) ? $this->DataSet->Data["Axis"][$AxisID]["Display"] : AXIS_FORMAT_DEFAULT;
		$Scale = [];

		if ($XMin != $XMax) {
			$Found = FALSE;
			$Rescaled = FALSE;
			$Scaled10Factor = .0001;
			$Result = 0;
			while (!$Found) {
				foreach($Factors as $Key => $Factor) {
					if (!$Found) {
						$XMinRescaled = (!($this->modulo($XMin, $Factor * $Scaled10Factor) == 0) || ($XMin != floor($XMin))) ? (floor($XMin / ($Factor * $Scaled10Factor)) * $Factor * $Scaled10Factor) : $XMin;
						$XMaxRescaled = (!($this->modulo($XMax, $Factor * $Scaled10Factor) == 0) || ($XMax != floor($XMax))) ? (floor($XMax / ($Factor * $Scaled10Factor)) * $Factor * $Scaled10Factor + ($Factor * $Scaled10Factor)) : $XMax;

						$ScaleHeightRescaled = abs($XMaxRescaled - $XMinRescaled);
						if (!$Found && floor($ScaleHeightRescaled / ($Factor * $Scaled10Factor)) <= $MaxDivs) {
							$Found = TRUE;
							$Rescaled = TRUE;
							$Result = $Factor * $Scaled10Factor;
						}
					}
				}

				$Scaled10Factor = $Scaled10Factor * 10;
			}

			/* ReCall Min / Max / Height */
			if ($Rescaled) {
				$XMin = $XMinRescaled;
				$XMax = $XMaxRescaled;
				$ScaleHeight = $ScaleHeightRescaled;
			}

			/* Compute rows size */
			$Rows = floor($ScaleHeight / $Result);
			($Rows == 0) AND $Rows = 1;
			$RowHeight = $ScaleHeight / $Rows;

			/* Return the results */
			$Scale["Rows"] = $Rows;
			$Scale["RowHeight"] = $RowHeight;
			$Scale["XMin"] = $XMin;
			$Scale["XMax"] = $XMax;
			/* Compute the needed decimals for the metric view to avoid repetition of the same X Axis labels */
			if ($Mode == AXIS_FORMAT_METRIC && $Format == NULL) {
				$Done = FALSE;
				$GoodDecimals = 0;
				for ($Decimals = 0; $Decimals <= 10; $Decimals++) {
					if (!$Done) {
						$LastLabel = "zob";
						$ScaleOK = TRUE;
						for ($i = 0; $i <= $Rows; $i++) {
							$Value = $XMin + $i * $RowHeight;
							$Label = $this->scaleFormat($Value, AXIS_FORMAT_METRIC, $Decimals);
							($LastLabel == $Label) AND $ScaleOK = FALSE;
							$LastLabel = $Label;
						}

						if ($ScaleOK) {
							$Done = TRUE;
							$GoodDecimals = $Decimals;
						}
					}
				}

				$Scale["Format"] = $GoodDecimals;
			}
		} else {
			/* If all values are the same we keep a +1/-1 scale */
			$Scale["Rows"] = 2;
			$Scale["RowHeight"] = 1;
			$Scale["XMin"] = $XMax - 1;
			$Scale["XMax"] = $XMax + 1;
		}

		return ($Scale);
	}

	function modulo($Value1, $Value2)
	{

		return (floor($Value2) == 0) ? 0 : ($Value1 % $Value2);

		#if (floor($Value2) == 0) {
		#	return (0);
		#}

		#if (floor($Value2) != 0) {
		#	return ($Value1 % $Value2);
		#}

		#$MinValue = min($Value1, $Value2); # Momchil TODO: Does it ever get here ?
		#$Factor = 10;
		#while (floor($MinValue * $Factor) == 0) {
		#	$Factor = $Factor * 10;
		#}

		#return (($Value1 * $Factor) % ($Value2 * $Factor));
	}

	/* Draw an X threshold */
	function drawXThreshold($Value, array $Format = [])
	{
		$R = 255;
		$G = 0;
		$B = 0;
		$Alpha = 50;
		$Weight = NULL;
		$Ticks = 6;
		$Wide = FALSE;
		$WideFactor = 5;
		$WriteCaption = FALSE;
		$Caption = NULL;
		$CaptionAlign = CAPTION_LEFT_TOP;
		$CaptionOffset = 5;
		$CaptionR = 255;
		$CaptionG = 255;
		$CaptionB = 255;
		$CaptionAlpha = 100;
		$DrawBox = TRUE;
		$DrawBoxBorder = FALSE;
		$BorderOffset = 3;
		$BoxRounded = TRUE;
		$RoundedRadius = 3;
		$BoxR = 0;
		$BoxG = 0;
		$BoxB = 0;
		$BoxAlpha = 30;
		$BoxSurrounding = "";
		$BoxBorderR = 255;
		$BoxBorderG = 255;
		$BoxBorderB = 255;
		$BoxBorderAlpha = 100;
		$ValueIsLabel = FALSE;

		/* Override defaults */
		extract($Format);

		$Data = $this->DataSet->getData();
		$AbscissaMargin = $this->getAbscissaMargin($Data);
		$XScale = $this->scaleGetXSettings();

		if (is_array($Value)) {
			foreach($Value as $Key => $ID) {
				$this->drawXThreshold($ID, $Format);
			}
			return (0);
		}

		if ($ValueIsLabel) {
			$Format["ValueIsLabel"] = FALSE;
			foreach($Data["Series"][$Data["Abscissa"]]["Data"] as $Key => $SerieValue) {
				if ($SerieValue == $Value) {
					$this->drawXThreshold($Key, $Format);
				}
			}
			return (0);
		}

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

		if ($Caption == NULL) {
			if (isset($Data["Abscissa"])) {
				$Caption = (isset($Data["Series"][$Data["Abscissa"]]["Data"][$Value])) ? $Data["Series"][$Data["Abscissa"]]["Data"][$Value] : $Value;
			} else {
				$Caption = $Value;
			}
		}

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$XStep = (($this->GraphAreaX2 - $this->GraphAreaX1) - $XScale[0] * 2) / $XScale[1];
			$XPos = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value;
			$YPos1 = $this->GraphAreaY1 + $Data["YMargin"];
			$YPos2 = $this->GraphAreaY2 - $Data["YMargin"];
			if ($XPos >= $this->GraphAreaX1 + $AbscissaMargin && $XPos <= $this->GraphAreaX2 - $AbscissaMargin) {
				$this->drawLine($XPos, $YPos1, $XPos, $YPos2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
				if ($Wide) {
					$this->drawLine($XPos - 1, $YPos1, $XPos - 1, $YPos2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
					$this->drawLine($XPos + 1, $YPos1, $XPos + 1, $YPos2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
				}

				if ($WriteCaption) {
					if ($CaptionAlign == CAPTION_LEFT_TOP) {
						$Y = $YPos1 + $CaptionOffset;
						$CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
					} else {
						$Y = $YPos2 - $CaptionOffset;
						$CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
					}

					$this->drawText($XPos, $Y, $Caption, $CaptionSettings);
				}

				return ["X" => $XPos];
			}

		} elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
			$XStep = (($this->GraphAreaY2 - $this->GraphAreaY1) - $XScale[0] * 2) / $XScale[1];
			$XPos = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value;
			$YPos1 = $this->GraphAreaX1 + $Data["YMargin"];
			$YPos2 = $this->GraphAreaX2 - $Data["YMargin"];
			if ($XPos >= $this->GraphAreaY1 + $AbscissaMargin && $XPos <= $this->GraphAreaY2 - $AbscissaMargin) {
				$this->drawLine($YPos1, $XPos, $YPos2, $XPos, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
				if ($Wide) {
					$this->drawLine($YPos1, $XPos - 1, $YPos2, $XPos - 1, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
					$this->drawLine($YPos1, $XPos + 1, $YPos2, $XPos + 1, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
				}

				if ($WriteCaption) {
					if ($CaptionAlign == CAPTION_LEFT_TOP) {
						$Y = $YPos1 + $CaptionOffset;
						$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
					} else {
						$Y = $YPos2 - $CaptionOffset;
						$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
					}

					$this->drawText($Y, $XPos, $Caption, $CaptionSettings);
				}

				return ["X" => $XPos];
			}
		}
	}

	/* Draw an X threshold area */
	function drawXThresholdArea($Value1, $Value2, array $Format = [])
	{
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
		$AreaName = NULL;
		$NameAngle = ZONE_NAME_ANGLE_AUTO;
		$NameR = 255;
		$NameG = 255;
		$NameB = 255;
		$NameAlpha = 100;
		$DisableShadowOnArea = TRUE;

		extract($Format);

		$RestoreShadow = $this->Shadow;
		($DisableShadowOnArea && $this->Shadow) AND $this->Shadow = FALSE;
		($BorderAlpha > 100) AND $BorderAlpha = 100;
		$Data = $this->DataSet->getData();
		$XScale = $this->scaleGetXSettings();
		$AbscissaMargin = $this->getAbscissaMargin($Data);

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$XStep = (($this->GraphAreaX2 - $this->GraphAreaX1) - $XScale[0] * 2) / $XScale[1];
			$XPos1 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value1;
			$XPos2 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value2;
			$YPos1 = $this->GraphAreaY1 + $Data["YMargin"];
			$YPos2 = $this->GraphAreaY2 - $Data["YMargin"];
			($XPos1 < $this->GraphAreaX1 + $XScale[0]) AND $XPos1 = $this->GraphAreaX1 + $XScale[0];
			($XPos1 > $this->GraphAreaX2 - $XScale[0]) AND $XPos1 = $this->GraphAreaX2 - $XScale[0];
			($XPos2 < $this->GraphAreaX1 + $XScale[0]) AND $XPos2 = $this->GraphAreaX1 + $XScale[0];
			($XPos2 > $this->GraphAreaX2 - $XScale[0]) AND $XPos2 = $this->GraphAreaX2 - $XScale[0];

			$this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha]);
			if ($Border) {
				$this->drawLine($XPos1, $YPos1, $XPos1, $YPos2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
				$this->drawLine($XPos2, $YPos1, $XPos2, $YPos2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
			}

			if ($AreaName != NULL) {
				$XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
				$YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
				if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
					$TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $AreaName);
					$TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
					$NameAngle = (abs($XPos2 - $XPos1) > $TxtWidth) ? 0 : 90;
				}

				$this->Shadow = $RestoreShadow;
				$this->drawText($XPos, $YPos, $AreaName, ["R" => $NameR,"G" => $NameG,"B" => $NameB,"Alpha" => $NameAlpha,"Angle" => $NameAngle,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				if ($DisableShadowOnArea) {
					$this->Shadow = FALSE;
				}
			}

			$this->Shadow = $RestoreShadow;

			return ["X1" => $XPos1,"X2" => $XPos2];

		} elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
			$XStep = (($this->GraphAreaY2 - $this->GraphAreaY1) - $XScale[0] * 2) / $XScale[1];
			$XPos1 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value1;
			$XPos2 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value2;
			$YPos1 = $this->GraphAreaX1 + $Data["YMargin"];
			$YPos2 = $this->GraphAreaX2 - $Data["YMargin"];
			($XPos1 < $this->GraphAreaY1 + $XScale[0]) AND $XPos1 = $this->GraphAreaY1 + $XScale[0];
			($XPos1 > $this->GraphAreaY2 - $XScale[0]) AND $XPos1 = $this->GraphAreaY2 - $XScale[0];
			($XPos2 < $this->GraphAreaY1 + $XScale[0]) AND $XPos2 = $this->GraphAreaY1 + $XScale[0];
			($XPos2 > $this->GraphAreaY2 - $XScale[0]) AND $XPos2 = $this->GraphAreaY2 - $XScale[0];

			$this->drawFilledRectangle($YPos1, $XPos1, $YPos2, $XPos2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha]);
			if ($Border) {
				$this->drawLine($YPos1, $XPos1, $YPos2, $XPos1, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
				$this->drawLine($YPos1, $XPos2, $YPos2, $XPos2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
			}

			if ($AreaName != NULL) {
				$XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
				$YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
				$this->Shadow = $RestoreShadow;
				$this->drawText($YPos, $XPos, $AreaName, ["R" => $NameR,"G" => $NameG,"B" => $NameB,"Alpha" => $NameAlpha,"Angle" => 0,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				if ($DisableShadowOnArea) {
					$this->Shadow = FALSE;
				}
			}

			$this->Shadow = $RestoreShadow;

			return ["X1" => $XPos1,"X2" => $XPos2];
		}
	}

	/* Draw an Y threshold with the computed scale */
	function drawThreshold($Value, array $Format = [])
	{

		$AxisID = 0;
		$R = 255;
		$G = 0;
		$B = 0;
		$Alpha = 50;
		$Weight = NULL;
		$Ticks = 6;
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
		$NoMargin = FALSE;

		/* Override defaults */
		extract($Format);

		$Data = $this->DataSet->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			return (-1);
		}

		if (is_array($Value)) {
			foreach($Value as $Key => $ID) {
				$this->drawThreshold($ID, $Format);
			}

			return (0);
		}

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

		$AbscissaMargin = $this->getAbscissaMargin($Data);
		($NoMargin) AND $AbscissaMargin = 0;
		($Caption == NULL) AND $Caption = $Value;

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$YPos = $this->scaleComputeY($Value, ["AxisID" => $AxisID]);
			if ($YPos >= $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"] && $YPos <= $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"]) {
				$X1 = $this->GraphAreaX1 + $AbscissaMargin;
				$X2 = $this->GraphAreaX2 - $AbscissaMargin;
				$this->drawLine($X1, $YPos, $X2, $YPos, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
				if ($Wide) {
					$this->drawLine($X1, $YPos - 1, $X2, $YPos - 1, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
					$this->drawLine($X1, $YPos + 1, $X2, $YPos + 1, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
				}

				if ($WriteCaption) {
					if ($CaptionAlign == CAPTION_LEFT_TOP) {
						$X = $X1 + $CaptionOffset;
						$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT;
					} else {
						$X = $X2 - $CaptionOffset;
						$CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT;
					}

					$this->drawText($X, $YPos, $Caption, $CaptionSettings);
				}
			}

			return ["Y" => $YPos];
		}

		if ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
			$XPos = $this->scaleComputeY($Value,["AxisID" => $AxisID]);
			if ($XPos >= $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] && $XPos <= $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"]) {
				$Y1 = $this->GraphAreaY1 + $AbscissaMargin;
				$Y2 = $this->GraphAreaY2 - $AbscissaMargin;
				$this->drawLine($XPos, $Y1, $XPos, $Y2,["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
				if ($Wide) {
					$this->drawLine($XPos - 1, $Y1, $XPos - 1, $Y2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
					$this->drawLine($XPos + 1, $Y1, $XPos + 1, $Y2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / $WideFactor,"Ticks" => $Ticks]);
				}

				if ($WriteCaption) {
					if ($CaptionAlign == CAPTION_LEFT_TOP) {
						$Y = $Y1 + $CaptionOffset;
						$CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
					} else {
						$Y = $Y2 - $CaptionOffset;
						$CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
					}

					$CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
					$this->drawText($XPos, $Y, $Caption, $CaptionSettings);
				}
			}

			return ["Y" => $XPos];
		}
	}

	/* Draw a threshold with the computed scale */
	function drawThresholdArea($Value1, $Value2, array $Format = [])
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
		$AreaName = NULL;
		$NameAngle = ZONE_NAME_ANGLE_AUTO;
		$NameR = 255;
		$NameG = 255;
		$NameB = 255;
		$NameAlpha = 100;
		$DisableShadowOnArea = TRUE;
		$NoMargin = FALSE;

		extract($Format);

		$Data = $this->DataSet->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			return (-1);
		}

		if ($Value1 > $Value2) {
			list($Value1, $Value2) = [$Value2,$Value1];
		}

		$RestoreShadow = $this->Shadow;
		($DisableShadowOnArea && $this->Shadow) AND $this->Shadow = FALSE;
		($BorderAlpha > 100) AND $BorderAlpha = 100;

		$AbscissaMargin = $this->getAbscissaMargin($Data);
		($NoMargin) AND $AbscissaMargin = 0;

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$XPos1 = $this->GraphAreaX1 + $AbscissaMargin;
			$XPos2 = $this->GraphAreaX2 - $AbscissaMargin;
			$YPos1 = $this->scaleComputeY($Value1, ["AxisID" => $AxisID]);
			$YPos2 = $this->scaleComputeY($Value2, ["AxisID" => $AxisID]);

			($YPos1 < $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"]) AND $YPos1 = $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
			($YPos1 > $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"]) AND $YPos1 = $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];
			($YPos2 < $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"]) AND $YPos2 = $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"];
			($YPos2 > $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"]) AND $YPos2 = $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"];

			$this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha]);
			if ($Border) {
				$this->drawLine($XPos1, $YPos1, $XPos2, $YPos1, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
				$this->drawLine($XPos1, $YPos2, $XPos2, $YPos2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
			}

			if ($AreaName != NULL) {
				$XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
				$YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
				$this->Shadow = $RestoreShadow;
				$this->drawText($XPos, $YPos, $AreaName, ["R" => $NameR,"G" => $NameG,"B" => $NameB,"Alpha" => $NameAlpha,"Angle" => 0,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				if ($DisableShadowOnArea) {
					$this->Shadow = FALSE;
				}
			}

			$this->Shadow = $RestoreShadow;

			return ["Y1" => $YPos1,"Y2" => $YPos2];

		} elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {

			$YPos1 = $this->GraphAreaY1 + $AbscissaMargin;
			$YPos2 = $this->GraphAreaY2 - $AbscissaMargin;
			$XPos1 = $this->scaleComputeY($Value1, ["AxisID" => $AxisID]);
			$XPos2 = $this->scaleComputeY($Value2, ["AxisID" => $AxisID]);

			($XPos1 < $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"]) AND $XPos1 = $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
			($XPos1 > $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"]) AND $XPos1 = $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];
			($XPos2 < $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"]) AND $XPos2 = $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"];
			($XPos2 > $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"]) AND $XPos2 = $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"];

			$this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha]);
			if ($Border) {
				$this->drawLine($XPos1, $YPos1, $XPos1, $YPos2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
				$this->drawLine($XPos2, $YPos1, $XPos2, $YPos2, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Ticks" => $BorderTicks]);
			}

			if ($AreaName != NULL) {
				$XPos = ($YPos2 - $YPos1) / 2 + $YPos1;
				$YPos = ($XPos2 - $XPos1) / 2 + $XPos1;
				if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
					$TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $AreaName);
					$TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
					$NameAngle = (abs($XPos2 - $XPos1) > $TxtWidth) ? 0 : 90;
				}

				$this->Shadow = $RestoreShadow;
				$this->drawText($YPos, $XPos, $AreaName, ["R" => $NameR,"G" => $NameG,"B" => $NameB,"Alpha" => $NameAlpha,"Angle" => $NameAngle,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
				if ($DisableShadowOnArea) {
					$this->Shadow = FALSE;
				}
			}

			$this->Shadow = $RestoreShadow;

			return ["Y1" => $XPos1,"Y2" => $XPos2];
		}
	}

	function scaleGetXSettings()
	{
		$Data = $this->DataSet->getData();
		foreach($Data["Axis"] as $AxisID => $Settings) {
			if ($Settings["Identity"] == AXIS_X) {
				return [$Settings["Margin"],$Settings["Rows"]];
			}
		}
	}

	function scaleComputeY($Values, array $Option, $ReturnOnly0Height = FALSE) // $values is often set to 0 not [0]
	{
		$Values = $this->convertToArray($Values);
		if (count($Values) == 0) { // Momchil
			$Values = [0];
		}

		$AxisID = isset($Option["AxisID"]) ? $Option["AxisID"] : 0;
		$SerieName = isset($Option["SerieName"]) ? $Option["SerieName"] : NULL;
		$Data = $this->DataSet->getData();
		if (!isset($Data["Axis"][$AxisID])) {
			return (-1);
		}

		if ($SerieName != NULL) {
			$AxisID = $Data["Series"][$SerieName]["Axis"];
		}

		$Result = [];
		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Data["Axis"][$AxisID]["Margin"] * 2;
			$ScaleHeight = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
			$Step = $Height / $ScaleHeight;
			if ($ReturnOnly0Height) {
				foreach($Values as $Key => $Value) {
					$Result[] = ($Value == VOID) ? VOID : $Step * $Value;
				}
			} else {
				foreach($Values as $Key => $Value) {
					if ($Value == VOID) {
						$Result[] = VOID;
					} else {
						if (!is_numeric($Value)) { // Momchil: No idea how that will affect the overall image
							$Value = 1;
						}
						$Result[] = $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"] - ($Step * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]));
					}
				}
			}

		} else {
			$Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Data["Axis"][$AxisID]["Margin"] * 2;
			$ScaleWidth = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
			$Step = $Width / $ScaleWidth;
			if ($ReturnOnly0Height) {
				foreach($Values as $Key => $Value) {
					$Result[] = ($Value == VOID) ? VOID : $Step * $Value;
				}
			} else {
				foreach($Values as $Key => $Value) {
					if ($Value == VOID) {
						$Result[] = VOID;
					} else {
						if (!is_numeric($Value)) { // Momchil: No idea how that will affect the overall image
							$Value = 1;
						}
						$Result[] = $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] + ($Step * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]));
					}
				}
			}
		}

		return (count($Result) == 1) ? $Result[0] : $Result;

	}

	/* Format the axis values */
	function scaleFormat($Value, $Mode = NULL, $Format = NULL, $Unit = NULL)
	{
		if ($Value == VOID) {
			return "";
		}

		$ret = $Value . $Unit; # Momchil: this is not the same as default for the switch

		switch ($Mode) {
			case AXIS_FORMAT_TRAFFIC:
				if ($Value == 0) {
					$ret = "0B";
				} else {
					$Units = ["B","KB","MB","GB","TB","PB"];
					$Sign = "";

					if ($Value < 0) {
						$Value = abs($Value);
						$Sign = "-";
					}

					$Value = number_format($Value / pow(1024, ($Scale = floor(log($Value, 1024)))), 2, ",", ".");
					$ret = $Sign . $Value . " " . $Units[$Scale];
				}
				break;
			case AXIS_FORMAT_CUSTOM:
				if (function_exists($Format)) {
					$ret = (call_user_func($Format, $Value));
				}
				break;
			case AXIS_FORMAT_DATE:
				$Pattern = ($Format == NULL) ? "d/m/Y" : $Format;
				$ret = gmdate($Pattern, $Value);
				break;
			case AXIS_FORMAT_TIME:
				$Pattern = ($Format == NULL) ? "H:i:s" : $Format;
				$ret = gmdate($Pattern, $Value);
				break;
			case AXIS_FORMAT_CURRENCY:
				$ret = $Format . number_format($Value, 2);
				break;
			case AXIS_FORMAT_METRIC:
				if (abs($Value) >= 1000) {
					$ret = (round($Value / 1000, $Format) . "k" . $Unit);
				} elseif (abs($Value) > 1000000) {
					$ret =(round($Value / 1000000, $Format) . "m" . $Unit);
				} elseif (abs($Value) > 1000000000) {
					$ret = (round($Value / 1000000000, $Format) . "g" . $Unit);
				}
				break;
		}

		return $ret;
	}

	/* Write Max value on a chart */
	function writeBounds($Type = BOUND_BOTH, array $Format = [])
	{
		$MaxLabelTxt = "max=";
		$MinLabelTxt = "min=";
		$Decimals = 1;
		$ExcludedSeries = "";
		$DisplayOffset = 4;
		$DisplayColor = DISPLAY_MANUAL;
		$MaxDisplayR = 0;
		$MaxDisplayG = 0;
		$MaxDisplayB = 0;
		$MinDisplayR = 255;
		$MinDisplayG = 255;
		$MinDisplayB = 255;
		$MinLabelPos = BOUND_LABEL_POS_AUTO;
		$MaxLabelPos = BOUND_LABEL_POS_AUTO;
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
			"BoxBorderAlpha" => $BoxBorderAlpha
		];

		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		$Data = $this->DataSet->getData();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"] && !isset($ExcludedSeries[$SerieName])) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				$MinValue = $this->DataSet->getMin($SerieName);
				$MaxValue = $this->DataSet->getMax($SerieName);
				$MinPos = VOID;
				$MaxPos = VOID;

				foreach($Serie["Data"] as $Key => $Value) {
					if ($Value == $MinValue && $MinPos == VOID) {
						$MinPos = $Key;
					}

					if ($Value == $MaxValue) {
						$MaxPos = $Key;
					}
				}

				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					$X = $this->GraphAreaX1 + $XMargin;
					$SerieOffset = isset($Serie["XOffset"]) ? $Serie["XOffset"] : 0;
					if ($Type == BOUND_MAX || $Type == BOUND_BOTH) {
						if ($MaxLabelPos == BOUND_LABEL_POS_TOP || ($MaxLabelPos == BOUND_LABEL_POS_AUTO && $MaxValue >= 0)) {
							$YPos = $PosArray[$MaxPos] - $DisplayOffset + 2;
							$Align = TEXT_ALIGN_BOTTOMMIDDLE;
						}

						if ($MaxLabelPos == BOUND_LABEL_POS_BOTTOM || ($MaxLabelPos == BOUND_LABEL_POS_AUTO && $MaxValue < 0)) {
							$YPos = $PosArray[$MaxPos] + $DisplayOffset + 2;
							$Align = TEXT_ALIGN_TOPMIDDLE;
						}

						$XPos = $X + $MaxPos * $XStep + $SerieOffset;
						$Label = $MaxLabelTxt . $this->scaleFormat(round($MaxValue, $Decimals), $Mode, $Format, $Unit);
						$TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $Label);
						$XOffset = 0;
						$YOffset = 0;

						($TxtPos[0]["X"] < $this->GraphAreaX1) AND $XOffset = (($this->GraphAreaX1 - $TxtPos[0]["X"]) / 2);
						($TxtPos[1]["X"] > $this->GraphAreaX2) AND $XOffset = - (($TxtPos[1]["X"] - $this->GraphAreaX2) / 2);
						($TxtPos[2]["Y"] < $this->GraphAreaY1) AND $YOffset = $this->GraphAreaY1 - $TxtPos[2]["Y"];
						($TxtPos[0]["Y"] > $this->GraphAreaY2) AND $YOffset = - ($TxtPos[0]["Y"] - $this->GraphAreaY2);

						$CaptionSettings["R"] = $MaxDisplayR;
						$CaptionSettings["G"] = $MaxDisplayG;
						$CaptionSettings["B"] = $MaxDisplayB;
						$CaptionSettings["Align"] = $Align;
						$this->drawText($XPos + $XOffset, $YPos + $YOffset, $Label, $CaptionSettings);
					}

					if ($Type == BOUND_MIN || $Type == BOUND_BOTH) {
						if ($MinLabelPos == BOUND_LABEL_POS_TOP || ($MinLabelPos == BOUND_LABEL_POS_AUTO && $MinValue >= 0)) {
							$YPos = $PosArray[$MinPos] - $DisplayOffset + 2;
							$Align = TEXT_ALIGN_BOTTOMMIDDLE;
						}

						if ($MinLabelPos == BOUND_LABEL_POS_BOTTOM || ($MinLabelPos == BOUND_LABEL_POS_AUTO && $MinValue < 0)) {
							$YPos = $PosArray[$MinPos] + $DisplayOffset + 2;
							$Align = TEXT_ALIGN_TOPMIDDLE;
						}

						$XPos = $X + $MinPos * $XStep + $SerieOffset;
						$Label = $MinLabelTxt . $this->scaleFormat(round($MinValue, $Decimals), $Mode, $Format, $Unit);
						$TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $Label);
						$XOffset = 0;
						$YOffset = 0;

						($TxtPos[0]["X"] < $this->GraphAreaX1) AND $XOffset = (($this->GraphAreaX1 - $TxtPos[0]["X"]) / 2);
						($TxtPos[1]["X"] > $this->GraphAreaX2) AND $XOffset = - (($TxtPos[1]["X"] - $this->GraphAreaX2) / 2);
						($TxtPos[2]["Y"] < $this->GraphAreaY1) AND $YOffset = $this->GraphAreaY1 - $TxtPos[2]["Y"];
						($TxtPos[0]["Y"] > $this->GraphAreaY2) AND $YOffset = - ($TxtPos[0]["Y"] - $this->GraphAreaY2);

						$CaptionSettings["R"] = $MinDisplayR;
						$CaptionSettings["G"] = $MinDisplayG;
						$CaptionSettings["B"] = $MinDisplayB;
						$CaptionSettings["Align"] = $Align;
						$this->drawText($XPos + $XOffset, $YPos - $DisplayOffset + $YOffset, $Label, $CaptionSettings);
					}

				} else {
					$XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					$X = $this->GraphAreaY1 + $XMargin;
					$SerieOffset = isset($Serie["XOffset"]) ? $Serie["XOffset"] : 0;
					if ($Type == BOUND_MAX || $Type == BOUND_BOTH) {
						if ($MaxLabelPos == BOUND_LABEL_POS_TOP || ($MaxLabelPos == BOUND_LABEL_POS_AUTO && $MaxValue >= 0)) {
							$YPos = $PosArray[$MaxPos] + $DisplayOffset + 2;
							$Align = TEXT_ALIGN_MIDDLELEFT;
						}

						if ($MaxLabelPos == BOUND_LABEL_POS_BOTTOM || ($MaxLabelPos == BOUND_LABEL_POS_AUTO && $MaxValue < 0)) {
							$YPos = $PosArray[$MaxPos] - $DisplayOffset + 2;
							$Align = TEXT_ALIGN_MIDDLERIGHT;
						}

						$XPos = $X + $MaxPos * $XStep + $SerieOffset;
						$Label = $MaxLabelTxt . $this->scaleFormat($MaxValue, $Mode, $Format, $Unit);
						$TxtPos = $this->getTextBox($YPos, $XPos, $this->FontName, $this->FontSize, 0, $Label);
						$XOffset = 0;
						$YOffset = 0;

						($TxtPos[0]["X"] < $this->GraphAreaX1) AND $XOffset = $this->GraphAreaX1 - $TxtPos[0]["X"];
						($TxtPos[1]["X"] > $this->GraphAreaX2) AND $XOffset = - ($TxtPos[1]["X"] - $this->GraphAreaX2);
						($TxtPos[2]["Y"] < $this->GraphAreaY1) AND $YOffset = ($this->GraphAreaY1 - $TxtPos[2]["Y"]) / 2;
						($TxtPos[0]["Y"] > $this->GraphAreaY2) AND $YOffset = - (($TxtPos[0]["Y"] - $this->GraphAreaY2) / 2);

						$CaptionSettings["R"] = $MaxDisplayR;
						$CaptionSettings["G"] = $MaxDisplayG;
						$CaptionSettings["B"] = $MaxDisplayB;
						$CaptionSettings["Align"] = $Align;
						$this->drawText($YPos + $XOffset, $XPos + $YOffset, $Label, $CaptionSettings);
					}

					if ($Type == BOUND_MIN || $Type == BOUND_BOTH) {
						if ($MinLabelPos == BOUND_LABEL_POS_TOP || ($MinLabelPos == BOUND_LABEL_POS_AUTO && $MinValue >= 0)) {
							$YPos = $PosArray[$MinPos] + $DisplayOffset + 2;
							$Align = TEXT_ALIGN_MIDDLELEFT;
						}

						if ($MinLabelPos == BOUND_LABEL_POS_BOTTOM || ($MinLabelPos == BOUND_LABEL_POS_AUTO && $MinValue < 0)) {
							$YPos = $PosArray[$MinPos] - $DisplayOffset + 2;
							$Align = TEXT_ALIGN_MIDDLERIGHT;
						}

						$XPos = $X + $MinPos * $XStep + $SerieOffset;
						$Label = $MinLabelTxt . $this->scaleFormat($MinValue, $Mode, $Format, $Unit);
						$TxtPos = $this->getTextBox($YPos, $XPos, $this->FontName, $this->FontSize, 0, $Label);
						$XOffset = 0;
						$YOffset = 0;

						($TxtPos[0]["X"] < $this->GraphAreaX1) AND $XOffset = $this->GraphAreaX1 - $TxtPos[0]["X"];
						($TxtPos[1]["X"] > $this->GraphAreaX2) AND $XOffset = - ($TxtPos[1]["X"] - $this->GraphAreaX2);
						($TxtPos[2]["Y"] < $this->GraphAreaY1) AND $YOffset = ($this->GraphAreaY1 - $TxtPos[2]["Y"]) / 2;
						($TxtPos[0]["Y"] > $this->GraphAreaY2) AND $YOffset = - (($TxtPos[0]["Y"] - $this->GraphAreaY2) / 2);

						$CaptionSettings["R"] = $MinDisplayR;
						$CaptionSettings["G"] = $MinDisplayG;
						$CaptionSettings["B"] = $MinDisplayB;
						$CaptionSettings["Align"] = $Align;
						$this->drawText($YPos + $XOffset, $XPos + $YOffset, $Label, $CaptionSettings);
					}
				}
			}
		}
	}

	/* Draw a plot chart */
	function drawPlotChart(array $Format = [])
	{
		$PlotSize = NULL;
		$PlotBorder = FALSE;
		$BorderR = 50;
		$BorderG = 50;
		$BorderB = 50;
		$BorderAlpha = 30;
		$BorderSize = 2;
		$Surrounding = NULL;
		$DisplayValues = FALSE;
		$DisplayOffset = 4;
		$DisplayColor = DISPLAY_MANUAL;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$RecordImageMap = FALSE;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$SerieWeight = (isset($Serie["Weight"])) ? $Serie["Weight"] + 2 : 2;
				($PlotSize != NULL) AND 	$SerieWeight = $PlotSize;
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				if ($Surrounding != NULL) {
					$BorderR = $R + $Surrounding;
					$BorderG = $G + $Surrounding;
					$BorderB = $B + $Surrounding;
				}

				if (isset($Serie["Picture"])) {
					$Picture = $Serie["Picture"];
					list($PicWidth, $PicHeight, $PicType) = $this->getPicInfo($Picture);
				} else {
					$Picture = NULL;
					$PicOffset = 0;
				}

				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				$AxisID = $Serie["Axis"];
				$Shape = $Serie["Shape"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$SerieDescription = (isset($Serie["Description"])) ? $Serie["Description"] : $SerieName;
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;

				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					if ($Picture != NULL) {
						$PicOffset = $PicHeight / 2;
						$SerieWeight = 0;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$PosArray = $this->convertToArray($PosArray);

					foreach($PosArray as $Key => $Y) {
						if ($DisplayValues) $this->drawText($X, $Y - $DisplayOffset - $SerieWeight - $BorderSize - $PicOffset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), array(
							"R" => $DisplayR,
							"G" => $DisplayG,
							"B" => $DisplayB,
							"Align" => TEXT_ALIGN_BOTTOMMIDDLE
						));
						if ($Y != VOID) {
							if ($RecordImageMap) {
								$this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $SerieWeight, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}

							if ($Picture != NULL) {
								$this->drawFromPicture($PicType, $Picture, $X - $PicWidth / 2, $Y - $PicHeight / 2);
							} else {
								$this->drawShape($X, $Y, $Shape, $SerieWeight, $PlotBorder, $BorderSize, $R, $G, $B, $Alpha, $BorderR, $BorderG, $BorderB, $BorderAlpha);
							}
						}

						$X = $X + $XStep;
					}

				} else {
					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					if ($Picture != NULL) {
						$PicOffset = $PicWidth / 2;
						$SerieWeight = 0;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$PosArray = $this->convertToArray($PosArray);

					foreach($PosArray as $Key => $X) {
						if ($DisplayValues) $this->drawText($X + $DisplayOffset + $SerieWeight + $BorderSize + $PicOffset, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), array(
							"Angle" => 270,
							"R" => $DisplayR,
							"G" => $DisplayG,
							"B" => $DisplayB,
							"Align" => TEXT_ALIGN_BOTTOMMIDDLE
						));
						if ($X != VOID) {
							if ($RecordImageMap) {
								$this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $SerieWeight, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}

							if ($Picture != NULL) {
								$this->drawFromPicture($PicType, $Picture, $X - $PicWidth / 2, $Y - $PicHeight / 2);
							} else {
								$this->drawShape($X, $Y, $Shape, $SerieWeight, $PlotBorder, $BorderSize, $R, $G, $B, $Alpha, $BorderR, $BorderG, $BorderB, $BorderAlpha);
							}
						}

						$Y = $Y + $YStep;
					}
				}
			}
		}
	}

	/* Draw a spline chart */
	function drawSplineChart($Format = [])
	{
		# Momchil: The sandbox system requires it
		$Format = $this->convertToArray($Format);

		$BreakVoid = TRUE;
		$VoidTicks = 4;
		$BreakR = NULL; // 234
		$BreakG = NULL; // 55
		$BreakB = NULL; // 26
		$DisplayValues = FALSE;
		$DisplayOffset = 2;
		$DisplayColor = DISPLAY_MANUAL;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$RecordImageMap = FALSE;
		$ImageMapPlotSize = 5;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				$Weight = $Serie["Weight"];
				if ($BreakR == NULL) {
					$BreakSettings = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $VoidTicks];
				} else {
					$BreakSettings = ["R" => $BreakR,"G" => $BreakG,"B" => $BreakB,"Alpha" => $Alpha,"Ticks" => $VoidTicks,"Weight" => $Weight];
				}

				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$SerieDescription = (isset($Serie["Description"])) ? $Serie["Description"] : $SerieName;
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$WayPoints = [];
					$Force = $XStep / 5;

					$PosArray = $this->convertToArray($PosArray);

					$LastGoodY = NULL;
					$LastGoodX = NULL;
					$LastX = 1;
					$LastY = 1;
					foreach($PosArray as $Key => $Y) {
						if ($DisplayValues) $this->drawText($X, $Y - $DisplayOffset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), array(
							"R" => $DisplayR,
							"G" => $DisplayG,
							"B" => $DisplayB,
							"Align" => TEXT_ALIGN_BOTTOMMIDDLE
						));
						if ($RecordImageMap && $Y != VOID) {
							$this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
						}

						if ($Y == VOID && $LastY != NULL) {
							$this->drawSpline($WayPoints, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
							$WayPoints = [];
						}

						if ($Y != VOID && $LastY == NULL && $LastGoodY != NULL && !$BreakVoid) {
							$this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $BreakSettings);
						}

						if ($Y != VOID) $WayPoints[] = [$X,$Y];
						if ($Y != VOID) {
							$LastGoodY = $Y;
							$LastGoodX = $X;
						}

						if ($Y == VOID) {
							$Y = NULL;
						}

						$LastX = $X;
						$LastY = $Y;
						$X = $X + $XStep;
					}

					$this->drawSpline($WayPoints, ["Force" => $Force,"R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);

				} else {
					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$WayPoints = [];
					$Force = $YStep / 5;

					$PosArray = $this->convertToArray($PosArray);

					$LastGoodY = NULL;
					$LastGoodX = NULL;
					$LastX = 1;
					$LastY = 1;
					foreach($PosArray as $Key => $X) {
						if ($DisplayValues) {
							$this->drawText($X + $DisplayOffset, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["Angle" => 270,"R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
						}

						if ($RecordImageMap && $X != VOID) {
							$this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
						}

						if ($X == VOID && $LastX != NULL) {
							$this->drawSpline($WayPoints, ["Force" => $Force,"R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
							$WayPoints = [];
						}

						if ($X != VOID && $LastX == NULL && $LastGoodX != NULL && !$BreakVoid) {
							$this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $BreakSettings);
						}

						if ($X != VOID) {
							$WayPoints[] = [$X,	$Y];
						#} # Momchil
						#if ($X != VOID) {
							$LastGoodX = $X;
							$LastGoodY = $Y;
						} else {
						#if ($X == VOID) {
							$X = NULL;
						}

						$LastX = $X;
						$LastY = $Y;
						$Y = $Y + $YStep;
					}

					$this->drawSpline($WayPoints, ["Force" => $Force,"R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
				}
			}
		}
	}

	/* Draw a filled spline chart */
	function drawFilledSplineChart(array $Format = [])
	{
		$DisplayValues = FALSE;
		$DisplayOffset = 2;
		$DisplayColor = DISPLAY_MANUAL;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$AroundZero = TRUE;
		$Threshold = NULL;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				if ($AroundZero) {
					$YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
				}

				if ($Threshold != NULL) {
					foreach($Threshold as $Key => $Params) {
						$Threshold[$Key]["MinX"] = $this->scaleComputeY($Params["Min"], ["AxisID" => $Serie["Axis"]]);
						$Threshold[$Key]["MaxX"] = $this->scaleComputeY($Params["Max"], ["AxisID" => $Serie["Axis"]]);
					}
				}

				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$WayPoints = [];
					$Force = $XStep / 5;
					if (!$AroundZero) {
						$YZero = $this->GraphAreaY2 - 1;
					}

					if ($YZero > $this->GraphAreaY2 - 1) {
						$YZero = $this->GraphAreaY2 - 1;
					}

					if ($YZero < $this->GraphAreaY1 + 1) {
						$YZero = $this->GraphAreaY1 + 1;
					}

					// $LastX = ""; $LastY = ""; # UNUSED
					$PosArray = $this->convertToArray($PosArray);

					foreach($PosArray as $Key => $Y) {
						if ($DisplayValues) {
							$this->drawText($X, $Y - $DisplayOffset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
						}

						if ($Y == VOID) {
							$Area = $this->drawSpline($WayPoints, ["Force" => $Force,"PathOnly" => TRUE]);
							if (count($Area) > 0) //if ( $Area != "" )
							{
								foreach($Area as $key => $Points) {
									$Corners = [$Area[$key][0]["X"], $YZero];
									foreach($Points as $subKey => $Point) {
										$Corners[] = ($subKey == count($Points) - 1) ? $Point["X"] - 1 : $Point["X"];
										$Corners[] = $Point["Y"] + 1;
									}

									$Corners[] = $Points[$subKey]["X"] - 1;
									$Corners[] = $YZero;
									$this->drawPolygonChart($Corners, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / 2,"NoBorder" => TRUE,"Threshold" => $Threshold]);
								}

								$this->drawSpline($WayPoints, ["Force" => $Force,"R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks]);
							}

							$WayPoints = [];
						} else {
							$WayPoints[] = [$X,$Y - .5]; /* -.5 for AA visual fix */
						}

						$X = $X + $XStep;
					}

					$Area = $this->drawSpline($WayPoints, ["Force" => $Force,"PathOnly" => TRUE]);
					if (count($Area) > 0) //if ( $Area != "" )
					{
						foreach($Area as $key => $Points) {
							$Corners = [$Area[$key][0]["X"], $YZero];
							foreach($Points as $subKey => $Point) {
								$Corners[] = ($subKey == count($Points) - 1) ? $Point["X"] - 1 : $Point["X"];
								$Corners[] = $Point["Y"] + 1;
							}

							$Corners[] = $Points[$subKey]["X"] - 1;
							$Corners[] = $YZero;
							$this->drawPolygonChart($Corners, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / 2,"NoBorder" => TRUE,"Threshold" => $Threshold]);
						}

						$this->drawSpline($WayPoints, ["Force" => $Force,"R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks]);
					}
				} else {
					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$WayPoints = [];
					$Force = $YStep / 5;
					if (!$AroundZero) {
						$YZero = $this->GraphAreaX1 + 1;
					}

					if ($YZero > $this->GraphAreaX2 - 1) {
						$YZero = $this->GraphAreaX2 - 1;
					}

					if ($YZero < $this->GraphAreaX1 + 1) {
						$YZero = $this->GraphAreaX1 + 1;
					}

					$PosArray = $this->convertToArray($PosArray);

					foreach($PosArray as $Key => $X) {
						if ($DisplayValues) {
							$this->drawText($X + $DisplayOffset, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), array(
								"Angle" => 270,
								"R" => $DisplayR,
								"G" => $DisplayG,
								"B" => $DisplayB,
								"Align" => TEXT_ALIGN_BOTTOMMIDDLE
							));
						}

						if ($X == VOID) {
							$Area = $this->drawSpline($WayPoints, ["Force" => $Force,"PathOnly" => TRUE]);
							if (count($Area) > 0) // if ( $Area != "" )
							{
								foreach($Area as $key => $Points) {
									$Corners = [$YZero,$Area[$key][0]["Y"]];
									foreach($Points as $subKey => $Point) {
										$Corners[] = ($subKey == count($Points) - 1) ? $Point["X"] - 1 : $Point["X"];
										$Corners[] = $Point["Y"];
									}

									$Corners[] = $YZero;
									$Corners[] = $Points[$subKey]["Y"] - 1;
									$this->drawPolygonChart($Corners, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / 2,"NoBorder" => TRUE,"Threshold" => $Threshold]);
								}

								$this->drawSpline($WayPoints, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha / 2,"NoBorder" => TRUE,"Threshold" => $Threshold]);
							}

							$WayPoints = [];
						} else {
							$WayPoints[] = [$X,$Y];
						}

						$Y = $Y + $YStep;
					}

					$Area = $this->drawSpline($WayPoints, ["Force" => $Force,"PathOnly" => TRUE]);
					if (count($Area) > 0) // if ( $Area != "" )
					{
						foreach($Area as $key => $Points) {
							$Corners = [];
							$Corners[] = $YZero;
							$Corners[] = $Area[$key][0]["Y"];
							foreach($Points as $subKey => $Point) {
								$Corners[] = ($subKey == count($Points) - 1) ? $Point["X"] - 1 : $Point["X"];
								$Corners[] = $Point["Y"];
							}

							$Corners[] = $YZero;
							$Corners[] = $Points[$subKey]["Y"] - 1;
							$this->drawPolygonChart($Corners, ["Force" => $Force,"R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks]);
						}

						$this->drawSpline($WayPoints, ["Force" => $Force,"R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks]);
					}
				}
			}
		}
	}

	/* Draw a line chart */
	function drawLineChart(array $Format = [])
	{
		$BreakVoid = TRUE;
		$VoidTicks = 4;
		$BreakR = NULL;
		$BreakG = NULL;
		$BreakB = NULL;
		$DisplayValues = FALSE;
		$DisplayOffset = 2;
		$DisplayColor = DISPLAY_MANUAL;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$RecordImageMap = FALSE;
		$ImageMapPlotSize = 5;
		$ForceColor = FALSE;
		$ForceR = 0;
		$ForceG = 0;
		$ForceB = 0;
		$ForceAlpha = 100;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				$Weight = $Serie["Weight"];
				if ($ForceColor) {
					$R = $ForceR;
					$G = $ForceG;
					$B = $ForceB;
					$Alpha = $ForceAlpha;
				}

				if ($BreakR == NULL) {
					$BreakSettings = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $VoidTicks,"Weight" => $Weight];
				} else {
					$BreakSettings = ["R" => $BreakR,"G" => $BreakG,"B" => $BreakB,"Alpha" => $Alpha,"Ticks" => $VoidTicks,"Weight" => $Weight];
				}

				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$SerieDescription = (isset($Serie["Description"])) ? $Serie["Description"] : $SerieName;
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$LastX = NULL;
					$LastY = NULL;

					$PosArray = $this->convertToArray($PosArray);

					$LastGoodY = NULL;
					$LastGoodX = NULL;
					foreach($PosArray as $Key => $Y) {
						if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
							if ($Serie["Data"][$Key] > 0) {
								$Align = TEXT_ALIGN_BOTTOMMIDDLE;
								$Offset = $DisplayOffset;
							} else {
								$Align = TEXT_ALIGN_TOPMIDDLE;
								$Offset = - $DisplayOffset;
							}

							$this->drawText($X, $Y - $Offset - $Weight, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => $Align]);
						}

						if ($RecordImageMap && $Y != VOID) {
							$this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
						}

						if ($Y != VOID && $LastX != NULL && $LastY != NULL) $this->drawLine($LastX, $LastY, $X, $Y, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,	"Weight" => $Weight]);
						if ($Y != VOID && $LastY == NULL && $LastGoodY != NULL && !$BreakVoid) {
							$this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $BreakSettings);
							$LastGoodY = NULL;
						}

						if ($Y != VOID) {
							$LastGoodY = $Y;
							$LastGoodX = $X;
						}

						if ($Y == VOID) {
							$Y = NULL;
						}

						$LastX = $X;
						$LastY = $Y;
						$X = $X + $XStep;
					}

				} else {
					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$LastX = NULL;
					$LastY = NULL;

					$PosArray = $this->convertToArray($PosArray);

					$LastGoodY = NULL;
					$LastGoodX = NULL;
					foreach($PosArray as $Key => $X) {
						if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
							$this->drawText($X + $DisplayOffset + $Weight, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["Angle" => 270,"R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
						}

						if ($RecordImageMap && $X != VOID) {
							$this->addToImageMap("CIRCLE", floor($X) . "," . floor($Y) . "," . $ImageMapPlotSize, $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
						}

						if ($X != VOID && $LastX != NULL && $LastY != NULL) $this->drawLine($LastX, $LastY, $X, $Y, ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight]);
						if ($X != VOID && $LastX == NULL && $LastGoodY != NULL && !$BreakVoid) {
							$this->drawLine($LastGoodX, $LastGoodY, $X, $Y, $BreakSettings);
							$LastGoodY = NULL;
						}

						if ($X != VOID) {
							$LastGoodY = $Y;
							$LastGoodX = $X;
						}

						if ($X == VOID) {
							$X = NULL;
						}

						$LastX = $X;
						$LastY = $Y;
						$Y = $Y + $YStep;
					}
				}
			}
		}
	}

	/* Draw a line chart */
	function drawZoneChart($SerieA, $SerieB, array $Format = [])
	{
		$AxisID = 0;
		$LineR = 150;
		$LineG = 150;
		$LineB = 150;
		$LineAlpha = 50;
		$LineTicks = 1;
		$AreaR = 150;
		$AreaG = 150;
		$AreaB = 150;
		$AreaAlpha = 5;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		if (!isset($Data["Series"][$SerieA]["Data"]) || !isset($Data["Series"][$SerieB]["Data"])) {
			return (0);
		}

		$SerieAData = $Data["Series"][$SerieA]["Data"];
		$SerieBData = $Data["Series"][$SerieB]["Data"];
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		$Mode = $Data["Axis"][$AxisID]["Display"];
		$Format = $Data["Axis"][$AxisID]["Format"];
		$Unit = $Data["Axis"][$AxisID]["Unit"];
		$PosArrayA = $this->scaleComputeY($SerieAData, ["AxisID" => $AxisID]);
		$PosArrayB = $this->scaleComputeY($SerieBData, ["AxisID" => $AxisID]);
		if (count($PosArrayA) != count($PosArrayB)) {
			return (0);
		}

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			if ($XDivs == 0) {
				$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
			} else {
				$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
			}

			$X = $this->GraphAreaX1 + $XMargin;
			$LastX = NULL;
			$LastY = NULL;
			$LastX = NULL;
			$LastY1 = NULL;
			$LastY2 = NULL;
			$BoundsA = [];
			$BoundsB = [];
			foreach($PosArrayA as $Key => $Y1) {
				$Y2 = $PosArrayB[$Key];
				$BoundsA[] = $X;
				$BoundsA[] = $Y1;
				$BoundsB[] = $X;
				$BoundsB[] = $Y2;
				$LastX = $X;
				$LastY1 = $Y1;
				$LastY2 = $Y2;
				$X = $X + $XStep;
			}

			$Bounds = array_merge($BoundsA, $this->reversePlots($BoundsB));
			$this->drawPolygonChart($Bounds, ["R" => $AreaR,"G" => $AreaG,"B" => $AreaB,"Alpha" => $AreaAlpha]);
			for ($i = 0; $i <= count($BoundsA) - 4; $i = $i + 2) {
				$this->drawLine($BoundsA[$i], $BoundsA[$i + 1], $BoundsA[$i + 2], $BoundsA[$i + 3], ["R" => $LineR,"G" => $LineG,"B" => $LineB,"Alpha" => $LineAlpha,"Ticks" => $LineTicks]);
				$this->drawLine($BoundsB[$i], $BoundsB[$i + 1], $BoundsB[$i + 2], $BoundsB[$i + 3], ["R" => $LineR,"G" => $LineG,"B" => $LineB,"Alpha" => $LineAlpha,"Ticks" => $LineTicks]);
			}
		} else {
			if ($XDivs == 0) {
				$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
			} else {
				$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
			}

			$Y = $this->GraphAreaY1 + $XMargin;
			$LastX = NULL;
			$LastY = NULL;
			$LastY = NULL;
			$LastX1 = NULL;
			$LastX2 = NULL;
			$BoundsA = [];
			$BoundsB = [];
			foreach($PosArrayA as $Key => $X1) {
				$X2 = $PosArrayB[$Key];
				$BoundsA[] = $X1;
				$BoundsA[] = $Y;
				$BoundsB[] = $X2;
				$BoundsB[] = $Y;
				$LastY = $Y;
				$LastX1 = $X1;
				$LastX2 = $X2;
				$Y = $Y + $YStep;
			}

			$Bounds = array_merge($BoundsA, $this->reversePlots($BoundsB));
			$this->drawPolygonChart($Bounds, ["R" => $AreaR,"G" => $AreaG,"B" => $AreaB,"Alpha" => $AreaAlpha]);
			for ($i = 0; $i <= count($BoundsA) - 4; $i = $i + 2) {
				$this->drawLine($BoundsA[$i], $BoundsA[$i + 1], $BoundsA[$i + 2], $BoundsA[$i + 3], ["R" => $LineR,"G" => $LineG,"B" => $LineB,"Alpha" => $LineAlpha,"Ticks" => $LineTicks]);
				$this->drawLine($BoundsB[$i], $BoundsB[$i + 1], $BoundsB[$i + 2], $BoundsB[$i + 3], ["R" => $LineR,"G" => $LineG,"B" => $LineB,"Alpha" => $LineAlpha,"Ticks" => $LineTicks]);
			}
		}
	}

	/* Draw a step chart */
	function drawStepChart(array $Format = [])
	{
		$BreakVoid = FALSE;
		$ReCenter = TRUE;
		$VoidTicks = 4;
		$BreakR = NULL;
		$BreakG = NULL;
		$BreakB = NULL;
		$DisplayValues = FALSE;
		$DisplayOffset = 2;
		$DisplayColor = DISPLAY_MANUAL;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$RecordImageMap = FALSE;
		$ImageMapPlotSize = 5;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				$Weight = $Serie["Weight"];
				$SerieDescription = (isset($Serie["Description"])) ? $Serie["Description"] : $SerieName;

				if ($BreakR == NULL) {
					$BreakSettings = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $VoidTicks,"Weight" => $Weight];
				} else {
					$BreakSettings = ["R" => $BreakR,"G" => $BreakG,"B" => $BreakB,"Alpha" => $Alpha,"Ticks" => $VoidTicks,"Weight" => $Weight];
				}

				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight];
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;

				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$LastX = NULL;
					$LastY = NULL;

					$PosArray = $this->convertToArray($PosArray);

					$LastGoodY = NULL;
					$LastGoodX = NULL;
					$Init = FALSE;
					foreach($PosArray as $Key => $Y) {
						if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
							if ($Y <= $LastY) {
								$Align = TEXT_ALIGN_BOTTOMMIDDLE;
								$Offset = $DisplayOffset;
							} else {
								$Align = TEXT_ALIGN_TOPMIDDLE;
								$Offset = - $DisplayOffset;
							}

							$this->drawText($X, $Y - $Offset - $Weight, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => $Align]);
						}

						if ($Y != VOID && $LastX != NULL && $LastY != NULL) {
							$this->drawLine($LastX, $LastY, $X, $LastY, $Color);
							$this->drawLine($X, $LastY, $X, $Y, $Color);
							if ($ReCenter && $X + $XStep < $this->GraphAreaX2 - $XMargin) {
								$this->drawLine($X, $Y, $X + $XStep, $Y, $Color);
								if ($RecordImageMap) {
									$this->addToImageMap("RECT", floor($X - $ImageMapPlotSize) . "," . floor($Y - $ImageMapPlotSize) . "," . floor($X + $XStep + $ImageMapPlotSize) . "," . floor($Y + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
								}
							} else {
								if ($RecordImageMap) {
									$this->addToImageMap("RECT", floor($LastX - $ImageMapPlotSize) . "," . floor($LastY - $ImageMapPlotSize) . "," . floor($X + $ImageMapPlotSize) . "," . floor($LastY + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
								}
							}
						}

						if ($Y != VOID && $LastY == NULL && $LastGoodY != NULL && !$BreakVoid) {
							if ($ReCenter) {
								$this->drawLine($LastGoodX + $XStep, $LastGoodY, $X, $LastGoodY, $BreakSettings);
								if ($RecordImageMap) {
									$this->addToImageMap("RECT", floor($LastGoodX + $XStep - $ImageMapPlotSize) . "," . floor($LastGoodY - $ImageMapPlotSize) . "," . floor($X + $ImageMapPlotSize) . "," . floor($LastGoodY + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
								}
							} else {
								$this->drawLine($LastGoodX, $LastGoodY, $X, $LastGoodY, $BreakSettings);
								if ($RecordImageMap) {
									$this->addToImageMap("RECT", floor($LastGoodX - $ImageMapPlotSize) . "," . floor($LastGoodY - $ImageMapPlotSize) . "," . floor($X + $ImageMapPlotSize) . "," . floor($LastGoodY + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
								}
							}

							$this->drawLine($X, $LastGoodY, $X, $Y, $BreakSettings);
							$LastGoodY = NULL;

						} elseif (!$BreakVoid && $LastGoodY == NULL && $Y != VOID) {
							$this->drawLine($this->GraphAreaX1 + $XMargin, $Y, $X, $Y, $BreakSettings);
							if ($RecordImageMap) {
								$this->addToImageMap("RECT", floor($this->GraphAreaX1 + $XMargin - $ImageMapPlotSize) . "," . floor($Y - $ImageMapPlotSize) . "," . floor($X + $ImageMapPlotSize) . "," . floor($Y + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}
						}

						if ($Y != VOID) {
							$LastGoodY = $Y;
							$LastGoodX = $X;
						}

						if ($Y == VOID) {
							$Y = NULL;
						}

						if (!$Init && $ReCenter) {
							$X = $X - $XStep / 2;
							$Init = TRUE;
						}

						$LastX = $X;
						$LastY = $Y;
						if ($LastX < $this->GraphAreaX1 + $XMargin) {
							$LastX = $this->GraphAreaX1 + $XMargin;
						}

						$X = $X + $XStep;
					}

					if ($ReCenter) {
						$this->drawLine($LastX, $LastY, $this->GraphAreaX2 - $XMargin, $LastY, $Color);
						if ($RecordImageMap) {
							$this->addToImageMap("RECT", floor($LastX - $ImageMapPlotSize) . "," . floor($LastY - $ImageMapPlotSize) . "," . floor($this->GraphAreaX2 - $XMargin + $ImageMapPlotSize) . "," . floor($LastY + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
						}
					}

				} else {
					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$LastX = NULL;
					$LastY = NULL;

					$PosArray = $this->convertToArray($PosArray);

					$LastGoodY = NULL;
					$LastGoodX = NULL;
					$Init = FALSE;
					foreach($PosArray as $Key => $X) {
						if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
							if ($X >= $LastX) {
								$Align = TEXT_ALIGN_MIDDLELEFT;
								$Offset = $DisplayOffset;
							} else {
								$Align = TEXT_ALIGN_MIDDLERIGHT;
								$Offset = - $DisplayOffset;
							}

							$this->drawText($X + $Offset + $Weight, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => $Align]);
						}

						if ($X != VOID && $LastX != NULL && $LastY != NULL) {
							$this->drawLine($LastX, $LastY, $LastX, $Y, $Color);
							$this->drawLine($LastX, $Y, $X, $Y, $Color);
							if ($RecordImageMap) {
								$this->addToImageMap("RECT", floor($LastX - $ImageMapPlotSize) . "," . floor($LastY - $ImageMapPlotSize) . "," . floor($LastX + $XStep + $ImageMapPlotSize) . "," . floor($Y + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}
						}

						if ($X != VOID && $LastX == NULL && $LastGoodY != NULL && !$BreakVoid) {
							$this->drawLine($LastGoodX, $LastGoodY, $LastGoodX, $LastGoodY + $YStep, $Color);
							if ($RecordImageMap) {
								$this->addToImageMap("RECT", floor($LastGoodX - $ImageMapPlotSize) . "," . floor($LastGoodY - $ImageMapPlotSize) . "," . floor($LastGoodX + $ImageMapPlotSize) . "," . floor($LastGoodY + $YStep + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}

							$this->drawLine($LastGoodX, $LastGoodY + $YStep, $LastGoodX, $Y, $BreakSettings);
							if ($RecordImageMap) {
								$this->addToImageMap("RECT", floor($LastGoodX - $ImageMapPlotSize) . "," . floor($LastGoodY + $YStep - $ImageMapPlotSize) . "," . floor($LastGoodX + $ImageMapPlotSize) . "," . floor($YStep + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}

							$this->drawLine($LastGoodX, $Y, $X, $Y, $BreakSettings);
							$LastGoodY = NULL;
						} elseif ($X != VOID && $LastGoodY == NULL && !$BreakVoid) {
							$this->drawLine($X, $this->GraphAreaY1 + $XMargin, $X, $Y, $BreakSettings);
							if ($RecordImageMap) {
								$this->addToImageMap("RECT", floor($X - $ImageMapPlotSize) . "," . floor($this->GraphAreaY1 + $XMargin - $ImageMapPlotSize) . "," . floor($X + $ImageMapPlotSize) . "," . floor($Y + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}
						}

						if ($X != VOID) {
							$LastGoodY = $Y;
							$LastGoodX = $X;
						}

						if ($X == VOID) {
							$X = NULL;
						}

						if (!$Init && $ReCenter) {
							$Y = $Y - $YStep / 2;
							$Init = TRUE;
						}

						$LastX = $X;
						$LastY = $Y;
						if ($LastY < $this->GraphAreaY1 + $XMargin) {
							$LastY = $this->GraphAreaY1 + $XMargin;
						}

						$Y = $Y + $YStep;
					}

					if ($ReCenter) {
						$this->drawLine($LastX, $LastY, $LastX, $this->GraphAreaY2 - $XMargin, $Color);
						if ($RecordImageMap) {
							$this->addToImageMap("RECT", floor($LastX - $ImageMapPlotSize) . "," . floor($LastY - $ImageMapPlotSize) . "," . floor($LastX + $ImageMapPlotSize) . "," . floor($this->GraphAreaY2 - $XMargin + $ImageMapPlotSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
						}
					}
				}
			}
		}
	}

	/* Draw a step chart */
	function drawFilledStepChart(array $Format = [])
	{
		$ReCenter = TRUE;
		$DisplayValues = FALSE;
		$DisplayOffset = 2;
		$DisplayColor = DISPLAY_MANUAL;
		$ForceTransparency = NULL;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$AroundZero = TRUE;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				$Weight = $Serie["Weight"];
				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$Color = ["R" => $R,"G" => $G,"B" => $B];
				$Color["Alpha"] = ($ForceTransparency != NULL) ? $ForceTransparency : $Alpha;

				$PosArray = $this->scaleComputeY($Serie["Data"],["AxisID" => $Serie["Axis"]]);
				$YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					if ($YZero > $this->GraphAreaY2 - 1) {
						$YZero = $this->GraphAreaY2 - 1;
					}

					if ($YZero < $this->GraphAreaY1 + 1) {
						$YZero = $this->GraphAreaY1 + 1;
					}

					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$LastX = NULL;
					$LastY = NULL;
					if (!$AroundZero) {
						$YZero = $this->GraphAreaY2 - 1;
					}

					$PosArray = $this->convertToArray($PosArray);

					$LastGoodY = NULL;
					$LastGoodX = NULL;
					$Points = [];
					$Init = FALSE;

					foreach($PosArray as $Key => $Y) {

						if ($Y == VOID && $LastX != NULL && $LastY != NULL && (count($Points) > 0)) {
							$Points += [$LastX,$LastY,$X,$LastY,$X,$YZero];
							$this->drawPolygon($Points, $Color);
							$Points = [];
						}

						if ($Y != VOID && $LastX != NULL && $LastY != NULL) {

							if (count($Points) == 0) {
								$Points[] = $LastX;
								$Points[] = $YZero;
							}

							$Points += [$LastX,$LastY,$X,$LastY,$X,$Y];
						}

						if ($Y != VOID) {
							$LastGoodY = $Y;
							$LastGoodX = $X;
						} else { # Momchil
						#if ($Y == VOID) {
							$Y = NULL;
						}

						if (!$Init && $ReCenter) {
							$X = $X - $XStep / 2;
							$Init = TRUE;
						}

						$LastX = $X;
						$LastY = $Y;
						if ($LastX < $this->GraphAreaX1 + $XMargin) {
							$LastX = $this->GraphAreaX1 + $XMargin;
						}

						$X = $X + $XStep;
					}

					if ($ReCenter) {
						$Points += [$LastX + $XStep / 2, $LastY,$LastX + $XStep / 2, $YZero];
					} else {
						$Points[] = $LastX;
						$Points[] = $YZero;
					}

					$this->drawPolygon($Points, $Color);

				} else {
					if ($YZero < $this->GraphAreaX1 + 1) {
						$YZero = $this->GraphAreaX1 + 1;
					}

					if ($YZero > $this->GraphAreaX2 - 1) {
						$YZero = $this->GraphAreaX2 - 1;
					}

					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$LastX = NULL;
					$LastY = NULL;

					$PosArray = $this->convertToArray($PosArray);

					$LastGoodY = NULL;
					$LastGoodX = NULL;
					$Points = [];
					foreach($PosArray as $Key => $X) {

						if ($X == VOID && $LastX != NULL && $LastY != NULL && (count($Points) > 0)) {
							$Points += [$LastX,$LastY,$LastX,$Y,$YZero,$Y];
							$this->drawPolygon($Points, $Color);
							$Points = [];
						}

						if ($X != VOID && $LastX != NULL && $LastY != NULL) {
							(count($Points) == 0) AND $Points = [$YZero, $LastY];
							$Points += [$LastX,$LastY,$LastX,$Y,$X,$Y];
						}

						if ($X != VOID) {
							$LastGoodY = $Y;
							$LastGoodX = $X;
						} else { # Momchil
						#if ($X == VOID) {
							$X = NULL;
						}

						if ($LastX == NULL && $ReCenter) {
							$Y = $Y - $YStep / 2;
						}

						$LastX = $X;
						$LastY = $Y;
						if ($LastY < $this->GraphAreaY1 + $XMargin) {
							$LastY = $this->GraphAreaY1 + $XMargin;
						}

						$Y = $Y + $YStep;
					}

					if ($ReCenter) {
						$Points += [$LastX,$LastY + $YStep / 2,$YZero,$LastY + $YStep / 2];
					} else {
						$Points[] = $YZero;
						$Points[] = $LastY;
					}

					$this->drawPolygon($Points, $Color);
				}
			}
		}
	}

	/* Draw an area chart */
	function drawAreaChart(array $Format = [])
	{
		$DisplayValues = FALSE;
		$DisplayOffset = 2;
		$DisplayColor = DISPLAY_MANUAL;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$ForceTransparency = 25;
		$AroundZero = TRUE;
		$Threshold = NULL;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				$YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
				if ($Threshold != NULL) {
					foreach($Threshold as $Key => $Params) {
						$Threshold[$Key]["MinX"] = $this->scaleComputeY($Params["Min"], ["AxisID" => $Serie["Axis"]]);
						$Threshold[$Key]["MaxX"] = $this->scaleComputeY($Params["Max"], ["AxisID" => $Serie["Axis"]]);
					}
				}

				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					if ($YZero > $this->GraphAreaY2 - 1) {
						$YZero = $this->GraphAreaY2 - 1;
					}

					$Areas = [];
					$AreaID = 0;
					$Areas[$AreaID][] = $this->GraphAreaX1 + $XMargin;
					$Areas[$AreaID][] = ($AroundZero) ? $YZero : $this->GraphAreaY2 - 1;

					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$LastX = NULL;
					$LastY = NULL;

					$PosArray = $this->convertToArray($PosArray);

					foreach($PosArray as $Key => $Y) {
						if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
							if ($Serie["Data"][$Key] > 0) {
								$Align = TEXT_ALIGN_BOTTOMMIDDLE;
								$Offset = $DisplayOffset;
							} else {
								$Align = TEXT_ALIGN_TOPMIDDLE;
								$Offset = - $DisplayOffset;
							}

							$this->drawText($X, $Y - $Offset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => $Align]);
						}

						if ($Y == VOID && isset($Areas[$AreaID])) {
							$Areas[$AreaID][] = ($LastX == NULL) ? $X : $LastX;
							$Areas[$AreaID][] = ($AroundZero) ? $YZero : $this->GraphAreaY2 - 1;
							$AreaID++;
						} elseif ($Y != VOID) {
							if (!isset($Areas[$AreaID])) {
								$Areas[$AreaID][] = $X;
								$Areas[$AreaID][] = ($AroundZero) ? $YZero : $this->GraphAreaY2 - 1;
							}

							$Areas[$AreaID][] = $X;
							$Areas[$AreaID][] = $Y;
						}

						$LastX = $X;
						$X = $X + $XStep;
					}

					$Areas[$AreaID][] = $LastX;
					$Areas[$AreaID][] = ($AroundZero) ? $YZero : $this->GraphAreaY2 - 1;

					/* Handle shadows in the areas */
					if ($this->Shadow) {
						$ShadowArea = [];
						foreach($Areas as $Key => $Points) {
							$ShadowArea[$Key] = [];
							foreach($Points as $Key2 => $Value) {
								$ShadowArea[$Key][] = ($Key2 % 2 == 0) ? $Value + $this->ShadowX : $Value + $this->ShadowY;
							}
						}

						foreach($ShadowArea as $Key => $Points) {
							$this->drawPolygonChart($Points, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa]);
						}
					}

					$Alpha = $ForceTransparency != NULL ? $ForceTransparency : $Alpha;
					$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Threshold" => $Threshold];

					foreach($Areas as $Key => $Points){
						$this->drawPolygonChart($Points, $Color);
					}

				} else {
					if ($YZero < $this->GraphAreaX1 + 1) {
						$YZero = $this->GraphAreaX1 + 1;
					}

					if ($YZero > $this->GraphAreaX2 - 1) {
						$YZero = $this->GraphAreaX2 - 1;
					}

					$AreaID = 0;
					$Areas = [];
					$Areas[$AreaID][] = ($AroundZero) ? $YZero : $this->GraphAreaX1 + 1;
					$Areas[$AreaID][] = $this->GraphAreaY1 + $XMargin;

					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$LastX = NULL;
					$LastY = NULL;

					$PosArray = $this->convertToArray($PosArray);

					foreach($PosArray as $Key => $X) {
						if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
							if ($Serie["Data"][$Key] > 0) {
								$Align = TEXT_ALIGN_BOTTOMMIDDLE;
								$Offset = $DisplayOffset;
							} else {
								$Align = TEXT_ALIGN_TOPMIDDLE;
								$Offset = - $DisplayOffset;
							}

							$this->drawText($X + $Offset, $Y, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit),["Angle" => 270,"R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => $Align]);
						}

						if ($X == VOID && isset($Areas[$AreaID])) {
							$Areas[$AreaID][] = ($AroundZero) ? $YZero : $this->GraphAreaX1 + 1;
							$Areas[$AreaID][] = ($LastY == NULL) ? $Y : $LastY;
							$AreaID++;
						} elseif ($X != VOID) {
							if (!isset($Areas[$AreaID])) {
								$Areas[$AreaID][] = ($AroundZero) ? $YZero : $this->GraphAreaX1 + 1;
								$Areas[$AreaID][] = $Y;
							}

							$Areas[$AreaID][] = $X;
							$Areas[$AreaID][] = $Y;
						}

						$LastX = $X;
						$LastY = $Y;
						$Y = $Y + $YStep;
					}

					$Areas[$AreaID][] = ($AroundZero) ? $YZero : $this->GraphAreaX1 + 1;
					$Areas[$AreaID][] = $LastY;

					/* Handle shadows in the areas */
					if ($this->Shadow) {
						$ShadowArea = [];
						foreach($Areas as $Key => $Points) {
							$ShadowArea[$Key] = [];
							foreach($Points as $Key2 => $Value) {
								$ShadowArea[$Key][] = ($Key2 % 2 == 0) ? ($Value + $this->ShadowX) : ($Value + $this->ShadowY);
							}
						}

						foreach($ShadowArea as $Key => $Points) {
							$this->drawPolygonChart($Points, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa]);
						}
					}

					$Alpha = $ForceTransparency != NULL ? $ForceTransparency : $Alpha;
					$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Threshold" => $Threshold];
					foreach($Areas as $Key => $Points) {
						$this->drawPolygonChart($Points, $Color);
					}
				}
			}
		}
	}

	/* Draw a bar chart */
	function drawBarChart(array $Format = [])
	{
		$Floating0Serie = NULL;
		$Floating0Value = NULL;
		$Draw0Line = FALSE;
		$DisplayValues = FALSE;
		$DisplayOrientation = ORIENTATION_HORIZONTAL;
		$DisplayOffset = 2;
		$DisplayColor = DISPLAY_MANUAL;
		$DisplayFont = isset($Format["DisplaySize"]) ? $Format["DisplaySize"] : $this->FontName; # Momchil: Probably a bug
		$DisplaySize = $this->FontSize;
		$DisplayPos = LABEL_POS_OUTSIDE;
		$DisplayShadow = TRUE;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$AroundZero = TRUE;
		$Interleave = .5;
		$Rounded = FALSE;
		$RoundRadius = 4;
		$Surrounding = NULL;
		$BorderR = -1;
		$BorderG = -1;
		$BorderB = -1;
		$Gradient = FALSE;
		$GradientMode = GRADIENT_SIMPLE;
		$GradientAlpha = 20;
		$GradientStartR = 255;
		$GradientStartG = 255;
		$GradientStartB = 255;
		$GradientEndR = 0;
		$GradientEndG = 0;
		$GradientEndB = 0;
		$TxtMargin = 6;
		$OverrideColors = NULL;
		$OverrideSurrounding = 30;
		$InnerSurrounding = NULL;
		$InnerBorderR = -1;
		$InnerBorderG = -1;
		$InnerBorderB = -1;
		$RecordImageMap = FALSE;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		if ($OverrideColors != NULL) {
			$OverrideColors = $this->validatePalette($OverrideColors, $OverrideSurrounding);
			$this->DataSet->saveExtendedData("Palette", $OverrideColors);
		}

		$RestoreShadow = $this->Shadow;
		$SeriesCount = $this->countDrawableSeries();
		$CurrentSerie = 0;
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = $R;
					$DisplayG = $G;
					$DisplayB = $B;
				}

				if ($Surrounding != NULL) {
					$BorderR = $R + $Surrounding;
					$BorderG = $G + $Surrounding;
					$BorderB = $B + $Surrounding;
				}

				if ($InnerSurrounding != NULL) {
					$InnerBorderR = $R + $InnerSurrounding;
					$InnerBorderG = $G + $InnerSurrounding;
					$InnerBorderB = $B + $InnerSurrounding;
				}

				$InnerColor = ($InnerBorderR == - 1) ? NULL : ["R" => $InnerBorderR,"G" => $InnerBorderG,"B" => $InnerBorderB];
				$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"BorderR" => $BorderR,"BorderG" => $BorderG,"BorderB" => $BorderB];
				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$SerieDescription =  (isset($Serie["Description"])) ? $Serie["Description"] : $SerieName;

				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				if ($Floating0Value != NULL) {
					$YZero = $this->scaleComputeY($Floating0Value, ["AxisID" => $Serie["Axis"]]);
				} else {
					$YZero = $this->scaleComputeY([], ["AxisID" => $Serie["Axis"]]);
				}

				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					($YZero > $this->GraphAreaY2 - 1) AND $YZero = $this->GraphAreaY2 - 1;
					($YZero < $this->GraphAreaY1 + 1) AND $YZero = $this->GraphAreaY1 + 1;
					$XStep = ($XDivs == 0) ? 0 : ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					$X = $this->GraphAreaX1 + $XMargin;
					$Y1 = ($AroundZero) ? $YZero : $this->GraphAreaY2 - 1;

					if ($XDivs == 0) {
						$XSize = ($this->GraphAreaX2 - $this->GraphAreaX1) / ($SeriesCount + $Interleave);
					} else {
						$XSize = ($XStep / ($SeriesCount + $Interleave));
					}

					$XOffset = - ($XSize * $SeriesCount) / 2 + $CurrentSerie * $XSize;
					if ($X + $XOffset <= $this->GraphAreaX1) {
						$XOffset = $this->GraphAreaX1 - $X + 1;
					}

					$this->DataSet->Data["Series"][$SerieName]["XOffset"] = $XOffset + $XSize / 2;
					$XSpace = ($Rounded || $BorderR != - 1) ? 1 : 0;

					$PosArray = $this->convertToArray($PosArray);

					$ID = 0;
					foreach($PosArray as $Key => $Y2) {
						if ($Floating0Serie != NULL) {
							$Value = (isset($Data["Series"][$Floating0Serie]["Data"][$Key])) ? $Data["Series"][$Floating0Serie]["Data"][$Key] : 0;
							$YZero = $this->scaleComputeY($Value, ["AxisID" => $Serie["Axis"]]);
							($YZero > $this->GraphAreaY2 - 1) AND $YZero = $this->GraphAreaY2 - 1;
							($YZero < $this->GraphAreaY1 + 1) AND $YZero = $this->GraphAreaY1 + 1;
							$Y1 = ($AroundZero) ? $YZero : $this->GraphAreaY2 - 1;
						}

						if ($OverrideColors != NULL) {
							if (isset($OverrideColors[$ID])) {
								$Color = ["R" => $OverrideColors[$ID]["R"],"G" => $OverrideColors[$ID]["G"],"B" => $OverrideColors[$ID]["B"],"Alpha" => $OverrideColors[$ID]["Alpha"],"BorderR" => $OverrideColors[$ID]["BorderR"],"BorderG" => $OverrideColors[$ID]["BorderG"],"BorderB" => $OverrideColors[$ID]["BorderB"]];
							} else {
								$Color = $this->getRandomColor();
							}
						}

						if ($Y2 != VOID) {
							$BarHeight = $Y1 - $Y2;
							if ($Serie["Data"][$Key] == 0) {
								$this->drawLine($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y1, $Color);
								if ($RecordImageMap) {
									$this->addToImageMap("RECT", floor($X + $XOffset + $XSpace) . "," . floor($Y1 - 1) . "," . floor($X + $XOffset + $XSize - $XSpace) . "," . floor($Y1 + 1), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
								}
							} else {
								if ($RecordImageMap) {
									$this->addToImageMap("RECT", floor($X + $XOffset + $XSpace) . "," . floor($Y1) . "," . floor($X + $XOffset + $XSize - $XSpace) . "," . floor($Y2), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
								}

								if ($Rounded){
									$this->drawRoundedFilledRectangle($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, $RoundRadius, $Color);
								} else {
									$this->drawFilledRectangle($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, $Color);
									if ($InnerColor != NULL) {
										$this->drawRectangle($X + $XOffset + $XSpace + 1, min($Y1, $Y2) + 1, $X + $XOffset + $XSize - $XSpace - 1, max($Y1, $Y2) - 1, $InnerColor);
									}

									if ($Gradient) {
										$this->Shadow = FALSE;
										if ($GradientMode == GRADIENT_SIMPLE) {
											if ($Serie["Data"][$Key] >= 0) {
												$GradienColor = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $GradientAlpha];
											} else {
												$GradienColor = ["StartR" => $GradientEndR,"StartG" => $GradientEndG,"StartB" => $GradientEndB,"EndR" => $GradientStartR,"EndG" => $GradientStartG,"EndB" => $GradientStartB,"Alpha" => $GradientAlpha];
											}
											$this->drawGradientArea($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, DIRECTION_VERTICAL, $GradienColor);
										} elseif ($GradientMode == GRADIENT_EFFECT_CAN) {
											$GradienColor1 = ["StartR" => $GradientEndR,"StartG" => $GradientEndG,"StartB" => $GradientEndB,"EndR" => $GradientStartR,"EndG" => $GradientStartG,"EndB" => $GradientStartB,"Alpha" => $GradientAlpha];
											$GradienColor2 = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $GradientAlpha];
											$XSpan = floor($XSize / 3);
											$this->drawGradientArea($X + $XOffset + $XSpace, $Y1, $X + $XOffset + $XSpan - $XSpace, $Y2, DIRECTION_HORIZONTAL, $GradienColor1);
											$this->drawGradientArea($X + $XOffset + $XSpan + $XSpace, $Y1, $X + $XOffset + $XSize - $XSpace, $Y2, DIRECTION_HORIZONTAL, $GradienColor2);
										}

										$this->Shadow = $RestoreShadow;
									}
								}

								if ($Draw0Line) {
									$Line0Color = ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 20];
									$Line0Width = (abs($Y1 - $Y2) > 3) ? 3 : 1;
									($Y1 - $Y2 < 0) AND $Line0Width = - $Line0Width;
									$this->drawFilledRectangle($X + $XOffset + $XSpace, floor($Y1), $X + $XOffset + $XSize - $XSpace, floor($Y1) - $Line0Width, $Line0Color);
									$this->drawLine($X + $XOffset + $XSpace, floor($Y1), $X + $XOffset + $XSize - $XSpace, floor($Y1), $Line0Color);
								}
							}

							if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
								($DisplayShadow) AND $this->Shadow = TRUE;
								$Caption = $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit);
								$TxtPos = $this->getTextBox(0, 0, $DisplayFont, $DisplaySize, 90, $Caption);
								$TxtHeight = $TxtPos[0]["Y"] - $TxtPos[1]["Y"] + $TxtMargin;
								if ($DisplayPos == LABEL_POS_INSIDE && abs($TxtHeight) < abs($BarHeight)) {
									$CenterX = (($X + $XOffset + $XSize - $XSpace) - ($X + $XOffset + $XSpace)) / 2 + $X + $XOffset + $XSpace;
									$CenterY = ($Y2 - $Y1) / 2 + $Y1;
									$this->drawText($CenterX, $CenterY, $Caption, ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontSize" => $DisplaySize,"Angle" => 90]);
								} else {
									if ($Serie["Data"][$Key] >= 0) {
										$Align = TEXT_ALIGN_BOTTOMMIDDLE;
										$Offset = $DisplayOffset;
									} else {
										$Align = TEXT_ALIGN_TOPMIDDLE;
										$Offset = - $DisplayOffset;
									}

									$this->drawText($X + $XOffset + $XSize / 2, $Y2 - $Offset, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => $Align,"FontSize" => $DisplaySize]);
								}

								$this->Shadow = $RestoreShadow;
							}
						}

						$X = $X + $XStep;
						$ID++;
					}

				} else {

					($YZero < $this->GraphAreaX1 + 1) AND $YZero = $this->GraphAreaX1 + 1;
					($YZero > $this->GraphAreaX2 - 1) AND $YZero = $this->GraphAreaX2 - 1;
					$YStep = ($XDivs == 0) ? 0 : ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					$Y = $this->GraphAreaY1 + $XMargin;
					$X1 = ($AroundZero) ? $YZero : $this->GraphAreaX1 + 1;

					if ($XDivs == 0) {
						$YSize = ($this->GraphAreaY2 - $this->GraphAreaY1) / ($SeriesCount + $Interleave);
					} else {
						$YSize = ($YStep / ($SeriesCount + $Interleave));
					}

					$YOffset = - ($YSize * $SeriesCount) / 2 + $CurrentSerie * $YSize;
					if ($Y + $YOffset <= $this->GraphAreaY1) {
						$YOffset = $this->GraphAreaY1 - $Y + 1;
					}

					$this->DataSet->Data["Series"][$SerieName]["XOffset"] = $YOffset + $YSize / 2;
					$YSpace = ($Rounded || $BorderR != - 1) ? 1 : 0;

					$PosArray = $this->convertToArray($PosArray);

					$ID = 0;
					foreach($PosArray as $Key => $X2) {
						if ($Floating0Serie != NULL) {
							$Value = (isset($Data["Series"][$Floating0Serie]["Data"][$Key])) ? $Data["Series"][$Floating0Serie]["Data"][$Key] : 0;
							$YZero = $this->scaleComputeY($Value, ["AxisID" => $Serie["Axis"]]);
							($YZero < $this->GraphAreaX1 + 1) AND $YZero = $this->GraphAreaX1 + 1;
							($YZero > $this->GraphAreaX2 - 1) AND $YZero = $this->GraphAreaX2 - 1;
							$X1 = ($AroundZero) ? $YZero : $this->GraphAreaX1 + 1;
						}

						if ($OverrideColors != NULL) {
							if (isset($OverrideColors[$ID])) {
								$Color = ["R" => $OverrideColors[$ID]["R"],"G" => $OverrideColors[$ID]["G"],"B" => $OverrideColors[$ID]["B"],"Alpha" => $OverrideColors[$ID]["Alpha"],"BorderR" => $OverrideColors[$ID]["BorderR"],"BorderG" => $OverrideColors[$ID]["BorderG"],"BorderB" => $OverrideColors[$ID]["BorderB"]];
							} else {
								$Color = $this->getRandomColor();
							}
						}

						if ($X2 != VOID) {
							$BarWidth = $X2 - $X1;
							if ($Serie["Data"][$Key] == 0) {
								$this->drawLine($X1, $Y + $YOffset + $YSpace, $X1, $Y + $YOffset + $YSize - $YSpace, $Color);
								if ($RecordImageMap) {
									$this->addToImageMap("RECT", floor($X1 - 1) . "," . floor($Y + $YOffset + $YSpace) . "," . floor($X1 + 1) . "," . floor($Y + $YOffset + $YSize - $YSpace), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
								}
							} else {
								if ($RecordImageMap) {
									$this->addToImageMap("RECT", floor($X1) . "," . floor($Y + $YOffset + $YSpace) . "," . floor($X2) . "," . floor($Y + $YOffset + $YSize - $YSpace), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
								}

								if ($Rounded) {
									$this->drawRoundedFilledRectangle($X1 + 1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, $RoundRadius, $Color);
								} else {
									$this->drawFilledRectangle($X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, $Color);
									if ($InnerColor != NULL) {
										$this->drawRectangle(min($X1, $X2) + 1, $Y + $YOffset + $YSpace + 1, max($X1, $X2) - 1, $Y + $YOffset + $YSize - $YSpace - 1, $InnerColor);
									}

									if ($Gradient) {
										$this->Shadow = FALSE;
										if ($GradientMode == GRADIENT_SIMPLE) {
											if ($Serie["Data"][$Key] >= 0) {
												$GradienColor = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $GradientAlpha];
											} else {
												$GradienColor = ["StartR" => $GradientEndR,"StartG" => $GradientEndG,"StartB" => $GradientEndB,"EndR" => $GradientStartR,"EndG" => $GradientStartG,"EndB" => $GradientStartB,"Alpha" => $GradientAlpha];
											}

											$this->drawGradientArea($X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSize - $YSpace, DIRECTION_HORIZONTAL, $GradienColor);
										} elseif ($GradientMode == GRADIENT_EFFECT_CAN) {
											$GradienColor1 = ["StartR" => $GradientEndR,"StartG" => $GradientEndG,"StartB" => $GradientEndB,"EndR" => $GradientStartR,"EndG" => $GradientStartG,"EndB" => $GradientStartB,"Alpha" => $GradientAlpha];
											$GradienColor2 = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $GradientAlpha];
											$YSpan = floor($YSize / 3);
											$this->drawGradientArea($X1, $Y + $YOffset + $YSpace, $X2, $Y + $YOffset + $YSpan - $YSpace, DIRECTION_VERTICAL, $GradienColor1);
											$this->drawGradientArea($X1, $Y + $YOffset + $YSpan, $X2, $Y + $YOffset + $YSize - $YSpace, DIRECTION_VERTICAL, $GradienColor2);
										}

										$this->Shadow = $RestoreShadow;
									}
								}

								if ($Draw0Line) {
									$Line0Color = ["R" => 0,"G" => 0,"B" => 0,"Alpha" => 20];
									$Line0Width = (abs($X1 - $X2) > 3) ? 3 : 1;
									($X2 - $X1 < 0) AND $Line0Width = - $Line0Width;
									$this->drawFilledRectangle(floor($X1), $Y + $YOffset + $YSpace, floor($X1) + $Line0Width, $Y + $YOffset + $YSize - $YSpace, $Line0Color);
									$this->drawLine(floor($X1), $Y + $YOffset + $YSpace, floor($X1), $Y + $YOffset + $YSize - $YSpace, $Line0Color);
								}
							}

							if ($DisplayValues && $Serie["Data"][$Key] != VOID) {
								($DisplayShadow) AND $this->Shadow = TRUE;
								$Caption = $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit);
								$TxtPos = $this->getTextBox(0, 0, $DisplayFont, $DisplaySize, 0, $Caption);
								$TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"] + $TxtMargin;
								if ($DisplayPos == LABEL_POS_INSIDE && abs($TxtWidth) < abs($BarWidth)) {
									$CenterX = ($X2 - $X1) / 2 + $X1;
									$CenterY = (($Y + $YOffset + $YSize - $YSpace) - ($Y + $YOffset + $YSpace)) / 2 + ($Y + $YOffset + $YSpace);
									$this->drawText($CenterX, $CenterY, $Caption, ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontSize" => $DisplaySize]);
								} else {
									if ($Serie["Data"][$Key] >= 0) {
										$Align = TEXT_ALIGN_MIDDLELEFT;
										$Offset = $DisplayOffset;
									} else {
										$Align = TEXT_ALIGN_MIDDLERIGHT;
										$Offset = - $DisplayOffset;
									}

									$this->drawText($X2 + $Offset, $Y + $YOffset + $YSize / 2, $Caption, ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => $Align,"FontSize" => $DisplaySize]);
								}

								$this->Shadow = $RestoreShadow;
							}
						}

						$Y = $Y + $YStep;
						$ID++;
					}
				}

				$CurrentSerie++;
			}
		}
	}

	/* Draw a bar chart */
	function drawStackedBarChart(array $Format = [])
	{
		$DisplayValues = FALSE;
		$DisplayOrientation = ORIENTATION_AUTO;
		$DisplayRound = 0;
		$DisplayColor = DISPLAY_MANUAL;
		$DisplayFont = $this->FontName;
		$DisplaySize = $this->FontSize;
		$DisplayR = 0;
		$DisplayG = 0;
		$DisplayB = 0;
		$Interleave = .5;
		$Rounded = FALSE;
		$RoundRadius = 4;
		$Surrounding = NULL;
		$BorderR = -1;
		$BorderG = -1;
		$BorderB = -1;
		$Gradient = FALSE;
		$GradientMode = GRADIENT_SIMPLE;
		$GradientAlpha = 20;
		$GradientStartR = 255;
		$GradientStartG = 255;
		$GradientStartB = 255;
		$GradientEndR = 0;
		$GradientEndG = 0;
		$GradientEndB = 0;
		$InnerSurrounding = NULL;
		$InnerBorderR = -1;
		$InnerBorderG = -1;
		$InnerBorderB = -1;
		$RecordImageMap = FALSE;
		$FontFactor = 8;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_STACKED;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		$RestoreShadow = $this->Shadow;
		$LastX = [];
		$LastY = [];
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				if ($DisplayColor == DISPLAY_AUTO) {
					$DisplayR = 255;
					$DisplayG = 255;
					$DisplayB = 255;
				}

				if ($Surrounding != NULL) {
					$BorderR = $R + $Surrounding;
					$BorderG = $G + $Surrounding;
					$BorderB = $B + $Surrounding;
				}

				if ($InnerSurrounding != NULL) {
					$InnerBorderR = $R + $InnerSurrounding;
					$InnerBorderG = $G + $InnerSurrounding;
					$InnerBorderB = $B + $InnerSurrounding;
				}

				$InnerColor = ($InnerBorderR == - 1) ? NULL : ["R" => $InnerBorderR,"G" => $InnerBorderG,"B" => $InnerBorderB];
				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$SerieDescription = (isset($Serie["Description"])) ? $Serie["Description"] : $SerieName;
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]], TRUE);
				$YZero = $this->scaleComputeY(0, ["AxisID" => $Serie["Axis"]]);
				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
				$Color = ["TransCorner" => TRUE,"R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"BorderR" => $BorderR,"BorderG" => $BorderG,"BorderB" => $BorderB];
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					($YZero > $this->GraphAreaY2 - 1) AND $YZero = $this->GraphAreaY2 - 1;
					($YZero > $this->GraphAreaY2 - 1) AND $YZero = $this->GraphAreaY2 - 1;

					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$XSize = ($XStep / (1 + $Interleave));
					$XOffset = - ($XSize / 2);

					$PosArray = $this->convertToArray($PosArray);

					foreach($PosArray as $Key => $Height) {
						if ($Height != VOID && $Serie["Data"][$Key] != 0) {
							$Pos = ($Serie["Data"][$Key] > 0) ? "+" : "-";

							(!isset($LastY[$Key])) AND $LastY[$Key] = [];
							(!isset($LastY[$Key][$Pos])) AND $LastY[$Key][$Pos] = $YZero;

							$Y1 = $LastY[$Key][$Pos];
							$Y2 = $Y1 - $Height;
							$YSpaceUp = (($Rounded || $BorderR != - 1) && ($Pos == "+" && $Y1 != $YZero)) ? 1 : 0;
							$YSpaceDown = (($Rounded || $BorderR != - 1) && ($Pos == "-" && $Y1 != $YZero)) ? 1 : 0;

							if ($RecordImageMap) {
								$this->addToImageMap("RECT", floor($X + $XOffset) . "," . floor($Y1 - $YSpaceUp + $YSpaceDown) . "," . floor($X + $XOffset + $XSize) . "," . floor($Y2), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}

							if ($Rounded) {
								$this->drawRoundedFilledRectangle($X + $XOffset, $Y1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2, $RoundRadius, $Color);
							} else {
								$this->drawFilledRectangle($X + $XOffset, $Y1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2, $Color);
								if ($InnerColor != NULL) {
									$RestoreShadow = $this->Shadow;
									$this->Shadow = FALSE;
									$this->drawRectangle(min($X + $XOffset + 1, $X + $XOffset + $XSize), min($Y1 - $YSpaceUp + $YSpaceDown, $Y2) + 1, max($X + $XOffset + 1, $X + $XOffset + $XSize) - 1, max($Y1 - $YSpaceUp + $YSpaceDown, $Y2) - 1, $InnerColor);
									$this->Shadow = $RestoreShadow;
								}

								if ($Gradient) {
									$this->Shadow = FALSE;
									if ($GradientMode == GRADIENT_SIMPLE) {
										$GradientColor = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $GradientAlpha];
										$this->drawGradientArea($X + $XOffset, $Y1 - 1 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2 + 1, DIRECTION_VERTICAL, $GradientColor);
									} elseif ($GradientMode == GRADIENT_EFFECT_CAN) {
										$GradientColor1 = ["StartR" => $GradientEndR,"StartG" => $GradientEndG,"StartB" => $GradientEndB,"EndR" => $GradientStartR,"EndG" => $GradientStartG,"EndB" => $GradientStartB,"Alpha" => $GradientAlpha];
										$GradientColor2 = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $GradientAlpha];
										$XSpan = floor($XSize / 3);
										$this->drawGradientArea($X + $XOffset - .5, $Y1 - .5 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSpan, $Y2 + .5, DIRECTION_HORIZONTAL, $GradientColor1);
										$this->drawGradientArea($X + $XSpan + $XOffset - .5, $Y1 - .5 - $YSpaceUp + $YSpaceDown, $X + $XOffset + $XSize, $Y2 + .5, DIRECTION_HORIZONTAL, $GradientColor2);
									}

									$this->Shadow = $RestoreShadow;
								}
							}

							if ($DisplayValues) {
								$BarHeight = abs($Y2 - $Y1) - 2;
								$BarWidth = $XSize + ($XOffset / 2) - $FontFactor;
								$Caption = $this->scaleFormat(round($Serie["Data"][$Key], $DisplayRound), $Mode, $Format, $Unit);
								$TxtPos = $this->getTextBox(0, 0, $DisplayFont, $DisplaySize, 0, $Caption);
								$TxtHeight = abs($TxtPos[2]["Y"] - $TxtPos[0]["Y"]);
								$TxtWidth = abs($TxtPos[1]["X"] - $TxtPos[0]["X"]);
								$XCenter = (($X + $XOffset + $XSize) - ($X + $XOffset)) / 2 + $X + $XOffset;
								$YCenter = (($Y2) - ($Y1 - $YSpaceUp + $YSpaceDown)) / 2 + $Y1 - $YSpaceUp + $YSpaceDown;
								$Done = FALSE;
								if ($DisplayOrientation == ORIENTATION_HORIZONTAL || $DisplayOrientation == ORIENTATION_AUTO) {
									if ($TxtHeight < $BarHeight && $TxtWidth < $BarWidth) {
										$this->drawText($XCenter, $YCenter, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontSize" => $DisplaySize,"FontName" => $DisplayFont]);
										$Done = TRUE;
									}
								}

								if ($DisplayOrientation == ORIENTATION_VERTICAL || ($DisplayOrientation == ORIENTATION_AUTO && !$Done)) {
									if ($TxtHeight < $BarWidth && $TxtWidth < $BarHeight) {
										$this->drawText($XCenter, $YCenter, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Angle" => 90,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontSize" => $DisplaySize,"FontName" => $DisplayFont]);
									}
								}
							}

							$LastY[$Key][$Pos] = $Y2;
						}

						$X = $X + $XStep;
					}
				} else { # SCALE_POS_LEFTRIGHT

					($YZero < $this->GraphAreaX1 + 1) AND $YZero = $this->GraphAreaX1 + 1;
					($YZero > $this->GraphAreaX2 - 1) AND $YZero = $this->GraphAreaX2 - 1;

					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$YSize = $YStep / (1 + $Interleave);
					$YOffset = - ($YSize / 2);

					$PosArray = $this->convertToArray($PosArray);

					foreach($PosArray as $Key => $Width) {
						if ($Width != VOID && $Serie["Data"][$Key] != 0) {
							$Pos = ($Serie["Data"][$Key] > 0) ? "+" : "-";
							(!isset($LastX[$Key])) AND $LastX[$Key] = [];
							(!isset($LastX[$Key][$Pos])) AND $LastX[$Key][$Pos] = $YZero;
							$X1 = $LastX[$Key][$Pos];
							$X2 = $X1 + $Width;
							$XSpaceLeft = (($Rounded || $BorderR != - 1) && ($Pos == "+" && $X1 != $YZero)) ? 2 : 0;
							$XSpaceRight = (($Rounded || $BorderR != - 1) && ($Pos == "-" && $X1 != $YZero)) ? 2 : 0;

							if ($RecordImageMap) {
								$this->addToImageMap("RECT", floor($X1 + $XSpaceLeft) . "," . floor($Y + $YOffset) . "," . floor($X2 - $XSpaceRight) . "," . floor($Y + $YOffset + $YSize), $this->toHTMLColor($R, $G, $B), $SerieDescription, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit));
							}

							if ($Rounded) {
								$this->drawRoundedFilledRectangle($X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, $RoundRadius, $Color);
							} else {
								$this->drawFilledRectangle($X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, $Color);
								if ($InnerColor != NULL) {
									$RestoreShadow = $this->Shadow;
									$this->Shadow = FALSE;
									$this->drawRectangle(min($X1 + $XSpaceLeft, $X2 - $XSpaceRight) + 1, min($Y + $YOffset, $Y + $YOffset + $YSize) + 1, max($X1 + $XSpaceLeft, $X2 - $XSpaceRight) - 1, max($Y + $YOffset, $Y + $YOffset + $YSize) - 1, $InnerColor);
									$this->Shadow = $RestoreShadow;
								}

								if ($Gradient) {
									$this->Shadow = FALSE;
									if ($GradientMode == GRADIENT_SIMPLE) {
										$GradientColor = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $GradientAlpha];
										$this->drawGradientArea($X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, DIRECTION_HORIZONTAL, $GradientColor);
									} elseif ($GradientMode == GRADIENT_EFFECT_CAN) {
										$GradientColor1 = ["StartR" => $GradientEndR,"StartG" => $GradientEndG,"StartB" => $GradientEndB,"EndR" => $GradientStartR,"EndG" => $GradientStartG,"EndB" => $GradientStartB,"Alpha" => $GradientAlpha];
										$GradientColor2 = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $GradientAlpha];
										$YSpan = floor($YSize / 3);
										$this->drawGradientArea($X1 + $XSpaceLeft, $Y + $YOffset, $X2 - $XSpaceRight, $Y + $YOffset + $YSpan, DIRECTION_VERTICAL, $GradientColor1);
										$this->drawGradientArea($X1 + $XSpaceLeft, $Y + $YOffset + $YSpan, $X2 - $XSpaceRight, $Y + $YOffset + $YSize, DIRECTION_VERTICAL, $GradientColor2);
									}

									$this->Shadow = $RestoreShadow;
								}
							}

							if ($DisplayValues) {
								$BarWidth = abs($X2 - $X1) - $FontFactor;
								$BarHeight = $YSize + ($YOffset / 2) - $FontFactor / 2;
								$Caption = $this->scaleFormat(round($Serie["Data"][$Key], $DisplayRound), $Mode, $Format, $Unit);
								$TxtPos = $this->getTextBox(0, 0, $DisplayFont, $DisplaySize, 0, $Caption);
								$TxtHeight = abs($TxtPos[2]["Y"] - $TxtPos[0]["Y"]);
								$TxtWidth = abs($TxtPos[1]["X"] - $TxtPos[0]["X"]);
								$XCenter = ($X2 - $X1) / 2 + $X1;
								$YCenter = (($Y + $YOffset + $YSize) - ($Y + $YOffset)) / 2 + $Y + $YOffset;
								$Done = FALSE;
								if ($DisplayOrientation == ORIENTATION_HORIZONTAL || $DisplayOrientation == ORIENTATION_AUTO) {
									if ($TxtHeight < $BarHeight && $TxtWidth < $BarWidth) {
										$this->drawText($XCenter, $YCenter, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontSize" => $DisplaySize,"FontName" => $DisplayFont]);
										$Done = TRUE;
									}
								}

								if ($DisplayOrientation == ORIENTATION_VERTICAL || ($DisplayOrientation == ORIENTATION_AUTO && !$Done)) {
									if ($TxtHeight < $BarWidth && $TxtWidth < $BarHeight) {
										$this->drawText($XCenter, $YCenter, $this->scaleFormat($Serie["Data"][$Key], $Mode, $Format, $Unit), ["R" => $DisplayR,"G" => $DisplayG,"B" => $DisplayB,"Angle" => 90,"Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontSize" => $DisplaySize,"FontName" => $DisplayFont]);
									}
								}
							}

							$LastX[$Key][$Pos] = $X2;
						}

						$Y = $Y + $YStep;
					}
				}
			}
		}
	}

	/* Draw a stacked area chart */
	function drawStackedAreaChart(array $Format = [])
	{
		$DrawLine = FALSE;
		$LineSurrounding = NULL;
		$LineR = VOID;
		$LineG = VOID;
		$LineB = VOID;
		$LineAlpha = 100;
		$DrawPlot = FALSE;
		$PlotRadius = 2;
		$PlotBorder = 1;
		$PlotBorderSurrounding = NULL;
		$PlotBorderR = 0;
		$PlotBorderG = 0;
		$PlotBorderB = 0;
		$PlotBorderAlpha = 50;
		$ForceTransparency = NULL;

		/* Override defaults */
		extract($Format);

		$this->LastChartLayout = CHART_LAST_LAYOUT_STACKED;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		$RestoreShadow = $this->Shadow;
		$this->Shadow = FALSE;
		/* Build the offset data series */

		// $OffsetData    = ""; # UNUSED

		$OverallOffset = [];
		$SerieOrder = [];
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$SerieOrder[] = $SerieName;
				foreach($Serie["Data"] as $Key => $Value) {

					($Value == VOID) AND $Value = 0;
					$Sign = ($Value >= 0) ? "+" : "-";
					(!isset($OverallOffset[$Key]) || !isset($OverallOffset[$Key][$Sign])) AND $OverallOffset[$Key][$Sign] = 0;
					$Data["Series"][$SerieName]["Data"][$Key] = ($Sign == "+") ? $Value + $OverallOffset[$Key][$Sign] : $Value - $OverallOffset[$Key][$Sign];
					$OverallOffset[$Key][$Sign] = $OverallOffset[$Key][$Sign] + abs($Value);
				}
			}
		}

		$SerieOrder = array_reverse($SerieOrder);

		// $LastX = ""; $LastY = ""; # UNUSED

		foreach($SerieOrder as $Key => $SerieName) {
			$Serie = $Data["Series"][$SerieName];
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {

				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				($ForceTransparency != NULL) AND $Alpha = $ForceTransparency;
				$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha];

				if ($LineSurrounding != NULL) {
					$LineColor = ["R" => $R + $LineSurrounding,"G" => $G + $LineSurrounding,"B" => $B + $LineSurrounding,"Alpha" => $Alpha];
				} elseif ($LineR != VOID) {
					$LineColor = ["R" => $LineR,"G" => $LineG,"B" => $LineB,"Alpha" => $LineAlpha];
				} else {
					$LineColor = $Color;
				}

				if ($PlotBorderSurrounding != NULL) {
					$PlotBorderColor = ["R" => $R + $PlotBorderSurrounding,"G" => $G + $PlotBorderSurrounding,"B" => $B + $PlotBorderSurrounding,"Alpha" => $PlotBorderAlpha];
				} else {
					$PlotBorderColor = ["R" => $PlotBorderR,"G" => $PlotBorderG,"B" => $PlotBorderB,"Alpha" => $PlotBorderAlpha];
				}

				$AxisID = $Serie["Axis"];
				$Mode = $Data["Axis"][$AxisID]["Display"];
				$Format = $Data["Axis"][$AxisID]["Format"];
				$Unit = $Data["Axis"][$AxisID]["Unit"];
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]], TRUE);
				$YZero = $this->scaleComputeY([1], ["AxisID" => $Serie["Axis"]]); // MOMCHIL FIX FOR THE INCIDENTS BY TYPE
				$this->DataSet->Data["Series"][$SerieName]["XOffset"] = 0;
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					($YZero < $this->GraphAreaY1 + 1) AND $YZero = $this->GraphAreaY1 + 1;
					($YZero > $this->GraphAreaY2 - 1) AND $YZero = $this->GraphAreaY2 - 1;

					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;

					$PosArray = $this->convertToArray($PosArray);

					$Plots = [$X, $YZero];

					foreach($PosArray as $Key => $Height) {
						if ($Height != VOID) {
							$Plots[] = $X;
							$Plots[] = $YZero - $Height;
						}

						$X = $X + $XStep;
					}

					$Plots[] = $X - $XStep;
					$Plots[] = $YZero;
					$this->drawPolygon($Plots, $Color);
					$this->Shadow = $RestoreShadow;
					if ($DrawLine) {
						for ($i = 2; $i <= count($Plots) - 6; $i = $i + 2) {
							$this->drawLine($Plots[$i], $Plots[$i + 1], $Plots[$i + 2], $Plots[$i + 3], $LineColor);
						}
					}

					if ($DrawPlot) {
						for ($i = 2; $i <= count($Plots) - 4; $i = $i + 2) {
							if ($PlotBorder != 0) {
								$this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius + $PlotBorder, $PlotBorderColor);
							}

							$this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius, $Color);
						}
					}

					$this->Shadow = FALSE;

				} elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
					($YZero < $this->GraphAreaX1 + 1) AND $YZero = $this->GraphAreaX1 + 1;
					($YZero > $this->GraphAreaX2 - 1) AND $YZero = $this->GraphAreaX2 - 1;

					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;

					$PosArray = $this->convertToArray($PosArray);

					$Plots = [$YZero, $Y];
					foreach($PosArray as $Key => $Height) {
						if ($Height != VOID) {
							$Plots[] = $YZero + $Height;
							$Plots[] = $Y;
						}

						$Y = $Y + $YStep;
					}

					$Plots[] = $YZero;
					$Plots[] = $Y - $YStep;
					$this->drawPolygon($Plots, $Color);
					$this->Shadow = $RestoreShadow;
					if ($DrawLine) {
						for ($i = 2; $i <= count($Plots) - 6; $i = $i + 2) {
							$this->drawLine($Plots[$i], $Plots[$i + 1], $Plots[$i + 2], $Plots[$i + 3], $LineColor);
						}
					}

					if ($DrawPlot) {
						for ($i = 2; $i <= count($Plots) - 4; $i = $i + 2) {
							if ($PlotBorder != 0) {
								$this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius + $PlotBorder, $PlotBorderColor);
							}

							$this->drawFilledCircle($Plots[$i], $Plots[$i + 1], $PlotRadius, $Color);
						}
					}

					$this->Shadow = FALSE;
				}
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Returns a random color */
	function getRandomColor($Alpha = 100)
	{
		return ["R" => rand(0, 255),"G" => rand(0, 255),"B" => rand(0, 255),"Alpha" => $Alpha];
	}

	/* Validate a palette */
	function validatePalette($Colors, $Surrounding = NULL)
	{
		$Result = [];

		if (!is_array($Colors)) {
			return ($this->getRandomColor());
		}

		foreach($Colors as $Key => $Values) {

			$Result[$Key]["R"] = (isset($Values["R"])) ? $Values["R"] : rand(0, 255);
			$Result[$Key]["G"] = (isset($Values["G"])) ? $Values["G"] : rand(0, 255);
			$Result[$Key]["B"] = (isset($Values["B"])) ? $Values["B"] : rand(0, 255);
			$Result[$Key]["Alpha"] = (isset($Values["Alpha"])) ? $Values["Alpha"] : 100;

			if ($Surrounding != NULL) {
				$Result[$Key]["BorderR"] = $Result[$Key]["R"] + $Surrounding;
				$Result[$Key]["BorderG"] = $Result[$Key]["G"] + $Surrounding;
				$Result[$Key]["BorderB"] = $Result[$Key]["B"] + $Surrounding;

			} else {
				$Result[$Key]["BorderR"] = (isset($Values["BorderR"])) ? $Values["BorderR"] : $Result[$Key]["R"];
				$Result[$Key]["BorderG"] = (isset($Values["BorderG"])) ? $Values["BorderG"] : $Result[$Key]["G"];
				$Result[$Key]["BorderB"] = (isset($Values["BorderB"])) ? $Values["BorderB"] : $Result[$Key]["B"];
				$Result[$Key]["BorderAlpha"] = (isset($Values["BorderAlpha"])) ? $Values["BorderAlpha"] : $Result[$Key]["Alpha"];

			}
		}

		return ($Result);
	}

	/* Draw the derivative chart associated to the data series */
	function drawDerivative(array $Format = [])
	{
		$Offset = 10;
		$SerieSpacing = 3;
		$DerivativeHeight = 4;
		$ShadedSlopeBox = FALSE;
		$DrawBackground = TRUE;
		$BackgroundR = 255;
		$BackgroundG = 255;
		$BackgroundB = 255;
		$BackgroundAlpha = 20;
		$DrawBorder = TRUE;
		$BorderR = 0;
		$BorderG = 0;
		$BorderB = 0;
		$BorderAlpha = 100;
		$Caption = TRUE;
		$CaptionHeight = 10;
		$CaptionWidth = 20;
		$CaptionMargin = 4;
		$CaptionLine = FALSE;
		$CaptionBox = FALSE;
		$CaptionBorderR = 0;
		$CaptionBorderG = 0;
		$CaptionBorderB = 0;
		$CaptionFillR = 255;
		$CaptionFillG = 255;
		$CaptionFillB = 255;
		$CaptionFillAlpha = 80;
		$PositiveSlopeStartR = 184;
		$PositiveSlopeStartG = 234;
		$PositiveSlopeStartB = 88;
		$PositiveSlopeEndR = 239;
		$PositiveSlopeEndG = 31;
		$PositiveSlopeEndB = 36;
		$NegativeSlopeStartR = 184;
		$NegativeSlopeStartG = 234;
		$NegativeSlopeStartB = 88;
		$NegativeSlopeEndR = 67;
		$NegativeSlopeEndG = 124;
		$NegativeSlopeEndB = 227;

		/* Override defaults */
		extract($Format);

		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();
		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$YPos = $this->DataSet->Data["GraphArea"]["Y2"] + $Offset;
		} else {
			$XPos = $this->DataSet->Data["GraphArea"]["X2"] + $Offset;
		}

		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				$R = $Serie["Color"]["R"];
				$G = $Serie["Color"]["G"];
				$B = $Serie["Color"]["B"];
				$Alpha = $Serie["Color"]["Alpha"];
				$Ticks = $Serie["Ticks"];
				$Weight = $Serie["Weight"];
				$AxisID = $Serie["Axis"];
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					if ($Caption) {
						if ($CaptionLine) {
							$StartX = floor($this->GraphAreaX1 - $CaptionWidth + $XMargin - $CaptionMargin);
							$EndX = floor($this->GraphAreaX1 - $CaptionMargin + $XMargin);
							$CaptionSettings = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight];
							if ($CaptionBox) {
								$this->drawFilledRectangle($StartX, $YPos, $EndX, $YPos + $CaptionHeight, ["R" => $CaptionFillR,"G" => $CaptionFillG,"B" => $CaptionFillB,"BorderR" => $CaptionBorderR,"BorderG" => $CaptionBorderG,"BorderB" => $CaptionBorderB,"Alpha" => $CaptionFillAlpha]);
							}

							$this->drawLine($StartX + 2, $YPos + ($CaptionHeight / 2), $EndX - 2, $YPos + ($CaptionHeight / 2), $CaptionSettings);

						} else {
							$this->drawFilledRectangle($this->GraphAreaX1 - $CaptionWidth + $XMargin - $CaptionMargin, $YPos, $this->GraphAreaX1 - $CaptionMargin + $XMargin, $YPos + $CaptionHeight, ["R" => $R,"G" => $G,"B" => $B,"BorderR" => $CaptionBorderR,"BorderG" => $CaptionBorderG,"BorderB" => $CaptionBorderB]);
						}
					}

					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;
					$TopY = $YPos + ($CaptionHeight / 2) - ($DerivativeHeight / 2);
					$BottomY = $YPos + ($CaptionHeight / 2) + ($DerivativeHeight / 2);
					$StartX = floor($this->GraphAreaX1 + $XMargin);
					$EndX = floor($this->GraphAreaX2 - $XMargin);

					if ($DrawBackground) {
						$this->drawFilledRectangle($StartX - 1, $TopY - 1, $EndX + 1, $BottomY + 1, ["R" => $BackgroundR,"G" => $BackgroundG,"B" => $BackgroundB,"Alpha" => $BackgroundAlpha]);
					}

					if ($DrawBorder) {
						$this->drawRectangle($StartX - 1, $TopY - 1, $EndX + 1, $BottomY + 1, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha]);
					}

					$PosArray = $this->convertToArray($PosArray);

					$RestoreShadow = $this->Shadow;
					$this->Shadow = FALSE;
					/* Determine the Max slope index */
					$LastX = NULL;
					$LastY = NULL;
					$MinSlope = 0;
					$MaxSlope = 1;
					foreach($PosArray as $Key => $Y) {
						if ($Y != VOID && $LastX != NULL) {
							$Slope = ($LastY - $Y);
							($Slope > $MaxSlope) AND $MaxSlope = $Slope;
							($Slope < $MinSlope) AND $MinSlope = $Slope;
						}

						if ($Y == VOID) {
							$LastX = NULL;
							$LastY = NULL;
						} else {
							$LastX = $X;
							$LastY = $Y;
						}
					}

					$LastX = NULL;
					$LastY = NULL;
					$LastColor = NULL;
					foreach($PosArray as $Key => $Y) {
						if ($Y != VOID && $LastY != NULL) {
							$Slope = ($LastY - $Y);
							if ($Slope >= 0) {
								$SlopeIndex = (100 / $MaxSlope) * $Slope;
								$R = (($PositiveSlopeEndR - $PositiveSlopeStartR) / 100) * $SlopeIndex + $PositiveSlopeStartR;
								$G = (($PositiveSlopeEndG - $PositiveSlopeStartG) / 100) * $SlopeIndex + $PositiveSlopeStartG;
								$B = (($PositiveSlopeEndB - $PositiveSlopeStartB) / 100) * $SlopeIndex + $PositiveSlopeStartB;
							} elseif ($Slope < 0) {
								$SlopeIndex = (100 / abs($MinSlope)) * abs($Slope);
								$R = (($NegativeSlopeEndR - $NegativeSlopeStartR) / 100) * $SlopeIndex + $NegativeSlopeStartR;
								$G = (($NegativeSlopeEndG - $NegativeSlopeStartG) / 100) * $SlopeIndex + $NegativeSlopeStartG;
								$B = (($NegativeSlopeEndB - $NegativeSlopeStartB) / 100) * $SlopeIndex + $NegativeSlopeStartB;
							}

							$Color = ["R" => $R,"G" => $G,"B" => $B];

							if ($ShadedSlopeBox && $LastColor != NULL) // && $Slope != 0
							{
								$GradientSettings = ["StartR" => $LastColor["R"],"StartG" => $LastColor["G"],"StartB" => $LastColor["B"],"EndR" => $R,"EndG" => $G,"EndB" => $B];
								$this->drawGradientArea($LastX, $TopY, $X, $BottomY, DIRECTION_HORIZONTAL, $GradientSettings);
							} elseif (!$ShadedSlopeBox || $LastColor == NULL) { // || $Slope == 0
								$this->drawFilledRectangle(floor($LastX), $TopY, floor($X), $BottomY, $Color);
							}
							$LastColor = $Color;
						}

						if ($Y == VOID) {
							$LastY = NULL;
						} else {
							$LastX = $X;
							$LastY = $Y;
						}

						$X = $X + $XStep;
					}

					$YPos = $YPos + $CaptionHeight + $SerieSpacing;

				} else { # ($Data["Orientation"] == SCALE_POS_LEFTRIGHT)

					if ($Caption) {
						$StartY = floor($this->GraphAreaY1 - $CaptionWidth + $XMargin - $CaptionMargin);
						$EndY = floor($this->GraphAreaY1 - $CaptionMargin + $XMargin);
						if ($CaptionLine) {
							$CaptionSettings = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks,"Weight" => $Weight];
							if ($CaptionBox) {
								$this->drawFilledRectangle($XPos, $StartY, $XPos + $CaptionHeight, $EndY, ["R" => $CaptionFillR,"G" => $CaptionFillG,"B" => $CaptionFillB,"BorderR" => $CaptionBorderR,"BorderG" => $CaptionBorderG,"BorderB" => $CaptionBorderB,"Alpha" => $CaptionFillAlpha]);
							}

							$this->drawLine($XPos + ($CaptionHeight / 2), $StartY + 2, $XPos + ($CaptionHeight / 2), $EndY - 2, $CaptionSettings);
						} else {
							$this->drawFilledRectangle($XPos, $StartY, $XPos + $CaptionHeight, $EndY, ["R" => $R,"G" => $G,"B" => $B,"BorderR" => $CaptionBorderR,"BorderG" => $CaptionBorderG,"BorderB" => $CaptionBorderB]);
						}
					}

					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;
					$TopX = $XPos + ($CaptionHeight / 2) - ($DerivativeHeight / 2);
					$BottomX = $XPos + ($CaptionHeight / 2) + ($DerivativeHeight / 2);
					$StartY = floor($this->GraphAreaY1 + $XMargin);
					$EndY = floor($this->GraphAreaY2 - $XMargin);
					if ($DrawBackground) {
						$this->drawFilledRectangle($TopX - 1, $StartY - 1, $BottomX + 1, $EndY + 1, ["R" => $BackgroundR,"G" => $BackgroundG,"B" => $BackgroundB,"Alpha" => $BackgroundAlpha]);
					}

					if ($DrawBorder) {
						$this->drawRectangle($TopX - 1, $StartY - 1, $BottomX + 1, $EndY + 1, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha]);
					}

					$PosArray = $this->convertToArray($PosArray);

					$RestoreShadow = $this->Shadow;
					$this->Shadow = FALSE;
					/* Determine the Max slope index */
					$LastX = NULL;
					$LastY = NULL;
					$MinSlope = 0;
					$MaxSlope = 1;
					foreach($PosArray as $Key => $X) {
						if ($X != VOID && $LastX != NULL) {
							$Slope = ($X - $LastX);
							($Slope > $MaxSlope) AND $MaxSlope = $Slope;
							($Slope < $MinSlope) AND $MinSlope = $Slope;
						}

						$LastX = ($X == VOID) ? NULL : $X;
					}

					$LastX = NULL;
					$LastY = NULL;
					$LastColor = NULL;
					foreach($PosArray as $Key => $X) {
						if ($X != VOID && $LastX != NULL) {
							$Slope = ($X - $LastX);
							if ($Slope >= 0) {
								$SlopeIndex = (100 / $MaxSlope) * $Slope;
								$R = (($PositiveSlopeEndR - $PositiveSlopeStartR) / 100) * $SlopeIndex + $PositiveSlopeStartR;
								$G = (($PositiveSlopeEndG - $PositiveSlopeStartG) / 100) * $SlopeIndex + $PositiveSlopeStartG;
								$B = (($PositiveSlopeEndB - $PositiveSlopeStartB) / 100) * $SlopeIndex + $PositiveSlopeStartB;
							} elseif ($Slope < 0) {
								$SlopeIndex = (100 / abs($MinSlope)) * abs($Slope);
								$R = (($NegativeSlopeEndR - $NegativeSlopeStartR) / 100) * $SlopeIndex + $NegativeSlopeStartR;
								$G = (($NegativeSlopeEndG - $NegativeSlopeStartG) / 100) * $SlopeIndex + $NegativeSlopeStartG;
								$B = (($NegativeSlopeEndB - $NegativeSlopeStartB) / 100) * $SlopeIndex + $NegativeSlopeStartB;
							}

							$Color = ["R" => $R,"G" => $G,"B" => $B];

							if ($ShadedSlopeBox && $LastColor != NULL) {
								$GradientSettings = ["StartR" => $LastColor["R"],"StartG" => $LastColor["G"],"StartB" => $LastColor["B"],"EndR" => $R,"EndG" => $G,"EndB" => $B];
								$this->drawGradientArea($TopX, $LastY, $BottomX, $Y, DIRECTION_VERTICAL, $GradientSettings);
							} elseif (!$ShadedSlopeBox || $LastColor == NULL) {
								$this->drawFilledRectangle($TopX, floor($LastY), $BottomX, floor($Y), $Color);
							}

							$LastColor = $Color;
						}

						if ($X == VOID) {
							$LastX = NULL;
						} else {
							$LastX = $X;
							$LastY = $Y;
						}

						$Y = $Y + $XStep;
					}

					$XPos = $XPos + $CaptionHeight + $SerieSpacing;
				}

				$this->Shadow = $RestoreShadow;
			}
		}
	}

	/* Draw the line of best fit */
	function drawBestFit(array $Format = [])
	{
		$OverrideTicks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$OverrideR = isset($Format["R"]) ? $Format["R"] : VOID;
		$OverrideG = isset($Format["G"]) ? $Format["G"] : VOID;
		$OverrideB = isset($Format["B"]) ? $Format["B"] : VOID;
		$OverrideAlpha = isset($Format["Alpha"]) ? $Format["Alpha"] : VOID;
		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();

		foreach($Data["Series"] as $SerieName => $Serie) {

			if ($Serie["isDrawable"] == TRUE && $SerieName != $Data["Abscissa"]) {
				if ($OverrideR != VOID && $OverrideG != VOID && $OverrideB != VOID) {
					$R = $OverrideR;
					$G = $OverrideG;
					$B = $OverrideB;
				} else {
					$R = $Serie["Color"]["R"];
					$G = $Serie["Color"]["G"];
					$B = $Serie["Color"]["B"];
				}

				$Ticks = ($OverrideTicks == NULL) ? $Serie["Ticks"] : $OverrideTicks;
				$Alpha = ($OverrideAlpha == VOID) ? $Serie["Color"]["Alpha"] : $OverrideAlpha;
				$Color = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha,"Ticks" => $Ticks];
				$AxisID = $Serie["Axis"];
				$PosArray = $this->scaleComputeY($Serie["Data"], ["AxisID" => $Serie["Axis"]]);

				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {

					if ($XDivs == 0) {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
					} else {
						$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
					}

					$X = $this->GraphAreaX1 + $XMargin;

					$PosArray = $this->convertToArray($PosArray);

					$Sxy = 0;
					$Sx = 0;
					$Sy = 0;
					$Sxx = 0;

					foreach($PosArray as $Key => $Y) {
						if ($Y != VOID) {
							$Sxy = $Sxy + $X * $Y;
							$Sx = $Sx + $X;
							$Sy = $Sy + $Y;
							$Sxx = $Sxx + $X * $X;
						}

						$X = $X + $XStep;
					}

					$n = count($this->DataSet->stripVOID($PosArray)); //$n = count($PosArray);
					$M = (($n * $Sxy) - ($Sx * $Sy)) / (($n * $Sxx) - ($Sx * $Sx));
					$B = (($Sy) - ($M * $Sx)) / ($n);
					$X1 = $this->GraphAreaX1 + $XMargin;
					$Y1 = $M * $X1 + $B;
					$X2 = $this->GraphAreaX2 - $XMargin;
					$Y2 = $M * $X2 + $B;
					if ($Y1 < $this->GraphAreaY1) {
						$X1 = $X1 + ($this->GraphAreaY1 - $Y1);
						$Y1 = $this->GraphAreaY1;
					}

					if ($Y1 > $this->GraphAreaY2) {
						$X1 = $X1 + ($Y1 - $this->GraphAreaY2);
						$Y1 = $this->GraphAreaY2;
					}

					if ($Y2 < $this->GraphAreaY1) {
						$X2 = $X2 - ($this->GraphAreaY1 - $Y2);
						$Y2 = $this->GraphAreaY1;
					}

					if ($Y2 > $this->GraphAreaY2) {
						$X2 = $X2 - ($Y2 - $this->GraphAreaY2);
						$Y2 = $this->GraphAreaY2;
					}

					$this->drawLine($X1, $Y1, $X2, $Y2, $Color);

				} else {
					if ($XDivs == 0) {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
					} else {
						$YStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
					}

					$Y = $this->GraphAreaY1 + $XMargin;

					$PosArray = $this->convertToArray($PosArray);

					$Sxy = 0;
					$Sx = 0;
					$Sy = 0;
					$Sxx = 0;
					foreach($PosArray as $Key => $X) {
						if ($X != VOID) {
							$Sxy = $Sxy + $X * $Y;
							$Sx = $Sx + $Y;
							$Sy = $Sy + $X;
							$Sxx = $Sxx + $Y * $Y;
						}

						$Y = $Y + $YStep;
					}

					$n = count($this->DataSet->stripVOID($PosArray)); //$n = count($PosArray);
					$M = (($n * $Sxy) - ($Sx * $Sy)) / (($n * $Sxx) - ($Sx * $Sx));
					$B = (($Sy) - ($M * $Sx)) / ($n);
					$Y1 = $this->GraphAreaY1 + $XMargin;
					$X1 = $M * $Y1 + $B;
					$Y2 = $this->GraphAreaY2 - $XMargin;
					$X2 = $M * $Y2 + $B;
					if ($X1 < $this->GraphAreaX1) {
						$Y1 = $Y1 + ($this->GraphAreaX1 - $X1);
						$X1 = $this->GraphAreaX1;
					}

					if ($X1 > $this->GraphAreaX2) {
						$Y1 = $Y1 + ($X1 - $this->GraphAreaX2);
						$X1 = $this->GraphAreaX2;
					}

					if ($X2 < $this->GraphAreaX1) {
						$Y2 = $Y2 - ($this->GraphAreaY1 - $X2);
						$X2 = $this->GraphAreaX1;
					}

					if ($X2 > $this->GraphAreaX2) {
						$Y2 = $Y2 - ($X2 - $this->GraphAreaX2);
						$X2 = $this->GraphAreaX2;
					}

					$this->drawLine($X1, $Y1, $X2, $Y2, $Color);
				}
			}
		}
	}

	/* Write labels */
	function writeLabel($SeriesName, $Indexes, array $Format = [])
	{
		$OverrideTitle = NULL;
		$ForceLabels = NULL;
		$DrawPoint = LABEL_POINT_BOX;
		$DrawVerticalLine = FALSE;
		$VerticalLineR = 0;
		$VerticalLineG = 0;
		$VerticalLineB = 0;
		$VerticalLineAlpha = 40;
		$VerticalLineTicks = 2;

		/* Override defaults */
		extract($Format);

		$Data = $this->DataSet->getData();
		list($XMargin, $XDivs) = $this->scaleGetXSettings();

		$Indexes = $this->convertToArray($Indexes);
		$SeriesName = $this->convertToArray($SeriesName);

		if ($ForceLabels != NULL) {
			$ForceLabels = $this->convertToArray($ForceLabels);
		}

		foreach($Indexes as $Key => $Index) {
			$Series = [];
			if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
				if ($XDivs == 0) {
					$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1) / 4;
				} else {
					$XStep = ($this->GraphAreaX2 - $this->GraphAreaX1 - $XMargin * 2) / $XDivs;
				}

				$X = $this->GraphAreaX1 + $XMargin + $Index * $XStep;
				if ($DrawVerticalLine) {
					$this->drawLine($X, $this->GraphAreaY1 + $Data["YMargin"], $X, $this->GraphAreaY2 - $Data["YMargin"], ["R" => $VerticalLineR,"G" => $VerticalLineG,"B" => $VerticalLineB,"Alpha" => $VerticalLineAlpha,"Ticks" => $VerticalLineTicks]);
				}

				$MinY = $this->GraphAreaY2;

				foreach($SeriesName as $iKey => $SerieName) {

					if (isset($Data["Series"][$SerieName]["Data"][$Index])) {
						$AxisID = $Data["Series"][$SerieName]["Axis"];
						$XAxisMode = $Data["XAxisDisplay"];
						$XAxisFormat = $Data["XAxisFormat"];
						$XAxisUnit = $Data["XAxisUnit"];
						$AxisMode = $Data["Axis"][$AxisID]["Display"];
						$AxisFormat = $Data["Axis"][$AxisID]["Format"];
						$AxisUnit = $Data["Axis"][$AxisID]["Unit"];

						if (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
							$XLabel = $this->scaleFormat($Data["Series"][$Data["Abscissa"]]["Data"][$Index], $XAxisMode, $XAxisFormat, $XAxisUnit);
						} else {
							$XLabel = "";
						}

						if ($OverrideTitle != NULL) {
							$Description = $OverrideTitle;
						} elseif (count($SeriesName) == 1) {
							$Description = $Data["Series"][$SerieName]["Description"] . " - " . $XLabel;
						} elseif (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
							$Description = $XLabel;
						}

						$Serie = [
							"R" => $Data["Series"][$SerieName]["Color"]["R"],
							"G" => $Data["Series"][$SerieName]["Color"]["G"],
							"B" => $Data["Series"][$SerieName]["Color"]["B"],
							"Alpha" => $Data["Series"][$SerieName]["Color"]["Alpha"]
						];
						$SerieOffset = (count($SeriesName) == 1 && isset($Data["Series"][$SerieName]["XOffset"])) ? $Data["Series"][$SerieName]["XOffset"] : 0;
						$Value = $Data["Series"][$SerieName]["Data"][$Index];
						($Value == VOID) AND $Value = "NaN";

						if ($ForceLabels != NULL) {
							$Caption = isset($ForceLabels[$Key]) ? $ForceLabels[$Key] : "Not set";
						} else {
							$Caption = $this->scaleFormat($Value, $AxisMode, $AxisFormat, $AxisUnit);
						}

						if ($this->LastChartLayout == CHART_LAST_LAYOUT_STACKED) {
							$LookFor = ($Value >= 0) ? "+" : "-";
							$Value = 0;
							foreach($Data["Series"] as $Name => $SerieLookup) {
								if ($SerieLookup["isDrawable"] == TRUE && $Name != $Data["Abscissa"]) {
									if (isset($Data["Series"][$Name]["Data"][$Index]) && $Data["Series"][$Name]["Data"][$Index] != VOID) {
										if ($Data["Series"][$Name]["Data"][$Index] >= 0 && $LookFor == "+") {
											$Value = $Value + $Data["Series"][$Name]["Data"][$Index];
										}

										if ($Data["Series"][$Name]["Data"][$Index] < 0 && $LookFor == "-") {
											$Value = $Value - $Data["Series"][$Name]["Data"][$Index];
										}

										if ($Name == $SerieName) {
											break;
										}
									}
								}
							}
						}

						$X = floor($this->GraphAreaX1 + $XMargin + $Index * $XStep + $SerieOffset);
						$Y = floor($this->scaleComputeY($Value, ["AxisID" => $AxisID]));
						if ($Y < $MinY) {
							$MinY = $Y;
						}

						if ($DrawPoint == LABEL_POINT_CIRCLE) {
							$this->drawFilledCircle($X, $Y, 3, ["R" => 255,"G" => 255,"B" => 255,"BorderR" => 0,"BorderG" => 0,"BorderB" => 0]);
						} elseif ($DrawPoint == LABEL_POINT_BOX) {
							$this->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["R" => 255,"G" => 255,"B" => 255,"BorderR" => 0,"BorderG" => 0,"BorderB" => 0]);
						}

						$Series[] = ["Format" => $Serie,"Caption" => $Caption];
					}
				}

				$this->drawLabelBox($X, $MinY - 3, $Description, $Series, $Format);

			} else {
				if ($XDivs == 0) {
					$XStep = ($this->GraphAreaY2 - $this->GraphAreaY1) / 4;
				} else {
					$XStep = ($this->GraphAreaY2 - $this->GraphAreaY1 - $XMargin * 2) / $XDivs;
				}

				$Y = $this->GraphAreaY1 + $XMargin + $Index * $XStep;
				if ($DrawVerticalLine) {
					$this->drawLine($this->GraphAreaX1 + $Data["YMargin"], $Y, $this->GraphAreaX2 - $Data["YMargin"], $Y, ["R" => $VerticalLineR,"G" => $VerticalLineG,"B" => $VerticalLineB,"Alpha" => $VerticalLineAlpha,"Ticks" => $VerticalLineTicks]);
				}

				$MinX = $this->GraphAreaX2;
				foreach($SeriesName as $Key => $SerieName) {
					if (isset($Data["Series"][$SerieName]["Data"][$Index])) {
						$AxisID = $Data["Series"][$SerieName]["Axis"];
						$XAxisMode = $Data["XAxisDisplay"];
						$XAxisFormat = $Data["XAxisFormat"];
						$XAxisUnit = $Data["XAxisUnit"];
						$AxisMode = $Data["Axis"][$AxisID]["Display"];
						$AxisFormat = $Data["Axis"][$AxisID]["Format"];
						$AxisUnit = $Data["Axis"][$AxisID]["Unit"];
						if (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
							$XLabel = $this->scaleFormat($Data["Series"][$Data["Abscissa"]]["Data"][$Index], $XAxisMode, $XAxisFormat, $XAxisUnit);
						} else {
							$XLabel = "";
						}

						if ($OverrideTitle != NULL) {
							$Description = $OverrideTitle;
						} elseif (count($SeriesName) == 1) {
							if (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) $Description = $Data["Series"][$SerieName]["Description"] . " - " . $XLabel;
						} elseif (isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index])) {
							$Description = $XLabel;
						}

						$Serie = [];
						if (isset($Data["Extended"]["Palette"][$Index])) {
							$Serie["R"] = $Data["Extended"]["Palette"][$Index]["R"];
							$Serie["G"] = $Data["Extended"]["Palette"][$Index]["G"];
							$Serie["B"] = $Data["Extended"]["Palette"][$Index]["B"];
							$Serie["Alpha"] = $Data["Extended"]["Palette"][$Index]["Alpha"];
						} else {
							$Serie["R"] = $Data["Series"][$SerieName]["Color"]["R"];
							$Serie["G"] = $Data["Series"][$SerieName]["Color"]["G"];
							$Serie["B"] = $Data["Series"][$SerieName]["Color"]["B"];
							$Serie["Alpha"] = $Data["Series"][$SerieName]["Color"]["Alpha"];
						}

						if (count($SeriesName) == 1 && isset($Data["Series"][$SerieName]["XOffset"])) {
							$SerieOffset = $Data["Series"][$SerieName]["XOffset"];
						} else {
							$SerieOffset = 0;
						}

						$Value = $Data["Series"][$SerieName]["Data"][$Index];
						if ($ForceLabels != NULL) {
							$Caption = isset($ForceLabels[$Key]) ? $ForceLabels[$Key] : "Not set";
						} else {
							$Caption = $this->scaleFormat($Value, $AxisMode, $AxisFormat, $AxisUnit);
						}

						if ($Value == VOID) {
							$Value = "NaN";
						}

						if ($this->LastChartLayout == CHART_LAST_LAYOUT_STACKED) {
							$LookFor = ($Value >= 0) ? "+" : "-";
							$Value = 0;

							foreach($Data["Series"] as $Name => $SerieLookup) {
								if ($SerieLookup["isDrawable"] == TRUE && $Name != $Data["Abscissa"]) {
									if (isset($Data["Series"][$Name]["Data"][$Index]) && $Data["Series"][$Name]["Data"][$Index] != VOID) {
										if ($Data["Series"][$Name]["Data"][$Index] >= 0 && $LookFor == "+") {
											$Value = $Value + $Data["Series"][$Name]["Data"][$Index];
										}

										if ($Data["Series"][$Name]["Data"][$Index] < 0 && $LookFor == "-") {
											$Value = $Value - $Data["Series"][$Name]["Data"][$Index];
										}

										if ($Name == $SerieName) {
											break;
										}
									}
								}
							}
						}

						$X = floor($this->scaleComputeY($Value,["AxisID" => $AxisID]));
						$Y = floor($this->GraphAreaY1 + $XMargin + $Index * $XStep + $SerieOffset);
						if ($X < $MinX) {
							$MinX = $X;
						}

						if ($DrawPoint == LABEL_POINT_CIRCLE) {
							$this->drawFilledCircle($X, $Y, 3, ["R" => 255,"G" => 255,"B" => 255,"BorderR" => 0,"BorderG" => 0,"BorderB" => 0]);
						} elseif ($DrawPoint == LABEL_POINT_BOX) {
							$this->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["R" => 255,	"G" => 255,"B" => 255,"BorderR" => 0,"BorderG" => 0,"BorderB" => 0]);
						}

						$Series[] = ["Format" => $Serie,"Caption" => $Caption];
					}
				}

				$this->drawLabelBox($MinX, $Y - 3, $Description, $Series, $Format);
			}
		}
	}

	/* Draw a label box */
	function drawLabelBox($X, $Y, $Title, $Captions, array $Format = [])
	{

		$NoTitle = NULL;
		$BoxWidth = 50;
		$DrawSerieColor = TRUE;
		$SerieR = NULL;
		$SerieG = NULL;
		$SerieB = NULL;
		$SerieAlpha = NULL;
		$SerieBoxSize = 6;
		$SerieBoxSpacing = 4;
		$VerticalMargin = 10;
		$HorizontalMargin = 8;
		$R = isset($Format["R"]) ? $Format["R"] : $this->FontColorR;
		$G = isset($Format["G"]) ? $Format["G"] : $this->FontColorG;
		$B = isset($Format["B"]) ? $Format["B"] : $this->FontColorB;
		$Alpha = $this->FontColorA;
		$FontName = $this->FontName;
		$FontSize = $this->FontSize;
		$TitleMode = LABEL_TITLE_NOBACKGROUND;
		$TitleR = $R;
		$TitleG = $G;
		$TitleB = $B;
		$TitleAlpha = 100;
		$TitleBackgroundR = 0;
		$TitleBackgroundG = 0;
		$TitleBackgroundB = 0;
		$TitleBackgroundAlpha = 100;
		$GradientStartR = 255;
		$GradientStartG = 255;
		$GradientStartB = 255;
		$GradientEndR = 220;
		$GradientEndG = 220;
		$GradientEndB = 220;
		$BoxAlpha = 100;

		/* Override defaults */
		extract($Format);

		if (!$DrawSerieColor) {
			$SerieBoxSize = 0;
			$SerieBoxSpacing = 0;
		}

		$TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, 0, $Title);
		$TitleWidth = ($TxtPos[1]["X"] - $TxtPos[0]["X"]) + $VerticalMargin * 2;
		$TitleHeight = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]);
		if ($NoTitle) {
			$TitleWidth = 0;
			$TitleHeight = 0;
		}

		$CaptionWidth = 0;
		$CaptionHeight = - $HorizontalMargin;
		if (isset($Captions["Caption"])){ # Momchil TODO No idea why I have to do that
				$TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, 0, $Captions["Caption"]);
				$CaptionWidth = max($CaptionWidth, ($TxtPos[1]["X"] - $TxtPos[0]["X"]) + $VerticalMargin * 2);
				$CaptionHeight = $CaptionHeight + max(($TxtPos[0]["Y"] - $TxtPos[2]["Y"]), ($SerieBoxSize + 2)) + $HorizontalMargin;
		} else {
			foreach($Captions as $Caption) {
				$TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, 0, $Caption["Caption"]);
				$CaptionWidth = max($CaptionWidth, ($TxtPos[1]["X"] - $TxtPos[0]["X"]) + $VerticalMargin * 2);
				$CaptionHeight = $CaptionHeight + max(($TxtPos[0]["Y"] - $TxtPos[2]["Y"]), ($SerieBoxSize + 2)) + $HorizontalMargin;
			}
		}

		if ($CaptionHeight <= 5) {
			$CaptionHeight = $CaptionHeight + $HorizontalMargin / 2;
		}

		if ($DrawSerieColor) {
			$CaptionWidth = $CaptionWidth + $SerieBoxSize + $SerieBoxSpacing;
		}

		$BoxWidth = max($BoxWidth, $TitleWidth, $CaptionWidth);
		$XMin = $X - 5 - floor(($BoxWidth - 10) / 2);
		$XMax = $X + 5 + floor(($BoxWidth - 10) / 2);
		$RestoreShadow = $this->Shadow;
		$ShadowX = $this->ShadowX; # Local var just for speed
		if ($this->Shadow == TRUE) {
			$this->Shadow = FALSE;
			$Poly = [$X + $ShadowX, $Y + $ShadowX, $X + 5 + $ShadowX, $Y - 5 + $ShadowX, $XMax + $ShadowX, $Y - 5 + $ShadowX];
			if ($NoTitle) {
				$Poly[] = $XMax + $ShadowX;
				$Poly[] = $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2 + $ShadowX;
				$Poly[] = $XMin + $ShadowX;
				$Poly[] = $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2 + $ShadowX;
			} else {
				$Poly[] = $XMax + $ShadowX;
				$Poly[] = $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3 + $ShadowX;
				$Poly[] = $XMin + $ShadowX;
				$Poly[] = $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3 + $ShadowX;
			}

			$Poly[] = $XMin +  $ShadowX;
			$Poly[] = $Y - 5 + $ShadowX;
			$Poly[] = $X - 5 + $ShadowX;
			$Poly[] = $Y - 5 + $ShadowX;
			$this->drawPolygon($Poly, ["R" => $this->ShadowR,"G" => $this->ShadowG,"B" => $this->ShadowB,"Alpha" => $this->Shadowa]);
		}

		/* Draw the background */
		$GradientSettings = ["StartR" => $GradientStartR,"StartG" => $GradientStartG,"StartB" => $GradientStartB,"EndR" => $GradientEndR,"EndG" => $GradientEndG,"EndB" => $GradientEndB,"Alpha" => $BoxAlpha];
		if ($NoTitle) {
			$this->drawGradientArea($XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax, $Y - 6, DIRECTION_VERTICAL, $GradientSettings);
		} else {
			$this->drawGradientArea($XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 6, DIRECTION_VERTICAL, $GradientSettings);
		}

		$Poly = [$X, $Y, $X - 5, $Y - 5, $X + 5, $Y - 5];
		$this->drawPolygon($Poly, ["R" => $GradientEndR,"G" => $GradientEndG,"B" => $GradientEndB,"Alpha" => $BoxAlpha,"NoBorder" => TRUE]);
		/* Outer border */
		$OuterBorderColor = $this->allocateColor(100, 100, 100, $BoxAlpha);
		imageline($this->Picture, $XMin, $Y - 5, $X - 5, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $X, $Y, $X - 5, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $X, $Y, $X + 5, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $X + 5, $Y - 5, $XMax, $Y - 5, $OuterBorderColor);
		if ($NoTitle) {
			imageline($this->Picture, $XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMin, $Y - 5, $OuterBorderColor);
			imageline($this->Picture, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax, $Y - 5, $OuterBorderColor);
			imageline($this->Picture, $XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $OuterBorderColor);
		} else {
			imageline($this->Picture, $XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMin, $Y - 5, $OuterBorderColor);
			imageline($this->Picture, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 5, $OuterBorderColor);
			imageline($this->Picture, $XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $OuterBorderColor);
		}

		/* Inner border */
		$InnerBorderColor = $this->allocateColor(255, 255, 255, $BoxAlpha);
		imageline($this->Picture, $XMin + 1, $Y - 6, $X - 5, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $X, $Y - 1, $X - 5, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $X, $Y - 1, $X + 5, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $X + 5, $Y - 6, $XMax - 1, $Y - 6, $InnerBorderColor);
		if ($NoTitle) {
			imageline($this->Picture, $XMin + 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMin + 1, $Y - 6, $InnerBorderColor);
			imageline($this->Picture, $XMax - 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax - 1, $Y - 6, $InnerBorderColor);
			imageline($this->Picture, $XMin + 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $XMax - 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 2, $InnerBorderColor);
		} else {
			imageline($this->Picture, $XMin + 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMin + 1, $Y - 6, $InnerBorderColor);
			imageline($this->Picture, $XMax - 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax - 1, $Y - 6, $InnerBorderColor);
			imageline($this->Picture, $XMin + 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax - 1, $Y - 4 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $InnerBorderColor);
		}

		/* Draw the separator line */
		if ($TitleMode == LABEL_TITLE_NOBACKGROUND && !$NoTitle) {
			$YPos = $Y - 7 - $CaptionHeight - $HorizontalMargin - $HorizontalMargin / 2;
			$XMargin = $VerticalMargin / 2;
			$this->drawLine($XMin + $XMargin, $YPos + 1, $XMax - $XMargin, $YPos + 1, ["R" => $GradientEndR,"G" => $GradientEndG,"B" => $GradientEndB,"Alpha" => $BoxAlpha]);
			$this->drawLine($XMin + $XMargin, $YPos, $XMax - $XMargin, $YPos, ["R" => $GradientStartR,"G" => $GradientStartG,"B" => $GradientStartB,"Alpha" => $BoxAlpha]);
		} elseif ($TitleMode == LABEL_TITLE_BACKGROUND) {
			$this->drawFilledRectangle($XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin + $HorizontalMargin / 2, array(
				"R" => $TitleBackgroundR,
				"G" => $TitleBackgroundG,
				"B" => $TitleBackgroundB,
				"Alpha" => $BoxAlpha
			));
			imageline($this->Picture, $XMin + 1, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin + $HorizontalMargin / 2 + 1, $XMax - 1, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin + $HorizontalMargin / 2 + 1, $InnerBorderColor);
		}

		/* Write the description */
		if (!$NoTitle) {
			$this->drawText($XMin + $VerticalMargin, $Y - 7 - $CaptionHeight - $HorizontalMargin * 2, $Title, ["Align" => TEXT_ALIGN_BOTTOMLEFT,"R" => $TitleR,"G" => $TitleG,	"B" => $TitleB]);
		}

		/* Write the value */
		$YPos = $Y - 5 - $HorizontalMargin;
		$XPos = $XMin + $VerticalMargin + $SerieBoxSize + $SerieBoxSpacing;
		if (isset($Captions["Caption"])){ # Momchil TODO No idea why I have to do that (same thing on line 6782)
			$TxtPos = $this->getTextBox($XPos, $YPos, $FontName, $FontSize, 0, $Captions["Caption"]);
			$CaptionHeight = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]);
			/* Write the serie color if needed */
			if ($DrawSerieColor) {
				$BoxSettings = ["R" => $Captions["Format"]["R"],"G" => $Captions["Format"]["G"],"B" => $Captions["Format"]["B"],"Alpha" => $Captions["Format"]["Alpha"],"BorderR" => 0,"BorderG" => 0,"BorderB" => 0];
				$this->drawFilledRectangle($XMin + $VerticalMargin, $YPos - $SerieBoxSize, $XMin + $VerticalMargin + $SerieBoxSize, $YPos, $BoxSettings);
			}

			$this->drawText($XPos, $YPos, $Captions["Caption"], ["Align" => TEXT_ALIGN_BOTTOMLEFT]);
			$YPos = $YPos - $CaptionHeight - $HorizontalMargin;
		} else {
			foreach($Captions as $Key => $Caption) {
				$TxtPos = $this->getTextBox($XPos, $YPos, $FontName, $FontSize, 0, $Caption["Caption"]);
				$CaptionHeight = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]);
				/* Write the serie color if needed */
				if ($DrawSerieColor) {
					$BoxSettings = ["R" => $Caption["Format"]["R"],"G" => $Caption["Format"]["G"],"B" => $Caption["Format"]["B"],"Alpha" => $Caption["Format"]["Alpha"],"BorderR" => 0,"BorderG" => 0,"BorderB" => 0];
					$this->drawFilledRectangle($XMin + $VerticalMargin, $YPos - $SerieBoxSize, $XMin + $VerticalMargin + $SerieBoxSize, $YPos, $BoxSettings);
				}

				$this->drawText($XPos, $YPos, $Caption["Caption"], ["Align" => TEXT_ALIGN_BOTTOMLEFT]);
				$YPos = $YPos - $CaptionHeight - $HorizontalMargin;
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Draw a basic shape */
	function drawShape($X, $Y, $Shape, $PlotSize, $PlotBorder, $BorderSize, $R, $G, $B, $Alpha, $BorderR, $BorderG, $BorderB, $BorderAlpha)
	{
		$RGB = ["R" => $R,"G" => $G,"B" => $B,"Alpha" => $Alpha];

		switch ($Shape){
			case SERIE_SHAPE_FILLEDCIRCLE:
				if ($PlotBorder) {
					$this->drawFilledCircle($X, $Y, $PlotSize + $BorderSize, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha]);
				}
				$this->drawFilledCircle($X, $Y, $PlotSize, $RGB);
				break;
			case SERIE_SHAPE_FILLEDSQUARE:
				if ($PlotBorder) {
					$this->drawFilledRectangle($X - $PlotSize - $BorderSize, $Y - $PlotSize - $BorderSize, $X + $PlotSize + $BorderSize, $Y + $PlotSize + $BorderSize, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha]);
				}
				$this->drawFilledRectangle($X - $PlotSize, $Y - $PlotSize, $X + $PlotSize, $Y + $PlotSize, $RGB);
				break;
			case SERIE_SHAPE_FILLEDTRIANGLE:
				if ($PlotBorder) {
					$Pos = [$X, $Y - $PlotSize - $BorderSize, $X - $PlotSize - $BorderSize, $Y + $PlotSize + $BorderSize, $X + $PlotSize + $BorderSize, $Y + $PlotSize + $BorderSize];
					$this->drawPolygon($Pos, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha]);
				}
				$Pos = [$X, $Y - $PlotSize, $X - $PlotSize, $Y + $PlotSize, $X + $PlotSize, $Y + $PlotSize];
				$this->drawPolygon($Pos, $RGB);
				break;
			case SERIE_SHAPE_TRIANGLE:
				$this->drawLine($X, $Y - $PlotSize, $X - $PlotSize, $Y + $PlotSize, $RGB);
				$this->drawLine($X - $PlotSize, $Y + $PlotSize, $X + $PlotSize, $Y + $PlotSize, $RGB);
				$this->drawLine($X + $PlotSize, $Y + $PlotSize, $X, $Y - $PlotSize, $RGB);
				break;
			case SERIE_SHAPE_SQUARE:
				$this->drawRectangle($X - $PlotSize, $Y - $PlotSize, $X + $PlotSize, $Y + $PlotSize, $RGB);
				break;
			case SERIE_SHAPE_CIRCLE:
				$this->drawCircle($X, $Y, $PlotSize, $PlotSize, $RGB);
				break;
			case SERIE_SHAPE_DIAMOND:
				$Pos = [$X - $PlotSize, $Y, $X, $Y - $PlotSize, $X + $PlotSize, $Y, $X, $Y + $PlotSize];
				$this->drawPolygon($Pos, ["NoFill" => TRUE,"BorderR" => $R,"BorderG" => $G,"BorderB" => $B,"BorderAlpha" => $Alpha]);
				break;
			case SERIE_SHAPE_FILLEDDIAMOND:
				if ($PlotBorder) {
					$Pos = [$X - $PlotSize - $BorderSize, $Y, $X, $Y - $PlotSize - $BorderSize, $X + $PlotSize + $BorderSize, $Y, $X, $Y + $PlotSize + $BorderSize];
					$this->drawPolygon($Pos, ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha]);
				}
				$Pos = [$X - $PlotSize, $Y, $X, $Y - $PlotSize, $X + $PlotSize, $Y, $X, $Y + $PlotSize];
				$this->drawPolygon($Pos, $RGB);
				break;
		}
	}

	function drawPolygonChart($Points, array $Format = [])
	{
		$R = isset($Format["R"]) ? $Format["R"] : 0;
		$G = isset($Format["G"]) ? $Format["G"] : 0;
		$B = isset($Format["B"]) ? $Format["B"] : 0;
		$Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
		$Threshold = NULL;
		$NoFill = FALSE;
		$NoBorder = FALSE;
		$Surrounding = NULL;
		$BorderR = $R;
		$BorderG = $G;
		$BorderB = $B;
		$BorderAlpha = $Alpha / 2;

		extract($Format);

		if ($Surrounding != NULL) {
			$BorderR = $R + $Surrounding;
			$BorderG = $G + $Surrounding;
			$BorderB = $B + $Surrounding;
		}

		$RestoreShadow = $this->Shadow;
		$this->Shadow = FALSE;
		$AllIntegers = TRUE;
		for ($i = 0; $i <= count($Points) - 2; $i = $i + 2) {
			if ($this->getFirstDecimal($Points[$i + 1]) != 0) {
				$AllIntegers = FALSE;
			}
		}

		/* Convert polygon to segments */
		$Segments = [];
		for ($i = 2; $i <= count($Points) - 2; $i = $i + 2) {
			$Segments[] = ["X1" => $Points[$i - 2],"Y1" => $Points[$i - 1],"X2" => $Points[$i],"Y2" => $Points[$i + 1]];
		}

		$Segments[] = ["X1" => $Points[$i - 2],"Y1" => $Points[$i - 1],"X2" => $Points[0],"Y2" => $Points[1]];
		/* Simplify straight lines */
		$Result = [];
		$inHorizon = FALSE;
		$LastX = VOID;
		foreach($Segments as $Key => $Pos) {
			if ($Pos["Y1"] != $Pos["Y2"]) {
				if ($inHorizon) {
					$inHorizon = FALSE;
					$Result[] = ["X1" => $LastX,"Y1" => $Pos["Y1"],"X2" => $Pos["X1"],"Y2" => $Pos["Y1"]];
				}

				$Result[] = ["X1" => $Pos["X1"],"Y1" => $Pos["Y1"],"X2" => $Pos["X2"],"Y2" => $Pos["Y2"]];
			} else {
				if (!$inHorizon) {
					$inHorizon = TRUE;
					$LastX = $Pos["X1"];
				}
			}
		}

		$Segments = $Result;
		/* Do we have something to draw */

		if (count($Segments) == 0) {
			return (0);
		}

		/* For segments debugging purpose */

		// foreach($Segments as $Key => $Pos)
		// echo $Pos["X1"].",".$Pos["Y1"].",".$Pos["X2"].",".$Pos["Y2"]."\r\n";

		/* Find out the min & max Y boundaries */
		$MinY = OUT_OF_SIGHT;
		$MaxY = OUT_OF_SIGHT;
		foreach($Segments as $Key => $Coords) {
			if ($MinY == OUT_OF_SIGHT || $MinY > min($Coords["Y1"], $Coords["Y2"])) {
				$MinY = min($Coords["Y1"], $Coords["Y2"]);
			}

			if ($MaxY == OUT_OF_SIGHT || $MaxY < max($Coords["Y1"], $Coords["Y2"])) {
				$MaxY = max($Coords["Y1"], $Coords["Y2"]);
			}
		}

		$YStep = ($AllIntegers) ? 1 : .5;
		$MinY = floor($MinY);
		$MaxY = floor($MaxY);
		/* Scan each Y lines */
		$DefaultColor = $this->allocateColor($R, $G, $B, $Alpha);
		#$DebugLine = 0;
		$DebugColor = $this->allocateColor(255, 0, 0, 100);
		$MinY = floor($MinY);
		$MaxY = floor($MaxY);
		$YStep = 1;
		if (!$NoFill) {

			// if ( $DebugLine ) { $MinY = $DebugLine; $MaxY = $DebugLine; }

			for ($Y = $MinY; $Y <= $MaxY; $Y = $Y + $YStep) {
				$Intersections = [];
				$LastSlope = NULL;
				$RestoreLast = "-";
				foreach($Segments as $Key => $Coords) {
					$X1 = $Coords["X1"];
					$X2 = $Coords["X2"];
					$Y1 = $Coords["Y1"];
					$Y2 = $Coords["Y2"];
					if (min($Y1, $Y2) <= $Y && max($Y1, $Y2) >= $Y) {
						if ($Y1 == $Y2) {
							$X = $X1;
						} else {
							$X = $X1 + (($Y - $Y1) * $X2 - ($Y - $Y1) * $X1) / ($Y2 - $Y1);
						}

						$X = floor($X);
						if ($X2 == $X1) {
							$Slope = "!";
						} else {
							$SlopeC = ($Y2 - $Y1) / ($X2 - $X1);
							if ($SlopeC == 0) {
								$Slope = "=";
							} elseif ($SlopeC > 0) {
								$Slope = "+";
							} elseif ($SlopeC < 0) {
								$Slope = "-";
							}
						}

						if (!is_array($Intersections)) {
							$Intersections[] = $X;
						} elseif (!in_array($X, $Intersections)) {
							$Intersections[] = $X;
						} elseif (in_array($X, $Intersections)) {
							#if ($Y == $DebugLine) {
							#	echo $Slope . "/" . $LastSlope . "(" . $X . ") ";
							#}

							if ($Slope == "=" && $LastSlope == "-") {
								$Intersections[] = $X;
							}

							if ($Slope != $LastSlope && $LastSlope != "!" && $LastSlope != "=") {
								$Intersections[] = $X;
							}

							if ($Slope != $LastSlope && $LastSlope == "!" && $Slope == "+") {
								$Intersections[] = $X;
							}
						}

						if (is_array($Intersections) && in_array($X, $Intersections) && $LastSlope == "=" && ($Slope == "-")) {
							$Intersections[] = $X;
						}

						$LastSlope = $Slope;
					}
				}

				if ($RestoreLast != "-") {
					$Intersections[] = $RestoreLast;
					echo "@" . $Y . "\r\n";
				}

				if (is_array($Intersections)) {
					sort($Intersections);
					#if ($Y == $DebugLine) {
					#	print_r($Intersections);
					#}

					/* Remove NULL plots */
					$Result = [];
					for ($i = 0; $i <= count($Intersections) - 1; $i = $i + 2) {
						if (isset($Intersections[$i + 1])) {
							if ($Intersections[$i] != $Intersections[$i + 1]) {
								$Result[] = $Intersections[$i];
								$Result[] = $Intersections[$i + 1];
							}
						}
					}

					// if ( is_array($Result) )

					if (count($Result) > 0) {
						$Intersections = $Result;
						$LastX = OUT_OF_SIGHT;
						foreach($Intersections as $Key => $X) {
							if ($LastX == OUT_OF_SIGHT) {
								$LastX = $X;
							} elseif ($LastX != OUT_OF_SIGHT) {
								if ($this->getFirstDecimal($LastX) > 1) {
									$LastX++;
								}

								$Color = $DefaultColor;
								if ($Threshold != NULL) {
									foreach($Threshold as $Key => $Parameters) {
										if ($Y <= $Parameters["MinX"] && $Y >= $Parameters["MaxX"]) {
											$R = (isset($Parameters["R"])) ? $Parameters["R"] : 0;
											$G = (isset($Parameters["G"])) ? $Parameters["G"] : 0;
											$B = (isset($Parameters["B"])) ? $Parameters["B"] : 0;
											$Alpha = (isset($Parameters["Alpha"])) ? $Parameters["Alpha"] : 100;
											$Color = $this->allocateColor($R, $G, $B, $Alpha);
										}
									}
								}

								imageline($this->Picture, $LastX, $Y, $X, $Y, $Color);
								#if ($Y == $DebugLine) {
								#	imageline($this->Picture, $LastX, $Y, $X, $Y, $DebugColor);
								#}

								$LastX = OUT_OF_SIGHT;
							}
						}
					}
				}
			}
		} # No Fill

		/* Draw the polygon border, if required */
		if (!$NoBorder) {
			foreach($Segments as $Key => $Coords) {
				$this->drawLine($Coords["X1"], $Coords["Y1"], $Coords["X2"], $Coords["Y2"], ["R" => $BorderR,"G" => $BorderG,"B" => $BorderB,"Alpha" => $BorderAlpha,"Threshold" => $Threshold]);
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Return the abscissa margin */
	function getAbscissaMargin($Data)
	{
		foreach($Data["Axis"] as $AxisID => $Values) {
			if ($Values["Identity"] == AXIS_X) {
				return ($Values["Margin"]);
			}
		}

		return (0);
	}

	/* Convert a string to a single element array */
	function convertToArray($Value)
	{
		return (is_array($Value)) ? $Value : [$Value];
	}

}

?>