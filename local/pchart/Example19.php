<?php
 /*
     Example19 : Error reporting
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(10,4,3,2,3,3,2,1,0,7,4,3,2,3,3,5,1,0,7),"Serie1");
 $DataSet->AddPoint(array(1,4,2,6,2,3,0,1,-5,1,2,4,5,2,1,0,6,4,30),"Serie2");
 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie();
 $DataSet->SetXAxisName("Samples");
 $DataSet->SetYAxisName("Temperature");
 $DataSet->SetSerieName("January","Serie1");

 // Initialise the graph
 $Test = new pChart(700,230);
 $Test->reportWarnings("GD");
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->setGraphArea(60,30,585,185);
 $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
 $Test->drawGraphArea(255,255,255,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
 $Test->drawGrid(4,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test->setFontProperties("Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the cubic curve graph
 $Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());

 // Finish the graph
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawLegend(600,30,$DataSet->GetDataDescription(),255,255,255);
 $Test->setFontProperties("Fonts/tahoma.ttf",10);
 $Test->drawTitle(50,22,"Example 19",50,50,50,585);
 $Test->Stroke("example19.png");
?>