YUI.add('moodle-mod_quiz-util-page', function (Y, NAME) {

/* global YUI */

/**
 * A collection of utility classes for use with pages.
 *
 * @module moodle-mod_quiz-util
 * @submodule moodle-mod_quiz-util-page
 */

Y.namespace('Moodle.mod_quiz.util.page');

/**
 * A collection of utility classes for use with pages.
 *
 * @class Moodle.mod_quiz.util.page
 * @static
 */
Y.Moodle.mod_quiz.util.page = {
    CSS: {
        PAGE: 'page'
    },
    CONSTANTS: {
        ACTIONMENUIDPREFIX: 'action-menu-',
        ACTIONMENUBARIDSUFFIX: '-menubar',
        ACTIONMENUMENUIDSUFFIX: '-menu',
        PAGEIDPREFIX: 'page-',
        PAGENUMBERPREFIX: M.util.get_string('page', 'moodle') + ' '
    },
    SELECTORS: {
        ACTIONMENU: 'div.moodle-actionmenu',
        ACTIONMENUBAR: '.menubar',
        ACTIONMENUMENU: '.menu',
        ADDASECTION: '[data-action="addasection"]',
        PAGE: 'li.page',
        INSTANCENAME: '.instancename',
        NUMBER: 'h4'
    },

    /**
     * Retrieve the page item from one of it's child Nodes.
     *
     * @method getPageFromComponent
     * @param pagecomponent {Node} The component Node.
     * @return {Node|null} The Page Node.
     */
    getPageFromComponent: function(pagecomponent) {
        return Y.one(pagecomponent).ancestor(this.SELECTORS.PAGE, true);
    },

    /**
     * Retrieve the page item from one of it's previous siblings.
     *
     * @method getPageFromSlot
     * @param pagecomponent {Node} The component Node.
     * @return {Node|null} The Page Node.
     */
    getPageFromSlot: function(slot) {
        return Y.one(slot).previous(this.SELECTORS.PAGE);
    },

    /**
     * Returns the page ID for the provided page.
     *
     * @method getId
     * @param page {Node} The page to find an ID for.
     * @return {Number|false} The ID of the page in question or false if no ID was found.
     */
    getId: function(page) {
        // We perform a simple substitution operation to get the ID.
        var id = page.get('id').replace(
                this.CONSTANTS.PAGEIDPREFIX, '');

        // Attempt to validate the ID.
        id = parseInt(id, 10);
        if (typeof id === 'number' && isFinite(id)) {
            return id;
        }
        return false;
    },

    /**
     * Updates the page id for the provided page.
     *
     * @method setId
     * @param page {Node} The page to update the number for.
     * @param id int The id value.
     * @return void
     */
    setId: function(page, id) {
        page.set('id', this.CONSTANTS.PAGEIDPREFIX + id);
    },

    /**
     * Determines the page name for the provided page.
     *
     * @method getName
     * @param page {Node} The page to find a name for.
     * @return {string|false} The name of the page in question or false if no ID was found.
     */
    getName: function(page) {
        var instance = page.one(this.SELECTORS.INSTANCENAME);
        if (instance) {
            return instance.get('firstChild').get('data');
        }
        return null;
    },

    /**
     * Determines the page number for the provided page.
     *
     * @method getNumber
     * @param page {Node} The page to find a number for.
     * @return {Number|false} The number of the page in question or false if no number was found.
     */
    getNumber: function(page) {
        // We perform a simple substitution operation to get the number.
        var number = page.one(this.SELECTORS.NUMBER).get('text').replace(
                this.CONSTANTS.PAGENUMBERPREFIX, '');

        // Attempt to validate the ID.
        number = parseInt(number, 10);
        if (typeof number === 'number' && isFinite(number)) {
            return number;
        }
        return false;
    },

    /**
     * Updates the page number for the provided page.
     *
     * @method setNumber
     * @param page {Node} The page to update the number for.
     * @return void
     */
    setNumber: function(page, number) {
        page.one(this.SELECTORS.NUMBER).set('text', this.CONSTANTS.PAGENUMBERPREFIX + number);
    },

    /**
     * Returns a list of all page elements.
     *
     * @method getPages
     * @return {node[]} An array containing page nodes.
     */
    getPages: function() {
        return Y.all(Y.Moodle.mod_quiz.util.slot.SELECTORS.PAGECONTENT + ' ' +
                     Y.Moodle.mod_quiz.util.slot.SELECTORS.SECTIONUL + ' ' +
                    this.SELECTORS.PAGE);
    },

    /**
     * Is the given element a page element?
     *
     * @method isPage
     * @param page Page node
     * @return boolean
     */
    isPage: function(page) {
        if (!page) {
            return false;
        }
        return page.hasClass(this.CSS.PAGE);
    },

    /**
     * Does the page have atleast one slot?
     *
     * @method isEmpty
     * @param page Page node
     * @return boolean
     */
    isEmpty: function(page) {
        var activity = page.next('li.activity');
        if (!activity) {
            return true;
        }
        return !activity.hasClass('slot');
    },

    /**
     * Add a page and related elements to the list of slots.
     *
     * @method add
     * @param beforenode Int | Node | HTMLElement | String to add
     * @return page Page node
     */
    add: function(beforenode) {
        var pagenumber = this.getNumber(this.getPageFromSlot(beforenode)) + 1;
        var pagehtml = M.mod_quiz.resource_toolbox.get('config').pagehtml;

        // Normalise the page number.
        pagehtml = pagehtml.replace(/%%PAGENUMBER%%/g, pagenumber);

        // Create the page node.
        var page = Y.Node.create(pagehtml);

        // Assign is as a drop target.
        YUI().use('dd-drop', function(Y) {
            var drop = new Y.DD.Drop({
                node: page,
                groups: M.mod_quiz.dragres.groups
            });
            page.drop = drop;
        });

        // Insert in the correct place.
        beforenode.insert(page, 'after');

        // Enhance the add menu to make if fully visible and clickable.
        if (typeof M.core.actionmenu !== "undefined") {
            M.core.actionmenu.newDOMNode(page);
        }
        return page;
    },

    /**
     * Remove a page and related elements from the list of slots.
     *
     * @method remove
     * @param page Page node
     * @return void
     */
    remove: function(page, keeppagebreak) {
        // Remove page break from previous slot.
        var previousslot = page.previous(Y.Moodle.mod_quiz.util.slot.SELECTORS.SLOT);
        if (!keeppagebreak && previousslot) {
            Y.Moodle.mod_quiz.util.slot.removePageBreak(previousslot);
        }
        page.remove();
    },

    /**
     * Reset the order of the numbers given to each page.
     *
     * @method reorderPages
     * @return void
     */
    reorderPages: function() {
        // Get list of page nodes.
        var pages = this.getPages();
        var currentpagenumber = 0;
        // Loop through pages incrementing the number each time.
        pages.each(function(page) {
            // Is the page empty?
            if (this.isEmpty(page)) {
                var keeppagebreak = page.next('li.slot') ? true : false;
                this.remove(page, keeppagebreak);
                return;
            }

            currentpagenumber++;
            // Set page number.
            this.setNumber(page, currentpagenumber);
            this.setId(page, currentpagenumber);
        }, this);

        // Reorder action menus
        this.reorderActionMenus();
    },

    /**
     * Reset the order of the numbers given to each action menu.
     *
     * @method reorderActionMenus
     * @return void
     */
    reorderActionMenus: function() {
        // Get list of action menu nodes.
        var actionmenus = this.getActionMenus();
        // Loop through pages incrementing the number each time.
        actionmenus.each(function(actionmenu, key) {
            var previousActionMenu = actionmenus.item(key - 1),
                previousActionMenunumber = 0;
            if (previousActionMenu) {
                previousActionMenunumber = this.getActionMenuId(previousActionMenu);
            }
            var id = previousActionMenunumber + 1;

            // Set menu id.
            this.setActionMenuId(actionmenu, id);

            // Update action-menu-1-menubar
            var menubar = actionmenu.one(this.SELECTORS.ACTIONMENUBAR);
            menubar.set('id', this.CONSTANTS.ACTIONMENUIDPREFIX + id + this.CONSTANTS.ACTIONMENUBARIDSUFFIX);

            // Update action-menu-1-menu
            var menumenu = actionmenu.one(this.SELECTORS.ACTIONMENUMENU);
            menumenu.set('id', this.CONSTANTS.ACTIONMENUIDPREFIX + id + this.CONSTANTS.ACTIONMENUMENUIDSUFFIX);

            // Update the URL of the add-section action.
            menumenu.one(this.SELECTORS.ADDASECTION).set('href',
                menumenu.one(this.SELECTORS.ADDASECTION).get('href').replace(/\baddsectionatpage=\d+\b/, 'addsectionatpage=' + id));

        }, this);
    },

    /**
     * Returns a list of all page elements.
     *
     * @method getActionMenus
     * @return {node[]} An array containing page nodes.
     */
    getActionMenus: function() {
        return Y.all(Y.Moodle.mod_quiz.util.slot.SELECTORS.PAGECONTENT + ' ' +
                     Y.Moodle.mod_quiz.util.slot.SELECTORS.SECTIONUL + ' ' +
                     this.SELECTORS.ACTIONMENU);
    },

    /**
     * Returns the ID for the provided action menu.
     *
     * @method getId
     * @param actionmenu {Node} The actionmenu to find an ID for.
     * @return {Number|false} The ID of the actionmenu in question or false if no ID was found.
     */
    getActionMenuId: function(actionmenu) {
        // We perform a simple substitution operation to get the ID.
        var id = actionmenu.get('id').replace(
                this.CONSTANTS.ACTIONMENUIDPREFIX, '');

        // Attempt to validate the ID.
        id = parseInt(id, 10);
        if (typeof id === 'number' && isFinite(id)) {
            return id;
        }
        return false;
    },

    /**
     * Updates the page id for the provided page.
     *
     * @method setId
     * @param page {Node} The page to update the number for.
     * @param id int The id value.
     * @return void
     */
    setActionMenuId: function(actionmenu, id) {
        actionmenu.set('id', this.CONSTANTS.ACTIONMENUIDPREFIX + id);
    }
};


}, '@VERSION@', {"requires": ["node", "moodle-mod_quiz-util-base"]});
