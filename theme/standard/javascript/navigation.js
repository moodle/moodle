
function customise_dock_for_theme() {
    if (!M.core_dock) {
        return false;
    }
    // Throw a lightbox for the navigation boxes
    M.core_dock.cfg.panel.modal = true;
    return true;
}