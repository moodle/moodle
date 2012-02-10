Drag-and-drop markers question type

You can use markers with text labels as drag items onto rectangular, and in
Moodle 2.2+ version circular and polygon defined drop zones on a background image.

This question type requires that gapselect question type
https://github.com/moodleou/moodle-qtype_ddimageortext/ and
https://github.com/moodleou/moodle-qtype_gapselect/
to be installed in order to work.

This question type was written by Jamie Pratt (http://jamiep.org/).

This question type is compatible with Moodle 2.1+ (MOODLE_21_STABLE branch) or
2.2+ (master branch).

To install using git for a 2.2+ Moodle installation, type this command in the
root of your Moodle install:

git clone git://github.com/moodleou/moodle-qtype_ddmarker.git question/type/ddmarker

To install using git for a 2.1+ Moodle installation, type this command in the
root of your Moodle install:

git clone -b MOODLE_21_STABLE git://github.com/moodleou/moodle-qtype_ddmarker.git question/type/ddmarker

Then add question/type/ddmarker to your git ignore.

Alternatively, download the zip from
    Moodle 2.2+ - https://github.com/moodleou/moodle-qtype_ddmarker/zipball/master
    Moodle 2.1+ - https://github.com/moodleou/moodle-qtype_ddmarker/zipball/MOODLE_21_STABLE
unzip it into the question/type folder, and then rename the new folder to ddmarker.
