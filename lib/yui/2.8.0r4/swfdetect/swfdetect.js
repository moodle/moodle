/*
Copyright (c) 2009, Yahoo! Inc. All rights reserved.
Code licensed under the BSD License:
http://developer.yahoo.net/yui/license.txt
version: 2.8.0r4
*/
/**
 * Utility for Flash version detection
 * @namespace YAHOO.util
 * @module swfdetect
 */
YAHOO.namespace("util");

/**
 * Flafh detection utility.
 * @class SWFDetect
 * @static
 */
(function () {
	
var version = 0;
var uA = YAHOO.env.ua;
var sF = "ShockwaveFlash";

 	if (uA.gecko || uA.webkit || uA.opera) {
		   if ((mF = navigator.mimeTypes['application/x-shockwave-flash'])) {
		      if ((eP = mF.enabledPlugin)) {
				 var vS = [];
		         vS = eP.description.replace(/\s[rd]/g, '.').replace(/[A-Za-z\s]+/g, '').split('.');
		        version = vS[0] + '.';
				switch((vS[2].toString()).length)
				{
					case 1:
					version += "00";
					break;
					case 2: 
					version += "0";
					break;
				}
		 		version +=  vS[2];
				version = parseFloat(version);
		      }
		   }
		}
		else if(uA.ie) {
		    try
		    {
		        var ax6 = new ActiveXObject(sF + "." + sF + ".6");
		        ax6.AllowScriptAccess = "always";
		    }
		    catch(e)
		    {
		        if(ax6 != null)
		        {
		            version = 6.0;
		        }
		    }
		    if (version == 0) {
		    try
		    {
		        var ax  = new ActiveXObject(sF + "." + sF);
		       	var vS = [];
		        vS = ax.GetVariable("$version").replace(/[A-Za-z\s]+/g, '').split(',');
		        version = vS[0] + '.';
				switch((vS[2].toString()).length)
				{
					case 1:
					version += "00";
					break;
					case 2: 
					version += "0";
					break;
				}
		 		version +=  vS[2];
				version = parseFloat(version);
				
		    } catch (e) {}
		    }
		}
		
		uA.flash = version;
		
YAHOO.util.SWFDetect = {		
		getFlashVersion : function () {
			return version;
		},
		
		isFlashVersionAtLeast : function (ver) {
			return version >= ver;
		}	
	};
})();
YAHOO.register("swfdetect", YAHOO.util.SWFDetect, {version: "2.8.0r4", build: "2449"});
