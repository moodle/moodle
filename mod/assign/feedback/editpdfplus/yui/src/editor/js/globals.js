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
 * A list of globals used by this module.
 *
 * @module moodle-assignfeedback_editpdfplus-editor
 */
var AJAXBASE = M.cfg.wwwroot + '/mod/assign/feedback/editpdfplus/ajax.php',
        AJAXBASEPROGRESS = M.cfg.wwwroot + '/mod/assign/feedback/editpdfplus/ajax_progress.php',
        CSS = {
            DIALOGUE: 'assignfeedback_editpdfplus_widget'
        },
        SELECTOR = {
            PREVIOUSBUTTON: '.navigate-previous-button',
            NEXTBUTTON: ' .navigate-next-button',
            PAGESELECT: '.navigate-page-select',
            LOADINGICON: '.loading',
            PROGRESSBARCONTAINER: '.progress-info.progress-striped',
            DRAWINGREGION: '.drawingregion',
            DRAWINGREGIONCLASS: 'drawingregion',
            DRAWINGCANVAS: '.drawingcanvas',
            DRAWINGTOOLBAR: 'drawingtoolbar',
            SAVE: '.savebutton',
            ANNOTATIONCOLOURBUTTON: '.annotationcolourbutton',
            DELETEANNOTATIONBUTTON: '.deleteannotationbutton',
            WARNINGMESSAGECONTAINER: '.warningmessages',
            ICONMESSAGECONTAINER: '.assignfeedback_editpdfplus_infoicon',
            UNSAVEDCHANGESDIV: '.assignfeedback_editpdf_warningmessages',
            UNSAVEDCHANGESINPUT: 'input[name="assignfeedback_editpdfplus_haschanges"]',
            UNSAVEDCHANGESDIVEDIT: '.assignfeedback_editpdfplus_unsavedchanges_edit',
            HELPMESSAGETITLE: '#afppHelpmessageTitle',
            HELPMESSAGE: '#afppHelpmessageBody',
            USERINFOREGION: '[data-region="user-info"]',
            ROTATELEFTBUTTON: '.rotateleftbutton',
            ROTATERIGHTBUTTON: '.rotaterightbutton',
            DIALOGUE: '.' + CSS.DIALOGUE,
            CUSTOMTOOLBARID: '#toolbaraxis',
            CUSTOMTOOLBARS: '.customtoolbar',
            AXISCUSTOMTOOLBAR: '.menuaxisselection',
            CUSTOMTOOLBARBUTTONS: '.costumtoolbarbutton',
            GENERICTOOLBARBUTTONS: '.generictoolbarbutton',
            HELPBTNCLASS: '.helpmessage',
            STATUTSELECTOR: '#menustatutselection',
            QUESTIONSELECTOR: '#menuquestionselection',
            STUDENTVALIDATION: '#student_valide_button'
        },
        SELECTEDBORDERCOLOUR = 'rgba(200, 200, 255, 0.9)',
        SELECTEDFILLCOLOUR = 'rgba(200, 200, 255, 0.5)',
        ANNOTATIONCOLOUR = {
            'white': 'rgb(255,255,255)',
            'yellowlemon': 'rgb(255,255,0)',
            'yellow': 'rgb(255,207,53)',
            'red': 'rgb(239,69,64)',
            'green': 'rgb(152,202,62)',
            //'blue': 'rgb(125,159,211)',
            'blue': 'rgb(0,0,255)',
            'black': 'rgb(51,51,51)'
        },
        CLICKTIMEOUT = 300,
        TOOLSELECTOR = {
            'select': '.selectbutton',
            'drag': '.dragbutton',
            'resize': '.resizebutton'
        },
        TOOLTYPE = {
            'HIGHLIGHTPLUS': 1,
            'LINEPLUS': 2,
            'STAMPPLUS': 3,
            'FRAME': 4,
            'VERTICALLINE': 5,
            'STAMPCOMMENT': 6,
            'COMMENTPLUS': 7,
            'PEN': 8,
            'LINE': 9,
            'RECTANGLE': 10,
            'OVAL': 11,
            'HIGHLIGHT': 12
        },
        TOOLTYPELIB = {
            'HIGHLIGHTPLUS': 'highlightplus',
            'LINEPLUS': 'lineplus',
            'STAMPPLUS': 'stampplus',
            'FRAME': 'frame',
            'VERTICALLINE': 'verticalline',
            'STAMPCOMMENT': 'stampcomment',
            'COMMENTPLUS': 'commentplus',
            'PEN': 'pen',
            'LINE': 'line',
            'RECTANGLE': 'rectangle',
            'OVAL': 'oval',
            'HIGHLIGHT': 'highlight'
        },
        STROKEWEIGHT = 2;