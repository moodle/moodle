/**
 * Tour management code.
 *
 * @module     tool_usertours/managetours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 */
import {prefetchStrings} from 'core/prefetch';
import {get_string as getString} from 'core/str';
import {confirm as confirmModal} from 'core/notification';

/**
 * Handle tour management actions.
 *
 * @param   {Event} e
 * @private
 */
const removeTourHandler = e => {
    const deleteButton = e.target.closest('[data-action="delete"]');
    if (deleteButton) {
        e.preventDefault();
        removeTourFromLink(deleteButton.href);
    }
};

/**
 * Handle removal of a tour with confirmation.
 *
 * @param {string} targetUrl
 * @private
 */
const removeTourFromLink = targetUrl => {
    confirmModal(
        getString('confirmtourremovaltitle', 'tool_usertours'),
        getString('confirmtourremovalquestion', 'tool_usertours'),
        getString('yes', 'core'),
        getString('no', 'core'),
        () => {
            window.location = targetUrl;
        }
    );
};

/**
 * Set up the tour management handlers.
 */
export const setup = () => {
    prefetchStrings('tool_usertours', [
        'confirmtourremovaltitle',
        'confirmtourremovalquestion',
    ]);

    prefetchStrings('core', [
        'yes',
        'no',
    ]);

    document.querySelector('body').addEventListener('click', removeTourHandler);
};
