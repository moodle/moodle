<?php   
 /* CAT:Misc */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,NULL,TRUE);

 /* Draw a rounded filled rectangle */
 $RectangleSettings = array("R"=>209,"G"=>31,"B"=>27,"Alpha"=>50,"Surrounding"=>30);
 $myPicture->drawRoundedFilledRectangle(10,25,70,55,5,$RectangleSettings);

 /* Draw a rounded filled rectangle */
 $RectangleSettings = array("R"=>209,"G"=>125,"B"=>27,"Alpha"=>50,"Surrounding"=>30);
 $myPicture->drawRoundedFilledRectangle(10,85,70,115,5,$RectangleSettings);

 /* Draw a rounded filled rectangle */
 $RectangleSettings = array("R"=>209,"G"=>198,"B"=>27,"Alpha"=>50,"Surrounding"=>30);
 $myPicture->drawRoundedFilledRectangle(10,135,70,165,5,$RectangleSettings);

 /* Draw a rounded filled rectangle */
 $RectangleSettings = array("R"=>134,"G"=>209,"B"=>27,"Alpha"=>50,"Surrounding"=>30);
 $myPicture->drawRoundedFilledRectangle(10,185,70,215,5,$RectangleSettings);

 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Draw a rounded filled rectangle */
 $RectangleSettings = array("R"=>209,"G"=>198,"B"=>27,"Alpha"=>100,"Surrounding"=>30,"Radius"=>20);
 $myPicture->drawRoundedFilledRectangle(100,20,680,210,20,$RectangleSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.transparent.background.png");
?>