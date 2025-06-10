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
 * This file is part of the Moodle apps support for the choicegroup plugin.
 * Defines some "providers" in the app init process so they can be used by all group choices.
 *
 * @copyright   2019 Dani Palou <dpalou@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

var that = this;

/**
 * Offline provider.
 */

var CHOICEGROUP_TABLE = 'addon_mod_choicegroup_responses';

// Define the database tables.
var siteSchema = {
    name: 'AddonModChoiceGroupOfflineProvider',
    version: 1,
    onlyCurrentSite: true,
    tables: [
        {
            name: CHOICEGROUP_TABLE,
            columns: [
                {
                    name: 'choicegroupid',
                    type: 'INTEGER',
                    primaryKey: true
                },
                {
                    name: 'name',
                    type: 'TEXT'
                },
                {
                    name: 'courseid',
                    type: 'INTEGER'
                },
                {
                    name: 'cmid',
                    type: 'INTEGER'
                },
                {
                    name: 'data',
                    type: 'TEXT'
                },
                {
                    name: 'deleting',
                    type: 'INTEGER'
                },
                {
                    name: 'timecreated',
                    type: 'INTEGER'
                }
            ]
        }
    ]
};

/**
 * Class to handle offline group choices.
 */
function AddonModChoiceGroupOfflineProvider() {
    // Register the schema so the tables are created.
    that.CoreSitesProvider.registerSiteSchema(siteSchema);
}

/**
 * Delete a response stored in DB.
 *
 * @param id Group choice ID to remove.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved if deleted, rejected if failure.
 */
AddonModChoiceGroupOfflineProvider.prototype.deleteResponse = function(id, siteId) {
    return that.CoreSitesProvider.getSite(siteId).then(function(site) {

        return site.getDb().deleteRecords(CHOICEGROUP_TABLE, {choicegroupid: id});
    });
};

/**
 * Get all offline responses.
 *
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved with responses.
 */
AddonModChoiceGroupOfflineProvider.prototype.getResponses = function(siteId) {
    return that.CoreSitesProvider.getSite(siteId).then(function(site) {
        return site.getDb().getRecords(CHOICEGROUP_TABLE).then(function(records) {
            // Parse the data of each record.
            records.forEach(function(record) {
                record.data = that.CoreTextUtilsProvider.parseJSON(record.data, []);
            });

            return records;
        });
    });
};

/**
 * Check if there are offline responses to send.
 *
 * @param id Group choice ID.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved with boolean: true if has offline answers, false otherwise.
 */
AddonModChoiceGroupOfflineProvider.prototype.hasResponse = function(id, siteId) {
    return this.getResponse(id, siteId).then(function(response) {
        return !!response.choicegroupid;
    }).catch(function() {
        // No offline data found, return false.
        return false;
    });
};

/**
 * Get an offline response.
 *
 * @param id Group choice ID to get.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved with the stored data.
 */
AddonModChoiceGroupOfflineProvider.prototype.getResponse = function(id, siteId) {
    return that.CoreSitesProvider.getSite(siteId).then(function(site) {

        return site.getDb().getRecord(CHOICEGROUP_TABLE, {choicegroupid: id}).then(function(record) {
            // Parse the data.
            record.data = that.CoreTextUtilsProvider.parseJSON(record.data, []);

            return record;
        });
    });
};

/**
 * Store a response to a group choice.
 *
 * @param id Group choice ID.
 * @param name Group choice name.
 * @param courseId Course ID the group choice belongs to.
 * @param cmId Course module ID.
 * @param data List of selected options.
 * @param deleting If true, the user is deleting responses, if false, submitting.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved when data is successfully stored.
 */
AddonModChoiceGroupOfflineProvider.prototype.saveResponses = function(id, name, courseId, cmId, data, deleting, siteId) {
    data = data || [];

    return that.CoreSitesProvider.getSite(siteId).then(function(site) {
        var entry = {
            choicegroupid: id,
            name: name,
            courseid: courseId,
            cmid: cmId,
            data: JSON.stringify(data),
            deleting: deleting ? 1 : 0,
            timecreated: Date.now()
        };

        return site.getDb().insertRecord(CHOICEGROUP_TABLE, entry);
    });
};

var choiceGroupOffline = new AddonModChoiceGroupOfflineProvider();

/**
 * Group choice provider.
 */

/**
 * Class to handle group choices.
 */
function AddonModChoiceGroupProvider() { }

