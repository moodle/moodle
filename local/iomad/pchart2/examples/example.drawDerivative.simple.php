<?php   
 /* CAT:Mathematical */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(3,12,15,8,5,-5,5,-5,-3,4,5,10),"Probe");
 $MyData->setAxisName(0,"Temperatures");
 $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"),"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");
 $MyData->setAbscissaName("Months");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Turn of AAliasing */
 $myPicture->Antialias = FALSE;

 /* Set the default font */ 
 $myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Define the chart area */
 $myPicture->setGraphArea(50,40,680,170);

 /* Draw the scale */
 $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>100,"GridG"=>100,"GridB"=>100,"GridAlpha"=>15);
 $myPicture->drawScale($scaleSettings);

 /* Draw the chart */
 $myPicture->Antialias = TRUE;
 $myPicture->drawSplineChart();
 $myPicture->Antialias = FALSE;

 /* Draw the series derivative graph */
 $myPicture->drawDerivative(array("Caption"=>FALSE));

 /* Write the chart legend */
 $myPicture->drawLegend(640,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawDerivative.simple.png");
?>