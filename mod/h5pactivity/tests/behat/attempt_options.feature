@mod @mod_h5pactivity @core_h5p @javascript
Feature: Attempts review settings.
  In order to let users to review attempts
  As a teacher
  I need to have specific settings to let students access the attempts report

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |

  Scenario: Student accessing an activity with attempt review
    Given the following "activity" exists:
      | activity       | h5pactivity          |
      | name           | H5P package          |
      | intro          | Test H5P description |
      | course         | C1                   |
      | idnumber       | h5ppackage           |
      | enabletracking | 1                    |
      | reviewmode     | 1                    |
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
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    Then "Attempts report" "link" should exist in current page administration
    And I should not see "This content is displayed in preview mode"

  Scenario: Student accessing an activity without attempt review
    Given the following "activity" exists:
      | activity       | h5pactivity          |
      | name           | H5P package          |
      | intro          | Test H5P description |
      | course         | C1                   |
      | idnumber       | h5ppackage           |
      | enabletracking | 1                    |
      | reviewmode     | 0                    |
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
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    Then "Attempts report" "link" should not exist in current page administration
    And I should not see "This content is displayed in preview mode"

  Scenario: Student accessing an activity without tracking
    Given the following "activity" exists:
      | activity       | h5pactivity          |
      | name           | H5P package          |
      | intro          | Test H5P description |
      | course         | C1                   |
      | idnumber       | h5ppackage           |
      | enabletracking | 0                    |
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
    When I am on the "H5P package" "h5pactivity activity" page logged in as student1
    Then "Attempts report" "link" should not exist in current page administration
    And I should see "This content is displayed in preview mode"

  Scenario: Teacher accessing an activity with attempt review
    Given the following "activity" exists:
      | activity       | h5pactivity          |
      | name           | H5P package          |
      | intro          | Test H5P description |
      | course         | C1                   |
      | idnumber       | h5ppackage           |
      | enabletracking | 1                    |
      | reviewmode     | 1                    |
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
    When I am on the "H5P package" "h5pactivity activity" page logged in as teacher1
    Then "Attempts report" "link" should exist in current page administration
    And I should see "This content is displayed in preview mode"

  Scenario: Teacher accessing an activity without attempt review
    Given the following "activity" exists:
      | activity       | h5pactivity          |
      | name           | H5P package          |
      | intro          | Test H5P description |
      | course         | C1                   |
      | idnumber       | h5ppackage           |
      | enabletracking | 1                    |
      | reviewmode     | 0                    |
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
    When I am on the "H5P package" "h5pactivity activity" page logged in as teacher1
    Then "Attempts report" "link" should exist in current page administration
    And I should see "This content is displayed in preview mode"
