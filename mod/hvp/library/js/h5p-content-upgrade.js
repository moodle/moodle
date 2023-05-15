/* global H5PAdminIntegration H5PUtils */

(function ($, Version) {
  var info, $log, $container, librariesCache = {}, scriptsCache = {};

  // Initialize
  $(document).ready(function () {
    // Get library info
    info = H5PAdminIntegration.libraryInfo;

    // Get and reset container
    const $wrapper = $('#h5p-admin-container').html('');
    $log = $('<ul class="content-upgrade-log"></ul>').appendTo($wrapper);
    $container = $('<div><p>' + info.message + '</p></div>').appendTo($wrapper);

    // Make it possible to select version
    var $version = $(getVersionSelect(info.versions)).appendTo($container);

    // Add "go" button
    $('<button/>', {
      class: 'h5p-admin-upgrade-button',
      text: info.buttonLabel,
      click: function () {
        // Start new content upgrade
        new ContentUpgrade($version.val());
      }
    }).appendTo($container);
  });

  /**
   * Generate html for version select.
   *
   * @param {Object} versions
   * @returns {String}
   */
  var getVersionSelect = function (versions) {
    var html = '';
    for (var id in versions) {
      html += '<option value="' + id + '">' + versions[id] + '</option>';
    }
    if (html !== '') {
      html = '<select>' + html + '</select>';
      return html;
    }
  };

  /**
   * Displays a throbber in the status field.
   *
   * @param {String} msg
   * @returns {_L1.Throbber}
   */
  function Throbber(msg) {
    var $throbber = H5PUtils.throbber(msg);
    $container.html('').append($throbber);

    /**
     * Makes it possible to set the progress.
     *
     * @param {String} progress
     */
    this.setProgress = function (progress) {
      $throbber.text(msg + ' ' + progress);
    };
  }

  /**
   * Start a new content upgrade.
   *
   * @param {Number} libraryId
   * @returns {_L1.ContentUpgrade}
   */
  function ContentUpgrade(libraryId) {
    var self = this;

    // Get selected version
    self.version = new Version(info.versions[libraryId]);
    self.version.libraryId = libraryId;

    // Create throbber with loading text and progress
    self.throbber = new Throbber(info.inProgress.replace('%ver', self.version));

    self.started = new Date().getTime();
    self.io = 0;

    // Track number of working
    self.working = 0;

    var start = function () {
      // Get the next batch
      self.nextBatch({
        libraryId: libraryId,
        token: info.token
      });
    };

    if (window.Worker !== undefined) {
      // Prepare our workers
      self.initWorkers();
      start();
    }
    else {
      // No workers, do the job ourselves
      self.loadScript(info.scriptBaseUrl + '/h5p-content-upgrade-process.js' + info.buster, start);
    }
  }

  /**
   * Initialize workers
   */
  ContentUpgrade.prototype.initWorkers = function () {
    var self = this;

    // Determine number of workers (defaults to 4)
    var numWorkers = (window.navigator !== undefined && window.navigator.hardwareConcurrency ? window.navigator.hardwareConcurrency : 4);
    self.workers = new Array(numWorkers);

    // Register message handlers
    var messageHandlers = {
      done: function (result) {
        self.workDone(result.id, result.params, this);
      },
      error: function (error) {
        self.printError(error.err);
        self.workDone(error.id, null, this);
      },
      loadLibrary: function (details) {
        var worker = this;
        self.loadLibrary(details.name, new Version(details.version), function (err, library) {
          if (err) {
            // Reset worker?
            return;
          }

          worker.postMessage({
            action: 'libraryLoaded',
            library: library
          });
        });
      }
    };

    for (var i = 0; i < numWorkers; i++) {
      self.workers[i] = new Worker(info.scriptBaseUrl + '/h5p-content-upgrade-worker.js' + info.buster);
      self.workers[i].onmessage = function (event) {
        if (event.data.action !== undefined && messageHandlers[event.data.action]) {
          messageHandlers[event.data.action].call(this, event.data);
        }
      };
    }
  };

  /**
   * Get the next batch and start processing it.
   *
   * @param {Object} outData
   */
  ContentUpgrade.prototype.nextBatch = function (outData) {
    var self = this;

    // Track time spent on IO
    var start = new Date().getTime();
    $.post(info.infoUrl, outData, function (inData) {
      self.io += new Date().getTime() - start;
      if (!(inData instanceof Object)) {
        // Print errors from backend
        return self.setStatus(inData);
      }
      if (inData.left === 0) {
        var total = new Date().getTime() - self.started;

        if (window.console && console.log) {
          console.log('The upgrade process took ' + (total / 1000) + ' seconds. (' + (Math.round((self.io / (total / 100)) * 100) / 100) + ' % IO)' );
        }

        // Terminate workers
        self.terminate();

        // Nothing left to process
        return self.setStatus(info.done);
      }

      self.left = inData.left;
      self.token = inData.token;

      // Start processing
      self.processBatch(inData.params, inData.skipped);
    });
  };

  /**
   * Set current status message.
   *
   * @param {String} msg
   */
  ContentUpgrade.prototype.setStatus = function (msg) {
    $container.html(msg);
  };

  /**
   * Process the given parameters.
   *
   * @param {Object} parameters
   */
  ContentUpgrade.prototype.processBatch = function (parameters, skipped) {
    var self = this;

    // Track upgraded params
    self.upgraded = {};
    self.skipped = skipped;

    // Track current batch
    self.parameters = parameters;

    // Create id mapping
    self.ids = [];
    for (var id in parameters) {
      if (parameters.hasOwnProperty(id)) {
        self.ids.push(id);
      }
    }

    // Keep track of current content
    self.current = -1;

    if (self.workers !== undefined) {
      // Assign each worker content to upgrade
      for (var i = 0; i < self.workers.length; i++) {
        self.assignWork(self.workers[i]);
      }
    }
    else {

      self.assignWork();
    }
  };

  /**
   *
   */
  ContentUpgrade.prototype.assignWork = function (worker) {
    var self = this;

    var id = self.ids[self.current + 1];
    if (id === undefined) {
      return false; // Out of work
    }
    self.current++;
    self.working++;

    if (worker) {
      worker.postMessage({
        action: 'newJob',
        id: id,
        name: info.library.name,
        oldVersion: info.library.version,
        newVersion: self.version.toString(),
        params: self.parameters[id]
      });
    }
    else {
      new H5P.ContentUpgradeProcess(info.library.name, new Version(info.library.version), self.version, self.parameters[id], id, function loadLibrary(name, version, next) {
        self.loadLibrary(name, version, function (err, library) {
          if (library.upgradesScript) {
            self.loadScript(library.upgradesScript, function (err) {
              if (err) {
                err = info.errorScript.replace('%lib', name + ' ' + version);
              }
              next(err, library);
            });
          }
          else {
            next(null, library);
          }
        });

      }, function done(err, result) {
        if (err) {
          self.printError(err);
          result = null;
        }

        self.workDone(id, result);
      });
    }
  };

  /**
   *
   */
  ContentUpgrade.prototype.workDone = function (id, result, worker) {
    var self = this;

    self.working--;
    if (result === null) {
      self.skipped.push(id);
    }
    else {
      self.upgraded[id] = result;
    }

    // Update progress message
    self.throbber.setProgress(Math.round((info.total - self.left + self.current) / (info.total / 100)) + ' %');

    // Assign next job
    if (self.assignWork(worker) === false && self.working === 0) {
      // All workers have finsihed.
      self.nextBatch({
        libraryId: self.version.libraryId,
        token: self.token,
        skipped: JSON.stringify(self.skipped),
        params: JSON.stringify(self.upgraded)
      });
    }
  };

  /**
   *
   */
  ContentUpgrade.prototype.terminate = function () {
    var self = this;

    if (self.workers) {
      // Stop all workers
      for (var i = 0; i < self.workers.length; i++) {
        self.workers[i].terminate();
      }
    }
  };

  var librariesLoadedCallbacks = {};

  /**
   * Load library data needed for content upgrade.
   *
   * @param {String} name
   * @param {Version} version
   * @param {Function} next
   */
  ContentUpgrade.prototype.loadLibrary = function (name, version, next) {
    var self = this;

    var key = name + '/' + version.major + '/' + version.minor;

    if (librariesCache[key] === true) {
      // Library is being loaded, que callback
      if (librariesLoadedCallbacks[key] === undefined) {
        librariesLoadedCallbacks[key] = [next];
        return;
      }
      librariesLoadedCallbacks[key].push(next);
      return;
    }
    else if (librariesCache[key] !== undefined) {
      // Library has been loaded before. Return cache.
      next(null, librariesCache[key]);
      return;
    }

    // Track time spent loading
    var start = new Date().getTime();
    librariesCache[key] = true;
    $.ajax({
      dataType: 'json',
      cache: true,
      url: info.libraryBaseUrl + '/' + key
    }).fail(function () {
      self.io += new Date().getTime() - start;
      next(info.errorData.replace('%lib', name + ' ' + version));
    }).done(function (library) {
      self.io += new Date().getTime() - start;
      librariesCache[key] = library;
      next(null, library);

      if (librariesLoadedCallbacks[key] !== undefined) {
        for (var i = 0; i < librariesLoadedCallbacks[key].length; i++) {
          librariesLoadedCallbacks[key][i](null, library);
        }
      }
      delete librariesLoadedCallbacks[key];
    });
  };

  /**
   * Load script with upgrade hooks.
   *
   * @param {String} url
   * @param {Function} next
   */
  ContentUpgrade.prototype.loadScript = function (url, next) {
    var self = this;

    if (scriptsCache[url] !== undefined) {
      next();
      return;
    }

    // Track time spent loading
    var start = new Date().getTime();
    $.ajax({
      dataType: 'script',
      cache: true,
      url: url
    }).fail(function () {
      self.io += new Date().getTime() - start;
      next(true);
    }).done(function () {
      scriptsCache[url] = true;
      self.io += new Date().getTime() - start;
      next();
    });
  };

  /**
   *
   */
  ContentUpgrade.prototype.printError = function (error) {
    var self = this;

    switch (error.type) {
      case 'errorParamsBroken':
        error = info.errorContent.replace('%id', error.id) + ' ' + info.errorParamsBroken;
        break;

      case 'libraryMissing':
        error = info.errorLibrary.replace('%lib', error.library);
        break;

      case 'scriptMissing':
        error = info.errorScript.replace('%lib', error.library);
        break;

      case 'errorTooHighVersion':
        error = info.errorContent.replace('%id', error.id) + ' ' + info.errorTooHighVersion.replace('%used', error.used).replace('%supported', error.supported);
        break;

      case 'errorNotSupported':
        error = info.errorContent.replace('%id', error.id) + ' ' + info.errorNotSupported.replace('%used', error.used);
        break;
    }

    $('<li>' + info.error + '<br/>' + error + '</li>').appendTo($log);
  };

})(H5P.jQuery, H5P.Version);
