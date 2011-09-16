M.mod_lti = M.mod_lti || {};

M.mod_lti.editor = {
    init: function(Y, settings){
        this.Y = Y;
        var self = this;
        this.settings = Y.JSON.parse(settings);

        var typeSelector = Y.one('#id_typeid');
        typeSelector.on('change', function(e){
            self.toggleEditButtons();
        });

        this.createTypeEditorButtons();
        
        this.toggleEditButtons();
    },
    
    getSelectedToolTypeOption: function(){
        var Y = this.Y;
        var typeSelector = Y.one('#id_typeid');
        
        return typeSelector.one('option[value=' + typeSelector.get('value') + ']');
    },
    
    /**
     * Adds buttons for creating, editing, and deleting tool types
     */
    createTypeEditorButtons: function(){
        var Y = this.Y;
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
        
        var addIcon = createIcon('lti_add_tool_type', 'Add new tool type', this.settings.add_icon_url);
        var editIcon = createIcon('lti_edit_tool_type', 'Edit new tool type', this.settings.edit_icon_url);
        var deleteIcon  = createIcon('lti_delete_tool_type', 'Delete tool type', this.settings.delete_icon_url);
        
        editIcon.on('click', function(e){
            var toolTypeId = typeSelector.get('value');
            
            if(self.getSelectedToolTypeOption().getAttribute('editable')){
                window.open(self.settings.instructor_tool_type_edit_url + '&action=edit&typeid=' + toolTypeId, 'edit_tool');
            }
        });
        
        addIcon.on('click', function(e){
            window.open(self.settings.instructor_tool_type_edit_url + '&action=add', 'add_tool');
        });
        
        deleteIcon.on('click', function(e){
            var toolTypeId = typeSelector.get('value');
            
            if(self.getSelectedToolTypeOption().getAttribute('editable')){
                Y.io(self.settings.instructor_tool_type_edit_url + '&action=delete&typeid=' + toolTypeId, {
                    on: {
                        success: function(){
                            getSelectedOption().remove();
                        },
                        failure: function(){

                        }
                    }
                });
            }
        });
        
        typeSelector.insert(addIcon, 'after');
        addIcon.insert(editIcon, 'after');
        editIcon.insert(deleteIcon, 'after');
    },
    
    toggleEditButtons: function(){
        var Y = this.Y;
        
        var lti_edit_tool_type = Y.one('#lti_edit_tool_type');
        var lti_delete_tool_type = Y.one('#lti_delete_tool_type');
        
        if(this.getSelectedToolTypeOption().getAttribute('editable')){
            lti_edit_tool_type.setStyle('opacity', '1');
            lti_delete_tool_type.setStyle('opacity', '1');
        } else {
            lti_edit_tool_type.setStyle('opacity', '.2');
            lti_delete_tool_type.setStyle('opacity', '.2');
        }
    },
    
    addToolType: function(text, value){
        var Y = this.Y;
        var typeSelector = Y.one('#id_typeid');
        
        var option = Y.Node.create('<option />')
                        .set('text', text)
                        .set('value', value)
                        .set('selected', 'selected');
                        
        typeSelector.append(option);
    },
    
    updateToolType: function(text, value){
        var Y = this.Y;
        var typeSelector = Y.one('#id_typeid');
        
        var option = Y.Node.create('<option />')
                        .set('text', text)
                        .set('value', value)
                        .set('selected', 'selected');
                        
        typeSelector.append(option);
    }

};



