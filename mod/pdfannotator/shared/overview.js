/*
 * This file is a collection of JavaScript functions that control the behaviour
 * of the overview pages / templates for both student and teacher
 *
 * @package   mod_pdfannotator
 * @copyright 2018 onward RWTH Aachen, Rabea de Groot and Anna Heynkes (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 *
 * @param {type} Y
 * @param {type} __annotatorid
 * @param {type} __role
 * @return {undefined}
 */
function startOverview(Y, __annotatorid, __cmid, __capabilities, __action) { // Wrapper function that is called by controller.php

    require(['jquery', 'core/templates', 'core/notification'], function ($, templates, notification) {

        /************************** 1. Call initialising functions **************************/

        styleTableReset();

        hideAlert();

        addDropdownNavigation(null, __capabilities, __cmid);

        letUserSelectNumberOfItemsPerPage();

        if (__action === 'overviewquestions') {
            enableFilteringOfQuestions();
        } else if (__action === 'overviewanswers') {
            enableFilteringOfAnswers();
        } else if (__action === 'overviewreports') {
            enableFilteringOfReports();
        }

        shortenTextOverview();
        renderMathJax();

        /************************** 2. Function definitions **************************/

        /**
         * Function reduces the extra white space for the 'reset table' option added after table sort.
         *
         * @returns {undefined}
         */
        function styleTableReset() {
            var resetlink = document.getElementsByClassName('resettable mdl-right')[0];
            var filtercontainer = document.getElementById('pdfannotator-filter');
            if ( (typeof resetlink != 'undefined') && (typeof filtercontainer != 'undefined')) {
                $(resetlink).insertAfter(filtercontainer);
            }
        }
        /**
         * Function removes residual info boxes from the top of the page (if present, e.g. after unsubscribing).
         *
         * @returns {undefined}
         */
        function hideAlert() {
            setTimeout(function () {
                let notificationpanel = document.getElementById("pdfannotator_notificationpanel");
                if (notificationpanel instanceof Object) {
                    while (notificationpanel.hasChildNodes()) {
                        notificationpanel.removeChild(notificationpanel.firstChild);
                    }
                }
            }, 5000);
        }
        /**This function adds a select option for choosing the number of tablerows to be displayed
         * per page.
         *
         * @returns {undefined}
         */
        function letUserSelectNumberOfItemsPerPage() {

            // 1. Create a selection element.
            var itemsPerPage = document.createElement("SELECT");
            itemsPerPage.setAttribute("id", "itemsPerPage");
            itemsPerPage.classList.add('custom-select');

            let option0 = document.createElement("OPTION");
            option0.value = 5;
            option0.text = 5;

            let option1 = document.createElement("OPTION");
            option1.value = 10;
            option1.text = 10;

            let option2 = document.createElement("OPTION");
            option2.value = -1;
            option2.text = M.util.get_string('all', 'pdfannotator');

            // 2. Set current choice.
            var query = window.location.search;
            var pos = query.indexOf('&itemsperpage=');
            if ((pos !== null) && (pos != -1)) {

                let pastchoice = query.slice(pos + 14, pos + 16);
                let str = pastchoice.split('&');
                pastchoice = str[0];

                if (pastchoice === '5') {
                    option0.selected = true;
                } else if (pastchoice === '10') {
                    option1.selected = true;
                } else {
                    option2.selected = true;
                }
            } else {
                option0.selected = true; // Default.
            }

            // 3. Add options.
            itemsPerPage.add(option0);
            itemsPerPage.add(option1);
            itemsPerPage.add(option2);

            // 4. Add a label.
            var label = document.createElement("LABEL");
            var text = document.createTextNode(M.util.get_string('itemsperpage', 'pdfannotator'));
            label.setAttribute("for", "itemsPerPage");
            label.appendChild(text);

            // 5. Wrap it.
            var wrapper = document.createElement("DIV");
            wrapper.id = 'itemsperpagewrapper';
            wrapper.append(itemsPerPage);
            wrapper.insertBefore(label,itemsPerPage);

            // 6. Attach the wrapper to the pagination navigation (if present).
            var pagenav = document.getElementsByClassName('pagination-centered');
            if ((pagenav !== null) && (pagenav.length > 0)) {
                pagenav[1].parentNode.appendChild(wrapper);

            } else if ((pos !== null) && (pos != -1)) {
                var table = document.getElementsByClassName('flexible')[0];
                $(wrapper).insertAfter(table);

            } else {
                return;
            }

            // 7. Add functionality to the selection element.
            itemsPerPage.onchange = function () {
                var select = this;
                select = select.options[select.selectedIndex];
                select.selected = true;
                var oldpath = window.location.pathname + window.location.search;
                var newurl = '';
                if (oldpath.includes('itemsperpage')) {
                    let activepage = document.getElementsByClassName("page-item active");
                    if ((typeof(activepage) !== 'undefined') && (activepage !== null) && ( (typeof(activepage.nextSibling) == 'undefined') || (activepage.nextSibling == null) ) && (select.value == 10)) {
                        let pos2 = query.indexOf('&page=');
                        newurl = window.location.pathname + query.slice(0, pos2) + '&page=0' + '&itemsperpage=' + select.value;
                    } else {
                        newurl = window.location.pathname + query.slice(0, pos) + '&itemsperpage=' + select.value;
                    }

                } else {
                    newurl = oldpath + '&itemsperpage=' + select.value;
                }
                if (oldpath.includes('answerfilter')) {
                    var answerfilter = document.getElementById('answerfilter');
                    answerfilter = answerfilter.options[answerfilter.selectedIndex];
                    answerfilter.selected = true;
                    newurl += '&answerfilter=' + answerfilter.value;
                    newurl = newurl.replace('subscribeQuestion', 'overviewanswers');
                    newurl = newurl.replace('unsubscribeQuestion', 'overviewanswers');

                } else if (oldpath.includes('reportfilter')) {
                    var reportfilter = document.getElementById('reportfilter');
                    reportfilter = reportfilter.options[reportfilter.selectedIndex];
                    reportfilter.selected = true;
                    newurl += '&reportfilter=' + reportfilter.value;
                    newurl = newurl.replace('markreportasread', 'overviewreports');
                    newurl = newurl.replace('markreportasunread', 'overviewreports');

                } else if (oldpath.includes('questionfilter')) {
                    var questionfilter = document.getElementById('questionfilter');
                    questionfilter = questionfilter.options[questionfilter.selectedIndex];
                    questionfilter.selected = true;
                    newurl += '&questionfilter=' + questionfilter.value;
                }
                window.location.href = newurl;
            };

        }

        function enableFilteringOfQuestions() {

            var query = window.location.search;

            // 1. Create a selection element.
            var filter = document.createElement("SELECT");
            filter.setAttribute("id", "questionfilter");
            filter.classList.add('custom-select');

            let openquestions = document.createElement("OPTION");
            openquestions.value = 0;
            openquestions.text = M.util.get_string('openquestions', 'pdfannotator');

            let closedquestions = document.createElement("OPTION");
            closedquestions.value = 1;
            closedquestions.text = M.util.get_string('closedquestions', 'pdfannotator');

            let allquestions = document.createElement("OPTION");
            allquestions.value = 2;
            allquestions.text = M.util.get_string('allquestions', 'pdfannotator');

            // 2. Set current choice.
            var filtername = 'questionfilter=';
            var pos = query.indexOf(filtername);
            if (pos !== null && pos != -1) {
                let pastchoice = query.slice(pos + filtername.length, pos + filtername.length + 1);
                if (pastchoice === '0') {
                    openquestions.selected = true;
                } else if (pastchoice === '1') {
                    closedquestions.selected = true;
                } else {
                    allquestions.selected = true;
                }
            } else {
                openquestions.selected = true; // Default.
            }

            // 3. Add options.
            filter.add(openquestions);
            filter.add(closedquestions);
            filter.add(allquestions);

            // 4. Place filter next to the headline.
            var container = document.getElementById('pdfannotator-filter');
            container.appendChild(filter);

            // 5. Add functionality to the selection element.
            addFilterEventlistener(filter, filtername, query, pos);

        }

        function enableFilteringOfAnswers() {

            var query = window.location.search;

            // 1. Create a selection element.
            var filter = document.createElement("SELECT");
            filter.setAttribute("id", "answerfilter");
            filter.classList.add('custom-select');

            let option0 = document.createElement("OPTION");
            option0.value = 0;
            option0.text = M.util.get_string('allanswers', 'pdfannotator');

            let option1 = document.createElement("OPTION");
            option1.value = 1;
            option1.text = M.util.get_string('subscribedanswers', 'pdfannotator');

            // 2. Set current choice.
            var filtername = 'answerfilter=';
            var pos = query.indexOf(filtername);
            if (pos !== null && pos != -1) {
                let pastchoice = query.slice(pos + filtername.length, pos + filtername.length + 1);
                if (pastchoice === '0') {
                    option0.selected = true;
                } else {
                    option1.selected = true;
                }
            } else {
                option1.selected = true; // Default.
            }

            // 3. Add options.
            filter.add(option0);
            filter.add(option1);

            // 4. Place filter next to the headline.
            var container = document.getElementById('pdfannotator-filter');
            container.appendChild(filter);

            // 5. Add functionality to the selection element.
            addFilterEventlistener(filter, filtername, query, pos);

        }
        /**
         * This function adds a select option. Users can choose to see all reports,
         * only unseen reports or only seen reports.
         *
         * @returns {undefined}
         */
        function enableFilteringOfReports() {

            var query = window.location.search;

            // 1. Create a selection element.
            var filter = document.createElement("SELECT");
            filter.setAttribute("id", "reportfilter");
            filter.classList.add('custom-select');

            let option0 = document.createElement("OPTION");
            option0.value = 2;
            option0.text = M.util.get_string('allreports', 'pdfannotator');

            let option1 = document.createElement("OPTION");
            option1.value = 0;
            option1.text = M.util.get_string('unseenreports', 'pdfannotator');

            let option2 = document.createElement("OPTION");
            option2.value = 1;
            option2.text = M.util.get_string('seenreports', 'pdfannotator');

            // 2. Set current choice.
            var filtername = 'reportfilter=';
            var pos = query.indexOf(filtername);
            if (pos !== null && pos != -1) {
                let pastchoice = query.slice(pos + filtername.length, pos + filtername.length + 1);
                if (pastchoice === '2') {
                    option0.selected = true;
                } else if (pastchoice === '0') {
                    option1.selected = true;
                } else {
                    option2.selected = true;
                }
            } else {
                option1.selected = true; // Default.
            }

            // 3. Add options.
            filter.add(option0);
            filter.add(option1);
            filter.add(option2);

            // 4. Place filter next to the headline.
            var container = document.getElementById('pdfannotator-filter');
            container.appendChild(filter);

            // 5. Add functionality to the selection element.
            addFilterEventlistener(filter, filtername, query, pos);

        }

        function addFilterEventlistener(filter, filtername, query, pos) {
            filter.onchange = function () {
                var select = this;
                select = this.options[select.selectedIndex];
                select.selected = true;
                var newurl = window.location.pathname + query;
                var regex = new RegExp(filtername + '(\\d+)');
                if (pos !== null && pos != -1) {
                    newurl = newurl.replace(regex, filtername + select.value);
                } else {
                    newurl += '&' + filtername + select.value;
                }
                // Go back to page 0, because the current page might not exist anymore after filtering.
                var pagepos = query.indexOf('&page=');
                if ( (pagepos !== null) && (pagepos != -1)) {
                    newurl = newurl.replace(/page=(\d+)/, 'page=0');
                }
                window.location.href = newurl;
            };
        }

    });

    /**
     * Shorten display of any report or question to a maximum of 120 characters and display
     * a 'view more'/'view less' link
     *
     * Copyright 2013 Viral Patel and other contributors
     * http://viralpatel.net
     *
     * slightly modified by RWTH Aachen in 2018-2019
     *
     * Permission is hereby granted, free of charge, to any person obtaining
     * a copy of this software and associated documentation files (the
     * "Software"), to deal in the Software without restriction, including
     * without limitation the rights to use, copy, modify, merge, publish,
     * distribute, sublicense, and/or sell copies of the Software, and to
     * permit persons to whom the Software is furnished to do so, subject to
     * the following conditions:
     *
     * The above copyright notice and this permission notice shall be
     * included in all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
     * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
     * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
     * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
     * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
     * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
     * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
     *
     * @param {type} $
     * @returns {undefined}
     */
    function shortenTextOverview() {
        require(['jquery'], function ($) {
            var showChar = 120;
            var ellipsestext = "...";
            var moretext = M.util.get_string('showmore', 'pdfannotator');
            var lesstext = M.util.get_string('showless', 'pdfannotator');
            $('.more').each(function () {
                var content = this.innerText;
                
                var innerhtml = this.innerHTML;
                var posbegin = innerhtml.indexOf('<span');
                var labelspan = '';
                // If entry has "hidden from students"- or "restricted"- label display it after more/less and don't hide it.
                if (posbegin !== -1) {
                    var posend = innerhtml.indexOf('</span>')
                    labelspan = innerhtml.slice(posbegin, posend + 7);
                    var labeltext = labelspan.slice(labelspan.indexOf('>') + 1, labelspan.indexOf('</span>'));
                    content = content.replace(labeltext, '');
                    labelspan = '<br>' + labelspan;
                }

                var widthParent = $(this).parent()[0].offsetWidth;
                if (widthParent === 0) {
                    widthParent = 917; // Minimum width.
                }
                showChar = widthParent / 3;
                if (content.length > (showChar + ellipsestext.length)) {

                    let x = 0;
                    let i1, i2, i3;
                    i1 = i2 = i3 = 0;
                    // If content contains MathJax, don't cut it in a formula.
                    while (i1 !== -1 || i2 !== -1 || i3 !== -1) {
                        i1 = content.indexOf('\(', x);
                        if (i1 > showChar) {
                            i1 = -1;
                        }
                        if (i1 > -1) {
                            x = content.indexOf('\)', x) + 4;
                            showChar = Math.max(showChar, x);
                        }
                        i2 = content.indexOf('\['.x);
                        if (i2 > showChar) {
                            i2 = -1;
                        }
                        if (i2 > -1 && i2 < showChar) {
                            x = content.indexOf('\]', x) + 4;
                            showChar = Math.max(showChar, x);
                        }
                        i3 = content.indexOf('$$', x);
                        if (i3 > showChar) {
                            i3 = -1;
                        }
                        if (i3 > -1 && i3 < showChar) {
                            x = content.indexOf('$$', i3 + 1) + 4
                            showChar = Math.max(showChar, x);
                        }
                        if (showChar === content.length) {
                            return;
                        }
                    }
                    var c = content.substr(0, showChar); // First part of the string.
                    var h = content.slice(showChar); // Second part of the string.

                    var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>' + labelspan;

                    $(this).html(html);
                }

            });

            $(".morelink").click(function () {
                if ($(this).hasClass("less")) {
                    $(this).removeClass("less");
                    $(this).html(moretext);
                } else {
                    $(this).addClass("less");
                    $(this).html(lesstext);
                }
                $(this).parent().prev().toggle();
                $(this).prev().toggle();
                return false;
            });
        });
    }

    function renderMathJax() {
        var counter = 0;
        let mathjax = function () {
            if (typeof (MathJax) !== "undefined") {
                MathJax.Hub.Queue(['Typeset', MathJax.Hub]);
            } else if (counter < 10) {
                counter++;
                setTimeout(mathjax, 200);
            } else {
            }
        };
        mathjax();
    }

}