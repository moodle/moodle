<?php   
 /* CAT:Line chart */

 /* Set the default timezone */
 date_default_timezone_set('Etc/GMT');

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();

 $BaseTs = mktime(0,0,0,12,25,2011);
 $LastIn = 0; $LastOut = 0;
 for($i=0; $i<= 1440; $i++)
  {
   $LastIn  = abs($LastIn + rand(-1000,+1000));
   $LastOut = abs($LastOut + rand(-1000,+1000));
   $MyData->addPoints($LastIn,"Inbound");
   $MyData->addPoints($LastOut,"Outbound");

   $MyData->addPoints($BaseTs+$i*60,"TimeStamp");
  }
 $MyData->setAxisName(0,"Bandwidth");
 $MyData->setAxisDisplay(0,AXIS_FORMAT_TRAFFIC);
 $MyData->setSerieDescription("TimeStamp","time");
 $MyData->setAbscissa("TimeStamp");
 $MyData->setXAxisDisplay(AXIS_FORMAT_TIME,"H:00"); 

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw a background */
 $Settings = array("R"=>90, "G"=>90, "B"=>90, "Dash"=>1, "DashR"=>120, "DashG"=>120, "DashB"=>120);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings); 

 /* Overlay with a gradient */ 
 $Settings = array("StartR"=>200, "StartG"=>200, "StartB"=>200, "EndR"=>50, "EndG"=>50, "EndB"=>50, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings); 
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,$Settings); 

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(150,35,"Interface bandwidth usage",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Define the chart area */
 $myPicture->setGraphArea(60,40,680,200);

 /* Draw the scale */
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"RemoveSkippedAxis"=>TRUE,"DrawSubTicks"=>FALSE,"Mode"=>SCALE_MODE_START0,"LabelingMethod"=>LABELING_DIFFERENT);
 $myPicture->drawScale($scaleSettings);

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Draw the line chart */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 $myPicture->drawLineChart();

 /* Write a label over the chart */ 
 $myPicture->writeLabel("Inbound",720);

 /* Write the chart legend */
 $myPicture->drawLegend(580,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawSplineChart.network.png");
?>