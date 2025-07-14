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
 * Contain the logic for the loading icon.
 *
 * @module     core/loadingicon
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates'], function($, Templates) {
    var TEMPLATES = {
        LOADING: 'core/loading',
    };

    var getIcon = function() {
        return Templates.render(TEMPLATES.LOADING, {});
    };

    /**
     * Add a loading icon to the end of the specified container and return an unresolved promise.
     *
     * Resolution of the returned promise causes the icon to be faded out and removed.
     *
     * @method  addIconToContainer
     * @param   {jQuery|HTMLElement}  container  The element to add the spinner to
     * @return  {Promise} The Promise used to create the icon.
     */
    var addIconToContainer = function(container) {
        return getIcon()
        .then(function(html) {
            var loadingIcon = $(html).hide();
            $(container).append(loadingIcon);
            loadingIcon.fadeIn(150);

            return loadingIcon;
        });
    };

    /**
     * Add a loading icon to the end of the specified container and return an unresolved promise.
     *
     * Resolution of the returned promise causes the icon to be faded out and removed.
     *
     * @method  addIconToContainerWithPromise
     * @param   {jQuery|HTMLElement}  container  The element to add the spinner to
     * @param   {Promise} loadingIconPromise The jQuery Promise which determines the removal of the icon
     * @return  {jQuery}  The Promise used to create and then remove the icon.
     */
    var addIconToContainerRemoveOnCompletion = function(container, loadingIconPromise) {
        return getIcon()
        .then(function(html) {
            var loadingIcon = $(html).hide();
            $(container).append(loadingIcon);
            loadingIcon.fadeIn(150);

            return $.when(loadingIcon.promise(), loadingIconPromise);
        })
        .then(function(loadingIcon) {
            // Once the content has finished loading and
            // the loading icon has been shown then we can
            // fade the icon away to reveal the content.
            return loadingIcon.fadeOut(100).promise();
        })
        .then(function(loadingIcon) {
            loadingIcon.remove();

            return;
        });
    };

    /**
     * Add a loading icon to the end of the specified container and return an unresolved promise.
     *
     * Resolution of the returned promise causes the icon to be faded out and removed.
     *
     * @method  addIconToContainerWithPromise
     * @param   {jQuery|HTMLElement}  container  The element to add the spinner to
     * @return  {Promise} A jQuery Promise to resolve when ready
     */
    var addIconToContainerWithPromise = function(container) {
        var loadingIconPromise = $.Deferred();

        addIconToContainerRemoveOnCompletion(container, loadingIconPromise);

        return loadingIconPromise;
    };

    return {
        getIcon: getIcon,
        addIconToContainer: addIconToContainer,
        addIconToContainerWithPromise: addIconToContainerWithPromise,
        addIconToContainerRemoveOnCompletion: addIconToContainerRemoveOnCompletion,
    };

});
