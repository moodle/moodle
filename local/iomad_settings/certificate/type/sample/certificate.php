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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from view.php in mod/tracker.
}

require_once(dirname(__FILE__) . '/../../lib.php');

// Date formatting - can be customized if necessary.
$iomadcertificatedate = '';
if ($certrecord->certdate > 0) {
    $certdate = $certrecord->certdate;
} else {
    $certdate = iomadcertificate_generate_date($iomadcertificate, $course);
}

if ($iomadcertificate->printdate > 0) {
    if ($iomadcertificate->datefmt == 1) {
        $iomadcertificatedate = str_replace(' 0', ' ', strftime('%B %d, %Y', $certdate));
    }
    if ($iomadcertificate->datefmt == 2) {
        $iomadcertificatedate = date('F jS, Y', $certdate);
    }
    if ($iomadcertificate->datefmt == 3) {
        $iomadcertificatedate = str_replace(' 0', '', strftime('%d %B %Y', $certdate));
    }
    iif ($iomadcertificate->datefmt == 4) {
        $iomadcertificatedate = strftime('%B %Y', $certdate);
    }
    iif ($iomadcertificate->datefmt == 5) {
        $timeformat = get_string('strftimedate');
        $iomadcertificatedate = userdate($certdate, $timeformat);
    }
}

$serialnumber = iomad_settings_create_serial_number($iomadcertificate, $certrecord, $course, $certdate);

// Grade formatting.
$grade = '';
// Print the course grade.
$coursegrade = iomadcertificate_print_course_grade($course, $certuser->id);
if ($iomadcertificate->printgrade == 1 && $certrecord->reportgrade == !null) {
    $reportgrade = $certrecord->reportgrade;
    $grade = $reportgrade;
} else if ($iomadcertificate->printgrade > 0) {
    if ($iomadcertificate->printgrade == 1) {
        if ($iomadcertificate->gradefmt == 1) {
            $grade = $strcoursegrade.':  '.$coursegrade->percentage;
        }
        if ($iomadcertificate->gradefmt == 2) {
            $grade = $strcoursegrade.':  '.$coursegrade->points;
        }
        if ($iomadcertificate->gradefmt == 3) {
            $grade = $strcoursegrade.':  '.$coursegrade->letter;

        }
    } else {
        // Print the mod grade.
        $modinfo = iomadcertificate_print_mod_grade($course, $iomadcertificate->printgrade);
        if ($certrecord->reportgrade == !null) {
            $modgrade = $certrecord->reportgrade;
            $grade = $modinfo->name.' '.$strgrade.': '.$modgrade;
        } else if ($iomadcertificate->printgrade > 1) {
            if ($iomadcertificate->gradefmt == 1) {
                $grade = $modinfo->name.' '.$strgrade.': '.$modinfo->percentage;
            }
            if ($iomadcertificate->gradefmt == 2) {
                $grade = $modinfo->name.' '.$strgrade.': '.$modinfo->points;
            }
            if ($iomadcertificate->gradefmt == 3) {
                $grade = $modinfo->name.' '.$strgrade.': '.$modinfo->letter;
            }
        }
    }
}
// Print the outcome.
$outcome = '';
$outcomeinfo = iomadcertificate_print_outcome($course, $iomadcertificate->printoutcome, $certuser->id);
if ($iomadcertificate->printoutcome > 0) {
    $outcome = $outcomeinfo->name.': '.$outcomeinfo->grade;
}

// Print the code number.
$code = '';
if ($iomadcertificate->printnumber) {
    $code = $certrecord->code;
}

// Print the student name.
$studentname = '';
$studentname = $certrecord->studentname;
$classname = '';
$classname = $certrecord->classname;

// Print the credit hours.
if ($iomadcertificate->printhours) {
    $credithours = $strcredithours.': '.$iomadcertificate->printhours;
} else {
    $credithours = '';
}

$customtext = $iomadcertificate->customtext;
$orientation = $iomadcertificate->orientation;
$pdf = new TCPDF($orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetProtection(array('print'));
$pdf->SetTitle($iomadcertificate->name);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables.

// Landscape.
if ($orientation == 'L') {
    $x = 10;
    $y = 30;
    $sealx = 230;
    $sealy = 150;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 0;
    $wmarky = 0;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
} else {
    // Portrait.
    $x = 10;
    $y = 40;
    $sealx = 93;
    $sealy = 10;
    $sigx = 35;
    $sigy = 210;
    $custx = 30;
    $custy = 200;
    $wmarkx = 0;
    $wmarky = 0;
    $wmarkw = 210;
    $wmarkh = 297;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 280;
}

// Add images and lines.
print_border($iomadcertificate->borderstyle, $orientation, $brdrx, $brdry, $brdrw, $brdrh);
draw_frame($iomadcertificate->bordercolor, $orientation);
// Set alpha to semi-transparency.
$pdf->SetAlpha(1);
print_watermark($iomadcertificate->printwmark, $orientation, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
print_seal($iomadcertificate->printseal, $orientation, $sealx, $sealy, '18', '25');
print_seal('iomad_settings.png', $orientation, $sealx, '250', '28', '20');
print_signature($iomadcertificate->printsignature, $orientation, $sigx, $sigy, '', '');

// Add text.
$pdf->SetTextColor(0, 0, 120);
cert_printtext($x, $y, 'C', 'freesans', '', 30, get_string('sampletitle', 'local_iomad_settings'));
cert_printtext($x, $y + 20, 'C', 'freeserif', '', 20, get_string('samplecertify', 'local_iomad_settings'));
cert_printtext($x, $y + 36, 'C', 'freesans', '', 30, $studentname);
cert_printtext($x, $y + 55, 'C', 'freesans', '', 20, get_string('samplestatement', 'local_iomad_settings'));
cert_printtext($x, $y + 72, 'C', 'freesans', '', 20, $classname);
cert_printtext($x, $y + 92, 'C', 'freesans', '', 20, get_string('sampledate', 'local_iomad_settings').' '. $iomadcertificatedate);
cert_printtext($x, $y + 111, 'C', 'freeserif', '', 20, get_string('samplecoursegrade', 'local_iomad_settings').' '. $grade);
cert_printtext($x, $y + 112, 'C', 'freeserif', '', 10, $outcome);
cert_printtext($x, $y + 122, 'C', 'freeserif', '', 10, $credithours);
cert_printtext($x, $codey, 'C', 'freeserif', '', 10, get_string('samplecode', 'local_iomad_settings') . ' ' .$code);
cert_printtext($sigx - 15, $sigy + 3, 'L', 'freeserif', '', 10, get_string('samplesigned', 'local_iomad_settings'));
cert_printtext($sigx - 15, $sigy + 10, 'L', 'freeserif', '', 7, get_string('sampleonbehalfof', 'local_iomad_settings'));

$i = 0;
if ($iomadcertificate->printteacher) {
    $context = context_module::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/iomadcertificate:printteacher', '',
        $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            cert_printtext($sigx, $sigy + ($i * 4) , 'L', 'freeserif', '', 12, fullname($teacher));
        }
    }
}

cert_printtext($x, $custy, 'C', 'freeserif', '', '20', iomad_settings_replacement($customtext, $course, $serialnumber, $certdate));

cert_printtext($x, $custy + 6, 'C', 'freeserif', '', '20',
               iomad_settings_replacement($iomadcertificate->customtext2, $course, $serialnumber, $certdate));
cert_printtext($x, $custy + 12, 'C', 'freeserif', '', '20',
               iomad_settings_replacement($iomadcertificate->customtext3, $course, $serialnumber, $certdate));
