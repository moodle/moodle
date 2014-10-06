YUI.add('moodle-mod_quiz-util-slot', function (Y, NAME) {

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
    CONSTANTS: {
        SLOTIDPREFIX : 'slot-'
    },
    SELECTORS: {
        SLOT: 'li.slot',
        INSTANCENAME: '.instancename',
        NUMBER: 'span.slotnumber',
        PAGECONTENT : 'div#page-content',
        SECTIONUL : 'ul.section'
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
        var number = slot.one(this.SELECTORS.NUMBER).get('text');
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
        slot.one(this.SELECTORS.NUMBER).set('text', number);
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
     * Returns the previous slot to the give slot.
     *
     * @method getPrevious
     * @param slot Slot node
     * @return {node|false} The previous slot node or false.
     */
    getPrevious: function(slot) {
        return slot.previous(this.SELECTORS.SLOT);
    },

    /**
     * Reset the order of the numbers given to each slot.
     *
     * @method reorder_slots
     * @return void
     */
    reorder_slots: function() {
        // Get list of slot nodes.
        var slots = this.getSlots();
        // Loop through slots incrementing the number each time.
        slots.each(function(slot) {
            var previousSlot = this.getPrevious(slot),
                previousslotnumber = 0;
            if(previousSlot){
                previousslotnumber = this.getNumber(previousSlot);
            }

            // Set slot number.
            this.setNumber(slot, previousslotnumber + 1);
        }, this);

    }
};


}, '@VERSION@', {"requires": ["node", "moodle-mod_quiz-util-base"]});
