<?php
 /*
     Example8 : A radar graph
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array("Memory","Disk","Network","Slots","CPU"),"Label");
 $DataSet->AddPoint(array(1,2,3,4,3),"Serie1");
 $DataSet->AddPoint(array(1,4,2,6,2),"Serie2");
 $DataSet->AddSerie("Serie1");
 $DataSet->AddSerie("Serie2");
 $DataSet->SetAbsciseLabelSerie("Label");


 $DataSet->SetSerieName("Reference","Serie1");
 $DataSet->SetSerieName("Tested computer","Serie2");

 // Initialise the graph
 $Test = new pChart(400,400);
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawFilledRoundedRectangle(7,7,393,393,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,395,395,5,230,230,230);
 $Test->setGraphArea(30,30,370,370);
 $Test->drawFilledRoundedRectangle(30,30,370,370,5,255,255,255);
 $Test->drawRoundedRectangle(30,30,370,370,5,220,220,220);

 // Draw the radar graph
 $Test->drawRadarAxis($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE,20,120,120,120,230,230,230);
 $Test->drawFilledRadar($DataSet->GetData(),$DataSet->GetDataDescription(),50,20);

 // Finish the graph
 $Test->drawLegend(15,15,$DataSet->GetDataDescription(),255,255,255);
 $Test->setFontProperties("Fonts/tahoma.ttf",10);
 $Test->drawTitle(0,22,"Example 8",50,50,50,400);
 $Test->Stroke("example8.png");
?>