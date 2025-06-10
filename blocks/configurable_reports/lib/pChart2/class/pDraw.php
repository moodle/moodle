<?php
/*
pDraw - class extension with drawing methods

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

/* The GD extension is mandatory */
if (!function_exists("gd_info")) {
	echo "GD extension must be loaded. \r\n";
	exit();
}

use pChart\pException;
use pChart\pColor;
use pChart\pColorGradient;
use pChart\pData;

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
define("ALL", 69);
define("NONE", 31);
define("AUTO", 690000);
define("OUT_OF_SIGHT", PHP_INT_MIN);
/* Axis configuration */
define("AXIS_FORMAT_DEFAULT", 680001);
define("AXIS_FORMAT_TIME", 680002);
define("AXIS_FORMAT_DATE", 680003);
define("AXIS_FORMAT_METRIC", 680004);
define("AXIS_FORMAT_CURRENCY", 680005);
define("AXIS_FORMAT_TRAFFIC", 680006);
define("AXIS_FORMAT_CUSTOM", 680007);
/* Axis position */
define("AXIS_POSITION_LEFT", 681001);
define("AXIS_POSITION_RIGHT", 681002);
define("AXIS_POSITION_TOP", 681001);
define("AXIS_POSITION_BOTTOM", 681002);
/* Families of data points */
define("SERIE_SHAPE_FILLEDCIRCLE", 681011);
define("SERIE_SHAPE_FILLEDTRIANGLE", 681012);
define("SERIE_SHAPE_FILLEDSQUARE", 681013);
define("SERIE_SHAPE_FILLEDDIAMOND", 681017);
define("SERIE_SHAPE_CIRCLE", 681014);
define("SERIE_SHAPE_TRIANGLE", 681015);
define("SERIE_SHAPE_SQUARE", 681016);
define("SERIE_SHAPE_DIAMOND", 681018);
/* Axis position */
define("AXIS_X", 682001);
define("AXIS_Y", 682002);
/* Replacement to the PHP NULL keyword */
define("VOID", 0.123456789);

/* 2D barcode libs */
define("BARCODES_ENGINE_AZTEC", 'Aztec');
define("BARCODES_ENGINE_QRCODE", 'QRCode');
define("BARCODES_ENGINE_PDF417", 'PDF417');
define("BARCODES_ENGINE_DMTX", 'DMTX');

/* Linear barcode libs */
define("BARCODES_ENGINE_UPC", 'UPC');
define("BARCODES_ENGINE_CODE11", 'Code11');
define("BARCODES_ENGINE_CODE39", 'Code39');
define("BARCODES_ENGINE_CODE93", 'Code93');
define("BARCODES_ENGINE_CODE128", 'Code128');
define("BARCODES_ENGINE_CODABAR", 'Codabar');
define("BARCODES_ENGINE_ITF", 'ITF');
define("BARCODES_ENGINE_PHARMA", 'Pharmacode');
define("BARCODES_ENGINE_POSTNET", 'Postnet');
define("BARCODES_ENGINE_MSI", 'MSI');
define("BARCODES_ENGINE_RMS4CC", 'Rms4cc');
define("BARCODES_ENGINE_EANEXT", 'Eanext');
define("BARCODES_ENGINE_B2OF5", 'b2of5');
define("BARCODES_ENGINE_IMB", 'IMB');

class pDraw
{
	/* GD picture object */
	protected $Picture;
	/* Image settings, size, quality, .. */
	private $XSize = 0; // Width of the picture
	private $YSize = 0; // Height of the picture
	private $Antialias = TRUE; // Turn anti alias on or off
	private $AntialiasQuality = 0; // Quality of the anti aliasing implementation (0-1)
	private $TransparentBackground = FALSE;
	/* Graph area settings */
	private $GraphAreaX1 = 0; // Graph area X origin
	private $GraphAreaY1 = 0; // Graph area Y origin
	private $GraphAreaX2 = 0; // Graph area bottom right X position
	private $GraphAreaY2 = 0; // Graph area bottom right Y position
	private $GraphAreaXdiff = 0; // $X2 - $X1
	private $GraphAreaYdiff = 0; // $Y2 - $Y1
	/* Font properties */
	private $FontName = NULL; // Default font file
	private $FontSize = 12; // Default font size
	private $FontColor; // Default color settings
	/* Shadow properties */
	private $Shadow = FALSE; // Turn shadows on or off
	private $ShadowX = 0; // X Offset of the shadow
	private $ShadowY = 0; // Y Offset of the shadow
	private $ShadowColor;
	private $ShadowColorAlloc;

	/* Data Set - read only would have been nice to have */
	public $myData;

	/* Class constructor */
	function __construct(int $XSize, int $YSize, bool $TransparentBackground = FALSE)
	{
		$this->myData = new pData();

		$this->XSize = $XSize;
		$this->YSize = $YSize;

		/* Momchil: I will leave it here in case someone needs it
		$memory_limit = ini_get("memory_limit");
		if (intval($memory_limit) * 1024 * 1024 < $XSize * $YSize * 3 * 1.7){ # Momchil: for black & white gifs -> use 1 and not 3
			echo "Memory limit: ".$memory_limit." Mb ".PHP_EOL;
			echo "Estimated required: ".round(($XSize * $YSize * 3 * 1.7)/(1024 * 1024), 3)." Mb ".PHP_EOL;
			$this->Picture = imagecreatetruecolor(1, 1);
			throw pException::InvalidDimentions("Can not allocate enough memory for an image that big! Check your PHP memory_limit configuration option.");
		}
		*/

		$this->Picture = imagecreatetruecolor($XSize, $YSize);
		if ($this->Picture == FALSE){
			throw pException::InvalidDimentions("Failed to create true color image!");
		}

		$this->TransparentBackground = $TransparentBackground;
		if ($TransparentBackground) {
			imagealphablending($this->Picture, FALSE); #  TRUE by default on True color images
			imagefilledrectangle($this->Picture, 0, 0, $XSize, $YSize, imagecolorallocatealpha($this->Picture, 255, 255, 255, 127));
			imagealphablending($this->Picture, TRUE);
			imagesavealpha($this->Picture, TRUE);
		} else {
			# Momchil: $this->allocateColor([255,255,255,100]); sets alpha at 1.27 which is not completely transparent
			imagefilledrectangle($this->Picture, 0, 0, $XSize, $YSize, imagecolorallocatealpha($this->Picture, 255, 255, 255, 0));
		}

		/* default shadow color */
		$this->ShadowColor = new pColor(0,0,0,10);
		$this->ShadowColorAlloc = $this->allocateColor([0,0,0,10]);

		/* default font color */
		$this->FontColor = new pColor(255);
	}

	function __destruct()
	{
		if (is_resource($this->Picture)){
			imagedestroy($this->Picture);
		}
	}

	/* Fix box coordinates */
	private function fixBoxCoordinates($Xa, $Ya, $Xb, $Yb)
	{
		return [
			min($Xa, $Xb),
			min($Ya, $Yb),
			max($Xa, $Xb),
			max($Ya, $Yb)
		];
	}

