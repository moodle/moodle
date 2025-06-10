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

const context = this;

/**
 * Offline provider.
 */

const CHOICEGROUP_TABLE = 'addon_mod_choicegroup_responses';
let waitDBReady;

// Define the database tables.
const siteSchema = {
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
class AddonModChoiceGroupOfflineProvider {

    constructor() {
        // Register the schema so the tables are created.
        waitDBReady = context.CoreSitesProvider.registerSiteSchema(siteSchema);
    }

    /**
     * Delete a response stored in DB.
     *
     * @param id Group choice ID to remove.
     * @param siteId Site ID. If not defined, current site.
     * @return Promise resolved if deleted, rejected if failure.
     */
    deleteResponse(id, siteId) {
        return context.CoreSitesProvider.getSite(siteId).then((site) => {
            return site.getDb().deleteRecords(CHOICEGROUP_TABLE, {choicegroupid: id});
        });
    }

    /**
     * Get all offline responses.
     *
     * @param siteId Site ID. If not defined, current site.
     * @return Promise resolved with responses.
     */
    getResponses(siteId) {
        return context.CoreSitesProvider.getSite(siteId).then((site) => {
            return site.getDb().getRecords(CHOICEGROUP_TABLE).then((records) => {
                // Parse the data of each record.
                records.forEach((record) => {
                    record.data = context.CoreTextUtilsProvider.parseJSON(record.data, []);
                });

                return records;
            });
        });
    }

    /**
     * Check if there are offline responses to send.
     *
     * @param id Group choice ID.
     * @param siteId Site ID. If not defined, current site.
     * @return Promise resolved with boolean: true if has offline answers, false otherwise.
     */
    hasResponse(id, siteId) {
        return this.getResponse(id, siteId).then((response) => {
            return !!response.choicegroupid;
        }).catch(() => {
            // No offline data found, return false.
            return false;
        });
    }

    /**
     * Get an offline response.
     *
     * @param id Group choice ID to get.
     * @param siteId Site ID. If not defined, current site.
     * @return Promise resolved with the stored data.
     */
    getResponse(id, siteId) {
        return context.CoreSitesProvider.getSite(siteId).then((site) => {

            return site.getDb().getRecord(CHOICEGROUP_TABLE, {choicegroupid: id}).then((record) => {
                // Parse the data.
                record.data = context.CoreTextUtilsProvider.parseJSON(record.data, []);

                return record;
            });
        });
    }

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
    saveResponses(id, name, courseId, cmId, data, deleting, siteId) {
        data = data || [];

        return context.CoreSitesProvider.getSite(siteId).then((site) => {
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
    }
}

const choiceGroupOffline = new AddonModChoiceGroupOfflineProvider();

/**
 * Group choice provider.
 */

/**
 * Class to handle group choices.
 */
class AddonModChoiceGroupProvider {

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
    deleteResponses(id, name, courseId, cmId, allowOffline, siteId) {
        siteId = siteId || context.CoreSitesProvider.getCurrentSiteId();

        // Convenience function to store the delete to be synchronized later.
        var storeOffline = () => {
            return choiceGroupOffline.saveResponses(id, name, courseId, cmId, undefined, true, siteId).then(() => {
                return false;
            });
        };

        if (!context.CoreAppProvider.isOnline() && allowOffline) {
            // App is offline, store the action.
            return storeOffline();
        }

        // If there's already some data to be sent to the server, discard it first.
        return choiceGroupOffline.deleteResponse(id, siteId).catch(() => {
            // Nothing was stored already.
        }).then(() => {
            // Now try to delete the responses in the server.
            return this.deleteResponsesOnline(id, siteId).then(() => {
                return true;
            }).catch((error) => {
                if (!allowOffline || context.CoreUtilsProvider.isWebServiceError(error)) {
                    // The WebService has thrown an error, this means that responses cannot be submitted.
                    return Promise.reject(error);
                }

                // Couldn't connect to server, store in offline.
                return storeOffline();
            });
        });
    }

    /**
     * Delete responses from a group choice. It will fail if offline or cannot connect.
     *
     * @param id Group choice ID to remove.
     * @param siteId Site ID. If not defined, current site.
     * @return Promise resolved if deleted, rejected if failure.
     */
    deleteResponsesOnline(id, siteId) {
        return context.CoreSitesProvider.getSite(siteId).then((site) => {
            var params = {
                choicegroupid: id
            };

            return site.write('mod_choicegroup_delete_choicegroup_responses', params).then((response) => {

                if (!response || response.status === false) {
                    // Couldn't delete the responses. Reject the promise.
                    var error = response && response.warnings && response.warnings[0] ?
                            response.warnings[0] : new context.CoreError('');

                    return Promise.reject(error);
                }
            });
        });
    }

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
    submitResponses(id, name, courseId, cmId, data, allowOffline, siteId) {
        siteId = siteId || context.CoreSitesProvider.getCurrentSiteId();

        // Convenience function to store the delete to be synchronized later.
        var storeOffline = () => {
            return choiceGroupOffline.saveResponses(id, name, courseId, cmId, data, false, siteId).then(() => {
                return false;
            });
        };

        if (!context.CoreAppProvider.isOnline() && allowOffline) {
            // App is offline, store the action.
            return storeOffline();
        }

        // If there's already some data to be sent to the server, discard it first.
        return choiceGroupOffline.deleteResponse(id, siteId).catch(() => {
            // Nothing was stored already.
        }).then(() => {
            // Now try to delete the responses in the server.
            return this.submitResponsesOnline(id, data, siteId).then(() => {
                return true;
            }).catch((error) => {
                if (!allowOffline || context.CoreUtilsProvider.isWebServiceError(error)) {
                    // The WebService has thrown an error, this means that responses cannot be submitted.
                    return Promise.reject(error);
                }

                // Couldn't connect to server, store in offline.
                return storeOffline();
            });
        });
    }

    /**
     * Send responses from a group choice to Moodle. It will fail if offline or cannot connect.
     *
     * @param id Group choice ID to submit.
     * @param data The responses to send.
     * @param siteId Site ID. If not defined, current site.
     * @return Promise resolved if deleted, rejected if failure.
     */
    submitResponsesOnline(id, data, siteId) {
        return context.CoreSitesProvider.getSite(siteId).then((site) => {
            var params = {
                choicegroupid: id,
                data: data
            };

            return site.write('mod_choicegroup_submit_choicegroup_response', params).then((response) => {

                if (!response || response.status === false) {
                    // Couldn't delete the responses. Reject the promise.
                    var error = response && response.warnings && response.warnings[0] ?
                            response.warnings[0] : new context.CoreError('');

                    return Promise.reject(error);
                }
            });
        });
    }

}

const choiceGroupProvider = new AddonModChoiceGroupProvider();

/**
 * Group choice sync provider.
 */

/**
 * Class to handle group choice sync.
 */
class AddonModChoiceGroupSyncProvider extends this.CoreSyncBaseProvider {

    constructor() {
        super('AddonModChoiceGroupSyncProvider');

        this.AUTO_SYNCED = 'addon_mod_choicegroup_autom_synced';
    }

    /**
     * Try to synchronize all the group choices in a certain site or in all sites.
     *
     * @param force Wether to force sync not depending on last execution.
     * @return Promise resolved if sync is successful, rejected if sync fails.
     */
    syncAllChoiceGroups(force) {
        return this.syncOnSites('group choices', this.syncAllChoiceGroupsFunc.bind(this, !!force),
                context.CoreSitesProvider.getCurrentSiteId());
    }

    /*
     * Sync all pending group choices on a site.
     *
     * @param force Wether to force sync not depending on last execution.
     * @param siteId Site ID to sync.
     * @return Promise resolved if sync is successful, rejected if sync fails.
     */
    syncAllChoiceGroupsFunc(force, siteId) {
        return choiceGroupOffline.getResponses(siteId).then((responses) => {
            // Sync all responses.
            var promises = responses.map((response) => {
                var promise = force ? this.syncChoiceGroup(response.choicegroupid, siteId) :
                        this.syncChoiceGroupIfNeeded(response.choicegroupid, siteId);

                return promise.then((result) => {
                    if (result && result.updated) {
                        // Sync successful, send event.
                        context.CoreEventsProvider.trigger(this.AUTO_SYNCED, {
                            choiceGroupId: response.choicegroupid,
                            warnings: result.warnings
                        }, siteId);
                    }
                });
            });

            return Promise.all(promises);
        });
    }

    /**
     * Sync a group choice only if a certain time has passed since the last time.
     *
     * @param id Group choice ID to be synced.
     * @param siteId Site ID. If not defined, current site.
     * @return Promise resolved when the group choice is synced or it doesn't need to be synced.
     */
    syncChoiceGroupIfNeeded(id, siteId) {
        return this.isSyncNeeded(id, siteId).then((needed) => {
            if (needed) {
                return this.syncChoiceGroup(id, siteId);
            }
        });
    }

    /**
     * Synchronize a group choice.
     *
     * @param id Group choice ID to be synced.
     * @param siteId Site ID. If not defined, current site.
     * @return Promise resolved if sync is successful, rejected otherwise.
     */
    syncChoiceGroup(id, siteId) {
        return context.CoreSitesProvider.getSite(siteId).then((site) => {
            siteId = site.getId();

            if (this.isSyncing(id, siteId)) {
                // There's already a sync ongoing for this group choice, return the promise.
                return this.getOngoingSync(id, siteId);
            }

            this.logger.debug('Try to sync group choice ' + id);

            var courseId;
            var cmId;
            var result = {
                warnings: [],
                updated: false
            };

            // Get the data to synchronize.
            return choiceGroupOffline.getResponse(id, siteId).catch(() => {
                // No offline data found, return empty object.
                return {};
            }).then((data) => {
                if (!data.choicegroupid) {
                    // Nothing to sync.
                    return;
                }

                if (!context.CoreAppProvider.isOnline()) {
                    // Cannot sync in offline.
                    return Promise.reject(new context.CoreNetworkError());
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

                return promise.then(() => {
                    // Success sending the data. Delete the data stored.
                    result.updated = true;

                    return choiceGroupOffline.deleteResponse(id, siteId);
                }).catch((error) => {
                    if (context.CoreUtilsProvider.isWebServiceError(error)) {
                        // The WebService has thrown an error, this means that responses cannot be submitted. Delete them.
                        result.updated = true;

                        return choiceGroupOffline.deleteResponse(id, siteId).then(() => {
                            // Responses deleted, add a warning.
                            result.warnings.push(context.TranslateService.instant('core.warningofflinedatadeleted', {
                                component: context.TranslateService.instant('plugin.mod_choicegroup.modulename'),
                                name: data.name,
                                error: context.CoreTextUtilsProvider.getErrorMessageFromError(error)
                            }));
                        });
                    }

                    // Couldn't connect to server, reject.
                    return Promise.reject(error);
                });
            }).then(() => {
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

                    return context.CoreSitePluginsProvider.getContent('mod_choicegroup', 'mobile_course_view', args, preSets)
                            .catch(() => {
                        // Ignore errors.
                    });
                }
            }).then(() => {
                // Sync finished, set sync time.
                return this.setSyncTime(id, siteId);
            }).then(() => {
                // All done, return the result.
                return result;
            });

            return this.addOngoingSync(id, syncPromise, siteId);
        });
    }
}

const choiceGroupSync = new AddonModChoiceGroupSyncProvider();

/**
 * Group choice sync handler. It will be registered in the cron delegate.
 */

/**
 * Handler to trigger group choice sync.
 */
class AddonModChoiceGroupSyncCronHandler {

    constructor() {
        this.name = 'AddonModChoiceGroupSyncCronHandler';
    }

    /**
     * Execute the process.
     *
     * @param siteId ID of the site affected, undefined for all sites.
     * @param force Wether the execution is forced (manual sync).
     * @return Promise resolved when done, rejected if failure.
     */
    execute(siteId, force) {
        // Only allow synchronizing current site.
        if (!siteId || siteId == context.CoreSitesProvider.getCurrentSiteId()) {
            return choiceGroupSync.syncAllChoiceGroups(force);
        }
    }

    /**
     * Get the time between consecutive executions.
     *
     * @return Time between consecutive executions (in ms).
     */
    getInterval() {
        return choiceGroupSync.syncInterval;
    }

}


/**
 * Link handler to treat links to a group choice.
 */

/**
 * Handler to treat links to the index page.
 */
class AddonModChoiceGroupLinkHandler extends this.CoreContentLinksModuleIndexHandler {

    constructor() {
        super('AddonModChoiceGroup', 'choicegroup');

        this.name = 'AddonModChoiceGroupLinkHandler';
    }

}

// Register the sync handler. Wait until the DB tables are created.
waitDBReady.then(() => {
    context.CoreCronDelegate.register(new AddonModChoiceGroupSyncCronHandler());
});

// Register the link handler.
this.CoreContentLinksDelegate.registerHandler(new AddonModChoiceGroupLinkHandler());

const result = {
    choiceGroupProvider: choiceGroupProvider,
    choiceGroupOffline: choiceGroupOffline,
    choiceGroupSync: choiceGroupSync,
};

result;
