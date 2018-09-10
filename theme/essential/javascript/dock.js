/**
 * Customise the dock for this theme.
 *
 * Tasks we do within this function:
 *   - Add 'block' as a class to the dock panel so that its items are styled the same as they are when being displayed
 *     in page as blocks.
 *   - Constrain the width of the docked block to the window width using a responsible max-width.
 *   - Handle the opening/closing of the Bootstrap collapsible navbar on small screens.
 */
function customise_dock_for_theme(dock) {
    // Add the "block" class to docked blocks.
    // This prevents having to restyle all docked blocks and simply use standard block styling.
    // First we wait until the panel has been generated.
    dock.on('dock:panelgenerated', function () {
        // Then we wait until the panel it is being shown for the first time.
        dock.get('panel').once('dockpanel:beforeshow', function () {
            // Finally we add the block class.
            Y.all('.dockeditempanel_content').addClass('block');
        });
        dock.get('panel').on('dockpanel:beforeshow', function () {
            var content = Y.all('.dockeditempanel_content');
            // Finally set a responsible max width.
            content.setStyle('maxWidth', content.get('winWidth') - dock.get('dockNode').get('offsetWidth') - 10);
        });
    });

    // Handle the opening/closing of the bootstrap collapsible navbar on small screens.
    // This is a complex little bit of JS because we need to simulate Bootstrap actions in order to measure height changes
    // in the dom and apply them as spacing to the dock.
    dock.on('dock:initialised', function () {
        var navbar = Y.one('header.navbar'),
            navbarbtn = Y.one('header.navbar .btn-navbar'),
            navcollapse = Y.one('header.navbar .nav-collapse'),
            container = Y.one('#dock .dockeditem_container'),
            margintop = null,
            newmargintop = null,
            diff = null;
        if (navbar && navbarbtn && container) {
            margintop = parseInt(container.getStyle('marginTop').replace(/px$/, ''), 10);
            diff = margintop - parseInt(navbar.get('offsetHeight'), 10);
            navbarbtn.ancestor().on('click', function () {
                // We need to fake the collapsible region being active, this JS *ALWAYS* executes before the bootstrap JS.
                navcollapse.toggleClass('active');
                if (!this.hasClass('active')) {
                    newmargintop = (parseInt(navbar.get('offsetHeight'), 10) + diff);
                    container.setStyle('marginTop', newmargintop + 'px');
                } else {
                    container.setStyle('marginTop', margintop + 'px');
                }
                // Undo the simulation.
                navcollapse.toggleClass('active');
                // Tell the dock things have changed so that it automatically resizes things.
                dock.fire('dock:itemschanged');
            }, navbarbtn);
        }
    });
}