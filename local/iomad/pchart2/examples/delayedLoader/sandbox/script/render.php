<?php
 /*
     render.php - Sandbox rendering engine

     Version     : 1.1.0
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 18/01/11

     This file can be distributed under the license you can find at :

                       http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

 session_start();

 if ( !isset($_GET["Mode"]) ) { $Mode = "Render"; } else { $Mode = $_GET["Mode"]; }

 $Constants = readConstantFile();

 /* -- Retrieve General configuration items -------------------------------- */
 $g_width		= $_SESSION["g_width"];
 $g_height		= $_SESSION["g_height"];
 $g_border		= $_SESSION["g_border"];

 $g_aa			= $_SESSION["g_aa"];
 $g_shadow		= $_SESSION["g_shadow"];
 $g_transparent		= $_SESSION["g_transparent"];

 $g_title_enabled	= $_SESSION["g_title_enabled"];
 $g_title		= $_SESSION["g_title"];
 $g_title_align		= $_SESSION["g_title_align"];
 $g_title_x		= $_SESSION["g_title_x"];
 $g_title_y		= $_SESSION["g_title_y"];
 $g_title_color		= $_SESSION["g_title_color"];
 $g_title_font		= $_SESSION["g_title_font"];
 $g_title_font_size	= $_SESSION["g_title_font_size"];
 $g_title_box		= $_SESSION["g_title_box"];

 $g_solid_enabled	= $_SESSION["g_solid_enabled"];
 $g_solid_color		= $_SESSION["g_solid_color"];
 $g_solid_dashed	= $_SESSION["g_solid_dashed"];

 $g_gradient_enabled	= $_SESSION["g_gradient_enabled"];
 $g_gradient_start	= $_SESSION["g_gradient_start"];
 $g_gradient_end	= $_SESSION["g_gradient_end"];
 $g_gradient_direction	= $_SESSION["g_gradient_direction"];
 $g_gradient_alpha	= $_SESSION["g_gradient_alpha"];
 /* ------------------------------------------------------------------------ */

 /* -- Retrieve Data configuration items ----------------------------------- */
 $d_serie1_enabled	= $_SESSION["d_serie1_enabled"];
 $d_serie2_enabled	= $_SESSION["d_serie2_enabled"];
 $d_serie3_enabled	= $_SESSION["d_serie3_enabled"];
 $d_absissa_enabled	= $_SESSION["d_absissa_enabled"];

 $d_serie1_name		= $_SESSION["d_serie1_name"];
 $d_serie2_name		= $_SESSION["d_serie2_name"];
 $d_serie3_name		= $_SESSION["d_serie3_name"];

 $d_serie1_axis		= $_SESSION["d_serie1_axis"];
 $d_serie2_axis		= $_SESSION["d_serie2_axis"];
 $d_serie3_axis		= $_SESSION["d_serie3_axis"];

 $data0 		= $_SESSION["data0"];
 $data1 		= $_SESSION["data1"];
 $data2 		= $_SESSION["data2"];
 $absissa 		= $_SESSION["absissa"];

 $d_normalize_enabled	= $_SESSION["d_normalize_enabled"];

 $d_axis0_name		= $_SESSION["d_axis0_name"];
 $d_axis1_name		= $_SESSION["d_axis1_name"];
 $d_axis2_name		= $_SESSION["d_axis2_name"];

 $d_axis0_unit		= $_SESSION["d_axis0_unit"];
 $d_axis1_unit		= $_SESSION["d_axis1_unit"];
 $d_axis2_unit		= $_SESSION["d_axis2_unit"];

 $d_axis0_position	= $_SESSION["d_axis0_position"];
 $d_axis1_position	= $_SESSION["d_axis1_position"];
 $d_axis2_position	= $_SESSION["d_axis2_position"];

 $d_axis0_format	= $_SESSION["d_axis0_format"];
 $d_axis1_format	= $_SESSION["d_axis1_format"];
 $d_axis2_format	= $_SESSION["d_axis2_format"];

 /* ------------------------------------------------------------------------ */

 /* -- Retrieve Scale configuration items ---------------------------------- */
 $s_x			= $_SESSION["s_x"];
 $s_y			= $_SESSION["s_y"];
 $s_width		= $_SESSION["s_width"];
 $s_height		= $_SESSION["s_height"];
 $s_direction		= $_SESSION["s_direction"];
 $s_arrows_enabled	= $_SESSION["s_arrows_enabled"];
 $s_mode		= $_SESSION["s_mode"];
 $s_cycle_enabled	= $_SESSION["s_cycle_enabled"];
 $s_x_margin		= $_SESSION["s_x_margin"];
 $s_y_margin		= $_SESSION["s_y_margin"];
 $s_automargin_enabled	= $_SESSION["s_automargin_enabled"];
 $s_font		= $_SESSION["s_font"];
 $s_font_size		= $_SESSION["s_font_size"];
 $s_font_color		= $_SESSION["s_font_color"];

 $s_x_labeling		= $_SESSION["s_x_labeling"];
 $s_x_skip		= $_SESSION["s_x_skip"];
 $s_x_label_rotation	= $_SESSION["s_x_label_rotation"];

 $s_grid_color		= $_SESSION["s_grid_color"];
 $s_grid_alpha		= $_SESSION["s_grid_alpha"];
 $s_grid_x_enabled	= $_SESSION["s_grid_x_enabled"];
 $s_grid_y_enabled	= $_SESSION["s_grid_y_enabled"];

 $s_ticks_color		= $_SESSION["s_ticks_color"];
 $s_ticks_alpha		= $_SESSION["s_ticks_alpha"];
 $s_subticks_color	= $_SESSION["s_subticks_color"];
 $s_subticks_alpha	= $_SESSION["s_subticks_alpha"];
 $s_subticks_enabled	= $_SESSION["s_subticks_enabled"];
 /* ------------------------------------------------------------------------ */

 /* -- Retrieve Chart configuration items ---------------------------------- */
 $c_family		= $_SESSION["c_family"];
 $c_display_values	= $_SESSION["c_display_values"];
 $c_break_color		= $_SESSION["c_break_color"];
 $c_break		= $_SESSION["c_break"];

 $c_plot_size		= $_SESSION["c_plot_size"];
 $c_border_size		= $_SESSION["c_border_size"];
 $c_border_enabled	= $_SESSION["c_border_enabled"];

 $c_bar_classic		= $_SESSION["c_bar_classic"];
 $c_bar_rounded		= $_SESSION["c_bar_rounded"];
 $c_bar_gradient	= $_SESSION["c_bar_gradient"];
 $c_around_zero1	= $_SESSION["c_around_zero1"];

 $c_transparency	= $_SESSION["c_transparency"];
 $c_forced_transparency	= $_SESSION["c_forced_transparency"];
 $c_around_zero2	= $_SESSION["c_around_zero2"];
 /* ------------------------------------------------------------------------ */

 /* -- Retrieve Legend configuration items ---------------------------------- */
 $l_enabled		= $_SESSION["l_enabled"];

 $l_font		= $_SESSION["l_font"];
 $l_font_size		= $_SESSION["l_font_size"];
 $l_font_color		= $_SESSION["l_font_color"];

 $l_margin		= $_SESSION["l_margin"];
 $l_alpha		= $_SESSION["l_alpha"];
 $l_format		= $_SESSION["l_format"];

 $l_orientation		= $_SESSION["l_orientation"];
 $l_box_size		= $_SESSION["l_box_size"];

 $l_position		= $_SESSION["l_position"];
 $l_x			= $_SESSION["l_x"];
 $l_y			= $_SESSION["l_y"];

 $l_family		= $_SESSION["l_family"];
 /* ------------------------------------------------------------------------ */

 /* -- Retrieve Threshold configuration items ------------------------------ */
 $t_enabled		= $_SESSION["t_enabled"];

 $t_value		= $_SESSION["t_value"];
 $t_axis		= $_SESSION["t_axis"];

 $t_color		= $_SESSION["t_color"];
 $t_alpha		= $_SESSION["t_alpha"];
 $t_ticks		= $_SESSION["t_ticks"];

 $t_caption		= $_SESSION["t_caption"];
 $t_box			= $_SESSION["t_box"];
 $t_caption_enabled	= $_SESSION["t_caption_enabled"];
 /* ------------------------------------------------------------------------ */

 /* -- Retrieve slope chart configuration items ---------------------------- */
 $sl_enabled		= $_SESSION["sl_enabled"];
 $sl_shaded		= $_SESSION["sl_shaded"];
 $sl_caption_enabled	= $_SESSION["sl_caption_enabled"];
 $sl_caption_line	= $_SESSION["sl_caption_line"];
 /* ------------------------------------------------------------------------ */

 /* -- Retrieve color configuration items ---------------------------------- */
 $p_template		= $_SESSION["p_template"];
 /* ------------------------------------------------------------------------ */

 /* pChart library inclusions */
 include("../../../class/pData.class.php");
 include("../../../class/pDraw.class.php");
 include("../../../class/pImage.class.php");

 $myData = new pData();
 if ( $Mode == "Source" )
  {
   echo "&lt;?php\r\n";
   echo 'include("class/pData.class.php");'."\r\n";
   echo 'include("class/pDraw.class.php");'."\r\n";
   echo 'include("class/pImage.class.php");'."\r\n";
   echo "\r\n";
   echo '$myData = new pData();'."\r\n";
  }

 if ( $p_template != "default" )
  $myData->loadPalette("../../../palettes/".$p_template.".color",TRUE);

 $Axis = "";
 if ( $d_serie1_enabled == "true" )
  {
   $data0  = stripTail($data0);
   $Values = preg_split("/!/",right($data0,strlen($data0)-1));
   foreach($Values as $key => $Value)
    { if ( $Value == "" ) { $Value = VOID; } $myData->addPoints($Value,"Serie1"); }

   $myData->setSerieDescription("Serie1",$d_serie1_name);
   $myData->setSerieOnAxis("Serie1",$d_serie1_axis);
   $Axis[$d_serie1_axis] = TRUE;

   if ( $Mode == "Source" )
    {
     $Data = "";
     foreach($Values as $key => $Value)
      { if ( $Value == "" || $Value == VOID ) { $Value = "VOID"; } $Data = $Data.",".toString($Value); }
     $Data = right($Data,strlen($Data)-1);

     echo '$myData->addPoints(array('.$Data.'),"Serie1");'."\r\n";
     echo '$myData->setSerieDescription("Serie1","'.$d_serie1_name.'");'."\r\n";
     echo '$myData->setSerieOnAxis("Serie1",'.$d_serie1_axis.');'."\r\n\r\n";

     $Axis[$d_serie1_axis] = TRUE;
    }
  }

 if ( $d_serie2_enabled == "true" )
  {
   $data1  = stripTail($data1);
   $Values = preg_split("/!/",right($data1,strlen($data1)-1));
   foreach($Values as $key => $Value)
    { if ( $Value == "" ) { $Value = VOID; } $myData->addPoints($Value,"Serie2"); }

   $myData->setSerieDescription("Serie2",$d_serie2_name);
   $myData->setSerieOnAxis("Serie2",$d_serie2_axis);
   $Axis[$d_serie2_axis] = TRUE;

   if ( $Mode == "Source" )
    {
     $Data = "";
     foreach($Values as $key => $Value)
      { if ( $Value == "" ) { $Value = "VOID"; } $Data = $Data.",".toString($Value); }
     $Data = right($Data,strlen($Data)-1);

     echo '$myData->addPoints(array('.$Data.'),"Serie2");'."\r\n";
     echo '$myData->setSerieDescription("Serie2","'.$d_serie2_name.'");'."\r\n";
     echo '$myData->setSerieOnAxis("Serie2",'.$d_serie2_axis.');'."\r\n\r\n";

     $Axis[$d_serie2_axis] = TRUE;
    }
  }

 if ( $d_serie3_enabled == "true" )
  {
   $data2  = stripTail($data2);
   $Values = preg_split("/!/",right($data2,strlen($data2)-1));
   foreach($Values as $key => $Value)
    { if ( $Value == "" ) { $Value = VOID; } $myData->addPoints($Value,"Serie3"); }

   $myData->setSerieDescription("Serie3",$d_serie3_name);
   $myData->setSerieOnAxis("Serie3",$d_serie3_axis);
   $Axis[$d_serie3_axis] = TRUE;

   if ( $Mode == "Source" )
    {
     $Data = "";
     foreach($Values as $key => $Value)
      { if ( $Value == "" ) { $Value = "VOID"; } $Data = $Data.",".toString($Value); }
     $Data = right($Data,strlen($Data)-1);

     echo '$myData->addPoints(array('.$Data.'),"Serie3");'."\r\n";
     echo '$myData->setSerieDescription("Serie3","'.$d_serie3_name.'");'."\r\n";
     echo '$myData->setSerieOnAxis("Serie3",'.$d_serie3_axis.');'."\r\n\r\n";

     $Axis[$d_serie3_axis] = TRUE;
    }
  }

 if ( $d_absissa_enabled == "true" )
  {
   $absissa = stripTail($absissa);
   $Values  = preg_split("/!/",right($absissa,strlen($absissa)-1));
   foreach($Values as $key => $Value)
    { if ( $Value == "" ) { $Value = VOID; } $myData->addPoints($Value,"Absissa"); }

   $myData->setAbscissa("Absissa");

   if ( $Mode == "Source" )
    {
     $Data = "";
     foreach($Values as $key => $Value)
      { if ( $Value == "" ) { $Value = "VOID"; } $Data = $Data.",".toString($Value); }
     $Data = right($Data,strlen($Data)-1);

     echo '$myData->addPoints(array('.$Data.'),"Absissa");'."\r\n";
     echo '$myData->setAbscissa("Absissa");'."\r\n\r\n";
    }
  }

 if ( isset($Axis[0]) )
  {
   if ( $d_axis0_position == "left" ) { $myData->setAxisPosition(0,AXIS_POSITION_LEFT); } else { $myData->setAxisPosition(0,AXIS_POSITION_RIGHT); }
   $myData->setAxisName(0,$d_axis0_name);
   $myData->setAxisUnit(0,$d_axis0_unit);

   if ( $d_axis0_format == "AXIS_FORMAT_METRIC" )	{ $myData->setAxisDisplay(0,680004); }
   if ( $d_axis0_format == "AXIS_FORMAT_CURRENCY" )	{ $myData->setAxisDisplay(0,680005,"$"); }

   if ( $Mode == "Source" )
    {
     if ( $d_axis0_position == "left" ) { echo '$myData->setAxisPosition(0,AXIS_POSITION_LEFT);'."\r\n"; } else { echo '$myData->setAxisPosition(0,AXIS_POSITION_RIGHT);'."\r\n"; }
     echo '$myData->setAxisName(0,"'.$d_axis0_name.'");'."\r\n";
     echo '$myData->setAxisUnit(0,"'.$d_axis0_unit.'");'."\r\n\r\n";
    }
  }

 if ( isset($Axis[1]) )
  {
   if ( $d_axis1_position == "left" ) { $myData->setAxisPosition(1,AXIS_POSITION_LEFT); } else { $myData->setAxisPosition(1,AXIS_POSITION_RIGHT); }
   $myData->setAxisName(1,$d_axis1_name);
   $myData->setAxisUnit(1,$d_axis1_unit);

   if ( $Mode == "Source" )
    {
     if ( $d_axis1_position == "left" ) { echo '$myData->setAxisPosition(1,AXIS_POSITION_LEFT);'."\r\n"; } else { echo '$myData->setAxisPosition(1,AXIS_POSITION_RIGHT);'."\r\n"; }
     echo '$myData->setAxisName(1,"'.$d_axis1_name.'");'."\r\n";
     echo '$myData->setAxisUnit(1,"'.$d_axis1_unit.'");'."\r\n\r\n";
    }
  }

 if ( isset($Axis[2]) )
  {
   if ( $d_axis2_position == "left" ) { $myData->setAxisPosition(2,AXIS_POSITION_LEFT); } else { $myData->setAxisPosition(2,AXIS_POSITION_RIGHT); }
   $myData->setAxisName(2,$d_axis2_name);
   $myData->setAxisUnit(2,$d_axis2_unit);

   if ( $Mode == "Source" )
    {
     if ( $d_axis2_position == "left" ) { echo '$myData->setAxisPosition(2,AXIS_POSITION_LEFT);'."\r\n"; } else { echo '$myData->setAxisPosition(2,AXIS_POSITION_RIGHT);'."\r\n"; }
     echo '$myData->setAxisName(2,"'.$d_axis2_name.'");'."\r\n";
     echo '$myData->setAxisUnit(2,"'.$d_axis2_unit.'");'."\r\n\r\n";
    }
  }

 if ( $d_normalize_enabled == "true" )
  {
   if ( $Mode == "Render" )
    $myData->normalize(100);
   else
    echo '$myData->normalize(100);'."\r\n";
  }

 if ( $Mode == "Render" )
  {
   if ( $g_transparent == "true" )
    $myPicture = new pImage($g_width,$g_height,$myData,TRUE);
   else
    $myPicture = new pImage($g_width,$g_height,$myData);
  }
 else
  {
   $myPicture = new pImage($g_width,$g_height,$myData);
   if ( $g_transparent == "true" )
    echo '$myPicture = new pImage('.$g_width.','.$g_height.',$myData,TRUE);'."\r\n";
   else
    echo '$myPicture = new pImage('.$g_width.','.$g_height.',$myData);'."\r\n";
  }

 if ( $g_aa == "false" )
  {
   if ( $Mode == "Render" )
    $myPicture->Antialias = FALSE;
   else
    echo '$myPicture->Antialias = FALSE;'."\r\n";
  }

 if ( $g_solid_enabled == "true" )
  {
   list($R,$G,$B) = extractColors($g_solid_color);
   $Settings = array("R"=>$R,"G"=>$G,"B"=>$B);

   if ( $g_solid_dashed == "true" ) { $Settings["Dash"] = TRUE; $Settings["DashR"]=$R+20; $Settings["DashG"]=$G+20; $Settings["DashB"]=$B+20; }

   if ( $Mode == "Render" )
    $myPicture->drawFilledRectangle(0,0,$g_width,$g_height,$Settings);
   else
    {
     echo dumpArray("Settings",$Settings);
     echo '$myPicture->drawFilledRectangle(0,0,'.$g_width.','.$g_height.',$Settings);'."\r\n\r\n";
    }
  }

 if ( $g_gradient_enabled == "true" )
  {
   list($StartR,$StartG,$StartB) = extractColors($g_gradient_start);
   list($EndR,$EndG,$EndB)       = extractColors($g_gradient_end);

   $Settings = array("StartR"=>$StartR,"StartG"=>$StartG,"StartB"=>$StartB,"EndR"=>$EndR,"EndG"=>$EndG,"EndB"=>$EndB,"Alpha"=>$g_gradient_alpha);

   if ( $Mode == "Render" )
    {
     if ( $g_gradient_direction == "vertical" )
      $myPicture->drawGradientArea(0,0,$g_width,$g_height,DIRECTION_VERTICAL,$Settings);
     else
      $myPicture->drawGradientArea(0,0,$g_width,$g_height,DIRECTION_HORIZONTAL,$Settings);
    }
   else
    {
     echo dumpArray("Settings",$Settings);

     if ( $g_gradient_direction == "vertical" )
      echo '$myPicture->drawGradientArea(0,0,'.$g_width.','.$g_height.',DIRECTION_VERTICAL,$Settings);'."\r\n\r\n";
     else
      echo '$myPicture->drawGradientArea(0,0,'.$g_width.','.$g_height.',DIRECTION_HORIZONTAL,$Settings);'."\r\n\r\n";
    }
  }

 if ( $Mode == "Render" )
  {
   if ( $g_border == "true" ) { $myPicture->drawRectangle(0,0,$g_width-1,$g_height-1,array("R"=>0,"G"=>0,"B"=>0)); }
   if ( $g_shadow == "true" ) { $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20)); }
  }
 else
  {
   if ( $g_border == "true" ) { echo '$myPicture->drawRectangle(0,0,'.($g_width-1).','.($g_height-1).',array("R"=>0,"G"=>0,"B"=>0));'."\r\n\r\n"; }
   if ( $g_shadow == "true" ) { echo '$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));'."\r\n\r\n"; }
  }

 if ( $g_title_enabled == "true" )
  {
   if ( $Mode == "Render" )
    $myPicture->setFontProperties(array("FontName"=>"../../../fonts/".$g_title_font,"FontSize"=>$g_title_font_size));
   else
    echo '$myPicture->setFontProperties(array("FontName"=>"fonts/'.$g_title_font.'","FontSize"=>'.$g_title_font_size.'));'."\r\n";

   list($R,$G,$B) = extractColors($g_title_color);

   $TextSettings = array("Align"=>getTextAlignCode($g_title_align),"R"=>$R,"G"=>$G,"B"=>$B);
   if ( $g_title_box == "true" ) { $TextSettings["DrawBox"] = TRUE; $TextSettings["BoxAlpha"] = 30; }

   if ( $Mode == "Render" )
    $myPicture->drawText($g_title_x,$g_title_y,$g_title,$TextSettings);
   else
    {
     echo dumpArray("TextSettings",$TextSettings);
     echo '$myPicture->drawText('.$g_title_x.','.$g_title_y.',"'.$g_title.'",$TextSettings);'."\r\n\r\n";
    }
  }

 /* Scale section */
 if ( $Mode == "Render" )
  { if ( $g_shadow == "true" ) { $myPicture->setShadow(FALSE); } }
 else
  { if ( $g_shadow == "true" ) { echo '$myPicture->setShadow(FALSE);'."\r\n"; } }

 if ( $Mode == "Render" )
  $myPicture->setGraphArea($s_x,$s_y,$s_x+$s_width,$s_y+$s_height);
 else
  echo '$myPicture->setGraphArea('.$s_x.','.$s_y.','.($s_x+$s_width).','.($s_y+$s_height).');'."\r\n";

 list($R,$G,$B) = extractColors($s_font_color);
 if ( $Mode == "Render" )
  $myPicture->setFontProperties(array("R"=>$R,"G"=>$G,"B"=>$B,"FontName"=>"../../../fonts/".$s_font,"FontSize"=>$s_font_size));
 else
  echo '$myPicture->setFontProperties(array("R"=>'.$R.',"G"=>'.$G.',"B"=>'.$B.',"FontName"=>"fonts/'.$s_font.'","FontSize"=>'.$s_font_size.'));'."\r\n\r\n";

 /* Scale specific parameters -------------------------------------------------------------------------------- */
 list($GridR,$GridG,$GridB) = extractColors($s_grid_color);
 list($TickR,$TickG,$TickB) = extractColors($s_ticks_color);
 list($SubTickR,$SubTickG,$SubTickB) = extractColors($s_subticks_color);

 if ( $s_direction == "SCALE_POS_LEFTRIGHT" ) { $Pos = 690101; } else { $Pos = 690102; }
 if ( $s_x_labeling == "LABELING_ALL") { $Labeling = 691011; } else { $Labeling = 691012; }
 if ( $s_mode == "SCALE_MODE_FLOATING" ) { $iMode = 690201; }
 if ( $s_mode == "SCALE_MODE_START0" ) { $iMode = 690202; }
 if ( $s_mode == "SCALE_MODE_ADDALL" ) { $iMode = 690203; }
 if ( $s_mode == "SCALE_MODE_ADDALL_START0" ) { $iMode = 690204; }

 $Settings = array("Pos"=>$Pos,"Mode"=>$iMode,"LabelingMethod"=>$Labeling,"GridR"=>$GridR,"GridG"=>$GridG,"GridB"=>$GridB,"GridAlpha"=>$s_grid_alpha,"TickR"=>$TickR,"TickG"=>$TickG,"TickB"=>$TickB,"TickAlpha"=>$s_ticks_alpha,"LabelRotation"=>$s_x_label_rotation);

 if ( $s_x_skip	!= 0 ) { $Settings["LabelSkip"] = $s_x_skip; }
 if ( $s_cycle_enabled == "true" ) { $Settings["CycleBackground"] = TRUE; }
 if ( $s_arrows_enabled == "true" ) { $Settings["DrawArrows"] = TRUE; }
 if ( $s_grid_x_enabled == "true" ) { $Settings["DrawXLines"] = TRUE; } else { $Settings["DrawXLines"] = 0; }
 if ( $s_subticks_enabled == "true" )
  { $Settings["DrawSubTicks"] = TRUE; $Settings["SubTickR"] = $SubTickR; $Settings["SubTickG"] = $SubTickG; $Settings["SubTickB"] = $SubTickB; $Settings["SubTickAlpha"] = $s_subticks_alpha;}
 if ( $s_automargin_enabled == "false" )
  { $Settings["XMargin"] = $s_x_margin; $Settings["YMargin"] = $s_y_margin; }

 if ( $Mode == "Render" )
  {
   if ( $s_grid_y_enabled == "true" ) { $Settings["DrawYLines"] = ALL; } else { $Settings["DrawYLines"] = NONE; }
   $myPicture->drawScale($Settings);
  }
 else
  {
   if ( $s_grid_y_enabled == "true" ) { $Settings["DrawYLines"] = "ALL"; } else { $Settings["DrawYLines"] = "NONE"; }
   echo dumpArray("Settings",$Settings);
   echo '$myPicture->drawScale($Settings);'."\r\n\r\n";
  }
 /* ---------------------------------------------------------------------------------------------------------- */

 if ( $Mode == "Render" )
  { if ( $g_shadow == "true" ) { $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10)); } }
 else
  { if ( $g_shadow == "true" ) { echo '$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));'."\r\n\r\n"; } }

 /* Chart specific parameters -------------------------------------------------------------------------------- */
 if ( $c_display_values == "true" ) { $Config = array("DisplayValues"=>TRUE); } else { $Config = ""; }

 if ( $c_family == "plot" )
  {
   $Config["PlotSize"] = $c_plot_size;
   if ( $c_border_enabled == "true" ) { $Config["PlotBorder"] = TRUE; $Config["BorderSize"] = $c_border_size; }

   if ( $Mode == "Render" )
    $myPicture->drawPlotChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawPlotChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "line" )
  {
   if ( $c_break == "true" )
    {
     list($BreakR,$BreakG,$BreakB) = extractColors($c_break_color);

     $Config["BreakVoid"] = 0;
     $Config["BreakR"] = $BreakR;
     $Config["BreakG"] = $BreakG;
     $Config["BreakB"] = $BreakB;
    }

   if ( $Mode == "Render" )
    $myPicture->drawLineChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawLineChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "step" )
  {
   if ( $c_break == "true" )
    {
     list($BreakR,$BreakG,$BreakB) = extractColors($c_break_color);

     $Config["BreakVoid"] = 0;
     $Config["BreakR"] = $BreakR;
     $Config["BreakG"] = $BreakG;
     $Config["BreakB"] = $BreakB;
    }

   if ( $Mode == "Render" )
    $myPicture->drawStepChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawStepChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "spline" )
  {
   if ( $c_break == "true" )
    {
     list($BreakR,$BreakG,$BreakB) = extractColors($c_break_color);

     $Config["BreakVoid"] = 0;
     $Config["BreakR"] = $BreakR;
     $Config["BreakG"] = $BreakG;
     $Config["BreakB"] = $BreakB;
    }

   if ( $Mode == "Render" )
    $myPicture->drawSplineChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawSplineChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "bar" )
  {
   if ( $c_bar_rounded == "true" )  { $Config["Rounded"] = TRUE; }
   if ( $c_bar_gradient == "true" ) { $Config["Gradient"] = TRUE; }
   if ( $c_around_zero1 == "true" ) { $Config["AroundZero"] = TRUE; }

   if ( $Mode == "Render" )
    $myPicture->drawBarChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawBarChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "area" )
  {
   if ( $c_forced_transparency == "true" ) { $Config["ForceTransparency"] = $c_transparency; }
   if ( $c_around_zero2 == "true" ) { $Config["AroundZero"] = TRUE; }

   if ( $Mode == "Render" )
    $myPicture->drawAreaChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawAreaChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "fstep" )
  {
   if ( $c_forced_transparency == "true" ) { $Config["ForceTransparency"] = $c_transparency; }
   if ( $c_around_zero2 == "true" ) { $Config["AroundZero"] = TRUE; } else { $Config["AroundZero"] = FALSE; }

   if ( $Mode == "Render" )
    $myPicture->drawFilledStepChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawFilledStepChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "fspline" )
  {
   if ( $c_forced_transparency == "true" ) { $Config["ForceTransparency"] = $c_transparency; }
   if ( $c_around_zero2 == "true" ) { $Config["AroundZero"] = TRUE; }

   if ( $Mode == "Render" )
    $myPicture->drawFilledSplineChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawFilledSplineChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "sbar" )
  {
   if ( $c_bar_rounded == "true" )  { $Config["Rounded"] = TRUE; }
   if ( $c_bar_gradient == "true" ) { $Config["Gradient"] = TRUE; }
   if ( $c_around_zero1 == "true" ) { $Config["AroundZero"] = TRUE; }

   if ( $Mode == "Render" )
    $myPicture->drawStackedBarChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawStackedBarChart($Config);'."\r\n";
    }
  }

 if ( $c_family == "sarea" )
  {
   if ( $c_forced_transparency == "true" ) { $Config["ForceTransparency"] = $c_transparency; }
   if ( $c_around_zero2 == "true" )        { $Config["AroundZero"] = TRUE; }

   if ( $Mode == "Render" )
    $myPicture->drawStackedAreaChart($Config);
   else
    {
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawStackedAreaChart($Config);'."\r\n";
    }
  }

 if ( $t_enabled == "true" )
  {
   list($R,$G,$B) = extractColors($t_color);

   $Config = "";
   $Config["R"] = $R; $Config["G"] = $G; $Config["B"] = $B;
   $Config["Alpha"] = $t_alpha;

   if ( isset($myData->Data["Axis"][$t_axis]) ) { $Config["AxisID"] = $t_axis; }

   if ( $t_ticks == "true" ) { $Config["Ticks"] = 4; } else { $Config["Ticks"] = 0; }

   if ( $t_caption_enabled == "true" )
    {
     $Config["WriteCaption"] = TRUE;
     $Config["Caption"] = $t_caption;
     if ( $t_box == "true" ) { $Config["DrawBox"] = TRUE; }
    }

   if ( $Mode == "Render" )
    $myPicture->drawThreshold($t_value,$Config);
   else
    {
     $Config["Caption"] = chr(34).$t_caption.chr(34);

     echo "\r\n";
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawThreshold('.$t_value.',$Config);'."\r\n";
    }
  }

 if ( $l_enabled == "true" )
  {
   list($R,$G,$B) = extractColors($l_font_color);

   $Config = "";
   $Config["FontR"]    = $R; $Config["FontG"] = $G; $Config["FontB"] = $B;
   $Config["FontName"] = "../../../fonts/".$l_font;
   $Config["FontSize"] = $l_font_size;
   $Config["Margin"]   = $l_margin;
   $Config["Alpha"]    = $l_alpha;
   $Config["BoxSize"]  = $l_box_size;

   if ( $l_format == "LEGEND_NOBORDER" ) { $Config["Style"] = 690800; }
   if ( $l_format == "LEGEND_BOX" )      { $Config["Style"] = 690801; }
   if ( $l_format == "LEGEND_ROUND" )    { $Config["Style"] = 690802; }

   if ( $l_orientation == "LEGEND_VERTICAL" )   { $Config["Mode"] = 690901; }
   if ( $l_orientation == "LEGEND_HORIZONTAL" ) { $Config["Mode"] = 690902; }

   if ( $l_family == "LEGEND_FAMILY_CIRCLE" ) { $Config["Family"] = 691052; }
   if ( $l_family == "LEGEND_FAMILY_LINE" ) { $Config["Family"] = 691053; }

   $Size = $myPicture->getLegendSize($Config);
   if ( $l_position == "CORNER_TOP_RIGHT" )
    { $l_y = $l_margin + 10; $l_x = $g_width - $Size["Width"] - 10 + $l_margin; }
   if ( $l_position == "CORNER_BOTTOM_RIGHT" )
    { $l_y = $g_height - $Size["Height"] - 10 + $l_margin; $l_x = $g_width - $Size["Width"] - 10 + $l_margin; }

   if ( $Mode == "Render" )
    $myPicture->drawLegend($l_x,$l_y,$Config);
   else
    {
     $Config["FontName"] = chr(34)."fonts/".$l_font.chr(34);

     echo "\r\n";
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawLegend('.$l_x.','.$l_y.',$Config);'."\r\n";
    }
  }

 if ( $sl_enabled == "true" )
  {
   $Config = "";
   $Config["CaptionMargin"] = 10;
   $Config["CaptionWidth"]  = 10;

   if ( $sl_shaded == "true" ) { $Config["ShadedSlopeBox"] = TRUE; }
   if ( $sl_caption_enabled != "true" ) { $Config["Caption"] = FALSE; }
   if ( $sl_caption_line == "true" ) { $Config["CaptionLine"] =TRUE; }

   if ( $Mode == "Render" )
    $myPicture->drawDerivative($Config);
   else
    {
     echo "\r\n";
     echo dumpArray("Config",$Config);
     echo '$myPicture->drawDerivative($Config);'."\r\n";
    }
  }

 if ( $Mode == "Render" )
  $myPicture->stroke();
 else
  echo "\r\n".'$myPicture->stroke();'."\r\n?>";

 function extractColors($Hexa)
  {
   if ( strlen($Hexa) != 6 ) { return(array(0,0,0)); }

   $R = hexdec(left($Hexa,2));
   $G = hexdec(mid($Hexa,3,2));
   $B = hexdec(right($Hexa,2));

   return(array($R,$G,$B));
  }

 function getTextAlignCode($Mode)
  {
   if ( $Mode == "TEXT_ALIGN_TOPLEFT" )      { return(690401); }
   if ( $Mode == "TEXT_ALIGN_TOPMIDDLE" )    { return(690402); }
   if ( $Mode == "TEXT_ALIGN_TOPRIGHT" )     { return(690403); }
   if ( $Mode == "TEXT_ALIGN_MIDDLELEFT" )   { return(690404); }
   if ( $Mode == "TEXT_ALIGN_MIDDLEMIDDLE" ) { return(690405); }
   if ( $Mode == "TEXT_ALIGN_MIDDLERIGHT" )  { return(690406); }
   if ( $Mode == "TEXT_ALIGN_BOTTOMLEFT" )   { return(690407); }
   if ( $Mode == "TEXT_ALIGN_BOTTOMMIDDLE" ) { return(690408); }
   if ( $Mode == "TEXT_ALIGN_BOTTOMRIGHT" )  { return(690409); }
  }

 function dumpArray($Name,$Values)
  {
   if ( $Values == "" ) { return('$'.$Name.' = "";'."\r\n"); }

   $Result = '$'.$Name." = array(";
   foreach ($Values as $Key => $Value)
    { $Result = $Result.chr(34).$Key.chr(34)."=>".translate($Value).", "; }

   $Result = left($Result,strlen($Result)-2).");\r\n";

   return($Result);
  }

 function translate($Value)
  {
   global $Constants;

   if ( isset($Constants[$Value]))
    return($Constants[$Value]);
   else
    return($Value);
  }

 function stripTail($Values)
  {
   $Values = preg_split("/!/",right($Values,strlen($Values)-1));

   $Temp = ""; $Result = "";
   foreach($Values as $Key => $Value)
    {
     if ( $Value == "" )
      { $Temp[] = VOID; }
     else
      {
       if ( $Temp != "" && $Result != "" )
        { $Result = array_merge($Result,$Temp); }
       elseif( $Temp != "" && $Result == "" )
        { $Result = $Temp; }

       $Result[] = $Value;
       $Temp = "";
      }
    }

   $Serialized = "!"; foreach($Result as $Key => $Value) { $Serialized = $Serialized.$Value."!"; }
   $Serialized = left($Serialized,strlen($Serialized)-1);

   return($Serialized);
  }

 function readConstantFile()
  {
   $FileName = "../includes/constants.txt";

   $handle = @fopen($FileName, "r");
   if ($handle)
    {
     $Result = "";
     while (($buffer = fgets($handle, 4096)) !== false)
      {
       $Values = preg_split("/,/",$buffer);
       $Result[$Values[0]] = $Values[1];
      }
     fclose($handle);
     return($Result);
    }
   else
    { return(array("VOID"=>"0.12345")); }
  }

 function toString($Value)
  {
   if ( is_numeric($Value) || $Value == "VOID")
    return($Value);
   else
    return(chr(34).$Value.chr(34));
  }

 function left($value,$NbChar)
  { return substr($value,0,$NbChar); }

 function right($value,$NbChar)
  { return substr($value,strlen($value)-$NbChar,$NbChar); }

 function mid($value,$Depart,$NbChar)
  { return substr($value,$Depart-1,$NbChar); }
?>