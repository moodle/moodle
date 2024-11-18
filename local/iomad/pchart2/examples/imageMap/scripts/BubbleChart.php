<?php
 /* Library settings */
 define("CLASS_PATH", "../../../class");
 define("FONT_PATH", "../../../fonts");

 /* pChart library inclusions */
 include(CLASS_PATH."/pData.class.php");
 include(CLASS_PATH."/pDraw.class.php");
 include(CLASS_PATH."/pImage.class.php");
 include(CLASS_PATH."/pBubble.class.php");

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

 /* Retrieve the image map */
 if (isset($_GET["ImageMap"]) || isset($_POST["ImageMap"]))
  $myPicture->dumpImageMap("ImageMapBarChart",IMAGE_MAP_STORAGE_FILE,"BarChart","../tmp");

 /* Set the image map name */
 $myPicture->initialiseImageMap("ImageMapBarChart",IMAGE_MAP_STORAGE_FILE,"BarChart","../tmp");

 /* Turn of AAliasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

 $myPicture->setFontProperties(array("FontName"=>FONT_PATH."/pf_arma_five.ttf","FontSize"=>6));

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
 $Settings = array("RecordImageMap"=>TRUE,"ForceAlpha"=>50);
 $myBubbleChart->drawBubbleChart($bubbleDataSeries,$bubbleWeightSeries,$Settings);

 /* Write the chart legend */
 $myPicture->drawLegend(570,13,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("../tmp/Bubble Chart.png");
?>