// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Manage the courses view for the overview block.
 *
 * @copyright  2018 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import * as Repository from 'block_myoverview/repository';
import * as PagedContentFactory from 'core/paged_content_factory';
import * as PubSub from 'core/pubsub';
import * as CustomEvents from 'core/custom_interaction_events';
import * as Notification from 'core/notification';
import * as Templates from 'core/templates';
import * as CourseEvents from 'core_course/events';
import SELECTORS from 'block_myoverview/selectors';
import * as PagedContentEvents from 'core/paged_content_events';
import * as Aria from 'core/aria';
import {debounce} from 'core/utils';

const TEMPLATES = {
    COURSES_CARDS: 'block_myoverview/view-cards',
    COURSES_LIST: 'block_myoverview/view-list',
    COURSES_SUMMARY: 'block_myoverview/view-summary',
    NOCOURSES: 'core_course/no-courses'
};

const GROUPINGS = {
    GROUPING_ALLINCLUDINGHIDDEN: 'allincludinghidden',
    GROUPING_ALL: 'all',
    GROUPING_INPROGRESS: 'inprogress',
    GROUPING_FUTURE: 'future',
    GROUPING_PAST: 'past',
    GROUPING_FAVOURITES: 'favourites',
    GROUPING_HIDDEN: 'hidden'
};

const NUMCOURSES_PERPAGE = [12, 24, 48, 96, 0];

let loadedPages = [];

let courseOffset = 0;

let lastPage = 0;

let lastLimit = 0;

let namespace = null;

/**
 * Get filter values from DOM.
 *
 * @param {object} root The root element for the courses view.
 * @return {filters} Set filters.
 */
const getFilterValues = root => {
    const courseRegion = root.find(SELECTORS.courseView.region);
    return {
        display: courseRegion.attr('data-display'),
        grouping: courseRegion.attr('data-grouping'),
        sort: courseRegion.attr('data-sort'),
        displaycategories: courseRegion.attr('data-displaycategories'),
        customfieldname: courseRegion.attr('data-customfieldname'),
        customfieldvalue: courseRegion.attr('data-customfieldvalue'),
    };
};

// We want the paged content controls below the paged content area.
// and the controls should be ignored while data is loading.
const DEFAULT_PAGED_CONTENT_CONFIG = {
    ignoreControlWhileLoading: true,
    controlPlacementBottom: true,
    persistentLimitKey: 'block_myoverview_user_paging_preference'
};

/**
 * Get enrolled courses from backend.
 *
 * @param {object} filters The filters for this view.
 * @param {int} limit The number of courses to show.
 * @return {promise} Resolved with an array of courses.
 */
const getMyCourses = (filters, limit) => {
    return Repository.getEnrolledCoursesByTimeline({
        offset: courseOffset,
        limit: limit,
        classification: filters.grouping,
        sort: filters.sort,
        customfieldname: filters.customfieldname,
        customfieldvalue: filters.customfieldvalue
    });
};

/**
 * Search for enrolled courses from backend.
 *
 * @param {object} filters The filters for this view.
 * @param {int} limit The number of courses to show.
 * @param {string} searchValue What does the user want to search within their courses.
 * @return {promise} Resolved with an array of courses.
 */
const getSearchMyCourses = (filters, limit, searchValue) => {
    return Repository.getEnrolledCoursesByTimeline({
        offset: courseOffset,
        limit: limit,
        classification: 'search',
        sort: filters.sort,
        customfieldname: filters.customfieldname,
        customfieldvalue: filters.customfieldvalue,
        searchvalue: searchValue
    });
};

/**
 * Get the container element for the favourite icon.
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id number
 * @return {Object} The favourite icon container
 */
const getFavouriteIconContainer = (root, courseId) => {
    return root.find(SELECTORS.FAVOURITE_ICON + '[data-course-id="' + courseId + '"]');
};

/**
 * Get the paged content container element.
 *
 * @param {Object} root The course overview container
 * @param {Number} index Rendered page index.
 * @return {Object} The rendered paged container.
 */
const getPagedContentContainer = (root, index) => {
    return root.find('[data-region="paged-content-page"][data-page="' + index + '"]');
};

/**
 * Get the course id from a favourite element.
 *
 * @param {Object} root The favourite icon container element.
 * @return {Number} Course id.
 */
const getCourseId = root => {
    return root.attr('data-course-id');
};

