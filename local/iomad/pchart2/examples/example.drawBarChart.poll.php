<?php
 /* CAT:Bar Chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();
 $MyData->addPoints(array(60,30,10),"Answers");
 $MyData->setAxisName(0,"Answers (%)");
 $MyData->addPoints(array("I do agree  ","I disagree  ","No opinion  "),"Options");
 $MyData->setAbscissa("Options");

 /* Create the pChart object */
 $myPicture = new pImage(500,220,$MyData);

 /* Write the chart title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>15));
 $myPicture->drawText(20,34,"Q: Flexibility is a key point of this library",array("FontSize"=>20));

 /* Define the default font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6));

 /* Set the graph area */
 $myPicture->setGraphArea(70,60,480,200);
 $myPicture->drawGradientArea(70,60,480,200,DIRECTION_HORIZONTAL,array("StartR"=>200,"StartG"=>200,"StartB"=>200,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>30));

 /* Draw the chart scale */
 $scaleSettings = array("AxisAlpha"=>10,"TickAlpha"=>10,"DrawXLines"=>FALSE,"Mode"=>SCALE_MODE_START0,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"Pos"=>SCALE_POS_TOPBOTTOM);
 $myPicture->drawScale($scaleSettings);

 /* Turn on shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Draw the chart */
 $myPicture->drawBarChart(array("DisplayValues"=>TRUE,"DisplayShadow"=>TRUE,"DisplayPos"=>LABEL_POS_INSIDE,"Rounded"=>TRUE,"Surrounding"=>30));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawBarChart.poll.png");
?>