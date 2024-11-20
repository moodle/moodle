<?php
/*
pDraw - pChart core class

Version     : 2.1.4
Made by     : Jean-Damien POGOLOTTI
Last Update : 19/01/2014

This file can be distributed under the license you can find at :

http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/
/* The GD extension is mandatory */

if (!extension_loaded('gd') && !extension_loaded('gd2')) {
	echo "GD extension must be loaded. \r\n";
	exit();
}

/* Image map handling */
define("IMAGE_MAP_STORAGE_FILE", 680001);
define("IMAGE_MAP_STORAGE_SESSION", 680002);
/* Last generated chart layout */
define("CHART_LAST_LAYOUT_REGULAR", 680011);
define("CHART_LAST_LAYOUT_STACKED", 680012);
/* ImageMap string delimiter */
define("IMAGE_MAP_DELIMITER", chr(1));

class pImage extends pDraw
{
	/* Image settings, size, quality, .. */
	var $XSize = NULL; // Width of the picture
	var $YSize = NULL; // Height of the picture
	var $Picture = NULL; // GD picture object
	var $Antialias = TRUE; // Turn antialias on or off
	var $AntialiasQuality = 0; // Quality of the antialiasing implementation (0-1)

	// var $Mask		= "";				// Already drawn pixels mask (Filled circle implementation) # UNUSED

	var $TransparentBackground = FALSE; // Just to know if we need to flush the alpha channels when rendering
	/* Graph area settings */
	var $GraphAreaX1 = NULL; // Graph area X origin
	var $GraphAreaY1 = NULL; // Graph area Y origin
	var $GraphAreaX2 = NULL; // Graph area bottom right X position
	var $GraphAreaY2 = NULL; // Graph area bottom right Y position
	/* Scale settings */
	var $ScaleMinDivHeight = 20; // Minimum height for scame divs
	/* Font properties */
	var $FontName = "fonts/GeosansLight.ttf"; // Default font file
	var $FontSize = 12; // Default font size
	var $FontBox = NULL; // Return the bounding box of the last written string
	var $FontColorR = 0; // Default color settings
	var $FontColorG = 0; // Default color settings
	var $FontColorB = 0; // Default color settings
	var $FontColorA = 100; // Default transparency
	/* Shadow properties */
	var $Shadow = FALSE; // Turn shadows on or off
	var $ShadowX = NULL; // X Offset of the shadow
	var $ShadowY = NULL; // Y Offset of the shadow
	var $ShadowR = NULL; // R component of the shadow
	var $ShadowG = NULL; // G component of the shadow
	var $ShadowB = NULL; // B component of the shadow
	var $Shadowa = NULL; // Alpha level of the shadow
	/* Image map */
	var $ImageMap = NULL; // Aray containing the image map
	var $ImageMapIndex = "pChart"; // Name of the session array
	var $ImageMapStorageMode = NULL; // Save the current imagemap storage mode
	var $ImageMapAutoDelete = TRUE; // Automatic deletion of the image map temp files
	/* Data Set */
	var $DataSet = NULL; // Attached dataset
	/* Last generated chart info */
	var $LastChartLayout = CHART_LAST_LAYOUT_REGULAR; // Last layout : regular or stacked
	
	/* Class constructor */
	function __construct($XSize, $YSize, $DataSet = NULL, $TransparentBackground = FALSE)
	{
		$this->TransparentBackground = $TransparentBackground;
		if ($DataSet != NULL) {
			$this->DataSet = $DataSet;
		}

		$this->XSize = $XSize;
		$this->YSize = $YSize;
		$this->Picture = imagecreatetruecolor($XSize, $YSize);
		if ($this->TransparentBackground) {
			imagealphablending($this->Picture, FALSE);
			imagefilledrectangle($this->Picture, 0, 0, $XSize, $YSize, imagecolorallocatealpha($this->Picture, 255, 255, 255, 127));
			imagealphablending($this->Picture, TRUE);
			imagesavealpha($this->Picture, true);
		} else {
			imagefilledrectangle($this->Picture, 0, 0, $XSize, $YSize, $this->AllocateColor(255, 255, 255));
		}
	}
	
	function __destruct(){
		imagedestroy($this->Picture);
	}

