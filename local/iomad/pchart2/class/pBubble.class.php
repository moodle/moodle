<?php
 /*
     pBubble - class to draw bubble charts

     Version     : 2.1.4
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 19/01/2014

     This file can be distributed under the license you can find at :

                       http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

 define("BUBBLE_SHAPE_ROUND"		, 700001);
 define("BUBBLE_SHAPE_SQUARE"		, 700002);

 /* pBubble class definition */
 class pBubble
  {
   var $pChartObject;
   var $pDataObject;

   /* Class creator */
   function pBubble($pChartObject,$pDataObject)
    {
     $this->pChartObject = $pChartObject;
     $this->pDataObject  = $pDataObject;
    }

   /* Prepare the scale */
   function bubbleScale($DataSeries,$WeightSeries)
    {
     if ( !is_array($DataSeries) )	{ $DataSeries = array($DataSeries); }
     if ( !is_array($WeightSeries) )	{ $WeightSeries = array($WeightSeries); }

     /* Parse each data series to find the new min & max boundaries to scale */
     $NewPositiveSerie = ""; $NewNegativeSerie = ""; $MaxValues = 0; $LastPositive = 0; $LastNegative = 0;
     foreach($DataSeries as $Key => $SerieName)
      {
       $SerieWeightName = $WeightSeries[$Key];

       $this->pDataObject->setSerieDrawable($SerieWeightName,FALSE);

       if ( count($this->pDataObject->Data["Series"][$SerieName]["Data"]) > $MaxValues ) { $MaxValues = count($this->pDataObject->Data["Series"][$SerieName]["Data"]); }

       foreach($this->pDataObject->Data["Series"][$SerieName]["Data"] as $Key => $Value)
        {
         if ( $Value >= 0 )
          {
           $BubbleBounds = $Value + $this->pDataObject->Data["Series"][$SerieWeightName]["Data"][$Key];

           if ( !isset($NewPositiveSerie[$Key]) )
            { $NewPositiveSerie[$Key] = $BubbleBounds; }
           elseif ( $NewPositiveSerie[$Key] < $BubbleBounds )
            { $NewPositiveSerie[$Key] = $BubbleBounds; }

           $LastPositive = $BubbleBounds;
          }
         else
          {
           $BubbleBounds = $Value - $this->pDataObject->Data["Series"][$SerieWeightName]["Data"][$Key];

           if ( !isset($NewNegativeSerie[$Key]) )
            { $NewNegativeSerie[$Key] = $BubbleBounds; }
           elseif ( $NewNegativeSerie[$Key] > $BubbleBounds )
            { $NewNegativeSerie[$Key] = $BubbleBounds; }

           $LastNegative = $BubbleBounds;
          }
        }
      }

     /* Check for missing values and all the fake positive serie */
     if ( $NewPositiveSerie != "" )
      {
       for ($i=0; $i<$MaxValues; $i++) { if (!isset($NewPositiveSerie[$i])) { $NewPositiveSerie[$i] = $LastPositive; } }

       $this->pDataObject->addPoints($NewPositiveSerie,"BubbleFakePositiveSerie");
      }

     /* Check for missing values and all the fake negative serie */
     if ( $NewNegativeSerie != "" )
      {
       for ($i=0; $i<$MaxValues; $i++) { if (!isset($NewNegativeSerie[$i])) { $NewNegativeSerie[$i] = $LastNegative; } }

       $this->pDataObject->addPoints($NewNegativeSerie,"BubbleFakeNegativeSerie");
      }
    }

   function resetSeriesColors()
    {
     $Data    = $this->pDataObject->getData();
     $Palette = $this->pDataObject->getPalette();

     $ID = 0;
     foreach($Data["Series"] as $SerieName => $SeriesParameters)
      {
       if ( $SeriesParameters["isDrawable"] )
        {
         $this->pDataObject->Data["Series"][$SerieName]["Color"]["R"]     = $Palette[$ID]["R"];
         $this->pDataObject->Data["Series"][$SerieName]["Color"]["G"]     = $Palette[$ID]["G"];
         $this->pDataObject->Data["Series"][$SerieName]["Color"]["B"]     = $Palette[$ID]["B"];
         $this->pDataObject->Data["Series"][$SerieName]["Color"]["Alpha"] = $Palette[$ID]["Alpha"];
         $ID++;
        }
      }
    }

   /* Prepare the scale */
   function drawBubbleChart($DataSeries,$WeightSeries,$Format="")
    {
     $ForceAlpha	= isset($Format["ForceAlpha"]) ? $Format["ForceAlpha"] : VOID;
     $DrawBorder	= isset($Format["DrawBorder"]) ? $Format["DrawBorder"] : TRUE;
     $BorderWidth	= isset($Format["BorderWidth"]) ? $Format["BorderWidth"] : 1;
     $Shape		= isset($Format["Shape"]) ? $Format["Shape"] : BUBBLE_SHAPE_ROUND;
     $Surrounding	= isset($Format["Surrounding"]) ? $Format["Surrounding"] : NULL;
     $BorderR		= isset($Format["BorderR"]) ? $Format["BorderR"] : 0;
     $BorderG		= isset($Format["BorderG"]) ? $Format["BorderG"] : 0;
     $BorderB		= isset($Format["BorderB"]) ? $Format["BorderB"] : 0;
     $BorderAlpha	= isset($Format["BorderAlpha"]) ? $Format["BorderAlpha"] : 30;
     $RecordImageMap	= isset($Format["RecordImageMap"]) ? $Format["RecordImageMap"] : FALSE;

     if ( !is_array($DataSeries) )	{ $DataSeries = array($DataSeries); }
     if ( !is_array($WeightSeries) )	{ $WeightSeries = array($WeightSeries); }

     $Data    = $this->pDataObject->getData();
     $Palette = $this->pDataObject->getPalette();

     if ( isset($Data["Series"]["BubbleFakePositiveSerie"] ) ) { $this->pDataObject->setSerieDrawable("BubbleFakePositiveSerie",FALSE); }
     if ( isset($Data["Series"]["BubbleFakeNegativeSerie"] ) ) { $this->pDataObject->setSerieDrawable("BubbleFakeNegativeSerie",FALSE); }

     $this->resetSeriesColors();

     list($XMargin,$XDivs) = $this->pChartObject->scaleGetXSettings();

     foreach($DataSeries as $Key => $SerieName)
      {
       $AxisID	= $Data["Series"][$SerieName]["Axis"];
       $Mode	= $Data["Axis"][$AxisID]["Display"];
       $Format	= $Data["Axis"][$AxisID]["Format"];
       $Unit	= $Data["Axis"][$AxisID]["Unit"];

       if (isset($Data["Series"][$SerieName]["Description"])) { $SerieDescription = $Data["Series"][$SerieName]["Description"]; } else { $SerieDescription = $SerieName; }

       $XStep	= ($this->pChartObject->GraphAreaX2-$this->pChartObject->GraphAreaX1-$XMargin*2)/$XDivs;

       $X = $this->pChartObject->GraphAreaX1 + $XMargin;
       $Y = $this->pChartObject->GraphAreaY1 + $XMargin;

       $Color = array("R"=>$Palette[$Key]["R"],"G"=>$Palette[$Key]["G"],"B"=>$Palette[$Key]["B"],"Alpha"=>$Palette[$Key]["Alpha"]);

       if ( $ForceAlpha != VOID ) { $Color["Alpha"]=$ForceAlpha; }

       if ( $DrawBorder )
        {
         if ( $BorderWidth != 1 )
          {
           if ( $Surrounding != NULL )
            { $BorderR = $Palette[$Key]["R"]+$Surrounding; $BorderG = $Palette[$Key]["G"]+$Surrounding; $BorderB = $Palette[$Key]["B"]+$Surrounding; }
           else
            { $BorderR = $BorderR; $BorderG = $BorderG; $BorderB = $BorderB; }
           if ( $ForceAlpha != VOID ) { $BorderAlpha = $ForceAlpha/2; }
           $BorderColor = array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha);
          }
         else
          {
           $Color["BorderAlpha"] = $BorderAlpha;

           if ( $Surrounding != NULL )
            { $Color["BorderR"] = $Palette[$Key]["R"]+$Surrounding; $Color["BorderG"] = $Palette[$Key]["G"]+$Surrounding; $Color["BorderB"] = $Palette[$Key]["B"]+$Surrounding; }
           else
            { $Color["BorderR"] = $BorderR; $Color["BorderG"] = $BorderG; $Color["BorderB"] = $BorderB; }
           if ( $ForceAlpha != VOID ) { $Color["BorderAlpha"] = $ForceAlpha/2; }
          }
        }

       foreach($Data["Series"][$SerieName]["Data"] as $iKey => $Point)
        {
         $Weight = $Point + $Data["Series"][$WeightSeries[$Key]]["Data"][$iKey];

         $PosArray    = $this->pChartObject->scaleComputeY($Point,array("AxisID"=>$AxisID));
         $WeightArray = $this->pChartObject->scaleComputeY($Weight,array("AxisID"=>$AxisID));

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $XDivs == 0 ) { $XStep = 0; } else { $XStep = ($this->pChartObject->GraphAreaX2-$this->pChartObject->GraphAreaX1-$XMargin*2)/$XDivs; }
           $Y = floor($PosArray); $CircleRadius = floor(abs($PosArray - $WeightArray)/2);

           if ( $Shape == BUBBLE_SHAPE_SQUARE )
            {
             if ( $RecordImageMap ) { $this->pChartObject->addToImageMap("RECT",floor($X-$CircleRadius).",".floor($Y-$CircleRadius).",".floor($X+$CircleRadius).",".floor($Y+$CircleRadius),$this->pChartObject->toHTMLColor($Palette[$Key]["R"],$Palette[$Key]["G"],$Palette[$Key]["B"]),$SerieDescription,$Data["Series"][$WeightSeries[$Key]]["Data"][$iKey]); }

             if ( $BorderWidth != 1 )
              {
               $this->pChartObject->drawFilledRectangle($X-$CircleRadius-$BorderWidth,$Y-$CircleRadius-$BorderWidth,$X+$CircleRadius+$BorderWidth,$Y+$CircleRadius+$BorderWidth,$BorderColor);
               $this->pChartObject->drawFilledRectangle($X-$CircleRadius,$Y-$CircleRadius,$X+$CircleRadius,$Y+$CircleRadius,$Color);
              }
             else
              $this->pChartObject->drawFilledRectangle($X-$CircleRadius,$Y-$CircleRadius,$X+$CircleRadius,$Y+$CircleRadius,$Color);
            }
           elseif ( $Shape == BUBBLE_SHAPE_ROUND )
            {
             if ( $RecordImageMap ) { $this->pChartObject->addToImageMap("CIRCLE",floor($X).",".floor($Y).",".floor($CircleRadius),$this->pChartObject->toHTMLColor($Palette[$Key]["R"],$Palette[$Key]["G"],$Palette[$Key]["B"]),$SerieDescription,$Data["Series"][$WeightSeries[$Key]]["Data"][$iKey]); }

             if ( $BorderWidth != 1 )
              {
               $this->pChartObject->drawFilledCircle($X,$Y,$CircleRadius+$BorderWidth,$BorderColor);
               $this->pChartObject->drawFilledCircle($X,$Y,$CircleRadius,$Color);
              }
             else
              $this->pChartObject->drawFilledCircle($X,$Y,$CircleRadius,$Color);
            }

           $X = $X + $XStep;
          }
         elseif ( $Data["Orientation"] == SCALE_POS_TOPBOTTOM )
          {
           if ( $XDivs == 0 ) { $XStep = 0; } else { $XStep = ($this->pChartObject->GraphAreaY2-$this->pChartObject->GraphAreaY1-$XMargin*2)/$XDivs; }
           $X = floor($PosArray); $CircleRadius = floor(abs($PosArray - $WeightArray)/2);

           if ( $Shape == BUBBLE_SHAPE_SQUARE )
            {
             if ( $RecordImageMap ) { $this->pChartObject->addToImageMap("RECT",floor($X-$CircleRadius).",".floor($Y-$CircleRadius).",".floor($X+$CircleRadius).",".floor($Y+$CircleRadius),$this->pChartObject->toHTMLColor($Palette[$Key]["R"],$Palette[$Key]["G"],$Palette[$Key]["B"]),$SerieDescription,$Data["Series"][$WeightSeries[$Key]]["Data"][$iKey]); }

             if ( $BorderWidth != 1 )
              {
               $this->pChartObject->drawFilledRectangle($X-$CircleRadius-$BorderWidth,$Y-$CircleRadius-$BorderWidth,$X+$CircleRadius+$BorderWidth,$Y+$CircleRadius+$BorderWidth,$BorderColor);
               $this->pChartObject->drawFilledRectangle($X-$CircleRadius,$Y-$CircleRadius,$X+$CircleRadius,$Y+$CircleRadius,$Color);
              }
             else
              $this->pChartObject->drawFilledRectangle($X-$CircleRadius,$Y-$CircleRadius,$X+$CircleRadius,$Y+$CircleRadius,$Color);
            }
           elseif ( $Shape == BUBBLE_SHAPE_ROUND )
            {
             if ( $RecordImageMap ) { $this->pChartObject->addToImageMap("CIRCLE",floor($X).",".floor($Y).",".floor($CircleRadius),$this->pChartObject->toHTMLColor($Palette[$Key]["R"],$Palette[$Key]["G"],$Palette[$Key]["B"]),$SerieDescription,$Data["Series"][$WeightSeries[$Key]]["Data"][$iKey]); }

             if ( $BorderWidth != 1 )
              {
               $this->pChartObject->drawFilledCircle($X,$Y,$CircleRadius+$BorderWidth,$BorderColor);
               $this->pChartObject->drawFilledCircle($X,$Y,$CircleRadius,$Color);
              }
             else
              $this->pChartObject->drawFilledCircle($X,$Y,$CircleRadius,$Color);
            }

           $Y = $Y + $XStep;
          }
        }
      }
    }

   function writeBubbleLabel($SerieName,$SerieWeightName,$Points,$Format="")
    {
     $OverrideTitle	= isset($Format["OverrideTitle"]) ? $Format["OverrideTitle"] : NULL;
     $DrawPoint		= isset($Format["DrawPoint"]) ? $Format["DrawPoint"] : LABEL_POINT_BOX;

     if ( !is_array($Points) ) { $Point = $Points; $Points = ""; $Points[] = $Point; }

     $Data    = $this->pDataObject->getData();
     $Palette = $this->pDataObject->getPalette();

     if ( !isset($Data["Series"][$SerieName]) || !isset($Data["Series"][$SerieWeightName]) )
      return(0);

     list($XMargin,$XDivs) = $this->pChartObject->scaleGetXSettings();

     $AxisID	 = $Data["Series"][$SerieName]["Axis"];
     $AxisMode	 = $Data["Axis"][$AxisID]["Display"];
     $AxisFormat = $Data["Axis"][$AxisID]["Format"];
     $AxisUnit	 = $Data["Axis"][$AxisID]["Unit"];
     $XStep	 = ($this->pChartObject->GraphAreaX2-$this->pChartObject->GraphAreaX1-$XMargin*2)/$XDivs;

     $X = $this->pChartObject->GraphAreaX1 + $XMargin;
     $Y = $this->pChartObject->GraphAreaY1 + $XMargin;

     $Color = array("R"=>$Data["Series"][$SerieName]["Color"]["R"],"G"=>$Data["Series"][$SerieName]["Color"]["G"],"B"=>$Data["Series"][$SerieName]["Color"]["B"],"Alpha"=>$Data["Series"][$SerieName]["Color"]["Alpha"]);

     foreach($Points as $Key => $Point)
      {
       $Value    = $Data["Series"][$SerieName]["Data"][$Point];
       $PosArray = $this->pChartObject->scaleComputeY($Value,array("AxisID"=>$AxisID));

       if ( isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Point]) )
        $Abscissa = $Data["Series"][$Data["Abscissa"]]["Data"][$Point]." : ";
       else
        $Abscissa = "";

       $Value   = $this->pChartObject->scaleFormat($Value,$AxisMode,$AxisFormat,$AxisUnit);
       $Weight  = $Data["Series"][$SerieWeightName]["Data"][$Point];
       $Caption = $Abscissa.$Value." / ".$Weight;

       if ( isset($Data["Series"][$SerieName]["Description"]) )
        $Description = $Data["Series"][$SerieName]["Description"];
       else
        $Description = "No description";

       $Series = "";
       $Series[] = array("Format"=>$Color,"Caption"=>$Caption);

       if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
        {
         if ( $XDivs == 0 ) { $XStep = 0; } else { $XStep = ($this->pChartObject->GraphAreaX2-$this->pChartObject->GraphAreaX1-$XMargin*2)/$XDivs; }

         $X = floor($X + $Point * $XStep);
         $Y = floor($PosArray);
        }
       else
        {
         if ( $XDivs == 0 ) { $YStep = 0; } else { $YStep = ($this->pChartObject->GraphAreaY2-$this->pChartObject->GraphAreaY1-$XMargin*2)/$XDivs; }

         $X = floor($PosArray);
         $Y = floor($Y + $Point * $YStep);
        }

       if ( $DrawPoint == LABEL_POINT_CIRCLE )
        $this->pChartObject->drawFilledCircle($X,$Y,3,array("R"=>255,"G"=>255,"B"=>255,"BorderR"=>0,"BorderG"=>0,"BorderB"=>0));
       elseif ( $DrawPoint == LABEL_POINT_BOX )
        $this->pChartObject->drawFilledRectangle($X-2,$Y-2,$X+2,$Y+2,array("R"=>255,"G"=>255,"B"=>255,"BorderR"=>0,"BorderG"=>0,"BorderB"=>0));

       $this->pChartObject->drawLabelBox($X,$Y-3,$Description,$Series,$Format);
      }
    }
  }
?>