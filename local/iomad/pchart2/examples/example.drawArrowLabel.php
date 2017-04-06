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
 $myPicture->drawText(10,13,"drawArrowLabel() - Adaptative label positionning",array("R"=>255,"G"=>255,"B"=>255));

 /* Turn on shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw an arrow with a 45 degree angle */ 
 $ArrowSettings = array("FillR"=>37,"FillG"=>78,"FillB"=>117,"Length"=>40,"Angle"=>45);
 $myPicture->drawArrowLabel(348,113,"Blue",$ArrowSettings);

 /* Draw an arrow with a 135 degree angle */ 
 $ArrowSettings = array("FillR"=>188,"FillG"=>49,"FillB"=>42,"Length"=>40,"Angle"=>135,"Position"=>POSITION_BOTTOM,"Ticks"=>2);
 $myPicture->drawArrowLabel(348,117,"Red",$ArrowSettings);

 /* Draw an arrow with a 225 degree angle */ 
 $ArrowSettings = array("FillR"=>51,"FillG"=>119,"FillB"=>35,"Length"=>40,"Angle"=>225,"Position"=>POSITION_BOTTOM,"Ticks"=>3);
 $myPicture->drawArrowLabel(352,117,"Green",$ArrowSettings);

 /* Draw an arrow with a 315 degree angle */ 
 $ArrowSettings = array("FillR"=>239,"FillG"=>231,"FillB"=>97,"Length"=>40,"Angle"=>315,"Ticks"=>4);
 $myPicture->drawArrowLabel(352,113,"Yellow",$ArrowSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawArrowLabel.png");
?>