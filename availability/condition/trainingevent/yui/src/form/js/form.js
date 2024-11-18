/**
 * JavaScript for form editing trainingevent conditions.
 *
 * @module moodle-availability_trainingevent-form
 */
M.availability_trainingevent = M.availability_trainingevent || {};

/**
 * @class M.availability_trainingevent.form
 * @extends M.core_availability.plugin
 */
M.availability_trainingevent.form = Y.Object(M.core_availability.plugin);

/**
 * Groups available for selection (alphabetical order).
 *
 * @property trainingevents
 * @type Array
 */
M.availability_trainingevent.form.trainingevents = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} trainingevents Array of objects containing trainingeventid => name
 */
M.availability_trainingevent.form.initInner = function(trainingevents) {
    this.trainingevents = trainingevents;
};

M.availability_trainingevent.form.getNode = function(json) {
    // Create HTML structure.
    var html = '<label><span class="pr-3">' + M.util.get_string('title', 'availability_trainingevent') + '</span> ' +
            '<span class="availability-trainingevent">' +
            '<select name="id" class="custom-select">' +
            '<option value="choose">' + M.util.get_string('choosedots', 'moodle') + '</option>' +
            '<option value="any">' + M.util.get_string('anytrainingevent', 'availability_trainingevent') + '</option>';
    for (var i = 0; i < this.trainingevents.length; i++) {
        var trainingevent = this.trainingevents[i];
        // String has already been escaped using format_string.
        html += '<option value="' + trainingevent.id + '">' + trainingevent.name + '</option>';
    }
    html += '</select></span></label>';
    var node = Y.Node.create('<span class="form-inline">' + html + '</span>');

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
    if (!M.availability_trainingevent.form.addedEvents) {
        M.availability_trainingevent.form.addedEvents = true;
        var root = Y.one('.availability-field');
        root.delegate('change', function() {
            // Just update the form fields.
            M.core_availability.form.update();
        }, '.availability_trainingevent select');
    }

    return node;
};

M.availability_trainingevent.form.fillValue = function(value, node) {
    var selected = node.one('select[name=id]').get('value');
    if (selected === 'choose') {
        value.id = 'choose';
    } else if (selected !== 'any') {
        value.id = parseInt(selected, 10);
    }
};

M.availability_trainingevent.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    // Check trainingevent item id.
    if (value.id && value.id === 'choose') {
        errors.push('availability_trainingevent:error_selecttrainingevent');
    }
};
