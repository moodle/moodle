<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web CT question importer.
 *
 * @package    qformat_webct
 * @copyright  2004 ASP Consulting http://www.asp-consulting.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Manipulate HTML editites in a string. Used by WebCT import.
 * @param string $string
 * @return string
 */
function unhtmlentities($string) {
    $search = array ("'<script[?>]*?>.*?</script>'si",  // Remove javascript.
                 "'<[\/\!]*?[^<?>]*?>'si",  // Remove HTML tags.
                 "'([\r\n])[\s]+'",  // Remove spaces.
                 "'&(quot|#34);'i",  // Remove HTML entites.
                 "'&(amp|#38);'i",
                 "'&(lt|#60);'i",
                 "'&(gt|#62);'i",
                 "'&(nbsp|#160);'i",
                 "'&(iexcl|#161);'i",
                 "'&(cent|#162);'i",
                 "'&(pound|#163);'i",
                 "'&(copy|#169);'i",
                 "'&#(\d+);'e");  // Evaluate like PHP.
    $replace = array ("",
                  "",
                  "\\1",
                  "\"",
                  "&",
                  "<",
                  "?>",
                  " ",
                  chr(161),
                  chr(162),
                  chr(163),
                  chr(169),
                  "chr(\\1)");
    return preg_replace ($search, $replace, $string);
}

/**
 * Helper function for WebCT import.
 * @param unknown_type $formula
 */
function qformat_webct_convert_formula($formula) {

    // Remove empty space, as it would cause problems otherwise.
    $formula = str_replace(' ', '', $formula);

    // Remove paranthesis after e,E and *10**.
    while (preg_match('~[0-9.](e|E|\\*10\\*\\*)\\([+-]?[0-9]+\\)~', $formula, $regs)) {
        $formula = str_replace(
                $regs[0], preg_replace('/[)(]/', '', $regs[0]), $formula);
    }

    // Replace *10** with e where possible.
    while (preg_match('~(^[+-]?|[^eE][+-]|[^0-9eE+-])[0-9.]+\\*10\\*\\*[+-]?[0-9]+([^0-9.eE]|$)~',
            $formula, $regs)) {
        $formula = str_replace(
                $regs[0], str_replace('*10**', 'e', $regs[0]), $formula);
    }

    // Replace other 10** with 1e where possible.
    while (preg_match('~(^|[^0-9.eE])10\\*\\*[+-]?[0-9]+([^0-9.eE]|$)~', $formula, $regs)) {
        $formula = str_replace(
                $regs[0], str_replace('10**', '1e', $regs[0]), $formula);
    }

    // Replace all other base**exp with the PHP equivalent function pow(base,exp)
    // (Pretty tricky to exchange an operator with a function).
    while (2 == count($splits = explode('**', $formula, 2))) {

        // Find $base.
        if (preg_match('~^(.*[^0-9.eE])?(([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][+-]?[0-9]+)?|\\{[^}]*\\})$~',
                $splits[0], $regs)) {
            // The simple cases.
            $base = $regs[2];
            $splits[0] = $regs[1];

        } else if (preg_match('~\\)$~', $splits[0])) {
            // Find the start of this parenthesis.
            $deep = 1;
            for ($i = 1; $deep; ++$i) {
                if (!preg_match('~^(.*[^[:alnum:]_])?([[:alnum:]_]*([)(])([^)(]*[)(]){'.$i.'})$~',
                        $splits[0], $regs)) {
                    print_error('parenthesisinproperstart', 'question', '', $splits[0]);
                }
                if ('(' == $regs[3]) {
                    --$deep;
                } else if (')' == $regs[3]) {
                    ++$deep;
                } else {
                    print_error('impossiblechar', 'question', '', $regs[3]);
                }
            }
            $base = $regs[2];
            $splits[0] = $regs[1];

        } else {
            print_error('badbase', 'question', '', $splits[0]);
        }

        // Find $exp (similar to above but a little easier).
        if (preg_match('~^([+-]?(\\{[^}]\\}|([0-9]+(\\.[0-9]*)?|\\.[0-9]+)([eE][+-]?[0-9]+)?))(.*)~',
                $splits[1], $regs)) {
            // The simple case.
            $exp = $regs[1];
            $splits[1] = $regs[6];

        } else if (preg_match('~^[+-]?[[:alnum:]_]*\\(~', $splits[1])) {
            // Find the end of the parenthesis.
            $deep = 1;
            for ($i = 1; $deep; ++$i) {
                if (!preg_match('~^([+-]?[[:alnum:]_]*([)(][^)(]*){'.$i.'}([)(]))(.*)~',
                        $splits[1], $regs)) {
                    print_error('parenthesisinproperclose', 'question', '', $splits[1]);
                }
                if (')' == $regs[3]) {
                    --$deep;
                } else if ('(' == $regs[3]) {
                    ++$deep;
                } else {
                    print_error('impossiblechar', 'question');
                }
            }
            $exp = $regs[1];
            $splits[1] = $regs[4];
        }

        // Replace it!
        $formula = "$splits[0]pow($base,$exp)$splits[1]";
    }

    // Nothing more is known to need to be converted.

    return $formula;
}


