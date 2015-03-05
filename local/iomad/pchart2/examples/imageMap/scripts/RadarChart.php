<?php   
 /* Library settings */
 define("CLASS_PATH", "../../../class");
 define("FONT_PATH", "../../../fonts");

 /* pChart library inclusions */
 include(CLASS_PATH."/pData.class.php");
 include(CLASS_PATH."/pDraw.class.php");
 include(CLASS_PATH."/pImage.class.php");
 include(CLASS_PATH."/pRadar.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();   
 $MyData->addPoints(array(8,4,6,4,2,7),"ScoreA");  
 $MyData->addPoints(array(2,7,3,3,1,3),"ScoreB");  
 $MyData->setSerieDescription("ScoreA","Application A");
 $MyData->setSerieDescription("ScoreB","Application B");
 $MyData->setPalette("ScoreA",array("R"=>157,"G"=>196,"B"=>22));

 /* Define the absissa serie */
 $MyData->addPoints(array("Speed","Weight","Cost","Size","Ease","Utility"),"Families");
 $MyData->setAbscissa("Families");

 /* Create the pChart object */
 $myPicture = new pImage(300,300,$MyData);

 /* Retrieve the image map */
 if (isset($_GET["ImageMap"]) || isset($_POST["ImageMap"]))
  $myPicture->dumpImageMap("ImageMapRadarChart",IMAGE_MAP_STORAGE_FILE,"RadarChart","../tmp");

 /* Set the image map name */
 $myPicture->initialiseImageMap("ImageMapRadarChart",IMAGE_MAP_STORAGE_FILE,"RadarChart","../tmp");

 /* Draw the background */
 $myPicture->drawGradientArea(0,0,300,300,DIRECTION_VERTICAL,array("StartR"=>200,"StartG"=>200,"StartB"=>200,"EndR"=>240,"EndG"=>240,"EndB"=>240,"Alpha"=>100));

 /* Add a border to the picture */
 $RectangleSettings = array("R"=>180,"G"=>180,"B"=>180,"Alpha"=>100);
 $myPicture->drawRectangle(0,0,299,299,array("R"=>0,"G"=>0,"B"=>0));

 /* Set the default font properties */ 
 $myPicture->setFontProperties(array("FontName"=>FONT_PATH."/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));

 /* Enable shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Create the pRadar object */ 
 $SplitChart = new pRadar();

 /* Draw a radar chart */ 
 $myPicture->setGraphArea(10,10,290,290);
 $Options = array("RecordImageMap"=>TRUE,"DrawPoly"=>TRUE,"WriteValues"=>TRUE,"ValueFontSize"=>8,"Layout"=>RADAR_LAYOUT_CIRCLE,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>100,"EndR"=>207,"EndG"=>227,"EndB"=>125,"EndAlpha"=>50));
 $SplitChart->drawRadar($myPicture,$MyData,$Options);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("../tmp/RadarChart.png");
?>