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
 * A javascript module to handle user AJAX actions.
 *
 * @module     core_courseformat/local/activitychooser/repository
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import ajax from 'core/ajax';
import Log from 'core/log';

// A promises map for caching specific section modules data.
const sectionsModulesPromises = new Map();

// A promises map for caching a specific section footer data.
const sectionsFooterPromises = new Map();

/**
 * Fetch all the information on modules we'll need in the activity chooser.
 *
 * @method activityModules
 * @deprecated since Moodle 5.1
 * @todo Remove in Moodle 6.0 (MDL-86310)
 * @param {Number} courseid What course to fetch the modules for
 * @param {Number} sectionnum What course section to fetch the modules for
 * @return {object} jQuery promise
 */
export const activityModules = (courseid, sectionnum) => {
    Log.debug('The activityModules function is deprecated. Use sectionActivityModules instead.');
    const request = {
        methodname: 'core_course_get_course_content_items',
        args: {
            courseid: courseid,
            sectionnum: sectionnum,
        },
    };
    return ajax.call([request])[0];
};

/**
 * Fetch all the information on modules that can be added to a section.
 *
 * @method sectionActivityModules
 * @param {Number} courseId What course to fetch the modules for
 * @param {Number} sectionId What course section to fetch the modules for
 * @return {object} jQuery promise
 */
export const sectionActivityModules = (courseId, sectionId) => {
    const request = {
        methodname: 'core_courseformat_get_section_content_items',
        args: {
            courseid: courseId,
            sectionid: sectionId,
        },
    };
    return ajax.call([request])[0];
};

/**
 * Given a module name, module ID & the current course we want to specify that the module
 * is a users' favourite.
 *
 * @method favouriteModule
 * @param {String} modName Frankenstyle name of the component to add favourite
 * @param {int} modID ID of the module. Mainly for LTI cases where they have same / similar names
 * @return {object} jQuery promise
 */
export const favouriteModule = (modName, modID) => {
    const request = {
        methodname: 'core_course_add_content_item_to_user_favourites',
        args: {
            componentname: modName,
            contentitemid: modID,
        },
    };
    const promise = ajax.call([request])[0];
    // After the promise is resolved, we need to invalidate the cache for the section.
    return promise.then(() => {
        sectionsModulesPromises.clear();
        return true;
    });
};

/**
 * Given a module name, module ID & the current course we want to specify that the module
 * is no longer a users' favourite.
 *
 * @method unfavouriteModule
 * @param {String} modName Frankenstyle name of the component to add favourite
 * @param {int} modID ID of the module. Mainly for LTI cases where they have same / similar names
 * @return {object} jQuery promise
 */
export const unfavouriteModule = (modName, modID) => {
    const request = {
        methodname: 'core_course_remove_content_item_from_user_favourites',
        args: {
            componentname: modName,
            contentitemid: modID,
        },
    };

    const promise = ajax.call([request])[0];
    // After the promise is resolved, we need to invalidate the cache for the section.
    return promise.then(() => {
        sectionsModulesPromises.clear();
        return true;
    });
};

/**
 * Fetch all the information on modules we'll need in the activity chooser.
 *
 * @method fetchFooterData
 * @param {Number} courseid What course to fetch the data for
 * @param {Number} sectionid What section to fetch the data for
 * @return {object} jQuery promise
 */
export const fetchFooterData = (courseid, sectionid) => {
    const request = {
        methodname: 'core_course_get_activity_chooser_footer',
        args: {
            courseid: courseid,
            sectionid: sectionid,
        },
    };
    return ajax.call([request])[0];
};

/**
 * Legacy method to fetch all the information on modules using section number.
 *
 * @method fetchSectionModules
 *
 * @deprecated since Moodle 5.1
 * @todo Remove in Moodle 6.0 (MDL-86310)
 * @param {Number} courseId Course ID.
 * @param {Number} sectionNum Section number.
 * @param {Number} sectionReturnNum Section return.
 * @param {Number} beforeMod Before module number to be used in the module.
 * @return {Object} Tab data.
 */
