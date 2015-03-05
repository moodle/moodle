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
 $myPicture->drawText(10,13,"drawFromJPG() - add pictures to your charts",array("R"=>255,"G"=>255,"B"=>255));

 /* Turn off shadow computing */ 
 $myPicture->setShadow(FALSE);

 /* Draw a JPG object */
 $myPicture->drawFromJPG(100,45,"resources/landscape1.jpg");

 /* Turn on shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Draw a JPG object */
 $myPicture->drawFromJPG(380,45,"resources/landscape2.jpg");

 /* Write the legend */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
 $TextSettings = array("R"=>255,"G"=>255,"B"=>255,"FontSize"=>10,"FontName"=>"../fonts/calibri.ttf","Align"=>TEXT_ALIGN_BOTTOMMIDDLE);
 $myPicture->drawText(220,210,"Without shadow",$TextSettings);
 $myPicture->drawText(490,210,"With enhanced shadow",$TextSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawFromJPG.png");
?>