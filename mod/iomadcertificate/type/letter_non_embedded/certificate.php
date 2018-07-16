<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * letter_non_embedded iomadcertificate type
 *
 * @package    mod_iomadcertificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$pdf = new PDF($iomadcertificate->orientation, 'pt', 'LETTER', true, 'UTF-8', false);

$pdf->SetTitle($iomadcertificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
// Landscape
if ($iomadcertificate->orientation == 'L') {
    $x = 28;
    $y = 125;
    $sealx = 590;
    $sealy = 425;
    $sigx = 130;
    $sigy = 440;
    $custx = 133;
    $custy = 440;
    $wmarkx = 100;
    $wmarky = 90;
    $wmarkw = 600;
    $wmarkh = 420;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 792;
    $brdrh = 612;
    $codey = 505;
} else { // Portrait
    $x = 28;
    $y = 170;
    $sealx = 440;
    $sealy = 590;
    $sigx = 85;
    $sigy = 580;
    $custx = 88;
    $custy = 580;
    $wmarkx = 78;
    $wmarky = 130;
    $wmarkw = 450;
    $wmarkh = 480;
    $brdrx = 10;
    $brdry = 10;
    $brdrw = 594;
    $brdrh = 771;
    $codey = 660;
}

// Add images and lines
iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
iomadcertificate_draw_frame_letter($pdf, $iomadcertificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.1);
iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$pdf->SetTextColor(0, 0, 120);
iomadcertificate_print_text($pdf, $x, $y, 'C', 'Helvetica', '', 30, get_string('title', 'iomadcertificate'));
$pdf->SetTextColor(0, 0, 0);
iomadcertificate_print_text($pdf, $x, $y + 55, 'C', 'Times', '', 20, get_string('certify', 'iomadcertificate'));
iomadcertificate_print_text($pdf, $x, $y + 105, 'C', 'Helvetica', '', 30, fullname($USER));
iomadcertificate_print_text($pdf, $x, $y + 155, 'C', 'Helvetica', '', 20, get_string('statement', 'iomadcertificate'));
iomadcertificate_print_text($pdf, $x, $y + 205, 'C', 'Helvetica', '', 20, format_string($course->fullname));
iomadcertificate_print_text($pdf, $x, $y + 255, 'C', 'Helvetica', '', 14, iomadcertificate_get_date($iomadcertificate, $certrecord, $course));
iomadcertificate_print_text($pdf, $x, $y + 283, 'C', 'Times', '', 10, iomadcertificate_get_grade($iomadcertificate, $course));
iomadcertificate_print_text($pdf, $x, $y + 311, 'C', 'Times', '', 10, iomadcertificate_get_outcome($iomadcertificate, $course));
if ($iomadcertificate->printhours) {
    iomadcertificate_print_text($pdf, $x, $y + 339, 'C', 'Times', '', 10, get_string('credithours', 'iomadcertificate') . ': ' . $iomadcertificate->printhours);
}
iomadcertificate_print_text($pdf, $x, $codey, 'C', 'Times', '', 10, iomadcertificate_get_code($iomadcertificate, $certrecord));
$i = 0;
if ($iomadcertificate->printteacher) {
    $context = context_module::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/iomadcertificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            iomadcertificate_print_text($pdf, $sigx, $sigy + ($i * 12), 'L', 'Times', '', 12, fullname($teacher));
        }
    }
}

iomadcertificate_print_text($pdf, $custx, $custy, 'L', null, null, null, $iomadcertificate->customtext);
