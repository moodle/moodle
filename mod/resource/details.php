<?PHP // $Id$

    require("../../config.php");
    require("lib.php");

    require_login();

    $usehtmleditor = can_use_richtext_editor();

    if ($form = data_submitted($destination)) { 

        if (! $course = get_record("course", "id", $form->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You can't modify this course!");
        }

        $strediting = get_string("editingaresource", "resource");
        $strname = get_string("name");

        print_header("$course->shortname: $strediting", "$course->shortname: $strediting",
                      "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> $strediting");

        if (!$form->name or !$form->type or !$form->summary) {
            error(get_string("filloutallfields"), $HTTP_REFERER);
        }

        print_simple_box_start("center", "", "$THEME->cellheading");

        if ($usehtmleditor and $form->type == HTML) {
            $onsubmit = "onsubmit=\"copyrichtext(theform.alltext);\"";
        } else {
            $onsubmit = "";
        }
        echo "<FORM NAME=theform METHOD=post $onsubmit ACTION=\"$form->destination\">";
        echo "<TABLE CELLPADDING=5 ALIGN=CENTER>";
        echo "<TR><TD ALIGN=right NOWRAP><P><B>$strname:</B></P></TD><TD><P>$form->name</P></A></TD></TR>";

        $strtypename = $RESOURCE_TYPE["$form->type"];
        $strexample  = get_string("example", "resource");

        switch ($form->type) {
            case REFERENCE: 
                $strexamplereference = get_string("examplereference", "resource");
                ?>
                <TR VALIGN=top>
                    <TD ALIGN=right NOWRAP>
                        <P><B><?=$strtypename?>:</B></P>
                    </TD>
                    <TD>
                        <TEXTAREA NAME="reference" ROWS=3 COLS=50 WRAP="virtual"><? p($form->reference) ?></TEXTAREA>
                    </TD>
                </TR>
                <TR VALIGN=top>
                    <TD ALIGN=right NOWRAP>
                        <P><B>(<?=$strexample?>)</B></P>
                    </TD>
                    <TD>
                    <P><?=$strexamplereference?></P>
                    </TD>
                </TR>

                <?
                break;

            case WEBPAGE:
            case WEBLINK:
            case PROGRAM:
                $strexampleurl = get_string("exampleurl", "resource");
                ?>
                <TR VALIGN=top>
                    <TD ALIGN=right NOWRAP>
                        <P><B><?=$strtypename?>:</B></P>
                    </TD>
                    <TD>
                        <INPUT NAME="reference" SIZE=\"100\" VALUE="<? p($form->reference) ?>">
                    </TD>
                </TR>
                <TR VALIGN=top>
                    <TD ALIGN=right NOWRAP>
                        <P><B>(<?=$strexample?>)</B></P>
                    </TD>
                    <TD>
                    <P><?=$strexampleurl?>
                    </P>
                    </TD>
                </TR>

                <?
                break;

            case UPLOADEDFILE:
                $strfilename = get_string("filename", "resource");
                $strnote     = get_string("note", "resource");
                $strnotefile = get_string("notefile", "resource", "$CFG->wwwroot/files/index.php?id=$course->id");
                ?>
                <TR VALIGN=top>
                    <TD ALIGN=right NOWRAP>
                        <P><B><?=$strfilename?>:</B></P>
                    </TD>
                    <TD>
                        <?
                          $rootdir = $CFG->dataroot."/".$course->id;
                          $coursedirs = get_directory_list($rootdir, $CFG->moddata);
                          foreach ($coursedirs as $dir) {
                              $options["$dir"] = $dir;
                          }
                          choose_from_menu ($options, "reference", $form->reference);
                        ?>
                    </TD>
                </TR>
                <TR VALIGN=top>
                    <TD ALIGN=right NOWRAP>
                        <P><B><?=$strnote?>:</B></P>
                    </TD>
                    <TD>
                    <P><?=$strnotefile?>
                    </P>
                    </TD>
                </TR>

                <?
                break;

            case PLAINTEXT: 
                $strfulltext = get_string("fulltext", "resource");
                ?>
                <TR VALIGN=top>
                    <TD ALIGN=right NOWRAP>
                        <P><B><?=$strfulltext?>:</B></P><br \>
                        <font SIZE="1">
                        <? helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br \>
                        <? helpbutton("text", get_string("helptext"), "moodle", true, true) ?> <br \>
                        </font>
                    </TD>
                    <TD>
                        <TEXTAREA NAME="alltext" ROWS=20 COLS=50 WRAP="virtual"><? p($form->alltext) ?></TEXTAREA>
                    </TD>
                </TR>
                <?
                break;

            case HTML:
                $strhtmlfragment = get_string("htmlfragment", "resource");
                ?>
                <TR VALIGN=top>
                    <TD ALIGN=right NOWRAP>
                        <P><B><?=$strhtmlfragment?>:</B></P><br \>
                        <font SIZE="1">
                        <? helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br \>
                        <? if ($usehtmleditor) {
                            helpbutton("richtext", get_string("helprichtext"), "moodle", true, true);
                           } else {   
                            helpbutton("html", get_string("helphtml"), "moodle", true, true);
                           } ?><br \>
                        </font>
                    </TD>
                    <TD>
                        <? print_textarea($usehtmleditor, 20, 50, 680, 400, "alltext", $form->alltext); ?>
                    </TD>
                </TR>
                <?
                break;

            default:
                error(get_string("notypechosen", "resource"), $HTTP_REFERER);
                break;
        }

        ?>
        </TABLE>
        <input type="hidden" name=summary    value="<? p($form->summary) ?>">
        <input type="hidden" name=type       value="<? p($form->type) ?>">
        <input type="hidden" name=name       value="<? p($form->name) ?>">

        <input type="hidden" name=course     value="<? p($form->course) ?>">
        <input type="hidden" name=coursemodule     value="<? p($form->coursemodule) ?>">
        <input type="hidden" name=section       value="<? p($form->section) ?>">
        <input type="hidden" name=module     value="<? p($form->module) ?>">
        <input type="hidden" name=modulename value="<? p($form->modulename) ?>">
        <input type="hidden" name=instance   value="<? p($form->instance) ?>">
        <input type="hidden" name=mode       value="<? p($form->mode) ?>">
        <CENTER>
        <input type="submit" value="<? print_string("savechanges") ?>">
        <input type="submit" name=cancel value="<? print_string("cancel") ?>">
        </CENTER>
        </FORM>
<?
        if ($usehtmleditor) {
            print_richedit_javascript("theform", "alltext", "yes");
        }
        print_simple_box_end();
        print_footer($course);

    } else {
        error("This script was called incorrectly");
    }
?>