	/* Draw a polygon */
	public function drawPolygon(array $Points, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$NoFill = isset($Format["NoFill"]) ? $Format["NoFill"] : FALSE;
		$NoBorder = isset($Format["NoBorder"]) ? $Format["NoBorder"] : FALSE;
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : $Color->newOne()->AlphaSlash(2);
		if (isset($Format["Surrounding"])){
			$BorderColor->RGBChange($Format["Surrounding"]);
		}

		/* Calling the imagefilledpolygon() function over the $Points array used to round it */

		$PointCount = count($Points);

		$RestoreShadow = $this->Shadow;

		if (!$NoFill) {
			if ($this->Shadow) {
				$this->Shadow = FALSE;
				$Shadow = []; 
				for ($i = 0; $i <= $PointCount - 1; $i += 2) {
					$Shadow[] = $Points[$i] + $this->ShadowX;
					$Shadow[] = $Points[$i + 1] + $this->ShadowY;
				}

				$this->drawPolygon($Shadow, ["Color" => $this->ShadowColor,"NoBorder" => TRUE]);
			}

			if ($PointCount >= 6) {
				imagefilledpolygon($this->Picture, $Points, $PointCount / 2, $this->allocateColor($Color->get()));
			}
		}

		if (!$NoBorder) {

			$BorderSettings = ["Color" => ($NoFill) ? $Color : $BorderColor];

			for ($i = 0; $i <= $PointCount - 1; $i += 2) {
				if (isset($Points[$i + 2])) {
					if (!($Points[$i] == $Points[$i + 2] && $Points[$i + 1] == $Points[$i + 3])){
						$this->drawLine($Points[$i], $Points[$i + 1], $Points[$i + 2], $Points[$i + 3], $BorderSettings);
					}
				} else {
					if (!($Points[$i] == $Points[0] && $Points[$i + 1] == $Points[1])){
						$this->drawLine($Points[$i], $Points[$i + 1], $Points[0], $Points[1], $BorderSettings);
					}
				}
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Apply AALias correction to the rounded box boundaries */
	private function offsetCorrection($Value, $Mode) # UNUSED
	{
		$Value = round($Value, 1);

		if ($Value == 0 && $Mode == 1) {
			 $ret = .9;
		} elseif ($Value == 0) {
			 $ret = 0;
		} else {
			$matrix = [
				1 => [1 => .9,.1 => .9,.2 => .8,.3 => .8,.4 => .7,.5 => .5,.6 => .8,.7 => .7,.8 => .6,.9 => .9],
				2 => [1 => .9,.1 => .1,.2 => .2,.3 => .3,.4 => .4,.5 => .5,.6 => .8,.7 => .7,.8 => .8,.9 => .9],
				3 => [1 => .9,.1 => .1,.2 => .2,.3 => .3,.4 => .4,.5 => .9,.6 => .6,.7 => .7,.8 => .4,.9 => .5],
				4 => [1 => -1,.1 => .1,.2 => .2,.3 => .3,.4 => .1,.5 => -.1,.6 => .8,.7 => .1,.8 => .1,.9 => .1]
			];
			$ret = $matrix[$Mode][$Value];
		}

		return $ret;
	}

	/* Draw a rectangle with rounded corners */
	public function drawRoundedRectangle($X1, $Y1, $X2, $Y2, $Radius, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);

		list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
		($X2 - $X1 < $Radius) AND $Radius = floor(($X2 - $X1) / 2);
		($Y2 - $Y1 < $Radius) AND $Radius = floor(($Y2 - $Y1) / 2);
		$Options = ["Color" => $Color,"NoBorder" => TRUE];

		if ($Radius <= 0) {
			$this->drawRectangle($X1, $Y1, $X2, $Y2, $Options);
			return;
		}

		if ($this->Antialias) {
			$this->drawLine($X1 + $Radius, $Y1, $X2 - $Radius, $Y1, $Options);
			if (($Y1 + $Radius) != ($Y2 - $Radius)) {
				$this->drawLine($X2, $Y1 + $Radius, $X2, $Y2 - $Radius, $Options);
			}
			$this->drawLine($X2 - $Radius, $Y2, $X1 + $Radius, $Y2, $Options);
			if (($Y1 + $Radius) != ($Y2 - $Radius)) {
				$this->drawLine($X1, $Y1 + $Radius, $X1, $Y2 - $Radius, $Options);
			}
		} else {
			$AllocatedColor = $this->allocateColor($Color->get());
			imageline($this->Picture, $X1 + $Radius, $Y1, $X2 - $Radius, $Y1, $AllocatedColor);
			imageline($this->Picture, $X2, $Y1 + $Radius, $X2, $Y2 - $Radius, $AllocatedColor);
			imageline($this->Picture, $X2 - $Radius, $Y2, $X1 + $Radius, $Y2, $AllocatedColor);
			imageline($this->Picture, $X1, $Y1 + $Radius, $X1, $Y2 - $Radius, $AllocatedColor);
		}

		$Step = rad2deg(1/$Radius);

		for ($i = 0; $i <= 90; $i += $Step) {

			$cos1 = cos(deg2rad($i + 180)) * $Radius;
			$sin1 = sin(deg2rad($i + 180)) * $Radius;
			$cos2 = cos(deg2rad($i + 90)) * $Radius;
			$sin2 = sin(deg2rad($i + 90)) * $Radius;

			$X = $cos1 + $X1 + $Radius;
			$Y = $sin1 + $Y1 + $Radius;
			$this->drawAntialiasPixel($X, $Y, $Color);
			$X = $cos2 + $X1 + $Radius;
			$Y = $sin2 + $Y2 - $Radius;
			$this->drawAntialiasPixel($X, $Y, $Color);
			$X = -$cos1 + $X2 - $Radius;
			$Y = -$sin1 + $Y2 - $Radius;
			$this->drawAntialiasPixel($X, $Y, $Color);
			$X = -$cos2 + $X2 - $Radius;
			$Y = -$sin2 + $Y1 + $Radius;
			$this->drawAntialiasPixel($X, $Y, $Color);
		}

	}

	/* Draw a rectangle with rounded corners */
	public function drawRoundedFilledRectangle($X1, $Y1, $X2, $Y2, $Radius, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : $Color->newOne();
		if (isset($Format["Surrounding"])){
			$BorderColor->RGBChange($Format["Surrounding"]);
		}

		/* Temporary fix for AA issue */
		$Y1 = floor($Y1);
		$Y2 = floor($Y2);
		$X1 = floor($X1);
		$X2 = floor($X2);

		list($X1, $Y1, $X2, $Y2) = $this->fixBoxCoordinates($X1, $Y1, $X2, $Y2);
		if ($X2 - $X1 < $Radius * 2) {
			$Radius = floor(($X2 - $X1) / 4);
		}

		if ($Y2 - $Y1 < $Radius * 2) {
			$Radius = floor(($Y2 - $Y1) / 4);
		}

		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawRoundedFilledRectangle($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, $Radius, ["Color" => $this->ShadowColor]);
		}

		$Format = ["Color" => $Color,"NoBorder" => TRUE];
		if ($Radius <= 0) {
			$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Format);
			return;
		}

		$YTop = $Y1 + $Radius;
		$YBottom = $Y2 - $Radius;
		$Step = rad2deg(1/$Radius);
		$Positions = [];
		$Radius--;
		$MinY = 0;
		$MaxY = 0;
		for ($i = 0; $i <= 90; $i += $Step) {
			$cos = cos(deg2rad($i + 180)) * $Radius;
			$Xp1 = $cos + $X1 + $Radius;
			$Xp2 = -$cos + $X2 - $Radius;
			$Yp = floor(sin(deg2rad($i + 180)) * $Radius + $YTop);
			($MinY == 0 || $Yp > $MinY) AND $MinY = $Yp;
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

			$cos = cos(deg2rad($i + 90)) * $Radius;
			$Xp1 = $cos + $X1 + $Radius;
			$Xp2 = -$cos + $X2 - $Radius;
			$Yp = floor(sin(deg2rad($i + 90)) * $Radius + $YBottom);
			($MaxY == 0 || $Yp < $MaxY) AND $MaxY = $Yp;
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

		$ColorAlloc = $this->allocateColor($Color->get());
		foreach($Positions as $Yp => $Bounds) {
			$X1 = $Bounds["X1"];
			$X1Dec = $this->getFirstDecimal($X1);
			if ($X1Dec != 0) {
				$X1 = ceil($X1);
			}

			$X2 = $Bounds["X2"];
			$X2Dec = $this->getFirstDecimal($X2);
			if ($X2Dec != 0) {
				$X2 = floor($X2) - 1;
			}

			imageline($this->Picture, intval($X1), intval($Yp), intval($X2), intval($Yp), $ColorAlloc);
		}

		$this->drawFilledRectangle($X1, $MinY + 1, floor($X2), $MaxY - 1, $Format);
		$Radius++;
		$this->drawRoundedRectangle($X1, $Y1, $X2 + 1, $Y2 - 1, $Radius, ["Color" => $BorderColor]);
		$this->Shadow = $RestoreShadow;
	}

	/* Draw a rectangle */
	public function drawRectangle($X1, $Y1, $X2, $Y2, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$NoAngle = isset($Format["NoAngle"]) ? $Format["NoAngle"] : FALSE;

		($X1 > $X2) AND list($X1, $X2) = [$X2,$X1];
		($Y1 > $Y2) AND list($Y1, $Y2) = [$Y2,$Y1];

		$Format = ["Color" => $Color, "Ticks" => $Ticks];
		if ($this->Antialias) {
			if ($NoAngle) {
				$this->drawLine($X1 + 1, $Y1, $X2 - 1, $Y1, $Format);
				$this->drawLine($X2, $Y1 + 1, $X2, $Y2 - 1, $Format);
				$this->drawLine($X2 - 1, $Y2, $X1 + 1, $Y2, $Format);
				$this->drawLine($X1, $Y1 + 1, $X1, $Y2 - 1, $Format);
			} else {
				$this->drawLine($X1 + 1, $Y1, $X2 - 1, $Y1, $Format);
				$this->drawLine($X2, $Y1, $X2, $Y2, $Format);
				$this->drawLine($X2 - 1, $Y2, $X1 + 1, $Y2, $Format);
				$this->drawLine($X1, $Y1, $X1, $Y2, $Format);
			}
		} else {
			imagerectangle($this->Picture, intval($X1), intval($Y1), intval($X2), intval($Y2), $this->allocateColor($Color->get()));
		}
	}

	/* Draw a filled rectangle */
	public function drawFilledRectangle($X1, $Y1, $X2, $Y2, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : NULL;
		if (isset($Format["Surrounding"])){
			$BorderColor = $Color->newOne()->RGBChange($Format["Surrounding"]);
		}
		$NoBorder = isset($Format["NoBorder"]) ? $Format["NoBorder"] : FALSE;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$NoAngle = isset($Format["NoAngle"]) ? $Format["NoAngle"] : FALSE;
		$Dash = isset($Format["Dash"]) ? $Format["Dash"] : FALSE;
		$DashStep = isset($Format["DashStep"]) ? $Format["DashStep"] : 4;
		$DashColor = isset($Format["DashColor"]) ? $Format["DashColor"] : new pColor(0,0,0,$Color->AlphaGet());

		($X1 > $X2) AND list($X1, $X2) = [$X2,$X1];
		($Y1 > $Y2) AND list($Y1, $Y2) = [$Y2,$Y1];

		$X1c = intval(ceil($X1));
		$Y1c = intval(ceil($Y1));
		$X2f = intval(floor($X2));
		$Y2f = intval(floor($Y2));

		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawFilledRectangle($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, ["Color" => $this->ShadowColor,"Ticks" => $Ticks,"NoAngle" => $NoAngle]);
		}

		$ColorA = $Color->get();
		$ColorAlloc = $this->allocateColor($ColorA);

		if ($NoAngle) {
			imagefilledrectangle($this->Picture, $X1c + 1, $Y1c, $X2f - 1, $Y2f, $ColorAlloc);
			imageline($this->Picture, $X1c, $Y1c + 1, $X1c, $Y2f - 1, $ColorAlloc);
			imageline($this->Picture, $X2f, $Y1c + 1, $X2f, $Y2f - 1, $ColorAlloc);
		} else {
			imagefilledrectangle($this->Picture, $X1c, $Y1c, $X2f, $Y2f, $ColorAlloc);
		}

		if ($Dash) {
			if (!is_null($BorderColor)) {
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

			$Y = $iY1 - $DashStep;
			for ($X = $iX1; $X <= $iX2 + ($iY2 - $iY1); $X = $X + $DashStep) {
				$Y += $DashStep;
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

				imageline($this->Picture, $Xa, $Ya, $Xb, $Yb, $this->allocateColor($DashColor->get()));
			}
		}

		if ($this->Antialias && !$NoBorder) {

			$defaultAlpha = $ColorA[3];

			if ($X1 < $X1c) {
				$ColorA[3] = $defaultAlpha * ($X1c - $X1);
				imageline($this->Picture, $X1c - 1, $Y1c, $X1c - 1, $Y2f, $this->allocateColor($ColorA));
			}

			if ($Y1 < $Y1c) {
				$ColorA[3] = $defaultAlpha * ($Y1c - $Y1);
				imageline($this->Picture, $X1c, $Y1c - 1, $X2f, $Y1c - 1, $this->allocateColor($ColorA));
			}

			if ($X2 > $X2f) {
				$ColorA[3] = $defaultAlpha * (.5 - ($Y2 - $Y2f));
				imageline($this->Picture, $X2f + 1, $Y1c, $X2f + 1, $Y2f, $this->allocateColor($ColorA));
			}

			if ($Y2 > $Y2f) {
				$ColorA[3] = $defaultAlpha * (.5 - ($Y2 - $Y2f));
				imageline($this->Picture, $X1c, $Y2f + 1, $X2f, $Y2f + 1, $this->allocateColor($ColorA));
			}
		}

		if (!is_null($BorderColor)) {
			$this->drawRectangle($X1, $Y1, $X2, $Y2, ["Color" => $BorderColor, "Ticks" => $Ticks,"NoAngle" => $NoAngle]);
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Draw a rectangular marker of the specified size */
	public function drawRectangleMarker($X, $Y, array $Format = [])
	{
		$Size = isset($Format["Size"]) ? $Format["Size"] : 4;
		$HalfSize = floor($Size / 2);
		$this->drawFilledRectangle($X - $HalfSize, $Y - $HalfSize, $X + $HalfSize, $Y + $HalfSize, $Format);
	}

	/* Drawn a spline based on the bezier function */
	public function drawSpline(array $Coordinates, array $Format = [])
	{
		$NoDraw = isset($Format["NoDraw"]) ? $Format["NoDraw"] : FALSE;
		$Force  = isset($Format["Force"])  ? $Format["Force"]  : 30;
		$Forces = isset($Format["Forces"]) ? $Format["Forces"] : [];

		$Result = [];
		$Count = count($Coordinates)-1;

		for ($i = 1; $i <= $Count; $i++) {
			$X1 = $Coordinates[$i - 1][0];
			$Y1 = $Coordinates[$i - 1][1];
			$X2 = $Coordinates[$i][0];
			$Y2 = $Coordinates[$i][1];
			if (!empty($Forces)) { # Momchil: used in Scatter
				$Force = $Forces[$i];
			}

			/* First segment */
			if ($i == 1) {
				$Xv1 = $X1;
				$Yv1 = $Y1;
			} else {
				$Angle1 = $this->getAngle($XLast, $YLast, $X1, $Y1);
				$Angle2 = $this->getAngle($X1, $Y1, $X2, $Y2);
				$XOff = cos(deg2rad($Angle2)) * $Force + $X1;
				$YOff = sin(deg2rad($Angle2)) * $Force + $Y1;
				$Xv1 = cos(deg2rad($Angle1)) * $Force + $XOff;
				$Yv1 = sin(deg2rad($Angle1)) * $Force + $YOff;
			}

			/* Last segment */
			if ($i == $Count) {
				$Xv2 = $X2;
				$Yv2 = $Y2;
			} else {
				# Momchil: it is possible to save some calcs here
				# $Angle2 is already defined if not 0,1,Last member
				# cos(($Angle2 + 180) * M_PI / 180) is negated cos($Angle2 * M_PI / 180) (or at least close enough)
				# Not worth the code complexity (very few calls)
				$Angle1 = $this->getAngle($X2, $Y2, $Coordinates[$i + 1][0], $Coordinates[$i + 1][1]);
				$Angle2 = $this->getAngle($X1, $Y1, $X2, $Y2);
				$XOff = cos(deg2rad($Angle2 + 180)) * $Force + $X2;
				$YOff = sin(deg2rad($Angle2 + 180)) * $Force + $Y2;
				$Xv2 = cos(deg2rad($Angle1 + 180)) * $Force + $XOff;
				$Yv2 = sin(deg2rad($Angle1 + 180)) * $Force + $YOff;
			}

			$Path = $this->drawBezier($X1, $Y1, $X2, $Y2, $Xv1, $Yv1, $Xv2, $Yv2, $Format);
			if ($NoDraw) {
				$Result[] = $Path;
			}

			$XLast = $X1;
			$YLast = $Y1;
		}

		return $Result;
	}

	/* Draw a bezier curve with two controls points */
	public function drawBezier($X1, $Y1, $X2, $Y2, $Xv1, $Yv1, $Xv2, $Yv2, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$Segments = isset($Format["Segments"]) ? $Format["Segments"] : NULL;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$NoDraw = isset($Format["NoDraw"]) ? $Format["NoDraw"] : FALSE;
		$Weight = isset($Format["Weight"]) ? $Format["Weight"] : NULL;
		$ShowControl = isset($Format["ShowControl"]) ? $Format["ShowControl"] : FALSE;
		$DrawArrow = isset($Format["DrawArrow"]) ? $Format["DrawArrow"] : FALSE;
		$ArrowSize = isset($Format["ArrowSize"]) ? $Format["ArrowSize"] : 10;
		$ArrowRatio = isset($Format["ArrowRatio"]) ? $Format["ArrowRatio"] : .5;
		$ArrowTwoHeads = isset($Format["ArrowTwoHeads"]) ? $Format["ArrowTwoHeads"] : FALSE;

		if (is_null($Segments)) {
			$Length = hypot(($X2 - $X1),($Y2 - $Y1));
			$Precision = ($Length * 125) / 1000;
		} else {
			$Precision = $Segments; # used here: example.spring.complex.php
		}

		$P = [
			["X" => $X1,  "Y" => $Y1],
			["X" => $Xv1, "Y" => $Yv1],
			["X" => $Xv2, "Y" => $Yv2],
			["X" => $X2,  "Y" => $Y2]
		];

		/* Compute the bezier points */
		$Q = [];
		$ID = 0;
		for ($i = 0; $i <= $Precision; $i++) {
			$u = $i / $Precision;
			$C = [
				pow((1 - $u),3),
				($u * 3) * (1 - $u) * (1 - $u),
				3 * $u * $u * (1 - $u),
				pow($u,3)
			];

			$Q[$ID] = ["X" => 0, "Y" => 0];

			for ($j = 0; $j <= 3; $j++) {
				$Q[$ID]["X"] += ($P[$j]["X"] * $C[$j]);
				$Q[$ID]["Y"] += ($P[$j]["Y"] * $C[$j]);
			}

			$ID++;
		}

		$Q[$ID]["X"] = $X2;
		$Q[$ID]["Y"] = $Y2;

		if ($NoDraw) {
			return $Q;
		}

		$Cpt = 1;
		$Mode = TRUE;
		$Qcount = count($Q);

		/* Draw the bezier */
		for($i=1;$i<$Qcount;$i++){ # omits the first member on purpose
			list($Cpt, $Mode) = $this->drawLine($Q[$i - 1]["X"], $Q[$i - 1]["Y"], $Q[$i]["X"], $Q[$i]["Y"], ["Color" => $Color,"Ticks" => $Ticks,"Cpt" => $Cpt,"Mode" => $Mode,"Weight" => $Weight]);
		}

		/* Display the control points */
		if ($ShowControl) {
			$Xv1 = floor($Xv1);
			$Yv1 = floor($Yv1);
			$Xv2 = floor($Xv2);
			$Yv2 = floor($Yv2);
			$this->drawLine($X1, $Y1, $X2, $Y2, ["Color" => new pColor(0,0,0,30)]);
			$this->drawRectangleMarker($Xv1, $Yv1, ["Color" => new pColor(255,0,0,100),"BorderColor" => new pColor(255),"Size" => 4]);
			$this->drawText($Xv1 + 4, $Yv1, "v1");
			$this->drawRectangleMarker($Xv2, $Yv2, ["Color" => new pColor(0,0,255,100),"BorderColor" => new pColor(255),"Size" => 4]);
			$this->drawText($Xv2 + 4, $Yv2, "v2");
		}

		if ($DrawArrow) {
			$ArrowSettings = ["FillColor" => $Color,"Size" => $ArrowSize,"Ratio" => $ArrowRatio];
			if ($ArrowTwoHeads){
				/* Get the first segment */
				$FirstTwo = array_slice($Q, 0, 2);
				$this->drawArrow($FirstTwo[1]["X"], $FirstTwo[1]["Y"], $FirstTwo[0]["X"], $FirstTwo[0]["Y"], $ArrowSettings);
			}
			/* Get the last segment */
			$LastTwo = array_slice($Q, -2, 2);
			$this->drawArrow($LastTwo[0]["X"], $LastTwo[0]["Y"],$LastTwo[1]["X"], $LastTwo[1]["Y"], $ArrowSettings);
		}

		return $Q;
	}

	/* Draw a line between two points */
	public function drawLine($X1, $Y1, $X2, $Y2, array $Format = []) # FAST
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$Cpt = isset($Format["Cpt"]) ? $Format["Cpt"] : 1;
		$Threshold = isset($Format["Threshold"]) ? $Format["Threshold"] : [];
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$Weight = isset($Format["Weight"]) ? $Format["Weight"] : NULL;
		$Mode = isset($Format["Mode"]) ? $Format["Mode"] : 1;

		# Keep it as some of the examples pass 0 for Ticks
		if ($Ticks === 0){
			$Ticks = NULL;
		}
		
		$X1 = intval($X1);
		$X2 = intval($X2);
		$Y1 = intval($Y1);
		$Y2 = intval($Y2);

		if ($this->Antialias == FALSE && is_null($Ticks)) {
			if ($this->Shadow) {
				imageline($this->Picture, $X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, $this->ShadowColorAlloc);
			}

			imageline($this->Picture, $X1, $Y1, $X2, $Y2, $this->allocateColor($Color->get()));
			return [$Cpt, $Mode];
		}

		$Distance = hypot(($X2 - $X1), ($Y2 - $Y1));
		if ($Distance == 0) {
			return [$Cpt, $Mode];
			#throw pException::InvalidDimentions("Line coordinates are not valid!");
		}

		$XStep = ($X2 - $X1) / $Distance;
		$YStep = ($Y2 - $Y1) / $Distance;

		/* Derivative algorithm for overweighted lines, re-route to polygons primitives */
		if (!is_null($Weight)) {
			$Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
			$AngleCosPlus90 = cos(deg2rad($Angle + 90)) * $Weight;
			$AngleSinPlus90 = sin(deg2rad($Angle + 90)) * $Weight;

			if (is_null($Ticks)) {
				$Points = [-$AngleCosPlus90 + $X1, -$AngleSinPlus90 + $Y1, $AngleCosPlus90 + $X1, $AngleSinPlus90 + $Y1, $AngleCosPlus90 + $X2, $AngleSinPlus90 + $Y2, -$AngleCosPlus90+ $X2, -$AngleSinPlus90 + $Y2];
				$this->drawPolygon($Points, ["Color" => $Color]);
			} else {
				for ($i = 0; $i <= $Distance; $i = $i + $Ticks * 2) {
					$Xa = $XStep * $i + $X1;
					$Ya = $YStep * $i + $Y1;
					$Xb = $XStep * ($i + $Ticks) + $X1;
					$Yb = $YStep * ($i + $Ticks) + $Y1;
					$Points = [-$AngleCosPlus90 + $Xa, -$AngleSinPlus90 + $Ya, $AngleCosPlus90 + $Xa, $AngleSinPlus90 + $Ya, $AngleCosPlus90 + $Xb, $AngleSinPlus90 + $Yb, -$AngleCosPlus90 + $Xb, -$AngleSinPlus90 + $Yb];
					$this->drawPolygon($Points, ["Color" => $Color]);
				}
			}

			return [$Cpt, $Mode];
		}

		if (empty($Threshold) && is_null($Ticks)){ # Momchil: Fast path based on my test cases
			for ($i = 0; $i <= $Distance; $i++) {
				$this->drawAntialiasPixel($i * $XStep + $X1, $i * $YStep + $Y1, $Color);
			}

		} else {

			for ($i = 0; $i <= $Distance; $i++) {
				$X = $i * $XStep + $X1;
				$Y = $i * $YStep + $Y1;

				foreach($Threshold as $Parameters) {
					if ($Y <= $Parameters["MinX"] && $Y >= $Parameters["MaxX"]) {
						$Color = $Parameters["Color"];
					}
				}

				if (!is_null($Ticks)) {
					if ($Cpt % $Ticks == 0) {
						$Cpt = 0;
						$Mode ^= 1;
					}
					($Mode) AND $this->drawAntialiasPixel($X, $Y, $Color);
					$Cpt++;
				} else {
					$this->drawAntialiasPixel($X, $Y, $Color);
				}
			}

		}

		return [$Cpt,$Mode];
	}

	/* Draw a circle */
	public function drawCircle($Xc, $Yc, $Height, $Width, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;
		$Mask =  isset($Format["Mask"])  ? $Format["Mask"]  : [];

		$Height = abs($Height);
		$Width = abs($Width);
		($Height == 0) AND $Height = 1;
		($Width == 0) AND $Width = 1;
		$Xc = floor($Xc);
		$Yc = floor($Yc);
		$RestoreShadow = $this->Shadow;

		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawCircle($Xc + $this->ShadowX, $Yc + $this->ShadowY, $Height, $Width, ["Color" => $this->ShadowColor,"Ticks" => $Ticks]);
		}

		$Step = rad2deg(1/max($Width, $Height));
		$Mode = TRUE;
		$Cpt = 1;

		for ($i = 0; $i <= 360; $i += $Step) {
			$X = cos(deg2rad($i)) * $Height + $Xc;
			$Y = sin(deg2rad($i)) * $Width + $Yc;
			if (!is_null($Ticks)) {
				if ($Cpt % $Ticks == 0) {
					$Cpt = 0;
					$Mode ^= 1; # invert
				}

				if ($Mode) {
					if (isset($Mask[$Xc])) {
						if (!in_array($Yc, $Mask[$Xc])) {
							$this->drawAntialiasPixel($X, $Y, $Color);
						}
					} else {
						$this->drawAntialiasPixel($X, $Y, $Color);
					}
				}

				$Cpt++;
			} else {
				if (isset($Mask[$Xc])) {
					if (!in_array($Yc, $Mask[$Xc])) {
						$this->drawAntialiasPixel($X, $Y, $Color);
					}
				} else {
					$this->drawAntialiasPixel($X, $Y, $Color);
				}
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Draw a filled circle */
	public function drawFilledCircle($X, $Y, $Radius, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(0);
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : NULL;
		if(isset($Format["Surrounding"])){
			$BorderColor = $Color->newOne()->RGBChange($Format["Surrounding"]);
		}
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;

		$X = floor($X);
		$Y = floor($Y);
		$Radius = ($Radius == 0) ? 1 : abs($Radius);

		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawFilledCircle($X + $this->ShadowX, $Y + $this->ShadowY, $Radius, ["Color" => $this->ShadowColor,"Ticks" => $Ticks]);
		}

		$Mask = [];
		$ColorAlloc = $this->allocateColor($Color->get());
		for ($i = 0; $i <= $Radius * 2; $i++) {
			$Slice = sqrt($Radius * $Radius - ($Radius - $i) * ($Radius - $i));
			$XPos = floor($Slice);
			$YPos = $Y + $i - $Radius;
			#$AAlias = $Slice - floor($Slice); # Momchil: UNUSED
			$Mask[$X - $XPos][] = $YPos;
			$Mask[$X + $XPos][] = $YPos;
			imageline($this->Picture, intval($X - $XPos), intval($YPos), intval($X + $XPos), intval($YPos), $ColorAlloc);
		}

		if ($this->Antialias) {
			$this->drawCircle($X, $Y, $Radius, $Radius, ["Color" => $Color,"Ticks" => $Ticks, "Mask" => $Mask]);
		}

		if (!is_null($BorderColor)) {
			$this->drawCircle($X, $Y, $Radius, $Radius, ["Color" => $BorderColor,"Ticks" => $Ticks]);
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Write text */
	public function drawText($X, $Y, string $Text, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : $this->FontColor;
		$Angle = isset($Format["Angle"]) ? $Format["Angle"] : 0;
		$Align = isset($Format["Align"]) ? $Format["Align"] : TEXT_ALIGN_BOTTOMLEFT;
		$FontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->FontName;
		$FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->FontSize;
		$ShowOrigine = isset($Format["ShowOrigine"]) ? $Format["ShowOrigine"] : FALSE;
		$TOffset = isset($Format["TOffset"]) ? $Format["TOffset"] : 2;
		$DrawBox = isset($Format["DrawBox"]) ? $Format["DrawBox"] : FALSE;
		#$DrawBoxBorder = isset($Format["DrawBoxBorder"]) ? $Format["DrawBoxBorder"] : TRUE;
		$BorderOffset = isset($Format["BorderOffset"]) ? $Format["BorderOffset"] : 6;
		$BoxRounded = isset($Format["BoxRounded"]) ? $Format["BoxRounded"] : FALSE;
		$RoundedRadius = isset($Format["RoundedRadius"]) ? $Format["RoundedRadius"] : 6;
		$BoxColor = isset($Format["BoxColor"]) ? $Format["BoxColor"] : new pColor(255,255,255,50);
		$BoxSurrounding = isset($Format["BoxSurrounding"]) ? $Format["BoxSurrounding"] : 0;
		$BoxBorderColor = isset($Format["BoxBorderColor"]) ? $Format["BoxBorderColor"] : $BoxColor->newOne();
		$NoShadow = isset($Format["NoShadow"]) ? $Format["NoShadow"] : FALSE;

		$Shadow = $this->Shadow;
		($NoShadow) AND $this->Shadow = FALSE;

		if ($BoxSurrounding != 0) {
			$BoxBorderColor->RGBChange(-$BoxSurrounding);
			$BoxBorderColor->AlphaSet($BoxColor->AlphaGet());
		}

		if ($ShowOrigine) {
			$this->drawRectangleMarker($X, $Y, ["Color" => new pColor(255,0,0), "BorderColor" => new pColor(255), "Size" => 4]);
		}

		$TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, $Angle, $Text);
		if ($DrawBox && in_array($Angle,[0,90,180,270])) {
			$T = ($Angle == 0) ? ["X" => - $TOffset, "Y" => $TOffset] : ["X" => 0, "Y" => 0];
			$X1 = min($TxtPos[0]["X"], $TxtPos[1]["X"], $TxtPos[2]["X"], $TxtPos[3]["X"]) - $BorderOffset + 3;
			$Y1 = min($TxtPos[0]["Y"], $TxtPos[1]["Y"], $TxtPos[2]["Y"], $TxtPos[3]["Y"]) - $BorderOffset;
			$X2 = max($TxtPos[0]["X"], $TxtPos[1]["X"], $TxtPos[2]["X"], $TxtPos[3]["X"]) + $BorderOffset + 3;
			$Y2 = max($TxtPos[0]["Y"], $TxtPos[1]["Y"], $TxtPos[2]["Y"], $TxtPos[3]["Y"]) + $BorderOffset - 3;
			$X1 = $X1 - $TxtPos[$Align]["X"] + $X + $T["X"];
			$Y1 = $Y1 - $TxtPos[$Align]["Y"] + $Y + $T["Y"];
			$X2 = $X2 - $TxtPos[$Align]["X"] + $X + $T["X"];
			$Y2 = $Y2 - $TxtPos[$Align]["Y"] + $Y + $T["Y"];
			$Settings = ["Color" => $BoxColor,"BorderColor" => $BoxBorderColor];
			if ($BoxRounded) {
				#Momchil: Visual fix applied
				$this->drawRoundedFilledRectangle($X1-3, $Y1-2, $X2, $Y2, $RoundedRadius, $Settings);
			} else {
				$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, $Settings);
			}
		}

		$this->verifyFontDefined();

		$X = $X + $X - (int)$TxtPos[$Align]["X"];
		$Y = $Y + $Y - (int)$TxtPos[$Align]["Y"];

		if ($this->Shadow) {
			imagettftext($this->Picture, $FontSize, intval($Angle), intval($X + $this->ShadowX), intval($Y + $this->ShadowY), $this->ShadowColorAlloc, realpath($FontName), $Text);
		}

		imagettftext($this->Picture, $FontSize, intval($Angle), intval($X), intval($Y), $this->allocateColor($Color->get()), realpath($FontName), $Text);
		$this->Shadow = $Shadow;

		return $TxtPos;
	}

	/* Draw a gradient within a defined area */
	public function drawGradientArea($X1, $Y1, $X2, $Y2, $Direction, array $Colors, $Levels = NULL)
	{
		if (!is_null($Levels)) {
			$Colors["EndColor"] = $Colors["StartColor"]->newOne()->RGBChange($Levels);
		}

		$GradientColor = new pColorGradient($Colors["StartColor"]->newOne(), $Colors["EndColor"]->newOne());

		/* Draw a gradient within a defined area */
		$Shadow = $this->Shadow;
		$this->Shadow = FALSE;
		if (!$GradientColor->isGradient()) {
			$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["Color" => $Colors["StartColor"]]);
			return;
		}

		($X1 > $X2) AND list($X1, $X2) = [$X2,$X1];
		($Y1 > $Y2) AND list($Y1, $Y2) = [$Y2,$Y1];

		$Step = $GradientColor->findStep();

		if ($Direction == DIRECTION_VERTICAL){

				$StepSize = abs($Y2 - $Y1)/ $Step;
				$GradientColor->setSegments($Step);
				$StartY = $Y1;
				$EndY = floor($Y2) + 1;
				$LastY2 = $StartY;

				for ($i = 0; $i < $Step; $i++) {

					$Y2 = floor($StartY + ($i * $StepSize));
					($Y2 > $EndY) AND $Y2 = $EndY;

					if (($Y1 != $Y2 && $Y1 < $Y2) || $Y2 == $EndY) {
						$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["Color" => $GradientColor->getLatest()]);
						$LastY2 = max($LastY2, $Y2);
						$Y1 = $Y2 + 1;
					}

					$GradientColor->moveNext();
				}

				if ($LastY2 < $EndY) {
					for ($i = $LastY2 + 1; $i < $EndY; $i++) {
						$this->drawLine($X1, $i, $X2, $i, ["Color" => $GradientColor->getLatest()]);
					}
				}

		} elseif ($Direction == DIRECTION_HORIZONTAL) {

				$StepSize = abs($X2 - $X1) / $Step;
				$GradientColor->setSegments($Step);
				$StartX = $X1;
				$EndX = $X2;

				for ($i = 0; $i < $Step; $i++) {

					$X2 = floor($StartX + ($i * $StepSize));
					($X2 > $EndX) AND $X2 = $EndX;

					if (($X1 != $X2 && $X1 < $X2) || $X2 == $EndX) {
						$this->drawFilledRectangle($X1, $Y1, $X2, $Y2, ["Color" => $GradientColor->getLatest()]);
						$X1 = $X2 + 1;
					}

					$GradientColor->moveNext();
				}

				if ($X2 < $EndX) {
					$this->drawFilledRectangle($X2, $Y1, $EndX, $Y2, ["Color" => $GradientColor->getLatest()]);
				}
		}

		$this->Shadow = $Shadow;
	}

	/* Draw an aliased pixel */
	public function drawAntialiasPixel($X, $Y, pColor $Color) # FAST
	{
		if ($X < 0 || $Y < 0 || ceil($X) > $this->XSize || ceil($Y) > $this->YSize){
			#debug_print_backtrace();
			throw pException::InvalidCoordinates("Trying to draw outside of image dimentions.");
		}

		$ColorA = $Color->get();

		if (!$this->Antialias) {
			if ($this->Shadow) {
				# That can go out of range
				imagesetpixel($this->Picture, $X + $this->ShadowX, $Y + $this->ShadowY, $this->ShadowColorAlloc);
			}

			imagesetpixel($this->Picture, $X, $Y, $this->allocateColor($ColorA));
			return;
		}

		$Xi = floor($X);
		$Yi = floor($Y);

		if ($Xi == $X && $Yi == $Y) {

			$this->drawAlphaPixel($X, $Y, $ColorA);

		} else {

			$Yleaf = $Y - $Yi;
			$Xleaf = $X - $Xi;

			$defaultAlpha = $ColorA[3];

			if ($this->AntialiasQuality == 0) {
				switch(TRUE){
					case ($Yleaf == 0):
						$ColorA[3] = $defaultAlpha * (1 - $Xleaf);
						$this->drawAlphaPixel($Xi, $Yi, $ColorA);
						$ColorA[3] = $defaultAlpha * $Xleaf;
						$this->drawAlphaPixel($Xi + 1, $Yi, $ColorA);
						break;
					case ($Xleaf == 0):
						$ColorA[3] = $defaultAlpha * (1 - $Yleaf);
						$this->drawAlphaPixel($Xi, $Yi, $ColorA);
						$ColorA[3] = $defaultAlpha * ($Yleaf);
						$this->drawAlphaPixel($Xi, $Yi + 1, $ColorA);
						break;
					default:
						$ColorA[3] = $defaultAlpha * ((1 - $Xleaf) * (1 - $Yleaf));
						$this->drawAlphaPixel($Xi, $Yi, $ColorA);
						$ColorA[3] = $defaultAlpha * ($Xleaf * (1 - $Yleaf));
						$this->drawAlphaPixel($Xi + 1, $Yi, $ColorA);
						$ColorA[3] = $defaultAlpha * ((1 - $Xleaf) * $Yleaf);
						$this->drawAlphaPixel($Xi, $Yi + 1, $ColorA);
						$ColorA[3] = $defaultAlpha * ($Xleaf * $Yleaf);
						$this->drawAlphaPixel($Xi + 1, $Yi + 1, $ColorA);
				}
			} else {
				$ColorA[3] = (1 - $Xleaf) * (1 - $Yleaf) * $defaultAlpha;
				if ($ColorA[3] > $this->AntialiasQuality) {
					$this->drawAlphaPixel($Xi, $Yi, $ColorA);
				}

				$ColorA[3] = $Xleaf * (1 - $Yleaf) * $defaultAlpha;
				if ($ColorA[3] > $this->AntialiasQuality) {
					$this->drawAlphaPixel($Xi + 1, $Yi, $ColorA);
				}

				$ColorA[3] = (1 - $Xleaf) * $Yleaf * $defaultAlpha;
				if ($ColorA[3] > $this->AntialiasQuality) {
					$this->drawAlphaPixel($Xi, $Yi + 1, $ColorA);
				}

				$ColorA[3] = $Xleaf * $Yleaf * $defaultAlpha;
				if ($ColorA[3] > $this->AntialiasQuality) {
					$this->drawAlphaPixel($Xi + 1, $Yi + 1, $ColorA);
				}
			}

		}
	}

	/* Draw a semi-transparent pixel */
	private function drawAlphaPixel($X, $Y, array $ColorA) # FAST
	{
		if ($this->Shadow) {
			$ShadowColorA = $this->ShadowColor->get();
			$ShadowColorA[3] *= floor($ColorA[3] / 100);
			imagesetpixel($this->Picture, intval($X + $this->ShadowX), intval($Y + $this->ShadowY), $this->allocateColor($ShadowColorA));
		}

		imagesetpixel($this->Picture, intval($X), intval($Y), $this->allocateColor($ColorA));
	}

	/* Allocate a color with transparency */
	public function allocateColor(array $ColorA) # FAST
	{
		($ColorA[3] < 0)   AND $ColorA[3] = 0;
		($ColorA[3] > 100) AND $ColorA[3] = 100;

		return imagecolorallocatealpha($this->Picture, $ColorA[0], $ColorA[1], $ColorA[2], intval(1.27 * (100 - $ColorA[3])));
	}

	private function allocatepColor(pColor $color)
	{
		list ($R, $G, $B, $A) = $color->get();
		return imagecolorallocatealpha($this->Picture, $R, $G, $B, intval(1.27 * (100 - $A)));
	}

	/* Load a PNG file and draw it over the chart */
	public function drawFromPNG($X, $Y, $FileName)
	{
		$PicInfo = $this->getPicInfo($FileName);
		$PicInfo[2] = 'imagecreatefrompng'; # force PNG
		$this->drawFromPicture($PicInfo, $FileName, $X, $Y);
	}

	/* Load a GIF file and draw it over the chart */
	public function drawFromGIF($X, $Y, $FileName)
	{
		$PicInfo = $this->getPicInfo($FileName);
		$PicInfo[2] = 'imagecreatefromgif'; # force GIF
		$this->drawFromPicture($PicInfo, $FileName, $X, $Y);
	}

	/* Load a JPEG file and draw it over the chart */
	public function drawFromJPG($X, $Y, $FileName)
	{
		$PicInfo = $this->getPicInfo($FileName);
		$PicInfo[2] = 'imagecreatefromjpeg'; # force JPG
		$this->drawFromPicture($PicInfo, $FileName, $X, $Y);
	}

	public function getPicInfo($FileName)
	{
		if (!file_exists($FileName)) {
			throw pException::InvalidImageType("Image ".$FileName." was not found");
		}

		$Info = getimagesize($FileName);

		switch ($Info["mime"]){
			case "image/png":
				$Type = 'imagecreatefrompng';
				break;
			case "image/gif":
				$Type = 'imagecreatefromgif';
				break;
			case "image/jpeg":
				$Type = 'imagecreatefromjpeg';
				break;
			default:
				throw pException::InvalidImageType($FileName." is an unsupported type - ".$Info["mime"]);
		}

		return [$Info[0],$Info[1],$Type];
	}

	/* Generic loader function for external pictures */
	public function drawFromPicture($PicInfo, $FileName, $X, $Y)
	{
		list($Width, $Height, $PicType) = $PicInfo;

		$Raster = $PicType($FileName);

		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			if ($PicType == 'imagecreatefromjpeg') {
				$this->drawFilledRectangle($X + $this->ShadowX, $Y + $this->ShadowY, $X + $Width + $this->ShadowX, $Y + $Height + $this->ShadowY, ["Color" => $this->ShadowColor]);
			} else {
				$TranparentID = imagecolortransparent($Raster);
				$ShadowColorAlloc = $this->ShadowColor->get();
				$defaultAlpha = $ShadowColorAlloc[3];
				for ($Xc = 0; $Xc <= $Width - 1; $Xc++) {
					for ($Yc = 0; $Yc <= $Height - 1; $Yc++) {
						$Values = imagecolorsforindex($Raster, imagecolorat($Raster, $Xc, $Yc));
						if ($Values["alpha"] < 120) {
							$ShadowColorAlloc[3] = floor($defaultAlpha * (1 - $Values["alpha"]/127));
							$this->drawAlphaPixel($X + $Xc + $this->ShadowX, $Y + $Yc + $this->ShadowY, $ShadowColorAlloc);
						}
					}
				}
			}
		}

		$this->Shadow = $RestoreShadow;
		imagecopy($this->Picture, $Raster, intval($X), intval($Y), 0, 0, $Width, $Height);
		imagedestroy($Raster);
	}

