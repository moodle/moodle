<?php   
 /*
     Example1 : A simple line chart
 */

 // Standard inclusions      
 include("pChart/pData.class");   
 include("pChart/pChart.class");   
  
 // Dataset definition    
 $DataSet = new pData;   
 $DataSet->ImportFromCSV("Sample/bulkdata.csv",",",array(1,2,3),FALSE,0);   
 $DataSet->AddAllSeries();   
 $DataSet->SetAbsciseLabelSerie();   
 $DataSet->SetSerieName("January","Serie1");   
 $DataSet->SetSerieName("February","Serie2");   
 $DataSet->SetSerieName("March","Serie3");   
 $DataSet->SetYAxisName("Average age");
 $DataSet->SetYAxisUnit("µs");
  
 // Initialise the graph   
 $Test = new pChart(700,230);
 $Test->setFontProperties("Fonts/tahoma.ttf",8);   
 $Test->setGraphArea(70,30,680,200);   
 $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);   
 $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);   
 $Test->drawGraphArea(255,255,255,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);   
 $Test->drawGrid(4,TRUE,230,230,230,50);
  
 // Draw the 0 line   
 $Test->setFontProperties("Fonts/tahoma.ttf",6);   
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);   
  
 // Draw the line graph
 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());   
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);   
  
 // Finish the graph   
 $Test->setFontProperties("Fonts/tahoma.ttf",8);   
 $Test->drawLegend(75,35,$DataSet->GetDataDescription(),255,255,255);   
 $Test->setFontProperties("Fonts/tahoma.ttf",10);   
 $Test->drawTitle(60,22,"example 1",50,50,50,585);   
 $Test->Stroke("example1.png");
?>