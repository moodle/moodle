<?php
 /* CAT:Drawing */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(700,230);

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
 $myPicture->drawText(10,13,"drawGradientArea() - Transparency & colors",array("R"=>255,"G"=>255,"B"=>255));

 /* Draw a gradient area */
 $GradientSettings = array("StartR"=>181,"StartG"=>209,"StartB"=>27,"Alpha"=>100,"Levels"=>-50);
 $myPicture->drawGradientArea(20,60,400,170,DIRECTION_HORIZONTAL,$GradientSettings);

 /* Draw a gradient area */
 $GradientSettings = array("StartR"=>209,"StartG"=>134,"StartB"=>27,"Alpha"=>30,"Levels"=>-50);
 $myPicture->drawGradientArea(30,30,200,200,DIRECTION_VERTICAL,$GradientSettings);

 /* Draw a gradient area */
 $GradientSettings = array("StartR"=>209,"StartG"=>31,"StartB"=>27,"Alpha"=>100,"Levels"=>50);
 $myPicture->drawGradientArea(480,50,650,80,DIRECTION_HORIZONTAL,$GradientSettings);

 /* Draw a gradient area */
 $GradientSettings = array("StartR"=>209,"StartG"=>125,"StartB"=>27,"Alpha"=>100,"Levels"=>50);
 $myPicture->drawGradientArea(480,90,650,120,DIRECTION_VERTICAL,$GradientSettings);

 /* Draw a gradient area */
 $GradientSettings = array("StartR"=>209,"StartG"=>198,"StartB"=>27,"Alpha"=>100,"Levels"=>50);
 $myPicture->drawGradientArea(480,130,650,160,DIRECTION_HORIZONTAL,$GradientSettings);

 /* Draw a gradient area */
 $GradientSettings = array("StartR"=>134,"StartG"=>209,"StartB"=>27,"Alpha"=>100,"Levels"=>50);
 $myPicture->drawGradientArea(480,170,650,200,DIRECTION_HORIZONTAL,$GradientSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawGradientArea.png");
?>