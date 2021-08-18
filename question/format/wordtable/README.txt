# Description
Moodle2Word is a plugin that allows quiz questions to be exported from Moodle into a Word file.
The Word file can then be used to quickly review large numbers of questions
(either online or in print), or to prepare paper tests (where the answers and feedback are hidden).

Moodle2Word also supports importing questions from structured tables in Word directly into the Moodle question bank.
The tables support all the question components (stem, answer options, option-specific and general feedback, 
hints, tags and question meta-data such as penalties grades and other options), as well as embedded images. 

All the main question types except Numerical and Calculated questions are fully supported.
Numerical and Calculated questions are exported, but cannot be imported.

Some OU question types added to core in Moodle 3.0 are also supported: Drag and drop onto image, 
Drag and drop markers, Drag and drop into text and Select missing words.
All-or-Nothing Multiple Choice is also supported. These additional question types all require that
custom versions of the questions be installed to replace the defaults, however.

The Cloze question syntax is particularly useful, as it does not require any knowledge of the
arcane Moodle syntax; instead, use bold for drop-down menu items, and italic for fill-in text fields.

# Language support
Exported questions are labelled in the language of the current Moodle user interface, and the spell-check language is
also set to the correct language.
Similarly, questions can be imported in the same language, not just English.
Both left-to-right and right-to-left languages (such as Arabic and Hebrew) are supported.

# Supporting Word templates
Word templates to support the plugin can be downloaded from the demonstration website
www.Moodle2Word.net, and are available for Word 2003, 2007, 2010 and 2013 (Windows),
and Word 2004 and 2011 (MacOSX). The Windows templates also support a simple question preview facility,
as well as uploading questions from within Word.

If questions contain images, then you need to install the Word template in order to be able to convert
the images in exported questions into embedded images in Word, as they are not automatically visible.

The Word templates are available in most major languages, including English, Spanish and Chinese.
Templates for other languages can be easily added, so feel free to ask.
