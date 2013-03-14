/* YUI 3.9.0 (build 5827) Copyright 2013 Yahoo! Inc. http://yuilibrary.com/license/ */
YUI.add("array-invoke",function(e,t){e.Array.invoke=function(t,n){var r=e.Array(arguments,2,!0),i=e.Lang.isFunction,s=[];return e.Array.each(e.Array(t),function(e,t){e&&i(e[n])&&(s[t]=e[n].apply(e,r))}),s}},"3.9.0",{requires:["yui-base"]});
