<?php // $Id$
//
///////////////////////////////////////////////////////////////
// XML import/export
//
//////////////////////////////////////////////////////////////////////////
// Based on default.php, included by ../import.php

class quiz_file_format extends quiz_default_format {

function indent_xhtml($source, $indenter = ' ') { 
    // xml tidier-upper
    // (c) Ari Koivula http://ventionline.com
    
    // Remove all pre-existing formatting. 
    // Remove all newlines. 
    $source = str_replace("\n", '', $source); 
    $source = str_replace("\r", '', $source); 
    // Remove all tabs. 
    $source = str_replace("\t", '', $source); 
    // Remove all space after ">" and before "<". 
    $source = ereg_replace(">( )*", ">", $source); 
    $source = ereg_replace("( )*<", "<", $source); 

    // Iterate through the source. 
    $level = 0; 
    $source_len = strlen($source); 
    $pt = 0; 
    while ($pt < $source_len) { 
        if ($source{$pt} === '<') { 
            // We have entered a tag. 
            // Remember the point where the tag starts. 
            $started_at = $pt; 
            $tag_level = 1; 
            // If the second letter of the tag is "/", assume its an ending tag. 
            if ($source{$pt+1} === '/') { 
                $tag_level = -1; 
            } 
            // If the second letter of the tag is "!", assume its an "invisible" tag. 
            if ($source{$pt+1} === '!') { 
                $tag_level = 0; 
            } 
            // Iterate throught the source until the end of tag. 
            while ($source{$pt} !== '>') { 
                $pt++; 
            } 
            // If the second last letter is "/", assume its a self ending tag. 
            if ($source{$pt-1} === '/') { 
                $tag_level = 0; 
            } 
            $tag_lenght = $pt+1-$started_at; 
             
            // Decide the level of indention for this tag. 
            // If this was an ending tag, decrease indent level for this tag.. 
            if ($tag_level === -1) { 
                $level--; 
            } 
            // Place the tag in an array with proper indention. 
            $array[] = str_repeat($indenter, $level).substr($source, $started_at, $tag_lenght); 
            // If this was a starting tag, increase the indent level after this tag. 
            if ($tag_level === 1) { 
                $level++; 
            } 
            // if it was a self closing tag, dont do shit. 
        } 
        // Were out of the tag. 
        // If next letter exists... 
        if (($pt+1) < $source_len) { 
            // ... and its not an "<". 
            if ($source{$pt+1} !== '<') { 
                $started_at = $pt+1; 
                // Iterate through the source until the start of new tag or until we reach the end of file. 
                while ($source{$pt} !== '<' && $pt < $source_len) { 
                    $pt++; 
                } 
                // If we found a "<" (we didnt find the end of file) 
                if ($source{$pt} === '<') { 
                    $tag_lenght = $pt-$started_at; 
                    // Place the stuff in an array with proper indention. 
                    $array[] = str_repeat($indenter, $level).substr($source, $started_at, $tag_lenght); 
                } 
            // If the next tag is "<", just advance pointer and let the tag indenter take care of it. 
            } else { 
                $pt++; 
            } 
        // If the next letter doesnt exist... Were done... well, almost.. 
        } else { 
            break; 
        } 
    } 
    // Replace old source with the new one we just collected into our array. 
    $source = implode($array, "\n"); 
    return $source; 
} 


function export_file_extension() {
    // override default type so extension is .xml
    
    return ".xml";
}

function get_qtype( $type_id ) {
    // translates question type code number into actual name
   
    switch( $type_id ) {
    case TRUEFALSE:
        $name = 'truefalse';
        break;
    case MULTICHOICE:
        $name = 'multichoice';
        break;
    case SHORTANSWER:
        $name = 'shortanswer';
        break;
    case NUMERICAL:
        $name = 'numerical';
        break;
    case MATCH:
        $name = 'matching';
        break;
    case DESCRIPTION:
        $name = 'description';
        break;
    case MULTIANSWER:
        $name = 'cloze';
        break;
    default:
        $name = '';
        error( "question type $type_id is not defined in get_qtype" );
    }
    return $name;
}

function writetext( $raw ) {
    // generates <text></text> tags, processing raw text therein 

    // for now, don't allow any additional tags in text 
    // otherwise xml rules would probably get broken
    $raw = strip_tags( $raw );

    return "<text>$raw</text>\n";
}

function writequestion( $question ) {
    // turns question into string
    // question reflects database fields for general question and specific to type

    // initial string;
    $expout = "";

    // add comment
    $expout .= "\n\n<!-- question: $question->id  name: $question->name -->\n";

    // add opening tag
    $question_type = $this->get_qtype( $question->qtype );
    $name_text = $this->writetext( $question->name );
    $question_text = $this->writetext( $question->questiontext );
    $expout .= "<question type=\"$question_type\">\n";   
    $expout .= "<name>".$this->writetext($name_text)."</name>\n";
    $expout .= "<questiontext>".$this->writetext($question_text)."</questiontext>\n";   

    // output depends on question type
    switch($question->qtype) {
    case TRUEFALSE:
        $true_percent = round( $question->trueanswer->fraction * 100 );
        $false_percent = round( $question->falseanswer->fraction * 100 );
        // true answer
        $expout .= "<answer fraction=\"$true_percent\">\n";
        $expout .= $this->writetext("true")."\n";
        $expout .= "<feedback>".$this->writetext( $question->trueanswer->feedback )."</feedback>\n";
        $expout .= "</answer>\n";


        // false answer
        $expout .= "<answer fraction=\"$false_percent\">\n";
        $expout .= $this->writetext("false")."\n";
        $expout .= "<feedback>".$this->writetext( $question->falseanswer->feedback )."</feedback>\n";
        $expout .= "</answer>\n";
        break;
    case MULTICHOICE:
        foreach($question->answers as $answer) {
            $percent = round( $answer->fraction * 100 );
            $expout .= "<answer fraction=\"$percent\">\n";
            $expout .= $this->writetext( $answer->answer );
            $expout .= "<feedback>".$this->writetext( $answer->feedback )."</feedback>\n";
            $expout .= "</answer>\n";
            }
        break;
    case SHORTANSWER:
        foreach($question->answers as $answer) {
            $percent = 100 * $answer->fraction;
            $expout .= "<answer fraction=\"$percent\">\n";
            $expout .= $this->writetext( $answer->answer );
            $expout .= "<feedback>".$this->writetext( $answer->feedback )."</feedback>\n";
            $expout .= "</answer>\n";
        }
        break;
    case NUMERICAL:
        $expout .= "<min>$question->min</min>\n";
        $expout .= "<max>$question->max</max>\n";
        $expout .= "<feedback>".$this->writetext( $answer->feedback )."</feedback>\n";
        break;
    case MATCH:
        foreach($question->subquestions as $subquestion) {
            $expout .= "<subquestion>\n";
            $expout .= $this->writetext( $subquestion->questiontext );
            $expout .= "<answer>".$this->writetext( $subquestion->answertext )."</answer>\n";
            $expout .= "</subquestion>\n";
        }
        break;
    case DESCRIPTION:
        $expout .= "<!-- DESCRIPTION type is not supported -->\n";
        break;
    case MULTIANSWER:
        $expout .= "<!-- CLOZE type is not supported -->\n";
        break;
    default:
        error( "No handler for qtype $question->qtype for GIFT export" );
    }
    // close the question tag
    $expout .= "</question>\n";
    // run through xml tidy function
    return $this->indent_xhtml( $expout, '  ' ); 
}
}

?>
