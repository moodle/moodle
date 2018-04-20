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
 * Book module language strings
 *
 * @package    mod_book
 * @copyright  2004-2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['modulename'] = 'Book';
$string['modulename_help'] = 'The book module enables a teacher to create a multi-page resource in a book-like format, with chapters and subchapters. Books can contain media files as well as text and are useful for displaying lengthy passages of information which can be broken down into sections.

A book may be used

* To display reading material for individual modules of study
* As a staff departmental handbook
* As a showcase portfolio of student work';
$string['modulename_link'] = 'mod/book/view';
$string['modulenameplural'] = 'Books';
$string['pluginname'] = 'Book';
$string['pluginadministration'] = 'Book administration';

$string['toc'] = 'Table of contents';
$string['chapterandsubchaptersdeleted'] = 'Chapter "{$a->title}" and its {$a->subchapters} subchapters were deleted';
$string['chapterdeleted'] = 'Chapter "{$a->title}" was deleted';
$string['customtitles'] = 'Custom titles';
$string['customtitles_help'] = 'Normally the chapter title is displayed in the table of contents (TOC) AND as a heading above the content.

If the custom titles checkbox is ticked, the chapter title is NOT displayed as a heading above the content. A different title (perhaps longer than the chapter title) may be entered as part of the content.';
$string['chapters'] = 'Chapters';
$string['chaptertitle'] = 'Chapter title';
$string['content'] = 'Content';
$string['deletechapter'] = 'Delete chapter "{$a}"';
$string['editingchapter'] = 'Editing chapter';
$string['eventchaptercreated'] = 'Chapter created';
$string['eventchapterdeleted'] = 'Chapter deleted';
$string['eventchapterupdated'] = 'Chapter updated';
$string['eventchapterviewed'] = 'Chapter viewed';
$string['editchapter'] = 'Edit chapter "{$a}"';
$string['hidechapter'] = 'Hide chapter "{$a}"';
$string['movechapterup'] = 'Move chapter up "{$a}"';
$string['movechapterdown'] = 'Move chapter down "{$a}"';
$string['privacy:metadata'] = 'The book activity module does not store any personal data.';
$string['search:activity'] = 'Book - resource information';
$string['search:chapter'] = 'Book - chapters';
$string['showchapter'] = 'Show chapter "{$a}"';
$string['subchapter'] = 'Subchapter';
$string['navimages'] = 'Images';
$string['navoptions'] = 'Available options for navigational links';
$string['navoptions_desc'] = 'Options for displaying navigation on the book pages';
$string['navstyle'] = 'Style of navigation';
$string['navstyle_help'] = '* Images - Icons are used for navigation
* Text - Chapter titles are used for navigation';
$string['navtext'] = 'Text';
$string['navtoc'] = 'TOC Only';
$string['nocontent'] = 'No content has been added to this book yet.';
$string['numbering'] = 'Chapter formatting';
$string['numbering_help'] = '* None - Chapter and subchapter titles have no formatting
* Numbers - Chapters and subchapter titles are numbered 1, 1.1, 1.2, 2, ...
* Bullets - Subchapters are indented and displayed with bullets in the table of contents
* Indented - Subchapters are indented in the table of contents';
$string['numbering0'] = 'None';
$string['numbering1'] = 'Numbers';
$string['numbering2'] = 'Bullets';
$string['numbering3'] = 'Indented';
$string['numberingoptions'] = 'Available options for chapter formatting';
$string['numberingoptions_desc'] = 'Options for displaying chapters and subchapters in the table of contents';
$string['addafter'] = 'Add new chapter';
$string['confchapterdelete'] = 'Do you really want to delete this chapter?';
$string['confchapterdeleteall'] = 'Do you really want to delete this chapter and all its subchapters?';
$string['top'] = 'top';
$string['navprev'] = 'Previous';
$string['navprevtitle'] = 'Previous: {$a}';
$string['navnext'] = 'Next';
$string['navnexttitle'] = 'Next: {$a}';
$string['navexit'] = 'Exit book';
$string['book:addinstance'] = 'Add a new book';
$string['book:read'] = 'View book';
$string['book:edit'] = 'Edit book chapters';
$string['book:viewhiddenchapters'] = 'View hidden book chapters';
$string['errorchapter'] = 'Error reading chapter of book.';

$string['page-mod-book-x'] = 'Any book module page';
$string['subchapternotice'] = '(Only available once the first chapter has been created)';
$string['subplugintype_booktool'] = 'Book tool';
$string['subplugintype_booktool_plural'] = 'Book tools';

$string['removeallbooktags'] = 'Remove all book tags';
$string['tagarea_book_chapters'] = 'Book chapters';
$string['tagsdeleted'] = 'Book tags have been deleted';
$string['tagtitle'] = 'See the "{$a}" tag';
