M.gradingform_rubriceditor = {'templates' : {}, 'eventhandler' : null, 'name' : null, 'Y' : null};

/**
 * This function is called for each rubriceditor on page.
 */
M.gradingform_rubriceditor.init = function(Y, options) {
    M.gradingform_rubriceditor.name = options.name
    M.gradingform_rubriceditor.Y = Y
    M.gradingform_rubriceditor.templates[options.name] = {
        'criterion' : options.criteriontemplate,
        'level' : options.leveltemplate
    }
    M.gradingform_rubriceditor.disablealleditors()
    Y.on('click', M.gradingform_rubriceditor.clickanywhere, 'body', null)
    M.gradingform_rubriceditor.addhandlers()
};

// Adds handlers for clicking submit button. This function must be called each time JS adds new elements to html
M.gradingform_rubriceditor.addhandlers = function() {
    var Y = M.gradingform_rubriceditor.Y
    var name = M.gradingform_rubriceditor.name
    if (M.gradingform_rubriceditor.eventhandler) M.gradingform_rubriceditor.eventhandler.detach()
    M.gradingform_rubriceditor.eventhandler = Y.on('click', M.gradingform_rubriceditor.buttonclick, '#rubric-'+name+' input[type=submit]', null);
}

M.gradingform_rubriceditor.disablealleditors = function() {
    var Y = M.gradingform_rubriceditor.Y
    var name = M.gradingform_rubriceditor.name
    Y.all('#rubric-'+name+' .level').each( function(node) {M.gradingform_rubriceditor.editmode(node, false)} );
    Y.all('#rubric-'+name+' .description').each( function(node) {M.gradingform_rubriceditor.editmode(node, false)} );
}

M.gradingform_rubriceditor.clickanywhere = function(e) {
    var el = e.target
    // if clicked on button - disablecurrenteditor, continue
    if (el.get('tagName') == 'INPUT' && el.get('type') == 'submit') {
        return
    }
    // else if clicked on level and this level is not enabled - enable it
    // or if clicked on description and this description is not enabled - enable it
    var focustb = false
    while (el && !(el.hasClass('level') || el.hasClass('description'))) {
        if (el.hasClass('score')) focustb = true
        el = el.get('parentNode')
    }
    if (el) {
        if (el.one('textarea').getStyle('display') == 'none') {
            M.gradingform_rubriceditor.disablealleditors()
            M.gradingform_rubriceditor.editmode(el, true, focustb)
        }
        return
    }
    // else disablecurrenteditor
    M.gradingform_rubriceditor.disablealleditors()
}

M.gradingform_rubriceditor.editmode = function(el, editmode, focustb) {
    var ta = el.one('textarea')
    if (!ta.get('parentNode').one('.plainvalue')) {
        ta.get('parentNode').append('<div class="plainvalue"></div>')
    }
    var tb = el.one('input[type=text]')
    if (tb && !tb.get('parentNode').one('.plainvalue')) {
        tb.get('parentNode').append('<div class="plainvalue"></div>')
    }
    if (!editmode) {
        if (ta.getStyle('display') == 'none') return;
        var value = ta.get('value')
        if (value.length) ta.get('parentNode').one('.plainvalue').removeClass('empty')
        else {
            value = (el.hasClass('level')) ? M.str.gradingform_rubric.levelempty : M.str.gradingform_rubric.criterionempty
            ta.get('parentNode').one('.plainvalue').addClass('empty')
        }
        ta.get('parentNode').one('.plainvalue').set('innerHTML', value)
        ta.get('parentNode').one('.plainvalue').setStyle('display', 'block')
        ta.setStyle('display', 'none')
        if (tb) {
            tb.get('parentNode').one('.plainvalue').set('innerHTML', tb.get('value'))
            tb.get('parentNode').one('.plainvalue').setStyle('display', 'inline-block')
            tb.setStyle('display', 'none')
        }
    } else {
        if (tb) {
            tb.get('parentNode').one('.plainvalue').setStyle('display', 'none')
            tb.setStyle('display', 'inline-block')
        }
        var width = ta.get('parentNode').getComputedStyle('width') // TODO min width
        var height = ta.get('parentNode').getComputedStyle('height') // TODO min height
        if (el.hasClass('level')) {
            height = el.getComputedStyle('height') - el.one('.score').getComputedStyle('height')
        } else if (el.hasClass('description')) {
            height = el.get('parentNode').getComputedStyle('height')
        }
        ta.get('parentNode').one('.plainvalue').setStyle('display', 'none')
        ta.setStyle('display', 'block').setStyle('width', width).setStyle('height', height)
        if (tb && focustb) tb.focus(); else ta.focus()
    }
    if (!ta.get('parentNode').one('.plainvalue').one('.pseudotablink')) {
        var pseudotablink = '<a href="#" class="pseudotablink"> </a>'
        ta.get('parentNode').one('.plainvalue').append(pseudotablink)
        ta.get('parentNode').one('.plainvalue').one('.pseudotablink').on('focus', M.gradingform_rubriceditor.clickanywhere)
        if (tb) {
            tb.get('parentNode').one('.plainvalue').append(pseudotablink)
            tb.get('parentNode').one('.plainvalue').one('.pseudotablink').on('focus', M.gradingform_rubriceditor.clickanywhere)
        }
    }
}

