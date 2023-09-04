/**
 * This function updates the global logout status.
 */
function updateStatus()
{
    var nFailed = 0;
    var nProgress = 0;
    for (var sp in window.spStatus) {
        switch (window.spStatus[sp]) {
            case 'failed':
                nFailed += 1;
                break;
            case 'inprogress':
                nProgress += 1;
                break;
        }
    }

    if (nFailed > 0) {
        $('#logout-failed-message').show();
    }

    if (nProgress === 0 && nFailed === 0) {
        $('#logout-completed').show();
        $('#done-form').submit();
    }
}

/**
 * This function updates the logout status for a given SP.
 *
 * @param spId The ID of the SP.
 * @param status The new status.
 * @param reason The reason for the status change.
 */
function updateSPStatus(spId, status, reason)
{
    if (window.spStatus[spId] === status) {
        // unchanged
        return;
    }

    $('#statusimage-' + spId).attr('src', window.stateImage[status]).attr('alt', window.stateText[status]).attr('title', reason);
    window.spStatus[spId] = status;

    var formId = 'logout-iframe-' + spId;
    var existing = $('input[name="' + formId + '"]');
    if (existing.length === 0) {
        // don't have an existing form element, add one
        var elementHTML = '<input type="hidden" name="' + formId + '" value="' + status + '" />';
        $('#failed-form , #done-form').append(elementHTML);
    } else {
        // update existing element
        existing.attr('value', status);
    }

    updateStatus();
}

/**
 * Mark logout as completed for an SP.
 *
 * This method will be called by the SimpleSAML\IdP\IFrameLogoutHandler class upon successful logout from the SP.
 *
 * @param spId The SP that completed logout successfully.
 */
function logoutCompleted(spId)
{
    updateSPStatus(spId, 'completed', '');
}

/**
 * Mark logout as failed for an SP.
 *
 * This method will be called by the SimpleSAML\IdP\IFrameLogoutHandler class upon logout failure from the SP.
 *
 * @param spId The SP that failed to complete logout.
 * @param reason The reason why logout failed.
 */
function logoutFailed(spId, reason)
{
    updateSPStatus(spId, 'failed', reason);
}

/**
 * Set timeouts for all logout operations.
 *
 * If an SP didn't reply by the timeout, we'll mark it as failed.
 */
function timeoutSPs()
{
    var cTime = ((new Date()).getTime() - window.startTime) / 1000;
    for (var sp in window.spStatus) {
        if (window.spTimeout[sp] <= cTime && window.spStatus[sp] === 'inprogress') {
            logoutFailed(sp, 'Timeout');
        }
    }
    window.timeoutID = window.setTimeout(timeoutSPs, 1000);
}

$('document').ready(function () {
    window.startTime = (new Date()).getTime();
    if (window.type === 'js') {
        window.timeoutID = window.setTimeout(timeoutSPs, 1000);
        updateStatus();
    } else if (window.type === 'init') {
        $('#logout-type-selector').attr('value', 'js');
        $('#logout-all').focus();
    }
});