	/* Enable / Disable and set shadow properties */
	function setShadow($Enabled = TRUE, array $Format = [])
	{
		$X = 2;
		$Y = 2;
		$R = 0;
		$G = 0;
		$B = 0;
		$Alpha = 10;
		
		/* Override defaults */
		extract($Format);
				
		$this->Shadow = $Enabled;
		$this->ShadowX = $X;
		$this->ShadowY = $Y;
		$this->ShadowR = $R;
		$this->ShadowG = $G;
		$this->ShadowB = $B;
		$this->Shadowa = $Alpha;
		
		if ($this->ShadowX == 0 || $this->ShadowY == 0){
			die("Invalid shadow specs");
		}
	}

	/* Set the graph area position */
	function setGraphArea($X1, $Y1, $X2, $Y2)
	{
		if ($X2 < $X1 || $X1 == $X2 || $Y2 < $Y1 || $Y1 == $Y2) {
			return (-1);
		}

		$this->GraphAreaX1 = $X1;
		$this->DataSet->Data["GraphArea"]["X1"] = $X1;
		$this->GraphAreaY1 = $Y1;
		$this->DataSet->Data["GraphArea"]["Y1"] = $Y1;
		$this->GraphAreaX2 = $X2;
		$this->DataSet->Data["GraphArea"]["X2"] = $X2;
		$this->GraphAreaY2 = $Y2;
		$this->DataSet->Data["GraphArea"]["Y2"] = $Y2;
	}

	/* Return the width of the picture */
	function getWidth()
	{
		return ($this->XSize);
	}

	/* Return the heigth of the picture */
	function getHeight()
	{
		return ($this->YSize);
	}

	/* Render the picture to a file */
	function render($FileName)
	{
		if ($this->TransparentBackground) {
			imagealphablending($this->Picture, false);
			imagesavealpha($this->Picture, true);
		}

		imagepng($this->Picture, $FileName);
	}

	/* Render the picture to a web browser stream */
	function stroke($BrowserExpire = FALSE)
	{
		if ($this->TransparentBackground) {
			imagealphablending($this->Picture, false);
			imagesavealpha($this->Picture, true);
		}

		if ($BrowserExpire) {
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
		}

		header('Content-type: image/png');
		imagepng($this->Picture);
	}

	/* Automatic output method based on the calling interface */
	function autoOutput($FileName = "output.png")
	{
		if (php_sapi_name() == "cli") {
			$this->Render($FileName);
		} else {
			$this->Stroke();
		}
	}

	/* Return the length between two points */
	function getLength($X1, $Y1, $X2, $Y2)
	{
		return (sqrt(pow(max($X1, $X2) - min($X1, $X2) , 2) + pow(max($Y1, $Y2) - min($Y1, $Y2) , 2)));
	}

	/* Return the orientation of a line */
	function getAngle($X1, $Y1, $X2, $Y2)
	{
		$Opposite = $Y2 - $Y1;
		$Adjacent = $X2 - $X1;
		$Angle = rad2deg(atan2($Opposite, $Adjacent));
		
		return (($Angle > 0) ? $Angle : (360 - abs($Angle)));
	}

	/* Return the surrounding box of text area */
	function getTextBox_deprecated($X, $Y, $FontName, $FontSize, $Angle, $Text)
	{
		$Size = imagettfbbox($FontSize, $Angle, $FontName, $Text);
		$Width = $this->getLength($Size[0], $Size[1], $Size[2], $Size[3]) + 1;
		$Height = $this->getLength($Size[2], $Size[3], $Size[4], $Size[5]) + 1;
		$RealPos[0]["X"] = $X;
		$RealPos[0]["Y"] = $Y;
		$RealPos[1]["X"] = cos((360 - $Angle) * PI / 180) * $Width + $RealPos[0]["X"];
		$RealPos[1]["Y"] = sin((360 - $Angle) * PI / 180) * $Width + $RealPos[0]["Y"];
		$RealPos[2]["X"] = cos((270 - $Angle) * PI / 180) * $Height + $RealPos[1]["X"];
		$RealPos[2]["Y"] = sin((270 - $Angle) * PI / 180) * $Height + $RealPos[1]["Y"];
		$RealPos[3]["X"] = cos((180 - $Angle) * PI / 180) * $Width + $RealPos[2]["X"];
		$RealPos[3]["Y"] = sin((180 - $Angle) * PI / 180) * $Width + $RealPos[2]["Y"];
		$RealPos[TEXT_ALIGN_BOTTOMLEFT]["X"] = $RealPos[0]["X"];
		$RealPos[TEXT_ALIGN_BOTTOMLEFT]["Y"] = $RealPos[0]["Y"];
		$RealPos[TEXT_ALIGN_BOTTOMRIGHT]["X"] = $RealPos[1]["X"];
		$RealPos[TEXT_ALIGN_BOTTOMRIGHT]["Y"] = $RealPos[1]["Y"];
		return ($RealPos);
	}

