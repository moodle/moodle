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
 * JavaScript objects, functions as well as usage of some YUI library for
 * enabling drag and drop interaction for dran-anddrop words into sentences
 * (ddimagetoimage)
 *
 * @package    qtype
 * @subpackage ddimagetoimage
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//global variables
var ddimagetoimage_currentzindex = 10;


/*
 * The way it seems to be, if there are more than one of this type of question
 * on a page, then this file is shared between them. Therefore it has to cope
 * with ALL the questions of this type on the page.
 */
(function() {

    // start of App object by all questions of this type on a page.
    YAHOO.example.DDApp = {
        init : function() {
            var questionspans = YAHOO.util.Dom.getElementsByClassName("ddimagetoimage_questionid_for_javascript");

            // we need this loop in case of more than one of this qtype on one page
            for (var i = 0; i < questionspans.length; i++) {
                // The Questions object should now contain a QuestionDataObject
                // object for each question of this type on the page.
                Questions[questionspans[i].id] = new QuestionDataObject(questionspans[i].id);
            }

            // populate the arrays "slots" and "players" for each question
            var tempSlots = YAHOO.util.Dom.getElementsByClassName("slot", "span");
            var tempPlayers = YAHOO.util.Dom.getElementsByClassName("player", "span");

            //ie7 zoom message
            ie7_zoom_message();

            for (var i = 0; i < tempSlots.length; i++) {
                var name_prefix = tempSlots[i].id.split("_")[0] + "_";
                var q = Questions[name_prefix];
                var g = getGroupForThis(tempSlots[i].id);

                var ddtarget = new YAHOO.util.DDTarget(tempSlots[i].id, g);
                q.tempSlots.push(tempSlots[i]);
                q.slots.push(ddtarget);
            }

            for (var i = 0; i < tempPlayers.length; i++) {
                var name_prefix = tempPlayers[i].id.split("_")[0] + "_";
                var q = Questions[name_prefix];
                var g = getGroupForThis(tempPlayers[i].id);
                var ddplayer = new YAHOO.example.DDPlayer(tempPlayers[i].id, g);
                q.tempPlayers.push(tempPlayers[i]);
                q.players.push(ddplayer);
            }

            for (var i = 0; i < questionspans.length; i++) {
                var q = Questions[questionspans[i].id];
                var groupwidth = getWidthForAllGroups(q.tempPlayers);
                for (var j = 0; j < q.tempSlots.length; j++) {
                    var g = getGroupForThis(q.tempSlots[j].id);
                    setWidth(q.tempSlots[j], groupwidth[g]);
                }
                for (var j = 0; j < q.tempPlayers.length; j++) {
                    var g = getGroupForThis(q.tempPlayers[j].id);
                    setWidth(q.tempPlayers[j], groupwidth[g]);
                }

                // set responses for all slots
                setResponsesForAllSlots(q.tempSlots, q.tempPlayers);
            }
        }
    };
    // end of App object ////////////////////////////////////////////////////////

    // beginning of Player object (the draggable item) //////////////////////////
    YAHOO.example.DDPlayer = function(id, sGroup, config) {
        YAHOO.example.DDPlayer.superclass.constructor.apply(this, arguments);
        this.initPlayer(id, sGroup, config);
    };

    YAHOO.extend(YAHOO.example.DDPlayer, YAHOO.util.DD, {
        TYPE :"DDPlayer",

        initPlayer : function(id, sGroup, config) {
            this.isTarget = false;
            this.currentPos = YAHOO.util.Dom.getXY(this.getEl());
        },

        //Abstract method called after a drag/drop object is clicked and the drag or mousedown time thresholds have beeen met.
        startDrag : function(x, y) {
            YAHOO.util.Dom.setStyle(this.getEl(), "zIndex", ddimagetoimage_currentzindex++);
            YAHOO.util.Dom.removeClass(this.getEl(), 'placed');

            if (is_infinite(this.getEl()) && !this.slot){
                var currentplayer = this.getEl().id.replace(/_clone[0-9]+$/, '');
                var ddplayer = YAHOO.util.DragDropMgr.getDDById(currentplayer);
                clone_player(ddplayer);
            }

            if (this.slot) { // dragging starts from a slot
                var hiddenElement = document.getElementById(this.slot.getEl().id + '_hidden');
                hiddenElement.value = '';
                this.slot.player = null;
                this.slot = null;
            }
        },

        //Abstract method called when this item is dropped on another DragDrop obj
        onDragDrop : function(e, id) {
            // get the drag and drop object that was targeted
            var target = YAHOO.util.DragDropMgr.getDDById(id);
            var dragged = this.getEl();

            //get the question-prefix of slot and player and check whether they belong to the same question
            var slotprefix = target.id.split("_")[0] + "_";
            var playerprefix = dragged.id.split("_")[0] + "_";
            if (slotprefix != playerprefix){
                var p = YAHOO.util.DragDropMgr.getDDById(dragged.id);
                p.startDrag(0,0);
                p.onInvalidDrop(null);
                return;
            }

            show_element(this.getEl());

            if (target.player) { // there's a player already there
                var oldplayer = target.player;
                oldplayer.startDrag(0,0);
                oldplayer.onInvalidDrop(null);
            }

            YAHOO.util.DragDropMgr.moveToEl(dragged, target.getEl());
            this.slot = target;
            target.player = this;
            YAHOO.util.Dom.setXY(target.player.getEl(), YAHOO.util.Dom.getXY(target.getEl()));
            YAHOO.util.Dom.addClass(this.getEl(), 'placed');
            if (YAHOO.util.Dom.hasClass(target.getEl(), 'readonly')) {
                if (YAHOO.util.Dom.hasClass(target.getEl(), 'correct')) {
                    YAHOO.util.Dom.addClass(this.getEl(), 'correct');
                } else if (YAHOO.util.Dom.hasClass(target.getEl(), 'incorrect')) {
                    YAHOO.util.Dom.addClass(this.getEl(), 'incorrect');
                }
            }

            // set value
            var hiddenElement = document.getElementById(id + '_hidden');
            hiddenElement.value = this.getEl().id.split("_")[1];
        },

        //Abstract method called when this item is dropped on an area with no drop target
        onInvalidDrop : function(e) {
            YAHOO.util.Dom.setXY(this.getEl(), YAHOO.util.Dom.getXY(this.originalplayer.getEl()));
            YAHOO.util.Dom.removeClass(this.getEl(), 'placed');
        }
    });
    // end of Player object (the draggable item) ////////////////////////////////

    YAHOO.util.Event.onDOMReady(YAHOO.example.DDApp.init, YAHOO.example.DDApp, true);

    // Objects///////////////////////////////////////////////////////////////////
    var Questions = new Object();

    function QuestionDataObject(theid) {
        this.id = theid;
        this.tempSlots = [];
        this.tempPlayers = [];
        this.slots = [];
        this.players = [];
    }

    // template for the slot object
    function SlotObject(id, group, currentvalue, values, callback) {
        this.id = id;
        this.group = group;
        this.currentvalue = currentvalue;
        this.values = values;
        this.callback = callback;
    }
    // End of Objects ///////////////////////////////////////////////////////////

    // functions ////////////////////////////////////////////////////////////////

    function clone_player(p) {
        var el = p.getEl();
        var newNode = document.createElement('div');
        newNode.className = el.className;
        newNode.innerHTML = el.innerHTML;

        if (!p.clones) {
            p.clones = [];
        }
        newNode.id = el.id + '_clone' + p.clones.length; // _clone0 for first

        newNode.style.position = 'absolute';

        var region = YAHOO.util.Dom.getRegion(el);
        var height = region.bottom - region.top;
        var width = region.right - region.left;

        // -2 is becuase get_region includes the border, but style.width/height does not.
        newNode.style.height = (height - 2) + "px";
        newNode.style.width = (width - 2) + "px";

        el.parentNode.parentNode.appendChild(newNode);
        YAHOO.util.Dom.setXY(newNode, YAHOO.util.Dom.getXY(el));

        var g = getGroupForThis(el.id);
        var p2 = new YAHOO.example.DDPlayer(newNode.id, g);
        p2.originalplayer = p;

        hide_element(el);
        show_element(newNode);

        if (is_readonly(newNode)) {
            p2.lock();
        }

        p.clones[p.clones.length] = p2;
        return p2;
    }

    function clone_players(players){
        for (var i = 0; i < players.length; i++) {
            var p = YAHOO.util.DragDropMgr.getDDById(players[i].id);
            clone_player(p);
        }
    }

    function is_readonly(el){
        return YAHOO.util.Dom.hasClass(el, 'readonly');
    }

    function is_infinite(el){
        return YAHOO.util.Dom.hasClass(el, 'infinite');
    }

    function show_element(el){
        YAHOO.util.Dom.setStyle(el, 'visibility', 'visible');
    }

    function hide_element(el){
        YAHOO.util.Dom.setStyle(el, 'visibility', 'hidden');
    }

    function list_of_slots_and_players(slots, players) {
        this.slots = slots;
        this.players = players;
    }

    function set_xy_after_resize(e, slotsandplayerobj){
        setTimeout(function() {
            set_xy_after_resize_actual(e,slotsandplayerobj);
        }, 0);
    }

    function set_xy_after_resize_actual(e, slotsandplayerobj) {
        var slots = slotsandplayerobj.slots;
        var players = slotsandplayerobj.players;
        for (var i = 0; i < players.length; i++) {
            var original = YAHOO.util.DragDropMgr.getDDById(players[i].id);
            for (var index in original.clones) {
                var c = original.clones[index];
                if (c.slot) {
                    //player is in slot
                    YAHOO.util.Dom.setXY(c.getEl(), YAHOO.util.Dom.getXY(c.slot.getEl()));
                } else {
                    //player is not in slot
                    YAHOO.util.Dom.setXY(c.getEl(), YAHOO.util.Dom.getXY(c.originalplayer.getEl()));
                }
            }
        }
    }

    function setResponsesForAllSlots(slots, players) {
        clone_players(players);

        for (var i = 0; i < slots.length; i++) {
            var slot = slots[i];

            var hiddenElement = document.getElementById(slot.id + '_hidden');
            if (!hiddenElement) {
                continue;
            }

            // get group
            var group = getGroupForThis(slot.id);

            // get array of values
            var values = getValuesForThisSlot(slot.id, players);

            var currentvalue = parseInt(hiddenElement.value);
            // if slot is occupied
            if (currentvalue) {
                // Find player
                var idbits = hiddenElement.id.split('_');
                idbits[1] = currentvalue;
                idbits.pop();
                var newid = idbits.join('_');
                var original = YAHOO.util.DragDropMgr.getDDById(newid);
                var index = original.clones.length - 1;
                original.clones[index].startDrag(0, 0);
                original.clones[index].onDragDrop(null, slot.id);
            }
            YAHOO.util.Event.addListener(slot.id, "focus", setFocus);
            YAHOO.util.Event.addListener(slot.id, "blur", setBlur);

            YAHOO.util.Event.addListener(slot.id, "mousedown", mouseDown);

            var myobj = new SlotObject(slot.id, group, currentvalue, values, funCallKeys);

            // event keydown
            YAHOO.util.Event.addListener(slot.id, "keydown", funCallKeys, myobj);
        }

        //resize
        var listofslotsandplayers = new list_of_slots_and_players(slots, players);
        YAHOO.util.Event.addListener(window, "resize", set_xy_after_resize, listofslotsandplayers);
        YAHOO.util.Event.addListener(window, "load", set_xy_after_resize, listofslotsandplayers);
    }

    function getValuesForThisSlot(slotid, players) {
        var gslot = getGroupForThis(slotid);
        var values = [];
        var j = 0;
        for (var i = 0; i < players.length; i++) {
            var pElement = players[i];
            var gplayer = getGroupForThis(pElement.id);

            // from the same group
            if (gslot == gplayer) {
                values[j++] = pElement.id;
            }
        }
        return values;
    }

    function getWidthForAllGroups(allplayers) {
        var widtharray = [];
        for (var i = 0; i < allplayers.length; i++) {
            var g = getGroupForThis(allplayers[i].id);
            var width = getWidthForThisElement(allplayers[i]);
            if (!widtharray[g] || width > widtharray[g]) {
                widtharray[g] = width;
            }
        }
        return widtharray;
    }

    function getWidthForThisGroup(allplayers, group) {
        var tempwidth = 0;
        for (var i = 0; i < allplayers.length; i++) {
            var g = getGroupForThis(allplayers[i].id);
            if (group != g) {
                continue;
            }
            var width = getWidthForThisElement(allplayers[i]);
            if (width > tempwidth) {
                tempwidth = width;
            }
        }
        return tempwidth;
    }

    function getWidthForThisElement(el) {
        var region = YAHOO.util.Dom.getRegion(el);
        return region.right - region.left;
    }

    function setWidth(el, gwidth) {
        var width = getWidthForThisElement(el);
        var remainder = (gwidth - width) + 10;

        // IE8 does not rewrap lines when the padding changes, so this
        // change uses different layout for that browser version only.
        if (navigator.appVersion.indexOf('MSIE 8') != -1) {
            var region = YAHOO.util.Dom.getRegion(el);
            var height = region.bottom - region.top;
            el.style.display = 'inline-block';
            el.style.width = gwidth + 'px';
            el.style.height = height + 'px';
            return;
        }

        YAHOO.util.Dom.setStyle(el, 'padding-right', Math.floor((remainder + 1) / 2) + 'px');
        YAHOO.util.Dom.setStyle(el, 'padding-left', Math.floor(remainder / 2) + 'px');
    }

    function funCallKeys(e, slotobj) {
        //disable the key access when readonly
        if (is_readonly(document.getElementById(slotobj.values[0]))) {
            return;
        }

        var evt = e || window.event;
        var key = evt.keyCode;

        switch (key) {
        case 39: // arrow right (forwards)
        case 40: // arrow down (forwards)
        case 32: // space (forwards)
            changeObject(slotobj, 1);
            return false; //this has to return false because of IE

        case 37: // arrow left (backwards)
        case 38: // arrow up (backwards)
            changeObject(slotobj, -1);
            return false; //this has to return false because of IE

        case 66: // B (backwards)
        case 98: // b (backwards)
        case 80: // P (previous)
        case 112: // p (previous)
            changeObject(slotobj, -1);
            break;

        case 13: // cariage return (forwards)
        case 70: // F (forwards)
        case 102: // f (forwards)
        case 78: // N (next)
        case 110: // n (next)
            changeObject(slotobj, 1);
            break;

        case 27: // escape (empty the drop box)
            changeObject(slotobj, 0);
        default:
            return true;
        }
        return true;
    }

    function changeObject(slotobj, direction) {
        // Prevent infinite loop if there are no values for this slot
        if (slotobj.values.length == 0) {
            return;
        }

        if (direction == 0) {
            call_YUI_startDrag_onInvalidDrop(slotobj);
            return;
        }

        var hiddenElement = document.getElementById(slotobj.id + '_hidden');

        // Get current position in values list
        var selectedIndex = -1;
        for(var i = 0; i < slotobj.values.length; i++) {
            if (slotobj.values[i].split("_")[1] == hiddenElement.value) {
                selectedIndex = i;
                break;
            }
        }

        var currentIndex = selectedIndex;
        while (true) {
            // Get new position in values list
            selectedIndex += direction;
            if (selectedIndex > slotobj.values.length) {
                selectedIndex-= slotobj.values.length + 1;
            }
            if (selectedIndex < 0) {
                selectedIndex += slotobj.values.length + 1;
            }

            //empty the slot at the beginning or the end of the players list
            if (selectedIndex == slotobj.values.length) {
                call_YUI_startDrag_onInvalidDrop(slotobj);
                return;
            }

            // If we loop round back to the current one then there are
            // no more options, so stop
            if (selectedIndex == currentIndex) {
                break;
            }

            // Check the item at the new position is not used
            var original = YAHOO.util.DragDropMgr.getDDById(slotobj.values[selectedIndex]);
            var index = original.clones.length - 1;
            if (!original.clones[index].slot) {
                // This one is not in a slot so we can use it
                original.clones[index].startDrag(0, 0);
                original.clones[index].onDragDrop(null, slotobj.id);
                break;
            }
        }
    }

    function call_YUI_startDrag_onInvalidDrop(slotobj){
        var target = YAHOO.util.DragDropMgr.getDDById(slotobj.id);
        if (!target.player) {
            return;
        }
        var player = target.player;

        // Find player and call YUI methods
        player.startDrag(0, 0);
        player.onInvalidDrop(null);
    }

    function getGroupForThis(str) {
        var g = str.split("_")[2];
        return g;
    }

    function mouseDown() {
    }

    function setFocus() {
        YAHOO.util.Dom.addClass(this, 'focussed');
    }

    function setBlur() {
        YAHOO.util.Dom.removeClass(this, 'focussed');
    }

    function ie7_zoom_message (){
        var browser = navigator.appVersion;
        if (browser.indexOf('MSIE 7') > -1){
            var b = document.body.getBoundingClientRect();
            var magnifactor = (b.right - b.left)/document.body.clientWidth;
            if (magnifactor < 0.9  || magnifactor > 1.1){

                var answers = YAHOO.util.Dom.getElementsByClassName("answercontainer", "div");
                for(var i=0; i<answers.length; i++) {
                    var block = document.createElement('div');

                    var text = 'This question type is not compatible with the zoom feature in Internet Explorer 7. '+
                                'Please press Ctrl+0 and then click on the question number in the navigation panel '+
                                'on the left to reload this question. Alternatively ';
                    block.appendChild(document.createTextNode(text));

                    //add ie8 link
                    var ie8link = document.createElement('a');
                    ie8link.href = 'http://www.microsoft.com/uk/windows/internet-explorer';
                    ie8link.appendChild(document.createTextNode('upgrade your browser to Internet Explorer 8'));
                    block.appendChild(ie8link);

                    var ie8textsuffix = ' before carrying on.';
                    block.appendChild(document.createTextNode(ie8textsuffix));

                    YAHOO.util.Dom.setStyle(block, 'margin', '5px 5px 5px 0');
                    YAHOO.util.Dom.setStyle(block, 'padding', '5px');
                    YAHOO.util.Dom.setStyle(block, 'border', '1px  solid #BB0000');
                    YAHOO.util.Dom.setStyle(block, 'background-color', '#FFFAFA');
                    answers[i].parentNode.insertBefore(block, answers[i]);
                    innerHideAnswers(answers[i]);
                }
            }
        }
    }

    function innerHideAnswers(answers) {
        setTimeout(function() {
            answers.parentNode.removeChild(answers);
        }, 0);
    }
})();
