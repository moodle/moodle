/* 
 * Library for flash destection
 */

//WARNING: before to use this function you need to load lib/swfobject/swfobject.js + YUI: 'yahoo-min.js', 'event-min.js', 'connection-min.js'
function setflashversiontosession (wwwroot, sesskey) {
    var flashversion = swfobject.getFlashPlayerVersion();
    var callback = {}; //the callback is mandatory in 2.8.0r4 because there is a bug when checking xdr attribute
    YAHOO.util.Connect.asyncRequest('GET',wwwroot+'/login/environment.php?sesskey='+sesskey+'&flashversion='+flashversion.major+'.'+flashversion.minor+'.'+flashversion.release, callback);
}