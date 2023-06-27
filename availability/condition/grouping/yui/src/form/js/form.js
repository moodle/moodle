/**
 * JavaScript for form editing grouping conditions.
 *
 * @module moodle-availability_grouping-form
 */
M.availability_grouping = M.availability_grouping || {};

/**
 * @class M.availability_grouping.form
 * @extends M.core_availability.plugin
 */
M.availability_grouping.form = Y.Object(M.core_availability.plugin);

/**
 * Groupings available for selection (alphabetical order).
 *
 * @property groupings
 * @type Array
 */
M.availability_grouping.form.groupings = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} groupings Array of objects containing groupingid => name
 */
M.availability_grouping.form.initInner = function(groupings) {
    this.groupings = groupings;
};

M.availability_grouping.form.getNode = function(json) {
    // Create HTML structure.
    var html = '<label><span class="pr-3">' + M.util.get_string('title', 'availability_grouping') + '</span> ' +
            '<span class="availability-group">' +
            '<select name="id" class="custom-select">' +
            '<option value="choose">' + M.util.get_string('choosedots', 'moodle') + '</option>';
    for (var i = 0; i < this.groupings.length; i++) {
        var grouping = this.groupings[i];
        // String has already been escaped using format_string.
        html += '<option value="' + grouping.id + '">' + grouping.name + '</option>';
    }
    html += '</select></span></label>';
    var node = Y.Node.create('<span class="form-inline">' + html + '</span>');

    // Set initial value if specified.
    if (json.id !== undefined &&
            node.one('select[name=id] > option[value=' + json.id + ']')) {
        node.one('select[name=id]').set('value', '' + json.id);
    }

    // Add event handlers (first time only).
    if (!M.availability_grouping.form.addedEvents) {
        M.availability_grouping.form.addedEvents = true;
        var root = Y.one('.availability-field');
        root.delegate('change', function() {
            // Just update the form fields.
            M.core_availability.form.update();
        }, '.availability_grouping select');
    }

    return node;
};

M.availability_grouping.form.fillValue = function(value, node) {
    var selected = node.one('select[name=id]').get('value');
    if (selected === 'choose') {
        value.id = 'choose';
    } else {
        value.id = parseInt(selected, 10);
    }
};

M.availability_grouping.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    // Check grouping item id.
    if (value.id === 'choose') {
        errors.push('availability_grouping:error_selectgrouping');
    }
};
