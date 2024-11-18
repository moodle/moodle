<?php
 /* CAT:Barcode */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pBarcode39.class.php");
 include("../class/pBarcode128.class.php");
 include("../class/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(600,310,NULL,TRUE);

 /* Draw the rounded box */
 $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>30));
 $Settings = array("R"=>255,"G"=>255,"B"=>255,"BorderR"=>0,"BorderG"=>0,"BorderB"=>0);
 $myPicture->drawRoundedFilledRectangle(10,10,590,300,10,$Settings);

 /* Draw the cell divisions */
 $myPicture->setShadow(FALSE);
 $Settings = array("R"=>0,"G"=>0,"B"=>0);
 $myPicture->drawLine(10,110,590,110,$Settings);
 $myPicture->drawLine(200,10,200,110,$Settings);
 $myPicture->drawLine(400,10,400,110,$Settings);
 $myPicture->drawLine(10,160,590,160,$Settings);
 $myPicture->drawLine(220,160,220,200,$Settings);
 $myPicture->drawLine(320,160,320,200,$Settings);
 $myPicture->drawLine(10,200,590,200,$Settings);
 $myPicture->drawLine(400,220,400,300,$Settings);

 /* Write the fields labels */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>10));
 $Settings = array("R"=>0,"G"=>0,"B"=>0,"Align"=>TEXT_ALIGN_TOPLEFT);
 $myPicture->drawText(20,20,"FROM",$Settings);
 $myPicture->drawText(210,20,"TO",$Settings);
 $myPicture->drawText(20,120,"ACCT.\r\nNUMBER",$Settings);
 $myPicture->drawText(20,166,"QUANTITY",$Settings);
 $myPicture->drawText(230,166,"SHIPMENT CODE",$Settings);
 $myPicture->drawText(330,166,"SENDER CODE",$Settings);
 $myPicture->drawText(410,220,"MFG DATE",$Settings);
 $myPicture->drawText(410,260,"NET WEIGTH",$Settings);

 /* Filling the fields values */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>16));
 $myPicture->drawText(70,20,"BEBEER INC\r\n342, MAIN STREET\r\n33000 BORDEAUX\r\nFRANCE",$Settings);
 $myPicture->drawText(250,20,"MUSTAFA'S BAR\r\n18, CAPITOL STREET\r\n31000 TOULOUSE\r\nFRANCE",$Settings);

 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>35));
 $myPicture->drawText(100,120,"2342355552340",$Settings);

 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>20));
 $Settings = array("R"=>0,"G"=>0,"B"=>0,"Align"=>TEXT_ALIGN_TOPRIGHT);
 $myPicture->drawText(210,180,"75 CANS",$Settings);
 $myPicture->drawText(310,180,"TLSE",$Settings);
 $myPicture->drawText(580,180,"WAREHOUSE#SLOT#B15",$Settings);

 $Settings = array("R"=>0,"G"=>0,"B"=>0,"Align"=>TEXT_ALIGN_TOPLEFT);
 $myPicture->drawText(410,236,"06/06/2010",$Settings);
 $myPicture->drawText(410,276,"12.340 Kg",$Settings);

 /* Create the barcode 39 object */
 $Barcode39 = new pBarcode39("../");
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 $Settings = array("ShowLegend"=>TRUE,"Height"=>55,"DrawArea"=>TRUE,"DrawArea"=>FALSE);
 $Barcode39->draw($myPicture,"12250000234502",30,220,$Settings);

 $Settings = array("ShowLegend"=>TRUE,"Height"=>14,"DrawArea"=>TRUE,"DrawArea"=>FALSE);
 $Barcode39->draw($myPicture,"75 cans",260,220,$Settings);
 $Barcode39->draw($myPicture,"06062010",260,260,$Settings);

 /* Create the barcode 128 object */
 $Barcode128 = new pBarcode128("../");
 $Settings = array("ShowLegend"=>TRUE,"Height"=>65,"DrawArea"=>TRUE,"DrawArea"=>FALSE);
 $Barcode128->draw($myPicture,"TLSE",450,25,$Settings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.barcode.png");
?>