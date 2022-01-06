H5P.Version = (function () {
  /**
   * Make it easy to keep track of version details.
   *
   * @class
   * @namespace H5P
   * @param {String} version
   */
  function Version(version) {

    if (typeof version === 'string') {
      // Name version string (used by content upgrade)
      var versionSplit = version.split('.', 3);
      this.major =+ versionSplit[0];
      this.minor =+ versionSplit[1];
    }
    else {
      // Library objects (used by editor)
      if (version.localMajorVersion !== undefined) {
        this.major =+ version.localMajorVersion;
        this.minor =+ version.localMinorVersion;
      }
      else {
        this.major =+ version.majorVersion;
        this.minor =+ version.minorVersion;
      }
    }

    /**
     * Public. Custom string for this object.
     *
     * @returns {String}
     */
    this.toString = function () {
      return version;
    };
  }

  return Version;
})();
