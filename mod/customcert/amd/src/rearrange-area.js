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
 * AMD module used when rearranging a custom certificate.
 *
 * @module     mod_customcert/rearrange-area
 * @package    mod_customcert
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/yui', 'core/fragment', 'mod_customcert/dialogue', 'core/notification',
        'core/str', 'core/templates', 'core/ajax'],
        function($, Y, fragment, Dialogue, notification, str, template, ajax) {

            /**
             * RearrangeArea class.
             *
             * @param {String} selector The rearrange PDF selector
             */
            var RearrangeArea = function(selector) {
                this._node = $(selector);
                this._setEvents();
            };

            RearrangeArea.prototype.CUSTOMCERT_REF_POINT_TOPLEFT = 0;
            RearrangeArea.prototype.CUSTOMCERT_REF_POINT_TOPCENTER = 1;
            RearrangeArea.prototype.CUSTOMCERT_REF_POINT_TOPRIGHT = 2;
            RearrangeArea.prototype.PIXELSINMM = 3.779527559055;

            RearrangeArea.prototype._setEvents = function() {
                this._node.on('click', '.element', this._editElement.bind(this));
            };

            RearrangeArea.prototype._editElement = function(event) {
                var elementid = event.currentTarget.id.substr(8);
                var contextid = this._node.attr('data-contextid');
                var params = {
                    'elementid': elementid
                };

                fragment.loadFragment('mod_customcert', 'editelement', contextid, params).done(function(html, js) {
                    str.get_string('editelement', 'mod_customcert').done(function(title) {
                        Y.use('moodle-core-formchangechecker', function() {
                            new Dialogue(
                                title,
                                '<div id=\'elementcontent\'></div>',
                                this._editElementDialogueConfig.bind(this, elementid, html, js),
                                undefined,
                                true
                            );
                        }.bind(this));
                    }.bind(this));
                }.bind(this)).fail(notification.exception);
            };

            RearrangeArea.prototype._editElementDialogueConfig = function(elementid, html, js, popup) {
                // Place the content in the dialogue.
                template.replaceNode('#elementcontent', html, js);

                // We may have dragged the element changing it's position.
                // Ensure the form has the current up-to-date location.
                this._setPositionInForm(elementid);

                // Add events for when we save, close and cancel the page.
                var body = $(popup.getContent());
                body.on('click', '#id_submitbutton', function(e) {
                    // Do not want to ask the user if they wish to stay on page after saving.
                    M.core_formchangechecker.reset_form_dirty_state();
                    // Save the data.
                    this._saveElement(elementid).then(function() {
                        // Update the DOM to reflect the adjusted value.
                        this._getElementHTML(elementid).done(function(html) {
                            var elementNode = this._node.find('#element-' + elementid);
                            var refpoint = parseInt($('#id_refpoint').val());
                            var refpointClass = '';
                            if (refpoint == this.CUSTOMCERT_REF_POINT_TOPLEFT) {
                                refpointClass = 'refpoint-left';
                            } else if (refpoint == this.CUSTOMCERT_REF_POINT_TOPCENTER) {
                                refpointClass = 'refpoint-center';
                            } else if (refpoint == this.CUSTOMCERT_REF_POINT_TOPRIGHT) {
                                refpointClass = 'refpoint-right';
                            }
                            elementNode.empty().append(html);
                            // Update the ref point.
                            elementNode.removeClass();
                            elementNode.addClass('element ' + refpointClass);
                            elementNode.attr('data-refpoint', refpoint);
                            // Move the element.
                            var posx = $('#editelementform #id_posx').val();
                            var posy = $('#editelementform #id_posy').val();
                            this._setPosition(elementid, refpoint, posx, posy);
                            // All done.
                            popup.close();
                        }.bind(this));
                    }.bind(this)).fail(notification.exception);
                    e.preventDefault();
                }.bind(this));

                body.on('click', '#id_cancel', function(e) {
                    popup.close();
                    e.preventDefault();
                });
            };

            RearrangeArea.prototype._setPosition = function(elementid, refpoint, posx, posy) {
                var element = Y.one('#element-' + elementid);

                posx = Y.one('#pdf').getX() + posx * this.PIXELSINMM;
                posy = Y.one('#pdf').getY() + posy * this.PIXELSINMM;
                var nodewidth = parseFloat(element.getComputedStyle('width'));
                var maxwidth = element.width * this.PIXELSINMM;

                if (maxwidth && (nodewidth > maxwidth)) {
                    nodewidth = maxwidth;
                }

                switch (refpoint) {
                    case this.CUSTOMCERT_REF_POINT_TOPCENTER:
                        posx -= nodewidth / 2;
                        break;
                    case this.CUSTOMCERT_REF_POINT_TOPRIGHT:
                        posx = posx - nodewidth + 2;
                        break;
                }

                element.setX(posx);
                element.setY(posy);
            };

            RearrangeArea.prototype._setPositionInForm = function(elementid) {
                var posxelement = $('#editelementform #id_posx');
                var posyelement = $('#editelementform #id_posy');

                if (posxelement.length && posyelement.length) {
                    var element = Y.one('#element-' + elementid);
                    var posx = element.getX() - Y.one('#pdf').getX();
                    var posy = element.getY() - Y.one('#pdf').getY();
                    var refpoint = parseInt(element.getData('refpoint'));
                    var nodewidth = parseFloat(element.getComputedStyle('width'));

                    switch (refpoint) {
                        case this.CUSTOMCERT_REF_POINT_TOPCENTER:
                            posx += nodewidth / 2;
                            break;
                        case this.CUSTOMCERT_REF_POINT_TOPRIGHT:
                            posx += nodewidth;
                            break;
                    }

                    posx = Math.round(parseFloat(posx / this.PIXELSINMM));
                    posy = Math.round(parseFloat(posy / this.PIXELSINMM));

                    posxelement.val(posx);
                    posyelement.val(posy);
                }
            };

            RearrangeArea.prototype._getElementHTML = function(elementid) {
                // Get the variables we need.
                var templateid = this._node.attr('data-templateid');

                // Call the web service to get the updated element.
                var promises = ajax.call([{
                    methodname: 'mod_customcert_get_element_html',
                    args: {
                        templateid: templateid,
                        elementid: elementid
                    }
                }]);

                // Return the promise.
                return promises[0];
            };

            RearrangeArea.prototype._saveElement = function(elementid) {
                // Get the variables we need.
                var templateid = this._node.attr('data-templateid');
                var inputs = $('#editelementform').serializeArray();

                // Call the web service to save the element.
                var promises = ajax.call([{
                    methodname: 'mod_customcert_save_element',
                    args: {
                        templateid: templateid,
                        elementid: elementid,
                        values: inputs
                    }
                }]);

                // Return the promise.
                return promises[0];
            };

            return {
                init: function(selector) {
                    new RearrangeArea(selector);
                }
            };
        }
    );
