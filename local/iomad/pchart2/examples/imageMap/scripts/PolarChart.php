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
 $MyData->addPoints(array(10,20,30,40,50,60,70,80,90),"ScoreA"); 
 $MyData->addPoints(array(20,40,50,12,10,30,40,50,60),"ScoreB"); 
 $MyData->setSerieDescription("ScoreA","Coverage A");
 $MyData->setSerieDescription("ScoreB","Coverage B");

 /* Define the absissa serie */
 $MyData->addPoints(array(40,80,120,160,200,240,280,320,360),"Coord");
 $MyData->setAbscissa("Coord");

 /* Create the pChart object */
 $myPicture = new pImage(300,300,$MyData);

 /* Retrieve the image map */
 if (isset($_GET["ImageMap"]) || isset($_POST["ImageMap"]))
  $myPicture->dumpImageMap("ImageMapPolarChart",IMAGE_MAP_STORAGE_FILE,"PolarChart","../tmp");

 /* Set the image map name */
 $myPicture->initialiseImageMap("ImageMapPolarChart",IMAGE_MAP_STORAGE_FILE,"PolarChart","../tmp");

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
 $Options = array("RecordImageMap"=>TRUE,"LabelPos"=>RADAR_LABELS_HORIZONTAL,"BackgroundGradient"=>array("StartR"=>255,"StartG"=>255,"StartB"=>255,"StartAlpha"=>50,"EndR"=>32,"EndG"=>109,"EndB"=>174,"EndAlpha"=>30),"AxisRotation"=>0,"DrawPoly"=>TRUE,"PolyAlpha"=>50, "FontName"=>FONT_PATH."/pf_arma_five.ttf","FontSize"=>6);
 $SplitChart->drawPolar($myPicture,$MyData,$Options);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("../tmp/PolarChart.png");
?>