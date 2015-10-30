YUI.add('moodle-availability_group-form', function (Y, NAME) {

/**
 * JavaScript for form editing group conditions.
 *
 * @module moodle-availability_group-form
 */
M.availability_group = M.availability_group || {};

/**
 * @class M.availability_group.form
 * @extends M.core_availability.plugin
 */
M.availability_group.form = Y.Object(M.core_availability.plugin);

/**
 * Groups available for selection (alphabetical order).
 *
 * @property groups
 * @type Array
 */
M.availability_group.form.groups = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} groups Array of objects containing groupid => name
 */
M.availability_group.form.initInner = function(groups) {
    this.groups = groups;
};

M.availability_group.form.getNode = function(json) {
    // Create HTML structure.
    var strings = M.str.availability_group;
    var html = '<label>' + strings.title + ' <span class="availability-group">' +
            '<select name="id">' +
            '<option value="choose">' + M.str.moodle.choosedots + '</option>' +
            '<option value="any">' + strings.anygroup + '</option>';
    for (var i = 0; i < this.groups.length; i++) {
        var group = this.groups[i];
        // String has already been escaped using format_string.
        html += '<option value="' + group.id + '">' + group.name + '</option>';
    }
    html += '</select></span></label>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values (leave default 'choose' if creating afresh).
    if (json.creating === undefined) {
        if (json.id !== undefined &&
                node.one('select[name=id] > option[value=' + json.id + ']')) {
            node.one('select[name=id]').set('value', '' + json.id);
        } else if (json.id === undefined) {
            node.one('select[name=id]').set('value', 'any');
        }
    }

    // Add event handlers (first time only).
    if (!M.availability_group.form.addedEvents) {
        M.availability_group.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
            // Just update the form fields.
            M.core_availability.form.update();
        }, '.availability_group select');
    }

    return node;
};

M.availability_group.form.fillValue = function(value, node) {
    var selected = node.one('select[name=id]').get('value');
    if (selected === 'choose') {
        value.id = 'choose';
    } else if (selected !== 'any') {
        value.id = parseInt(selected, 10);
    }
};

M.availability_group.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    // Check group item id.
    if (value.id && value.id === 'choose') {
        errors.push('availability_group:error_selectgroup');
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
