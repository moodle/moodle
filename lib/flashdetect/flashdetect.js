/* 
 * Library for flash destection
 */

//WARNING: before to use this function you need to load lib/swfobject/swfobject.js + YUI: 'yahoo-min.js', 'event-min.js', 'connection-min.js'
function setflashversiontosession (wwwroot, sesskey) {
    var flashversion = swfobject.getFlashPlayerVersion();
    YAHOO.util.Connect.asyncRequest('GET',wwwroot+'/login/environment.php?sesskey='+sesskey+'&flashversion='+flashversion.major+'.'+flashversion.minor+'.'+flashversion.release);
}