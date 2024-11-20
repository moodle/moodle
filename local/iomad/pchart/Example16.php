<?php
 /*
     Example16 : Importing CSV data
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->ImportFromCSV("Sample/CO2.csv",",",array(1,2,3,4),TRUE,0);
 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie();
 $DataSet->SetYAxisName("CO2 concentrations");

 // Initialise the graph
 $Test = new pChart(700,230);
 $Test->reportWarnings("GD");
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->setGraphArea(60,30,680,180);
 $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
 $Test->drawGraphArea(255,255,255,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,90,2);
 $Test->drawGrid(4,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test->setFontProperties("Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the line graph
 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

 // Finish the graph
 $Test->setFontProperties("Fonts/tahoma.ttf",8);   
 $Test->drawLegend(70,40,$DataSet->GetDataDescription(),255,255,255);   
 $Test->setFontProperties("Fonts/tahoma.ttf",10);
 $Test->drawTitle(60,22,"CO2 concentrations at Mauna Loa",50,50,50,585);
 $Test->Stroke("example16.png");
?>