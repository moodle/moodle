/**
 * JavaScript for form editing role conditions.
 *
 * @module moodle-availability_role-form
 */
M.availability_role = M.availability_role || {};

/**
 * @class M.availability_role.form
 * @extends M.core_availability.plugin
 */
M.availability_role.form = Y.Object(M.core_availability.plugin);

/**
 * Roles available for selection.
 *
 * @property roles
 * @type Array
 */
M.availability_role.form.roles = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} roles Array of objects containing roleid => name
 */
M.availability_role.form.initInner = function(roles) {
    this.roles = roles;
};

M.availability_role.form.getNode = function(json) {
    // Create HTML structure.
    var strings = M.str.availability_role;
    var html = '<label>' + strings.title + ' <span class="availability-group">' +
            '<select name="id">' +
            '<option value="choose">' + M.str.moodle.choosedots + '</option>';
    Y.each(this.roles, function(rolename, id) {
        html += '<option value="' + id + '">' + rolename + '</option>';
    });
    html += '</select></span></label>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial value if specified.
    if (json.id !== undefined &&
            node.one('select[name=id] > option[value=' + json.id + ']')) {
        node.one('select[name=id]').set('value', '' + json.id);
    }

    // Add event handlers (first time only).
    if (!M.availability_role.form.addedEvents) {
        M.availability_role.form.addedEvents = true;
        var root = Y.one('.availability-field');
        root.delegate('change', function() {
            // Just update the form fields.
            M.core_availability.form.update();
        }, '.availability_role select');
    }

    return node;
};

M.availability_role.form.fillValue = function(value, node) {
    var selected = node.one('select[name=id]').get('value');
    if (selected === 'choose') {
        value.id = 'choose';
    } else {
        value.id = parseInt(selected, 10);
    }
};

M.availability_role.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    // Check grouping item id.
    if (value.id === 'choose') {
        errors.push('availability_role:error_selectrole');
    }
};