	/* Mirror Effect */
	public function drawAreaMirror($X, $Y, $Width, $Height, array $Format = [])
	{
		$StartAlpha = isset($Format["StartAlpha"]) ? $Format["StartAlpha"] : 80;
		$EndAlpha = isset($Format["EndAlpha"]) ? $Format["EndAlpha"] : 0;
		$AlphaStep = ($StartAlpha - $EndAlpha) / $Height;
		$Picture = imagecreatetruecolor($this->XSize, $this->YSize);
		imagecopy($Picture, $this->Picture, 0, 0, 0, 0, $this->XSize, $this->YSize);
		for ($i = 1; $i <= $Height; $i++) {
			if ($Y + ($i - 1) < $this->YSize && $Y - $i > 0) {
				imagecopymerge($Picture, $this->Picture, $X, $Y + ($i - 1), $X, $Y - $i, $Width, 1, intval($StartAlpha - $AlphaStep * $i));
			}
		}

		imagecopy($this->Picture, $Picture, 0, 0, 0, 0, $this->XSize, $this->YSize);

		imagedestroy($Picture);
	}

	/* Draw an arrow */
	public function drawArrow($X1, $Y1, $X2, $Y2, array $Format = [])
	{
		$FillColor = isset($Format["FillColor"]) ? $Format["FillColor"] : new pColor(0);
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : $FillColor->newOne();
		$Size = isset($Format["Size"]) ? $Format["Size"] : 10;
		$Ratio = isset($Format["Ratio"]) ? $Format["Ratio"] : .5;
		$TwoHeads = isset($Format["TwoHeads"]) ? $Format["TwoHeads"] : FALSE;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;

		$RGB = ["Color" => $BorderColor];

		/* Override Shadow support, this will be managed internally */
		$RestoreShadow = $this->Shadow;
		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$this->drawArrow($X1 + $this->ShadowX, $Y1 + $this->ShadowY, $X2 + $this->ShadowX, $Y2 + $this->ShadowY, ["FillColor" => $this->ShadowColor,"Size" => $Size,"Ratio" => $Ratio,"TwoHeads" => $TwoHeads,"Ticks" => $Ticks]);
		}

		/* Draw the 1st Head */
		$Angle = $this->getAngle($X1, $Y1, $X2, $Y2);
		$TailX = cos(deg2rad($Angle - 180)) * $Size + $X2;
		$TailY = sin(deg2rad($Angle - 180)) * $Size + $Y2;
		$Scale = $Size * $Ratio;
		$cos90 = cos(deg2rad($Angle - 90)) * $Scale;
		$sin90 = sin(deg2rad($Angle - 90)) * $Scale;

		$Points = [$X2, $Y2, $cos90 + $TailX, $sin90 + $TailY, -$cos90 + $TailX, -$sin90 + $TailY, $X2, $Y2];
		/* Visual correction */
		($Angle == 180 || $Angle == 360) AND $Points[4] = $Points[2];
		($Angle == 90 || $Angle == 270) AND $Points[5] = $Points[3];

		$fillColorAlloc = $this->allocateColor($FillColor->get());
		imagefilledpolygon($this->Picture, $Points, 4, $fillColorAlloc);
		$this->drawLine($Points[0], $Points[1], $Points[2], $Points[3], $RGB);
		$this->drawLine($Points[2], $Points[3], $Points[4], $Points[5], $RGB);
		$this->drawLine($Points[0], $Points[1], $Points[4], $Points[5], $RGB);
		/* Draw the second head */
		if ($TwoHeads) {
			$Angle = $this->getAngle($X2, $Y2, $X1, $Y1);
			$cos90 = cos(deg2rad($Angle - 90)) * $Scale;
			$sin90 = sin(deg2rad($Angle - 90)) * $Scale;
			$TailX2 = cos(deg2rad($Angle - 180)) * $Size + $X1;
			$TailY2 = sin(deg2rad($Angle - 180)) * $Size + $Y1;
			$Points = [$X1, $Y1, $cos90 + $TailX2, $sin90 + $TailY2, -$cos90 + $TailX2, -$sin90 + $TailY2, $X1, $Y1];
			/* Visual correction */
			($Angle == 180 || $Angle == 360) AND $Points[4] = $Points[2];
			($Angle == 90 || $Angle == 270) AND $Points[5] = $Points[3];

			imagefilledpolygon($this->Picture, $Points, 4, $fillColorAlloc);
			$this->drawLine($Points[0], $Points[1], $Points[2], $Points[3], $RGB);
			$this->drawLine($Points[2], $Points[3], $Points[4], $Points[5], $RGB);
			$this->drawLine($Points[0], $Points[1], $Points[4], $Points[5], $RGB);
			$this->drawLine($TailX, $TailY, $TailX2, $TailY2, ["Color" => $BorderColor,"Ticks" => $Ticks]);
		} else {
			$this->drawLine($X1, $Y1, $TailX, $TailY, ["Color" => $BorderColor,"Ticks" => $Ticks]);
		}