	/* Return the surrounding box of text area */
	function getTextBox($X, $Y, $FontName, $FontSize, $Angle, $Text)
	{
		$coords = imagettfbbox($FontSize, 0, $FontName, $Text);
		$a = deg2rad($Angle);
		$ca = cos($a);
		$sa = sin($a);
		$RealPos = [];
		for ($i = 0; $i < 7; $i+= 2) {
			$RealPos[$i / 2]["X"] = $X + round($coords[$i] * $ca + $coords[$i + 1] * $sa);
			$RealPos[$i / 2]["Y"] = $Y + round($coords[$i + 1] * $ca - $coords[$i] * $sa);
		}

		$RealPos[TEXT_ALIGN_BOTTOMLEFT]["X"] = $RealPos[0]["X"];
		$RealPos[TEXT_ALIGN_BOTTOMLEFT]["Y"] = $RealPos[0]["Y"];
		$RealPos[TEXT_ALIGN_BOTTOMRIGHT]["X"] = $RealPos[1]["X"];
		$RealPos[TEXT_ALIGN_BOTTOMRIGHT]["Y"] = $RealPos[1]["Y"];
		$RealPos[TEXT_ALIGN_TOPLEFT]["X"] = $RealPos[3]["X"];
		$RealPos[TEXT_ALIGN_TOPLEFT]["Y"] = $RealPos[3]["Y"];
		$RealPos[TEXT_ALIGN_TOPRIGHT]["X"] = $RealPos[2]["X"];
		$RealPos[TEXT_ALIGN_TOPRIGHT]["Y"] = $RealPos[2]["Y"];
		$RealPos[TEXT_ALIGN_BOTTOMMIDDLE]["X"] = ($RealPos[1]["X"] - $RealPos[0]["X"]) / 2 + $RealPos[0]["X"];
		$RealPos[TEXT_ALIGN_BOTTOMMIDDLE]["Y"] = ($RealPos[0]["Y"] - $RealPos[1]["Y"]) / 2 + $RealPos[1]["Y"];
		$RealPos[TEXT_ALIGN_TOPMIDDLE]["X"] = ($RealPos[2]["X"] - $RealPos[3]["X"]) / 2 + $RealPos[3]["X"];
		$RealPos[TEXT_ALIGN_TOPMIDDLE]["Y"] = ($RealPos[3]["Y"] - $RealPos[2]["Y"]) / 2 + $RealPos[2]["Y"];
		$RealPos[TEXT_ALIGN_MIDDLELEFT]["X"] = ($RealPos[0]["X"] - $RealPos[3]["X"]) / 2 + $RealPos[3]["X"];
		$RealPos[TEXT_ALIGN_MIDDLELEFT]["Y"] = ($RealPos[0]["Y"] - $RealPos[3]["Y"]) / 2 + $RealPos[3]["Y"];
		$RealPos[TEXT_ALIGN_MIDDLERIGHT]["X"] = ($RealPos[1]["X"] - $RealPos[2]["X"]) / 2 + $RealPos[2]["X"];
		$RealPos[TEXT_ALIGN_MIDDLERIGHT]["Y"] = ($RealPos[1]["Y"] - $RealPos[2]["Y"]) / 2 + $RealPos[2]["Y"];
		$RealPos[TEXT_ALIGN_MIDDLEMIDDLE]["X"] = ($RealPos[1]["X"] - $RealPos[3]["X"]) / 2 + $RealPos[3]["X"];
		$RealPos[TEXT_ALIGN_MIDDLEMIDDLE]["Y"] = ($RealPos[0]["Y"] - $RealPos[2]["Y"]) / 2 + $RealPos[2]["Y"];
		return ($RealPos);
	}

