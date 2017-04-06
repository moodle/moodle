<?php   
 /* CAT:Mathematical */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 for($i=0;$i<=100;$i++) { $MyData->addPoints(rand(0,20),"Probe 1"); }
 $MyData->setAxisName(0,"Temperatures");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the chart title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>11));
 $myPicture->drawText(160,35,"Measured temperature",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
 $myPicture->drawText(340,30,"(and associated standard deviation)",array("FontSize"=>10,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Define the chart area */
 $myPicture->setGraphArea(60,50,670,200);

 /* Draw the scale */
 $scaleSettings = array("LabelSkip"=>9,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the line chart */
 $myPicture->drawPlotChart(array("PlotSize"=>2));

 /* Compute the serie average and standard deviation */ 
 $Average = $MyData->getSerieAverage("Probe 1");

 /* Compute the serie standard deviation */ 
 $StandardDeviation = $MyData->getStandardDeviation("Probe 1"); 

 /* Draw a threshold area */
 $myPicture->setShadow(FALSE);
 $myPicture->drawThresholdArea($Average-$StandardDeviation,$Average+$StandardDeviation,array("R"=>100,"G"=>100,"B"=>200,"Alpha"=>10));
 $myPicture->setShadow(TRUE);

 /* Draw the serie average */
 $myPicture->drawThreshold($Average,array("WriteCaption"=>TRUE,"Caption"=>"Average value","AxisID"=>0));

 /* Draw the standard deviation boundaries */
 $ThresholdSettings = array("WriteCaption"=>TRUE,"CaptionAlign"=>CAPTION_RIGHT_BOTTOM ,"Caption"=>"SD","AxisID"=>0,"R"=>0,"G"=>0,"B"=>0);
 $myPicture->drawThreshold($Average+$StandardDeviation,$ThresholdSettings);
 $myPicture->drawThreshold($Average-$StandardDeviation,$ThresholdSettings);

 /* Write the coefficient of variation */
 $CoefficientOfVariation = round($MyData->getCoefficientOfVariation("Probe 1"),1);
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));
 $myPicture->drawText(610,46,"coefficient of variation : ".$CoefficientOfVariation,array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawStandardDeviation.png");
?>