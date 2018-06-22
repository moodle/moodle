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
 * A4_embedded iomadcertificate type
 *
 * @package    mod
 * @subpackage iomadcertificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from view.php
}

$pdf = new PDF($iomadcertificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($iomadcertificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
global $DB;

// Landscape
if ($iomadcertificate->orientation == 'L') {
    $x = 40;
    $y = 56;
    $sealx = 40;
    $sealy = 35;
    $sigx = 200;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 40;
    $wmarky = 31;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
    $alignment = 'L';
} else { // Portrait
    $x = 10;
    $y = 76;
    $sealx = 90;
    $sealy = 50;
    $sigx = 130;
    $sigy = 230;
    $custx = 30;
    $custy = 230;
    $wmarkx = 26;
    $wmarky = 58;
    $wmarkw = 158;
    $wmarkh = 170;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 250;
    $alignment = 'C';
}

$certificateseal = "";
$certificatesignature = "";
$certificateborder = "";
$certificatewatermark = "";
$showgrade = true;
$uselogo = true;
$usesignature = true;
$useborder = true;
$usewaterkark = true;

// Get the site defaults
$sitecontext = context_system::instance();
$fs = get_file_storage();
if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad_settings', 'iomadcertificate_logo', 0, 'sortorder DESC, id ASC', false)) {
    if (!count($files) < 1) {
        $file = reset($files);
        unset($files);
        $certificateseal = $file->get_filename();
        $seal_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_logo_');
    }
}
if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad_settings', 'iomadcertificate_signature', 0, 'sortorder DESC, id ASC', false)) {
    if (!count($files) < 1) {
        $file = reset($files);
        unset($files);
        $certificatesignature = $file->get_filename();
        $signature_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_signature_');
    }
}
if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad_settings', 'iomadcertificate_border', 0, 'sortorder DESC, id ASC', false)) {
    if (!count($files) < 1) {
        $file = reset($files);
        unset($files);
        $certificateborder = $file->get_filename();
        $border_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_border_');
    }
}
if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad_settings', 'iomadcertificate_watermark', 0, 'sortorder DESC, id ASC', false)) {
    if (!count($files) < 1) {
        $file = reset($files);
        unset($files);
        $certificatewatermark = $file->get_filename();
        $watermark_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_watermark_');
    }
}

$companyid = 0;
if ($companyid = iomad::is_company_user($certuser)) {
    if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad', 'companycertificateseal', $companyid, 'sortorder DESC, id ASC', false)) {
        if (!count($files) < 1) {
            if (!empty($certificateseal)) {
                @unlink($seal_filename);
            }
            $file = reset($files);
            unset($files);
            $certificateseal = $file->get_filename();
            $seal_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_logo_');
        }
    }

    if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad', 'companycertificatesignature', $companyid, 'sortorder DESC, id ASC', false)) {
        if (!count($files) < 1) {
            if (!empty($certificatesignature)) {
                @unlink($signature_filename);
            }
            $file = reset($files);
            unset($files);
            $certificatesignature = $file->get_filename();
            $signature_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_signature_');
        }
    }

    if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad', 'companycertificateborder', $companyid, 'sortorder DESC, id ASC', false)) {
        if (!count($files) < 1) {
            if (!empty($certificateborder)) {
                @unlink($border_filename);
            }
            $file = reset($files);
            unset($files);
            $certificateborder = $file->get_filename();
            $border_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_border_');
        }
    }

    if ($files = $fs->get_area_files($sitecontext->id, 'local_iomad', 'companycertificatewatermark', $companyid, 'sortorder DESC, id ASC', false)) {
        if (!count($files) < 1) {
            if (!empty($certificatewatermark)) {
                @unlink($watermark_filename);
            }
            $file = reset($files);
            unset($files);
            $certificatewatermark = $file->get_filename();
            $watermark_filename = $file->copy_content_to_temp($iomadcertificate->name, 'iomadcertificate_watermark_');
        }
    }
}

// Get the company certificat design info, if appropriate.
if (!empty($companyid)) {
    if ($companycertificateinfo = $DB->get_record('companycertificate', array('companyid' => $companyid))) {
        $uselogo = $companycertificateinfo->uselogo;
        $usesignature = $companycertificateinfo->usesignature;
        $useborder = $companycertificateinfo->useborder;
        $usewatermark = $companycertificateinfo->usewatermark;
        $showgrade = $companycertificateinfo->showgrade;
    }
}
// Add images and lines
if ($useborder) {
    if (empty($certificateborder)) {
        iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
    } else {
        $pdf->Image($border_filename, $brdrx, $brdry, $brdrw, $brdrh);
        @unlink($border_filename);
    }
}
iomadcertificate_draw_frame($pdf, $iomadcertificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
if ($usewatermark) {
    if (empty($certificatewatermark)) {
        iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
    } else {
        $pdf->Image($watermark_filename, $wmarkx, $wmarky, $wmarkw, $wmarkh);
        @unlink($watermark_filename);
    }
}
$pdf->SetAlpha(1);
if ($uselogo) {
    if (empty($certificateseal)) {
        iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
    } else {
        $pdf->Image($seal_filename, $sealx, $sealy, '', '');
        @unlink($seal_filename);
    }
}
if ($usesignature) {
    if (empty($certificatesignature)) {
        iomadcertificate_print_image($pdf, $iomadcertificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');
    } else {
        $pdf->Image($signature_filename, $sigx, $sigy, '', '');
        @unlink($signature_filename);
    }
}

$gradeinfo = explode(':', iomadcertificate_get_grade($iomadcertificate, $course, $certuser->id));
if (!empty($gradeinfo[1])) {
    $gradestr = $gradeinfo[1];
} else {
    $gradestr = "0";
}

if ($showgrade) {
    $dategradestring = get_string('companyscore', 'iomadcertificate', $gradestr) . ' ' .
    get_string('companydate', 'iomadcertificate', iomadcertificate_get_date($iomadcertificate, $certrecord, $course, $certuser->id));
} else {
    $dategradestring = get_string('companydatecap', 'iomadcertificate', iomadcertificate_get_date($iomadcertificate, $certrecord, $course, $certuser->id));
}

// Add text
$pdf->SetTextColor(0, 0, 120);
$pdf->SetTextColor(0, 0, 0);
iomadcertificate_print_text($pdf, $x, $y + 20, $alignment, 'freeserif', '', 20, get_string('companycertify', 'iomadcertificate'));
iomadcertificate_print_text($pdf, $x, $y + 34, $alignment, 'freeserif', '', 30, fullname($certuser));
iomadcertificate_print_text($pdf, $x, $y + 53, $alignment, 'freeserif', '', 20, get_string('companydetails', 'iomadcertificate'));
iomadcertificate_print_text($pdf, $x, $y + 68, $alignment, 'freeserif', '', 20, $course->fullname);
iomadcertificate_print_text($pdf, $x, $y + 86, $alignment, 'freeserif', '', 14, $dategradestring);
iomadcertificate_print_text($pdf, $x, $codey, 'C', 'freeserif', '', 10, iomadcertificate_get_code($iomadcertificate, $certrecord));
iomadcertificate_print_text($pdf, $custx, $custy, $alignment, null, null, null, $iomadcertificate->customtext);
