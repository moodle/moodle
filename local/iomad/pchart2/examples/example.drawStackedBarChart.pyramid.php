<?php   
 /* CAT:Stacked chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(20,40,65,100,70,55,40,22,12),"Male");
 $MyData->addPoints(array(-22,-44,-61,-123,-74,-60,-52,-34,-21),"Female");
 $MyData->setAxisName(0,"Community members");
 $MyData->addPoints(array("0-10","10-20","20-30","30-40","40-50","50-60","60-70","70-80","80-90"),"Labels");
 $MyData->setSerieDescription("Labels","Ages");
 $MyData->setAbscissa("Labels");
 $MyData->setAxisDisplay(0,AXIS_FORMAT_CUSTOM,"YAxisFormat");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));

 /* Set the default font properties */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Draw the scale and the chart */
 $myPicture->setGraphArea(60,20,680,190);
 $myPicture->drawScale(array("DrawSubTicks"=>TRUE,"Mode"=>SCALE_MODE_ADDALL));
 $myPicture->setShadow(FALSE);
 $myPicture->drawStackedBarChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"Gradient"=>TRUE,"Surrounding"=>-20,"InnerSurrounding"=>20));

 /* Write the chart legend */
 $myPicture->drawLegend(600,210,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawStackedBarChart.pyramid.png");

 function YAxisFormat($Value) { return(abs($Value)); } 
?>