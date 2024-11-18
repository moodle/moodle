<?php
 /* CAT:Misc */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(700,230);

 /* Define default font settings */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>14));

 /* Create the background */
 $myPicture->drawGradientArea(0,0,500,230,DIRECTION_HORIZONTAL,array("StartR"=>217,"StartG"=>250,"StartB"=>116,"EndR"=>181,"EndG"=>209,"EndB"=>27,"Alpha"=>100));
 $RectangleSettings = array("R"=>181,"G"=>209,"B"=>27,"Alpha"=>100);
 $myPicture->drawFilledRectangle(500,0,700,230,$RectangleSettings);

 /* Enable shadow computing on a (+1,+1) basis */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Draw the left area */
 $RectangleSettings = array("R"=>181,"G"=>209,"B"=>27,"Alpha"=>100);
 $myPicture->drawRoundedFilledRectangle(-5,0,20,240,10,$RectangleSettings);
 $TextSettings = array("R"=>255,"G"=>255,"B"=>255,"Angle"=>90,"Align"=>TEXT_ALIGN_MIDDLELEFT);
 $myPicture->drawText(10,220,"My first chart",$TextSettings);

 /* Draw the right area */
 $RectangleSettings = array("R"=>191,"G"=>219,"B"=>37,"Alpha"=>100,"Surrounding"=>20,"Ticks"=>2);
 $myPicture->drawFilledRectangle(510,10,689,219,$RectangleSettings);

 /* Write the legend */
 $TextSettings = array("R"=>255,"G"=>255,"B"=>255,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE);
 $myPicture->drawText(600,30,"Weather data",$TextSettings);
 $TextSettings = array("R"=>106,"G"=>125,"B"=>3,"Align"=>TEXT_ALIGN_TOPLEFT,"FontSize"=>11);
 $myPicture->drawText(520,45,"The   data  shown  here   has   been",$TextSettings);
 $myPicture->drawText(520,60,"collected from European locations",$TextSettings);
 $myPicture->drawText(520,75,"by the French NAVI system.",$TextSettings);
 $myPicture->drawFromPNG(540,90,"resources/blocnote.png");

 /* Disable shadow computing  */
 $myPicture->setShadow(FALSE);

 /* Draw the picture border */
 $RectangleSettings = array("R"=>181,"G"=>209,"B"=>27,"Alpha"=>100);
 $myPicture->drawRectangle(0,0,699,229,$RectangleSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawingObjects.png");
?>