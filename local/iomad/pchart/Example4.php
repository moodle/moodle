<?php
 /*
     Example4 : Showing how to draw area
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->ImportFromCSV("Sample/datawithtitle.csv",",",array(1,2,3),TRUE,0);
 $DataSet->AddSerie("Serie2");
 $DataSet->SetAbsciseLabelSerie();
 $DataSet->removeSerieName("Serie1");
 $DataSet->removeSerieName("Serie3");

 // Initialise the graph
 $Test = new pChart(700,230);
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->setGraphArea(60,30,680,200);
 $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
 $Test->drawGraphArea(255,255,255,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
 $Test->drawGrid(4,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test->setFontProperties("Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the area
 $Test->drawArea($DataSet->GetData(),"Serie1","Serie3",239,238,227,50);

 // Draw the line graph
 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

 // Finish the graph
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawLegend(65,35,$DataSet->GetDataDescription(),250,250,250);
 $Test->setFontProperties("Fonts/tahoma.ttf",10);
 $Test->drawTitle(60,22,"Example 4",50,50,50,585);
 $Test->Stroke("example4.png");
?>