<?PHP  // $Id$ 
// Alton College, Hampshire, UK - Tom Flannaghan
// Imports learnwise multiple choice quizzes
// Based on format.php, included by ../../import.php

function string_between($text, $start, $end)
{
	$startpos = strpos($text, $start) + strlen($start);
	$endpos = strpos($text, $end);
	
	if($startpos <= $endpos) return substr($text, $startpos, $endpos - $startpos);
}
	
	

class quiz_file_format extends quiz_default_format {

	function readquestions($lines) {
        $questions = array();
        $currentquestion = array();
		
		foreach($lines as $line) {
			$line = trim($line);
			$currentquestion[] = $line;
			
			if($question = $this->readquestion($currentquestion)) {
				$questions[] = $question;
				$currentquestion = array();
			}
		}
				
		return $questions;
	}

        function unhtmlentities($string)
        // puts all the &gt; etc stuff back to 'normal' 
        // good for PHP 4.1.0 on
        {
          $trans_tbl = get_html_translation_table(HTML_ENTITIES);
          $trans_tbl = array_flip($trans_tbl);
          return strtr($string, $trans_tbl);
        }

    function readquestion($lines) {
		$text = '';
		foreach ($lines as $line) $text .= $line;
		$text = str_replace(array('\t','\n','\r','\''), array('','','','\\\''), $text);
		$len = strlen($text);

		$question = NULL;

		if($questiontype = sscanf($text, '<question type="%s">'))
		{
			if($pos = stripos($text, '</question>'))
			{
				$text = substr($text, 0, $pos);
				$text = stristr($text, '<question');
				
				$questiontext = '';
				$questionaward = '';
				$questionhint = '';
				$optionlist = '';
				$questiontext = string_between($text, '<text>', '</text>');
				$questionaward = string_between($text, '<award>', '</award>');
				$questionhint = string_between($text, '<hint>', '</hint>');
				$optionlist = string_between($text, '<answer>', '</answer>');
				
				$optionlist = explode('<option', $optionlist);
				$n=0;
				
				foreach($optionlist as $option)
				{
					$a = string_between($option, ' correct="', '">');
					$b = string_between($option, '">', '</option>');
					
					$options_correct[$n] = $a; $options_text[$n] = $b;
					//echo "$a, $b<br>";
					$n++;
					
				}
				
				$question->qtype = MULTICHOICE;
				$question->name = substr($this->unhtmlentities($questiontext),0,30);
				if(strlen($questionlen)<30) $question->name .= '...';
				$question->questiontext = $this->unhtmlentities($questiontext);
				$question->single = 1;
				$question->feedback[] = '';
				$question->usecase = 0;
				$question->defaultgrade = 1;
				$question->image = '';
				
				for($n=0; $n<sizeof($options_text); $n++)
				{
					if($options_text[$n])
					{
						if($options_correct[$n]=='yes') $fraction = (int) $questionaward; else $fraction = 0;
						$question->fraction[] = $fraction;
						$question->answer[] = $this->unhtmlentities($options_text[$n]);
						//echo "hello: $options_text[$n], $fraction<br>";
					}
				}
			}
		}
		
        return $question;
    }
}

?>
