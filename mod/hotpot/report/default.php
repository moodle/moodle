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

	function display($cm, $course, $hotpot) {	 /// This function just displays the report
		return true;
	}

	function add_question_headings(&$questions, &$table, $align='center', $size=50, $wrap=false, $fontsize=0) {
		$count = count($questions);
		$questionids = array_keys($questions);
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

	function remove_column(&$col, &$table) {

		if (is_array($table)) {
			unset($table[$col]);
			$table = array_values($table);

		} else if (is_object($table)) {
			$vars = get_object_vars($table);
			foreach ($vars as $name=>$value) {
				switch ($name) {
					case 'data' :
					case 'stat' :
					case 'foot' :
						$array = &$table->$name;
						$count = count($array);
						for ($row=0; $row<$count; $row++) {
							$this->remove_column($col, $array[$row]);
						}
						break;
					case 'head' :
					case 'align' :
					case 'class' :
					case 'fontsize' :
					case 'size' :
					case 'wrap' :
						$this->remove_column($col, $table->$name);
						break;
					case 'statheadercols' :
						$array = &$table->$name;
						$count = count($array);
						for ($i=0; $i<$count; $i++) {
							if ($array[$i]>=$col) {
								$array[$i] --;
							}
						}
						break;
				} // end switch
			} // end foreach
		} // end if
	} // end function

/////////////////////////////////////////////////
/// print a report in html, text or Excel format
/////////////////////////////////////////////////

// the stuff to print is contained in $table
// which has the following properties:

//	 $table->border	 	border width for the table
//	 $table->cellpadding	padding on each cell
//	 $table->cellspacing	spacing between cells
//	 $table->tableclass	class for table
//	 $table->width	 	table width

//	 $table->align	  is an array of column alignments
//	 $table->class	  is an array of column classes
//	 $table->size	  is an array of column sizes
//	 $table->wrap	  is an array of column wrap/nowrap switches
//	 $table->fontsize is an array of fontsizes

//	 $table->caption is a caption (=title) for the report
//	 $table->head	 is an array of headings (all TH cells)
//	 $table->data[]	 is an array of arrays containing the data (all TD cells)
//			if a row is given as "hr", a "tabledivider" is inserted
//	 $table->stat[]	 is an array of arrays containing the statistics rows (TD and TH cells)
//	 $table->foot[]	 is an array of arrays containing the footer rows (all TH cells)

//	 $table->statheadercols	is an array of column numbers which are headers


//////////////////////////////////////////
/// print an html report

	function print_html(&$cm, &$table, $mode, $tablename='') {

		$this->print_html_table($table);
		
		// print buttons, if required	
		if (isset($mode) && !empty($table)) {

			// button options
			$options = array(
				'id'=>"$cm->id",
				'mode'=>$mode,
				'noheader'=>'yes'
			);
			if ($tablename) {
				$options['tablename'] = $tablename;
			}
			
			print '<table border="0" align="center"><tr>';
			print '<td>';
			
			$options["download"] = "xls";
			print_single_button("report.php", $options, get_string("downloadexcel"));
			
			print '</td><td>';
			
			$options["download"] = "txt";
			print_single_button("report.php", $options, get_string("downloadtext"));
			
			print '</table>';
		}
	}
	function print_html_table($table) {
		global $THEME;

		// do nothing if the table is empty
		if (empty($table) || $this->set_colspan($table)==0) return true;
		
		// default class for the table
		if (empty($table->tableclass)) {
			$table->tableclass = 'generaltable';
		}

		// default classes for TD and TH
		$d = $table->tableclass.'cell';
		$h = $table->tableclass.'header';
		
		$th_side = NULL;
		$th_side->start = '<th valign="top" align="right" class="'.$h.'">';
		$th_side->end   = '</th>'."\n";

		$td = array();
		$th_top = array();

		for ($i=0; $i<$table->colspan; $i++) {

			$align = empty($table->align[$i]) ? '' : ' align="'.$table->align[$i].'"';
			$class = empty($table->class[$i]) ? $d : ' class="'.$table->class[$i].'"';
			$size  = empty($table->size[$i])  ? '' : ' width="'.$table->size[$i].'"';
			$wrap  = empty($table->wrap[$i])  ? '' : ' nowrap="nowrap"';

			$th_top[$i]->start = '<th align="center"'.$size.' class="'.$h.'" nowrap="nowrap">';
			$th_top[$i]->end   = '</th>'."\n";

			$td[$i]->start = '<td valign="top"'.$align.$class.$wrap.'>';
			$td[$i]->end   = '</td>'."\n";

			if (!empty($table->fontsize[$i])) {
					$td[$i]->start .= '<font size="'.$table->fontsize[$i].'">';
					$td[$i]->end = '</font>'.$td[$i]->end;
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

		print_simple_box_start("center", "$table->width", "#ffffff", 0);
		print '<table width="100%" border="'.$table->border.'" valign="top" align="center"  cellpadding="'.$table->cellpadding.'" cellspacing="'.$table->cellspacing.'" class="'.$table->tableclass.'">'."\n";

		if (isset($table->caption)) {
			print '<tr><td colspan="'.$table->colspan.'" class="'.$table->tableclass.'header"><b>'.$table->caption.'</b></td></tr>'."\n";
		}

		if (isset($table->head)) {
			print '<tr>'."\n";
			foreach ($table->head as $i => $cell) {
				print $th_top[$i]->start.$cell.$th_top[$i]->end;
			}
			print '</tr>'."\n";
		}

		if (isset($table->data)) {
			foreach ($table->data as $cells) {
				print '<tr>'."\n";
				if (is_array($cells))	{
					foreach ($cells as $i => $cell) {
						print $td[$i]->start.$cell.$td[$i]->end;
					}
				} else if ($cells == 'hr') {
					print '<td colspan="'.$table->colspan.'"><div class="tabledivider"></div></td>'."\n";
				}
				print '</tr>'."\n";
			}
		}

		if (isset($table->stat)) {
			if (empty($table->statheadercols)) {
				$table->statheadercols = array();
			}
			foreach ($table->stat as $cells) {
				print '<tr>'."\n";
				foreach ($cells as $i => $cell) {
					if (in_array($i, $table->statheadercols)) {
						print $th_side->start.$cell.$th_side->end;
					} else {
						print $td[$i]->start.$cell.$td[$i]->end;
					}
				}
				print '</tr>'."\n";
			}
		}

		if (isset($table->foot)) {
			foreach ($table->foot as $cells) {
				print '<tr>'."\n";
				foreach ($cells as $i => $cell) {
					if ($i==0) {
						print $th_side->start.$cell.$th_side->end;
					} else {
						print $th_top[$i]->start.$cell.$th_top[$i]->end;
					}
				}
				print '</tr>'."\n";
			}
		}
		print '</table>'."\n";
		print_simple_box_end();

		return true;
	}
	function set_colspan(&$table) {
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
		return $table->colspan;
	}

//////////////////////////////////////////
/// print a text report

	function print_text(&$course, &$hotpot, &$table) {
		$this->print_text_headers($course, $hotpot);
		$this->print_text_head($table);
		$this->print_text_data($table);
		$this->print_text_stat($table);
		$this->print_text_foot($table);
	}
	function print_text_headers($course, $hotpot) {
		header("Content-Type: application/download\n"); 
		header("Content-Disposition: attachment; filename=$course->shortname $hotpot->name.txt");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
	}
	function print_text_head(&$table) {
		if (isset($table->head)) {	
			$this->print_text_cells($table->head);
		}
	}
	function print_text_data(&$table) {
		if (isset($table->data)) {	
			foreach ($table->data as $cells) {
				$this->print_text_cells($cells);
			}
		}
	}		
	function print_text_stat(&$table) {
		if (isset($table->stat)) {	
			foreach ($table->stat as $cells) {
				$this->print_text_cells($cells);
			}
		}
	}
	function print_text_foot(&$table) {
		if (isset($table->foot)) {	
			foreach ($table->foot as $cells) {
				$this->print_text_cells($cells);
			}
		}
	}
	function print_text_cells(&$cells) {

		// do nothing if there are no cells
		if (empty($cells)) return;

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

	function print_excel(&$course, &$hotpot, &$table) {
		global $CFG;
		require_once("$CFG->libdir/excel/Worksheet.php");
		require_once("$CFG->libdir/excel/Workbook.php");

		// array of format objects (one for each cell)
		$fmt = array();

		// Create a new workbook and worksheet
		$wb = new Workbook("-");
		$ws = &$wb->add_worksheet(get_string('reportsimplestat', 'quiz'));
		
		$this->print_excel_headers($course, $hotpot);
		$this->print_excel_head($wb, $ws, $table, $fmt);
		$this->print_excel_data($wb, $ws, $table, $fmt);
		$this->print_excel_stat($wb, $ws, $table, $fmt);
		$this->print_excel_foot($wb, $ws, $table, $fmt);

		$wb->close();
	}
	function print_excel_headers(&$course, &$instance) {
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$course->shortname $instance->name.xls" );
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
	}
	function print_excel_head(&$wb, &$ws, &$table, &$fmt) {

		// format properties
		$properties = array('bold'=>1, 'align'=>'center');

		// print the headings
		$this->print_excel_cells($wb, $ws, $table, $fmt, $properties, $table->head);
	}		
	function print_excel_data(&$wb, &$ws, &$table, &$fmt) {

		// do nothing if there are no cells
		if (empty($table->data)) return;
		
		// format properties
		$properties = array();

		// print the data cells
		foreach ($table->data as $cells) {
			$this->print_excel_cells($wb, $ws, $table, $fmt, $properties, $cells, array());
		}
	}		
	function print_excel_stat(&$wb, &$ws, &$table, &$fmt) {

		// do nothing if there are no cells
		if (empty($table->stat)) return;

		// format properties
		$properties = array('align'=>'right');

		$i_count = count($table->stat);
		foreach ($table->stat as $i => $cells) {

			// set border on top and bottom row
			$properties['top'] = ($i==0) ? 1 : 0;
			$properties['bottom'] = ($i==($i_count-1)) ? 1 : 0;

			// print this row of statistics
			$this->print_excel_cells($wb, $ws, $table, $fmt, $properties, $cells, $table->statheadercols);
		}
	}		
	function print_excel_foot(&$wb, &$ws, &$table, &$fmt) {

		// do nothing if there are no cells
		if (empty($table->foot)) return;

		// format properties
		$properties = array('bold'=>1, 'align'=>'center');

		$i_count = count($table->foot);
		foreach ($table->foot as $i => $cells) {

			// set border on top and bottom row
			$properties['top'] = ($i==0) ? 1 : 0;
			$properties['bottom'] = ($i==($i_count-1)) ? 1 : 0;

			// print this footer row
			$this->print_excel_cells($wb, $ws, $table, $fmt, $properties, $cells);
		}
	}		
	function print_excel_cells(&$wb, &$ws, &$table, &$fmt, &$properties, &$cells, $statheadercols=NULL) {

		// do nothing if there are no cells
		if (empty($cells)) return;

		// next row
		$row = count($fmt);

		// initialize formats for this row
		$fmt[$row] = array();

		foreach($cells as $col => $cell) {

			if ($row==0) {
				// set column width (excel-width = web-width / 5)
				$width = (isset($table->size[$col]) && is_numeric($table->size[$col])) ? ceil($table->size[$col]/5) : ($col==0 ? 30 : 15);
				//$ws->set_column($col, $col, $width);
			}
			
			// set text wrap if $cell is a string and contains a newline
			if (is_string($cell) && preg_match("/\n/", $cell)) {
				$properties['text_wrap'] = 1;
			} else {
				$properties['text_wrap'] = 0;
			}

			// set bold, if required (for stat)
			if (isset($statheadercols)) {
				$properties['bold'] = in_array($col, $statheadercols) ? 1 : 0;
				$properties['align'] = in_array($col, $statheadercols) ? 'right' : $table->align[$col];
			}

			// create (a reference to) a new format object
			$fmt[$row][$col] = &$wb->add_format($properties);
	
			// set vertical alignment
			$fmt[$row][$col]->set_align('top');

			// write cell
			if (is_numeric($cell)) {
				$ws->write_number($row, $col, $cell, $fmt[$row][$col]);
			} else {
				$ws->write_string($row, $col, $cell, $fmt[$row][$col]);
			}
		}
	}
}

?>