/**
 * Delete responses from a group choice.
 *
 * @param id Group choice ID to remove.
 * @param name The group choice name.
 * @param courseId Course ID the group choice belongs to.
 * @param cmId Course module ID.
 * @param allowOffline Whether to allow storing the data in offline.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved with boolean: true if deleted in server, false if stored in offline. Rejected if failure.
 */
AddonModChoiceGroupProvider.prototype.deleteResponses = function(id, name, courseId, cmId, allowOffline, siteId) {
    siteId = siteId || that.CoreSitesProvider.getCurrentSiteId();

    var self = this;

    // Convenience function to store the delete to be synchronized later.
    var storeOffline = function() {
        return choiceGroupOffline.saveResponses(id, name, courseId, cmId, undefined, true, siteId).then(function() {
            return false;
        });
    };

    if (!that.CoreAppProvider.isOnline() && allowOffline) {
        // App is offline, store the action.
        return storeOffline();
    }

    // If there's already some data to be sent to the server, discard it first.
    return choiceGroupOffline.deleteResponse(id, siteId).catch(function() {
        // Nothing was stored already.
    }).then(function() {
        // Now try to delete the responses in the server.
        return self.deleteResponsesOnline(id, siteId).then(function() {
            return true;
        }).catch(function(error) {
            if (!allowOffline || that.CoreUtilsProvider.isWebServiceError(error)) {
                // The WebService has thrown an error, this means that responses cannot be submitted.
                return Promise.reject(error);
            }

            // Couldn't connect to server, store in offline.
            return storeOffline();
        });
    });
};

/**
 * Delete responses from a group choice. It will fail if offline or cannot connect.
 *
 * @param id Group choice ID to remove.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved if deleted, rejected if failure.
 */
AddonModChoiceGroupProvider.prototype.deleteResponsesOnline = function(id, siteId) {
    return that.CoreSitesProvider.getSite(siteId).then(function(site) {
        var params = {
            choicegroupid: id
        };

        return site.write('mod_choicegroup_delete_choicegroup_responses', params).then(function(response) {

            if (!response || response.status === false) {
                // Couldn't delete the responses. Reject the promise.
                var error = response && response.warnings && response.warnings[0] ?
                        response.warnings[0] : that.CoreUtilsProvider.createFakeWSError('');

                return Promise.reject(error);
            }
        });
    });
};

/**
 * Send the responses to a group choice.
 *
 * @param id Group choice ID to submit.
 * @param name The group choice name.
 * @param courseId Course ID the group choice belongs to.
 * @param cmId Course module ID.
 * @param data The responses to send.
 * @param allowOffline Whether to allow storing the data in offline.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved with boolean: true if responses sent to server, false if stored in offline. Rejected if failure.
 */
AddonModChoiceGroupProvider.prototype.submitResponses = function(id, name, courseId, cmId, data, allowOffline, siteId) {
    siteId = siteId || that.CoreSitesProvider.getCurrentSiteId();

    var self = this;

    // Convenience function to store the delete to be synchronized later.
    var storeOffline = function() {
        return choiceGroupOffline.saveResponses(id, name, courseId, cmId, data, false, siteId).then(function() {
            return false;
        });
    };

    if (!that.CoreAppProvider.isOnline() && allowOffline) {
        // App is offline, store the action.
        return storeOffline();
    }

    // If there's already some data to be sent to the server, discard it first.
    return choiceGroupOffline.deleteResponse(id, siteId).catch(function() {
        // Nothing was stored already.
    }).then(function() {
        // Now try to delete the responses in the server.
        return self.submitResponsesOnline(id, data, siteId).then(function() {
            return true;
        }).catch(function(error) {
            if (!allowOffline || that.CoreUtilsProvider.isWebServiceError(error)) {
                // The WebService has thrown an error, this means that responses cannot be submitted.
                return Promise.reject(error);
            }

            // Couldn't connect to server, store in offline.
            return storeOffline();
        });
    });
};

/**
 * Send responses from a group choice to Moodle. It will fail if offline or cannot connect.
 *
 * @param id Group choice ID to submit.
 * @param data The responses to send.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved if deleted, rejected if failure.
 */
