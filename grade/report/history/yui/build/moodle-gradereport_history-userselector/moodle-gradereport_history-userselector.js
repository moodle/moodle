YUI.add('moodle-gradereport_history-userselector', function (Y, NAME) {

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
    CLOSEBTN : 'close-button',
    CONTENT : 'usp-content',
    DETAILS : 'details',
    EXTRAFIELDS : 'extrafields',
    FOOTER : 'usp-footer',
    FULLNAME : 'fullname',
    HIDDEN : 'hidden',
    LIGHTBOX : 'usp-loading-lightbox',
    LOADINGICON : 'loading-icon',
    MORERESULTS : 'usp-more-results',
    OPTIONS : 'options',
    PICTURE : 'picture',
    SEARCH : 'usp-search',
    SEARCHBTN : 'usp-search-btn',
    SEARCHFIELD : 'usp-search-field',
    SEARCHRESULTS : 'usp-search-results',
    SELECTED : 'selected',
    TOTALUSERS : 'totalusers',
    USER : 'user',
    USERS : 'users',
    WRAP : 'usp-wrap'
};
var SELECTORS = {
    AJAXCONTENT: '.' + CSS.AJAXCONTENT,
    FOOTER: '.' + CSS.FOOTER,
    FOOTERCLOSE: '.' + CSS.FOOTER + ' .' + CSS.CLOSEBTN + ' input',
    FULLNAME: '.' + CSS.FULLNAME + ' label',
    LIGHTBOX: '.' + CSS.LIGHTBOX,
    MORERESULTS: '.' + CSS.MORERESULTS,
    OPTIONS: '.' + CSS.OPTIONS,
    RESULTSUSERS: '.' + CSS.SEARCHRESULTS + ' .' + CSS.USERS,
    SEARCHBTN: '.' + CSS.SEARCHBTN,
    SEARCHFIELD: '.' + CSS.SEARCHFIELD,
    SELECTEDNAMES: '.felement .selectednames',
    TRIGGER: '.gradereport_history_plugin input.selectortrigger',
    USER: '.' + CSS.USER,
    USERFULLNAMES: 'input[name="userfullnames"]',
    USERIDS: 'input[name="userids"]',
    USERSELECT: '.' + CSS.USER + ' .' + CSS.CHECKBOX + ' input[type=checkbox]'
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
            list,
            params,
            tpl;

        tpl = Y.Handlebars.compile(
                '<div class="{{CSS.WRAP}}">' +
                    '<div class="{{CSS.CONTENT}}" aria-live="polite">' +
                        '<div class="{{CSS.AJAXCONTENT}}"></div>' +
                        '<div class="{{CSS.LIGHTBOX}} {{CSS.HIDDEN}}">' +
                            '<img class="{{CSS.LOADINGICON}}" alt="{{get_string "loading" "admin"}}"' +
                                'src="{{{loadingIcon}}}">' +
                        '</div>' +
                    '</div>' +
                    '<div class="{{CSS.FOOTER}}">' +
                        '<div class="{{CSS.SEARCH}}">' +
                            '<label for="{{CSS.IDENROLUSERSEARCH}}" class="{{CSS.ACCESSHIDE}}">' +
                                '{{get_string "usersearch" "enrol"}}' +
                            '</label>' +
                            '<input type="text" class="{{CSS.SEARCHFIELD}}" value="" />' +
                            '<input type="button" class="{{CSS.SEARCHBTN}}"' +
                                'value="{{get_string "usersearch" "enrol"}}">' +
                        '</div>' +
                        '<div class="{{CSS.CLOSEBTN}}">' +
                            '<input type="button" value="{{get_string "finishselectingusers" COMPONENT}}">' +
                        '</div>' +
                    '<div>' +
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

        this.set(USP.SEARCH, bb.one(SELECTORS.SEARCHFIELD));
        this.set(USP.SEARCHBTN, bb.one(SELECTORS.SEARCHBTN));

        // Load the list of users.
        this.loadUsersFromForm();

        // Add the event on the button that opens the dialogue.
        Y.one(SELECTORS.TRIGGER).on('click', this.show, this);

        // The button to finalize the selection.
        bb.one(SELECTORS.FOOTERCLOSE).on('click', this.finishSelectingUsers, this);

        params = this.get(USP.PARAMS);
        params.id = this.get(USP.COURSEID);
        this.set(USP.PARAMS, params);

        Y.on('key', this.preSearch, this.get(USP.SEARCH), 'down:13', this);
        this.get(USP.SEARCHBTN).on('click', this.preSearch, this);
    },

    /**
     * Before the search starts.
     *
     * @method preSearch
     */
    preSearch : function(e) {
        this.search(null, false);
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
        params.search = this.get(USP.SEARCH).get('value');
        params.page = this.get(USP.PAGE);
        params.perpage = this.get(USP.PERPAGE);

        Y.io(M.cfg.wwwroot+this.get(USP.AJAXURL), {
            method:'POST',
            data:build_querystring(params),
            on : {
                start : this.displayLoading,
                complete: this.processSearchResults,
                end : this.removeLoading
            },
            context:this,
            arguments:{
                append:append
            }
        });
    },

    /**
     * Display the loading info.
     *
     * @method displayLoading
     */
    displayLoading : function() {
        var bb = this.get('boundingBox');
        bb.one(SELECTORS.LIGHTBOX).removeClass(CSS.HIDDEN);
        bb.one(SELECTORS.AJAXCONTENT).addClass(CSS.HIDDEN);
        bb.one(SELECTORS.FOOTER).addClass(CSS.HIDDEN);
    },

    /**
     * Hide the loading info.
     *
     * @method removeLoading
     */
    removeLoading : function() {
        var bb = this.get('boundingBox');
        bb.one(SELECTORS.LIGHTBOX).addClass(CSS.HIDDEN);
        bb.one(SELECTORS.AJAXCONTENT).removeClass(CSS.HIDDEN);
        bb.one(SELECTORS.FOOTER).removeClass(CSS.HIDDEN);
        this.centerDialogue();
    },

    /**
     * Process and display the search results.
     *
     * @method processSearchResults
     */
    processSearchResults : function(tid, outcome, args) {
        var result = false,
            users,
            userTemplate,
            count,
            selected,
            i,
            node,
            usersstr,
            content,
            fetchmore,
            checked;
        try {
            result = Y.JSON.parse(outcome.responseText);
            if (result.error) {
                return new M.core.ajaxException(result);
            }
        } catch (e) {
            new M.core.exception(e);
        }
        if (!result.success) {
            this.setContent = M.util.get_string('errajaxsearch', 'enrol');
        }

        if (!args.append) {
            users = create('<div class="'+CSS.USERS+'"></div>');
        } else {
            users = this.get('boundingBox').one(SELECTORS.RESULTSUSERS);
        }

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
            users.append(node);
        }
        this.set(USP.USERCOUNT, count);
        if (!args.append) {
            if (result.response.totalusers == '1') {
                usersstr = M.util.get_string('ajaxoneuserfound', 'enrol');
            } else {
                usersstr = M.util.get_string('ajaxxusersfound','enrol', result.response.totalusers);
            }
            content = create('<div class="'+CSS.SEARCHRESULTS+'"></div>')
                .append(create('<div class="'+CSS.TOTALUSERS+'">'+usersstr+'</div>'))
                .append(users);
            if (result.response.totalusers > (this.get(USP.PAGE)+1)*this.get(USP.PERPAGE)) {
                fetchmore = create('<div class="'+CSS.MORERESULTS+'"><a href="#">'+M.util.get_string('ajaxnext25', 'enrol')+'</a></div>');
                fetchmore.on('click', this.search, this, true);
                content.append(fetchmore);
            }
            this.setContent(content);

            // Delegate the action when selecting a user.
            Y.delegate("click", this.selectUser, users, SELECTORS.USERSELECT, this);
        } else {
            if (result.response.totalusers <= (this.get(USP.PAGE)+1)*this.get(USP.PERPAGE)) {
                this.get('boundingBox').one(SELECTORS.MORERESULTS).remove();
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
            fullname = user.one(SELECTORS.FULLNAME).get('innerHTML'),
            checked = e.currentTarget.get('checked'),
            userId = user.getData('userid');

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
         * The search field.
         *
         * @attribute search
         * @type Node
         */
        search : {
            setter : function(node) {
                var n = Y.one(node);
                if (!n) {
                    Y.fail(USP.NAME+': invalid search node set');
                }
                return n;
            }
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

    draggable: {
        value: true
    }

});

Y.namespace('M.gradereport_history.UserSelector').init = function(cfg) {
    return new USERSELECTOR(cfg);
};


}, '@VERSION@', {
    "requires": [
        "dd-plugin",
        "escape",
        "event-delegate",
        "event-key",
        "handlebars",
        "io-base",
        "json-parse",
        "moodle-core-notification-dialogue",
        "overlay"
    ]
});