/**
 * Hide the favourite icon.
 *
 * @param {Object} root The favourite icon container element.
 * @param {Number} courseId Course id number.
 */
const hideFavouriteIcon = (root, courseId) => {
    const iconContainer = getFavouriteIconContainer(root, courseId);

    const isFavouriteIcon = iconContainer.find(SELECTORS.ICON_IS_FAVOURITE);
    isFavouriteIcon.addClass('hidden');
    Aria.hide(isFavouriteIcon);

    const notFavourteIcon = iconContainer.find(SELECTORS.ICON_NOT_FAVOURITE);
    notFavourteIcon.removeClass('hidden');
    Aria.unhide(notFavourteIcon);
};

/**
 * Show the favourite icon.
 *
 * @param {Object} root The course overview container.
 * @param {Number} courseId Course id number.
 */
const showFavouriteIcon = (root, courseId) => {
    const iconContainer = getFavouriteIconContainer(root, courseId);

    const isFavouriteIcon = iconContainer.find(SELECTORS.ICON_IS_FAVOURITE);
    isFavouriteIcon.removeClass('hidden');
    Aria.unhide(isFavouriteIcon);

    const notFavourteIcon = iconContainer.find(SELECTORS.ICON_NOT_FAVOURITE);
    notFavourteIcon.addClass('hidden');
    Aria.hide(notFavourteIcon);
};

/**
 * Get the action menu item
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id.
 * @return {Object} The add to favourite menu item.
 */
const getAddFavouriteMenuItem = (root, courseId) => {
    return root.find('[data-action="add-favourite"][data-course-id="' + courseId + '"]');
};

/**
 * Get the action menu item
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id.
 * @return {Object} The remove from favourites menu item.
 */
const getRemoveFavouriteMenuItem = (root, courseId) => {
    return root.find('[data-action="remove-favourite"][data-course-id="' + courseId + '"]');
};

/**
 * Add course to favourites
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id number
 */
const addToFavourites = (root, courseId) => {
    const removeAction = getRemoveFavouriteMenuItem(root, courseId);
    const addAction = getAddFavouriteMenuItem(root, courseId);

    setCourseFavouriteState(courseId, true).then(success => {
        if (success) {
            PubSub.publish(CourseEvents.favourited, courseId);
            removeAction.removeClass('hidden');
            addAction.addClass('hidden');
            showFavouriteIcon(root, courseId);
        } else {
            Notification.alert('Starring course failed', 'Could not change favourite state');
        }
        return;
    }).catch(Notification.exception);
};

/**
 * Remove course from favourites
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id number
 */
const removeFromFavourites = (root, courseId) => {
    const removeAction = getRemoveFavouriteMenuItem(root, courseId);
    const addAction = getAddFavouriteMenuItem(root, courseId);

    setCourseFavouriteState(courseId, false).then(success => {
        if (success) {
            PubSub.publish(CourseEvents.unfavorited, courseId);
            removeAction.addClass('hidden');
            addAction.removeClass('hidden');
            hideFavouriteIcon(root, courseId);
        } else {
            Notification.alert('Starring course failed', 'Could not change favourite state');
        }
        return;
    }).catch(Notification.exception);
};

/**
 * Get the action menu item
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id.
 * @return {Object} The hide course menu item.
 */
const getHideCourseMenuItem = (root, courseId) => {
    return root.find('[data-action="hide-course"][data-course-id="' + courseId + '"]');
};

/**
 * Get the action menu item
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id.
 * @return {Object} The show course menu item.
 */
const getShowCourseMenuItem = (root, courseId) => {
    return root.find('[data-action="show-course"][data-course-id="' + courseId + '"]');
};

/**
 * Hide course
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id number
 */
const hideCourse = (root, courseId) => {
    const hideAction = getHideCourseMenuItem(root, courseId);
    const showAction = getShowCourseMenuItem(root, courseId);
    const filters = getFilterValues(root);

    setCourseHiddenState(courseId, true);

    // Remove the course from this view as it is now hidden and thus not covered by this view anymore.
    // Do only if we are not in "All (including archived)" view mode where really all courses are shown.
    if (filters.grouping !== GROUPINGS.GROUPING_ALLINCLUDINGHIDDEN) {
        hideElement(root, courseId);
    }

    hideAction.addClass('hidden');
    showAction.removeClass('hidden');
};

/**
 * Show course
 *
 * @param {Object} root The course overview container
 * @param {Number} courseId Course id number
 */
