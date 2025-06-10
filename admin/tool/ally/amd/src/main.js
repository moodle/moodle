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
 * Action selector.
 *
 * Ally admin tool log view.
 *
 * @package    tool_ally
 * @copyright  Code based on admin/tool/behatdump (c) Guy Thomas 2018
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


(function() {
    var pluginJSURL = function(path) {
        return M.cfg.wwwroot + "/pluginfile.php/" + M.cfg.contextid + "/tool_ally/" + path;
    };

    require.config({
        enforceDefine: false,
        paths: {
            // Vendor code.
            "tool_ally/vue_2_5_16": [
                "https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.min",
                // CDN Fallback - whoop whoop!
                pluginJSURL("vendorjs/vue")
            ],
            "tool_ally/vuerouter_2_5_3": [
                "https://cdn.jsdelivr.net/npm/vue-router@2.5.3/dist/vue-router.min",
                // CDN Fallback - whoop whoop!
                pluginJSURL("vendorjs/vuerouter")
            ],

            // Note, vuedatable is not via a CDN because it has been customised (made more accessible).
            "tool_ally/vuedatatable": pluginJSURL("vendorjs/vuedatatable"),

            // Vue components
            "tool_ally/vuecomp": [
                pluginJSURL('vue/comps')
            ],
        }
    });
})();


define(['jquery', 'tool_ally/vue_2_5_16', 'tool_ally/vuerouter_2_5_3',
    'tool_ally/vuedatatable'], function($, Vue, VueRouter, VueDataTable) {

    Vue.use(VueRouter);
    // Note: .default is necessary when you are using require as opposed to import (require is AMD method to load modules).
    Vue.use(VueDataTable.default);

    return {
        vue: null,

        getBootstrapVersion: function() {
            var version = 2; // Default is to assume 2.
            if (window.$ && window.$.fn && window.$.fn.tooltip && window.$.fn.tooltip.Constructor) {
                version = window.$.fn.tooltip.Constructor.VERSION;
                if (version === undefined) {
                    version = 2; // Assume BS 2.
                }
            }
            version = parseInt(version);
            return version;
        },

        applyBootstrapClass: function() {
            $('body').addClass('bs-major-version-' + this.getBootstrapVersion());
        },

        init: function(vueOpts) {

            this.applyBootstrapClass();

            var dfd = $.Deferred();

            // It seemed neccessary to load this once the document was ready otherwise we occasionally get a blank page.
            $(document).ready(function() {
                // Default opts. No spread operator in ES5 :-(
                var opts = {
                    el: '#app',
                    router: null
                };
                for (var property in vueOpts) {
                    if (vueOpts.hasOwnProperty(property)) {
                        opts[property] = vueOpts[property];
                    }
                }
                if (opts.routes) {
                    opts.router = new VueRouter({
                        routes: opts.routes
                    });
                }

                // Register global components.
                if (opts.globalComponents) {
                    for (var compKey in opts.globalComponents) {
                        if (opts.globalComponents.hasOwnProperty(compKey)) {
                            var component = opts.globalComponents[compKey];
                            Vue.component(compKey, component);
                        }
                    }
                }

                this.vue = new Vue(opts).$mount('#app');

                dfd.resolve(this.vue);
            });

            return dfd;
        }
    };

});