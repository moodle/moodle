<?php
 /*
     Example24 : X versus Y chart
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;

 // Compute the points
 for($i=0;$i<=360;$i=$i+10)
  {
   $DataSet->AddPoint(cos($i*3.14/180)*80+$i,"Serie1");
   $DataSet->AddPoint(sin($i*3.14/180)*80+$i,"Serie2");
  }

 $DataSet->SetSerieName("Trigonometric function","Serie1");
 $DataSet->AddSerie("Serie1");
 $DataSet->AddSerie("Serie2");
 $DataSet->SetXAxisName("X Axis");
 $DataSet->SetYAxisName("Y Axis");

 // Initialise the graph
 $Test = new pChart(300,300);
 $Test->drawGraphAreaGradient(0,0,0,-100,TARGET_BACKGROUND);

 // Prepare the graph area
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->setGraphArea(55,30,270,230);
 $Test->drawXYScale($DataSet->GetData(),$DataSet->GetDataDescription(),"Serie1","Serie2",213,217,221,TRUE,45);
 $Test->drawGraphArea(213,217,221,FALSE);
 $Test->drawGraphAreaGradient(30,30,30,-50);
 $Test->drawGrid(4,TRUE,230,230,230,20);

 // Draw the chart
 $Test->setShadowProperties(2,2,0,0,0,60,4);
 $Test->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Serie1","Serie2",0);
 $Test->clearShadow();

 // Draw the title
 $Title = "Drawing X versus Y charts trigonometric functions  ";
 $Test->drawTextBox(0,280,300,300,$Title,0,255,255,255,ALIGN_RIGHT,TRUE,0,0,0,30);

 // Draw the legend
 $Test->setFontProperties("Fonts/pf_arma_five.ttf",6);
 $DataSet->RemoveSerie("Serie2");
 $Test->drawLegend(160,5,$DataSet->GetDataDescription(),0,0,0,0,0,0,255,255,255,FALSE);

 $Test->Stroke("example24.png");
?>