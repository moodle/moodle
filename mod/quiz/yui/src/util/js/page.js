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
    CONSTANTS: {
        PAGEIDPREFIX : 'page-',
        PAGENUMBERPREFIX : 'Page '
    },
    SELECTORS: {
        PAGE: 'li.page',
        INSTANCENAME: '.instancename'
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
     * Determines the page ID for the provided page.
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
        // We perform a simple substitution operation to get the ID.
        var number = page.get('text').replace(
                this.CONSTANTS.PAGENUMBERPREFIX, '');

        // Attempt to validate the ID.
        number = parseInt(number, 10);
        if (typeof number === 'number' && isFinite(number)) {
            return number;
        }
        return false;
    }
};
