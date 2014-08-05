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
    AJAXURL : 'ajaxurl',
    BASE : 'base',
    CHECKBOX_NAME_PREFIX : 'usp-u',
    COURSEID : 'courseid',
    DIALOGUE_PREFIX : 'moodle-dialogue',
    NAME : 'gradereport_history_usp',
    PAGE : 'page',
    PARAMS : 'params',
    PERPAGE : 'perPage',
    SEARCH : 'search',
    SEARCHBTN : 'searchbtn',
    SELECTEDUSERS : 'selectedusers',
    URL : 'url',
    USERCOUNT : 'userCount',
    USERFULLNAMES : 'userfullnames'
};
var CSS = {
    ACCESSHIDE : 'accesshide',
    AJAXCONTENT : 'usp-ajax-content',
    CHECKBOX : 'usp-checkbox',
    CLOSE : 'close',
    CLOSEBTN : 'usp-finish',
    CONTENT : 'usp-content',
    DETAILS : 'details',
    EXTRAFIELDS : 'extrafields',
    FIRSTADDED : 'usp-first-added',
    FULLNAME : 'fullname',
    HEADER : 'usp-header',
    HIDDEN : 'hidden',
    LIGHTBOX : 'usp-loading-lightbox',
    LOADINGICON : 'loading-icon',
    MORERESULTS : 'usp-more-results',
    OPTIONS : 'options',
    PICTURE : 'usp-picture',
    RESULTSCOUNT : 'usp-results-count',
    SEARCH : 'usp-search',
    SEARCHBTN : 'usp-search-btn',
    SEARCHFIELD : 'usp-search-field',
    SEARCHRESULTS : 'usp-search-results',
    SELECTED : 'selected',
    USER : 'user',
    USERS : 'users',
    WRAP : 'usp-wrap'
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
var create = Y.Node.create;

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
     * @type {Boolean}
     * @private
     */
    _firstDisplay: true,

    /**
     * The list of all the users selected while the dialogue is open.
     *
     * @type {Object}
     * @property _usersBufferList
     * @private
     */
    _usersBufferList: null,

    /**
     * Compiled template function for a user node.
     *
     * @property _userTemplate
     * @type Function
     * @private
     */
    _userTemplate : null,

    initializer : function() {
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
                                'aria-labelledby="{{get_string "search" "moodle"}}" value="" />' +
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

        content = create(
            tpl({
                COMPONENT: COMPONENT,
                CSS: CSS,
                loadingIcon: M.util.image_url('i/loading', 'moodle')
            })
        );

        // Set the title and content.
        this.getStdModNode(Y.WidgetStdMod.HEADER).prepend(create('<h1>' + this.get('title') + '</h1>'));
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        // Use standard dialogue class name. This removes the default styling of the footer.
        this.get('boundingBox').one('.moodle-dialogue-wrap').addClass('moodle-dialogue-content');

        // Load the list of users.
        this.loadUsersFromForm();

        // Add the event on the button that opens the dialogue.
        Y.one(SELECTORS.TRIGGER).on('click', this.show, this);

        // The button to finalize the selection.
        bb.one(SELECTORS.FINISHBTN).on('click', this.finishSelectingUsers, this);

        // Delegate the action to select a user.
        Y.delegate("click", this.selectUser, bb.one(SELECTORS.AJAXCONTENT), SELECTORS.USERSELECT, this);
        Y.delegate("click", this.selectUser, bb.one(SELECTORS.AJAXCONTENT), SELECTORS.PICTURE, this);

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
    show : function(e) {
        var bb;
        this._usersBufferList = {};
        if (this._firstDisplay) {
            // Load the default list of users when the dialogue is loaded for the first time.
            this._firstDisplay = false;
            this.search(e, false);
        } else {
            // Leave the content as is, but reset the selection.
            this._usersBufferList = Y.clone(this.get(USP.USERFULLNAMES));
            bb = this.get('boundingBox');
            bb.all(SELECTORS.USERSELECT).set('checked', false);
            Y.Object.each(this._usersBufferList, function(v, k) {
                bb.one(SELECTORS.USERSELECT + '[name=' + USP.CHECKBOX_NAME_PREFIX + k + ']').set('checked', true);
            });
        }
        Y.namespace('M.gradereport_history.UserSelector').superclass.show.call(this);
    },

    /**
     * Search for users.
     *
     * @method search
     */
    search : function(e, append) {
        if (e) {
            e.halt();
            e.preventDefault();
        }
        var params;
        if (append) {
            this.set(USP.PAGE, this.get(USP.PAGE)+1);
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

        Y.io(M.cfg.wwwroot+this.get(USP.AJAXURL), {
            method:'POST',
            data:build_querystring(params),
            on : {
                start : this.preSearch,
                complete: this.processSearchResults,
                end : this.postSearch
            },
            context:this,
            "arguments": {      // Quoted because this is a reserved keyword.
                append: append
            }
        });
    },

    /**
     * Pre search callback.
     *
     * @method preSearch
     * @param {Mixed} unused Not sure what that is.
     * @param {Object} args The arguments passed from YUI.io()
     */
    preSearch: function(unused, args) {
        var bb = this.get('boundingBox');

        // Display the lightbox.
        bb.one(SELECTORS.LIGHTBOX).removeClass(CSS.HIDDEN);

        // Set the number of results to 'loading...'.
        if (!args.append) {
            bb.one(SELECTORS.RESULTSCOUNT).setContent(M.util.get_string('loading', 'admin'));
        }
    },

    /**
     * Post search callback.
     *
     * @method postSearch
     * @param {Mixed} unused Not sure what that is.
     * @param {Object} args The arguments passed from YUI.io()
     */
    postSearch: function(unused, args) {
        var bb = this.get('boundingBox'),
            firstAdded = bb.one(SELECTORS.FIRSTADDED);

        // Hide the lightbox.
        bb.one(SELECTORS.LIGHTBOX).addClass(CSS.HIDDEN);

        // Sets the focus on the newly added user if we are appending results.
        if (args.append && firstAdded) {
            firstAdded.one(SELECTORS.USERSELECT).focus();
        }
    },

    /**
     * Process and display the search results.
     *
     * @method processSearchResults
     */
    processSearchResults : function(tid, outcome, args) {
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
            checked,
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
            bb.one(SELECTORS.RESULTSCOUNT).setContent(M.util.get_string('errajaxsearch', COMPONENT));
            return;
        }

        // Create the div containing the users when it is a fresh search.
        if (!args.append) {
            users = create('<div class="'+CSS.USERS+'"></div>');
        } else {
            users = bb.one(SELECTORS.RESULTSUSERS);
        }

        // Compile the template for each user node.
        if (!this._userTemplate) {
            this._userTemplate = Y.Handlebars.compile(
                '<div class="{{CSS.USER}} {{selected}} clearfix" data-userid="{{userId}}">' +
                    '<div class="{{CSS.CHECKBOX}}">' +
                        '<input {{checked}} name="{{USP.CHECKBOX_NAME_PREFIX}}{{userId}}" type="checkbox"' +
                            'id="{{checkboxId}}" aria-describedby="{{checkboxId}} {{extraFieldsId}}"/>' +
                    '</div>' +
                    '<div class="{{CSS.PICTURE}}">{{{picture}}}</div>' +
                    '<div class="{{CSS.DETAILS}}">' +
                        '<div class="{{CSS.FULLNAME}}">' +
                            '<label for="{{checkboxId}}">{{fullname}}</label>' +
                        '</div>' +
                        '<div id="{{extraFieldsId}}" class="{{CSS.EXTRAFIELDS}}">{{extrafields}}</div>' +
                    '</div>' +
                '</div>'
            );
        }
        userTemplate = this._userTemplate;

        // Append the users one by one.
        count = this.get(USP.USERCOUNT);
        selected = '';
        for (i in result.response.users) {
            count++;
            user = result.response.users[i];

            // If already selected, add class.
            if (Y.Object.hasKey(this._usersBufferList, user.userid)) {
                selected = ' ' + CSS.SELECTED;
                checked = 'checked';
            } else {
                selected = '';
                checked = '';
            }

            node = create(userTemplate({
                checkboxId: Y.guid(),
                checked: checked,
                COMPONENT: COMPONENT,
                count: count,
                CSS: CSS,
                extrafields: user.extrafields,
                extraFieldsId: Y.guid(),
                fullname: user.fullname,
                picture: user.picture,
                selected: selected,
                userId: user.userid,
                USP: USP
            }));

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
                bb.one(SELECTORS.RESULTSCOUNT).setContent(M.util.get_string('noresults', 'moodle'));
                content = '';
            } else {
                if (totalUsers === 1) {
                    bb.one(SELECTORS.RESULTSCOUNT).setContent(M.util.get_string('foundoneuser', COMPONENT));
                } else {
                    bb.one(SELECTORS.RESULTSCOUNT).setContent(M.util.get_string('foundnusers', COMPONENT, totalUsers));
                }

                content = create('<div class="'+CSS.SEARCHRESULTS+'"></div>')
                    .append(users);
                if (result.response.totalusers > (this.get(USP.PAGE)+1)*this.get(USP.PERPAGE)) {
                    fetchmore = create('<div class="'+CSS.MORERESULTS+'">' +
                        '<a href="#" role="button">'+M.util.get_string('loadmoreusers', COMPONENT)+'</a></div>');
                    fetchmore.one('a').on('click', this.search, this, true);
                    fetchmore.one('a').on('key', this.search, 'space', this, true);
                    content.append(fetchmore);
                }
            }
            this.setContent(content);
        } else {
            if (totalUsers <= (this.get(USP.PAGE)+1)*this.get(USP.PERPAGE)) {
                bb.one(SELECTORS.MORERESULTS).remove();
            }
        }
    },

    /**
     * Fetch more results.
     *
     * @param {EventFacade} e The event.
     */
    fetchMore: function(e) {
        this.search(e, true);
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
     * @return Void
     */
    applySelection: function(e) {
        var userIds = Y.Object.values(this._usersBufferList);
        this.set(USP.SELECTEDUSERS, userIds);
        this.set(USP.USERFULLNAMES, this._usersBufferList);
        this.setnamedisplay();
        Y.one(SELECTORS.USERIDS).set('value', userIds.join());
    },

    /**
     * Loads the users from the form.
     *
     * @method loadUsersFromForm
     * @return Void
     */
    loadUsersFromForm: function() {
        var list = Y.one(SELECTORS.USERIDS).get('value').split(',');
        if (list[0] === '') {
            list = [];
        }
        this.set(USP.SELECTEDUSERS, list);
    },

    /**
     * Select a user.
     *
     * @method SelectUser
     * @param {EventFacade} e The event.
     */
    selectUser : function(e) {
        var user = e.currentTarget.ancestor(SELECTORS.USER),
            checkbox = user.one(SELECTORS.USERSELECT),
            fullname = user.one(SELECTORS.FULLNAME).get('innerHTML'),
            checked = checkbox.get('checked'),
            userId = user.getData('userid');

        if (e.currentTarget !== checkbox) {
            // We triggered the selection from another node, so we need to change the checkbox value.
            checked = !checked;
            checkbox.set('checked', checked);
        }

        if (checked) {
            // Selecting the user.
            this._usersBufferList[userId] = fullname;
            user.addClass(CSS.SELECTED);
        } else {
            // De-selecting the user.
            delete this._usersBufferList[userId];
            delete this._usersBufferList[parseInt(userId, 10)]; // Also remove number'd keys.
            user.removeClass(CSS.SELECTED);
        }
    },

    /**
     * Set the content of the dialogue.
     *
     * @method setContent
     * @param {String} content The content.
     */
    setContent: function(content) {
        this.get('boundingBox').one(SELECTORS.AJAXCONTENT).setContent(content);
    },

    /**
     * Display the names of the selected users in the form.
     *
     * @method setnamedisplay
     */
    setnamedisplay: function() {
        var namelist = Y.Object.values(this.get(USP.USERFULLNAMES));
        Y.one(SELECTORS.SELECTEDNAMES).set('innerHTML', namelist.join(', '));
        Y.one(SELECTORS.USERFULLNAMES).set('value', namelist.join());
    }
}, {
    NAME : USP.NAME,
    CSS_PREFIX : USP.CSS_PREFIX,
    ATTRS : {

        /**
         * List of extra classes.
         *
         * @attribute extraClasses
         * @type Array
         */
        extraClasses: {
            value: [
                'gradereport_history_usp'
            ]
        },

        /**
         * The header.
         *
         * @attribute title
         * @type String
         */
        title: {
            validator: Y.Lang.isString,
            value: M.util.get_string('selectusers', COMPONENT)
        },

        /**
         * Whether to focus on the target that caused the Widget to be shown.
         *
         * @attribute focusOnPreviousTargetAfterHide
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
         * @type {String|Number}
         */
        width: {
            value: '500px'
        },

        /**
         * The current page URL.
         *
         * @attribute url
         * @type String
         */
        url : {
            validator : Y.Lang.isString
        },

        /**
         * The URL to the Ajax file.
         *
         * @attribute ajaxurl
         * @type String
         */
        ajaxurl : {
            validator : Y.Lang.isString
        },

        /**
         * IDs of the selected users.
         *
         * @attribute selectedusers
         * @type Array
         */
        selectedusers : {
            validator : Y.Lang.isArray,
            value : null
        },

        /**
         * The names of the selected users.
         *
         * @attribute userfullnames
         * @type Object
         */
        userfullnames : {
            validator : Y.Lang.isObject,
            value : null
        },

        /**
         * The course ID.
         *
         * @attribute courseid
         * @type Number
         */
        courseid : {
            value : null
        },

        /**
         * Array of parameters.
         *
         * @attribute params
         * @type Array
         */
        params : {
            validator : Y.Lang.isArray,
            value : []
        },

        /**
         * The page we are on.
         *
         * @attribute page
         * @type Number
         */
        page : {
            validator : Y.Lang.isNumber,
            value : 0
        },

        /**
         * The number of users displayed.
         *
         * @attribute userCount
         * @type Number
         */
        userCount : {
            value : 0,
            validator : Y.Lang.isNumber
        },

        /**
         * The number of results per page.
         *
         * @attribute perPage
         * @type Number
         */
        perPage : {
            value: 25,
            Validator: Y.Lang.isNumber
        }

    }
});
// Y.augment(Y.namespace('M.gradereport_history').UserSelector, Y.EventTarget);

Y.Base.modifyAttrs(Y.namespace('M.gradereport_history.UserSelector'), {

    /**
     * Boolean indicating whether or not the Widget is visible.
     *
     * @attribute visible
     * @default true
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
