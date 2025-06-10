/**
 * Availability password - YUI code for password form
 *
 * @module     moodle-availability_password-form
 * @copyright  2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * JavaScript for form editing profile conditions.
 *
 * @module moodle-availability_password-form
 */
M.availability_password = M.availability_password || {}; // eslint-disable-line camelcase

/*
 * @class M.availability_password.form
 * @extends M.core_availability.plugin
 */
M.availability_password.form = Y.Object(M.core_availability.plugin);

/**
 * Initialises this plugin.
 *
 * @method initInner
 */
M.availability_password.form.initInner = function() {
    // Does nothing.
};

M.availability_password.form.instId = 1;

M.availability_password.form.getNode = function(json) {
    "use strict";
    var html, node, root, id;

    id = 'availability_password' + M.availability_password.form.instId;
    M.availability_password.form.instId += 1;

    // Create HTML structure.
    html = '<label><span class="pr-3">' + M.util.get_string('title', 'availability_password') + '</span> ' +
            '<span class="availability-group">' +
            '<input type="text" class="form-control" name="availability_password" id="' + id + '">';
    node = Y.Node.create('<span class="form-inline">' + html + '</span>');

    // Set initial values, if specified.
    if (json.password !== undefined) {
        node.one('input[name=availability_password]').set('value', json.password);
    }

    // Add event handlers (first time only).
    if (!M.availability_password.form.addedEvents) {
        M.availability_password.form.addedEvents = true;
        root = Y.one('.availability-field');
        root.delegate('valuechange', function() {
            // Trigger the updating of the hidden availability data whenever the password field changes.
            M.core_availability.form.update();
        }, '.availability_password input[name=availability_password]');
    }

    return node;
};

/**
 * Called whenever M.core_availability.form.update() is called - this is used to
 * save the value from the form into the hidden availability data.
 *
 * @param {Object} value
 * @param {Object} node
 */
M.availability_password.form.fillValue = function(value, node) {
    "use strict";

    // Store the password.
    value.password = node.one('input[name=availability_password]').get('value').trim();
};

M.availability_password.form.fillErrors = function(errors, node) {
    "use strict";
    var value = {};
    this.fillValue(value, node);

    // Check the password has been set.
    if (value.password === undefined || value.password === '') {
        errors.push('availability_password:error_setpassword');
    }
};
