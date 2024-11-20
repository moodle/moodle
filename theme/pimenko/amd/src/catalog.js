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
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/templates', 'core/config'], function($, ajax, templates, cfg) {
    // General filter.
    const categorySelect = $('#page-course-index-category .categoryselect .urlselect .custom-select');
    const tagSelect = $('#page-course-index-category .tagselect .urlselect .custom-select');
    const categorySearch = $('#page-course-index-category .searchcourse form.simplesearchform input');
    const categoryidselect = $("input[name='categoryid']");
    // Custom field filter.
    const customfieldSearch = $('#page-course-index-category .customfieldsearch form');
    const customfieldDate = $('#page-course-index-category .customfielddate form select');
    const customfieldSelect = $('#page-course-index-category .customfieldselect form select');
    const resetbutton = $('#page-course-index-category button.btn[data-filteraction=\'reset\']');

    const startQuery = function() {
        categorySelect.attr("disabled", "true");
        tagSelect.attr("disabled", "true");
        categorySearch.attr("disabled", "true");
        customfieldSearch.attr("disabled", "true");
        customfieldDate.attr("disabled", "true");
        customfieldSelect.attr("disabled", "true");
        resetbutton.attr("disabled", "true");
        $('#course-gallery').attr("style", "opacity: 0.25;");
        $('#loader-gallery').attr("style", "display: flex;");
    };

    const endQuery = function(nbCourses) {
        categorySelect.removeAttr("disabled");
        tagSelect.removeAttr("disabled");
        categorySearch.removeAttr("disabled");
        customfieldSearch.removeAttr("disabled");
        customfieldDate.removeAttr("disabled");
        customfieldSelect.removeAttr("disabled");
        resetbutton.removeAttr("disabled");
        $('#course-gallery').attr("style", "opacity: 1;");
        $('#loader-gallery').attr("style", "display: none;");
        let buttonLoadMore = $('#load-more');
        if (nbCourses > 12) {
            buttonLoadMore.show();
        }

        buttonLoadMore.click(function() {
            let courses = document.getElementsByClassName('course-gallery');
            let nbCourseMore = 1;
            for (let i in courses) {
                if (courses.item(parseInt(i)).style.display === 'none') {
                    if (nbCourseMore <= 6) {
                        courses.item(parseInt(i)).style.display = 'flex';
                        if (parseInt(i) === courses.length - 1) {
                            $('#load-more').hide();
                        }
                        nbCourseMore++;
                    } else {
                        break;
                    }
                }
            }
        });
    };

    const doQuery = function(response) {
        if (typeof (response) != 'undefined') {
            let courses = response.courses;
            let toDestroy;
            let nbCourse = 1;
            let template = {};
            for (let i in courses) {
                // Url of picture.
                if (courses[i].overviewfiles[0] && courses[i].overviewfiles[0].fileurl) {
                    courses[i].urlimg = courses[i].overviewfiles[0].fileurl.replace("webservice\/", "");
                }

                courses[i].name = courses[i].fullname;
                courses[i].category = courses[i].categoryname;
                courses[i].rootpath = cfg.wwwroot;
                if (courses[i].enrollmentmethods.includes("synopsispaypal")) {
                    courses[i].url = cfg.wwwroot + '/enrol/synopsispaypal/index.php?id=' + courses[i].id;
                } else if (courses[i].enrollmentmethods.includes("synopsis")) {
                    courses[i].url = cfg.wwwroot + '/enrol/synopsis/index.php?id=' + courses[i].id;
                } else {
                    courses[i].url = cfg.wwwroot + '/course/view.php?id=' + courses[i].id;
                }

                if (nbCourse <= 12) {
                    courses[i].display = "flex";
                } else {
                    courses[i].display = "none";
                }
                nbCourse++;

                // Course 1 to remove.
                if (courses[i].id === 1) {
                    toDestroy = i;
                }
            }
            // Remove the course 1 of moodle.
            if (toDestroy) {
                courses.splice(toDestroy, 1);
            }
            if (courses.length > 12) {
                template.loadmore = true;
            }

            template.courses = courses;

            // Rendering of results.
            templates.render('theme_pimenko/course_gallery', template, 'theme_pimenko').then(function(html) {
                let element = document.getElementsByClassName('course-gallery-container')[0];
                element.innerHTML = String(html);
                endQuery(courses.length);
            }).catch(function(ex) {
                /* eslint no-console: "off" */
                console.error(ex);
                endQuery();
            });
        }
    };

    const eventcatalog = function() {
        categorySelect.change(function() {
            let href = categorySelect.val();
            categorySearch.val("");

            if (href !== "all") {
                document.location.href = href;
            } else {
                document.location.href = "/course/index.php";
            }
        });

        customfieldSearch.submit(function(event) {
            event.preventDefault();
            event.stopPropagation();

            let href = this.action;
            let value = this.querySelector('input').value;

            if (value === "") {
                href = href + '&customfieldvalue=all';
                document.location.href = href;
            } else {
                href = href + '&customfieldvalue=' + value;
                document.location.href = href;
            }
        });

        customfieldDate.change(function() {

            let parent = this.form;
            let datevalues = new FormData(parent);
            let href = parent.action;
            let categoryid = false;

            if (categoryidselect) {
                categoryid = categoryidselect.val();
            }

            let datatype = parent.closest('.customfielddate').dataset.name;

            href = href + '?customfieldselected=' + datatype + '&day=' +
                datevalues.get('date_selector[day]') +
                '&month=' + datevalues.get('date_selector[month]') +
                '&year=' + datevalues.get('date_selector[year]');

            if (categoryid) {
                href = href + '&categoryid=' + categoryid;
            }

            document.location.href = href;
        });

        resetbutton.click(function() {
            document.location.href = "/course/index.php?categoryid=" + categoryidselect.val();
        });

        $('#page-course-index-category .searchcourse form.simplesearchform').submit(function(event) {
            event.preventDefault();
            event.stopPropagation();

            let categoryid = 0;

            if (categoryidselect) {
                categoryid = categoryidselect.val();
            }

            let searchargs = {
                criterianame: 'search',
                criteriavalue: categorySearch.val(),
                categoryid: categoryid,
                page: 0,
                perpage: 100
            };

            let promises = ajax.call([
                {methodname: 'theme_pimenko_search_courses', args: searchargs}
            ]);

            startQuery();

            promises[0].done(function(response) {
                tagSelect.prop('selectedIndex', 0);
                customfieldSearch.prop('selectedIndex', 0);
                customfieldDate.prop('selectedIndex', 0);
                customfieldSelect.prop('selectedIndex', 0);
                doQuery(response);
            }).fail(function(ex) {
                /* eslint no-console: "off" */
                console.error('search_courses : ' + ex);
            });
        });

        $('#load-more').click(function() {
            let courses = document.getElementsByClassName('course-gallery');
            let nbCourseMore = 1;
            for (let i in courses) {
                if (courses.item(parseInt(i)).style.display === 'none') {
                    if (nbCourseMore <= 12) {
                        courses.item(parseInt(i)).style.display = 'flex';
                        if (parseInt(i) === courses.length - 1) {
                            $('#load-more').hide();
                        }
                        nbCourseMore++;
                    } else {
                        break;
                    }
                }
            }
        });
    };

    return {
        init: function() {
            eventcatalog();
        }
    };
});
