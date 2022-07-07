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
 * Javascript extensions for the External Tool activity editor.
 *
 * @package    mod
 * @subpackage lti
 * @copyright  Copyright (c) 2011 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function(){
    var Y;

    M.mod_lti = M.mod_lti || {};

    M.mod_lti.LTI_SETTING_NEVER = 0;
    M.mod_lti.LTI_SETTING_ALWAYS = 1;
    M.mod_lti.LTI_SETTING_DELEGATE = 2;

    M.mod_lti.editor = {
        init: function(yui3, settings){
            if(yui3){
                Y = yui3;
            }

            var self = this;
            this.settings = Y.JSON.parse(settings);

            this.urlCache = {};
            this.toolTypeCache = {};

            var updateToolMatches = function(){
                self.updateAutomaticToolMatch(Y.one('#id_toolurl'));
                self.updateAutomaticToolMatch(Y.one('#id_securetoolurl'));
            };

            var typeSelector = Y.one('#id_typeid');
            if (typeSelector) {
                this.addOptGroups();

                typeSelector.on('change', function(e){
                    // Reset configuration fields when another preconfigured tool is selected.
                    self.resetToolFields();

                    updateToolMatches();

                    self.toggleEditButtons();

                    if (self.getSelectedToolTypeOption().getAttribute('toolproxy')){
                        var allowname = Y.one('#id_instructorchoicesendname');
                        allowname.set('checked', !self.getSelectedToolTypeOption().getAttribute('noname'));

                        var allowemail = Y.one('#id_instructorchoicesendemailaddr');
                        allowemail.set('checked', !self.getSelectedToolTypeOption().getAttribute('noemail'));

                        var allowgrades = Y.one('#id_instructorchoiceacceptgrades');
                        allowgrades.set('checked', !self.getSelectedToolTypeOption().getAttribute('nogrades'));
                        self.toggleGradeSection();
                    }
                });

                this.createTypeEditorButtons();

                this.toggleEditButtons();
            }

            var contentItemButton = Y.one('[name="selectcontent"]');
            if (contentItemButton) {
                var contentItemUrl = contentItemButton.getAttribute('data-contentitemurl');
                // Handle configure from link button click.
                contentItemButton.on('click', function() {
                    var contentItemId = self.getContentItemId();
                    if (contentItemId) {
                        // Get activity name and description values.
                        var title = Y.one('#id_name').get('value').trim();
                        var text = Y.one('#id_introeditor').get('value').trim();

                        // Set data to be POSTed.
                        var postData = {
                            id: contentItemId,
                            course: self.settings.courseId,
                            title: title,
                            text: text
                        };

                        require(['mod_lti/contentitem'], function(contentitem) {
                            contentitem.init(contentItemUrl, postData, function(returnData) {
                                if (!returnData.multiple) {
                                    M.mod_lti.editor.toggleGradeSection();
                                }
                            });
                        });
                    }
                });
            }

            var textAreas = new Y.NodeList([
                Y.one('#id_toolurl'),
                Y.one('#id_securetoolurl'),
                Y.one('#id_resourcekey'),
                Y.one('#id_password')
            ]);

            var debounce;
            textAreas.on('keyup', function(e){
                clearTimeout(debounce);

                // If no more changes within 2 seconds, look up the matching tool URL
                debounce = setTimeout(function(){
                    updateToolMatches();
                }, 2000);
            });

            var allowgrades = Y.one('#id_instructorchoiceacceptgrades');
            allowgrades.on('change', this.toggleGradeSection, this);

            if (typeSelector) {
                updateToolMatches();
            }
        },

        toggleGradeSection: function(e) {
            if (e) {
                e.preventDefault();
            }
            var allowgrades = Y.one('#id_instructorchoiceacceptgrades');
            var gradefieldset = Y.one('#id_modstandardgrade');
            if (!allowgrades.get('checked')) {
                gradefieldset.hide();
            } else {
                gradefieldset.show();
            }
        },

        clearToolCache: function(){
            this.urlCache = {};
            this.toolTypeCache = {};
        },

        updateAutomaticToolMatch: function(field){
            if (!field) {
                return;
            }

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

            // Hide the display if the url box is empty
            if(!url){
                automatchToolDisplay.setStyle('display', 'none');
            } else {
                automatchToolDisplay.set('innerHTML', '');
                automatchToolDisplay.setStyle('display', '');
            }

            var selectedToolType = parseInt(typeSelector.get('value'));
            var selectedOption = typeSelector.one('option[value="' + selectedToolType + '"]');

            // A specific tool type is selected (not "auto")
            // We still need to check with the server to get privacy settings
            if(selectedToolType > 0){
                // If the entered domain matches the domain of the tool configuration...
                var domainRegex = /(?:https?:\/\/)?(?:www\.)?([^\/]+)(?:\/|$)/i;
                var match = domainRegex.exec(url);
                if(match && match[1] && match[1].toLowerCase() === selectedOption.getAttribute('domain').toLowerCase()){
                    automatchToolDisplay.set('innerHTML',  '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url + '" />' + M.util.get_string('using_tool_configuration', 'lti') + selectedOption.get('text'));
                } else {
                    // The entered URL does not match the domain of the tool configuration
                    automatchToolDisplay.set('innerHTML', '<img style="vertical-align:text-bottom" src="' + self.settings.warning_icon_url + '" />' + M.util.get_string('domain_mismatch', 'lti'));
                }
            }

            var key = Y.one('#id_resourcekey');
            var secret = Y.one('#id_password');

            // Indicate the tool is manually configured
            // We still check the Launch URL with the server as course/site tools may override privacy settings
            if(key.get('value') !== '' && secret.get('value') !== ''){
                automatchToolDisplay.set('innerHTML',  '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url + '" />' + M.util.get_string('custom_config', 'lti'));
            }

            var continuation = function(toolInfo, inputfield){
                if (inputfield === undefined || (inputfield.get('id') != 'id_securetoolurl' || inputfield.get('value'))) {
                    self.updatePrivacySettings(toolInfo);
                }
                if(toolInfo.toolname){
                    automatchToolDisplay.set('innerHTML',  '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url + '" />' + M.util.get_string('using_tool_configuration', 'lti') + toolInfo.toolname);
                } else if(!selectedToolType) {
                    // Inform them custom configuration is in use
                    if(key.get('value') === '' || secret.get('value') === ''){
                        automatchToolDisplay.set('innerHTML', '<img style="vertical-align:text-bottom" src="' + self.settings.warning_icon_url + '" />' + M.util.get_string('tool_config_not_found', 'lti'));
                    }
                }
                if (toolInfo.cartridge) {
                    automatchToolDisplay.set('innerHTML', '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url +
                                             '" />' + M.util.get_string('using_tool_cartridge', 'lti'));
                }
            };

            // Cache urls which have already been checked to increase performance
            // Don't use URL cache if tool type manually selected
            if(selectedToolType && self.toolTypeCache[selectedToolType]){
                return continuation(self.toolTypeCache[selectedToolType]);
            } else if(self.urlCache[url] && !selectedToolType){
                return continuation(self.urlCache[url]);
            } else if(!selectedToolType && !url) {
                // No tool type or url set
                return continuation({}, field);
            } else {
                self.findToolByUrl(url, selectedToolType, function(toolInfo){
                    if(toolInfo){
                        // Cache the result based on whether the URL or tool type was used to look up the tool
                        if(!selectedToolType){
                            self.urlCache[url] = toolInfo;
                        } else {
                            self.toolTypeCache[selectedToolType] = toolInfo;
                        }

                        Y.one('#id_urlmatchedtypeid').set('value', toolInfo.toolid);

                        continuation(toolInfo);
                    }
                });
            }
        },

        /**
         * Updates display of privacy settings to show course / site tool configuration settings.
         */
        updatePrivacySettings: function(toolInfo){
            if(!toolInfo || !toolInfo.toolid){
                toolInfo = {
                    sendname: M.mod_lti.LTI_SETTING_DELEGATE,
                    sendemailaddr: M.mod_lti.LTI_SETTING_DELEGATE,
                    acceptgrades: M.mod_lti.LTI_SETTING_DELEGATE
                }
            }

            var setting, control;

            var privacyControls = {
                sendname: Y.one('#id_instructorchoicesendname'),
                sendemailaddr: Y.one('#id_instructorchoicesendemailaddr'),
                acceptgrades: Y.one('#id_instructorchoiceacceptgrades')
            };

            // Store a copy of user entered privacy settings as we may overwrite them
            if(!this.userPrivacySettings){
                this.userPrivacySettings = {};
            }

            for(setting in privacyControls){
                if(privacyControls.hasOwnProperty(setting)){
                    control = privacyControls[setting];

                    // Only store the value if it hasn't been forced by the editor
                    if(!control.get('disabled')){
                        this.userPrivacySettings[setting] = control.get('checked');
                    }
                }
            }

            // Update UI based on course / site tool configuration
            for(setting in privacyControls){
                if(privacyControls.hasOwnProperty(setting)){
                    var settingValue = toolInfo[setting];
                    control = privacyControls[setting];

                    if(settingValue == M.mod_lti.LTI_SETTING_NEVER){
                        control.set('disabled', true);
                        control.set('checked', false);
                        control.set('title', M.util.get_string('forced_help', 'lti'));
                    } else if(settingValue == M.mod_lti.LTI_SETTING_ALWAYS){
                        control.set('disabled', true);
                        control.set('checked', true);
                        control.set('title', M.util.get_string('forced_help', 'lti'));
                    } else if(settingValue == M.mod_lti.LTI_SETTING_DELEGATE){
                        control.set('disabled', false);

                        // Get the value out of the stored copy
                        control.set('checked', this.userPrivacySettings[setting]);
                        control.set('title', '');
                    }
                }
            }

            this.toggleGradeSection();
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
                // One ore more course tools exist

                var globalGroup = Y.Node.create('<optgroup />')
                                    .set('id', 'global_tool_group')
                                    .set('label', M.util.get_string('global_tool_types', 'lti'));

                var courseGroup = Y.Node.create('<optgroup />')
                                    .set('id', 'course_tool_group')
                                    .set('label', M.util.get_string('course_tool_types', 'lti'));

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

            var addIcon = createIcon('lti_add_tool_type', M.util.get_string('addtype', 'lti'), this.settings.add_icon_url);
            var editIcon = createIcon('lti_edit_tool_type', M.util.get_string('edittype', 'lti'), this.settings.edit_icon_url);
            var deleteIcon  = createIcon('lti_delete_tool_type', M.util.get_string('deletetype', 'lti'), this.settings.delete_icon_url);

            editIcon.on('click', function(e){
                var toolTypeId = typeSelector.get('value');

                if(self.getSelectedToolTypeOption().getAttribute('editable')){
                    window.open(self.settings.instructor_tool_type_edit_url + '&action=edit&typeid=' + toolTypeId, 'edit_tool');
                } else {
                    alert(M.util.get_string('cannot_edit', 'lti'));
                }
            });

            addIcon.on('click', function(e){
                window.open(self.settings.instructor_tool_type_edit_url + '&action=add', 'add_tool');
            });

            deleteIcon.on('click', function(e){
                var toolTypeId = typeSelector.get('value');

                if(self.getSelectedToolTypeOption().getAttribute('editable')){
                    if(confirm(M.util.get_string('delete_confirmation', 'lti'))){
                        self.deleteTool(toolTypeId);
                    }
                } else {
                    alert(M.util.get_string('cannot_delete', 'lti'));
                }
            });

            typeSelector.insert(addIcon, 'after');
            addIcon.insert(editIcon, 'after');
            editIcon.insert(deleteIcon, 'after');
        },

        toggleEditButtons: function(){
            var lti_edit_tool_type = Y.one('#lti_edit_tool_type');
            var lti_delete_tool_type = Y.one('#lti_delete_tool_type');

            // Make the edit / delete icons look enabled / disabled.
            // Does not work in older browsers, but alerts will catch those cases.
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

            // Adding the new tool may affect which tool gets matched automatically
            this.clearToolCache();
            this.updateAutomaticToolMatch(Y.one('#id_toolurl'));
            this.updateAutomaticToolMatch(Y.one('#id_securetoolurl'));
            this.toggleEditButtons();

            require(["core/notification"], function (notification) {
                notification.addNotification({
                    message: M.util.get_string('tooltypeadded', 'lti'),
                    type: "success"
                });
            });
        },

        updateToolType: function(toolType){
            var typeSelector = Y.one('#id_typeid');

            var option = typeSelector.one('option[value="' + toolType.id + '"]');
            option.set('text', toolType.name)
                  .set('domain', toolType.tooldomain);

            // Editing the tool may affect which tool gets matched automatically
            this.clearToolCache();
            this.updateAutomaticToolMatch(Y.one('#id_toolurl'));
            this.updateAutomaticToolMatch(Y.one('#id_securetoolurl'));

            require(["core/notification"], function (notification) {
                notification.addNotification({
                    message: M.util.get_string('tooltypeupdated', 'lti'),
                    type: "success"
                });
            });
        },

        deleteTool: function(toolTypeId){
            var self = this;

            Y.io(self.settings.instructor_tool_type_edit_url + '&action=delete&typeid=' + toolTypeId, {
                on: {
                    success: function(){
                        self.getSelectedToolTypeOption().remove();

                        // Editing the tool may affect which tool gets matched automatically
                        self.clearToolCache();
                        self.updateAutomaticToolMatch(Y.one('#id_toolurl'));
                        self.updateAutomaticToolMatch(Y.one('#id_securetoolurl'));

                        require(["core/notification"], function (notification) {
                            notification.addNotification({
                                message: M.util.get_string('tooltypedeleted', 'lti'),
                                type: "success"
                            });
                        });
                    },
                    failure: function(){
                        require(["core/notification"], function (notification) {
                            notification.addNotification({
                                message: M.util.get_string('tooltypenotdeleted', 'lti'),
                                type: "problem"
                            });
                        });
                    }
                }
            });
        },

        findToolByUrl: function(url, toolId, callback){
            var self = this;

            Y.io(self.settings.ajax_url, {
                data: {action: 'find_tool_config',
                        course: self.settings.courseId,
                        toolurl: url,
                        toolid: toolId || 0
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
        },

        /**
         * Gets the tool type ID of the selected tool that supports Content-Item selection.
         *
         * @returns {number|boolean} The ID of the tool type if it supports Content-Item selection. False, otherwise.
         */
        getContentItemId: function() {
            try {
                var selected = this.getSelectedToolTypeOption();
                if (selected.getAttribute('data-contentitem')) {
                    return selected.getAttribute('data-id');
                }
                return false;
            } catch (err) {
                // Tool selector not available - check for hidden fields instead.
                var content = Y.one('input[name="contentitem"]');
                if (!content || !content.get('value')) {
                    return false;
                }
                return Y.one('input[name="typeid"]').get('value');
            }
        },

        /**
         * Resets the values of fields related to the LTI tool settings.
         */
        resetToolFields: function() {
            // Reset values for all text fields.
            var fields = Y.all('#id_toolurl, #id_securetoolurl, #id_instructorcustomparameters, #id_icon, #id_secureicon');
            fields.set('value', null);

            // Reset value for launch container select box.
            Y.one('#id_launchcontainer').set('value', 1);
        }
    };
})();
