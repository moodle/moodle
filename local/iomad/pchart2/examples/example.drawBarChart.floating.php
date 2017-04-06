<?php   
 /* CAT:Bar Chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->loadPalette("../palettes/blind.color",TRUE);
 $MyData->addPoints(array(150,220,300,250,420,200,300,200,100),"Server A");
 $MyData->addPoints(array(140,0,340,300,320,300,200,100,50),"Server B");
 $MyData->setAxisName(0,"Hits");
 $MyData->addPoints(array("January","February","March","April","May","Juin","July","August","September"),"Months");
 $MyData->setSerieDescription("Months","Month");
 $MyData->setAbscissa("Months");

 /* Create the floating 0 data serie */
 $MyData->addPoints(array(60,80,20,40,0,50,90,30,100),"Floating 0");
 $MyData->setSerieDrawable("Floating 0",FALSE);

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Draw the scale  */
 $myPicture->setGraphArea(50,30,680,200);
 $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10));

 /* Turn on shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the chart */
 $settings = array("Floating0Serie"=>"Floating 0","Draw0Line"=>TRUE,"Gradient"=>TRUE,"DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255,"DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);
 $myPicture->drawBarChart($settings);

 /* Write the chart legend */
 $myPicture->drawLegend(580,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawBarChart.floating.png");
?>