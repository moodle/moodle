M.gradingform_guideeditor = {'templates' : {}, 'eventhandler' : null, 'name' : null, 'Y' : null};

/**
 * This function is called for each guideeditor on page.
 */
M.gradingform_guideeditor.init = function(Y, options) {
    M.gradingform_guideeditor.name = options.name
    M.gradingform_guideeditor.Y = Y
    M.gradingform_guideeditor.templates[options.name] = {
        'criterion' : options.criteriontemplate,
        'comment' : options.commenttemplate
    }
    M.gradingform_guideeditor.disablealleditors()
    Y.on('click', M.gradingform_guideeditor.clickanywhere, 'body', null)
    YUI().use('event-touch', function (Y) {
        Y.one('body').on('touchstart', M.gradingform_guideeditor.clickanywhere);
        Y.one('body').on('touchend', M.gradingform_guideeditor.clickanywhere);
    })
    M.gradingform_guideeditor.addhandlers()
};

// Adds handlers for clicking submit button. This function must be called each time JS adds new elements to html
M.gradingform_guideeditor.addhandlers = function() {
    var Y = M.gradingform_guideeditor.Y
    var name = M.gradingform_guideeditor.name
    if (M.gradingform_guideeditor.eventhandler) {
        M.gradingform_guideeditor.eventhandler.detach()
    }
    M.gradingform_guideeditor.eventhandler = Y.on('click', M.gradingform_guideeditor.buttonclick, '#guide-'+name+' input[type=submit]', null);
}

// switches all input text elements to non-edit mode
M.gradingform_guideeditor.disablealleditors = function() {
    var Y = M.gradingform_guideeditor.Y
    var name = M.gradingform_guideeditor.name
    Y.all('#guide-'+name+' .criteria .description input[type=text]:not(.pseudotablink)').each( function(node) {M.gradingform_guideeditor.editmode(node, false)} );
    Y.all('#guide-'+name+' .criteria .description textarea').each( function(node) {M.gradingform_guideeditor.editmode(node, false)} );
    Y.all('#guide-'+name+' .comments .description textarea').each( function(node) {M.gradingform_guideeditor.editmode(node, false)} );
}

// function invoked on each click on the page. If criterion values are clicked
// it switches the element to edit mode. If guide button is clicked it does nothing so the 'buttonclick'
// function is invoked
M.gradingform_guideeditor.clickanywhere = function(e) {
    if (e.type == 'touchstart') {
        return
    }
    var el = e.target
    // if clicked on button - disablecurrenteditor, continue
    if (el.get('tagName') == 'INPUT' && el.get('type') == 'submit') {
        return
    }
    // if clicked on description item and this item is not enabled - enable it
    var container = null
    if ((container = el.ancestor('.criterionname')) || (container = el.ancestor('.criterionmaxscore'))) {
        el = container.one('input[type=text]')
    } else if ((container = el.ancestor('.criteriondesc')) || (container = el.ancestor('.criteriondescmarkers'))) {
        el = container.one('textarea')
    } else {
        el = null
    }
    if (el) {
        if (el.hasClass('hiddenelement')) {
            M.gradingform_guideeditor.disablealleditors()
            M.gradingform_guideeditor.editmode(el, true)
        }
        return
    }
    // else disablecurrenteditor
    M.gradingform_guideeditor.disablealleditors()
}

