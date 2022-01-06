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
 * Defines the function to be used from the mobile course view template.
 *
 * @copyright   2019 Dani Palou <dpalou@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const multipleEnrol = this.CONTENT_OTHERDATA.multipleenrollmentspossible;
const allowOffline = true;

if (Array.isArray(this.CONTENT_OTHERDATA.data) && this.CONTENT_OTHERDATA.data.length == 0) {
    // When there are no responses we receive an empty array instead of an empty object. Fix it.
    this.CONTENT_OTHERDATA.data = {};
}

const originalData = this.CoreUtilsProvider.clone(this.CONTENT_OTHERDATA.data);

/**
 * Send responses to the site.
 */
this.submitResponses = () => {
    let promise;

    if (!this.CONTENT_OTHERDATA.allowupdate) {
        // Ask the user to confirm.
        promise = this.CoreDomUtilsProvider.showConfirm(this.TranslateService.instant('core.areyousure'));
    } else {
        // No need to confirm.
        promise = Promise.resolve();
    }

    promise.then(() => {
        // Submit the responses now.
        return this.CoreDomUtilsProvider.showModalLoading('core.sending', true);
    }).then((modal) => {
        var data = this.CoreUtilsProvider.objectToArrayOfObjects(this.CONTENT_OTHERDATA.data, 'name', 'value');

        if (multipleEnrol) {
            // In multiple enrol, the WS expects to receive 'true' as a string instead of 1 or 0.
            data.forEach((entry) => {
                entry.value = String(entry.value);
            });
        }

        return this.choiceGroupProvider.submitResponses(this.module.instance, this.module.name, this.courseId, this.module.id, data,
                allowOffline).then((online) => {

            // Responses have been sent to server or stored to be sent later.
            this.CoreDomUtilsProvider.showToast(this.TranslateService.instant('plugin.mod_choicegroup.choicegroupsaved'));

            if (online) {
                // Check completion since it could be configured to complete once the user answers the choice.
                this.CoreCourseProvider.checkModuleCompletion(this.courseId, this.module.completiondata);

                // Data has been sent, refresh the content.
                return this.refreshContent(true);
            } else {
                // Data stored in offline.
                return this.loadOfflineData();
            }

        }).catch((message) => {
            this.CoreDomUtilsProvider.showErrorModalDefault(message, 'Error submitting responses.', true);
        }).finally(() => {
            modal.dismiss();
        });
    }).catch(() => {
        // User cancelled, ignore.
    });
};

/**
 * Delete the responses. Only if multiple enrol is not allowed.
 */
this.deleteResponses = () => {
    return this.CoreDomUtilsProvider.showModalLoading('core.sending', true).then((modal) => {
        return this.choiceGroupProvider.deleteResponses(this.module.instance, this.module.name, this.courseId, this.module.id,
                allowOffline).then((online) => {

            // Responses have been sent to server or stored to be sent later.
            this.CoreDomUtilsProvider.showToast(this.TranslateService.instant('plugin.mod_choicegroup.choicegroupsaved'));

            if (online) {
                // Data has been sent, refresh the content.
                return this.refreshContent(true);
            } else {
                // Data stored in offline.
                return this.loadOfflineData();
            }

        }).catch((message) => {
            this.CoreDomUtilsProvider.showErrorModalDefault(message, 'Error deleting responses.', true);
        }).finally(() => {
            modal.dismiss();
        });
    });
};

/**
 * Check if the activity has offline data to be sent.
 *
 * @return Promise resolved when done.
 */
this.loadOfflineData = () => {
    // Get the offline response if it exists.
    return this.choiceGroupOffline.getResponse(this.module.instance).then((response) => {
        this.hasOffline = true;

        if (response.deleting) {
            // Uncheck selected option. Delete is only possible if there is no multiple enrolment.
            delete this.CONTENT_OTHERDATA.data.responses;
            this.showDelete = false;
        } else {
            // Load the offline options into the model.
            this.CONTENT_OTHERDATA.data = {};

            response.data.forEach((entry) => {
                this.CONTENT_OTHERDATA.data[entry.name] = entry.value;
            });

            this.showDelete = !multipleEnrol; // Show delete if there is offline data and is not multiple enrol.
        }
    }).catch(() => {
        // Offline data not found. Use the original data.
        this.hasOffline = false;
        this.showDelete = this.CONTENT_OTHERDATA.answergiven;
        this.CONTENT_OTHERDATA.data = this.CoreUtilsProvider.clone(originalData);
    });
};

/**
 * Tries to synchronize the activity.
 *
 * @param showErrors If show errors to the user of hide them.
 * @param done Function to call when done.
 * @return Promise resolved with true if sync succeed, or false if failed.
 */
this.synchronize = (showErrors, done) => {
    this.refreshIcon = this.CoreConstants.ICON_LOADING;
    this.syncIcon = this.CoreConstants.ICON_LOADING;

    // Try to synchronize the group choice.
    return this.choiceGroupSync.syncChoiceGroup(this.module.instance).then((result) => {
        if (result.warnings && result.warnings.length) {
            this.CoreDomUtilsProvider.showErrorModal(result.warnings[0]);
        }

        return result.updated;
    }).catch((error) => {
        if (showErrors) {
            this.CoreDomUtilsProvider.showErrorModalDefault(error, 'core.errorsync', true);
        }

        return false;
    }).then((updated) => {
        if (updated) {
            // Data has been sent, fetch the content (WS data has already been updated in the sync process).
            return this.fetchContent(false);
        }

        // Check if the group choice has offline data.
        return this.loadOfflineData();
    }).finally(() => {
        done && done();
        this.refreshIcon = this.CoreConstants.ICON_REFRESH;
        this.syncIcon = this.CoreConstants.ICON_SYNC;
    });
};

/**
 * Refresh data.
 *
 * @param done Function to call when done.
 * @return Promise resolved when done.
 */
this.doRefresh = (done) => {
    this.refreshIcon = this.CoreConstants.ICON_LOADING;
    this.syncIcon = this.CoreConstants.ICON_LOADING;

    return this.refreshContent(false).finally(() => {
        done && done();
        this.refreshIcon = this.CoreConstants.ICON_REFRESH;
        this.syncIcon = this.CoreConstants.ICON_SYNC;
    });
};

this.moduleName = this.TranslateService.instant('plugin.mod_choicegroup.modulename');
this.isOnline = this.CoreAppProvider.isOnline();

// Refresh online status when changes.
const onlineObserver = this.Network.onChange().subscribe(() => {
    this.isOnline = this.CoreAppProvider.isOnline();
});

let syncObserver;

if (allowOffline) {
    // Try to synchronize the choice.
    this.synchronize(false).finally(() => {
        this.loaded = true;
    });

    // Update the view if the group choice is synchronized automatically.
    syncObserver = this.CoreEventsProvider.on(this.choiceGroupSync.AUTO_SYNCED, (data) => {
        if (data.choiceGroupId == this.module.instance) {
            // This group choice has been synchronized, fetch the content (WS data has already been updated in the sync process).
            return this.fetchContent(false);
        }
    }, this.CoreSitesProvider.getCurrentSiteId());
} else {
    // No offline allowed, just display the data.
    this.loaded = true;
    this.refreshIcon = this.CoreConstants.ICON_REFRESH;
    this.hasOffline = false;
    this.showDelete = this.CONTENT_OTHERDATA.answergiven;
}

/**
 * Component being destroyed.
 */
this.ngOnDestroy = () => {
    onlineObserver && onlineObserver.unsubscribe();
    syncObserver && syncObserver.off();
};
