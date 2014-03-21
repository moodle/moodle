YUI.add('moodle-availability_date-form', function (Y, NAME) {

/**
 * JavaScript for form editing date conditions.
 *
 * @module moodle-availability_date-form
 */
M.availability_date = M.availability_date || {};

/**
 * @class M.availability_date.form
 * @extends M.core_availability.plugin
 */
M.availability_date.form = Y.Object(M.core_availability.plugin);

/**
 * Initialises this plugin.
 *
 * Because the date fields are complex depending on Moodle calendar settings,
 * we create the HTML for these fields in PHP and pass it to this method.
 *
 * @method initInner
 * @param {String} html HTML to use for date fields
 * @param {Number} defaultTime Time value that corresponds to initial fields
 */
M.availability_date.form.initInner = function(html, defaultTime) {
    this.html = html;
    this.defaultTime = defaultTime;
};

M.availability_date.form.getNode = function(json) {
    var strings = M.str.availability_date;
    var html = strings.direction_before + ' <span class="availability-group">' +
            '<label><span class="accesshide">' + strings.direction_label + ' </span>' +
            '<select name="direction">' +
            '<option value="&gt;=">' + strings.direction_from + '</option>' +
            '<option value="&lt;">' + strings.direction_until + '</option>' +
            '</select></label></span> ' + this.html;
    var node = Y.Node.create('<span>' + html + '</span>');

    // Set initial value if non-default.
    if (json.t !== undefined) {
        node.setData('time', json.t);
        // Disable everything.
        node.all('select:not([name=direction])').each(function(select) {
            select.set('disabled', true);
        });

        var url = M.cfg.wwwroot + '/availability/condition/date/ajax.php?action=fromtime' +
            '&time=' + json.t;
        Y.io(url, { on : {
            success : function(id, response) {
                var fields = Y.JSON.parse(response.responseText);
                for (var field in fields) {
                    var select = node.one('select[name=x\\[' + field + '\\]]');
                    select.set('value', fields[field]);
                    select.set('disabled', false);
                }
            },
            failure : function() {
                window.alert(M.str.availability_date.ajaxerror);
            }
        }});
    } else {
        // Set default time that corresponds to the HTML selectors.
        node.setData('time', this.defaultTime);
    }
    if (json.d !== undefined) {
        node.one('select[name=direction]').set('value', json.d);
    }

    // Add event handlers (first time only).
    if (!M.availability_date.form.addedEvents) {
        M.availability_date.form.addedEvents = true;

        var root = Y.one('#fitem_id_availabilityconditionsjson');
        root.delegate('change', function() {
            // For the direction, just update the form fields.
            M.core_availability.form.update();
        }, '.availability_date select[name=direction]');

        root.delegate('change', function() {
            // Update time using AJAX call from root node.
            M.availability_date.form.updateTime(this.ancestor('span.availability_date'));
        }, '.availability_date select:not([name=direction])');
    }

    if (node.one('a[href=#]')) {
        // Add the date selector magic.
        M.form.dateselector.init_single_date_selector(node);

        // This special handler detects when the date selector changes the year.
        var yearSelect = node.one('select[name=x\\[year\\]]');
        var oldSet = yearSelect.set;
        yearSelect.set = function(name, value) {
            oldSet.call(yearSelect, name, value);
            if (name === 'selectedIndex') {
                // Do this after timeout or the other fields haven't been set yet.
                setTimeout(function() {
                    M.availability_date.form.updateTime(node);
                }, 0);
            }
        };
    }

    return node;
};

/**
 * Updates time from AJAX. Whenever the field values change, we recompute the
 * actual time via an AJAX request to Moodle.
 *
 * This will set the 'time' data on the node and then update the form, once it
 * gets an AJAX response.
 *
 * @method updateTime
 * @param {Y.Node} component Node for plugin controls
 */
M.availability_date.form.updateTime = function(node) {
    // After a change to the date/time we need to recompute the
    // actual time using AJAX because it depends on the user's
    // time zone and calendar options.
    var url = M.cfg.wwwroot + '/availability/condition/date/ajax.php?action=totime' +
            '&year=' + node.one('select[name=x\\[year\\]]').get('value') +
            '&month=' + node.one('select[name=x\\[month\\]]').get('value') +
            '&day=' + node.one('select[name=x\\[day\\]]').get('value') +
            '&hour=' + node.one('select[name=x\\[hour\\]]').get('value') +
            '&minute=' + node.one('select[name=x\\[minute\\]]').get('value');
    Y.io(url, { on : {
        success : function(id, response) {
            node.setData('time', response.responseText);
            M.core_availability.form.update();
        },
        failure : function() {
            window.alert(M.str.availability_date.ajaxerror);
        }
    }});
};

M.availability_date.form.fillValue = function(value, node) {
    value.d = node.one('select[name=direction]').get('value');
    value.t = parseInt(node.getData('time'), 10);
};


}, '@VERSION@', {"requires": ["base", "node", "event", "io", "moodle-core_availability-form"]});