		/* Re-enable shadows */
		$this->Shadow = $RestoreShadow;
	}

	/* Draw a label with associated arrow */
	public function drawArrowLabel($X1, $Y1, $Text, array $Format = [])
	{
		$FillColor = isset($Format["FillColor"]) ? $Format["FillColor"] : new pColor(0);
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : $FillColor->newOne();
		$FontName = isset($Format["FontName"]) ? $Format["FontName"] : $this->FontName;
		$FontSize = isset($Format["FontSize"]) ? $Format["FontSize"] : $this->FontSize;
		$Length = isset($Format["Length"]) ? $Format["Length"] : 50;
		$Angle = isset($Format["Angle"]) ? $Format["Angle"] : 315;
		#$Size = isset($Format["Size"]) ? $Format["Size"] : 10;
		$Position = isset($Format["Position"]) ? $Format["Position"] : POSITION_TOP;
		$RoundPos = isset($Format["RoundPos"]) ? $Format["RoundPos"] : FALSE;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : NULL;

		$Angle = $Angle % 360;
		$X2 = sin(deg2rad($Angle + 180)) * $Length + $X1;
		$Y2 = cos(deg2rad($Angle + 180)) * $Length + $Y1;
		($RoundPos && $Angle > 0 && $Angle < 180) AND $Y2 = ceil($Y2);
		($RoundPos && $Angle > 180) AND $Y2 = floor($Y2);

		$this->drawArrow($X2, $Y2, $X1, $Y1, $Format);

		$this->verifyFontDefined();
		$Size = imagettfbbox($FontSize, 0, realpath($FontName), $Text);
		$TxtWidth = max(abs($Size[2] - $Size[0]), abs($Size[0] - $Size[6]));
		#$TxtHeight = max(abs($Size[1] - $Size[7]), abs($Size[3] - $Size[1])); # UNUSED
		$RGB = ["Color" => $BorderColor];

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

		$this->drawLine($X2, $Y2, $TxtWidth, $Y2, ["Color" => $BorderColor,"Ticks" => $Ticks]);
		$this->drawText($X2, $Y3, $Text, $RGB);

	}

	/* Draw a progress bar filled with specified % */
	public function drawProgress($X, $Y, $Percent, array $Format = [])
	{
		($Percent > 100) AND $Percent = 100;
		($Percent < 0) AND $Percent = 0;

		$Width = 200;
		$Height = 20;
		$Orientation = ORIENTATION_HORIZONTAL;
		$ShowLabel = FALSE;
		$LabelPos = LABEL_POS_INSIDE;
		$Margin = 10;
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(130);
		$FadeColor = NULL;
		$BorderColor = $Color->newOne();
		$BoxBorderColor = isset($Format["BoxBorderColor"]) ? $Format["BoxBorderColor"] : new pColor(0);
		$BoxBackColor = isset($Format["BoxBackColor"]) ? $Format["BoxBackColor"] : new pColor(255);
		$Surrounding = NULL;
		$BoxSurrounding = NULL;
		$NoAngle = FALSE;

		/* Override defaults */
		extract($Format);

		if (!is_null($Surrounding)) {
			$BorderColor = $Color->newOne()->RGBChange($Surrounding);
		}

		if (!is_null($BoxSurrounding)) {
			$BoxBorderColor = $BoxBackColor->newOne()->RGBChange($Surrounding);
		}

		if ($Orientation == ORIENTATION_VERTICAL) {
			$InnerHeight = (($Height - 2) / 100) * $Percent;
			$this->drawFilledRectangle($X, $Y, $X + $Width, $Y - $Height, ["Color" => $BoxBackColor,"BorderColor" => $BoxBorderColor,"NoAngle" => $NoAngle]);
			$RestoreShadow = $this->Shadow;
			$this->Shadow = FALSE;
			if (!is_null($FadeColor)) {
				$Gradient = new pColorGradient($Color, $FadeColor);
				$Gradient->setSegments(100);
				$this->drawGradientArea($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, DIRECTION_VERTICAL, ["StartColor"=>$Gradient->getStep($Percent),"EndColor"=>$Color]);
				(!is_null($Surrounding)) AND $this->drawRectangle($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, ["Color" => new pColor(255,255,255,$Surrounding)]);
			} else {
				$this->drawFilledRectangle($X + 1, $Y - 1, $X + $Width - 1, $Y - $InnerHeight, ["Color" => $Color,"BorderColor" => $BorderColor]);
			}
			$this->Shadow = $RestoreShadow;

			if ($ShowLabel){
				switch ($LabelPos) {
					case LABEL_POS_BOTTOM:
						$this->drawText($X + ($Width / 2), $Y + $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_TOPMIDDLE]);
						break;
					case LABEL_POS_TOP:
						$this->drawText($X + ($Width / 2), $Y - $Height - $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_BOTTOMMIDDLE]);
						break;
					case LABEL_POS_INSIDE:
						$this->drawText($X + ($Width / 2), $Y - $InnerHeight - $Margin, $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT,"Angle" => 90]);
						break;
					case LABEL_POS_CENTER:
						$this->drawText($X + ($Width / 2), $Y - ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLEMIDDLE,"Angle" => 90]);
						break;
				}
			}

		} else {
			$InnerWidth = ($Percent == 100) ? $Width - 1 : (($Width - 2) / 100) * $Percent;
			$this->drawFilledRectangle($X, $Y, $X + $Width, $Y + $Height, ["Color" => $BoxBackColor,"BorderColor" => $BoxBorderColor,"NoAngle" => $NoAngle]);
			$RestoreShadow = $this->Shadow;
			$this->Shadow = FALSE;
			if (!is_null($FadeColor)) {
				$Gradient = new pColorGradient($Color, $FadeColor);
				$Gradient->setSegments(100);
				$this->drawGradientArea($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, DIRECTION_HORIZONTAL, ["StartColor"=>$Color,"EndColor"=>$Gradient->getStep($Percent)]);
				(!is_null($Surrounding)) AND $this->drawRectangle($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, ["Color" => new pColor(255,255,255,$Surrounding)]);
			} else {
				$this->drawFilledRectangle($X + 1, $Y + 1, $X + $InnerWidth, $Y + $Height - 1, ["Color" => $Color,"BorderColor" => $BorderColor]);
			}
			$this->Shadow = $RestoreShadow;

			if ($ShowLabel){
				switch ($LabelPos) {
					case LABEL_POS_LEFT:
						$this->drawText($X - $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLERIGHT]);
						break;
					case LABEL_POS_RIGHT:
						$this->drawText($X + $Width + $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT]);
						break;
					case LABEL_POS_CENTER:
						$this->drawText($X + ($Width / 2), $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
						break;
					case LABEL_POS_INSIDE:
						$this->drawText($X + $InnerWidth + $Margin, $Y + ($Height / 2), $Percent . "%", ["Align" => TEXT_ALIGN_MIDDLELEFT]);
						break;
				}
			}
		}

	}

	/* Get the legend box size */
	public function getLegendSize(array $Format = [])
	{
		$FontName = $this->FontName;
		$FontSize = $this->FontSize;
		#$BoxSize = 5;
		$Margin = 5;
		#$Style = LEGEND_ROUND;
		$Mode = LEGEND_VERTICAL;
		$BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 5;
		$BoxHeight = isset($Format["BoxHeight"]) ? $Format["BoxHeight"] : 5;
		$IconAreaWidth = $BoxWidth;
		$IconAreaHeight = $BoxHeight;
		$XSpacing = 5;

		extract($Format);

		$Data = $this->myData->getData();

		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] && $SerieName != $Data["Abscissa"] && !is_null($Serie["Picture"])) {
				list($PicWidth, $PicHeight) = $this->getPicInfo($Serie["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$YStep = max($this->FontSize, $IconAreaHeight) + 5;
		#$XStep = $IconAreaWidth + 5;
		$XStep = $XSpacing;
		$X = 100;
		$Y = 100;
		$Boundaries = ["L" => $X, "T" => 100, "R" => 0, "B" => 0];
		$vY = $Y;
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] && $SerieName != $Data["Abscissa"]) {
				$Lines = preg_split("/\n/", $Serie["Description"]);
				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->getTextBox($X + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Serie["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
					$vY = $vY + max($this->FontSize * count($Lines), $IconAreaHeight) + 5;
				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->getTextBox($X + $IconAreaWidth + 6, $vY + $IconAreaHeight / 2 + (($this->FontSize + 3) * $Key), $FontName, $FontSize, 0, $Value);
						($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
						($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
						($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
						$Width[] = $BoxArray[1]["X"];
					}
					$X = max($Width) + $XStep;
				}
			}
		}

		$vY = $vY - $YStep;
		$TopOffset = 100 - $Boundaries["T"];
		($Boundaries["B"] - ($vY + $IconAreaHeight) < $TopOffset) AND $Boundaries["B"] = $vY + $IconAreaHeight + $TopOffset;

		return [
			"Width" => ($Boundaries["R"] + $Margin) - ($Boundaries["L"] - $Margin),
			"Height" => ($Boundaries["B"] + $Margin) - ($Boundaries["T"] - $Margin)
		];
	}

	/* Draw the legend of the active series */
	public function drawLegend($X, $Y, array $Format = [])
	{
		$Family = LEGEND_FAMILY_BOX;
		$FontName = $this->FontName;
		$FontSize = $this->FontSize;
		$FontColor = $this->FontColor;
		$BoxWidth = isset($Format["BoxWidth"]) ? $Format["BoxWidth"] : 5;
		$BoxHeight = isset($Format["BoxHeight"]) ? $Format["BoxHeight"] : 5;
		$IconAreaWidth = $BoxWidth;
		$IconAreaHeight = $BoxHeight;
		$XSpacing = 5;
		$Margin = 5;
		$Color = NULL;
		$BorderColor = NULL;
		$Surrounding = NULL;
		$Style = LEGEND_ROUND;
		$Mode = LEGEND_VERTICAL;

		/* Override defaults */
		extract($Format);

		if ($X == $this->XSize){
			$X -= $BoxWidth;
		}

		if ($Y == $this->YSize){
			$Y -= $BoxHeight;
		}

		(is_null($Color)) AND $Color = new pColor(200);
		(is_null($BorderColor)) AND $BorderColor = new pColor(255);
		(!is_null($Surrounding)) AND $BorderColor->RGBChange($Surrounding);
		
		$Data = $this->myData->getData();

		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] && $SerieName != $Data["Abscissa"] && !is_null($Serie["Picture"])) {
				list($PicWidth, $PicHeight) = $this->getPicInfo($Serie["Picture"]);
				($IconAreaWidth < $PicWidth) AND $IconAreaWidth = $PicWidth;
				($IconAreaHeight < $PicHeight) AND $IconAreaHeight = $PicHeight;
			}
		}

		$YStep = max($this->FontSize, $IconAreaHeight) + 5;
		#$XStep = $IconAreaWidth + 5;
		$XStep = $XSpacing;
		$Boundaries = ["L" => $X, "T" => $Y, "R" => 0, "B" => 0];
		$vY = $Y;
		$vX = $X;
		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] && $SerieName != $Data["Abscissa"]) {
				$Lines = preg_split("/\n/", $Serie["Description"]);
				if ($Mode == LEGEND_VERTICAL) {
					$BoxArray = $this->getTextBox($vX + $IconAreaWidth + 4, $vY + $IconAreaHeight / 2, $FontName, $FontSize, 0, $Serie["Description"]);
					($Boundaries["T"] > $BoxArray[2]["Y"] + $IconAreaHeight / 2) AND $Boundaries["T"] = $BoxArray[2]["Y"] + $IconAreaHeight / 2;
					($Boundaries["R"] < $BoxArray[1]["X"] + 2) AND $Boundaries["R"] = $BoxArray[1]["X"] + 2;
					($Boundaries["B"] < $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2) AND $Boundaries["B"] = $BoxArray[1]["Y"] + 2 + $IconAreaHeight / 2;
					$vY = $vY + max($this->FontSize * count($Lines), $IconAreaHeight) + 5;
				} elseif ($Mode == LEGEND_HORIZONTAL) {
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
		#$vX = $vX - $XStep;
		$TopOffset = $Y - $Boundaries["T"];
		($Boundaries["B"] - ($vY + $IconAreaHeight) < $TopOffset) AND $Boundaries["B"] = $vY + $IconAreaHeight + $TopOffset;

		if ($Style == LEGEND_ROUND) {
			$this->drawRoundedFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, $Margin, ["Color" => $Color,"BorderColor" => $BorderColor]);
		} elseif ($Style == LEGEND_BOX) {
			$this->drawFilledRectangle($Boundaries["L"] - $Margin, $Boundaries["T"] - $Margin, $Boundaries["R"] + $Margin, $Boundaries["B"] + $Margin, ["Color" => $Color,"BorderColor" => $BorderColor]);
		}

		$RestoreShadow = $this->Shadow;
		$this->Shadow = FALSE;
		foreach($Data["Series"] as $SerieName => $Serie) {

			if ($Serie["isDrawable"] && $SerieName != $Data["Abscissa"]) {

				$Color = $Serie["Color"];
				$Ticks = $Serie["Ticks"];
				$Weight = $Serie["Weight"];

				if (!is_null($Serie["Picture"])) {
					list($PicWidth, $PicHeight) = $this->getPicInfo($Serie["Picture"]);
					$PicX = $X + $IconAreaWidth / 2;
					$PicY = $Y + $IconAreaHeight / 2;
					$this->drawFromPNG($PicX - $PicWidth / 2, $PicY - $PicHeight / 2, $Serie["Picture"]);

				} else {
					if ($Family == LEGEND_FAMILY_BOX) {
	
						$XOffset = ($BoxWidth != $IconAreaWidth) ? floor(($IconAreaWidth - $BoxWidth) / 2) : 0;
						$YOffset = ($BoxHeight != $IconAreaHeight) ? floor(($IconAreaHeight - $BoxHeight) / 2) : 0;

						$this->drawFilledRectangle($X + 1 + $XOffset, $Y + 1 + $YOffset, $X + $BoxWidth + $XOffset + 1, $Y + $BoxHeight + 1 + $YOffset, ["Color" => new pColor(0,0,0,20)]);
						$this->drawFilledRectangle($X + $XOffset, $Y + $YOffset, $X + $BoxWidth + $XOffset, $Y + $BoxHeight + $YOffset, ["Color" => $Color,"Surrounding" => 20]);

					} elseif ($Family == LEGEND_FAMILY_CIRCLE) {
						$this->drawFilledCircle($X + 1 + $IconAreaWidth / 2, $Y + 1 + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["Color" => new pColor(0,0,0,20)]);
						$this->drawFilledCircle($X + $IconAreaWidth / 2, $Y + $IconAreaHeight / 2, min($IconAreaHeight / 2, $IconAreaWidth / 2), ["Color" => $Color,"Surrounding" => 20]);

					} elseif ($Family == LEGEND_FAMILY_LINE) {
						$this->drawLine($X + 1, $Y + 1 + $IconAreaHeight / 2, $X + 1 + $IconAreaWidth, $Y + 1 + $IconAreaHeight / 2, ["Color" => new pColor(0,0,0,20),"Ticks" => $Ticks,"Weight" => $Weight]);
						$this->drawLine($X, $Y + $IconAreaHeight / 2, $X + $IconAreaWidth, $Y + $IconAreaHeight / 2, ["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
					}
				}

				$Lines = preg_split("/\n/", $Serie["Description"]);
				if ($Mode == LEGEND_VERTICAL) {
					foreach($Lines as $Key => $Value) {
						$this->drawText($X + $IconAreaWidth + 4, $Y + $IconAreaHeight / 2 + (($this->FontSize + 3) * $Key), $Value, ["Color" => $FontColor,"Align" => TEXT_ALIGN_MIDDLELEFT,"FontSize" => $FontSize,"FontName" => $FontName]);
					}
					$Y = $Y + max($this->FontSize * count($Lines), $IconAreaHeight) + 5;
					
				} elseif ($Mode == LEGEND_HORIZONTAL) {
					$Width = [];
					foreach($Lines as $Key => $Value) {
						$BoxArray = $this->drawText($X + $IconAreaWidth + 4, $Y + 2 + $IconAreaHeight / 2 + (($this->FontSize + 3) * $Key), $Value, ["Color" => $FontColor,"Align" => TEXT_ALIGN_MIDDLELEFT,"FontSize" => $FontSize,"FontName" => $FontName]);
						$Width[] = $BoxArray[1]["X"];
					}

					$X = max($Width) + 2 + $XStep;
				}
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	public function drawScale(array $Format = [])
	{
		$Pos = isset($Format["Pos"]) ? $Format["Pos"] : SCALE_POS_LEFTRIGHT;
		$Floating = isset($Format["Floating"]) ? $Format["Floating"] : FALSE;
		$Mode = isset($Format["Mode"]) ? $Format["Mode"] : SCALE_MODE_FLOATING;
		$RemoveXAxis = isset($Format["RemoveXAxis"]) ? $Format["RemoveXAxis"] : FALSE;
		$RemoveYAxis = isset($Format["RemoveYAxis"]) ? $Format["RemoveYAxis"] : FALSE;
		$MinDivHeight = isset($Format["MinDivHeight"]) ? $Format["MinDivHeight"] : 20;
		$Factors = isset($Format["Factors"]) ? $Format["Factors"] : [1,2,5];
		$ManualScale = isset($Format["ManualScale"]) ? $Format["ManualScale"] : array("0" => ["Min" => - 100,"Max" => 100]);
		$XMargin = isset($Format["XMargin"]) ? $Format["XMargin"] : AUTO;
		$YMargin = isset($Format["YMargin"]) ? $Format["YMargin"] : 0;
		$ScaleSpacing = isset($Format["ScaleSpacing"]) ? $Format["ScaleSpacing"] : 15;
		$InnerTickWidth = isset($Format["InnerTickWidth"]) ? $Format["InnerTickWidth"] : 2;
		$OuterTickWidth = isset($Format["OuterTickWidth"]) ? $Format["OuterTickWidth"] : 2;
		$DrawXLines = isset($Format["DrawXLines"]) ? $Format["DrawXLines"] : TRUE;
		$DrawYLines = isset($Format["DrawYLines"]) ? $Format["DrawYLines"] : [ALL];
		$GridTicks = isset($Format["GridTicks"]) ? $Format["GridTicks"] : 4;
		$GridColor = isset($Format["GridColor"]) ? ["Color" => $Format["GridColor"]] : ["Color" => new pColor(255,255,255,40)];
		$AxisColor = isset($Format["AxisColor"]) ? ["Color" => $Format["AxisColor"]] : ["Color" => new pColor(0)];
		$TickColor = isset($Format["TickColor"]) ? ["Color" => $Format["TickColor"]] : ["Color" => new pColor(0)];
		$DrawSubTicks = isset($Format["DrawSubTicks"]) ? $Format["DrawSubTicks"] : FALSE;
		$InnerSubTickWidth = isset($Format["InnerSubTickWidth"]) ? $Format["InnerSubTickWidth"] : 0;
		$OuterSubTickWidth = isset($Format["OuterSubTickWidth"]) ? $Format["OuterSubTickWidth"] : 2;
		$SubTickColor = isset($Format["TickColor"]) ? ["Color" => $Format["TickColor"]] : ["Color" => new pColor(255,0,0,100)];
		$XReleasePercent = isset($Format["XReleasePercent"]) ? $Format["XReleasePercent"] : 1;
		$DrawArrows = isset($Format["DrawArrows"]) ? $Format["DrawArrows"] : FALSE;
		$ArrowSize = isset($Format["ArrowSize"]) ? $Format["ArrowSize"] : 8;
		$CycleBackground = isset($Format["CycleBackground"]) ? $Format["CycleBackground"] : FALSE;
		$BackgroundColor1 = isset($Format["BackgroundColor1"]) ? ["Color" => $Format["BackgroundColor1"]] : ["Color" => new pColor(255,255,255,20)];
		$BackgroundColor2 = isset($Format["BackgroundColor2"]) ? ["Color" => $Format["BackgroundColor2"]] : ["Color" => new pColor(230,230,230,20)];
		$LabelingMethod = isset($Format["LabelingMethod"]) ? $Format["LabelingMethod"] : LABELING_ALL;
		$LabelSkip = isset($Format["LabelSkip"]) ? $Format["LabelSkip"] : 0;
		$LabelRotation = isset($Format["LabelRotation"]) ? $Format["LabelRotation"] : 0;
		$RemoveSkippedAxis = isset($Format["RemoveSkippedAxis"]) ? $Format["RemoveSkippedAxis"] : FALSE;
		$SkippedAxisTicks = isset($Format["SkippedAxisTicks"]) ? $Format["SkippedAxisTicks"] : $GridTicks + 2;
		$SkippedAxisColor = isset($Format["SkippedAxisColor"]) ? $Format["SkippedAxisColor"] : $GridColor["Color"]->newOne()->AlphaChange(-30);
		$SkippedTickColor = isset($Format["SkippedTickColor"]) ? ["Color" => $Format["SkippedTickColor"]] : ["Color" => $TickColor["Color"]->newOne()->AlphaChange(-80)];
		$SkippedInnerTickWidth = isset($Format["SkippedInnerTickWidth"]) ? $Format["SkippedInnerTickWidth"] : 0;
		$SkippedOuterTickWidth = isset($Format["SkippedOuterTickWidth"]) ? $Format["SkippedOuterTickWidth"] : 2;

		$SkippedAxisColor = ["Color" => $SkippedAxisColor, "Ticks" => $SkippedAxisTicks];
		$GridColor["Ticks"] = $GridTicks;

		/* Floating scale require X & Y margins to be set manually */
		($Floating && ($XMargin == AUTO || $YMargin == 0)) AND $Floating = FALSE;

		($DrawYLines == NONE || $DrawYLines == [NONE]) AND $DrawYLines = [];
		($DrawYLines == ALL) AND $DrawYLines = [ALL];

		$TicksNotZero = ($InnerTickWidth != 0 || $OuterTickWidth != 0);
		$SkippedTicksNotZero = ($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0);

		/* Check LabelRotation range */
		if (($LabelRotation < 0) || ($LabelRotation > 359)){
			throw pException::InvalidInput("drawScale: LabelRotation must be between 0 and 359");
		}

		$Data = $this->myData->getData();
		$Abscissa = $Data["Abscissa"];

		/* Unset the abscissa axis, needed if we display multiple charts on the same picture */
		if (!is_null($Abscissa)) {
			foreach($Data["Axis"] as $AxisID => $Parameters) {
				if ($Parameters["Identity"] == AXIS_X) {
					unset($Data["Axis"][$AxisID]);
				}
			}
		}

		/* Build the scale settings */
		$GotAbscissa = FALSE;
		foreach($Data["Axis"] as $AxisID => $AxisParameter) {
			if ($AxisParameter["Identity"] == AXIS_X) {
				$GotAbscissa = TRUE;
			}

			if ($Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_Y) {
				$Height = $this->GraphAreaYdiff - $YMargin * 2;
			} elseif ($Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_X) {
				$Height = $this->GraphAreaXdiff;
			} elseif ($Pos == SCALE_POS_TOPBOTTOM && $AxisParameter["Identity"] == AXIS_Y) {
				$Height = $this->GraphAreaXdiff - $YMargin * 2;;
			} else {
				$Height = $this->GraphAreaYdiff;
			}

			$AxisMin = PHP_INT_MAX;
			$AxisMax = OUT_OF_SIGHT;
			if ($Mode == SCALE_MODE_FLOATING || $Mode == SCALE_MODE_START0) {
				foreach($Data["Series"] as $SerieID => $SerieParameter) {
					if ($SerieParameter["Axis"] == $AxisID && $Data["Series"][$SerieID]["isDrawable"] && $Abscissa != $SerieID) {
						if (!is_numeric($Data["Series"][$SerieID]["Max"]) || !is_numeric($Data["Series"][$SerieID]["Min"])){
							throw pException::InvalidInput("Series ".$SerieID.": non-numeric input");
						}
						$AxisMax = max($AxisMax, $Data["Series"][$SerieID]["Max"]);
						$AxisMin = min($AxisMin, $Data["Series"][$SerieID]["Min"]);
					}
				}

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
					throw pException::InvalidInput("Manual scale boundaries not set.");
				}

			} elseif ($Mode == SCALE_MODE_ADDALL || $Mode == SCALE_MODE_ADDALL_START0) {

				$Series = [];
				foreach($Data["Series"] as $SerieID => $SerieParameter) {
					if ($SerieParameter["Axis"] == $AxisID && $SerieParameter["isDrawable"] && $Abscissa != $SerieID) {
						$Series[$SerieID] = count($Data["Series"][$SerieID]["Data"]);
					}
				}

				for ($ID = 0; $ID <= max($Series) - 1; $ID++) {
					$PointMin = 0;
					$PointMax = 0;
					foreach($Series as $SerieID => $ValuesCount) {
						if (isset($Data["Series"][$SerieID]["Data"][$ID]) && !is_null($Data["Series"][$SerieID]["Data"][$ID])) {
							$Value = $Data["Series"][$SerieID]["Data"][$ID];
							if ($Value > 0) {
								$PointMax += $Value;
							} else {
								$PointMin += $Value;
							}
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
		}

		/* Still no X axis */
		if ($GotAbscissa == FALSE) {
			if (!is_null($Abscissa)) {
				$Points = count($Data["Series"][$Abscissa]["Data"]);
			} else {
				$Points = 0;
				foreach($Data["Series"] as $SerieParameter) {
					if ($SerieParameter["isDrawable"]) {
						$Points = max($Points, count($SerieParameter["Data"]));
					}
				}
			}

			$AxisID = count($Data["Axis"]);
			$Data["Axis"][$AxisID]["Identity"] = AXIS_X;
			$Data["Axis"][$AxisID]["Position"] = ($Pos == SCALE_POS_LEFTRIGHT) ? AXIS_POSITION_BOTTOM : AXIS_POSITION_LEFT;
			
			/* Override Abscissa position */
			if ($Data["AbscissaProperties"]["Position"] != 0){
				$Data["Axis"][$AxisID]["Position"] = $Data["AbscissaProperties"]["Position"];
			}

			(!is_null($Data["AbscissaProperties"]["Name"])) AND $Data["Axis"][$AxisID]["Name"] = $Data["AbscissaProperties"]["Name"];

			if ($XMargin == AUTO) {
				$Height = ($Pos == SCALE_POS_LEFTRIGHT) ? $this->GraphAreaXdiff : $this->GraphAreaYdiff;
				$Data["Axis"][$AxisID]["Margin"] = ($Points == 1) ? ($Height / 2) : (($Height / $Points) / 2);
			} else {
				$Data["Axis"][$AxisID]["Margin"] = $XMargin;
			}

			$Data["Axis"][$AxisID]["Rows"] = $Points - 1;
		}

		$this->myData->saveData(["Orientation" => $Pos, "Axis" => $Data["Axis"], "YMargin" => $YMargin]);

		$AxisPos = ["L" => $this->GraphAreaX1, "R" => $this->GraphAreaX2, "T" => $this->GraphAreaY1, "B" => $this->GraphAreaY2];

		foreach($Data["Axis"] as $AxisID => $Parameters) {
			if (isset($Parameters["Color"])) {
				$ColorAxis = ["Color" => $Parameters["Color"]];
				$ColorTick = ["Color" => $Parameters["Color"]];
			} else {
				$ColorAxis = $AxisColor;
				$ColorTick = $TickColor;
			}

			$ColorAxis["FontName"] = $this->FontName;
			$ColorAxis["FontSize"] = $this->FontSize;

			$ColorAxisArrow = ["FillColor" => $ColorAxis['Color'],"Size" => $ArrowSize];
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
								$this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], $ColorAxis);
							} else {
								$FloatingOffset = 0;
								$this->drawLine($this->GraphAreaX1, $AxisPos["B"], $this->GraphAreaX2, $AxisPos["B"], $ColorAxis);
							}

							if ($DrawArrows) {
								$this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["B"], $ColorAxisArrow);
							}
						}

						$Width = $this->GraphAreaXdiff - $Parameters["Margin"] * 2;
						$Step = ($Parameters["Rows"] == 0) ? $Width : $Width / ($Parameters["Rows"]);
						$MaxBottom = $AxisPos["B"];

						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
							$YPos = $AxisPos["B"];
							if (!is_null($Abscissa)) {
								if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
									$Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["AbscissaProperties"]);
								} else {
									$Value = "";
								}
							} else {
								if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
									$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["AbscissaProperties"]);
								} else {
									$Value = $i;
								}
							}

							$ID++;
							$Skipped = TRUE;
							if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
								$Bounds = $this->drawText($XPos, $YPos + $OuterTickWidth + $YLabelOffset, $Value, ["Angle" => $LabelRotation,"Align" => $LabelAlign] + $ColorAxis);
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

								if ($SkippedTicksNotZero && !$RemoveXAxis && !$RemoveSkippedAxis) {
									$this->drawLine($XPos, $YPos - $SkippedInnerTickWidth, $XPos, $YPos + $SkippedOuterTickWidth, $SkippedTickColor);
								}
							} else {
								if ($DrawXLines && ($XPos != $this->GraphAreaX1 && $XPos != $this->GraphAreaX2)) {
									$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $GridColor);
								}

								if ($TicksNotZero && !$RemoveXAxis) {
									$this->drawLine($XPos, $YPos - $InnerTickWidth, $XPos, $YPos + $OuterTickWidth, $ColorTick);
								}
							}
						}

						if (isset($Parameters["Name"]) && !$RemoveXAxis) {
							$YPos = $MaxBottom + 2;
							$XPos = $this->GraphAreaX1 + ($this->GraphAreaXdiff) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE] + $ColorAxis);
							$MaxBottom = $Bounds[0]["Y"];
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
								$this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], $ColorAxis);
							} else {
								$FloatingOffset = 0;
								$this->drawLine($this->GraphAreaX1, $AxisPos["T"], $this->GraphAreaX2, $AxisPos["T"], $ColorAxis);
							}

							if ($DrawArrows) {
								$this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["T"], $ColorAxisArrow);
							}
						}

						$Width = $this->GraphAreaXdiff - $Parameters["Margin"] * 2;
						$Step = ($Parameters["Rows"] == 0) ? $Width : $Width / $Parameters["Rows"];
						$MinTop = $AxisPos["T"];

						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
							$YPos = $AxisPos["T"];
							if (!is_null($Abscissa)) {
								if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
									$Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["AbscissaProperties"]);
								} else {
									$Value = "";
								}
							} else {
								if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
									$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["AbscissaProperties"]);
								} else {
									$Value = $i;
								}
							}

							$ID++;
							$Skipped = TRUE;
							if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
								$Bounds = $this->drawText($XPos, $YPos - $OuterTickWidth - $YLabelOffset, $Value, ["Angle" => $LabelRotation,"Align" => $LabelAlign] + $ColorAxis);
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

								if ($SkippedTicksNotZero && !$RemoveXAxis && !$RemoveSkippedAxis) {
									$this->drawLine($XPos, $YPos + $SkippedInnerTickWidth, $XPos, $YPos - $SkippedOuterTickWidth, $SkippedTickColor);
								}
							} else {
								if ($DrawXLines) {
									$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $GridColor);
								}

								if ($TicksNotZero && !$RemoveXAxis) {
									$this->drawLine($XPos, $YPos + $InnerTickWidth, $XPos, $YPos - $OuterTickWidth, $ColorTick);
								}
							}
						}

						if (isset($Parameters["Name"]) && !$RemoveXAxis) {
							$YPos = $MinTop - 2;
							$XPos = $this->GraphAreaX1 + $this->GraphAreaXdiff / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE] + $ColorAxis);
							$MinTop = $Bounds[2]["Y"];
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
								$this->drawLine($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], $ColorAxis);
							} else {
								$FloatingOffset = 0;
								$this->drawLine($AxisPos["L"], $this->GraphAreaY1, $AxisPos["L"], $this->GraphAreaY2, $ColorAxis);
							}

							if ($DrawArrows) {
								$this->drawArrow($AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 + ($ArrowSize * 2), $ColorAxisArrow);
							}
						}

						$Height = $this->GraphAreaYdiff - $Parameters["Margin"] * 2;
						$Step = ($Parameters["Rows"] == 0) ? $Height :  $Height / $Parameters["Rows"];
						$MinLeft = $AxisPos["L"];

						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$YPos = $this->GraphAreaY1 + $Parameters["Margin"] + $Step * $i;
							$XPos = $AxisPos["L"];
							if (!is_null($Abscissa)) {
								if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
									$Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["AbscissaProperties"]);
								} else {
									$Value = "";
								}
							} else {
								if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
									$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["AbscissaProperties"]);
								} else {
									$Value = strval($i);
								}
							}

							$ID++;
							$Skipped = TRUE;
							if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
								$Bounds = $this->drawText($XPos - $OuterTickWidth + $XLabelOffset, $YPos, $Value, ["Angle" => $LabelRotation,"Align" => $LabelAlign] + $ColorAxis);
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

								if ($SkippedTicksNotZero && !$RemoveXAxis && !$RemoveSkippedAxis) {
									$this->drawLine($XPos - $SkippedOuterTickWidth, $YPos, $XPos + $SkippedInnerTickWidth, $YPos, $SkippedTickColor);
								}
							} else {
								if ($DrawXLines && ($YPos != $this->GraphAreaY1 && $YPos != $this->GraphAreaY2)) {
									$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $GridColor);
								}

								if ($TicksNotZero && !$RemoveXAxis) {
									$this->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, $ColorTick);
								}
							}
						}

						if (isset($Parameters["Name"]) && !$RemoveXAxis) {
							$XPos = $MinLeft - 2;
							$YPos = $this->GraphAreaY1 + ($this->GraphAreaY2 - $this->GraphAreaY1) / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 90] + $ColorAxis);
							$MinLeft = $Bounds[0]["X"];
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
								$this->drawLine($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], $ColorAxis);
							} else {
								$FloatingOffset = 0;
								$this->drawLine($AxisPos["R"], $this->GraphAreaY1, $AxisPos["R"], $this->GraphAreaY2, $ColorAxis);
							}

							if ($DrawArrows) {
								$this->drawArrow($AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 + ($ArrowSize * 2), $ColorAxisArrow);
							}
						}

						$Height = $this->GraphAreaYdiff - $Parameters["Margin"] * 2;
						$Step = ($Parameters["Rows"] == 0) ? $Height : $Height / $Parameters["Rows"];
						$MaxRight = $AxisPos["R"];

						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$YPos = $this->GraphAreaY1 + $Parameters["Margin"] + $Step * $i;
							$XPos = $AxisPos["R"];
							if (!is_null($Abscissa)) {
								if (isset($Data["Series"][$Abscissa]["Data"][$i])) {
									$Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i], $Data["AbscissaProperties"]);
								} else {
									$Value = "";
								}
							} else {
								if (isset($Parameters["ScaleMin"]) && isset($Parameters["RowHeight"])) {
									$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Data["AbscissaProperties"]);
								} else {
									$Value = strval($i);
								}
							}

							$ID++;
							$Skipped = TRUE;
							if ($this->isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip) && !$RemoveXAxis) {
								$Bounds = $this->drawText($XPos + $OuterTickWidth + $XLabelOffset, $YPos, $Value, ["Angle" => $LabelRotation,"Align" => $LabelAlign] + $ColorAxis);
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

								if ($SkippedTicksNotZero && !$RemoveXAxis && !$RemoveSkippedAxis) {
									$this->drawLine($XPos + $SkippedOuterTickWidth, $YPos, $XPos - $SkippedInnerTickWidth, $YPos, $SkippedTickColor);
								}
							} else {
								if ($DrawXLines) {
									$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $GridColor);
								}

								if ($TicksNotZero && !$RemoveXAxis) {
									$this->drawLine($XPos + $OuterTickWidth, $YPos, $XPos - $InnerTickWidth, $YPos, $ColorTick);
								}
							}
						}

						if (isset($Parameters["Name"]) && !$RemoveXAxis) {
							$XPos = $MaxRight + 4;
							$YPos = $this->GraphAreaY1 + $this->GraphAreaYdiff / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 270] + $ColorAxis);
							$MaxRight = $Bounds[1]["X"];
						}

						$AxisPos["R"] = $MaxRight + $ScaleSpacing;
					}
				}

			} elseif ($Parameters["Identity"] == AXIS_Y) {

				if ($Pos == SCALE_POS_LEFTRIGHT) {
					if ($Parameters["Position"] == AXIS_POSITION_LEFT) {

						if ($Floating) {
							$FloatingOffset = $XMargin;
							if (!$RemoveYAxis){
								$this->drawLine($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY2 - $Parameters["Margin"], $ColorAxis);
							}
						} else {
							$FloatingOffset = 0;
							if (!$RemoveYAxis){
								$this->drawLine($AxisPos["L"], $this->GraphAreaY1, $AxisPos["L"], $this->GraphAreaY2, $ColorAxis);
							}
						}

						if ($DrawArrows) {
							$this->drawArrow($AxisPos["L"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["L"], $this->GraphAreaY1 - ($ArrowSize * 2), $ColorAxisArrow);
						}

						$Height = $this->GraphAreaYdiff - $Parameters["Margin"] * 2;
						$Step = $Height / $Parameters["Rows"];
						$SubTicksSize = $Step / 2;
						$MinLeft = $AxisPos["L"];
						$LastY = NULL;
						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$YPos = $this->GraphAreaY2 - $Parameters["Margin"] - $Step * $i;
							$XPos = $AxisPos["L"];
							$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters);
							$BGColor = ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2;

							if (!is_null($LastY) && $CycleBackground && ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines))) {
								$this->drawFilledRectangle($this->GraphAreaX1 + $FloatingOffset, $LastY, $this->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
							}

							if ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines)) {
								$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $GridColor);
							}

							if ($DrawSubTicks && $i != $Parameters["Rows"]) {
								$this->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, $SubTickColor);
							}

							if ($TicksNotZero) {
								$this->drawLine($XPos - $OuterTickWidth, $YPos, $XPos + $InnerTickWidth, $YPos, $ColorTick);
							}
							$Bounds = $this->drawText($XPos - $OuterTickWidth - 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLERIGHT] + $ColorAxis);
							$TxtLeft = $XPos - $OuterTickWidth - 2 - ($Bounds[1]["X"] - $Bounds[0]["X"]);
							$MinLeft = min($MinLeft, $TxtLeft);
							$LastY = $YPos;
						}

						if (isset($Parameters["Name"])) {
							$XPos = $MinLeft - 2;
							$YPos = $this->GraphAreaY1 + $this->GraphAreaYdiff / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 90] + $ColorAxis);
							$MinLeft = $Bounds[2]["X"];
						}

						$AxisPos["L"] = $MinLeft - $ScaleSpacing;

					} elseif ($Parameters["Position"] == AXIS_POSITION_RIGHT) {

						if ($Floating) {
							$FloatingOffset = $XMargin;
							$this->drawLine($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY2 - $Parameters["Margin"], $ColorAxis);
						} else {
							$FloatingOffset = 0;
							$this->drawLine($AxisPos["R"], $this->GraphAreaY1, $AxisPos["R"], $this->GraphAreaY2, $ColorAxis);
						}

						if ($DrawArrows) {
							$this->drawArrow($AxisPos["R"], $this->GraphAreaY1 + $Parameters["Margin"], $AxisPos["R"], $this->GraphAreaY1 - ($ArrowSize * 2), $ColorAxisArrow);
						}

						$Height = $this->GraphAreaYdiff - $Parameters["Margin"] * 2;
						$Step = $Height / $Parameters["Rows"];
						$SubTicksSize = $Step / 2;
						$MaxLeft = $AxisPos["R"];
						$LastY = NULL;
						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$YPos = $this->GraphAreaY2 - $Parameters["Margin"] - $Step * $i;
							$XPos = $AxisPos["R"];
							$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters);
							$BGColor = ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2;

							if (!is_null($LastY) && $CycleBackground && ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines))) {
								$this->drawFilledRectangle($this->GraphAreaX1 + $FloatingOffset, $LastY, $this->GraphAreaX2 - $FloatingOffset, $YPos, $BGColor);
							}

							if ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines)) {
								$this->drawLine($this->GraphAreaX1 + $FloatingOffset, $YPos, $this->GraphAreaX2 - $FloatingOffset, $YPos, $GridColor);
							}

							if ($DrawSubTicks && $i != $Parameters["Rows"]) {
								$this->drawLine($XPos - $OuterSubTickWidth, $YPos - $SubTicksSize, $XPos + $InnerSubTickWidth, $YPos - $SubTicksSize, $SubTickColor);
							}

							if ($TicksNotZero) {
								$this->drawLine($XPos - $InnerTickWidth, $YPos, $XPos + $OuterTickWidth, $YPos, $ColorTick);
							}
							$Bounds = $this->drawText($XPos + $OuterTickWidth + 2, $YPos, $Value, ["Align" => TEXT_ALIGN_MIDDLELEFT] + $ColorAxis);
							$TxtLeft = $XPos + $OuterTickWidth + 2 + ($Bounds[1]["X"] - $Bounds[0]["X"]);
							$MaxLeft = max($MaxLeft, $TxtLeft);
							$LastY = $YPos;
						}

						if (isset($Parameters["Name"])) {
							$XPos = $MaxLeft + 6;
							$YPos = $this->GraphAreaY1 + $this->GraphAreaYdiff / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE,"Angle" => 270] + $ColorAxis);
							$MaxLeft = $Bounds[2]["X"];
						}

						$AxisPos["R"] = $MaxLeft + $ScaleSpacing;
					}

				} elseif ($Pos == SCALE_POS_TOPBOTTOM) {

					if ($Parameters["Position"] == AXIS_POSITION_TOP) {
						if ($Floating) {
							$FloatingOffset = $XMargin;
							$this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], $ColorAxis);
						} else {
							$FloatingOffset = 0;
							$this->drawLine($this->GraphAreaX1, $AxisPos["T"], $this->GraphAreaX2, $AxisPos["T"], $ColorAxis);
						}

						if ($DrawArrows) {
							$this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["T"], $this->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["T"], $ColorAxisArrow);
						}

						$Width = $this->GraphAreaXdiff - $Parameters["Margin"] * 2;
						$Step = $Width / $Parameters["Rows"];
						$SubTicksSize = $Step / 2;
						$MinTop = $AxisPos["T"];
						$LastX = NULL;
						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
							$YPos = $AxisPos["T"];
							$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters);
							$BGColor = ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2;

							if (!is_null($LastX) && $CycleBackground && ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines))) {
								$this->drawFilledRectangle($LastX, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $BGColor);
							}

							if ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines)) {
								$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $GridColor);
							}

							if ($DrawSubTicks && $i != $Parameters["Rows"]) {
								$this->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, $SubTickColor);
							}

							if ($TicksNotZero) {
								$this->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, $ColorTick);
							}
							$Bounds = $this->drawText($XPos, $YPos - $OuterTickWidth - 2, $Value, ["Align" => TEXT_ALIGN_BOTTOMMIDDLE] + $ColorAxis);
							$TxtHeight = $YPos - $OuterTickWidth - 2 - ($Bounds[1]["Y"] - $Bounds[2]["Y"]);
							$MinTop = min($MinTop, $TxtHeight);
							$LastX = $XPos;
						}

						if (isset($Parameters["Name"])) {
							$YPos = $MinTop - 2;
							$XPos = $this->GraphAreaX1 + $this->GraphAreaXdiff / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_BOTTOMMIDDLE] + $ColorAxis);
							$MinTop = $Bounds[2]["Y"];
						}

						$AxisPos["T"] = $MinTop - $ScaleSpacing;

					} elseif ($Parameters["Position"] == AXIS_POSITION_BOTTOM) {
						if ($Floating) {
							$FloatingOffset = $XMargin;
							$this->drawLine($this->GraphAreaX1 + $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], $ColorAxis);
						} else {
							$FloatingOffset = 0;
							$this->drawLine($this->GraphAreaX1, $AxisPos["B"], $this->GraphAreaX2, $AxisPos["B"] ,$ColorAxis);
						}

						if ($DrawArrows) {
							$this->drawArrow($this->GraphAreaX2 - $Parameters["Margin"], $AxisPos["B"], $this->GraphAreaX2 + ($ArrowSize * 2), $AxisPos["B"], $ColorAxisArrow);
						}

						$Width = $this->GraphAreaXdiff - $Parameters["Margin"] * 2;
						$Step = $Width / $Parameters["Rows"];
						$SubTicksSize = $Step / 2;
						$MaxBottom = $AxisPos["B"];
						$LastX = NULL;
						for ($i = 0; $i <= $Parameters["Rows"]; $i++) {
							$XPos = $this->GraphAreaX1 + $Parameters["Margin"] + $Step * $i;
							$YPos = $AxisPos["B"];
							$Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"] * $i, $Parameters);
							$BGColor = ($i % 2 == 1) ? $BackgroundColor1 : $BackgroundColor2;

							if (!is_null($LastX) && $CycleBackground && ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines))) {
								$this->drawFilledRectangle($LastX, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $BGColor);
							}

							if ($DrawYLines == [ALL] || in_array($AxisID, $DrawYLines)) {
								$this->drawLine($XPos, $this->GraphAreaY1 + $FloatingOffset, $XPos, $this->GraphAreaY2 - $FloatingOffset, $GridColor);
							}

							if ($DrawSubTicks && $i != $Parameters["Rows"]) {
								$this->drawLine($XPos + $SubTicksSize, $YPos - $OuterSubTickWidth, $XPos + $SubTicksSize, $YPos + $InnerSubTickWidth, $SubTickColor);
							}

							if ($TicksNotZero) {
								$this->drawLine($XPos, $YPos - $OuterTickWidth, $XPos, $YPos + $InnerTickWidth, $ColorTick);
							}
							$Bounds = $this->drawText($XPos, $YPos + $OuterTickWidth + 2, $Value, ["Align" => TEXT_ALIGN_TOPMIDDLE] + $ColorAxis);
							$TxtHeight = $YPos + $OuterTickWidth + 2 + ($Bounds[1]["Y"] - $Bounds[2]["Y"]);
							$MaxBottom = max($MaxBottom, $TxtHeight);
							$LastX = $XPos;
						}

						if (isset($Parameters["Name"])) {
							$YPos = $MaxBottom + 2;
							$XPos = $this->GraphAreaX1 + $this->GraphAreaXdiff / 2;
							$Bounds = $this->drawText($XPos, $YPos, $Parameters["Name"], ["Align" => TEXT_ALIGN_TOPMIDDLE] + $ColorAxis);
							$MaxBottom = $Bounds[0]["Y"];
						}

						$AxisPos["B"] = $MaxBottom + $ScaleSpacing;
					}
				}
			}
		}
	}

	private function isValidLabel($Value, $LastValue, $LabelingMethod, $ID, $LabelSkip)
	{
		$ret = TRUE;

		switch($LabelingMethod){
			case LABELING_DIFFERENT:
				$ret = ($Value != $LastValue);
				break;
			case LABELING_ALL:
				if ($LabelSkip != 0){
					$ret = (($ID + $LabelSkip) % ($LabelSkip + 1) == 1);
				}
				break;
		}

		return $ret;
	}

	/* Compute the scale, check for the best visual factors */
	public function computeScale($XMin, $XMax, $MaxDivs, array $Factors, $AxisID = 0)
	{
		$Results = [];
		$GoodScaleFactors = [];

		/* Compute each factors */
		foreach($Factors as $Factor) {
			$Results[$Factor] = $this->processScale($XMin, $XMax, $MaxDivs, [$Factor], $AxisID);
		}

		/* Remove scales that are creating to much decimals */
		foreach($Results as $Key => $Result) {
			if (($Result["RowHeight"] - floor($Result["RowHeight"])) < .6) {
				$GoodScaleFactors[] = $Key;
			}
		}

		/* Found no correct scale, shame,... returns the 1st one as default */
		if (empty($GoodScaleFactors)) {
			return $Results[$Factors[0]];
		}

		/* Find the factor that cause the maximum number of Rows */
		$MaxRows = 0;
		$BestFactor = 0;
		foreach($GoodScaleFactors as $Factor) {
			if ($Results[$Factor]["Rows"] > $MaxRows) {
				$MaxRows = $Results[$Factor]["Rows"];
				$BestFactor = $Factor;
			}
		}

		/* Return the best visual scale */
		return $Results[$BestFactor];
	}

	/* Compute the best matching scale based on size & factors */
	private function processScale($XMin, $XMax, $MaxDivs, array $Factors, $AxisID)
	{
		$XMin = intval($XMin);
		$XMax = intval($XMax);

		$Scale = [
			"Rows" => 2,
			"RowHeight" => 1,
			"XMin" => $XMax - 1,
			"XMax" => $XMax + 1
		];

		if ($XMin == $XMax) {
			return $Scale;
		}

		$Data = $this->myData->getData();

		$ScaleHeight = abs(ceil($XMax) - floor($XMin));
		$Format = (isset($Data["Axis"][$AxisID]["Format"])) ?  $Data["Axis"][$AxisID]["Format"] : NULL;
		$Mode = (isset($Data["Axis"][$AxisID]["Display"])) ? $Data["Axis"][$AxisID]["Display"] : AXIS_FORMAT_DEFAULT;

		$Found = FALSE;
		$Rescaled = FALSE;
		$Scaled10Factor = .0001;
		$Result = 0;
		while (!$Found) {
			foreach($Factors as $Factor) {
				if ($Factor == 0){
					continue;
				}

				$R = $Factor * $Scaled10Factor;
				if ($R > PHP_INT_MAX){
					break 2;
				}

				if (floor($R) != 0){
					$XMinRescaled = ((($XMin % $R) != 0) || ($XMin != floor($XMin))) ? (floor($XMin / $R) * $R) : $XMin;
					$XMaxRescaled = ((($XMax % $R) != 0) || ($XMax != floor($XMax))) ? (floor($XMax / $R) * $R + $R) : $XMax;
				} else {
					$XMinRescaled = floor($XMin / $R) * $R;
					$XMaxRescaled = floor($XMax / $R) * $R + $R;
				}

				$ScaleHeightRescaled = abs($XMaxRescaled - $XMinRescaled);

				if (floor($ScaleHeightRescaled / $R) <= $MaxDivs) {
					$Found = TRUE;
					$Rescaled = TRUE;
					$Result = $R;
				}

				$Scaled10Factor *= 10;
			}
		}

		/* ReCall Min / Max / Height */
		if ($Rescaled) {
			$XMin = $XMinRescaled;
			$XMax = $XMaxRescaled;
			$ScaleHeight = $ScaleHeightRescaled;
		}

		/* Compute rows size */
		if ($Result == 0) {
			$Rows = 0;
		} else {
			$Rows = floor($ScaleHeight / $Result);
		}
		($Rows == 0) AND $Rows = 1;
		$RowHeight = $ScaleHeight / $Rows;

		/* Return the results */
		$Scale["Rows"] = $Rows;
		$Scale["RowHeight"] = $RowHeight;
		$Scale["XMin"] = $XMin;
		$Scale["XMax"] = $XMax;
		/* Compute the needed decimals for the metric view to avoid repetition of the same X Axis labels */
		if ($Mode == AXIS_FORMAT_METRIC && is_null($Format)) {

			$GoodDecimals = 0;
			for ($Decimals = 0; $Decimals <= 10; $Decimals++) {
				$LastLabel = "zob";
				$ScaleOK = TRUE;
				for ($i = 0; $i <= $Rows; $i++) {
					$Label = $this->scaleFormat(($XMin + $i * $RowHeight), ["Display" => $Mode, "Format" => NULL, "Unit" => $Decimals]);
					($LastLabel == $Label) AND $ScaleOK = FALSE;
					$LastLabel = $Label;
				}

				if ($ScaleOK) {
					$GoodDecimals = $Decimals;
					break;
				}
			}

			$Scale["Format"] = $GoodDecimals;
		}

		return $Scale;
	}

	/* Draw an X threshold */
	public function drawXThreshold(array $Values, array $Format = [])
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(255,0,0,50);
		$Weight = isset($Format["Weight"]) ? $Format["Weight"] : NULL;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 6;
		$Wide = isset($Format["Wide"]) ? $Format["Wide"] : FALSE;
		$WideFactor = isset($Format["WideFactor"]) ? $Format["WideFactor"] : 5;
		$WriteCaption = isset($Format["WriteCaption"]) ? $Format["WriteCaption"] : FALSE;
		$Caption = isset($Format["Caption"]) ? $Format["Caption"] : NULL;
		$CaptionAlign = isset($Format["CaptionAlign"]) ? $Format["CaptionAlign"] : CAPTION_LEFT_TOP;
		$CaptionOffset = isset($Format["CaptionOffset"]) ? $Format["CaptionOffset"] : 5;
		$CaptionColor = isset($Format["CaptionColor"]) ? $Format["CaptionColor"] : new pColor(255);
		$DrawBox = isset($Format["DrawBox"]) ? $Format["DrawBox"] : TRUE;
		$DrawBoxBorder = isset($Format["DrawBoxBorder"]) ? $Format["DrawBoxBorder"] : FALSE;
		$BorderOffset = isset($Format["BorderOffset"]) ? $Format["BorderOffset"] : 3;
		$BoxRounded = isset($Format["BoxRounded"]) ? $Format["BoxRounded"] : TRUE;
		$RoundedRadius = isset($Format["RoundedRadius"]) ? $Format["RoundedRadius"] : 3;
		$BoxColor = isset($Format["BoxColor"]) ? $Format["BoxColor"] : new pColor(0,0,0,30);
		$BoxSurrounding = isset($Format["BoxSurrounding"]) ? $Format["BoxSurrounding"] : 0;
		$BoxBorderColor = isset($Format["BoxBorderColor"]) ? $Format["BoxBorderColor"] : new pColor(255);
		$ValueIsLabel = isset($Format["ValueIsLabel"]) ? $Format["ValueIsLabel"] : FALSE;

		$AbscissaMargin = $this->myData->getAbscissaMargin();
		$XScale = $this->myData->scaleGetXSettings();
		$Data = $this->myData->getData();

		$CaptionSettings = [
			"DrawBox" => $DrawBox,
			"DrawBoxBorder" => $DrawBoxBorder,
			"BorderOffset" => $BorderOffset,
			"BoxRounded" => $BoxRounded,
			"RoundedRadius" => $RoundedRadius,
			"BoxColor" => $BoxColor,
			"BoxSurrounding" => $BoxSurrounding,
			"BoxBorderColor" => $BoxBorderColor, # Momchil: must match drawThreshold
			"Color" => $CaptionColor
		];

		$WideColor = $Color->newOne()->AlphaSlash($WideFactor);
		$wLineSettings = ["Color" => $WideColor,"Ticks" => $Ticks];

		foreach($Values as $Value){

			if ($ValueIsLabel) {
				$Format["ValueIsLabel"] = FALSE;
				foreach($Data["Series"][$Data["Abscissa"]]["Data"] as $Key => $SerieValue) {
					if ($SerieValue == $Value) {
						$this->drawXThreshold([$Key], $Format);
					}
				}
				return;
			}

			if (is_null($Caption)) {
				if (!is_null($Data["Abscissa"])) {
					$Caption = (isset($Data["Series"][$Data["Abscissa"]]["Data"][$Value])) ? $Data["Series"][$Data["Abscissa"]]["Data"][$Value] : $Value;
				} else {
					$Caption = $Value;
				}
			}

			if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
				$XStep = ($this->GraphAreaXdiff - $XScale[0] * 2) / $XScale[1];
				$XPos = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value;
				$YPos1 = $this->GraphAreaY1 + $Data["YMargin"];
				$YPos2 = $this->GraphAreaY2 - $Data["YMargin"];
				if ($XPos >= $this->GraphAreaX1 + $AbscissaMargin && $XPos <= $this->GraphAreaX2 - $AbscissaMargin) {
					$this->drawLine($XPos, $YPos1, $XPos, $YPos2, ["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
					if ($Wide) {
						$this->drawLine($XPos - 1, $YPos1, $XPos - 1, $YPos2, $wLineSettings);
						$this->drawLine($XPos + 1, $YPos1, $XPos + 1, $YPos2, $wLineSettings);
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
				}

			} elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
				$XStep = ($this->GraphAreaYdiff - $XScale[0] * 2) / $XScale[1];
				$XPos = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value;
				$YPos1 = $this->GraphAreaX1 + $Data["YMargin"];
				$YPos2 = $this->GraphAreaX2 - $Data["YMargin"];
				if ($XPos >= $this->GraphAreaY1 + $AbscissaMargin && $XPos <= $this->GraphAreaY2 - $AbscissaMargin) {
					$this->drawLine($YPos1, $XPos, $YPos2, $XPos, ["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
					if ($Wide) {
						$this->drawLine($YPos1, $XPos - 1, $YPos2, $XPos - 1, $wLineSettings);
						$this->drawLine($YPos1, $XPos + 1, $YPos2, $XPos + 1, $wLineSettings);
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
				}
			}

		} # foreach

	}

	/* Draw an X threshold area */
	public function drawXThresholdArea($Value1, $Value2, array $Format = []) 
	{
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(255,0,0,20);
		$Border = isset($Format["Border"]) ? $Format["Border"] : TRUE;
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : NULL;
		$BorderTicks = isset($Format["BorderTicks"]) ? $Format["BorderTicks"] : 2;
		$AreaName = isset($Format["AreaName"]) ? $Format["AreaName"] : NULL;
		$NameAngle = isset($Format["NameAngle"]) ? $Format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
		$NameColor = isset($Format["NameColor"]) ? $Format["NameColor"] : new pColor(255);
		$DisableShadowOnArea = isset($Format["DisableShadowOnArea"]) ? $Format["DisableShadowOnArea"] : TRUE;

		if (is_null($BorderColor)){
			$BorderColor = $Color->newOne()->AlphaChange(20);
		}

		$RestoreShadow = $this->Shadow;
		($DisableShadowOnArea && $this->Shadow) AND $this->Shadow = FALSE;
		$XScale = $this->myData->scaleGetXSettings();
		$Data = $this->myData->getData();
		
		$bLineSettings = ["Color" => $BorderColor,"Ticks" => $BorderTicks];

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$XStep = ($this->GraphAreaXdiff - $XScale[0] * 2) / $XScale[1];
			$XPos1 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value1;
			$XPos2 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value2;
			$YPos1 = $this->GraphAreaY1 + $Data["YMargin"];
			$YPos2 = $this->GraphAreaY2 - $Data["YMargin"];
			($XPos1 < $this->GraphAreaX1 + $XScale[0]) AND $XPos1 = $this->GraphAreaX1 + $XScale[0];
			($XPos1 > $this->GraphAreaX2 - $XScale[0]) AND $XPos1 = $this->GraphAreaX2 - $XScale[0];
			($XPos2 < $this->GraphAreaX1 + $XScale[0]) AND $XPos2 = $this->GraphAreaX1 + $XScale[0];
			($XPos2 > $this->GraphAreaX2 - $XScale[0]) AND $XPos2 = $this->GraphAreaX2 - $XScale[0];

			$this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["Color" => $Color]);
			if ($Border) {
				$this->drawLine($XPos1, $YPos1, $XPos1, $YPos2, $bLineSettings);
				$this->drawLine($XPos2, $YPos1, $XPos2, $YPos2, $bLineSettings);
			}

			if (!is_null($AreaName)) {
				$XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
				$YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
				if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
					$TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $AreaName);
					$TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
					$NameAngle = (abs($XPos2 - $XPos1) > $TxtWidth) ? 0 : 90; 
				}

				$this->Shadow = $RestoreShadow;
				$this->drawText($XPos, $YPos, $AreaName, ["Color" => $NameColor,"Angle" => $NameAngle,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
			}

		} elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
			$XStep = ($this->GraphAreaYdiff - $XScale[0] * 2) / $XScale[1];
			$XPos1 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value1;
			$XPos2 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value2;
			$YPos1 = $this->GraphAreaX1 + $Data["YMargin"];
			$YPos2 = $this->GraphAreaX2 - $Data["YMargin"];
			($XPos1 < $this->GraphAreaY1 + $XScale[0]) AND $XPos1 = $this->GraphAreaY1 + $XScale[0];
			($XPos1 > $this->GraphAreaY2 - $XScale[0]) AND $XPos1 = $this->GraphAreaY2 - $XScale[0];
			($XPos2 < $this->GraphAreaY1 + $XScale[0]) AND $XPos2 = $this->GraphAreaY1 + $XScale[0];
			($XPos2 > $this->GraphAreaY2 - $XScale[0]) AND $XPos2 = $this->GraphAreaY2 - $XScale[0];

			$this->drawFilledRectangle($YPos1, $XPos1, $YPos2, $XPos2, ["Color" => $Color]);
			if ($Border) {
				$this->drawLine($YPos1, $XPos1, $YPos2, $XPos1, $bLineSettings);
				$this->drawLine($YPos1, $XPos2, $YPos2, $XPos2, $bLineSettings);
			}

			if (!is_null($AreaName)) {
				$XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
				$YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
				$this->Shadow = $RestoreShadow;
				$this->drawText($YPos, $XPos, $AreaName, ["Color" => $NameColor,"Angle" => 0,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Draw an Y threshold with the computed scale */
	public function drawThreshold(array $Values, array $Format = [])
	{
		$AxisID = isset($Format["AxisID"]) ? $Format["AxisID"] : 0;
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(255,0,0,20);
		$Weight = isset($Format["Weight"]) ? $Format["Weight"] : NULL;
		$Ticks = isset($Format["Ticks"]) ? $Format["Ticks"] : 6;
		$Wide = isset($Format["Wide"]) ? $Format["Wide"] : FALSE;
		$WideFactor = isset($Format["WideFactor"]) ? $Format["WideFactor"] : 5;
		$WriteCaption = isset($Format["WriteCaption"]) ? $Format["WriteCaption"] : FALSE;
		$Caption = isset($Format["Caption"]) ? $Format["Caption"] : NULL;
		$CaptionAlign = isset($Format["CaptionAlign"]) ? $Format["CaptionAlign"] : CAPTION_LEFT_TOP;
		$CaptionOffset = isset($Format["CaptionOffset"]) ? $Format["CaptionOffset"] : 10;
		$CaptionColor = isset($Format["CaptionColor"]) ? $Format["CaptionColor"] : new pColor(255);
		$DrawBox = isset($Format["DrawBox"]) ? $Format["DrawBox"] : TRUE;
		$DrawBoxBorder = isset($Format["DrawBoxBorder"]) ? $Format["DrawBoxBorder"] : FALSE;
		$BorderOffset = isset($Format["BorderOffset"]) ? $Format["BorderOffset"] : 5;
		$BoxRounded = isset($Format["BoxRounded"]) ? $Format["BoxRounded"] : TRUE;
		$RoundedRadius = isset($Format["RoundedRadius"]) ? $Format["RoundedRadius"] : 3;
		$BoxColor = isset($Format["BoxColor"]) ? $Format["BoxColor"] : new pColor(0,0,0,20);
		$BoxSurrounding = isset($Format["BoxSurrounding"]) ? $Format["BoxSurrounding"] : 0;
		$BoxBorderColor = isset($Format["BoxBorderColor"]) ? $Format["BoxBorderColor"] : new pColor(255);
		$NoMargin = isset($Format["NoMargin"]) ? $Format["NoMargin"] : FALSE;

		$WideColor = $Color->newOne()->AlphaSlash($WideFactor);

		$Data = $this->myData->getData();

		if (!isset($Data["Axis"][$AxisID])) {
			throw pException::InvalidInput("Axis ID is invalid");
		}

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

		$AbscissaMargin = $this->myData->getAbscissaMargin();
		($NoMargin) AND $AbscissaMargin = 0;
		$wLineSettings = ["Color" => $WideColor,"Ticks" => $Ticks];

		foreach ($Values as $Value){
			(is_null($Caption)) AND $Caption = $Value;

			if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
				$YPos = $this->scaleComputeYSingle($Value, $AxisID);
				if ($YPos >= $this->GraphAreaY1 + $Data["Axis"][$AxisID]["Margin"] && $YPos <= $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"]) {
					$X1 = $this->GraphAreaX1 + $AbscissaMargin;
					$X2 = $this->GraphAreaX2 - $AbscissaMargin;
					$this->drawLine($X1, $YPos, $X2, $YPos, ["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
					if ($Wide) {
						$this->drawLine($X1, $YPos - 1, $X2, $YPos - 1, $wLineSettings);
						$this->drawLine($X1, $YPos + 1, $X2, $YPos + 1, $wLineSettings);
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

			}

			if ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {
				$XPos = $this->scaleComputeYSingle($Value, $AxisID);
				if ($XPos >= $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] && $XPos <= $this->GraphAreaX2 - $Data["Axis"][$AxisID]["Margin"]) {
					$Y1 = $this->GraphAreaY1 + $AbscissaMargin;
					$Y2 = $this->GraphAreaY2 - $AbscissaMargin;
					$this->drawLine($XPos, $Y1, $XPos, $Y2,["Color" => $Color,"Ticks" => $Ticks,"Weight" => $Weight]);
					if ($Wide) {
						$this->drawLine($XPos - 1, $Y1, $XPos - 1, $Y2, $wLineSettings);
						$this->drawLine($XPos + 1, $Y1, $XPos + 1, $Y2, $wLineSettings);
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
			}

		} # foreach
	}

	/* Draw a threshold with the computed scale */
	public function drawThresholdArea($Value1, $Value2, array $Format = [])
	{
		$AxisID = isset($Format["AxisID"]) ? $Format["AxisID"] : 0;
		$Color = isset($Format["Color"]) ? $Format["Color"] : new pColor(255,0,0,20);
		$Border = isset($Format["Border"]) ? $Format["Border"] : TRUE;
		$BorderColor = isset($Format["BorderColor"]) ? $Format["BorderColor"] : $Color->newOne()->AlphaChange(20);
		$BorderTicks = isset($Format["BorderTicks"]) ? $Format["BorderTicks"] : 2;
		$AreaName = isset($Format["AreaName"]) ? $Format["AreaName"] : NULL;
		$NameAngle = isset($Format["NameAngle"]) ? $Format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
		$NameColor = isset($Format["NameColor"]) ? $Format["NameColor"] : new pColor(255);
		$DisableShadowOnArea = isset($Format["DisableShadowOnArea"]) ? $Format["DisableShadowOnArea"] : TRUE;
		$NoMargin = isset($Format["NoMargin"]) ? $Format["NoMargin"] : FALSE;

		$Data = $this->myData->getData();

		if (!isset($Data["Axis"][$AxisID])) {
			throw pException::InvalidInput("Axis ID is invalid");
		}

		$margin = $Data["Axis"][$AxisID]["Margin"];

		if ($Value1 > $Value2) {
			list($Value1, $Value2) = [$Value2,$Value1];
		}

		$RestoreShadow = $this->Shadow;
		($DisableShadowOnArea && $this->Shadow) AND $this->Shadow = FALSE;

		$AbscissaMargin = $this->myData->getAbscissaMargin();
		($NoMargin) AND $AbscissaMargin = 0;
		$LineSettings = ["Color" => $BorderColor,"Ticks" => $BorderTicks];

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$XPos1 = $this->GraphAreaX1 + $AbscissaMargin;
			$XPos2 = $this->GraphAreaX2 - $AbscissaMargin;
			$YPos1 = $this->scaleComputeYSingle($Value1, $AxisID);
			$YPos2 = $this->scaleComputeYSingle($Value2, $AxisID);

			($YPos1 < $this->GraphAreaY1 + $margin) AND $YPos1 = $this->GraphAreaY1 + $margin;
			($YPos1 > $this->GraphAreaY2 - $margin) AND $YPos1 = $this->GraphAreaY2 - $margin;
			($YPos2 < $this->GraphAreaY1 + $margin) AND $YPos2 = $this->GraphAreaY1 + $margin;
			($YPos2 > $this->GraphAreaY2 - $margin) AND $YPos2 = $this->GraphAreaY2 - $margin;

			$this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["Color" => $Color]);
			if ($Border) {
				$this->drawLine($XPos1, $YPos1, $XPos2, $YPos1, $LineSettings);
				$this->drawLine($XPos1, $YPos2, $XPos2, $YPos2, $LineSettings);
			}

			if (!is_null($AreaName)) {
				$XPos = ($XPos2 - $XPos1) / 2 + $XPos1;
				$YPos = ($YPos2 - $YPos1) / 2 + $YPos1;
				$this->Shadow = $RestoreShadow;
				$this->drawText($XPos, $YPos, $AreaName, ["Color" => $NameColor,"Angle" => 0,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
			}

		} elseif ($Data["Orientation"] == SCALE_POS_TOPBOTTOM) {

			$YPos1 = $this->GraphAreaY1 + $AbscissaMargin;
			$YPos2 = $this->GraphAreaY2 - $AbscissaMargin;
			$XPos1 = $this->scaleComputeYSingle($Value1, $AxisID);
			$XPos2 = $this->scaleComputeYSingle($Value2, $AxisID);

			($XPos1 < $this->GraphAreaX1 + $margin) AND $XPos1 = $this->GraphAreaX1 + $margin;
			($XPos1 > $this->GraphAreaX2 - $margin) AND $XPos1 = $this->GraphAreaX2 - $margin;
			($XPos2 < $this->GraphAreaX1 + $margin) AND $XPos2 = $this->GraphAreaX1 + $margin;
			($XPos2 > $this->GraphAreaX2 - $margin) AND $XPos2 = $this->GraphAreaX2 - $margin;

			$this->drawFilledRectangle($XPos1, $YPos1, $XPos2, $YPos2, ["Color" => $Color]);
			if ($Border) {
				$this->drawLine($XPos1, $YPos1, $XPos1, $YPos2, $LineSettings);
				$this->drawLine($XPos2, $YPos1, $XPos2, $YPos2, $LineSettings);
			}

			if (!is_null($AreaName)) {
				$XPos = ($YPos2 - $YPos1) / 2 + $YPos1;
				$YPos = ($XPos2 - $XPos1) / 2 + $XPos1;
				if ($NameAngle == ZONE_NAME_ANGLE_AUTO) {
					$TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $AreaName);
					$TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
					$NameAngle = (abs($XPos2 - $XPos1) > $TxtWidth) ? 0 : 90;
				}

				$this->Shadow = $RestoreShadow;
				$this->drawText($YPos, $XPos, $AreaName, ["Color" => $NameColor,"Angle" => $NameAngle,"Align" => TEXT_ALIGN_MIDDLEMIDDLE]);
			}
		}

		$this->Shadow = $RestoreShadow;
	}

	public function scaleComputeYSingle($Value, int $AxisID)
	{
		if ($Value == VOID) {
			return VOID;
		}

		$Data = $this->myData->getData();

		$Scale = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
		$Margin = $Data["Axis"][$AxisID]["Margin"];

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$Height = $this->GraphAreaYdiff - $Margin * 2;
			$Result = $this->GraphAreaY2 - $Margin - (($Height / $Scale) * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]));
		} else {
			$Width = $this->GraphAreaXdiff - $Margin * 2;
			$Result = $this->GraphAreaX1 + $Margin + (($Width / $Scale) * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]));
		}

		return $Result;
	}

	public function scaleComputeY(array $Values, int $AxisID)
	{
		$Data = $this->myData->getData();
		$Result = [];

		$Scale = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
		$Margin = $Data["Axis"][$AxisID]["Margin"];

		foreach($Values as $Value){

			if ($Value == VOID) {
				$Result[] = VOID;
			} else {
				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
					$Height = $this->GraphAreaYdiff - $Margin * 2;
					$Result[] = $this->GraphAreaY2 - $Margin - (($Height / $Scale) * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]));
				} else {
					$Width = $this->GraphAreaXdiff - $Margin * 2;
					$Result[] = $this->GraphAreaX1 + $Margin + (($Width / $Scale) * ($Value - $Data["Axis"][$AxisID]["ScaleMin"]));
				}
			}
		}

		return $Result;
	}

	/* Used in pCharts->drawStackedAreaChart() & pCharts->drawStackedBarChart() */
	public function scaleComputeY0HeightOnly(array $Values, int $AxisID)
	{
		$Data = $this->myData->getData();
		$Scale = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
		$Result = [];

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$Height = $this->GraphAreaYdiff - $Data["Axis"][$AxisID]["Margin"] * 2;
			foreach($Values as $Value) {
				$Result[] = ($Value == VOID) ? VOID : ($Height / $Scale) * $Value;
			}
		} else {
			$Width = $this->GraphAreaXdiff - $Data["Axis"][$AxisID]["Margin"] * 2;
			foreach($Values as $Value) {
				$Result[] = ($Value == VOID) ? VOID : ($Width / $Scale) * $Value;
			}
		}

		return $Result;
	}

	/* Format the axis values */
	public function scaleFormat($Value, array $Axis)
	{
		if ($Value == VOID) {
			return "";
		}

		# Momchil: this is not the same as default for the switch
		# $Value comes as an INT or FLOAT but is used as a STRING as well
		$ret = strval($Value) . $Axis["Unit"];

		switch ($Axis["Display"]) {
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
					$ret = $Sign . strval($Value) . " " . $Units[$Scale];
				}
				break;
			case AXIS_FORMAT_CUSTOM:
				if (is_callable($Axis["Format"])) {
					$ret = $Axis["Format"]($Value);
				}
				break;
			case AXIS_FORMAT_DATE:
				$ret = gmdate((is_null($Axis["Format"])) ? "d/m/Y" : $Axis["Format"], $Value);
				break;
			case AXIS_FORMAT_TIME:
				$ret = gmdate((is_null($Axis["Format"])) ? "H:i:s" : $Axis["Format"], $Value);
				break;
			case AXIS_FORMAT_CURRENCY:
				$ret = $Axis["Format"] . number_format($Value, 2);
				break;
			case AXIS_FORMAT_METRIC:
				if (is_null($Axis["Format"])){
					$Axis["Format"] = 0;
				}
				if (abs($Value) >= 1000) {
					$ret = (round($Value / 1000, $Axis["Format"]) . "k" . $Axis["Unit"]);
				} elseif (abs($Value) > 1000000) {
					$ret = (round($Value / 1000000, $Axis["Format"]) . "m" . $Axis["Unit"]);
				} elseif (abs($Value) > 1000000000) {
					$ret = (round($Value / 1000000000, $Axis["Format"]) . "g" . $Axis["Unit"]);
				}
				break;
		}

		return strval($ret);
	}

	/* Write Max value on a chart */
	public function writeBounds($Type = BOUND_BOTH, array $Format = [])
	{
		$MaxLabelTxt = "max=";
		$MinLabelTxt = "min=";
		$Decimals = 1;
		$ExcludedSeries = [];
		$DisplayOffset = 4;
		#$DisplayColor = DISPLAY_MANUAL;
		$MaxDisplayColor = new pColor(0);
		$MinDisplayColor = new pColor(255);
		$MinLabelPos = BOUND_LABEL_POS_AUTO;
		$MaxLabelPos = BOUND_LABEL_POS_AUTO;
		$DrawBox = TRUE;
		$DrawBoxBorder = FALSE;
		$BorderOffset = 5;
		$BoxRounded = TRUE;
		$RoundedRadius = 3;
		$BoxColor = new pColor(0,0,0,30);
		$BoxSurrounding = 0;
		$BoxBorderColor = new pColor(0,0,0,50);

		/* Override defaults */
		extract($Format);

		$CaptionSettings = [
			"DrawBox" => $DrawBox,
			"DrawBoxBorder" => $DrawBoxBorder,
			"BorderOffset" => $BorderOffset,
			"BoxRounded" => $BoxRounded,
			"RoundedRadius" => $RoundedRadius,
			"BoxColor" => $BoxColor,
			"BoxSurrounding" => $BoxSurrounding,
			"BoxBorderColor" => $BoxBorderColor
		];

		list($XMargin, $XDivs) = $this->myData->scaleGetXSettings();

		$Data = $this->myData->getData();

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			$XStep = ($this->GraphAreaXdiff - $XMargin * 2) / $XDivs;
		} else {
			$XStep = ($this->GraphAreaYdiff - $XMargin * 2) / $XDivs;
		}

		foreach($Data["Series"] as $SerieName => $Serie) {
			if ($Serie["isDrawable"] && $SerieName != $Data["Abscissa"] && !isset($ExcludedSeries[$SerieName])) {

				$MinValue = $Serie["Min"];
				$MaxValue = $Serie["Max"];
				$MinPos = array_search($MinValue, $Serie["Data"]);
				$MaxPos = array_search($MaxValue, $Serie["Data"]);
				$AxisID = $Serie["Axis"];
				$PosArray = $this->scaleComputeY($Serie["Data"], $Serie["Axis"]);
				$SerieOffset = $Serie["XOffset"];

				if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {

					$X = $this->GraphAreaX1 + $XMargin;

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
						$Label = $MaxLabelTxt . $this->scaleFormat(round($MaxValue, $Decimals), $Data["Axis"][$AxisID]);
						$TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $Label);
						$XOffset = 0;
						$YOffset = 0;

						($TxtPos[0]["X"] < $this->GraphAreaX1) AND $XOffset = (($this->GraphAreaX1 - $TxtPos[0]["X"]) / 2);
						($TxtPos[1]["X"] > $this->GraphAreaX2) AND $XOffset = - (($TxtPos[1]["X"] - $this->GraphAreaX2) / 2);
						($TxtPos[2]["Y"] < $this->GraphAreaY1) AND $YOffset = $this->GraphAreaY1 - $TxtPos[2]["Y"];
						($TxtPos[0]["Y"] > $this->GraphAreaY2) AND $YOffset = - ($TxtPos[0]["Y"] - $this->GraphAreaY2);

						$CaptionSettings["Color"] = $MaxDisplayColor;
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
						$Label = $MinLabelTxt . $this->scaleFormat(round($MinValue, $Decimals), $Data["Axis"][$AxisID]);
						$TxtPos = $this->getTextBox($XPos, $YPos, $this->FontName, $this->FontSize, 0, $Label);
						$XOffset = 0;
						$YOffset = 0;

						($TxtPos[0]["X"] < $this->GraphAreaX1) AND $XOffset = (($this->GraphAreaX1 - $TxtPos[0]["X"]) / 2);
						($TxtPos[1]["X"] > $this->GraphAreaX2) AND $XOffset = - (($TxtPos[1]["X"] - $this->GraphAreaX2) / 2);
						($TxtPos[2]["Y"] < $this->GraphAreaY1) AND $YOffset = $this->GraphAreaY1 - $TxtPos[2]["Y"];
						($TxtPos[0]["Y"] > $this->GraphAreaY2) AND $YOffset = - ($TxtPos[0]["Y"] - $this->GraphAreaY2);

						$CaptionSettings["Color"] = $MinDisplayColor;
						$CaptionSettings["Align"] = $Align;
						$this->drawText($XPos + $XOffset, $YPos - $DisplayOffset + $YOffset, $Label, $CaptionSettings);
					}

				} else {

					$X = $this->GraphAreaY1 + $XMargin;

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
						$Label = $MaxLabelTxt . $this->scaleFormat($MaxValue, $Data["Axis"][$AxisID]);
						$TxtPos = $this->getTextBox($YPos, $XPos, $this->FontName, $this->FontSize, 0, $Label);
						$XOffset = 0;
						$YOffset = 0;

						($TxtPos[0]["X"] < $this->GraphAreaX1) AND $XOffset = $this->GraphAreaX1 - $TxtPos[0]["X"];
						($TxtPos[1]["X"] > $this->GraphAreaX2) AND $XOffset = - ($TxtPos[1]["X"] - $this->GraphAreaX2);
						($TxtPos[2]["Y"] < $this->GraphAreaY1) AND $YOffset = ($this->GraphAreaY1 - $TxtPos[2]["Y"]) / 2;
						($TxtPos[0]["Y"] > $this->GraphAreaY2) AND $YOffset = - (($TxtPos[0]["Y"] - $this->GraphAreaY2) / 2);
	
						$CaptionSettings["Color"] = $MaxDisplayColor;
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
						$Label = $MinLabelTxt . $this->scaleFormat($MinValue, $Data["Axis"][$AxisID]);
						$TxtPos = $this->getTextBox($YPos, $XPos, $this->FontName, $this->FontSize, 0, $Label);
						$XOffset = 0;
						$YOffset = 0;

						($TxtPos[0]["X"] < $this->GraphAreaX1) AND $XOffset = $this->GraphAreaX1 - $TxtPos[0]["X"];
						($TxtPos[1]["X"] > $this->GraphAreaX2) AND $XOffset = - ($TxtPos[1]["X"] - $this->GraphAreaX2);
						($TxtPos[2]["Y"] < $this->GraphAreaY1) AND $YOffset = ($this->GraphAreaY1 - $TxtPos[2]["Y"]) / 2;
						($TxtPos[0]["Y"] > $this->GraphAreaY2) AND $YOffset = - (($TxtPos[0]["Y"] - $this->GraphAreaY2) / 2);

						$CaptionSettings["Color"] = $MinDisplayColor;
						$CaptionSettings["Align"] = $Align;
						$this->drawText($YPos + $XOffset, $XPos + $YOffset, $Label, $CaptionSettings);
					}
				}
			}
		}
	}

	/* Write labels */
	public function writeLabel(array $SeriesName, array $Indexes, array $Format = [])
	{
		$OverrideTitle = isset($Format["OverrideTitle"]) ? $Format["OverrideTitle"] : NULL;
		$ForceLabels = isset($Format["ForceLabels"]) ? $Format["ForceLabels"] : [];
		$DrawPoint = isset($Format["DrawPoint"]) ? $Format["DrawPoint"] : LABEL_POINT_BOX;
		$DrawVerticalLine = isset($Format["DrawVerticalLine"]) ? $Format["DrawVerticalLine"] : FALSE;
		$OverrideColors = isset($Format["OverrideColors"]) ? $Format["OverrideColors"] : [];
		$VerticalLineColor = isset($Format["VerticalLineColor"]) ? $Format["VerticalLineColor"] : new pColor(0,0,0,40);
		$VerticalLineTicks = isset($Format["VerticalLineTicks"]) ? $Format["VerticalLineTicks"] : 2;
		$forStackedChart = isset($Format["forStackedChart"]) ? $Format["forStackedChart"] : FALSE;

		list($XMargin, $XDivs) = $this->myData->scaleGetXSettings();
		$Data = $this->myData->getData();

		if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {
			if ($XDivs == 0) {
				$XStep = $this->GraphAreaXdiff / 4;
			} else {
				$XStep = ($this->GraphAreaXdiff - $XMargin * 2) / $XDivs;
			}
		} else {
			if ($XDivs == 0) {
				$XStep = $this->GraphAreaYdiff / 4;
			} else {
				$XStep = ($this->GraphAreaYdiff - $XMargin * 2) / $XDivs;
			}
		}

		foreach($Indexes as $Key => $Index) {
			$Series = [];
			$Index = intval($Index);
			$AbscissaDataSet = (!is_null($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index]));

			if ($Data["Orientation"] == SCALE_POS_LEFTRIGHT) {

				$X = $this->GraphAreaX1 + $XMargin + $Index * $XStep;
				if ($DrawVerticalLine) {
					$this->drawLine($X, $this->GraphAreaY1 + $Data["YMargin"], $X, $this->GraphAreaY2 - $Data["YMargin"], ["Color" => $VerticalLineColor,"Ticks" => $VerticalLineTicks]);
				}

				$MinY = $this->GraphAreaY2;

				foreach($SeriesName as $SerieName) {

					$SerieName = strval($SerieName);

					if (isset($Data["Series"][$SerieName]["Data"][$Index])) {
						$AxisID = $Data["Series"][$SerieName]["Axis"];

						if ($AbscissaDataSet) {
							$XLabel = $this->scaleFormat($Data["Series"][$Data["Abscissa"]]["Data"][$Index], $Data["AbscissaProperties"]);
						} else {
							$XLabel = "";
						}

						if (!is_null($OverrideTitle)) {
							$Description = $OverrideTitle;
						} elseif (count($SeriesName) == 1) {
							$Description = $Data["Series"][$SerieName]["Description"] . " - " . $XLabel;
						} elseif ($AbscissaDataSet) {
							$Description = $XLabel;
						}

						if (isset($OverrideColors[$Index])) {
							$SerieColor = $OverrideColors[$Index];
						} else {
							$SerieColor = $Data["Series"][$SerieName]["Color"];
						}

						$SerieOffset = (count($SeriesName) == 1) ? $Data["Series"][$SerieName]["XOffset"] : 0;
						$Value = $Data["Series"][$SerieName]["Data"][$Index];
						($Value == VOID) AND $Value = "NaN";
						
						if (!empty($ForceLabels)) {
							$Caption = isset($ForceLabels[$Key]) ? $ForceLabels[$Key] : "Not set";
						} else {
							$Caption = $this->scaleFormat($Value, $Data["Axis"][$AxisID]);
						}

						if ($forStackedChart) {
							$LookFor = ($Value >= 0) ? "+" : "-";
							$Value = 0;
							foreach($Data["Series"] as $Name => $SerieLookup) {
								if ($SerieLookup["isDrawable"] && $Name != $Data["Abscissa"]) {
									if (isset($SerieLookup["Data"][$Index]) && $SerieLookup["Data"][$Index] != VOID) {
										if ($SerieLookup["Data"][$Index] >= 0 && $LookFor == "+") {
											$Value = $Value + $SerieLookup["Data"][$Index];
										}

										if ($SerieLookup["Data"][$Index] < 0 && $LookFor == "-") {
											$Value = $Value - $SerieLookup["Data"][$Index];
										}

										if ($Name == $SerieName) {
											break;
										}
									}
								}
							}
						}

						$X = floor($this->GraphAreaX1 + $XMargin + $Index * $XStep + $SerieOffset);
						$Y = floor($this->scaleComputeYSingle($Value, $AxisID));
						if ($Y < $MinY) {
							$MinY = $Y;
						}

						if ($DrawPoint == LABEL_POINT_CIRCLE) {
							$this->drawFilledCircle($X, $Y, 3, ["Color" => new pColor(255),"BorderColor" => new pColor(0)]);
						} elseif ($DrawPoint == LABEL_POINT_BOX) {
							$this->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["Color" => new pColor(255),"BorderColor" => new pColor(0)]);
						}

						$Series[] = ["Color" => $SerieColor,"Caption" => $Caption];
					}
				}

				$this->drawLabelBox($X, $MinY - 3, $Description, $Series, $Format);

			} else {

				$Y = $this->GraphAreaY1 + $XMargin + $Index * $XStep;
				if ($DrawVerticalLine) {
					$this->drawLine($this->GraphAreaX1 + $Data["YMargin"], $Y, $this->GraphAreaX2 - $Data["YMargin"], $Y, ["Color" => $VerticalLineColor,"Ticks" => $VerticalLineTicks]);
				}

				$MinX = $this->GraphAreaX2;
				foreach($SeriesName as $SerieName) {
					if (isset($Data["Series"][$SerieName]["Data"][$Index])) {
						$AxisID = $Data["Series"][$SerieName]["Axis"];

						if ($AbscissaDataSet) {
							$XLabel = $this->scaleFormat($Data["Series"][$Data["Abscissa"]]["Data"][$Index], $Data["AbscissaProperties"]);
						} else {
							$XLabel = "";
						}

						if (!is_null($OverrideTitle)) {
							$Description = $OverrideTitle;
						} elseif (count($SeriesName) == 1) {
							if ($AbscissaDataSet){
								$Description = $Data["Series"][$SerieName]["Description"] . " - " . $XLabel;
							}
						} elseif (!$AbscissaDataSet) {
							$Description = $XLabel;
						}

						if (isset($OverrideColors[$Index])) {
							$SerieColor = $OverrideColors[$Index];
						} else {
							$SerieColor = $Data["Series"][$SerieName]["Color"];
						}

						$SerieOffset = (count($SeriesName) == 1) ? $Data["Series"][$SerieName]["XOffset"] : 0;
						$Value = $Data["Series"][$SerieName]["Data"][$Index];
						($Value == VOID) AND $Value = "NaN";
						
						if (!empty($ForceLabels)) { # Momchil: example.drawLabel.caption.php shows these correspond to Index and not Serie
							$Caption = isset($ForceLabels[$Key]) ? $ForceLabels[$Key] : "Not set";
						} else {
							$Caption = $this->scaleFormat($Value, $Data["Axis"][$AxisID]);
						}

						if ($forStackedChart) {
							$LookFor = ($Value >= 0) ? "+" : "-";
							$Value = 0;
							foreach($Data["Series"] as $Name => $SerieLookup) {
								if ($SerieLookup["isDrawable"] && $Name != $Data["Abscissa"]) {
									if (isset($SerieLookup["Data"][$Index]) && $SerieLookup["Data"][$Index] != VOID) {
										if ($SerieLookup["Data"][$Index] >= 0 && $LookFor == "+") {
											$Value = $Value + $SerieLookup["Data"][$Index];
										}

										if ($SerieLookup["Data"][$Index] < 0 && $LookFor == "-") {
											$Value = $Value - $SerieLookup["Data"][$Index];
										}

										if ($Name == $SerieName) {
											break;
										}
									}
								}
							}
						}

						$X = floor($this->scaleComputeYSingle($Value, $AxisID));
						$Y = floor($this->GraphAreaY1 + $XMargin + $Index * $XStep + $SerieOffset);
						if ($X < $MinX) {
							$MinX = $X;
						}

						if ($DrawPoint == LABEL_POINT_CIRCLE) {
							$this->drawFilledCircle($X, $Y, 3, ["Color" => new pColor(255),"BorderColor" => new pColor(0)]);
						} elseif ($DrawPoint == LABEL_POINT_BOX) {
							$this->drawFilledRectangle($X - 2, $Y - 2, $X + 2, $Y + 2, ["Color" => new pColor(255),"BorderColor" => new pColor(0)]);
						}

						$Series[] = ["Color" => $SerieColor,"Caption" => $Caption];
					}
				}

				$this->drawLabelBox($MinX, $Y - 3, $Description, $Series, $Format);
			}
		}
	}

	/* Draw a label box */
	public function drawLabelBox($X, $Y, $Title, $Captions, array $Format = [])
	{
		$NoTitle = FALSE;
		$BoxWidth = 50;
		$DrawSerieColor = TRUE;
		$SerieBoxSize = 6;
		$SerieBoxSpacing = 4;
		$VerticalMargin = 10;
		$HorizontalMargin = 8;
		$Color = $this->FontColor;
		$FontName = $this->FontName;
		$FontSize = $this->FontSize;
		$TitleMode = LABEL_TITLE_NOBACKGROUND;
		$TitleColor = $Color;
		$TitleBackgroundColor = NULL;
		$GradientStartColor = NULL;
		$GradientEndColor = NULL;
		$BoxAlpha = 100;

		/* Override defaults */
		extract($Format);

		if(is_null($TitleBackgroundColor)){
			$TitleBackgroundColor = new pColor(0,0,0, $BoxAlpha);
		}

		if(is_null($GradientStartColor)){
			$GradientStartColor = new pColor(255,255,255, $BoxAlpha);
		}

		if(is_null($GradientEndColor)){
			$GradientEndColor = new pColor(220,220,220, $BoxAlpha);
		}

		if (!$DrawSerieColor) {
			$SerieBoxSize = 0;
			$SerieBoxSpacing = 0;
		}

		if ($NoTitle) {
			$TitleWidth = 0;
			$TitleHeight = 0;
		} else {
			$TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, 0, $Title);
			$TitleWidth = ($TxtPos[1]["X"] - $TxtPos[0]["X"]) + $VerticalMargin * 2;
			$TitleHeight = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]);
		}

		$CaptionWidth = 0;
		$CaptionHeight = - $HorizontalMargin;
		if (isset($Captions["Caption"])){
			$Captions = [$Captions];
		}

		foreach($Captions as $Caption) {
			$TxtPos = $this->getTextBox($X, $Y, $FontName, $FontSize, 0, $Caption["Caption"]);
			$CaptionWidth = max($CaptionWidth, ($TxtPos[1]["X"] - $TxtPos[0]["X"]) + $VerticalMargin * 2);
			$CaptionHeight +=  max(($TxtPos[0]["Y"] - $TxtPos[2]["Y"]), ($SerieBoxSize + 2)) + $HorizontalMargin;
		}

		($CaptionHeight <= 5) AND $CaptionHeight += $HorizontalMargin / 2;
		($DrawSerieColor) AND $CaptionWidth += $SerieBoxSize + $SerieBoxSpacing;
		$BoxHeight = $TitleHeight + $CaptionHeight + $HorizontalMargin * (($NoTitle) ? 2 : 3);
		$BoxWidth = max($BoxWidth, $TitleWidth, $CaptionWidth);
		$XMin = $X - 5 - intval(floor(($BoxWidth - 10) / 2));
		$XMax = $X + 5 + intval(floor(($BoxWidth - 10) / 2));
		$RestoreShadow = $this->Shadow;
		$ShadowX = $this->ShadowX;

		if ($this->Shadow) {
			$this->Shadow = FALSE;
			$Poly = [
				$X + $ShadowX,
				$Y + $ShadowX,
				$X + 5 + $ShadowX,
				$Y - 5 + $ShadowX,
				$XMax + $ShadowX,
				$Y - 5 + $ShadowX,
				$XMax + $ShadowX,
				$Y - 5 - $BoxHeight + $ShadowX,
				$XMin + $ShadowX,
				$Y - 5 - $BoxHeight + $ShadowX,
				$XMin +  $ShadowX,
				$Y - 5 + $ShadowX,
				$X - 5 + $ShadowX,
				$Y - 5 + $ShadowX
			];

			$this->drawPolygon($Poly, ["Color" => $this->ShadowColor]);
		}

		/* Draw the background */
		$this->drawGradientArea($XMin, $Y - 5 - $BoxHeight, $XMax, $Y - 6, DIRECTION_VERTICAL, ["StartColor"=>$GradientStartColor,"EndColor"=>$GradientEndColor]);

		$Poly = [$X, $Y, $X - 5, $Y - 5, $X + 5, $Y - 5];
		$this->drawPolygon($Poly, ["Color" => $GradientEndColor,"NoBorder" => TRUE]);
		/* Outer border */
		$OuterBorderColor = $this->allocateColor([100, 100, 100, $BoxAlpha]);
		imageline($this->Picture, $XMin, $Y - 5, $X - 5, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $X, $Y, $X - 5, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $X, $Y, $X + 5, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $X + 5, $Y - 5, $XMax, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $XMin, $Y - 5 - $BoxHeight, $XMin, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $XMax, $Y - 5 - $BoxHeight, $XMax, $Y - 5, $OuterBorderColor);
		imageline($this->Picture, $XMin, $Y - 5 - $BoxHeight, $XMax, $Y - 5 - $BoxHeight, $OuterBorderColor);

		/* Inner border */
		$InnerBorderColor = $this->allocateColor([255, 255, 255, $BoxAlpha]);
		imageline($this->Picture, $XMin + 1, $Y - 6, $X - 5, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $X, $Y - 1, $X - 5, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $X, $Y - 1, $X + 5, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $X + 5, $Y - 6, $XMax - 1, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $XMin + 1, $Y - 4 - $BoxHeight, $XMin + 1, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $XMax - 1, $Y - 4 - $BoxHeight, $XMax - 1, $Y - 6, $InnerBorderColor);
		imageline($this->Picture, $XMin + 1, $Y - 4 - $BoxHeight, $XMax - 1, $Y - 4 - $BoxHeight, $InnerBorderColor);

		/* Draw the separator line */
		if ($TitleMode == LABEL_TITLE_NOBACKGROUND && !$NoTitle) {
			$YPos = $Y - 7 - $CaptionHeight - $HorizontalMargin - $HorizontalMargin / 2;
			$XMargin = $VerticalMargin / 2;
			$this->drawLine($XMin + $XMargin, $YPos + 1, $XMax - $XMargin, $YPos + 1, ["Color" => $GradientEndColor->newOne()->AlphaSet($BoxAlpha)]);
			$this->drawLine($XMin + $XMargin, $YPos, $XMax - $XMargin, $YPos, ["Color" => $GradientStartColor->newOne()->AlphaSet($BoxAlpha)]);
		} elseif ($TitleMode == LABEL_TITLE_BACKGROUND) {
			$this->drawFilledRectangle($XMin, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin * 3, $XMax, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin / 2, ["Color" => $TitleBackgroundColor]);
			imageline($this->Picture, $XMin + 1, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin / 2 + 1, $XMax - 1, $Y - 5 - $TitleHeight - $CaptionHeight - $HorizontalMargin / 2 + 1, $InnerBorderColor);
		}

		/* Write the description */
		if (!$NoTitle) {
			$this->drawText($XMin + $VerticalMargin, $Y - 7 - $CaptionHeight - $HorizontalMargin * 2, $Title, ["Align" => TEXT_ALIGN_BOTTOMLEFT,"Color" => $TitleColor]);
		}

		/* Write the value */
		$YPos = $Y - 5 - $HorizontalMargin;
		$XPos = $XMin + $VerticalMargin + $SerieBoxSize + $SerieBoxSpacing;

		foreach($Captions as $Caption) {
			$TxtPos = $this->getTextBox($XPos, $YPos, $FontName, $FontSize, 0, $Caption["Caption"]);
			$CaptionHeight = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]);
			/* Write the serie color if needed */
			if ($DrawSerieColor) {
				$BoxSettings = ["Color" => $Caption["Color"],"BorderColor" => new pColor(0)];
				$this->drawFilledRectangle($XMin + $VerticalMargin, $YPos - $SerieBoxSize, $XMin + $VerticalMargin + $SerieBoxSize, $YPos, $BoxSettings);
			}

			/* Momchil: visual fix */
			if (!$DrawSerieColor) {
				$YPos += 3;
			}

			$this->drawText($XPos, $YPos, $Caption["Caption"], ["Align" => TEXT_ALIGN_BOTTOMLEFT]);
			$YPos = $YPos - $CaptionHeight - $HorizontalMargin;
		}

		$this->Shadow = $RestoreShadow;
	}

	/* Draw a basic shape */
	public function drawShape($X, $Y, $Shape, $PlotSize, $PlotBorder, $BorderSize, pColor $Color, pColor $BorderColor)
	{
		switch ($Shape){
			case SERIE_SHAPE_FILLEDCIRCLE:
				if ($PlotBorder) {
					$this->drawFilledCircle($X, $Y, $PlotSize + $BorderSize, ["Color" => $BorderColor]);
				}
				$this->drawFilledCircle($X, $Y, $PlotSize,["Color" => $Color]);
				break;
			case SERIE_SHAPE_FILLEDSQUARE:
				if ($PlotBorder) {
					$this->drawFilledRectangle($X - $PlotSize - $BorderSize, $Y - $PlotSize - $BorderSize, $X + $PlotSize + $BorderSize, $Y + $PlotSize + $BorderSize, ["Color" => $BorderColor]);
				}
				$this->drawFilledRectangle($X - $PlotSize, $Y - $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["Color" => $Color]);
				break;
			case SERIE_SHAPE_FILLEDTRIANGLE:
				if ($PlotBorder) {
					$this->drawPolygon([$X, $Y - $PlotSize - $BorderSize, $X - $PlotSize - $BorderSize, $Y + $PlotSize + $BorderSize, $X + $PlotSize + $BorderSize, $Y + $PlotSize + $BorderSize], ["Color" => $BorderColor]);
				}
				$this->drawPolygon([$X, $Y - $PlotSize, $X - $PlotSize, $Y + $PlotSize, $X + $PlotSize, $Y + $PlotSize], ["Color" => $Color]);
				break;
			case SERIE_SHAPE_TRIANGLE:
				$this->drawLine($X, $Y - $PlotSize, $X - $PlotSize, $Y + $PlotSize, ["Color" => $Color]);
				$this->drawLine($X - $PlotSize, $Y + $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["Color" => $Color]);
				$this->drawLine($X + $PlotSize, $Y + $PlotSize, $X, $Y - $PlotSize, ["Color" => $Color]);
				break;
			case SERIE_SHAPE_SQUARE:
				$this->drawRectangle($X - $PlotSize, $Y - $PlotSize, $X + $PlotSize, $Y + $PlotSize, ["Color" => $Color]);
				break;
			case SERIE_SHAPE_CIRCLE:
				$this->drawCircle($X, $Y, $PlotSize, $PlotSize, ["Color" => $Color]);
				break;
			case SERIE_SHAPE_DIAMOND:
				$this->drawPolygon([$X - $PlotSize, $Y, $X, $Y - $PlotSize, $X + $PlotSize, $Y, $X, $Y + $PlotSize], ["NoFill" => TRUE,"Color" => $BorderColor]);
				break;
			case SERIE_SHAPE_FILLEDDIAMOND:
				if ($PlotBorder) {
					$this->drawPolygon([$X - $PlotSize - $BorderSize, $Y, $X, $Y - $PlotSize - $BorderSize, $X + $PlotSize + $BorderSize, $Y, $X, $Y + $PlotSize + $BorderSize], ["Color" => $BorderColor]);
				}
				$this->drawPolygon([$X - $PlotSize, $Y, $X, $Y - $PlotSize, $X + $PlotSize, $Y, $X, $Y + $PlotSize], ["Color" => $Color]);
				break;
		}
	}

	/* Enable / Disable and set shadow properties */
	public function setShadow(bool $Enabled = TRUE, array $Format = [])
	{
		$this->Shadow = $Enabled;

		/* Disable the shadow and exit */
		if (!$Enabled){
			return;
		}

		if(isset($Format["X"])){
			$this->ShadowX = $Format["X"];
		}

		if(isset($Format["Y"])){
			$this->ShadowY = $Format["Y"];
		}

		if (isset($Format["Color"])){
			$this->ShadowColor = $Format["Color"];
			$this->ShadowColorAlloc = $this->allocateColor($this->ShadowColor->get());
		}
	}

	public function restoreShadow(array $shadow)
	{
		if(!isset($shadow["Enabled"])){
			throw pException::InvalidInput("Invalid shadow specs");
		}

		$this->setShadow((bool)$shadow["Enabled"], $shadow);
	}

	public function getShadow()
	{
		return [
			'Enabled' => $this->Shadow,
			'X' => $this->ShadowX,
			'Y' => $this->ShadowY,
			'Color' => $this->ShadowColor
		];
	}

	public function getFont()
	{
		return [
			'Name' => $this->FontName,
			'Size' => $this->FontSize,
			'Color' => $this->FontColor
		];
	}

	public function setAntialias(bool $enabled, float $quality = 0)
	{
		$this->Antialias = $enabled;
		$this->AntialiasQuality = $quality;
	}

	public function getGraphAreaDiffs()
	{
		return [$this->GraphAreaXdiff, $this->GraphAreaYdiff];
	}

	/* Set the graph area position */
	public function setGraphArea($X1, $Y1, $X2, $Y2)
	{
		if ($X2 < $X1 || $X1 == $X2 || $Y2 < $Y1 || $Y1 == $Y2) {
			throw pException::InvalidInput("Invalid graph specs");
		}

		$this->GraphAreaX1 = $X1;
		$this->GraphAreaY1 = $Y1;
		$this->GraphAreaX2 = $X2;
		$this->GraphAreaY2 = $Y2;

		$this->GraphAreaXdiff = $X2 - $X1;
		$this->GraphAreaYdiff = $Y2 - $Y1;
	}

	public function getGraphAreaCoordinates()
	{
		return [
			"L" => $this->GraphAreaX1,
			"R" => $this->GraphAreaX2,
			"T" => $this->GraphAreaY1,
			"B" => $this->GraphAreaY2
		];
	}

	/* Return the orientation of a line */
	public function getAngle($X1, $Y1, $X2, $Y2)
	{
		#$Opposite = $Y2 - $Y1;
		#$Adjacent = $X2 - $X1;
		$Angle = rad2deg(atan2($Y2 - $Y1, $X2 - $X1));

		return (($Angle > 0) ? $Angle : (360 - abs($Angle)));
	}

	/* Return the surrounding box of text area */
	public function getTextBox($X, $Y, $FontName, $FontSize, $Angle, $Text)
	{
		$this->verifyFontDefined();
		$coords = imagettfbbox($FontSize, 0, realpath($FontName), $Text);
		$a = deg2rad($Angle);
		$ca = cos($a);
		$sa = sin($a);
		$Pos = [];
		for ($i = 0; $i < 7; $i+= 2) {
			$Pos[$i / 2]["X"] = $X + round($coords[$i] * $ca + $coords[$i + 1] * $sa);
			$Pos[$i / 2]["Y"] = $Y + round($coords[$i + 1] * $ca - $coords[$i] * $sa);
		}

		$Pos[TEXT_ALIGN_BOTTOMLEFT] = $Pos[0];
		$Pos[TEXT_ALIGN_BOTTOMRIGHT] = $Pos[1];
		$Pos[TEXT_ALIGN_TOPLEFT] = $Pos[3];
		$Pos[TEXT_ALIGN_TOPRIGHT] = $Pos[2];
		$Pos[TEXT_ALIGN_BOTTOMMIDDLE]["X"] = ($Pos[1]["X"] - $Pos[0]["X"]) / 2 + $Pos[0]["X"];
		$Pos[TEXT_ALIGN_BOTTOMMIDDLE]["Y"] = ($Pos[0]["Y"] - $Pos[1]["Y"]) / 2 + $Pos[1]["Y"];
		$Pos[TEXT_ALIGN_TOPMIDDLE]["X"] = ($Pos[2]["X"] - $Pos[3]["X"]) / 2 + $Pos[3]["X"];
		$Pos[TEXT_ALIGN_TOPMIDDLE]["Y"] = ($Pos[3]["Y"] - $Pos[2]["Y"]) / 2 + $Pos[2]["Y"];
		$Pos[TEXT_ALIGN_MIDDLELEFT]["X"] = ($Pos[0]["X"] - $Pos[3]["X"]) / 2 + $Pos[3]["X"];
		$Pos[TEXT_ALIGN_MIDDLELEFT]["Y"] = ($Pos[0]["Y"] - $Pos[3]["Y"]) / 2 + $Pos[3]["Y"];
		$Pos[TEXT_ALIGN_MIDDLERIGHT]["X"] = ($Pos[1]["X"] - $Pos[2]["X"]) / 2 + $Pos[2]["X"];
		$Pos[TEXT_ALIGN_MIDDLERIGHT]["Y"] = ($Pos[1]["Y"] - $Pos[2]["Y"]) / 2 + $Pos[2]["Y"];
		$Pos[TEXT_ALIGN_MIDDLEMIDDLE]["X"] = ($Pos[1]["X"] - $Pos[3]["X"]) / 2 + $Pos[3]["X"];
		$Pos[TEXT_ALIGN_MIDDLEMIDDLE]["Y"] = ($Pos[0]["Y"] - $Pos[2]["Y"]) / 2 + $Pos[2]["Y"];

		return $Pos;
	}

	public function draw1DBarcode(array $code, int $StartX, int $StartY, array $options)
	{
		$padding = $options['padding'];

		$x = $StartX + $padding;
		$y = $StartY + $padding;

		$width = 0;
		$widths = array_values($options['widths']);
		foreach ($code as $block){
			foreach ($block['m'] as $module){
				$width += $module[1] * $widths[$module[2]];
			}
		}

		$w = (!is_null($options['width']))  ? intval($options['width'])  : intval(ceil($width * $options['scale']));
		$h = (!is_null($options['height'])) ? intval($options['height']) : intval(ceil(80 * $options['scale']));

		if ($width > 0) {
			$scale = $w / $width;
			$scale = ($scale > 1) ? $scale : 1;
		} else {
			$scale = 1;
		}

		$scaleY = $scale * $options['ratio']; # Pharmacode 2T
		$palette = array_values($options['palette']);

		# pre-allocate colors
		foreach($palette as $id => $color) {
			$palette[$id] = $this->allocatepColor($color);
		}

		$label = $options['label'];
		if ($label['skip'] != TRUE) {
			$lcolor = $this->allocatepColor($label['color']);
			$lsize = (int)$label['size'];
			$bgH = $h + $label['height'] + 2;
		} else {
			$bgH = $h;
		}

		if (!boolval($options['nobackground'])){
			imagefilledrectangle($this->Picture, $StartX, $StartY, $StartX + $w + ($padding * 2), $StartY + $bgH + ($padding * 2), $palette[0]);
		}

		foreach ($code as $block) {

			if (isset($block['l'])) {
				$ly = (isset($block['l'][1]) ? (float)$block['l'][1] : 1);
				$my = round($y + min($h, $h + ($ly - 1) * intval($label['height'])));
			} else {
				$my = $y + $h;
			}

			$mx = $x;
			foreach ($block['m'] as $module) {
				$mw = $mx + $module[1] * $widths[$module[2]] * $scale;
				$c = count($module);
				if ($c == 3){
					if ($module[0]){
						imagefilledrectangle($this->Picture, intval($mx), $y, intval($mw - 1), intval($my - 1), $palette[$module[0]]);
					}
				} else if ($c == 5){
					if ($module[0]){
						$ym = intval($module[4] * $scaleY);
						imagefilledrectangle($this->Picture, intval($mx), $y + $ym, intval($mw - 1), intval($y + $ym + ($module[3] * $scaleY)), $palette[$module[0]]);
					}
				}
				$mx = $mw;
			}
			if ($label['skip'] != TRUE) {
				if (isset($block['l'])) {
					$text = $block['l'][0];
					$lx = (isset($block['l'][2]) ? (float)$block['l'][2] : 0.5);
					$lx = ($x + ($mx - $x) * $lx);
					$lw = imagefontwidth($lsize) * strlen($text);
					$lx = intval(round($lx - $lw / 2));
					$ly = ($y + $h + $ly * $label['height']);
					$ly = intval(round($ly - imagefontheight($lsize)));
					$ly += $label['offset'];
					if (!is_null($label['ttf'])) {
						$ly +=($lsize*2);
						imagettftext($this->Picture, $lsize, 0, $lx, $ly, $lcolor, realpath($label['ttf']), $text);
					} else {
						imagestring($this->Picture,  $lsize, $lx, $ly, $text, $lcolor);
					}
				}
			}

			$x = $mx;
		}
	}

	public function draw2DBarcode(array $pixelGrid, int $StartX, int $StartY, array $options)
	{
		$padding = $options['padding'];
		$scaleX = $options['scale'];

		$w = count($pixelGrid[0]);
		$width = ($w * $scaleX) + $padding * 2;

		// Apply scaling & aspect ratio
		if (isset($options['ratio'])) { # PDF417
			$scaleY = $scaleX * $options['ratio'];
			$h = count($pixelGrid);
			$height = ($h * $scaleY) + $padding * 2;
		} else {
			$scaleY = $scaleX;
			$h = $w;
			$height = $width;
		}

		if (isset($options['pattern'])){ # DTMX
			if ((bool)$options['pattern']){ # rectangular
				$h = count($pixelGrid);
				$height = ($h * $scaleY) + $padding * 2;
			}
		}

		// Draw the background
		if (!boolval($options['nobackground'])){
			$bgColorAlloc = $this->allocatepColor($options['palette']['bgColor']);
			imagefilledrectangle($this->Picture, $StartX, $StartY, $StartX + $width, $StartY + $height, $bgColorAlloc);
		}
		$colorAlloc = $this->allocatepColor($options['palette']['color']);

		$StartX += $padding;
		$StartY += $padding;

		// Render the barcode
		for($y = 0; $y < $h; $y++) {
			for($x = 0; $x < $w; $x++) {
				if ($pixelGrid[$y][$x] & 1) {
					imagefilledrectangle(
						$this->Picture,
						($x * $scaleX) + $StartX,
						($y * $scaleY) + $StartY,
						(($x + 1) * $scaleX - 1) + $StartX,
						(($y + 1) * $scaleY - 1) + $StartY,
						$colorAlloc
					);
				}
			}
		}
	}

	private function verifyFontDefined()
	{
		if (is_null($this->FontName))
		{
			throw pException::InvalidResourcePath("No font path defined!");
		}
	}

	/* Set current font properties */
	public function setFontProperties(array $Format = [])
	{
		$this->FontColor = (isset($Format['Color'])) ? $Format['Color'] : new pColor(0);

		(isset($Format['FontSize'])) AND $this->FontSize = $Format['FontSize'];

		if (isset($Format['FontName'])){
			$this->FontName = $Format['FontName'];
			if (!file_exists($this->FontName)){
				throw pException::InvalidResourcePath("Font path ".$this->FontName. " does not exist!");
			}
		}
	}

	/* Returns the 1st decimal values (used to correct AA bugs) */
	public function getFirstDecimal($Value)
	{
		return floor(($Value - floor($Value))*10);
	}

	/* Reverse an array of points */
	public function reversePlots(array $Plots)
	{
		$Result = [];
		for ($i = count($Plots) - 2; $i >= 0; $i = $i - 2) {
			$Result[] = $Plots[$i];
			$Result[] = $Plots[$i + 1];
		}

		return $Result;
	}

	/* Return the width of the picture */
	public function getWidth()
	{
		return $this->XSize;
	}

	/* Return the height of the picture */
	public function getHeight()
	{
		return $this->YSize;
	}

	/* http://php.net/manual/en/function.imagefilter.php */
	public function setFilter(int $filtertype, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $arg4 = NULL)
	{
		switch (TRUE){
			case (!is_null($arg1)):
				$ret = imagefilter($this->Picture, $filtertype, $arg1);
				break;
			case (!is_null($arg2)):
				$ret = imagefilter($this->Picture, $filtertype, $arg1, $arg2);
				break;
			case (!is_null($arg3)):
				$ret = imagefilter($this->Picture, $filtertype, $arg1, $arg2, $arg3);
				break;
			case (!is_null($arg4)):
				$ret = imagefilter($this->Picture, $filtertype, $arg1, $arg2, $arg3, $arg4);
				break;
			default:
				$ret = imagefilter($this->Picture, $filtertype);
		}

		if (!$ret){
			throw pException::InvalidImageFilter("Could not apply image filter!");
		}
	}

	/* Render the picture to a file */
	public function render(string $FileName, int $Compression = 6, int $Filters = PNG_NO_FILTER)
	{
		if ($this->TransparentBackground) {
			imagealphablending($this->Picture, FALSE);
		}

		imagepng($this->Picture, $FileName, $Compression, $Filters);
	}

	/* Render the picture to a web browser stream */
	public function stroke(bool $BrowserExpire = FALSE, int $Compression = 6, int $Filters = PNG_NO_FILTER)
	{
		if ($this->TransparentBackground) {
			imagealphablending($this->Picture, FALSE);
		}

		if ($BrowserExpire) {
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Cache-Control: no-cache, must-revalidate"); # HTTP/1.1
			header("Pragma: no-cache");
		}

		header('Content-type: image/png');
		imagepng($this->Picture, NULL, $Compression, $Filters);
	}

	public function toBase64(int $Compression = 6, int $Filters = PNG_NO_FILTER)
	{
		if ($this->TransparentBackground) {
			imagealphablending($this->Picture, FALSE);
		}

		$TempHandle = fopen("php://temp", "wb");
		imagepng($this->Picture, $TempHandle, $Compression, $Filters);
		$stats = fstat($TempHandle);
		rewind($TempHandle);
		$Raw = fread($TempHandle, $stats['size']);
		fclose($TempHandle);

		return base64_encode($Raw);
	}

	/*	Automatic output method based on the calling interface
		Momchil: Added support for Compression & Filters
		Compression must be between 0 and 9 -> http://php.net/manual/en/function.imagepng.php 
		http://php.net/manual/en/image.constants.php
		https://www.w3.org/TR/PNG-Filters.html
		https://stackoverflow.com/questions/3048382/in-php-imagepng-accepts-a-filter-parameter-how-do-these-filters-affect-the-f
	*/
	public function autoOutput(string $FileName = "output.png", int $Compression = 6, int $Filters = PNG_NO_FILTER)
	{
		if (php_sapi_name() == "cli") {
			$this->render($FileName, $Compression, $Filters);
		} else {
			$this->stroke(TRUE, $Compression, $Filters);
		}
	}

}
