/* YUI 3.9.0 (build 5827) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("node-event-simulate",function(e,t){e.Node.prototype.simulate=function(t,n){e.Event.simulate(e.Node.getDOMNode(this),t,n)},e.Node.prototype.simulateGesture=function(t,n,r){e.Event.simulateGesture(this,t,n,r)}},"3.9.0",{requires:["node-base","event-simulate","gesture-simulate"]});