// switch the criterion item to edit mode or switch back
M.gradingform_guideeditor.editmode = function(el, editmode) {
    var Y = M.gradingform_guideeditor.Y
    var ta = el
    if (!editmode && ta.hasClass('hiddenelement')) {
        return;
    }
    if (editmode && !ta.hasClass('hiddenelement')) {
        return;
    }
    var pseudotablink = '<span class="pseudotablink" tabindex="0"></span>',
        taplain = ta.next('.plainvalue'),
        tbplain = null,
        tb = el.one('.score input[type=text]')
    // add 'plainvalue' next to textarea for description/definition and next to input text field for score (if applicable)
    if (!taplain && ta.get('name') != '') {
        ta.insert('<div class="plainvalue">'+pseudotablink+'<span class="textvalue">&nbsp;</span></div>', 'after')
        taplain = ta.next('.plainvalue')
        taplain.one('.pseudotablink').on('focus', M.gradingform_guideeditor.clickanywhere)
        if (tb) {
            tb.get('parentNode').append('<span class="plainvalue">'+pseudotablink+'<span class="textvalue">&nbsp;</span></span>')
            tbplain = tb.get('parentNode').one('.plainvalue')
            tbplain.one('.pseudotablink').on('focus', M.gradingform_guideeditor.clickanywhere)
        }
    }
    if (tb && !tbplain) {
        tbplain = tb.get('parentNode').one('.plainvalue')
    }
    if (!editmode) {
        // if we need to hide the input fields, copy their contents to plainvalue(s). If description/definition
        // is empty, display the default text ('Click to edit ...') and add/remove 'empty' CSS class to element
        var value = Y.Lang.trim(ta.get('value'));
        if (value.length) {
            taplain.removeClass('empty')
        } else if (ta.get('name').indexOf('[shortname]') > 1){
            value = M.util.get_string('clicktoeditname', 'gradingform_guide')
            taplain.addClass('editname')
        } else {
            value = M.util.get_string('clicktoedit', 'gradingform_guide')
            taplain.addClass('empty')
        }
        // Replace newlines with <br> tags, when displaying in the page.
        taplain.one('.textvalue').set('innerHTML', Y.Escape.html(value).replace(/(?:\r\n|\r|\n)/g, '<br>'))
        if (tb) {
            tbplain.one('.textvalue').set('innerHTML', Y.Escape.html(tb.get('value')))
        }
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
            if (ta.get('name').indexOf('[maxscore]') > 1) {
                ta.setStyle('width', '25px');
            } else {
                var width = parseFloat(ta.get('parentNode').getComputedStyle('width'))-10,
                    height = parseFloat(ta.get('parentNode').getComputedStyle('height'))
                ta.setStyle('width', Math.max(width,50)+'px')
                ta.setStyle('height', Math.max(height,30)+'px')
            }
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
    if (editmode) {
        ta.focus()
    }
}

// handler for clicking on submit buttons within guideeditor element. Adds/deletes/rearranges criteria/comments on client side
M.gradingform_guideeditor.buttonclick = function(e, confirmed) {
    var Y = M.gradingform_guideeditor.Y
    var name = M.gradingform_guideeditor.name
    if (e.target.get('type') != 'submit') {
        return;
    }
    M.gradingform_guideeditor.disablealleditors()
    var chunks = e.target.get('id').split('-')
    var section = chunks[1]
    var action = chunks[chunks.length-1]

    if (chunks[0] != name || (section != 'criteria' && section != 'comments')) {
        return;
    }
    // prepare the id of the next inserted criterion
    var elements_str;
    if (section == 'criteria') {
        elements_str = '#guide-'+name+' .criteria .criterion'
    } else if (section == 'comments') {
        elements_str = '#guide-'+name+' .comments .criterion'
    }
    var newid = 0;
    if (action == 'addcriterion' || action == 'addcomment') {
        newid = M.gradingform_guideeditor.calculatenewid(elements_str);
    }
    var dialog_options = {
        'scope' : this,
        'callbackargs' : [e, true],
        'callback' : M.gradingform_guideeditor.buttonclick
    };
    if (chunks.length == 3 && (action == 'addcriterion' || action == 'addcomment')) {
        // ADD NEW CRITERION OR COMMENT
        var parentel = Y.one('#'+name+'-'+section)
        if (parentel.one('>tbody')) {
            parentel = parentel.one('>tbody')
        }
        if (section == 'criteria') {
            var newcriterion = M.gradingform_guideeditor.templates[name]['criterion']
            parentel.append(newcriterion.replace(/\{CRITERION-id\}/g, 'NEWID'+newid).replace(/\{.+?\}/g, ''))
        } else if (section == 'comments') {
            var newcomment = M.gradingform_guideeditor.templates[name]['comment']
            parentel.append(newcomment.replace(/\{COMMENT-id\}/g, 'NEWID'+newid).replace(/\{.+?\}/g, ''))
        }

        M.gradingform_guideeditor.addhandlers();
        M.gradingform_guideeditor.disablealleditors()
        M.gradingform_guideeditor.assignclasses(elements_str)

        // Enable edit mode of the newly added criterion/comment entry.
        var inputTarget = 'shortname';
        if (action == 'addcomment') {
            inputTarget = 'description';
        }
        var inputTargetId = '#guide-' + name + ' #' + name + '-' + section + '-NEWID' + newid + '-' + inputTarget;
        M.gradingform_guideeditor.editmode(Y.one(inputTargetId), true);
    } else if (chunks.length == 4 && action == 'moveup') {
        // MOVE UP
        el = Y.one('#'+name+'-'+section+'-'+chunks[2])
        if (el.previous()) {
            el.get('parentNode').insertBefore(el, el.previous())
        }
        M.gradingform_guideeditor.assignclasses(elements_str)
    } else if (chunks.length == 4 && action == 'movedown') {
        // MOVE DOWN
        el = Y.one('#'+name+'-'+section+'-'+chunks[2])
        if (el.next()) {
            el.get('parentNode').insertBefore(el.next(), el)
        }
        M.gradingform_guideeditor.assignclasses(elements_str)
    } else if (chunks.length == 4 && action == 'delete') {
        // DELETE
        if (confirmed) {
            Y.one('#'+name+'-'+section+'-'+chunks[2]).remove()
            M.gradingform_guideeditor.assignclasses(elements_str)
        } else {
            dialog_options['message'] = M.util.get_string('confirmdeletecriterion', 'gradingform_guide')
            M.util.show_confirm_dialog(e, dialog_options);
        }
    } else {
        // unknown action
        return;
    }
    e.preventDefault();
}

// properly set classes (first/last/odd/even) and/or criterion sortorder for elements Y.all(elements_str)
M.gradingform_guideeditor.assignclasses = function (elements_str) {
    var elements = M.gradingform_guideeditor.Y.all(elements_str)
    for (var i=0; i<elements.size(); i++) {
        elements.item(i).removeClass('first').removeClass('last').removeClass('even').removeClass('odd').
            addClass(((i%2)?'odd':'even') + ((i==0)?' first':'') + ((i==elements.size()-1)?' last':''))
        elements.item(i).all('input[type=hidden]').each(
            function(node) {if (node.get('name').match(/sortorder/)) { node.set('value', i)}}
        );
    }
}

// returns unique id for the next added element, it should not be equal to any of Y.all(elements_str) ids
M.gradingform_guideeditor.calculatenewid = function (elements_str) {
    var newid = 1
    M.gradingform_guideeditor.Y.all(elements_str).each( function(node) {
        var idchunks = node.get('id').split('-'), id = idchunks.pop();
        if (id.match(/^NEWID(\d+)$/)) { newid = Math.max(newid, parseInt(id.substring(5))+1)};
    } );
    return newid
}
