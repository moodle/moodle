YUI.add('moodle-availability_dataformcontent-form', function (Y, NAME) {

/**
 * JavaScript for form editing dataformcontent conditions.
 *
 * @module moodle-availability_dataformcontent-form
 */
M.availability_dataformcontent = M.availability_dataformcontent || {};

/**
 * @class M.availability_dataformcontent.form
 * @extends M.core_availability.plugin
 */
M.availability_dataformcontent.form = Y.Object(M.core_availability.plugin);

/**
 * Dataforms available for selection (alphabetical order).
 *
 * @property dataforms
 * @type Array
 */
M.availability_dataformcontent.form.dataforms = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} dataforms Array of objects containing dataformid => name
 */
M.availability_dataformcontent.form.initInner = function(dataforms) {
    this.dataforms = dataforms;
};

M.availability_dataformcontent.form.getNode = function(json) {
    // Create HTML structure.
    var html = '<label>' + M.util.get_string('title', 'availability_dataformcontent') +
            ' <span class="availability-dataformcontent">' +
            '<select name="id">' +
            '<option value="choose">' + M.util.get_string('choosedots', 'moodle') + '</option>';
    for (var i = 0; i < this.dataforms.length; i++) {
        var dataform = this.dataforms[i];
        // String has already been escaped using format_string.
        html += '<option value="' + dataform.id + '">' + dataform.name + '</option>';
    }
    html += '</select></span></label>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial value if specified.
    if (json.id !== undefined &&
            node.one('select[name=id] > option[value=' + json.id + ']')) {
        node.one('select[name=id]').set('value', '' + json.id);
    }

    // Add event handlers (first time only).
    if (!M.availability_dataformcontent.form.addedEvents) {
        M.availability_dataformcontent.form.addedEvents = true;
        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
            // Just update the form fields.
            M.core_availability.form.update();
        }, '.availability_dataformcontent select');
    }

    return node;
};

M.availability_dataformcontent.form.fillValue = function(value, node) {
    var selected = node.one('select[name=id]').get('value');
    if (selected === 'choose') {
        value.id = 'choose';
    } else {
        value.id = parseInt(selected, 10);
    }
};

M.availability_dataformcontent.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    // Check dataform item id.
    if (value.id === 'choose') {
        errors.push('availability_dataformcontent:error_selectdataform');
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
