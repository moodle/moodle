<?php   
 if ( !isset($_GET["Seed"]) )
  { $Seed = "Unknown"; }
 else
  { $Seed = $_GET["Seed"]; }

 /* pChart library inclusions */
 include("../../class/pDraw.class.php");
 include("../../class/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(700,230);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>50));
 $RectangleSettings = array("R"=>180,"G"=>180,"B"=>180,"Alpha"=>100);

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>150,"G"=>150,"B"=>150));

 /* Write the title */ 
 $myPicture->setFontProperties(array("FontName"=>"../../fonts/advent_light.ttf","FontSize"=>40));
 $myPicture->drawText(130,130,"Delayed loading script",array("R"=>255,"G"=>255,"B"=>255));

 /* Write the seed # */ 
 $myPicture->setFontProperties(array("FontName"=>"../../fonts/advent_light.ttf","FontSize"=>10));
 $myPicture->drawText(130,140,"Seed # : ".$Seed,array("R"=>255,"G"=>255,"B"=>255));

 /* Draw a bezier curve */ 
 $BezierSettings = array("R"=>255,"G"=>255,"B"=>255,"Ticks"=>4,"DrawArrow"=>TRUE,"ArrowTwoHeads"=>TRUE);
 $myPicture->drawBezier(360,170,670,120,430,100,560,190,$BezierSettings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("draw.png");
?>