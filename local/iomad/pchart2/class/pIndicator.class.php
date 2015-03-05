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

 define("INDICATOR_CAPTION_DEFAULT"	, 700001);
 define("INDICATOR_CAPTION_EXTENDED"	, 700002);

 define("INDICATOR_CAPTION_INSIDE"	, 700011);
 define("INDICATOR_CAPTION_BOTTOM"	, 700012);

 define("INDICATOR_VALUE_BUBBLE"	, 700021);
 define("INDICATOR_VALUE_LABEL"		, 700022);

 /* pIndicator class definition */
 class pIndicator
  {
   var $pChartObject;

   /* Class creator */
   function pIndicator($pChartObject)
    {
     $this->pChartObject = $pChartObject;
    }

   /* Draw an indicator */
   function draw($X,$Y,$Width,$Height,$Format="")
    {
     $Values			= isset($Format["Values"]) ? $Format["Values"] : VOID;
     $IndicatorSections		= isset($Format["IndicatorSections"]) ? $Format["IndicatorSections"] : NULL;
     $ValueDisplay		= isset($Format["ValueDisplay"]) ? $Format["ValueDisplay"] : INDICATOR_VALUE_BUBBLE;
     $SectionsMargin		= isset($Format["SectionsMargin"]) ? $Format["SectionsMargin"] : 4;
     $DrawLeftHead		= isset($Format["DrawLeftHead"]) ? $Format["DrawLeftHead"] : TRUE;
     $DrawRightHead		= isset($Format["DrawRightHead"]) ? $Format["DrawRightHead"] : TRUE;
     $HeadSize			= isset($Format["HeadSize"]) ? $Format["HeadSize"] : floor($Height/4);
     $TextPadding		= isset($Format["TextPadding"]) ? $Format["TextPadding"] : 4;
     $CaptionLayout		= isset($Format["CaptionLayout"]) ? $Format["CaptionLayout"] : INDICATOR_CAPTION_EXTENDED;
     $CaptionPosition		= isset($Format["CaptionPosition"]) ? $Format["CaptionPosition"] : INDICATOR_CAPTION_INSIDE;
     $CaptionColorFactor	= isset($Format["CaptionColorFactor"]) ? $Format["CaptionColorFactor"] : NULL;
     $CaptionR			= isset($Format["CaptionR"]) ? $Format["CaptionR"] : 255;
     $CaptionG			= isset($Format["CaptionG"]) ? $Format["CaptionG"] : 255;
     $CaptionB			= isset($Format["CaptionB"]) ? $Format["CaptionB"] : 255;
     $CaptionAlpha		= isset($Format["CaptionAlpha"]) ? $Format["CaptionAlpha"] : 100;
     $SubCaptionColorFactor	= isset($Format["SubCaptionColorFactor"]) ? $Format["SubCaptionColorFactor"] : NULL;
     $SubCaptionR		= isset($Format["SubCaptionR"]) ? $Format["SubCaptionR"] : 50;
     $SubCaptionG		= isset($Format["SubCaptionG"]) ? $Format["SubCaptionG"] : 50;
     $SubCaptionB		= isset($Format["SubCaptionB"]) ? $Format["SubCaptionB"] : 50;
     $SubCaptionAlpha		= isset($Format["SubCaptionAlpha"]) ? $Format["SubCaptionAlpha"] : 100;
     $ValueFontName		= isset($Format["ValueFontName"]) ? $Format["ValueFontName"] : $this->pChartObject->FontName;
     $ValueFontSize		= isset($Format["ValueFontSize"]) ? $Format["ValueFontSize"] : $this->pChartObject->FontSize;
     $CaptionFontName		= isset($Format["CaptionFontName"]) ? $Format["CaptionFontName"] : $this->pChartObject->FontName;
     $CaptionFontSize		= isset($Format["CaptionFontSize"]) ? $Format["CaptionFontSize"] : $this->pChartObject->FontSize;
     $Unit			= isset($Format["Unit"]) ? $Format["Unit"] : "";

     /* Convert the Values to display to an array if needed */
     if ( !is_array($Values) ) { $Value = $Values; $Values = ""; $Values[] = $Value; }

     /* No section, let's die */
     if ( $IndicatorSections == NULL ) { return(0); }

     /* Determine indicator visual configuration */
     $OverallMin = $IndicatorSections[0]["End"]; $OverallMax = $IndicatorSections[0]["Start"];
     foreach ($IndicatorSections as $Key => $Settings)
      {
       if ( $Settings["End"] > $OverallMax )   { $OverallMax = $Settings["End"]; }
       if ( $Settings["Start"] < $OverallMin ) { $OverallMin = $Settings["Start"]; }
      }
     $RealWidth = $Width - (count($IndicatorSections)-1)*$SectionsMargin;
     $XScale    = $RealWidth / ($OverallMax-$OverallMin);

     $X1 = $X; $ValuesPos = "";
     foreach ($IndicatorSections as $Key => $Settings)
      {
       $Color      = array("R"=>$Settings["R"],"G"=>$Settings["G"],"B"=>$Settings["B"]);
       $Caption    = $Settings["Caption"];
       $SubCaption = $Settings["Start"]." - ".$Settings["End"];

       $X2 = $X1 + ($Settings["End"] - $Settings["Start"]) * $XScale;

       if ( $Key == 0 && $DrawLeftHead )
        {
         $Poly = ""; $Poly[] = $X1-1; $Poly[] = $Y; $Poly[] = $X1-1; $Poly[] = $Y+$Height; $Poly[] = $X1-1-$HeadSize; $Poly[] = $Y+($Height/2);
         $this->pChartObject->drawPolygon($Poly,$Color);
         $this->pChartObject->drawLine($X1-2,$Y,$X1-2-$HeadSize,$Y+($Height/2),$Color);
         $this->pChartObject->drawLine($X1-2,$Y+$Height,$X1-2-$HeadSize,$Y+($Height/2),$Color);
        }

       /* Determine the position of the breaks */
       $Break = "";
       foreach($Values as $iKey => $Value)
        {
         if ( $Value >= $Settings["Start"] && $Value <= $Settings["End"] )
          {
           $XBreak  = $X1 + ($Value - $Settings["Start"]) * $XScale;
           $ValuesPos[$Value] = $XBreak;
           $Break[] = floor($XBreak);
          }
        }

       if ( $ValueDisplay == INDICATOR_VALUE_LABEL )
        {
         if ( $Break == "" )
          $this->pChartObject->drawFilledRectangle($X1,$Y,$X2,$Y+$Height,$Color);
         else
          {
           sort($Break);
           $Poly = ""; $Poly[] = $X1; $Poly[] = $Y; $LastPointWritten = FALSE;
           foreach($Break as $iKey => $Value)
            {
             if ( $Value-5 >= $X1 )
              { $Poly[] = $Value-5; $Poly[] = $Y; }
             elseif ($X1 - ($Value-5) > 0 )
              {
               $Offset = $X1 - ($Value-5);
               $Poly = ""; $Poly[] = $X1; $Poly[] = $Y + $Offset;
              }

             $Poly[] = $Value;   $Poly[] = $Y+5;

             if ( $Value+5 <= $X2 )
              { $Poly[] = $Value+5; $Poly[] = $Y; }
             elseif (($Value+5) > $X2 )
              {
               $Offset = ($Value+5) - $X2;
               $Poly[] = $X2; $Poly[] = $Y + $Offset;
               $LastPointWritten = TRUE;
              }

            }
           if ( !$LastPointWritten ) { $Poly[] = $X2; $Poly[] = $Y; }
           $Poly[] = $X2; $Poly[] = $Y+$Height;
           $Poly[] = $X1; $Poly[] = $Y+$Height;

           $this->pChartObject->drawPolygon($Poly,$Color);
          }
        }
       else
        $this->pChartObject->drawFilledRectangle($X1,$Y,$X2,$Y+$Height,$Color);

       if ( $Key == count($IndicatorSections)-1 && $DrawRightHead )
        {
         $Poly = ""; $Poly[] = $X2+1; $Poly[] = $Y; $Poly[] = $X2+1; $Poly[] = $Y+$Height; $Poly[] = $X2+1+$HeadSize; $Poly[] = $Y+($Height/2);
         $this->pChartObject->drawPolygon($Poly,$Color);
         $this->pChartObject->drawLine($X2+1,$Y,$X2+1+$HeadSize,$Y+($Height/2),$Color);
         $this->pChartObject->drawLine($X2+1,$Y+$Height,$X2+1+$HeadSize,$Y+($Height/2),$Color);
        }

       if ( $CaptionPosition == INDICATOR_CAPTION_INSIDE )
        {
         $TxtPos  = $this->pChartObject->getTextBox($X1,$Y+$Height+$TextPadding,$CaptionFontName,$CaptionFontSize,0,$Caption);
         $YOffset = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]) + $TextPadding;

         if ( $CaptionLayout == INDICATOR_CAPTION_EXTENDED )
          {
           $TxtPos  = $this->pChartObject->getTextBox($X1,$Y+$Height+$TextPadding,$CaptionFontName,$CaptionFontSize,0,$SubCaption);
           $YOffset = $YOffset + ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]) + $TextPadding*2;
          }

         $XOffset = $TextPadding;
        }
       else
        { $YOffset = 0; $XOffset = 0; }

       if ( $CaptionColorFactor == NULL )
        { $CaptionColor    = array("Align"=>TEXT_ALIGN_TOPLEFT,"FontName"=>$CaptionFontName,"FontSize"=>$CaptionFontSize,"R"=>$CaptionR,"G"=>$CaptionG,"B"=>$CaptionB,"Alpha"=>$CaptionAlpha); }
       else
        { $CaptionColor    = array("Align"=>TEXT_ALIGN_TOPLEFT,"FontName"=>$CaptionFontName,"FontSize"=>$CaptionFontSize,"R"=>$Settings["R"]+$CaptionColorFactor,"G"=>$Settings["G"]+$CaptionColorFactor,"B"=>$Settings["B"]+$CaptionColorFactor); }

       if ( $SubCaptionColorFactor == NULL )
        $SubCaptionColor = array("Align"=>TEXT_ALIGN_TOPLEFT,"FontName"=>$CaptionFontName,"FontSize"=>$CaptionFontSize,"R"=>$SubCaptionR,"G"=>$SubCaptionG,"B"=>$SubCaptionB,"Alpha"=>$SubCaptionAlpha);
       else
        $SubCaptionColor = array("Align"=>TEXT_ALIGN_TOPLEFT,"FontName"=>$CaptionFontName,"FontSize"=>$CaptionFontSize,"R"=>$Settings["R"]+$SubCaptionColorFactor,"G"=>$Settings["G"]+$SubCaptionColorFactor,"B"=>$Settings["B"]+$SubCaptionColorFactor);

       $RestoreShadow = $this->pChartObject->Shadow;
       $this->pChartObject->Shadow = FALSE;

       if ( $CaptionLayout == INDICATOR_CAPTION_DEFAULT )
        $this->pChartObject->drawText($X1,$Y+$Height+$TextPadding,$Caption,$CaptionColor);
       elseif ( $CaptionLayout == INDICATOR_CAPTION_EXTENDED )
        {
         $TxtPos        = $this->pChartObject->getTextBox($X1,$Y+$Height+$TextPadding,$CaptionFontName,$CaptionFontSize,0,$Caption);
         $CaptionHeight = $TxtPos[0]["Y"] - $TxtPos[2]["Y"];

         $this->pChartObject->drawText($X1+$XOffset,$Y+$Height-$YOffset+$TextPadding,$Caption,$CaptionColor);
         $this->pChartObject->drawText($X1+$XOffset,$Y+$Height-$YOffset+$CaptionHeight+$TextPadding*2,$SubCaption,$SubCaptionColor);
        }

       $this->pChartObject->Shadow = $RestoreShadow;

       $X1 = $X2 + $SectionsMargin;
      }

     $RestoreShadow = $this->pChartObject->Shadow;
     $this->pChartObject->Shadow = FALSE;

     foreach($Values as $Key => $Value)
      {
       if ( $Value >= $OverallMin && $Value <= $OverallMax )
        {
         foreach ($IndicatorSections as $Key => $Settings)
          {
           if ( $Value >= $Settings["Start"] && $Value <= $Settings["End"] )
            {
             $X1 = $ValuesPos[$Value]; //$X + $Key*$SectionsMargin + ($Value - $OverallMin) * $XScale;

             if ( $ValueDisplay == INDICATOR_VALUE_BUBBLE )
              {
               $TxtPos = $this->pChartObject->getTextBox($X1,$Y,$ValueFontName,$ValueFontSize,0,$Value.$Unit);
               $Radius = floor(($TxtPos[1]["X"] - $TxtPos[0]["X"] + $TextPadding*4)/2);

               $this->pChartObject->drawFilledCircle($X1,$Y,$Radius+4,array("R"=>$Settings["R"]+20,"G"=>$Settings["G"]+20,"B"=>$Settings["B"]+20));
               $this->pChartObject->drawFilledCircle($X1,$Y,$Radius,array("R"=>255,"G"=>255,"B"=>255));

               $TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE,"FontName"=>$ValueFontName,"FontSize"=>$ValueFontSize);
               $this->pChartObject->drawText($X1-1,$Y-1,$Value.$Unit,$TextSettings);
              }
             elseif( $ValueDisplay == INDICATOR_VALUE_LABEL )
              {
               $Caption = "";
               $Caption[] = array("Format"=>array("R"=>$Settings["R"],"G"=>$Settings["G"],"B"=>$Settings["B"],"Alpha"=>100),"Caption"=>$Value.$Unit);
               $this->pChartObject->drawLabelBox(floor($X1),floor($Y)+2,"Value - ".$Settings["Caption"],$Caption);
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