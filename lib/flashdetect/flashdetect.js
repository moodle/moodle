///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/* 
 * Library for flash destection
 */

//WARNING: before to use this function you need to load lib/swfobject/swfobject.js + YUI: 'yahoo-min.js', 'event-min.js', 'connection-min.js'
function setflashversiontosession (wwwroot, sesskey) {
    var flashversion = swfobject.getFlashPlayerVersion();
    var callback = {}; //the callback is mandatory in 2.8.0r4 because there is a bug when checking xdr attribute
    YAHOO.util.Connect.asyncRequest('GET',wwwroot+'/login/environment.php?sesskey='+sesskey+'&flashversion='+flashversion.major+'.'+flashversion.minor+'.'+flashversion.release, callback);
}