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
 $myPicture->drawText(10,13,"drawText() - add some text to your charts",array("R"=>255,"G"=>255,"B"=>255));

 /* Enable shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Write some text */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/advent_light.ttf","FontSize"=>20));
 $TextSettings = array("R"=>255,"G"=>255,"B"=>255,"Angle"=>10);
 $myPicture->drawText(60,115,"10 degree text",$TextSettings);

 /* Write some text */ 
 $TextSettings = array("R"=>0,"G"=>0,"B"=>0,"Angle"=>0,"FontSize"=>40);
 $myPicture->drawText(220,130,"Simple text",$TextSettings);

 /* Write some text */ 
 $TextSettings = array("R"=>200,"G"=>100,"B"=>0,"Angle"=>90,"FontSize"=>14);
 $myPicture->drawText(500,170,"Vertical Text",$TextSettings);

 /* Write some text */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Bedizen.ttf","FontSize"=>6));
 $TextSettings = array("DrawBox"=>TRUE,"BoxRounded"=>TRUE,"R"=>0,"G"=>0,"B"=>0,"Angle"=>0,"FontSize"=>10);
 $myPicture->drawText(220,160,"Encapsulated text",$TextSettings);

 /* Write some text */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>6));
 $TextSettings = array("DrawBox"=>TRUE,"R"=>0,"G"=>0,"B"=>0,"Angle"=>0,"FontSize"=>10);
 $myPicture->drawText(220,195,"Text in a box",$TextSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawText.png");
?>