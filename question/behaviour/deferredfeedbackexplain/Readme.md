# Deferred feedback with explanation question behaviour
https://moodle.org/plugins/qbehaviour_deferredfeedbackexplain

This Moodle question behaviour was created by Tim Hunt.

## Introduction

This question behaviour is just like deferred feedback, but with an additional
text box where students can give a reason why they gave the answer they did.

No attempt is made to automatically grade the explanation, nor is it required.
However, it may be used in various ways, for example

1. The teacher may want to manually edit the grades where the student gave a wrong answer, to give partial credit if the student used the right method or approach.
2. The student might want to explain their thinking, so that later, when the results and feedback are revealed, they are reminded of what they were thinking at the time, and so can reflect more deeply.

## How to install

This is a standard question behaviour, and so is installed in any of the usual ways.

You can install it from the Moodle plugins directory:
https://moodle.org/plugins/view.php?plugin=qbehaviour_deferredfeedbackexplain

Alternatively, you can download the zip file from here:
https://github.com/timhunt/moodle-qbehaviour_deferredfeedbackexplain/archive/master.zip
Unzip that, rename the folder to `deferredfeedbackexplain`, and copy it to be
`question/behaviour/deferredfeedbackexplain` in your Moodle instal.

Alternatively, you can install it using git. In the top of your Moodle instal
type:
```
git clone git://github.com/timhunt/moodle-qbehaviour_deferredfeedbackexplain.git question/behaviour/deferredfeedbackexplain
echo '/question/behaviour/deferredfeedbackexplain/' >> .git/info/exclude
```

Once you have copied the files into place, visit **Site administration** ->
**Notifications** in your Moodle site to allow plugin to instal itself.
