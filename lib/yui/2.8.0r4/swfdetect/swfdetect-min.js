/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.8.0r4
*/
YAHOO.namespace("util");(function(){var A=0;var B=YAHOO.env.ua;var C="ShockwaveFlash";if(B.gecko||B.webkit||B.opera){if((mF=navigator.mimeTypes["application/x-shockwave-flash"])){if((eP=mF.enabledPlugin)){var G=[];G=eP.description.replace(/\s[rd]/g,".").replace(/[A-Za-z\s]+/g,"").split(".");A=G[0]+".";switch((G[2].toString()).length){case 1:A+="00";break;case 2:A+="0";break;}A+=G[2];A=parseFloat(A);}}}else{if(B.ie){try{var D=new ActiveXObject(C+"."+C+".6");D.AllowScriptAccess="always";}catch(F){if(D!=null){A=6;}}if(A==0){try{var E=new ActiveXObject(C+"."+C);var G=[];G=E.GetVariable("$version").replace(/[A-Za-z\s]+/g,"").split(",");A=G[0]+".";switch((G[2].toString()).length){case 1:A+="00";break;case 2:A+="0";break;}A+=G[2];A=parseFloat(A);}catch(F){}}}}B.flash=A;YAHOO.util.SWFDetect={getFlashVersion:function(){return A;},isFlashVersionAtLeast:function(H){return A>=H;}};})();YAHOO.register("swfdetect",YAHOO.util.SWFDetect,{version:"2.8.0r4",build:"2449"});