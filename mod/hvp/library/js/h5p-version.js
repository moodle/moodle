H5P.Version = (function () {
  /**
   * Make it easy to keep track of version details.
   *
   * @class
   * @namespace H5P
   * @param {String} version
   */
  function Version(version) {
    var versionSplit = version.split('.', 3);

    // Public
    this.major =+ versionSplit[0];
    this.minor =+ versionSplit[1];

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
