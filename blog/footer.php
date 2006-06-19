                </td>
            </tr>
        </table>
    </td>
<?php 
print '<!-- End page content -->'."\n";

// The right column
if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
    echo '<td style="vertical-align: top; width: '. $preferred_width_right .'px;">';
    echo '<!-- Begin right side blocks -->'."\n";
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
    print_spacer(1, 120, true);
    echo '<!-- End right side blocks -->'."\n";
    echo '</td>';
}
?>

    </tr>
</table>

<?php

// Janne comment: Let's move this in here
// so IE gets more time to load the
// Page.

if (isset($usehtmleditor) && $usehtmleditor) {
    // Janne comment: there are two text fields in form
    // so lets try to replace them both with
    // HTMLArea editors
    use_html_editor();
}

if (isset($course) && ($course->id)) {
    print_footer($course);
} else {
    print_footer();
}
?>
