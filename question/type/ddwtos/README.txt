Drag-and-drop, words to sentences question type

This question type requires that gapselect question type
https://github.com/timhunt/moodle-qtype_gapselect/
to be installed in order to work.

This question type was originally the work of Mahmoud Kassaei at
the Open University (http://www.open.ac.uk/).
It was updated to work with the Moodle 2.1 question engine by Tim Hunt.
It was then refactored extenstively by Jamie Pratt (http://jamiep.org/)
as part of creating the gapselect question type.


This question type is compatible with Moodle 2.1+.

To install using git, type this command in the root of your Moodle install
    git clone git://github.com/timhunt/moodle-qtype_ddwtos.git question/type/ddwtos
Then add question/type/ddwtosto your git ignore.

Alternatively, download the zip from
    https://github.com/timhunt/moodle-qtype_ddwtos/zipball/master
unzip it into the question/type folder, and then rename the new folder to
ddwtos.


Note that, if you put superscripts and subscripts in your drag boxes, then there
is a weird layout bug with web browsers that means the boxes will not line up.
You can solve this by putting something like this in your theme CSS:
/*
 * Superscript and subscript: don't use default styling, it does weird things to
 * line height. This fix comes from https://github.com/necolas/normalize.css
 */
sub,
sup {
    font-size: 80%;
    position: relative;
    vertical-align: baseline;
}
sup {
    top: -0.4em;
}
sub {
    bottom: -0.2em;
}
