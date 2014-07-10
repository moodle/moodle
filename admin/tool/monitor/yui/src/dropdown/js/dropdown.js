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
        this.plugin.on('change', this.updateEventsList, this);
    },

    /**
     * Method to update the events list drop down when plugin list drop down is changed.
     *
     * @method updateEventsList
     */
    updateEventsList: function() {
        var plugin = this.plugin.get('value'); // Get component name.
        var namespace = '\\' + plugin + '\\';
        this.eventname.all(SELECTORS.OPTION).hide(); // Hide all options.
        this.eventname.all(SELECTORS.OPTION).each(function(node) {
            // Make sure we highlight only nodes with correct namespace.
            if (node.get('value').substring(0, namespace.length) === namespace) {
                node.show();
            }
        });
        // Mark the default choose node as visible and selected.
        var choosenode = this.eventname.one(SELECTORS.CHOOSE);
        choosenode.show().set('selected', 'selected');
    }
}, {
    NAME: 'dropDown',
    ATTRS: {}
});

Y.namespace('M.tool_monitor.DropDown').init = function(config) {
    return new DropDown(config);
};
