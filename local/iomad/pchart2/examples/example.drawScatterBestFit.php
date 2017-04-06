<?php   
 /* CAT:Mathematical */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");
 include("../class/pScatter.class.php");

 /* Create the pData object */
 $myData = new pData();  

 /* Create the X axis and the binded series */
 for ($i=0;$i<=360;$i=$i+10) { $myData->addPoints(rand(1,20)*10+rand(0,$i),"Probe 1"); }
 for ($i=0;$i<=360;$i=$i+10) { $myData->addPoints(rand(1,2)*10+rand(0,$i),"Probe 2"); }
 $myData->setAxisName(0,"X-Index");
 $myData->setAxisXY(0,AXIS_X);
 $myData->setAxisPosition(0,AXIS_POSITION_TOP);

 /* Create the Y axis and the binded series */
 for ($i=0;$i<=360;$i=$i+10) { $myData->addPoints($i,"Probe 3"); }
 $myData->setSerieOnAxis("Probe 3",1);
 $myData->setAxisName(1,"Y-Index");
 $myData->setAxisXY(1,AXIS_Y);
 $myData->setAxisPosition(1,AXIS_POSITION_LEFT);

 /* Create the 1st scatter chart binding */
 $myData->setScatterSerie("Probe 1","Probe 3",0);
 $myData->setScatterSerieDescription(0,"This year");
 $myData->setScatterSerieColor(0,array("R"=>0,"G"=>0,"B"=>0));

 /* Create the 2nd scatter chart binding */
 $myData->setScatterSerie("Probe 2","Probe 3",1);
 $myData->setScatterSerieDescription(1,"Last Year");

 /* Create the pChart object */
 $myPicture = new pImage(400,400,$myData);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,400,400,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,400,400,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,400,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"drawScatterBestFit() - Linear regression",array("R"=>255,"G"=>255,"B"=>255));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,399,399,array("R"=>0,"G"=>0,"B"=>0));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 
 /* Set the graph area */
 $myPicture->setGraphArea(50,60,350,360);

 /* Create the Scatter chart object */
 $myScatter = new pScatter($myPicture,$myData);

 /* Draw the scale */
 $myScatter->drawScatterScale();

 /* Turn on shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw a scatter plot chart */
 $myScatter->drawScatterPlotChart();

 /* Draw the legend */
 $myScatter->drawScatterLegend(280,380,array("Mode"=>LEGEND_HORIZONTAL,"Style"=>LEGEND_NOBORDER));

 /* Draw the line of best fit */
 $myScatter->drawScatterBestFit();

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawScatterBestFit.png");
?>