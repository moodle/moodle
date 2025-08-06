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
 * prints the tabbed bar
 *
 * @author Peter Dias
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_glossary
 * @copyright 2021 Peter Dias
 */
defined('MOODLE_INTERNAL') || die;

    echo html_writer::start_div('entrybox');
    if (!isset($category)) {
        $category = "";
    }


    switch ($tab) {
        case GLOSSARY_CATEGORY_VIEW:
            glossary_print_categories_menu($cm, $glossary, $hook, $category);
        break;
        case GLOSSARY_APPROVAL_VIEW:
            glossary_print_approval_menu($cm, $glossary, $mode, $hook, $sortkey, $sortorder);
        break;
        case GLOSSARY_AUTHOR_VIEW:
            $search = "";
            glossary_print_author_menu($cm, $glossary, "author", $hook, $sortkey, $sortorder, 'print');
        break;
        case GLOSSARY_IMPORT_VIEW:
            $search = "";
            $l = "";
            glossary_print_import_menu($cm, $glossary, 'import', $hook, $sortkey, $sortorder);
        break;
        case GLOSSARY_EXPORT_VIEW:
            $search = "";
            $l = "";
            glossary_print_export_menu($cm, $glossary, 'export', $hook, $sortkey, $sortorder);
        break;
        case GLOSSARY_DATE_VIEW:
            if (!$sortkey) {
                $sortkey = 'UPDATE';
            }
            if (!$sortorder) {
                $sortorder = 'desc';
            }
            glossary_print_alphabet_menu($cm, $glossary, "date", $hook, $sortkey, $sortorder);
        break;
        case GLOSSARY_STANDARD_VIEW:
        default:
            glossary_print_alphabet_menu($cm, $glossary, "letter", $hook, $sortkey, $sortorder);
            if ($mode == 'search' and $hook) {
                echo html_writer::tag('div', "$strsearch: $hook");
            }
        break;
    }
    echo html_writer::empty_tag('hr');
    echo html_writer::end_div();
