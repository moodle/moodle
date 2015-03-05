<?php
 /* CAT:Misc */

 /* Include all the classes */ 
 include("../class/pDraw.class.php"); 
 include("../class/pImage.class.php"); 
 include("../class/pData.class.php");
 include("../class/pCache.class.php");

 /* Create your dataset object */ 
 $myData = new pData(); 
 
 /* Add data in your dataset */ 
 $myData->addPoints(array(1,3,4,3,5));

 /* Create the cache object */
 $myCache = new pCache(array("CacheFolder"=>"../cache"));

 /* Compute the hash linked to the chart data */
 $ChartHash = $myCache->getHash($myData);

 /* Test if we got this hash in our cache already */
 if ( $myCache->isInCache($ChartHash))
  {
   /* If we have it, get the picture from the cache! */
   $myCache->autoOutput($ChartHash,"pictures/example.cache.png");
  }
 else
  {
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

   /* Do some cosmetics */
   $myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>100));
   $myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
   $myPicture->drawText(10,13,"Test of the pCache class",array("R"=>255,"G"=>255,"B"=>255));

   /* Push the rendered picture to the cache */
   $myCache->writeToCache($ChartHash,$myPicture);

   /* Render the picture */
   $myPicture->autoOutput("pictures/example.cache.png");
  }
?>