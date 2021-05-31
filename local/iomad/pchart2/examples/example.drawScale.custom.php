<?php
 /* CAT:Scaling */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();
 $MyData->addPoints(array(1700,2500,7800,4500,3150),"Distance");
 $MyData->setAxisName(0,"Maximum distance");
 $MyData->setAxisUnit(0,"m");
 $MyData->setAxisDisplay(0,AXIS_FORMAT_CUSTOM,"YAxisFormat");

 /* Create the abscissa serie */
 $MyData->addPoints(array(1230768000,1233446400,1235865600,1238544000,1241136000,1243814400),"Timestamp");
 $MyData->setSerieDescription("Timestamp","Sampled Dates");
 $MyData->setAbscissa("Timestamp");
 $MyData->setAbscissaName("Dates");
 $MyData->setXAxisDisplay(AXIS_FORMAT_CUSTOM,"XAxisFormat");

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
 $myPicture->drawText(10,13,"drawScale() - draw the X-Y scales",array("R"=>255,"G"=>255,"B"=>255));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Draw the scale */
 $myPicture->setGraphArea(60,60,660,190);
 $myPicture->drawScale();
 $myPicture->drawFilledRectangle(60,60,660,190,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));

 /* Write the chart title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(350,55,"My chart title",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawScale.custom.png");

 function YAxisFormat($Value) { return(round($Value/1000,2)."k"); }
 function XAxisFormat($Value) { return((($Value-1230768000)/(60*60*24))." day"); }
?>