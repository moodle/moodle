<?php
 /*
     Example21 : Playing with background
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(9,9,9,10,10,11,12,14,16,17,18,18,19,19,18,15,12,10,9),"Serie1");
 $DataSet->AddPoint(array(10,11,11,12,12,13,14,15,17,19,22,24,23,23,22,20,18,16,14),"Serie2");
 $DataSet->AddPoint(array(4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22),"Serie3");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("Serie3");
 $DataSet->SetAbsciseLabelSerie("Serie3");
 $DataSet->SetSerieName("January","Serie1");
 $DataSet->SetSerieName("February","Serie2");
 $DataSet->SetYAxisName("Temperature");
 $DataSet->SetYAxisUnit("°C");
 $DataSet->SetXAxisUnit("h");

 // Initialise the graph
 $Test = new pChart(700,230);
 $Test->drawGraphAreaGradient(132,153,172,50,TARGET_BACKGROUND);

 // Graph area setup
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->setGraphArea(60,20,585,180);
 $Test->drawGraphArea(213,217,221,FALSE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,213,217,221,TRUE,0,2);
 $Test->drawGraphAreaGradient(162,183,202,50);
 $Test->drawGrid(4,TRUE,230,230,230,20);

 // Draw the line chart
 $Test->setShadowProperties(3,3,0,0,0,30,4);
 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->clearShadow();
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),4,2,-1,-1,-1,TRUE);

 // Draw the legend
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawLegend(605,142,$DataSet->GetDataDescription(),236,238,240,52,58,82);

 // Draw the title
 $Title = "Average Temperatures during the first months of 2008  ";
 $Test->drawTextBox(0,210,700,230,$Title,0,255,255,255,ALIGN_RIGHT,TRUE,0,0,0,30);

 // Render the picture
 $Test->addBorder(2);
 $Test->Stroke("example21.png");
?>