<?php
 /* CAT:Polar and radars */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pRadar.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();
 $MyData->addPoints(array(10,20,30,40,50,60,70,80,90),"ScoreA");
 $MyData->addPoints(array(20,40,50,12,10,30,40,50,60),"ScoreB");
 $MyData->setSerieDescription("ScoreA","Coverage A");
 $MyData->setSerieDescription("ScoreB","Coverage B");

 /* Define the absissa serie */
 $MyData->addPoints(array(40,80,120,160,200,240,280,320,360),"Coord");
 $MyData->setAbscissa("Coord");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Draw a solid background */
 $Settings = array("R"=>179, "G"=>217, "B"=>91, "Dash"=>1, "DashR"=>199, "DashG"=>237, "DashB"=>111);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay some gradient areas */
 $Settings = array("StartR"=>194, "StartG"=>231, "StartB"=>44, "EndR"=>43, "EndG"=>107, "EndB"=>58, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

 /* Write the picture title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"pRadar - Draw polar charts",array("R"=>255,"G"=>255,"B"=>255));

 /* Set the default font properties */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));

 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Create the pRadar object */
 $SplitChart = new pRadar();

 /* Draw a polar chart */
 $myPicture->setGraphArea(10,25,340,225);
 $Options = array("BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50), "FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6);
 $SplitChart->drawPolar($myPicture,$MyData,$Options);

 /* Draw a polar chart */
 $myPicture->setGraphArea(350,25,690,225);
 $Options = array("LabelPos"=>RADAR_LABELS_HORIZONTAL,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>50,"EndR"=>32,"EndG"=>109,"EndB"=>174,"EndAlpha"=>30),"AxisRotation"=>0,"DrawPoly"=>TRUE,"PolyAlpha"=>50, "FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6);
 $SplitChart->drawPolar($myPicture,$MyData,$Options);

 /* Write the chart legend */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 $myPicture->drawLegend(270,205,array("Style"=>LEGEND_BOX,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.polar.png");
?>