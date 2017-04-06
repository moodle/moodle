<?php   
 /* CAT:Barcode */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pBarcode128.class.php");
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

 /* Draw the border */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

 /* Write the title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"Barcode 128 - Add barcode to your pictures",array("R"=>255,"G"=>255,"B"=>255));

 /* Create the barcode 128 object */
 $Barcode = new pBarcode128("../");

 /* Draw a simple barcode */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 $Settings = array("ShowLegend"=>TRUE,"DrawArea"=>TRUE);
 $Barcode->draw($myPicture,"pChart Rocks!",50,50,$Settings);

 /* Draw a rotated barcode */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>12));
 $Settings = array("ShowLegend"=>TRUE,"DrawArea"=>TRUE,"Angle"=>90);
 $Barcode->draw($myPicture,"Turn me on",650,50,$Settings);

 /* Draw a rotated barcode */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>12));
 $Settings = array("R"=>255,"G"=>255,"B"=>255,"AreaR"=>150,"AreaG"=>30,"AreaB"=>27,"ShowLegend"=>TRUE,"DrawArea"=>TRUE,"Angle"=>350,"AreaBorderR"=>70,"AreaBorderG"=>20,"AreaBorderB"=>20);
 $Barcode->draw($myPicture,"Do what you want !",290,140,$Settings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawbarcode128.png");
?>