AddonModChoiceGroupProvider.prototype.submitResponsesOnline = function(id, data, siteId) {
    return that.CoreSitesProvider.getSite(siteId).then(function(site) {
        var params = {
            choicegroupid: id,
            data: data
        };

        return site.write('mod_choicegroup_submit_choicegroup_response', params).then(function(response) {

            if (!response || response.status === false) {
                // Couldn't delete the responses. Reject the promise.
                var error = response && response.warnings && response.warnings[0] ?
                        response.warnings[0] : that.CoreUtilsProvider.createFakeWSError('');

                return Promise.reject(error);
            }
        });
    });
};

var choiceGroupProvider = new AddonModChoiceGroupProvider();

/**
 * Group choice sync provider.
 */

/**
 * Class to handle group choice sync.
 */
function AddonModChoiceGroupSyncProvider() {
    // Inherit from sync base provider.
    that.CoreSyncBaseProvider.call(this, 'AddonModChoiceGroupSyncProvider', that.CoreLoggerProvider, that.CoreSitesProvider,
            that.CoreAppProvider, that.CoreSyncProvider, that.CoreTextUtilsProvider, that.TranslateService,
            that.CoreTimeUtilsProvider);

    this.AUTO_SYNCED = 'addon_mod_choicegroup_autom_synced';
}

AddonModChoiceGroupSyncProvider.prototype = Object.create(this.CoreSyncBaseProvider.prototype);
AddonModChoiceGroupSyncProvider.prototype.constructor = AddonModChoiceGroupSyncProvider;

/**
 * Try to synchronize all the group choices in a certain site or in all sites.
 *
 * @param force Wether to force sync not depending on last execution.
 * @return Promise resolved if sync is successful, rejected if sync fails.
 */
AddonModChoiceGroupSyncProvider.prototype.syncAllChoiceGroups = function(force) {
    return this.syncOnSites('group choices', this.syncAllChoiceGroupsFunc.bind(this), [force],
            that.CoreSitesProvider.getCurrentSiteId());
};

/**
 * Sync all pending group choices on a site.
 *
 * @param siteId Site ID to sync.
 * @param force Wether to force sync not depending on last execution.
 * @return Promise resolved if sync is successful, rejected if sync fails.
 */
AddonModChoiceGroupSyncProvider.prototype.syncAllChoiceGroupsFunc = function(siteId, force) {
    var self = this;

    return choiceGroupOffline.getResponses(siteId).then(function(responses) {
        // Sync all responses.
        var promises = responses.map(function(response) {
            var promise = force ? self.syncChoiceGroup(response.choicegroupid, siteId) :
                    self.syncChoiceGroupIfNeeded(response.choicegroupid, siteId);

            return promise.then(function(result) {
                if (result && result.updated) {
                    // Sync successful, send event.
                    that.CoreEventsProvider.trigger(self.AUTO_SYNCED, {
                        choiceGroupId: response.choicegroupid,
                        warnings: result.warnings
                    }, siteId);
                }
            });
        });

        return Promise.all(promises);
    });
};

/**
 * Sync a group choice only if a certain time has passed since the last time.
 *
 * @param id Group choice ID to be synced.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved when the group choice is synced or it doesn't need to be synced.
 */
AddonModChoiceGroupSyncProvider.prototype.syncChoiceGroupIfNeeded = function(id, siteId) {
    var self = this;

    return this.isSyncNeeded(id, siteId).then(function(needed) {
        if (needed) {
            return self.syncChoiceGroup(id, siteId);
        }
    });
};

/**
 * Synchronize a group choice.
 *
 * @param id Group choice ID to be synced.
 * @param siteId Site ID. If not defined, current site.
 * @return Promise resolved if sync is successful, rejected otherwise.
 */
