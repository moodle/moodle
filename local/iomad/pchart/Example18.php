<?php
 /*
     Example18 : Missing values
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(2,5,7,"","",5,6,4,8,4,"",2,5,6,4,5,6,7,6),"Serie1");
 $DataSet->AddPoint(array(-1,-3,-1,-2,-4,-1,"",-4,-5,-3,-2,-2,-3,-3,-5,-4,-3,-1,""),"Serie2");
 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie();
 $DataSet->SetSerieName("Raw #1","Serie1");
 $DataSet->SetSerieName("Raw #2","Serie2");
 $DataSet->SetYAxisName("Response time");
 $DataSet->SetXAxisName("Sample #ID");

 //print_r($DataSet->GetData());

 // Initialise the graph
 $Test = new pChart(700,230);
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->setGraphArea(55,30,585,185);
 $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
 $Test->drawGraphArea(255,255,255,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
 $Test->drawGrid(4,TRUE,230,230,230,50);

 // Draw the 0 line
 $Test->setFontProperties("Fonts/tahoma.ttf",6);
 $Test->drawTreshold(0,143,55,72,TRUE,TRUE);

 // Draw the line graph
 $DataSet->RemoveSerie("Serie2");
 $Test->drawFilledLineGraph($DataSet->GetData(),$DataSet->GetDataDescription(),60,TRUE);   

 // Draw the curve graph
 $DataSet->RemoveSerie("Serie1");
 $DataSet->AddSerie("Serie2");
 $Test->setShadowProperties(2,2,200,200,200,50);
 $Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);   
 $Test->clearShadow();

 // Finish the graph
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawLegend(600,30,$DataSet->GetDataDescription(),255,255,255);
 $Test->setFontProperties("Fonts/tahoma.ttf",10);
 $Test->drawTitle(50,22,"Example 18",50,50,50,585);
 $Test->Stroke("example18.png");
?>