	/* Set current font properties */
	function setFontProperties(array $Format = [])
	{
		$R = -1;
		$G = -1;
		$B = -1;
		$Alpha = 100;
		$FontName = NULL;
		$FontSize = NULL;
		
		/* Override defaults */
		extract($Format);
		
		($R != - 1) AND $this->FontColorR = $R;
		($G != - 1) AND $this->FontColorG = $G;
		($B != - 1) AND $this->FontColorB = $B;
		($Alpha != NULL) AND $this->FontColorA = $Alpha;
		($FontName != NULL) AND $this->FontName = $FontName;
		($FontSize != NULL) AND $this->FontSize = $FontSize;
		
	}

	/* Returns the 1st decimal values (used to correct AA bugs) */
	function getFirstDecimal($Value)
	{
		$Values = preg_split("/\./", $Value);
		return (isset($Values[1])) ? substr($Values[1], 0, 1) : 0;
	}

	/* Attach a dataset to your pChart Object */
	function setDataSet(&$DataSet)
	{
		$this->DataSet = $DataSet;
	}

	/* Print attached dataset contents to STDOUT */
	function printDataSet()
	{
		print_r($this->DataSet);
	}

	/* Initialise the image map methods */
	function initialiseImageMap($Name = "pChart", $StorageMode = IMAGE_MAP_STORAGE_SESSION, $UniqueID = "imageMap", $StorageFolder = "tmp")
	{
		$this->ImageMapIndex = $Name;
		$this->ImageMapStorageMode = $StorageMode;
		if ($StorageMode == IMAGE_MAP_STORAGE_SESSION) {
			if (!isset($_SESSION)) {
				session_start();
			}

			$_SESSION[$this->ImageMapIndex] = NULL;
		} elseif ($StorageMode == IMAGE_MAP_STORAGE_FILE) {
			$this->ImageMapFileName = $UniqueID;
			$this->ImageMapStorageFolder = $StorageFolder;
			if (file_exists($StorageFolder . "/" . $UniqueID . ".map")) {
				unlink($StorageFolder . "/" . $UniqueID . ".map");
			}
		}
	}

	/* Add a zone to the image map */
	function addToImageMap($Type, $Plots, $Color = NULL, $Title = NULL, $Message = NULL, $HTMLEncode = FALSE)
	{
		if ($this->ImageMapStorageMode == NULL) {
			$this->initialiseImageMap();
		}

		/* Encode the characters in the imagemap in HTML standards */
		$Title = str_replace("&#8364;", "\u20AC", $Title); # Momchil TODO TEST THIS
		$Title = htmlentities($Title, ENT_QUOTES ); #, "ISO-8859-15"); # As of PHP 5.6 The default value for the encoding parameter = the default_charset config option.
		
		if ($HTMLEncode) {
			$Message = htmlentities($Message, ENT_QUOTES); #, "ISO-8859-15");
			#$Message = str_replace("&lt;", "<", $Message); # Seems covered Example #1 A htmlentities() example
			#$Message = str_replace("&gt;", ">", $Message); # http://php.net/manual/en/function.htmlentities.php
		}

		if ($this->ImageMapStorageMode == IMAGE_MAP_STORAGE_SESSION) {
			if (!isset($_SESSION)) {
				$this->initialiseImageMap();
			}
			$_SESSION[$this->ImageMapIndex][] = [$Type,$Plots,$Color,$Title,$Message];
			
		} elseif ($this->ImageMapStorageMode == IMAGE_MAP_STORAGE_FILE) {
			$Handle = fopen($this->ImageMapStorageFolder . "/" . $this->ImageMapFileName . ".map", 'a');
			fwrite($Handle, $Type . IMAGE_MAP_DELIMITER . $Plots . IMAGE_MAP_DELIMITER . $Color . IMAGE_MAP_DELIMITER . $Title . IMAGE_MAP_DELIMITER . $Message . "\r\n");
			fclose($Handle);
		}
	}

	/* Remove VOID values from an imagemap custom values array */
	function removeVOIDFromArray($SerieName, array $Values)
	{
		if (!isset($this->DataSet->Data["Series"][$SerieName])) {
			return (-1);
		}

		$Result = [];
		foreach($this->DataSet->Data["Series"][$SerieName]["Data"] as $Key => $Value) {
			if ($Value != VOID && isset($Values[$Key])) {
				$Result[] = $Values[$Key];
			}
		}

		return ($Result);
	}

