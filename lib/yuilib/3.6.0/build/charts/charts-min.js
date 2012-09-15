/*
YUI 3.6.0 (build 5521)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("charts",function(b){function a(c){if(c.type!="pie"){return new b.CartesianChart(c);}else{return new b.PieChart(c);}}b.Chart=a;},"3.6.0",{requires:["charts-base"]});