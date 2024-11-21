# H5P Question Format #

This plugin imports various H5P content types into Moodle question types.

Certain H5P content types are able to be translated into questions
using standard Moodle question types. Not all H5P content types will
be supported since they do not all have analogous Moodle question types
with similar functionality. There will be some unavoidable differences
in behaviour.

If you do not have access to install this or would like to use it or
related prototypes without installing, you can create an account at
moodle.openlearner.org and export the questions from there to your site.

To install copy this directory to question/format/h5p in Moodle directory
structure. Login as admin to complete plugin installation.  Then select
this format during question bank import.

To import H5P content load a Quiz (Question Set) .h5p file or a Column
content type file which contains some of the supported question content
types as the import file and import. Individual questions will be extract
from the Quiz.

Currently supports import of following H5P content types

* Single Choice Set - extracts into individual multichoice questions
* Dialog/Flash Cards - creates a short answer question from each card
* Guess The Answer- creates a short answer question from the card
* Multichoice Question - creates a multichoice question
* True/False Question - creates a true false question
* Drag and Drop Question - creates a Drag and drop onto image question
* Essay - creates an essay question
* Fill in the Blank Question - creates a embedded answer (cloze) question 
with embedded short answer
* Advanced Fill in the Blanks Question - creates a embedded answer (cloze)
question with embedded short answer or multichoice subquestions
* Drag the Text Question - creates a Drag and drop into text question
* Mark The Words - Creates Word select if that question type is installed
* Image Sequencing - Creates an ordering question if that question type is
* Find the Hotspot/Multiple Hotspots- Creates a Drag and drop markers question 
* Crossword - Creates separate short answer questions from the clues with the puzzle word as answer

and any of above which are included in Branching Scenario, Column, Course
Presentation, Interactive Book, Interactive Video, or Quiz (Question Set).

If you want to convert questions into H5P use the _Repurpose resources_ content
type plugin https://moodle.org/plugins/contenttype_repurpose.

## License ##

2020 onward Daniel Thies <dethies@gmail.com>

This program is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation, either version 3 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
for more details.

You should have received a copy of the GNU General Public License along
with this program.  If not, see <http://www.gnu.org/licenses/>.
