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

var that = this;
var allowOffline = this.CoreConfigConstants.versioncode > 3800; // In 3.8.0 and older plugins couldn't add DB schemas.
var multipleEnrol = this.CONTENT_OTHERDATA.multipleenrollmentspossible;

if (Array.isArray(this.CONTENT_OTHERDATA.data) && this.CONTENT_OTHERDATA.data.length == 0) {
    // When there are no responses we receive an empty array instead of an empty object. Fix it.
    this.CONTENT_OTHERDATA.data = {};
}

var originalData = this.CoreUtilsProvider.clone(this.CONTENT_OTHERDATA.data);

/**
 * Send responses to the site.
 */
this.submitResponses = function() {
    var promise;

    if (!that.CONTENT_OTHERDATA.allowupdate) {
        // Ask the user to confirm.
        that.CoreDomUtilsProvider.showConfirm(that.TranslateService.instant('core.areyousure'));
    } else {
        // No need to confirm.
        promise = Promise.resolve();
    }

    promise.then(function() {
        // Submit the responses now.
        var modal = that.CoreDomUtilsProvider.showModalLoading('core.sending', true);
        var data = that.CoreUtilsProvider.objectToArrayOfObjects(that.CONTENT_OTHERDATA.data, 'name', 'value');

        if (multipleEnrol) {
            // In multiple enrol, the WS expects to receive 'true' as a string instead of 1 or 0.
            data.forEach(function(entry) {
                entry.value = String(entry.value);
            });
        }

        that.choiceGroupProvider.submitResponses(that.module.instance, that.module.name, that.courseId, that.module.id, data,
                allowOffline).then(function(online) {

            // Responses have been sent to server or stored to be sent later.
            that.CoreDomUtilsProvider.showToast(that.TranslateService.instant('plugin.mod_choicegroup.choicegroupsaved'));

            if (online) {
                // Check completion since it could be configured to complete once the user answers the choice.
                that.CoreCourseProvider.checkModuleCompletion(that.courseId, that.module.completiondata);

                // Data has been sent, refresh the content.
                return that.refreshContent(true);
            } else {
                // Data stored in offline.
                return that.loadOfflineData();
            }

        }).catch((message) => {
            that.CoreDomUtilsProvider.showErrorModalDefault(message, 'Error submitting responses.', true);
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
this.deleteResponses = function() {
    var modal = that.CoreDomUtilsProvider.showModalLoading('core.sending', true);

    that.choiceGroupProvider.deleteResponses(that.module.instance, that.module.name, that.courseId, that.module.id, allowOffline)
            .then(function(online) {

        // Responses have been sent to server or stored to be sent later.
        that.CoreDomUtilsProvider.showToast(that.TranslateService.instant('plugin.mod_choicegroup.choicegroupsaved'));

        if (online) {
            // Data has been sent, refresh the content.
            return that.refreshContent(true);
        } else {
            // Data stored in offline.
            return that.loadOfflineData();
        }

    }).catch((message) => {
        that.CoreDomUtilsProvider.showErrorModalDefault(message, 'Error deleting responses.', true);
    }).finally(() => {
        modal.dismiss();
    });
};

/**
 * Check if the activity has offline data to be sent.
 *
 * @return Promise resolved when done.
 */
this.loadOfflineData = function() {
    // Get the offline response if it exists.
    return that.choiceGroupOffline.getResponse(that.module.instance).then(function(response) {
        that.hasOffline = true;

        if (response.deleting) {
            // Uncheck selected option. Delete is only possible if there is no multiple enrolment.
            delete that.CONTENT_OTHERDATA.data.responses;
            that.showDelete = false;
        } else {
            // Load the offline options into the model.
            that.CONTENT_OTHERDATA.data = {};

            response.data.forEach(function(entry) {
                that.CONTENT_OTHERDATA.data[entry.name] = entry.value;
            });

            that.showDelete = !multipleEnrol; // Show delete if there is offline data and is not multiple enrol.
        }
    }).catch(function() {
        // Offline data not found. Use the original data.
        that.hasOffline = false;
        that.showDelete = that.CONTENT_OTHERDATA.answergiven;
        that.CONTENT_OTHERDATA.data = that.CoreUtilsProvider.clone(originalData);
    });
}

/**
 * Tries to synchronize the activity.
 *
 * @param showErrors If show errors to the user of hide them.
 * @param done Function to call when done.
 * @return Promise resolved with true if sync succeed, or false if failed.
 */
this.synchronize = function(showErrors, done) {
    that.refreshIcon = 'spinner';
    that.syncIcon = 'spinner';

    // Try to synchronize the group choice.
    return that.choiceGroupSync.syncChoiceGroup(that.module.instance).then(function(result) {
        if (result.warnings && result.warnings.length) {
            that.CoreDomUtilsProvider.showErrorModal(result.warnings[0]);
        }

        return result.updated;
    }).catch(function(error) {
        if (showErrors) {
            that.CoreDomUtilsProvider.showErrorModalDefault(error, 'core.errorsync', true);
        }

        return false;
    }).then(function(updated) {
        if (updated) {
            // Data has been sent, fetch the content (WS data has already been updated in the sync process).
            return that.fetchContent(false);
        }

        // Check if the group choice has offline data.
        return that.loadOfflineData();
    }).finally(function() {
        done && done();
        that.refreshIcon = 'refresh';
        that.syncIcon = 'sync';
    });
};

/**
 * Refresh data.
 *
 * @param done Function to call when done.
 * @return Promise resolved when done.
 */
this.doRefresh = function(done) {
    that.refreshIcon = 'spinner';
    that.syncIcon = 'spinner';

    return that.refreshContent(false).finally(function() {
        done && done();
        that.refreshIcon = 'refresh';
        that.syncIcon = 'sync';
    });
};

this.moduleName = this.TranslateService.instant('plugin.mod_choicegroup.modulename');
this.isOnline = this.CoreAppProvider.isOnline();

// Refresh online status when changes.
var onlineObserver = this.Network.onchange().subscribe(function() {
    that.isOnline = that.CoreAppProvider.isOnline();
});

var syncObserver;

if (allowOffline) {
    // Try to synchronize the choice.
    this.synchronize(false).finally(function() {
        that.loaded = true;
    });

    // Update the view if the group choice is synchronized automatically.
    syncObserver = this.CoreEventsProvider.on(this.choiceGroupSync.AUTO_SYNCED, function(data) {
        if (data.choiceGroupId == that.module.instance) {
            // This group choice has been synchronized, fetch the content (WS data has already been updated in the sync process).
            return that.fetchContent(false);
        }
    }, this.CoreSitesProvider.getCurrentSiteId());
} else {
    // No offline allowed, just display the data.
    this.loaded = true;
    that.refreshIcon = 'refresh';
    that.hasOffline = false;
    that.showDelete = that.CONTENT_OTHERDATA.answergiven;
}

/**
 * Component being destroyed.
 */
this.ngOnDestroy = function() {
    onlineObserver && onlineObserver.unsubscribe();
    syncObserver && syncObserver.off();
};
