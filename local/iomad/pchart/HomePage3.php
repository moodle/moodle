<?php
 /*
     Example21 : A single stacked bar graph
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(1,2,5),"Serie1");
 $DataSet->AddPoint(array(3,2,2),"Serie2");
 $DataSet->AddPoint(array(3,4,1),"Serie3");
 $DataSet->AddPoint(array("A#~1","A#~2","A#~3"),"Labels");
 $DataSet->AddAllSeries();
 $DataSet->RemoveSerie("Labels");
 $DataSet->SetAbsciseLabelSerie("Labels");
 $DataSet->SetSerieName("Alpha","Serie1");
 $DataSet->SetSerieName("Beta","Serie2");
 $DataSet->SetSerieName("Gama","Serie3");
 $DataSet->SetXAxisName("Samples IDs");
 $DataSet->SetYAxisName("Test Marker");
 $DataSet->SetYAxisUnit("µm");

 // Initialise the graph
 $Test = new pChart(380,400);
 $Test->drawGraphAreaGradient(90,90,90,90,TARGET_BACKGROUND);

 // Graph area setup
 $Test->setFontProperties("Fonts/pf_arma_five.ttf",6);
 $Test->setGraphArea(110,180,350,360);
 $Test->drawGraphArea(213,217,221,FALSE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALLSTART0,213,217,221,TRUE,0,2,TRUE);
 $Test->drawGraphAreaGradient(40,40,40,-50);
 $Test->drawGrid(4,TRUE,230,230,230,5);

 // Draw the title   
 $Test->setFontProperties("Fonts/tahoma.ttf",10);
 $Title = "  Average growth size for selected\r\n  DNA samples  ";   
 $Test->setLineStyle(2);
 $Test->drawLine(51,-2,51,402,0,0,0);   
 $Test->setLineStyle(1);
 $Test->drawTextBox(0,0,50,400,$Title,90,255,255,255,ALIGN_BOTTOM_CENTER,TRUE,0,0,0,30);   
 $Test->setFontProperties("Fonts/pf_arma_five.ttf",6);

 // Draw the bar graph
 $Test->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),70);

 // Second chart
 $DataSet->SetXAxisName("");
 $Test->clearScale();
 $Test->setGraphArea(110,20,350,140);
 $Test->drawGraphArea(213,217,221,FALSE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_START0,213,217,221,TRUE,0,2);
 $Test->drawGraphAreaGradient(40,40,40,-50);
 $Test->drawGrid(4,TRUE,230,230,230,5);

 // Draw the line chart
 $Test->setShadowProperties(0,3,0,0,0,30,4);
 $Test->drawFilledCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription(),.1,40);
 $Test->clearShadow();

 // Write the legend
 $Test->drawLegend(-2,3,$DataSet->GetDataDescription(),0,0,0,0,0,0,255,255,255,FALSE);

 // Finish the graph
 $Test->addBorder(1);
 $Test->Render("HomePage2.png");
?>