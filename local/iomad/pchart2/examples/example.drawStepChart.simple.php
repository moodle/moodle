<?php
 /* CAT:Step chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(VOID,VOID,VOID,2,6,3),"Probe 1");
 $MyData->addPoints(array(13,12,15,18,15,10),"Probe 2");
 $MyData->setAxisName(0,"Temperatures");
 $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun"),"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");

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
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

 /* Draw the step chart */
 $myPicture->drawStepChart();

 /* Write the chart legend */
 $myPicture->drawLegend(590,17,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawStepChart.simple.png");
?>