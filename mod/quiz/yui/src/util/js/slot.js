/**
 * A collection of utility classes for use with slots.
 *
 * @module moodle-mod_quiz-util
 * @submodule moodle-mod_quiz-util-slot
 */

Y.namespace('Moodle.mod_quiz.util.slot');

/**
 * A collection of utility classes for use with slots.
 *
 * @class Moodle.mod_quiz.util.slot
 * @static
 */
Y.Moodle.mod_quiz.util.slot = {
    CSS: {
        SLOT: 'slot',
        QUESTIONTYPEDESCRIPTION: 'qtype_description',
        CANNOT_DEPEND: 'question_dependency_cannot_depend'
    },
    CONSTANTS: {
        SLOTIDPREFIX: 'slot-',
        QUESTION: M.util.get_string('question', 'moodle')
    },
    SELECTORS: {
        SLOT: 'li.slot',
        INSTANCENAME: '.instancename',
        NUMBER: 'span.slotnumber',
        PAGECONTENT: 'div#page-content',
        PAGEBREAK: 'span.page_split_join_wrapper',
        ICON: '.icon',
        QUESTIONTYPEDESCRIPTION: '.qtype_description',
        SECTIONUL: 'ul.section',
        DEPENDENCY_WRAPPER: '.question_dependency_wrapper',
        DEPENDENCY_LINK: '.question_dependency_wrapper .cm-edit-action',
        DEPENDENCY_ICON: '.question_dependency_wrapper .icon'
    },

    /**
     * Retrieve the slot item from one of it's child Nodes.
     *
     * @method getSlotFromComponent
     * @param slotcomponent {Node} The component Node.
     * @return {Node|null} The Slot Node.
     */
    getSlotFromComponent: function(slotcomponent) {
        return Y.one(slotcomponent).ancestor(this.SELECTORS.SLOT, true);
    },

    /**
     * Determines the slot ID for the provided slot.
     *
     * @method getId
     * @param slot {Node} The slot to find an ID for.
     * @return {Number|false} The ID of the slot in question or false if no ID was found.
     */
    getId: function(slot) {
        // We perform a simple substitution operation to get the ID.
        var id = slot.get('id').replace(
                this.CONSTANTS.SLOTIDPREFIX, '');

        // Attempt to validate the ID.
        id = parseInt(id, 10);
        if (typeof id === 'number' && isFinite(id)) {
            return id;
        }
        return false;
    },

    /**
     * Determines the slot name for the provided slot.
     *
     * @method getName
     * @param slot {Node} The slot to find a name for.
     * @return {string|false} The name of the slot in question or false if no ID was found.
     */
    getName: function(slot) {
        var instance = slot.one(this.SELECTORS.INSTANCENAME);
        if (instance) {
            return instance.get('firstChild').get('data');
        }
        return null;
    },

    /**
     * Determines the slot number for the provided slot.
     *
     * @method getNumber
     * @param slot {Node} The slot to find the number for.
     * @return {Number|false} The number of the slot in question or false if no number was found.
     */
    getNumber: function(slot) {
        if (!slot) {
            return false;
        }
        // We perform a simple substitution operation to get the number.
        var number = slot.one(this.SELECTORS.NUMBER).get('text').replace(
                        this.CONSTANTS.QUESTION, '');
        // Attempt to validate the ID.
        number = parseInt(number, 10);
        if (typeof number === 'number' && isFinite(number)) {
            return number;
        }
        return false;
    },

    /**
     * Updates the slot number for the provided slot.
     *
     * @method setNumber
     * @param slot {Node} The slot to update the number for.
     * @return void
     */
    setNumber: function(slot, number) {
        var numbernode = slot.one(this.SELECTORS.NUMBER);
        numbernode.setHTML('<span class="accesshide">' + this.CONSTANTS.QUESTION + '</span> ' + number);
    },

    /**
     * Returns a list of all slot elements on the page.
     *
     * @method getSlots
     * @return {node[]} An array containing slot nodes.
     */
    getSlots: function() {
        return Y.all(this.SELECTORS.PAGECONTENT + ' ' + this.SELECTORS.SECTIONUL + ' ' + this.SELECTORS.SLOT);
    },

    /**
     * Returns a list of all slot elements on the page that have numbers. Excudes description questions.
     *
     * @method getSlots
     * @return {node[]} An array containing slot nodes.
     */
    getNumberedSlots: function() {
        var selector = this.SELECTORS.PAGECONTENT + ' ' + this.SELECTORS.SECTIONUL;
            selector += ' ' + this.SELECTORS.SLOT + ':not(' + this.SELECTORS.QUESTIONTYPEDESCRIPTION + ')';
        return Y.all(selector);
    },

    /**
     * Returns the previous slot to the given slot.
     *
     * @method getPrevious
     * @param slot Slot node
     * @return {node|false} The previous slot node or false.
     */
    getPrevious: function(slot) {
        return slot.previous(this.SELECTORS.SLOT);
    },

    /**
     * Returns the previous numbered slot to the given slot.
     *
     * Ignores slots containing description question types.
     *
     * @method getPrevious
     * @param slot Slot node
     * @return {node|false} The previous slot node or false.
     */
    getPreviousNumbered: function(slot) {
        var previous = slot.previous(this.SELECTORS.SLOT + ':not(' + this.SELECTORS.QUESTIONTYPEDESCRIPTION + ')');
        if (previous) {
            return previous;
        }

        var section = slot.ancestor('li.section').previous('li.section');
        while (section) {
            var questions = section.all(this.SELECTORS.SLOT + ':not(' + this.SELECTORS.QUESTIONTYPEDESCRIPTION + ')');
            if (questions.size() > 0) {
                return questions.item(questions.size() - 1);
            }
            section = section.previous('li.section');
        }
        return false;
    },

    /**
     * Reset the order of the numbers given to each slot.
     *
     * @method reorderSlots
     * @return void
     */
    reorderSlots: function() {
        // Get list of slot nodes.
        var slots = this.getSlots();
        // Loop through slots incrementing the number each time.
        slots.each(function(slot) {

            if (!Y.Moodle.mod_quiz.util.page.getPageFromSlot(slot)) {
                // Move the next page to the front.
                var nextpage = slot.next(Y.Moodle.mod_quiz.util.page.SELECTORS.PAGE);
                slot.swap(nextpage);
            }

            var previousSlot = this.getPreviousNumbered(slot),
                previousslotnumber = 0;
            if (slot.hasClass(this.CSS.QUESTIONTYPEDESCRIPTION)) {
                return;
            }

            if (previousSlot) {
                previousslotnumber = this.getNumber(previousSlot);
            }

            // Set slot number.
            this.setNumber(slot, previousslotnumber + 1);
        }, this);
    },

    /**
     * Add class only-has-one-slot to those sections that need it.
     *
     * @method updateOneSlotSections
     * @return void
     */
    updateOneSlotSections: function() {
        Y.all('.mod-quiz-edit-content ul.slots li.section').each(function(section) {
            if (section.all(this.SELECTORS.SLOT).size() > 1) {
                section.removeClass('only-has-one-slot');
            } else {
                section.addClass('only-has-one-slot');
            }
        }, this);
    },

    /**
     * Remove a slot and related elements from the list of slots.
     *
     * @method remove
     * @param slot Slot node
     * @return void
     */
    remove: function(slot) {
        var page = Y.Moodle.mod_quiz.util.page.getPageFromSlot(slot);
        slot.remove();
        // Is the page empty.
        if (!Y.Moodle.mod_quiz.util.page.isEmpty(page)) {
            return;
        }
        // If so remove it. Including add menu and page break.
        Y.Moodle.mod_quiz.util.page.remove(page);
    },

    /**
     * Returns a list of all page break elements on the page.
     *
     * @method getPageBreaks
     * @return {node[]} An array containing page break nodes.
     */
    getPageBreaks: function() {
        var selector = this.SELECTORS.PAGECONTENT + ' ' + this.SELECTORS.SECTIONUL;
            selector += ' ' + this.SELECTORS.SLOT + this.SELECTORS.PAGEBREAK;
        return Y.all(selector);
    },

    /**
     * Retrieve the page break element item from the given slot.
     *
     * @method getPageBreak
     * @param slot Slot node
     * @return {Node|null} The Page Break Node.
     */
    getPageBreak: function(slot) {
        return Y.one(slot).one(this.SELECTORS.PAGEBREAK);
    },

    /**
     * Add a page break and related elements to the list of slots.
     *
     * @method addPageBreak
     * @param beforenode Int | Node | HTMLElement | String to add
     * @return pagebreak PageBreak node
     */
    addPageBreak: function(slot) {
        var nodetext = M.mod_quiz.resource_toolbox.get('config').addpageiconhtml;
        nodetext = nodetext.replace('%%SLOT%%', this.getNumber(slot));
        var pagebreak = Y.Node.create(nodetext);
        slot.one('div').insert(pagebreak, 'after');
        return pagebreak;
    },

    /**
     * Remove a pagebreak from the given slot.
     *
     * @method removePageBreak
     * @param slot Slot node
     * @return boolean
     */
    removePageBreak: function(slot) {
        var pagebreak = this.getPageBreak(slot);
        if (!pagebreak) {
            return false;
        }
        pagebreak.remove();
        return true;
    },

    /**
     * Reorder each pagebreak by iterating through each related slot.
     *
     * @method reorderPageBreaks
     * @return void
     */
    reorderPageBreaks: function() {
        // Get list of slot nodes.
        var slots = this.getSlots();
        var slotnumber = 0;
        // Loop through slots incrementing the number each time.
        slots.each(function(slot, key) {
            slotnumber++;
            var pagebreak = this.getPageBreak(slot);
            var nextitem = slot.next('li.activity');
            if (!nextitem) {
                // Last slot in a section. Should not have an icon.
                return;
            }

            // No pagebreak and not last slot. Add one.
            if (!pagebreak) {
                pagebreak = this.addPageBreak(slot);
            }

            // Remove last page break if there is one.
            if (pagebreak && key === slots.size() - 1) {
                this.removePageBreak(slot);
            }

            // Get page break anchor element.
            var pagebreaklink = pagebreak.get('childNodes').item(0);

            // Get the correct title.
            var action = '';
            var iconname = '';
            if (Y.Moodle.mod_quiz.util.page.isPage(nextitem)) {
                action = 'removepagebreak';
                iconname = 'e/remove_page_break';
            } else {
                action = 'addpagebreak';
                iconname = 'e/insert_page_break';
            }

            // Update the link and image titles
            pagebreaklink.set('title', M.util.get_string(action, 'quiz'));
            pagebreaklink.setData('action', action);
            // Update the image title.
            var icon = pagebreaklink.one(this.SELECTORS.ICON);
            icon.set('title', M.util.get_string(action, 'quiz'));
            icon.set('alt', M.util.get_string(action, 'quiz'));

            // Update the image src.
            icon.set('src', M.util.image_url(iconname));

            // Get anchor url parameters as an associative array.
            var params = Y.QueryString.parse(pagebreaklink.get('href'));
            // Update slot number.
            params.slot = slotnumber;
            // Create the new url.
            var newurl = '';
            for (var index in params) {
                if (newurl.length) {
                    newurl += "&";
                }
                newurl += index + "=" + params[index];
            }
            // Update the anchor.
            pagebreaklink.set('href', newurl);
        }, this);
    },

    /**
     * Update the dependency icons.
     *
     * @method updateAllDependencyIcons
     * @return void
     */
    updateAllDependencyIcons: function() {
        // Get list of slot nodes.
        var slots = this.getSlots(),
            slotnumber = 0,
            previousslot = null;
        // Loop through slots incrementing the number each time.
        slots.each(function(slot) {
            slotnumber++;

            if (slotnumber == 1 || previousslot.getData('canfinish') === '0') {
                slot.one(this.SELECTORS.DEPENDENCY_WRAPPER).addClass(this.CSS.CANNOT_DEPEND);
            } else {
                slot.one(this.SELECTORS.DEPENDENCY_WRAPPER).removeClass(this.CSS.CANNOT_DEPEND);
            }
            this.updateDependencyIcon(slot, null);

            previousslot = slot;
        }, this);
    },

    /**
     * Update the slot icon to indicate the new requiresprevious state.
     *
     * @method slot Slot node
     * @method requiresprevious Whether this node now requires the previous one.
     * @return void
     */
    updateDependencyIcon: function(slot, requiresprevious) {
        var link = slot.one(this.SELECTORS.DEPENDENCY_LINK);
        var icon = slot.one(this.SELECTORS.DEPENDENCY_ICON);
        var previousSlot = this.getPrevious(slot);
        var a = {thisq: this.getNumber(slot)};
        if (previousSlot) {
            a.previousq = this.getNumber(previousSlot);
        }

        if (requiresprevious === null) {
            requiresprevious = link.getData('action') === 'removedependency';
        }

        if (requiresprevious) {
            link.set('title', M.util.get_string('questiondependencyremove', 'quiz', a));
            link.setData('action', 'removedependency');
            window.require(['core/templates'], function(Templates) {
                Templates.renderPix('t/locked', 'core', M.util.get_string('questiondependsonprevious', 'quiz')).then(
                    function(html) {
                        icon.replace(html);
                    }
                );
            });
        } else {
            link.set('title', M.util.get_string('questiondependencyadd', 'quiz', a));
            link.setData('action', 'adddependency');
            window.require(['core/templates'], function(Templates) {
                Templates.renderPix('t/unlocked', 'core', M.util.get_string('questiondependencyfree', 'quiz')).then(
                    function(html) {
                        icon.replace(html);
                    }
                );
            });
        }
    }
};
