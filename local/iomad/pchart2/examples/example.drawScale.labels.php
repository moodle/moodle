<?php   
 /* CAT:Scaling */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(8,10,24,25,25,24,23,22,20,12,10,4),"Temperature");
 $MyData->addPoints(array(2,4,6,4,5,3,6,4,5,8,6,1),"Pressure");
 $MyData->setSerieDrawable("Pressure",FALSE);
 $MyData->setAxisName(0,"Temperatures");
 $MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"),"Labels");
 $MyData->setSerieDescription("Labels","Months");
 $MyData->setAbscissa("Labels");

 /* Create the pChart object */
 $myPicture = new pImage(700,390,$MyData);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,700,390,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,390,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,389,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"drawScale() - draw the X-Y scales",array("R"=>255,"G"=>255,"B"=>255));

 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(350,55,"My chart title",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Define the 1st chart area */
 $myPicture->setGraphArea(60,70,660,200);
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Draw the scale */
 $scaleSettings = array("DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,"RemoveXAxis"=>TRUE);
 $myPicture->drawScale($scaleSettings);
 $myPicture->drawBarChart(array("Surrounding"=>-30,"InnerSurrounding"=>30));

 /* Define the 2nd chart area */
 $myPicture->setGraphArea(60,220,660,360);
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Draw the scale */
 $scaleSettings = array("DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $MyData->setSerieDrawable("Temperature",FALSE);
 $MyData->setSerieDrawable("Pressure",TRUE);
 $MyData->setAxisName(0,"Pressure");
 $myPicture->drawScale($scaleSettings);
 $myPicture->drawBarChart(array("Surrounding"=>-30,"InnerSurrounding"=>30));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawScale.labels.png");
?>