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
// Author(s):	leonreinders@hetnet.nl				LEON
//
// History:
//
//	LEON	04-08-2001
//			Initial Implementation
//
//	OZ		30-08-2001
//			* Correct a problem where the last amendment could not be re-done.
//			* Alter the way the buttons are disabled so that the background of
//			the button is transparent and matches the user rebar background
//			color - specifically - buttonface.
//
//	OZ		22-01-2002
//			Only do any saving if the history option is enabled.  This should
//			speed up the editor in the default case.  This is because the
//			history option takes entire copies of the buffer being edited which
//			can consume large amounts of memory.
//
////////////////////////////////////////////////////////////////////////////////

var history = new Object;
history.items = [];
history.cursor = -1;

// saveHistory(): Saves a copy of the document in the history.items.items buffer
function saveHistory() {
	if (!getOption("history")) return;
	codeSweeper();
	history.items[history.items.length] = doc.innerHTML;
	history.cursor = history.items.length;
	// window.status = 'saveHistory() cursor=' + history.cursor + ', items = ' + history.items.length;
	showHistory();
}

// goHistory(): Advance or retreat the history.items.items cursor and show the
// document as it was at that point in time.
function goHistory(value) {

	if (!RichEditor.txtView) return;
	switch(value) {
	case -1:
		i = history.cursor - 1;
		// when first start undoing, save final state at end of history buffer
		// so it can be re-done.
		if (history.cursor == history.items.length) {
			saveHistory();
		}
		history.cursor = i;
		break;
	case 1:
		history.cursor ++;
		break;
	}
	if (history.items[history.cursor]) {
		doc.innerHTML = history.items[history.cursor];
	}
	// window.status = 'goHistory(' + value + ') cursor=' + history.cursor + ', items = ' + history.items.length;
	showHistory()
}

// showHistory(): enable and disable the history.items buttons as appropriate
function showHistory() {

	if (history.cursor > 0) {
		btnPrev.className = "";
		btnPrev.disabled = false;
	} else {
		btnPrev.className = "disabled";
		btnPrev.disabled = true;
	}

	if (history.cursor < history.items.length - 1) {
		btnNext.className = "";
		btnNext.disabled = false;
	} else {
		btnNext.className = "disabled";
		btnNext.disabled = true;
	}
}
