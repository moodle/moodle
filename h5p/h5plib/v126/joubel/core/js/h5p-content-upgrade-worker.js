/* global importScripts */
var H5P = H5P || {};
importScripts('h5p-version.js', 'h5p-content-upgrade-process.js');

var libraryLoadedCallback;

/**
 * Register message handlers
 */
var messageHandlers = {
  newJob: function (job) {
    // Start new job
    new H5P.ContentUpgradeProcess(job.name, new H5P.Version(job.oldVersion), new H5P.Version(job.newVersion), job.params, job.id, function loadLibrary(name, version, next) {
      // TODO: Cache?
      postMessage({
        action: 'loadLibrary',
        name: name,
        version: version.toString()
      });
      libraryLoadedCallback = next;
    }, function done(err, result) {
      if (err) {
        // Return error
        postMessage({
          action: 'error',
          id: job.id,
          err: err.message ? err.message : err
        });

        return;
      }

      // Return upgraded content
      postMessage({
        action: 'done',
        id: job.id,
        params: result
      });
    });
  },
  libraryLoaded: function (data) {
    var library = data.library;
    if (library.upgradesScript) {
      try {
        importScripts(library.upgradesScript);
      }
      catch (err) {
        libraryLoadedCallback(err);
        return;
      }
    }
    libraryLoadedCallback(null, data.library);
  }
};

/**
 * Handle messages from our master
 */
onmessage = function (event) {
  if (event.data.action !== undefined && messageHandlers[event.data.action]) {
    messageHandlers[event.data.action].call(this, event.data);
  }
};
