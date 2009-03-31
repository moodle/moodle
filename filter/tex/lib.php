<?php  //$Id$

function tex_sanitize_formula($texexp) {
    /// Check $texexp against blacklist (whitelisting could be more complete but also harder to maintain)
    $tex_blacklist = array(
        'include','def','command','loop','repeat','open','toks','output',
        'input','catcode','name','^^',
        '\every','\errhelp','\errorstopmode','\scrollmode','\nonstopmode',
        '\batchmode','\read','\write','csname','\newhelp','\uppercase',
        '\lowercase','\relax','\aftergroup',
        '\afterassignment','\expandafter','\noexpand','\special'
    );

    return  str_ireplace($tex_blacklist, 'forbiddenkeyword', $texexp);
}

/**
 * Purge all caches when settings changed.
 */
function filter_tex_updatedcallback($name) {
    global $CFG;

    if (file_exists("$CFG->dataroot/filter/tex")) {
        remove_dir("$CFG->dataroot/filter/tex");
    }
    if (file_exists("$CFG->dataroot/filter/algebra")) {
        remove_dir("$CFG->dataroot/filter/algebra");
    }
    if (file_exists("$CFG->dataroot/temp/latex")) {
        remove_dir("$CFG->dataroot/temp/latex");
    }

    delete_records('cache_filters', 'filter', 'tex');
    delete_records('cache_filters', 'filter', 'algebra');
}

?>