/*
Copyright (c) 2010, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.com/yui/license.html
version: 3.2.0
build: 2676
*/
YUI.add("lang/datatype-date-format_pl",function(A){A.Intl.add("datatype-date-format","pl",{"a":["niedz.","pon.","wt.","śr.","czw.","pt.","sob."],"A":["niedziela","poniedziałek","wtorek","środa","czwartek","piątek","sobota"],"b":["sty","lut","mar","kwi","maj","cze","lip","sie","wrz","paź","lis","gru"],"B":["stycznia","lutego","marca","kwietnia","maja","czerwca","lipca","sierpnia","września","października","listopada","grudnia"],"c":"%a, %d %b %Y %H:%M:%S %Z","p":["AM","PM"],"P":["am","pm"],"x":"%d-%m-%y","X":"%H:%M:%S"});},"3.2.0");YUI.add("lang/datatype-date_pl",function(A){},"3.2.0",{use:["lang/datatype-date-format_pl"]});YUI.add("lang/datatype_pl",function(A){},"3.2.0",{use:["lang/datatype-date_pl"]});