// Define a function that will run in the context of a
// Node instance:
var CSS = {
        LINK: 'mod-assign-history-link',
        OPEN: 'mod-assign-history-link-open',
        CLOSED: 'mod-assign-history-link-closed',
        PANEL: 'mod-assign-history-panel'
    },
    COUNT = 0,
    TOGGLE = function() {
        var id = this.get('for'),
            panel = Y.one('#' + id);
        if (this.hasClass(CSS.OPEN)) {
            this.removeClass(CSS.OPEN);
            this.addClass(CSS.CLOSED);
            this.setStyle('overflow', 'hidden');
            panel.hide();
        } else {
            this.removeClass(CSS.CLOSED);
            this.addClass(CSS.OPEN);
            panel.show();
        }
    },
    HISTORY = function() {
        var link = null,
            panel = null,
            wrapper = null,
            container = this;

        // Loop through all the children of this container and turn
        // every odd node to a link to open/close the following panel.
        this.get('children').each(function() {
            if (link) {
                COUNT++;
                // First convert the link to an anchor.
                wrapper = Y.Node.create('<a/>');
                panel = this;
                container.insertBefore(wrapper, link);
                link.remove(false);
                wrapper.appendChild(link);

                // Add a for attribute to the link to link to the id of the panel.
                if (!panel.get('id')) {
                    panel.set('id', CSS.PANEL + COUNT);
                }
                wrapper.set('for', panel.get('id'));
                // Add an aria attribute for the live region.
                panel.set('aria-live', 'polite');

                wrapper.addClass(CSS.LINK);
                if (COUNT == 1) {
                    wrapper.addClass(CSS.OPEN);
                } else {
                    wrapper.addClass(CSS.CLOSED);
                }
                panel.addClass(CSS.PANEL);
                if (COUNT == 1) {
                    panel.show();
                } else {
                    panel.hide();
                }
                link = null;
            } else {
                link = this;
            }
        });

        // Setup event listeners.
        this.delegate('click', TOGGLE, '.' + CSS.LINK);
    };

// Use addMethod to add history to the Node prototype:
Y.Node.addMethod("history", HISTORY);

// Extend this functionality to NodeLists.
Y.NodeList.importMethod(Y.Node.prototype, "history");
