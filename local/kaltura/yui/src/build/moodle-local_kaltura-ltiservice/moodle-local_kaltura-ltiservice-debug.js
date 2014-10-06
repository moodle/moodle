YUI.add('moodle-local_kaltura-ltiservice', function (Y, NAME) {

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
 * YUI module for displaying an LTI launch within a YUI panel.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

 /**
 * This method calls the base class constructor
 * @method LTISERVICE
 */
var LTISERVICE = function() {
    LTISERVICE.superclass.constructor.apply(this, arguments);
};

Y.extend(LTISERVICE, Y.Base, {
    /**
     * Init function for the checkboxselection module
     */
    init : function() {
        alert('ltiservice');

    }
},
{
    NAME : 'moodle-local_kaltura-ltiservice'
});
M.local_kaltura = M.local_kaltura || {};

/**
 * Entry point for ltiservice module
 * @param string params additional parameters.
 * @return object the ltiservice object
 */
M.local_kaltura.init = function(params) {
    return new LTISERVICE(params);
};

}, '@VERSION@', {"requires": ["base", "node"]});
