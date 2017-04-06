<?php   
 /* CAT:Line chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(3,12,15,8,5,-5),"Probe 1");
 $MyData->addPoints(array(2,7,5,18,19,22),"Probe 2");
 $MyData->setSerieWeight("Probe 1",2);
 $MyData->setSerieTicks("Probe 2",4);
 $MyData->setAxisName(0,"Temperatures");
 $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun"),"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>8,"R"=>255,"G"=>255,"B"=>255));
 $myPicture->drawText(10,16,"Average recorded temperature",array("FontSize"=>11,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6,"R"=>0,"G"=>0,"B"=>0));

 /* Define the chart area */
 $myPicture->setGraphArea(60,40,650,200);

 /* Draw the scale */
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the line chart */
 $myPicture->drawLineChart();
 $myPicture->drawPlotChart(array("DisplayValues"=>TRUE,"PlotBorder"=>TRUE,"BorderSize"=>2,"Surrounding"=>-60,"BorderAlpha"=>80));

 /* Write the chart legend */
 $myPicture->drawLegend(590,9,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL,"FontR"=>255,"FontG"=>255,"FontB"=>255));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawLineChart.plots.png");
?>