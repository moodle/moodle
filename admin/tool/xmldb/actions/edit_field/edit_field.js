/// $Id $

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas     http://dougiamas.com  //
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
        transformForm();

    /// Get the required fields
        var typeField         = document.getElementById('menutype');
        var sequenceField     = document.getElementById('menusequence');

    /// Register the rest of events
        if (typeField.addEventListener) {
        /// Standard
            typeField.addEventListener('change', transformForm, false);
            sequenceField.addEventListener('change', transformForm, false);
        } else {
        /// IE 5.5
            typeField.attachEvent('onchange', transformForm);
            sequenceField.attachEvent('onchange', transformForm);
        }
    }

/**
 * This function controls all modifications to perform when any field changes
 */
function transformForm(event) {

/// Initialize all the needed variables
    var typeField         = document.getElementById('menutype');
    var lengthField       = document.getElementById('length');
    var decimalsField     = document.getElementById('decimals');
    var unsignedField     = document.getElementById('menuunsigned');
    var notnullField      = document.getElementById('menunotnull');
    var sequenceField     = document.getElementById('menusequence');
    var defaultField      = document.getElementById('default');

    var lengthTip         = document.getElementById('lengthtip');
    var decimalsTip       = document.getElementById('decimalstip');

/// Initially, enable everything
    decimalsField.disabled = false;
    unsignedField.disabled = false;
    notnullField.disabled = false;
    sequenceField.disabled = false;
    defaultField.disabled = false;

/// Based on sequence, disable some items
    if (sequenceField.value == '1') {
        unsignedField.disabled = true;
        unsignedField.value = '1';
        notnullField.disabled = true;
        notnullField.value = '1';
        defaultField.disabled = true;
        defaultField.value = '';
    }


/// Based on type, disable some items
    switch (typeField.value) {
        case '1':  // XMLDB_TYPE_INTEGER
            lengthTip.innerHTML = ' 1...20';
            decimalsTip.innerHTML = '';
            decimalsField.disabled = true;
            decimalsField.value = '';
            break;
        case '2':  // XMLDB_TYPE_NUMBER
            lengthTip.innerHTML = ' 1...20';
            decimalsTip.innerHTML = ' 0...length or empty';
            break;
        case '3':  // XMLDB_TYPE_FLOAT
            lengthTip.innerHTML = ' 1...20 or empty';
            decimalsTip.innerHTML = ' 0...length or empty';
            break;
        case '4':  // XMLDB_TYPE_CHAR
            lengthTip.innerHTML = ' 1...255';
            decimalsTip.innerHTML = '';
            decimalsField.disabled = true;
            decimalsField.value = '';
            unsignedField.disabled = true;
            unsignedField.value = '0';
            sequenceField.disabled = true;
            sequenceField.value = '0';
            break;
        case '5':  // XMLDB_TYPE_TEXT
            lengthTip.innerHTML = ' small, medium, big';
            decimalsTip.innerHTML = '';
            decimalsField.disabled = true;
            decimalsField.value = '';
            unsignedField.disabled = true;
            unsignedField.value = '0';
            sequenceField.disabled = true;
            sequenceField.value = '0';
            defaultField.disabled = true;
            defaultField.value = '';
            break;
        case '6':  // XMLDB_TYPE_BINARY
            lengthTip.innerHTML = ' small, medium, big';
            decimalsTip.innerHTML = '';
            decimalsField.disabled = true;
            decimalsField.value = '';
            unsignedField.disabled = true;
            unsignedField.value = '0';
            sequenceField.disabled = true;
            sequenceField.value = '0';
            defaultField.disabled = true;
            defaultField.value = '';
            break;
        case '7':  // XMLDB_TYPE_DATETIME
            lengthTip.innerHTML = '';
            lengthField.disabled = true;
            lengthField.value = '';
            decimalsTip.innerHTML = '';
            decimalsField.disabled = true;
            decimalsField.value = '';
            unsignedField.disabled = true;
            unsignedField.value = '0';
            sequenceField.disabled = true;
            sequenceField.value = '0';
            defaultField.disabled = true;
            defaultField.value = '';
            break;
    }
}
