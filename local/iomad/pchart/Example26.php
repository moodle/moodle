<?php
 /*
     Example26 : Two Y axis / shadow demonstration
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(110,101,118,108,110,106,104),"Serie1");
 $DataSet->AddPoint(array(700,2705,2041,1712,2051,846,903),"Serie2");
 $DataSet->AddPoint(array("03 Oct","02 Oct","01 Oct","30 Sep","29 Sep","28 Sep","27 Sep"),"Serie3");
 $DataSet->AddSerie("Serie1");
 $DataSet->SetAbsciseLabelSerie("Serie3");
 $DataSet->SetSerieName("SourceForge Rank","Serie1");
 $DataSet->SetSerieName("Web Hits","Serie2");

 // Initialise the graph
 $Test = new pChart(660,230);
 $Test->drawGraphAreaGradient(90,90,90,90,TARGET_BACKGROUND);

 // Prepare the graph area
 $Test->setFontProperties("fonts/tahoma.ttf",8);
 $Test->setGraphArea(60,40,595,190);

 // Initialise graph area
 $Test->setFontProperties("fonts/tahoma.ttf",8);

 // Draw the SourceForge Rank graph
 $DataSet->SetYAxisName("Sourceforge Rank");
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,213,217,221,TRUE,0,0);
 $Test->drawGraphAreaGradient(40,40,40,-50);
 $Test->drawGrid(4,TRUE,230,230,230,10);
 $Test->setShadowProperties(3,3,0,0,0,30,4);
 $Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->clearShadow();
 $Test->drawFilledCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription(),.1,30);
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

 // Clear the scale
 $Test->clearScale();

 // Draw the 2nd graph
 $DataSet->RemoveSerie("Serie1");
 $DataSet->AddSerie("Serie2");
 $DataSet->SetYAxisName("Web Hits");
 $Test->drawRightScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,213,217,221,TRUE,0,0);
 $Test->drawGrid(4,TRUE,230,230,230,10);
 $Test->setShadowProperties(3,3,0,0,0,30,4);
 $Test->drawCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription());
 $Test->clearShadow();
 $Test->drawFilledCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription(),.1,30);
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

 // Write the legend (box less)
 $Test->setFontProperties("fonts/tahoma.ttf",8);
 $Test->drawLegend(530,5,$DataSet->GetDataDescription(),0,0,0,0,0,0,255,255,255,FALSE);

 // Write the title
 $Test->setFontProperties("fonts/MankSans.ttf",18);
 $Test->setShadowProperties(1,1,0,0,0);
 $Test->drawTitle(0,0,"SourceForge ranking summary",255,255,255,660,30,TRUE);
 $Test->clearShadow();

 // Render the picture
 $Test->Stroke("example26.png");
?>