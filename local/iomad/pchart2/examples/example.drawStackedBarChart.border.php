<?php   
 /* CAT:Stacked chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(4,VOID,VOID,12,8,3),"Frontend #1");
 $MyData->addPoints(array(3,12,15,8,5,5),"Frontend #2");
 $MyData->addPoints(array(2,7,5,18,19,22),"Frontend #3");
 $MyData->setAxisName(0,"Average Usage");
 $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun"),"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");


 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));

 /* Set the default font properties */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Draw the scale and the chart */
 $myPicture->setGraphArea(60,20,680,190);
 $myPicture->drawScale(array("DrawSubTicks"=>TRUE,"Mode"=>SCALE_MODE_ADDALL_START0));
 $myPicture->setShadow(FALSE);
 $myPicture->drawStackedBarChart(array("Surrounding"=>-15,"InnerSurrounding"=>15));

 /* Write a label */
 $myPicture->writeLabel(array("Frontend #1","Frontend #2","Frontend #3"),1,array("DrawVerticalLine"=>TRUE)); 

 /* Write the chart legend */
 $myPicture->drawLegend(480,210,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawStackedBarChart.shaded.png");
?>