Drag-and-drop markers question type
-----------------------------------

You can use markers with text labels as drag items onto rectangular, circular or
polygon drop zones on a background image.

This question type requires that gapselect question type
https://github.com/moodleou/moodle-qtype_ddimageortext/ and
https://github.com/moodleou/moodle-qtype_gapselect/
to be installed in order to work.

This question type was written by Jamie Pratt (http://jamiep.org/) for the Open
University.

This version of this question type is compatible with Moodle 2.5+. There are
other versions available for Moodle 2.3+.

###Installation

####Installation Using Git

To install using git for a 2.3+ Moodle installation, type this command in the
root of your Moodle install:
    git clone git://github.com/moodleou/moodle-qtype_ddmarker.git question/type/ddmarker
Then add question/type/ddmarker to your git ignore.

####Installation From Downloaded zip file

Alternatively, download the zip from:
    https://github.com/moodleou/moodle-qtype_ddmarker/zipball/master
unzip it into the question/type folder, and then rename the new folder to ddmarker.

###Converting 'image target' type questions to this type

The imagetarget question type question type will not be upgraded to use with Moodle beyond version 1.9 it seems.

But you can convert your existing image target questions and question attempt data to be drag and drop marker questions and they
will work as the image target previously worked.

There are two ways to convert your imagetarget questions to ddmarker question types.

####Automatic Conversion (recommended)

Conversion will happen automatically when you upgrade for Moodle 2.0.

It is recommended you follow the following steps:

Upgrade your site to Moodle 2.0. This involves:

* Upgrade the code base to Moodle 2.0
* Remove the imagetarget question type code from question/type/imagetarget/
* Go to your http://{moodleroot}/admin/ to trigger the upgrade of the db

At this point your imagetarget questions won't work and will show up as
'missing type' but once that upgrade is done change your Moodle code to Moodle 2.2+

* install the ddmarker question type code in question type ddmarker.
* then go to your http://{moodleroot}/admin/ to trigger the upgrade of the db

This will convert all your imagetarget questions to ddmarker automatically.

####Manual Conversion

You can also use a manual script to convert imagetarget questions to ddmarker
after upgrading to Moodle 2.1 or greater.

* Log in as admin.
* You will find a script in the admin menu under plugins/question types/ to
convert your imagetarget questions to ddmarker.
(This just converts your questions themselves.)
* In order to convert your question attempt data to be used with the ddmarker
question type and Moodle 2.1 or greater you need to find the 'question engine
upgrade helper' which will appear at the root of the admin menu. Use this script to:
* _Reset the upgrade of all attempt data._
* _Run the attempt data upgrade again._

###Issues with converting image target question type questions

####Background image shrinkage

The ddmarker question type will shrink the background image of your questions to
be within a maximum size and width while at the time preserving aspect ratio of
the image. The default max width is 600 pixels and height 400 pixels.

The ddmarker question type does this whenever you edit a question and save it again.

Unfortunately when you open up a question to edit it this means that if the
image is to big it will be shrunk but at present the position of your drop zones
are not moved to compensate for this shrinkage. There won't be a problem until
you try to edit and save a question but when you open it in the editor the drop
zones will not be in the correct position. If you save the question they
they will then be saved in the wrong position.

####Work around for background image shrinkage

Either:

* do not edit questions which have sizes above the allowed max.
* or you can change the allowed maximum size of images there are two constant at
the top of question/type/ddmarker/questiontype.php that define the size limits.

The limit is just there to shrink outrageously large images down to a reasonable
size automatically. You could set the value of the constants defining the max
size to a very large value to effectively disable the image size constraints all
together.

####Lack of drag label in imagetarget questions

Normally each draggable marker in the ddmarker question type has a text label.
There are no labels for the imagetarget markers though, I needed to pick
something that will work in any language as the label for the single imagetarget
draggable marker so I picked 'X'.

####Work around for lack of drag label in imagetarget questions

Your teachers can edit this label after the question has been created. And they
might also like to take advantage of the ability in the ddmarker question type
to be able to specify the correct drop zones for more than one drag marker onto
the same image, giving each drag marker a different label.

You can change the default drag marker label which you can find defined in a
constant at the top of question/type/ddmarker/lib.php

####No automatic feedback telling the user whether they got the question right, partially right or wrong.

In the imagetarget question you get a text message telling the user whether they
got the question correct, partially correct or wrong. In many of the new
question types in Moodle 2.1 onwards the teacher is expected to enter the
message that the user will see in the question definition. This is called
'combined feedback' and by default it is blank. So questions that have been
converted from per 2.1 Moodle, from imagetarget question types that used to
give some feedback to the user will no longer do so.

####Work around for lack of automatic feedback telling the user whether they got

the question right, partially right or wrong.

You can use this plug in
https://github.com/jamiepratt/moodle-admin_tool_questionaddfeedback to bulk add
feedback to questions.
