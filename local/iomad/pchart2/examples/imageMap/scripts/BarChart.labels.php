<?php   
 /* Library settings */
 define("CLASS_PATH", "../../../class");
 define("FONT_PATH", "../../../fonts");

 /* pChart library inclusions */
 include(CLASS_PATH."/pData.class.php");
 include(CLASS_PATH."/pDraw.class.php");
 include(CLASS_PATH."/pImage.class.php");

 /* Create and populate the pData object */
 $MyData = new pData();  
 $MyData->addPoints(array(150,220,300,250,420,200,300,200,100),"Server A");
 $MyData->addPoints(array(140,VOID,340,300,320,300,200,100,50),"Server B");
 $MyData->setAxisName(0,"Hits");
 $MyData->addPoints(array("January","February","March","April","May","Juin","July","August","September"),"Months");
 $MyData->setSerieDescription("Months","Month");
 $MyData->setAbscissa("Months");

 /* Create the pChart object */
 $myPicture = new pImage(700,230,$MyData);

 /* Retrieve the image map */
 if (isset($_GET["ImageMap"]) || isset($_POST["ImageMap"]))
  $myPicture->dumpImageMap("ImageMapBarChart",IMAGE_MAP_STORAGE_FILE,"BarChart.labels","../tmp");

 /* Set the image map name */
 $myPicture->initialiseImageMap("ImageMapBarChart",IMAGE_MAP_STORAGE_FILE,"BarChart.labels","../tmp");

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

 /* Set the default font */
 $myPicture->setFontProperties(array("FontName"=>FONT_PATH."/pf_arma_five.ttf","FontSize"=>6));

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
 $Settings = array("RecordImageMap"=>TRUE);
 $myPicture->drawBarChart($Settings);

 /* Replace the labels of the image map */
 $Labels = array("Jan: 140","Feb: 0","Mar: 340","Apr: 300","May: 320","Jun: 300","Jul: 200","Aug: 100","Sep: 50");
 $myPicture->replaceImageMapValues("Server B", $Labels);

 /* Repalce the titles of the image map */
 $Titles = array("Jan 2k11","Feb 2k11","Mar 2k11","Apr 2k11","May 2k11","Jun 2k11","Jul 2k11","Aug 2k11","Sep 2k11");
 $myPicture->replaceImageMapTitle("Server A", "Second server");
 $myPicture->replaceImageMapTitle("Server B", $Titles);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("../tmp/BarChart.labels.png");
?>