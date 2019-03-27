var currentlyloaded = [];

/**
 * Loads the data for the clicked navigation item.
 *
 * @param  {Object} clickednode The jquery object for the clicked node.
 */
function handleClick(clickednode) {
    var contextcrumb = '';
    var parentnodes = clickednode.parents('li');
    for (var i = parentnodes.length; i >= 0; i--) {
        var treenodes = window.$(parentnodes[i]);
        if (treenodes.hasClass('item')) {
            if (contextcrumb == '') {
                contextcrumb = treenodes[0].innerText;
            } else {
                contextcrumb = contextcrumb + ' | ' + treenodes[0].innerText;
            }
        } else if (treenodes.hasClass('menu-item')) {
            if (contextcrumb == '') {
                contextcrumb = treenodes[0].firstChild.textContent;
            } else {
                contextcrumb = contextcrumb + ' | ' + treenodes[0].firstChild.textContent;
            }
        }
    }
    var datafile = clickednode.attr('data-var');
    loadContent(datafile, function() {
        addFileDataToMainArea(window[datafile], contextcrumb);
    });
}

/**
 * Load content to be displayed.
 *
 * @param  {String}   datafile The json data to be displayed.
 * @param  {Function} callback The function to run after loading the json file.
 */
function loadContent(datafile, callback) {

    // Check to see if this file has already been loaded. If so just go straight to the callback.
    if (fileIsLoaded(datafile)) {
        callback();
        return;
    }

    // This (user_data_index) is defined in data_index.js
    var data = window.user_data_index[datafile];
    var newscript = document.createElement('script');

    if (newscript.readyState) {
        newscript.onreadystatechange = function() {
            if (this.readyState == 'complete' || this.readyState == 'loaded') {
                this.onreadystatechange = null;
                callback();
            }
        };
    } else {
        newscript.onload = function() {
            callback();
        };
    }

    newscript.type = 'text/javascript';
    newscript.src = encodeURIComponent(data);
    newscript.charset = 'utf-8';
    document.getElementsByTagName("head")[0].appendChild(newscript);

    // Keep track that this file has already been loaded.
    currentlyloaded.push(datafile);
}

/**
 * Checks to see if the datafile has already been loaded onto the page or not.
 *
 * @param  {String} datafile The file entry we are checking to see if it is already loaded.
 * @return {Boolean} True if already loaded otherwise false.
 */
function fileIsLoaded(datafile) {
    for (var index in currentlyloaded) {
        if (currentlyloaded[index] == datafile) {
            return true;
        }
    }
    return false;
}

/**
 * Adds the loaded data to the main content area of the page.
 *
 * @param {Object} data  Data to be added to the main content area of the page.
 * @param {String} title Title for the content area.
 */
function addFileDataToMainArea(data, title) {
    var dataarea = window.$('[data-main-content]');
    while (dataarea[0].firstChild) {
        dataarea[0].removeChild(dataarea[0].firstChild);
    }
    var htmldata = makeList(data);

    var areatitle = document.createElement('h2');
    areatitle.innerHTML = title;
    dataarea[0].appendChild(areatitle);

    var maincontentlist = document.createElement('div');
    maincontentlist.innerHTML = htmldata;
    dataarea[0].appendChild(maincontentlist.firstChild);
}

/**
 * Creates an unordered list with the json data provided.
 *
 * @param  {Object} jsondata The json data to turn into an unordered list.
 * @return {String} The html string of the unordered list.
 */
function makeList(jsondata) {
    var html = '<ul>';
    for (var key in jsondata) {
        html += '<li>';
        if (typeof jsondata[key] == 'object') {
            html += key;
            html += makeList(jsondata[key]);
        } else {
            html += key + ': ' + jsondata[key];
        }
        html += '</li>';
    }
    html += '</ul>';
    return html;
}

window.$(document).ready(function() {
    window.$('[data-var]').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        handleClick(window.$(this));
    });
});
