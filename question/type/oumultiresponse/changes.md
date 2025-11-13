# Change log for the OU Multi-response question type

## Changes in 2.5

* This version is compatible with Moodle 5.0.
* Added option to use the qtype_multichoice site-wide default setting for Show standard instruction.
* Fixed the misalignment of correct and incorrect icons for Multiresponse sub-questions in
  Combined questions with horizontal layout.
* Increased the size of the Choice and Feedback text areas in the Answers section to two lines.
* Fixed automated tests.
* Fixed a theme issue where the Choice Tiny Editor disrupted the theme’s column layout on the edit question page.
* Added required attribute for the answer field when used in a Combined question.


## Changes in 2.4

* This version works with Moodle 4.0.


## Changes in 2.3

* Option added to hide the 'Select one or more:' message.
* Fix layout to match recent changes in Moodle core multiple choice layout
* ... including when used in combined questions.


## Changes in 2.2

* Change for when OU Multi-response question subquestions
  are used inside combined questions, so that the question
  authoring can control 'Number the choices' setting.


## Changes in 2.1

* Support for the Moodle mobile app.
* Update Behat tests to work with Moodle 3.8.


## Changes in 2.0

* Fix positioning of the right/wrong icons in Moodle 3.5+.
* Fix automated tests to pass with Moodle 3.6.


## Changes in 1.9

* Travis-CI automated testing integration.
* Privacy API implementation.
* Better wording in the question type chooser to help distinguish this
  from the standard Moodle multiple choice type.
* Fix some coding style.
* Due to privacy API support, this version now only works in Moodle 3.4+
  For older Moodles, you will need to use a previous version of this plugin.


## 1.8 and before

Changes were not documented.
