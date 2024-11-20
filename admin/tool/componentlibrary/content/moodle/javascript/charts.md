---
layout: docs
title: "Moodle Charts"
date: 2020-01-14T16:32:24+01:00
draft: false
---

## How it works

The core chart_builder interface for the ChartJS library that allows you to create a nice visual presentation of your data.

## Source files

* `lib/amd/src/chart_builder.js`
* `lib/amd/src/chart_*.js`
* `lib/templates/chart.mustache`

## Core api

Create a new instance of your required chart type

{{< php >}}
  $chart1 = new chart_bar();
  $series1 = new chart_series('Data type', $data);
  $chart1->add_series($series1);
  $OUTPUT->render($chart1);
{{< / php >}}


### JavaScript

{{< mustache template="core/chart" >}}
{
    "chartdata": "{\u0022type\u0022:\u0022pie\u0022,\u0022series\u0022:[{\u0022label\u0022:\u0022Sales\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[1000,1170,660,1030],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null}],\u0022labels\u0022:[\u00222004\u0022,\u00222005\u0022,\u00222006\u0022,\u00222007\u0022],\u0022title\u0022:\u0022PIE CHART\u0022,\u0022axes\u0022:{\u0022x\u0022:[],\u0022y\u0022:[]},\u0022config_colorset\u0022:null,\u0022doughnut\u0022:null}",
    "withtable": true
}
{{< /mustache >}}

{{< mustache template="core/chart" >}}
{
    "chartdata": "{\u0022type\u0022:\u0022pie\u0022,\u0022series\u0022:[{\u0022label\u0022:\u0022Sales\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[1000,1170,660,1030],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null}],\u0022labels\u0022:[\u00222004\u0022,\u00222005\u0022,\u00222006\u0022,\u00222007\u0022],\u0022title\u0022:\u0022DOUGHNUT CHART\u0022,\u0022axes\u0022:{\u0022x\u0022:[],\u0022y\u0022:[]},\u0022config_colorset\u0022:null,\u0022doughnut\u0022:true}",
    "withtable": true
}
{{< /mustache >}}

{{< mustache template="core/chart" >}}
{
    "chartdata": "{\u0022type\u0022:\u0022line\u0022,\u0022series\u0022:[{\u0022label\u0022:\u0022Sales\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[1000,1170,660,1030],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null},{\u0022label\u0022:\u0022Expenses\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[400,460,1120,540],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null}],\u0022labels\u0022:[\u00222004\u0022,\u00222005\u0022,\u00222006\u0022,\u00222007\u0022],\u0022title\u0022:\u0022TENSIONED LINES CHART\u0022,\u0022axes\u0022:{\u0022x\u0022:[],\u0022y\u0022:[]},\u0022config_colorset\u0022:null,\u0022smooth\u0022:false}",
    "withtable": true
}
{{< /mustache >}}

{{< mustache template="core/chart" >}}
{
    "chartdata": "{\u0022type\u0022:\u0022line\u0022,\u0022series\u0022:[{\u0022label\u0022:\u0022Sales\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[1000,1170,660,1030],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null},{\u0022label\u0022:\u0022Expenses\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[400,460,1120,540],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null}],\u0022labels\u0022:[\u00222004\u0022,\u00222005\u0022,\u00222006\u0022,\u00222007\u0022],\u0022title\u0022:\u0022SMOOTH LINES CHART\u0022,\u0022axes\u0022:{\u0022x\u0022:[],\u0022y\u0022:[]},\u0022config_colorset\u0022:null,\u0022smooth\u0022:true}",
    "withtable": true
}
{{< /mustache >}}

