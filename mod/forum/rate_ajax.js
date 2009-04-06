// $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// Javascript used to handle AJAX forum ratings

/**
 * This function initializes all the stuff needed to have forum ratings
 * working under AJAX. Basically it adds one onload listener that triggers
 * the add_menu_listeners() function to add menu listeners
 */
function init_rate_ajax () {
    YAHOO.util.Event.onDOMReady(add_menu_listeners);
}

/**
 * This function adds event listeners to any rating
 * menu found in he page (class = forumpostratingmenu)
 * and prevents manual submission
 */
function add_menu_listeners(e) {

    /** hide the submit button */
    var submitbutton = YAHOO.util.Dom.get('forumpostratingsubmit');
    submitbutton.style.display = 'none';

    /** prevent form submission **/
    var form = YAHOO.util.Dom.get('form');
    YAHOO.util.Event.addListener(form, 'submit', prevent_form_submission);

    /** add listeners to all rating menus */
    var menus = YAHOO.util.Dom.getElementsByClassName('forumpostratingmenu', 'select');
    for (var i=0; i<menus.length; i++) {
        var menu = menus[i];
        YAHOO.util.Event.addListener(menu, 'change', perform_rate, menu);
    }
}

/**
 * This function prevents manual form submission, to avoid
 * rate form to be submitted completely
 */
function prevent_form_submission(e) {

    /** stop submission completely **/
    YAHOO.util.Event.stopEvent(e);
}

/**
 * This function performs the communication with the server
 * in order to send new rates and receive feedback about that.
 * It is the action thrown by all the menu listeners defined above
 */
function perform_rate(e, menu) {

    /** define response behaviour **/
    var callback = {
        success: rate_success,
        failure: rate_failure,
        args:menu
    };

    /** Here goes the request **/
    var url = rate_ajax_config.wwwroot +  '/mod/forum/rate_ajax.php?postid=' + menu.name + '&rate=' + menu.value + '&sesskey=' + rate_ajax_config.sesskey;
    YAHOO.util.Connect.asyncRequest('GET', url, callback, null);

    /** Start animation **/
    var animatedElement   = YAHOO.util.Dom.getAncestorByTagName(menu, 'div');
    animatedElement.style.background = "url('" + rate_ajax_config.pixpath + "/i/loading_small.gif') no-repeat top right";

}

/**
 * Code to execute when we receive a success in the AJAX response
 * It should print updated info about ratings (replacing previous one)
 */
function rate_success(o) {
    menu = this.args;

    /** Stop animation **/
    var animatedElement   = YAHOO.util.Dom.getAncestorByTagName(menu, 'div');
    animatedElement.style.background = '';

    /** Parse json response **/
    var response = new Object();
    try {
        response = YAHOO.lang.JSON.parse(o.responseText);
    } catch (e) { /** Fake response **/
        response.status = 'Error';
        response.message= '';
    }

    /** Process error response **/
    if (response.status != 'Ok') {
        display_error(menu, response);
    } else {
        display_response(menu, response);
    }

    /** That's all, really simple **/
}

/**
 * Code to execute when we receive a failure in the AJAX response
 * It should print some error message
 */
function rate_failure(o) {
    menu = this.args;

    /** Stop animation **/
    var animatedElement   = YAHOO.util.Dom.getAncestorByTagName(menu, 'div');
    animatedElement.style.background = '';

    /** Process error response **/
    display_error(menu);
}

/**
 * This function will display the correct response received from server
 */
function display_response(menu, response) {

    /** Correct response, revert menu color if neeeded **/
    if (menu.style.backgroundColor == 'red') {
        menu.style.backgroundColor = null;
    }

    /** Process ok response, displaying it **/
    var ratingsDiv  = YAHOO.util.Dom.getAncestorByTagName(menu, 'div');
    var ratingsSpan = YAHOO.util.Dom.getFirstChildBy(ratingsDiv, function(el){return YAHOO.util.Dom.hasClass(el,'forumpostratingtext');});

    /** span doesn't exist (first rate), add it, shouldn't happen ever but... fallback**/
    if (!ratingsSpan) {
        ratingsSpan = document.createElement('span');
        YAHOO.util.Dom.addClass(ratingsSpan, 'forumpostratingtext');
        ratingsDiv.appendChild(ratingsSpan);
    }

    /** finally replace span HTML **/
    ratingsSpan.innerHTML = response.message ? response.message : ''; /** Prevent null to be printed in IE7 **/
}

/**
 * This function will perform the desired actions to inform
 * about the error response of ajax request
 */
function display_error(menu, response) {
    /** Set red background color in menu - silly error measure **/
    menu.style.backgroundColor = 'red';
}

