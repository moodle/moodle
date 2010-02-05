<?php

/**
 * This is a slight variatoin on the standard_renderer_factory that uses
 * custom_corners_core_renderer instead of core_renderer.
 *
 * This generates the slightly different HTML that the custom_corners theme expects.
 *
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function theme_enable_block_region($region) {
    global $PAGE, $OUTPUT;
    return (
        empty($PAGE->layout_options['noblocks']) &&
        array_key_exists($PAGE->pagelayout, $PAGE->theme->layouts) &&
        in_array($region, $PAGE->theme->layouts[$PAGE->pagelayout]['regions']) &&
        $PAGE->blocks->region_has_content($region, $OUTPUT)
    );
}