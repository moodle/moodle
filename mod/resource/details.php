<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

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
            error(get_string("filloutallfields"), $_SERVER["HTTP_REFERER"]);
        }

        $form->alltext = "";
        if ($form->instance) {
            $form->alltext = get_field("resource", "alltext", "id", "$form->instance");
        }

        print_simple_box_start("center", "", "$THEME->cellheading");

        if ($usehtmleditor and $form->type == HTML) {
            $onsubmit = "onsubmit=\"copyrichtext(theform.alltext);\"";
        } else {
            $onsubmit = "";
        }
        echo "<form name=theform method=post $onsubmit action=\"$form->destination\">";
        echo "<table cellpadding=5 align=center>";
        echo "<tr><td align=right nowrap><p><b>$strname:</b></p></td><td><p>$form->name</p></a></td></tr>";

        $strtypename = $RESOURCE_TYPE["$form->type"];
        $strexample  = get_string("example", "resource");

        switch ($form->type) {
            case REFERENCE: 
                $strexamplereference = get_string("examplereference", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?=$strtypename?>:</b></p>
                    </td>
                    <td>
                        <textarea name="reference" rows=3 cols=50 wrap="virtual"><? p($form->reference) ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b>(<?=$strexample?>)</b></p>
                    </td>
                    <td>
                    <p><?=$strexamplereference?></p>
                    </td>
                </TR>

                <?
                break;

            case WEBPAGE:
                $strexampleurl = get_string("exampleurl", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?=$strtypename?>:</b></p>
                    </td>
                    <td>
                        <input name="reference" size="100" value="<? p($form->reference) ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>&nbsp;
                    </td>
                    <td>
                        <p><?php echo "($strexample) $strexampleurl" ?></p>
                    </td>
                </tr>

                <?
                break;

            case WEBLINK:

                $strexampleurl    = get_string("exampleurl", "resource");
                $strnewwindow     = get_string("newwindow", "resource");
                $strnewwindowopen = get_string("newwindowopen", "resource");

                foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
                    $stringname = "str$optionname";
                    $$stringname = get_string("new$optionname", "resource");
                    $window->$optionname = "";
                    $jsoption[] = "\"$optionname\"";
                }
                $alljsoptions = implode(",", $jsoption);

                if ($form->instance) {     // Re-editing
                    if (!$form->alltext) {
                        $newwindow = "";
                        $window->resizable = "checked";  // Defaults
                        $window->scrollbars = "checked";
                        $window->status = "checked";
                        $window->location = "checked";
                        $window->width = 620;
                        $window->height = 450;
                    } else {
                        $newwindow = "checked";
                        $rawoptions = explode(',', $form->alltext); 
                        foreach ($rawoptions as $rawoption) {
                            $option = explode('=', trim($rawoption));
                            $optionname = $option[0];
                            $optionvalue = $option[1];
                            if ($optionname == "height" or $optionname == "width") {
                                $window->$optionname = $optionvalue;
                            } else if ($optionvalue) {
                                $window->$optionname = "checked";
                            }
                        }
                    }
                } else {
                    $newwindow = "checked";
                    $window->resizable = "checked";
                    $window->scrollbars = "checked";
                    $window->status = "checked";
                    $window->location = "checked";
                    $window->width = 620;
                    $window->height = 450;
                }

                echo $alloptions;

                ?>

                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php p($strtypename) ?>:</b></p>
                    </td>
                    <td>
                        <input name="reference" size="100" value="<?php p($form->reference) ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>&nbsp;
                    </td>
                    <td>
                        <p><?php echo "($strexample) $strexampleurl" ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php p($strnewwindow) ?></b></p>
                    </td>
                    <td>
                      <script>
                          var subitems = [<?php echo $alljsoptions; ?>];
                      </script>
                      <input name="setnewwindow" type=hidden value=1>
                      <input name="newwindow" type=checkbox value=1 <?php p($newwindow) ?> 
                        onclick="return lockoptions('theform','newwindow', subitems)"> 
                      <?php p($strnewwindowopen) ?>
                    <ul>
                      <?php
                          foreach ($window as $name => $value) {
                              if ($name == "height" or $name == "width") {
                                  continue;
                              }
                              echo "<input name=\"h$name\" type=hidden value=0>";
                              echo "<input name=\"$name\" type=checkbox value=1 ".$window->$name.">";
                              $stringname = "str$name";
                              echo $$stringname."<br />";
                          }
                      ?>

                      <input name="hwidth" type=hidden value=0>
                      <input name="width" type=text size=4 value="<?php p($window->width) ?>">
                        <?php p($strwidth) ?><br />

                      <input name="hheight" type=hidden value=0>
                      <input name="height" type=text size=4 value="<?php p($window->height) ?>">
                        <?php p($strheight) ?><br />
                      <?php
                        if (!$newwindow) {
                            echo "<script>";
                            echo "lockoptions('theform','newwindow', subitems);";
                            echo "</script>";
                        }
                      ?>
                    </ul>
                    </p>
                    </td>
                </tr>

                <?
                break;

            case PROGRAM:
                $strexampleurl = get_string("exampleurl", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?=$strtypename?>:</b></p>
                    </td>
                    <td>
                        <input name="reference" size="100" value="<? p($form->reference) ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>&nbsp;
                    </td>
                    <td>
                        <p><?php echo "($strexample) $strexampleurl" ?></p>
                    </td>
                </tr>

                <?
                break;

            case UPLOADEDFILE:
                $strfilename = get_string("filename", "resource");
                $strnote     = get_string("note", "resource");
                $strnotefile = get_string("notefile", "resource", "$CFG->wwwroot/files/index.php?id=$course->id");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?=$strfilename?>:</b></p>
                    </td>
                    <td>
                        <?
                          $rootdir = $CFG->dataroot."/".$course->id;
                          $coursedirs = get_directory_list($rootdir, $CFG->moddata);
                          foreach ($coursedirs as $dir) {
                              $options["$dir"] = $dir;
                          }
                          choose_from_menu ($options, "reference", $form->reference);
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?=$strnote?>:</b></p>
                    </td>
                    <td>
                    <p><?=$strnotefile?>
                    </p>
                    </td>
                </tr>

                <?
                break;

            case PLAINTEXT: 
                $strfulltext = get_string("fulltext", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?=$strfulltext?>:</b></p><br />
                        <font size="1">
                        <? helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br />
                        <? helpbutton("text", get_string("helptext"), "moodle", true, true) ?> <br />
                        <? emoticonhelpbutton("theform", "alltext") ?> <br />
                        </font>
                    </td>
                    <td>
                        <textarea name="alltext" rows=20 cols=50 wrap="virtual"><? p($form->alltext) ?></textarea>
                    </td>
                </tr>
                <?
                break;

            case WIKITEXT:
                $strfulltext = get_string("fulltext", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap="true">
                        <p><b><?=$strfulltext?>:</b></p><br \>
                        <font size="1">
                        <? helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br />
                        <? helpbutton("wiki", get_string("helpwiki"), "moodle", true, true) ?> <br />
                        </font>
                    </td>
                    <td>
                        <textarea name="alltext" rows="20" cols="50" wrap="virtual"><? p($form->alltext) ?></textarea>
                    </td>
                </tr>
                <?
                break;

            case HTML:
                $strhtmlfragment = get_string("htmlfragment", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?=$strhtmlfragment?>:</b></p><br />
                        <font size="1">
                        <? helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br />
                        <? if ($usehtmleditor) {
                              helpbutton("richtext", get_string("helprichtext"), "moodle", true, true);
                           } else {   
                              helpbutton("html", get_string("helphtml"), "moodle", true, true);
                           } ?> <br />
                        </font>
                    </td>
                    <td>
                        <? print_textarea($usehtmleditor, 20, 50, 680, 400, "alltext", $form->alltext); ?>
                    </td>
                </tr>
                <?
                break;

            default:
                error(get_string("notypechosen", "resource"), $_SERVER["HTTP_REFERER"]);
                break;
        }

        ?>
        </table>
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
        <center>
        <input type="submit" value="<? print_string("savechanges") ?>">
        <input type="submit" name=cancel value="<? print_string("cancel") ?>">
        </center>
        </form>
<?
        if ($usehtmleditor and $form->type == HTML) {
            print_richedit_javascript("theform", "alltext", "yes");
        }
        print_simple_box_end();
        print_footer($course);

    } else {
        error("This script was called incorrectly");
    }
?>
