YUI.add('moodle-tool_monitor-dropdown', function (Y, NAME) {

/**
 * A module to manage dropdowns on the rule add/edit form.
 *
 * @module moodle-tool_monitor-dropdown
 */

/**
 * A module to manage dependent selects on the edit page.
 *
 * @since Moodle 2.8
 * @class moodle-tool_monitor.dropdown
 * @extends Base
 * @constructor
 */
function DropDown() {
    DropDown.superclass.constructor.apply(this, arguments);
}


var SELECTORS = {
        PLUGIN: '#id_plugin',
        EVENTNAME: '#id_eventname',
        OPTION: 'option',
        CHOOSE: 'option[value=""]'
    };

Y.extend(DropDown, Y.Base, {

    /**
     * Reference to the plugin node.
     *
     * @property plugin
     * @type Object
     * @default null
     * @protected
     */
    plugin: null,

    /**
     * Reference to the plugin node.
     *
     * @property eventname
     * @type Object
     * @default null
     * @protected
     */
    eventname: null,

    /**
     * Initializer.
     * Basic setup and delegations.
     *
     * @method initializer
     */
    initializer: function() {
        this.plugin = Y.one(SELECTORS.PLUGIN);
        this.eventname = Y.one(SELECTORS.EVENTNAME);
        var selection = this.eventname.get('value'); // Get selected event name.
        this.updateEventsList();
        this.updateSelection(selection);
        this.plugin.on('change', this.updateEventsList, this);
    },

    /**
     * Method to update the events list drop down when plugin list drop down is changed.
     *
     * @method updateEventsList
     */
    updateEventsList: function() {
        var node, options, choosenode;
        var plugin = this.plugin.get('value'); // Get component name.
        var namespace = '\\' + plugin + '\\';
        this.eventname.all(SELECTORS.OPTION).remove(true); // Delete all nodes.
        options = this.get('eventlist');

        // Mark the default choose node as visible and selected.
        choosenode = Y.Node.create('<option value="">' + options[''] + '</option>');
        choosenode.set('selected', 'selected');
        this.eventname.appendChild(choosenode);

        Y.Object.each(options, function(value, key) {
            // Make sure we highlight only nodes with correct namespace.
            if (key.substring(0, namespace.length) === namespace) {
                node = Y.Node.create('<option value="' + key + '">' + value + '</option>');
                this.eventname.appendChild(node);
            }
        }, this);

    },

    /**
     * Method to update the selected node from the options list.
     *
     * @method updateSelection
     * @param {string} selection The options node value that should be selected.
     */
    updateSelection: function(selection) {
        this.eventname.get('options').each(function(opt) {
            if (opt.get('value') === selection) {
                opt.set('selected', 'selected');
            }
        }, this);
    }
}, {
    NAME: 'dropDown',
    ATTRS: {
        /**
         * A list of events with components.
         *
         * @attribute eventlist
         * @default null
         * @type Object
         */
        eventlist: null
    }
});

Y.namespace('M.tool_monitor.DropDown').init = function(config) {
    return new DropDown(config);
};


}, '@VERSION@', {"requires": ["base", "event", "node"]});