AddonModChoiceGroupSyncProvider.prototype.syncChoiceGroup = function(id, siteId) {
    var self = this;

    return that.CoreSitesProvider.getSite(siteId).then(function(site) {
        siteId = site.getId();

        if (self.isSyncing(id, siteId)) {
            // There's already a sync ongoing for this group choice, return the promise.
            return self.getOngoingSync(id, siteId);
        }

        self.logger.debug('Try to sync group choice ' + id);

        var courseId;
        var cmId;
        var result = {
            warnings: [],
            updated: false
        };

        // Get the data to synchronize.
        return choiceGroupOffline.getResponse(id, siteId).catch(function() {
            // No offline data found, return empty object.
            return {};
        }).then(function(data) {
            if (!data.choicegroupid) {
                // Nothing to sync.
                return;
            }

            if (!that.CoreAppProvider.isOnline()) {
                // Cannot sync in offline.
                return Promise.reject(null);
            }

            courseId = data.courseid;
            cmId = data.cmid;

            // Send the responses.
            var promise;

            if (data.deleting) {
                // The user has deleted his responses.
                promise = choiceGroupProvider.deleteResponsesOnline(id, siteId);
            } else {
                // The user has added a response.
                promise = choiceGroupProvider.submitResponsesOnline(id, data.data, siteId);
            }

            return promise.then(function() {
                // Success sending the data. Delete the data stored.
                result.updated = true;

                return choiceGroupOffline.deleteResponse(id, siteId);
            }).catch(function(error) {
                if (that.CoreUtilsProvider.isWebServiceError(error)) {
                    // The WebService has thrown an error, this means that responses cannot be submitted. Delete them.
                    result.updated = true;

                    return choiceGroupOffline.deleteResponse(id, siteId).then(function() {
                        // Responses deleted, add a warning.
                        result.warnings.push(that.TranslateService.instant('core.warningofflinedatadeleted', {
                            component: that.TranslateService.instant('plugin.mod_choicegroup.modulename'),
                            name: data.name,
                            error: that.CoreTextUtilsProvider.getErrorMessageFromError(error)
                        }));
                    });
                }

                // Couldn't connect to server, reject.
                return Promise.reject(error);
            });
        }).then(function() {
            if (result.updated) {
                // Data has been sent to server, refresh the data.
                var args = {
                    courseid: courseId,
                    cmid: cmId
                };
                var preSets = {
                    getFromCache: false,
                    emergencyCache: false
                };

                return that.CoreSitePluginsProvider.getContent('mod_choicegroup', 'mobile_course_view', args, preSets)
                        .catch(function() {
                    // Ignore errors.
                });
            }
        }).then(function() {
            // Sync finished, set sync time.
            return self.setSyncTime(id, siteId);
        }).then(function() {
            // All done, return the result.
            return result;
        });

        return self.addOngoingSync(id, syncPromise, siteId);
    });
};

var choiceGroupSync = new AddonModChoiceGroupSyncProvider();

/**
 * Group choice sync handler. It will be registered in the cron delegate.
 */

/**
 * Handler to trigger group choice sync.
 */
function AddonModChoiceGroupSyncCronHandler() {
    this.name = 'AddonModChoiceGroupSyncCronHandler';
}

/**
 * Execute the process.
 *
 * @param siteId ID of the site affected, undefined for all sites.
 * @param force Wether the execution is forced (manual sync).
 * @return Promise resolved when done, rejected if failure.
 */
AddonModChoiceGroupSyncCronHandler.prototype.execute = function(siteId, force) {
    // Only allow synchronizing current site.
    if (!siteId || siteId == that.CoreSitesProvider.getCurrentSiteId()) {
        return choiceGroupSync.syncAllChoiceGroups(force);
    }
};

/**
 * Get the time between consecutive executions.
 *
 * @return Time between consecutive executions (in ms).
 */
AddonModChoiceGroupSyncCronHandler.prototype.getInterval = function() {
    return choiceGroupSync.syncInterval;
};

/**
 * Link handler to treat links to a group choice.
 */

/**
 * Handler to treat links to the index page.
 */
function AddonModChoiceGroupLinkHandler() {
    that.CoreContentLinksModuleIndexHandler.call(this, that.CoreCourseHelperProvider, 'AddonModChoiceGroup', 'choicegroup');

    this.name = "AddonModChoiceGroupLinkHandler";
}

AddonModChoiceGroupLinkHandler.prototype = Object.create(this.CoreContentLinksModuleIndexHandler.prototype);
AddonModChoiceGroupLinkHandler.prototype.constructor = AddonModChoiceGroupLinkHandler;

// Register the sync handler. Wait a bit to make sure the DB tables are created.
setTimeout(function() {
    that.CoreCronDelegate.register(new AddonModChoiceGroupSyncCronHandler());
}, 500);

// Register the link handler.
this.CoreContentLinksDelegate.registerHandler(new AddonModChoiceGroupLinkHandler());

var result = {
    choiceGroupProvider: choiceGroupProvider,
    choiceGroupOffline: choiceGroupOffline,
};

if (this.CoreConfigConstants.versioncode > 3800) {
    // 3.8.0 and older versions of the app have a bug when returning classes with Angular dependencies.
    // Only return the sync provider if the version is newer.
    result.choiceGroupSync = choiceGroupSync;
}

result;
