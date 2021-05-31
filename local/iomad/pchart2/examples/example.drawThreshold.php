<?php
 /* CAT:Misc */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();
 $MyData->addPoints(array(24,-25,26,25,25),"Temperature");
 $MyData->setAxisName(0,"Temperatures");
 $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun"),"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");

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
 $myPicture->drawText(10,13,"drawThreshold() - draw a treshold in the charting area",array("R"=>255,"G"=>255,"B"=>255));

 /* Write the chart title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(250,55,"My chart title",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Draw the scale and do some cosmetics */
 $myPicture->setGraphArea(60,60,450,190);
 $myPicture->drawFilledRectangle(70,70,440,180,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
 $myPicture->drawScale(array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"DrawSubTicks"=>TRUE));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Draw static thresholds */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 $myPicture->drawThreshold(5,array("WriteCaption"=>TRUE,"Caption"=>"Step 1","BoxAlpha"=>100,"BoxR"=>255,"BoxG"=>40,"BoxB"=>70,"Alpha"=>70,"Ticks"=>1,"R"=>255,"G"=>40,"B"=>70));
 $myPicture->drawThreshold(15,array("WriteCaption"=>TRUE,"Caption"=>"Step 2","BoxAlpha"=>100,"BoxR"=>40,"BoxG"=>70,"BoxB"=>255,"Alpha"=>70,"Ticks"=>2,"R"=>40,"G"=>70,"B"=>255));

 /* Disable shadow computing */
 $myPicture->setShadow(FALSE);

 /* Draw the scale and do some cosmetics */
 $myPicture->setGraphArea(500,60,670,190);
 $myPicture->drawFilledRectangle(505,65,665,185,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
 $myPicture->drawScale(array("XMargin"=>5,"YMargin"=>5,"Floating"=>TRUE,"Pos"=>SCALE_POS_TOPBOTTOM,"DrawSubTicks"=>TRUE));
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Draw static thresholds */
 $myPicture->drawThreshold(5,array("Alpha"=>70,"Ticks"=>1));
 $myPicture->drawThreshold(array(10,12),array("Alpha"=>70,"Ticks"=>2,"R"=>0,"G"=>0,"B"=>255));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawThreshold.png");
?>