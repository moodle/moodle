<?php   
 /* CAT:Scaling */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(24,-25,26,25,25),"Temperature");
 $MyData->addPoints(array(1,2,VOID,9,10),"Humidity 1");
 $MyData->addPoints(array(1,VOID,7,-9,0),"Humidity 2");
 $MyData->addPoints(array(-1,-1,-1,-1,-1),"Humidity 3");
 $MyData->addPoints(array(0,0,0,0,0),"Vide");
 $MyData->setSerieOnAxis("Temperature",0);
 $MyData->setSerieOnAxis("Humidity 1",1);
 $MyData->setSerieOnAxis("Humidity 2",1);
 $MyData->setSerieOnAxis("Humidity 3",1);
 $MyData->setSerieOnAxis("Vide",2);
 $MyData->setAxisPosition(2,AXIS_POSITION_RIGHT);
 $MyData->setAxisName(0,"Temperature");
 $MyData->setAxisName(1,"Humidity");
 $MyData->setAxisName(2,"Empty value");

 /* Create the abscissa serie */
 $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun"),"Labels");
 $MyData->setSerieDescription("Labels","My labels");
 $MyData->setAbscissa("Labels");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

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
 $myPicture->drawText(10,13,"drawScale() - draw the X-Y scales",array("R"=>255,"G"=>255,"B"=>255));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Draw the scale */
 $myPicture->setGraphArea(90,60,660,190);
 $myPicture->drawFilledRectangle(90,60,660,190,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
 $myPicture->drawScale(array("DrawYLines"=>array(0),"Pos"=>SCALE_POS_LEFTRIGHT));

 /* Write the chart title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(350,55,"My chart title",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawScale.multiple.png");
?>