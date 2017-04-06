<?php   
 /* CAT:Surface chart*/

 /* pChart library inclusions */
 include("../class/pData.class.php");
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");
 include("../class/pSurface.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(400,220);

 /* Create a solid background */
 $Settings = array("R"=>50, "G"=>70, "B"=>0,"Dash"=>1, "DashR"=>30, "DashG"=>50, "DashB"=>0);
 $myPicture->drawFilledRectangle(0,0,400,400,$Settings);

 /* Do a gradient overlay */
 $Settings = array("StartR"=>194, "StartG"=>131, "StartB"=>44, "EndR"=>43, "EndG"=>7, "EndB"=>58, "Alpha"=>50);
 $myPicture->drawGradientArea(0,0,400,400,DIRECTION_VERTICAL,$Settings);
 $myPicture->drawGradientArea(0,0,400,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,399,399,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"pSurface() :: 2D surface charts",array("R"=>255,"G"=>255,"B"=>255));

 $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1));

 /* Create the surface object */
 $mySurface = new pSurface($myPicture);

 /* Set the grid size */
 $mySurface->setGrid(200,0);

 /* Write the axis labels */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/pf_arma_five.ttf","FontSize"=>6,"R"=>255,"G"=>255,"B"=>255));

 /* Draw the surface chart */
 $Palette = array(0=>array("R"=>0,"G"=>0,"B"=>0),
                  1=>array("R"=>29,"G"=>243,"B"=>119),
                  2=>array("R"=>238,"G"=>216,"B"=>78),
                  3=>array("R"=>246,"G"=>45,"B"=>53));

 $myPicture->setGraphArea(40,40,380,80);
 $mySurface->writeYLabels(array("Labels"=>"1st Seq"));
 for($i=0; $i<=200; $i++) { $mySurface->addPoint($i,0,rand(0,3)); }
 $mySurface->drawSurface(array("Padding"=>0,"Palette"=>$Palette));

 $myPicture->setGraphArea(40,100,380,140);
 $mySurface->writeYLabels(array("Labels"=>"2nd Seq"));
 for($i=0; $i<=200; $i++) { $mySurface->addPoint($i,0,rand(0,3)); }
 $mySurface->drawSurface(array("Padding"=>0,"Palette"=>$Palette));

 $myPicture->setGraphArea(40,160,380,200);
 $mySurface->writeYLabels(array("Labels"=>"3rd Seq"));
 for($i=0; $i<=200; $i++) { $mySurface->addPoint($i,0,rand(0,3)); }
 $mySurface->drawSurface(array("Padding"=>0,"Palette"=>$Palette));

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.surface.palette.png");
?>