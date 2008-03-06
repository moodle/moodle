<?PHP  // $Id$

////////////////////////////////////////////////////////////////////
/// Default class for report plugins
///
/// Doesn't do anything on it's own -- it needs to be extended.
/// This class displays quiz reports.  Because it is called from
/// within /mod/quiz/report.php you can assume that the page header
/// and footer are taken care of.
///
/// This file can refer to itself as report.php to pass variables
/// to itself - all these will also be globally available.  You must
/// pass "id=$cm->id" or q=$quiz->id", and "mode=reportname".
////////////////////////////////////////////////////////////////////

// Included by ../report.php

class hotpot_default_report {

    function display($hotpot, $cm, $course, $users, $attempts, $questions, $options) {
        /// This function just displays the report
        // it is replaced by the "display" functions in the scripts in the "report" folder
        return true;
    }

    function add_question_headings(&$questions, &$table, $align='center', $size=50, $wrap=false, $fontsize=0) {
        $count = count($questions);
        for ($i=0; $i<$count; $i++) {
            $table->head[] = get_string('questionshort', 'hotpot', $i+1);
            if (isset($table->align)) {
                $table->align[] = $align;
            }
            if (isset($table->size)) {
                $table->size[] = $size;
            }
            if (isset($table->wrap)) {
                $table->wrap[] = $wrap;
            }
            if (isset($table->fontsize)) {
                $table->fontsize[] = $fontsize;
            }
        }

    }

