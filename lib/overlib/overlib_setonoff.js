//\/////
//\  overLIB Set On/Off Plugin
//\  This file requires overLIB 4.10 or later.
//\
//\  overLIB 4.10 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2003. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//   $Revision$                $Date$
//
//\/////
//\mini
////////
// PRE-INIT
// Ignore these lines, configuration is below.
////////
if (typeof olInfo == 'undefined' || typeof olInfo.meets == 'undefined' || !olInfo.meets(4.10)) alert('overLIB 4.10 or later is required for the Set On/Off Plugin.');
else {
registerCommands('seton, setoff');
var olSetType;
// END OF CONFIGURATION
// Don't change anything below this line, all configuration is above.
////////
// PLUGIN FUNCTIONS
////////
// Set runtime variables
function setOnOffVariables() {
	olSetType = 0;
}
// Parses Set On/Off Parameters
function parseOnOffExtras(pf, i, ar) {
	var k = i, v;

	if (k < ar.length) {
		if (ar[k] == SETON||ar[k] == SETOFF) { olSetType = 1; k = opt_MULTICOMMANDS(++k, ar); return k; }
	}

	return -1;
}
///////
//  HELPER FUNCTIONS
///////
// searches arg list for COMMAND; 
function hasCommand(istrt, args, COMMAND) {
	for (var i = istrt; i < args.length; i++) {
		if (typeof args[i] == 'number' &&  args[i] == COMMAND) return i;
	}

	return -1;
}
// scans for toggle like commands to be forced ON/OFF
function scanCommandSet(pf, args) {
	var k = -1, j, je;

	if (olSetType) {
		// search for SETON command
		while ((k = hasCommand(++k, args, SETON)) < args.length && k > -1) {
			je = opt_MULTICOMMANDS(k + 1, args);
			for (j = k + 1; j <  (k + je); j++) setNoParamCommand(1, pf, args[j]);
			k += (je - 1);
		}
		// search for SETOFF command
		k = -1;
		while ((k = hasCommand(++k, args, SETOFF)) < args.length && k > -1) {
			je = opt_MULTICOMMANDS(k + 1, args);
			for (j = k + 1; j <  (k + je); j++) setNoParamCommand(0, pf, args[j]);
			k += (je - 1);
		}		
	}

	return true;
}
var olRe;
// set command according to whichType (0 or 1)
function setNoParamCommand(whichType, pf, COMMAND) {
	var v = pms[COMMAND - 1 - pmStart];

	if(pmt && !olRe) olRe = eval('/' + pmt.split(',').join('|') + '/');
	if (pf != 'ol_' &&  /capturefirst/.test(v)) return;  // no o3_capturefirst variable
	if (pf != 'ol_' &&  /wrap/.test(v) &&  eval(pf + 'wrap') &&  (whichType == 0)) {
		nbspCleanup();   // undo wrap effects since checked after all parsing
		o3_width = ol_width;
	}

	if (olRe.test(v))	eval(pf + v + '=' + ((whichType && COMMAND == AUTOSTATUSCAP) ? whichType++ : whichType));
}
function opt_MULTICOMMANDS(i, ar) {
	var k = i;

	while (k < ar.length &&  typeof ar[k] == 'number' &&  ar[k] > pmStart) {k++; if (ar[k - 1] == 'SETON'||ar[k - 1] == 'SETOFF') break;}
	k -= (k < ar.length ? 2 : 1);

	return k;
}
////////
// PLUGIN REGISTRATIONS
////////
registerRunTimeFunction(setOnOffVariables);
registerCmdLineFunction(parseOnOffExtras);
registerPostParseFunction(scanCommandSet);
}