                </td>
            </tr>
        </table>
    <?php if (!empty($THEME->customcorners)) print_custom_corners_end(); ?>
    </td>
<?php
print '<!-- End page content -->'."\n";

// The right column
if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $editing) {
    echo '<td style="vertical-align: top; width: '. $preferred_width_right .'px;" id="right-column">';
    echo '<!-- Begin right side blocks -->'."\n";
    if (!empty($THEME->customcorners)) print_custom_corners_start();
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
    print_spacer(1, 120, true);
    if (!empty($THEME->customcorners)) print_custom_corners_end();
    echo '<!-- End right side blocks -->'."\n";
    echo '</td>';
}
?>

    </tr>
</table>

<?php

print_footer($course);
?>
