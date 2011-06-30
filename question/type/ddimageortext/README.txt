A drag-and-drop, images to images question type

This question type requires that gapselect question type
https://github.com/timhunt/moodle-qtype_gapselect/
to be installed in order to work.

This question type was written by Jamie Pratt (http://jamiep.org/).

This question type is compatible with Moodle 2.1+.

Requires this fix : MDL-28099
filepicker form element does not work with element names with an index such as image[0]

To install using git, type this command in the root of your Moodle install
    git clone git://github.com/jamiepratt/moodle-qtype_ddimagetoimage.git question/type/ddimagetoimage
Then add question/type/ddimagetoimage to your git ignore.

Alternatively, download the zip from
    https://github.com/jamiepratt/moodle-qtype_ddimagetoimage/zipball/master
unzip it into the question/type folder, and then rename the new folder to
ddimagetoimage.
