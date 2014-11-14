YUI.add('moodle-availability_grade-form', function (Y, NAME) {

/**
 * JavaScript for form editing grade conditions.
 *
 * @module moodle-availability_grade-form
 */
M.availability_grade = M.availability_grade || {};

/**
 * @class M.availability_grade.form
 * @extends M.core_availability.plugin
 */
M.availability_grade.form = Y.Object(M.core_availability.plugin);

/**
 * Grade items available for selection.
 *
 * @property grades
 * @type Array
 */
M.availability_grade.form.grades = null;

/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {Array} grades Array of objects containing gradeid => name
 */
M.availability_grade.form.initInner = function(grades) {
    this.grades = grades;
    this.nodesSoFar = 0;
};

M.availability_grade.form.getNode = function(json) {
    // Increment number used for unique ids.
    this.nodesSoFar++;

    // Create HTML structure.
    var strings = M.str.availability_grade;
    var html = '<label>' + strings.title + ' <span class="availability-group">' +
            '<select name="id"><option value="0">' + M.str.moodle.choosedots + '</option>';
    for (var i = 0; i < this.grades.length; i++) {
        var grade = this.grades[i];
        // String has already been escaped using format_string.
        html += '<option value="' + grade.id + '">' + grade.name + '</option>';
    }
    html += '</select></span></label> <span class="availability-group">' +
            '<label><input type="checkbox" name="min"/>' + strings.option_min +
            '</label> <label><span class="accesshide">' + strings.label_min +
            '</span><input type="text" name="minval" title="' +
            strings.label_min + '"/></label>%</span>' +
            '<span class="availability-group">' +
            '<label><input type="checkbox" name="max"/>' + strings.option_max +
            '</label> <label><span class="accesshide">' + strings.label_max +
            '</span><input type="text" name="maxval" title="' +
            strings.label_max + '"/></label>%</span>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values.
    if (json.id !== undefined &&
            node.one('select[name=id] > option[value=' + json.id + ']')) {
        node.one('select[name=id]').set('value', '' + json.id);
    }
    if (json.min !== undefined) {
        node.one('input[name=min]').set('checked', true);
        node.one('input[name=minval]').set('value', json.min);
    }
    if (json.max !== undefined) {
        node.one('input[name=max]').set('checked', true);
        node.one('input[name=maxval]').set('value', json.max);
    }

    // Disables/enables text input fields depending on checkbox.
    var updateCheckbox = function(check, focus) {
        var input = check.ancestor('label').next('label').one('input');
        var checked = check.get('checked');
        input.set('disabled', !checked);
        if (focus && checked) {
            input.focus();
        }
        return checked;
    };
    node.all('input[type=checkbox]').each(updateCheckbox);

    // Add event handlers (first time only).
    if (!M.availability_grade.form.addedEvents) {
        M.availability_grade.form.addedEvents = true;

        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
            // For the grade item, just update the form fields.
            M.core_availability.form.update();
        }, '.availability_grade select[name=id]');

        root.delegate('click', function() {
            updateCheckbox(this, true);
        }, '.availability_grade input[type=checkbox]');

        root.delegate('valuechange', function() {
            // For grade values, just update the form fields.
            M.core_availability.form.update();
        }, '.availability_grade input[type=text]');
    }

    return node;
};

M.availability_grade.form.fillValue = function(value, node) {
    value.id = parseInt(node.one('select[name=id]').get('value'), 10);
    if (node.one('input[name=min]').get('checked')) {
        value.min = this.getValue('minval', node);
    }
    if (node.one('input[name=max]').get('checked')) {
        value.max = this.getValue('maxval', node);
    }
};

/**
 * Gets the numeric value of an input field. Supports decimal points (using
 * dot or comma).
 *
 * @method getValue
 * @return {Number|String} Value of field as number or string if not valid
 */
M.availability_grade.form.getValue = function(field, node) {
    // Get field value.
    var value = node.one('input[name=' + field + ']').get('value');

    // If it is not a valid positive number, return false.
    if (!(/^[0-9]+([.,][0-9]+)?$/.test(value))) {
        return value;
    }

    // Replace comma with dot and parse as floating-point.
    var result = parseFloat(value.replace(',', '.'));
    if (result < 0 || result > 100) {
        return value;
    } else {
        return result;
    }
};

M.availability_grade.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);

    // Check grade item id.
    if (value.id === 0) {
        errors.push('availability_grade:error_selectgradeid');
    }

    // Check numeric values.
    if ((value.min !== undefined && typeof(value.min) === 'string') ||
            (value.max !== undefined && typeof(value.max) === 'string')) {
        errors.push('availability_grade:error_invalidnumber');
    } else if (value.min !== undefined && value.max !== undefined &&
            value.min >= value.max) {
        errors.push('availability_grade:error_backwardrange');
    }
};


}, '@VERSION@', {"requires": ["base", "node", "event", "moodle-core_availability-form"]});
