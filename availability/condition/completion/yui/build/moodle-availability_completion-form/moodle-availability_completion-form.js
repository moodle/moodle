YUI.add('moodle-availability_completion-form', function (Y, NAME) {

/**
 * JavaScript for form editing completion conditions.
 *
 * @module moodle-availability_completion-form
 */
M.availability_completion = M.availability_completion || {};

/**
 * @class M.availability_completion.form
 * @extends M.core_availability.plugin
 */
M.availability_completion.form = Y.Object(M.core_availability.plugin);

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} cms Array of objects containing cmid => name
 */
M.availability_completion.form.initInner = function(cms) {
    this.cms = cms;
};

M.availability_completion.form.getNode = function(json) {
    // Create HTML structure.
    var html = M.util.get_string('title', 'availability_completion') + ' <span class="availability-group"><label>' +
            '<span class="accesshide">' + M.util.get_string('label_cm', 'availability_completion') + ' </span>' +
            '<select name="cm" title="' + M.util.get_string('label_cm', 'availability_completion') + '">' +
            '<option value="0">' + M.util.get_string('choosedots', 'moodle') + '</option>';
    for (var i = 0; i < this.cms.length; i++) {
        var cm = this.cms[i];
        // String has already been escaped using format_string.
        html += '<option value="' + cm.id + '">' + cm.name + '</option>';
    }
    html += '</select></label> <label><span class="accesshide">' +
                M.util.get_string('label_completion', 'availability_completion') +
            ' </span><select name="e" title="' + M.util.get_string('label_completion', 'availability_completion') + '">' +
            '<option value="1">' + M.util.get_string('option_complete', 'availability_completion') + '</option>' +
            '<option value="0">' + M.util.get_string('option_incomplete', 'availability_completion') + '</option>' +
            '<option value="2">' + M.util.get_string('option_pass', 'availability_completion') + '</option>' +
            '<option value="3">' + M.util.get_string('option_fail', 'availability_completion') + '</option>' +
            '</select></label></span>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values.
    if (json.cm !== undefined &&
            node.one('select[name=cm] > option[value=' + json.cm + ']')) {
        node.one('select[name=cm]').set('value', '' + json.cm);
    }
    if (json.e !== undefined) {
        node.one('select[name=e]').set('value', '' + json.e);
    }

    // Add event handlers (first time only).
    if (!M.availability_completion.form.addedEvents) {
        M.availability_completion.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
            // Whichever dropdown changed, just update the form.
            M.core_availability.form.update();
        }, '.availability_completion select');
    }

    return node;
};

M.availability_completion.form.fillValue = function(value, node) {
    value.cm = parseInt(node.one('select[name=cm]').get('value'), 10);
    value.e = parseInt(node.one('select[name=e]').get('value'), 10);
};

M.availability_completion.form.fillErrors = function(errors, node) {
    var cmid = parseInt(node.one('select[name=cm]').get('value'), 10);
    if (cmid === 0) {
        errors.push('availability_completion:error_selectcmid');
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
