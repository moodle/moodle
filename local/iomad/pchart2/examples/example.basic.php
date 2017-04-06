<?php
 /* CAT:Misc */

 /* Include all the classes */ 
 include("../class/pDraw.class.php"); 
 include("../class/pImage.class.php"); 
 include("../class/pData.class.php");

 /* Create your dataset object */ 
 $myData = new pData(); 
 
 /* Add data in your dataset */ 
 $myData->addPoints(array(1,3,4,3,5));

 /* Create a pChart object and associate your dataset */ 
 $myPicture = new pImage(700,230,$myData);

 /* Choose a nice font */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>11));

 /* Define the boundaries of the graph area */
 $myPicture->setGraphArea(60,40,670,190);

 /* Draw the scale, keep everything automatic */ 
 $myPicture->drawScale();

 /* Draw the scale, keep everything automatic */ 
 $myPicture->drawSplineChart();

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.basic.png");
?>