<?php   
 /* Library settings */
 define("CLASS_PATH", "../../../class");
 define("FONT_PATH", "../../../fonts");

 /* pChart library inclusions */
 include(CLASS_PATH."/pDraw.class.php");
 include(CLASS_PATH."/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(700,230);

 /* Retrieve the image map */
 if (isset($_GET["ImageMap"]) || isset($_POST["ImageMap"]))
  $myPicture->dumpImageMap("ImageMap1",IMAGE_MAP_STORAGE_FILE,"Shapes","../tmp");

 /* Set the image map name */
 $myPicture->initialiseImageMap("ImageMap1",IMAGE_MAP_STORAGE_FILE,"Shapes","../tmp");

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,700,230,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>FONT_PATH."/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"drawFilledRectangle() - Transparency & colors",array("R"=>255,"G"=>255,"B"=>255));

 /* Turn on shadow computing */ 
 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));

 /* Turn on Antialiasing */
 $myPicture->Antialias = TRUE;

 /* Draw a customized filled circle */ 
 $CircleSettings = array("R"=>267,"G"=>165,"B"=>169,"Dash"=>TRUE,"BorderR"=>255,"BorderG"=>255,"BorderB"=>255);
 $myPicture->drawFilledCircle(300,120,50,$CircleSettings);
 $myPicture->addToImageMap("CIRCLE","300,120,50",$myPicture->toHTMLColor(267,165,169),"Circle","My Message");

 /* Draw a customized polygon */
 $Plots           = array(402,62,460,80,420,190,360,168);
 $PolygonSettings = array("R"=>71,"G"=>87,"B"=>145,"Dash"=>TRUE,"BorderR"=>255,"BorderG"=>255,"BorderB"=>255);
 $myPicture->drawPolygon($Plots,$PolygonSettings);
 $myPicture->addToImageMap("POLY","402,62,460,80,420,190,360,168",$myPicture->toHTMLColor(71,87,145),"Polygon","My Message");

 /* Turn of Antialiasing */
 $myPicture->Antialias = FALSE;

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>150,"G"=>200,"B"=>170,"Dash"=>TRUE,"DashR"=>170,"DashG"=>220,"DashB"=>190,"BorderR"=>255,"BorderG"=>255,"BorderB"=>255);
 $myPicture->drawFilledRectangle(20,60,210,170,$RectangleSettings);
 $myPicture->addToImageMap("RECT","20,60,210,170",$myPicture->toHTMLColor(150,200,170),"Box 1","Message 1");

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>209,"G"=>134,"B"=>27,"Alpha"=>30);
 $myPicture->drawFilledRectangle(30,30,200,200,$RectangleSettings);
 $myPicture->addToImageMap("RECT","30,30,200,200",$myPicture->toHTMLColor(209,134,27),"Box 2","Message 2");

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>209,"G"=>31,"B"=>27,"Alpha"=>100,"Surrounding"=>30);
 $myPicture->drawFilledRectangle(480,50,650,80,$RectangleSettings);
 $myPicture->addToImageMap("RECT","480,50,650,80",$myPicture->toHTMLColor(209,31,27),"Box 3","Message 3");

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>209,"G"=>125,"B"=>27,"Alpha"=>100,"Surrounding"=>30);
 $myPicture->drawFilledRectangle(480,90,650,120,$RectangleSettings);
 $myPicture->addToImageMap("RECT","480,90,650,120",$myPicture->toHTMLColor(209,125,27),"Box 4","Message 4");

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>209,"G"=>198,"B"=>27,"Alpha"=>100,"Surrounding"=>30,"Ticks"=>2);
 $myPicture->drawFilledRectangle(480,130,650,160,$RectangleSettings);
 $myPicture->addToImageMap("RECT","480,130,650,160",$myPicture->toHTMLColor(209,198,27),"Box 5","Message 5");

 /* Draw a customized filled rectangle */ 
 $RectangleSettings = array("R"=>134,"G"=>209,"B"=>27,"Alpha"=>100,"Surrounding"=>30,"Ticks"=>2);
 $myPicture->drawFilledRectangle(480,170,650,200,$RectangleSettings);
 $myPicture->addToImageMap("RECT","480,170,650,200",$myPicture->toHTMLColor(134,209,27),"Box 6","Message 6");

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("../tmp/Shapes.png");
?>