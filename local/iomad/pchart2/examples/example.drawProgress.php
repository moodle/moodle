<?php
 /* CAT:Progress bars */

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
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0, "G"=>0, "B"=>0));

 /* Write the picture title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf", "FontSize"=>6));
 $myPicture->drawText(10,13, "drawProgress() - Simple progress bars",array("R"=>255, "G"=>255, "B"=>255));

 /* Set the font & shadow options */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf", "FontSize"=>10));
 $myPicture->setShadow(TRUE,array("X"=>1, "Y"=>1, "R"=>0, "G"=>0, "B"=>0, "Alpha"=>20));

 /* Draw a progress bar */
 $progressOptions = array("R"=>209, "G"=>31, "B"=>27, "Surrounding"=>20, "BoxBorderR"=>0, "BoxBorderG"=>0, "BoxBorderB"=>0, "BoxBackR"=>255, "BoxBackG"=>255, "BoxBackB"=>255, "RFade"=>206, "GFade"=>133, "BFade"=>30, "ShowLabel"=>TRUE);
 $myPicture->drawProgress(40,60,77,$progressOptions);

 /* Draw a progress bar */
 $progressOptions = array("Width"=>165, "R"=>209, "G"=>125, "B"=>27, "Surrounding"=>20, "BoxBorderR"=>0, "BoxBorderG"=>0, "BoxBorderB"=>0, "BoxBackR"=>255, "BoxBackG"=>255, "BoxBackB"=>255,"NoAngle"=>TRUE, "ShowLabel"=>TRUE, "LabelPos"=>LABEL_POS_RIGHT);
 $myPicture->drawProgress(40,100,50,$progressOptions);

 /* Draw a progress bar */
 $progressOptions = array("Width"=>165, "R"=>209, "G"=>198, "B"=>27, "Surrounding"=>20, "BoxBorderR"=>0, "BoxBorderG"=>0, "BoxBorderB"=>0, "BoxBackR"=>255, "BoxBackG"=>255, "BoxBackB"=>255, "ShowLabel"=>TRUE, "LabelPos"=>LABEL_POS_LEFT);
 $myPicture->drawProgress(75,140,25,$progressOptions);

 /* Draw a progress bar */
 $progressOptions = array("Width"=>400, "R"=>134, "G"=>209, "B"=>27, "Surrounding"=>20, "BoxBorderR"=>0, "BoxBorderG"=>0, "BoxBorderB"=>0, "BoxBackR"=>255, "BoxBackG"=>255, "BoxBackB"=>255, "RFade"=>206, "GFade"=>133, "BFade"=>30, "ShowLabel"=>TRUE, "LabelPos"=>LABEL_POS_CENTER);
 $myPicture->drawProgress(40,180,80,$progressOptions);

 /* Draw a progress bar */
 $progressOptions = array("Width"=>20, "Height"=>150, "R"=>209, "G"=>31, "B"=>27, "Surrounding"=>20, "BoxBorderR"=>0, "BoxBorderG"=>0, "BoxBorderB"=>0, "BoxBackR"=>255, "BoxBackG"=>255, "BoxBackB"=>255, "RFade"=>206, "GFade"=>133, "BFade"=>30, "ShowLabel"=>TRUE, "Orientation"=>ORIENTATION_VERTICAL, "LabelPos"=>LABEL_POS_BOTTOM);
 $myPicture->drawProgress(500,200,77,$progressOptions);

 /* Draw a progress bar */
 $progressOptions = array("Width"=>20, "Height"=>150, "R"=>209, "G"=>125, "B"=>27, "Surrounding"=>20, "BoxBorderR"=>0, "BoxBorderG"=>0, "BoxBorderB"=>0, "BoxBackR"=>255, "BoxBackG"=>255, "BoxBackB"=>255,"NoAngle"=>TRUE, "ShowLabel"=>TRUE, "Orientation"=>ORIENTATION_VERTICAL, "LabelPos"=>LABEL_POS_TOP);
 $myPicture->drawProgress(540,200,50,$progressOptions);

 /* Draw a progress bar */
 $progressOptions = array("Width"=>20, "Height"=>150, "R"=>209, "G"=>198, "B"=>27, "Surrounding"=>20, "BoxBorderR"=>0, "BoxBorderG"=>0, "BoxBorderB"=>0, "BoxBackR"=>255, "BoxBackG"=>255, "BoxBackB"=>255, "ShowLabel"=>TRUE, "Orientation"=>ORIENTATION_VERTICAL, "LabelPos"=>LABEL_POS_INSIDE);
 $myPicture->drawProgress(580,200,25,$progressOptions);

 /* Draw a progress bar */
 $progressOptions = array("Width"=>20, "Height"=>150, "R"=>134, "G"=>209, "B"=>27, "Surrounding"=>20, "BoxBorderR"=>0, "BoxBorderG"=>0, "BoxBorderB"=>0, "BoxBackR"=>255, "BoxBackG"=>255, "BoxBackB"=>255, "RFade"=>206, "GFade"=>133, "BFade"=>30, "ShowLabel"=>TRUE, "Orientation"=>ORIENTATION_VERTICAL, "LabelPos"=>LABEL_POS_CENTER);
 $myPicture->drawProgress(620,200,80,$progressOptions);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawProgress.png");
?>