	/* Replace the title of one image map serie */
	function replaceImageMapTitle($OldTitle, $NewTitle)
	{
		if ($this->ImageMapStorageMode == NULL) {
			return (-1);
		}

		if (is_array($NewTitle)) {
			$NewTitle = $this->removeVOIDFromArray($OldTitle, $NewTitle);
		}

		if ($this->ImageMapStorageMode == IMAGE_MAP_STORAGE_SESSION) {
			if (!isset($_SESSION)) {
				return (-1);
			}

			if (is_array($NewTitle)) {
				$ID = 0;
				foreach($_SESSION[$this->ImageMapIndex] as $Key => $Settings) {
					if ($Settings[3] == $OldTitle && isset($NewTitle[$ID])) {
						$_SESSION[$this->ImageMapIndex][$Key][3] = $NewTitle[$ID];
						$ID++;
					}
				}
			} else {
				foreach($_SESSION[$this->ImageMapIndex] as $Key => $Settings) {
					if ($Settings[3] == $OldTitle) {
						$_SESSION[$this->ImageMapIndex][$Key][3] = $NewTitle;
					}
				}
			}
			
		} elseif ($this->ImageMapStorageMode == IMAGE_MAP_STORAGE_FILE) {
			$TempArray = [];
			$Handle = @fopen($this->ImageMapStorageFolder . "/" . $this->ImageMapFileName . ".map", "r");
			if ($Handle) {
				while (($Buffer = fgets($Handle, 4096)) !== false) {
					$Fields = preg_split("/" . IMAGE_MAP_DELIMITER . "/", str_replace([chr(10),chr(13)] , "", $Buffer));
					$TempArray[] = [$Fields[0],$Fields[1],$Fields[2],$Fields[3],$Fields[4]];
				}

				fclose($Handle);
				if (is_array($NewTitle)) {
					$ID = 0;
					foreach($TempArray as $Key => $Settings) {
						if ($Settings[3] == $OldTitle && isset($NewTitle[$ID])) {
							$TempArray[$Key][3] = $NewTitle[$ID];
							$ID++;
						}
					}
				} else {
					foreach($TempArray as $Key => $Settings) {
						if ($Settings[3] == $OldTitle) {
							$TempArray[$Key][3] = $NewTitle;
						}
					}
				}

				$Handle = fopen($this->ImageMapStorageFolder . "/" . $this->ImageMapFileName . ".map", 'w');
				foreach($TempArray as $Key => $Settings) {
					fwrite($Handle, $Settings[0] . IMAGE_MAP_DELIMITER . $Settings[1] . IMAGE_MAP_DELIMITER . $Settings[2] . IMAGE_MAP_DELIMITER . $Settings[3] . IMAGE_MAP_DELIMITER . $Settings[4] . "\r\n");
				}

				fclose($Handle);
			}
		}
	}

	/* Replace the values of the image map contents */
	function replaceImageMapValues($Title, array $Values)
	{
		if ($this->ImageMapStorageMode == NULL) {
			return (-1);
		}

		$Values = $this->removeVOIDFromArray($Title, $Values);
		$ID = 0;
		if ($this->ImageMapStorageMode == IMAGE_MAP_STORAGE_SESSION) {
			if (!isset($_SESSION)) {
				return (-1);
			}

			foreach($_SESSION[$this->ImageMapIndex] as $Key => $Settings) {
				if ($Settings[3] == $Title) {
					if (isset($Values[$ID])) {
						$_SESSION[$this->ImageMapIndex][$Key][4] = $Values[$ID];
					}
					$ID++;
				}
			}
			
		} elseif ($this->ImageMapStorageMode == IMAGE_MAP_STORAGE_FILE) {
			$TempArray = [];
			$Handle = @fopen($this->ImageMapStorageFolder . "/" . $this->ImageMapFileName . ".map", "r");
			if ($Handle) {
				while (($Buffer = fgets($Handle, 4096)) !== false) {
					$Fields = preg_split("/" . IMAGE_MAP_DELIMITER . "/", str_replace([chr(10) ,chr(13)] , "", $Buffer));
					$TempArray[] = [$Fields[0],$Fields[1],$Fields[2],$Fields[3],$Fields[4]];
				}

				fclose($Handle);
				foreach($TempArray as $Key => $Settings) {
					if ($Settings[3] == $Title) {
						if (isset($Values[$ID])) {
							$TempArray[$Key][4] = $Values[$ID];
						}
						$ID++;
					}
				}

				$Handle = fopen($this->ImageMapStorageFolder . "/" . $this->ImageMapFileName . ".map", 'w');
				foreach($TempArray as $Key => $Settings) {
					fwrite($Handle, $Settings[0] . IMAGE_MAP_DELIMITER . $Settings[1] . IMAGE_MAP_DELIMITER . $Settings[2] . IMAGE_MAP_DELIMITER . $Settings[3] . IMAGE_MAP_DELIMITER . $Settings[4] . "\r\n");
				}

				fclose($Handle);
			}
		}
	}

