Release Notes

Release 4.4.0 (Build - 2025110900)
New Features:
* [PR590](https://github.com/PoetOS/moodle-mod_questionnaire/pull/590): Allow responses to be deleted automatically after a specified time. This is disabled by default.

Improvements:
* [PR618](https://github.com/PoetOS/moodle-mod_questionnaire/pull/618): Make printable questionnaire more readable.
* Updated deprecated functions.
* [PR589](https://github.com/PoetOS/moodle-mod_questionnaire/pull/589): Allow selection of submitted responses or in progress responses on results pages.
* [PR596](https://github.com/PoetOS/moodle-mod_questionnaire/pull/596): Allow sorting on name and submission date on summary page.
* [PR588](https://github.com/PoetOS/moodle-mod_questionnaire/pull/588): Add a print button to the My report page.
* [PR378](https://github.com/PoetOS/moodle-mod_questionnaire/pull/378): Add open and close dates back to view page.
* [PR604](https://github.com/PoetOS/moodle-mod_questionnaire/pull/604): Show all "other" free choices for rate questions individually on report pages.
* [PR616](https://github.com/PoetOS/moodle-mod_questionnaire/pull/616): Only show dependent questions answered on response page.
* [I120](https://github.com/PoetOS/moodle-mod_questionnaire/issues/120): Added idnumber to export.

Bug Fixes:
* Ensure numbering doesn't appear on results pages when they are turned off.
* Mobile - ensure pull to refresh doesn't resend question responses.
* Fixed empty key error.
* Fixed oversize icon display in 4.5.
* Thank you page header is now filtered.

Release 4.1.1 (Build - 2024082900)

Improvements:
* Compatible with Moodle 4.3 and 4.4.
* Compatible with PHP8.2.
* PR449 - Allow localized answer options to be displayed correctly in conditions.
* PR506 - Accessibility: improved accessibility for essay box type.
* PR495 - Accessibility: The slider values should be associated with the labels and the slider should be programmatically associated with the question
* PR497 - Accessibility: Rate table does not have a programmatically associated caption.
* PR496 - Accessibility: Numeric instructions not programmatically associated with field.
* PR505 - Accessibility: Rate form controls within the table are not accessible.
* PR501 - Accessibility: Check boxes missing group label.
* PR511 - Accessibility: Radio buttons & Yes/No missing group labels.
* PR517 - Mobile: Update sectiontext questions to display on mobile.
* PR520 - Add Slider question type compatibility with Feedback features.
* PR526 - Have additional info text pass through filters everywhere fixes.
* PR534 - Fix namespace issues with externallib.php file.
* PR536 - Support for user identity fields in Download Responses.
* PR577 - Improved headings in report page.
* PR581 - Mobile: Adapt mobile code to ionic 7.
* PR569 - Adopt icon size to 24×24 with a smaller content as other icons.
* PR586, PR579, PR594 - Various deprecations fixed.
* PR593 - Ensure "pdf" extension force.

Bug Fixes:
* PR508 - General PHP fixes.
* PR523 - Behat activity completion fix.
* PR514 - Section text qtype should not support feedback.
* PR516 - Course description displays properly.

Release 4.1.0 (Build - 2023081100)

Initial release for Moodle 4.1 forward.

(see CHANGES.md in release 4.00 for earlier changes.)

