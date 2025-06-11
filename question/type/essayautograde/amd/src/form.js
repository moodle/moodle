// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the term of the GNU General Public License as published by
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
 * javascript for Essay (autograde) edit form
 *
 * @module      qtype_essayautograde/form
 * @category    output
 * @copyright   2018 Gordon Bateson (gordon.bateson@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 3.0
 */
define(["jquery"], function($) {

    /** @alias module:qtype_essayautograde/form */
    var JS = {};

    // cache for standard width of TEXT input elements
    JS.sizewidths = new Array();

    /**
     * initialize this AMD module
     */
    JS.init = function() {
        JS.init_responseformat();
        JS.init_target_phrases();
        JS.init_add_button("addbands", "id_gradebands");
        JS.init_add_button("addphrases", "id_targetphrases");
    };

    /**
     * If the responseformat is changed, for example to "Plain text",
     * then change the format of the related editors too.
     */
    JS.init_responseformat = function() {
        var fmt = document.querySelector("select[name=responseformat]");
        $(fmt).change(function(){
            var fmtvalue = this.options[this.selectedIndex].value;
            var fmttext = this.options[this.selectedIndex].innerText;
            var fmtnumber = 0;  // Moodle format
            if (fmtvalue == "plain" || fmtvalue == "monospaced") {
                fmtnumber = 2; // Plain text format
            } else if (fmtvalue == "editor" || fmtvalue == "editorfilepicker") {
                fmtnumber = 1; // HTML format
            }
            var names = new Array("responsetemplate", "responsesample");
            names.forEach(function(name){
                // Changing the format may not immediately change the display in the browser.
                // For example, changing from "plain" to "editor" will not magically load an editor.
                // However, the new value will be returned to the server,
                // and used next time the question is displayed or edited.
                var fmt = document.querySelector("[name='" + name + "[format]']");
                if (fmt) {
                    if (fmt.matches("select")) {
                        // the user has selected "Plain text editor" in the Editor preferences
                        for (var i=0; i < fmt.options.length; i++) {
                            if (fmt.options[i].value == fmtnumber) {
                                fmt.options[i].selected = true;
                            }
                        }
                    } else if (fmt.matches("input[type=hidden]")) {
                        fmt.value = fmtnumber;
                    }
                }
                var txt = document.querySelector("[name='" + name + "[text]']");
                if (txt) {
                    var editor = null;
                    var editable = null;

                    var elm = txt.previousElementSibling;
                    if (elm && elm.matches(".editor_atto")) {
                        editor = elm;
                        editable = editor.querySelector("#id_" + name + "editable");
                    }

                    var elm = txt.nextElementSibling;
                    if (elm && elm.matches("#id_" + name + "_parent")) {
                        editor = elm;
                        elm = editor.querySelector("#id_" + name + "_ifr");
                        if (elm) {
                            // cross browser access to <iframe> <body>
                            elm = (elm.contentWindow || elm.contentDocument);
                            if (elm.document) {
                                elm = elm.document;
                            }
                            editable = elm.body;
                        }
                    }

                    if (editor) {
                        if (fmtnumber == 0 || fmtnumber == 1) {
                            // hide TEXTAREA
                            txt.hidden = true;
                            txt.style.display = "none";
                        } else {
                            // show TEXTAREA
                            txt.hidden = false;
                            txt.style.display = "";
                            // Transfer editor content to txt.
                            // Note that this will strip HTML tags.
                            if (editable) {
                                txt.value = editable.innerText;
                            }
                        }
                        if (fmtnumber == 0 || fmtnumber == 2) {
                            // hide editor
                            editor.hidden = true;
                            editor.style.display = "none";
                            elm = editor.parentNode.querySelector(".editor_atto_notification");
                            if (elm) {
                                elm.hidden = true;
                                elm.style.display = "none";
                            }
                        } else {
                            // show editor
                            editor.hidden = false;
                            editor.style.display = "";
                            // Transfer txt content to editor.
                            if (editable) {
                                editable.innerHTML = txt.value;
                            }
                        }
                        var msg = txt.parentNode.querySelector("#noinline-message");
                        if (fmtnumber == 0) {
                            // show message to explain why TEXTAREA and editor have disappeared
                            if (msg) {
                                msg.hidden = false;
                                msg.style.display = "";
                            } else {
                                msg = document.createElement("DIV");
                                msg.setAttribute("id", "noinline-message");
                                msg.appendChild(document.createTextNode(fmttext));
                                txt.parentNode.appendChild(msg);
                            }
                        } else if (msg) {
                            msg.hidden = true;
                            msg.style.display = "none";
                        }
                    }
                }
            });
        });
    };

    /**
     * Make the target phrase text boxes "expandable",
     * i.e. expand/contract to fit the width of the content
     */
    JS.init_target_phrases = function() {
        $("input[id^=id_phrasematch_]").each(function(){
            $(this).keyup(function(){
                // get min width for a box with this "size"
                var sizewidth = 0;
                var size = $(this).attr("size");
                if (size) {
                    if (size in JS.sizewidths) {
                        sizewidth = JS.sizewidths[size];
                    } else {
                        var elm = document.createElement("INPUT");
                        $(elm).attr("size", size);
                        $(elm).css("width", "auto");
                        $(elm).hide().appendTo("BODY");
                        sizewidth = $(elm).outerWidth();
                        $(elm).remove();
                        JS.sizewidths[size] = sizewidth;
                    }
                }
                // get required width for this text value
                var txt = document.createTextNode($(this).val());
                var elm = document.createElement("SPAN");
                $(elm).append(txt).hide().appendTo("BODY");
                var w = Math.max($(elm).width(), sizewidth);
                $(elm).remove();
                $(this).width(w);
            });
            $(this).triggerHandler("keyup");
        });
    };

    /**
     * modify an "add" button so that the page scrolls down
     * to the appropriate anchor when it reloads
     *
     * @param {string} name
     * @param {string} anchor
     */
    JS.init_add_button = function(name, anchor) {
        $("input[name=" + name + "]").click(function(){
            var url = $(this).closest("form").prop("action");
            url = url.replace(new RegExp("#.*$"), "");
            $(this).closest("form").prop("action", url + "#" + anchor);
        });
    };

    return JS;
});
