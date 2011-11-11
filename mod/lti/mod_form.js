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
 * MRTODO: Brief description of this file
 *
 * @package    mod
 * @subpackage lti
 * @copyright  2011 onwards MRTODO
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function(){
    var Y;

    M.mod_lti = M.mod_lti || {};

    M.mod_lti.editor = {
        init: function(yui3, settings){
            if(yui3){
                Y = yui3;
            }

            var self = this;
            this.settings = Y.JSON.parse(settings);

            this.urlCache = {};

            this.addOptGroups();

            var updateToolMatches = function(){
                self.updateAutomaticToolMatch(Y.one('#id_toolurl'));
                self.updateAutomaticToolMatch(Y.one('#id_securetoolurl'));
            };

            var typeSelector = Y.one('#id_typeid');
            typeSelector.on('change', function(e){
                updateToolMatches();

                self.toggleEditButtons();
            });

            this.createTypeEditorButtons();

            this.toggleEditButtons();

            var textAreas = new Y.NodeList([
                Y.one('#id_toolurl'),
                Y.one('#id_securetoolurl'),
                Y.one('#id_resourcekey'),
                Y.one('#id_password')
            ]);

            var debounce;
            textAreas.on('keyup', function(e){
                clearTimeout(debounce);

                //If no more changes within 2 seconds, look up the matching tool URL
                debounce = setTimeout(function(){
                    updateToolMatches();
                }, 2000);
            });

            updateToolMatches();
        },

        clearToolCache: function(){
            this.urlCache = {};
        },

        updateAutomaticToolMatch: function(field){
            var self = this;

            var toolurl = field;
            var typeSelector = Y.one('#id_typeid');

            var id = field.get('id') + '_lti_automatch_tool';
            var automatchToolDisplay = Y.one('#' + id);

            if(!automatchToolDisplay){
                automatchToolDisplay = Y.Node.create('<span />')
                                        .set('id', id)
                                        .setStyle('padding-left', '1em');

                toolurl.insert(automatchToolDisplay, 'after');
            }

            var url = toolurl.get('value');

            //Hide the display if the url box is empty
            if(!url){
                automatchToolDisplay.setStyle('display', 'none');
            } else {
                automatchToolDisplay.set('innerHTML', '');
                automatchToolDisplay.setStyle('display', '');
            }

            var selectedToolType = typeSelector.get('value');
            var selectedOption = typeSelector.one('option[value="' + selectedToolType + '"]');

            //A specific tool type is selected (not "auto")
            if(selectedToolType > 0){
                //If the entered domain matches the domain of the tool configuration...
                var domainRegex = /(?:https?:\/\/)?(?:www\.)?([^\/]+)(?:\/|$)/i;
                var match = domainRegex.exec(url);
                if(match && match[1] && match[1].toLowerCase() === selectedOption.getAttribute('domain').toLowerCase()){
                    automatchToolDisplay.set('innerHTML',  '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url + '" />' + M.str.lti.using_tool_configuration + selectedOption.get('text'));
                } else {
                    //The entered URL does not match the domain of the tool configuration
                    automatchToolDisplay.set('innerHTML', '<img style="vertical-align:text-bottom" src="' + self.settings.warning_icon_url + '" />' + M.str.lti.domain_mismatch);
                }

                return;
            }

            var key = Y.one('#id_resourcekey');
            var secret = Y.one('#id_password');

            //We don't care what tool type this tool is associated with if it's manually configured'
            if(key.get('value') !== '' && secret.get('value') !== ''){
                automatchToolDisplay.set('innerHTML',  '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url + '" />' + M.str.lti.custom_config);
            } else {
                var continuation = function(toolInfo){
                    if(toolInfo.toolname){
                        automatchToolDisplay.set('innerHTML',  '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url + '" />' + M.str.lti.using_tool_configuration + toolInfo.toolname);
                    } else {
                        //Inform them custom configuration is in use
                        if(key.get('value') === '' || secret.get('value') === ''){
                            automatchToolDisplay.set('innerHTML', '<img style="vertical-align:text-bottom" src="' + self.settings.warning_icon_url + '" />' + M.str.lti.tool_config_not_found);
                        }
                    }
                };

                //Cache urls which have already been checked to increaes performance
                if(self.urlCache[url]){
                    continuation(self.urlCache[url]);
                } else {
                    self.findToolByUrl(url, function(toolInfo){
                        self.urlCache[url] = toolInfo;

                        continuation(toolInfo);
                    });
                }
            }
        },

        getSelectedToolTypeOption: function(){
            var typeSelector = Y.one('#id_typeid');

            return typeSelector.one('option[value="' + typeSelector.get('value') + '"]');
        },

        /**
         * Separate tool listing into option groups. Server-side select control
         * doesn't seem to support this.
         */
        addOptGroups: function(){
            var typeSelector = Y.one('#id_typeid');

            if(typeSelector.one('option[courseTool=1]')){
                //One ore more course tools exist

                var globalGroup = Y.Node.create('<optgroup />')
                                    .set('id', 'global_tool_group')
                                    .set('label', M.str.lti.global_tool_types);

                var courseGroup = Y.Node.create('<optgroup />')
                                    .set('id', 'course_tool_group')
                                    .set('label', M.str.lti.course_tool_types);

                var globalOptions = typeSelector.all('option[globalTool=1]').remove().each(function(node){
                    globalGroup.append(node);
                });

                var courseOptions = typeSelector.all('option[courseTool=1]').remove().each(function(node){
                    courseGroup.append(node);
                });

                if(globalOptions.size() > 0){
                    typeSelector.append(globalGroup);
                }

                if(courseOptions.size() > 0){
                    typeSelector.append(courseGroup);
                }
            }
        },

        /**
         * Adds buttons for creating, editing, and deleting tool types.
         * Javascript is a requirement to edit course level tools at this point.
         */
        createTypeEditorButtons: function(){
            var self = this;

            var typeSelector = Y.one('#id_typeid');

            var createIcon = function(id, tooltip, iconUrl){
                return Y.Node.create('<a />')
                        .set('id', id)
                        .set('title', tooltip)
                        .setStyle('margin-left', '.5em')
                        .set('href', 'javascript:void(0);')
                        .append(Y.Node.create('<img src="' + iconUrl + '" />'));
            }

            var addIcon = createIcon('lti_add_tool_type', M.str.lti.addtype, this.settings.add_icon_url);
            var editIcon = createIcon('lti_edit_tool_type', M.str.lti.edittype, this.settings.edit_icon_url);
            var deleteIcon  = createIcon('lti_delete_tool_type', M.str.lti.deletetype, this.settings.delete_icon_url);

            editIcon.on('click', function(e){
                var toolTypeId = typeSelector.get('value');

                if(self.getSelectedToolTypeOption().getAttribute('editable')){
                    window.open(self.settings.instructor_tool_type_edit_url + '&action=edit&typeid=' + toolTypeId, 'edit_tool');
                } else {
                    alert(M.str.lti.cannot_edit);
                }
            });

            addIcon.on('click', function(e){
                window.open(self.settings.instructor_tool_type_edit_url + '&action=add', 'add_tool');
            });

            deleteIcon.on('click', function(e){
                var toolTypeId = typeSelector.get('value');

                if(self.getSelectedToolTypeOption().getAttribute('editable')){
                    if(confirm(M.str.lti.delete_confirmation)){
                        self.deleteTool(toolTypeId);
                    }
                } else {
                    alert(M.str.lti.cannot_delete);
                }
            });

            typeSelector.insert(addIcon, 'after');
            addIcon.insert(editIcon, 'after');
            editIcon.insert(deleteIcon, 'after');
        },

        toggleEditButtons: function(){
            var lti_edit_tool_type = Y.one('#lti_edit_tool_type');
            var lti_delete_tool_type = Y.one('#lti_delete_tool_type');

            //Make the edit / delete icons look enabled / disabled.
            //Does not work in older browsers, but alerts will catch those cases.
            if(this.getSelectedToolTypeOption().getAttribute('editable')){
                lti_edit_tool_type.setStyle('opacity', '1');
                lti_delete_tool_type.setStyle('opacity', '1');
            } else {
                lti_edit_tool_type.setStyle('opacity', '.2');
                lti_delete_tool_type.setStyle('opacity', '.2');
            }
        },

        addToolType: function(toolType){
            var typeSelector = Y.one('#id_typeid');
            var course_tool_group = Y.one('#course_tool_group');

            var option = Y.Node.create('<option />')
                            .set('text', toolType.name)
                            .set('value', toolType.id)
                            .set('selected', 'selected')
                            .setAttribute('editable', '1')
                            .setAttribute('courseTool', '1')
                            .setAttribute('domain', toolType.tooldomain);

            if(course_tool_group){
                course_tool_group.append(option);
            } else {
                typeSelector.append(option);
            }

            //Adding the new tool may affect which tool gets matched automatically
            this.clearToolCache();
            this.updateAutomaticToolMatch(Y.one('#id_toolurl'));
            this.updateAutomaticToolMatch(Y.one('#id_securetoolurl'));
        },

        updateToolType: function(toolType){
            var typeSelector = Y.one('#id_typeid');

            var option = typeSelector.one('option[value="' + toolType.id + '"]');
            option.set('text', toolType.name)
                  .set('domain', toolType.tooldomain);

            //Editing the tool may affect which tool gets matched automatically
            this.clearToolCache();
            this.updateAutomaticToolMatch(Y.one('#id_toolurl'));
            this.updateAutomaticToolMatch(Y.one('#id_securetoolurl'));
        },

        deleteTool: function(toolTypeId){
            var self = this;

            Y.io(self.settings.instructor_tool_type_edit_url + '&action=delete&typeid=' + toolTypeId, {
                on: {
                    success: function(){
                        self.getSelectedToolTypeOption().remove();

                        //Editing the tool may affect which tool gets matched automatically
                        self.clearToolCache();
                        self.updateAutomaticToolMatch(Y.one('#id_toolurl'));
                        self.updateAutomaticToolMatch(Y.one('#id_securetoolurl'));
                    },
                    failure: function(){

                    }
                }
            });
        },

        findToolByUrl: function(url, callback){
            var self = this;

            Y.io(self.settings.ajax_url, {
                data: {action: 'find_tool_config',
                        course: self.settings.courseId,
                        toolurl: url
                },

                on: {
                    success: function(transactionid, xhr){
                        var response = xhr.response;

                        var toolInfo = Y.JSON.parse(response);

                        callback(toolInfo);
                    },
                    failure: function(){

                    }
                }
            });
        }

    };
})();
