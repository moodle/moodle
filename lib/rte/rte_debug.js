////////////////////////////////////////////////////////////////////////////////
//
// HTML Text Editing Component for hosting in Web Pages
// Copyright (C) 2001  Ramesys (Contracting Services) Limited
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
//
// You should have received a copy of the GNU LesserGeneral Public License
// along with this program; if not a copy can be obtained from
//
//    http://www.gnu.org/copyleft/lesser.html
//
// or by writing to:
//
//    Free Software Foundation, Inc.
//    59 Temple Place - Suite 330,
//    Boston,
//    MA  02111-1307,
//    USA.
//
// Original Developer:
//
//	Austin David France
//	Ramesys (Contracting Services) Limited
//	Mentor House
//	Ainsworth Street
//	Blackburn
//	Lancashire
//	BB1 6AY
//	United Kingdom
//  email: Austin.France@Ramesys.com
//
// Home Page:    http://richtext.sourceforge.net/
// Support:      http://richtext.sourceforge.net/
//
////////////////////////////////////////////////////////////////////////////////
//
// Authors & Contributers:
//
//	OZ		Austin David France		[austin.france@ramesys.com]
//				Primary Developer
//
////////////////////////////////////////////////////////////////////////////////

// DBG(): Get the debug window handle in a safe manaer.
function DBGGetWindow(el) {
	if (el) {
		// Debug window closed?
		try { el.className; } catch(e) {
			if (e.number == -2147418094) {
				return null;
			}
		}
	}
	return el;
}

// DBG(): Debug routine activated by the debugWindow property
function DBG(n, str)
{
	// Initialise debug functionality, first time in or if DBG() is called
	// with no arguments (as called from put_debugWindow).
	if (typeof(n) == "undefined" || !DBG.fInitialised) {
		var el = DBGGetWindow(public_description.debugWindow);
		if (el) {
			el.className = "debugWindow";
			el.innerHTML = '<table width="100%" id="debug">'
							+ '<tr><th>Seq</th><th>Caller</th><th>Debug</th></tr>'
							+ '</table>';
			DBG.idTable = el.all("debug");
		}
		DBG.fInitialised = true;
		DBG.seq = 0;
	}

	// If debug window supplied, then output debug message, assuming one was
	// supplied.
	if (typeof(str) != "undefined") {
		var el = DBGGetWindow(DBG.idTable);
		if (el) {
			var row = el.insertRow(1);
			var caller = DBG.caller.toString().substr(9);
			var cell = row.insertCell();
			cell.innerText = DBG.seq++;
			cell.nowrap = '';
			cell = row.insertCell();
			cell.innerText = caller.substr(0, caller.indexOf('\n'));
			cell.nowrap = '';
			row.insertCell().innerText = str;
		} else {
			// If no debug window, but RichEdit.debug is true, then output
			// debugs to status bar.
			if (RichEditor.debug) {
				window.status = str;
			}
		}
	}
}
