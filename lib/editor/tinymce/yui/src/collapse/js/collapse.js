var COLLAPSE = function() {
    COLLAPSE.superclass.constructor.apply(this, arguments);
};

Y.extend(COLLAPSE, Y.Base, {
    // A location to store the node toggling template so that we do not have to create it each time.
    toggleNodeTemplate : null,

    /**
      * Set up basic values for static access.
      */
    init : function() {
        this.initialise_toggles(10);
    },

    /**
     * Has TinyMCE been loaded and the editors been initialised?
     * Designed mainly for IE
     * @return bool
     */
    editors_initialised : function() {
        return typeof tinyMCE !== 'undefined';
    },

    initialise_toggles : function(refreshes) {
        var editors_initialised = this.editors_initialised(), self = this, editor;
        if (!editors_initialised && refreshes) {
            setTimeout(function() {
                    self.initialise_toggles(refreshes - 1);
                }, 100);
            return;
        }

        // Create the toggle template for use later
        this.toggleNodeTemplate = Y.Node.create('<a class="toggle_editor_toolbar" />');
        this.toggleNodeTemplate.setContent(M.util.get_string('showeditortoolbar', 'form'));

        // Delegate clicks of the toggle_editor_toolbar
        Y.one('body').delegate('click', this.toggle_collapse_from_event, 'a.toggle_editor_toolbar', this);

        // Set up editors which have already been created
        for (editor in tinyMCE.editors) {
            this.setup_collapse(tinyMCE.editors[editor]);
        }

        // Set up for future editors.
        // I haven't yet found a way of directly delegating the editor.onInit event. Instead we have to listen for the
        // tinyMCE.onAddEditor event, and then add a further event listener to the editor's onInit event.
        // onAddEditor is triggered before the editor has been created.
        // We use Y.Bind to ensure that context is maintained.
        tinyMCE.onAddEditor.add(Y.bind(this.add_setup_collapse_listener, this));

    },

    /**
      * Setup a listener for a new editor which will actually set the editor up
      * @param {Manager} mgr
      * @param {Editor} ed
      */
    add_setup_collapse_listener : function (mgr, ed) {
        // Bind the editor.onInit function to set this editor up. This ensures we maintain our context (this)
        ed.onInit.add(Y.bind(this.setup_collapse, this));
    },

    /**
      * Setup the toggle system for the provided editor
      *
      * @param {Editor} ed The TinyMCE editor instance
      */
    setup_collapse : function(ed) {
        var textarea = Y.Node(ed.getElement()),
            editortable = Y.Node(ed.getContainer()).one('> table'),
            thisToggleNode;

        // Does this text area support collapsing at all?
        if (!textarea.hasClass('collapsible')) {
            return;
        }

        // Did we find an appropriate table to work with
        if (!editortable) {
            return;
        }

        // Add toggle button.
        thisToggleNode = this.toggleNodeTemplate.cloneNode(true);
        editortable.get('parentNode').insert(thisToggleNode, editortable);

        // Toggle the toolbars initially.
        if (Y.Node(ed.getElement()).hasClass('collapsed')) {
            this.toggle_collapse(thisToggleNode, editortable, 0);
        } else {
            this.toggle_collapse(thisToggleNode, editortable, 1);
        }

        // When TinyMCE initialises itself, it adds a height to the table.
        // Unfortuately, the height it sets is too big for when the editor is collpsed.
        // Fortunately, the hight is not necessary, so we can just remove it.
        // (If you re-size the editor then it will remove this style attribute itself.)
        editortable.setStyle('height', '');
        editortable.setStyle('width', '');
    },

    /**
      * Toggle the specified editor toolbars.
      *
      * @param {Node} button The toggle button which we have to change the text for
      * @param {Node} editortable The table which the tinyMCE editor is in
      * @param {Boolean} newstate The intended toggle state
      */
    toggle_collapse : function(button, editortable, newstate) {
        var toolbar = editortable.one('td.mceToolbar').ancestor('tr'),
            statusbar = editortable.one('.mceStatusbar').ancestor('tr');

        // Check whether we have a state already.
        if (typeof newstate === 'undefined') {
            if (toolbar.getStyle('display') === 'none') {
                newstate = 1;
            } else {
                newstate = 0;
            }
        }

        // Toggle the various states and update the button text to suit
        if (newstate === 0) {
            toolbar.hide();
            statusbar.hide();
            button.setContent(M.util.get_string('showeditortoolbar', 'form'));
        } else {
            toolbar.show();
            statusbar.show();
            button.setContent(M.util.get_string('hideeditortoolbar', 'form'));
        }
    },

    toggle_collapse_from_event : function(thisevent) {
        var button = thisevent.target.ancestor('a', true),
            editortable = thisevent.target.ancestor('span', true).one('table.mceLayout');
        this.toggle_collapse(button, editortable);
    }
});

M.editor_collapse = M.editor_collapse || {};
M.editor_collapse.init = function(params) {
    return new COLLAPSE(params);
};
