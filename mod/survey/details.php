<?PHP // $Id$

    require("../../config.php");

    if ($form = data_submitted($destination)) { 

        if (! $course = get_record("course", "id", $form->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You can't modify this course!");
        }

        $streditingasurvey = get_string("editingasurvey", "survey");

        print_header("$course->shortname: $streditingasurvey", "$course->fullname",
                      "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> 
                       -> $streditingasurvey");

        if (!$form->name or !$form->template) {
            error(get_string("filloutallfields"), $_SERVER["HTTP_REFERER"]);
        }

        print_simple_box_start("center", "", "$THEME->cellheading");
        ?>
        <FORM NAME=form METHOD=post ACTION="<? p($form->destination)?>">
        <TABLE CELLPADDING=5 ALIGN=CENTER>
        <TR><TD ALIGN=right NOWRAP><P><B><? print_string("name") ?>:</B></P></TD>
            <TD><P><? p($form->name) ?></P></A></TD></TR>

        <TR VALIGN=top>
            <TD ALIGN=right NOWRAP>
                <P><B><? print_string("introtext", "survey") ?>:</B></P><BR>
                <font SIZE="1">
                <? helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br \>
                <? helpbutton("text", get_string("helptext"), "moodle", true, true) ?><br \>
                </font>
            </TD>
            <TD>
                <TEXTAREA NAME="intro" ROWS=20 COLS=50 WRAP="virtual"><? 
                if ($form->intro) {
                    p($form->intro);
                } else {
                    $form->intro = get_field("survey", "intro", "id", $form->template);
                    $form->intro = get_string($form->intro, "survey");
                    p($form->intro);
                }
                ?></TEXTAREA>
            </TD>
        </TR>
        </TABLE>
        <input type="hidden" name=name       value="<? p($form->name) ?>">
        <input type="hidden" name=template   value="<? p($form->template) ?>">

        <input type="hidden" name=course     value="<? p($form->course) ?>">
        <input type="hidden" name=coursemodule     value="<? p($form->coursemodule) ?>">
        <input type="hidden" name=section       value="<? p($form->section) ?>">
        <input type="hidden" name=module     value="<? p($form->module) ?>">
        <input type="hidden" name=modulename value="<? p($form->modulename) ?>">
        <input type="hidden" name=instance   value="<? p($form->instance) ?>">
        <input type="hidden" name=mode       value="<? p($form->mode) ?>">
        <CENTER>
        <input type="submit" value="<? print_string("savechanges") ?>">
        </CENTER>
        </FORM>
        <?
        print_simple_box_end();
        print_footer($course);

     } else {
        error("You can't use this page like that!");
     }

?>
