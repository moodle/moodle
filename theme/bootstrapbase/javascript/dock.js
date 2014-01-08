/**
 * Customise the dock for this theme.
 */
function customise_dock_for_theme() {
    // Add the "block" class to docked blocks.
    // This prevents having to restyle all docked blocks and simply use standard block styling.
    M.core_dock.on('dock:panelgenerated', function(){
        Y.all('.dockeditempanel_content').addClass('block');
    });
}