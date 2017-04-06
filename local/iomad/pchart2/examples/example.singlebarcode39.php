<?php   
 /* CAT:Barcode */

 /* pChart library inclusions */
 include("../class/pDraw.class.php");
 include("../class/pBarcode39.class.php");
 include("../class/pImage.class.php");

 /* Create the barcode 39 object */
 $Barcode = new pBarcode39("../");

 /* String to be written on the barcode */
 $String = "This is a test";

 /* Retrieve the barcode projected size */
 $Settings = array("ShowLegend"=>TRUE,"DrawArea"=>TRUE);
 $Size = $Barcode->getSize($String,$Settings);

 /* Create the pChart object */
 $myPicture = new pImage($Size["Width"],$Size["Height"]);

 /* Set the font to use */
 $myPicture->setFontProperties(array("FontName"=>"../fonts/GeosansLight.ttf"));

 /* Render the barcode */
 $Barcode->draw($myPicture,$String,10,10,$Settings);

 /* Render the picture (choose the best way) */
 $myPicture->autoOutput("pictures/example.singlebarcode39.png");
?>