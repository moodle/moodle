<?php
// This file is part of Book plugin for Moodle - http://moodle.org/
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
 * Book module language strings
 *
 * @package    mod_book
 * @copyright  2004-2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['modulename'] = 'Book';
$string['modulenameplural'] = 'Books';
$string['modulename_help'] = 'Book is a simple multipage study material.';
$string['pluginname'] = 'Book';
$string['pluginadministration'] = 'Book administration';

$string['toc'] = 'Table of contents';
$string['faq'] = 'Book FAQ';
$string['faq_help'] = '
*Why only two levels?*

Two levels are generally enough for all books, three levels would lead to poorly structured documents. Book module is designed for
creation of short multipage study materials. It is usually better to use PDF format for longer documents. The easiest way to create PDFs are
virtual printers (see
<a  href="http://sector7g.wurzel6.de/pdfcreator/index_en.htm"  target="_blank">PDFCreator</a>,
<a  href="http://fineprint.com/products/pdffactory/index.html"  target="_blank">PDFFactory</a>,
<a  href="http://www.adobe.com/products/acrobatstd/main.html"  target="_blank">Adobe Acrobat</a>,
etc.).

*Can students edit books?*

Only teachers can create and edit books. There are no plans to implement student editing for books, but somebody may create something
similar for students (Portfolio?). The main reason is to keep Book module as simple as possible.

*How do I search the books?*

At present there is only one way, use browser\'s search capability in print page. Global searching is now possible only in Moodle forums.
It would be nice to have global searching for all resources including books, any volunteers?

*My titles do not fit on one line.*

Either rephrase your titles or ask your site admin to change TOC
width. It is defined globally for all books in module configuration
page.';

$string['customtitles'] = 'Custom titles';
$string['customtitles_help'] = 'Chapter titles are displayed automatically only in TOC.';

$string['chapters'] = 'Chapters';
$string['editingchapter'] = 'Editing chapter';
$string['chaptertitle'] = 'Chapter title';
$string['content'] = 'Content';
$string['subchapter'] = 'Subchapter';

$string['numbering'] = 'Chapter numbering';
$string['numbering_help'] = '* None - chapter and subchapter titles are not formatted at all, use if you want to define special numbering styles. For example letters: in chapter title type "A First Chapter", "A.1 Some Subchapter",...
* Numbers - chapters and subchapters are numbered (1, 1.1, 1.2, 2, ...)
* Bullets - subchapters are indented and displayed with bullets
* Indented - subchapters are indented';

$string['numbering0'] = 'None';
$string['numbering1'] = 'Numbers';
$string['numbering2'] = 'Bullets';
$string['numbering3'] = 'Indented';
$string['numberingoptions'] = 'Available numbering options';
$string['numberingoptions_help'] = 'Select numbering options that should be available when creating new books.';

$string['chapterscount'] = 'Chapters';

$string['addafter'] = 'Add new chapter';
$string['confchapterdelete'] = 'Do you really want to delete this chapter?';
$string['confchapterdeleteall'] = 'Do you really want to delete this chapter and all its subchapters?';

$string['top'] = 'top';

$string['navprev'] = 'Previous';
$string['navnext'] = 'Next';
$string['navexit'] = 'Exit book';

$string['book:addinstance'] = 'Add a new book';
$string['book:read'] = 'Read book';
$string['book:edit'] = 'Edit book chapters';
$string['book:viewhiddenchapters'] = 'View hidden book chapters';

$string['errorchapter'] = 'Error reading book chapter.';

$string['page-mod-book-x'] = 'Any book module page';

$string['missingfilemanagement'] = 'Dear users of Book module, I supposed you have already notised that it is not possible to delete or manage files used in Book chapters. Please vote in {$a} to get this fixed, thanks. Petr Škoda';