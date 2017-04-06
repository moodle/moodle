<?php   
 /* CAT:Mathematical */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");
 include("../class/pScatter.class.php");

 /* Create the pData object */
 $myData = new pData();  

 /* Define all the data series */
 $myData->addPoints(array(10,8,13,9,11,14,6,4,12,7,5),"X1");
 $myData->addPoints(array(8.04,6.95,7.58,8.81,8.33,9.96,7.24,4.26,10.84,4.82,5.68),"Y1");
 $myData->addPoints(array(10,8,13,9,11,14,6,4,12,7,5),"X2");
 $myData->addPoints(array(9.14,8.14,8.74,8.77,9.26,8.1,6.13,3.1,9.13,7.26,4.74),"Y2");
 $myData->addPoints(array(10,8,13,9,11,14,6,4,12,7,5),"X3");
 $myData->addPoints(array(7.46,6.77,12.74,7.11,7.81,8.84,6.08,5.39,8.15,6.42,5.73),"Y3");
 $myData->addPoints(array(8,8,8,8,8,8,8,19,8,8,8),"X4");
 $myData->addPoints(array(6.58,5.76,7.71,8.84,8.47,7.04,5.25,12.5,5.56,7.91,6.89),"Y4");

 /* Create the X axis */
 $myData->setAxisName(0,"X");
 $myData->setAxisXY(0,AXIS_X);
 $myData->setAxisPosition(0,AXIS_POSITION_BOTTOM);

 /* Create the Y axis */
 $myData->setSerieOnAxis("Y1",1);
 $myData->setSerieOnAxis("Y2",1);
 $myData->setSerieOnAxis("Y3",1);
 $myData->setSerieOnAxis("Y4",1);
 $myData->setAxisName(1,"Y");
 $myData->setAxisXY(1,AXIS_Y);
 $myData->setAxisPosition(1,AXIS_POSITION_LEFT);

 /* Create the scatter chart binding */
 $myData->setScatterSerie("X1","Y1",0);
 $myData->setScatterSerie("X2","Y2",1);
 $myData->setScatterSerie("X3","Y3",2);
 $myData->setScatterSerie("X4","Y4",3);
 $myData->setScatterSerieDrawable(1,FALSE);
 $myData->setScatterSerieDrawable(2,FALSE);
 $myData->setScatterSerieDrawable(3,FALSE);

 /* Create the pChart object */
 $myPicture = new pImage(800,582,$myData);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,800,582,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,800,582,DIRECTION_VERTICAL,$Settings);

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,799,581,array("R"=>0,"G"=>0,"B"=>0));

 /* Write the title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>23));
 $myPicture->drawText(55,50,"Anscombe's Quartet drawing example",array("R"=>255,"G"=>255,"B"=>255));
 $myPicture->drawText(55,65,"This example demonstrate the importance of graphing data before analysing it. (The line of best fit is the same for all datasets)",array("FontSize"=>12,"R"=>255,"G"=>255,"B"=>255));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 
 /* Create the Scatter chart object */
 $myScatter = new pScatter($myPicture,$myData);

 /* Turn on shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the 1st chart */
 $myPicture->setGraphArea(56,90,380,285);
 $myScatter->drawScatterScale(array("XMargin"=>5,"YMargin"=>5,"Floating"=>TRUE,"DrawSubTicks"=>TRUE));
 $myScatter->drawScatterPlotChart();
 $myScatter->drawScatterBestFit();

 /* Draw the 2nt chart */
 $myData->setScatterSerieDrawable(0,FALSE);
 $myData->setScatterSerieDrawable(1,TRUE);
 $myPicture->setGraphArea(436,90,760,285);
 $myScatter->drawScatterScale(array("XMargin"=>5,"YMargin"=>5,"Floating"=>TRUE,"DrawSubTicks"=>TRUE));
 $myScatter->drawScatterPlotChart();
 $myScatter->drawScatterBestFit();

 /* Draw the 3rd chart */
 $myData->setScatterSerieDrawable(1,FALSE);
 $myData->setScatterSerieDrawable(2,TRUE);
 $myPicture->setGraphArea(56,342,380,535);
 $myScatter->drawScatterScale(array("XMargin"=>5,"YMargin"=>5,"Floating"=>TRUE,"DrawSubTicks"=>TRUE));
 $myScatter->drawScatterPlotChart();
 $myScatter->drawScatterBestFit();

 /* Draw the 4th chart */
 $myData->setScatterSerieDrawable(2,FALSE);
 $myData->setScatterSerieDrawable(3,TRUE);
 $myPicture->setGraphArea(436,342,760,535);
 $myScatter->drawScatterScale(array("XMargin"=>5,"YMargin"=>5,"Floating"=>TRUE,"DrawSubTicks"=>TRUE));
 $myScatter->drawScatterPlotChart();
 $myScatter->drawScatterBestFit();

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawAnscombeQuartet.png");
?>