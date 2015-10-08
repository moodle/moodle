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
 * Competency selector handler.
 *
 * @module     tool_lp/competencyselector
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery',
        'core/notification',
        'core/ajax',
        'core/templates',
        'tool_lp/dialogue',
        'core/str',
        'tool_lp/tree'],
       function($, notification, ajax, templates, Dialogue, str, Ariatree) {

    return {

        // Private variables and functions.
        /** @var {Object} frameworks - Site frameworks data */
        frameworks : null,

        /** @var {Dialogue} popup - Pop up where competencies are displayed. */
        popup : null,

        /** @var {Number} selectedCompetency - Currently selected competency. */
        selectedCompetency : 0,

        /** @var {Object[]} requests - Requests specified by the dependant module. */
        requests : [],

        /** @var {String} pagerender - Template to render, specified by the dependant module. */
        pagerender : null,

        /** @var {String} pageregion - Region to render in, specified by the dependant module. */
        pageregion : null,

        /** @var {Callback} addCompetencyCallback - Additional stuff to execute once the region is updated. */
        addCompetencyCallback : null,

        /** @var {Number} The page context ID. */
        pageContextId: null,

        /**
         * Returns the load competency frameworks promise.
         *
         * Caller can act depending on whether frameworks were found or not.
         *
         * @param {Number} pagectxid The page context ID.
         * @return {Promise}
         * @method init
         */
        init : function(pagectxid) {
            this.pageContextId = pagectxid;

            var loadframeworks = ajax.call([
                { methodname: 'tool_lp_list_competency_frameworks', args: {
                    sort: 'shortname',
                    context: { contextid: this.pageContextId }
                }}
            ]);

            loadframeworks[0].done(function(frameworks) {
                this.frameworks = frameworks;
            }.bind(this)).fail(notification.exception);

            return loadframeworks[0];
        },

        /**
         * Sets the data required by the module.
         * @param {Object[]} requests - Array of objects with the requests to send once a competency is selected.
         * @param {String} pagerender - The template to render.
         * @param {String} pageregion - The region to render in.
         * @param {Callback} addCompetencyCallback - Additional stuff to execute once all done.
         * @method setAddCompetencyRequests
         */
        setAddCompetencyRequests : function(requests, pagerender, pageregion, addCompetencyCallback) {
            this.requests = requests;
            this.pagerender = pagerender;
            this.pageregion = pageregion;
            this.addCompetencyCallback = addCompetencyCallback;
        },

        /**
         * Get the search text from the input field and reload the tree based on the search.
         *
         * @method applyFilter
         * @param {Event} e The event that triggered the button.
         */
        applyFilter : function(e) {

            e.preventDefault();

            var searchInput = $('[data-region="filtercompetencies"] input');
            var searchText = searchInput.val();
            var framework = $('[data-action="chooseframework"]');
            var frameworkid = framework.val();

            this.searchCompetencies().done(function (competencies) {
                var i = 0;

                var framework = this.frameworks[0];
                for (i = 0; i < this.frameworks.length; i++) {
                    if (this.frameworks[i].id == frameworkid) {
                        framework = this.frameworks[i];
                        framework.selected = true;
                    } else {
                        this.frameworks[i].selected = false;
                    }
                }
                framework.selected = true;
                var context = {
                    framework: framework,
                    frameworks: this.frameworks,
                    competencies: competencies,
                    search: searchText
                };
                templates.render('tool_lp/link_competencies', context).done(function(html) {
                    $('[data-region="competencylinktree"]').replaceWith(html);
                    this.initLinkCourseCompetencies();
                }.bind(this)).fail(notification.exception);
            }.bind(this)).fail(notification.exception);
        },

        /**
         * The link course competencies popup was just opened and we need to initialise it.
         *
         * @method initLinkCourseCompetencies
         */
        initLinkCourseCompetencies : function() {
            var requests;

            new Ariatree('[data-enhance=linktree]', function(target) {
                this.selectedCompetency = target.data('id');
            }.bind(this));

            $('[data-action="chooseframework"]').change(function(e) {
                return this.applyFilter(e);
            }.bind(this));

            $('[data-region="filtercompetencies"] button').click(function(e) {
                $(e.target).attr('disabled', 'disabled');
                return this.applyFilter(e);
            }.bind(this));

            $('[data-region="competencylinktree"] [data-action="cancel"]').click(function(e) {
                $(e.target).attr('disabled', 'disabled');
                e.preventDefault();
                this.popup.close();
            }.bind(this));
            $('[data-region="competencylinktree"] [data-action="add"]').click(function(e) {
                var btn = $(e.target);

                e.preventDefault();
                if (!this.selectedCompetency) {
                    return;
                }

                btn.attr('disabled', 'disabled');

                // The required callbacks and rendered templates depends on the page, but we should always
                // attach the selectCompetency to the first request, the one adding data to the database.
                this.requests[0].args.competencyid = this.selectedCompetency;
                requests = ajax.call(this.requests);

                requests[1].done(function(context) {
                    templates.render(this.pagerender, context).done(function(html, js) {
                        this.popup.close();
                        $('[data-region="' + this.pageregion + '"]').replaceWith(html);
                        templates.runTemplateJS(js);

                        // Extra callback in case we need additional processes.
                        if (typeof this.addCompetencyCallback !== "undefined") {
                            this.addCompetencyCallback();
                        }
                    }.bind(this)).fail(function(err) {
                        btn.removeAttr('disabled');
                        notification.exception(err);
                    });
                }.bind(this)).fail(function(err) {
                    btn.removeAttr('disabled');
                    notification.exception(err);
                });
            }.bind(this));
        },

        /**
         * Turn the flat list of competencies into a tree.
         *
         * @method addCompetencyChildren
         * @param {Object} parent The current parent node
         * @param {Object[]} competencies The flat list of all nodes.
         */
        addCompetencyChildren : function(parent, competencies) {
            var i;

            for (i = 0; i < competencies.length; i++) {
                if (competencies[i].parentid == parent.id) {
                    parent.haschildren = true;
                    competencies[i].children = [];
                    competencies[i].haschildren = false;
                    parent.children[parent.children.length] = competencies[i];
                    this.addCompetencyChildren(competencies[i], competencies);
                }
            }
        },

        /**
         * Get the search text from the input, and reload the tree.
         *
         * @method searchCompetencies
         * @return {promise} When resolved it will contain the tree of competencies.
         */
        searchCompetencies : function() {
            var deferred = $.Deferred();
            var searchInput = $('[data-region="filtercompetencies"] input');
            var searchText = '';
            if (searchInput.length) {
                searchText = searchInput.val();
            }
            var framework = $('[data-action="chooseframework"]');
            var frameworkid = this.frameworks[0].id;
            if (framework.length) {
                frameworkid = framework.val();
            }

            var loadCompetencies = ajax.call([
                { methodname: 'tool_lp_search_competencies', args: {
                    searchtext: searchText,
                    competencyframeworkid: frameworkid
                }}
            ]);

            loadCompetencies[0].done(function (competencies) {
                // Expand the list of competencies into a tree.
                var i, competenciestree = [];
                for (i = 0; i < competencies.length; i++) {
                    var onecompetency = competencies[i];
                    if (onecompetency.parentid == 0) {
                        onecompetency.children = [];
                        onecompetency.haschildren = 0;
                        competenciestree[competenciestree.length] = onecompetency;
                        this.addCompetencyChildren(onecompetency, competencies);
                    }
                }
                deferred.resolve(competenciestree);
            }.bind(this)).fail(function (ex) { deferred.reject(ex); });

            return deferred.promise();
        },

        /**
         * Open a popup to choose competencies.
         *
         * @method openCompetencySelector
         */
        openCompetencySelector : function() {
            this.searchCompetencies().done(function (competencies) {
                var framework = this.frameworks[0];
                framework.selected = true;
                var context = { framework: framework, frameworks: this.frameworks, competencies: competencies, search: '' };
                templates.render('tool_lp/link_competencies', context).done(function(html) {
                    str.get_string('linkcompetencies', 'tool_lp').done(function(title) {
                        this.popup = new Dialogue(
                            title,
                            html, // The link UI.
                            function() {
                                this.initLinkCourseCompetencies();
                            }.bind(this)
                        );
                    }.bind(this)).fail(notification.exception);
                }.bind(this)).fail(notification.exception);
            }.bind(this)).fail(notification.exception);
        }
    };
});
