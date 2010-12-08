<?php

$string['modulename'] = 'Book';
$string['modulenameplural'] = 'Books';
$string['modulename_help'] = 'Book is a simple multipage study material.';
$string['pluginname'] = 'Book';
$string['pluginadministration'] = 'Book administration';

$string['toc'] = 'Table of Contents';
$string['tocwidth'] = 'Select width of the Table of Contents for all books.';
$string['book_tocwidth'] = 'Table of Contents width';
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

$string['disableprinting'] = 'Disable Printing';
$string['disableprinting_help'] = 'Hide print icons.';
$string['customtitles'] = 'Custom Titles';
$string['customtitles_help'] = 'Chapter titles are displayed automatically only in TOC.';

$string['editingchapter'] = 'Editing chapter';
$string['chaptertitle'] = 'Chapter Title';
$string['content'] = 'Content';
$string['subchapter'] = 'Subchapter';

$string['numbering'] = 'Chapter Numbering';
$string['numbering_help'] = '* None - chapter and subchapter titles are not formatted at all, use if you want to define special numbering styles. For example letters: in chapter title type "A First Chapter", "A.1 Some Subchapter",...
* Numbers - chapters and subchapters are numbered (1, 1.1, 1.2, 2, ...)
* Bullets - subchapters are indented and displayed with bullets
* Indented - subchapters are indented';

$string['numbering0'] = 'None';
$string['numbering1'] = 'Numbers';
$string['numbering2'] = 'Bullets';
$string['numbering3'] = 'Indented';

$string['chapterscount'] = 'Chapters';

$string['addafter'] = 'Add new chapter';
$string['confchapterdelete'] = 'Do you really want to delete this chapter?';
$string['confchapterdeleteall'] = 'Do you really want to delete this chapter and all its subchapters?';

$string['generateimscp'] = 'Generate IMS content package';
$string['printbook'] = 'Print Complete Book';
$string['printchapter'] = 'Print This Chapter';
$string['printdate'] = 'Date';
$string['printedby'] = 'Printed by';
$string['top'] = 'top';

$string['navprev'] = 'Previous';
$string['navnext'] = 'Next';
$string['navexit'] = 'Exit Book';

$string['importingchapters'] = 'Importing chapters into book';
$string['import'] = 'Import';
$string['import_help'] = 'You can import a single HTML file or every HTML file in a direcory. Relative file links are converted to absolute chapter links. Images, flash and Java are relinked too.';
$string['doimport'] = 'Import';
$string['doexport'] = 'Export';
$string['importing'] = 'Importing';
$string['relinking'] = 'Relinking';
$string['importinfo'] = 'Import selected HTML file or directory.<br />Chapters are sorted alphabetically using file names.<br />Files named \'sub_*.*\' are always imported as subchapters.';
$string['maindirectory'] = 'Main directory';
$string['fileordir'] = 'File or directory';

$string['book:read'] = 'Read book';
$string['book:edit'] = 'Edit book chapters';
$string['book:viewhiddenchapters'] = 'View hidden book chapters';
$string['book:import'] = 'Import chapters';
$string['book:print'] = 'Print book';
$string['book:exportimscp'] = 'Export book as IMS content package';
