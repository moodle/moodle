<?php   
 /*
     Example22 : Customizing plot graphs
 */

 // Standard inclusions
 include("pChart/pData.class");
 include("pChart/pChart.class");
  
 // Dataset definition
 $DataSet = new pData;
 $DataSet->AddPoint(array(60,70,90,110,100,90),"Serie1");
 $DataSet->AddPoint(array(40,50,60,80,70,60),"Serie2");
 $DataSet->AddPoint(array("Jan","Feb","Mar","Apr","May","Jun"),"Serie3");
 $DataSet->AddSerie("Serie1");
 $DataSet->AddSerie("Serie2");
 $DataSet->SetAbsciseLabelSerie("Serie3");
 $DataSet->SetSerieName("Company A","Serie1");
 $DataSet->SetSerieName("Company B","Serie2");
 $DataSet->SetYAxisName("Product sales");
 $DataSet->SetYAxisUnit("k");
 $DataSet->SetSerieSymbol("Serie1","Sample/Point_Asterisk.gif");
 $DataSet->SetSerieSymbol("Serie2","Sample/Point_Cd.gif");

 // Initialise the graph   
 $Test = new pChart(700,230);
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->setGraphArea(65,30,650,200);
 $Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
 $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
 $Test->drawGraphArea(255,255,255,TRUE);
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
 $Test->drawGrid(4,TRUE,230,230,230,50);
  
 // Draw the title
 $Test->setFontProperties("Fonts/pf_arma_five.ttf",6);
 $Title = "Comparative product sales for company A & B  ";
 $Test->drawTextBox(65,30,650,45,$Title,0,255,255,255,ALIGN_RIGHT,TRUE,0,0,0,30);
  
 // Draw the line graph
 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
  
 // Draw the legend
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->drawLegend(80,60,$DataSet->GetDataDescription(),255,255,255);

 // Render the chart
 $Test->Stroke("example22.png");
?>