const showCourse = (root, courseId) => {
    const hideAction = getHideCourseMenuItem(root, courseId);
    const showAction = getShowCourseMenuItem(root, courseId);
    const filters = getFilterValues(root);

    setCourseHiddenState(courseId, null);

    // Remove the course from this view as it is now shown again and thus not covered by this view anymore.
    // Do only if we are not in "All (including archived)" view mode where really all courses are shown.
    if (filters.grouping !== GROUPINGS.GROUPING_ALLINCLUDINGHIDDEN) {
        hideElement(root, courseId);
    }

    hideAction.removeClass('hidden');
    showAction.addClass('hidden');
};

/**
 * Set the courses hidden status and push to repository
 *
 * @param {Number} courseId Course id to favourite.
 * @param {Boolean} status new hidden status.
 * @return {Promise} Repository promise.
 */
const setCourseHiddenState = (courseId, status) => {

    // If the given status is not hidden, the preference has to be deleted with a null value.
    if (status === false) {
        status = null;
    }
    return Repository.updateUserPreferences({
        preferences: [
            {
                type: 'block_myoverview_hidden_course_' + courseId,
                value: status
            }
        ]
    });
};

/**
 * Reset the loadedPages dataset to take into account the hidden element
 *
 * @param {Object} root The course overview container
 * @param {Number} id The course id number
 */
const hideElement = (root, id) => {
    const pagingBar = root.find('[data-region="paging-bar"]');
    const jumpto = parseInt(pagingBar.attr('data-active-page-number'));

    // Get a reduced dataset for the current page.
    const courseList = loadedPages[jumpto];
    let reducedCourse = courseList.courses.reduce((accumulator, current) => {
        if (+id !== +current.id) {
            accumulator.push(current);
        }
        return accumulator;
    }, []);

    // Get the next page's data if loaded and pop the first element from it.
    if (typeof (loadedPages[jumpto + 1]) !== 'undefined') {
        const newElement = loadedPages[jumpto + 1].courses.slice(0, 1);

        // Adjust the dataset for the reset of the pages that are loaded.
        loadedPages.forEach((courseList, index) => {
            if (index > jumpto) {
                let popElement = [];
                if (typeof (loadedPages[index + 1]) !== 'undefined') {
                    popElement = loadedPages[index + 1].courses.slice(0, 1);
                }
                loadedPages[index].courses = [...loadedPages[index].courses.slice(1), ...popElement];
            }
        });

        reducedCourse = [...reducedCourse, ...newElement];
    }

    // Check if the next page is the last page and if it still has data associated to it.
    if (lastPage === jumpto + 1 && loadedPages[jumpto + 1].courses.length === 0) {
        const pagedContentContainer = root.find('[data-region="paged-content-container"]');
        PagedContentFactory.resetLastPageNumber($(pagedContentContainer).attr('id'), jumpto);
    }

    loadedPages[jumpto].courses = reducedCourse;

    // Reduce the course offset.
    courseOffset--;

    // Render the paged content for the current.
    const pagedContentPage = getPagedContentContainer(root, jumpto);
    renderCourses(root, loadedPages[jumpto]).then((html, js) => {
        return Templates.replaceNodeContents(pagedContentPage, html, js);
    }).catch(Notification.exception);

    // Delete subsequent pages in order to trigger the callback.
    loadedPages.forEach((courseList, index) => {
        if (index > jumpto) {
            const page = getPagedContentContainer(root, index);
            page.remove();
        }
    });
};

/**
 * Set the courses favourite status and push to repository
 *
 * @param {Number} courseId Course id to favourite.
 * @param {boolean} status new favourite status.
 * @return {Promise} Repository promise.
 */
const setCourseFavouriteState = (courseId, status) => {

    return Repository.setFavouriteCourses({
        courses: [
            {
                'id': courseId,
                'favourite': status
            }
        ]
    }).then(result => {
        if (result.warnings.length === 0) {
            loadedPages.forEach(courseList => {
                courseList.courses.forEach((course, index) => {
                    if (course.id == courseId) {
                        courseList.courses[index].isfavourite = status;
                    }
                });
            });
            return true;
        } else {
            return false;
        }
    }).catch(Notification.exception);
};

/**
 * Given there are no courses to render provide the rendered template.
 *
 * @param {object} root The root element for the courses view.
 * @return {promise} jQuery promise resolved after rendering is complete.
 */
