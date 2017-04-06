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
 $MyData->addPoints(array(20,40,50,30,10,30,40,50,60),"ScoreB"); 
 $MyData->setSerieDescription("ScoreA","Coverage A");
 $MyData->setSerieDescription("ScoreB","Coverage B");

 /* Define the absissa serie */
 $MyData->addPoints(array(40,80,120,160,200,240,280,320,360),"Coord");
 $MyData->setAbscissa("Coord");

 /* Create the pChart object */
 $myPicture = new pImage(300,300,$MyData);
 $myPicture->drawGradientArea(0,0,300,300,DIRECTION_VERTICAL,array("StartR"=>200,"StartG"=>200,"StartB"=>200,"EndR"=>240,"EndG"=>240,"EndB"=>240,"Alpha"=>100));
 $myPicture->drawGradientArea(0,0,300,20,DIRECTION_HORIZONTAL,array("StartR"=>30,"StartG"=>30,"StartB"=>30,"EndR"=>100,"EndG"=>100,"EndB"=>100,"Alpha"=>100));
 $myPicture->drawLine(0,20,300,20,array("R"=>255,"G"=>255,"B"=>255));
 $RectangleSettings = array("R"=>180,"G"=>180,"B"=>180,"Alpha"=>100);

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,299,299,array("R"=>0,"G"=>0,"B"=>0));

 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"pRadar - Draw radar charts",array("R"=>255,"G"=>255,"B"=>255));

 /* Set the default font properties */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));

 /* Enable shadow computing */ 
 $myPicture->setShadow(FALSE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Create the pRadar object */ 
 $SplitChart = new pRadar();

 /* Draw a radar chart */ 
 $myPicture->setGraphArea(10,25,290,290);
 $Options = array("DrawPoly"=>TRUE,"WriteValues"=>TRUE,"ValueFontSize"=>8,"Layout"=>RADAR_LAYOUT_CIRCLE,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50));
 $SplitChart->drawPolar($myPicture,$MyData,$Options);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.polar.values.png");
?>