M.gradingform_rubriceditor = {'templates' : {}, 'eventhandler' : null};

/**
 * This function is called for each rubriceditor on page.
 */
M.gradingform_rubriceditor.init = function(Y, options) {
    M.gradingform_rubriceditor.templates[options.name] = {
        'criterion' : options.criteriontemplate,
        'level' : options.leveltemplate
    }
    M.gradingform_rubriceditor.addhandlers(Y, options.name);
};

// Adds handlers for clicking submit button. This function must be called each time JS adds new elements to html
M.gradingform_rubriceditor.addhandlers = function(Y, name) {
    if (M.gradingform_rubriceditor.eventhandler) M.gradingform_rubriceditor.eventhandler.detach()
    M.gradingform_rubriceditor.eventhandler = Y.on('click', M.gradingform_rubriceditor.buttonclick, '#rubriceditor-'+name+' input[type=submit]', null, Y, name);
}

// handler for clicking on submit buttons within rubriceditor element. Adds/deletes/rearranges criteria and/or levels on client side
M.gradingform_rubriceditor.buttonclick = function(e, Y, name, confirmed) {
    if (e.target.get('type') != 'submit') return;
    var chunks = e.target.get('id').split('-'),
        action = chunks[chunks.length-1]
    if (chunks[0] != name) return;
    var elements_str
    if (chunks.length>3 || action == 'addlevel') {
        elements_str = '#rubriceditor-'+name+' #'+name+'-'+chunks[1]+'-levels .level'
    } else {
        elements_str = '#rubriceditor-'+name+' .criterion'
    }
    // prepare the id of the next inserted level or criterion
    var newid = 1
    if (action == 'addcriterion' || action == 'addlevel') {
        Y.all(elements_str).each( function(node) {
            var idchunks = node.get('id').split('-'), id = idchunks.pop();
            if (id.match(/^NEWID(\d+)$/)) newid = Math.max(newid, parseInt(id.substring(5))+1);
        } );
    }
    var dialog_options = {
        'scope' : this,
        'callbackargs' : [e, Y, name, true],
        'callback' : M.gradingform_rubriceditor.buttonclick
    };
    if (chunks.length == 2 && action == 'addcriterion') {
        // ADD NEW CRITERION
        var newcriterion = M.gradingform_rubriceditor.templates[name]['criterion'].
            replace(/\{CRITERION-id\}/g, 'NEWID'+newid).replace(/\{.+?\}/g, '')
        Y.one('#'+name+'-criteria').append(newcriterion)
        M.gradingform_rubriceditor.addhandlers(Y, name);
    } else if (chunks.length == 3 && action == 'addlevel') {
        // ADD NEW LEVEL
        var newlevel = M.gradingform_rubriceditor.templates[name]['level'].
            replace(/\{CRITERION-id\}/g, chunks[1]).replace(/\{LEVEL-id\}/g, 'NEWID'+newid).replace(/\{.+?\}/g, '')
        Y.one('#'+name+'-'+chunks[1]+'-levels').append(newlevel)
        M.gradingform_rubriceditor.addhandlers(Y, name);
    } else if (chunks.length == 3 && action == 'moveup') {
        // MOVE CRITERION UP
        el = Y.one('#'+name+'-'+chunks[1])
        if (el.previous()) el.get('parentNode').insertBefore(el, el.previous())
    } else if (chunks.length == 3 && action == 'movedown') {
        // MOVE CRITERION DOWN
        el = Y.one('#'+name+'-'+chunks[1])
        if (el.next()) el.get('parentNode').insertBefore(el.next(), el)
    } else if (chunks.length == 3 && action == 'delete') {
        // DELETE CRITERION
        if (confirmed) {
            Y.one('#'+name+'-'+chunks[1]).remove()
        } else {
            dialog_options['message'] = M.str.gradingform_rubric.confirmdeletecriterion
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else if (chunks.length == 5 && action == 'delete') {
        // DELETE LEVEL
        if (confirmed) {
            Y.one('#'+name+'-'+chunks[1]+'-'+chunks[2]+'-'+chunks[3]).remove()
        } else {
            dialog_options['message'] = M.str.gradingform_rubric.confirmdeletelevel
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else {
        // unknown action
        return;
    }
    e.preventDefault();
    // properly set classes and sortorder
    var elements = Y.all(elements_str)
    for (var i=0;i<elements.size();i++) {
        elements.item(i).removeClass('first').removeClass('last').removeClass('even').removeClass('odd').
            addClass(((i%2)?'odd':'even') + ((i==0)?' first':'') + ((i==elements.size()-1)?' last':''))
        elements.item(i).all('input[type=hidden]').each(
            function(node) { if (node.get('name').match(/sortorder/)) node.set('value', i) }
        );
    }
}