/**
 * Web CT question importer.
 *
 * @copyright  2004 ASP Consulting http://www.asp-consulting.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_webct extends qformat_default {
    /** @var string path to the temporary directory. */
    public $tempdir = '';

    /**
     * This plugin provide import
     * @return bool true
     */
    public function provide_import() {
        return true;
    }

    public function can_import_file($file) {
        $mimetypes = array(
            mimeinfo('type', '.txt'),
            mimeinfo('type', '.zip')
        );
        return in_array($file->get_mimetype(), $mimetypes);
    }

    public function mime_type() {
        return mimeinfo('type', '.zip');
    }

    /**
     * Store an image file in a draft filearea
     * @param array $text, if itemid element don't exists it will be created
     * @param string tempdir path to root of image tree
     * @param string filepathinsidetempdir path to image in the tree
     * @param string filename image's name
     * @return string new name of the image as it was stored
     */
    protected function store_file_for_text_field(&$text, $tempdir, $filepathinsidetempdir, $filename) {
        global $USER;
        $fs = get_file_storage();
        if (empty($text['itemid'])) {
            $text['itemid'] = file_get_unused_draft_itemid();
        }
        // As question file areas don't support subdirs,
        // convert path to filename.
        // So that images with same name can be imported.
        $newfilename = clean_param(str_replace('/', '__', $filepathinsidetempdir . '__' . $filename), PARAM_FILE);
        $filerecord = array(
            'contextid' => context_user::instance($USER->id)->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $text['itemid'],
            'filepath'  => '/',
            'filename'  => $newfilename,
        );
        $fs->create_file_from_pathname($filerecord, $tempdir . '/' . $filepathinsidetempdir . '/' . $filename);
        return $newfilename;
    }

    /**
     * Given an HTML text with references to images files,
     * store all images in a draft filearea,
     * and return an array with all urls in text recoded,
     * format set to FORMAT_HTML, and itemid set to filearea itemid
     * @param string text text to parse and recode
     * @return array with keys text, format, itemid.
     */
    public function text_field($text) {
        $data = array();
        // Step one, find all file refs then add to array.
        preg_match_all('|<img[^>]+src="([^"]*)"|i', $text, $out); // Find all src refs.

        $filepaths = array();
        foreach ($out[1] as $path) {
            $fullpath = $this->tempdir . '/' . $path;
            if (is_readable($fullpath) && !in_array($path, $filepaths)) {
                $dirpath = dirname($path);
                $filename = basename($path);
                $newfilename = $this->store_file_for_text_field($data, $this->tempdir, $dirpath, $filename);
                $text = preg_replace("|$path|", "@@PLUGINFILE@@/" . $newfilename, $text);
                $filepaths[] = $path;
            }

        }
        $data['text'] = $text;
        $data['format'] = FORMAT_HTML;
        return $data;
    }

    /**
     * Does any post-processing that may be desired
     * Clean the temporary directory if a zip file was imported
     * @return bool success
     */
    public function importpostprocess() {
        if ($this->tempdir != '') {
            fulldelete($this->tempdir);
        }
        return true;
    }

    /**
     * Return content of all files containing questions,
     * as an array one element for each file found,
     * For each file, the corresponding element is an array of lines.
     * @param string filename name of file
     * @return mixed contents array or false on failure
     */
    public function readdata($filename) {

        // Find if we are importing a .txt file.
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) == 'txt') {
            if (!is_readable($filename)) {
                $this->error(get_string('filenotreadable', 'error'));
                return false;
            }
            return file($filename);
        }
        // We are importing a zip file.
        // Create name for temporary directory.
        $uniquecode = time();
        $this->tempdir = make_temp_directory('webct_import/' . $uniquecode);
        if (is_readable($filename)) {
            if (!copy($filename, $this->tempdir . '/webct.zip')) {
                $this->error(get_string('cannotcopybackup', 'question'));
                fulldelete($this->tempdir);
                return false;
            }
            if (unzip_file($this->tempdir . '/webct.zip', '', false)) {
                $dir = $this->tempdir;
                if ((($handle = opendir($dir))) == false) {
                    // The directory could not be opened.
                    fulldelete($this->tempdir);
                    return false;
                }
                // Create arrays to store files and directories.
                $dirfiles = array();
                $dirsubdirs = array();
                $slash = '/';

                // Loop through all directory entries, and construct two temporary arrays containing files and sub directories.
                while (false !== ($entry = readdir($handle))) {
                    if (is_dir($dir. $slash .$entry) && $entry != '..' && $entry != '.') {
                        $dirsubdirs[] = $dir. $slash .$entry;
                    } else if ($entry != '..' && $entry != '.') {
                        $dirfiles[] = $dir. $slash .$entry;
                    }
                }
                if ((($handle = opendir($dirsubdirs[0]))) == false) {
                    // The directory could not be opened.
                    fulldelete($this->tempdir);
                    return false;
                }
                while (false !== ($entry = readdir($handle))) {
                    if (is_dir($dirsubdirs[0]. $slash .$entry) && $entry != '..' && $entry != '.') {
                        $dirsubdirs[] = $dirsubdirs[0]. $slash .$entry;
                    } else if ($entry != '..' && $entry != '.') {
                        $dirfiles[] = $dirsubdirs[0]. $slash .$entry;
                    }
                }
                return file($dirfiles[1]);
            } else {
                $this->error(get_string('cannotunzip', 'question'));
                fulldelete($this->temp_dir);
            }
        } else {
            $this->error(get_string('cannotreaduploadfile', 'error'));
            fulldelete($this->tempdir);
        }
        return false;
    }

    public function readquestions ($lines) {
        $webctnumberregex =
                '[+-]?([0-9]+(\\.[0-9]*)?|\\.[0-9]+)((e|E|\\*10\\*\\*)([+-]?[0-9]+|\\([+-]?[0-9]+\\)))?';

        $questions = array();
        $errors = array();
        $warnings = array();
        $webctoptions = array();

        $ignorerestofquestion = false;

        $nlinecounter = 0;
        $nquestionstartline = 0;
        $bishtmltext = false;
        $lines[] = ":EOF:";    // For an easiest processing of the last line.
        // We don't call defaultquestion() here, it will be called later.

        foreach ($lines as $line) {
            $nlinecounter++;
            $line = textlib::convert($line, 'windows-1252', 'utf-8');
            // Processing multiples lines strings.

            if (isset($questiontext) and is_string($questiontext)) {
                if (preg_match("~^:~", $line)) {
                    $questiontext = $this->text_field(trim($questiontext));
                    $question->questiontext = $questiontext['text'];
                    $question->questiontextformat = $questiontext['format'];
                    if (isset($questiontext['itemid'])) {
                        $question->questiontextitemid = $questiontext['itemid'];
                    }
                    unset($questiontext);
                } else {
                    $questiontext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($answertext) and is_string($answertext)) {
                if (preg_match("~^:~", $line)) {
                    $answertext = trim($answertext);
                    if ($question->qtype == 'multichoice' || $question->qtype == 'match' ) {
                        $question->answer[$currentchoice] = $this->text_field($answertext);
                        $question->subanswers[$currentchoice] = $question->answer[$currentchoice];

                    } else {
                        $question->answer[$currentchoice] = $answertext;
                        $question->subanswers[$currentchoice] = $answertext;
                    }
                    unset($answertext);
                } else {
                    $answertext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($responsetext) and is_string($responsetext)) {
                if (preg_match("~^:~", $line)) {
                    $question->subquestions[$currentchoice] = trim($responsetext);
                    unset($responsetext);
                } else {
                    $responsetext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($feedbacktext) and is_string($feedbacktext)) {
                if (preg_match("~^:~", $line)) {
                    $question->feedback[$currentchoice] = $this->text_field(trim($feedbacktext));
                    unset($feedbacktext);
                } else {
                    $feedbacktext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($generalfeedbacktext) and is_string($generalfeedbacktext)) {
                if (preg_match("~^:~", $line)) {
                    $question->tempgeneralfeedback = trim($generalfeedbacktext);
                    unset($generalfeedbacktext);
                } else {
                    $generalfeedbacktext .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            if (isset($graderinfo) and is_string($graderinfo)) {
                if (preg_match("~^:~", $line)) {
                    $question->graderinfo['text'] = trim($graderinfo);
                    $question->graderinfo['format'] = FORMAT_HTML;
                    unset($graderinfo);
                } else {
                    $graderinfo .= str_replace('\:', ':', $line);
                    continue;
                }
            }

            $line = trim($line);

            if (preg_match("~^:(TYPE|EOF):~i", $line)) {
                // New Question or End of File.
                if (isset($question)) {            // If previous question exists, complete, check and save it.

                    // Setup default value of missing fields.
                    if (!isset($question->name)) {
                        $question->name = $this->create_default_question_name(
                                $question->questiontext, get_string('questionname', 'question'));
                    }
                    if (!isset($question->defaultmark)) {
                        $question->defaultmark = 1;
                    }
                    if (!isset($question->image)) {
                        $question->image = '';
                    }

                    // Perform sanity checks.
                    $questionok = true;
                    if (strlen($question->questiontext) == 0) {
                        $warnings[] = get_string('missingquestion', 'qformat_webct', $nquestionstartline);
                        $questionok = false;
                    }
                    if (count($question->answer) < 1) {  // A question must have at least 1 answer.
                        $errors[] = get_string('missinganswer', 'qformat_webct', $nquestionstartline);
                        $questionok = false;
                    } else {
                        // Create empty feedback array.
                        foreach ($question->answer as $key => $dataanswer) {
                            if (!isset($question->feedback[$key])) {
                                $question->feedback[$key]['text'] = '';
                                $question->feedback[$key]['format'] = FORMAT_HTML;
                            }
                        }
                        // This tempgeneralfeedback allows the code to work with versions from 1.6 to 1.9.
                        // When question->generalfeedback is undefined, the webct feedback is added to each answer feedback.
                        if (isset($question->tempgeneralfeedback)) {
                            if (isset($question->generalfeedback)) {
                                $generalfeedback = $this->text_field($question->tempgeneralfeedback);
                                $question->generalfeedback = $generalfeedback['text'];
                                $question->generalfeedbackformat = $generalfeedback['format'];
                                if (isset($generalfeedback['itemid'])) {
                                    $question->genralfeedbackitemid = $generalfeedback['itemid'];
                                }
                            } else {
                                foreach ($question->answer as $key => $dataanswer) {
                                    if ($question->tempgeneralfeedback != '') {
                                        $question->feedback[$key]['text'] = $question->tempgeneralfeedback
                                                .'<br/>'.$question->feedback[$key]['text'];
                                    }
                                }
                            }
                            unset($question->tempgeneralfeedback);
                        }
                        $maxfraction = -1;
                        $totalfraction = 0;
                        foreach ($question->fraction as $fraction) {
                            if ($fraction > 0) {
                                $totalfraction += $fraction;
                            }
                            if ($fraction > $maxfraction) {
                                $maxfraction = $fraction;
                            }
                        }
                        switch ($question->qtype) {
                            case 'shortanswer':
                                if ($maxfraction != 1) {
                                    $maxfraction = $maxfraction * 100;
                                    $errors[] = "'$question->name': ".get_string('wronggrade', 'qformat_webct', $nlinecounter)
                                            .' '.get_string('fractionsnomax', 'question', $maxfraction);
                                    $questionok = false;
                                }
                                break;

                            case 'multichoice':
                                $question = $this->add_blank_combined_feedback($question);

                                if ($question->single) {
                                    if ($maxfraction != 1) {
                                        $maxfraction = $maxfraction * 100;
                                        $errors[] = "'$question->name': ".get_string('wronggrade', 'qformat_webct', $nlinecounter)
                                                .' '.get_string('fractionsnomax', 'question', $maxfraction);
                                        $questionok = false;
                                    }
                                } else {
                                    $totalfraction = round($totalfraction, 2);
                                    if ($totalfraction != 1) {
                                        $totalfraction = $totalfraction * 100;
                                        $errors[] = "'$question->name': ".get_string('wronggrade', 'qformat_webct', $nlinecounter)
                                                .' '.get_string('fractionsaddwrong', 'question', $totalfraction);
                                        $questionok = false;
                                    }
                                }
                                break;

                            case 'calculated':
                                foreach ($question->answers as $answer) {
                                    if ($formulaerror = qtype_calculated_find_formula_errors($answer)) {
                                        $warnings[] = "'$question->name': ". $formulaerror;
                                        $questionok = false;
                                    }
                                }
                                foreach ($question->dataset as $dataset) {
                                    $dataset->itemcount = count($dataset->datasetitem);
                                }
                                $question->import_process = true;
                                unset($question->answer); // Not used in calculated question.
                                break;
                            case 'match':
                                // MDL-10680:
                                // Switch subquestions and subanswers.
                                $question = $this->add_blank_combined_feedback($question);
                                foreach ($question->subquestions as $id => $subquestion) {
                                    $temp = $question->subquestions[$id];
                                    $question->subquestions[$id] = $question->subanswers[$id];
                                    $question->subanswers[$id] = $temp;
                                }
                                if (count($question->answer) < 3) {
                                    // Add a dummy missing question.
                                    $question->name = 'Dummy question added '.$question->name;
                                    $question->answer[] = 'dummy';
                                    $question->subanswers[] = 'dummy';
                                    $question->subquestions[] = 'dummy';
                                    $question->fraction[] = '0.0';
                                    $question->feedback[] = '';
                                }
                                break;
                            default:
                                // No problemo.
                        }
                    }

                    if ($questionok) {
                        $questions[] = $question;    // Store it.
                        unset($question);            // And prepare a new one.
                        $question = $this->defaultquestion();
                    }
                }
                $nquestionstartline = $nlinecounter;
            }

            // Processing Question Header.

            if (preg_match("~^:TYPE:MC:1(.*)~i", $line, $webctoptions)) {
                // Multiple Choice Question with only one good answer.
                $question = $this->defaultquestion();
                $question->feedback = array();
                $question->qtype = 'multichoice';
                $question->single = 1;        // Only one answer is allowed.
                $ignorerestofquestion = false;
                continue;
            }

            if (preg_match("~^:TYPE:MC:N(.*)~i", $line, $webctoptions)) {
                // Multiple Choice Question with several good answers.
                $question = $this->defaultquestion();
                $question->feedback = array();
                $question->qtype = 'multichoice';
                $question->single = 0;        // Many answers allowed.
                $ignorerestofquestion = false;
                continue;
            }

            if (preg_match("~^:TYPE:S~i", $line)) {
                // Short Answer Question.
                $question = $this->defaultquestion();
                $question->feedback = array();
                $question->qtype = 'shortanswer';
                $question->usecase = 0;       // Ignore case.
                $ignorerestofquestion = false;
                continue;
            }

            if (preg_match("~^:TYPE:C~i", $line)) {
                // Calculated Question.
                $question = $this->defaultquestion();
                $question->qtype = 'calculated';
                $question->answers = array(); // No problem as they go as :FORMULA: from webct.
                $question->units = array();
                $question->dataset = array();

                // To make us pass the end-of-question sanity checks.
                $question->answer = array('dummy');
                $question->fraction = array('1.0');
                $question->feedback = array();

                $currentchoice = -1;
                $ignorerestofquestion = false;
                continue;
            }

            if (preg_match("~^:TYPE:M~i", $line)) {
                // Match Question.
                $question = $this->defaultquestion();
                $question->qtype = 'match';
                $question->feedback = array();
                $ignorerestofquestion = false;         // Match question processing is not debugged.
                continue;
            }

            if (preg_match("~^:TYPE:P~i", $line)) {
                // Paragraph Question.
                $question = $this->defaultquestion();
                $question->qtype = 'essay';
                $question->responseformat = 'editor';
                $question->responsefieldlines = 15;
                $question->attachments = 0;
                $question->graderinfo = array(
                        'text' => '',
                        'format' => FORMAT_HTML,
                    );
                $question->feedback = array();
                $question->generalfeedback = '';
                $question->generalfeedbackformat = FORMAT_HTML;
                $question->generalfeedbackfiles = array();
                $question->questiontextformat = FORMAT_HTML;
                $ignorerestofquestion = false;
                // To make us pass the end-of-question sanity checks.
                $question->answer = array('dummy');
                $question->fraction = array('1.0');
                continue;
            }

            if (preg_match("~^:TYPE:~i", $line)) {
                // Unknow question type.
                $warnings[] = get_string('unknowntype', 'qformat_webct', $nlinecounter);
                unset($question);
                $ignorerestofquestion = true;         // Question Type not handled by Moodle.
                continue;
            }

            if ($ignorerestofquestion) {
                continue;
            }

            if (preg_match("~^:TITLE:(.*)~i", $line, $webctoptions)) {
                $name = trim($webctoptions[1]);
                $question->name = $this->clean_question_name($name);
                continue;
            }

            if (preg_match("~^:IMAGE:(.*)~i", $line, $webctoptions)) {
                $filename = trim($webctoptions[1]);
                if (preg_match("~^http://~i", $filename)) {
                    $question->image = $filename;
                }
                continue;
            }

            // Need to put the parsing of calculated items here to avoid ambitiuosness:
            // if question isn't defined yet there is nothing to do here (avoid notices).
            if (!isset($question)) {
                continue;
            }
            if (isset($question->qtype ) && 'calculated' == $question->qtype && preg_match(
                    "~^:([[:lower:]].*|::.*)-(MIN|MAX|DEC|VAL([0-9]+))::?:?($webctnumberregex)~", $line, $webctoptions)) {
                $datasetname = preg_replace('/^::/', '', $webctoptions[1]);
                $datasetvalue = qformat_webct_convert_formula($webctoptions[4]);
                switch ($webctoptions[2]) {
                    case 'MIN':
                        $question->dataset[$datasetname]->min = $datasetvalue;
                        break;
                    case 'MAX':
                        $question->dataset[$datasetname]->max = $datasetvalue;
                        break;
                    case 'DEC':
                        $datasetvalue = floor($datasetvalue); // Int only!
                        $question->dataset[$datasetname]->length = max(0, $datasetvalue);
                        break;
                    default:
                        // The VAL case.
                        $question->dataset[$datasetname]->datasetitem[$webctoptions[3]] = new stdClass();
                        $question->dataset[$datasetname]->datasetitem[$webctoptions[3]]->itemnumber = $webctoptions[3];
                        $question->dataset[$datasetname]->datasetitem[$webctoptions[3]]->value  = $datasetvalue;
                        break;
                }
                continue;
            }

            $bishtmltext = preg_match("~:H$~i", $line);  // True if next lines are coded in HTML.
            if (preg_match("~^:QUESTION~i", $line)) {
                $questiontext = '';               // Start gathering next lines.
                continue;
            }

            if (preg_match("~^:ANSWER([0-9]+):([^:]+):([0-9\.\-]+):(.*)~i", $line, $webctoptions)) { // Shortanswer.
                $currentchoice = $webctoptions[1];
                $answertext = $webctoptions[2];            // Start gathering next lines.
                $question->fraction[$currentchoice] = ($webctoptions[3]/100);
                continue;
            }

            if (preg_match("~^:ANSWER([0-9]+):([0-9\.\-]+)~i", $line, $webctoptions)) {
                $answertext = '';                 // Start gathering next lines.
                $currentchoice = $webctoptions[1];
                $question->fraction[$currentchoice] = ($webctoptions[2]/100);
                continue;
            }

            if (preg_match('~^:ANSWER:~i', $line)) { // Essay.
                $graderinfo  = '';      // Start gathering next lines.
                continue;
            }

            if (preg_match('~^:FORMULA:(.*)~i', $line, $webctoptions)) {
                // Answer for a calculated question.
                ++$currentchoice;
                $question->answers[$currentchoice] =
                        qformat_webct_convert_formula($webctoptions[1]);

                // Default settings.
                $question->fraction[$currentchoice] = 1.0;
                $question->tolerance[$currentchoice] = 0.0;
                $question->tolerancetype[$currentchoice] = 2; // Nominal (units in webct).
                $question->feedback[$currentchoice]['text'] = '';
                $question->feedback[$currentchoice]['format'] = FORMAT_HTML;
                $question->correctanswerlength[$currentchoice] = 4;

                $datasetnames =
                        question_bank::get_qtype('calculated')->find_dataset_names($webctoptions[1]);
                foreach ($datasetnames as $datasetname) {
                    $question->dataset[$datasetname] = new stdClass();
                    $question->dataset[$datasetname]->datasetitem = array();
                    $question->dataset[$datasetname]->name = $datasetname;
                    $question->dataset[$datasetname]->distribution = 'uniform';
                    $question->dataset[$datasetname]->status = 'private';
                }
                continue;
            }

            if (preg_match("~^:L([0-9]+)~i", $line, $webctoptions)) {
                $answertext = '';                 // Start gathering next lines.
                $currentchoice = $webctoptions[1];
                $question->fraction[$currentchoice] = 1;
                continue;
            }

            if (preg_match("~^:R([0-9]+)~i", $line, $webctoptions)) {
                $responsetext = '';                // Start gathering next lines.
                $currentchoice = $webctoptions[1];
                continue;
            }

            if (preg_match("~^:REASON([0-9]+):?~i", $line, $webctoptions)) {
                $feedbacktext = '';               // Start gathering next lines.
                $currentchoice = $webctoptions[1];
                continue;
            }
            if (preg_match("~^:FEEDBACK([0-9]+):?~i", $line, $webctoptions)) {
                $generalfeedbacktext = '';               // Start gathering next lines.
                $currentchoice = $webctoptions[1];
                continue;
            }
            if (preg_match('~^:FEEDBACK:(.*)~i', $line, $webctoptions)) {
                $generalfeedbacktext = '';               // Start gathering next lines.
                continue;
            }
            if (preg_match('~^:LAYOUT:(.*)~i', $line, $webctoptions)) {
                // Ignore  since layout in question_multichoice  is no more used in Moodle.
                // $webctoptions[1] contains either vertical or horizontal.
                continue;
            }

            if (isset($question->qtype ) && 'calculated' == $question->qtype
                    && preg_match('~^:ANS-DEC:([1-9][0-9]*)~i', $line, $webctoptions)) {
                // We can but hope that this always appear before the ANSTYPE property.
                $question->correctanswerlength[$currentchoice] = $webctoptions[1];
                continue;
            }

            if (isset($question->qtype )&& 'calculated' == $question->qtype
                    && preg_match("~^:TOL:($webctnumberregex)~i", $line, $webctoptions)) {
                // We can but hope that this always appear before the TOL property.
                $question->tolerance[$currentchoice] =
                        qformat_webct_convert_formula($webctoptions[1]);
                continue;
            }

            if (isset($question->qtype )&& 'calculated' == $question->qtype && preg_match('~^:TOLTYPE:percent~i', $line)) {
                // Percentage case is handled as relative in Moodle.
                $question->tolerance[$currentchoice]  /= 100;
                $question->tolerancetype[$currentchoice] = 1; // Relative.
                continue;
            }

            if (preg_match('~^:UNITS:(.+)~i', $line, $webctoptions)
                    and $webctunits = trim($webctoptions[1])) {
                // This is a guess - I really do not know how different webct units are separated...
                $webctunits = explode(':', $webctunits);
                $unitrec->multiplier = 1.0; // Webct does not seem to support this.
                foreach ($webctunits as $webctunit) {
                    $unitrec->unit = trim($webctunit);
                    $question->units[] = $unitrec;
                }
                continue;
            }

            if (!empty($question->units) && preg_match('~^:UNITREQ:(.*)~i', $line, $webctoptions)
                    && !$webctoptions[1]) {
                // There are units but units are not required so add the no unit alternative.
                // We can but hope that the UNITS property always appear before this property.
                $unitrec->unit = '';
                $unitrec->multiplier = 1.0;
                $question->units[] = $unitrec;
                continue;
            }

            if (!empty($question->units) && preg_match('~^:UNITCASE:~i', $line)) {
                // This could be important but I was not able to figure out how
                // it works so I ignore it for now.
                continue;
            }

            if (isset($question->qtype )&& 'calculated' == $question->qtype && preg_match('~^:ANSTYPE:dec~i', $line)) {
                $question->correctanswerformat[$currentchoice] = '1';
                continue;
            }
            if (isset($question->qtype )&& 'calculated' == $question->qtype && preg_match('~^:ANSTYPE:sig~i', $line)) {
                $question->correctanswerformat[$currentchoice] = '2';
                continue;
            }
        }

        if (count($errors) > 0) {
            echo '<p>'.get_string('errorsdetected', 'qformat_webct', count($errors)).'</p><ul>';
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul>';
            unset($questions);     // No questions imported.
        }

        if (count($warnings) > 0) {
            echo '<p>'.get_string('warningsdetected', 'qformat_webct', count($warnings)).'</p><ul>';
            foreach ($warnings as $warning) {
                echo "<li>$warning</li>";
            }
            echo '</ul>';
        }
        return $questions;
    }
}
