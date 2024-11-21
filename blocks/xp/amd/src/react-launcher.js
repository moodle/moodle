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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.lup.plus

/**
 * React app launcher.
 *
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import Pending from 'core/pending';
import Log from 'core/log';
import Notification from 'core/notification';

/**
 * App launcher.
 *
 * @param {String} mod The module name.
 * @param {String} rootId The root ID.
 * @param {String} propsId The props ID.
 */
export function launch(mod, rootId, propsId) {
    const props = JSON.parse(document.getElementById(propsId).textContent);
    launchWithProps(mod, rootId, props);
}

/**
 * App launcher with props.
 *
 * @param {String} mod The module name.
 * @param {String} rootId The root ID.
 * @param {Object} props The props.
 */
async function launchWithProps(mod, rootId, props = {}) {
    const {startApp} = await loadModule(mod);
    startApp(document.getElementById(rootId), props);
}

/**
 * Load the module.
 *
 * @param {String} mod The react module.
 * @returns {Promise} Resolving with the module exported values.
 */
async function loadModule(mod) {
    const loader = $.Deferred();
    const pending = new Pending('block_xp/react-launcher:launch');

    // Load the app module. By convension a module app needs to return
    // an object with (at least) two properties: `dependencies`, and `startApp`.
    require([mod], function(mod) {
        var dependencies = [];
        var optionalDependencies = [];
        var dependenciesLoadedCallback = function() {
            return;
        };

        // If the module defines dependencies, set them up..
        if (mod.dependencies) {
            dependencies = mod.dependencies.list;
            dependenciesLoadedCallback = mod.dependencies.loader;
            optionalDependencies = mod.dependencies.optional || [];
        }

        // Load the dependencies.
        require([].concat(dependencies), function() {
            const deps = [...arguments];
            dependenciesLoadedCallback(deps);
            loader.resolve(mod);
        }, function(err) {

            // Modules that failed to load that are not optional are mocked.
            err.requireModules
                .filter((m) => optionalDependencies.includes(m))
                .forEach((module) => {
                    Log.warn(`block_xp launcher: Mocking optional module ${module} as it was not found.`);
                    require.undef(module);
                    // Don't ask why, but calling define directly confuses rollup...
                    const fn = define;
                    fn(module, function() {
                        return null;
                    });
                });

            // Retrigger the module loading.
            require(err.requireModules, function() {
                // Noop.
            });
        });
    });

    return loader.then((mod) => {
        pending.resolve();
        return mod;
    }).catch(Notification.exception);
}
