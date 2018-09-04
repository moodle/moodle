/** @namespace H5PEditor */
var H5PEditor = H5PEditor || {};

/**
 * The library list cache
 *
 * @type Object
 */
var llc = H5PEditor.LibraryListCache = {
  libraryCache: {},
  librariesComingIn: {},
  librariesMissing: {},
  que: []
};

/**
 * Get data for a list of libraries
 *
 * @param {Array} libraries - list of libraries to load info for (uber names)
 * @param {Function} handler - Callback when list of libraries is loaded
 * @param {Function} thisArg - Context for the callback function
 */
llc.getLibraries = function(libraries, handler, thisArg) {
  var cachedLibraries = [];
  var status = 'hasAll';
  for (var i = 0; i < libraries.length; i++) {
    if (libraries[i] in llc.libraryCache) {
      // Libraries that are missing on the server are set to null...
      if (llc.libraryCache[libraries[i]] !== null) {
        cachedLibraries.push(llc.libraryCache[libraries[i]]);
      }
    }
    else if (libraries[i] in llc.librariesComingIn) {
      if (status === 'hasAll') {
        status = 'onTheWay';
      }
    }
    else {
      status = 'requestThem';
      llc.librariesComingIn[libraries[i]] = true;
    }
  }
  switch (status) {
    case 'hasAll':
      handler.call(thisArg, cachedLibraries);
      break;
  case 'onTheWay':
    llc.que.push({libraries: libraries, handler: handler, thisArg: thisArg});
    break;
  case 'requestThem':
    var ajaxParams = {
      type: "POST",
      url: H5PEditor.getAjaxUrl('libraries'),
      success: function(data) {
        llc.setLibraries(data, libraries);
        handler.call(thisArg, data);
        llc.runQue();
      },
      data: {
        'libraries': libraries
      },
      dataType: "json"
    };
    H5PEditor.$.ajax(ajaxParams);
    break;
  }
};

/**
 * Call all qued handlers
 */
llc.runQue = function() {
  var l = llc.que.length;
  for (var i = 0; i < l; i++) {
    var handlerObject = llc.que.shift();
    llc.getLibraries(handlerObject.libraries, handlerObject.handler, handlerObject.thisArg);
  }
};

/**
 * We've got new libraries from the server, save them
 *
 * @param {Array} libraries - Libraries with info from server
 * @param {Array} requestedLibraries - List of what libraries we requested
 */
llc.setLibraries = function(libraries, requestedLibraries) {
  var reqLibraries = requestedLibraries.slice();
  for (var i = 0; i < libraries.length; i++) {
    llc.libraryCache[libraries[i].uberName] = libraries[i];
    if (libraries[i].uberName in llc.librariesComingIn) {
      delete llc.librariesComingIn[libraries[i].uberName];
    }
    var index = reqLibraries.indexOf(libraries[i].uberName);
    if (index > -1) {
      reqLibraries.splice(index, 1);
    }
  }
  for (var i = 0; i < reqLibraries.length; i++) {
    llc.libraryCache[reqLibraries[i]] = null;
    if (reqLibraries[i] in llc.librariesComingIn) {
      delete llc.librariesComingIn[libraries[i]];
    }
  }
};
