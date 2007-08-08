//\/////
//\  overLIB Exclusive Plugin
//\  This file requires overLIB 4.10 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//   $Revision$                      $Date$
//\/////
//\mini
////////
// PRE-INIT
// Ignore these lines, configuration is below.
////////
if (typeof olInfo == 'undefined' || typeof olInfo.meets == 'undefined' || !olInfo.meets(4.10)) alert('overLIB 4.10 or later is required for the Debug Plugin.');
else {
registerCommands('exclusive,exclusivestatus,exclusiveoverride');
var olOverrideIsSet;  // variable which tells if override is set


////////
// DEFAULT CONFIGURATION
// Settings you want everywhere are set here. All of this can also be
// changed on your html page or through an overLIB call.
////////
if (typeof ol_exclusive == 'undefined') var ol_exclusive = 0;
if (typeof ol_exclusivestatus == 'undefined') var ol_exclusivestatus = 'Please close open popup first.';

////////
// END OF CONFIGURATION
// Don't change anything below this line, all configuration is above.
////////


////////
// INIT
////////
// Runtime variables init. Don't change for config!
var o3_exclusive = 0;
var o3_exclusivestatus = '';

////////
// PLUGIN FUNCTIONS
////////

// Set runtime variables
function setExclusiveVariables() {
	o3_exclusive = ol_exclusive;
	o3_exclusivestatus = ol_exclusivestatus;
}

// Parses Exclusive Parameters
function parseExclusiveExtras(pf,i,ar) {
	var k = i,v;

	olOverrideIsSet = false;  // a global variable

	if (k < ar.length) {
		if (ar[k] == EXCLUSIVEOVERRIDE) { if(pf !=  'ol_') olOverrideIsSet = true; return k; }
		if (ar[k] == EXCLUSIVE) { eval(pf +  'exclusive = (' +  pf + 'exclusive == 0) ? 1 : 0'); return k; }
		if (ar[k] == EXCLUSIVESTATUS) { eval(pf + "exclusivestatus = '" + escSglQuote(ar[++k]) + "'"); return k; }
	}

	return -1;
}

///////
//  HELPER FUNCTIONS
///////
// set status message and indicate whether popup is exclusive
function isExclusive(args) {
	var rtnVal = false;

	if(args != null) rtnVal = hasCommand(args, EXCLUSIVEOVERRIDE);

	if(rtnVal) return false;
	else {
		self.status = (o3_exclusive) ? o3_exclusivestatus : '';
		return o3_exclusive;
	}

}

// checks overlib argument list to see if it has a COMMAND argument
function hasCommand(args, COMMAND) {
	var rtnFlag = false;

	for (var i=0; i<args.length; i++) {
		if (typeof args[i] == 'number' &&  args[i] == COMMAND) {
			rtnFlag = true;
			break;
		}
	}

	return rtnFlag;
}

// makes sure exclusive setting is off
function clearExclusive() {
	o3_exclusive = 0;
}

function setExclusive() {
	o3_exclusive = (o3_showingsticky &&  o3_exclusive);
}

function chkForExclusive() {
	if (olOverrideIsSet) o3_exclusive = 0;  // turn it off in case it's been set.

	return true;
}

////////
// PLUGIN REGISTRATIONS
////////
registerRunTimeFunction(setExclusiveVariables);
registerCmdLineFunction(parseExclusiveExtras);
registerPostParseFunction(chkForExclusive);
registerHook("createPopup",setExclusive,FBEFORE);
registerHook("hideObject",clearExclusive,FAFTER);
if (olInfo.meets(4.10)) registerNoParameterCommands('exclusive');
}