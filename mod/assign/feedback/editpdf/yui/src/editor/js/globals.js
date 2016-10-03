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
/* eslint-disable no-unused-vars */

/**
 * A list of globals used by this module.
 *
 * @module moodle-assignfeedback_editpdf-editor
 */
var AJAXBASE = M.cfg.wwwroot + '/mod/assign/feedback/editpdf/ajax.php',
    AJAXBASEPROGRESS = M.cfg.wwwroot + '/mod/assign/feedback/editpdf/ajax_progress.php',
    CSS = {
        DIALOGUE : 'assignfeedback_editpdf_widget'
    },
    SELECTOR = {
        PREVIOUSBUTTON :  '.navigate-previous-button',
        NEXTBUTTON :  ' .navigate-next-button',
        SEARCHCOMMENTSBUTTON : '.searchcommentsbutton',
        SEARCHFILTER : '.assignfeedback_editpdf_commentsearch input',
        SEARCHCOMMENTSLIST : '.assignfeedback_editpdf_commentsearch ul',
        PAGESELECT : '.navigate-page-select',
        LOADINGICON : '.loading',
        PROGRESSBARCONTAINER : '.progress-info.progress-striped',
        DRAWINGREGION : '.drawingregion',
        DRAWINGCANVAS : '.drawingcanvas',
        SAVE : '.savebutton',
        COMMENTCOLOURBUTTON : '.commentcolourbutton',
        COMMENTMENU : '.commentdrawable a',
        ANNOTATIONCOLOURBUTTON :  '.annotationcolourbutton',
        DELETEANNOTATIONBUTTON : '.deleteannotationbutton',
        UNSAVEDCHANGESDIV : '.assignfeedback_editpdf_unsavedchanges',
        STAMPSBUTTON : '.currentstampbutton',
        DIALOGUE : '.' + CSS.DIALOGUE
    },
    SELECTEDBORDERCOLOUR = 'rgba(200, 200, 255, 0.9)',
    SELECTEDFILLCOLOUR = 'rgba(200, 200, 255, 0.5)',
    COMMENTTEXTCOLOUR = 'rgb(51, 51, 51)',
    COMMENTCOLOUR = {
        'white' : 'rgb(255,255,255)',
        'yellow' : 'rgb(255,236,174)',
        'red' : 'rgb(249,181,179)',
        'green' : 'rgb(214,234,178)',
        'blue' : 'rgb(203,217,237)',
        'clear' : 'rgba(255,255,255, 0)'
    },
    ANNOTATIONCOLOUR = {
        'white' : 'rgb(255,255,255)',
        'yellow' : 'rgb(255,207,53)',
        'red' : 'rgb(239,69,64)',
        'green' : 'rgb(152,202,62)',
        'blue' : 'rgb(125,159,211)',
        'black' : 'rgb(51,51,51)'
    },
    CLICKTIMEOUT = 300,
    TOOLSELECTOR = {
        'comment': '.commentbutton',
        'pen': '.penbutton',
        'line': '.linebutton',
        'rectangle': '.rectanglebutton',
        'oval': '.ovalbutton',
        'stamp': '.stampbutton',
        'select': '.selectbutton',
        'highlight': '.highlightbutton'
    },
    STROKEWEIGHT = 4;
