/*******************************************************
COOKIE FUNCTIONALITY
Based on "Night of the Living Cookie" by Bill Dortch
(c) 2003, Ryan Parman
http://www.skyzyx.com
Distributed according to SkyGPL 2.1, http://www.skyzyx.com/license/
*******************************************************/
function cookie(name, value, expires, path, domain, secure)
{
	// Passed Values
	this.name=name;
	this.value=value;
	this.expires=expires;
	this.path=path;
	this.domain=domain;
	this.secure=secure;

	// Read cookie
	this.read=function()
	{
		// To allow for faster parsing
		var ck=document.cookie;

		var arg = this.name + "=";
		var alen = arg.length;
		var clen = ck.length;
		var i = 0;

		while (i < clen)
		{
			var j = i + alen;
			if (ck.substring(i, j) == arg)
			{
				var endstr = ck.indexOf (";", j);
				if (endstr == -1) endstr = ck.length;
				return unescape(ck.substring(j, endstr));
			}
			i = ck.indexOf(" ", i) + 1;
			if (i == 0) break;
		}
		return null;
	};

	// Set cookie
	this.set=function()
	{
		// Store initial value of "this.expires" for re-initialization.
		expStore=this.expires;

		// Set time to absolute zero.
		exp = new Date();
		base = new Date(0);
		skew = base.getTime();
		if (skew > 0)  exp.setTime (exp.getTime() - skew);
		exp.setTime(exp.getTime() + (this.expires*24*60*60*1000));
		this.expires=exp;

		document.cookie = this.name + "=" + escape (this.value) +
				((this.expires) ? "; expires=" + this.expires.toGMTString() : "") +
				((this.path) ? "; path=" + this.path : "") +
				((this.domain) ? "; domain=" + this.domain : "") +
				((this.secure) ? "; secure" : "");

		// Re-initialize
		this.expires=expStore;
	};

	// Kill cookie
	this.kill=function()
	{
		document.cookie = this.name + "=" +
				((this.path) ? "; path=" + this.path : "") +
				((this.domain) ? "; domain=" + this.domain : "") +
				"; expires=Thu, 01-Jan-70 00:00:01 GMT";
	};

	// Change cookie settings.
	this.changeName=function(chName) { this.kill(); this.name=chName; this.set(); };
	this.changeVal=function(chVal) { this.kill(); this.value=chVal; this.set(); };
	this.changeExp=function(chExp) { this.kill(); this.expires=chExp; this.set(); };
	this.changePath=function(chPath) { this.kill(); this.path=chPath; this.set(); };
	this.changeDomain=function(chDom) { this.kill(); this.domain=chDom; this.set(); };
	this.changeSecurity=function(chSec) { this.kill(); this.secure=chSec; this.set(); }
}
