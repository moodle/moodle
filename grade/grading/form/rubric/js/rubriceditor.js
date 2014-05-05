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
    YUI().use('event-touch', function (Y) {
        Y.one('body').on('touchstart', M.gradingform_rubriceditor.clickanywhere);
        Y.one('body').on('touchend', M.gradingform_rubriceditor.clickanywhere);
    })
    M.gradingform_rubriceditor.addhandlers()
};

// Adds handlers for clicking submit button. This function must be called each time JS adds new elements to html
M.gradingform_rubriceditor.addhandlers = function() {
    var Y = M.gradingform_rubriceditor.Y
    var name = M.gradingform_rubriceditor.name
    if (M.gradingform_rubriceditor.eventhandler) M.gradingform_rubriceditor.eventhandler.detach()
    M.gradingform_rubriceditor.eventhandler = Y.on('click', M.gradingform_rubriceditor.buttonclick, '#rubric-'+name+' input[type=submit]', null);
}

// switches all input text elements to non-edit mode
M.gradingform_rubriceditor.disablealleditors = function() {
    var Y = M.gradingform_rubriceditor.Y
    var name = M.gradingform_rubriceditor.name
    Y.all('#rubric-'+name+' .level').each( function(node) {M.gradingform_rubriceditor.editmode(node, false)} );
    Y.all('#rubric-'+name+' .description').each( function(node) {M.gradingform_rubriceditor.editmode(node, false)} );
}

// function invoked on each click on the page. If level and/or criterion description is clicked
// it switches this element to edit mode. If rubric button is clicked it does nothing so the 'buttonclick'
// function is invoked
M.gradingform_rubriceditor.clickanywhere = function(e) {
    if (e.type == 'touchstart') return
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
        if (el.one('textarea').hasClass('hiddenelement')) {
            M.gradingform_rubriceditor.disablealleditors()
            M.gradingform_rubriceditor.editmode(el, true, focustb)
        }
        return
    }
    // else disablecurrenteditor
    M.gradingform_rubriceditor.disablealleditors()
}