const noCoursesRender = root => {
    const nocoursesimg = root.find(SELECTORS.courseView.region).attr('data-nocoursesimg');
    const newcourseurl = root.find(SELECTORS.courseView.region).attr('data-newcourseurl');
    return Templates.render(TEMPLATES.NOCOURSES, {
        nocoursesimg: nocoursesimg,
        newcourseurl: newcourseurl
    });
};

/**
 * Render the dashboard courses.
 *
 * @param {object} root The root element for the courses view.
 * @param {array} coursesData containing array of returned courses.
 * @return {promise} jQuery promise resolved after rendering is complete.
 */
const renderCourses = (root, coursesData) => {

    const filters = getFilterValues(root);

    let currentTemplate = '';
    if (filters.display === 'card') {
        currentTemplate = TEMPLATES.COURSES_CARDS;
    } else if (filters.display === 'list') {
        currentTemplate = TEMPLATES.COURSES_LIST;
    } else {
        currentTemplate = TEMPLATES.COURSES_SUMMARY;
    }

    if (!coursesData) {
        return noCoursesRender(root);
    } else {
        // Sometimes we get weird objects coming after a failed search, cast to ensure typing functions.
        if (Array.isArray(coursesData.courses) === false) {
            coursesData.courses = Object.values(coursesData.courses);
        }
        // Whether the course category should be displayed in the course item.
        coursesData.courses = coursesData.courses.map(course => {
            course.showcoursecategory = filters.displaycategories === 'on';
            return course;
        });
        if (coursesData.courses.length) {
            return Templates.render(currentTemplate, {
                courses: coursesData.courses,
            });
        } else {
            return noCoursesRender(root);
        }
    }
};

/**
 * Return the callback to be passed to the subscribe event
 *
 * @param {object} root The root element for the courses view
 * @return {function} Partially applied function that'll execute when passed a limit
 */
const setLimit = root => {
    // @param {Number} limit The paged limit that is passed through the event.
    return limit => root.find(SELECTORS.courseView.region).attr('data-paging', limit);
};

/**
 * Intialise the paged list and cards views on page load.
 * Returns an array of paged contents that we would like to handle here
 *
 * @param {object} root The root element for the courses view
 * @param {string} namespace The namespace for all the events attached
 */
const registerPagedEventHandlers = (root, namespace) => {
    const event = namespace + PagedContentEvents.SET_ITEMS_PER_PAGE_LIMIT;
    PubSub.subscribe(event, setLimit(root));
};

/**
 * Figure out how many items are going to be allowed to be rendered in the block.
 *
 * @param  {Number} pagingLimit How many courses to display
 * @param  {Object} root The course overview container
 * @return {Number[]} How many courses will be rendered
 */
const itemsPerPageFunc = (pagingLimit, root) => {
    let itemsPerPage = NUMCOURSES_PERPAGE.map(value => {
        let active = false;
        if (value === pagingLimit) {
            active = true;
        }

        return {
            value: value,
            active: active
        };
    });

    // Filter out all pagination options which are too large for the amount of courses user is enrolled in.
    const totalCourseCount = parseInt(root.find(SELECTORS.courseView.region).attr('data-totalcoursecount'), 10);
    return itemsPerPage.filter(pagingOption => {
        return pagingOption.value < totalCourseCount || pagingOption.value === 0;
    });
};

/**
 * Mutates and controls the loadedPages array and handles the bootstrapping.
 *
 * @param {Array|Object} coursesData Array of all of the courses to start building the page from
 * @param {Number} currentPage What page are we currently on?
 * @param {Object} pageData Any current page information
 * @param {Object} actions Paged content helper
 * @param {null|boolean} activeSearch Are we currently actively searching and building up search results?
 */