export async function getModulesData(courseId, sectionNum, sectionReturnNum, beforeMod) {
    Log.debug('The getModulesData function is deprecated. Use getSectionModulesData instead.');
    const cacheKey = `${courseId}-${sectionNum}`;
    if (!sectionsModulesPromises.has(cacheKey)) {
        sectionsModulesPromises.set(
            cacheKey,
            new Promise((resolve) => {
                resolve(activityModules(courseId, sectionNum));
            })
        );
    }

    const moduleData = await sectionsModulesPromises.get(cacheKey);

    // Early return if there is no module data.
    if (!moduleData) {
        throw new Error('Cannot fetch module data');
    }

    // Apply the section num to all the module instance links.
    return sectionMapper(
        moduleData,
        null, // We do not have a section ID here.
        sectionReturnNum,
        beforeMod,
        sectionNum, // Legacy section number.
    );
}

/**
 * Fetch all the information on modules we'll need in the activity chooser.
 *
 * @method fetchSectionModules
 *
 * @param {Number} courseId Course ID.
 * @param {Number} sectionId Section ID.
 * @param {Number} sectionReturnNum Section return.
 * @param {Number} beforeMod Before module number to be used in the module.
 * @return {Object} Tab data.
 */
export async function getSectionModulesData(courseId, sectionId, sectionReturnNum, beforeMod) {
    const cacheKey = `${courseId}-${sectionId}`;
    if (!sectionsModulesPromises.has(cacheKey)) {
        sectionsModulesPromises.set(
            cacheKey,
            new Promise((resolve) => {
                resolve(sectionActivityModules(courseId, sectionId));
            })
        );
    }

    const moduleData = await sectionsModulesPromises.get(cacheKey);

    // Early return if there is no module data.
    if (!moduleData) {
        throw new Error('Cannot fetch module data');
    }

    // Apply the section num to all the module instance links.
    return sectionMapper(
        moduleData,
        sectionId,
        sectionReturnNum,
        beforeMod,
    );
}

/**
 * Given the web service data and an ID we want to make a deep copy
 * of the WS data then add on the section num to the addoption URL
 *
 * @method sectionMapper
 * @TODO remove legacySectionNum param in Moodle 6.0 (MDL-86310)
 * @param {Object} webServiceData Our original data from the Web service call
 * @param {Number} sectionId The number of the section we need to append to the links
 * @param {Number|null} sectionReturnNum The number of the section return we need to append to the links
 * @param {Number|null} beforeMod The ID of the cm we need to append to the links
 * @param {Number|null} legacySectionNum The legacy section number to append to the links
 * @return {Array} [modules] with URL's built
 */
function sectionMapper(webServiceData, sectionId, sectionReturnNum, beforeMod, legacySectionNum = null) {
    // We need to take a fresh deep copy of the original data as an object is a reference type.
    const newData = JSON.parse(JSON.stringify(webServiceData));
    let urlParams = '&beforemod=' + (beforeMod ?? 0);
    if (sectionId) {
        urlParams += `&sectionid=${sectionId}`;
    }
    // Todo: Remove legacySectionNum in Moodle 6.0 (MDL-86310).
    if (legacySectionNum) {
        urlParams += `&section=${legacySectionNum}`;
    }
    if (sectionReturnNum) {
        urlParams += `&sr=${sectionReturnNum}`;
    }
    newData.content_items.forEach((module) => {
        module.link += urlParams;
    });
    return newData.content_items;
}

/**
 * Fetch the footer data for a specific section.
 *
 * @param {Number} courseId Course ID.
 * @param {Number} sectionNum Section number.
 * @return {Promise<Object>} Promise resolved with the footer data.
 */
export async function getModalFooterData(courseId, sectionNum) {
    const cacheKey = `${courseId}-${sectionNum}`;
    if (sectionsFooterPromises.has(cacheKey)) {
        return sectionsFooterPromises.get(cacheKey);
    }

    sectionsFooterPromises.set(
        cacheKey,
        new Promise((resolve) => {
            resolve(fetchFooterData(courseId, sectionNum));
        })
    );
    return sectionsFooterPromises.get(cacheKey);
}
