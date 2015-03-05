YUI.add('moodle-theme_bootstrap-zoom', function (Y, NAME) {

/* zoom.js
 * copyright  2014 Bas Brands, www.basbrands.nl
 * authors    Bas Brands, David Scotson
 * license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  */

var onZoom = function() {
  var zoomin = Y.one('body').hasClass('zoomin');
  if (zoomin) {
    Y.one('body').removeClass('zoomin');
    M.util.set_user_preference('theme_bootstrap_zoom', 'nozoom');
  } else {
    Y.one('body').addClass('zoomin');
    M.util.set_user_preference('theme_bootstrap_zoom', 'zoomin');
  }
};

//When the button with class .moodlezoom is clicked fire the onZoom function
M.theme_bootstrap = M.theme_bootstrap || {};
M.theme_bootstrap.zoom =  {
  init: function() {
    Y.one('body').delegate('click', onZoom, '.moodlezoom');
  }
};

}, '@VERSION@', {"requires": ["node"]});
