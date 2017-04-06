<?php   
 /* CAT:Mathematical */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(-4,VOID,VOID,12,8,3,4,5,0,4,6,10),"Probe 1");
 $MyData->addPoints(array(3,12,15,8,5,-5,5,-5,-3,4,5,10),"Probe 2");
 $MyData->addPoints(array(2,7,5,18,19,22,10,2,6,10,5,6),"Probe 3");
 $MyData->setSerieTicks("Probe 2",4);
 $MyData->setSerieWeight("Probe 3",2);
 $MyData->setAxisName(0,"Temperatures");
 $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"),"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");
 $MyData->setAbscissaName("Months");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"drawDerivative() - draw the series slope",array("R"=>255,"G"=>255,"B"=>255));

 /* Set the default font */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Draw the scale and the 1st chart */
 $myPicture->setGraphArea(60,40,680,150);
 $myPicture->drawFilledRectangle(60,40,680,150,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
 $myPicture->drawScale(array("DrawSubTicks"=>TRUE));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 $myPicture->drawLineChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO));

 /* Draw the series derivative graph */
 $myPicture->drawDerivative(array("ShadedSlopeBox"=>TRUE,"CaptionLine"=>TRUE));

 /* Write the chart legend */
 $myPicture->setFontProperties(array("R"=>255,"G"=>255,"B"=>255));
 $myPicture->drawLegend(560,8,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawDerivative.png");
?>