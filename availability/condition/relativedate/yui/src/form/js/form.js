/**
 * JavaScript for form editing relativedate conditions.
 *
 * @module moodle-availability_relativedate-form
 */
M.availability_relativedate = M.availability_relativedate || {};

// Class M.availability_relativedate.form @extends M.core_availability.plugin.
M.availability_relativedate.form = Y.Object(M.core_availability.plugin);

// Time fields available for selection.
M.availability_relativedate.form.timeFields = null;

// Start field available for selection.
M.availability_relativedate.form.startFields = null;

// A section or a module.
M.availability_relativedate.form.isSection = null;

// Optional warnings that can be displayed.
M.availability_relativedate.form.warningStrings = null;


/**
 * Initialises this plugin.
 *
 * @method initInner
 * @param {array} timeFields Collection of time fields
 * @param {array} startFields Collection of start fields
 * @param {boolean} isSection Is this a section
 * @param {array} warningStrings Collection of warning strings
 * @param {array} activitySelector Collection of activity fields
 */
M.availability_relativedate.form.initInner = function(timeFields, startFields, isSection, warningStrings, activitySelector) {
    this.timeFields = timeFields;
    this.startFields = startFields;
    this.isSection = isSection;
    this.warningStrings = warningStrings;
    this.activitySelector = activitySelector;
};

M.availability_relativedate.form.getNode = function(json) {
    var html = '<span class="availability-relativedate">';
    var fieldInfo;
    var i = 0;
    var j = 0;

    for (i = 0; i < this.warningStrings.length; i++) {
        html += '<div class="alert alert-warning alert-block fade in " role="alert">' + this.warningStrings[i] + '</div>';
    }
    html += '<label><select name="relativenumber">';
    for (i = 1; i < 60; i++) {
        html += '<option value="' + i + '">' + i + '</option>';
    }

    html += '</select></label> ';
    html += '<label><select name="relativednw">';
    for (i = 0; i < this.timeFields.length; i++) {
        fieldInfo = this.timeFields[i];
        html += '<option value="' + fieldInfo.field + '">' + fieldInfo.display + '</option>';
    }
    html += '</select></label> ';
    html += '<label><select name="relativestart">';

    for (i = 0; i < this.startFields.length; i++) {
        fieldInfo = this.startFields[i];
        html += '<option value="' + fieldInfo.field + '">' + fieldInfo.display + '</option>';
    }
    html += '</select></label>';
    html += '<label><select name="relativecoursemodule"' + (json.s != 7 ? ' style="display: none;"' : '') + '>';

    var defaultCourseModuleId = 0;

    for (i = 0; i < this.activitySelector.length; i++) {
        html += '<option disabled>' + this.activitySelector[i].name + '</option>';
        for (j = 0; j < this.activitySelector[i].coursemodules.length; j++) {
            html += '<option value="' + this.activitySelector[i].coursemodules[j].id + '"';
            if (this.activitySelector[i].coursemodules[j].completionenabled == 0) {
                html += ' disabled';
            } else {
                if (!defaultCourseModuleId) {
                    defaultCourseModuleId = this.activitySelector[i].coursemodules[j].id;
                }
            }
            html += '>' + this.activitySelector[i].coursemodules[j].name + '</option>';
        }
    }
    html += '</select></label>';
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial values if specified.
    i = 1;
    if (json.n !== undefined) {
        i = json.n;
    }
    node.one('select[name=relativenumber]').set('value', i);

    i = 2;
    if (json.d !== undefined) {
        i = json.d;
    }
    node.one('select[name=relativednw]').set('value', i);

    i = 1;
    if (json.s !== undefined) {
        i = json.s;
    }
    node.one('select[name=relativestart]').set('value', i);

    i = defaultCourseModuleId;
    if (json.m !== undefined) {
        i = json.m;
    }
    node.one('select[name=relativecoursemodule]').set('value', i);

    // Add event handlers (first time only).
    if (!M.availability_relativedate.form.addedEvents) {
        M.availability_relativedate.form.addedEvents = true;
        var root = Y.one('.availability-field');
        var updateForm = function(input) {
            var ancestorNode = input.ancestor('span.availability_relativedate');
            var op = ancestorNode.one('select[name=relativestart]');
            if (op.get('value') == '7') {
                ancestorNode.one('select[name=relativecoursemodule]').set('style', '');
            } else {
                ancestorNode.one('select[name=relativecoursemodule]').set('style', 'display: none;');
            }
            M.core_availability.form.update();
        };

        root.delegate('change', function() {
            updateForm(this);
        }, '.availability_relativedate select');
    }

    return node;
};

M.availability_relativedate.form.fillValue = function(value, node) {
    value.n = Number(node.one('select[name=relativenumber]').get('value'));
    value.d = Number(node.one('select[name=relativednw]').get('value'));
    value.s = Number(node.one('select[name=relativestart]').get('value'));
    value.m = 0;
    if (value.s == 7) {
        value.m = Number(node.one('select[name=relativecoursemodule]').get('value'));
    }
};

M.availability_relativedate.form.fillErrors = function(errors, node) {
    var value = {};
    this.fillValue(value, node);
};