    function set_legend(&$table, &$q, &$value, &$question) {
        // $q is the question number
        // $value is the value (=text) of the answer

        // check the legend is required
        if (isset($table->legend) && isset($value)) {

            // create question details array, if necessary
            if (empty($table->legend[$q])) {
                $table->legend[$q] = array(
                    'name' => hotpot_get_question_name($question),
                    'answers' => array()
                );
            }

            // search for this $value in answers array for this $q(uestion)
            $i_max = count($table->legend[$q]['answers']);
            for ($i=0; $i<$i_max; $i++) {
                if ($table->legend[$q]['answers'][$i]==$value) {
                    break;
                }
            }

            // add $value to answers array, if it was not there
            if ($i==$i_max) {
                $table->legend[$q]['answers'][$i] = $value;
            }

            // convert $value to alphabetic index (A, B ... AA, AB ...)
            $value = $this->dec_to_ALPHA($i);
        }
    }
    function create_legend_table(&$tables, &$table) {

        if (isset($table->legend)) {

            $legend->width = '*';
            $legend->tablealign = '*';
            $legend->border = isset($table->border) ? $table->border : NULL;
            $legend->cellpadding = isset($table->cellpadding) ? $table->cellpadding : NULL;
            $legend->cellspacing = isset($table->cellspacing) ? $table->cellspacing : NULL;
            $legend->tableclass = isset($table->tableclass) ? $table->tableclass : NULL;

            $legend->caption = get_string('reportlegend', 'hotpot');
            $legend->align = array('right', 'left');
            $legend->statheadercols = array(0);

            $legend->stat = array();

            // put the questions in order
            ksort($table->legend);

            foreach($table->legend as $q=>$question) {

                $legend->stat[] = array(
                    get_string('questionshort', 'hotpot', $q+1),
                    $question['name']
                );
                foreach($question['answers'] as $a=>$answer) {
                    $legend->stat[] = array(
                        $this->dec_to_ALPHA($a),
                        $answer
                    );
                }
            }

            unset($table->legend);
            $tables[] = $legend;
        }
    }
    function dec_to_ALPHA($dec) {
        if ($dec < 26) {
            return chr(ord('A') + $dec);
        } else {
            return $this->dec_to_ALPHA(intval($dec/26)-1).$this->dec_to_ALPHA($dec % 26);
        }
    }
    function remove_column(&$table, $target_col) {

        if (is_array($table)) {
            unset($table[$target_col]);
            $table = array_values($table);

        } else if (is_object($table)) {
            $vars = get_object_vars($table);
            foreach ($vars as $name=>$value) {
                switch ($name) {
                    case 'data' :
                    case 'stat' :
                    case 'foot' :
                        $skipcol = array();
                        $cells = &$table->$name;

                        $row_max = count($cells);
                        for ($row=0; $row<$row_max; $row++) {

                            $col = 0;
                            $col_max = count($cells[$row]);

                            $current_col = 0;
                            while ($current_col<$target_col && $col<$col_max) {

                                if (empty($skipcol[$current_col])) {

                                    $cell = $cells[$row][$col++];
                                    if (is_object($cell)) {
                                        if (isset($cell->rowspan) && is_numeric($cell->rowspan) && ($cell->rowspan>0)) {
                                            // skip cells below this one
                                            $skipcol[$current_col] = $cell->rowspan-1;
                                        }
                                        if (isset($cell->colspan) && is_numeric($cell->colspan) && ($cell->colspan>0)) {
                                            // skip cells to the right of this one
                                            for ($c=1; $c<$cell->colspan; $c++) {
                                                if (empty($skipcol[$current_col+$c])) {
                                                    $skipcol[$current_col+$c] = 1;
                                                } else {
                                                    $skipcol[$current_col+$c] ++;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    $skipcol[$current_col]--;
                                }
                                $current_col++;
                            }
                            if ($current_col==$target_col && $col<$col_max) {
                                $this->remove_column($cells[$row], $col);
                            }
                        } // end for $row
                        break;
                    case 'head' :
                    case 'align' :
                    case 'class' :
                    case 'fontsize' :
                    case 'size' :
                    case 'wrap' :
                        $this->remove_column($table->$name, $target_col);
                        break;
                    case 'statheadercols' :
                        $array = &$table->$name;
                        $count = count($array);
                        for ($i=0; $i<$count; $i++) {
                            if ($array[$i]>=$target_col) {
                                $array[$i] --;
                            }
                        }
                        break;
                } // end switch
            } // end foreach
        } // end if
    } // end function


    function expand_spans(&$table, $zone) {
        // expand multi-column and multi-row cells in a specified $zone of a $table

        // do nothing if this $zone is empty
        if (empty($table->$zone)) return;

        // shortcut to rows in this $table $zone
        $rows = &$table->{$zone};

        // loop through the rows
        foreach ($rows as $row=>$cells) {

            // check this is an array
            if (is_array($cells)) {

                // loop through the cells in this row
                foreach ($cells as $col=>$cell) {

                    if (is_object($cell)) {
                        if (isset($cell->rowspan) && is_numeric($cell->rowspan) && ($cell->rowspan>1)) {
                            // fill in cells below this one
                            $new_cell = array($cell->text);
                            for ($r=1; $r<$cell->rowspan; $r++) {
                                array_splice($rows[$row+$r], $col, 0, $new_cell);
                            }
                        }
                        if (isset($cell->colspan) && is_numeric($cell->colspan) && ($cell->colspan>1)) {
                            // fill in cells to the right of this one
                            $new_cells = array();
                            for ($c=1; $c<$cell->colspan; $c++) {
                                $new_cells[] = $cell->text;
                            }
                            array_splice($rows[$row], $col, 0, $new_cells);
                        }
                        // replace $cell object with plain text
                        $rows[$row][$col] = $cell->text;
                    }
                }
            }
        }
    }

/////////////////////////////////////////////////
/// print a report in html, text or Excel format
/////////////////////////////////////////////////

// the stuff to print is contained in $table
// which has the following properties:

//   $table->border     border width for the table
//   $table->cellpadding    padding on each cell
//   $table->cellspacing    spacing between cells
//   $table->tableclass class for table
//   $table->width      table width

//   $table->align    is an array of column alignments
//   $table->class    is an array of column classes
//   $table->size     is an array of column sizes
//   $table->wrap     is an array of column wrap/nowrap switches
//   $table->fontsize is an array of fontsizes

//   $table->caption is a caption (=title) for the report
//   $table->head    is an array of headings (all TH cells)
//   $table->data[]  is an array of arrays containing the data (all TD cells)
//          if a row is given as "hr", a "tabledivider" is inserted
//          if a cell is a string, it is assumed to be the cell content
//          a cell can also be an object, thus:
//              $cell->text : the content of the cell
//              $cell->rowspan : the row span of this cell
//              $cell->colspan : the column span of this cell
//          if rowspan or colspan are specified, neighboring cells are shifted accordingly
//   $table->stat[]  is an array of arrays containing the statistics rows (TD and TH cells)
//   $table->foot[]  is an array of arrays containing the footer rows (all TH cells)

//   $table->statheadercols is an array of column numbers which are headers


//////////////////////////////////////////
/// print a report

    function print_report(&$course, &$hotpot, &$tables, &$options) {
        switch ($options['reportformat']) {
            case 'txt':
                $this->print_text_report($course, $hotpot, $tables, $options);
                break;
            case 'xls':
                $this->print_excel_report($course, $hotpot, $tables, $options);
                break;
            default: // 'htm' (and anything else)
                $this->print_html_report($tables);
                break;
        }
    }

    function print_report_start(&$course, &$hotpot, &$options, &$table) {
        switch ($options['reportformat']) {
            case 'txt':
                $this->print_text_start($course, $hotpot, $options);
                break;
            case 'xls':
                $this->print_excel_start($course, $hotpot, $options);
                break;

            case 'htm':
                $this->print_html_start($course, $hotpot, $options);
                break;
        }
    }

    function print_report_cells(&$table, &$options, $zone) {
        switch ($options['reportformat']) {
            case 'txt':
                $fmt = 'text';
                break;
            case 'xls':
                $fmt = 'excel';
                break;
            default: // 'htm' (and anything else)
                $fmt = 'html';
                break;
        }
        $fn = "print_{$fmt}_{$zone}";
        $this->$fn($table, $options);
    }

    function print_report_finish(&$course, &$hotpot, &$options) {
        switch ($options['reportformat']) {
            case 'txt' :
                // do nothing
                break;
            case 'xls':
                $this->print_excel_finish($course, $hotpot, $options);
                break;
            case 'htm':
                $this->print_html_finish($course, $hotpot, $options);
                break;
        }
    }

//////////////////////////////////////////
/// print an html report

    function print_html_report(&$tables) {
        $count = count($tables);
        foreach($tables as $i=>$table) {

            $this->print_html_start($table);
            $this->print_html_head($table);
            $this->print_html_data($table);
            $this->print_html_stat($table);
            $this->print_html_foot($table);
            $this->print_html_finish($table);

            if (($i+1)<$count) {
                print_spacer(30, 10, true);
            }
        }
    }
    function print_html_start(&$table) {

        // default class for the table
        if (empty($table->tableclass)) {
            $table->tableclass = 'generaltable';
        }

        // default classes for TD and TH
        $d = $table->tableclass.'cell';
        $h = $table->tableclass.'header';

        $table->th_side = '<th valign="top" align="right" class="'.$h.'" scope="col">';

        $table->td = array();
        $table->th_top = array();

        if (empty($table->colspan)) {
            if (isset($table->head)) {
                $table->colspan = count($table->head);
            } else if (isset($table->data)) {
                $table->colspan = count($table->data[0]);
            } else if (isset($table->stat)) {
                $table->colspan = count($table->stat);
            } else if (isset($table->foot)) {
                $table->colspan = count($table->foot);
            } else {
                $table->colspan = 0;
            }
        }

        for ($i=0; $i<$table->colspan; $i++) {

            $align = empty($table->align[$i]) ? '' : ' align="'.$table->align[$i].'"';
            $class = empty($table->class[$i]) ? $d : ' class="'.$table->class[$i].'"';
            $class = ' class="'.(empty($table->class[$i]) ? $d : $table->class[$i]).'"';
            $size  = empty($table->size[$i])  ? '' : ' width="'.$table->size[$i].'"';
            $wrap  = empty($table->wrap[$i])  ? '' : ' nowrap="nowrap"';

            $table->th_top[$i] = '<th align="center"'.$size.' class="'.$h.'" nowrap="nowrap" scope="col">';

            $table->td[$i] = '<td valign="top"'.$align.$class.$wrap.'>';

            if (!empty($table->fontsize[$i])) {
                $table->td[$i] .= '<font size="'.$table->fontsize[$i].'">';
            }
        }

        if (empty($table->border)) {
            $table->border = 0;
        }
        if (empty($table->cellpadding)) {
            $table->cellpadding = 5;
        }
        if (empty($table->cellspacing)) {
            $table->cellspacing = 1;
        }
        if (empty($table->width)) {
            $table->width = "80%"; // actually the width of the "simple box"
        }
        if (empty($table->tablealign)) {
            $table->tablealign = "center";
        }

        if (isset($table->start)) {
            print $table->start."\n";
        }

        print_simple_box_start("$table->tablealign", "$table->width", "#ffffff", 0);
        print '<table width="100%" border="'.$table->border.'" valign="top" align="center"  cellpadding="'.$table->cellpadding.'" cellspacing="'.$table->cellspacing.'" class="'.$table->tableclass.'">'."\n";

        if (isset($table->caption)) {
            print '<tr><td colspan="'.$table->colspan.'" class="'.$table->tableclass.'header"><b>'.$table->caption.'</b></td></tr>'."\n";
        }

    }
    function print_html_head(&$table) {
        if (isset($table->head)) {
            print "<tr>\n";
            foreach ($table->head as $i=>$cell) {
                $th = $table->th_top[$i];
                print $th.$cell."</th>\n";
            }
            print "</tr>\n";
        }
    }
    function print_html_data(&$table) {
        if (isset($table->data)) {
            $skipcol = array();
            foreach ($table->data as $cells) {
                print "<tr>\n";
                if (is_array($cells)) {
                    $i = 0; // index on $cells
                    $col = 0; // column index
                    while ($col<$table->colspan && isset($cells[$i])) {
                        if (empty($skipcol[$col])) {
                            $cell = &$cells[$i++];
                            $td = $table->td[$col];
                            if (is_object($cell)) {
                                $text = $cell->text;
                                if (isset($cell->rowspan) && is_numeric($cell->rowspan) && ($cell->rowspan>0)) {
                                    $td = '<td rowspan="'.$cell->rowspan.'"'.substr($td, 3);
                                    // skip cells below this one
                                    $skipcol[$col] = $cell->rowspan-1;
                                }
                                if (isset($cell->colspan) && is_numeric($cell->colspan) && ($cell->colspan>0)) {
                                    $td = '<td colspan="'.$cell->colspan.'"'.substr($td, 3);
                                    // skip cells to the right of this one
                                    for ($c=1; $c<$cell->colspan; $c++) {
                                        if (empty($skipcol[$col+$c])) {
                                            $skipcol[$col+$c] = 1;
                                        } else {
                                            $skipcol[$col+$c] ++;
                                        }
                                    }
                                }
                            } else { // $cell is a string
                                $text = $cell;
                            }
                            print $td.$text.(empty($table->fontsize[$col]) ? '' : '</font>')."</td>\n";
                        } else {
                            $skipcol[$col]--;
                        }
                        $col++;
                    } // end while
                } else if ($cells=='hr') {
                    print '<td colspan="'.$table->colspan.'"><div class="tabledivider"></div></td>'."\n";
                }
                print "</tr>\n";
            }
        }
    }
    function print_html_stat(&$table) {
        if (isset($table->stat)) {
            if (empty($table->statheadercols)) {
                $table->statheadercols = array();
            }
            foreach ($table->stat as $cells) {
                print '<tr>';
                foreach ($cells as $i => $cell) {
                    if (in_array($i, $table->statheadercols)) {
                        $th = $table->th_side;
                        print $th.$cell."</th>\n";
                    } else {
                        $td = $table->td[$i];
                        print $td.$cell."</td>\n";
                    }
                }
                print "</tr>\n";
            }
        }
    }
    function print_html_foot(&$table) {
        if (isset($table->foot)) {
            foreach ($table->foot as $cells) {
                print "<tr>\n";
                foreach ($cells as $i => $cell) {
                    if ($i==0) {
                        $th = $table->th_side;
                        print $th.$cell."</th>\n";
                    } else {
                        $th = $table->th_top[$i];
                        print $th.$cell."</th>\n";
                    }
                }
                print "</tr>\n";
            }
        }
    }
    function print_html_finish(&$table) {
        print "</table>\n";
        print_simple_box_end();

        if (isset($table->finish)) {
            print $table->finish."\n";
        }
    }

//////////////////////////////////////////
/// print a text report

    function print_text_report(&$course, &$hotpot, &$tables, &$options) {
        $this->print_text_start($course, $hotpot, $options);
        foreach ($tables as $table) {
            $this->print_text_head($table, $options);
            $this->print_text_data($table, $options);
            $this->print_text_stat($table, $options);
            $this->print_text_foot($table, $options);
        }
    }
    function print_text_start(&$course, &$hotpot, &$options) {
        $downloadfilename = clean_filename("$course->shortname $hotpot->name.txt");
        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=$downloadfilename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }
    function print_text_head(&$table, &$options) {
        if (isset($table->caption)) {
            $i = strlen($table->caption);
            $data = array(
                array(str_repeat('=', $i)),
                array($table->caption),
                array(str_repeat('=', $i)),
            );
            foreach($data as $cells) {
                $this->print_text_cells($cells, $options);
            }
        }
        if (isset($table->head)) {
            $this->expand_spans($table, 'head');
            $this->print_text_cells($table->head, $options);
        }
    }
    function print_text_data(&$table, &$options) {
        if (isset($table->data)) {
            $this->expand_spans($table, 'data');
            foreach ($table->data as $cells) {
                $this->print_text_cells($cells, $options);
            }
        }
    }
    function print_text_stat(&$table, &$options) {
        if (isset($table->stat)) {
            $this->expand_spans($table, 'stat');
            foreach ($table->stat as $cells) {
                $this->print_text_cells($cells, $options);
            }
        }
    }
    function print_text_foot(&$table, &$options) {
        if (isset($table->foot)) {
            $this->expand_spans($table, 'foot');
            foreach ($table->foot as $cells) {
                $this->print_text_cells($cells, $options);
            }
        }
    }
    function print_text_cells(&$cells, &$options) {

        // do nothing if there are no cells
        if (empty($cells) || is_string($cells)) return;

        // convert to tab-delimted string
        $str = implode("\t", $cells);

        // replace newlines in string
        $str = preg_replace("/\n/", ",", $str);

        // set best newline for this browser (if it hasn't been done already)
        if (empty($this->nl)) {
            $s = &$_SERVER['HTTP_USER_AGENT'];
            $win = is_numeric(strpos($s, 'Win'));
            $mac = is_numeric(strpos($s, 'Mac')) && !is_numeric(strpos($s, 'OS X'));
            $this->nl = $win ? "\r\n" : ($mac ? "\r" : "\n");
        }

        print $str.$this->nl;
    }

//////////////////////////////////////////
/// print an Excel report

    function print_excel_report(&$course, &$hotpot, &$tables, &$options) {
        global $CFG;

        // create Excel workbook
        if (file_exists("$CFG->libdir/excellib.class.php")) {
            // Moodle >= 1.6
            require_once("$CFG->libdir/excellib.class.php");
            $wb = new MoodleExcelWorkbook("-");
            $wsnamelimit = 0; // no limit
        } else {
            // Moodle <= 1.5
            require_once("$CFG->libdir/excel/Worksheet.php");
            require_once("$CFG->libdir/excel/Workbook.php");
            $wb = new Workbook("-");
            $wsnamelimit = 31; // max length in chars
        }

        // send HTTP headers
        $this->print_excel_headers($wb, $course, $hotpot);

        // create one worksheet for each table
        foreach($tables as $table) {
            unset($ws);
            if (empty($table->caption)) {
                $wsname = '';
            } else {
                $wsname = strip_tags($table->caption);
                if ($wsnamelimit && strlen($wsname) > $wsnamelimit) {
                    $wsname = substr($wsname, -$wsnamelimit); // end of string
                    // $wsname = substr($wsname, 0, $wsnamelimit); // start of string
                }
            }
            $ws = &$wb->add_worksheet($wsname);

            $row = 0;
            $this->print_excel_head($wb, $ws, $table, $row, $options);
            $this->print_excel_data($wb, $ws, $table, $row, $options);
            $this->print_excel_stat($wb, $ws, $table, $row, $options);
            $this->print_excel_foot($wb, $ws, $table, $row, $options);
        }

        // close the workbook (and send it to the browser)
        $wb->close();
    }
    function print_excel_headers(&$wb, &$course, &$hotpot) {
        $downloadfilename = clean_filename("$course->shortname $hotpot->name.xls");
        if (method_exists($wb, 'send')) {
            // Moodle >=1.6
            $wb->send($downloadfilename);
        } else {
            // Moodle <=1.5
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=$downloadfilename" );
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
            header("Pragma: public");
        }
    }
    function print_excel_head(&$wb, &$ws, &$table, &$row, &$options) {
        // define format properties
        $properties = array(
            'bold'=>1,
            'align'=>'center',
            'v_align'=>'bottom',
            'text_wrap'=>1
        );

        // expand multi-column and multi-row cells
        $this->expand_spans($table, 'head');

        // print the headings
        $this->print_excel_cells($wb, $ws, $table, $row, $properties, $table->head, $options);
    }
    function print_excel_data(&$wb, &$ws, &$table, &$row, &$options) {
        // do nothing if there are no cells
        if (empty($table->data)) return;

        // define format properties
        $properties = array('text_wrap' => (empty($options['reportwrapdata']) ? 0 : 1));

        // expand multi-column and multi-row cells
        $this->expand_spans($table, 'data');

        // print rows
        foreach ($table->data as $cells) {
            $this->print_excel_cells($wb, $ws, $table, $row, $properties, $cells, $options);
        }
    }
    function print_excel_stat(&$wb, &$ws, &$table, &$row, &$options) {
        // do nothing if there are no cells
        if (empty($table->stat)) return;

        // define format properties
        $properties = array('align'=>'right');

        // expand multi-column and multi-row cells
        $this->expand_spans($table, 'stat');

        // print rows
        $i_count = count($table->stat);
        foreach ($table->stat as $i => $cells) {

            // set border on top and bottom row
            $properties['top'] = ($i==0) ? 1 : 0;
            $properties['bottom'] = ($i==($i_count-1)) ? 1 : 0;

            // print this row
            $this->print_excel_cells($wb, $ws, $table, $row, $properties, $cells, $options, $table->statheadercols);
        }
    }
    function print_excel_foot(&$wb, &$ws, &$table, &$row, &$options) {
        // do nothing if there are no cells
        if (empty($table->foot)) return;

        // define format properties
        $properties = array('bold'=>1, 'align'=>'center');

        // expand multi-column and multi-row cells
        $this->expand_spans($table, 'foot');

        // print rows
        $i_count = count($table->foot);
        foreach ($table->foot as $i => $cells) {

            // set border on top and bottom row
            $properties['top'] = ($i==0) ? 1 : 0;
            $properties['bottom'] = ($i==($i_count-1)) ? 1 : 0;

            // print this footer row
            $this->print_excel_cells($wb, $ws, $table, $row, $properties, $cells, $options);
        }
    }

    function print_excel_cells(&$wb, &$ws, &$table, &$row, &$properties, &$cells, &$options, $statheadercols=NULL) {
        // do nothing if there are no cells
        if (empty($cells) || is_string($cells)) return;

        // print cells
        foreach($cells as $col => $cell) {

            unset($fmt_properties);
            $fmt_properties = $properties;

            if (empty($fmt_properties['text_wrap'])) {
                if (strlen("$cell")>=9) {
                    // long cell value
                    $fmt_properties['align'] = 'left';
                }
            } else {
                if (strlen("$cell")<9 && strpos("$cell", "\n")===false) {
                    // short cell value (wrapping not required)
                    $fmt_properties['text_wrap'] = 0;
                }
            }

            // set bold, if required (for stat)
            if (isset($statheadercols)) {
                $fmt_properties['bold'] = in_array($col, $statheadercols) ? 1 : 0;
                $fmt_properties['align'] = in_array($col, $statheadercols) ? 'right' : $table->align[$col];
            }

            // set align, if required
            if (isset($table->align[$col]) && empty($fmt_properties['align'])) {
                $fmt_properties['align'] =  $table->align[$col];
            }

            // check to see that an identical format object has not already been created
            unset($fmt);

            if (isset($wb->pear_excel_workbook)) {
                // Moodle >=1.6
                $fmt_properties_obj = (object)$fmt_properties;
                foreach ($wb->pear_excel_workbook->_formats as $id=>$format) {
                    if ($format==$fmt_properties_obj) {
                        $fmt = &$wb->pear_excel_workbook->_formats[$id];
                        break;
                    }
                }
            } else {
                // Moodle <=1.5
                foreach ($wb->formats as $id=>$format) {
                    if (isset($format->properties) && $format->properties==$fmt_properties) {
                        $fmt = &$wb->formats[$id];
                        break;
                    }
                }
                if (is_numeric($cell) || empty($options['reportencoding'])) {
                    // do nothing
                } else {
                    $in_charset = '';
                    if (function_exists('mb_convert_encoding')) {
                        $in_charset = mb_detect_encoding($cell, 'auto');
                    }
                    if (empty($in_charset)) {
                        $in_charset = get_string('thischarset');
                    }
                    if ($in_charset != 'ASCII' && function_exists('mb_convert_encoding')) {
                        $cell = mb_convert_encoding($cell, $options['reportencoding'], $in_charset);
                    }
                }
            }

            // create new format object, if necessary (to avoid "too many cell formats" error)
            if (!isset($fmt)) {
                $fmt = &$wb->add_format($fmt_properties);
                $fmt->properties = &$fmt_properties;

                // set vertical alignment
                if (isset($fmt->properties['v_align'])) {
                    $fmt->set_align($fmt->properties['v_align']);
                } else {
                    $fmt->set_align('top'); // default
                }
            }

            // write cell
            if (is_numeric($cell) && !preg_match("/^0./", $cell)) {
                $ws->write_number($row, $col, $cell, $fmt);
            } else {
                $ws->write_string($row, $col, $cell, $fmt);
            }
        } // end foreach $col

        // increment $row
        $row++;
    }
}

?>
