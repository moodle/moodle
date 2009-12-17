<?php

/**
 * This is a slight variatoin on the standard_renderer_factory that uses
 * custom_corners_core_renderer instead of moodle_core_renderer.
 *
 * This generates the slightly different HTML that the custom_corners theme expects.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated Required to make the old $THEME->customcorners setting work.
 */
class custom_corners_renderer_factory extends standard_renderer_factory {
    public function __construct($theme) {
        parent::__construct($theme);
        global $CFG;
        require_once($CFG->themedir . '/custom_corners/renderers.php');
    }

    /* Implement the subclass method. */
    public function get_renderer(moodle_page $page, $module, $subtype=null) {
        if ($module == 'core') {
            return new custom_corners_core_renderer($page);
        }
        return parent::get_renderer($page, $module, $subtype);
    }
}
