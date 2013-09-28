/*
YUI 3.12.0 (build 8655935)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-areaspline-stacked",function(e,t){e.StackedAreaSplineSeries=e.Base.create("stackedAreaSplineSeries",e.AreaSeries,[e.CurveUtil,e.StackingUtil],{drawSeries:function(){this._stackCoordinates(),this.drawStackedAreaSpline()}},{ATTRS:{type:{value:"stackedAreaSpline"}}})},"3.12.0",{requires:["series-stacked","series-areaspline"]});
