<?php
function report_trainingsessions_print_pdf(&$pdf, $structure, &$aggregate, &$done, &$row, $level = 0) {

    $config = get_config('report_trainingsessions');

    //1586005530

    if (empty($structure)) {
        $str = get_string('nostructure', 'report_trainingsessions');
        $pdf->MultiCell(0,0,utf8_decode($str));
        return;
    }

    // Makes a blank dataobject.
    if (!isset($dataobject)) {
        $dataobject = new StdClass;
        $dataobject->elapsed = 0;
        $dataobject->events = 0;
    }

    if (is_array($structure)) {
        // Recurse in sub structures.
        foreach ($structure as $element) {
            if (isset($element->instance) && empty($element->instance->visible)) {
                // Non visible items should not be displayed.
                continue;
            }

            $level++;
            $res = report_trainingsessions_print_pdf($pdf, $element, $aggregate, $done, $row, $level);
            $level--;
            $dataobject->elapsed += $res->elapsed;
            $dataobject->events += (0 + @$res->events);
        }
    } else {
        // Prints a single row.

        if (!isset($element->instance) || !empty($element->instance->visible)) {
            // Non visible items should not be displayed.
            if (!empty($structure->name)) {

                // Write element name.
                $indent = str_pad('', 3 * $level, '');
                $str = $indent.shorten_text(strip_tags($structure->name), 85);

                if($structure->plugintype == "section"){
                    $pdf->Ln(2);
                    $pdf->SetFont('Arial','B',12);
                    $pdf->MultiCell(0,10,utf8_decode($str));
                }else{
                    $pdf->SetFont('Arial','',10);
                    $pdf->MultiCell(0,2,utf8_decode($str));
                }




                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id]))  {
                    $done++;
                    $dataobject = $aggregate[$structure->type][$structure->id];
                }
           

                // Saves the current row for post writing aggregates.
                $thisrow = $row;
                $row++;
                if (!empty($structure->subs)) {
                    $res = report_trainingsessions_print_pdf($pdf, $structure->subs, $aggregate, $done,
                                                             $row, $level + 1);
                     $dataobject->elapsed += $res->elapsed;
                     $dataobject->events += $res->events;
                }

                // Firstaccess.
                // $fa = @$aggregate[$structure->type][$structure->id]->firstaccess;
                // if (!empty($fa)) {
                //     $pdf->SetX(72);
                //     $pdf->MultiCell(90,4,utf8_decode((float)$fa));
                // }

                // Elapsed.
                $convertedelapsed = report_trainingsessions_format_time($dataobject->elapsed, 'pdf');
                
                $pdf->SetX(175);
                $pdf->MultiCell(0,4,$convertedelapsed);
             

                // if (!empty($config->showhits)) {
                //     $pdf->SetX(72);
                //     $pdf->MultiCell(0,1,utf8_decode($dataobject->events));
                // }
            } else {
                
                // It is only a structural module that should not impact on level.
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                    $dataobject = $aggregate[$structure->type][$structure->id];
                }

                if (!empty($structure->subs)) {
                    $res = report_trainingsessions_print_pdf($pdf, $structure->subs, $aggregate, $done,
                                                             $row, $level);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }
            }
        }
    }


    return $dataobject;
}
/**
 * Print session table in an initialied worksheet
 *
 * @param object $worksheet
 * @param int $row
 * @param array $sessions
 * @param object $course
 * @param object $xlsformats
 */
function report_trainingsessions_print_sessions_pdf(&$pdf, $row, $sessions, $courseorid, &$xlsformats) {
    global $CFG;

    if (is_object($courseorid)) {
        $courseid = $courseorid->id;
    } else {
        $courseid = $courseorid;
    }

    $hasltc = false;
    if (file_exists($CFG->dirroot.'/report/learningtimecheck/lib.php')) {
        $config = get_config('report_traningsessions');
        if (!empty($config->enablelearningtimecheckcoupling)) {
            require_once($CFG->dirroot.'/report/learningtimecheck/lib.php');
            $ltcconfig = get_config('report_learningtimecheck');
            $hasltc = true;
        }
    }

    $totalelapsed = 0;

    if (!empty($sessions)) {
        foreach ($sessions as $session) {

            if (empty($session->courses) || ($courseid && !array_key_exists($courseid, $session->courses))) {
                // Omit all sessions not visiting this course.
                continue;
            }

            // Fix eventual missing session end.
            if (!isset($session->sessionend) && empty($session->elapsed)) {
                // This is a "not true" session reliquate. Ignore it.
                continue;
            }

            // Fix all incoming sessions. possibly cropped by threshold effect.
            $session->sessionend = $session->sessionstart + $session->elapsed;

            $daysessions = report_trainingsessions_splice_session($session);

            foreach ($daysessions as $s) {

                if ($hasltc && !empty($config->enablelearningtimecheckcoupling)) {

                    if (!empty($ltcconfig->checkworkingdays) || !empty($ltcconfig->checkworkinghours)) {
                        if (!empty($ltcconfig->checkworkingdays)) {
                            if (!report_learningtimecheck_is_valid($fakecheck)) {
                                continue;
                            }
                        }

                        if (!empty($ltcconfig->checkworkinghours)) {
                            if (!report_learningtimecheck_check_day($fakecheck, $ltcconfig)) {
                                continue;
                            }

                            report_learningtimecheck_crop_session($s, $ltcconfig);
                            if ($s->sessionstart && $s->sessionend) {
                                // Segment was not invalidated, possibly shorter than original.
                                $s->elapsed = $s->sessionend - $s->sessionstart;
                            } else {
                                // Croping results concluded into an invalid segment.
                                continue;
                            }
                        }
                    }
                }

                $pdf->MultiCell(0,0,strftime("%d/%m/%Y %H:%M",@$s->sessionstart));
                if (!empty($s->sessionend)) {
                    $pdf->SetX(90);
                    $pdf->MultiCell(0,0,strftime("%d/%m/%Y %H:%M",@$s->sessionend));
                    
                }
                $pdf->SetX(170);
                $pdf->MultiCell(0,0,format_time(0 + @$s->elapsed));
                $elapsed = report_trainingsessions_format_time(0 + @$s->elapsed, 'other');
                $pdf->Ln(4);
                $totalelapsed += 0 + @$s->elapsed;

                $row++;
            }
        }
    }
    return $totalelapsed;
}

?>