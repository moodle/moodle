/// $Id $

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// Register the needed events
    onload=function() {
    /// Adjust the form on load
        disablePopupHeads();
    }

/**
 * This function disables some elements from the command and from the fields/keys/indexes drop downs
 */
function disablePopupHeads() {
    var popup = document.getElementById("menucommand");
    var i = popup.length;
    while (i--) {
        option = popup[i];
        if (option.value == "Fields" || option.value == "Keys" || option.value == "Indexes") {
            popup[i].disabled = true;
        }
    }
    var popup = document.getElementById("menufieldkeyindex");
    var i = popup.length;
    while (i--) {
        option = popup[i];
        if (option.value == "fieldshead" || option.value == "keyshead" || option.value == "indexeshead") {
            popup[i].disabled = true;
        }
    }
}
