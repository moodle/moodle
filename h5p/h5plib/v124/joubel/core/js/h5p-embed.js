/*jshint multistr: true */

/**
 * Converts old script tag embed to iframe
 */
var H5POldEmbed = H5POldEmbed || (function () {
  var head = document.getElementsByTagName('head')[0];
  var resizer = false;

  /**
   * Loads the resizing script
   */
  var loadResizer = function (url) {
    var data, callback = 'H5POldEmbed';
    resizer = true;

    // Callback for when content data is loaded.
    window[callback] = function (content) {
      // Add resizing script to head
      var resizer = document.createElement('script');
      resizer.src = content;
      head.appendChild(resizer);

      // Clean up
      head.removeChild(data);
      delete window[callback];
    };

    // Create data script
    data = document.createElement('script');
    data.src = url + (url.indexOf('?') === -1 ? '?' : '&') + 'callback=' + callback;
    head.appendChild(data);
  };

  /**
   * Replaced script tag with iframe
   */
  var addIframe = function (script) {
    // Add iframe
    var iframe = document.createElement('iframe');
    iframe.src = script.getAttribute('data-h5p');
    iframe.frameBorder = false;
    iframe.allowFullscreen = true;
    var parent = script.parentNode;
    parent.insertBefore(iframe, script);
    parent.removeChild(script);
  };

  /**
   * Go throught all script tags with the data-h5p attribute and load content.
   */
  function H5POldEmbed() {
    var scripts = document.getElementsByTagName('script');
    var h5ps = []; // Use seperate array since scripts grow in size.
    for (var i = 0; i < scripts.length; i++) {
      var script = scripts[i];
      if (script.src.indexOf('/h5p-resizer.js') !== -1) {
        resizer = true;
      }
      else if (script.hasAttribute('data-h5p')) {
        h5ps.push(script);
      }
    }
    for (i = 0; i < h5ps.length; i++) {
      if (!resizer) {
        loadResizer(h5ps[i].getAttribute('data-h5p'));
      }
      addIframe(h5ps[i]);
    }
  }

  return H5POldEmbed;
})();

new H5POldEmbed();
