/*
YUI 3.5.1 (build 22)
Copyright 2012 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/
YUI.add("app-transitions",function(b){function a(){}a.ATTRS={transitions:{setter:"_setTransitions",value:false}};a.CLASS_NAMES={transitioning:b.ClassNameManager.getClassName("app","transitioning")};a.FX={fade:{viewIn:"app:fadeIn",viewOut:"app:fadeOut"},slideLeft:{viewIn:"app:slideLeft",viewOut:"app:slideLeft"},slideRight:{viewIn:"app:slideRight",viewOut:"app:slideRight"}};a.prototype={transitions:{navigate:"fade",toChild:"slideLeft",toParent:"slideRight"},_setTransitions:function(d){var c=this.transitions;if(d&&d===true){return b.merge(c);}return d;}};b.App.Transitions=a;b.Base.mix(b.App,[a]);},"3.5.1",{requires:["app-base"]});