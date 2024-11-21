YUI.add('moodle-block_xp-rulepicker', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Rule picker of level up.
 *
 * @module     moodle-block_xp-rulepicker
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @main       moodle-block_xp-rulepicker
 */

/**
 * @module moodle-block_xp-rulepicker
 */

var COMPONENT = 'block_xp';
var CSS = {
    INFO: 'info',
    PREFIX: 'block_xp-rulepicker',
    RULE: 'rule',
};
var SELECTORS = {
    RULE: '.rule'
};

/**
 * Rule picker.
 *
 * @namespace Y.M.block_xp
 * @class RulePicker
 * @constructor
 */
var PICKER = function() {
    PICKER.superclass.constructor.apply(this, arguments);
};
Y.namespace('M.block_xp').RulePicker = Y.extend(PICKER, M.core.dialogue, {

    prepared: false,

    initializer: function() {
        this.publish('picked');
    },

    close: function() {
        this.hide();
    },

    display: function() {
        if (!this.prepared) {
            this.prepare();
        }

        this.show();
    },

    picked: function(e) {
        e.preventDefault();

        this.close();
        this.fire('picked', e.currentTarget.getData('id'));
    },

    prepare: function() {
        var content,
            tpl,
            html;

        html = '<div>';
        html += ' {{#rules}}';
        html += ' <div class="{{../CSS.RULE}}" data-id="{{id}}" tabindex="0" role="button" ';
        html += '      aria-describedby="block_xp_rulepicker_rule_title_{{id}}">';
        html += '   <h3 id="block_xp_rulepicker_rule_title_{{id}}">{{ name }}</h3>';
        html += '   {{#info}}';
        html += '   <div class="{{../../CSS.INFO}}">';
        html += '     {{ . }}';
        html += '   </div>';
        html += '   {{/info}}';
        html += ' </div>';
        html += ' {{/rules}}';
        html += '</div>';
        tpl = Y.Handlebars.compile(html);

        // Set the header.
        this.getStdModNode(Y.WidgetStdMod.HEADER).prepend(Y.Node.create('<h1>' + this.get('title') + '</h1>'));

        // Set the content.
        content = Y.Node.create(
            tpl({
                CSS: CSS,
                rules: this.get('rules')
            })
        );
        this.setStdModContent(Y.WidgetStdMod.BODY, content, Y.WidgetStdMod.REPLACE);

        // Use standard dialogue class name. This removes the default styling of the footer.
        this.get('boundingBox').one('.moodle-dialogue-wrap').addClass('moodle-dialogue-content');

        // When a rule is picked.
        this.get('boundingBox').delegate('click', this.picked, SELECTORS.RULE, this);
        this.get('boundingBox').delegate('key', this.picked, '32, 13', SELECTORS.RULE, this);

        this.prepared = true;
    }

}, {
    NAME: NAME,
    CSS_PREFIX: CSS.PREFIX,
    ATTRS: {

        rules: {
            validator: Y.Lang.isObject,
            value: null
        },

    }
});

Y.Base.modifyAttrs(Y.namespace('M.block_xp.RulePicker'), {

    modal: {
        value: true
    },

    render: {
        value: true
    },

    title: {
        valueFn: function() {
            return M.util.get_string('pickaconditiontype', COMPONENT);
        }
    },

    visible: {
        value: false
    },

    width: {
        value: '500px'
    }

});

Y.namespace('M.block_xp.RulePicker').init = function(config) {
    return new PICKER(config);
};


}, '@VERSION@', {"requires": ["base", "node", "handlebars", "moodle-core-notification-dialogue"]});
