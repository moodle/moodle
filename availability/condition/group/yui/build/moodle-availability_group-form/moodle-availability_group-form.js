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
    var html = '<label><span class="pr-3">' + M.util.get_string('title', 'availability_group') + '</span> ' +
            '<span class="availability-group">' +
            '<select name="id" class="custom-select">' +
            '<option value="choose">' + M.util.get_string('choosedots', 'moodle') + '</option>' +
            '<option value="any">' + M.util.get_string('anygroup', 'availability_group') + '</option>';
    for (var i = 0; i < this.groups.length; i++) {
        var group = this.groups[i];
        // String has already been escaped using format_string.
        html += '<option value="' + group.id + '" data-visibility="' + group.visibility + '">' + group.name + '</option>';
    }
    html += '</select></span></label>';
    var node = Y.Node.create('<span class="form-inline">' + html + '</span>');

    var select = node.one('select[name=id]');

    select.on('change', function(e) {
        var value = e.target.get('value');
        // Find the visibility of the selected group.
        var visibility = e.target.one('option[value=' + value + ']').get('dataset').visibility;

        var event;
        if (visibility > 0) {
            event = 'availability:privateRuleSet';
        } else {
            event = 'availability:privateRuleUnset';
        }
        node.fire(event, {plugin: 'group'});
    });

    // Set initial values (leave default 'choose' if creating afresh).
    if (json.creating === undefined) {
        if (json.id !== undefined) {
            var option = select.one('option[value=' + json.id + ']');
            if (option) {
                select.set('value', '' + json.id);
                var visibility = option.get('dataset').visibility;
                if (visibility > 0) {
                    // Defer firing the event, to allow event bubbling to be set up in M.core_availability.form.
                    window.setTimeout(function() {
                        node.fire('availability:privateRuleSet', {plugin: 'group'});
                    }, 0);
                }
            }
        } else if (json.id === undefined) {
            node.one('select[name=id]').set('value', 'any');
        }
    }

    // Add event handlers (first time only).
    if (!M.availability_group.form.addedEvents) {
        M.availability_group.form.addedEvents = true;
        var root = Y.one('.availability-field');
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