// switch the criterion description or level to edit mode or switch back
M.gradingform_rubriceditor.editmode = function(el, editmode, focustb) {
    var ta = el.one('textarea')
    if (!editmode && ta.hasClass('hiddenelement')) return;
    if (editmode && !ta.hasClass('hiddenelement')) return;
    var pseudotablink = '<input type="text" size="1" class="pseudotablink"/>',
        taplain = ta.get('parentNode').one('.plainvalue'),
        tbplain = null,
        tb = el.one('.score input[type=text]')
    // add 'plainvalue' next to textarea for description/definition and next to input text field for score (if applicable)
    if (!taplain) {
        ta.get('parentNode').append('<div class="plainvalue">'+pseudotablink+'<span class="textvalue">&nbsp;</span></div>')
        taplain = ta.get('parentNode').one('.plainvalue')
        taplain.one('.pseudotablink').on('focus', M.gradingform_rubriceditor.clickanywhere)
        if (tb) {
            tb.get('parentNode').append('<span class="plainvalue">'+pseudotablink+'<span class="textvalue">&nbsp;</span></span>')
            tbplain = tb.get('parentNode').one('.plainvalue')
            tbplain.one('.pseudotablink').on('focus', M.gradingform_rubriceditor.clickanywhere)
        }
    }
    if (tb && !tbplain) tbplain = tb.get('parentNode').one('.plainvalue')
    if (!editmode) {
        // if we need to hide the input fields, copy their contents to plainvalue(s). If description/definition
        // is empty, display the default text ('Click to edit ...') and add/remove 'empty' CSS class to element
        var value = ta.get('value')
        if (value.length) taplain.removeClass('empty')
        else {
            value = (el.hasClass('level')) ? M.str.gradingform_rubric.levelempty : M.str.gradingform_rubric.criterionempty
            taplain.addClass('empty')
        }
        taplain.one('.textvalue').set('innerHTML', value)
        if (tb) tbplain.one('.textvalue').set('innerHTML', tb.get('value'))
        // hide/display textarea, textbox and plaintexts
        taplain.removeClass('hiddenelement')
        ta.addClass('hiddenelement')
        if (tb) {
            tbplain.removeClass('hiddenelement')
            tb.addClass('hiddenelement')
        }
    } else {
        // if we need to show the input fields, set the width/height for textarea so it fills the cell
        try {
            var width = parseFloat(ta.get('parentNode').getComputedStyle('width')),
                height
            if (el.hasClass('level')) height = parseFloat(el.getComputedStyle('height')) - parseFloat(el.one('.score').getComputedStyle('height'))
            else height = parseFloat(ta.get('parentNode').getComputedStyle('height'))
            ta.setStyle('width', Math.max(width-16,50)+'px')
            ta.setStyle('height', Math.max(height,20)+'px')
        }
        catch (err) {
            // this browser do not support 'computedStyle', leave the default size of the textbox
        }
        // hide/display textarea, textbox and plaintexts
        taplain.addClass('hiddenelement')
        ta.removeClass('hiddenelement')
        if (tb) {
            tbplain.addClass('hiddenelement')
            tb.removeClass('hiddenelement')
        }
    }
    // focus the proper input field in edit mode
    if (editmode) { if (tb && focustb) tb.focus(); else ta.focus() }
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
        var levelsscores = [0], levidx = 1
        var parentel = Y.one('#'+name+'-criteria')
        if (parentel.one('>tbody')) parentel = parentel.one('>tbody')
        if (parentel.all('.criterion').size()) {
            var lastcriterion = parentel.all('.criterion').item(parentel.all('.criterion').size()-1).all('.level')
            for (levidx=0;levidx<lastcriterion.size();levidx++) levelsscores[levidx] = lastcriterion.item(levidx).one('.score input[type=text]').get('value')
        }
        for (levidx;levidx<3;levidx++) levelsscores[levidx] = parseFloat(levelsscores[levidx-1])+1
        var levelsstr = '';
        for (levidx=0;levidx<levelsscores.length;levidx++) {
            levelsstr += M.gradingform_rubriceditor.templates[name]['level'].replace(/\{LEVEL-id\}/g, 'NEWID'+(newlevid+levidx)).replace(/\{LEVEL-score\}/g, levelsscores[levidx])
        }
        var newcriterion = M.gradingform_rubriceditor.templates[name]['criterion'].replace(/\{LEVELS\}/, levelsstr)
        parentel.append(newcriterion.replace(/\{CRITERION-id\}/g, 'NEWID'+newid).replace(/\{.+?\}/g, ''))
        M.gradingform_rubriceditor.assignclasses('#rubric-'+name+' #'+name+'-criteria-NEWID'+newid+'-levels .level')
        M.gradingform_rubriceditor.addhandlers();
        M.gradingform_rubriceditor.disablealleditors()
        M.gradingform_rubriceditor.assignclasses(elements_str)
        M.gradingform_rubriceditor.editmode(Y.one('#rubric-'+name+' #'+name+'-criteria-NEWID'+newid+'-description'),true)
    } else if (chunks.length == 5 && action == 'addlevel') {
        // ADD NEW LEVEL
        var newscore = 0;
        parent = Y.one('#'+name+'-criteria-'+chunks[2]+'-levels')
        parent.all('.level').each(function (node) { newscore = Math.max(newscore, parseFloat(node.one('.score input[type=text]').get('value'))+1) })
        var newlevel = M.gradingform_rubriceditor.templates[name]['level'].
            replace(/\{CRITERION-id\}/g, chunks[2]).replace(/\{LEVEL-id\}/g, 'NEWID'+newlevid).replace(/\{LEVEL-score\}/g, newscore).replace(/\{.+?\}/g, '')
        parent.append(newlevel)
        M.gradingform_rubriceditor.addhandlers();
        M.gradingform_rubriceditor.disablealleditors()
        M.gradingform_rubriceditor.assignclasses(elements_str)
        M.gradingform_rubriceditor.editmode(parent.all('.level').item(parent.all('.level').size()-1), true)
    } else if (chunks.length == 4 && action == 'moveup') {
        // MOVE CRITERION UP
        el = Y.one('#'+name+'-criteria-'+chunks[2])
        if (el.previous()) el.get('parentNode').insertBefore(el, el.previous())
        M.gradingform_rubriceditor.assignclasses(elements_str)
    } else if (chunks.length == 4 && action == 'movedown') {
        // MOVE CRITERION DOWN
        el = Y.one('#'+name+'-criteria-'+chunks[2])
        if (el.next()) el.get('parentNode').insertBefore(el.next(), el)
        M.gradingform_rubriceditor.assignclasses(elements_str)
    } else if (chunks.length == 4 && action == 'delete') {
        // DELETE CRITERION
        if (confirmed) {
            Y.one('#'+name+'-criteria-'+chunks[2]).remove()
            M.gradingform_rubriceditor.assignclasses(elements_str)
        } else {
            dialog_options['message'] = M.str.gradingform_rubric.confirmdeletecriterion
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else if (chunks.length == 6 && action == 'delete') {
        // DELETE LEVEL
        if (confirmed) {
            Y.one('#'+name+'-criteria-'+chunks[2]+'-'+chunks[3]+'-'+chunks[4]).remove()
            M.gradingform_rubriceditor.assignclasses(elements_str)
        } else {
            dialog_options['message'] = M.str.gradingform_rubric.confirmdeletelevel
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else {
        // unknown action
        return;
    }
    e.preventDefault();
}

// properly set classes (first/last/odd/even), level width and/or criterion sortorder for elements Y.all(elements_str)
M.gradingform_rubriceditor.assignclasses = function (elements_str) {
    var elements = M.gradingform_rubriceditor.Y.all(elements_str)
    for (var i=0;i<elements.size();i++) {
        elements.item(i).removeClass('first').removeClass('last').removeClass('even').removeClass('odd').
            addClass(((i%2)?'odd':'even') + ((i==0)?' first':'') + ((i==elements.size()-1)?' last':''))
        elements.item(i).all('input[type=hidden]').each(
            function(node) {if (node.get('name').match(/sortorder/)) node.set('value', i)}
        );
        if (elements.item(i).hasClass('level')) elements.item(i).set('width', Math.round(100/elements.size())+'%')
    }
}

// returns unique id for the next added element, it should not be equal to any of Y.all(elements_str) ids
M.gradingform_rubriceditor.calculatenewid = function (elements_str) {
    var newid = 1
    M.gradingform_rubriceditor.Y.all(elements_str).each( function(node) {
        var idchunks = node.get('id').split('-'), id = idchunks.pop();
        if (id.match(/^NEWID(\d+)$/)) newid = Math.max(newid, parseInt(id.substring(5))+1);
    } );
    return newid
}