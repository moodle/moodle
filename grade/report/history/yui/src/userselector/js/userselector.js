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
 * The User Selector for the grade history report.
 *
 * @module     moodle-gradereport_history-userselector
 * @package    gradereport_history
 * @copyright  2013 NetSpot Pty Ltd (https://www.netspot.com.au)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @main       moodle-gradereport_history-userselector
 */

/**
 * @module moodle-gradereport_history-userselector
 */

var COMPONENT = 'gradereport_history';
var USP = {
    AJAXURL: 'ajaxurl',
    BASE: 'base',
    CHECKBOX_NAME_PREFIX: 'usp-u',
    COURSEID: 'courseid',
    DIALOGUE_PREFIX: 'moodle-dialogue',
    NAME: 'gradereport_history_usp',
    PAGE: 'page',
    PARAMS: 'params',
    PERPAGE: 'perPage',
    SEARCH: 'search',
    SEARCHBTN: 'searchbtn',
    SELECTEDUSERS: 'selectedUsers',
    URL: 'url',
    USERCOUNT: 'userCount'
};
var CSS = {
    ACCESSHIDE: 'accesshide',
    AJAXCONTENT: 'usp-ajax-content',
    CHECKBOX: 'usp-checkbox',
    CLOSE: 'close',
    CLOSEBTN: 'usp-finish',
    CONTENT: 'usp-content',
    DETAILS: 'details',
    EXTRAFIELDS: 'extrafields',
    FIRSTADDED: 'usp-first-added',
    FULLNAME: 'fullname',
    HEADER: 'usp-header',
    HIDDEN: 'hidden',
    LIGHTBOX: 'usp-loading-lightbox',
    LOADINGICON: 'loading-icon icon',
    MORERESULTS: 'usp-more-results',
    OPTIONS: 'options',
    PICTURE: 'usp-picture',
    RESULTSCOUNT: 'usp-results-count',
    SEARCH: 'usp-search',
    SEARCHBTN: 'usp-search-btn',
    SEARCHFIELD: 'usp-search-field',
    SEARCHRESULTS: 'usp-search-results',
    SELECTED: 'selected',
    USER: 'usp-user',
    USERS: 'usp-users',
    WRAP: 'usp-wrap'
};
var SELECTORS = {
    AJAXCONTENT: '.' + CSS.AJAXCONTENT,
    FINISHBTN: '.' + CSS.CLOSEBTN + ' input',
    FIRSTADDED: '.' + CSS.FIRSTADDED,
    FULLNAME: '.' + CSS.FULLNAME + ' label',
    LIGHTBOX: '.' + CSS.LIGHTBOX,
    MORERESULTS: '.' + CSS.MORERESULTS,
    OPTIONS: '.' + CSS.OPTIONS,
    PICTURE: '.' + CSS.USER + ' .userpicture',
    RESULTSCOUNT: '.' + CSS.RESULTSCOUNT,
    RESULTSUSERS: '.' + CSS.SEARCHRESULTS + ' .' + CSS.USERS,
    SEARCHBTN: '.' + CSS.SEARCHBTN,
    SEARCHFIELD: '.' + CSS.SEARCHFIELD,
    SELECTEDNAMES: '.felement .selectednames',
    TRIGGER: '.gradereport_history_plugin input.selectortrigger',
    USER: '.' + CSS.USER,
    USERFULLNAMES: 'input[name="userfullnames"]',
    USERIDS: 'input[name="userids"]',
    USERSELECT: '.' + CSS.CHECKBOX + ' input[type=checkbox]'
};

/**
 * User Selector.
 *
 * @namespace M.gradereport_history
 * @class UserSelector
 * @constructor
 */

var USERSELECTOR = function() {
    USERSELECTOR.superclass.constructor.apply(this, arguments);
};
Y.namespace('M.gradereport_history').UserSelector = Y.extend(USERSELECTOR, M.core.dialogue, {

    /**
     * Whether or not this is the first time the user displays the dialogue within that request.
     *
     * @property _firstDisplay
     * @type Boolean
     * @private
     */
    _firstDisplay: true,

    /**
     * The list of all the users selected while the dialogue is open.
     *
     * @type Object
     * @property _usersBufferList
     * @private
     */
    _usersBufferList: null,

    /**
     * The Node on which the focus is set.
     *
     * @property _userTabFocus
     * @type Node
     * @private
     */
    _userTabFocus: null,

    /**
     * Compiled template function for a user node.
     *
     * @property _userTemplate
     * @type Function
     * @private
     */
    _userTemplate: null,

    initializer: function() {
        var bb = this.get('boundingBox'),
            content,
            params,
            tpl;

        tpl = Y.Handlebars.compile(
            '<div class="{{CSS.WRAP}}">' +
                '<div class="{{CSS.HEADER}}">' +
                    '<div class="{{CSS.SEARCH}}" role="search">' +
                        '<form>' +
                            '<input type="text" class="{{CSS.SEARCHFIELD}}" ' +
                                'aria-label="{{get_string "search" "moodle"}}" value="" />' +
                            '<input type="submit" class="{{CSS.SEARCHBTN}}"' +
                                'value="{{get_string "search" "moodle"}}">' +
                        '</form>' +
                        '<div aria-live="polite" class="{{CSS.RESULTSCOUNT}}">{{get_string "loading" "admin"}}</div>' +
                    '</div>' +
                '</div>' +
                '<div class="{{CSS.CONTENT}}">' +
                    '<form>' +
                        '<div class="{{CSS.AJAXCONTENT}}" aria-live="polite"></div>' +
                        '<div class="{{CSS.LIGHTBOX}} {{CSS.HIDDEN}}">' +
                            '<img class="{{CSS.LOADINGICON}}" alt="{{get_string "loading" "admin"}}"' +
                                'src="{{{loadingIcon}}}">' +
                        '</div>' +
                        '<div class="{{CSS.CLOSEBTN}}">' +
                            '<input type="submit" value="{{get_string "finishselectingusers" COMPONENT}}">' +
                        '</div>' +
                    '</form>' +
                '</div>' +
            '</div>');

        content = Y.Node.create(
            tpl({
                COMPONENT: COMPONENT,
                CSS: CSS,
                loadingIcon: M.util.image_url('i/loading', 'moodle')
            })
        );

        // Set the title and content.
        this.getStdModNode(Y.WidgetStdMod.HEADER).prepend(Y.Node.create('<h1>' + this.get('title') + '</h1>'));
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        // Use standard dialogue class name. This removes the default styling of the footer.
        this.get('boundingBox').one('.moodle-dialogue-wrap').addClass('moodle-dialogue-content');

        // Add the event on the button that opens the dialogue.
        Y.one(SELECTORS.TRIGGER).on('click', this.show, this);

        // The button to finalize the selection.
        bb.one(SELECTORS.FINISHBTN).on('click', this.finishSelectingUsers, this);

        // Delegate the keyboard navigation in the users list.
        bb.delegate('key', this.userKeyboardNavigation, 'down:38,40', SELECTORS.AJAXCONTENT, this);

        // Delegate the action to select a user.
        Y.delegate('click', this.selectUser, SELECTORS.AJAXCONTENT, SELECTORS.USERSELECT, this);
        Y.delegate('click', this.selectUser, SELECTORS.AJAXCONTENT, SELECTORS.PICTURE, this);

        params = this.get(USP.PARAMS);
        params.id = this.get(USP.COURSEID);
        this.set(USP.PARAMS, params);

        bb.one(SELECTORS.SEARCHBTN).on('click', this.search, this, false);
    },

    /**
     * Display the dialogue.
     *
     * @method show
     */
    show: function(e) {
        var bb;
        this._usersBufferList = Y.clone(this.get(USP.SELECTEDUSERS));
        if (this._firstDisplay) {
            // Load the default list of users when the dialogue is loaded for the first time.
            this._firstDisplay = false;
            this.search(e, false);
        } else {
            // Leave the content as is, but reset the selection.
            bb = this.get('boundingBox');

            // Remove all the selected users.
            bb.all(SELECTORS.USER).each(function(node) {
                this.markUserNode(node, false);
            }, this);

            // Select the users.
            Y.Object.each(this._usersBufferList, function(v, k) {
                var user = bb.one(SELECTORS.USER + '[data-userid="' + k + '"]');
                if (user) {
                    this.markUserNode(user, true);
                }
            }, this);

            // Reset the tab focus.
            this.setUserTabFocus(bb.one(SELECTORS.USER));
        }
        return Y.namespace('M.gradereport_history.UserSelector').superclass.show.call(this);
    },

    /**
     * Search for users.
     *
     * @method search
     * @param {EventFacade} e The event.
     * @param {Boolean} append Whether we want to append the results to the current results or not.
     */
    search: function(e, append) {
        if (e) {
            e.preventDefault();
        }
        var params;
        if (append) {
            this.set(USP.PAGE, this.get(USP.PAGE) + 1);
        } else {
            this.set(USP.USERCOUNT, 0);
            this.set(USP.PAGE, 0);
        }
        params = this.get(USP.PARAMS);
        params.sesskey = M.cfg.sesskey;
        params.action = 'searchusers';
        params.search = this.get('boundingBox').one(SELECTORS.SEARCHFIELD).get('value');
        params.page = this.get(USP.PAGE);
        params.perpage = this.get(USP.PERPAGE);

        Y.io(M.cfg.wwwroot + this.get(USP.AJAXURL), {
            method: 'POST',
            data: window.build_querystring(params),
            on: {
                start: this.preSearch,
                complete: this.processSearchResults,
                end: this.postSearch
            },
            context: this,
            "arguments": {      // Quoted because this is a reserved keyword.
                append: append
            }
        });
    },

    /**
     * Pre search callback.
     *
     * @method preSearch
     * @param {String} transactionId The transaction ID.
     * @param {Object} args The arguments passed from YUI.io()
     */
    preSearch: function(unused, args) {
        var bb = this.get('boundingBox');

        // Display the lightbox.
        bb.one(SELECTORS.LIGHTBOX).removeClass(CSS.HIDDEN);

        // Set the number of results to 'loading...'.
        if (!args.append) {
            bb.one(SELECTORS.RESULTSCOUNT).setHTML(M.util.get_string('loading', 'admin'));
        }
    },

    /**
     * Post search callback.
     *
     * @method postSearch
     * @param {String} transactionId The transaction ID.
     * @param {Object} args The arguments passed from YUI.io()
     */
    postSearch: function(transactionId, args) {
        var bb = this.get('boundingBox'),
            firstAdded = bb.one(SELECTORS.FIRSTADDED),
            firstUser;

        // Hide the lightbox.
        bb.one(SELECTORS.LIGHTBOX).addClass(CSS.HIDDEN);

        if (args.append && firstAdded) {
            // Sets the focus on the newly added user if we are appending results.
            this.setUserTabFocus(firstAdded);
            firstAdded.one(SELECTORS.USERSELECT).focus();
        } else {
            // New search result, set the tab focus on the first user returned.
            firstUser = bb.one(SELECTORS.USER);
            if (firstUser) {
                this.setUserTabFocus(firstUser);
            }
        }
    },

    /**
     * Process and display the search results.
     *
     * @method processSearchResults
     * @param {String} tid The transaction ID.
     * @param {Object} outcome The response object.
     * @param {Object} args The arguments passed from YUI.io().
     */
    processSearchResults: function(tid, outcome, args) {
        var result = false,
            error = false,
            bb = this.get('boundingBox'),
            users,
            userTemplate,
            count,
            selected,
            i,
            firstAdded = true,
            node,
            content,
            fetchmore,
            totalUsers;

        // Decodes the result.
        try {
            result = Y.JSON.parse(outcome.responseText);
            if (!result.success || result.error) {
                error = true;
            }
        } catch (e) {
            error = true;
        }

        // There was an error.
        if (error) {
            this.setContent('');
            bb.one(SELECTORS.RESULTSCOUNT).setHTML(M.util.get_string('errajaxsearch', COMPONENT));
            return;
        }

        // Create the div containing the users when it is a fresh search.
        if (!args.append) {
            users = Y.Node.create('<div role="listbox" aria-activedescendant="" aria-multiselectable="true" class="' +
                                   CSS.USERS +
                                   '"></div>');
        } else {
            users = bb.one(SELECTORS.RESULTSUSERS);
        }

        // Compile the template for each user node.
        if (!this._userTemplate) {
            this._userTemplate = Y.Handlebars.compile(
                '<div role="option" aria-selected="false" class="{{CSS.USER}} clearfix" ' +
                        'data-userid="{{userId}}">' +
                    '<div class="{{CSS.CHECKBOX}}">' +
                        '<input name="{{USP.CHECKBOX_NAME_PREFIX}}{{userId}}" type="checkbox" tabindex="-1"' +
                            'id="{{checkboxId}}" aria-describedby="{{checkboxId}} {{extraFieldsId}}"/>' +
                    '</div>' +
                    '<div class="{{CSS.PICTURE}}">{{{picture}}}</div>' +
                    '<div class="{{CSS.DETAILS}}">' +
                        '<div class="{{CSS.FULLNAME}}">' +
                            '<label for="{{checkboxId}}">{{fullname}}</label>' +
                        '</div>' +
                        '<div id="{{extraFieldsId}}" class="{{CSS.EXTRAFIELDS}}">{{{extrafields}}}</div>' +
                    '</div>' +
                '</div>'
            );
        }
        userTemplate = this._userTemplate;

        // Append the users one by one.
        count = this.get(USP.USERCOUNT);
        selected = '';
        var user;
        for (i in result.response.users) {
            count++;
            user = result.response.users[i];

            // If already selected.
            if (Y.Object.hasKey(this._usersBufferList, user.userid)) {
                selected = true;
            } else {
                selected = false;
            }

            node = Y.Node.create(userTemplate({
                checkboxId: Y.guid(),
                COMPONENT: COMPONENT,
                count: count,
                CSS: CSS,
                extrafields: user.extrafields,
                extraFieldsId: Y.guid(),
                fullname: user.fullname,
                picture: user.picture,
                userId: user.userid,
                USP: USP
            }));

            this.markUserNode(node, selected);

            // Noting the first user that was when adding more results.
            if (args.append && firstAdded) {
                users.all(SELECTORS.FIRSTADDED).removeClass(CSS.FIRSTADDED);
                node.addClass(CSS.FIRSTADDED);
                firstAdded = false;
            }
            users.append(node);
        }
        this.set(USP.USERCOUNT, count);

        // Update the count of users, and add a button to load more if need be.
        totalUsers = parseInt(result.response.totalusers, 10);
        if (!args.append) {
            if (totalUsers === 0) {
                bb.one(SELECTORS.RESULTSCOUNT).setHTML(M.util.get_string('noresults', 'moodle'));
                content = '';
            } else {
                if (totalUsers === 1) {
                    bb.one(SELECTORS.RESULTSCOUNT).setHTML(M.util.get_string('foundoneuser', COMPONENT));
                } else {
                    bb.one(SELECTORS.RESULTSCOUNT).setHTML(M.util.get_string('foundnusers', COMPONENT, totalUsers));
                }

                content = Y.Node.create('<div class="' + CSS.SEARCHRESULTS + '"></div>')
                    .append(users);
                if (result.response.totalusers > (this.get(USP.PAGE) + 1) * this.get(USP.PERPAGE)) {
                    fetchmore = Y.Node.create('<div class="' + CSS.MORERESULTS + '">' +
                        '<a href="#" role="button">' + M.util.get_string('loadmoreusers', COMPONENT) + '</a></div>');
                    fetchmore.one('a').on('click', this.search, this, true);
                    fetchmore.one('a').on('key', this.search, 'space', this, true);
                    content.append(fetchmore);
                }
            }
            this.setContent(content);
        } else {
            if (totalUsers <= (this.get(USP.PAGE) + 1) * this.get(USP.PERPAGE)) {
                bb.one(SELECTORS.MORERESULTS).remove();
            }
        }
    },

    /**
     * When the user has finished selecting users.
     *
     * @method finishSelectingUsers
     * @param {EventFacade} e The event.
     */
    finishSelectingUsers: function(e) {
        e.preventDefault();
        this.applySelection();
        this.hide();
    },

    /**
     * Apply the selection made.
     *
     * @method applySelection
     * @param {EventFacade} e The event.
     */
    applySelection: function() {
        var userIds = Y.Object.keys(this._usersBufferList);
        this.set(USP.SELECTEDUSERS, Y.clone(this._usersBufferList))
            .setNameDisplay();
        Y.one(SELECTORS.USERIDS).set('value', userIds.join());
    },

    /**
     * Select a user.
     *
     * @method SelectUser
     * @param {EventFacade} e The event.
     */
    selectUser: function(e) {
        var user = e.currentTarget.ancestor(SELECTORS.USER),
            checkbox = user.one(SELECTORS.USERSELECT),
            fullname = user.one(SELECTORS.FULLNAME).get('innerHTML'),
            checked = checkbox.get('checked'),
            userId = user.getData('userid');

        if (e.currentTarget !== checkbox) {
            // We triggered the selection from another node, so we need to change the checkbox value.
            checked = !checked;
        }

        if (checked) {
            // Selecting the user.
            this._usersBufferList[userId] = fullname;
        } else {
            // De-selecting the user.
            delete this._usersBufferList[userId];
            delete this._usersBufferList[parseInt(userId, 10)]; // Also remove numbered keys.
        }

        this.markUserNode(user, checked);
    },

    /**
     * Mark a user node as selected or not.
     *
     * This only takes care of the DOM side of things, not the internal mechanism
     * storing what users have been selected or not.
     *
     * @param {Node} node The user node.
     * @param {Boolean} selected True to mark as selected.
     * @chainable
     */
    markUserNode: function(node, selected) {
        if (selected) {
            node.addClass(CSS.SELECTED)
                .set('aria-selected', true)
                .one(SELECTORS.USERSELECT)
                    .set('checked', true);
        } else {
            node.removeClass(CSS.SELECTED)
                .set('aria-selected', false)
                .one(SELECTORS.USERSELECT)
                    .set('checked', false);
        }
        return this;
    },

    /**
     * Set the content of the dialogue.
     *
     * @method setContent
     * @param {String} content The content.
     * @chainable
     */
    setContent: function(content) {
        this.get('boundingBox').one(SELECTORS.AJAXCONTENT).setHTML(content);
        return this;
    },

    /**
     * Display the names of the selected users in the form.
     *
     * @method setNameDisplay
     */
    setNameDisplay: function() {
        var namelist = Y.Object.values(this.get(USP.SELECTEDUSERS));
        Y.one(SELECTORS.SELECTEDNAMES).set('innerHTML', namelist.join(', '));
        Y.one(SELECTORS.USERFULLNAMES).set('value', namelist.join());
    },

    /**
     * User keyboard navigation.
     *
     * @method userKeyboardNavigation
     */
    userKeyboardNavigation: function(e) {
        var bb = this.get('boundingBox'),
            users = bb.all(SELECTORS.USER),
            direction = 1,
            user,
            current = e.target.ancestor(SELECTORS.USER, true);

        if (e.keyCode === 38) {
            direction = -1;
        }

        user = this.findFocusableUser(users, current, direction);
        if (user) {
            e.preventDefault();
            user.one(SELECTORS.USERSELECT).focus();
            this.setUserTabFocus(user);
        }
    },

    /**
     * Find the next or previous focusable node.
     *
     * @param {NodeList} users The list of users.
     * @param {Node} user The user to start with.
     * @param {Number} direction The direction in which to go.
     * @return {Node|null} A user node, or null if not found.
     * @method findFocusableUser
     */
    findFocusableUser: function(users, user, direction) {
        var index = users.indexOf(user);

        if (users.size() < 1) {
            Y.log('The users list is empty', 'debug', COMPONENT);
            return null;
        }

        if (index < 0) {
            Y.log('Unable to find the user in the list of users', 'debug', COMPONENT);
            return users.item(0);
        }

        index += direction;

        // Wrap the navigation when reaching the top of the bottom.
        if (index < 0) {
            index = users.size() - 1;
        } else if (index >= users.size()) {
            index = 0;
        }

        return users.item(index);
    },

    /**
     * Set the user tab focus.
     *
     * @param {Node} user The user node.
     * @method setUserTabFocus
     */
    setUserTabFocus: function(user) {
        if (this._userTabFocus) {
            this._userTabFocus.setAttribute('tabindex', '-1');
        }
        if (!user) {
            // We were not passed a user, there is apparently none in the dialogue. Nothing to do here \\\o/.
            return;
        }

        this._userTabFocus = user.one(SELECTORS.USERSELECT);
        this._userTabFocus.setAttribute('tabindex', '0');

        this.get('boundingBox').one(SELECTORS.RESULTSUSERS).setAttribute('aria-activedescendant', this._userTabFocus.generateID());
    }

}, {
    NAME: USP.NAME,
    CSS_PREFIX: USP.CSS_PREFIX,
    ATTRS: {

        /**
         * The header.
         *
         * @attribute title
         * @default selectusers language string.
         * @type String
         */
        title: {
            validator: Y.Lang.isString,
            valueFn: function() {
                return M.util.get_string('selectusers', COMPONENT);
            }
        },

        /**
         * The current page URL.
         *
         * @attribute url
         * @default null
         * @type String
         */
        url: {
            validator: Y.Lang.isString,
            value: null
        },

        /**
         * The URL to the Ajax file.
         *
         * @attribute ajaxurl
         * @default null
         * @type String
         */
        ajaxurl: {
            validator: Y.Lang.isString,
            value: null
        },

        /**
         * The names of the selected users.
         *
         * The keys are the user IDs, the values are their fullname.
         *
         * @attribute selectedUsers
         * @default null
         * @type Object
         */
        selectedUsers: {
            validator: Y.Lang.isObject,
            value: null,
            getter: function(v) {
                if (v === null) {
                    return {};
                }
                return v;
            }
        },

        /**
         * The course ID.
         *
         * @attribute courseid
         * @default null
         * @type Number
         */
        courseid: {
            value: null
        },

        /**
         * Array of parameters.
         *
         * @attribute params
         * @default []
         * @type Array
         */
        params: {
            validator: Y.Lang.isArray,
            value: []
        },

        /**
         * The page we are on.
         *
         * @attribute page
         * @default 0
         * @type Number
         */
        page: {
            validator: Y.Lang.isNumber,
            value: 0
        },

        /**
         * The number of users displayed.
         *
         * @attribute userCount
         * @default 0
         * @type Number
         */
        userCount: {
            value: 0,
            validator: Y.Lang.isNumber
        },

        /**
         * The number of results per page.
         *
         * @attribute perPage
         * @default 25
         * @type Number
         */
        perPage: {
            value: 25,
            Validator: Y.Lang.isNumber
        }

    }
});

Y.Base.modifyAttrs(Y.namespace('M.gradereport_history.UserSelector'), {

    /**
     * List of extra classes.
     *
     * @attribute extraClasses
     * @default ['gradereport_history_usp']
     * @type Array
     */
    extraClasses: {
        value: [
            'gradereport_history_usp'
        ]
    },

    /**
     * Whether to focus on the target that caused the Widget to be shown.
     *
     * @attribute focusOnPreviousTargetAfterHide
     * @default true
     * @type Node
     */
    focusOnPreviousTargetAfterHide: {
        value: true
    },

    /**
     *
     * Width.
     *
     * @attribute width
     * @default '500px'
     * @type String|Number
     */
    width: {
        value: '500px'
    },

    /**
     * Boolean indicating whether or not the Widget is visible.
     *
     * @attribute visible
     * @default false
     * @type Boolean
     */
    visible: {
        value: false
    },

   /**
    * Whether the widget should be modal or not.
    *
    * @attribute modal
    * @type Boolean
    * @default true
    */
    modal: {
        value: true
    },

   /**
    * Whether the widget should be draggable or not.
    *
    * @attribute draggable
    * @type Boolean
    * @default true
    */
    draggable: {
        value: true
    }

});

Y.namespace('M.gradereport_history.UserSelector').init = function(cfg) {
    return new USERSELECTOR(cfg);
};
