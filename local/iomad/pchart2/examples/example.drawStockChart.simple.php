<?php
 /* CAT:Stock chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");
 include("../class/pStock.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(35,28,17,27,12,12,20,15,20,28),"Open");
 $MyData->addPoints(array(20,17,25,20,25,23,16,29,26,17),"Close");
 $MyData->addPoints(array(10,11,14,11,9,4,3,7,9,5),"Min");
 $MyData->addPoints(array(37,32,33,29,29,25,22,34,29,31),"Max");
 $MyData->setAxisDisplay(0,AXIS_FORMAT_CURRENCY,"$");

 $MyData->addPoints(array("Dec 13","Dec 14","Dec 15","Dec 16","Dec 17", "Dec 20","Dec 21","Dec 22","Dec 23","Dec 24"),"Time");
 $MyData->setAbscissa("Time");
 $MyData->setAbscissaName("Time");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Turn of AAliasing */
 $myPicture->Antialias = FALSE;

 /* Draw the border */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Define the chart area */
 $myPicture->setGraphArea(60,30,650,190);

 /* Draw the scale */
 $scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

 /* Create the pStock object */
 $mystockChart = new pStock($myPicture,$MyData);

 /* Draw the stock chart */
 $stockSettings = array("BoxUpR"=>255,"BoxUpG"=>255,"BoxUpB"=>255,"BoxDownR"=>0,"BoxDownG"=>0,"BoxDownB"=>0);
 $mystockChart->drawStockChart($stockSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawStockChart.simple.png");
?>