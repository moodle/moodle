<?php
 /* CAT:Bubble chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");
 include("../class/pBubble.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(34,55,15,62,38,42),"Probe1");
 $MyData->addPoints(array(5,30,20,9,15,10),"Probe1Weight");
 $MyData->addPoints(array(5,10,-5,-1,0,-10),"Probe2");
 $MyData->addPoints(array(6,10,14,10,14,6),"Probe2Weight");
 $MyData->setSerieDescription("Probe1","This year");
 $MyData->setSerieDescription("Probe2","Last year");
 $MyData->setAxisName(0,"Current stock");
 $MyData->addPoints(array("Apple","Banana","Orange","Lemon","Peach","Strawberry"),"Product");
 $MyData->setAbscissa("Product");
 $MyData->setAbscissaName("Selected Products");

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

 /* Create the Bubble chart object and scale up */
 $myPicture->Antialias = TRUE;
 $myBubbleChart = new pBubble($myPicture,$MyData);

 /* Scale up for the bubble chart */
 $bubbleDataSeries   = array("Probe1","Probe2");
 $bubbleWeightSeries = array("Probe1Weight","Probe2Weight");
 $myBubbleChart->bubbleScale($bubbleDataSeries,$bubbleWeightSeries);

 /* Draw the bubble chart */
 $myBubbleChart->drawBubbleChart($bubbleDataSeries,$bubbleWeightSeries,array("BorderWidth"=>4,"BorderAlpha"=>50,"Surrounding"=>20));

 /* Write the chart legend */
 $myPicture->drawLegend(570,13,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawBubbleChart.simple.png");
?>