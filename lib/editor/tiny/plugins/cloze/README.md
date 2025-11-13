Moodle Tiny Cloze question editor
=================================

![Release](https://img.shields.io/badge/Release-1.14-blue.svg)
[![Moodle Plugin CI](https://github.com/srobotta/moodle-tiny_cloze/actions/workflows/moodle-plugin-ci.yml/badge.svg)](https://github.com/srobotta/moodle-tiny_cloze/actions/workflows/moodle-plugin-ci.yml)
![Supported](https://img.shields.io/badge/Moodle-4.1+-orange.svg)
[![License GPL-3.0](https://img.shields.io/github/license/srobotta/moodle-tiny_cloze?color=lightgrey)](https://github.com/srobotta/moodle-tiny_cloze/blob/main/LICENSE)
[![GitHub contributors](https://img.shields.io/github/contributors/srobotta/moodle-tiny_cloze)](https://github.com/srobotta/moodle-tiny_cloze/graphs/contributors)

Created collaboratively at [MoodleMoot DACH 23 Zurich](https://moodlemootdach.org/).

A plugin for the Moodle Tiny editor to allow easier creation of Cloze questions
see https://docs.moodle.org/en/Embedded_Answers_(Cloze)_question_type.
Inspired by the Atto Cloze editor https://docs.moodle.org/en/Cloze_editor_for_Atto.

In fall 2024 the cloze question editor plugin received the award for "Best Open-Source TinyMCE Plugin for Moodle"

[![Best Open-Source TinyMCE Plugin for Moodle](tinymce-award-cloze-editor.png 'Best Open-Source TinyMCE Plugin for Moodle')](https://www.tiny.cloud/blog/celebrating-moodle-innovation-tinymce-awards-winners-2024/)

## Overview

This is an editor plugin to provide an interface for creating and
modifying embedded answer (Cloze) questions. To install, add a new directory
named `cloze` in `lib/editor/tiny/plugins`, unzip the content of this zip archive
into the newly created directory and visit notifications to update database.

This plugin requires Moodle 4.1 or later as that ships with a new version of the Tiny
editor and the Atto editor is due to be phased out in a future version of Moodle.

## Installation

 - Create a new directory `cloze` in `<moodleroot>/lib/editor/tiny/plugins`.
 - Copy repository content into the newly created directory.
 - Install the plugin from Moodle.

There are no settings for the plugin that can be set in the Moodle Site administration.

## Usage

The button ![edit cloze question icon](pix/icon.svg) should appear in the editor only
while editing embedded answer (Cloze) questions. The icon does not appear when editing
any other text field (whether it's a different question type or not even the question
text field). You will notice that in a Cloze question below the question
text in the feedback area the icon is missing.

### Create a new question

To create a new Cloze question in the text, position the cursor at the desired place
and click on the ![edit cloze question icon](pix/icon.svg) button inside the editor
toolbar or use the "Insert" -> "Cloze question editor" menu item.

![editor menu for cloze questions](screenshots/cloze_4_menu.png)

A popup window will let you choose any of the available question types.

![Screenshot with dialogue to choose question type](screenshots/cloze_5_dialogue_new.png)

Selecting any question type, you will see a plain language description of what that
question type does. Select one question type and then click the "Select question type"
button to actually define the answers and grading information for that question type.

![Screenshot with dialogue to edit numerical question](screenshots/cloze_6_dialogue_numeric.png)

In this particular case a numerical question is defined. The correct answer (e.g. of the
question of the boiling point of water in Fahrenheit) would be 212. However, there
is a tolerance of 10 Fahrenheit above or below the exact answer still to be graded with
0.5 points. The mark (in this case 1 point) is defined at the very top.

Finally, clicking "Insert question" adds the resulting Cloze question syntax into the
question text at the cursor position.

### Edit an existing question

To edit an existing question, please position the cursor in the editor somewhere at the
question text and click. This will select the whole question and the icon is highlighted
as in this screenshot:

![Screenshot with selected question](screenshots/cloze_2_selected.png)

When the toolbar button to edit the Cloze question is clicked, a dialogue window opens
to edit the answers of that question in the text.

![Screenshot editing selected question](screenshots/cloze_3_dialogue_edit.png)

Here you see the predefined answers that come from the question string in the text.
There is one correct and four incorrect answers (the last two answers are out of sight
and must be scrolled to view it). These possible answers are displayed as a selection
inside the text. This particular screenshot also demonstrates how to use one question
in different languages using the [multilang2 filter][1].

Once you are done with the changes, click the button "Insert question" to apply the
changed question string on the existing question.

### Technical details

Cloze questions come in a variety of question types. For some questions more
than one notation can be used, e.g. MC and MULTICHOICE refer to the same question type.
Whenever a question is edited that uses the abbreviation of the question type, upon
saving the changes with the plugin, the question type will use the long form. That does
not change any of its behaviour, but is just a convention.

Multi language elements can be used in the responses and in the feedback strings. However,
you must use the Moodle annotation with the <span> elements. The [multilang2 filter][1]
is not supported here. For more information please check out the Moodle documentation
at https://docs.moodle.org/en/Multi-language_content_filter.

## Troubleshooting FAQ

When the button ![edit cloze question icon](pix/icon.svg) does not appear in the toolbar and the menu "Insert" does not contain
the item to edit a cloze question then please check the following:

- Are you editing a cloze question type? Only for these questions the button appears.
- Are you in the field question text? The button does not appear on the feedback field or
other fields.
- Do you work with the TinyMCE? This plugin is for the current TinyMCE and does not work
with the TinyMCE legacy (this is the old TinyMCE that is not anymore available in newer
Moodle versions).
- Do you see any error messages in the Javascript console? Please hit F12 in your browser
that should bring up the developer tools.
![develpper tools window from Chromium](screenshots/developer-tools.png)
In that window click the console tab and delete
possible existing messages. Now reload the page (can be launched from inside the developer
tools window via Ctrl + r)
- Check that the Moodle dependencies are met. The plugin needs at least Moodle 4.1.

If the problem persists then please [open a new issue on Github](https://github.com/srobotta/moodle-tiny_cloze/issues/new)
or write a new comment on the [Moodle plugins directory](https://moodle.org/plugins/tiny_cloze).

## History

### v1.14
- Release for Moodle 5.1
- Remove unsupported PHP versions from CI chain.
- Improve regex check in answers.
- Some refactoring due to technical needs.
- Update screenshot with new layout.
- Fix format of deprecated language strings.
- Remove capability tiny/cloze:viewplugin.

### v1.13
- Language string missing for "grade" [#28](https://github.com/srobotta/moodle-tiny_cloze/issues/28).
- Improvements in the UI where possible answers are defined.
- Bring plugin in line with [MDL-84884](https://tracker.moodle.org/browse/MDL-84884)
[#27](https://github.com/srobotta/moodle-tiny_cloze/issues/27).

### v1.12
- Add Moodle 5.0 Support.
- Refactoring to add JavaScript unit test.

### v1.11
- Fix [Issue 25](https://github.com/srobotta/moodle-tiny_cloze/issues/25) on error in essay
questions in the questionnaire plugin.

### v1.10
- Fix issue with Moodle 4.1 where class loading doesn't seem to work for the question_bank class.
- Extend the README file by a Troubleshooting FAQ when the button is not displayed.

### v1.9
- Fix issue that if a selected text contains trailing or leading whitespaces,
  these are preserved around the cloze question string when inserted into the text.
- Support for a new REGEX and REGEX_C type that might available through a new
  plugin [moodle-qtype_multianswerrgx](https://github.com/rezeau/moodle-qtype_multianswerrgx)
  by [Joseph Rézeau](https://github.com/rezeau).
- Add CI of Moodle 4.5.
- Fix bug that escaped closing curly brakets where not recognized [issue #21](https://github.com/srobotta/moodle-tiny_cloze/issues/21).
- On double click on a cloze question opens the dialogue automatically.
- Delete icon disappears when there is one answer option left only.
- Style adaptations on the dalogue that will be necessary with upcoming Moodle 4.5. 

### v1.8
- Empty response field are automatically ignored when the question otherwise can be created
from the remaining filled response fields.
- When creating a new question, by default the first answer is marked as correct, all
subsequent answer options are marked as incorrect.

### v1.7
- When creating blanks for possible answers, the default grade is now "Correct" instead
of the previous "Incorrect".
- When a text is selected, the content is taken for the first answer field. This restores
a behaviour that the original cloze plugin for Atto contained.
- Wider input fields for answer and feedback in modal dialogue
(thanks to [Joseph Rézeau](https://github.com/rezeau)).

### v1.6
- Add Support for Moodle 4.4 and PHP 8.3.

### v1.5
- Extend dialogue UI with error messages when input fields contain invalid
values. This fixes [issue #18](https://github.com/srobotta/moodle-tiny_cloze/issues/18).
- Improve handling when adding an answer slot with autofill and focus.
- Prevent providing non-numerical values for answer and tolerance in numeric questions.

### v1.4
- Extend tests to cover functionality of the whole plugin.
- Custom grade values for answers (this addresses [issue #16](https://github.com/srobotta/moodle-tiny_cloze/issues/16))
- Add PHP 8.2 tests for the Moodle 4.3 test runs.
- Streamline the process when the strings for the dialogue are fetched.
- Add alt attributes for the icons.
- Add new modal creation process from Moodle 4.3 but keep the old process for backward compatibility.
- Add missing language string when capabilities are displayed.

### v1.3
- Add new CI stack for latest release and adapt changes to work with Moodle 4.3.

### v1.2
- Fix issue [Insert equation in question text](https://github.com/srobotta/moodle-tiny_cloze/issues/15).
  Credits to that fix actually go to [Mario Wehr](https://github.com/mwehr) because of his
  [contribution in this pull request](https://github.com/bfh/moodle-tiny_multilang2/pull/5)

### v1.1
- Fix source code view, special html was not always formatted correctly.

### v1.0
- First official release.
- Software maturity is set to RC.

### v0.1.0
- Start working on the project at Moodle Moot DACH 23.

[1]: <https://github.com/iarenaza/moodle-filter_multilang2> "Mutlilang v2 Filter Plugin"