<?php
/*
pIndicator - class to draw indicators

Version     : 2.4.0-dev
Made by     : Jean-Damien POGOLOTTI
Maintainedby: Momchil Bozhinov
Last Update : 01/09/2019

This file can be distributed under the license you can find at:
http://www.pchart.net/license

You can find the whole class documentation on the pChart web site.
*/

namespace pChart;

define("INDICATOR_CAPTION_DEFAULT", 700001);
define("INDICATOR_CAPTION_EXTENDED", 700002);
define("INDICATOR_CAPTION_INSIDE", 700011);
define("INDICATOR_CAPTION_BOTTOM", 700012);
define("INDICATOR_VALUE_BUBBLE", 700021);
define("INDICATOR_VALUE_LABEL", 700022);

/* pIndicator class definition */
class pIndicator
{
	private $myPicture;

	function __construct(\pChart\pDraw $pChartObject)
	{
		$this->myPicture = $pChartObject;
	}

	/* Draw an indicator */
	public function draw(int $X, int $Y, int $Width, int $Height, array $Format = [])
	{
		$fontProperties = $this->myPicture->getFont();

		/* No section */
		if (isset($Format["IndicatorSections"])){
			$IndicatorSections = $Format["IndicatorSections"];
		} else {
			throw pException::InvalidInput("Missing indicator settings");
		}
		$Values = [];
		$ValueDisplay = INDICATOR_VALUE_BUBBLE;
		$SectionsMargin = 4;
		$DrawLeftHead = TRUE;
		$DrawRightHead = TRUE;
		$HeadSize = floor($Height / 4);
		$TextPadding = 4;
		$CaptionLayout = INDICATOR_CAPTION_EXTENDED;
		$CaptionPosition = INDICATOR_CAPTION_INSIDE;
		$CaptionColorFactor = NULL;
		$CaptionColor = new pColor(255);
		$SubCaptionColorFactor = NULL;
		$SubCaptionColor = new pColor(50);
		$FontName = $fontProperties['Name'];
		$FontSize = $fontProperties['Size'];
		$CaptionFontName = $fontProperties['Name'];
		$CaptionFontSize = $fontProperties['Size'];
		$Unit = "";

		/* Override defaults */
		extract($Format);

		/* Determine indicator visual configuration */
		$OverallMin = $IndicatorSections[0]["End"];
		$OverallMax = $IndicatorSections[0]["Start"];
		foreach($IndicatorSections as $Settings) {
			($Settings["End"] > $OverallMax) AND $OverallMax = $Settings["End"];
			($Settings["Start"] < $OverallMin) AND $OverallMin = $Settings["Start"];
		}

		$LastSection = count($IndicatorSections) - 1;
		$RealWidth = $Width - $LastSection * $SectionsMargin;
		$XScale = $RealWidth / ($OverallMax - $OverallMin);
		$X1 = $X;
		$ValuesPos = [];
		$ShadowSpec = $this->myPicture->getShadow();
		$this->myPicture->setShadow(FALSE);

		foreach($IndicatorSections as $Key => $Settings) {
			$Color = ["Color" => $Settings['Color']];
			$Caption = $Settings["Caption"];
			$SubCaption = $Settings["Start"] . " - " . $Settings["End"];
			$X2 = $X1 + ($Settings["End"] - $Settings["Start"]) * $XScale;
			if ($Key == 0 && $DrawLeftHead) {
				$Poly = [$X1 - 1, $Y, $X1 - 1, $Y + $Height, $X1 - 1 - $HeadSize, $Y + ($Height / 2) ];
				$this->myPicture->drawPolygon($Poly, $Color);
				$this->myPicture->drawLine($X1 - 2, $Y, $X1 - 2 - $HeadSize, $Y + ($Height / 2), $Color);
				$this->myPicture->drawLine($X1 - 2, $Y + $Height, $X1 - 2 - $HeadSize, $Y + ($Height / 2), $Color);
			}

			/* Determine the position of the breaks */
			$Break = [];
			foreach($Values as $Value) {
				if ($Value >= $Settings["Start"] && $Value <= $Settings["End"]) {
					$XBreak = $X1 + ($Value - $Settings["Start"]) * $XScale;
					$ValuesPos[$Value] = $XBreak;
					$Break[] = floor($XBreak);
				}
			}

			if ($ValueDisplay == INDICATOR_VALUE_LABEL || empty($Break)) {

				sort($Break);
				$Poly = [$X1, $Y];
				$LastPointWritten = FALSE;
				foreach($Break as $Value) {

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
				$this->myPicture->drawPolygon($Poly, $Color);

			} else {
				$this->myPicture->drawFilledRectangle($X1, $Y, $X2, $Y + $Height, $Color);
			}

			if ($Key == $LastSection && $DrawRightHead) {
				$Poly = [$X2 + 1, $Y, $X2 + 1, $Y + $Height, $X2 + 1 + $HeadSize, $Y + ($Height / 2) ];
				$this->myPicture->drawPolygon($Poly, $Color);
				$this->myPicture->drawLine($X2 + 1, $Y, $X2 + 1 + $HeadSize, $Y + ($Height / 2), $Color);
				$this->myPicture->drawLine($X2 + 1, $Y + $Height, $X2 + 1 + $HeadSize, $Y + ($Height / 2), $Color);
			}

			if ($CaptionPosition == INDICATOR_CAPTION_INSIDE) {
				$TxtPos = $this->myPicture->getTextBox($X1, $Y + $Height + $TextPadding, $CaptionFontName, $CaptionFontSize, 0, $Caption);
				$YOffset = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]) + $TextPadding;
				if ($CaptionLayout == INDICATOR_CAPTION_EXTENDED) {
					$TxtPos = $this->myPicture->getTextBox($X1, $Y + $Height + $TextPadding, $CaptionFontName, $CaptionFontSize, 0, $SubCaption);
					$YOffset = $YOffset + ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]) + $TextPadding * 2;
				}

