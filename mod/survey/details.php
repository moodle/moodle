<?PHP // $Id$

    require_once("../../config.php");

    if ($form = data_submitted($destination)) { 

        if (! $course = get_record("course", "id", $form->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You can't modify this course!");
        }

        $streditingasurvey = get_string("editingasurvey", "survey");
        $strsurveys = get_string("modulenameplural", "survey");

        print_header("$course->shortname: $streditingasurvey", "$course->fullname",
                      "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a>".
                      " -> <a href=\"index.php?id=$course->id\">$strsurveys</a>".
                      " -> $form->name ($streditingasurvey)");

        if (!$form->name or !$form->template) {
            error(get_string("filloutallfields"), $_SERVER["HTTP_REFERER"]);
        }

        print_simple_box_start("center", "", "$THEME->cellheading");
        ?>
        <form name=form method=post action="<?php p($form->destination)?>">
        <table cellpadding=5 align=center>
        <tr><td align=right nowrap><p><b><?php print_string("name") ?>:</b></p></td>
            <td><p><?php p($form->name) ?></p></a></td></tr>

        <tr valign=top>
            <td align=right nowrap>
                <p><b><?php print_string("introtext", "survey") ?>:</b></p><br>
                <font size="1">
                <?php helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?> <br />
                <?php helpbutton("text", get_string("helptext"), "moodle", true, true) ?> <br />
                <?php emoticonhelpbutton("form", "intro"); ?> <br />
                </font>
            </td>
            <td>
                <textarea name="intro" rows=20 cols=50 wrap="virtual"><?php 
                if ($form->intro) {
                    p($form->intro);
                } else {
                    $form->intro = get_field("survey", "intro", "id", $form->template);
                    $form->intro = get_string($form->intro, "survey");
                    p($form->intro);
                }
                ?></textarea>
            </td>
        </tr>
        </table>
        <input type="hidden" name=name       value="<?php p($form->name) ?>">
        <input type="hidden" name=template   value="<?php p($form->template) ?>">

        <input type="hidden" name=course     value="<?php p($form->course) ?>">
        <input type="hidden" name=coursemodule     value="<?php p($form->coursemodule) ?>">
        <input type="hidden" name=section       value="<?php p($form->section) ?>">
        <input type="hidden" name=module     value="<?php p($form->module) ?>">
        <input type="hidden" name=modulename value="<?php p($form->modulename) ?>">
        <input type="hidden" name=instance   value="<?php p($form->instance) ?>">
        <input type="hidden" name=mode       value="<?php p($form->mode) ?>">
        <center>
        <input type="submit" value="<?php print_string("savechanges") ?>">
        </center>
        </form>
        <?php
        print_simple_box_end();
        print_footer($course);

     } else {
        error("You can't use this page like that!");
     }

?>
