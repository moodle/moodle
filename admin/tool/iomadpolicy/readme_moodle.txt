jQuery EU Cookie Law popups 1.1.3
-------------
https://github.com/wimagguc/jquery-eu-cookie-law-popup

Instructions to import 'jQuery EU Cookie Law popups' into Moodle:

1. Download the latest release from https://github.com/wimagguc/jquery-eu-cookie-law-popup/releases
2. Copy 'js/jquery-eu-cookie-law-popup.js' into 'amd/src/jquery-eu-cookie-law-popup.js':

   2.a. Replace jquery reference
------------------
(function($) {
------------------
with
------------------
define(['jquery'],function($) {
------------------

   2.b. Remove initialisation code. It will be added and configured only in the pages where is needed
------------------
$(document).ready( function() {
  if ($(".eupopup").length > 0) {
    $(document).euCookieLawPopup().init({
      'info' : 'YOU_CAN_ADD_MORE_SETTINGS_HERE',
      'popupTitle' : 'This website is using cookies. ',
      'popupText' : 'We use them to give you the best experience. If you continue using our website, we\'ll assume that you are happy to receive all cookies on this website.'
    });
  }
});
------------------

   2.c. Remove code
------------------
$(document).bind("user_cookie_consent_changed", function(event, object) {
  console.log("User cookie consent changed: " + $(object).attr('consent') );
});
------------------

   2.d. Replace
------------------
}(jQuery));
------------------
with
------------------
});
------------------

3. Copy the following styles from 'css/jquery-eu-cookie-law-popup.css' into the
"jquery-eu-cookie-law-popup styles" section in 'styles.css':
   .eupopup-container
   .eupopup-container-bottom
   .eupopup-closebutton
   .eupopup-buttons
   .eupopup-button
   .eupopup-button:hover
   .eupopup-button:focus

   Add "tool_iomadpolicy styles" to the end of the styles file.

4. Execute grunt to compile js
   grunt amd

5. Update version number in admin/tool/iomadpolicy/thirdpartylibs.xml
