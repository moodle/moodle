<?php   
 /* CAT:Drawing */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pImage.class.php");

 /* Create the pChart object */
 $myPicture = new pImage(700,230);
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>180,"StartG"=>193,"StartB"=>91,"EndR"=>120,"EndG"=>137,"EndB"=>72,"Alpha"=>100));
 $myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,array("StartR"=>180,"StartG"=>193,"StartB"=>91,"EndR"=>120,"EndG"=>137,"EndB"=>72,"Alpha"=>20));
 $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));

 /* Add a border to the picture */
 $myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));
 
 /* Write the picture title */ 
 $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
 $myPicture->drawText(10,13,"drawAntialiasPixel() - Drawing antialiased pixel with transparency",array("R"=>255,"G"=>255,"B"=>255));

 /* Draw some alpha pixels */ 
 for($X=0;$X<=160;$X++)
  {
   for($Y=0;$Y<=160;$Y++)
    {
     $PixelSettings = array("R"=>128,"G"=>255-$Y,"B"=>$X,"Alpha"=>cos(deg2rad($X*2))*50+50);

     $myPicture->drawAntialiasPixel($X*2+20.4,$Y+45,$PixelSettings);
     $myPicture->drawAntialiasPixel($X+400,$Y+45,$PixelSettings);
    }
  }

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.drawantialiaspixel.png");
?>