const pageBuilder = (coursesData, currentPage, pageData, actions, activeSearch = null) => {
    // If the courseData comes in an object then get the value otherwise it is a pure array.
    let courses = coursesData.courses ? coursesData.courses : coursesData;
    let nextPageStart = 0;
    let pageCourses = [];

    // If current page's data is loaded make sure we max it to page limit.
    if (typeof (loadedPages[currentPage]) !== 'undefined') {
        pageCourses = loadedPages[currentPage].courses;
        const currentPageLength = pageCourses.length;
        if (currentPageLength < pageData.limit) {
            nextPageStart = pageData.limit - currentPageLength;
            pageCourses = {...loadedPages[currentPage].courses, ...courses.slice(0, nextPageStart)};
        }
    } else {
        // When the page limit is zero, there is only one page of courses, no start for next page.
        nextPageStart = pageData.limit || false;
        pageCourses = (pageData.limit > 0) ? courses.slice(0, pageData.limit) : courses;
    }

    // Finished setting up the current page.
    loadedPages[currentPage] = {
        courses: pageCourses
    };

    // Set up the next page (if there is more than one page).
    const remainingCourses = nextPageStart !== false ? courses.slice(nextPageStart, courses.length) : [];
    if (remainingCourses.length) {
        loadedPages[currentPage + 1] = {
            courses: remainingCourses
        };
    }

    // Set the last page to either the current or next page.
    if (loadedPages[currentPage].courses.length < pageData.limit || !remainingCourses.length) {
        lastPage = currentPage;
        if (activeSearch === null) {
            actions.allItemsLoaded(currentPage);
        }
    } else if (typeof (loadedPages[currentPage + 1]) !== 'undefined'
        && loadedPages[currentPage + 1].courses.length < pageData.limit) {
        lastPage = currentPage + 1;
    }

    courseOffset = coursesData.nextoffset;
};

/**
 * In cases when switching between regular rendering and search rendering we need to reset some variables.
 */
const resetGlobals = () => {
    courseOffset = 0;
    loadedPages = [];
    lastPage = 0;
    lastLimit = 0;
};

/**
 * The default functionality of fetching paginated courses without special handling.
 *
 * @return {function(Object, Object, Object, Object, Object, Promise, Number): void}
 */
const standardFunctionalityCurry = () => {
    resetGlobals();
    return (filters, currentPage, pageData, actions, root, promises, limit) => {
        const pagePromise = getMyCourses(
            filters,
            limit
        ).then(coursesData => {
            pageBuilder(coursesData, currentPage, pageData, actions);
            return renderCourses(root, loadedPages[currentPage]);
        }).catch(Notification.exception);

        promises.push(pagePromise);
    };
};

/**
 * Initialize the searching functionality so we can call it when required.
 *
 * @return {function(Object, Number, Object, Object, Object, Promise, Number, String): void}
 */
const searchFunctionalityCurry = () => {
    resetGlobals();
    return (filters, currentPage, pageData, actions, root, promises, limit, inputValue) => {
        const searchingPromise = getSearchMyCourses(
            filters,
            limit,
            inputValue
        ).then(coursesData => {
            pageBuilder(coursesData, currentPage, pageData, actions);
            return renderCourses(root, loadedPages[currentPage]);
        }).catch(Notification.exception);

        promises.push(searchingPromise);
    };
};

/**
 * Initialise the courses list and cards views on page load.
 *
 * @param {object} root The root element for the courses view.
 * @param {function} promiseFunction How do we fetch the courses and what do we do with them?
 * @param {null | string} inputValue What to search for
 */
const initializePagedContent = (root, promiseFunction, inputValue = null) => {
    const pagingLimit = parseInt(root.find(SELECTORS.courseView.region).attr('data-paging'), 10);
    let itemsPerPage = itemsPerPageFunc(pagingLimit, root);

    const filters = getFilterValues(root);
    const config = {...{}, ...DEFAULT_PAGED_CONTENT_CONFIG};
    config.eventNamespace = namespace;

    const pagedContentPromise = PagedContentFactory.createWithLimit(
        itemsPerPage,
        (pagesData, actions) => {
            let promises = [];
            pagesData.forEach(pageData => {
                const currentPage = pageData.pageNumber;
                let limit = (pageData.limit > 0) ? pageData.limit : 0;

                // Reset local variables if limits have changed.
                if (+lastLimit !== +limit) {
                    loadedPages = [];
                    courseOffset = 0;
                    lastPage = 0;
                }

                if (lastPage === currentPage) {
                    // If we are on the last page and have it's data then load it from cache.
                    actions.allItemsLoaded(lastPage);
                    promises.push(renderCourses(root, loadedPages[currentPage]));
                    return;
                }

                lastLimit = limit;

                // Get 2 pages worth of data as we will need it for the hidden functionality.
                if (typeof (loadedPages[currentPage + 1]) === 'undefined') {
                    if (typeof (loadedPages[currentPage]) === 'undefined') {
                        limit *= 2;
                    }
                }

                // Call the curried function that'll handle the course promise and any manipulation of it.
                promiseFunction(filters, currentPage, pageData, actions, root, promises, limit, inputValue);
            });
            return promises;
        },
        config
    );

    pagedContentPromise.then((html, js) => {
        registerPagedEventHandlers(root, namespace);
        return Templates.replaceNodeContents(root.find(SELECTORS.courseView.region), html, js);
    }).catch(Notification.exception);
};

