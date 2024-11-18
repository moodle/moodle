/**
 * JavaScript for form editing company conditions.
 *
 * @module moodle-availability_company-form
 */
M.availability_company = M.availability_company || {};

/**
 * @class M.availability_company.form
 * @extends M.core_availability.plugin
 */
M.availability_company.form = Y.Object(M.core_availability.plugin);

/**
 * Groups available for selection (alphabetical order).
 *
 * @property companys
 * @type Array
 */
M.availability_company.form.companys = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} companys Array of objects containing companyid => name
 */
M.availability_company.form.initInner = function(companys) {
    this.companys = companys;
};

M.availability_company.form.getNode = function(json) {
    // Create HTML structure.
    var html = '<label><span class="pr-3">' + M.util.get_string('title', 'availability_company') + '</span> ' +
            '<span class="availability-company">' +
            '<select name="id" class="custom-select">' +
            '<option value="choose">' + M.util.get_string('choosedots', 'moodle') + '</option>' +
            '<option value="any">' + M.util.get_string('anycompany', 'availability_company') + '</option>';
    for (var i = 0; i < this.companys.length; i++) {
        var company = this.companys[i];
        // String has already been escaped using format_string.
        html += '<option value="' + company.id + '">' + company.name + '</option>';
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
    if (!M.availability_company.form.addedEvents) {
        M.availability_company.form.addedEvents = true;
        var root = Y.one('.availability-field');
        root.delegate('change', function() {
            // Just update the form fields.
            M.core_availability.form.update();
        }, '.availability_company select');
    }

    return node;
};

M.availability_company.form.fillValue = function(value, node) {
    var selected = node.one('select[name=id]').get('value');
    if (selected === 'choose') {
        value.id = 'choose';
    } else if (selected !== 'any') {
        value.id = parseInt(selected, 10);
    }
};

M.availability_company.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    // Check company item id.
    if (value.id && value.id === 'choose') {
        errors.push('availability_company:error_selectcompany');
    }
};
