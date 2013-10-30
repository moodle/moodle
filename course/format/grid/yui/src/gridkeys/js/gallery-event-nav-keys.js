YUI.add('gallery-event-nav-keys', function(Y) {

var keys = {
        enter    : 13,
        esc      : 27,
        backspace: 8,
        tab      : 9,
        pageUp   : 33,
        pageDown : 34,
        left     : 37,
        up       : 38,
        right    : 39,
        down     : 40
    };

Y.Object.each(keys, function (keyCode, name) {
    Y.Event.define({
        type: name,

        on: function (node, sub, notifier, filter) {
            var method = (filter) ? 'delegate' : 'on';

            sub._handle = node[method]('keydown', function (e) {
                if (e.keyCode === keyCode) {
                    notifier.fire(e);
                }
            }, filter);
        },

        delegate: function () {
            this.on.apply(this, arguments);
        },

        detach: function (node, sub) {
            sub._handle.detach();
        },

        detachDelegate: function () {
            this.detach.apply(this, arguments);
        }
    });
});


}, 'gallery-2011.02.02-21-07' ,{requires:['event-synthetic']});
