/*
YUI 3.12.0 (build 8655935)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-marker-stacked",function(e,t){e.StackedMarkerSeries=e.Base.create("stackedMarkerSeries",e.MarkerSeries,[e.StackingUtil],{setAreaData:function(){e.StackedMarkerSeries.superclass.setAreaData.apply(this),this._stackCoordinates.apply(this)}},{ATTRS:{type:{value:"stackedMarker"}}})},"3.12.0",{requires:["series-stacked","series-marker"]});
