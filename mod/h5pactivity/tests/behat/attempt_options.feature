@mod @mod_h5pactivity @core_h5p
Feature: Attempts review settings.
  In order to let users to review attempts
  As a teacher
  I need to have specific settings to let students access the attempts report

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | teacher        |

  Scenario Outline: Attempt review behaviour when accessing an H5P activity
    Given the following "activity" exists:
      | activity       | h5pactivity          |
      | name           | H5P package          |
      | intro          | Test H5P description |
      | course         | C1                   |
      | idnumber       | h5ppackage           |
      | enabletracking | <enabletracking>     |
      | reviewmode     | <reviewmode>         |
    And the following "mod_h5pactivity > attempt" exists:
      | user            | student1    |
      | h5pactivity     | H5P package |
      | attempt         | 1           |
      | interactiontype | compound    |
      | rawscore        | 2           |
      | maxscore        | 2           |
      | duration        | 4           |
      | completion      | 1           |
      | success         | 1           |
    When I am on the "H5P package" "h5pactivity activity" page logged in as <user>
    Then "Attempts report" "link" should <attemptsreportlink> in current page administration
    And I should <previewmode> "You are in preview mode."
    And I should <attempttracking> "Attempt tracking is not enabled for this activity."
    And I should <attempttrackingsettings> "You can enable it in Settings."

    Examples:
      | user     | enabletracking | reviewmode | attemptsreportlink | previewmode | attempttracking | attempttrackingsettings |
      | student1 | 1              | 1          | exist              | not see     | not see         | not see                 |
      | student1 | 1              | 0          | not exist          | not see     | not see         | not see                 |
      | student1 | 0              | 1          | not exist          | not see     | not see         | not see                 |
      | teacher1 | 1              | 1          | exist              | see         | not see         | not see                 |
      | teacher1 | 1              | 0          | exist              | see         | not see         | not see                 |
      | teacher1 | 0              | 1          | not exist          | see         | see             | see                     |
      | teacher2 | 0              | 1          | not exist          | see         | see             | not see                 |
