(function(){
    var Y;
    var self;
    
    M.mod_lti = M.mod_lti || {};

    M.mod_lti.editor = {
        init: function(yui3, settings){
            if(yui3){
                Y = yui3;
            }
            
            self = this;
            this.settings = Y.JSON.parse(settings);

            this.urlCache = {};

            this.addOptGroups();

            var typeSelector = Y.one('#id_typeid');
            typeSelector.on('change', function(e){
                self.toggleEditButtons();
            });

            this.createTypeEditorButtons();

            this.toggleEditButtons();
            
            var textAreas = new Y.NodeList([
                Y.one('#id_toolurl'),
                Y.one('#id_resourcekey'),
                Y.one('#id_password')
            ]);
            
            var debounce;
            textAreas.on('keyup', function(e){
                clearTimeout(debounce);

                //If no more changes within 2 seconds, look up the matching tool URL
                debounce = setTimeout(function(){
                    self.updateAutomaticToolMatch();
                }, 2000);
            });
            
            self.updateAutomaticToolMatch();
        },

        updateAutomaticToolMatch: function(){
            var toolurl = Y.one('#id_toolurl');
            var automatchToolDisplay = Y.one('#lti_automatch_tool');

            if(!automatchToolDisplay){
                automatchToolDisplay = Y.Node.create('<span />')
                                        .set('id', 'lti_automatch_tool')
                                        .setStyle('padding-left', '1em');
                                        
                toolurl.insert(automatchToolDisplay, 'after');
            }

            var url = toolurl.get('value');

            if(!url){
                automatchToolDisplay.setStyle('display', 'none');
                return;
            }

            var key = Y.one('#id_resourcekey');
            var secret = Y.one('#id_password');

            //We don't care what tool type this tool is associated with if it's manually configured'
            if(key.get('value') !== '' && secret.get('value') !== ''){
                automatchToolDisplay.set('innerHTML',  '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url + '" />Using custom tool configuration.');
            } else {
                var continuation = function(toolInfo){
                    automatchToolDisplay.setStyle('display', '');

                    if(toolInfo.toolname){
                        automatchToolDisplay.set('innerHTML',  '<img style="vertical-align:text-bottom" src="' + self.settings.green_check_icon_url + '" />Using tool configuration: ' + toolInfo.toolname);
                    } else {
                        //Inform them custom configuration is in use
                        if(key.get('value') === '' || secret.get('value') === ''){
                            automatchToolDisplay.set('innerHTML', '<img style="vertical-align:text-bottom" src="' + self.settings.yellow_check_icon_url + '" />Tool configuration not found for this URL.');
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

            return typeSelector.one('option[value=' + typeSelector.get('value') + ']');
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

                typeSelector.all('option[globalTool=1]').remove().each(function(node){
                    globalGroup.append(node);
                });

                typeSelector.all('option[courseTool=1]').remove().each(function(node){
                    courseGroup.append(node);
                });

                typeSelector.append(globalGroup);
                typeSelector.append(courseGroup);
            }
        },

        /**
         * Adds buttons for creating, editing, and deleting tool types.
         * Javascript is a requirement to edit course level tools at this point.
         */
        createTypeEditorButtons: function(){
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

                        Y.io(self.settings.instructor_tool_type_edit_url + '&action=delete&typeid=' + toolTypeId, {
                            on: {
                                success: function(){
                                    self.getSelectedToolTypeOption().remove();
                                },
                                failure: function(){

                                }
                            }
                        });
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

        addToolType: function(text, value){
            var typeSelector = Y.one('#id_typeid');
            var course_tool_group = Y.one('#course_tool_group');

            var option = Y.Node.create('<option />')
                            .set('text', text)
                            .set('value', value)
                            .set('selected', 'selected')
                            .setAttribute('editable', '1')
                            .setAttribute('courseTool', '1');

            if(course_tool_group){
                course_tool_group.append(option);
            } else {
                typeSelector.append(option);
            }
        },

        updateToolType: function(text, value){
            var typeSelector = Y.one('#id_typeid');

            var option = typeSelector.one('option[value=' + value + ']');
            option.set('text', text);
        },

        findToolByUrl: function(url, callback){
            Y.io(self.settings.ajax_url, { 
                data: { action: 'find_tool_config',
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