{{< mustache template="core/chart" >}}
{
    "chartdata": "{\u0022type\u0022:\u0022bar\u0022,\u0022series\u0022:[{\u0022label\u0022:\u0022Sales\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[1000,1170,660,1030],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null},{\u0022label\u0022:\u0022Expenses\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[400,460,1120,540],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null}],\u0022labels\u0022:[\u00222004\u0022,\u00222005\u0022,\u00222006\u0022,\u00222007\u0022],\u0022title\u0022:\u0022BAR CHART\u0022,\u0022axes\u0022:{\u0022x\u0022:[],\u0022y\u0022:[{\u0022label\u0022:null,\u0022labels\u0022:null,\u0022max\u0022:null,\u0022min\u0022:0,\u0022position\u0022:null,\u0022stepSize\u0022:null}]},\u0022config_colorset\u0022:null,\u0022horizontal\u0022:false,\u0022stacked\u0022:null}",
    "withtable": true
}
{{< /mustache >}}

{{< mustache template="core/chart" >}}
{
    "chartdata": "{\u0022type\u0022:\u0022bar\u0022,\u0022series\u0022:[{\u0022label\u0022:\u0022Sales\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[1000,1170,660,1030],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null},{\u0022label\u0022:\u0022Expenses\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[400,460,1120,540],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null}],\u0022labels\u0022:[\u00222004\u0022,\u00222005\u0022,\u00222006\u0022,\u00222007\u0022],\u0022title\u0022:\u0022HORIZONTAL BAR CHART\u0022,\u0022axes\u0022:{\u0022x\u0022:[],\u0022y\u0022:[{\u0022label\u0022:null,\u0022labels\u0022:null,\u0022max\u0022:null,\u0022min\u0022:0,\u0022position\u0022:null,\u0022stepSize\u0022:null}]},\u0022config_colorset\u0022:null,\u0022horizontal\u0022:true,\u0022stacked\u0022:null}",
    "withtable": true
}
{{< /mustache >}}

{{< mustache template="core/chart" >}}
{
    "chartdata": "{\u0022type\u0022:\u0022bar\u0022,\u0022series\u0022:[{\u0022label\u0022:\u0022Sales\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[1000,1170,660,1030],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null},{\u0022label\u0022:\u0022Expenses\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[400,460,1120,540],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null}],\u0022labels\u0022:[\u00222004\u0022,\u00222005\u0022,\u00222006\u0022,\u00222007\u0022],\u0022title\u0022:\u0022STACKED BAR CHART\u0022,\u0022axes\u0022:{\u0022x\u0022:[],\u0022y\u0022:[{\u0022label\u0022:null,\u0022labels\u0022:null,\u0022max\u0022:null,\u0022min\u0022:0,\u0022position\u0022:null,\u0022stepSize\u0022:null}]},\u0022config_colorset\u0022:null,\u0022horizontal\u0022:false,\u0022stacked\u0022:true}",
    "withtable": true
}
{{< /mustache >}}

{{< mustache template="core/chart" >}}
{
    "chartdata": "{\u0022type\u0022:\u0022bar\u0022,\u0022series\u0022:[{\u0022label\u0022:\u0022Expenses\u0022,\u0022labels\u0022:null,\u0022type\u0022:\u0022line\u0022,\u0022values\u0022:[400,460,1120,540],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null},{\u0022label\u0022:\u0022Sales\u0022,\u0022labels\u0022:null,\u0022type\u0022:null,\u0022values\u0022:[1000,1170,660,1030],\u0022colors\u0022:[],\u0022axes\u0022:{\u0022x\u0022:null,\u0022y\u0022:null},\u0022smooth\u0022:null}],\u0022labels\u0022:[\u00222004\u0022,\u00222005\u0022,\u00222006\u0022,\u00222007\u0022],\u0022title\u0022:\u0022BAR CHART COMBINED WITH LINE CHART\u0022,\u0022axes\u0022:{\u0022x\u0022:[],\u0022y\u0022:[{\u0022label\u0022:null,\u0022labels\u0022:null,\u0022max\u0022:null,\u0022min\u0022:0,\u0022position\u0022:null,\u0022stepSize\u0022:null}]},\u0022config_colorset\u0022:null,\u0022horizontal\u0022:false,\u0022stacked\u0022:null}",
    "withtable": true
}
{{< /mustache >}}
