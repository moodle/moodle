<?php
/*
pIndicator - class to draw indicators

Version     : 2.1.4
Made by     : Jean-Damien POGOLOTTI
Last Update : 19/01/2014

This file can be distributed under the license you can find at :

http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

define("INDICATOR_CAPTION_DEFAULT", 700001);
define("INDICATOR_CAPTION_EXTENDED", 700002);
define("INDICATOR_CAPTION_INSIDE", 700011);
define("INDICATOR_CAPTION_BOTTOM", 700012);
define("INDICATOR_VALUE_BUBBLE", 700021);
define("INDICATOR_VALUE_LABEL", 700022);

/* pIndicator class definition */
class pIndicator
{
	var $pChartObject;
	/* Class creator */
	function __construct($pChartObject)
	{
		$this->pChartObject = $pChartObject;
	}

	/* Draw an indicator */
	function draw($X, $Y, $Width, $Height, array $Format = [])
	{

		/* No section, let's die */
		if (isset($Format["IndicatorSections"])){
			$IndicatorSections = $Format["IndicatorSections"];
		} else {
			return (0);
		}
		$Values = VOID;
		$ValueDisplay = INDICATOR_VALUE_BUBBLE;
		$SectionsMargin = 4;
		$DrawLeftHead = TRUE;
		$DrawRightHead = TRUE;
		$HeadSize = floor($Height / 4);
		$TextPadding = 4;
		$CaptionLayout = INDICATOR_CAPTION_EXTENDED;
		$CaptionPosition = INDICATOR_CAPTION_INSIDE;
		$CaptionColorFactor = NULL;
		$CaptionR = 255;
		$CaptionG = 255;
		$CaptionB = 255;
		$CaptionAlpha = 100;
		$SubCaptionColorFactor = NULL;
		$SubCaptionR = 50;
		$SubCaptionG = 50;
		$SubCaptionB = 50;
		$SubCaptionAlpha = 100;
		$ValueFontName = $this->pChartObject->FontName;
		$ValueFontSize = $this->pChartObject->FontSize;
		$CaptionFontName = $this->pChartObject->FontName;
		$CaptionFontSize = $this->pChartObject->FontSize;
		$Unit = "";
		
		/* Override defaults */
		extract($Format);
		
		/* Convert the Values to display to an array if needed */
		(!is_array($Values)) AND $Values = [$Values];
		
		/* Determine indicator visual configuration */
		$OverallMin = $IndicatorSections[0]["End"];
		$OverallMax = $IndicatorSections[0]["Start"];
		foreach($IndicatorSections as $Key => $Settings) {
			($Settings["End"] > $OverallMax) AND $OverallMax = $Settings["End"];
			($Settings["Start"] < $OverallMin) AND $OverallMin = $Settings["Start"];
		}

		$RealWidth = $Width - (count($IndicatorSections) - 1) * $SectionsMargin;
		$XScale = $RealWidth / ($OverallMax - $OverallMin);
		$X1 = $X;
		$ValuesPos = [];
		foreach($IndicatorSections as $Key => $Settings) {
			$Color = ["R" => $Settings["R"],"G" => $Settings["G"],"B" => $Settings["B"]];
			$Caption = $Settings["Caption"];
			$SubCaption = $Settings["Start"] . " - " . $Settings["End"];
			$X2 = $X1 + ($Settings["End"] - $Settings["Start"]) * $XScale;
			if ($Key == 0 && $DrawLeftHead) {
				$Poly = [$X1 - 1, $Y, $X1 - 1, $Y + $Height, $X1 - 1 - $HeadSize, $Y + ($Height / 2) ];
				$this->pChartObject->drawPolygon($Poly, $Color);
				$this->pChartObject->drawLine($X1 - 2, $Y, $X1 - 2 - $HeadSize, $Y + ($Height / 2) , $Color);
				$this->pChartObject->drawLine($X1 - 2, $Y + $Height, $X1 - 2 - $HeadSize, $Y + ($Height / 2) , $Color);
			}

			/* Determine the position of the breaks */
			$Break = [];
			foreach($Values as $iKey => $Value) {
				if ($Value >= $Settings["Start"] && $Value <= $Settings["End"]) {
					$XBreak = $X1 + ($Value - $Settings["Start"]) * $XScale;
					$ValuesPos[$Value] = $XBreak;
					$Break[] = floor($XBreak);
				}
			}

			if ($ValueDisplay == INDICATOR_VALUE_LABEL) {
				if (count($Break) == 0){
					$this->pChartObject->drawFilledRectangle($X1, $Y, $X2, $Y + $Height, $Color);
				} else {
					sort($Break);
					$Poly = [$X1, $Y];
					$LastPointWritten = FALSE;
					foreach($Break as $iKey => $Value) {
						
						if ($Value - 5 >= $X1) {
							$Poly[] = $Value - 5;
							$Poly[] = $Y;
						} elseif ($X1 - ($Value - 5) > 0) {
							$Offset = $X1 - ($Value - 5);
							$Poly = [$X1, $Y + $Offset];
						}

						$Poly[] = $Value;
						$Poly[] = $Y + 5;
						
						if ($Value + 5 <= $X2) {
							$Poly[] = $Value + 5;
							$Poly[] = $Y;
						} elseif (($Value + 5) > $X2) {
							$Offset = ($Value + 5) - $X2;
							$Poly[] = $X2;
							$Poly[] = $Y + $Offset;
							$LastPointWritten = TRUE;
						}
					}

					if (!$LastPointWritten) {
						$Poly[] = $X2;
						$Poly[] = $Y;
					}

					$Poly[] = $X2;
					$Poly[] = $Y + $Height;
					$Poly[] = $X1;
					$Poly[] = $Y + $Height;
					$this->pChartObject->drawPolygon($Poly, $Color);
				}
			}
			else {
				$this->pChartObject->drawFilledRectangle($X1, $Y, $X2, $Y + $Height, $Color);
			}

			if ($Key == count($IndicatorSections) - 1 && $DrawRightHead) {
				$Poly = [$X2 + 1, $Y, $X2 + 1, $Y + $Height, $X2 + 1 + $HeadSize, $Y + ($Height / 2) ];
				$this->pChartObject->drawPolygon($Poly, $Color);
				$this->pChartObject->drawLine($X2 + 1, $Y, $X2 + 1 + $HeadSize, $Y + ($Height / 2) , $Color);
				$this->pChartObject->drawLine($X2 + 1, $Y + $Height, $X2 + 1 + $HeadSize, $Y + ($Height / 2) , $Color);
			}

			if ($CaptionPosition == INDICATOR_CAPTION_INSIDE) {
				$TxtPos = $this->pChartObject->getTextBox($X1, $Y + $Height + $TextPadding, $CaptionFontName, $CaptionFontSize, 0, $Caption);
				$YOffset = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]) + $TextPadding;
				if ($CaptionLayout == INDICATOR_CAPTION_EXTENDED) {
					$TxtPos = $this->pChartObject->getTextBox($X1, $Y + $Height + $TextPadding, $CaptionFontName, $CaptionFontSize, 0, $SubCaption);
					$YOffset = $YOffset + ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]) + $TextPadding * 2;
				}

				$XOffset = $TextPadding;
			} else {
				$YOffset = 0;
				$XOffset = 0;
			}

			if ($CaptionColorFactor == NULL) {
				$CaptionColor = ["Align" => TEXT_ALIGN_TOPLEFT,"FontName" => $CaptionFontName,"FontSize" => $CaptionFontSize,"R" => $CaptionR,"G" => $CaptionG,"B" => $CaptionB,"Alpha" => $CaptionAlpha];
			} else {
				$CaptionColor = ["Align" => TEXT_ALIGN_TOPLEFT,"FontName" => $CaptionFontName,"FontSize" => $CaptionFontSize,"R" => $Settings["R"] + $CaptionColorFactor,"G" => $Settings["G"] + $CaptionColorFactor,"B" => $Settings["B"] + $CaptionColorFactor];
			}

			if ($SubCaptionColorFactor == NULL) {
				$SubCaptionColor = ["Align" => TEXT_ALIGN_TOPLEFT,"FontName" => $CaptionFontName,"FontSize" => $CaptionFontSize,"R" => $SubCaptionR,"G" => $SubCaptionG,"B" => $SubCaptionB,"Alpha" => $SubCaptionAlpha];
			} else {
				$SubCaptionColor = ["Align" => TEXT_ALIGN_TOPLEFT,"FontName" => $CaptionFontName,"FontSize" => $CaptionFontSize,"R" => $Settings["R"] + $SubCaptionColorFactor,"G" => $Settings["G"] + $SubCaptionColorFactor,"B" => $Settings["B"] + $SubCaptionColorFactor];
			}

			$RestoreShadow = $this->pChartObject->Shadow;
			$this->pChartObject->Shadow = FALSE;
			if ($CaptionLayout == INDICATOR_CAPTION_DEFAULT) {
				$this->pChartObject->drawText($X1, $Y + $Height + $TextPadding, $Caption, $CaptionColor);
			} elseif ($CaptionLayout == INDICATOR_CAPTION_EXTENDED) {
				$TxtPos = $this->pChartObject->getTextBox($X1, $Y + $Height + $TextPadding, $CaptionFontName, $CaptionFontSize, 0, $Caption);
				$CaptionHeight = $TxtPos[0]["Y"] - $TxtPos[2]["Y"];
				$this->pChartObject->drawText($X1 + $XOffset, $Y + $Height - $YOffset + $TextPadding, $Caption, $CaptionColor);
				$this->pChartObject->drawText($X1 + $XOffset, $Y + $Height - $YOffset + $CaptionHeight + $TextPadding * 2, $SubCaption, $SubCaptionColor);
			}

			$this->pChartObject->Shadow = $RestoreShadow;
			$X1 = $X2 + $SectionsMargin;
		}

		$RestoreShadow = $this->pChartObject->Shadow;
		$this->pChartObject->Shadow = FALSE;
		foreach($Values as $Key => $Value) {
			if ($Value >= $OverallMin && $Value <= $OverallMax) {
				foreach($IndicatorSections as $Key => $Settings) {
					if ($Value >= $Settings["Start"] && $Value <= $Settings["End"]) {
						$X1 = $ValuesPos[$Value]; //$X + $Key*$SectionsMargin + ($Value - $OverallMin) * $XScale;
						if ($ValueDisplay == INDICATOR_VALUE_BUBBLE) {
							$TxtPos = $this->pChartObject->getTextBox($X1, $Y, $ValueFontName, $ValueFontSize, 0, $Value . $Unit);
							$Radius = floor(($TxtPos[1]["X"] - $TxtPos[0]["X"] + $TextPadding * 4) / 2);
							$this->pChartObject->drawFilledCircle($X1, $Y, $Radius + 4, ["R" => $Settings["R"] + 20,"G" => $Settings["G"] + 20,"B" => $Settings["B"] + 20]);
							$this->pChartObject->drawFilledCircle($X1, $Y, $Radius, ["R" => 255,"G" => 255,"B" => 255]);
							$this->pChartObject->drawText($X1 - 1, $Y - 1, $Value . $Unit, ["Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontName" => $ValueFontName,"FontSize" => $ValueFontSize]);
						} elseif ($ValueDisplay == INDICATOR_VALUE_LABEL) {
							$Caption = array(
								"Format" => ["R" => $Settings["R"],"G" => $Settings["G"],"B" => $Settings["B"],"Alpha" => 100],
								"Caption" => $Value . $Unit
							);
							$this->pChartObject->drawLabelBox(floor($X1) , floor($Y) + 2, "Value - " . $Settings["Caption"], $Caption);
						}
					}

					$X1 = $X2 + $SectionsMargin;
				}
			}
		}

		$this->pChartObject->Shadow = $RestoreShadow;
	}
}

?>