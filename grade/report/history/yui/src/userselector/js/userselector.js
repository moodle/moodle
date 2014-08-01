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
    CLOSE : 'close',
    CLOSEBTN : 'close-button',
    CONTENT : 'usp-content',
    COUNT : 'count',
    DESELECT : 'deselect',
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
    SELECT : 'select',
    SELECTED : 'selected',
    TOTALUSERS : 'totalusers',
    USER : 'user',
    USERS : 'users',
    WRAP : 'usp-wrap'
};
var SELECTORS = {
    AJAXCONTENT: '.' + CSS.AJAXCONTENT,
    DESELECT: '.' + CSS.DESELECT,
    FOOTER: '.' + CSS.FOOTER,
    FOOTERCLOSE: '.' + CSS.FOOTER + ' .' + CSS.CLOSEBTN + ' input',
    FULLNAME: '.' + CSS.FULLNAME,
    LIGHTBOX: '.' + CSS.LIGHTBOX,
    MORERESULTS: '.' + CSS.MORERESULTS,
    OPTIONS: '.' + CSS.OPTIONS,
    RESULTSUSERS: '.' + CSS.SEARCHRESULTS + ' .' + CSS.USERS,
    SEARCHBTN: '.' + CSS.SEARCHBTN,
    SEARCHFIELD: '.' + CSS.SEARCHFIELD,
    SELECT: '.' + CSS.SELECT,
    SELECTEDNAMES: '.felement .selectednames',
    TRIGGER: '.gradereport_history_plugin input.selectortrigger',
    USER: '.' + CSS.USER,
    USERDESELECT: '.' + CSS.USER + ' .' + CSS.DESELECT,
    USERFULLNAMES: 'input[name="userfullnames"]',
    USERIDS: 'input[name="userids"]',
    USERSELECT: '.' + CSS.USER + ' .' + CSS.SELECT
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
                    '<div class="{{CSS.CONTENT}}">' +
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
        list = Y.one(SELECTORS.USERIDS).get('value').split(',');
        if (list[0] === '') {
            list = [];
        }
        this.set(USP.SELECTEDUSERS, list);

        list = [];
        if (this.get(USP.USERFULLNAMES) !== null) {
            Y.each(this.get(USP.USERFULLNAMES), function(value, key) {
                list[key] = value;
            });
        }
        this.set(USP.USERFULLNAMES, list);

        Y.one(SELECTORS.TRIGGER).on('click', this.show, this);

        bb.one(SELECTORS.FOOTERCLOSE).on('click', this.hide, this);
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
        if (this._firstDisplay) {
            // Load the default list of users when the dialogue is loaded for the first time.
            this._firstDisplay = false;
            this.search(e, false);
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
            fetchmore;
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
                    '<div class="{{CSS.COUNT}}">{{count}}</div>' +
                    '<div class="{{CSS.PICTURE}}">{{{picture}}}</div>' +
                    '<div class="{{CSS.DETAILS}}">' +
                        '<div class="{{CSS.FULLNAME}}">{{fullname}}</div>' +
                        '<div class="{{CSS.EXTRAFIELDS}}">{{extrafields}}</div>' +
                    '</div>' +
                    '<div class="{{CSS.OPTIONS}}">' +
                        '<input type="button" class="{{CSS.SELECT}}" value="{{get_string "select" "moodle"}}">' +
                        '<input type="button" class="{{CSS.DESELECT}}" value="{{get_string "deselect" COMPONENT}}">' +
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
            if (this.get(USP.SELECTEDUSERS).indexOf(user.userid) >= 0) {
                selected = ' '+CSS.SELECTED;
            } else {
                selected = '';
            }

            node = create(userTemplate({
                COMPONENT: COMPONENT,
                count: count,
                CSS: CSS,
                extrafields: user.extrafields,
                fullname: user.fullname,
                picture: user.picture,
                selected: selected,
                userId: user.userid
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
            Y.delegate("click", this.selectUser, users, SELECTORS.USERSELECT, this, args);
            Y.delegate("click", this.deselectUser, users, SELECTORS.USERDESELECT, this, args);
        } else {
            if (result.response.totalusers <= (this.get(USP.PAGE)+1)*this.get(USP.PERPAGE)) {
                this.get('boundingBox').one(SELECTORS.MORERESULTS).remove();
            }
        }
    },

    /**
     * Deselect a user.
     *
     * @method deselectUser
     * @param {EventFacade} e The event.
     * @param {Object} args A list of argments.
     */
    deselectUser : function(e, args) {
        var user = e.currentTarget.ancestor(SELECTORS.USER);
        var list = this.get(USP.SELECTEDUSERS);

        // Find and remove item from the array.
        var i = list.indexOf(user.getData('userid'));
        if (i != -1) {
            list.splice(i, 1);
        }
        this.set(USP.SELECTEDUSERS, list);
        Y.one(SELECTORS.USERIDS).set('value', list.join());

        var namelist = this.get(USP.USERFULLNAMES);
        delete namelist[user.getData('userid')];
        this.set(USP.USERFULLNAMES, namelist);
        this.setnamedisplay();

        user.removeClass(CSS.SELECTED);
    },

    /**
     * Select a user.
     *
     * @method SelectUser
     * @param {EventFacade} e The event.
     * @param {Object} args A list of argments.
     */
    selectUser : function(e, args) {
        var user = e.currentTarget.ancestor(SELECTORS.USER);

        // Add id to the userids element and internal js list.
        var list = this.get(USP.SELECTEDUSERS);
        list.push(user.getData('userid'));
        this.set(USP.SELECTEDUSERS, list);

        var fullname = user.one(SELECTORS.FULLNAME).get('innerHTML');
        var namelist = this.get(USP.USERFULLNAMES);
        namelist[user.getData('userid')] = fullname;
        this.set(USP.USERFULLNAMES, namelist);
        this.setnamedisplay();

        Y.one(SELECTORS.USERIDS).set('value', list.join());
        user.addClass(CSS.SELECTED);
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
        var namelist = this.get(USP.USERFULLNAMES);
        namelist = namelist.filter(function(x) {
             return x;
        });
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
