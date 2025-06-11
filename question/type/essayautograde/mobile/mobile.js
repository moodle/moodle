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
 * support for the mdl35+ mobile app. PHP calls this from within
 * classes/output/mobile.php
 * This file is the equivalent of 
 * qtype/YOURQTYPENAME/classes/YOURQTYPENAME.ts in the core app
 * e.g.
 * https://github.com/moodlehq/moodlemobile2/blob/v3.5.0/src/addon/qtype/ddwtos/classes/ddwtos.ts
 */

var that = this;

/**
 * replace bootstrap classes
 */
that.replaceBootstrapClasses = function(elm){

    // A regular expression to parse a bootstrap selector for margins and paddings
    var regex = new RegExp('^([mp])([tblrxy]?)-(\\d+)$');

    var names = elm.className.split(' ');
    names.forEach(function(name){

        var type = '';
        var side = '';
        var sides = [];
        var value = '';

        var m = name.match(regex);
        if (m && m[1]) {
            switch (m[1]) {
                case 'm': type = 'margin'; break;
                case 'p': type = 'padding'; break;
            }
            switch (m[2]) {
                case 't': side = 'Top'; break;
                case 'b': side = 'Bottom'; break;
                case 'l': side = 'Left'; break;
                case 'r': side = 'Right'; break;
                case 'y': sides = ['Top', 'Bottom']; break;
                case 'x': sides = ['Left', 'Right']; break;
                case '':  sides = ['Top', 'Right', 'Bottom', 'Left']; break;
            }
            switch (m[3]) {
                case '0': value = '0'; break;
                case '1': value = '0.25rem'; break;
                case '2': value = '0.5rem'; break;
                case '3': value = '1rem'; break;
                case '4': value = '1.5rem'; break;
            }
        } else {
            switch (name) {
                case 'rounded':
                    type = 'borderRadius';
                    value = '0.25rem';
                    break;
                case 'border':
                    type = 'border';
                    value = '1px solid #dee2e6';
                    break;
                case 'bg-secondary':
                    type = 'backgroundColor';
                    value = '#eeeeee';
                    break;
                case 'bg-danger':
                    type = 'backgroundColor';
                    value = '#ca3120';
                    break;
                case 'text-dark':
                    type = 'color';
                    value = '#343a40';
                    break;
                case 'text-light':
                    type = 'color';
                    value = '#f8f9fa';
                    break;
                case 'd-none':
                    type = 'display';
                    value = 'none';
                    break;
            }
        }

        if (type && value) {
            elm.classList.remove(name);
            if (sides.length) {
                sides.forEach(function(side){
                    elm.style[type + side] = value;
                });
            } else if (side) {
                elm.style[type + side] = value;
            } else {
                elm.style[type] = value;
            }
        }
    });
};

/**
 * getPluginString
 *
 * @param {string} component a full plugin name
 * @param {string} name of the required string
 */
that.getPluginString = function(component, name) {
    var p = this.CoreLangProvider;
    if (p) {
        var strings = p.sitePluginsStrings;
        var langs = new Array(p.getCurrentLanguage(),
                              p.getParentLanguage(),
                              p.getFallbackLanguage(),
                              p.getDefaultLanguage());
        var n = 'plugin.' + component + '.' + name;
        for (var i = 0; i < langs.length; i++) {
            var lang = langs[i];
            if (lang  && strings[lang] && strings[lang][n]) {
                return strings[lang][n]['value'];
            }
        }
    }
    // Oops, we couldn't find the string!
    return '[[' + component + '.' + name + ']]';
};


var result = {
    componentInit: function() {

        // Check that "this.question" was provided.
        if (! this.question) {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }

        // Create a temporary div to ease extraction of parts of the provided html.
        var div = document.createElement('div');
        div.innerHTML = this.question.html;

        // Replace Moodle's correct/incorrect classes, feedback and icons with mobile versions.
        that.CoreQuestionHelperProvider.replaceCorrectnessClasses(div);
        that.CoreQuestionHelperProvider.replaceFeedbackClasses(div);
        that.CoreQuestionHelperProvider.treatCorrectnessIcons(div);

        // Get useful parts of the data provided in the question's html.
        var text = div.querySelector('.qtext');
        if (text) {
            this.question.text = text.innerHTML;
        }

        var textarea = div.querySelector('.answer textarea');
        if (textarea === null) {
            // review or check
            textarea = div.querySelector('.answer .qtype_essay_response');
        }
        if (textarea) {
            textarea.style.borderRadius = '4px';
            textarea.style.padding = '6px 12px';
            if (textarea.matches('.readonly')) {
                textarea.style.border = '2px #b8dce2 solid'; // light blue
                textarea.style.backgroundColor = '#e7f3f5'; // lighter blue
            } else {
                textarea.style.backgroundColor = '#edf6f7'; // lightest blue
            }
            this.question.textarea = textarea.outerHTML;
        }

        var itemcount = div.querySelector('.itemcount');
        if (itemcount) {

            // Replace bootstrap styles with inline styles because
            // adding styles to 'mobile/styles_app.css' doesn't seem to be effective :-(
            that.replaceBootstrapClasses(itemcount);

            itemcount.querySelectorAll('p').forEach(function(p){
                that.replaceBootstrapClasses(p);
            });

            // Fix background and text color on "wordswarning" span.
            var warning = itemcount.querySelector(".warning");
            if (warning) {
                that.replaceBootstrapClasses(warning);
            }

            this.question.itemcount = itemcount.outerHTML;
        }

        /**
         * questionRendered
         */
        this.questionRendered = function(){

            var textarea = this.componentContainer.querySelector('textarea');
            var itemcount = this.componentContainer.querySelector('.itemcount');
            if (textarea && itemcount) {

                // Maybe "this.CoreLangProvider" has a method for fetching a string
                // but I can't find it, so we use our own method, thus:
                var minwordswarning = that.getPluginString("qtype_essayautograde", "minwordswarning");
                var maxwordswarning = that.getPluginString("qtype_essayautograde", "maxwordswarning");

                var countitems = itemcount.querySelector(".countitems");
                var value = countitems.querySelector(".value");
                var warning = countitems.querySelector(".warning");

                var itemtype = itemcount.dataset.itemtype;
                var minitems = parseInt(itemcount.dataset.minitems);
                var maxitems = parseInt(itemcount.dataset.maxitems);

                var itemsplit = '';
                switch (itemtype) {
                    case "chars": itemsplit = ""; break;
                    case "words": itemsplit = "[\\s—–]+"; break;
                    case "sentences": itemsplit = "[\\.?!]+"; break;
                    case "paragraphs": itemsplit = "[\\r\\n]+"; break;
                }

                if (itemsplit) {
                    itemsplit = new RegExp(itemsplit);
                    textarea.addEventListener("keyup", function() {
                        var text = textarea.value;
                        var warningtext = "";
                        var count = 0;
                        if (text) {
                            count = text.split(itemsplit).filter(function(item) {
                                return (item !== "");
                            }).length;
                            if (minitems && (count < minitems)) {
                                warningtext = minwordswarning;
                            }
                            if (maxitems && (count > maxitems)) {
                                warningtext = maxwordswarning;
                            }
                        }
                        value.innerText = count;
                        if (warning) {
                            warning.innerText = warningtext;
                            if (warningtext == "") {
                                warning.style.display = "none";
                            } else {
                                warning.style.display = "inline";
                            }
                        }
                    });
                }
            }
        };

        if (text && textarea) {
            return true;
        }

        // Oops, the expected elements, text and textarea, were not found !!
        return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
    }
};

// This next line is required as is (because of an eval step that puts this result object into the global scope).
result;
