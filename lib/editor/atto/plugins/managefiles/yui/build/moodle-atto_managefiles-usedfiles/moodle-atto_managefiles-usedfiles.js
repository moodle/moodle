YUI.add('moodle-atto_managefiles-usedfiles', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Atto text editor managefiles usedfiles plugin.
 *
 * @package    atto_managefiles
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * CSS constants.
 *
 * @type {Object}
 */
var CSS = {
    HASMISSINGFILES: 'has-missing-files',
    HASUNUSEDFILES: 'has-unused-files'
};

/**
 * Selectors constants.
 *
 * @type {Object}
 */
var SELECTORS = {
    FILEANCESTOR: '.fitem_fcheckbox',
    FORM: '#atto_managefiles_manageform',
    MISSINGFILES: '.missing-files'
};

M.atto_managefiles = M.atto_managefiles || {};
M.atto_managefiles.usedfiles = M.atto_managefiles.usedfiles || {

    /**
     * Init function.
     *
     * @param {Object} allFiles The keys are the file names, the values are the hashes.
     * @return {Void}
     */
    init: function(allFiles) {
        var form = Y.one(SELECTORS.FORM),
            usedFiles,
            missingFilesTxt,
            i;

        if (!form || !window.parent
                || !window.parent.M.atto_managefiles) {
            return;
        }

        usedFiles = window.parent.M.atto_managefiles.getUsedFiles();
        unusedFiles = M.atto_managefiles.usedfiles.findUnusedFiles(allFiles, usedFiles);
        missingFiles = M.atto_managefiles.usedfiles.findMissingFiles(allFiles, usedFiles);

        // There are some unused files.
        if (unusedFiles.length > 0) {
            // Loop over all the files in the form.
            form.all('input[type=checkbox][name^="deletefile"]').each(function(node) {
                // If the file is used, remove it.
                if (Y.Array.indexOf(unusedFiles, node.getData('filename')) === -1) {
                    node.ancestor(SELECTORS.FILEANCESTOR).remove();
                }
            });
            form.addClass(CSS.HASUNUSEDFILES);
        } else {
            // This is needed as the init may be called twice due to the double call to $PAGE->requires->yui_module().
            form.removeClass(CSS.HASUNUSEDFILES);
        }

        // There are some files missing.
        if (missingFiles.length > 0) {
            missingFilesTxt = '<ul>';
            for (i = 0; i < missingFiles.length; i++) {
                missingFilesTxt += '<li>' + Y.Escape.html(missingFiles[i]) + '</li>';
            }
            missingFilesTxt += '</ul>';
            form.one(SELECTORS.MISSINGFILES).setHTML('').append(missingFilesTxt);
            form.addClass(CSS.HASMISSINGFILES);
        } else {
            form.removeClass(CSS.HASMISSINGFILES);
        }
    },

    /**
     * Return an array of unused files.
     *
     * @param {Object} allFiles Where the keys are the file names.
     * @param {Object} usedFiles Where the keys are the file names.
     * @return {Array} Of file names.
     */
    findUnusedFiles: function(allFiles, usedFiles) {
        var key,
            list = [];
        for (key in allFiles) {
            if (!usedFiles[key]) {
                list.push(key);
            }
        }
        return list;
    },

    /**
     * Return an array of missing files.
     *
     * @param {Object} allFiles Where the keys are the file names.
     * @param {Object} usedFiles Where the keys are the file names.
     * @return {Array} Of file names.
     */
    findMissingFiles: function(allFiles, usedFiles) {
        var key,
            list = [];
        for (key in usedFiles) {
            if (!allFiles[key]) {
                list.push(key);
            }
        }
        return list;
    }
};


}, '@VERSION@', {"requires": ["node", "escape"]});
