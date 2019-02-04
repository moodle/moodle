<?php
 /* CAT:Spring chart */

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pSpring.class.php");
 include("../class/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(300,300);

 /* Draw the background */
 $Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
 $myPicture->drawFilledRectangle(0,0,300,300,$Settings);

 /* Overlay with a gradient */
 $Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,300,300,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,300,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>100,"EndG"=>100,"EndB"=>100,"Alpha"=>80));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,299,299,array("R"=>0,"G"=>0,"B"=>0));

 /* Write the picture title */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"pSpring - Draw spring charts",array("R"=>255,"G"=>255,"B"=>255));

 /* Set the graph area boundaries*/
 $myPicture->setGraphArea(20,20,280,280);

 /* Set the default font properties */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Forgotte.ttf","FontSize"=>9,"R"=>80,"G"=>80,"B"=>80));

 /* Enable shadow computing */
 $myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

 /* Create the pSpring object */
 $SpringChart = new pSpring();

 /* Create some nodes */
 $SpringChart->addNode(0,array("Shape"=>NODE_SHAPE_SQUARE,"FreeZone"=>60,"Size"=>20,"NodeType"=>NODE_TYPE_CENTRAL));
 $SpringChart->addNode(1,array("Connections"=>"0"));
 $SpringChart->addNode(2,array("Connections"=>"0"));
 $SpringChart->addNode(3,array("Shape"=>NODE_SHAPE_TRIANGLE,"Connections"=>"1"));
 $SpringChart->addNode(4,array("Shape"=>NODE_SHAPE_TRIANGLE,"Connections"=>"1"));
 $SpringChart->addNode(5,array("Shape"=>NODE_SHAPE_TRIANGLE,"Connections"=>"1"));
 $SpringChart->addNode(6,array("Connections"=>"2"));
 $SpringChart->addNode(7,array("Connections"=>"2"));
 $SpringChart->addNode(8,array("Connections"=>"2"));

 /* Set the nodes color */
 $SpringChart->setNodesColor(0,array("R"=>215,"G"=>163,"B"=>121,"BorderR"=>166,"BorderG"=>115,"BorderB"=>74));
 $SpringChart->setNodesColor(array(1,2),array("R"=>150,"G"=>215,"B"=>121,"Surrounding"=>-30));
 $SpringChart->setNodesColor(array(3,4,5),array("R"=>216,"G"=>166,"B"=>14,"Surrounding"=>-30));
 $SpringChart->setNodesColor(array(6,7,8),array("R"=>179,"G"=>121,"B"=>215,"Surrounding"=>-30));

 /* Set the link properties */
 $SpringChart->linkProperties(0,1,array("R"=>255,"G"=>0,"B"=>0,"Ticks"=>2));
 $SpringChart->linkProperties(0,2,array("R"=>255,"G"=>0,"B"=>0,"Ticks"=>2));

 /* Draw the spring chart */
 $Result = $SpringChart->drawSpring($myPicture);

 /* Output the statistics */
 // print_r($Result);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.spring.relations.png");
?>