/**
 * Listen to, and handle events for the myoverview block.
 *
 * @param {Object} root The myoverview block container element.
 * @param {HTMLElement} page The whole HTMLElement for our block.
 */
const registerEventListeners = (root, page) => {

    CustomEvents.define(root, [
        CustomEvents.events.activate
    ]);

    root.on(CustomEvents.events.activate, SELECTORS.ACTION_ADD_FAVOURITE, (e, data) => {
        const favourite = $(e.target).closest(SELECTORS.ACTION_ADD_FAVOURITE);
        const courseId = getCourseId(favourite);
        addToFavourites(root, courseId);
        data.originalEvent.preventDefault();
    });

    root.on(CustomEvents.events.activate, SELECTORS.ACTION_REMOVE_FAVOURITE, (e, data) => {
        const favourite = $(e.target).closest(SELECTORS.ACTION_REMOVE_FAVOURITE);
        const courseId = getCourseId(favourite);
        removeFromFavourites(root, courseId);
        data.originalEvent.preventDefault();
    });

    root.on(CustomEvents.events.activate, SELECTORS.FAVOURITE_ICON, (e, data) => {
        data.originalEvent.preventDefault();
    });

    root.on(CustomEvents.events.activate, SELECTORS.ACTION_HIDE_COURSE, (e, data) => {
        const target = $(e.target).closest(SELECTORS.ACTION_HIDE_COURSE);
        const courseId = getCourseId(target);
        hideCourse(root, courseId);
        data.originalEvent.preventDefault();
    });

    root.on(CustomEvents.events.activate, SELECTORS.ACTION_SHOW_COURSE, (e, data) => {
        const target = $(e.target).closest(SELECTORS.ACTION_SHOW_COURSE);
        const courseId = getCourseId(target);
        showCourse(root, courseId);
        data.originalEvent.preventDefault();
    });

    // Searching functionality event handlers.
    const input = page.querySelector(SELECTORS.region.searchInput);
    const clearIcon = page.querySelector(SELECTORS.region.clearIcon);

    clearIcon.addEventListener('click', () => {
        input.value = '';
        input.focus();
        clearSearch(clearIcon, root);
    });

    input.addEventListener('input', debounce(() => {
        if (input.value === '') {
            clearSearch(clearIcon, root);
        } else {
            activeSearch(clearIcon);
            initializePagedContent(root, searchFunctionalityCurry(), input.value.trim());
        }
    }, 1000));
};

/**
 * Reset the search icon and trigger the init for the block.
 *
 * @param {HTMLElement} clearIcon Our closing icon to manipulate.
 * @param {Object} root The myoverview block container element.
 */
export const clearSearch = (clearIcon, root) => {
    clearIcon.classList.add('d-none');
    init(root);
};

/**
 * Change the searching icon to its' active state.
 *
 * @param {HTMLElement} clearIcon Our closing icon to manipulate.
 */
const activeSearch = (clearIcon) => {
    clearIcon.classList.remove('d-none');
};

/**
 * Intialise the courses list and cards views on page load.
 *
 * @param {object} root The root element for the courses view.
 */
export const init = root => {
    root = $(root);
    loadedPages = [];
    lastPage = 0;
    courseOffset = 0;

    if (!root.attr('data-init')) {
        const page = document.querySelector(SELECTORS.region.selectBlock);
        registerEventListeners(root, page);
        namespace = "block_myoverview_" + root.attr('id') + "_" + Math.random();
        root.attr('data-init', true);
    }

    initializePagedContent(root, standardFunctionalityCurry());
};

/**
 * Reset the courses views to their original
 * state on first page load.courseOffset
 *
 * This is called when configuration has changed for the event lists
 * to cause them to reload their data.
 *
 * @param {Object} root The root element for the timeline view.
 */
export const reset = root => {
    if (loadedPages.length > 0) {
        loadedPages.forEach((courseList, index) => {
            let pagedContentPage = getPagedContentContainer(root, index);
            renderCourses(root, courseList).then((html, js) => {
                return Templates.replaceNodeContents(pagedContentPage, html, js);
            }).catch(Notification.exception);
        });
    } else {
        init(root);
    }
};
