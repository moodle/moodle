<?php
 /*
     SmallGraph: Let's go fast, draw small!
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(1,4,-3,2,-3,3,2,1,0,7,4,-3,2,-3,3,5,1,0,7),"Serie1");
 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie();
 $DataSet->SetSerieName("January","Serie1");

 // Initialise the graph
 $Test = new pChart(100,30);
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawFilledRoundedRectangle(2,2,98,28,2,230,230,230);
 $Test->setGraphArea(5,5,95,25);
 $Test->drawGraphArea(255,255,255);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,220,220,220,FALSE);

 // Draw the line graph
 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());

 // Finish the graph
 $Test->Render("SmallGraph.png");
?>