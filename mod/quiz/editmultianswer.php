<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    require_once("format.php");
    require_once("format/multianswer/format.php");
    require_once("../../files/mimetypes.php");

    if ($form = data_submitted("nomatch")) {

        // Standard checks
        if (! $category = get_record("quiz_categories", "id", $form->category)) {
            error("This question doesn't belong to a valid category!");
        }
        if (! $course = get_record("course", "id", $category->course)) {
            error("This question category doesn't belong to a valid course!");
        }
        require_login($course->id);
        if (!isteacher($course->id)) {
            error("You can't modify these questions!");
        }

        $question = extractMultiAnswerQuestion($form->questiontext);
        $question->id = $form->id;
        $question->qtype = $form->qtype;
        $question->name = $form->name;
        $question->category = $form->category;

        if (empty($form->image)) {
            $question->image = "";
        } else {
            $question->image = $form->image;
        }

        // Formcheck
        $err = array();
        if (empty($question->name)) {
            $err["name"] = get_string("missingname", "quiz");
        }
        if (empty($question->questiontext)) {
            $err["questiontext"] = get_string("missingquestiontext", "quiz");
        }
        if ($err) { // Formcheck failed
            $category = $form->category;
            notify(get_string("someerrorswerefound"));
            unset($_POST);
            require('question.php');
            exit;

        } else {

            if (!empty($question->id)) { // Question already exists
                if (!update_record("quiz_questions", $question)) {
                    error("Could not update question!");
                }
            } else {         // Question is a new one
                $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
                if (!$question->id = insert_record("quiz_questions", $question)) {
                    error("Could not insert new question!");
                }
            }
    
            // Now to save all the answers and type-specific options
            $result = quiz_save_question_options($question);

            if (!empty($result->error)) {
                error($result->error);
            }

            if (!empty($result->notice)) {
                notice_yesno($result->notice, "question.php?id=$question->id", "edit.php");
                print_footer($course);
                exit;
            }
    
            redirect("edit.php");
        }

    } else if ($question->questiontext and $question->id) {
        $answers = quiz_get_answers($question);

        foreach ($answers as $multianswer) {
            $parsableanswerdef = '{' . $multianswer->norm . ':';
            switch ($multianswer->answertype) {
                case MULTICHOICE:
                    $parsableanswerdef .= 'MULTICHOICE:';
                    break;
                case SHORTANSWER:
                    $parsableanswerdef .= 'SHORTANSWER:';
                    break;
                case NUMERICAL:
                    $parsableanswerdef .= 'NUMERICAL:';
                    break;
                default:
                    error("answertype $multianswer->answertype not recognized");
            }
            $separator= '';
            foreach ($multianswer->subanswers as $subanswer) {
                $parsableanswerdef .= $separator
                        . '%' . round(100*$subanswer->fraction) . '%';
                $parsableanswerdef .= $subanswer->answer;
                if (isset($subanswer->min) && isset($subanswer->max)
                        and $subanswer->min || $subanswer->max) {
                    // Special for numerical answers:
                    $errormargin = $subanswer->answer - $subanswer->min;
                    $parsableanswerdef .= ":$errormargin";
                }
                if ($subanswer->feedback) {
                    $parsableanswerdef .= "#$subanswer->feedback";
                }
                $separator = '~';
            }
            $parsableanswerdef .= '}';
            $question->questiontext = str_replace
                    ("{#$multianswer->positionkey}", $parsableanswerdef,
                     $question->questiontext);
        }
    }


?>

<FORM name="theform" method="post" <?php echo $onsubmit ?> action="editmultianswer.php">

<CENTER>

<TABLE cellpadding=5>

<TR valign=top>

    <TD align=right><P><B><?php  print_string("category", "quiz") ?>:</B></P></TD>

    <TD>

    <?php   choose_from_menu($categories, "category", "$question->category", ""); ?>

    </TD>

</TR>

<TR valign=top>

    <TD align=right><P><B><?php  print_string("questionname", "quiz") ?>:</B></P></TD>

    <TD>

        <INPUT type="text" name="name" size=40 value="<?php  p($question->name) ?>">

        <?php  if (isset($err["name"])) formerr($err["name"]); ?>

    </TD>

</TR>

<TR valign=top>

    <TD align=right><P><B><?php  print_string("question", "quiz") ?>:</B></P></TD>

    <TD>

        <?php  if (isset($err["questiontext"])) {

               formerr($err["questiontext"]); 

               echo "<BR />";

           }

           print_textarea($usehtmleditor, 15, 60, 630, 300, "questiontext", $question->questiontext);

           if ($usehtmleditor) {

               helpbutton("richtext", get_string("helprichtext"), "moodle");

           } else {

               helpbutton("text", get_string("helptext"), "moodle");

           }

        ?>

    </TD>

</TR>

<TR valign=top>

    <TD align=right><P><B><?php  print_string("imagedisplay", "quiz") ?>:</B></P></TD>

    <TD>

    <?php   if (empty($images)) {

            print_string("noimagesyet");

        } else {

            choose_from_menu($images, "image", "$question->image", get_string("none"),"","");

        }

    ?>

    </TD>

</TR>

</TABLE>



<INPUT type="hidden" name=id value="<?php  p($question->id) ?>">

<INPUT type="hidden" name=qtype value="<?php  p($question->qtype) ?>">

<INPUT type="hidden" name=defaultgrade value="<?php  p($question->defaultgrade) ?>">

<INPUT type="submit" value="<?php  print_string("savechanges") ?>">



</CENTER>

</FORM>

<?php  

   if ($usehtmleditor) { 

       print_richedit_javascript("theform", "questiontext", "no");

   }

?>

