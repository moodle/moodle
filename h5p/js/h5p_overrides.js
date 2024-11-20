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

H5P._getLibraryPath = H5P.getLibraryPath;
H5P.getLibraryPath = function (library) {
    if (H5PIntegration.moodleLibraryPaths) {
        if (H5PIntegration.moodleLibraryPaths[library]) {
            return H5PIntegration.moodleLibraryPaths[library];
        }
    }
    return H5P._getLibraryPath(library);
};
H5P.findInstanceFromId = function (contentId) {
    if (!contentId) {
        return H5P.instances[0];
    }
    if (H5P.instances !== undefined) {
        for (var i = 0; i < H5P.instances.length; i++) {
            if (H5P.instances[i].contentId === contentId) {
                return H5P.instances[i];
            }
        }
    }
    return undefined;
};
H5P.getXAPIStatements = function (contentId, statement) {
    var statements = [];
    var instance = H5P.findInstanceFromId(contentId);
    if (!instance){
        return statements;
    }
    if (instance.getXAPIData == undefined) {
        var xAPIData = {
            statement: statement
        };
    } else {
        var xAPIData = instance.getXAPIData();
    }
    if (xAPIData.statement != undefined) {
        statements.push(xAPIData.statement);
    }
    if (xAPIData.children != undefined) {
        statements = statements.concat(xAPIData.children.map(a => a.statement));
    }
    return statements;
};
H5P.getMoodleComponent = function () {
    if (H5PIntegration.moodleComponent) {
        return H5PIntegration.moodleComponent;
    }
    return undefined;
};

/**
 * Set the actor. (Moved to overrides due to MDL-69467)
 */
H5P.XAPIEvent.prototype.setActor = function () {
    if (H5PIntegration.user !== undefined) {
        this.data.statement.actor = {
            'name': H5PIntegration.user.name,
            'objectType': 'Agent'
        };
        if (H5PIntegration.user.id !== undefined) {
            this.data.statement.actor.account = {
                'name': H5PIntegration.user.id,
                'homePage': H5PIntegration.siteUrl
            }
        } else if (H5PIntegration.user.mail !== undefined) {
            this.data.statement.actor.mbox = 'mailto:' + H5PIntegration.user.mail;
        }
    } else {
        var uuid;
        try {
            if (localStorage.H5PUserUUID) {
                uuid = localStorage.H5PUserUUID;
            } else {
                uuid = H5P.createUUID();
                localStorage.H5PUserUUID = uuid;
            }
        }
        catch (err) {
            // LocalStorage and Cookies are probably disabled. Do not track the user.
            uuid = 'not-trackable-' + H5P.createUUID();
        }
        this.data.statement.actor = {
            'account': {
                'name': uuid,
                'homePage': H5PIntegration.siteUrl
            },
            'objectType': 'Agent'
        };
    }
};

/**
 * Get the actor.
 *
 * @returns {Object} The Actor object.
 */
H5P.getxAPIActor = function() {
    var actor = null;
    if (H5PIntegration.user !== undefined) {
        actor = {
            'name': H5PIntegration.user.name,
            'objectType': 'Agent'
        };
        if (H5PIntegration.user.id !== undefined) {
            actor.account = {
                'name': H5PIntegration.user.id,
                'homePage': H5PIntegration.siteUrl
            };
        } else if (H5PIntegration.user.mail !== undefined) {
            actor.mbox = 'mailto:' + H5PIntegration.user.mail;
        }
    } else {
        var uuid;
        try {
            if (localStorage.H5PUserUUID) {
                uuid = localStorage.H5PUserUUID;
            } else {
                uuid = H5P.createUUID();
                localStorage.H5PUserUUID = uuid;
            }
        } catch (err) {
            // LocalStorage and Cookies are probably disabled. Do not track the user.
            uuid = 'not-trackable-' + H5P.createUUID();
        }
        actor = {
            'account': {
                'name': uuid,
                'homePage': H5PIntegration.siteUrl
            },
            'objectType': 'Agent'
        };
    }
    return actor;
};

/**
 * Creates requests for inserting, updating and deleting content user data.
 * It overrides the contentUserDataAjax private method in h5p.js.
 *
 * @param {number} contentId What content to store the data for.
 * @param {string} dataType Identifies the set of data for this content.
 * @param {string} subContentId Identifies sub content
 * @param {function} [done] Callback when ajax is done.
 * @param {object} [data] To be stored for future use.
 * @param {boolean} [preload=false] Data is loaded when content is loaded.
 * @param {boolean} [invalidate=false] Data is invalidated when content changes.
 * @param {boolean} [async=true]
 */
H5P.contentUserDataAjax = function(contentId, dataType, subContentId, done, data, preload, invalidate, async) {
    var instance = H5P.findInstanceFromId(contentId);
    if (instance !== undefined) {
        var xAPIState = {
            activityId: H5P.XAPIEvent.prototype.getContentXAPIId(instance),
            stateId: dataType,
            state: data
        };
        H5P.externalDispatcher.trigger('xAPIState', xAPIState);
    }
};
