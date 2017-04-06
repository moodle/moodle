<?php   
 /* Library settings */
 define("CLASS_PATH", "../../../class");
 define("FONT_PATH", "../../../fonts");

 /* pChart library inclusions */
 include(CLASS_PATH."/pData.class.php");
 include(CLASS_PATH."/pDraw.class.php");
 include(CLASS_PATH."/pImage.class.php");
 include(CLASS_PATH."/pPie.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();   
 $MyData->addPoints(array(40,60,15,10,6,4),"ScoreA");  
 $MyData->setSerieDescription("ScoreA","Application A");

 /* Define the absissa serie */
 $MyData->addPoints(array("<10","10<>20","20<>40","40<>60","60<>80",">80"),"Labels");
 $MyData->setAbscissa("Labels");

 /* Create the pChart object */
 $myPicture = new pImage(300,260,$MyData);

 /* Retrieve the image map */
 if (isset($_GET["ImageMap"]) || isset($_POST["ImageMap"]))
  $myPicture->dumpImageMap("ImageMap2DRingChart",IMAGE_MAP_STORAGE_FILE,"2DRingChart","../tmp");

 /* Set the image map name */
 $myPicture->initialiseImageMap("ImageMap2DRingChart",IMAGE_MAP_STORAGE_FILE,"2DRingChart","../tmp");

 /* Draw a solid background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,300,300,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,300,260,DIRECTION_VERTICAL,$Settings);

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,299,259,array("R"=>0,"G"=>0,"B"=>0));

 /* Set the default font properties */ 
 $myPicture->setFontProperties(array("FontName"=>FONT_PATH."/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80));

 /* Enable shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>50));

 /* Create the pPie object */ 
 $Settings = array("RecordImageMap"=>TRUE);
 $PieChart = new pPie($myPicture,$MyData,$Settings);

 /* Draw an AA pie chart */ 
 $PieSettings = array("DrawLabels"=>TRUE,"LabelStacked"=>TRUE,"Border"=>TRUE,"RecordImageMap"=>TRUE);
 $PieChart->draw2DRing(160,125,$PieSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("../tmp/2DRingChart.png");
?>