/*jshint -W083 */
var H5PUpgrades = H5PUpgrades || {};

H5P.ContentUpgradeProcess = (function (Version) {

  /**
   * @class
   * @namespace H5P
   */
  function ContentUpgradeProcess(name, oldVersion, newVersion, params, id, loadLibrary, done) {
    var self = this;

    // Make params possible to work with
    try {
      params = JSON.parse(params);
      if (!(params instanceof Object)) {
        throw true;
      }
    }
    catch (event) {
      return done({
        type: 'errorParamsBroken',
        id: id
      });
    }

    self.loadLibrary = loadLibrary;
    self.upgrade(name, oldVersion, newVersion, params.params, params.metadata, function (err, upgradedParams, upgradedMetadata) {
      if (err) {
        err.id = id;
        return done(err);
      }

      done(null, JSON.stringify({params: upgradedParams, metadata: upgradedMetadata}));
    });
  }

  /**
   * Run content upgrade.
   *
   * @public
   * @param {string} name
   * @param {Version} oldVersion
   * @param {Version} newVersion
   * @param {Object} params
   * @param {Object} metadata
   * @param {Function} done
   */
  ContentUpgradeProcess.prototype.upgrade = function (name, oldVersion, newVersion, params, metadata, done) {
    var self = this;

    // Load library details and upgrade routines
    self.loadLibrary(name, newVersion, function (err, library) {
      if (err) {
        return done(err);
      }
      if (library.semantics === null) {
        return done({
          type: 'libraryMissing',
          library: library.name + ' ' + library.version.major + '.' + library.version.minor
        });
      }

      // Run upgrade routines on params
      self.processParams(library, oldVersion, newVersion, params, metadata, function (err, params, metadata) {
        if (err) {
          return done(err);
        }

        // Check if any of the sub-libraries need upgrading
        asyncSerial(library.semantics, function (index, field, next) {
          self.processField(field, params[field.name], function (err, upgradedParams) {
            if (upgradedParams) {
              params[field.name] = upgradedParams;
            }
            next(err);
          });
        }, function (err) {
          done(err, params, metadata);
        });
      });
    });
  };

  /**
   * Run upgrade hooks on params.
   *
   * @public
   * @param {Object} library
   * @param {Version} oldVersion
   * @param {Version} newVersion
   * @param {Object} params
   * @param {Function} next
   */
  ContentUpgradeProcess.prototype.processParams = function (library, oldVersion, newVersion, params, metadata, next) {
    if (H5PUpgrades[library.name] === undefined) {
      if (library.upgradesScript) {
        // Upgrades script should be loaded so the upgrades should be here.
        return next({
          type: 'scriptMissing',
          library: library.name + ' ' + newVersion
        });
      }

      // No upgrades script. Move on
      return next(null, params, metadata);
    }

    // Run upgrade hooks. Start by going through major versions
    asyncSerial(H5PUpgrades[library.name], function (major, minors, nextMajor) {
      if (major < oldVersion.major || major > newVersion.major) {
        // Older than the current version or newer than the selected
        nextMajor();
      }
      else {
        // Go through the minor versions for this major version
        asyncSerial(minors, function (minor, upgrade, nextMinor) {
          minor =+ minor;
          if (minor <= oldVersion.minor || minor > newVersion.minor) {
            // Older than or equal to the current version or newer than the selected
            nextMinor();
          }
          else {
            // We found an upgrade hook, run it
            var unnecessaryWrapper = (upgrade.contentUpgrade !== undefined ? upgrade.contentUpgrade : upgrade);

            try {
              unnecessaryWrapper(params, function (err, upgradedParams, upgradedExtras) {
                params = upgradedParams;
                if (upgradedExtras && upgradedExtras.metadata) { // Optional
                  metadata = upgradedExtras.metadata;
                }
                nextMinor(err);
              }, {metadata: metadata});
            }
            catch (err) {
              if (console && console.error) {
                console.error("Error", err.stack);
                console.error("Error", err.name);
                console.error("Error", err.message);
              }
              next(err);
            }
          }
        }, nextMajor);
      }
    }, function (err) {
      next(err, params, metadata);
    });
  };

  /**
   * Process parameter fields to find and upgrade sub-libraries.
   *
   * @public
   * @param {Object} field
   * @param {Object} params
   * @param {Function} done
   */
  ContentUpgradeProcess.prototype.processField = function (field, params, done) {
    var self = this;

    if (params === undefined || params === null) {
      return done();
    }

    switch (field.type) {
      case 'library':
        if (params.library === undefined || params.params === undefined) {
          return done();
        }

        // Look for available upgrades
        var usedLib = params.library.split(' ', 2);
        for (var i = 0; i < field.options.length; i++) {
          var availableLib = (typeof field.options[i] === 'string') ? field.options[i].split(' ', 2) : field.options[i].name.split(' ', 2);
          if (availableLib[0] === usedLib[0]) {
            if (availableLib[1] === usedLib[1]) {
              return done(); // Same version
            }

            // We have different versions
            var usedVer = new Version(usedLib[1]);
            var availableVer = new Version(availableLib[1]);
            if (usedVer.major > availableVer.major || (usedVer.major === availableVer.major && usedVer.minor >= availableVer.minor)) {
              return done({
                type: 'errorTooHighVersion',
                used: usedLib[0] + ' ' + usedVer,
                supported: availableLib[0] + ' ' + availableVer
              }); // Larger or same version that's available
            }

            // A newer version is available, upgrade params
            return self.upgrade(availableLib[0], usedVer, availableVer, params.params, params.metadata, function (err, upgradedParams, upgradedMetadata) {
              if (!err) {
                params.library = availableLib[0] + ' ' + availableVer.major + '.' + availableVer.minor;
                params.params = upgradedParams;
                if (upgradedMetadata) {
                  params.metadata = upgradedMetadata;
                }
              }
              done(err, params);
            });
          }
        }

        // Content type was not supporte by the higher version
        done({
          type: 'errorNotSupported',
          used: usedLib[0] + ' ' + usedVer
        });
        break;

      case 'group':
        if (field.fields.length === 1 && field.isSubContent !== true) {
          // Single field to process, wrapper will be skipped
          self.processField(field.fields[0], params, function (err, upgradedParams) {
            if (upgradedParams) {
              params = upgradedParams;
            }
            done(err, params);
          });
        }
        else {
          // Go through all fields in the group
          asyncSerial(field.fields, function (index, subField, next) {
            var paramsToProcess = params ? params[subField.name] : null;
            self.processField(subField, paramsToProcess, function (err, upgradedParams) {
              if (upgradedParams) {
                params[subField.name] = upgradedParams;
              }
              next(err);
            });

          }, function (err) {
            done(err, params);
          });
        }
        break;

      case 'list':
        // Go trough all params in the list
        asyncSerial(params, function (index, subParams, next) {
          self.processField(field.field, subParams, function (err, upgradedParams) {
            if (upgradedParams) {
              params[index] = upgradedParams;
            }
            next(err);
          });
        }, function (err) {
          done(err, params);
        });
        break;

      default:
        done();
    }
  };

  /**
   * Helps process each property on the given object asynchronously in serial order.
   *
   * @private
   * @param {Object} obj
   * @param {Function} process
   * @param {Function} finished
   */
  var asyncSerial = function (obj, process, finished) {
    var id, isArray = obj instanceof Array;

    // Keep track of each property that belongs to this object.
    if (!isArray) {
      var ids = [];
      for (id in obj) {
        if (obj.hasOwnProperty(id)) {
          ids.push(id);
        }
      }
    }

    var i = -1; // Keeps track of the current property

    /**
     * Private. Process the next property
     */
    var next = function () {
      id = isArray ? i : ids[i];
      process(id, obj[id], check);
    };

    /**
     * Private. Check if we're done or have an error.
     *
     * @param {String} err
     */
    var check = function (err) {
      // We need to use a real async function in order for the stack to clear.
      setTimeout(function () {
        i++;
        if (i === (isArray ? obj.length : ids.length) || (err !== undefined && err !== null)) {
          finished(err);
        }
        else {
          next();
        }
      }, 0);
    };

    check(); // Start
  };

  return ContentUpgradeProcess;
})(H5P.Version);
