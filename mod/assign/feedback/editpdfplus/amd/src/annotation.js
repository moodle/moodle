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
/*
 * @package    assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module mod_assignfeedback_editpdfplus/annotation
 * @param {Jquery} $
 * @param {Global} global constantes
 */
define(['jquery', './global'],
        function ($, global) {

            /********************************
             * CONSTRUCTOR and SUPER-CLASS *
             ********************************/

            // I am the internal, static counter for the number of models
            // that have been created in the system. This is used to
            // power the unique identifier of each instance.
            var instanceCount = 0;

            // I get the next instance ID.
            var getNewInstanceID = function () {
                // Precrement the instance count in order to generate the
                // next value instance ID.
                return(++instanceCount);
            };

            // I return an initialized object.
            /**
             * Annotation class.
             *
             * @class Annotation
             */
            function Annotation() {
                // Store the private instance id.
                this._instanceID = getNewInstanceID();
                // Return this object reference.
                return(this);

            }
            // I return the current instance count. I am a static method
            // on the Model class.
            Annotation.getInstanceCount = function () {
                return(instanceCount);
            };
            Annotation.prototype.getInstanceID = function () {
                return(this._instanceID);
            };

            /**************
             * Parameters *
             **************/

            /**
             * X position
             * @property x
             * @type Int
             * @public
             */
            Annotation.x = 0;
            /**
             * Y position
             * @property y
             * @type Int
             * @public
             */
            Annotation.y = 0;
            /**
             * Ending x position
             * @property endx
             * @type Int
             * @public
             */
            Annotation.endx = 0;
            /**
             * Ending y position
             * @property endy
             * @type Int
             * @public
             */
            Annotation.endy = 0;
            /**
             * Path
             * @property path
             * @type String - list of points like x1,y1:x2,y2
             * @public
             */
            Annotation.path = '';
            /**
             * Tool.
             * @property toolid
             * @type Int
             * @public
             */
            Annotation.toolid = 0;
            /**
             * Annotation colour.
             * @property colour
             * @type String
             * @public
             */
            Annotation.colour = 'red';
            /**
             * Reference to assignfeedback_editpdfplus.tool
             * @property tooltype
             * @type assignfeedback_editpdfplus.tool
             * @public
             */
            Annotation.tooltype = null;
            /**
             * id of the annotation in BDD.
             * @property id
             * @type Int
             * @public
             */
            Annotation.id = 0;
            /**
             * position x of the cartridge.
             * @property cartridgex
             * @type Int
             * @public
             */
            Annotation.cartridgex = 0;
            /**
             * position y of the cartridge.
             * @property cartridgey
             * @type Int
             * @public
             */
            Annotation.cartridgey = 0;
            /**
             * mode readonly demo or not
             * @property adminDemo
             * @type Boolean
             * @public
             */
            Annotation.adminDemo = 0;

            /*************
             * FUNCTIONS *
             *************/

            /**
             * Initialize tooltype object from an object from database with its base's id
             * @param {object} config
             */
            Annotation.prototype.init = function (config) {
                this.cartridgex = parseInt(config.cartridgex, 10) || 0;
                this.cartridgey = parseInt(config.cartridgey, 10) || 0;
                this.colour = config.colour || 'red';
                this.tooltype = config.tooltype;
                this.id = config.id;
                this.x = parseInt(config.x, 10) || 0;
                this.y = parseInt(config.y, 10) || 0;
                this.endx = parseInt(config.endx, 10) || 0;
                this.endy = parseInt(config.endy, 10) || 0;
                this.path = config.path || '';
                this.toolid = config.toolid;
            };

            /**
             * Initialize tooltype object from an object from database
             * @param {Tool} currentTool
             */
            Annotation.prototype.initAdminDemo = function (currentTool) {
                this.id = 'previsu_annot';
                this.displaylock = 1;
                this.adminDemo = 1;
                this.tooltype = currentTool;
                this.colour = currentTool.get_color();
            };

            /**
             * Draw an annotation - super class
             * @returns {Annotation} this annotation
             */
            Annotation.prototype.draw = function () {
                // Should be overridden by the subclass.
            };

            /**
             * Get the final color for the annotation
             * @return string
             */
            Annotation.prototype.get_color = function () {
                var color = global.ANNOTATIONCOLOUR[this.colour];
                if (!color) {
                    color = this.colour;
                } else {
                    // Add an alpha channel to the rgb colour.
                    color = color.replace('rgb', 'rgba');
                    color = color.replace(')', ',0.5)');
                }
                return color;
            };

            /**
             * Get the final color for the cartridge
             * @return string
             */
            Annotation.prototype.get_color_cartridge = function () {
                var color = global.ANNOTATIONCOLOUR[this.tooltype.get_color_cartridge()];
                if (!color) {
                    color = this.tooltype.get_color_cartridge();
                } else {
                    // Add an alpha channel to the rgb colour.
                    color = color.replace('rgb', 'rgba');
                    color = color.replace(')', ',0.5)');
                }
                return color;
            };

            /**
             * Init the HTML id for the cartridge's annotation
             */
            Annotation.prototype.init_div_cartridge_id = function () {
                var date = (new Date().toJSON()).replace(/:/g, '').replace(/\./g, '');
                if (this.tooltype.id) {
                    this.divcartridge = 'ct_' + this.tooltype.id + '_' + date;
                } else {
                    this.divcartridge = 'ct_' + this.id + '_' + date;
                }
            };

            /**
             * get the html node for the cartridge
             * @param {string} colorcartridge
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_div_cartridge = function (colorcartridge, canevas) {
                var div = "<div ";
                div += "id='" + this.divcartridge + "' ";
                div += "class='assignfeedback_editpdfplus_cartridge' ";
                div += "style='border-color: " + colorcartridge + ";position:relative;'> ";
                div += "</div>";
                if (canevas) {
                    canevas.append(div);
                }
                var divdisplay = $('#' + this.divcartridge);
                if (this.adminDemo < 1) {
                    //if (this.editor.get('readonly')) {
                    //    divdisplay.on('click', this.view_annot, this);
                    //}
                }
                return divdisplay;
            };

            /**
             * get the html node for the label cartridge
             * @param {string} colorcartridge
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_div_cartridge_label = function (colorcartridge, canevas/*, draggable*/) {
                var divcartridge = "<div ";
                divcartridge += "id='" + this.divcartridge + "_cartridge' ";
                divcartridge += "class='assignfeedback_editpdfplus_" + this.tooltype.getToolTypeLabel() + "_cartridge' ";
                //if (this.editor.get('readonly') && this.get_valref() === '') {
                //divcartridge += "style='border-right:none;padding-right:0px;color:" + colorcartridge + ";' ";
                //} else {
                divcartridge += "style='border-right-color: " + colorcartridge + ";color:" + colorcartridge + ";' ";
                //}
                divcartridge += "> ";
                divcartridge += this.tooltype.cartridge;
                divcartridge += "</div>";
                if (canevas) {
                    canevas.append(divcartridge);
                }
                var divcartridgedisplay = $('#' + this.divcartridge + "_cartridge");
                /*if (draggable && !this.editor.get('readonly')) {
                 divcartridgedisplay.on('mousedown', this.move_cartridge_begin, this);
                 return divcartridgedisplay;
                 }*/
                return divcartridgedisplay;
            };

            /**
             * get the html node for the textannot associated to the annotation
             * @param {string} colorcartridge
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_div_input = function (colorcartridge, canevas) {
                var divinput = "<div ";
                divinput += "id='" + this.divcartridge + "_display' ";
                divinput += "style='color:" + colorcartridge + "; ";
                //if (this.editor.get('readonly') && this.get_valref() === '') {
                //    divinput += "padding:0px;";
                //}
                divinput += "'></div>";
                canevas.append(divinput);
                var divinputdisplay = $("#" + this.divcartridge + "_display");
                //if (!this.editor.get('readonly')) {
                divinputdisplay.on("click", {annotation: this}, this.edit_annot);
                //}
                return divinputdisplay;
            };

            /**
             * get the html node for the edition of comment and parameters
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_div_edition = function (canevas) {
                var divedition = "<div ";
                divedition += "id='" + this.divcartridge + "_edit' ";
                divedition += "class='assignfeedback_editpdfplus_" + this.tooltype.getToolTypeLabel() + "_edition' ";
                divedition += "style='display:none;'> ";
                divedition += "<textarea id='"
                        + this.divcartridge
                        + "_editinput' type='text'"
                        //value=\""
                        //+ this.get_valref()
                        + " class='form-control' style='margin-bottom:5px;'";
                if (this.adminDemo === 1) {
                    divedition += ' readonly';
                }
                divedition += ">"
                        //+ this.get_valref()
                        + "</textarea>";
                divedition += "</div>";
                if (canevas) {
                    canevas.append(divedition);
                }
                var diveditiondisplay = $("#" + this.divcartridge + "_edit");
                var propositions = this.tooltype.texts;
                if (propositions && propositions.length > 0) {
                    var divproposition = "<div class='btn-group-vertical aepp-toolbar-vertical'></div>";
                    var divpropositiondisplay = $(divproposition);
                    var propositionarray = propositions.split('","');
                    for (var i = 0; i < propositionarray.length; i++) {
                        var buttontmp = "<button class='btn btn-outline-dark'";
                        if (this.adminDemo === 1) {
                            buttontmp += ' disabled';
                        }
                        buttontmp += " type='button'>"
                                + propositionarray[i].replace(/"/g, "")
                                + "</button>";
                        divpropositiondisplay.append(buttontmp);
                        if (this.adminDemo < 1) {
                            //buttontmpdisplay.on('click', this.fill_input_edition, this, propositionarray[i].replace('"', ''));
                        }
                        divpropositiondisplay.append("<br/>");
                    }
                    diveditiondisplay.append(divpropositiondisplay);
                }
                return diveditiondisplay;
            };

            /**
             * get the html node for the text annotation, tools and options
             * @param {string} colorcartridge
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_div_container = function (colorcartridge, canevas) {
                var divconteneur = "<div ";
                divconteneur += "class='assignfeedback_editpdfplus_" + this.tooltype.getToolTypeLabel() + "_conteneur' >";
                divconteneur += "</div>";
                if (canevas) {
                    canevas.append(divconteneur);
                }
                var divconteneurdisplay = $('.assignfeedback_editpdfplus_' + this.tooltype.getToolTypeLabel() + "_conteneur");
                var divinputdisplay = this.get_div_input(colorcartridge, divconteneurdisplay);
                divinputdisplay.addClass('assignfeedback_editpdfplus_' + this.tooltype.getToolTypeLabel() + '_input');
                var onof = 1;
                if (this.displaylock || this.displaylock >= 0) {
                    onof = this.displaylock;
                }
                var inputonof = "<input type='hidden' id='" + this.divcartridge + "_onof' value=" + onof + " />";
                if (canevas) {
                    divconteneurdisplay.append(inputonof);
                }
                divconteneurdisplay.append(this.get_input_question());

                return divconteneurdisplay;
            };

            /**
             * get the html node for toolbar on annotation
             * @return node
             */
            Annotation.prototype.get_toolbar = function () {
                var divtoolbar = $("<div id='"
                        + this.divcartridge
                        + "_toolbar' class='btn-group btn-group-sm aepp-toolbar'></div>");
                //var readonly = this.editor.get('readonly');
                //if (!readonly) {
                divtoolbar.append(this.get_button_visibility_left());
                divtoolbar.append(this.get_button_visibility_right());
                divtoolbar.append(this.get_button_save());
                divtoolbar.append(this.get_button_cancel());
                if (this.tooltype.reply === 1) {
                    divtoolbar.append(this.get_button_question());
                }
                divtoolbar.append(this.get_button_remove());
                //} else {
                //    divtoolbar.append(this.get_button_student_status());
                //}
                return divtoolbar;
            }
            ;

            /**
             * get the html node for the hidden input to keep information about question state
             * @return node
             */
            Annotation.prototype.get_input_question = function () {
                var qst = 0;
                if (this.answerrequested && this.answerrequested === 1) {
                    qst = 1;
                }
                return "<input type='hidden' id='" + this.divcartridge + "_question' value='" + qst + "'/>";
            };

            /**
             * get the html node for the button to set visibility on right
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_button_visibility_right = function () {
                var buttonvisibility = "<button id='"
                        + this.divcartridge
                        + "_buttonedit_right' class='btn btn-sm btn-outline-dark'";
                if (this.adminDemo === 1) {
                    buttonvisibility += ' disabled';
                }
                buttonvisibility += " type='button'>";
                buttonvisibility += "<i class='fa fa-arrow-right' aria-hidden='true'></i>";
                buttonvisibility += "</button>";
                var buttonvisibilitydisplay = $(buttonvisibility);
                if (this.adminDemo < 1) {
                    //buttonvisibilitydisplay.on('click', this.change_visibility_annot('r'));
                }
                return buttonvisibilitydisplay;
            };

            /**
             * get the html node for the button to set visibility on left
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_button_visibility_left = function () {
                var buttonvisibility = "<button id='"
                        + this.divcartridge
                        + "_buttonedit_left' class='btn btn-sm btn-outline-dark'";
                if (this.adminDemo === 1) {
                    buttonvisibility += ' disabled';
                }
                buttonvisibility += " type='button'>";
                buttonvisibility += "<i class='fa fa-arrow-left' aria-hidden='true'></i>";
                buttonvisibility += "</button>";
                var buttonvisibilitydisplay = $(buttonvisibility);
                if (this.adminDemo < 1) {
                    //buttonvisibilitydisplay.on('click', this.change_visibility_annot('l'));
                }
                return buttonvisibilitydisplay;
            };

            /**
             * get the html node for the button to save the text in the annotation
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_button_save = function () {
                var buttonsave = "<button id='"
                        + this.divcartridge
                        + "_buttonsave' style='display:none;margin-left:110px;' class='btn btn-sm btn-outline-dark'";
                if (this.adminDemo === 1) {
                    buttonsave += ' disabled';
                }
                buttonsave += " type='button'>"
                        + "<i class='fa fa-check' aria-hidden='true'></i>"
                        + "</button>";
                var buttonsavedisplay = $(buttonsave);
                if (this.adminDemo < 1) {
                    buttonsavedisplay.on('click', this.save_annot);
                }
                return buttonsavedisplay;
            };
            /**
             * get the html node for the button to cancel the text edition of the annotation
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_button_cancel = function () {
                var buttoncancel = "<button id='"
                        + this.divcartridge
                        + "_buttoncancel' style='display:none;' class='btn btn-sm btn-outline-dark'";
                if (this.adminDemo === 1) {
                    buttoncancel += ' disabled';
                }
                buttoncancel += " type='button'>"
                        + "<i class='fa fa-undo' aria-hidden='true'></i>"
                        + "</button>";
                var buttoncanceldisplay = $(buttoncancel);
                if (this.adminDemo < 1) {
                    //buttoncanceldisplay.on('click', this.cancel_edit, this);
                }
                return buttoncanceldisplay;
            };

            /**
             * get the html node for the button to set a question
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_button_question = function () {
                var buttonquestion = "<button id='"
                        + this.divcartridge
                        + "_buttonquestion' style='display:none;margin-left:10px;' class='btn btn-sm btn-outline-dark'";
                if (this.adminDemo === 1) {
                    buttonquestion += ' disabled';
                }
                buttonquestion += " type='button'>"
                        + '<span class="fa-stack fa-lg" style="line-height: 1em;width: 1em;">'
                        + '<i class="fa fa-question-circle-o fa-stack-1x"></i>'
                        + '<i class="fa fa-ban fa-stack-1x text-danger"></i>'
                        + '</span>'
                        + "</button>";
                var buttonquestiondisplay = $(buttonquestion);
                if (this.adminDemo < 1) {
                    //buttonquestiondisplay.on('click', this.change_question_status, this);
                }
                return buttonquestiondisplay;
            };

            /**
             * get the html node for the button to remove the annotation
             * @param {JQuery Entity} canevas
             * @return {JQuery Entity} node
             */
            Annotation.prototype.get_button_remove = function () {
                var buttontrash = "<button id='"
                        + this.divcartridge
                        + "_buttonremove' style='display:none;margin-left:10px;' class='btn btn-sm btn-outline-dark'";
                if (this.adminDemo === 1) {
                    buttontrash += ' disabled';
                }
                buttontrash += " type='button'>"
                        + "<i class='fa fa-trash' aria-hidden='true'></i>"
                        + "</button>";
                var buttontrashdisplay = $(buttontrash);
                if (this.adminDemo < 1) {
                    //buttontrashdisplay.on('click', this.remove_by_trash, this);
                }
                return buttontrashdisplay;
            };

            /**
             * display the annotation according to parameters and profile
             */
            Annotation.prototype.apply_visibility_annot = function () {
                var divdisplay = $('#' + this.divcartridge + "_display");
                var interrupt = $('#' + this.divcartridge + "_onof");
                var buttonplusr = $('#' + this.divcartridge + "_buttonedit_right");
                var buttonplusl = $('#' + this.divcartridge + "_buttonedit_left");
                var buttonstatus = $('#' + this.divcartridge + "_radioContainer");
                if (interrupt) {
                    if (interrupt.val() === '1') {
                        if (buttonplusr) {
                            buttonplusr.show();
                        }
                        if (buttonplusl) {
                            buttonplusl.show();
                        }
                    } else if (interrupt.val() === '0') {
                        if (buttonplusr) {
                            buttonplusr.show();
                        }
                        if (buttonplusl) {
                            buttonplusl.hide();
                        }
                    } else {
                        if (buttonplusr) {
                            buttonplusr.hide();
                        }
                        if (buttonplusl) {
                            buttonplusl.show();
                        }
                    }
                }
                if (divdisplay) {
                    divdisplay.html(this.get_text_to_diplay_in_cartridge());
                }
                if (this.tooltype.getToolTypeLabel() === 'frame' && buttonplusr) {
                    buttonplusr.hide();
                    buttonplusl.hide();
                }
                if (buttonstatus) {
                    buttonstatus.hide();
                }
                this.apply_question_status();
            };

            /**
             * get the html node for the text to display for the annotation, according to parameters
             * @return node
             */
            Annotation.prototype.get_text_to_diplay_in_cartridge = function () {
                var valref = this.get_valref();
                var interrupt = $('#' + this.divcartridge + "_onof");
                var finalcontent = "";
                if (valref === '' /*&& !this.editor.get('readonly')*/) {
                    finalcontent = '&nbsp;&nbsp;&nbsp;&nbsp';
                }
                if (interrupt.val() === '1' && valref !== '') {
                    finalcontent = valref.substr(0, 20);
                } else if (interrupt.val() === '0' && valref !== '') {
                    finalcontent = '...';
                } else if (valref !== '') {
                    finalcontent = valref;
                }
                if (this.answerrequested === 1) {
                    finalcontent += '&nbsp;<span style="color:red;">[?]</span>';
                }
                return finalcontent;
            };

            /**
             * change question set of the annotation
             */
            Annotation.prototype.apply_question_status = function () {
                var buttonquestion = $('#' + this.divcartridge + "_buttonquestion");
                var questionvalue = $('#' + this.divcartridge + "_question");
                var value = 0;
                if (questionvalue) {
                    value = parseInt(questionvalue.val(), 10);
                }
                if (buttonquestion) {
                    if (value === 1) {
                        buttonquestion.html('<i class="fa fa-question-circle-o"></i>');
                    } else {
                        buttonquestion.html('<span class="fa-stack fa-lg" style="line-height: 1em;width: 1em;">'
                                + '<i class="fa fa-question-circle-o fa-stack-1x"></i>'
                                + '<i class="fa fa-ban fa-stack-1x text-danger"></i>'
                                + '</span>');
                    }
                }
                return;
            };

            /**
             * global method, draw empty cartridge
             */
            Annotation.prototype.draw_catridge = function () {
                return true;
            };

            /**
             * display annotation edditing view
             * @param {Event} event
             */
            Annotation.prototype.edit_annot = function (event) {
                if (event.data.annotation.tooltype.typetool <= global.TOOLTYPE.COMMENTPLUS/* && !this.parent_annot_element*/) {
                    var annot = event.data.annotation;
                    var divprincipale = $('#' + annot.divcartridge);
                    var divdisplay = $('#' + annot.divcartridge + "_display");
                    var divedit = $('#' + annot.divcartridge + "_edit");
                    var buttonplusr = $('#' + annot.divcartridge + "_buttonedit_right");
                    var buttonplusl = $('#' + annot.divcartridge + "_buttonedit_left");
                    var buttonsave = $('#' + annot.divcartridge + "_buttonsave");
                    var buttoncancel = $('#' + annot.divcartridge + "_buttoncancel");
                    var buttonquestion = $('#' + annot.divcartridge + "_buttonquestion");
                    var buttonrotation = $('#' + annot.divcartridge + "_buttonrotation");
                    var buttonremove = $('#' + annot.divcartridge + "_buttonremove");
                    var input = $('#' + annot.divcartridge + "_editinput");
                    divdisplay.hide();
                    if (buttonplusr) {
                        buttonplusr.hide();
                    }
                    if (buttonplusl) {
                        buttonplusl.hide();
                    }
                    if (buttonrotation) {
                        buttonrotation.hide();
                    }
                    divedit.show();
                    buttonsave.show();
                    buttoncancel.show();
                    if (buttonquestion) {
                        buttonquestion.show();
                    }
                    buttonremove.show();
                    divprincipale.css('z-index', 1000);
                    if (input) {
                        input.attr('focus', 'on');
                    }
                    event.data.annotation.disabled_canvas_event();
                    $('#canevas').on('click',
                            {annotation: annot, action: 'clickoutside'},
                            annot.save_annot_clickout);
                }
            };

            /**
             * fill input edition with new text
             * @param {event} e
             * @param {string} unputtext
             */
            Annotation.prototype.fill_input_edition = function (e, unputtext) {
                var input = $('#' + this.divcartridge + "_editinput");
                if (input) {
                    input.set('value', unputtext);
                }
                this.save_annot(unputtext);
            };
            Annotation.prototype.save_annot_clickout = function (event) {
                if ((event.target.id === "canevas" /*&& this.editor.currentannotation === this*/)) {
                    if (event.data.annotation.adminDemo === 1) {
                        event.data.annotation.cancel_edit();
                    } else {
                        //event.data.annotation.save_annot(null);
                    }
                }
                return;
            };

            /**
             * save text annotation
             * @param {string} result
             */
            Annotation.prototype.save_annot = function (result) {
                if (typeof result !== 'string') {
                    var input = $('#' + this.divcartridge + "_editinput");
                    if (input) {
                        result = input.val();
                    }
                }
                this.textannot = result;
                //this.editor.save_current_page();
                if (result.length === 0) {
                    result = "&nbsp;&nbsp;";
                }
                this.hide_edit();
                this.apply_visibility_annot();
            };

            /**
             * cancel annotation detail view
             */
            Annotation.prototype.cancel_edit = function () {
                //if (!(clickType === 'clickoutside' /*&& this.editor.currentannotation === this)*/)) {
                var valref = this.get_valref();
                var input = $('#' + this.divcartridge + "_editinput");
                if (valref && input) {
                    input.set('value', valref);
                }
                this.hide_edit();
                this.apply_visibility_annot();
                var divprincipale = $('#' + this.divcartridge);
                if (divprincipale) {
                    divprincipale.off();
                }
                //}
                //return;
            };

            /**
             * remove annotation detail view
             * @param {Event} e
             * @param {string} clickType
             */
            Annotation.prototype.hide_edit = function (e, clickType) {
                if (!clickType || !(clickType === 'clickoutside' && this.editor.currentannotation === this)) {
                    var divprincipale = $('#' + this.divcartridge);
                    var divdisplay = $('#' + this.divcartridge + "_display");
                    var divedit = $('#' + this.divcartridge + "_edit");
                    var divvisu = $('#' + this.divcartridge + "_visu");
                    var buttonsave = $('#' + this.divcartridge + "_buttonsave");
                    var buttoncancel = $('#' + this.divcartridge + "_buttoncancel");
                    var buttonquestion = $('#' + this.divcartridge + "_buttonquestion");
                    var buttonrotation = $('#' + this.divcartridge + "_buttonrotation");
                    var buttonremove = $('#' + this.divcartridge + "_buttonremove");
                    var buttonstatus = $('#' + this.divcartridge + "_radioContainer");
                    if (divdisplay) {
                        divdisplay.show();
                        divdisplay.css('color', this.get_color_cartridge());
                    }
                    if (buttonrotation) {
                        buttonrotation.show();
                    }
                    if (divedit) {
                        divedit.hide();
                        buttonsave.hide();
                        buttoncancel.hide();
                    }
                    if (divvisu) {
                        divvisu.hide();
                    }
                    if (buttonquestion) {
                        buttonquestion.hide();
                    }
                    if (buttonremove) {
                        buttonremove.hide();
                    }
                    if (divprincipale) {
                        divprincipale.css('z-index', 1);
                        $("#canevas").off();
                        //if (this.editor.get('readonly')) {
                        //    divprincipale.on('click', this.view_annot, this, 'click');
                        //}
                    }
                    if (divedit) {
                        this.enabled_canvas_event();
                    }
                    if (buttonstatus) {
                        buttonstatus.hide();
                    }
                }
            };

            /**
             * Disable canvas event (click on other tool or annotation)
             */
            Annotation.prototype.disabled_canvas_event = function () {
                var drawingcanvas = $(global.SELECTOR.DRAWINGCANVAS);
                drawingcanvas.off('click');
            };

            /**
             * Enable canvas event (click on other tool or annotation)
             */
            Annotation.prototype.enabled_canvas_event = function () {
                /*var drawingcanvas = this.editor.get_dialogue_element(SELECTOR.DRAWINGCANVAS);
                 drawingcanvas.on('gesturemovestart', this.editor.edit_start, null, this.editor);
                 drawingcanvas.on('gesturemove', this.editor.edit_move, null, this.editor);
                 drawingcanvas.on('gesturemoveend', this.editor.edit_end, null, this.editor);*/
            };

            /**
             * change the visibility of the annotation according to parameters and variable sens
             * @param {char} sens
             */
            Annotation.prototype.change_visibility_annot = function (sens) {
                var interrupt = $('#' + this.divcartridge + "_onof");
                var finalvalue = parseInt(interrupt.val(), 10);
                if (sens === 'r') {
                    finalvalue += 1;
                } else {
                    finalvalue -= 1;
                }
                interrupt.val(finalvalue);
                this.displaylock = finalvalue;
                this.apply_visibility_annot();
                //this.editor.save_current_page();
            };

            /**
             * get the final reference text value
             * @return node
             */
            Annotation.prototype.get_valref = function () {
                if (this.textannot && this.textannot.length > 0 && typeof this.textannot === 'string') {
                    return this.textannot;
                }
                return '';
            };

            return Annotation;
        });