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
 $myPicture->drawText(10,13,"setShadow() - Add shadows",array("R"=>255,"G"=>255,"B"=>255));

 /* Enable shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Draw a filled circle */ 
 $formSettings = array("R"=>201,"G"=>230,"B"=>40,"Alpha"=>100,"Surrounding"=>30);
 $myPicture->drawFilledCircle(90,120,30,$formSettings);

 /* Draw a filled rectangle */ 
 $formSettings = array("R"=>231,"G"=>197,"B"=>40,"Alpha"=>100,"Surrounding"=>30);
 $myPicture->drawFilledRectangle(160,90,280,150,$formSettings);

 /* Draw a filled rounded rectangle */ 
 $formSettings = array("R"=>231,"G"=>102,"B"=>40,"Alpha"=>100,"Surrounding"=>70);
 $myPicture->drawRoundedFilledRectangle(320,90,440,150,5,$formSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.setShadow.png");
?>