// handler for clicking on submit buttons within rubriceditor element. Adds/deletes/rearranges criteria and/or levels on client side
M.gradingform_rubriceditor.buttonclick = function(e, confirmed) {
    var Y = M.gradingform_rubriceditor.Y
    var name = M.gradingform_rubriceditor.name
    if (e.target.get('type') != 'submit') return;
    M.gradingform_rubriceditor.disablealleditors()
    var chunks = e.target.get('id').split('-'),
        action = chunks[chunks.length-1]
    if (chunks[0] != name || chunks[1] != 'criteria') return;
    var elements_str
    if (chunks.length>4 || action == 'addlevel') {
        elements_str = '#rubric-'+name+' #'+name+'-criteria-'+chunks[2]+'-levels .level'
    } else {
        elements_str = '#rubric-'+name+' .criterion'
    }
    // prepare the id of the next inserted level or criterion
    if (action == 'addcriterion' || action == 'addlevel') {
        var newid = M.gradingform_rubriceditor.calculatenewid('#rubric-'+name+' .criterion')
        var newlevid = M.gradingform_rubriceditor.calculatenewid('#rubric-'+name+' .level')
    }
    var dialog_options = {
        'scope' : this,
        'callbackargs' : [e, true],
        'callback' : M.gradingform_rubriceditor.buttonclick
    };
    if (chunks.length == 3 && action == 'addcriterion') {
        // ADD NEW CRITERION
        var nlevels = 3
        var criteria = Y.all('#'+name+'-criteria .criterion')
        if (criteria.size()) nlevels = Math.max(nlevels, criteria.item(criteria.size()-1).all('.level').size())
        var levelsstr = '';
        for (var levidx=0;levidx<nlevels;levidx++) {
            levelsstr += M.gradingform_rubriceditor.templates[name]['level'].
            replace(/\{CRITERION-id\}/g, 'NEWID'+newid).replace(/\{LEVEL-id\}/g, 'NEWID'+(newlevid+levidx)).replace(/\{.+?\}/g, '')
        }
        var newcriterion = M.gradingform_rubriceditor.templates[name]['criterion'].
            replace(/\{CRITERION-id\}/g, 'NEWID'+newid).replace(/\{LEVELS\}/, levelsstr).replace(/\{.+?\}/g, '')
        var parentel = Y.one('#'+name+'-criteria')
        if (parentel.one('>tbody')) parentel = parentel.one('>tbody')
        parentel.append(newcriterion)
        M.gradingform_rubriceditor.addhandlers();
        M.gradingform_rubriceditor.assignclasses('#rubric-'+name+' #'+name+'-criteria-NEWID'+newid+'-levels .level')
        M.gradingform_rubriceditor.editmode(Y.one('#rubric-'+name+' #'+name+'-criteria-NEWID'+newid+'-description'),true)
    } else if (chunks.length == 5 && action == 'addlevel') {
        // ADD NEW LEVEL
        var newlevel = M.gradingform_rubriceditor.templates[name]['level'].
            replace(/\{CRITERION-id\}/g, chunks[2]).replace(/\{LEVEL-id\}/g, 'NEWID'+newlevid).replace(/\{.+?\}/g, '')
        Y.one('#'+name+'-criteria-'+chunks[2]+'-levels').append(newlevel)
        var levels = Y.all('#'+name+'-criteria-'+chunks[2]+'-levels .level')
        if (levels.size()) levels.set('width', Math.round(100/levels.size())+'%')
        M.gradingform_rubriceditor.addhandlers();
        M.gradingform_rubriceditor.editmode(levels.item(levels.size()-1),true)
    } else if (chunks.length == 4 && action == 'moveup') {
        // MOVE CRITERION UP
        el = Y.one('#'+name+'-criteria-'+chunks[2])
        if (el.previous()) el.get('parentNode').insertBefore(el, el.previous())
    } else if (chunks.length == 4 && action == 'movedown') {
        // MOVE CRITERION DOWN
        el = Y.one('#'+name+'-criteria-'+chunks[2])
        if (el.next()) el.get('parentNode').insertBefore(el.next(), el)
    } else if (chunks.length == 4 && action == 'delete') {
        // DELETE CRITERION
        if (confirmed) {
            Y.one('#'+name+'-criteria-'+chunks[2]).remove()
        } else {
            dialog_options['message'] = M.str.gradingform_rubric.confirmdeletecriterion
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else if (chunks.length == 6 && action == 'delete') {
        // DELETE LEVEL
        if (confirmed) {
            Y.one('#'+name+'-criteria-'+chunks[2]+'-'+chunks[3]+'-'+chunks[4]).remove()
            levels = Y.all('#'+name+'-criteria-'+chunks[2]+'-levels .level')
            if (levels.size()) levels.set('width', Math.round(100/levels.size())+'%')
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
    M.gradingform_rubriceditor.assignclasses(elements_str)
}

M.gradingform_rubriceditor.assignclasses = function (elements_str) {
    var elements = M.gradingform_rubriceditor.Y.all(elements_str)
    for (var i=0;i<elements.size();i++) {
        elements.item(i).removeClass('first').removeClass('last').removeClass('even').removeClass('odd').
            addClass(((i%2)?'odd':'even') + ((i==0)?' first':'') + ((i==elements.size()-1)?' last':''))
        elements.item(i).all('input[type=hidden]').each(
            function(node) { if (node.get('name').match(/sortorder/)) node.set('value', i) }
        );
    }
}

M.gradingform_rubriceditor.calculatenewid = function (elements_str) {
    var newid = 1
    M.gradingform_rubriceditor.Y.all(elements_str).each( function(node) {
        var idchunks = node.get('id').split('-'), id = idchunks.pop();
        if (id.match(/^NEWID(\d+)$/)) newid = Math.max(newid, parseInt(id.substring(5))+1);
    } );
    return newid
}