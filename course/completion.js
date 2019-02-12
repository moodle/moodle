
M.core_completion = {};

M.core_completion.init = function(Y) {
    // Check the reload-forcing
    var changeDetector = Y.one('#completion_dynamic_change');
    if (changeDetector.get('value') > 0) {
        changeDetector.set('value', 0);
        window.location.reload();
        return;
    }

    var handle_success = function(id, o, args) {
        Y.one('#completion_dynamic_change').set('value', 1);

        if (o.responseText != 'OK') {
            alert('An error occurred when attempting to save your tick mark.\n\n('+o.responseText+'.)'); //TODO: localize

        } else {
            var current = args.state.get('value');
            var modulename = args.modulename.get('value'),
                altstr,
                iconkey,
                button = args.image.get('parentNode');

            if (current == 1) {
                altstr = M.util.get_string('completion-alt-manual-y', 'completion', modulename);
                iconkey = 'i/completion-manual-y';
                args.state.set('value', 0);
            } else {
                altstr = M.util.get_string('completion-alt-manual-n', 'completion', modulename);
                iconkey = 'i/completion-manual-n';
                args.state.set('value', 1);
            }

            require(['core/templates', 'core/notification'], function(Templates, Notification) {
                Templates.renderPix(iconkey, 'core', altstr).then(function(html) {
                    var id = button.get('id'),
                        postFocus = '$(document.getElementById("' + id + '")).focus();';

                    Templates.replaceNode(args.image.getDOMNode(), html, postFocus);
                }).catch(Notification.exception);
            });
        }

        args.ajax.remove();
    };

    var handle_failure = function(id, o, args) {
        alert('An error occurred when attempting to save your tick mark.\n\n('+o.responseText+'.)'); //TODO: localize
        args.ajax.remove();
    };

    var toggle = function(e) {
        e.preventDefault();

        var form = e.target;
        var cmid = 0;
        var completionstate = 0;
        var state = null;
        var image = null;
        var modulename = null;

        var inputs = Y.Node.getDOMNode(form).getElementsByTagName('input');
        for (var i=0; i<inputs.length; i++) {
            switch (inputs[i].name) {
                 case 'id':
                     cmid = inputs[i].value;
                     break;
                  case 'completionstate':
                     completionstate = inputs[i].value;
                     state = Y.one(inputs[i]);
                     break;
                  case 'modulename':
                     modulename = Y.one(inputs[i]);
                     break;
            }
        }
        image = form.one('button .icon');

        // start spinning the ajax indicator
        var ajax = Y.Node.create('<div class="ajaxworking" />');
        form.append(ajax);

        var cfg = {
            method: "POST",
            data: 'id='+cmid+'&completionstate='+completionstate+'&fromajax=1&sesskey='+M.cfg.sesskey,
            on: {
                success: handle_success,
                failure: handle_failure
            },
            arguments: {state: state, image: image, ajax: ajax, modulename: modulename}
        };

        Y.use('io-base', function(Y) {
            Y.io(M.cfg.wwwroot+'/course/togglecompletion.php', cfg);
        });
    };

    // register submit handlers on manual tick completion forms
    Y.all('form.togglecompletion').each(function(form) {
        if (!form.hasClass('preventjs')) {
            Y.on('submit', toggle, form);
        }
    });

    // hide the help if there are no completion toggles or icons
    var help = Y.one('#completionprogressid');
    if (help && !(Y.one('form.togglecompletion') || Y.one('.autocompletion'))) {
        help.setStyle('display', 'none');
    }
};