				$XOffset = $TextPadding;
			} else {
				$YOffset = 0;
				$XOffset = 0;
			}

			if (!is_null($CaptionColorFactor)) {
				$CaptionColor = $Settings['Color']->newOne()->RGBChange($CaptionColorFactor);
			}
			$CaptionSettings = ["Align" => TEXT_ALIGN_TOPLEFT,"FontName" => $CaptionFontName,"FontSize" => $CaptionFontSize,"Color" => $CaptionColor];

			if (!is_null($SubCaptionColorFactor)) {
				$SubCaptionColor = $Settings['Color']->newOne()->RGBChange($SubCaptionColorFactor);
			}
			$SubCaptionSettgins = ["Align" => TEXT_ALIGN_TOPLEFT,"FontName" => $CaptionFontName,"FontSize" => $CaptionFontSize,"Color" => $SubCaptionColor];

			if ($CaptionLayout == INDICATOR_CAPTION_DEFAULT) {
				$this->myPicture->drawText($X1, $Y + $Height + $TextPadding, $Caption, $CaptionSettings);
			} elseif ($CaptionLayout == INDICATOR_CAPTION_EXTENDED) {
				$TxtPos = $this->myPicture->getTextBox($X1, $Y + $Height + $TextPadding, $CaptionFontName, $CaptionFontSize, 0, $Caption);
				$CaptionHeight = $TxtPos[0]["Y"] - $TxtPos[2]["Y"];
				$this->myPicture->drawText($X1 + $XOffset, $Y + $Height - $YOffset + $TextPadding, $Caption, $CaptionSettings);
				$this->myPicture->drawText($X1 + $XOffset, $Y + $Height - $YOffset + $CaptionHeight + $TextPadding * 2, $SubCaption, $SubCaptionSettgins);
			}

			$X1 = $X2 + $SectionsMargin;
		}

		foreach($Values as $Value) {
			if ($Value >= $OverallMin && $Value <= $OverallMax) {
				foreach($IndicatorSections as $Settings) {
					if ($Value >= $Settings["Start"] && $Value <= $Settings["End"]) {
						$X1 = $ValuesPos[$Value]; //$X + $Key*$SectionsMargin + ($Value - $OverallMin) * $XScale;
						if ($ValueDisplay == INDICATOR_VALUE_BUBBLE) {
							$TxtPos = $this->myPicture->getTextBox($X1, $Y, $FontName, $FontSize, 0, strval($Value) . $Unit);
							$Radius = floor(($TxtPos[1]["X"] - $TxtPos[0]["X"] + $TextPadding * 4) / 2);
							$this->myPicture->drawFilledCircle($X1, $Y, $Radius + 4, ["Color" => $Settings["Color"]->newOne()->RGBChange(20)]);
							$this->myPicture->drawFilledCircle($X1, $Y, $Radius, ["Color" => new pColor(255)]);
							$this->myPicture->drawText($X1 + 1, $Y, strval($Value) . $Unit, ["Align" => TEXT_ALIGN_MIDDLEMIDDLE,"FontName" => $FontName,"FontSize" => $FontSize]);
						} elseif ($ValueDisplay == INDICATOR_VALUE_LABEL) {
							$this->myPicture->drawLabelBox(floor($X1), floor($Y) + 2, "Value - " . $Settings["Caption"], ["Color" => $Settings["Color"]->newOne()->AlphaSet(100),"Caption" => strval($Value) . $Unit]);
						}
					}

					$X1 = $X2 + $SectionsMargin;
				}
			}
		}

		$this->myPicture->restoreShadow($ShadowSpec);
	}
}