	/* Dump the image map */
	function dumpImageMap($Name = "pChart", $StorageMode = IMAGE_MAP_STORAGE_SESSION, $UniqueID = "imageMap", $StorageFolder = "tmp")
	{
		$this->ImageMapIndex = $Name;
		$this->ImageMapStorageMode = $StorageMode;
		if ($this->ImageMapStorageMode == IMAGE_MAP_STORAGE_SESSION) {
			if (!isset($_SESSION)) {
				session_start();
			}

			if ($_SESSION[$Name] != NULL) {
				foreach($_SESSION[$Name] as $Key => $Params) {
					echo $Params[0] . IMAGE_MAP_DELIMITER . $Params[1] . IMAGE_MAP_DELIMITER . $Params[2] . IMAGE_MAP_DELIMITER . $Params[3] . IMAGE_MAP_DELIMITER . $Params[4] . "\r\n";
				}
			}
			
		} elseif ($this->ImageMapStorageMode == IMAGE_MAP_STORAGE_FILE) {
			if (file_exists($StorageFolder . "/" . $UniqueID . ".map")) {
				$Handle = @fopen($StorageFolder . "/" . $UniqueID . ".map", "r");
				if ($Handle) {
					while (($Buffer = fgets($Handle, 4096)) !== false) {
						echo $Buffer;
					}
				}

				fclose($Handle);
				if ($this->ImageMapAutoDelete) {
					unlink($StorageFolder . "/" . $UniqueID . ".map");
				}
			}
		}

		/* When the image map is returned to the client, the script ends */
		exit();
	}

	/* Return the HTML converted color from the RGB composite values */
	function toHTMLColor($R, $G, $B) # Momchil: Not worth caching
	{
		
		$R = dechex(max(min(255, $R), 0));
		$G = dechex(max(min(255, $G), 0));
		$B = dechex(max(min(255, $B), 0));
			
		$Color = "#" . (strlen($R) < 2 ? '0' : '') . $R;
		$Color.= (strlen($G) < 2 ? '0' : '') . $G;
		$Color.= (strlen($B) < 2 ? '0' : '') . $B;
		
		return ($Color);
	}

	/* Reverse an array of points */
	function reversePlots($Plots)
	{
		$Result = [];
		for ($i = count($Plots) - 2; $i >= 0; $i = $i - 2) {
			$Result[] = $Plots[$i];
			$Result[] = $Plots[$i + 1];
		}

		return ($Result);
	}

	/* Mirror Effect */
	function drawAreaMirror($X, $Y, $Width, $Height, array $Format = [])
	{
		$StartAlpha = isset($Format["StartAlpha"]) ? $Format["StartAlpha"] : 80;
		$EndAlpha = isset($Format["EndAlpha"]) ? $Format["EndAlpha"] : 0;
		$AlphaStep = ($StartAlpha - $EndAlpha) / $Height;
		$Picture = imagecreatetruecolor($this->XSize, $this->YSize);
		imagecopy($Picture, $this->Picture, 0, 0, 0, 0, $this->XSize, $this->YSize);
		for ($i = 1; $i <= $Height; $i++) {
			if ($Y + ($i - 1) < $this->YSize && $Y - $i > 0) {
				imagecopymerge($Picture, $this->Picture, $X, $Y + ($i - 1) , $X, $Y - $i, $Width, 1, $StartAlpha - $AlphaStep * $i);
			}
		}

		imagecopy($this->Picture, $Picture, 0, 0, 0, 0, $this->XSize, $this->YSize);
		
		imagedestroy($Picture);
	}
}

?>