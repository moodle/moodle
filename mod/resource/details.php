<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_login();

    $usehtmleditor = can_use_html_editor();

    if ($form = data_submitted($destination)) { 

        if (! $course = get_record("course", "id", $form->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id);

        if (!isteacher($course->id)) {
            error("You can't modify this course!");
        }

        $stredit = get_string("edit");
        $strediting = get_string("editingaresource", "resource");
        $strname = get_string("name");
        $strtypename = $RESOURCE_TYPE["$form->type"];
        $strexample  = get_string("example", "resource");
        $strresources = get_string("modulenameplural", "resource");

        $form->name = stripslashes($form->name);   // remove slashes

        print_header("$course->shortname: $strediting", "$course->shortname: $strediting",
                      "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> 
                       <a href=\"index.php?id=$course->id\">$strresources</a> -> $form->name ($stredit)");

        if (!$form->name or !$form->type or !$form->summary) {
            error(get_string("filloutallfields"), $_SERVER["HTTP_REFERER"]);
        }

        $form->alltext = "";
        if ($form->instance) {
            $form->alltext = get_field("resource", "alltext", "id", "$form->instance");
        }

        print_simple_box_start("center", "", "$THEME->cellheading");

        echo "<form name=theform method=post action=\"$form->destination\">";
        echo "<table cellpadding=5 align=center>";
        echo "<tr><td align=right nowrap><p><b>$strname:</b></p></td><td><p>$form->name</p></a></td></tr>";


        switch ($form->type) {
            case REFERENCE: 
                $strexamplereference = get_string("examplereference", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php echo $strtypename?>:</b></p>
                    </td>
                    <td>
                        <textarea name="reference" rows=3 cols=50 wrap="virtual"><?php  p($form->reference) ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b>(<?php echo $strexample?>)</b></p>
                    </td>
                    <td>
                    <p><?php echo $strexamplereference?></p>
                    </td>
                </tr>

                <?php
                break;

            case WEBPAGE:
                $strexampleurl = get_string("exampleurl", "resource");
                $strsearch     = get_string("search");
                if (empty($form->reference)) {
                    $form->reference = $CFG->resource_defaulturl;
                }
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php echo $strtypename?>:</b></p>
                    </td>
                    <td>
                        <input name="reference" size="80" value="<?php  p($form->reference) ?>">
                        <?php 
                          echo "<input type=button name=searchbutton value=\"$strsearch ...\" ".
                                "onClick=\"return window.open('$CFG->resource_websearch', 'websearch', 'menubar=1,location=1,directories=1,toolbar=1,scrollbars,resizable,width=800,height=600');\">\n";
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>&nbsp;
                    </td>
                    <td>
                        <p><?php echo "($strexample) $strexampleurl" ?></p>
                    </td>
                </tr>

                <?php
                break;

            case WEBLINK:

                $strexampleurl    = get_string("exampleurl", "resource");
                $strnewwindow     = get_string("newwindow", "resource");
                $strnewwindowopen = get_string("newwindowopen", "resource");
                $strsearch        = get_string("search");

                if (empty($form->reference)) {
                    $form->reference = $CFG->resource_defaulturl;
                }

                foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
                    $stringname = "str$optionname";
                    $$stringname = get_string("new$optionname", "resource");
                    $window->$optionname = "";
                    $jsoption[] = "\"$optionname\"";
                }
                $alljsoptions = implode(",", $jsoption);

                if ($form->instance) {     // Re-editing
                    if (!$form->alltext) {
                        $newwindow = "";   // Disable the new window
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
                    foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
                        $defaultvalue = "resource_popup$optionname";
                        $window->$optionname = $CFG->$defaultvalue;
                    }
                    $newwindow = $CFG->resource_popup;
                }

                ?>

                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php p($strtypename) ?>:</b></p>
                    </td>
                    <td>
                        <input name="reference" size="80" value="<?php p($form->reference) ?>">
                        <?php 
                          echo "<input type=button name=searchbutton value=\"$strsearch ...\" ".
                                "onClick=\"return window.open('$CFG->resource_websearch', 'websearch', 'menubar=1,location=1,directories=1,toolbar=1,scrollbars,resizable,width=800,height=600');\">\n";
                        ?>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>&nbsp;
                    </td>
                    <td>
                        <p><font size="-1"><?php echo "($strexample) $strexampleurl" ?></font></p>
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

                <?php
                break;

            case UPLOADEDFILE:
                $strfilename = get_string("filename", "resource");
                $strnote     = get_string("note", "resource");
                $strchooseafile = get_string("chooseafile", "resource");
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
                        $newwindow = "";   // Disable the new window
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
                    foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
                        $defaultvalue = "resource_popup$optionname";
                        $window->$optionname = $CFG->$defaultvalue;
                    }
                    $newwindow = $CFG->resource_popup;
                }

                ?>

                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php echo $strfilename?>:</b></p>
                    </td>
                    <td>
                        <?php
                          echo "<input name=\"reference\" size=\"50\" value=\"$form->reference\">&nbsp;";
                          button_to_popup_window ("/mod/resource/coursefiles.php?id=$course->id", 
                                                  "coursefiles", $strchooseafile, 500, 750, $strchooseafile);
                        ?>
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

                <?php
                break;


            case PROGRAM:
                $strexampleurl = get_string("exampleurl", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php echo $strtypename?>:</b></p>
                    </td>
                    <td>
                        <input name="reference" size="100" value="<?php  p($form->reference) ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>&nbsp;
                    </td>
                    <td>
                        <p><?php echo "($strexample) $strexampleurl" ?></p>
                    </td>
                </tr>

                <?php
                break;


            case PLAINTEXT: 
                $strfulltext = get_string("fulltext", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php echo $strfulltext?>:</b></p><br />
                        <font size="1">
                        <?php  helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br />
                        <?php  helpbutton("text", get_string("helptext"), "moodle", true, true) ?> <br />
                        <?php  emoticonhelpbutton("theform", "alltext") ?> <br />
                        </font>
                    </td>
                    <td>
                        <textarea name="alltext" rows=20 cols=50 wrap="virtual"><?php  p($form->alltext) ?></textarea>
                    </td>
                </tr>
                <?php
                break;

            case WIKITEXT:
                $strfulltext = get_string("fulltext", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap="true">
                        <p><b><?php echo $strfulltext?>:</b></p><br />
                        <font size="1">
                        <?php  helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br />
                        <?php  helpbutton("wiki", get_string("helpwiki"), "moodle", true, true) ?> <br />
                        </font>
                    </td>
                    <td>
                        <textarea name="alltext" rows="20" cols="50" wrap="virtual"><?php  p($form->alltext) ?></textarea>
                    </td>
                </tr>
                <?php
                break;

            case HTML:
                $strhtmlfragment = get_string("htmlfragment", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php echo $strhtmlfragment?>:</b></p><br />
                        <font size="1">
                        <?php  helpbutton("writing", get_string("helpwriting"), "moodle", true, true) ?><br />
                        <?php  if ($usehtmleditor) {
                              helpbutton("richtext", get_string("helprichtext"), "moodle", true, true);
                           } else {   
                              helpbutton("html", get_string("helphtml"), "moodle", true, true);
                           } ?> <br />
                        </font>
                    </td>
                    <td>
                        <?php  print_textarea($usehtmleditor, 20, 50, 680, 400, "alltext", $form->alltext); ?>
                    </td>
                </tr>
                <?php
                break;

            case DIRECTORY:
                $rawdirs = get_directory_list("$CFG->dataroot/$course->id", 'moddata', true, true, false);
                $dirs = array();
                foreach ($rawdirs as $rawdir) {
                   $dirs[$rawdir] = $rawdir;
                }
                $strdirectoryinfo = get_string("directoryinfo", "resource");
                $strmaindirectory = get_string("maindirectory", "resource");
                ?>
                <tr valign="top">
                    <td align="right" nowrap>
                        <p><b><?php echo $strtypename?>:</b></p>
                    </td>
                    <td>
                        <?php choose_from_menu($dirs, "reference", $form->reference, $strmaindirectory, '', '') ?>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="right" nowrap>&nbsp;
                    </td>
                    <td>
                        <p><?php echo "$strdirectoryinfo" ?></p>
                    </td>
                </tr>

                <?php
                break;

            default:
                error(get_string("notypechosen", "resource"), $_SERVER["HTTP_REFERER"]);
                break;
        }

        ?>
        </table>
        <input type="hidden" name=summary    value="<?php  p($form->summary) ?>">
        <input type="hidden" name=type       value="<?php  p($form->type) ?>">
        <input type="hidden" name=name       value="<?php  p($form->name) ?>">

        <input type="hidden" name=course     value="<?php  p($form->course) ?>">
        <input type="hidden" name=coursemodule     value="<?php  p($form->coursemodule) ?>">
        <input type="hidden" name=section       value="<?php  p($form->section) ?>">
        <input type="hidden" name=module     value="<?php  p($form->module) ?>">
        <input type="hidden" name=modulename value="<?php  p($form->modulename) ?>">
        <input type="hidden" name=instance   value="<?php  p($form->instance) ?>">
        <input type="hidden" name=mode       value="<?php  p($form->mode) ?>">
        <center>
        <input type="submit" value="<?php  print_string("savechanges") ?>">
        <input type="submit" name=cancel value="<?php  print_string("cancel") ?>">
        </center>
        </form>
<?php
        if ($usehtmleditor and $form->type == HTML) {
            use_html_editor("alltext");
        }
        print_simple_box_end();
        print_footer($course);

    } else {
        error("This script was called incorrectly");
    }
?>
