<?php
/**
 * This file defines a simple editor
 *
 * @author Jordi Piguillem
 * @author Josep Arus
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_wiki
 *
 */

/**
 * @TODO: Doc this function
 */
function wiki_print_editor_html($pageid, $content, $version = -1, $section = null, $upload = false, $deleteuploads = array()) {
    global $CFG, $OUTPUT;

    $OUTPUT->heading(strtoupper(get_string('formathtml', 'wiki')), 3);

    $action = $CFG->wwwroot.'/mod/wiki/edit.php?pageid='.$pageid;

    if (!empty($section)) {
        $action .= "&section=".urlencode($section);
    }

    echo $OUTPUT->container_start('container');
    echo '<form method="post" action="'.$action.'">';
    $textarea = print_textarea(true, 20, 100, 0, 0, "newcontent", $content, 0, true, '', 'form-textarea-advanced');
    echo $OUTPUT->container($textarea, 'wiki_editor');
    wiki_print_edit_form_default_fields('html', $pageid, $version, $upload, $deleteuploads);
    echo '</form>';
    echo $OUTPUT->container_end();
}
