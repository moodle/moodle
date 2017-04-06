<?php   
 /* CAT:Misc */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(150,220,300,250,420,200,300,200,100),"Server A");
 $MyData->addPoints(array(140,0,340,300,320,300,200,100,50),"Server B");
 $MyData->setAxisName(0,"Hits");
 $MyData->addPoints(array("January","February","March","April","May","Juin","July","August","September"),"Months");
 $MyData->setSerieDescription("Months","Month");
 $MyData->setAbscissa("Months");
 $MyData->setAbsicssaPosition(AXIS_POSITION_TOP); 

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Add a border to the picture */
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>80,"EndG"=>80,"EndB"=>80,"Alpha"=>100));
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>80,"EndG"=>80,"EndB"=>80,"Alpha"=>20));
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Define the chart area */
 $myPicture->setGraphArea(60,40,650,200);

 /* Draw the scale */
 $scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);

 /* Write the chart legend */
 $myPicture->drawLegend(580,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Turn on shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the chart */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
 $settings = array("Surrounding"=>-30,"InnerSurrounding"=>30,"Interleave"=>0);
 $myPicture->drawBarChart($settings);

 /* Draw the bottom black area */
 $myPicture->setShadow(FALSE);
 $myPicture->drawFilledRectangle(0,174,700,230,array("R"=>0,"G"=>0,"B"=>0));

 /* Do the mirror effect */
 $myPicture->drawAreaMirror(0,174,700,48);

 /* Draw the horizon line */
 $myPicture->drawLine(1,174,698,174,array("R"=>80,"G"=>80,"B"=>80));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawAreaMirror.png");
?>