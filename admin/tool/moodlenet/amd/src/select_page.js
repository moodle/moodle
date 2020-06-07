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
 * When returning to Moodle let the user select which course to add the resource to.
 *
 * @module     tool_moodlenet/select_page
 * @package    tool_moodlenet
 * @copyright  2020 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'core/ajax',
    'core/templates',
    'tool_moodlenet/selectors',
    'core/notification'
], function(
    Ajax,
    Templates,
    Selectors,
    Notification
) {
    /**
     * @var {string} The id corresponding to the import.
     */
    var importId;

    /**
     * Set up the page.
     *
     * @method init
     * @param {string} importIdString the string ID of the import.
     */
    var init = function(importIdString) {
        importId = importIdString;
        var page = document.querySelector(Selectors.region.selectPage);
        registerListenerEvents(page);
        addCourses(page);
    };

    /**
     * Renders the 'no-courses' template.
     *
     * @param {HTMLElement} areaReplace the DOM node to replace.
     * @returns {Promise}
     */
    var renderNoCourses = function(areaReplace) {
        return Templates.renderPix('courses', 'tool_moodlenet').then(function(img) {
            return img;
        }).then(function(img) {
            var temp = document.createElement('div');
            temp.innerHTML = img.trim();
            return Templates.render('core_course/no-courses', {
                nocoursesimg: temp.firstChild.src
            });
        }).then(function(html, js) {
            Templates.replaceNodeContents(areaReplace, html, js);
            areaReplace.classList.add('mx-auto');
            areaReplace.classList.add('w-25');
            return;
        });
    };

    /**
     * Render the course cards for those supplied courses.
     *
     * @param {HTMLElement} areaReplace the DOM node to replace.
     * @param {Array<courses>} courses the courses to render.
     * @returns {Promise}
     */
    var renderCourses = function(areaReplace, courses) {
        return Templates.render('tool_moodlenet/view-cards', {
            courses: courses
        }).then(function(html, js) {
            Templates.replaceNodeContents(areaReplace, html, js);
            areaReplace.classList.remove('mx-auto');
            areaReplace.classList.remove('w-25');
            return;
        });
    };

    /**
     * For a given input, the page & what to replace fetch courses and manage icons too.
     *
     * @method searchCourses
     * @param {string} inputValue What to search for
     * @param {HTMLElement} page The whole page element for our page
     * @param {HTMLElement} areaReplace The Element to replace the contents of
     */
    var searchCourses = function(inputValue, page, areaReplace) {
        var searchIcon = page.querySelector(Selectors.region.searchIcon);
        var clearIcon = page.querySelector(Selectors.region.clearIcon);

        if (inputValue !== '') {
            searchIcon.classList.add('d-none');
            clearIcon.parentElement.classList.remove('d-none');
        } else {
            searchIcon.classList.remove('d-none');
            clearIcon.parentElement.classList.add('d-none');
        }
        var args = {
            searchvalue: inputValue,
        };
        Ajax.call([{
            methodname: 'tool_moodlenet_search_courses',
            args: args
        }])[0].then(function(result) {
            if (result.courses.length === 0) {
                return renderNoCourses(areaReplace);
            } else {
                // Add the importId to the course link
                result.courses.forEach(function(course) {
                    course.viewurl += '&id=' + importId;
                });
                return renderCourses(areaReplace, result.courses);
            }
        }).catch(Notification.exception);
    };

    /**
     * Add the event listeners to our page.
     *
     * @method registerListenerEvents
     * @param {HTMLElement} page The whole page element for our page
     */
    var registerListenerEvents = function(page) {
        var input = page.querySelector(Selectors.region.searchInput);
        var courseArea = page.querySelector(Selectors.region.courses);
        var clearIcon = page.querySelector(Selectors.region.clearIcon);
        clearIcon.addEventListener('click', function() {
            input.value = '';
            searchCourses('', page, courseArea);
        });

        input.addEventListener('input', debounce(function() {
            searchCourses(input.value, page, courseArea);
        }, 300));
    };

    /**
     * Fetch the courses to show the user. We use the same WS structure & template as the search for consistency.
     *
     * @method addCourses
     * @param {HTMLElement} page The whole page element for our course page
     */
    var addCourses = function(page) {
        var courseArea = page.querySelector(Selectors.region.courses);
        searchCourses('', page, courseArea);
    };

    /**
     * Define our own debounce function as Moodle 3.7 does not have it.
     *
     * @method debounce
     * @from underscore.js
     * @copyright 2009-2020 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
     * @licence MIT
     * @param {function} func The function we want to keep calling
     * @param {number} wait Our timeout
     * @param {boolean} immediate Do we want to apply the function immediately
     * @return {function}
     */
    var debounce = function(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this;
            var args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) {
                    func.apply(context, args);
                }
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) {
                func.apply(context, args);
            }
        };
    };
    return {
        init: init,
    };
});
