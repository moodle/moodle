<?PHP // $Id$

    require("../../config.php");

    if (match_referer("$destination") && isset($HTTP_POST_VARS)) {    // form submitted
        $form = (object)$HTTP_POST_VARS;

        if (! $course = get_record("course", "id", $form->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You can't modify this course!");
        }

        print_header("$course->shortname: Editing a survey", "$course->shortname: Editing a survey",
                      "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> 
                      Editing a survey", "");

        print_simple_box_start("center", "", "$THEME->cellheading");
        ?>
        <FORM NAME=form METHOD=post ACTION="<? p($form->destination)?>">
        <TABLE CELLPADDING=5 ALIGN=CENTER>
        <TR><TD ALIGN=right NOWRAP><P><B>Name:</B></P></TD>
            <TD><P><? p($form->name) ?></P></A></TD></TR>

        <TR VALIGN=top>
            <TD ALIGN=right NOWRAP>
                <P><B>Introduction Text:</B></P>
            </TD>
            <TD>
                <TEXTAREA NAME="intro" ROWS=20 COLS=50 WRAP="virtual"><? 
                if ($form->intro) {
                    p($form->intro);
                } else {
                    p(get_field("survey", "intro", "id", $form->template));
                }
                ?></TEXTAREA>
            </TD>
        </TR>
        </TABLE>
        <input type="hidden" name=name       value="<? p($form->name) ?>">
        <input type="hidden" name=template   value="<? p($form->template) ?>">

        <input type="hidden" name=course     value="<? p($form->course) ?>">
        <input type="hidden" name=week       value="<? p($form->week) ?>">
        <input type="hidden" name=module     value="<? p($form->module) ?>">
        <input type="hidden" name=modulename value="<? p($form->modulename) ?>">
        <input type="hidden" name=instance   value="<? p($form->instance) ?>">
        <input type="hidden" name=mode       value="<? p($form->mode) ?>">
        <CENTER>
        <input type="submit" value="Save these settings">
        </CENTER>
        </FORM>
        <?
        print_simple_box_end();
        print_footer($course);

     }

?>
